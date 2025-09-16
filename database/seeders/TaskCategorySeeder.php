<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\TaskCategory;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $categories = TaskCategory::getDefaultCategories();

            foreach ($categories as $categoryData) {
                TaskCategory::create([
                    'company_id' => $company->id,
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'color' => $categoryData['color'],
                    'icon' => $categoryData['icon'],
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true
                ]);
            }
        }
    }
}
