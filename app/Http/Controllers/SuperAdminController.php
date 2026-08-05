<?php

namespace App\Http\Controllers;

use App\Models\BillingSubmission;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index()
    {
        return view('super-admin.index', [
            'users' => User::latest()->limit(20)->get(),
            'billingSubmissions' => BillingSubmission::with('company')->latest()->limit(50)->get(),
        ]);
    }

    public function confirmBilling(BillingSubmission $billingSubmission)
    {
        return $this->activateBilling($billingSubmission);
    }

    public function activateBilling(BillingSubmission $billingSubmission)
    {
        BillingSubmission::forCompany($billingSubmission->company_id)
            ->whereKeyNot($billingSubmission->id)
            ->where('status', 'confirmed')
            ->update(['status' => 'stopped']);

        $billingSubmission->update(['status' => 'confirmed']);

        // Update company active plan & expiry
        $company = $billingSubmission->company;
        $endsAt = $billingSubmission->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();

        $company->update([
            'active_plan' => $billingSubmission->package,
            'subscription_ends_at' => $endsAt,
        ]);

        return back()->with('success', 'Billing user berhasil diaktifkan.');
    }

    public function stopBilling(BillingSubmission $billingSubmission)
    {
        $billingSubmission->update(['status' => 'stopped']);

        // Clear company active plan & expiry
        $billingSubmission->company->update([
            'active_plan' => null,
            'subscription_ends_at' => null,
        ]);

        return back()->with('success', 'Billing user berhasil dihentikan.');
    }
}
