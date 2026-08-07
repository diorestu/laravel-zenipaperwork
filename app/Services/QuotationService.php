<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(private readonly DocumentCalculator $calculator)
    {
    }

    public function create(User $user, array $data): Quotation
    {
        return DB::transaction(function () use ($user, $data): Quotation {
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $customTaxes = $data['custom_taxes'] ?? [];
            $totals = $this->calculator->totals($data['items'], $taxRate, 0, $customTaxes);

            $quotation = Quotation::create([
                'company_id' => $user->company_id,
                'client_id' => $data['client_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'tax_rate' => $taxRate,
                'custom_taxes' => $totals['custom_taxes'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
            ]);
            $quotation->items()->createMany($totals['items']);

            return $quotation->load(['client', 'items']);
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data): Quotation {
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $customTaxes = $data['custom_taxes'] ?? [];
            $totals = $this->calculator->totals($data['items'], $taxRate, 0, $customTaxes);

            $quotation->update([
                'client_id' => $data['client_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => $data['status'] ?? $quotation->status,
                'tax_rate' => $taxRate,
                'custom_taxes' => $totals['custom_taxes'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
            ]);
            $quotation->items()->delete();
            $quotation->items()->createMany($totals['items']);

            return $quotation->refresh()->load(['client', 'items']);
        });
    }
}
