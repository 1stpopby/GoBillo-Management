<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create or get a test company
        $company = Company::firstOrCreate([
            'email' => 'test@company.com'
        ], [
            'name' => 'Test Company',
            'phone' => '123-456-7890',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'TS',
            'zip_code' => '12345',
            'country' => 'Test Country',
            'website' => 'https://testcompany.com'
        ]);

        // Create test admin user
        User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Test Admin',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_COMPANY_ADMIN,
            'company_id' => $company->id,
            'is_active' => true
        ]);

        echo "Test user created: admin@test.com / password123\n";
    }
}
