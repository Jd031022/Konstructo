<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->string('hardcopy_submission_date')->nullable()->after('hard_copy_received_at');
            $table->text('hardcopy_instructions')->nullable()->after('hardcopy_submission_date');
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['hardcopy_submission_date', 'hardcopy_instructions']);
        });
    }
};