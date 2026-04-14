<?php
// database/migrations/2026_04_14_000000_remove_basic_requirements_foreign_key.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign key from application_documents
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropForeign(['basic_requirement_id']);
            $table->dropColumn('basic_requirement_id');
        });

        // Drop basic_requirements table
        Schema::dropIfExists('basic_requirements');
    }

    public function down()
    {
        Schema::create('basic_requirements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('tct_link')->nullable();
            $table->string('tax_declaration_link')->nullable();
            $table->string('current_tax_receipt_link')->nullable();
            $table->string('spa_link')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->bigInteger('approved_by')->nullable();
            $table->timestamps();
            $table->bigInteger('application_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('reviewed_by')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('tct_checked')->default(false);
            $table->boolean('tax_declaration_checked')->default(false);
            $table->boolean('tax_receipt_checked')->default(false);
            $table->timestamp('auto_approved_at')->nullable();
        });

        Schema::table('application_documents', function (Blueprint $table) {
            $table->bigInteger('basic_requirement_id')->nullable();
            $table->foreign('basic_requirement_id')->references('id')->on('basic_requirements')->onDelete('SET NULL');
        });
    }
};