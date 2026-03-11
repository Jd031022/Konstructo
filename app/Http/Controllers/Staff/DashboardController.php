<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getStats()
    {
        try {
            // Only count submitted applications (exclude drafts)
            $total = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])->count();
            $pending = ApplicationDocument::where('status', 'pending')->count();
            $underReview = ApplicationDocument::where('status', 'under-review')->count();
            $approved = ApplicationDocument::where('status', 'approved')->count();
            $rejected = ApplicationDocument::where('status', 'rejected')->count();
            $forRelease = ApplicationDocument::where('status', 'for-release')->count();
            $verified = ApplicationDocument::where('status', 'verified')->count();
            
            // Get this month's total
            $thisMonthTotal = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
                
            // Get last month's total for growth calculation
            $lastMonthTotal = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
                
            // Get new today
            $newToday = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->whereDate('created_at', today())
                ->count();
            
            return response()->json([
                'total' => $total,
                'pending' => $pending,
                'under_review' => $underReview,
                'approved' => $approved,
                'rejected' => $rejected,
                'for_release' => $forRelease,
                'verified' => $verified,
                'completed' => $verified,
                'this_month_total' => $thisMonthTotal,
                'last_month_total' => $lastMonthTotal,
                'new_today' => $newToday,
                'completion_rate' => $total > 0 ? round(($verified / $total) * 100) : 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            
            return response()->json([
                'total' => 0,
                'pending' => 0,
                'under_review' => 0,
                'approved' => 0,
                'rejected' => 0,
                'for_release' => 0,
                'verified' => 0,
                'completed' => 0,
                'this_month_total' => 0,
                'last_month_total' => 0,
                'new_today' => 0,
                'completion_rate' => 0
            ]);
        }
    }
    
    public function getWeeklyTrend(Request $request)
    {
        try {
            $period = $request->get('period', 'this_month');
            
            // Get weekly data from database (only submitted applications)
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            
            $weeklyData = ApplicationDocument::select(
                DB::raw('EXTRACT(WEEK FROM created_at) as week'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('week')
            ->orderBy('week')
            ->get();
            
            // Format for chart
            $weeks = [];
            $values = [];
            
            foreach ($weeklyData as $data) {
                $weeks[] = 'Week ' . ($data->week - $startOfMonth->week + 1);
                $values[] = $data->total;
            }
            
            // If no data, return empty arrays
            return response()->json([
                'weeks' => $weeks,
                'values' => $values
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getWeeklyTrend: ' . $e->getMessage());
            
            return response()->json([
                'weeks' => [],
                'values' => []
            ]);
        }
    }
    
    public function getRecentActivities()
    {
        try {
            // Get recent activities from applications (exclude drafts)
            $recentApplications = ApplicationDocument::with('user')
                ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
            
            $activities = [];
            
            foreach ($recentApplications as $app) {
                $action = $this->getActionFromStatus($app->status);
                $activities[] = [
                    'action' => $action,
                    'description' => $app->user ? 
                        "Application #{$app->application_number} {$action} by {$app->user->first_name} {$app->user->last_name}" : 
                        "Application #{$app->application_number} {$action}",
                    'created_at' => $app->updated_at->toDateTimeString()
                ];
            }
            
            return response()->json($activities);
            
        } catch (\Exception $e) {
            Log::error('Error in getRecentActivities: ' . $e->getMessage());
            
            return response()->json([]);
        }
    }
    
    public function getUpcomingDeadlines()
    {
        try {
            // Get applications that need attention (pending for more than 5 days)
            $deadlines = ApplicationDocument::with('user')
                ->whereIn('status', ['pending', 'under-review'])
                ->where('created_at', '<=', now()->subDays(5))
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($app) {
                    $daysOld = now()->diffInDays($app->created_at);
                    $daysLeft = max(0, 14 - $daysOld); // Assuming 14-day processing time
                    
                    return [
                        'application_name' => 'Application #' . $app->application_number,
                        'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        'days_left' => $daysLeft,
                        'due_date' => $app->created_at->addDays(14)->format('M d, Y'),
                        'status' => $app->status
                    ];
                });
            
            return response()->json($deadlines);
            
        } catch (\Exception $e) {
            Log::error('Error in getUpcomingDeadlines: ' . $e->getMessage());
            
            return response()->json([]);
        }
    }
    
    private function getActionFromStatus($status)
    {
        return match($status) {
            'pending' => 'submitted',
            'under-review' => 'is under review',
            'approved' => 'was approved',
            'rejected' => 'was rejected',
            'for-release' => 'is ready for release',
            'verified' => 'was completed',
            default => 'was updated'
        };
    }
}