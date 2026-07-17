<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceExpenseRequest;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Services\InvoiceService;

class InvoiceExpenseController extends Controller
{
    public function store(StoreInvoiceExpenseRequest $request, Invoice $invoice, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        $data = $request->validated();
        $data['company_id'] = $invoice->company_id;
        $service->recordExpense($invoice, $data);

        return back()->with('success', 'Biaya tercatat.');
    }

    public function destroy(Invoice $invoice, InvoiceExpense $expense, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        abort_unless($expense->invoice_id === $invoice->id, 404);
        $service->deleteExpense($expense);

        return back()->with('success', 'Biaya dihapus.');
    }
}
