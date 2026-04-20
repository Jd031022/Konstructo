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

        public function exportDashboard()
    {
        try {
            // Set timeout to prevent execution time errors
            set_time_limit(120);
            
            // Fetch all dashboard data
            $stats = $this->getExportStats();
            $trendData = $this->getExportTrendData(30);
            
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

    private function getExportStats()
    {
        $totalApplications = ApplicationDocument::count();
        
        $pendingApplications = ApplicationDocument::where('status', 'pending')->count();
        $underReview = ApplicationDocument::where('status', 'under-review')->count();
        $approved = ApplicationDocument::where('status', 'approved')->count();
        $forRelease = ApplicationDocument::where('status', 'for-release')->count();
        $verified = ApplicationDocument::where('status', 'verified')->count();
        $rejected = ApplicationDocument::where('status', 'rejected')->count();
        
        $completionRate = $totalApplications > 0 ? round(($verified / $totalApplications) * 100) : 0;
        $activeUsers = User::count();
        $newUsersWeek = User::where('created_at', '>=', now()->subDays(7))->count();
        
        $lastMonthTotal = ApplicationDocument::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $thisMonthTotal = ApplicationDocument::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $applicationsTrend = $lastMonthTotal > 0 ? round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100) : 0;
        
        $avgProcessingTime = ApplicationDocument::whereNotNull('verified_at')
            ->select(DB::raw('COALESCE(AVG(EXTRACT(DAY FROM (verified_at - created_at))), 0) as avg_days'))
            ->value('avg_days');
        
        $pendingAging = ApplicationDocument::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(7))
            ->count();
        
        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'pending' => $pendingApplications,
            'under_review' => $underReview,
            'under-review' => $underReview,
            'approved' => $approved,
            'for_release' => $forRelease,
            'for-release' => $forRelease,
            'verified' => $verified,
            'rejected' => $rejected,
            'active_users' => $activeUsers,
            'completion_rate' => $completionRate,
            'applications_trend' => ($applicationsTrend > 0 ? '+' : '') . $applicationsTrend,
            'new_users_week' => $newUsersWeek,
            'this_month_applications' => $thisMonthTotal,
            'avg_processing_time' => round($avgProcessingTime ?? 0),
            'pending_aging' => $pendingAging,
        ];
    }

    private function getExportTrendData($days = 30)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = ApplicationDocument::whereDate('created_at', $date->toDateString())->count();
            $data[] = [
                'label' => $date->format('M d'),
                'count' => $count,
            ];
        }
        return $data;
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
