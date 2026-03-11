<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('last_updated_by')->nullable()->after('verified_by');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn('last_updated_by');
        });
    }
};