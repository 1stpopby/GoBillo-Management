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
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->default('company'); // 'company' or 'system'
            $table->string('name')->default('Default');
            
            // SMTP Configuration
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->default('tls'); // tls, ssl, or null
            
            // From Email Configuration
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            
            // Reply-To Configuration
            $table->string('reply_to_email')->nullable();
            $table->string('reply_to_name')->nullable();
            
            // Email Templates Settings
            $table->json('enabled_notifications')->nullable(); // Which notifications to send
            $table->string('email_signature')->nullable();
            $table->string('company_logo_url')->nullable();
            
            // Status and Testing
            $table->boolean('is_active')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->text('test_results')->nullable();
            
            // Usage Tracking
            $table->integer('emails_sent_today')->default(0);
            $table->integer('emails_sent_month')->default(0);
            $table->date('last_reset_date')->nullable();
            
            $table->timestamps();
            
            $table->index(['company_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
