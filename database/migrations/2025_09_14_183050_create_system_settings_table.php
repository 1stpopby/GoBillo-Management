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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'stripe_public_key', 'google_maps_api_key'
            $table->text('value')->nullable(); // The actual value (encrypted for sensitive data)
            $table->string('type')->default('string'); // string, boolean, integer, json, encrypted
            $table->string('group')->default('general'); // general, payment, integrations, security
            $table->string('label'); // Human readable label
            $table->text('description')->nullable(); // Description for the setting
            $table->boolean('is_encrypted')->default(false); // Whether the value is encrypted
            $table->boolean('is_required')->default(false); // Whether this setting is required
            $table->json('validation_rules')->nullable(); // Validation rules as JSON
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['group', 'sort_order']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};