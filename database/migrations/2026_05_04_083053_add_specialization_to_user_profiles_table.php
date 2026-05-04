<?php
// database/migrations/2026_01_01_000000_add_specialization_to_user_profiles.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('specialization')->nullable()->after('position');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('specialization');
        });
    }
};