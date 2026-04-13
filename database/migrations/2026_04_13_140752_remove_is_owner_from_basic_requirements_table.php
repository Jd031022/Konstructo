<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIsOwnerFromBasicRequirementsTable extends Migration
{
    public function up()
    {
        Schema::table('basic_requirements', function (Blueprint $table) {
            $table->dropColumn('is_owner');
        });
    }

    public function down()
    {
        Schema::table('basic_requirements', function (Blueprint $table) {
            $table->boolean('is_owner')->default(true)->after('spa_link');
        });
    }
}