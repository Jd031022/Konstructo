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
        Schema::table('user_sessions', function (Blueprint $table) {
            // Add any missing columns
            if (!Schema::hasColumn('user_sessions', 'device_info')) {
                $table->json('device_info')->nullable()->after('user_agent');
            }
            
            if (!Schema::hasColumn('user_sessions', 'login_at')) {
                $table->timestamp('login_at')->nullable()->after('session_id');
            }
            
            if (!Schema::hasColumn('user_sessions', 'logout_at')) {
                $table->timestamp('logout_at')->nullable()->after('last_activity_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $columns = ['device_info', 'login_at', 'logout_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('user_sessions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};