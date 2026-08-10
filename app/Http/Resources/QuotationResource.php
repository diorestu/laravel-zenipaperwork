<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'number' => $this->number,
            'issue_date' => $this->issue_date?->toDateString(),
            'valid_until' => $this->valid_until?->toDateString(),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_rate' => (float) $this->tax_rate,
            'tax_total' => (float) $this->tax_total,
            'custom_taxes' => $this->normalized_custom_taxes,
            'discount_type' => $this->discount_type ?? 'fixed',
            'discount_rate' => (float) ($this->discount_rate ?? 0),
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'total' => (float) $this->total,
            'notes' => $this->notes,
            'pdf_url' => route('quotations.pdf', $this->id),
            'client' => new ClientResource($this->whenLoaded('client')),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item): array => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])),
            'payment_terms' => $this->whenLoaded('paymentTerms', fn () => $this->paymentTerms->map(fn ($term): array => [
                'id' => $term->id,
                'term_number' => $term->term_number,
                'label' => $term->label,
                'amount' => (float) $term->amount,
                'due_date' => $term->due_date?->toDateString(),
            ])),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
