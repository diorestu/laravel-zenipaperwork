<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProcessRecurringInvoices extends Command
{
    protected $signature = 'invoices:recur';
    protected $description = 'Process recurring invoices that are due for renewal';

    public function handle(InvoiceService $service): int
    {
        $today = Carbon::today();
        $invoices = Invoice::where('is_recurring', true)
            ->whereNotNull('next_recurrence_date')
            ->whereDate('next_recurrence_date', '<=', $today->toDateString())
            ->get();

        $count = 0;

        foreach ($invoices as $invoice) {
            $newIssueDate = Carbon::parse($invoice->next_recurrence_date);
            $newDueDate = $invoice->due_date ? $newIssueDate->copy()->addDays($invoice->issue_date->diffInDays($invoice->due_date)) : null;
            
            $nextRecurrenceDate = $invoice->recurring_cycle === 'monthly'
                ? $newIssueDate->copy()->addMonth()
                : $newIssueDate->copy()->addYear();

            $newNumber = $invoice->number . '-' . $newIssueDate->format('Ym');

            $newInvoice = $invoice->replicate(['public_token', 'status', 'sent_at', 'amount_paid', 'credit_note_total', 'balance_due', 'expense_total', 'profit_total', 'parent_invoice_id']);
            $newInvoice->number = $newNumber;
            $newInvoice->issue_date = $newIssueDate;
            $newInvoice->due_date = $newDueDate;
            $newInvoice->status = 'draft';
            $newInvoice->public_token = Str::random(40);
            $newInvoice->parent_invoice_id = $invoice->id;
            // new invoice is also recurring
            $newInvoice->is_recurring = true;
            $newInvoice->recurring_cycle = $invoice->recurring_cycle;
            $newInvoice->next_recurrence_date = $nextRecurrenceDate;
            $newInvoice->save();

            foreach ($invoice->items as $item) {
                $newItem = $item->replicate(['invoice_id']);
                $newInvoice->items()->save($newItem);
            }

            $newInvoice->recalculateTotals();

            // Stop the old invoice from recurring further, or just let it be parent
            $invoice->update([
                'is_recurring' => false,
                'next_recurrence_date' => null,
            ]);

            $count++;
        }

        $this->info("Processed {$count} recurring invoices.");
        return Command::SUCCESS;
    }
}
