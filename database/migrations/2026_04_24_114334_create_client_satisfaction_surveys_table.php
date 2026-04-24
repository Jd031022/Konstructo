<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('application_documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('client_type', ['citizen', 'business', 'government']);
            $table->date('survey_date');
            $table->enum('sex', ['male', 'female']);
            $table->integer('age');
            $table->string('region_of_residence')->nullable();
            $table->string('service_availed');
            
            // CC Questions
            $table->enum('cc1_awareness', ['1', '2', '3', '4']);
            $table->enum('cc2_helpfulness', ['1', '2', '3', '4', '5'])->nullable();
            $table->enum('cc3_help_level', ['1', '2', '3', '4'])->nullable();
            
            // SQD Questions
            $table->enum('sqd0_satisfied', ['1', '2', '3', '4', '5']);
            $table->enum('sqd1_reasonable_time', ['1', '2', '3', '4', '5']);
            $table->enum('sqd2_requirements_followed', ['1', '2', '3', '4', '5']);
            $table->enum('sqd3_steps_easy', ['1', '2', '3', '4', '5']);
            $table->enum('sqd4_info_easy_find', ['1', '2', '3', '4', '5']);
            $table->enum('sqd5_reasonable_fees', ['1', '2', '3', '4', '5']);
            $table->enum('sqd6_fair_treatment', ['1', '2', '3', '4', '5']);
            $table->enum('sqd7_courteous_staff', ['1', '2', '3', '4', '5']);
            $table->enum('sqd8_got_what_needed', ['1', '2', '3', '4', '5']);
            
            // Optional fields
            $table->text('suggestions')->nullable();
            $table->string('email')->nullable();
            
            $table->timestamps();
            
            $table->unique(['application_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_satisfaction_surveys');
    }
};
