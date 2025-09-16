<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SiteContent;
use Illuminate\Support\Facades\Hash;

class ForgeDeploymentSeeder extends Seeder
{
    /**
     * Run the database seeds for Forge deployment.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Forge Deployment Seeder...');

        // Create SuperAdmin User
        $this->createSuperAdminUser();
        
        // Create Site Content
        $this->createSiteContent();
        
        $this->command->info('✅ Forge Deployment Seeder completed successfully!');
    }

    /**
     * Create SuperAdmin user
     */
    private function createSuperAdminUser(): void
    {
        $this->command->info('👤 Creating SuperAdmin user...');

        try {
            $user = User::firstOrCreate(
                ['email' => 'admin@gobillo.app'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password123'),
                    'role' => 'superadmin',
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->command->info('✅ SuperAdmin user created successfully!');
            } else {
                $this->command->info('ℹ️  SuperAdmin user already exists.');
            }
        } catch (\Exception $e) {
            $this->command->error('❌ Error creating SuperAdmin user: ' . $e->getMessage());
        }
    }

    /**
     * Create default site content
     */
    private function createSiteContent(): void
    {
        $this->command->info('📝 Creating site content...');

        $contents = [
            [
                'key' => 'landing_hero_title',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'text',
                'label' => 'Hero Title',
                'value' => 'Professional Construction Management Made Simple',
                'default_value' => 'Professional Construction Management Made Simple',
                'description' => 'Main hero title on landing page',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'landing_hero_subtitle',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'value' => 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we\'ve got you covered.',
                'default_value' => 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we\'ve got you covered.',
                'description' => 'Hero subtitle/description on landing page',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'landing_hero_cta_text',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'text',
                'label' => 'Hero CTA Button Text',
                'value' => 'Start Free Trial',
                'default_value' => 'Start Free Trial',
                'description' => 'Text for the main call-to-action button',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'landing_features_title',
                'page' => 'landing',
                'section' => 'features',
                'type' => 'text',
                'label' => 'Features Section Title',
                'value' => 'Everything You Need to Manage Construction Projects',
                'default_value' => 'Everything You Need to Manage Construction Projects',
                'description' => 'Title for features section',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'landing_pricing_title',
                'page' => 'landing',
                'section' => 'pricing',
                'type' => 'text',
                'label' => 'Pricing Section Title',
                'value' => 'Choose Your Plan',
                'default_value' => 'Choose Your Plan',
                'description' => 'Title for pricing section',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($contents as $content) {
            try {
                $siteContent = SiteContent::firstOrCreate(
                    ['key' => $content['key']],
                    $content
                );

                if ($siteContent->wasRecentlyCreated) {
                    $this->command->info("✅ Created content: {$content['key']}");
                } else {
                    $this->command->info("ℹ️  Content already exists: {$content['key']}");
                }
            } catch (\Exception $e) {
                $this->command->error("❌ Error creating content {$content['key']}: " . $e->getMessage());
            }
        }

        $this->command->info('✅ Site content creation completed!');
    }
}
