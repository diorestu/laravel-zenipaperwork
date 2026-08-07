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
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $discountRate = (float) ($data['discount_rate'] ?? 0);
            $discountType = (string) ($data['discount_type'] ?? 'fixed');
            $totals = $this->calculator->totals(
                $data['items'],
                $taxRate,
                0,
                $customTaxes,
                $discountAmount,
                $discountRate,
                $discountType
            );
            $terms = $this->normalizePaymentTerms($data['payment_terms'] ?? []);

            $quotation = Quotation::create([
                'company_id' => $user->company_id,
                'client_id' => $data['client_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'tax_rate' => $taxRate,
                'custom_taxes' => $totals['custom_taxes'],
                'discount_type' => $totals['discount_type'],
                'discount_rate' => $totals['discount_rate'],
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
            ]);
            $quotation->items()->createMany($totals['items']);
            $this->syncPaymentTerms($quotation, $terms);

            return $quotation->load(['client', 'items', 'paymentTerms']);
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data): Quotation {
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $customTaxes = $data['custom_taxes'] ?? [];
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $discountRate = (float) ($data['discount_rate'] ?? 0);
            $discountType = (string) ($data['discount_type'] ?? 'fixed');
            $totals = $this->calculator->totals(
                $data['items'],
                $taxRate,
                0,
                $customTaxes,
                $discountAmount,
                $discountRate,
                $discountType
            );
            $terms = $this->normalizePaymentTerms($data['payment_terms'] ?? []);

            $quotation->update([
                'client_id' => $data['client_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => $data['status'] ?? $quotation->status,
                'tax_rate' => $taxRate,
                'custom_taxes' => $totals['custom_taxes'],
                'discount_type' => $totals['discount_type'],
                'discount_rate' => $totals['discount_rate'],
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
            ]);
            $quotation->items()->delete();
            $quotation->items()->createMany($totals['items']);
            $this->syncPaymentTerms($quotation, $terms);

            return $quotation->refresh()->load(['client', 'items', 'paymentTerms']);
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

    private function syncPaymentTerms(Quotation $quotation, array $terms): void
    {
        $quotation->paymentTerms()->delete();

        if ($terms === []) {
            return;
        }

        $quotation->paymentTerms()->createMany($terms);
    }
}
