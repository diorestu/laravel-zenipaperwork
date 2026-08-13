<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCreditNoteRequest;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteController extends Controller
{
    public function create(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        return redirect()->route('invoices.show', ['invoice' => $invoice->id, 'modal' => 'credit-note']);
    }

    public function store(StoreCreditNoteRequest $request, Invoice $invoice, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        $note = $service->applyCreditNote($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Credit note '.$note->number.' diterapkan.');
    }

    public function show(CreditNote $creditNote)
    {
        $this->authorize('view', $creditNote);
        $creditNote->load(['company', 'client', 'invoice']);

        return view('credit-notes.show', ['note' => $creditNote]);
    }

    public function void(CreditNote $creditNote, InvoiceService $service)
    {
        $this->authorize('update', $creditNote);
        $service->voidCreditNote($creditNote);

        return back()->with('success', 'Credit note di-void.');
    }

    public function pdf(CreditNote $creditNote)
    {
        $this->authorize('view', $creditNote);
        $creditNote->load(['company', 'client', 'invoice']);
        $filename = str_replace(['/', '\\'], '-', $creditNote->number) . '.pdf';

        return Pdf::loadView('pdf.credit-note', ['note' => $creditNote])
            ->download($filename);
    }
}
