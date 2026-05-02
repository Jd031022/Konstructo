<?php
// app/Http/Controllers/Admin/SettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $currentTab = $request->get('tab', 'system-logs');
        
        $systemLogs = null;
        $applicationLogs = null;
        
        // Fetch system logs (activity_logs table)
        if ($currentTab == 'system-logs') {
            $query = ActivityLog::with('user')
                ->orderBy('created_at', 'desc');
            
            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ip_address', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('username', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            // Action filter
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            // Date range filter
            if ($request->filled('date_range')) {
                switch($request->date_range) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'yesterday':
                        $query->whereDate('created_at', today()->subDay());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', now()->subDays(7));
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                }
            }
            
            // Sorting
            if ($request->filled('sort')) {
                $direction = $request->get('direction', 'asc');
                
                switch($request->sort) {
                    case 'name':
                        $query->join('users', 'activity_logs.user_id', '=', 'users.id')
                              ->orderBy('users.first_name', $direction)
                              ->orderBy('users.last_name', $direction)
                              ->select('activity_logs.*');
                        break;
                    case 'username':
                        $query->join('users', 'activity_logs.user_id', '=', 'users.id')
                              ->orderBy('users.username', $direction)
                              ->select('activity_logs.*');
                        break;
                    default:
                        $query->orderBy($request->sort, $direction);
                }
            }
            
            $systemLogs = $query->paginate(15)->withQueryString();
        }
        
        // Fetch application review logs (application_review_activities table)
        if ($currentTab == 'application-logs') {
            $query = ApplicationReviewActivity::with(['reviewer', 'application'])
                ->orderBy('created_at', 'desc');
            
            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('remarks', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhereHas('reviewer', function($reviewerQuery) use ($search) {
                          $reviewerQuery->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->orWhere('username', 'like', "%{$search}%");
                      })
                      ->orWhereHas('application', function($appQuery) use ($search) {
                          $appQuery->where('application_number', 'like', "%{$search}%");
                      });
                });
            }
            
            // Action filter
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            // Date range filter
            if ($request->filled('date_range')) {
                switch($request->date_range) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'yesterday':
                        $query->whereDate('created_at', today()->subDay());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', now()->subDays(7));
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                }
            }
            
            // Sorting
            if ($request->filled('sort')) {
                $direction = $request->get('direction', 'asc');
                
                switch($request->sort) {
                    case 'reviewer':
                        $query->join('users', 'application_review_activities.reviewer_id', '=', 'users.id')
                              ->orderBy('users.first_name', $direction)
                              ->orderBy('users.last_name', $direction)
                              ->select('application_review_activities.*');
                        break;
                    case 'application':
                        $query->join('application_documents', 'application_review_activities.application_id', '=', 'application_documents.id')
                              ->orderBy('application_documents.application_number', $direction)
                              ->select('application_review_activities.*');
                        break;
                    default:
                        $query->orderBy($request->sort, $direction);
                }
            }
            
            $applicationLogs = $query->paginate(15)->withQueryString();
        }
        
        return view('admin.settings', compact('currentTab', 'systemLogs', 'applicationLogs'));
    }

    public function exportLogs(Request $request)
{
    try {
        $type = $request->get('type', 'system'); // system or application
        $format = $request->get('format', 'csv'); // csv or html
        
        if ($type === 'system') {
            $logs = $this->getSystemLogsForExport($request);
            $filename = 'system_logs_' . date('Y-m-d_His');
        } else {
            $logs = $this->getApplicationLogsForExport($request);
            $filename = 'application_logs_' . date('Y-m-d_His');
        }
        
        if ($format === 'csv') {
            return $this->exportAsCSV($logs, $type, $filename);
        } else {
            return $this->exportAsHTML($logs, $type, $filename);
        }
        
    } catch (\Exception $e) {
        Log::error('Logs export failed: ' . $e->getMessage());
        return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
    }
}

