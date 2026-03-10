<?php
// database/migrations/xxxx_update_users_role_check_constraint.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For PostgreSQL, drop the existing check constraint and add a new one
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        
        // Add new constraint with 'staff' instead of 'engineer'
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'applicant'))");
        
        // Update existing engineer records to staff
        DB::table('users')->where('role', 'engineer')->update(['role' => 'staff']);
    }

    public function down()
    {
        // Revert the constraint back to original
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'engineer', 'applicant'))");
        
        // Revert staff back to engineer (be careful with this)
        DB::table('users')->where('role', 'staff')->update(['role' => 'engineer']);
    }
};