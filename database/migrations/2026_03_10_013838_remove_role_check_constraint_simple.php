<?php
// database/migrations/xxxx_xx_xx_xxxxxx_remove_role_check_constraint_simple.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET session_replication_role = replica;');
        
        // Get all check constraints on the users table
        $constraints = DB::select("
            SELECT 
                c.conname,
                pg_get_constraintdef(c.oid) as condef
            FROM pg_constraint c
            JOIN pg_class t ON c.conrelid = t.oid
            WHERE t.relname = 'users'
            AND c.contype = 'c'
        ");

        foreach ($constraints as $constraint) {
            // Check if this constraint involves the role column
            if (strpos($constraint->condef, 'role') !== false) {
                // Drop the constraint
                DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS {$constraint->conname}");
                echo "Dropped constraint: {$constraint->conname}\n";
            }
        }

        // Also try to drop by common naming patterns
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS check_role");
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check1");
        
        // Re-enable foreign key checks
        DB::statement('SET session_replication_role = origin;');
        
        // Ensure the column is just a regular string
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('applicant')->change();
        });
    }

    public function down()
    {
        // Re-add the original constraint if needed
        DB::statement("
            ALTER TABLE users 
            ADD CONSTRAINT users_role_check 
            CHECK (role::text = ANY (ARRAY['admin'::character varying, 'engineer'::character varying, 'applicant'::character varying]::text[]))
        ");
    }
};