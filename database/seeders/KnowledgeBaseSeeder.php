<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KBCategory;
use App\Models\KBArticle;
use App\Models\KBTag;
use App\Models\KBBinding;
use Illuminate\Support\Str;

class KnowledgeBaseSeeder extends Seeder
{
    public function run()
    {
        // Create tags
        $tags = [
            'getting-started' => 'Getting Started',
            'projects' => 'Projects',
            'invoicing' => 'Invoicing',
            'employees' => 'Employees',
            'best-practices' => 'Best Practices',
            'troubleshooting' => 'Troubleshooting',
        ];
        
        $tagModels = [];
        foreach ($tags as $slug => $name) {
            $tagModels[$slug] = KBTag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
        
        // Create main categories
        $gettingStarted = KBCategory::firstOrCreate(
            ['slug' => 'getting-started'],
            [
                'name' => 'Getting Started',
                'description' => 'Learn the basics of ProMax Team and get your construction business up and running',
                'icon' => 'bi-rocket',
                'order' => 1,
                'is_active' => true
            ]
        );
        
        $projectManagement = KBCategory::firstOrCreate(
            ['slug' => 'projects'],
            [
                'name' => 'Project Management',
                'description' => 'Master project creation, tracking, and management',
                'icon' => 'bi-diagram-3',
                'order' => 2,
                'is_active' => true
            ]
        );
        
        $financial = KBCategory::firstOrCreate(
            ['slug' => 'invoicing'],
            [
                'name' => 'Financial Management',
                'description' => 'Handle invoices, estimates, payments, and financial reporting',
                'icon' => 'bi-currency-pound',
                'order' => 3,
                'is_active' => true
            ]
        );
        
        $employees = KBCategory::firstOrCreate(
            ['slug' => 'employees'],
            [
                'name' => 'Employee Management',
                'description' => 'Manage your team, track time, and handle payroll',
                'icon' => 'bi-people',
                'order' => 4,
                'is_active' => true
            ]
        );
        
        // Create Getting Started articles
        $article1 = KBArticle::firstOrCreate(
            ['slug' => 'welcome-to-promax-team'],
            [
                'category_id' => $gettingStarted->id,
                'title' => 'Welcome to ProMax Team',
                'summary' => 'Your complete guide to getting started with ProMax Team construction management software',
                'status' => 'published',
                'created_by' => 1,
                'priority' => 100,
                'order' => 1,
                'meta_data' => json_encode([
                    'meta_title' => 'Getting Started with ProMax Team',
                    'meta_description' => 'Learn how to get started with ProMax Team construction management software',
                    'is_featured' => true
                ]),
                'published_at' => now()
            ]
        );
        
        $article2 = KBArticle::firstOrCreate(
            ['slug' => 'setting-up-your-company'],
            [
                'category_id' => $gettingStarted->id,
                'title' => 'Setting Up Your Company Profile',
                'summary' => 'Configure your company details, branding, and preferences in ProMax Team',
                'status' => 'published',
                'created_by' => 1,
                'priority' => 90,
                'order' => 2,
                'meta_data' => json_encode([
                    'meta_title' => 'Company Setup Guide',
                    'meta_description' => 'Learn how to configure your company profile in ProMax Team'
                ]),
                'published_at' => now()
            ]
        );
        
        // Create Project Management articles
        $article3 = KBArticle::firstOrCreate(
            ['slug' => 'creating-first-project'],
            [
                'category_id' => $projectManagement->id,
                'title' => 'Creating Your First Project',
                'summary' => 'Step-by-step guide to creating and configuring a new construction project',
                'status' => 'published',
                'created_by' => 1,
                'priority' => 85,
                'order' => 1,
                'meta_data' => json_encode([
                    'meta_title' => 'How to Create Projects',
                    'meta_description' => 'Learn how to create and manage construction projects in ProMax Team',
                    'is_featured' => true
                ]),
                'published_at' => now()
            ]
        );
        
        // Create Financial Management articles  
        $article4 = KBArticle::firstOrCreate(
            ['slug' => 'creating-invoices'],
            [
                'category_id' => $financial->id,
                'title' => 'Creating and Managing Invoices',
                'summary' => 'Learn how to create professional invoices and track payments',
                'status' => 'published',
                'created_by' => 1,
                'priority' => 80,
                'order' => 1,
                'meta_data' => json_encode([
                    'meta_title' => 'Invoice Management Guide',
                    'meta_description' => 'Complete guide to creating and managing invoices in ProMax Team'
                ]),
                'published_at' => now()
            ]
        );
        
        // Note: Tags and bindings can be set up later when those relationships are fully configured
        // For now, we have successfully created the basic KB structure with categories and articles
        
        $this->command->info('Knowledge Base seeded with sample articles!');
    }
}