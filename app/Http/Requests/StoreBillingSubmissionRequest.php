<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillingSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageCompany() ?? false;
    }

    public function rules(): array
    {
        return [
            'package' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:100', 'in:qris,manual_transfer'],
            'proof' => ['nullable', 'file', 'max:4096'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
