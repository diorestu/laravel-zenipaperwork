<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoicePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        return [
            'term_number' => ['nullable', 'integer', 'min:1'],
            'term_label' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof' => ['nullable', 'file', 'max:4096'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $invoice = $this->route('invoice');
            if (! $invoice) {
                return;
            }

            $balanceDue = (float) $invoice->balance_due;
            $amount = (float) $this->input('amount', 0);
            $termNumber = $this->input('term_number');

            if ($amount > $balanceDue) {
                $validator->errors()->add(
                    'amount',
                    'Pembayaran tidak boleh melebihi sisa tagihan (Rp '.number_format($balanceDue, 0, ',', '.').').'
                );
            }

            if (! $termNumber || ! $invoice->paymentTerms()->exists()) {
                return;
            }

            $term = $invoice->paymentTerms()->where('term_number', (int) $termNumber)->first();
            $termDue = $term
                ? max((float) $term->amount - (float) $invoice->payments()->where('term_number', $term->term_number)->sum('amount'), 0)
                : null;

            if ($termDue === null || $termDue <= 0) {
                $validator->errors()->add('term_number', 'Termin pembayaran tidak tersedia.');

                return;
            }

            if ($amount > $termDue) {
                $validator->errors()->add(
                    'amount',
                    'Pembayaran tidak boleh melebihi sisa '.strtolower((string) $this->input('term_label', 'termin')).' (Rp '.number_format($termDue, 0, ',', '.').').'
                );
            }
        });
    }
}
