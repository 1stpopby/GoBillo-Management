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
            // On hold functionality
            $table->boolean('is_on_hold')->default(false)->after('is_delayed');
            $table->text('on_hold_reason')->nullable()->after('is_on_hold');
            $table->timestamp('on_hold_date')->nullable()->after('on_hold_reason');
            $table->timestamp('on_hold_removed_date')->nullable()->after('on_hold_date');
            
            // Enhanced delay tracking
            $table->timestamp('delay_applied_date')->nullable()->after('delay_reason');
            $table->timestamp('delay_removed_date')->nullable()->after('delay_applied_date');
            
            // Track who applied delay/hold
            $table->unsignedBigInteger('delay_applied_by')->nullable()->after('delay_removed_date');
            $table->unsignedBigInteger('on_hold_applied_by')->nullable()->after('delay_applied_by');
            
            // Foreign key constraints
            $table->foreign('delay_applied_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('on_hold_applied_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['delay_applied_by']);
            $table->dropForeign(['on_hold_applied_by']);
            
            $table->dropColumn([
                'is_on_hold',
                'on_hold_reason',
                'on_hold_date',
                'on_hold_removed_date',
                'delay_applied_date',
                'delay_removed_date',
                'delay_applied_by',
                'on_hold_applied_by'
            ]);
        });
    }
};