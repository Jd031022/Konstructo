<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('c_p_d_o_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('rating')->comment('1-5 star rating');
            $table->string('processing_time')->nullable();
            $table->string('responsiveness')->nullable();
            $table->string('clarity')->nullable();
            $table->string('fairness')->nullable();
            $table->string('overall_satisfaction')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('application_id')->references('id')->on('application_documents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent duplicate ratings
            $table->unique(['application_id', 'user_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('c_p_d_o_ratings');
    }
};