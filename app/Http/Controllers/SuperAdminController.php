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

        return back()->with('success', 'Billing user berhasil diaktifkan.');
    }

    public function stopBilling(BillingSubmission $billingSubmission)
    {
        $billingSubmission->update(['status' => 'stopped']);

        return back()->with('success', 'Billing user berhasil dihentikan.');
    }
}
