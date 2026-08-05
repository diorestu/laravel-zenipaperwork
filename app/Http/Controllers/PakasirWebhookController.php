<?php

namespace App\Http\Controllers;

use App\Models\BillingSubmission;
use App\Services\PakasirPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PakasirWebhookController extends Controller
{
    public function __invoke(Request $request, PakasirPaymentGateway $pakasir): JsonResponse
    {
        $payload = $request->all();
        $project = (string) config('services.pakasir.project');
        $orderId = (string) $request->input('order_id');
        $amount = (int) $request->input('amount');
        $status = strtolower((string) $request->input('status'));

        if ($project === '') {
            Log::error('Webhook Pakasir gagal diproses karena konfigurasi services.pakasir.project belum diatur.');
            return response()->json(['message' => 'Server misconfiguration.'], 500);
        }

        if (! hash_equals($project, (string) $request->input('project'))) {
            Log::warning('Webhook Pakasir ditolak karena project tidak sesuai.', ['payload' => $payload]);

            return response()->json(['message' => 'Project tidak valid.'], 422);
        }

        $submission = BillingSubmission::query()
            ->where('payment_gateway', 'pakasir')
            ->where('payment_order_id', $orderId)
            ->first();

        if (! $submission) {
            Log::warning('Webhook Pakasir tidak menemukan billing submission.', ['payload' => $payload]);

            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        // Return early if already processed to prevent duplicate processing and API hits
        if ($submission->status === 'confirmed') {
            return response()->json(['message' => 'Billing sudah diaktifkan sebelumnya.']);
        }

        if ((int) $submission->amount !== $amount) {
            Log::warning('Webhook Pakasir ditolak karena amount tidak sesuai.', [
                'payload' => $payload,
                'billing_submission_id' => $submission->id,
                'expected_amount' => (int) $submission->amount,
            ]);

            return response()->json(['message' => 'Nominal tidak valid.'], 422);
        }

        $detailPayload = $pakasir->detail($submission);
        $detailStatus = strtolower((string) (
            data_get($detailPayload, 'payment.status')
            ?? data_get($detailPayload, 'transaction.status')
            ?? data_get($detailPayload, 'status')
        ));

        $combinedPayload = array_replace_recursive($submission->payment_payload ?? [], [
            'webhook' => $payload,
            'detail' => $detailPayload,
        ]);

        if ($status !== 'completed' || $detailStatus !== 'completed') {
            $submission->update(['payment_payload' => $combinedPayload]);

            return response()->json(['message' => 'Webhook diterima, transaksi belum complete.']);
        }

        BillingSubmission::forCompany($submission->company_id)
            ->whereKeyNot($submission->id)
            ->where('status', 'confirmed')
            ->update(['status' => 'stopped']);

        $submission->update([
            'status' => 'confirmed',
            'payment_number' => data_get($detailPayload, 'payment_number') ?? $submission->payment_number,
            'payment_url' => data_get($detailPayload, 'payment_url') ?? $submission->payment_url,
            'payment_payload' => $combinedPayload,
        ]);

        // Update company active plan & expiry
        $company = $submission->company;
        $endsAt = $submission->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();
        $company->update([
            'active_plan' => $submission->package,
            'subscription_ends_at' => $endsAt,
        ]);

        return response()->json(['message' => 'Billing berhasil diaktifkan.']);
    }
}
