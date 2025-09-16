<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'John Smith',
                'email' => 'business@apple.com',
                'phone' => '+1-800-275-2273',
                'website' => 'https://www.apple.com',
            ],
            [
                'name' => 'Dell Technologies',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sales@dell.com',
                'phone' => '+1-800-289-3355',
                'website' => 'https://www.dell.com',
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Mike Wilson',
                'email' => 'business@hp.com',
                'phone' => '+1-800-474-6836',
                'website' => 'https://www.hp.com',
            ],
            [
                'name' => 'Lenovo',
                'contact_person' => 'Lisa Chen',
                'email' => 'support@lenovo.com',
                'phone' => '+1-855-253-6686',
                'website' => 'https://www.lenovo.com',
            ],
            [
                'name' => 'Microsoft Corporation',
                'contact_person' => 'David Brown',
                'email' => 'business@microsoft.com',
                'phone' => '+1-800-642-7676',
                'website' => 'https://www.microsoft.com',
            ],
            [
                'name' => 'Cisco Systems',
                'contact_person' => 'Jennifer Garcia',
                'email' => 'sales@cisco.com',
                'phone' => '+1-800-553-6387',
                'website' => 'https://www.cisco.com',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(
                ['name' => $vendor['name']],
                $vendor
            );
        }
    }
}