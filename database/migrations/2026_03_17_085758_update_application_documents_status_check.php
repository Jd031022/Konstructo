<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing check constraint
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        
        // Add the new check constraint with 'document-verification' included
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'pending'::character varying, 'under-review'::character varying, 'document-verification'::character varying, 'approved'::character varying, 'rejected'::character varying, 'for-release'::character varying, 'verified'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the updated constraint
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        
        // Restore the original constraint
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'pending'::character varying, 'under-review'::character varying, 'approved'::character varying, 'rejected'::character varying, 'for-release'::character varying, 'verified'::character varying]::text[]))");
    }
};