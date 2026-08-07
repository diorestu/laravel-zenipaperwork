<?php

namespace App\Services;

use App\Models\BillingSubmission;
use App\Models\Invoice;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SumopodPaymentGateway
{
    public function createQris(BillingSubmission $submission): array
    {
        $baseUrl = rtrim((string) SystemSetting::get('sumopod_base_url', config('services.sumopod.base_url', 'https://api-pay-sandbox.sumopod.com/api/v1')), '/');
        $apiKey = SystemSetting::get('sumopod_api_key', config('services.sumopod.api_key', ''));

        if (! $submission->payment_order_id || ! str_starts_with($submission->payment_order_id, 'INV-')) {
            $submission->update(['payment_order_id' => 'INV-B'.str_pad((string) $submission->id, 5, '0', STR_PAD_LEFT).'-'.time()]);
        }

        if (empty($apiKey)) {
            \Illuminate\Support\Facades\Log::warning('Sumopod Payment Gateway belum dikonfigurasi API Key-nya di Superadmin Settings.');

            return [
                'error' => 'API Key Sumopod belum diisi di Pengaturan Superadmin.',
                'status' => 'error',
            ];
        }

        $successUrl = str_replace('http://', 'https://', route('settings.billing.payment-result', [$submission, 'success']));
        $cancelUrl = str_replace('http://', 'https://', route('settings.billing.payment-result', [$submission, 'cancel']));

        $requestPayload = [
            'order_id' => (string) $submission->payment_order_id,
            'amount' => (int) round($submission->amount),
            'currency' => 'IDR',
            'expires_in_hours' => 24,
            'success_return_url' => $successUrl,
            'cancel_return_url' => $cancelUrl,
            'payment_method_type_code' => 'QRIS',
        ];

        try {
            $response = Http::timeout((int) config('services.sumopod.timeout', 15))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Api-Key' => $apiKey,
                ])
                ->acceptJson()
                ->post($baseUrl.'/payments', $requestPayload);

            $json = $response->json();
            $payload = is_array($json) ? $json : [];

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('Sumopod Payment API call failed', [
                    'status' => $response->status(),
                    'request_payload' => $requestPayload,
                    'response_body' => $response->body(),
                ]);

                $errMsg = data_get($payload, 'message')
                    ?? data_get($payload, 'error')
                    ?? data_get($payload, 'errors')
                    ?? $response->body();

                if (is_array($errMsg)) {
                    $errMsg = json_encode($errMsg);
                }

                $payload['error'] = (string) $errMsg;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Sumopod Payment API Exception: '.$e->getMessage());
            $payload = ['error' => 'Gagal terhubung ke API Sumopod: '.$e->getMessage()];
        }

        return $this->normalizePayload($payload, $submission);
    }

    public function createQrisForInvoice(Invoice $invoice): array
    {
        $baseUrl = rtrim((string) SystemSetting::get('sumopod_base_url', config('services.sumopod.base_url', 'https://api-pay-sandbox.sumopod.com/api/v1')), '/');
        $apiKey = SystemSetting::get('sumopod_api_key', config('services.sumopod.api_key', ''));

        if (! $apiKey) {
            throw ValidationException::withMessages([
                'payment' => 'Merchant belum mengkonfigurasi Payment Gateway Sumopod.',
            ]);
        }

        if (! $invoice->payment_order_id) {
            $invoice->update(['payment_order_id' => 'INV-'.$invoice->id.'-'.time()]);
        }

        $response = Http::timeout((int) config('services.sumopod.timeout', 15))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $apiKey,
            ])
            ->acceptJson()
            ->post($baseUrl.'/payments', [
                'order_id' => $invoice->payment_order_id,
                'amount' => (int) $invoice->balance_due,
                'currency' => 'IDR',
                'expires_in_hours' => 24,
                'success_return_url' => route('public.invoices.show', $invoice->public_token ?? $invoice->id),
                'cancel_return_url' => route('public.invoices.show', $invoice->public_token ?? $invoice->id),
                'payment_method_type_code' => 'QRIS',
            ])
            ->throw()
            ->json();

        $payload = is_array($response) ? $response : [];
        $normalized = $this->normalizeInvoicePayload($payload, $invoice);

        $invoice->update([
            'payment_number' => $normalized['payment_number'],
            'payment_url' => $normalized['payment_url'],
        ]);

        return $normalized;
    }

    public function detail(BillingSubmission $submission): array
    {
        $baseUrl = rtrim((string) SystemSetting::get('sumopod_base_url', config('services.sumopod.base_url', 'https://api-pay-sandbox.sumopod.com/api/v1')), '/');
        $apiKey = SystemSetting::get('sumopod_api_key', config('services.sumopod.api_key', ''));

        if (! $apiKey || ! $submission->payment_order_id) {
            return [];
        }

        try {
            $response = Http::timeout((int) config('services.sumopod.timeout', 15))
                ->withHeaders([
                    'X-Api-Key' => $apiKey,
                ])
                ->acceptJson()
                ->get($baseUrl.'/payments/'.$submission->payment_order_id)
                ->json();

            $payload = is_array($response) ? $response : [];
        } catch (\Throwable $e) {
            $payload = [];
        }

        return $this->normalizePayload($payload, $submission);
    }

    private function normalizePayload(array $payload, BillingSubmission $submission): array
    {
        $paymentUrl = data_get($payload, 'payment_link_url')
            ?? data_get($payload, 'payment_url')
            ?? data_get($payload, 'payment.payment_url');

        return $payload + [
            'payment_id' => data_get($payload, 'payment_id'),
            'payment_number' => data_get($payload, 'payment_code') ?? data_get($payload, 'payment_number'),
            'payment_url' => $paymentUrl,
            'status' => data_get($payload, 'status', 'pending'),
        ];
    }

    private function normalizeInvoicePayload(array $payload, Invoice $invoice): array
    {
        $paymentUrl = data_get($payload, 'payment_link_url')
            ?? data_get($payload, 'payment_url')
            ?? data_get($payload, 'payment.payment_url');

        return $payload + [
            'payment_id' => data_get($payload, 'payment_id'),
            'payment_number' => data_get($payload, 'payment_code') ?? data_get($payload, 'payment_number'),
            'payment_url' => $paymentUrl,
            'status' => data_get($payload, 'status', 'pending'),
        ];
    }
}
