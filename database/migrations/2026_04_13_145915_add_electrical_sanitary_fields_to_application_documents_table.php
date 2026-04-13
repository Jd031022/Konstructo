<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            // Electrical Engineer
            if (!Schema::hasColumn('application_documents', 'electrical_engineer_name')) {
                $table->string('electrical_engineer_name')->nullable()->after('engineer_license');
            }
            if (!Schema::hasColumn('application_documents', 'electrical_engineer_license')) {
                $table->string('electrical_engineer_license')->nullable()->after('electrical_engineer_name');
            }
            
            // Sanitary Engineer / Master Plumber
            if (!Schema::hasColumn('application_documents', 'sanitary_engineer_name')) {
                $table->string('sanitary_engineer_name')->nullable()->after('electrical_engineer_license');
            }
            if (!Schema::hasColumn('application_documents', 'sanitary_engineer_license')) {
                $table->string('sanitary_engineer_license')->nullable()->after('sanitary_engineer_name');
            }
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn([
                'electrical_engineer_name',
                'electrical_engineer_license',
                'sanitary_engineer_name',
                'sanitary_engineer_license',
            ]);
        });
    }
};