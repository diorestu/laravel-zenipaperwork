<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_email' => ['nullable', 'email', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'invoice_number_prefix' => ['nullable', 'string', 'max:20'],
            'invoice_number_format' => ['nullable', 'string', 'max:100'],
            'invoice_number_padding' => ['nullable', 'integer', 'min:1', 'max:10'],
            'invoice_next_number' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
