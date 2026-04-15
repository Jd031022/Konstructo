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
        Schema::table('application_documents', function (Blueprint $table) {
            // Add CPDO columns
            $table->string('cpdo_status')->default('pending')->after('status');
            $table->text('cpdo_remarks')->nullable()->after('cpdo_status');
            $table->timestamp('cpdo_approved_at')->nullable()->after('cpdo_remarks');
            $table->foreignId('cpdo_approved_by')->nullable()->after('cpdo_approved_at')
                  ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['cpdo_approved_by']);
            // Drop columns
            $table->dropColumn(['cpdo_status', 'cpdo_remarks', 'cpdo_approved_at', 'cpdo_approved_by']);
        });
    }
};