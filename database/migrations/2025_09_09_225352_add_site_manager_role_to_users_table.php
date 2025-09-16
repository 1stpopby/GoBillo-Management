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
        // For SQLite, we need to recreate the table to modify the CHECK constraint
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create a temporary table with the new constraint
            DB::statement('
                CREATE TABLE users_temp (
                    "id" integer primary key autoincrement not null, 
                    "company_id" integer, 
                    "name" varchar not null, 
                    "email" varchar not null, 
                    "email_verified_at" datetime, 
                    "password" varchar not null, 
                    "role" varchar check ("role" in (\'superadmin\', \'company_admin\', \'project_manager\', \'site_manager\', \'contractor\', \'subcontractor\', \'operative\', \'client\')) not null default \'contractor\', 
                    "phone" varchar, 
                    "avatar" varchar, 
                    "is_active" tinyint(1) not null default \'1\', 
                    "remember_token" varchar, 
                    "created_at" datetime, 
                    "updated_at" datetime, 
                    foreign key("company_id") references "companies"("id") on delete cascade
                )
            ');
            
            // Copy data from old table to new table
            DB::statement('INSERT INTO users_temp SELECT * FROM users');
            
            // Drop the old table
            DB::statement('DROP TABLE users');
            
            // Rename the temporary table
            DB::statement('ALTER TABLE users_temp RENAME TO users');
            
            // Recreate indexes if any exist
            DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        } else {
            // For other databases, modify the constraint directly
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN (\'superadmin\', \'company_admin\', \'project_manager\', \'site_manager\', \'contractor\', \'subcontractor\', \'operative\', \'client\'))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQLite, we need to recreate the table without the site_manager role
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create a temporary table with the old constraint
            DB::statement('
                CREATE TABLE users_temp (
                    "id" integer primary key autoincrement not null, 
                    "company_id" integer, 
                    "name" varchar not null, 
                    "email" varchar not null, 
                    "email_verified_at" datetime, 
                    "password" varchar not null, 
                    "role" varchar check ("role" in (\'superadmin\', \'company_admin\', \'project_manager\', \'contractor\', \'subcontractor\', \'operative\', \'client\')) not null default \'contractor\', 
                    "phone" varchar, 
                    "avatar" varchar, 
                    "is_active" tinyint(1) not null default \'1\', 
                    "remember_token" varchar, 
                    "created_at" datetime, 
                    "updated_at" datetime, 
                    foreign key("company_id") references "companies"("id") on delete cascade
                )
            ');
            
            // Copy data from old table to new table (users with site_manager role will fail)
            DB::statement('INSERT INTO users_temp SELECT * FROM users WHERE role != \'site_manager\'');
            
            // Drop the old table
            DB::statement('DROP TABLE users');
            
            // Rename the temporary table
            DB::statement('ALTER TABLE users_temp RENAME TO users');
            
            // Recreate indexes
            DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        } else {
            // For other databases
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN (\'superadmin\', \'company_admin\', \'project_manager\', \'contractor\', \'subcontractor\', \'operative\', \'client\'))');
        }
    }
};