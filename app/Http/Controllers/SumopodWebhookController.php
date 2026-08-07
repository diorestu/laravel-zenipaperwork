<?php

namespace App\Http\Controllers;

use App\Models\BillingSubmission;
use App\Models\Invoice;
use App\Services\SumopodPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SumopodWebhookController extends Controller
{
    public function __invoke(Request $request, SumopodPaymentGateway $sumopod): JsonResponse
    {
        $payload = $request->all();
        $orderId = (string) ($request->input('order_id') ?? data_get($payload, 'order_id'));
        $amount = (int) ($request->input('amount') ?? data_get($payload, 'amount'));
        $status = strtolower((string) ($request->input('status') ?? data_get($payload, 'status')));

        if (! $orderId) {
            Log::warning('Webhook Sumopod ditolak karena order_id tidak ditemukan.', ['payload' => $payload]);

            return response()->json(['message' => 'order_id wajib diisi.'], 422);
        }

        // 1. Check if this is a Billing Submission payment
        $submission = BillingSubmission::query()
            ->where('payment_order_id', $orderId)
            ->first();

        // 2. Check if this is an Invoice payment
        $invoice = null;
        if (! $submission && str_starts_with($orderId, 'INV-')) {
            $invoice = Invoice::where('payment_order_id', $orderId)->first();
        }

        if (! $submission && ! $invoice) {
            Log::warning('Webhook Sumopod tidak menemukan transaksi billing submission atau invoice.', ['payload' => $payload]);

            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $isPaid = in_array($status, ['paid', 'completed', 'success', 'settlement']);

        // Handle Invoice payment callback
        if ($invoice) {
            if ($isPaid) {
                if ($invoice->status !== 'paid') {
                    $invoice->payments()->create([
                        'company_id' => $invoice->company_id,
                        'amount' => $amount > 0 ? $amount : $invoice->balance_due,
                        'paid_at' => now(),
                        'method' => 'qris',
                        'reference' => 'Sumopod QRIS '.$orderId,
                        'notes' => 'Pembayaran otomatis via Webhook Sumopod QRIS',
                    ]);

                    $invoice->refresh();
                    $invoice->updateStatusBasedOnPayments();
                }

                return response()->json(['message' => 'Pembayaran invoice berhasil dicatat.']);
            }

            return response()->json(['message' => 'Webhook invoice diterima, status belum paid.']);
        }

        // Return early if billing submission already confirmed
        if ($submission->status === 'confirmed') {
            return response()->json(['message' => 'Billing sudah diaktifkan sebelumnya.']);
        }

        $detailPayload = rescue(fn () => $sumopod->detail($submission), [], false);
        $detailStatus = strtolower((string) (data_get($detailPayload, 'status') ?? $status));

        $combinedPayload = array_replace_recursive($submission->payment_payload ?? [], [
            'webhook' => $payload,
            'detail' => $detailPayload,
        ]);

        if (! $isPaid && ! in_array($detailStatus, ['paid', 'completed', 'success', 'settlement'])) {
            $submission->update(['payment_payload' => $combinedPayload]);

            return response()->json(['message' => 'Webhook diterima, transaksi belum paid.'], 200);
        }

        BillingSubmission::forCompany($submission->company_id)
            ->whereKeyNot($submission->id)
            ->where('status', 'confirmed')
            ->update(['status' => 'stopped']);

        $submission->update([
            'status' => 'confirmed',
            'payment_number' => data_get($payload, 'payment_code') ?? data_get($detailPayload, 'payment_number') ?? $submission->payment_number,
            'payment_url' => data_get($payload, 'payment_link_url') ?? data_get($detailPayload, 'payment_url') ?? $submission->payment_url,
            'payment_payload' => $combinedPayload,
        ]);

        // Update company active plan & expiry
        $company = $submission->company;
        $endsAt = $submission->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();
        $company->update([
            'active_plan' => $submission->package,
            'subscription_ends_at' => $endsAt,
        ]);

        return response()->json(['message' => 'Billing berhasil diaktifkan via Sumopod.']);
    }
}
