<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptops', 'color' => '#3B82F6', 'description' => 'Portable computers and laptops'],
            ['name' => 'Desktops', 'color' => '#10B981', 'description' => 'Desktop computers and workstations'],
            ['name' => 'Monitors', 'color' => '#F59E0B', 'description' => 'Computer monitors and displays'],
            ['name' => 'Printers', 'color' => '#EF4444', 'description' => 'Printers and scanning equipment'],
            ['name' => 'Mobile Devices', 'color' => '#8B5CF6', 'description' => 'Smartphones and tablets'],
            ['name' => 'Networking', 'color' => '#06B6D4', 'description' => 'Routers, switches, and network equipment'],
            ['name' => 'Servers', 'color' => '#84CC16', 'description' => 'Server hardware and equipment'],
            ['name' => 'Software', 'color' => '#F97316', 'description' => 'Software licenses and subscriptions'],
            ['name' => 'Furniture', 'color' => '#6B7280', 'description' => 'Office furniture and fixtures'],
            ['name' => 'Vehicles', 'color' => '#DC2626', 'description' => 'Company vehicles and transportation'],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}