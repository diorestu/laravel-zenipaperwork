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

            if ($amount > $balanceDue) {
                $validator->errors()->add(
                    'amount',
                    'Pembayaran tidak boleh melebihi sisa tagihan (Rp '.number_format($balanceDue, 0, ',', '.').').'
                );
            }
        });
    }
}
