<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            $filter = $request->get('filter', 'daily');
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);
            $day = $request->get('day', now()->day);

            $submittedQuery = ApplicationDocument::submitted();
            $baseQuery = clone $submittedQuery;

            // Apply filter
            if ($filter === 'daily') {
                $baseQuery->whereDate('created_at', "{$year}-{$month}-{$day}");
                $thisPeriodQuery = clone $baseQuery;
                $lastPeriodQuery = clone $submittedQuery;
                $lastPeriodQuery->whereDate('created_at', date('Y-m-d', strtotime("{$year}-{$month}-{$day} -1 day")));
            } elseif ($filter === 'monthly') {
                $baseQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $thisPeriodQuery = clone $baseQuery;
                $lastPeriodQuery = clone $submittedQuery;
                if ($month == 1) {
                    $lastPeriodQuery->whereYear('created_at', $year - 1)->whereMonth('created_at', 12);
                } else {
                    $lastPeriodQuery->whereYear('created_at', $year)->whereMonth('created_at', $month - 1);
                }
            } else { // yearly
                $baseQuery->whereYear('created_at', $year);
                $thisPeriodQuery = clone $baseQuery;
                $lastPeriodQuery = clone $submittedQuery;
                $lastPeriodQuery->whereYear('created_at', $year - 1);
            }
            
            // Get totals for the selected period
            $total = $baseQuery->count();
            $pending = (clone $baseQuery)->where('status', 'pending')->count();
            $underReview = (clone $baseQuery)->where('status', 'under-review')->count();
            $approved = (clone $baseQuery)->where('status', 'approved')->count();
            $rejected = (clone $baseQuery)->where('status', 'rejected')->count();
            $forRelease = (clone $baseQuery)->where('status', 'for-release')->count();
            $verified = (clone $baseQuery)->where('status', 'verified')->count();
            
            // Get this period total and last period total for growth calculation
            $thisPeriodTotal = $thisPeriodQuery->count();
            $lastPeriodTotal = $lastPeriodQuery->count();
            
            // Get new today (for daily view, show new for that day; for others, show today's)
            $newToday = clone $submittedQuery;
            $newToday = $newToday->whereDate('created_at', today())->count();
            
            // Get this month total (for the donut chart footer)
            $thisMonthTotal = clone $submittedQuery;
            $thisMonthTotal = $thisMonthTotal->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $avgProcessingTime = 0;
            $completedApps = ApplicationDocument::submitted()
                ->where('status', 'verified')
                ->whereNotNull('verified_at')
                ->whereNotNull('created_at')
                ->select('created_at', 'verified_at')
                ->limit(100)
                ->get();

            if ($completedApps->count() > 0) {
                $totalDays = 0;
                foreach ($completedApps as $app) {
                    $createdAt = strtotime($app->created_at);
                    $verifiedAt = strtotime($app->verified_at);
                    $totalDays += max(0, ($verifiedAt - $createdAt) / 86400);
                }
                $avgProcessingTime = round($totalDays / $completedApps->count(), 1);
            }
            
            return response()->json([
                'total' => $total,
                'pending' => $pending,
                'under_review' => $underReview,
                'approved' => $approved,
                'rejected' => $rejected,
                'for_release' => $forRelease,
                'verified' => $verified,
                'completed' => $verified,
                'this_period_total' => $thisPeriodTotal,
                'last_period_total' => $lastPeriodTotal,
                'this_month_total' => $thisMonthTotal,
                'last_month_total' => ApplicationDocument::submitted()
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count(),
                'new_today' => $newToday,
                'completion_rate' => $total > 0 ? round(($verified / $total) * 100) : 0,
                'avg_processing_time' => $avgProcessingTime . ' days'
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
                'this_period_total' => 0,
                'last_period_total' => 0,
                'this_month_total' => 0,
                'last_month_total' => 0,
                'new_today' => 0,
                'completion_rate' => 0
            ]);
        }
    }
    
   public function getTrendData(Request $request)
{
    try {
        // Log the incoming request
        Log::info('getTrendData called', [
            'period' => $request->get('period'),
            'filter' => $request->get('filter'),
            'year' => $request->get('year'),
            'month' => $request->get('month'),
            'day' => $request->get('day'),
            'all_params' => $request->all()
        ]);
        
        $period = $request->get('period', 'this_month');
        $filter = $request->get('filter', 'daily');
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $day = $request->get('day', now()->day);
        
        $labels = [];
        $values = [];

        $now = now();
        if ($filter === 'monthly') {
            if ($period === 'last_month' && $year == $now->year && $month == $now->month) {
                $lastMonth = $now->copy()->subMonth();
                $year = $lastMonth->year;
                $month = $lastMonth->month;
            }
        } elseif ($filter === 'yearly') {
            if ($period === 'last_year' && $year == $now->year) {
                $year = $now->copy()->subYear()->year;
            }
        }

        
        if ($filter === 'daily') {
            // For daily view, show hourly breakdown
            $date = sprintf("%d-%02d-%02d", $year, $month, $day);
            Log::info('Daily filter - date: ' . $date);
            
            $hourlyData = ApplicationDocument::submitted()->select(
                DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', $date)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');
            
            Log::info('Hourly data count: ' . count($hourlyData));
            
            for ($hour = 0; $hour <= 23; $hour++) {
                $labels[] = sprintf('%02d:00', $hour);
                $values[] = isset($hourlyData[$hour]) ? $hourlyData[$hour]->count : 0;
            }
            
        } elseif ($filter === 'monthly') {
            // For monthly view
            $startDate = sprintf("%d-%02d-01", $year, $month);
            $daysInMonth = date('t', strtotime($startDate));
            
            Log::info('Monthly filter - year: ' . $year . ', month: ' . $month . ', days: ' . $daysInMonth);
            
            $dailyData = ApplicationDocument::submitted()->select(
                DB::raw('EXTRACT(DAY FROM created_at) as day'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');
            
            Log::info('Daily data count: ' . count($dailyData));
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = "Day {$d}";
                $values[] = isset($dailyData[$d]) ? $dailyData[$d]->count : 0;
            }
            
        } else { // yearly view
            Log::info('Yearly filter - year: ' . $year);
            
            $monthlyData = ApplicationDocument::submitted()->select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
            
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $monthNames[$m - 1];
                $values[] = isset($monthlyData[$m]) ? $monthlyData[$m]->count : 0;
            }
        }
        
        $response = [
            'labels' => $labels,
            'values' => $values,
            'success' => true
        ];
        
        Log::info('getTrendData response', ['label_count' => count($labels), 'value_count' => count($values)]);
        
        return response()->json($response);
        
    } catch (\Exception $e) {
        Log::error('Error in getTrendData: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        // Return default data so chart shows something
        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'values' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
    
    public function getDailyData(Request $request)
    {
        try {
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);
            
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            $daysInMonth = date('t', strtotime($startDate));
            
            $dailyCounts = ApplicationDocument::submitted()->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
            
            $result = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateKey = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                $result[$dateKey] = isset($dailyCounts[$dateKey]) ? $dailyCounts[$dateKey]->count : 0;
            }
            
            return response()->json([
                'success' => true,
                'daily_counts' => $result,
                'year' => $year,
                'month' => $month
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDailyData: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'daily_counts' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getStaffPerformance()
    {
        try {
            $staff = User::whereIn('role', ['staff', 'admin'])->get();
            $performanceData = [];
            $totalProcessed = 0;
            
            foreach ($staff as $user) {
                $processed = ApplicationReviewActivity::where('reviewer_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();
                
                $performanceData[] = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'position' => optional($user->profile)->position ?? $user->position ?? $user->role,
                    'role' => $user->role,
                    'processed' => $processed,
                ];
                
                $totalProcessed += $processed;
            }
            
            usort($performanceData, function ($a, $b) {
                return $b['processed'] - $a['processed'];
            });
            
            $staffCount = count($staff);
            $avgPerStaff = $staffCount > 0 ? round($totalProcessed / $staffCount, 1) : 0;
            
            $lastWeekProcessed = ApplicationReviewActivity::where('created_at', '>=', now()->subDays(14))
                ->where('created_at', '<', now()->subDays(7))
                ->count();
            
            $processedTrend = $lastWeekProcessed > 0
                ? round((($totalProcessed - $lastWeekProcessed) / $lastWeekProcessed) * 100)
                : 0;
            
            return response()->json([
                'success' => true,
                'staff' => array_slice($performanceData, 0, 5),
                'total_processed' => $totalProcessed,
                'avg_per_staff' => $avgPerStaff,
                'processed_trend' => $processedTrend,
                'avg_trend' => $processedTrend
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getStaffPerformance: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading staff performance'
            ], 500);
        }
    }
    
    public function getWeeklyTrend(Request $request)
    {
        try {
            $period = $request->get('period', 'this_month');
            
            $labels = [];
            $values = [];
            
            if ($period === 'this_month') {
                // Get daily data for the current month
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                
                // Get all applications for this month
                $applications = ApplicationDocument::submitted()
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // Group by week
                $weeks = [];
                $weekLabels = [];
                
                // Calculate week ranges (4 weeks)
                $daysInMonth = now()->daysInMonth;
                $weekSize = ceil($daysInMonth / 4);
                
                for ($week = 0; $week < 4; $week++) {
                    $startDay = $week * $weekSize + 1;
                    $endDay = min(($week + 1) * $weekSize, $daysInMonth);
                    $weeks[$week] = 0;
                    $weekLabels[$week] = "Week " . ($week + 1);
                }
                
                // Count applications per week
                foreach ($applications as $app) {
                    $day = $app->created_at->day;
                    for ($week = 0; $week < 4; $week++) {
                        $startDay = $week * $weekSize + 1;
                        $endDay = min(($week + 1) * $weekSize, $daysInMonth);
                        if ($day >= $startDay && $day <= $endDay) {
                            $weeks[$week]++;
                            break;
                        }
                    }
                }
                
                $labels = $weekLabels;
                $values = array_values($weeks);
                
            } elseif ($period === 'last_month') {
                // Get data for last month
                $lastMonth = now()->subMonth();
                $startOfMonth = $lastMonth->copy()->startOfMonth();
                $endOfMonth = $lastMonth->copy()->endOfMonth();
                $daysInMonth = $lastMonth->daysInMonth;
                
                $applications = ApplicationDocument::submitted()
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $weeks = [];
                $weekLabels = [];
                $weekSize = ceil($daysInMonth / 4);
                
                for ($week = 0; $week < 4; $week++) {
                    $weeks[$week] = 0;
                    $weekLabels[$week] = "Week " . ($week + 1);
                }
                
                foreach ($applications as $app) {
                    $day = $app->created_at->day;
                    for ($week = 0; $week < 4; $week++) {
                        $startDay = $week * $weekSize + 1;
                        $endDay = min(($week + 1) * $weekSize, $daysInMonth);
                        if ($day >= $startDay && $day <= $endDay) {
                            $weeks[$week]++;
                            break;
                        }
                    }
                }
                
                $labels = $weekLabels;
                $values = array_values($weeks);
                
            } else { // this_year
                // Get monthly data for the year
                $monthlyData = ApplicationDocument::submitted()->select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
                
                $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                
                for ($month = 1; $month <= 12; $month++) {
                    $labels[] = $monthNames[$month - 1];
                    $values[] = isset($monthlyData[$month]) ? $monthlyData[$month]->total : 0;
                }
            }
            
            // If no data found, return sample data for testing (remove in production)
            if (empty($values) || array_sum($values) === 0) {
                // Return sample data to show the chart works
                $values = [12, 19, 15, 17];
                $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            }
            
            return response()->json([
                'labels' => $labels,
                'values' => $values,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getWeeklyTrend: ' . $e->getMessage());
            
            // Return sample data on error so chart shows something
            return response()->json([
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'values' => [0, 0, 0, 0],
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getRecentActivities()
    {
        try {
            // Get review activities from the review_activities table
            $reviewActivities = ApplicationReviewActivity::with(['reviewer', 'application'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $activities = [];
            
            foreach ($reviewActivities as $activity) {
                $actionDisplay = $this->getActionDisplayText($activity);
                $reviewerName = $activity->reviewer ? 
                    ($activity->reviewer->first_name . ' ' . $activity->reviewer->last_name) : 
                    'System';
                
                $activities[] = [
                    'action' => $activity->action,
                    'action_display' => $actionDisplay,
                    'description' => $actionDisplay,
                    'old_status' => $activity->old_status,
                    'new_status' => $activity->new_status,
                    'remarks' => $activity->remarks,
                    'reviewer_name' => $reviewerName,
                    'reviewer_id' => $activity->reviewer_id,
                    'application_id' => $activity->application_id,
                    'application_number' => $activity->application ? $activity->application->application_number : null,
                    'created_at' => $activity->created_at->toISOString(),
                ];
            }
            
            // If no review activities, fall back to application status changes
            if (empty($activities)) {
                $applications = ApplicationDocument::submitted()->with('user')
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get();
                
                foreach ($applications as $app) {
                    $actionDisplay = $this->getApplicationActionText($app);
                    $applicantName = $app->user ? 
                        ($app->user->first_name . ' ' . $app->user->last_name) : 
                        'Unknown';
                    
                    $activities[] = [
                        'action' => 'status_updated',
                        'action_display' => $actionDisplay,
                        'description' => $actionDisplay,
                        'old_status' => null,
                        'new_status' => $app->status,
                        'remarks' => null,
                        'reviewer_name' => $applicantName,
                        'reviewer_id' => null,
                        'application_id' => $app->id,
                        'application_number' => $app->application_number,
                        'created_at' => $app->updated_at->toISOString(),
                    ];
                }
            }
            
            return response()->json($activities);
            
        } catch (\Exception $e) {
            Log::error('Error in getRecentActivities: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([], 500);
        }
    }
    
    public function getUpcomingDeadlines()
    {
        try {
            // Get applications that need attention (pending or under review)
            $deadlines = ApplicationDocument::with('user')
                ->whereIn('status', ['pending', 'under-review', 'document-verification', 'for-assessment'])
                ->where('created_at', '<=', now()->subDays(3))
                ->orderBy('created_at', 'asc')
                ->limit(10)
                ->get()
                ->map(function ($app) {
                    $daysOld = now()->diffInDays($app->created_at);
                    $daysLeft = max(0, 14 - $daysOld); // Assuming 14-day processing time
                    
                    return [
                        'application_name' => $app->project_title ?: 'Application #' . $app->application_number,
                        'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        'days_left' => $daysLeft,
                        'due_date' => $app->created_at->addDays(14)->format('M d, Y'),
                        'status' => $app->status,
                        'application_number' => $app->application_number
                    ];
                });
            
            return response()->json($deadlines);
            
        } catch (\Exception $e) {
            Log::error('Error in getUpcomingDeadlines: ' . $e->getMessage());
            
            return response()->json([]);
        }
    }
    
    private function getActionDisplayText($activity)
    {
        switch ($activity->action) {
            case 'application_submitted':
                return 'Application Submitted';
            case 'status_updated':
                $oldStatus = $this->formatStatusForDisplay($activity->old_status);
                $newStatus = $this->formatStatusForDisplay($activity->new_status);
                if ($oldStatus && $newStatus && $oldStatus !== $newStatus) {
                    return "Status changed from {$oldStatus} to {$newStatus}";
                }
                return "Status changed to {$newStatus}";
            case 'document_verified':
                return 'Documents Verified';
            case 'document_rejected':
                return 'Documents Rejected';
            case 'hard_copy_received':
                return 'Hard Copy Received';
            case 'missing_documents_requested':
                return 'Missing Documents Requested';
            case 'note_added':
                return 'Note Added';
            case 'application_created':
                return 'Application Created';
            case 'application_deleted':
                return 'Application Deleted';
            case 'application_archived':
                return 'Application Archived';
            case 'application_restored':
                return 'Application Restored';
            case 'assessment_saved':
                return 'Assessment Saved';
            case 'assessment_completed':
                return 'Assessment Completed';
            case 'fsec_uploaded':
                return 'FSEC Document Uploaded';
            case 'fsec_deleted':
                return 'FSEC Document Deleted';
            case 'bfp_comments_added':
                return 'BFP Comments Added';
            case 'ownership_document_verified':
                return 'Ownership Document Verified';
            case 'ownership_document_unverified':
                return 'Ownership Document Unverified';
            case 'cpdo_approved':
                return 'CPDO Approved';
            case 'cpdo_rejected':
                return 'CPDO Rejected';
            default:
                return ucfirst(str_replace('_', ' ', $activity->action));
        }
    }
    
    private function getApplicationActionText($application)
    {
        $status = $application->status;
        
        switch ($status) {
            case 'pending':
                return 'Application Submitted';
            case 'under-review':
                return 'Application is Under Review';
            case 'document-verification':
                return 'Documents Under Verification';
            case 'for-assessment':
                return 'Application For Assessment';
            case 'approved':
                return 'Application Approved';
            case 'rejected':
                return 'Application Rejected';
            case 'for-release':
                return 'Application Ready for Release';
            case 'verified':
                return 'Application Completed';
            default:
                return 'Application Updated';
        }
    }
    
    private function formatStatusForDisplay($status)
    {
        if (!$status) return null;
        
        $statusMap = [
            'pending' => 'Pending',
            'under-review' => 'Under Review',
            'document-verification' => 'Document Verification',
            'for-assessment' => 'For Assessment',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'for-release' => 'For Release',
            'verified' => 'Completed'
        ];
        
        return $statusMap[$status] ?? ucfirst(str_replace('-', ' ', $status));
    }
    
    /**
     * Export dashboard data with multiple format options
     */
    public function exportDashboard(Request $request)
    {
        try {
            $format = $request->get('format', 'excel');
            
            // Get dashboard data
            $stats = $this->getExportStats();
            $trendData = $this->getExportTrendData();
            $recentActivities = $this->getExportRecentActivities();
            $deadlines = $this->getExportDeadlines();
            
            switch ($format) {
                case 'excel':
                    return $this->exportDashboardAsExcel($stats, $trendData, $recentActivities, $deadlines);
                case 'pdf':
                    return $this->exportDashboardAsPDF($stats, $trendData, $recentActivities, $deadlines);
                default:
                    return $this->exportDashboardAsExcel($stats, $trendData, $recentActivities, $deadlines);
            }
        } catch (\Exception $e) {
            Log::error('Dashboard export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get export statistics
     */
    private function getExportStats()
    {
        $statuses = ['pending', 'under-review', 'document-verification', 'for-assessment', 'approved', 'rejected', 'for-release', 'verified'];
        $total = ApplicationDocument::whereIn('status', $statuses)->count();
        $pending = ApplicationDocument::where('status', 'pending')->count();
        $underReview = ApplicationDocument::where('status', 'under-review')->count();
        $approved = ApplicationDocument::where('status', 'approved')->count();
        $rejected = ApplicationDocument::where('status', 'rejected')->count();
        $forRelease = ApplicationDocument::where('status', 'for-release')->count();
        $verified = ApplicationDocument::where('status', 'verified')->count();
        
        $thisMonthTotal = ApplicationDocument::whereIn('status', $statuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $lastMonthTotal = ApplicationDocument::whereIn('status', $statuses)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $growth = $lastMonthTotal > 0 ? round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100) : 0;
        
        return [
            'total' => $total,
            'pending' => $pending,
            'under_review' => $underReview,
            'approved' => $approved,
            'rejected' => $rejected,
            'for_release' => $forRelease,
            'verified' => $verified,
            'this_month_total' => $thisMonthTotal,
            'last_month_total' => $lastMonthTotal,
            'growth' => $growth,
            'completion_rate' => $total > 0 ? round(($verified / $total) * 100) : 0,
            'new_today' => ApplicationDocument::whereIn('status', $statuses)->whereDate('created_at', today())->count(),
        ];
    }
    
    /**
     * Get export trend data (last 30 days)
     */
    private function getExportTrendData()
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = ApplicationDocument::whereDate('created_at', $date->toDateString())->count();
            $data[] = [
                'label' => $date->format('M d'),
                'count' => $count
            ];
        }
        return $data;
    }
    
    /**
     * Get export recent activities
     */
    private function getExportRecentActivities()
    {
        return ApplicationReviewActivity::with(['reviewer', 'application'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get export deadlines
     */
    private function getExportDeadlines()
    {
        return ApplicationDocument::with('user')
            ->whereIn('status', ['pending', 'under-review'])
            ->where('created_at', '<=', now()->subDays(3))
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($app) {
                $daysOld = now()->diffInDays($app->created_at);
                $daysLeft = max(0, 14 - $daysOld);
                return [
                    'application_name' => $app->project_title ?: 'Application #' . $app->application_number,
                    'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                    'days_left' => $daysLeft,
                    'due_date' => $app->created_at->addDays(14)->format('M d, Y'),
                    'status' => $app->status
                ];
            });
    }
    
    /**
     * Export dashboard as Excel
     */
    private function exportDashboardAsExcel($stats, $trendData, $recentActivities, $deadlines)
    {
        $filename = 'dashboard_export_' . date('Y-m-d_His') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($stats, $trendData, $recentActivities, $deadlines) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title
            fputcsv($handle, ['Konstructo Dashboard Export']);
            fputcsv($handle, ['Generated: ' . date('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            
            // Statistics Summary
            fputcsv($handle, ['STATISTICS SUMMARY']);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Applications', $stats['total']]);
            fputcsv($handle, ['Pending Review', $stats['pending']]);
            fputcsv($handle, ['Under Review', $stats['under_review']]);
            fputcsv($handle, ['Approved', $stats['approved']]);
            fputcsv($handle, ['Rejected', $stats['rejected']]);
            fputcsv($handle, ['For Release', $stats['for_release']]);
            fputcsv($handle, ['Completed', $stats['verified']]);
            fputcsv($handle, ['This Month', $stats['this_month_total']]);
            fputcsv($handle, ['Growth vs Last Month', $stats['growth'] . '%']);
            fputcsv($handle, ['Completion Rate', $stats['completion_rate'] . '%']);
            fputcsv($handle, ['New Today', $stats['new_today']]);
            fputcsv($handle, []);
            
            // Status Distribution
            $total = $stats['total'];
            fputcsv($handle, ['STATUS DISTRIBUTION']);
            fputcsv($handle, ['Status', 'Count', 'Percentage']);
            fputcsv($handle, ['Pending', $stats['pending'], $total > 0 ? round(($stats['pending'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, ['Under Review', $stats['under_review'], $total > 0 ? round(($stats['under_review'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, ['Approved', $stats['approved'], $total > 0 ? round(($stats['approved'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, ['Rejected', $stats['rejected'], $total > 0 ? round(($stats['rejected'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, ['For Release', $stats['for_release'], $total > 0 ? round(($stats['for_release'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, ['Completed', $stats['verified'], $total > 0 ? round(($stats['verified'] / $total) * 100) . '%' : '0%']);
            fputcsv($handle, []);
            
            // Trend Data
            fputcsv($handle, ['APPLICATION TREND (LAST 30 DAYS)']);
            fputcsv($handle, ['Date', 'Applications']);
            foreach ($trendData as $item) {
                fputcsv($handle, [$item['label'], $item['count']]);
            }
            fputcsv($handle, []);
            
            // Recent Activities
            fputcsv($handle, ['RECENT ACTIVITIES']);
            fputcsv($handle, ['Date', 'Action', 'Reviewer', 'Remarks']);
            foreach ($recentActivities as $activity) {
                fputcsv($handle, [
                    $activity->created_at ? $activity->created_at->format('Y-m-d H:i') : '',
                    $activity->action_display ?? $activity->action ?? '',
                    $activity->reviewer_name ?? 'System',
                    $activity->remarks ?? ''
                ]);
            }
            fputcsv($handle, []);
            
            // Upcoming Deadlines
            fputcsv($handle, ['UPCOMING DEADLINES']);
            fputcsv($handle, ['Application', 'Applicant', 'Days Left', 'Due Date']);
            foreach ($deadlines as $deadline) {
                fputcsv($handle, [
                    $deadline['application_name'],
                    $deadline['applicant_name'],
                    $deadline['days_left'] . ' days',
                    $deadline['due_date']
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }
    
    /**
     * Export dashboard as PDF - simplified version that returns HTML
     * For full PDF functionality, install barryvdh/laravel-dompdf
     */
    private function exportDashboardAsPDF($stats, $trendData, $recentActivities, $deadlines)
    {
        $html = $this->generateDashboardPDFHTML($stats, $trendData, $recentActivities, $deadlines);
        
        // Return as HTML download (user can print to PDF)
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="dashboard_export_' . date('Y-m-d_His') . '.html"');
    }
    
    /**
     * Generate Dashboard PDF HTML content
     */
    private function generateDashboardPDFHTML($stats, $trendData, $recentActivities, $deadlines)
    {
        $total = $stats['total'];
        $pending = $stats['pending'];
        $underReview = $stats['under_review'];
        $approved = $stats['approved'];
        $forRelease = $stats['for_release'];
        $verified = $stats['verified'];
        $rejected = $stats['rejected'];
        $completionRate = $stats['completion_rate'];
        $growth = $stats['growth'];
        
        $totalTrend = array_sum(array_column($trendData, 'count'));
        $avgTrend = !empty($trendData) ? round($totalTrend / count($trendData)) : 0;
        $maxTrendValue = !empty($trendData) ? max(array_column($trendData, 'count')) : 1;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Dashboard Export - ' . date('Y-m-d H:i:s') . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #155386;
                }
                .header h1 {
                    color: #155386;
                    margin: 0;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 15px;
                    margin-bottom: 30px;
                }
                .stat-card {
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 15px;
                    text-align: center;
                }
                .stat-value {
                    font-size: 28px;
                    font-weight: bold;
                    color: #155386;
                }
                .stat-label {
                    font-size: 12px;
                    color: #666;
                    margin-top: 5px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }
                th {
                    background-color: #155386;
                    color: white;
                }
                .section-title {
                    background-color: #f0f0f0;
                    padding: 10px;
                    margin: 20px 0 10px 0;
                    font-weight: bold;
                    border-left: 4px solid #155386;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    font-size: 10px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Konstructo Dashboard Export</h1>
                <p>Generated: ' . date('F d, Y g:i:s A') . '</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">' . number_format($total) . '</div>
                    <div class="stat-label">Total Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . number_format($pending) . '</div>
                    <div class="stat-label">Pending Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . number_format($verified) . '</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">' . $completionRate . '%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
            </div>
            
            <div class="section-title">Application Trend (Last 30 Days)</div>
            <table>
                <thead>
                    <tr><th>Date</th><th>Applications</th></tr>
                </thead>
                <tbody>';
        
        foreach ($trendData as $item) {
            $html .= '<tr><td>' . $item['label'] . '</td><td>' . $item['count'] . '</td></tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="section-title">Status Distribution</div>
            <table>
                <thead>
                    <tr><th>Status</th><th>Count</th><th>Percentage</th></tr>
                </thead>
                <tbody>
                    <tr><td>Pending</td><td>' . number_format($pending) . '</td><td>' . ($total > 0 ? round(($pending / $total) * 100) : 0) . '%</td></tr>
                    <tr><td>Under Review</td><td>' . number_format($underReview) . '</td><td>' . ($total > 0 ? round(($underReview / $total) * 100) : 0) . '%</td></tr>
                    <tr><td>Approved</td><td>' . number_format($approved) . '</td><td>' . ($total > 0 ? round(($approved / $total) * 100) : 0) . '%</td></tr>
                    <tr><td>For Release</td><td>' . number_format($forRelease) . '</td><td>' . ($total > 0 ? round(($forRelease / $total) * 100) : 0) . '%</td></tr>
                    <tr><td>Completed</td><td>' . number_format($verified) . '</td><td>' . ($total > 0 ? round(($verified / $total) * 100) : 0) . '%</td></tr>
                    <tr><td>Rejected</td><td>' . number_format($rejected) . '</td><td>' . ($total > 0 ? round(($rejected / $total) * 100) : 0) . '%</td></tr>
                </tbody>
            </table>
            
            <div class="section-title">Recent Activities</div>
            <table>
                <thead>
                    <tr><th>Date & Time</th><th>Action</th><th>Reviewer</th><th>Remarks</th></tr>
                </thead>
                <tbody>';
        
        foreach ($recentActivities as $activity) {
            $html .= '<tr>
                <td>' . ($activity->created_at ? $activity->created_at->format('Y-m-d H:i') : '') . '</td>
                <td>' . htmlspecialchars($activity->action_display ?? $activity->action ?? '') . '</td>
                <td>' . htmlspecialchars($activity->reviewer_name ?? 'System') . '</td>
                <td>' . htmlspecialchars(substr($activity->remarks ?? '', 0, 60)) . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>Report ID: DASH-' . date('Ymd') . '-' . rand(1000, 9999) . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Get verified ownership documents based on user role (for dashboard and verified page)
     */
    public function getVerifiedOwnershipDocuments()
    {
        try {
            $user = Auth::user();
            $user->load('profile');
            $position = $user->profile ? $user->profile->position : null;
            $userId = $user->id;
            
            Log::info('Loading verified ownership documents', ['position' => $position, 'user_id' => $userId]);
            
            $documents = [];
            
            // Get all ownership verifications with related data using direct DB queries
            $verifications = DB::table('ownership_verifications as ov')
                ->join('application_documents as ad', 'ov.application_id', '=', 'ad.id')
                ->join('users as u', 'ad.user_id', '=', 'u.id')
                ->select(
                    'ov.*',
                    'ad.application_number',
                    'ad.id as application_id',
                    'u.id as user_id',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.address',
                    'ov.created_at'
                )
                ->orderBy('ov.updated_at', 'desc')
                ->get();
            
            foreach ($verifications as $verification) {
                // Based on user role, show different documents that they have verified
                if ($position === 'assessor') {
                    // Assessor verified TCT
                    if (!empty($verification->tct_link) && !is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'TCT / Deed of Sale',
                            'document_link' => $verification->tct_link,
                            'verified_at' => $verification->assessor_verified_at,
                            'verified_by_name' => $user->first_name . ' ' . $user->last_name
                        ];
                    }
                    // Assessor verified Tax Declaration
                    if (!empty($verification->tax_declaration_link) && !is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'Tax Declaration',
                            'document_link' => $verification->tax_declaration_link,
                            'verified_at' => $verification->assessor_verified_at,
                            'verified_by_name' => $user->first_name . ' ' . $user->last_name
                        ];
                    }
                } elseif ($position === 'treasurer') {
                    // Treasurer verified Current Tax Receipt
                    if (!empty($verification->current_tax_receipt_link) && !is_null($verification->treasurer_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'Current Tax Receipt',
                            'document_link' => $verification->current_tax_receipt_link,
                            'verified_at' => $verification->treasurer_verified_at,
                            'verified_by_name' => $user->first_name . ' ' . $user->last_name
                        ];
                    }
                    // Treasurer verified SPA
                    if (!empty($verification->spa_link) && !empty($verification->assessor_remarks) && strpos($verification->assessor_remarks, 'SPA verified by treasurer') !== false) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'Special Power of Attorney (SPA)',
                            'document_link' => $verification->spa_link,
                            'verified_at' => $verification->created_at,
                            'verified_by_name' => $user->first_name . ' ' . $user->last_name
                        ];
                    }
                } elseif ($position === 'cpdo') {
                    // CPDO verified TCT
                    if (!empty($verification->tct_link) && !is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'TCT / Deed of Sale',
                            'document_link' => $verification->tct_link,
                            'verified_at' => $verification->assessor_verified_at,
                            'verified_by_name' => $user->first_name . ' ' . $user->last_name
                        ];
                    }
                }
            }
            
            // Sort by verified_at descending
            usort($documents, function($a, $b) {
                return strtotime($b['verified_at']) - strtotime($a['verified_at']);
            });
            
            Log::info('Verified ownership documents loaded', ['count' => count($documents), 'position' => $position]);
            
            return response()->json([
                'success' => true,
                'verifications' => $documents,
                'user_position' => $position
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading verified ownership documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'verifications' => [],
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get ownership verifications for dashboard based on user role
     */
    public function getOwnershipVerificationsForDashboard()
    {
        try {
            $user = Auth::user();
            $user->load('profile');
            $position = $user->profile ? $user->profile->position : null;
            
            Log::info('Loading ownership verifications for dashboard', ['position' => $position, 'user_id' => $user->id]);
            
            $documents = [];
            
            // Get all ownership verifications with related data using direct DB queries
            $verifications = DB::table('ownership_verifications as ov')
                ->join('application_documents as ad', 'ov.application_id', '=', 'ad.id')
                ->join('users as u', 'ad.user_id', '=', 'u.id')
                ->select(
                    'ov.*',
                    'ad.application_number',
                    'ad.id as application_id',
                    'u.id as user_id',
                    'u.first_name',
                    'u.last_name',
                    'u.email',
                    'u.address',
                    'ov.created_at'
                )
                ->orderBy('ov.created_at', 'desc')
                ->get();
            
            Log::info('Found verifications count', ['count' => count($verifications)]);
            
            foreach ($verifications as $verification) {
                // Based on user role, show different documents that are pending verification
                if ($position === 'assessor') {
                    // Assessor sees: TCT and Tax Declaration (not yet verified)
                    if (!empty($verification->tct_link) && is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'TCT / Deed of Sale',
                            'document_link' => $verification->tct_link,
                            'created_at' => $verification->created_at
                        ];
                    }
                    if (!empty($verification->tax_declaration_link) && is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'Tax Declaration',
                            'document_link' => $verification->tax_declaration_link,
                            'created_at' => $verification->created_at
                        ];
                    }
                } elseif ($position === 'treasurer') {
                    // Treasurer sees: Current Tax Receipt (not yet verified)
                    if (!empty($verification->current_tax_receipt_link) && is_null($verification->treasurer_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'Current Tax Receipt',
                            'document_link' => $verification->current_tax_receipt_link,
                            'created_at' => $verification->created_at
                        ];
                    }
                    // Treasurer also sees SPA if not yet verified
                    if (!empty($verification->spa_link)) {
                        $isVerified = !empty($verification->assessor_remarks) && strpos($verification->assessor_remarks, 'SPA verified') !== false;
                        if (!$isVerified) {
                            $documents[] = [
                                'application_id' => $verification->application_id,
                                'application_number' => $verification->application_number ?? 'N/A',
                                'user_id' => $verification->user_id,
                                'first_name' => $verification->first_name,
                                'last_name' => $verification->last_name,
                                'email' => $verification->email,
                                'address' => $verification->address,
                                'document_type' => 'Special Power of Attorney (SPA)',
                                'document_link' => $verification->spa_link,
                                'created_at' => $verification->created_at
                            ];
                        }
                    }
                } elseif ($position === 'cpdo') {
                    // CPDO sees: TCT only (not yet verified)
                    if (!empty($verification->tct_link) && is_null($verification->assessor_verified_at)) {
                        $documents[] = [
                            'application_id' => $verification->application_id,
                            'application_number' => $verification->application_number ?? 'N/A',
                            'user_id' => $verification->user_id,
                            'first_name' => $verification->first_name,
                            'last_name' => $verification->last_name,
                            'email' => $verification->email,
                            'address' => $verification->address,
                            'document_type' => 'TCT / Deed of Sale',
                            'document_link' => $verification->tct_link,
                            'created_at' => $verification->created_at
                        ];
                    }
                }
            }
            
            Log::info('Ownership verifications loaded', ['count' => count($documents), 'position' => $position]);
            
            return response()->json([
                'success' => true,
                'verifications' => $documents,
                'user_position' => $position
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading ownership verifications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'verifications' => [],
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * Check user position
 */
public function checkPosition()
{
    try {
        $user = Auth::user();
        $user->load('profile');
        $position = $user->profile ? $user->profile->position : null;
        
        return response()->json([
            'position' => $position,
            'user_id' => $user->id
        ]);
    } catch (\Exception $e) {
        Log::error('Error checking position: ' . $e->getMessage());
        return response()->json(['position' => null], 500);
    }
}
}