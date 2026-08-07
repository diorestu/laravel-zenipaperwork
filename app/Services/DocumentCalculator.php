<?php

namespace App\Services;

class DocumentCalculator
{
    public function totals(array $items, float $taxRate = 0, float $pphRate = 0, array $customTaxes = []): array
    {
        $normalized = collect($items)
            ->filter(fn (array $item): bool => filled($item['description'] ?? null))
            ->map(function (array $item): array {
                $quantity = (float) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                return [
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => round($quantity * $unitPrice, 2),
                ];
            })
            ->values();

        $subtotal = round($normalized->sum('line_total'), 2);

        $calculatedTaxes = [];
        $taxTotal = 0;
        $pphAmount = 0;

        if (! empty($customTaxes)) {
            foreach ($customTaxes as $tax) {
                $name = trim((string) ($tax['name'] ?? 'Pajak'));
                $rate = max((float) ($tax['rate'] ?? 0), 0);
                $type = in_array($tax['type'] ?? '', ['addition', 'deduction'], true) ? $tax['type'] : 'addition';

                if ($name === '' || $rate <= 0) {
                    continue;
                }

                $amount = round($subtotal * ($rate / 100), 2);
                if ($type === 'addition') {
                    $taxTotal += $amount;
                } else {
                    $pphAmount += $amount;
                }

                $calculatedTaxes[] = [
                    'name' => $name,
                    'rate' => $rate,
                    'type' => $type,
                    'amount' => $amount,
                ];
            }
        } else {
            if ($taxRate > 0) {
                $taxTotal = round($subtotal * ($taxRate / 100), 2);
                $calculatedTaxes[] = [
                    'name' => 'PPN',
                    'rate' => $taxRate,
                    'type' => 'addition',
                    'amount' => $taxTotal,
                ];
            }
            if ($pphRate > 0) {
                $pphAmount = round($subtotal * ($pphRate / 100), 2);
                $calculatedTaxes[] = [
                    'name' => 'PPh',
                    'rate' => $pphRate,
                    'type' => 'deduction',
                    'amount' => $pphAmount,
                ];
            }
        }

        $taxTotal = round($taxTotal, 2);
        $pphAmount = round($pphAmount, 2);
        $total = max(round($subtotal + $taxTotal - $pphAmount, 2), 0);

        return [
            'items' => $normalized->all(),
            'subtotal' => $subtotal,
            'custom_taxes' => $calculatedTaxes,
            'tax_total' => $taxTotal,
            'pph_amount' => $pphAmount,
            'total' => $total,
        ];
    }
}
