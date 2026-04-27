<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
            
            $labels = [];
            $values = [];
            
            if ($period === 'this_month') {
                // Get daily data for the current month
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                
                // Get all applications for this month
                $applications = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
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
                
                $applications = ApplicationDocument::whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
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
                $monthlyData = ApplicationDocument::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
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
                $applications = ApplicationDocument::with('user')
                    ->whereIn('status', ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'])
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
    $statuses = ['pending', 'under-review', 'approved', 'rejected', 'for-release', 'verified'];
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
 * Export dashboard as PDF
 */
private function exportDashboardAsPDF($stats, $trendData, $recentActivities, $deadlines)
{
    $html = $this->generateDashboardPDFHTML($stats, $trendData, $recentActivities, $deadlines);
    
    // Use DomPDF if installed, otherwise fallback to HTML
    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('dashboard_export_' . date('Y-m-d_His') . '.pdf');
    }
    
    // Fallback to HTML download
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
    $trendChange = $stats['trend_change'] ?? 0;
    $avgProcessingTime = $stats['avg_processing_time'] ?? 0;
    $pendingAging = $stats['pending_aging'] ?? 0;
    
    $statusColors = [
        'pending' => '#F59E0B',
        'under-review' => '#8B5CF6',
        'approved' => '#10B981',
        'rejected' => '#EF4444',
        'for-release' => '#3B82F6',
        'verified' => '#22C55E'
    ];
    
    $statusLabels = [
        'pending' => 'Pending',
        'under-review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'for-release' => 'For Release',
        'verified' => 'Completed'
    ];
    
    $maxTrendValue = !empty($trendData) ? max(array_column($trendData, 'count')) : 1;
    $totalTrend = array_sum(array_column($trendData, 'count'));
    $avgTrend = !empty($trendData) ? round($totalTrend / count($trendData)) : 0;
    
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
            .stat-trend.negative {
                color: #ef4444;
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
            .two-columns {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
                margin-bottom: 30px;
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
                transition: height 0.3s ease;
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
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 500;
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
            .footer {
                text-align: center;
                padding: 20px;
                color: #6c757d;
                font-size: 11px;
                border-top: 1px solid #e9ecef;
                margin-top: 20px;
                font-weight: 400;
            }
            .progress-bar-container {
                width: 100%;
                background: #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
                margin-top: 8px;
            }
            .progress-bar-fill {
                background: linear-gradient(90deg, #155386 0%, #40798C 100%);
                height: 6px;
                border-radius: 10px;
                transition: width 0.3s ease;
            }
            .activity-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            
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
                .section {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
                .stats-grid {
                    page-break-inside: avoid;
                    break-inside: avoid;
                }
            }
            
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
                    <div class="stat-value">' . number_format($total) . '</div>
                    <div class="stat-trend ' . ($trendChange >= 0 ? '' : 'negative') . '">' . ($trendChange >= 0 ? '↑' : '↓') . ' ' . abs($trendChange) . '% from last month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value">' . number_format($pending) . '</div>
                    <div class="stat-trend">⏰ ' . $pendingAging . ' pending over 7 days</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">' . number_format($verified) . '</div>
                    <div class="stat-trend">✓ ' . $completionRate . '% completion rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avg Processing Time</div>
                    <div class="stat-value">' . $avgProcessingTime . ' days</div>
                    <div class="stat-trend">Average time to complete</div>
                </div>
            </div>
            
            <div class="two-columns">
                <!-- Application Trend Chart -->
                <div class="section">
                    <div class="section-header">Application Trend (Last 30 Days)</div>
                    <div class="section-content">
                        <div class="chart-container">';
    
    foreach ($trendData as $item) {
        $height = $maxTrendValue > 0 ? ($item['count'] / $maxTrendValue) * 180 : 20;
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
                            <div><strong>Peak:</strong> ' . number_format($maxTrendValue) . '</div>
                            <div><strong>Period:</strong> 30 days</div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Distribution -->
                <div class="section">
                    <div class="section-header">Application Status</div>
                    <div class="section-content">
                        <table>
                            <thead>
                                <tr><th>Status</th><th>Count</th><th>Percentage</th></tr>
                            </thead>
                            <tbody>';
    
    $statusList = [
        ['key' => 'pending', 'label' => 'Pending', 'count' => $pending],
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
                            <td>' . number_format($count) . '</span></td>
                            <td>' . $percent . '%</span></span></td>
                          </tr>';
    }
    
    $html .= '
                            </tbody>
                        </table>
                        <div class="progress-bar-container" style="margin-top: 15px;">
                            <div class="progress-bar-fill" style="width: ' . $completionRate . '%;"></div>
                        </div>
                        <div class="summary-stats" style="margin-top: 10px;">
                            <div><strong>Completion Rate:</strong> ' . $completionRate . '%</div>
                            <div><strong>In Progress:</strong> ' . number_format($pending + $underReview) . '</div>
                            <div><strong>Completed:</strong> ' . number_format($verified + $approved + $forRelease) . '</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="section">
                <div class="section-header">Recent Activities</div>
                <div class="section-content">
                    <table>
                        <thead>
                            <tr><th>Date & Time</th><th>Action</th><th>Reviewer</th><th>Remarks</th></tr>
                        </thead>
                        <tbody>';
    
    foreach ($recentActivities as $activity) {
        $actionDisplay = $activity->action_display ?? $activity->action ?? 'Activity';
        $actionLower = strtolower($actionDisplay);
        
        $badgeColor = '#6c757d';
        if (strpos($actionLower, 'approve') !== false) $badgeColor = '#10b981';
        elseif (strpos($actionLower, 'reject') !== false) $badgeColor = '#ef4444';
        elseif (strpos($actionLower, 'pending') !== false) $badgeColor = '#f59e0b';
        elseif (strpos($actionLower, 'review') !== false) $badgeColor = '#8b5cf6';
        
        $html .= '<tr>
                            <td>' . ($activity->created_at ? $activity->created_at->format('Y-m-d H:i') : '') . '</span></td>
                            <td><span class="status-badge" style="background:' . $badgeColor . '20; color:' . $badgeColor . ';">' . htmlspecialchars($actionDisplay) . '</span></span></td>
                            <td>' . htmlspecialchars($activity->reviewer_name ?? 'System') . '</span></td>
                            <td>' . htmlspecialchars(substr($activity->remarks ?? '', 0, 60)) . (strlen($activity->remarks ?? '') > 60 ? '...' : '') . '</span></td>
                          </tr>';
    }
    
    $html .= '
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Upcoming Deadlines -->
<div class="section">
    <div class="section-header">Upcoming Deadlines</div>
    <div class="section-content">
        <table>
            <thead>
                <tr><th>Application #</th><th>Applicant</th><th>Days Left</th><th>Due Date</th><th>Status</th></tr>
            </thead>
            <tbody>';
    
    // Convert to array if it's a collection
    $deadlinesArray = $deadlines instanceof \Illuminate\Support\Collection ? $deadlines->toArray() : $deadlines;
    
    foreach ($deadlinesArray as $deadline) {
        $daysLeft = $deadline['days_left'];
        $dueClass = '';
        $dueText = '';
        
        if ($daysLeft <= 1) {
            $dueClass = 'style="background:#ef444420; color:#dc2626;"';
            $dueText = 'Urgent';
        } elseif ($daysLeft <= 3) {
            $dueClass = 'style="background:#f59e0b20; color:#d97706;"';
            $dueText = 'Warning';
        } else {
            $dueClass = 'style="background:#10b98120; color:#16a34a;"';
            $dueText = 'On Track';
        }
        
        $html .= '<tr>
                        <td><strong>' . htmlspecialchars($deadline['application_name']) . '</strong></span></td>
                        <td>' . htmlspecialchars($deadline['applicant_name']) . '</span></td>
                        <td><span ' . $dueClass . ' class="status-badge">' . $deadline['days_left'] . ' days</span></span></td>
                        <td>' . $deadline['due_date'] . '</span></td>
                        <td><span ' . $dueClass . ' class="status-badge">' . $dueText . '</span></span></td>
                      </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        <div class="summary-stats" style="margin-top: 15px;">
            <div><strong>⚠️ Urgent (0-1 days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] <= 1)) . '</div>
            <div><strong>⚠️ Warning (2-3 days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] >= 2 && $d['days_left'] <= 3)) . '</div>
            <div><strong>✅ On Track (4+ days):</strong> ' . count(array_filter($deadlinesArray, fn($d) => $d['days_left'] >= 4)) . '</div>
        </div>
    </div>
</div>
            
            <!-- Executive Summary -->
            <div class="section">
                <div class="section-header">Executive Summary</div>
                <div class="section-content">
                    <div class="summary-stats" style="flex-wrap: wrap; gap: 20px;">
                        <div><strong>Total Applications:</strong> ' . number_format($total) . '</div>
                        <div><strong>Pending Review:</strong> ' . number_format($pending) . ' (' . ($total > 0 ? round(($pending / $total) * 100) : 0) . '%)</div>
                        <div><strong>Under Review:</strong> ' . number_format($underReview) . ' (' . ($total > 0 ? round(($underReview / $total) * 100) : 0) . '%)</div>
                        <div><strong>Completed:</strong> ' . number_format($verified + $approved + $forRelease) . ' (' . ($total > 0 ? round((($verified + $approved + $forRelease) / $total) * 100) : 0) . '%)</div>
                        <div><strong>Rejected:</strong> ' . number_format($rejected) . ' (' . ($total > 0 ? round(($rejected / $total) * 100) : 0) . '%)</div>
                        <div><strong>Average Processing Time:</strong> ' . $avgProcessingTime . ' days</div>
                        <div><strong>Aging Applications (&gt;7 days):</strong> ' . $pendingAging . '</div>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>This report was generated automatically. For questions, contact your system administrator.</p>
                <p>Report ID: DASH-' . date('Ymd') . '-' . rand(1000, 9999) . ' | Generated: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
        
        <script>
            // Auto-trigger print dialog when page loads (optional)
            // window.onload = function() { setTimeout(function() { window.print(); }, 500); };
        </script>
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

}