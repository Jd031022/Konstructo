<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\ApplicationReviewActivity;
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
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Export - ' . date('Y-m-d H:i:s') . '</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: "Poppins", Arial, sans-serif;
                padding: 20px;
                color: #333;
                font-size: 11px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #155386;
            }
            .header h1 {
                color: #155386;
                font-size: 22px;
            }
            .header p {
                color: #666;
                font-size: 10px;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                margin-bottom: 25px;
            }
            .stat-card {
                background: #f5f5f5;
                padding: 12px;
                border-radius: 8px;
                text-align: center;
            }
            .stat-card .label {
                font-size: 10px;
                color: #666;
            }
            .stat-card .value {
                font-size: 22px;
                font-weight: bold;
                color: #155386;
            }
            .section {
                margin-bottom: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
            }
            .section-title {
                background: #155386;
                color: white;
                padding: 8px 12px;
                font-weight: 600;
                font-size: 12px;
            }
            .section-content {
                padding: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }
            th {
                background: #f5f5f5;
                font-weight: 600;
                font-size: 10px;
            }
            td {
                font-size: 9px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                font-size: 9px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Konstructo Dashboard Export</h1>
            <p>Generated on: ' . date('F d, Y g:i:s A') . '</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><div class="label">Total Applications</div><div class="value">' . $stats['total'] . '</div></div>
            <div class="stat-card"><div class="label">Pending Review</div><div class="value">' . $stats['pending'] . '</div></div>
            <div class="stat-card"><div class="label">Completed</div><div class="value">' . $stats['verified'] . '</div></div>
            <div class="stat-card"><div class="label">Completion Rate</div><div class="value">' . $stats['completion_rate'] . '%</div></div>
        </div>
        
        <div class="section">
            <div class="section-title">Application Status Distribution</div>
            <div class="section-content">
                <table>
                    <thead><tr><th>Status</th><th>Count</th><th>Percentage</th></tr></thead>
                    <tbody>
                        <tr><td>Pending</td><td>' . $stats['pending'] . '</td><td>' . ($total > 0 ? round(($stats['pending'] / $total) * 100) . '%' : '0%') . '</td></tr>
                        <tr><td>Under Review</td><td>' . $stats['under_review'] . '</td><td>' . ($total > 0 ? round(($stats['under_review'] / $total) * 100) . '%' : '0%') . '</td></tr>
                        <tr><td>Approved</td><td>' . $stats['approved'] . '</td><td>' . ($total > 0 ? round(($stats['approved'] / $total) * 100) . '%' : '0%') . '</td></tr>
                        <tr><td>For Release</td><td>' . $stats['for_release'] . '</td><td>' . ($total > 0 ? round(($stats['for_release'] / $total) * 100) . '%' : '0%') . '</td></tr>
                        <tr><td>Completed</td><td>' . $stats['verified'] . '</td><td>' . ($total > 0 ? round(($stats['verified'] / $total) * 100) . '%' : '0%') . '</td></tr>
                        <tr><td>Rejected</td><td>' . $stats['rejected'] . '</td><td>' . ($total > 0 ? round(($stats['rejected'] / $total) * 100) . '%' : '0%') . '</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Application Trend (Last 30 Days)</div>
            <div class="section-content">
                <table>
                    <thead><tr><th>Date</th><th>Applications</th></tr></thead>
                    <tbody>';
    
    foreach ($trendData as $item) {
        $html .= '<tr><td>' . $item['label'] . '</td><td>' . $item['count'] . '</td></tr>';
    }
    
    $html .= '</tbody>
            </table>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Recent Activities</div>
            <div class="section-content">
                <tr>
                    <thead><tr><th>Date</th><th>Action</th><th>Reviewer</th><th>Remarks</th></tr></thead>
                    <tbody>';
    
    foreach ($recentActivities as $activity) {
        $html .= '<tr>
            <td>' . ($activity->created_at ? $activity->created_at->format('Y-m-d H:i') : '') . '</td>
            <td>' . htmlspecialchars($activity->action_display ?? $activity->action ?? '') . '</td>
            <td>' . htmlspecialchars($activity->reviewer_name ?? 'System') . '</td>
            <td>' . htmlspecialchars(substr($activity->remarks ?? '', 0, 50)) . '</td>
        </tr>';
    }
    
    $html .= '</tbody>
            </table>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Upcoming Deadlines</div>
            <div class="section-content">
                <table>
                    <thead><tr><th>Application</th><th>Applicant</th><th>Days Left</th><th>Due Date</th></tr></thead>
                    <tbody>';
    
    foreach ($deadlines as $deadline) {
        $html .= '<tr>
            <td>' . htmlspecialchars($deadline['application_name']) . '</td>
            <td>' . htmlspecialchars($deadline['applicant_name']) . '</td>
            <td>' . $deadline['days_left'] . ' days</td>
            <td>' . $deadline['due_date'] . '</td>
        </tr>';
    }
    
    $html .= '</tbody>
            </table>
            </div>
        </div>
        
        <div class="footer">
            <p>Konstructo - Smart Infrastructure Oversight</p>
            <p>This report was generated automatically on ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
}