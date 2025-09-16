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
        Schema::create('operative_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('operative_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'rejected'])->default('draft');
            $table->date('week_starting');
            $table->date('week_ending');
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->decimal('day_rate', 8, 2);
            $table->decimal('gross_amount', 10, 2)->default(0);
            $table->boolean('cis_applicable')->default(false);
            $table->decimal('cis_rate', 5, 2)->nullable();
            $table->decimal('cis_deduction', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['operative_id', 'status']);
            $table->index(['company_id', 'week_starting']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operative_invoices');
    }
};