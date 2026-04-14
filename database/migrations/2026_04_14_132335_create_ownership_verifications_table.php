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
        Schema::create('ownership_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('application_documents')->onDelete('cascade');
            $table->boolean('is_owner')->default(true);
            
            // Required documents
            $table->string('tct_link')->nullable();              // TCT or Deed of Sale
            $table->string('tax_declaration_link')->nullable();   // Tax Declaration
            $table->string('current_tax_receipt_link')->nullable(); // Current Tax Receipt
            
            // Optional document (required if is_owner = false)
            $table->string('spa_link')->nullable();               // Special Power of Attorney
            
            // Verification statuses
            $table->enum('assessor_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('treasurer_status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Remarks and timestamps
            $table->text('assessor_remarks')->nullable();
            $table->text('treasurer_remarks')->nullable();
            $table->timestamp('assessor_verified_at')->nullable();
            $table->timestamp('treasurer_verified_at')->nullable();
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index('application_id');
            $table->index('assessor_status');
            $table->index('treasurer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ownership_verifications');
    }
};