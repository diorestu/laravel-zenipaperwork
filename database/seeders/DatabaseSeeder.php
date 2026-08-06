<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['email' => 'admin@paperwork.local'],
            [
                'name' => 'Paperwork Demo Company',
                'phone' => '081234567890',
                'address' => 'Makassar, Indonesia',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@paperwork.local'],
            [
                'company_id' => $company->id,
                'name' => 'Admin Paperwork',
                'password' => 'password',
                'role' => 'owner',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@paperwork.local'],
            [
                'company_id' => null,
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => 'super_admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
