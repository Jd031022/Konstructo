<?php
// database/migrations/2026_04_07_create_assessment_fees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentFeesTable extends Migration
{
    public function up()
    {
        Schema::create('assessment_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->decimal('line_grade', 10, 2)->nullable();
            $table->decimal('building_fee', 10, 2)->nullable();
            $table->decimal('sanitary_fee', 10, 2)->nullable();
            $table->decimal('mechanical_fee', 10, 2)->nullable();
            $table->decimal('electrical_fee', 10, 2)->nullable();
            $table->decimal('others_amount', 10, 2)->nullable();
            $table->string('others_description')->nullable();
            $table->decimal('penalties_fines', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->text('assessment_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('application_id')->references('id')->on('application_documents')->onDelete('cascade');
            $table->foreign('assessed_by')->references('id')->on('users')->onDelete('set null');
            $table->index('application_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_fees');
    }
}