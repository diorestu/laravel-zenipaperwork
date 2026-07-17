<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        return [
            'number' => ['nullable', 'string', 'max:100'],
            'issue_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
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

            $max = (float) $invoice->total;
            $amount = (float) $this->input('amount', 0);

            if ($amount > $max) {
                $validator->errors()->add(
                    'amount',
                    'Credit note tidak boleh melebihi total invoice (Rp '.number_format($max, 0, ',', '.').').'
                );
            }
        });
    }
}
