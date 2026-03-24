<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('application_documents', 'basic_requirement_id')) {
                $table->foreignId('basic_requirement_id')->nullable()->after('user_id')
                    ->constrained('basic_requirements')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropForeign(['basic_requirement_id']);
            $table->dropColumn('basic_requirement_id');
        });
    }
};