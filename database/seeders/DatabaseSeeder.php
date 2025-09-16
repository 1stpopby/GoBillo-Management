<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create SuperAdmin (no company)
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gobillo.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPERADMIN,
            'company_id' => null,
            'is_active' => true,
        ]);

        // Create Demo Companies
        $company1 = Company::create([
            'name' => 'ABC Construction Corp',
            'slug' => 'abc-construction',
            'email' => 'admin@abc-construction.com',
            'phone' => '+1-555-0101',
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'country' => 'US',
            'website' => 'https://abc-construction.com',
            'description' => 'Leading construction company specializing in commercial and residential projects.',
            'status' => Company::STATUS_ACTIVE,
            'subscription_plan' => Company::PLAN_PROFESSIONAL,
            'subscription_ends_at' => Carbon::now()->addYear(),
            'max_users' => 50,
            'max_projects' => 100,
        ]);

        $company2 = Company::create([
            'name' => 'BuildRight Solutions',
            'slug' => 'buildright-solutions',
            'email' => 'admin@buildright.com',
            'phone' => '+1-555-0202',
            'address' => '456 Oak Avenue',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90001',
            'country' => 'US',
            'website' => 'https://buildright.com',
            'description' => 'Innovative construction solutions for modern infrastructure.',
            'status' => Company::STATUS_ACTIVE,
            'subscription_plan' => Company::PLAN_BASIC,
            'subscription_ends_at' => Carbon::now()->addMonths(6),
            'max_users' => 25,
            'max_projects' => 50,
        ]);

        $company3 = Company::create([
            'name' => 'Metro Builders Inc',
            'slug' => 'metro-builders',
            'email' => 'admin@metrobuilders.com',
            'phone' => '+1-555-0303',
            'address' => '789 Pine Street',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip_code' => '60601',
            'country' => 'US',
            'description' => 'Urban construction specialists.',
            'status' => Company::STATUS_ACTIVE,
            'subscription_plan' => Company::PLAN_TRIAL,
            'trial_ends_at' => Carbon::now()->addDays(30),
            'max_users' => 10,
            'max_projects' => 15,
        ]);

        // Create Users for Company 1 (ABC Construction)
        $admin1 = User::create([
            'name' => 'John Smith',
            'email' => 'admin@abc-construction.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_COMPANY_ADMIN,
            'company_id' => $company1->id,
            'phone' => '+1-555-1001',
            'is_active' => true,
        ]);

        $pm1 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@abc-construction.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PROJECT_MANAGER,
            'company_id' => $company1->id,
            'phone' => '+1-555-1002',
            'is_active' => true,
        ]);

        $contractor1 = User::create([
            'name' => 'Mike Wilson',
            'email' => 'mike@abc-construction.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CONTRACTOR,
            'company_id' => $company1->id,
            'phone' => '+1-555-1003',
            'is_active' => true,
        ]);

        $operative1 = User::create([
            'name' => 'David Brown',
            'email' => 'david@abc-construction.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OPERATIVE,
            'company_id' => $company1->id,
            'phone' => '+1-555-1004',
            'is_active' => true,
        ]);

        // Create Users for Company 2 (BuildRight)
        $admin2 = User::create([
            'name' => 'Lisa Davis',
            'email' => 'admin@buildright.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_COMPANY_ADMIN,
            'company_id' => $company2->id,
            'phone' => '+1-555-2001',
            'is_active' => true,
        ]);

        $pm2 = User::create([
            'name' => 'Robert Taylor',
            'email' => 'robert@buildright.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PROJECT_MANAGER,
            'company_id' => $company2->id,
            'phone' => '+1-555-2002',
            'is_active' => true,
        ]);

        // Create Users for Company 3 (Metro Builders)
        $admin3 = User::create([
            'name' => 'Emily Garcia',
            'email' => 'admin@metrobuilders.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_COMPANY_ADMIN,
            'company_id' => $company3->id,
            'phone' => '+1-555-3001',
            'is_active' => true,
        ]);

        // Create Clients for Company 1
        $client1 = Client::create([
            'company_id' => $company1->id,
            'name' => 'Robert Anderson',
            'email' => 'robert.anderson@email.com',
            'phone' => '+1-555-4001',
            'company' => 'Anderson Enterprises',
            'address' => '321 Business Blvd',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10002',
            'notes' => 'VIP client - prefers morning meetings',
            'is_active' => true,
        ]);

        $client2 = Client::create([
            'company_id' => $company1->id,
            'name' => 'Jennifer Martinez',
            'email' => 'jennifer.martinez@email.com',
            'phone' => '+1-555-4002',
            'company' => 'Martinez Holdings',
            'address' => '654 Corporate Ave',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10003',
            'notes' => 'Interested in eco-friendly construction',
            'is_active' => true,
        ]);

        // Create Clients for Company 2
        $client3 = Client::create([
            'company_id' => $company2->id,
            'name' => 'Michael Thompson',
            'email' => 'michael.thompson@email.com',
            'phone' => '+1-555-5001',
            'company' => 'Thompson Real Estate',
            'address' => '987 Sunset Blvd',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90002',
            'is_active' => true,
        ]);

        // Create Projects for Company 1
        $project1 = Project::create([
            'company_id' => $company1->id,
            'name' => 'Downtown Office Complex',
            'description' => 'Modern 15-story office building with retail space on ground floor',
            'client_id' => $client1->id,
            'manager_id' => $pm1->id,
            'status' => 'in_progress',
            'start_date' => Carbon::now()->subDays(30),
            'end_date' => Carbon::now()->addMonths(8),
            'budget' => 2500000.00,
            'address' => '100 Downtown Plaza',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10004',
            'priority' => 'high',
            'progress' => 35,
        ]);

        $project2 = Project::create([
            'company_id' => $company1->id,
            'name' => 'Residential Complex Phase 1',
            'description' => '50-unit luxury apartment complex with amenities',
            'client_id' => $client2->id,
            'manager_id' => $pm1->id,
            'status' => 'planning',
            'start_date' => Carbon::now()->addDays(15),
            'end_date' => Carbon::now()->addMonths(12),
            'budget' => 3200000.00,
            'address' => '200 Riverside Drive',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10005',
            'priority' => 'medium',
            'progress' => 10,
        ]);

        // Create Projects for Company 2
        $project3 = Project::create([
            'company_id' => $company2->id,
            'name' => 'Shopping Center Renovation',
            'description' => 'Complete renovation of existing shopping center',
            'client_id' => $client3->id,
            'manager_id' => $pm2->id,
            'status' => 'in_progress',
            'start_date' => Carbon::now()->subDays(45),
            'end_date' => Carbon::now()->addMonths(4),
            'budget' => 850000.00,
            'address' => '500 Commerce Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90003',
            'priority' => 'urgent',
            'progress' => 60,
        ]);

        // Assign users to projects
        $project1->users()->attach([
            $pm1->id => ['role' => 'manager', 'joined_at' => Carbon::now()->subDays(30)],
            $contractor1->id => ['role' => 'contractor', 'joined_at' => Carbon::now()->subDays(25)],
            $operative1->id => ['role' => 'operative', 'joined_at' => Carbon::now()->subDays(20)],
        ]);

        $project2->users()->attach([
            $pm1->id => ['role' => 'manager', 'joined_at' => Carbon::now()],
            $contractor1->id => ['role' => 'contractor', 'joined_at' => Carbon::now()],
        ]);

        $project3->users()->attach([
            $pm2->id => ['role' => 'manager', 'joined_at' => Carbon::now()->subDays(45)],
        ]);

        // Create Tasks for Company 1 Projects
        Task::create([
            'company_id' => $company1->id,
            'title' => 'Foundation Inspection',
            'description' => 'Conduct thorough inspection of foundation work',
            'project_id' => $project1->id,
            'assigned_to' => $contractor1->id,
            'created_by' => $pm1->id,
            'status' => 'completed',
            'priority' => 'high',
            'due_date' => Carbon::now()->subDays(5),
            'estimated_hours' => 8.0,
            'actual_hours' => 6.5,
            'progress' => 100,
        ]);

        Task::create([
            'company_id' => $company1->id,
            'title' => 'Steel Frame Installation',
            'description' => 'Install steel framework for floors 1-5',
            'project_id' => $project1->id,
            'assigned_to' => $operative1->id,
            'created_by' => $pm1->id,
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => Carbon::now()->addDays(14),
            'estimated_hours' => 120.0,
            'actual_hours' => 45.0,
            'progress' => 40,
        ]);

        Task::create([
            'company_id' => $company1->id,
            'title' => 'Electrical Planning',
            'description' => 'Plan electrical systems for the building',
            'project_id' => $project2->id,
            'assigned_to' => $contractor1->id,
            'created_by' => $pm1->id,
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => Carbon::now()->addDays(30),
            'estimated_hours' => 16.0,
            'progress' => 0,
        ]);

        // Create Tasks for Company 2 Projects
        Task::create([
            'company_id' => $company2->id,
            'title' => 'Demolition Work',
            'description' => 'Remove old fixtures and prepare for renovation',
            'project_id' => $project3->id,
            'assigned_to' => $pm2->id,
            'created_by' => $admin2->id,
            'status' => 'completed',
            'priority' => 'urgent',
            'due_date' => Carbon::now()->subDays(20),
            'estimated_hours' => 40.0,
            'actual_hours' => 38.0,
            'progress' => 100,
        ]);

        Task::create([
            'company_id' => $company2->id,
            'title' => 'New Flooring Installation',
            'description' => 'Install new flooring throughout the center',
            'project_id' => $project3->id,
            'assigned_to' => $pm2->id,
            'created_by' => $admin2->id,
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => Carbon::now()->addDays(21),
            'estimated_hours' => 80.0,
            'actual_hours' => 32.0,
            'progress' => 65,
        ]);

        // Seed modules and task categories
        $this->call([
            ModuleSeeder::class,
            TaskCategorySeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
