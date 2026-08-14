<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'quotation_id' => $this->quotation_id,
            'number' => $this->number,
            'issue_date' => $this->issue_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_rate' => (float) $this->tax_rate,
            'tax_total' => (float) $this->tax_total,
            'pph_rate' => (float) $this->pph_rate,
            'pph_amount' => (float) $this->pph_amount,
            'custom_taxes' => $this->normalized_custom_taxes,
            'discount_type' => $this->discount_type ?? 'fixed',
            'discount_rate' => (float) ($this->discount_rate ?? 0),
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'total' => (float) $this->total,
            'down_payment_amount' => (float) $this->down_payment_amount,
            'amount_paid' => (float) $this->amount_paid,
            'credit_note_total' => (float) $this->credit_note_total,
            'balance_due' => (float) $this->balance_due,
            'is_overdue' => (bool) $this->is_overdue,
            'notes' => $this->notes,
            'public_url' => route('public.invoices.show', $this->public_token),
            'pdf_url' => route('api.invoices.pdf', $this->id),
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
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($payment): array => [
                'id' => $payment->id,
                'payment_term_id' => $payment->payment_term_id,
                'payment_date' => $payment->payment_date?->toDateString(),
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
                'reference' => $payment->reference,
                'notes' => $payment->notes,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
