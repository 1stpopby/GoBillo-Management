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
        Schema::create('operative_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operative_invoice_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->date('work_date');
            $table->boolean('worked')->default(false);
            $table->decimal('hours_worked', 4, 2)->default(8.00);
            $table->text('description')->nullable();
            $table->decimal('day_rate', 8, 2);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['operative_invoice_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operative_invoice_items');
    }
};