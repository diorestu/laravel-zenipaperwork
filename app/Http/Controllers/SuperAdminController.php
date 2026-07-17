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
            'billingSubmissions' => BillingSubmission::with('company')->latest()->limit(20)->get(),
        ]);
    }

    public function confirmBilling(BillingSubmission $billingSubmission)
    {
        $billingSubmission->update(['status' => 'confirmed']);

        return back()->with('success', 'Billing dikonfirmasi.');
    }
}
