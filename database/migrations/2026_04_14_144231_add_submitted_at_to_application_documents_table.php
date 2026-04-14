<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('application_documents', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });
    }
};