<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingSubmissionRequest;
use App\Models\BillingSubmission;
use App\Services\PakasirPaymentGateway;
use App\Support\BillingPlans;
use Endroid\QrCode\Builder\Builder;

class BillingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $plans = BillingPlans::all();
        $submissions = BillingSubmission::forCompany($company->id)->latest()->get();

        $activePlan = BillingSubmission::forCompany($company->id)
            ->where('status', 'confirmed')
            ->latest()
            ->value('package');

        $onTrial = $company->onTrial();
        $trialEndsAt = $company->trial_ends_at;
        $trialDaysRemaining = $onTrial ? max(0, (int) now()->diffInDays($trialEndsAt)) : 0;

        return view('settings.billing', compact(
            'plans',
            'activePlan',
            'submissions',
            'onTrial',
            'trialEndsAt',
            'trialDaysRemaining'
        ));
    }

    public function store(StoreBillingSubmissionRequest $request, PakasirPaymentGateway $pakasir)
    {
        $data = $request->validated();
        $plan = BillingPlans::find($data['package']);
        abort_unless($plan, 422);

        $data['amount'] = BillingPlans::amountFor($plan, $data['billing_period']);

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

        $isMobile = $request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile');

        if ($submission->payment_method === 'manual_transfer') {
            if ($isMobile) {
                return redirect()->route('mobile.billing')->with('success', 'Tagihan manual dikirim untuk konfirmasi.');
            }

            return redirect()->route('settings.billing')->with('success', 'Tagihan manual dikirim untuk konfirmasi.');
        }

        if ($isMobile) {
            return redirect()->route('mobile.billing.show', $submission)->with('success', 'Pembayaran QRIS Pakasir berhasil dibuat.');
        }

        return redirect()->route('settings.billing.show', $submission)->with('success', 'Pembayaran dibuat.');
    }

    public function mobileIndex()
    {
        $company = auth()->user()->company;
        $plans = BillingPlans::all();
        $submissions = BillingSubmission::forCompany($company->id)->latest()->get();

        $activePlan = BillingSubmission::forCompany($company->id)
            ->where('status', 'confirmed')
            ->latest()
            ->value('package');

        $onTrial = $company->onTrial();
        $trialEndsAt = $company->trial_ends_at;
        $trialDaysRemaining = $onTrial ? max(0, (int) now()->diffInDays($trialEndsAt)) : 0;

        return view('mobile.billing', compact(
            'plans',
            'activePlan',
            'submissions',
            'onTrial',
            'trialEndsAt',
            'trialDaysRemaining'
        ));
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

    public function mobileShow(BillingSubmission $billingSubmission, PakasirPaymentGateway $pakasir)
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

        return view('mobile.billing-show', compact('submission', 'paymentQrCode'));
    }
}
