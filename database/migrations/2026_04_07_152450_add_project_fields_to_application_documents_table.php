<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectFieldsToApplicationDocumentsTable extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            // Step 1 fields - only add if they don't exist
            if (!Schema::hasColumn('application_documents', 'project_title')) {
                $table->string('project_title')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'project_location')) {
                $table->text('project_location')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'project_type')) {
                $table->string('project_type')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'lot_area')) {
                $table->decimal('lot_area', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'floor_area')) {
                $table->decimal('floor_area', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'num_floors')) {
                $table->integer('num_floors')->default(1);
            }
            if (!Schema::hasColumn('application_documents', 'estimated_cost')) {
                $table->decimal('estimated_cost', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'project_description')) {
                $table->text('project_description')->nullable();
            }
            
            // Owner information
            if (!Schema::hasColumn('application_documents', 'owner_name')) {
                $table->string('owner_name')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'owner_address')) {
                $table->text('owner_address')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'contact_number')) {
                $table->string('contact_number')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'owner_email')) {
                $table->string('owner_email')->nullable();
            }
            
            // Professional information
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
            
            // Step completion tracking
            if (!Schema::hasColumn('application_documents', 'step1_completed')) {
                $table->boolean('step1_completed')->default(false);
            }
            if (!Schema::hasColumn('application_documents', 'step1_completed_at')) {
                $table->timestamp('step1_completed_at')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'step2_completed')) {
                $table->boolean('step2_completed')->default(false);
            }
            if (!Schema::hasColumn('application_documents', 'step2_completed_at')) {
                $table->timestamp('step2_completed_at')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'step3_completed')) {
                $table->boolean('step3_completed')->default(false);
            }
            if (!Schema::hasColumn('application_documents', 'step3_completed_at')) {
                $table->timestamp('step3_completed_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $columns = [
                'project_title', 'project_location', 'project_type', 'lot_area', 'floor_area',
                'num_floors', 'estimated_cost', 'project_description', 'owner_name', 'owner_address',
                'contact_number', 'owner_email', 'architect_name', 'architect_license',
                'engineer_name', 'engineer_license', 'step1_completed', 'step1_completed_at',
                'step2_completed', 'step2_completed_at', 'step3_completed', 'step3_completed_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('application_documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}