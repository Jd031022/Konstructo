<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_profiles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('tin')->nullable();
            
            // Contact Information (moved from users table)
            $table->string('telephone')->nullable();
            $table->string('alternative_email')->nullable();
            
            // Address Information (moved from users table)
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            
            // Security & Authentication (moved from users table)
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};