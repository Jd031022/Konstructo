<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePositionsInUserProfilesTable extends Migration
{
    public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // First, drop the existing constraint if it exists
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            
            // Update any NULL or invalid values to a default valid value or keep as NULL
            // This ensures all existing rows comply with the new constraint
            DB::statement("UPDATE user_profiles SET position = NULL WHERE position IS NOT NULL AND position NOT IN ('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide')");
            
            // Add the new constraint with all positions
            DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position IS NULL OR position IN ('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide', 'treasurer', 'assessor'))");
        }
        
        // For MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_profiles MODIFY COLUMN position ENUM('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide', 'treasurer', 'assessor') NULL");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position IS NULL OR position IN ('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide'))");
        }
        
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_profiles MODIFY COLUMN position ENUM('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide') NULL");
        }
    }
}