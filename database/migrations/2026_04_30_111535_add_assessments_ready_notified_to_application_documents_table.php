<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->boolean('assessments_ready_notified')->default(false)->after('cpdo_assessed_at');
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn('assessments_ready_notified');
        });
    }
};