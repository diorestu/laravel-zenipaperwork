<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSubmission extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $fillable = [
        'company_id',
        'package',
        'amount',
        'payment_method',
        'payment_gateway',
        'payment_order_id',
        'payment_number',
        'payment_url',
        'payment_payload',
        'proof_path',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_payload' => 'array',
        ];
    }
}
