<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $fillable = ['company_id', 'client_id', 'number', 'issue_date', 'valid_until', 'status', 'subtotal', 'tax_rate', 'tax_total', 'custom_taxes', 'discount_type', 'discount_rate', 'discount_amount', 'total', 'notes'];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'custom_taxes' => 'array',
            'discount_rate' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
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

        return $fallback;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function paymentTerms(): HasMany
    {
        return $this->hasMany(QuotationPaymentTerm::class)->orderBy('term_number');
    }
}
