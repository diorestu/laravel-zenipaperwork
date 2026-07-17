<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillingSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'package' => 'business',
            'amount' => 149000,
            'payment_method' => 'manual_transfer',
            'status' => 'pending',
        ];
    }
}
