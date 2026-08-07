<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'client_id' => ['required', Rule::exists('clients', 'id')->where('company_id', $companyId)],
            'number' => ['required', 'string', 'max:100'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['nullable', Rule::in(['draft', 'sent', 'partial', 'paid', 'void'])],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pph_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'custom_taxes' => ['nullable', 'array', 'max:10'],
            'custom_taxes.*.name' => ['nullable', 'string', 'max:100'],
            'custom_taxes.*.rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'custom_taxes.*.type' => ['nullable', 'string', 'in:addition,deduction'],
            'discount_type' => ['nullable', 'string', 'in:fixed,percentage'],
            'discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'down_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_recurring' => ['boolean'],
            'recurring_cycle' => ['nullable', 'string', Rule::in(['monthly', 'yearly'])],
            'bank_account_id' => ['nullable', Rule::exists('bank_accounts', 'id')->where('company_id', $companyId)],
            'payment_terms' => ['nullable', 'array'],
            'payment_terms.*.label' => ['required_with:payment_terms.*.amount', 'nullable', 'string', 'max:100'],
            'payment_terms.*.amount' => ['required_with:payment_terms.*.label', 'nullable', 'numeric', 'min:0.01'],
            'payment_terms.*.due_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->isMethod('post')) {
                $company = $this->user()?->company;
                if ($company && $company->hasReachedInvoiceLimit()) {
                    $validator->errors()->add('limit', 'Limit jumlah invoice untuk paket Anda telah tercapai. Silakan upgrade paket Anda.');
                }
            }

            $subtotal = collect($this->input('items', []))->sum(function (array $item): float {
                return (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
            });
            $taxRate = (float) $this->input('tax_rate', 0);
            $grandTotal = $subtotal + ($subtotal * $taxRate / 100);
            $dp = (float) $this->input('down_payment_amount', 0);
            $paymentTerms = collect($this->input('payment_terms', []))
                ->filter(fn (array $term): bool => (float) ($term['amount'] ?? 0) > 0);
            $termTotal = $paymentTerms->sum(fn (array $term): float => (float) ($term['amount'] ?? 0));

            if ($dp > $grandTotal) {
                $validator->errors()->add(
                    'down_payment_amount',
                    'DP tidak boleh melebihi total invoice.'
                );
            }

            if ($termTotal > $grandTotal) {
                $validator->errors()->add(
                    'payment_terms',
                    'Total termin tidak boleh melebihi total invoice.'
                );
            }
        });
    }
}
