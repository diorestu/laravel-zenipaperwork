<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoicePaymentRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;

class InvoicePaymentController extends Controller
{
    public function store(StoreInvoicePaymentRequest $request, Invoice $invoice, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        $data = $request->validated();
        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('payment-proofs', 'public');
        }
        unset($data['proof']);

        $service->recordPayment($invoice, $data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment tercatat.');
    }
}
