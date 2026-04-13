<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('application_documents', 'architect_name')) {
                $table->string('architect_name')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'architect_license')) {
                $table->string('architect_license')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'engineer_name')) {
                $table->string('engineer_name')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'engineer_license')) {
                $table->string('engineer_license')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'electrical_engineer_name')) {
                $table->string('electrical_engineer_name')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'electrical_engineer_license')) {
                $table->string('electrical_engineer_license')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'sanitary_engineer_name')) {
                $table->string('sanitary_engineer_name')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'sanitary_engineer_license')) {
                $table->string('sanitary_engineer_license')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn([
                'architect_name',
                'architect_license',
                'engineer_name',
                'engineer_license',
                'electrical_engineer_name',
                'electrical_engineer_license',
                'sanitary_engineer_name',
                'sanitary_engineer_license',
            ]);
        });
    }
};