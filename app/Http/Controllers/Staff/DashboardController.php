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
        
        // Adjust dates based on period parameter
        if ($period === 'this_week') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
        } elseif ($period === 'last_week') {
            $startDate = $now->copy()->subWeek()->startOfWeek();
            $endDate = $now->copy()->subWeek()->endOfWeek();
        } elseif ($period === 'last_month') {
            $lastMonth = $now->copy()->subMonth();
            $year = $lastMonth->year;
            $month = $lastMonth->month;
            $startDate = $lastMonth->copy()->startOfMonth();
            $endDate = $lastMonth->copy()->endOfMonth();
        } elseif ($period === 'this_year') {
            $year = $now->year;
            $startDate = $now->copy()->startOfYear();
            $endDate = $now->copy()->endOfYear();
        } else { // this_month (default)
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
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
            // For monthly view - show daily breakdown
            $startDate = $startDate ?? $now->copy()->startOfMonth();
            $endDate = $endDate ?? $now->copy()->endOfMonth();
            $daysInMonth = $startDate->daysInMonth;
            
            Log::info('Monthly filter - start: ' . $startDate->format('Y-m-d') . ', end: ' . $endDate->format('Y-m-d'));
            
            $dailyData = ApplicationDocument::submitted()->select(
                DB::raw('EXTRACT(DAY FROM created_at) as day'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
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
            // Set timeout to prevent execution time errors
            set_time_limit(120);

            // Fetch all dashboard data
            $stats = $this->getExportStats();
            $trendData = $this->getExportTrendData();

            $recentApplications = ApplicationDocument::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(15)
                ->get();

            $userStats = [
                'admins' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'applicants' => User::where('role', 'applicant')->count(),
                'pending_applicants' => User::where('role', 'applicant')
                    ->where('approval_status', 'pending')->count(),
                'total' => User::count()
            ];

            // Get staff performance
            $staffPerformance = ApplicationReviewActivity::select('reviewer_id', DB::raw('COUNT(*) as total'))
                ->whereMonth('created_at', now()->month)
                ->groupBy('reviewer_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            $staffPerformanceData = [];
            foreach ($staffPerformance as $staff) {
                $user = User::find($staff->reviewer_id);
                if ($user) {
                    $staffPerformanceData[] = [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'total' => $staff->total
                    ];
                }
            }

            // Generate HTML
            $html = $this->generateExportHTML($stats, $trendData, $recentApplications, $userStats, $staffPerformanceData);

            $filename = 'konstructo_dashboard_' . date('Y-m-d_His') . '.html';

            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

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

    private function generateExportHTML($stats, $trendData, $recentApplications, $userStats, $staffPerformance)
    {
        $statusLabels = [
            'pending' => 'Pending', 
            'under-review' => 'Under Review', 
            'approved' => 'Approved',
            'rejected' => 'Rejected', 
            'for-release' => 'For Release', 
            'verified' => 'Completed'
        ];
        
        $statusColors = [
            'pending' => '#F59E0B', 
            'under-review' => '#3B82F6', 
            'approved' => '#10B981',
            'rejected' => '#EF4444', 
            'for-release' => '#8B5CF6', 
            'verified' => '#22C55E'
        ];
        
        $totalApplications = $stats['total_applications'] ?? 0;
        $pendingApplications = $stats['pending'] ?? $stats['pending_applications'] ?? 0;
        $underReview = $stats['under-review'] ?? $stats['under_review'] ?? 0;
        $approved = $stats['approved'] ?? 0;
        $forRelease = $stats['for-release'] ?? $stats['for_release'] ?? 0;
        $verified = $stats['verified'] ?? 0;
        $rejected = $stats['rejected'] ?? 0;
        $completionRate = $stats['completion_rate'] ?? 0;
        $applicationsTrend = $stats['applications_trend'] ?? '0';
        $pendingAging = $stats['pending_aging'] ?? 0;
        $activeUsers = $stats['active_users'] ?? 0;
        $newUsersWeek = $stats['new_users_week'] ?? 0;
        $avgProcessingTime = $stats['avg_processing_time'] ?? 0;
        $thisMonthApplications = $stats['this_month_applications'] ?? 0;
        
        $maxValue = !empty($trendData) ? max(array_column($trendData, 'count')) : 1;
        $totalTrend = array_sum(array_column($trendData, 'count'));
        $avgTrend = !empty($trendData) ? round($totalTrend / count($trendData)) : 0;
        
        $totalProcessed = array_sum(array_column($staffPerformance, 'total'));
        $avgPerStaff = !empty($staffPerformance) ? round($totalProcessed / count($staffPerformance)) : 0;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Dashboard Export - ' . date('Y-m-d H:i:s') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "Poppins", -apple-system, Arial, sans-serif;
                    background: #f0f2f5;
                    padding: 30px 20px;
                    color: #1a1a2e;
                    font-weight: 400;
                }
                .container {
                    max-width: 1300px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    padding: 30px;
                    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #e9ecef;
                    flex-wrap: wrap;
                    gap: 15px;
                }
                .header h1 {
                    color: #155386;
                    font-size: 28px;
                    font-weight: 600;
                    letter-spacing: -0.5px;
                }
                .header-date {
                    color: #6c757d;
                    font-size: 14px;
                    font-weight: 400;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .stat-card {
                    background: white;
                    border-radius: 16px;
                    padding: 20px;
                    border: 1px solid #e5e7eb;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .stat-label {
                    font-size: 13px;
                    color: #6c757d;
                    margin-bottom: 8px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    font-weight: 500;
                }
                .stat-value {
                    font-size: 32px;
                    font-weight: 700;
                    color: #155386;
                }
                .stat-trend {
                    font-size: 12px;
                    margin-top: 8px;
                    color: #10b981;
                    font-weight: 500;
                }
                .section {
                    margin-bottom: 30px;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    overflow: hidden;
                }
                .section-header {
                    background: linear-gradient(135deg, #155386 0%, #1F363D 100%);
                    padding: 15px 20px;
                    color: white;
                    font-weight: 600;
                    font-size: 16px;
                }
                .section-content {
                    padding: 20px;
                }
                .chart-container {
                    display: flex;
                    align-items: flex-end;
                    justify-content: center;
                    gap: 6px;
                    padding: 20px 0;
                    min-height: 280px;
                    width: 100%;
                }
                .chart-bar-wrapper {
                    flex: 1;
                    text-align: center;
                    min-width: 35px;
                    max-width: 60px;
                }
                .chart-bar {
                    background: linear-gradient(180deg, #155386 0%, #40798C 100%);
                    border-radius: 8px 8px 4px 4px;
                    margin: 0 auto;
                    width: 100%;
                    max-width: 45px;
                }
                .chart-label {
                    font-size: 9px;
                    color: #6c757d;
                    margin-top: 8px;
                    font-weight: 500;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .chart-value {
                    font-size: 10px;
                    font-weight: 600;
                    color: #155386;
                    margin-top: 4px;
                }
                .summary-stats {
                    display: flex;
                    justify-content: space-around;
                    background: #f8f9fa;
                    border-radius: 12px;
                    padding: 15px;
                    margin-top: 20px;
                    text-align: center;
                    flex-wrap: wrap;
                    gap: 15px;
                }
                .summary-stats div {
                    font-size: 13px;
                    font-weight: 500;
                }
                .summary-stats strong {
                    font-weight: 700;
                    color: #155386;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 12px 15px;
                    text-align: left;
                    border-bottom: 1px solid #e9ecef;
                }
                th {
                    background: #f8f9fa;
                    font-weight: 600;
                    font-size: 12px;
                    color: #495057;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                td {
                    font-size: 13px;
                    font-weight: 400;
                }
                tr:hover {
                    background: #f8f9fa;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 11px;
                    font-weight: 500;
                }
                .two-columns {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 25px;
                    margin-bottom: 30px;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    color: #6c757d;
                    font-size: 11px;
                    border-top: 1px solid #e9ecef;
                    margin-top: 20px;
                    font-weight: 400;
                }
                .print-btn {
                    background: #155386;
                    color: white;
                    border: none;
                    padding: 8px 18px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 13px;
                    font-family: "Poppins", sans-serif;
                    font-weight: 500;
                }
                .print-btn:hover {
                    background: #1F363D;
                }
                
                /* Staff Performance Card Styles */
                .staff-card {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    border-radius: 12px;
                    padding: 16px;
                    margin-bottom: 12px;
                    border: 1px solid #e5e7eb;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .staff-card:hover {
                    border-color: #155386;
                    box-shadow: 0 2px 8px rgba(21,83,134,0.1);
                }
                .staff-info {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .staff-avatar {
                    width: 48px;
                    height: 48px;
                    background: linear-gradient(135deg, #155386 0%, #40798C 100%);
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 600;
                    font-size: 18px;
                }
                .staff-details h4 {
                    font-size: 15px;
                    font-weight: 600;
                    color: #1a1a2e;
                    margin-bottom: 4px;
                }
                .staff-details p {
                    font-size: 11px;
                    color: #6c757d;
                }
                .staff-stats {
                    text-align: right;
                }
                .staff-count {
                    font-size: 28px;
                    font-weight: 700;
                    color: #155386;
                }
                .staff-label {
                    font-size: 11px;
                    color: #6c757d;
                    margin-top: 4px;
                }
                .progress-bar-container {
                    width: 100%;
                    background: #e5e7eb;
                    border-radius: 10px;
                    margin-top: 8px;
                    overflow: hidden;
                }
                .progress-bar-fill {
                    background: linear-gradient(90deg, #155386 0%, #40798C 100%);
                    height: 6px;
                    border-radius: 10px;
                    transition: width 0.3s ease;
                }
                .performance-summary {
                    display: flex;
                    justify-content: space-between;
                    gap: 15px;
                    margin-top: 20px;
                    padding-top: 15px;
                    border-top: 1px solid #e5e7eb;
                }
                .performance-summary-item {
                    flex: 1;
                    text-align: center;
                    background: #f8f9fa;
                    border-radius: 12px;
                    padding: 15px;
                }
                .performance-summary-item .label {
                    font-size: 12px;
                    color: #6c757d;
                    margin-bottom: 8px;
                }
                .performance-summary-item .value {
                    font-size: 24px;
                    font-weight: 700;
                    color: #155386;
                }
                
                /* Print Styles - Optimized for A4 */
                @media print {
                    body {
                        background: white;
                        padding: 0;
                        margin: 0;
                    }
                    .container {
                        box-shadow: none;
                        padding: 15px;
                        max-width: 100%;
                        border-radius: 0;
                    }
                    .print-btn {
                        display: none;
                    }
                    .section-header {
                        background: #155386 !important;
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                    .chart-bar {
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                    .stats-grid {
                        gap: 12px;
                    }
                    .stat-card {
                        padding: 12px;
                    }
                    .stat-value {
                        font-size: 24px;
                    }
                    .chart-container {
                        min-height: 200px;
                        gap: 4px;
                    }
                    .chart-bar-wrapper {
                        min-width: 25px;
                    }
                    .chart-label {
                        font-size: 7px;
                        white-space: normal;
                        word-break: break-word;
                        line-height: 1.2;
                    }
                    .chart-value {
                        font-size: 8px;
                    }
                    th, td {
                        padding: 8px 10px;
                    }
                    .two-columns {
                        gap: 15px;
                    }
                    .section-content {
                        padding: 12px;
                    }
                    .summary-stats {
                        padding: 10px;
                        gap: 10px;
                    }
                    .summary-stats div {
                        font-size: 11px;
                    }
                    .staff-card {
                        padding: 10px;
                        margin-bottom: 8px;
                    }
                    .staff-avatar {
                        width: 36px;
                        height: 36px;
                        font-size: 14px;
                    }
                    .staff-count {
                        font-size: 20px;
                    }
                    .performance-summary-item .value {
                        font-size: 18px;
                    }
                    /* Prevent page breaks inside sections */
                    .section {
                        page-break-inside: avoid;
                        break-inside: avoid;
                    }
                    .stats-grid {
                        page-break-inside: avoid;
                        break-inside: avoid;
                    }
                    .staff-card {
                        page-break-inside: avoid;
                        break-inside: avoid;
                    }
                }
                
                /* Responsive for smaller screens */
                @media (max-width: 768px) {
                    .stats-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 12px;
                    }
                    .two-columns {
                        grid-template-columns: 1fr;
                        gap: 20px;
                    }
                    .chart-container {
                        overflow-x: auto;
                        justify-content: flex-start;
                    }
                    .chart-bar-wrapper {
                        min-width: 45px;
                    }
                    .performance-summary {
                        flex-direction: column;
                        gap: 10px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div>
                        <h1>Konstructo Dashboard Export</h1>
                        <div class="header-date">Generated: ' . date('F d, Y g:i:s A') . '</div>
                    </div>
                    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
                </div>
                
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Applications</div>
                        <div class="stat-value">' . number_format($totalApplications) . '</div>
                        <div class="stat-trend">↑ ' . $applicationsTrend . '% from last month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Pending Review</div>
                        <div class="stat-value">' . number_format($pendingApplications) . '</div>
                        <div class="stat-trend">⏰ ' . $pendingAging . ' pending over 7 days</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value">' . number_format($activeUsers) . '</div>
                        <div class="stat-trend">+ ' . $newUsersWeek . ' this week</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Completion Rate</div>
                        <div class="stat-value">' . $completionRate . '%</div>
                        <div class="stat-trend">' . $verified . ' completed</div>
                    </div>
                </div>
                
                <!-- Trend Chart -->
                <div class="section">
                    <div class="section-header">Applications Trend (Last 30 Days)</div>
                    <div class="section-content">
                        <div class="chart-container">';
        
        foreach ($trendData as $item) {
            $height = $maxValue > 0 ? ($item['count'] / $maxValue) * 180 : 20;
            $height = max($height, 25);
            $html .= '
                            <div class="chart-bar-wrapper">
                                <div class="chart-bar" style="height: ' . $height . 'px;"></div>
                                <div class="chart-label">' . $item['label'] . '</div>
                                <div class="chart-value">' . $item['count'] . '</div>
                            </div>';
        }
        
        $html .= '
                        </div>
                        <div class="summary-stats">
                            <div><strong>Total:</strong> ' . number_format($totalTrend) . '</div>
                            <div><strong>Average:</strong> ' . number_format($avgTrend) . '</div>
                            <div><strong>Peak:</strong> ' . number_format($maxValue) . '</div>
                            <div><strong>Period:</strong> 30 days</div>
                        </div>
                    </div>
                </div>
                
                <div class="two-columns">
                    <!-- Status Distribution -->
                    <div class="section">
                        <div class="section-header">Application Status</div>
                        <div class="section-content">
                            <table>
                                <thead>
                                    <tr><th>Status</th><th>Count</th><th>Percentage</th></tr>
                                </thead>
                                <tbody>';
        
        $total = $totalApplications;
        $statusList = [
            ['key' => 'pending', 'label' => 'Pending', 'count' => $pendingApplications],
            ['key' => 'under-review', 'label' => 'Under Review', 'count' => $underReview],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $approved],
            ['key' => 'for-release', 'label' => 'For Release', 'count' => $forRelease],
            ['key' => 'verified', 'label' => 'Completed', 'count' => $verified],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $rejected],
        ];
        
        foreach ($statusList as $status) {
            $count = $status['count'];
            $percent = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $color = $statusColors[$status['key']] ?? '#6c757d';
            $html .= '<tr>
                            <td><span class="status-badge" style="background:' . $color . '20; color:' . $color . ';">' . $status['label'] . '</span></td>
                            <td>' . number_format($count) . '</td>
                            <td>' . $percent . '%</span></td>
                         </tr>';
        }
        
        $html .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- User Roles -->
                    <div class="section">
                        <div class="section-header">User Roles</div>
                        <div class="section-content">
                            <table>
                                <thead>
                                    <tr><th>Role</th><th>Count</th><th>Percentage</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Admins</span></td>
                                        <td>' . number_format($userStats['admins']) . '</span></td>
                                        <td>' . round(($userStats['admins'] / max($userStats['total'],1)) * 100, 1) . '%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Staff</span></td>
                                        <td>' . number_format($userStats['staff']) . '</span></td>
                                        <td>' . round(($userStats['staff'] / max($userStats['total'],1)) * 100, 1) . '%</span></td>
                                    </tr>
                                    <tr>
                                        <td>Applicants</span></td>
                                        <td>' . number_format($userStats['applicants']) . '</span></td>
                                        <td>' . round(($userStats['applicants'] / max($userStats['total'],1)) * 100, 1) . '%</span></td>
                                    </tr>
                                    <tr style="border-top: 2px solid #e9ecef;">
                                        <td><strong>Total Users</strong></span></td>
                                        <td><strong>' . number_format($userStats['total']) . '</strong></span></td>
                                        <td>100%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="summary-stats" style="margin-top: 15px;">
                                <div><strong>Pending Approval:</strong> ' . number_format($userStats['pending_applicants']) . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Applications -->
                <div class="section">
                    <div class="section-header">Recent Applications</div>
                    <div class="section-content">
                        <table>
                            <thead>
                                <tr><th>Application Number</th><th>Applicant</th><th>Submitted</th><th>Status</th><th>Last Updated</th></tr>
                            </thead>
                            <tbody>';
        
        foreach ($recentApplications as $app) {
            $statusKey = $app->status ?? 'pending';
            $statusClass = $statusColors[$statusKey] ?? '#6c757d';
            $statusDisplay = $statusLabels[$statusKey] ?? ucfirst($app->status ?? 'Unknown');
            $html .= '<tr>
                            <td><strong>' . e($app->application_number) . '</strong></span></td>
                            <td>' . e($app->user?->first_name ?? '') . ' ' . e($app->user?->last_name ?? '') . '</span></td>
                            <td>' . ($app->created_at ? $app->created_at->format('M d, Y') : 'N/A') . '</span></td>
                            <td><span class="status-badge" style="background:' . $statusClass . '20; color:' . $statusClass . ';">' . $statusDisplay . '</span></span></td>
                            <td>' . ($app->updated_at ? $app->updated_at->format('M d, Y') : 'N/A') . '</span></td>
                         </tr>';
        }
        
        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Staff Performance - With Full Design -->
                <div class="section">
                    <div class="section-header">Staff Performance (This Month)</div>
                    <div class="section-content">';
        
        if (!empty($staffPerformance)) {
            // Find max count for progress bar calculation
            $maxCount = !empty($staffPerformance) ? max(array_column($staffPerformance, 'total')) : 1;
            $topPerformer = !empty($staffPerformance) ? $staffPerformance[0]['name'] : 'N/A';
            
            $html .= '<div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="background: #10b98120; color: #10b981; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Top Performer: ' . e($topPerformer) . '</span>
                                </div>
                                <div style="font-size: 12px; color: #6c757d;">
                                    Showing top ' . count($staffPerformance) . ' performers
                                </div>
                            </div>';
            
            foreach ($staffPerformance as $index => $staff) {
                $percentage = $maxCount > 0 ? ($staff['total'] / $maxCount) * 100 : 0;
                $rank = $index + 1;
                $medalIcon = '';
                if ($rank === 1) {
                    $medalIcon = '<span style="background: #FFD700; color: #B8860B; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">1</span>';
                } elseif ($rank === 2) {
                    $medalIcon = '<span style="background: #C0C0C0; color: #808080; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">2</span>';
                } elseif ($rank === 3) {
                    $medalIcon = '<span style="background: #CD7F32; color: #8B4513; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">3</span>';
                } else {
                    $medalIcon = '<span style="background: #e5e7eb; color: #6c757d; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500;">' . $rank . '</span>';
                }
                
                // Get initials for avatar
                $nameParts = explode(' ', $staff['name']);
                $initials = '';
                foreach ($nameParts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
                $initials = substr($initials, 0, 2);
                
                $html .= '<div class="staff-card">
                                <div class="staff-info">
                                    <div class="staff-avatar">' . e($initials) . '</div>
                                    <div class="staff-details">
                                        <h4>' . e($staff['name']) . '</h4>
                                        <p>' . e($staff['email']) . '</p>
                                    </div>
                                </div>
                                <div class="staff-stats">
                                    <div class="staff-count">' . number_format($staff['total']) . '</div>
                                    <div class="staff-label">applications processed</div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width: ' . $percentage . '%;"></div>
                                    </div>
                                </div>
                            </div>';
            }
            
            $html .= '</div>';
        } else {
            $html .= '<div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 12px;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p style="margin-top: 15px; color: #6c757d;">No activity recorded this month</p>
                        </div>';
        }
        
        $html .= '<div class="performance-summary">
                        <div class="performance-summary-item">
                            <div class="label">Total Processed</div>
                            <div class="value">' . number_format($totalProcessed) . '</div>
                        </div>
                        <div class="performance-summary-item">
                            <div class="label">Average per Staff</div>
                            <div class="value">' . number_format($avgPerStaff) . '</div>
                        </div>
                        <div class="performance-summary-item">
                            <div class="label">Active Staff</div>
                            <div class="value">' . number_format(count($staffPerformance)) . '</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Section -->
            <div class="section">
                <div class="section-header">Executive Summary</div>
                <div class="section-content">
                    <div class="summary-stats" style="flex-wrap: wrap; gap: 20px;">
                        <div><strong>Completed:</strong> ' . number_format($verified) . ' applications</div>
                        <div><strong>In Progress:</strong> ' . number_format($underReview + $pendingApplications) . ' applications</div>
                        <div><strong>For Release:</strong> ' . number_format($forRelease) . ' applications</div>
                        <div><strong>Rejected:</strong> ' . number_format($rejected) . ' applications</div>
                        <div><strong>Average Processing:</strong> ' . $avgProcessingTime . ' days</div>
                        <div><strong>This Month:</strong> ' . number_format($thisMonthApplications) . ' new applications</div>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>This report was generated automatically. For questions, contact your system administrator.</p>
                <p>Report ID: KDS-' . date('Ymd') . '-' . rand(1000, 9999) . ' | Page generated: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';
        
        return $html;
    }
}