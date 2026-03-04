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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
    
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            
            $table->string('status', 50)->default('success');
            
            $table->timestamps();
          
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index('ip_address');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};