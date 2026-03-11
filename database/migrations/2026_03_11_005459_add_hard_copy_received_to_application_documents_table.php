<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->boolean('hard_copy_received')->default(false)->after('status');
            $table->timestamp('hard_copy_received_at')->nullable()->after('hard_copy_received');
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['hard_copy_received', 'hard_copy_received_at']);
        });
    }
};