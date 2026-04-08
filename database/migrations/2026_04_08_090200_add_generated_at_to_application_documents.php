<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('application_documents', 'application_number')) {
                $table->string('application_number')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('application_documents', 'generated_at')) {
                $table->timestamp('generated_at')->nullable()->after('application_number');
            }
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['generated_at']);
            // Don't drop application_number as it might be needed
        });
    }
};