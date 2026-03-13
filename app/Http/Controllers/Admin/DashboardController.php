<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            Log::info('Admin Dashboard getStats called');
            
            // Total applications (excluding drafts)
            $totalApplications = ApplicationDocument::whereIn('status', [
                'pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'
            ])->count();
            
            // Pending applications
            $pendingApplications = ApplicationDocument::where('status', 'pending')->count();
            
            // Count by status
            $statusCounts = [
                'pending' => ApplicationDocument::where('status', 'pending')->count(),
                'under_review' => ApplicationDocument::where('status', 'under-review')->count(),
                'approved' => ApplicationDocument::where('status', 'approved')->count(),
                'rejected' => ApplicationDocument::where('status', 'rejected')->count(),
                'for_release' => ApplicationDocument::where('status', 'for-release')->count(),
                'verified' => ApplicationDocument::where('status', 'verified')->count(),
            ];
            
            // Active users (users who have logged in recently)
            // If you don't have last_login_at, just count verified users
            $activeUsers = User::whereNotNull('email_verified_at')->count();
            
            // New users this week
            $newUsersWeek = User::where('created_at', '>=', now()->subDays(7))->count();
            
            // Calculate completion rate (verified + approved + for-release) / total
            $completedCount = ($statusCounts['verified'] + $statusCounts['approved'] + $statusCounts['for_release']);
            $completionRate = $totalApplications > 0 ? round(($completedCount / $totalApplications) * 100) : 0;
            
            // Calculate pending aging (applications pending for more than 7 days)
            $pendingAging = ApplicationDocument::where('status', 'pending')
                ->where('created_at', '<=', now()->subDays(7))
                ->count();
            
            // Calculate average processing time (in days) - simplified version
            $avgDays = 0;
            try {
                // Get completed applications with both created_at and verified_at
                $completedApps = ApplicationDocument::where('status', 'verified')
                    ->whereNotNull('verified_at')
                    ->whereNotNull('created_at')
                    ->select('created_at', 'verified_at')
                    ->limit(100)
                    ->get();
                
                if ($completedApps->count() > 0) {
                    $totalDays = 0;
                    foreach ($completedApps as $app) {
                        $created = strtotime($app->created_at);
                        $verified = strtotime($app->verified_at);
                        $days = ($verified - $created) / 86400; // seconds to days
                        $totalDays += $days;
                    }
                    $avgDays = round($totalDays / $completedApps->count(), 1);
                }
            } catch (\Exception $e) {
                Log::warning('Error calculating avg processing time: ' . $e->getMessage());
                $avgDays = 0;
            }
            
            // Calculate trends (compare with previous month)
            $lastMonthApps = ApplicationDocument::whereIn('status', [
                'pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'
            ])->where('created_at', '>=', now()->subDays(60))
              ->where('created_at', '<', now()->subDays(30))
              ->count();
            
            $currentMonthApps = ApplicationDocument::whereIn('status', [
                'pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'
            ])->where('created_at', '>=', now()->subDays(30))
              ->count();
            
            $applicationsTrend = $lastMonthApps > 0 
                ? '+' . round((($currentMonthApps - $lastMonthApps) / $lastMonthApps) * 100) . '%'
                : '+0%';
            
            $lastMonthCompleted = ApplicationDocument::whereIn('status', ['verified', 'approved', 'for-release'])
                ->where('created_at', '>=', now()->subDays(60))
                ->where('created_at', '<', now()->subDays(30))
                ->count();
            
            $currentMonthCompleted = ApplicationDocument::whereIn('status', ['verified', 'approved', 'for-release'])
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            $completionTrend = $lastMonthCompleted > 0 
                ? '+' . round((($currentMonthCompleted - $lastMonthCompleted) / $lastMonthCompleted) * 100) . '%'
                : '+0%';
            
            return response()->json([
                'success' => true,
                'total_applications' => $totalApplications,
                'pending_applications' => $pendingApplications,
                'active_users' => $activeUsers,
                'completion_rate' => $completionRate,
                'applications_trend' => $applicationsTrend,
                'pending_aging' => $pendingAging,
                'new_users_week' => $newUsersWeek,
                'completion_trend' => $completionTrend,
                'status_counts' => $statusCounts,
                'avg_processing_time' => $avgDays . ' days'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trend data for chart
     */
    public function getTrend(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            
            $data = [];
            $endDate = now();
            $startDate = now()->subDays($days);
            
            // Generate labels based on days
            if ($days <= 31) {
                // Daily for up to 31 days
                $currentDate = clone $startDate;
                while ($currentDate <= $endDate) {
                    $label = $currentDate->format('M d');
                    $count = ApplicationDocument::whereDate('created_at', $currentDate->toDateString())
                        ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                        ->count();
                    
                    $data[] = [
                        'label' => $label,
                        'count' => $count
                    ];
                    
                    $currentDate->addDay();
                }
            } else {
                // Weekly for longer periods
                $weeks = ceil($days / 7);
                for ($i = 0; $i < $weeks; $i++) {
                    $weekStart = now()->subWeeks($i + 1);
                    $weekEnd = now()->subWeeks($i);
                    $label = 'Week ' . ($weeks - $i);
                    
                    $count = ApplicationDocument::whereBetween('created_at', [$weekStart, $weekEnd])
                        ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                        ->count();
                    
                    array_unshift($data, [
                        'label' => $label,
                        'count' => $count
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'trend_data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getTrend: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading trend data'
            ], 500);
        }
    }
}