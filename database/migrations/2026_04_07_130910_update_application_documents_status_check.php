<?php
// database/migrations/2026_04_07_update_application_documents_status_check.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateApplicationDocumentsStatusCheck extends Migration
{
    public function up()
    {
        // Drop the existing check constraint
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        
        // Recreate the check constraint with 'for-assessment' included
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK ((status)::text = ANY (ARRAY[('draft'::character varying)::text, ('pending'::character varying)::text, ('under-review'::character varying)::text, ('document-verification'::character varying)::text, ('for-assessment'::character varying)::text, ('approved'::character varying)::text, ('rejected'::character varying)::text, ('for-release'::character varying)::text, ('verified'::character varying)::text]))");
    }

    public function down()
    {
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK ((status)::text = ANY (ARRAY[('draft'::character varying)::text, ('pending'::character varying)::text, ('under-review'::character varying)::text, ('document-verification'::character varying)::text, ('approved'::character varying)::text, ('rejected'::character varying)::text, ('for-release'::character varying)::text, ('verified'::character varying)::text]))");
    }
}