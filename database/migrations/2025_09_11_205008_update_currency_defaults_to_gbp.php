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
        // Update existing USD records to GBP
        DB::table('expenses')->where('currency', 'USD')->update(['currency' => 'GBP']);
        DB::table('project_expenses')->where('currency', 'USD')->update(['currency' => 'GBP']);
        DB::table('invoices')->where('currency', 'USD')->update(['currency' => 'GBP']);
        DB::table('estimates')->where('currency', 'USD')->update(['currency' => 'GBP']);
        
        // Update table defaults
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('currency', 3)->default('GBP')->change();
        });
        
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('currency', 3)->default('GBP')->change();
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency', 3)->default('GBP')->change();
        });
        
        Schema::table('estimates', function (Blueprint $table) {
            $table->string('currency', 3)->default('GBP')->change();
        });
        
        // Update payment related tables if they exist
        if (Schema::hasTable('payments')) {
            DB::table('payments')->where('currency', 'USD')->update(['currency' => 'GBP']);
            Schema::table('payments', function (Blueprint $table) {
                $table->string('currency', 3)->default('GBP')->change();
            });
        }
        
        if (Schema::hasTable('payment_methods')) {
            DB::table('payment_methods')->where('currency', 'USD')->update(['currency' => 'GBP']);
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->string('currency', 3)->default('GBP')->change();
            });
        }
        
        if (Schema::hasTable('payment_links')) {
            DB::table('payment_links')->where('currency', 'USD')->update(['currency' => 'GBP']);
            Schema::table('payment_links', function (Blueprint $table) {
                $table->string('currency', 3)->default('GBP')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert existing GBP records to USD
        DB::table('expenses')->where('currency', 'GBP')->update(['currency' => 'USD']);
        DB::table('project_expenses')->where('currency', 'GBP')->update(['currency' => 'USD']);
        DB::table('invoices')->where('currency', 'GBP')->update(['currency' => 'USD']);
        DB::table('estimates')->where('currency', 'GBP')->update(['currency' => 'USD']);
        
        // Revert table defaults
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
        
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
        
        Schema::table('estimates', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
        
        // Revert payment related tables if they exist
        if (Schema::hasTable('payments')) {
            DB::table('payments')->where('currency', 'GBP')->update(['currency' => 'USD']);
            Schema::table('payments', function (Blueprint $table) {
                $table->string('currency', 3)->default('USD')->change();
            });
        }
        
        if (Schema::hasTable('payment_methods')) {
            DB::table('payment_methods')->where('currency', 'GBP')->update(['currency' => 'USD']);
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->string('currency', 3)->default('USD')->change();
            });
        }
        
        if (Schema::hasTable('payment_links')) {
            DB::table('payment_links')->where('currency', 'GBP')->update(['currency' => 'USD']);
            Schema::table('payment_links', function (Blueprint $table) {
                $table->string('currency', 3)->default('USD')->change();
            });
        }
    }
};