private function exportAsCSV($logs, $type, $filename)
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];
    
    $callback = function() use ($logs, $type) {
        $handle = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers based on log type
        if ($type === 'system') {
            fputcsv($handle, [
                'Name',
                'Username',
                'Action',
                'IP Address',
                'Date',
                'Time',
                'Status'
            ]);
            
            foreach ($logs as $log) {
                $userName = $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User';
                $username = $log->user ? $log->user->username : 'N/A';
                
                // Format date for Excel recognition
                $dateTime = $log->created_at;
                $date = $dateTime->format('Y-m-d'); // Excel recognizes YYYY-MM-DD
                $time = $dateTime->format('H:i:s');
                
                fputcsv($handle, [
                    $userName,
                    $username,
                    $log->action,
                    $log->ip_address ?? 'N/A',
                    $date,
                    $time,
                    $log->status ?? 'success'
                ]);
            }
        } else {
            fputcsv($handle, [
                'Reviewer',
                'Application Number',
                'Action',
                'Old Status',
                'New Status',
                'Remarks',
                'Date',
                'Time',
                'IP Address'
            ]);
            
            foreach ($logs as $log) {
                $reviewerName = $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer';
                $appNumber = $log->application ? $log->application->application_number : 'N/A';
                
                // Format date for Excel recognition
                $dateTime = $log->created_at;
                $date = $dateTime->format('Y-m-d');
                $time = $dateTime->format('H:i:s');
                
                $actionLabels = [
                    'document_verified' => 'Document Verified',
                    'document_rejected' => 'Document Rejected',
                    'status_updated' => 'Status Updated',
                ];
                $action = $actionLabels[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
                
                fputcsv($handle, [
                    $reviewerName,
                    $appNumber,
                    $action,
                    $log->old_status ? str_replace('_', ' ', ucfirst($log->old_status)) : '',
                    $log->new_status ? str_replace('_', ' ', ucfirst($log->new_status)) : '',
                    $log->remarks ?? '',
                    $date,
                    $time,
                    $log->ip_address ?? 'N/A'
                ]);
            }
        }
        
        fclose($handle);
    };
    
    return response()->streamDownload($callback, $filename . '.csv', $headers);
}

private function exportAsHTML($logs, $type, $filename)
{
    $html = $this->generateLogsHTML($logs, $type);
    
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
}

