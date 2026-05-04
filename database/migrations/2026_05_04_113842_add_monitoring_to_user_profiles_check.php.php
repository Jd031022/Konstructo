<?php
// database/migrations/2026_01_xx_xxxxxx_add_monitoring_to_user_profiles_check.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing check constraint
        DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
        
        // Re-add the check constraint with 'monitoring' included
        DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position::text = ANY (ARRAY['engineer'::character varying, 'architect'::character varying, 'BFP'::character varying, 'cpdo'::character varying, 'administrative_aide'::character varying, 'treasurer'::character varying, 'assessor'::character varying, 'mayor'::character varying, 'monitoring'::character varying]::text[]))");
    }

    public function down()
    {
        // Drop the constraint
        DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
        
        // Restore original constraint (without monitoring)
        DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position::text = ANY (ARRAY['engineer'::character varying, 'architect'::character varying, 'BFP'::character varying, 'cpdo'::character varying, 'administrative_aide'::character varying, 'treasurer'::character varying, 'assessor'::character varying, 'mayor'::character varying]::text[]))");
    }
};