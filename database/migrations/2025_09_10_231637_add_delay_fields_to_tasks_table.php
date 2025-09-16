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
            $table->integer('delay_days')->nullable()->after('actual_cost')->comment('Number of days the task is delayed');
            $table->text('delay_reason')->nullable()->after('delay_days')->comment('Reason for the delay');
            $table->date('original_due_date')->nullable()->after('delay_reason')->comment('Original due date before any delays');
            $table->boolean('is_delayed')->default(false)->after('original_due_date')->comment('Flag to indicate if task is delayed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['delay_days', 'delay_reason', 'original_due_date', 'is_delayed']);
        });
    }
};