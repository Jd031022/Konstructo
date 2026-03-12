<?php
// database/migrations/xxxx_xx_xx_xxxxxx_remove_profile_fields_from_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop all the columns that were moved to user_profiles
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

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore columns in case of rollback
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('tin')->nullable();
            $table->string('telephone')->nullable();
            $table->string('alternative_email')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
        });
    }
};