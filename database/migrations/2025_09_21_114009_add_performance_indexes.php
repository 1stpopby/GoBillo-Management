<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for projects table foreign keys
        Schema::table('projects', function (Blueprint $table) {
            $table->index('company_id', 'idx_projects_company_id');
            $table->index('site_id', 'idx_projects_site_id');
            $table->index('client_id', 'idx_projects_client_id');
            $table->index('manager_id', 'idx_projects_manager_id');
            $table->index('status', 'idx_projects_status');
            $table->index('is_active', 'idx_projects_is_active');
            $table->index(['company_id', 'is_active'], 'idx_projects_company_active');
        });

        // Add indexes for sites table foreign keys
        Schema::table('sites', function (Blueprint $table) {
            $table->index('company_id', 'idx_sites_company_id');
            $table->index('client_id', 'idx_sites_client_id');
            $table->index('status', 'idx_sites_status');
            $table->index('is_active', 'idx_sites_is_active');
            $table->index(['company_id', 'is_active'], 'idx_sites_company_active');
        });

        // Add indexes for site_managers pivot table
        Schema::table('site_managers', function (Blueprint $table) {
            $table->index('site_id', 'idx_site_managers_site_id');
            $table->index('manager_id', 'idx_site_managers_manager_id');
            $table->index('is_active', 'idx_site_managers_is_active');
            $table->index(['site_id', 'manager_id'], 'idx_site_managers_site_manager');
            $table->index(['site_id', 'manager_id', 'is_active'], 'idx_site_managers_full');
        });

        // Add indexes for project_managers pivot table (if exists)
        if (Schema::hasTable('project_managers')) {
            Schema::table('project_managers', function (Blueprint $table) {
                $table->index('project_id', 'idx_project_managers_project_id');
                $table->index('manager_id', 'idx_project_managers_manager_id');
                $table->index('is_active', 'idx_project_managers_is_active');
                $table->index(['project_id', 'manager_id'], 'idx_project_managers_project_manager');
                $table->index(['project_id', 'manager_id', 'is_active'], 'idx_project_managers_full');
            });
        }

        // Add indexes for tasks table to optimize counts
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('project_id', 'idx_tasks_project_id');
            $table->index('status', 'idx_tasks_status');
            $table->index('completed_at', 'idx_tasks_completed_at');
            $table->index(['project_id', 'status'], 'idx_tasks_project_status');
        });

        // Add indexes for users table for role-based queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('company_id', 'idx_users_company_id');
            $table->index('role', 'idx_users_role');
            $table->index('is_active', 'idx_users_is_active');
            $table->index(['company_id', 'role', 'is_active'], 'idx_users_company_role_active');
        });

        // Add indexes for clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->index('company_id', 'idx_clients_company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_company_id');
            $table->dropIndex('idx_projects_site_id');
            $table->dropIndex('idx_projects_client_id');
            $table->dropIndex('idx_projects_manager_id');
            $table->dropIndex('idx_projects_status');
            $table->dropIndex('idx_projects_is_active');
            $table->dropIndex('idx_projects_company_active');
        });

        // Drop indexes for sites table
        Schema::table('sites', function (Blueprint $table) {
            $table->dropIndex('idx_sites_company_id');
            $table->dropIndex('idx_sites_client_id');
            $table->dropIndex('idx_sites_status');
            $table->dropIndex('idx_sites_is_active');
            $table->dropIndex('idx_sites_company_active');
        });

        // Drop indexes for site_managers table
        Schema::table('site_managers', function (Blueprint $table) {
            $table->dropIndex('idx_site_managers_site_id');
            $table->dropIndex('idx_site_managers_manager_id');
            $table->dropIndex('idx_site_managers_is_active');
            $table->dropIndex('idx_site_managers_site_manager');
            $table->dropIndex('idx_site_managers_full');
        });

        // Drop indexes for project_managers table (if exists)
        if (Schema::hasTable('project_managers')) {
            Schema::table('project_managers', function (Blueprint $table) {
                $table->dropIndex('idx_project_managers_project_id');
                $table->dropIndex('idx_project_managers_manager_id');
                $table->dropIndex('idx_project_managers_is_active');
                $table->dropIndex('idx_project_managers_project_manager');
                $table->dropIndex('idx_project_managers_full');
            });
        }

        // Drop indexes for tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_project_id');
            $table->dropIndex('idx_tasks_status');
            $table->dropIndex('idx_tasks_completed_at');
            $table->dropIndex('idx_tasks_project_status');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_company_id');
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_is_active');
            $table->dropIndex('idx_users_company_role_active');
        });

        // Drop indexes for clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('idx_clients_company_id');
        });
    }
};