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
            // Add missing columns that are in the Task model but not in the database
            $table->date('start_date')->nullable()->after('due_date');
            $table->timestamp('completed_at')->nullable()->after('actual_time_unit');
            $table->text('notes')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'completed_at', 'notes']);
        });
    }
};