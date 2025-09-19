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
        Schema::table('projects', function (Blueprint $table) {
            // Add site_id as nullable first to handle existing data
            $table->foreignId('site_id')->nullable()->after('company_id')->constrained()->onDelete('cascade');
            
            // Make client_id nullable since now projects belong to sites, and sites belong to clients
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            
            $table->index(['company_id', 'site_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
            $table->dropIndex(['company_id', 'site_id']);
            
            // Revert client_id to not nullable
            $table->dropForeign(['client_id']);
            $table->foreignId('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
