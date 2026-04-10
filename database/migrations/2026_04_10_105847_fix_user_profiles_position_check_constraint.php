<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUserProfilesPositionCheckConstraint extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop the existing constraint if it exists
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            
            // Add new constraint with all positions (including both cases for BFP)
            DB::statement("
                ALTER TABLE user_profiles 
                ADD CONSTRAINT user_profiles_position_check 
                CHECK (position IS NULL OR position IN (
                    'engineer', 
                    'architect', 
                    'BFP', 
                    'bfp', 
                    'cpdo', 
                    'administrative_aide', 
                    'treasurer', 
                    'assessor'
                ))
            ");
        }
        
        // For MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            // Drop existing constraint if any
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            
            // Modify the ENUM column to include all values
            DB::statement("
                ALTER TABLE user_profiles 
                MODIFY COLUMN position ENUM(
                    'engineer', 
                    'architect', 
                    'BFP', 
                    'cpdo', 
                    'administrative_aide', 
                    'treasurer', 
                    'assessor'
                ) NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop the constraint
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            
            // Restore original constraint (without treasurer and assessor)
            DB::statement("
                ALTER TABLE user_profiles 
                ADD CONSTRAINT user_profiles_position_check 
                CHECK (position IS NULL OR position IN (
                    'engineer', 
                    'architect', 
                    'BFP', 
                    'cpdo', 
                    'administrative_aide'
                ))
            ");
        }
        
        // For MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE user_profiles 
                MODIFY COLUMN position ENUM(
                    'engineer', 
                    'architect', 
                    'BFP', 
                    'cpdo', 
                    'administrative_aide'
                ) NULL
            ");
        }
    }
}