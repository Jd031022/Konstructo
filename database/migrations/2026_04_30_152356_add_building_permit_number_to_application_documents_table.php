<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->string('building_permit_number', 20)->nullable()->after('cpdo_assessed_at');
            $table->text('permit_remarks')->nullable()->after('building_permit_number');
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['building_permit_number', 'permit_remarks']);
        });
    }
};