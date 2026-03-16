<?php
// app/Http/Controllers/Admin/SettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;

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
        $type = $request->get('type', 'system');
        
        if ($type == 'application') {
            // Export application review logs
            $logs = ApplicationReviewActivity::with(['reviewer', 'application'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $filename = 'application-review-logs-' . now()->format('Y-m-d-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($logs) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($handle, [
                    'Reviewer',
                    'Reviewer Email',
                    'Application #',
                    'Action',
                    'Old Status',
                    'New Status',
                    'Remarks',
                    'Date & Time',
                    'IP Address'
                ]);
                
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown',
                        $log->reviewer ? $log->reviewer->email : 'N/A',
                        $log->application ? $log->application->application_number : 'N/A',
                        $log->action,
                        $log->old_status,
                        $log->new_status,
                        $log->remarks ?? '',
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->ip_address ?? 'N/A'
                    ]);
                }
                
                fclose($handle);
            };
        } else {
            // Export system logs
            $logs = ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $filename = 'system-logs-' . now()->format('Y-m-d-His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($logs) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($handle, [
                    'Name',
                    'Username',
                    'Email',
                    'Action',
                    'Description',
                    'IP Address',
                    'Date & Time',
                    'Status'
                ]);
                
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User',
                        $log->user ? $log->user->username : 'N/A',
                        $log->user ? $log->user->email : 'N/A',
                        $log->action,
                        $log->description ?? '',
                        $log->ip_address ?? 'N/A',
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->status
                    ]);
                }
                
                fclose($handle);
            };
        }
        
        return response()->stream($callback, 200, $headers);
    }
}