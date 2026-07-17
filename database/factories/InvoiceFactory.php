<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'client_id' => Client::factory(),
            'number' => 'INV-'.fake()->unique()->numerify('####'),
            'public_token' => Str::random(40),
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'sent',
            'subtotal' => 100000,
            'tax_rate' => 0,
            'tax_total' => 0,
            'total' => 100000,
        ];
    }
}
