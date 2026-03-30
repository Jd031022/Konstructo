<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function index()
    {
        // Your index method
    }
    
    public function show($id)
    {
        // Your show method
    }
    
    /**
     * Export applications (CSV) - excluding archived
     */
    public function export()
    {
        try {
            Log::info('Admin export applications started');
            
            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy'])
                ->where('is_archived', false)
                ->whereIn('status', [
                    'pending', 
                    'under-review', 
                    'document-verification',
                    'approved', 
                    'rejected', 
                    'for-release', 
                    'verified'
                ])
                ->get();
            
            Log::info('Found ' . $applications->count() . ' applications to export');
            
            $filename = 'applications_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');
                
                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Email',
                    'Phone',
                    'Status',
                    'Hard Copy Received',
                    'Date Submitted',
                    'Last Updated By',
                    'Google Drive Link'
                ]);
                
                foreach ($applications as $app) {
                    fputcsv($handle, [
                        $app->application_number,
                        $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        $app->user ? $app->user->email : '',
                        $app->user ? $app->user->phone_number : '',
                        ucfirst(str_replace('-', ' ', $app->status)),
                        $app->hard_copy_received ? 'Yes' : 'No',
                        $app->created_at ? $app->created_at->format('Y-m-d') : '',
                        $app->lastUpdatedBy ? $app->lastUpdatedBy->first_name . ' ' . $app->lastUpdatedBy->last_name : 'N/A',
                        $app->google_drive_link
                    ]);
                }
                
                fclose($handle);
            };
            
            Log::info('Admin export completed successfully');
            return response()->streamDownload($callback, $filename, $headers);
            
        } catch (\Exception $e) {
            Log::error('Error exporting applications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error exporting applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export archived applications (CSV)
     */
    public function exportArchived()
    {
        try {
            Log::info('Admin export archived applications started');

            $applications = ApplicationDocument::with(['user', 'lastUpdatedBy', 'archivedBy'])
                ->where('is_archived', true)
                ->get();

            Log::info('Found ' . $applications->count() . ' archived applications to export');

            $filename = 'archived_applications_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Email',
                    'Phone',
                    'Status',
                    'Hard Copy Received',
                    'Date Submitted',
                    'Archived Date',
                    'Archived By',
                    'Google Drive Link'
                ]);

                foreach ($applications as $app) {
                    fputcsv($handle, [
                        $app->application_number,
                        $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        $app->user ? $app->user->email : '',
                        $app->user ? $app->user->phone_number : '',
                        ucfirst(str_replace('-', ' ', $app->status)),
                        $app->hard_copy_received ? 'Yes' : 'No',
                        $app->created_at ? $app->created_at->format('Y-m-d') : '',
                        $app->archived_at ? $app->archived_at->format('Y-m-d') : '',
                        $app->archivedBy ? $app->archivedBy->first_name . ' ' . $app->archivedBy->last_name : 'N/A',
                        $app->google_drive_link
                    ]);
                }

                fclose($handle);
            };

            Log::info('Admin archived export completed successfully');
            return response()->streamDownload($callback, $filename, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting archived applications: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error exporting archived applications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Add other methods as needed
}