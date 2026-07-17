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
        'subtotal', 'tax_rate', 'tax_total', 'pph_rate', 'pph_amount',
        'total', 'down_payment_amount', 'expense_total', 'profit_total',
        'notes',
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
            'total' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'expense_total' => 'decimal:2',
            'profit_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice): void {
            $invoice->public_token ??= Str::random(40);
        });
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

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(InvoiceExpense::class);
    }

    public function getAmountPaidAttribute(): string
    {
        return number_format((float) $this->payments()->sum('amount'), 2, '.', '');
    }

    public function getCreditNoteTotalAttribute(): string
    {
        return number_format((float) $this->creditNotes()->where('status', 'applied')->sum('amount'), 2, '.', '');
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
        $paid = (float) $this->amount_paid;
        $creditNote = (float) $this->credit_note_total;
        $total = (float) $this->total;

        return number_format(max($total - $paid - $creditNote, 0), 2, '.', '');
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

    public function scopeDueForReminder(Builder $query): Builder
    {
        return $query->overdue()
            ->where(function (Builder $q): void {
                $q->whereNull('last_reminder_at')
                    ->orWhereDate('last_reminder_at', '<', now()->toDateString());
            });
    }
}
