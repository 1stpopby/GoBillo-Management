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
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('day_rate', 8, 2)->nullable()->after('salary');
            $table->boolean('cis_applicable')->default(false)->after('cis_status');
            $table->decimal('cis_rate', 5, 2)->default(20.00)->after('cis_applicable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['day_rate', 'cis_applicable', 'cis_rate']);
        });
    }
};