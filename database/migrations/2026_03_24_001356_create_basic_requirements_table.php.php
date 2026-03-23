<?php
// database/migrations/2026_03_24_create_basic_requirements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicRequirementsTable extends Migration
{
    public function up()
    {
        Schema::create('basic_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Proof of Ownership
            $table->string('tct_link')->nullable(); // Transfer Certificate of Title
            $table->string('tax_declaration_link')->nullable();
            $table->string('current_tax_receipt_link')->nullable();
            
            // Authorization (conditional)
            $table->string('deed_of_sale_link')->nullable();
            $table->string('spa_link')->nullable(); // Special Power of Attorney
            $table->boolean('is_owner')->default(true);
            
            // Status fields
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('basic_requirements');
    }
}