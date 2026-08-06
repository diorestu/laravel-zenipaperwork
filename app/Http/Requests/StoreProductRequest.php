<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $price = preg_replace('/[^0-9]/', '', (string) $this->price);
            $this->merge(['price' => $price !== '' ? $price : 0]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->isMethod('post')) {
                $company = $this->user()?->company;
                if ($company && $company->hasReachedProductLimit()) {
                    $validator->errors()->add('limit', 'Limit jumlah produk untuk paket Anda telah tercapai. Silakan upgrade paket Anda.');
                }
            }
        });
    }
}
