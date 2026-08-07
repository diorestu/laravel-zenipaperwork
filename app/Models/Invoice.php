<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $fillable = [
        'company_id', 'client_id', 'quotation_id', 'number', 'public_token',
        'issue_date', 'due_date', 'sent_at', 'last_reminder_at', 'status',
        'subtotal', 'tax_rate', 'tax_total', 'pph_rate', 'pph_amount', 'custom_taxes',
        'discount_type', 'discount_rate', 'discount_amount',
        'total', 'down_payment_amount', 'expense_total', 'profit_total',
        'amount_paid', 'credit_note_total', 'balance_due', 'notes',
        'is_recurring', 'recurring_cycle', 'next_recurrence_date', 'parent_invoice_id',
        'payment_order_id', 'payment_number', 'payment_url', 'bank_account_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
            'last_reminder_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'pph_rate' => 'decimal:2',
            'pph_amount' => 'decimal:2',
            'custom_taxes' => 'array',
            'discount_rate' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'expense_total' => 'decimal:2',
            'profit_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'credit_note_total' => 'decimal:2',
            'balance_due' => 'decimal:2',
            'is_recurring' => 'boolean',
            'next_recurrence_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice): void {
            $invoice->public_token ??= Str::random(40);
        });

        static::created(function (Invoice $invoice) {
            if (! $invoice->payment_order_id) {
                $invoice->updateQuietly([
                    'payment_order_id' => 'INV-'.str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });

        static::saved(function (Invoice $invoice): void {
            $invoice->recalculateTotals();
        });
    }

    public function recalculateTotals(): void
    {
        $paid = (float) $this->payments()->sum('amount');
        $credit = (float) $this->creditNotes()->where('status', 'applied')->sum('amount');
        $total = (float) $this->total;
        $balanceDue = max($total - $paid - $credit, 0);

        $newStatus = match (true) {
            $paid + $credit >= $total && $total > 0 => 'paid',
            $paid > 0 || $credit > 0 => 'partial',
            default => $this->status,
        };

        $this->updateQuietly([
            'amount_paid' => $paid,
            'credit_note_total' => $credit,
            'balance_due' => $balanceDue,
            'status' => $newStatus,
        ]);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function paymentTerms(): HasMany
    {
        return $this->hasMany(InvoicePaymentTerm::class)->orderBy('term_number');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(InvoiceExpense::class);
    }

    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function childInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function getAmountPaidAttribute(): string
    {
        return number_format((float) ($this->attributes['amount_paid'] ?? 0), 2, '.', '');
    }

    public function getCreditNoteTotalAttribute(): string
    {
        return number_format((float) ($this->attributes['credit_note_total'] ?? 0), 2, '.', '');
    }

    public function getDownPaymentPaidAttribute(): string
    {
        $paid = (float) $this->amount_paid;
        $dp = (float) $this->down_payment_amount;

        return number_format(min($paid, $dp), 2, '.', '');
    }

    public function getDownPaymentRemainingAttribute(): string
    {
        $paid = (float) $this->amount_paid;
        $dp = (float) $this->down_payment_amount;

        return number_format(max($dp - $paid, 0), 2, '.', '');
    }

    public function getBalanceDueAttribute(): string
    {
        return number_format((float) ($this->attributes['balance_due'] ?? 0), 2, '.', '');
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['paid', 'void', 'draft'], true)) {
            return false;
        }

        return $this->due_date !== null && $this->due_date->isPast();
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['sent', 'partial']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->unpaid()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString());
    }

    public function getNormalizedCustomTaxesAttribute(): array
    {
        $taxes = $this->custom_taxes;
        if (is_array($taxes) && count($taxes) > 0) {
            return collect($taxes)->map(function ($tax) {
                $subtotal = (float) $this->subtotal;
                $rate = (float) ($tax['rate'] ?? 0);
                return [
                    'name' => (string) ($tax['name'] ?? 'Pajak'),
                    'rate' => $rate,
                    'type' => (string) ($tax['type'] ?? 'addition'),
                    'amount' => (float) ($tax['amount'] ?? round($subtotal * ($rate / 100), 2)),
                ];
            })->all();
        }

        $fallback = [];
        $subtotal = (float) $this->subtotal;
        if ((float) $this->tax_rate > 0) {
            $fallback[] = [
                'name' => 'PPN',
                'rate' => (float) $this->tax_rate,
                'type' => 'addition',
                'amount' => (float) ($this->tax_total ?? round($subtotal * ($this->tax_rate / 100), 2)),
            ];
        }
        if ((float) $this->pph_rate > 0) {
            $fallback[] = [
                'name' => 'PPh',
                'rate' => (float) $this->pph_rate,
                'type' => 'deduction',
                'amount' => (float) ($this->pph_amount ?? round($subtotal * ($this->pph_rate / 100), 2)),
            ];
        }

        return $fallback;
    }

    public function scopeDueForReminder(Builder $query): Builder
    {
        return $query->overdue()
            ->where(function (Builder $q): void {
                $q->whereNull('last_reminder_at')
                    ->orWhereDate('last_reminder_at', '<', now()->toDateString());
            });
    }
}
