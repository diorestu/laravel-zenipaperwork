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
        $company = $request->user()->company;

        if (! $company) {
            return back()->with('error', 'Perusahaan Anda belum dikonfigurasi.');
        }

        $isMobile = $request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile');

        // Check 1: Prevent duplicate pending submissions
        $pendingSubmission = BillingSubmission::forCompany($company->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingSubmission) {
            $msg = 'Anda masih memiliki pengajuan billing yang sedang diproses (Paket '.str($pendingSubmission->package)->headline().'). Harap tunggu konfirmasi admin terlebih dahulu.';
            if ($isMobile) {
                return redirect()->route('mobile.billing')->with('error', $msg);
            }

            return redirect()->route('settings.billing')->with('error', $msg);
        }

        // Check 2: Prevent submissions for same or lower plans unless upgrading or on trial
        $currentPlanSlug = $company->getActivePlanSlug();
        $levels = ['starter' => 1, 'business' => 2, 'enterprise' => 3];
        $newPlanSlug = $data['package'];

        if ($currentPlanSlug && $currentPlanSlug !== 'trial') {
            $currentLevel = $levels[$currentPlanSlug] ?? 0;
            $newLevel = $levels[$newPlanSlug] ?? 0;

            if ($newLevel <= $currentLevel) {
                $msg = 'Anda saat ini sudah menggunakan paket '.str($currentPlanSlug)->headline().'. Pengajuan billing hanya diperbolehkan untuk upgrade ke paket layanan yang lebih tinggi.';
                if ($isMobile) {
                    return redirect()->route('mobile.billing')->with('error', $msg);
                }

                return redirect()->route('settings.billing')->with('error', $msg);
            }
        }

        $plan = BillingPlans::find($data['package']);
        abort_unless($plan, 422);

        $data['amount'] = BillingPlans::amountFor($plan, $data['billing_period']);

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('billing-proofs', 'public');
        }
        unset($data['proof']);

        $submission = BillingSubmission::create($data + [
            'company_id' => $company->id,
            'payment_gateway' => $data['payment_method'] === 'qris' ? 'pakasir' : null,
            'status' => 'pending',
        ]);

        if ($submission->payment_method === 'qris') {
            $submission->update(['payment_order_id' => 'BILL-'.$submission->id]);
            $payload = rescue(fn () => $pakasir->createQris($submission->refresh()), [], false);
            if (! empty($payload)) {
                $submission->update([
                    'payment_number' => $payload['payment_number'] ?? $payload['number'] ?? null,
                    'payment_url' => $payload['payment_url'] ?? $payload['checkout_url'] ?? $payload['url'] ?? null,
                    'payment_payload' => $payload,
                ]);
            }
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
            if (empty($payload['payment_number'])) {
                $payload = rescue(fn () => $pakasir->createQris($submission), [], false);
            }

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
            ? rescue(fn () => (new Builder)->build(data: $paymentNumber, size: 320, margin: 16)->getDataUri(), null, false)
            : null;

        return view('settings.billing-show', compact('submission', 'paymentQrCode'));
    }

    public function mobileShow(BillingSubmission $billingSubmission, PakasirPaymentGateway $pakasir)
    {
        $this->authorize('view', $billingSubmission);

        $submission = $billingSubmission->load('company');
        if ($submission->payment_method === 'qris' && ! $submission->payment_number) {
            $payload = rescue(fn () => $pakasir->detail($submission), [], false);
            if (empty($payload['payment_number'])) {
                $payload = rescue(fn () => $pakasir->createQris($submission), [], false);
            }

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
            ? rescue(fn () => (new Builder)->build(data: $paymentNumber, size: 320, margin: 16)->getDataUri(), null, false)
            : null;

        return view('mobile.billing-show', compact('submission', 'paymentQrCode'));
    }
}
