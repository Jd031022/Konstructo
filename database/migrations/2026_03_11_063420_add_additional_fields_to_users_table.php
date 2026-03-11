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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->date('date_of_birth')->nullable()->after('suffix');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('gender')->nullable()->after('place_of_birth');
            $table->string('civil_status')->nullable()->after('gender');
            $table->string('citizenship')->nullable()->after('civil_status');
            $table->string('tin')->nullable()->after('citizenship');
            
            // Contact Information
            $table->string('telephone')->nullable()->after('phone_number');
            $table->string('alternative_email')->nullable()->after('email');
            
            // Address Information (breakdown)
            $table->string('house_number')->nullable()->after('address');
            $table->string('street')->nullable()->after('house_number');
            $table->string('barangay')->nullable()->after('street');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            
            // Security
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_at');
            $table->string('two_factor_secret')->nullable()->after('password_changed_at');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'place_of_birth',
                'gender',
                'civil_status',
                'citizenship',
                'tin',
                'telephone',
                'alternative_email',
                'house_number',
                'street',
                'barangay',
                'city',
                'province',
                'last_login_at',
                'password_changed_at',
                'two_factor_secret',
                'two_factor_enabled',
            ]);
        });
    }
};