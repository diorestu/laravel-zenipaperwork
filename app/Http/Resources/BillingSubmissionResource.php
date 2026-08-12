<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package' => $this->package,
            'billing_period' => $this->billing_period,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'payment_gateway' => $this->payment_gateway,
            'payment_order_id' => $this->payment_order_id,
            'payment_number' => $this->payment_number,
            'payment_url' => $this->payment_url,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
