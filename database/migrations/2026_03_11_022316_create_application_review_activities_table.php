<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('application_review_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('reviewer_id'); // The staff who performed the action
            $table->string('action'); // e.g., 'status_updated', 'note_added', 'document_verified'
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('remarks')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('application_id')
                  ->references('id')
                  ->on('application_documents')
                  ->onDelete('cascade');
                  
            $table->foreign('reviewer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Indexes for faster queries
            $table->index('application_id');
            $table->index('reviewer_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_review_activities');
    }
};