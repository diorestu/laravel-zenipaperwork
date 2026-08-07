<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
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
            'valid_until' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['nullable', Rule::in(['draft', 'sent', 'approved', 'rejected', 'converted'])],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'custom_taxes' => ['nullable', 'array', 'max:10'],
            'custom_taxes.*.name' => ['nullable', 'string', 'max:100'],
            'custom_taxes.*.rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'custom_taxes.*.type' => ['nullable', 'string', 'in:addition,deduction'],
            'notes' => ['nullable', 'string'],
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
                if ($company && $company->hasReachedQuotationLimit()) {
                    $validator->errors()->add('limit', 'Limit jumlah penawaran untuk paket Anda telah tercapai. Silakan upgrade paket Anda.');
                }
            }
        });
    }
}
