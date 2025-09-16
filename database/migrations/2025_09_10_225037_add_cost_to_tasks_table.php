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
            $table->decimal('estimated_cost', 10, 2)->nullable()->after('estimated_time_unit')->comment('Estimated cost for this task');
            $table->decimal('actual_cost', 10, 2)->nullable()->after('actual_time_unit')->comment('Actual cost incurred for this task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['estimated_cost', 'actual_cost']);
        });
    }
};
