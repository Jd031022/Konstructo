<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBfpApplicationDataTable extends Migration
{
    public function up()
    {
        Schema::create('bfp_application_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('bfp_user_id')->nullable(); // BFP staff who uploaded/updated
            $table->string('fsec_link')->nullable(); // Fire Safety Evaluation Clearance file link
            $table->string('fsec_filename')->nullable(); // Original filename
            $table->text('bfp_comments')->nullable(); // BFP comments/recommendations
            $table->timestamp('fsec_uploaded_at')->nullable();
            $table->timestamp('bfp_comments_updated_at')->nullable();
            $table->timestamps();
            
            $table->foreign('application_id')->references('id')->on('application_documents')->onDelete('cascade');
            $table->foreign('bfp_user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('application_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bfp_application_data');
    }
}