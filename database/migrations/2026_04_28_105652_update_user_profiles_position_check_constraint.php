<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop the existing check constraint
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            
            // Add new check constraint with 'mayor' allowed
            DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position = ANY (ARRAY['engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide', 'treasurer', 'assessor', 'mayor', NULL]))");
        }
        // For MySQL
        else {
            // MySQL doesn't have check constraints like PostgreSQL,
            // but if you have an ENUM column, you need to modify it:
            DB::statement("ALTER TABLE user_profiles MODIFY COLUMN position ENUM('engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide', 'treasurer', 'assessor', 'mayor') NULL");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE user_profiles DROP CONSTRAINT IF EXISTS user_profiles_position_check');
            DB::statement("ALTER TABLE user_profiles ADD CONSTRAINT user_profiles_position_check CHECK (position = ANY (ARRAY['engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide', 'treasurer', 'assessor', NULL]))");
        }
    }
};