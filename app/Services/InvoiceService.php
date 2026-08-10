<?php

namespace App\Services;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Models\InvoicePayment;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(private readonly DocumentCalculator $calculator)
    {
    }

    public function create(User $user, array $data): Invoice
    {
        return DB::transaction(function () use ($user, $data): Invoice {
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $pphRate = (float) ($data['pph_rate'] ?? 0);
            $customTaxes = $data['custom_taxes'] ?? [];
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $discountRate = (float) ($data['discount_rate'] ?? 0);
            $discountType = (string) ($data['discount_type'] ?? 'fixed');
            $totals = $this->calculator->totals(
                $data['items'],
                $taxRate,
                $pphRate,
                $customTaxes,
                $discountAmount,
                $discountRate,
                $discountType
            );
            $terms = $this->normalizePaymentTerms($data['payment_terms'] ?? []);
            $downPayment = (float) ($terms[0]['amount'] ?? ($data['down_payment_amount'] ?? 0));
            $pphAmount = $totals['pph_amount'];
            $isRecurring = $data['is_recurring'] ?? false;
            $recurringCycle = $data['recurring_cycle'] ?? null;
            $nextRecurrenceDate = null;
            if ($isRecurring && $recurringCycle) {
                $nextRecurrenceDate = $recurringCycle === 'monthly'
                    ? \Carbon\Carbon::parse($data['issue_date'])->addMonth()
                    : \Carbon\Carbon::parse($data['issue_date'])->addYear();
            }

            $number = ! empty($data['number'])
                ? $data['number']
                : ($user->company ? $user->company->generateNextInvoiceNumber(\Carbon\Carbon::parse($data['issue_date'] ?? now()), true) : 'INV-'.now()->format('Ymd-His'));

            if (! empty($data['number']) && $user->company) {
                $user->company->increment('invoice_next_number');
            }

            $invoice = Invoice::create([
                'company_id' => $user->company_id,
                'client_id' => $data['client_id'],
                'quotation_id' => $data['quotation_id'] ?? null,
                'number' => $number,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'invoice_type' => $data['invoice_type'] ?? 'standard',
                'parent_invoice_id' => $data['parent_invoice_id'] ?? null,
                'tax_rate' => $taxRate,
                'pph_rate' => $pphRate,
                'pph_amount' => $pphAmount,
                'custom_taxes' => $totals['custom_taxes'],
                'discount_type' => $totals['discount_type'],
                'discount_rate' => $totals['discount_rate'],
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'down_payment_amount' => $downPayment,
                'profit_total' => $this->profitFor($totals['total'], $pphAmount, 0, $downPayment),
                'notes' => $data['notes'] ?? null,
                'is_recurring' => $isRecurring,
                'recurring_cycle' => $recurringCycle,
                'next_recurrence_date' => $nextRecurrenceDate,
                'bank_account_id' => $data['bank_account_id'] ?? null,
            ]);
            $invoice->items()->createMany($totals['items']);
            $this->syncPaymentTerms($invoice, $terms);

            return $invoice->load(['client', 'items', 'payments', 'paymentTerms', 'creditNotes', 'expenses']);
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data): Invoice {
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $pphRate = (float) ($data['pph_rate'] ?? 0);
            $customTaxes = $data['custom_taxes'] ?? [];
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $discountRate = (float) ($data['discount_rate'] ?? 0);
            $discountType = (string) ($data['discount_type'] ?? 'fixed');
            $totals = $this->calculator->totals(
                $data['items'],
                $taxRate,
                $pphRate,
                $customTaxes,
                $discountAmount,
                $discountRate,
                $discountType
            );
            $terms = $this->normalizePaymentTerms($data['payment_terms'] ?? []);
            $downPayment = (float) ($terms[0]['amount'] ?? ($data['down_payment_amount'] ?? 0));
            $pphAmount = $totals['pph_amount'];
            $isRecurring = $data['is_recurring'] ?? false;
            $recurringCycle = $data['recurring_cycle'] ?? null;
            $nextRecurrenceDate = null;
            if ($isRecurring && $recurringCycle) {
                $nextRecurrenceDate = $recurringCycle === 'monthly'
                    ? \Carbon\Carbon::parse($data['issue_date'])->addMonth()
                    : \Carbon\Carbon::parse($data['issue_date'])->addYear();
            }

            $invoice->update([
                'client_id' => $data['client_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'status' => $data['status'] ?? $invoice->status,
                'invoice_type' => $data['invoice_type'] ?? $invoice->invoice_type ?? 'standard',
                'parent_invoice_id' => $data['parent_invoice_id'] ?? $invoice->parent_invoice_id,
                'tax_rate' => $taxRate,
                'pph_rate' => $pphRate,
                'pph_amount' => $pphAmount,
                'custom_taxes' => $totals['custom_taxes'],
                'discount_type' => $totals['discount_type'],
                'discount_rate' => $totals['discount_rate'],
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'down_payment_amount' => $downPayment,
                'profit_total' => $this->profitFor($totals['total'], $pphAmount, (float) $invoice->expense_total, $downPayment),
                'notes' => $data['notes'] ?? null,
                'is_recurring' => $isRecurring,
                'recurring_cycle' => $recurringCycle,
                'next_recurrence_date' => $nextRecurrenceDate,
                'bank_account_id' => $data['bank_account_id'] ?? null,
            ]);
            $invoice->items()->delete();
            $invoice->items()->createMany($totals['items']);
            $this->syncPaymentTerms($invoice, $terms);

            return $invoice->refresh()->load(['client', 'items', 'payments', 'paymentTerms', 'creditNotes', 'expenses']);
        });
    }

    public function convertQuotation(Quotation $quotation, string $number): Invoice
    {
        return DB::transaction(function () use ($quotation, $number): Invoice {
            $pphAmount = round((float) $quotation->subtotal * ((float) $quotation->tax_rate / 100), 2);

            $invoice = Invoice::create([
                'company_id' => $quotation->company_id,
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'number' => $number,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => 'sent',
                'sent_at' => now(),
                'tax_rate' => $quotation->tax_rate,
                'pph_rate' => 0,
                'pph_amount' => $pphAmount,
                'custom_taxes' => $quotation->custom_taxes,
                'discount_type' => $quotation->discount_type ?? 'fixed',
                'discount_rate' => $quotation->discount_rate ?? 0,
                'discount_amount' => $quotation->discount_amount ?? 0,
                'subtotal' => $quotation->subtotal,
                'tax_total' => $quotation->tax_total,
                'total' => $quotation->total,
                'down_payment_amount' => 0,
                'profit_total' => $this->profitFor((float) $quotation->total, $pphAmount, 0, 0),
                'notes' => $quotation->notes,
            ]);

            $invoice->items()->createMany(
                $quotation->items->map(fn ($item): array => [
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ])->all()
            );

            $quotation->load('paymentTerms');
            if ($quotation->paymentTerms->isNotEmpty()) {
                $terms = $this->normalizePaymentTerms($quotation->paymentTerms->map(fn ($t) => [
                    'label' => $t->label,
                    'amount' => (float) $t->amount,
                    'due_date' => $t->due_date?->format('Y-m-d'),
                ])->all());
                $this->syncPaymentTerms($invoice, $terms);
            }

            $quotation->update(['status' => 'converted']);

            return $invoice->load(['client', 'items', 'paymentTerms', 'creditNotes', 'expenses']);
        });
    }

    private function normalizePaymentTerms(array $terms): array
    {
        return collect($terms)
            ->filter(fn (array $term): bool => (float) ($term['amount'] ?? 0) > 0)
            ->values()
            ->map(fn (array $term, int $index): array => [
                'term_number' => $index + 1,
                'label' => $term['label'] ?: 'Termin '.($index + 1),
                'amount' => (float) $term['amount'],
                'due_date' => $term['due_date'] ?? null,
            ])
            ->all();
    }

    private function syncPaymentTerms(Invoice $invoice, array $terms): void
    {
        $invoice->paymentTerms()->delete();

        if ($terms === []) {
            return;
        }

        $invoice->paymentTerms()->createMany($terms);
    }

    public function recordPayment(Invoice $invoice, array $data): InvoicePayment
    {
        return DB::transaction(function () use ($invoice, $data): InvoicePayment {
            $payment = $invoice->payments()->create($data);
            $invoice = $this->refreshAndDeriveStatus($invoice);

            return $payment;
        });
    }

    public function markAsSent(Invoice $invoice): Invoice
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => $invoice->sent_at ?? now(),
        ]);

        return $invoice;
    }

    public function recordExpense(Invoice $invoice, array $data): InvoiceExpense
    {
        return DB::transaction(function () use ($invoice, $data): InvoiceExpense {
            $expense = $invoice->expenses()->create($data);
            $this->recalculateExpenseTotals($invoice);

            return $expense;
        });
    }

    public function deleteExpense(InvoiceExpense $expense): void
    {
        DB::transaction(function () use ($expense): void {
            $invoice = $expense->invoice;
            $expense->delete();
            $this->recalculateExpenseTotals($invoice);
        });
    }

    public function applyCreditNote(Invoice $invoice, array $data): CreditNote
    {
        return DB::transaction(function () use ($invoice, $data): CreditNote {
            $note = $invoice->creditNotes()->create([
                'company_id' => $invoice->company_id,
                'client_id' => $invoice->client_id,
                'number' => $data['number'] ?? null,
                'issue_date' => $data['issue_date'],
                'amount' => $data['amount'],
                'reason' => $data['reason'] ?? null,
                'status' => 'applied',
                'notes' => $data['notes'] ?? null,
            ]);
            $this->refreshAndDeriveStatus($invoice);

            return $note;
        });
    }

    public function voidCreditNote(CreditNote $note): CreditNote
    {
        return DB::transaction(function () use ($note): CreditNote {
            $note->update(['status' => 'void']);
            $this->refreshAndDeriveStatus($note->invoice);

            return $note;
        });
    }

    private function refreshAndDeriveStatus(Invoice $invoice): Invoice
    {
        $invoice->refresh();
        $paid = (float) $invoice->amount_paid;
        $creditNote = (float) $invoice->credit_note_total;
        $total = (float) $invoice->total;

        $newStatus = match (true) {
            $paid + $creditNote >= $total && $total > 0 => 'paid',
            $paid > 0 || $creditNote > 0 => 'partial',
            default => $invoice->status,
        };

        $invoice->update(['status' => $newStatus]);

        return $invoice;
    }

    private function recalculateExpenseTotals(Invoice $invoice): void
    {
        $expenseTotal = (float) $invoice->expenses()->sum('amount');
        $invoice->update([
            'expense_total' => $expenseTotal,
            'profit_total' => $this->profitFor(
                (float) $invoice->total,
                (float) $invoice->pph_amount,
                $expenseTotal,
                (float) $invoice->down_payment_amount,
            ),
        ]);
    }

    private function profitFor(float $total, float $pphAmount, float $expenseTotal, float $downPayment): float
    {
        return round($total - $pphAmount - $expenseTotal - $downPayment, 2);
    }
}