private function generateLogsHTML($logs, $type)
{
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Konstructo ' . ucfirst($type) . ' Logs Export - ' . date('Y-m-d H:i:s') . '</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: "Poppins", -apple-system, Arial, sans-serif;
                background: #f0f2f5;
                padding: 30px 20px;
                color: #1a1a2e;
            }
            .container {
                max-width: 1400px;
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
            }
            .header-date {
                color: #6c757d;
                font-size: 14px;
            }
            .stats {
                display: flex;
                gap: 20px;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }
            .stat-card {
                background: #f8f9fa;
                border-radius: 12px;
                padding: 15px 25px;
                border: 1px solid #e5e7eb;
            }
            .stat-label {
                font-size: 12px;
                color: #6c757d;
                margin-bottom: 5px;
            }
            .stat-value {
                font-size: 28px;
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
            .status-success { background: #d1fae5; color: #065f46; }
            .status-failed { background: #fee2e2; color: #991b1b; }
            .footer {
                text-align: center;
                padding: 20px;
                color: #6c757d;
                font-size: 11px;
                border-top: 1px solid #e9ecef;
                margin-top: 20px;
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
            @media print {
                body { background: white; padding: 0; }
                .container { box-shadow: none; padding: 15px; }
                .print-btn { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div>
                    <h1>Konstructo ' . ucfirst($type) . ' Logs Export</h1>
                    <div class="header-date">Generated: ' . date('F d, Y g:i:s A') . '</div>
                </div>
                <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
            </div>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Total Entries</div>
                    <div class="stat-value">' . number_format($logs->count()) . '</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Date Range</div>
                    <div class="stat-value" style="font-size: 16px;">' . ($logs->isNotEmpty() ? $logs->first()->created_at->format('M d, Y') . ' - ' . $logs->last()->created_at->format('M d, Y') : 'N/A') . '</div>
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table>
                    <thead>';
    
    if ($type === 'system') {
        $html .= '<tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($logs as $log) {
            $userName = $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User';
            $username = $log->user ? $log->user->username : 'N/A';
            $date = $log->created_at->format('Y-m-d');
            $time = $log->created_at->format('H:i:s');
            $statusClass = ($log->status ?? 'success') === 'success' ? 'status-success' : 'status-failed';
            
            $html .= '<tr>
                        <td>' . htmlspecialchars($userName) . '</td>
                        <td>' . htmlspecialchars($username) . '</td>
                        <td>' . htmlspecialchars($log->action) . '</td>
                        <td>' . htmlspecialchars($log->ip_address ?? 'N/A') . '</td>
                        <td>' . $date . '</td>
                        <td>' . $time . '</td>
                        <td><span class="status-badge ' . $statusClass . '">' . ($log->status ?? 'success') . '</span></td>
                    </tr>';
        }
    } else {
        $html .= '<tr>
                        <th>Reviewer</th>
                        <th>Application Number</th>
                        <th>Action</th>
                        <th>Old Status</th>
                        <th>New Status</th>
                        <th>Remarks</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>';
        
        $actionLabels = [
            'document_verified' => 'Document Verified',
            'document_rejected' => 'Document Rejected',
            'status_updated' => 'Status Updated',
        ];
        
        foreach ($logs as $log) {
            $reviewerName = $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer';
            $appNumber = $log->application ? $log->application->application_number : 'N/A';
            $action = $actionLabels[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
            $date = $log->created_at->format('Y-m-d');
            $time = $log->created_at->format('H:i:s');
            $oldStatus = $log->old_status ? str_replace('_', ' ', ucfirst($log->old_status)) : '';
            $newStatus = $log->new_status ? str_replace('_', ' ', ucfirst($log->new_status)) : '';
            
            $html .= '<tr>
                        <td>' . htmlspecialchars($reviewerName) . '</td>
                        <td>' . htmlspecialchars($appNumber) . '</td>
                        <td>' . htmlspecialchars($action) . '</td>
                        <td>' . htmlspecialchars($oldStatus) . '</td>
                        <td>' . htmlspecialchars($newStatus) . '</td>
                        <td>' . htmlspecialchars($log->remarks ?? '') . '</td>
                        <td>' . $date . '</td>
                        <td>' . $time . '</td>
                        <td>' . htmlspecialchars($log->ip_address ?? 'N/A') . '</td>
                    </tr>';
        }
    }
    
    $html .= '</tbody>
                </table>
            </div>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>This report was generated automatically on ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

private function getSystemLogsForExport($request)
{
    $query = ActivityLog::query()->with('user');
    
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('action', 'LIKE', "%{$search}%")
              ->orWhere('ip_address', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($userQ) use ($search) {
                  $userQ->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
              });
        });
    }
    
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }
    
    if ($request->filled('date_range')) {
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
    }
    
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');
    $query->orderBy($sort, $direction);
    
    return $query->get();
}

private function getApplicationLogsForExport($request)
{
    $query = ApplicationReviewActivity::with(['reviewer', 'application']);
    
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('remarks', 'LIKE', "%{$search}%")
              ->orWhereHas('application', function($appQ) use ($search) {
                  $appQ->where('application_number', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('reviewer', function($userQ) use ($search) {
                  $userQ->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
              });
        });
    }
    
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }
    
    if ($request->filled('date_range')) {
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
    }
    
    $sort = $request->get('sort', 'created_at');
    $direction = $request->get('direction', 'desc');
    $query->orderBy($sort, $direction);
    
    return $query->get();
}
}