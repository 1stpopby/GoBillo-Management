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
        Schema::table('cis_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('employee_id');
            $table->string('payee_type')->default('employee')->after('user_id'); // 'employee' or 'user'
            $table->string('employment_status')->default('employed')->after('payee_type'); // 'employed' or 'self_employed'
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'payee_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cis_payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'payee_type']);
            $table->dropColumn(['user_id', 'payee_type', 'employment_status']);
        });
    }
};