<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_assessment_fees_table.php

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
        Schema::table('assessment_fees', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['others_amount', 'others_description']);
            
            // Add new column for additional fees
            $table->json('additional_fees')->nullable()->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_fees', function (Blueprint $table) {
            // Re-add old columns
            $table->decimal('others_amount', 10, 2)->nullable()->after('electrical_fee');
            $table->string('others_description', 255)->nullable()->after('others_amount');
            
            // Drop new column
            $table->dropColumn('additional_fees');
        });
    }
};