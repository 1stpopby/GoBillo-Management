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
        Schema::table('tasks', function (Blueprint $table) {
            // Add task_category_id foreign key
            $table->foreignId('task_category_id')->nullable()->after('project_id')->constrained()->onDelete('set null');
            
            $table->index(['company_id', 'task_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_category_id']);
            $table->dropColumn('task_category_id');
            $table->dropIndex(['company_id', 'task_category_id']);
        });
    }
};
