<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table_after_suffix.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'engineer', 'applicant'])
                  ->default('applicant')
                  ->after('suffix'); // This puts it right after the suffix column
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};