<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Vendor;
use App\Models\AssetTag;

class AssetsModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions using existing schema
        $permissions = [
            ['name' => 'assets.view', 'display_name' => 'View Assets', 'description' => 'View asset records', 'module' => 'assets', 'action' => 'view'],
            ['name' => 'assets.create', 'display_name' => 'Create Assets', 'description' => 'Create new assets', 'module' => 'assets', 'action' => 'create'],
            ['name' => 'assets.update', 'display_name' => 'Update Assets', 'description' => 'Update asset records', 'module' => 'assets', 'action' => 'edit'],
            ['name' => 'assets.delete', 'display_name' => 'Delete Assets', 'description' => 'Delete asset records', 'module' => 'assets', 'action' => 'delete'],
            ['name' => 'assets.import', 'display_name' => 'Import Assets', 'description' => 'Import assets from files', 'module' => 'assets', 'action' => 'manage'],
            ['name' => 'assets.export', 'display_name' => 'Export Assets', 'description' => 'Export asset data', 'module' => 'assets', 'action' => 'export'],
            ['name' => 'assets.attachments', 'display_name' => 'Manage Attachments', 'description' => 'Manage asset attachments', 'module' => 'assets', 'action' => 'manage'],
        ];

        foreach ($permissions as $permission) {
            \DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // Note: Role management would need to be implemented separately
        // as the existing system may not use Spatie's permission package

        // Create admin user if none exists
        $adminUser = User::where('email', 'admin@assets.local')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Assets Admin',
                'email' => 'admin@assets.local',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'company_id' => 1, // Assuming company with ID 1 exists
                'role' => 'company_admin', // Using existing role system
            ]);
        }

        // Seed basic data
        $this->call([
            AssetCategorySeeder::class,
            LocationSeeder::class,
            VendorSeeder::class,
            AssetTagSeeder::class,
        ]);

        // Create sample assets with relationships
        $categories = AssetCategory::all();
        $locations = Location::all();
        $vendors = Vendor::all();
        $tags = AssetTag::all();

        if ($categories->isNotEmpty() && $locations->isNotEmpty() && $vendors->isNotEmpty()) {
            // Create a few sample assets using existing categories, locations, vendors
            for ($i = 1; $i <= 10; $i++) {
                $asset = Asset::create([
                    'asset_code' => 'AST-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'name' => 'Sample Asset ' . $i,
                    'description' => 'This is a sample asset for demonstration purposes.',
                    'category_id' => $categories->random()->id,
                    'location_id' => $locations->random()->id,
                    'vendor_id' => $vendors->random()->id,
                    'purchase_date' => now()->subDays(rand(30, 365)),
                    'purchase_cost' => rand(100, 5000),
                    'depreciation_method' => rand(0, 1) ? 'NONE' : 'STRAIGHT_LINE',
                    'depreciation_life_months' => rand(0, 1) ? null : rand(12, 60),
                    'status' => collect(['IN_STOCK', 'ASSIGNED', 'MAINTENANCE'])->random(),
                    'serial_number' => 'SN' . str_pad($i, 8, '0', STR_PAD_LEFT),
                    'warranty_expiry' => rand(0, 1) ? now()->addDays(rand(30, 1095)) : null,
                    'notes' => 'Sample notes for asset ' . $i,
                ]);

                // Attach random tags
                if ($tags->isNotEmpty()) {
                    $asset->tags()->attach(
                        $tags->random(rand(0, 3))->pluck('id')->toArray()
                    );
                }
            }
        }

        $this->command->info('Assets module seeded successfully!');
        $this->command->info('Admin user created: admin@assets.local / password');
    }
}