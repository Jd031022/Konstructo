<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing check constraint
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        
        // Add new check constraint with all allowed values
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK (status IN ('draft', 'pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'))");
    }

    public function down()
    {
        DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
        DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK (status IN ('draft', 'pending', 'under-review', 'rejected', 'for-release', 'verified'))");
    }
};