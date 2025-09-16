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
        Schema::table('operative_invoices', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['project_id']);
            
            // Make project_id nullable and re-add the foreign key constraint
            $table->foreignId('project_id')->nullable()->change();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operative_invoices', function (Blueprint $table) {
            // Drop the nullable foreign key constraint
            $table->dropForeign(['project_id']);
            
            // Make project_id required again and re-add the foreign key constraint
            $table->foreignId('project_id')->nullable(false)->change();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
};
