<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('application_number', 10)->unique();
            $table->string('google_drive_link')->nullable();
            $table->enum('status', [
                'pending', 
                'verified', 
                'rejected'
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            // Indexes
            $table->index('user_id');
            $table->index('application_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_documents');
    }
};