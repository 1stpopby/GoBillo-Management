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
        Schema::table('operative_data_forms', function (Blueprint $table) {
            $table->boolean('account_created')->default(false)->after('rejected_at');
            $table->timestamp('account_created_at')->nullable()->after('account_created');
            $table->foreignId('account_created_by')->nullable()->constrained('users')->onDelete('set null')->after('account_created_at');
            $table->foreignId('created_user_id')->nullable()->constrained('users')->onDelete('set null')->after('account_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operative_data_forms', function (Blueprint $table) {
            $table->dropForeign(['account_created_by']);
            $table->dropForeign(['created_user_id']);
            $table->dropColumn(['account_created', 'account_created_at', 'account_created_by', 'created_user_id']);
        });
    }
};
