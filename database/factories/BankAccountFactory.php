<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'bank_name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
            'account_name' => fake()->company(),
            'account_number' => fake()->numerify('##########'),
            'branch' => fake()->city(),
            'currency' => 'IDR',
            'is_primary' => false,
            'is_active' => true,
        ];
    }
}
