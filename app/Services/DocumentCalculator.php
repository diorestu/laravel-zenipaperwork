<?php

namespace App\Services;

class DocumentCalculator
{
    public function totals(array $items, float $taxRate): array
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
        $taxTotal = round($subtotal * ($taxRate / 100), 2);

        return [
            'items' => $normalized->all(),
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'total' => round($subtotal + $taxTotal, 2),
        ];
    }
}
