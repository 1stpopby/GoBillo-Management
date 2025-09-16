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
            $table->string('cis_number')->nullable()->after('notes');
            $table->enum('cis_status', ['pending', 'verified', 'rejected', 'not_registered'])->default('pending')->after('cis_number');
            
            $table->index('cis_number');
            $table->index('cis_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['cis_number']);
            $table->dropIndex(['cis_status']);
            $table->dropColumn(['cis_number', 'cis_status']);
        });
    }
};