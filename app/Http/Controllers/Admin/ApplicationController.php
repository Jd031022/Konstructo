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
    
    /**
     * Export applications with multiple format options
     */
    public function exportApplications(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            
            // Get filtered applications based on current filters
            $applications = $this->getFilteredApplicationsForExport($request);
            
            switch ($format) {
                case 'csv':
                    return $this->exportAsCSV($applications);
                case 'excel':
                    return $this->exportAsExcel($applications);
                case 'pdf':
                    return $this->exportAsPDF($applications);
                case 'html':
                default:
                    return $this->exportAsHTML($applications);
            }
        } catch (\Exception $e) {
            Log::error('Applications export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get filtered applications for export
     */
    private function getFilteredApplicationsForExport(Request $request)
    {
        $query = ApplicationDocument::with('user')
            ->where('is_archived', false)
            ->whereIn('status', [
                'pending', 'under-review', 'approved', 
                'rejected', 'for-release', 'verified'
            ]);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply aging filter
        if ($request->filled('aging')) {
            switch ($request->aging) {
                case 'new':
                    $query->where('created_at', '>=', now()->subDays(2));
                    break;
                case 'warning':
                    $query->where('created_at', '>=', now()->subDays(5))
                          ->where('created_at', '<', now()->subDays(2));
                    break;
                case 'critical':
                    $query->where('created_at', '>=', now()->subDays(10))
                          ->where('created_at', '<', now()->subDays(5));
                    break;
                case 'overdue':
                    $query->where('created_at', '<', now()->subDays(10));
                    break;
            }
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Export as CSV (Excel compatible)
     */
    private function exportAsCSV($applications)
    {
        $filename = 'applications_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($applications) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($handle, [
                'Application Number',
                'Applicant Name',
                'Email',
                'Phone',
                'Status',
                'Submitted Date',
                'Last Updated',
                'Aging Days',
                'Project Title',
                'Project Location',
                'Project Type'
            ]);
            
            foreach ($applications as $app) {
                $applicantName = $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown';
                $agingDays = $this->calculateAgingDays($app->created_at);
                $statusText = $this->getStatusText($app->status);
                
                fputcsv($handle, [
                    $app->application_number,
                    $applicantName,
                    $app->user ? $app->user->email : 'N/A',
                    $app->user ? $app->user->phone_number : 'N/A',
                    $statusText,
                    $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : '',
                    $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : '',
                    $agingDays,
                    $app->project_title ?? '',
                    $app->project_location ?? '',
                    ucfirst(str_replace('_', ' ', $app->project_type ?? ''))
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Export as Excel (XLSX format)
     */
    private function exportAsExcel($applications)
    {
        $filename = 'applications_export_' . date('Y-m-d_His') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() use ($applications) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($handle, [
                'Application Number',
                'Applicant Name',
                'Email',
                'Phone',
                'Status',
                'Submitted Date',
                'Last Updated',
                'Aging Days',
                'Project Title',
                'Project Location',
                'Project Type'
            ]);
            
            foreach ($applications as $app) {
                $applicantName = $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown';
                $agingDays = $this->calculateAgingDays($app->created_at);
                $statusText = $this->getStatusText($app->status);
                
                fputcsv($handle, [
                    $app->application_number,
                    $applicantName,
                    $app->user ? $app->user->email : 'N/A',
                    $app->user ? $app->user->phone_number : 'N/A',
                    $statusText,
                    $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : '',
                    $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : '',
                    $agingDays,
                    $app->project_title ?? '',
                    $app->project_location ?? '',
                    ucfirst(str_replace('_', ' ', $app->project_type ?? ''))
                ]);
            }
            
            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Export as PDF
     */
    private function exportAsPDF($applications)
    {
        // Calculate statistics
        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'under_review' => $applications->where('status', 'under-review')->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
            'for_release' => $applications->where('status', 'for-release')->count(),
            'verified' => $applications->where('status', 'verified')->count(),
        ];
        
        $html = $this->generatePDFHTML($applications, $stats);
        
        // Use DomPDF if installed, otherwise fallback to HTML
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('applications_export_' . date('Y-m-d_His') . '.pdf');
        }
        
        // Fallback to HTML download
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="applications_export_' . date('Y-m-d_His') . '.html"');
    }

    /**
     * Export as HTML
     */
    private function exportAsHTML($applications)
    {
        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'under_review' => $applications->where('status', 'under-review')->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
            'for_release' => $applications->where('status', 'for-release')->count(),
            'verified' => $applications->where('status', 'verified')->count(),
        ];
        
        $html = $this->generateHTMLExport($applications, $stats);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="applications_export_' . date('Y-m-d_His') . '.html"');
    }

    /**
     * Generate PDF HTML content
     */
    private function generatePDFHTML($applications, $stats)
    {
        $statusColors = [
            'pending' => '#F59E0B',
            'under-review' => '#3B82F6',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            'for-release' => '#8B5CF6',
            'verified' => '#22C55E'
        ];
        
        $statusLabels = [
            'pending' => 'Pending Review',
            'under-review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'for-release' => 'For Release',
            'verified' => 'Completed'
        ];
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Applications Export - ' . date('Y-m-d H:i:s') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: "Poppins", Arial, sans-serif;
                    padding: 20px;
                    color: #333;
                    font-size: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #155386;
                }
                .header h1 {
                    color: #155386;
                    font-size: 20px;
                }
                .header p {
                    color: #666;
                    font-size: 10px;
                }
                .stats {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                }
                .stat-box {
                    background: #f5f5f5;
                    padding: 10px;
                    border-radius: 8px;
                    text-align: center;
                    min-width: 80px;
                }
                .stat-box .label {
                    font-size: 9px;
                    color: #666;
                }
                .stat-box .value {
                    font-size: 16px;
                    font-weight: bold;
                    color: #155386;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background: #155386;
                    color: white;
                    font-size: 9px;
                }
                td {
                    font-size: 9px;
                }
                tr:nth-child(even) {
                    background: #f9f9f9;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                    font-size: 8px;
                    color: #999;
                }
                .status-badge {
                    display: inline-block;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 8px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Konstructo Applications Export</h1>
                <p>Generated on: ' . date('F d, Y g:i:s A') . '</p>
            </div>
            
            <div class="stats">
                <div class="stat-box"><div class="label">Total</div><div class="value">' . $stats['total'] . '</div></div>
                <div class="stat-box"><div class="label">Pending</div><div class="value">' . $stats['pending'] . '</div></div>
                <div class="stat-box"><div class="label">Under Review</div><div class="value">' . $stats['under_review'] . '</div></div>
                <div class="stat-box"><div class="label">Approved</div><div class="value">' . $stats['approved'] . '</div></div>
                <div class="stat-box"><div class="label">For Release</div><div class="value">' . $stats['for_release'] . '</div></div>
                <div class="stat-box"><div class="label">Completed</div><div class="value">' . $stats['verified'] . '</div></div>
                <div class="stat-box"><div class="label">Rejected</div><div class="value">' . $stats['rejected'] . '</div></div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>App #</th>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Aging</th>
                        <th>Project Title</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($applications as $app) {
            $applicantName = $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown';
            $agingDays = $this->calculateAgingDays($app->created_at);
            $statusColor = $statusColors[$app->status] ?? '#6c757d';
            $statusLabel = $statusLabels[$app->status] ?? ucfirst($app->status);
            
            $html .= '<tr>
                        <td>' . htmlspecialchars($app->application_number) . '</td>
                        <td>' . htmlspecialchars($applicantName) . '</td>
                        <td>' . htmlspecialchars($app->user ? $app->user->email : 'N/A') . '</td>
                        <td><span class="status-badge" style="background:' . $statusColor . '20; color:' . $statusColor . ';">' . $statusLabel . '</span></td>
                        <td>' . ($app->created_at ? $app->created_at->format('Y-m-d') : '') . '</td>
                        <td>' . $agingDays . ' days</span></td>
                        <td>' . htmlspecialchars(substr($app->project_title ?? '', 0, 50)) . '</span></td>
                     </tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="footer">
                <p>Konstructo - Smart Infrastructure Oversight</p>
                <p>This report was generated automatically on ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Generate HTML Export content
     */
    private function generateHTMLExport($applications, $stats)
    {
        $statusColors = [
            'pending' => '#F59E0B',
            'under-review' => '#3B82F6',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            'for-release' => '#8B5CF6',
            'verified' => '#22C55E'
        ];
        
        $statusLabels = [
            'pending' => 'Pending Review',
            'under-review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'for-release' => 'For Release',
            'verified' => 'Completed'
        ];
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Konstructo Applications Export - ' . date('Y-m-d H:i:s') . '</title>
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
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .stat-card {
                    background: #f8f9fa;
                    border-radius: 16px;
                    padding: 20px;
                    text-align: center;
                    border: 1px solid #e5e7eb;
                }
                .stat-label {
                    font-size: 13px;
                    color: #6c757d;
                    margin-bottom: 8px;
                    text-transform: uppercase;
                }
                .stat-value {
                    font-size: 32px;
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
                        <h1>Konstructo Applications Export</h1>
                        <div class="header-date">Generated: ' . date('F d, Y g:i:s A') . '</div>
                    </div>
                    <button class="print-btn" onclick="window.print()">Print / Save PDF</button>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-label">Total Applications</div><div class="stat-value">' . $stats['total'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Pending</div><div class="stat-value">' . $stats['pending'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Under Review</div><div class="stat-value">' . $stats['under_review'] . '</div></div>
                    <div class="stat-card"><div class="stat-label">Completed</div><div class="stat-value">' . $stats['verified'] . '</div></div>
                </div>
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Application #</th>
                                <th>Applicant Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Last Updated</th>
                                <th>Aging Days</th>
                                <th>Project Title</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($applications as $app) {
            $applicantName = $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown';
            $agingDays = $this->calculateAgingDays($app->created_at);
            $statusColor = $statusColors[$app->status] ?? '#6c757d';
            $statusLabel = $statusLabels[$app->status] ?? ucfirst($app->status);
            $submittedDate = $app->created_at ? $app->created_at->format('M d, Y') : 'N/A';
            $updatedDate = $app->updated_at ? $app->updated_at->format('M d, Y') : 'N/A';
            
            $html .= '<tr>
                        <td><strong>' . htmlspecialchars($app->application_number) . '</strong></td>
                        <td>' . htmlspecialchars($applicantName) . '</td>
                        <td>' . htmlspecialchars($app->user ? $app->user->email : 'N/A') . '</td>
                        <td>' . htmlspecialchars($app->user ? $app->user->phone_number : 'N/A') . '</td>
                        <td><span class="status-badge" style="background:' . $statusColor . '20; color:' . $statusColor . ';">' . $statusLabel . '</span></td>
                        <td>' . $submittedDate . '</td>
                        <td>' . $updatedDate . '</td>
                        <td>' . $agingDays . ' days</span></td>
                        <td>' . htmlspecialchars(substr($app->project_title ?? '', 0, 60)) . '</span></td>
                     </tr>';
        }
        
        $html .= '</tbody>
                    </table>
                </div>
                
                <div class="footer">
                    <p>Konstructo - Smart Infrastructure Oversight</p>
                    <p>Report ID: KAP-' . date('Ymd') . '-' . rand(1000, 9999) . ' | Generated: ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Calculate aging days
     */
    private function calculateAgingDays($submittedAt)
    {
        if (!$submittedAt) return 0;
        $submittedDate = new \DateTime($submittedAt);
        $currentDate = new \DateTime();
        return $submittedDate->diff($currentDate)->days;
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'pending' => 'Pending Review',
            'under-review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'for-release' => 'For Release',
            'verified' => 'Completed'
        ];
        return $statusMap[$status] ?? ucfirst($status);
    }
    
}