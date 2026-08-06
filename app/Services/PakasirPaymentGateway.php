<?php

namespace App\Services;

use App\Models\BillingSubmission;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PakasirPaymentGateway
{
    public function createQris(BillingSubmission $submission): array
    {
        $baseUrl = rtrim((string) config('services.pakasir.base_url', 'https://app.pakasir.com/api'), '/');
        $project = config('services.pakasir.project');
        $apiKey = config('services.pakasir.api_key');

        if (! $project || ! $apiKey) {
            throw ValidationException::withMessages([
                'payment_method' => 'Konfigurasi Pakasir belum lengkap.',
            ]);
        }

        $response = Http::timeout((int) config('services.pakasir.timeout', 15))
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Paperwork/1.0')
            ->acceptJson()
            ->post($baseUrl.'/transactioncreate/qris', [
                'project' => $project,
                'order_id' => $submission->payment_order_id,
                'amount' => (int) $submission->amount,
                'api_key' => $apiKey,
            ])
            ->throw()
            ->json();

        $payload = is_array($response) ? $response : [];

        return $this->normalizePayload($payload, $submission);
    }

    public function detail(BillingSubmission $submission): array
    {
        $baseUrl = rtrim((string) config('services.pakasir.base_url', 'https://app.pakasir.com/api'), '/');
        $project = config('services.pakasir.project');
        $apiKey = config('services.pakasir.api_key');

        if (! $project || ! $apiKey || ! $submission->payment_order_id) {
            return [];
        }

        $response = Http::timeout((int) config('services.pakasir.timeout', 15))
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Paperwork/1.0')
            ->acceptJson()
            ->get($baseUrl.'/transactiondetail', [
                'project' => $project,
                'order_id' => $submission->payment_order_id,
                'amount' => (int) $submission->amount,
                'api_key' => $apiKey,
            ])
            ->throw()
            ->json();

        $payload = is_array($response) ? $response : [];

        return $this->normalizePayload($payload, $submission);
    }

    private function normalizePayload(array $payload, BillingSubmission $submission): array
    {
        $webBaseUrl = str_replace('/api', '', rtrim((string) config('services.pakasir.base_url', 'https://app.pakasir.com/api'), '/'));
        $project = config('services.pakasir.project');
        $paymentUrl = data_get($payload, 'payment.payment_url')
            ?? data_get($payload, 'transaction.payment_url')
            ?? data_get($payload, 'payment_url')
            ?? ($project ? "{$webBaseUrl}/pay/{$project}/".(int) $submission->amount.'?order_id='.$submission->payment_order_id.'&qris_only=1' : null);

        return $payload + [
            'payment_number' => data_get($payload, 'payment.payment_number')
                ?? data_get($payload, 'transaction.payment_number')
                ?? data_get($payload, 'payment_number')
                ?? data_get($payload, 'number'),
            'payment_url' => $paymentUrl,
        ];
    }
}
