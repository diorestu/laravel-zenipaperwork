<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'client_id' => Client::factory(),
            'number' => 'QUO-'.fake()->unique()->numerify('####'),
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'status' => 'draft',
            'subtotal' => 100000,
            'tax_rate' => 0,
            'tax_total' => 0,
            'total' => 100000,
        ];
    }
}
