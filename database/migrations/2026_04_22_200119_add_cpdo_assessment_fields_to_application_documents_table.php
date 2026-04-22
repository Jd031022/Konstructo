<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->date('cpdo_assessment_date')->nullable();
            $table->decimal('cpdo_zonal_location_fee', 12, 2)->nullable();
            $table->decimal('cpdo_palc_fee', 12, 2)->nullable();
            $table->decimal('cpdo_development_permit_fee', 12, 2)->nullable();
            $table->decimal('cpdo_alteration_permit_fee', 12, 2)->nullable();
            $table->decimal('cpdo_site_zoning_certificate_fee', 12, 2)->nullable();
            $table->decimal('cpdo_total_amount', 12, 2)->nullable();
            $table->text('cpdo_assessment_notes')->nullable();
            $table->json('cpdo_additional_fees')->nullable();
            $table->unsignedBigInteger('cpdo_assessed_by')->nullable();
            $table->timestamp('cpdo_assessed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn([
                'cpdo_assessment_date',
                'cpdo_zonal_location_fee',
                'cpdo_palc_fee',
                'cpdo_development_permit_fee',
                'cpdo_alteration_permit_fee',
                'cpdo_site_zoning_certificate_fee',
                'cpdo_total_amount',
                'cpdo_assessment_notes',
                'cpdo_additional_fees',
                'cpdo_assessed_by',
                'cpdo_assessed_at'
            ]);
        });
    }
};