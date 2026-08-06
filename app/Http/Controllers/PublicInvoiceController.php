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

    public function pay(string $token, \App\Services\PakasirPaymentGateway $gateway)
    {
        $invoice = Invoice::with(['company'])->where('public_token', $token)->firstOrFail();

        if (! in_array($invoice->status, ['sent', 'partial'])) {
            return back()->with('error', 'Tagihan ini tidak dapat dibayar saat ini.');
        }

        if (! $invoice->company->pakasir_project_id || ! $invoice->company->pakasir_api_key) {
            return back()->with('error', 'Perusahaan belum mengkonfigurasi Payment Gateway.');
        }

        if (! $invoice->payment_url) {
            try {
                $gateway->createQrisForInvoice($invoice);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }
        }

        return redirect()->away($invoice->payment_url);
    }
}
