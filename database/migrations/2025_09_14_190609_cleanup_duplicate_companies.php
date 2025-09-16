<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete duplicate companies (keep only ID 1)
        DB::table('subscriptions')->whereIn('company_id', [2, 3, 4, 5, 6, 7])->delete();
        DB::table('users')->whereIn('company_id', [2, 3, 4, 5, 6, 7])->delete();
        DB::table('projects')->whereIn('company_id', [2, 3, 4, 5, 6, 7])->delete();
        DB::table('clients')->whereIn('company_id', [2, 3, 4, 5, 6, 7])->delete();
        DB::table('tasks')->whereIn('company_id', [2, 3, 4, 5, 6, 7])->delete();
        DB::table('companies')->whereIn('id', [2, 3, 4, 5, 6, 7])->delete();
        
        // Create a subscription for the original company (ID 1) if it doesn't have one
        $originalCompany = DB::table('companies')->where('id', 1)->first();
        $professionalPlan = DB::table('membership_plans')->where('slug', 'professional')->first();
        
        if ($originalCompany && $professionalPlan) {
            // Check if company already has an active subscription
            $existingSubscription = DB::table('subscriptions')
                ->where('company_id', 1)
                ->whereIn('status', ['active', 'trial'])
                ->first();
            
            if (!$existingSubscription) {
                DB::table('subscriptions')->insert([
                    'company_id' => 1,
                    'membership_plan_id' => $professionalPlan->id,
                    'status' => 'active',
                    'billing_cycle' => 'monthly',
                    'starts_at' => now(),
                    'amount' => $professionalPlan->monthly_price,
                    'setup_fee' => $professionalPlan->setup_fee,
                    'currency' => 'GBP',
                    'next_billing_date' => now()->addMonth(),
                    'current_users' => DB::table('users')->where('company_id', 1)->count(),
                    'current_sites' => DB::table('sites')->where('company_id', 1)->count(),
                    'current_projects' => DB::table('projects')->where('company_id', 1)->count(),
                    'current_storage_gb' => 0.5, // Estimate
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed safely
    }
};