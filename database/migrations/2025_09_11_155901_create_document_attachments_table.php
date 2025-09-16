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
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('attachable_type'); // Employee, User, etc.
            $table->unsignedBigInteger('attachable_id');
            $table->string('document_type'); // cscs_card, driving_license, passport, etc.
            $table->string('document_name');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('mime_type');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'expiring_soon', 'archived'])->default('active');
            $table->boolean('requires_renewal')->default(false);
            $table->boolean('notification_sent')->default(false);
            $table->date('notification_sent_at')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['attachable_type', 'attachable_id']);
            $table->index(['document_type']);
            $table->index(['expiry_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};