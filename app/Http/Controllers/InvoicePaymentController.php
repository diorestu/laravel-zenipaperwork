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

        if ($invoice->paymentTerms()->exists() && isset($data['term_number'])) {
            $term = $invoice->paymentTerms()->where('term_number', (int) $data['term_number'])->first();
            $data['term_label'] = $term?->label;
        } else {
            unset($data['term_number'], $data['term_label']);
        }

        $service->recordPayment($invoice, $data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Pembayaran tercatat.');
    }
}
