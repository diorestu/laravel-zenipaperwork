<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->isMethod('post')) {
                $company = $this->user()?->company;
                if ($company && $company->hasReachedClientLimit()) {
                    $validator->errors()->add('limit', 'Limit jumlah klien untuk paket Anda telah tercapai. Silakan upgrade paket Anda.');
                }
            }
        });
    }
}
