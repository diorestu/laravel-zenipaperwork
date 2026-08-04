<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingSubmissionRequest;
use App\Models\BillingSubmission;
use App\Services\PakasirPaymentGateway;
use Endroid\QrCode\Builder\Builder;

class BillingController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $plans = $this->plans();
        $activePlan = BillingSubmission::forCompany($companyId)
            ->where('status', 'confirmed')
            ->latest()
            ->value('package');
        $submissions = BillingSubmission::forCompany($companyId)->latest()->get();

        return view('settings.billing', compact('plans', 'activePlan', 'submissions'));
    }

    public function store(StoreBillingSubmissionRequest $request, PakasirPaymentGateway $pakasir)
    {
        $data = $request->validated();
        $plan = collect($this->plans())->firstWhere('slug', $data['package']);
        abort_unless($plan, 422);

        $data['amount'] = $data['billing_period'] === 'yearly'
            ? (int) round($plan['amount'] * 12 * 0.9)
            : $plan['amount'];

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('billing-proofs', 'public');
        }
        unset($data['proof']);

        $submission = BillingSubmission::create($data + [
            'company_id' => $request->user()->company_id,
            'payment_gateway' => $data['payment_method'] === 'qris' ? 'pakasir' : null,
            'status' => 'pending',
        ]);

        if ($submission->payment_method === 'qris') {
            $submission->update(['payment_order_id' => 'BILL-'.$submission->id]);
            $payload = $pakasir->createQris($submission->refresh());
            $submission->update([
                'payment_number' => $payload['payment_number'] ?? $payload['number'] ?? null,
                'payment_url' => $payload['payment_url'] ?? $payload['checkout_url'] ?? $payload['url'] ?? null,
                'payment_payload' => $payload,
            ]);
        }

        if ($submission->payment_method === 'manual_transfer') {
            return redirect()->route('settings.billing')->with('success', 'Tagihan manual dikirim untuk konfirmasi.');
        }

        return redirect()->route('settings.billing.show', $submission)->with('success', 'Pembayaran dibuat.');
    }

    public function show(BillingSubmission $billingSubmission, PakasirPaymentGateway $pakasir)
    {
        $this->authorize('view', $billingSubmission);

        $submission = $billingSubmission->load('company');
        if ($submission->payment_method === 'qris' && ! $submission->payment_number) {
            $payload = rescue(fn () => $pakasir->detail($submission), [], false);

            if ($payload !== []) {
                $submission->update([
                    'payment_number' => $payload['payment_number'] ?? null,
                    'payment_url' => $payload['payment_url'] ?? null,
                    'payment_payload' => $payload,
                ]);
                $submission->refresh()->load('company');
            }
        }

        $paymentNumber = (string) ($submission->payment_number ?? data_get($submission->payment_payload, 'payment.payment_number'));
        $paymentQrCode = $paymentNumber !== ''
            ? (new Builder)->build(data: $paymentNumber, size: 320, margin: 16)->getDataUri()
            : null;

        return view('settings.billing-show', compact('submission', 'paymentQrCode'));
    }

    private function plans(): array
    {
        return [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'amount' => 25000,
                'features' => [
                    'Kelola hingga 100 klien',
                    'Kelola hingga 100 produk atau layanan',
                    'Buat hingga 50 invoice/quotation per bulan',
                    'Unduh PDF invoice dan penawaran',
                    'Riwayat pembayaran manual',
                ],
            ],
            [
                'slug' => 'business',
                'name' => 'Business',
                'amount' => 99000,
                'features' => [
                    'Kelola hingga 500 klien',
                    'Kelola hingga 500 produk atau layanan',
                    'Buat penawaran tanpa batas',
                    'Buat hingga 500 invoice',
                    'Pembayaran bertahap dan catatan termin',
                    'Riwayat pembayaran dan status pelunasan',
                    'Pengaturan rekening bank dan profil perusahaan',
                ],
            ],
            [
                'slug' => 'enterprise',
                'name' => 'Enterprise',
                'amount' => 299000,
                'features' => [
                    'Klien, produk, penawaran, dan invoice tanpa batas',
                    'Semua fitur Business',
                    'Prioritas dukungan operasional',
                    'Pendampingan setup dokumen perusahaan',
                    'Kebutuhan kapasitas dan workflow khusus',
                    'Review konfigurasi billing dan pembayaran',
                ],
            ],
        ];
    }
}
