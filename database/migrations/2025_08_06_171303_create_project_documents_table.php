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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type'); // pdf, image, document, etc.
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->enum('category', ['plans', 'photos', 'contracts', 'permits', 'reports', 'specifications', 'invoices', 'certificates', 'other']);
            $table->boolean('is_public')->default(false); // visible to client
            $table->json('tags')->nullable(); // for search and organization
            $table->integer('version')->default(1);
            $table->foreignId('parent_document_id')->nullable()->constrained('project_documents')->onDelete('cascade');
            $table->timestamps();

            $table->index(['project_id', 'category']);
            $table->index(['company_id', 'file_type']);
            $table->index(['uploaded_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
