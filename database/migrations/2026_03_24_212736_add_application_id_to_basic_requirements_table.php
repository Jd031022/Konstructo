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
        Schema::table('basic_requirements', function (Blueprint $table) {
            // Add application_id column
            if (!Schema::hasColumn('basic_requirements', 'application_id')) {
                $table->foreignId('application_id')->nullable()->after('user_id')
                    ->constrained('application_documents')
                    ->onDelete('cascade');
            }
            
            // Add reviewed_at column
            if (!Schema::hasColumn('basic_requirements', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('approved_at');
            }
            
            // Add reviewed_by column
            if (!Schema::hasColumn('basic_requirements', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
            
            // Add admin_notes column
            if (!Schema::hasColumn('basic_requirements', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('rejection_reason');
            }
            
            // Add indexes for better performance
            try {
                $table->index(['user_id', 'status'], 'basic_requirements_user_id_status_index');
            } catch (\Exception $e) {
                // Index might already exist
            }
            
            try {
                $table->index(['application_id', 'status'], 'basic_requirements_application_id_status_index');
            } catch (\Exception $e) {
                // Index might already exist
            }
            
            try {
                $table->index('submitted_at', 'basic_requirements_submitted_at_index');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_requirements', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('basic_requirements_user_id_status_index');
            $table->dropIndex('basic_requirements_application_id_status_index');
            $table->dropIndex('basic_requirements_submitted_at_index');
            
            // Drop foreign keys
            $table->dropForeign(['application_id']);
            $table->dropForeign(['reviewed_by']);
            
            // Drop columns
            if (Schema::hasColumn('basic_requirements', 'application_id')) {
                $table->dropColumn('application_id');
            }
            if (Schema::hasColumn('basic_requirements', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('basic_requirements', 'reviewed_by')) {
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('basic_requirements', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
};