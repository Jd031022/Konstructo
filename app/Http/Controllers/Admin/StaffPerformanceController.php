<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffPerformanceController extends Controller
{
    /**
     * Get staff performance data
     */
    public function getPerformance()
    {
        try {
            // Get all staff users
            $staff = User::whereIn('role', ['staff', 'admin'])->get();
            
            $performanceData = [];
            $totalProcessed = 0;
            
            foreach ($staff as $user) {
                // Count activities this week
                $processed = ApplicationReviewActivity::where('reviewer_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();
                
                $performanceData[] = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'role' => $user->role,
                    'processed' => $processed,
                ];
                
                $totalProcessed += $processed;
            }
            
            // Sort by processed count (highest first)
            usort($performanceData, function($a, $b) {
                return $b['processed'] - $a['processed'];
            });
            
            $staffCount = count($staff);
            $avgPerStaff = $staffCount > 0 ? round($totalProcessed / $staffCount, 1) : 0;
            
            // Calculate trends (compare with previous week)
            $lastWeekProcessed = ApplicationReviewActivity::where('created_at', '>=', now()->subDays(14))
                ->where('created_at', '<', now()->subDays(7))
                ->count();
            
            $processedTrend = $lastWeekProcessed > 0 
                ? round((($totalProcessed - $lastWeekProcessed) / $lastWeekProcessed) * 100)
                : 0;
            
            return response()->json([
                'success' => true,
                'staff' => array_slice($performanceData, 0, 5), // Top 5
                'total_processed' => $totalProcessed,
                'avg_per_staff' => $avgPerStaff,
                'processed_trend' => $processedTrend,
                'avg_trend' => $processedTrend
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getPerformance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading staff performance'
            ], 500);
        }
    }
}