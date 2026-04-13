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
            // Add checkbox status columns for document verification
            $table->boolean('tct_checked')->default(false)->after('tct_link')->comment('Checked by Assessor');
            $table->boolean('tax_declaration_checked')->default(false)->after('tax_declaration_link')->comment('Checked by Treasurer');
            $table->boolean('tax_receipt_checked')->default(false)->after('current_tax_receipt_link')->comment('Checked by Treasurer');
            $table->timestamp('auto_approved_at')->nullable()->after('approved_at')->comment('When auto-approval happened');
            
            // Remove deed_of_sale_link column since it's no longer used (TCT/Deed of Sale combined)
            $table->dropColumn('deed_of_sale_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_requirements', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn([
                'tct_checked',
                'tax_declaration_checked',
                'tax_receipt_checked',
                'auto_approved_at'
            ]);
            
            // Re-add deed_of_sale_link column
            $table->string('deed_of_sale_link', 255)->nullable()->after('current_tax_receipt_link');
        });
    }
};