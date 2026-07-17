<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class PublicInvoiceController extends Controller
{
    public function show(string $token)
    {
        $invoice = Invoice::with(['company', 'client', 'items', 'payments'])->where('public_token', $token)->firstOrFail();

        return view('invoices.public', compact('invoice'));
    }
}
