<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchiveFieldsToApplicationDocumentsTable extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('status');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->unsignedBigInteger('archived_by')->nullable()->after('archived_at');
            $table->text('archive_reason')->nullable()->after('archived_by');
            
            // Add foreign key constraint
            $table->foreign('archived_by')->references('id')->on('users')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn(['is_archived', 'archived_at', 'archived_by', 'archive_reason']);
        });
    }
}