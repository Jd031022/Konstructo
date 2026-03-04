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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->string('username_attempted', 100)->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent', 255)->nullable();
            
            $table->boolean('was_successful')->default(false);
            $table->string('failure_reason', 100)->nullable();
        
            $table->timestamps();
            
            $table->index('ip_address');
            $table->index('created_at');
            $table->index('was_successful');
            $table->index('user_id');
            $table->index('username_attempted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};