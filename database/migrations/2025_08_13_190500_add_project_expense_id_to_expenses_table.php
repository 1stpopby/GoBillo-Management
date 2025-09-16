<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'project_expense_id')) {
                $table->foreignId('project_expense_id')->nullable()->after('project_id')->constrained('project_expenses')->nullOnDelete();
                $table->index('project_expense_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'project_expense_id')) {
                $table->dropConstrainedForeignId('project_expense_id');
            }
        });
    }
};







