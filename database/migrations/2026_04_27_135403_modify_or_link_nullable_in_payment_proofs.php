<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->string('or_link')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->string('or_link')->nullable(false)->change();
        });
    }
};