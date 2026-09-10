<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
    use SoftDeletes;
    protected $fillable = ['invoice_id', 'term_number', 'term_label', 'amount', 'paid_at', 'method', 'reference', 'proof_path', 'notes'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (InvoicePayment $payment) => $payment->invoice?->recalculateTotals());
        static::deleted(fn (InvoicePayment $payment) => $payment->invoice?->recalculateTotals());
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
