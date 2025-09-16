<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Office',
                'address' => '123 Business Ave',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
            ],
            [
                'name' => 'Warehouse A',
                'address' => '456 Industrial Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90210',
                'country' => 'USA',
            ],
            [
                'name' => 'Remote Office',
                'address' => '789 Tech Drive',
                'city' => 'Austin',
                'state' => 'TX',
                'zip_code' => '73301',
                'country' => 'USA',
            ],
            [
                'name' => 'Data Center',
                'address' => '321 Server Street',
                'city' => 'Seattle',
                'state' => 'WA',
                'zip_code' => '98101',
                'country' => 'USA',
            ],
            [
                'name' => 'Branch Office',
                'address' => '654 Commerce Way',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60601',
                'country' => 'USA',
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}