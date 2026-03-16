<?php
// app/Http/Controllers/Admin/SettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        // Set default tab to 'logs'
        $currentTab = $request->get('tab', 'logs');
        
        // Initialize logs as null
        $logs = null;
        
        // Only fetch logs if we're on the logs tab
        if ($currentTab == 'logs') {
            // Start query with user relationship
            $query = ActivityLog::with('user');
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ip_address', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('username', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            // Apply action filter
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            // Apply date range filter
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
            
            // Apply sorting
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
            } else {
                // Default sort by created_at descending
                $query->orderBy('created_at', 'desc');
            }
            
            // Paginate results
            $logs = $query->paginate(15)->withQueryString();
        }
        
        // Return view with data
        return view('admin.settings', compact('currentTab', 'logs'));
    }

    public function exportLogs(Request $request)
    {
        // Fetch all logs for export
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Create filename with timestamp
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        
        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        // Create CSV content
        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($handle, [
                'Name',
                'Username',
                'Email',
                'Action',
                'IP Address',
                'Date & Time',
                'Status',
                'Description',
                'Metadata'
            ]);
            
            // Add data rows
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User',
                    $log->user ? $log->user->username : 'N/A',
                    $log->user ? $log->user->email : 'N/A',
                    $log->action,
                    $log->ip_address ?? 'N/A',
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->status,
                    $log->description ?? '',
                    $log->metadata ? json_encode($log->metadata) : ''
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}