<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->string('zoning_cert_link')->nullable()->after('rejection_reason');
            $table->string('locational_clearance_link')->nullable()->after('zoning_cert_link');
            $table->timestamp('zoning_cert_uploaded_at')->nullable()->after('locational_clearance_link');
            $table->timestamp('locational_clearance_uploaded_at')->nullable()->after('zoning_cert_uploaded_at');
            $table->bigInteger('zoning_cert_uploaded_by')->nullable()->after('locational_clearance_uploaded_at');
            $table->bigInteger('locational_clearance_uploaded_by')->nullable()->after('zoning_cert_uploaded_by');
            
            $table->foreign('zoning_cert_uploaded_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('locational_clearance_uploaded_by')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropForeign(['zoning_cert_uploaded_by']);
            $table->dropForeign(['locational_clearance_uploaded_by']);
            $table->dropColumn([
                'zoning_cert_link',
                'locational_clearance_link',
                'zoning_cert_uploaded_at',
                'locational_clearance_uploaded_at',
                'zoning_cert_uploaded_by',
                'locational_clearance_uploaded_by'
            ]);
        });
    }
};