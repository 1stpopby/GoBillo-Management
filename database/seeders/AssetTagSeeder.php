<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetTag;

class AssetTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'High Priority', 'color' => '#DC2626', 'description' => 'Assets requiring immediate attention'],
            ['name' => 'Under Warranty', 'color' => '#10B981', 'description' => 'Assets still covered by warranty'],
            ['name' => 'Maintenance Required', 'color' => '#F59E0B', 'description' => 'Assets needing maintenance'],
            ['name' => 'Critical', 'color' => '#EF4444', 'description' => 'Mission-critical assets'],
            ['name' => 'Pool A', 'color' => '#3B82F6', 'description' => 'Asset pool A allocation'],
            ['name' => 'Pool B', 'color' => '#8B5CF6', 'description' => 'Asset pool B allocation'],
            ['name' => 'Design Team', 'color' => '#EC4899', 'description' => 'Assets assigned to design team'],
            ['name' => 'Development', 'color' => '#06B6D4', 'description' => 'Development team assets'],
            ['name' => 'Testing', 'color' => '#84CC16', 'description' => 'Testing environment assets'],
            ['name' => 'Production', 'color' => '#F97316', 'description' => 'Production environment assets'],
            ['name' => 'Deprecated', 'color' => '#6B7280', 'description' => 'Assets marked for replacement'],
            ['name' => 'New Arrival', 'color' => '#22C55E', 'description' => 'Recently acquired assets'],
        ];

        foreach ($tags as $tag) {
            AssetTag::firstOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }
    }
}