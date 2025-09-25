<?php

namespace Database\Seeders;

use App\Models\OnboardingStep;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OnboardingStepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $steps = [
            [
                'key' => 'company_profile',
                'name' => 'Complete Company Profile',
                'description' => 'Set up your company information including name, address, phone, and email. This information will appear on your invoices and documents.',
                'order' => 1,
                'route' => 'settings',
                'icon' => 'bi-building',
                'is_active' => true
            ],
            [
                'key' => 'first_client',
                'name' => 'Add Your First Client',
                'description' => 'Create your first client profile. Clients are the businesses or individuals you work for on projects.',
                'order' => 2,
                'route' => 'clients.create',
                'icon' => 'bi-person-plus',
                'is_active' => true
            ],
            [
                'key' => 'first_manager',
                'name' => 'Create Manager Account',
                'description' => 'Set up a project manager account. Managers can oversee projects, assign tasks, and track progress.',
                'order' => 3,
                'route' => 'users.create',
                'icon' => 'bi-person-badge',
                'is_active' => true
            ],
            [
                'key' => 'operatives',
                'name' => 'Add Operatives',
                'description' => 'Add your field workers and operatives. These are the team members who will be working on your projects.',
                'order' => 4,
                'route' => 'employees.create',
                'icon' => 'bi-people',
                'is_active' => true
            ],
            [
                'key' => 'first_site',
                'name' => 'Create Your First Site',
                'description' => 'Set up your first construction site. Sites are physical locations where your projects take place.',
                'order' => 5,
                'route' => 'sites.create',
                'icon' => 'bi-geo-alt',
                'is_active' => true
            ],
            [
                'key' => 'first_project',
                'name' => 'Set Up First Project',
                'description' => 'Create your first project. Projects are the core of your business operations where you manage tasks, track progress, and handle finances.',
                'order' => 6,
                'route' => 'projects.create',
                'icon' => 'bi-kanban',
                'is_active' => true
            ]
        ];
        
        foreach ($steps as $step) {
            OnboardingStep::updateOrCreate(
                ['key' => $step['key']],
                $step
            );
        }
    }
}