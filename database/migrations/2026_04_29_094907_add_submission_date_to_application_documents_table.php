<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->date('submission_date')->nullable()->after('submitted_at');
            // Add index for faster daily queries
            $table->index(['user_id', 'submission_date', 'status']);
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn('submission_date');
        });
    }
};