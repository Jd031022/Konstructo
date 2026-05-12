<?php
// app/Http/Controllers/API/FastLoadController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use App\Models\AssessmentFee;
use App\Models\BfpApplicationData;
use App\Models\OwnershipVerification;
use App\Models\PaymentProof;
use App\Models\ApplicationReviewActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FastLoadController extends Controller
{
    private $cacheDuration = 300;
    
    public function getApplicationData($id)
    {
        try {
            $startTime = microtime(true);
            
            $user = auth()->user();
            $userPosition = $user->profile->position ?? $user->role;
            
            $cacheKey = "fast_load_app_{$id}_user_{$user->id}";
            
            $data = Cache::remember($cacheKey, $this->cacheDuration, function() use ($id, $userPosition) {
                return $this->fetchAllApplicationData($id, $userPosition);
            });
            
            $loadTime = round((microtime(true) - $startTime) * 1000);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'load_time_ms' => $loadTime,
                'from_cache' => Cache::has($cacheKey)
            ]);
            
        } catch (\Exception $e) {
            Log::error('FastLoad error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function fetchAllApplicationData($id, $userPosition)
    {
        // Get application with relationships
        $application = ApplicationDocument::with([
            'user:id,first_name,last_name,email,phone_number,address',
            'lastUpdatedBy:id,first_name,last_name,role'
        ])->find($id);
        
        if (!$application) {
            return null;
        }
        
        // Get assessment
        $assessment = AssessmentFee::where('application_id', $id)->first();
        
        // Get BFP data
        $bfpData = BfpApplicationData::where('application_id', $id)->first();
        
        // Get ownership data
        $ownership = OwnershipVerification::where('application_id', $id)->first();
        
        // Get payment proof/certificates
        $paymentProof = PaymentProof::where('application_id', $id)->first();
        
        // Get activities
        $activities = ApplicationReviewActivity::with('reviewer')
            ->where('application_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Determine user permissions
        $isMonitoring = $userPosition === 'monitoring';
        $isBFP = $userPosition && strtoupper($userPosition) === 'BFP';
        $isCPDO = $userPosition === 'cpdo';
        $isEngineer = $userPosition === 'engineer';
        $isArchitect = $userPosition === 'architect';
        $isAssessor = $userPosition === 'assessor';
        $isTreasurer = $userPosition === 'treasurer';
        
        // Filter document links
        $documentLinks = [];
        if ($application->document_links) {
            $documentLinks = array_filter($application->document_links, function($value) {
                return !empty($value) && $value !== 'undefined';
            });
        }
        
        // Format dates
        $createdAt = $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null;
        $updatedAt = $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null;
        
        // Format applicant name
        $applicantName = 'Unknown';
        if ($application->user) {
            $applicantName = trim($application->user->first_name . ' ' . $application->user->last_name);
            if (empty($applicantName)) {
                $applicantName = 'Unknown';
            }
        }
        
        // ========== CPDO ASSESSMENT DATA - FULL DETAILS ==========
        $cpdoAssessment = [
            'assessment_date' => $application->cpdo_assessment_date,
            'zonal_location_fee' => $application->cpdo_zonal_location_fee ?? 0,
            'palc_fee' => $application->cpdo_palc_fee ?? 0,
            'development_permit_fee' => $application->cpdo_development_permit_fee ?? 0,
            'alteration_permit_fee' => $application->cpdo_alteration_permit_fee ?? 0,
            'site_zoning_certificate_fee' => $application->cpdo_site_zoning_certificate_fee ?? 0,
            'total_cpdo_amount' => $application->cpdo_total_amount ?? 0,
            'cpdo_assessment_notes' => $application->cpdo_assessment_notes,
            'cpdo_additional_fees' => [],
            'has_assessment' => false
        ];
        
        // Parse additional fees
        if ($application->cpdo_additional_fees) {
            $cpdoAssessment['cpdo_additional_fees'] = json_decode($application->cpdo_additional_fees, true) ?: [];
        }
        
        // Check if assessment exists
        if ($application->cpdo_assessment_date || $application->cpdo_total_amount > 0 || !empty($cpdoAssessment['cpdo_additional_fees'])) {
            $cpdoAssessment['has_assessment'] = true;
        }
        
        // Get assessed by name
        $assessedByName = null;
        if ($application->cpdo_assessed_by) {
            $assessor = User::find($application->cpdo_assessed_by);
            if ($assessor) {
                $assessedByName = $assessor->first_name . ' ' . $assessor->last_name;
            }
        }
        $cpdoAssessment['cpdo_assessed_by'] = $assessedByName;
        $cpdoAssessment['cpdo_assessed_at'] = $application->cpdo_assessed_at;
        
        // ========== CERTIFICATES DATA (for upload section) ==========
        $certificates = [
            'zoning_cert' => [
                'link' => $paymentProof ? $paymentProof->zoning_cert_link : null,
                'uploaded_at' => $paymentProof ? $paymentProof->zoning_cert_uploaded_at : null,
                'uploaded_by' => null,
                'is_uploaded' => $paymentProof && !empty($paymentProof->zoning_cert_link)
            ],
            'locational_clearance' => [
                'link' => $paymentProof ? $paymentProof->locational_clearance_link : null,
                'uploaded_at' => $paymentProof ? $paymentProof->locational_clearance_uploaded_at : null,
                'uploaded_by' => null,
                'is_uploaded' => $paymentProof && !empty($paymentProof->locational_clearance_link)
            ]
        ];
        
        // Get uploader names
        if ($paymentProof && $paymentProof->zoning_cert_uploaded_by) {
            $uploader = User::find($paymentProof->zoning_cert_uploaded_by);
            if ($uploader) {
                $certificates['zoning_cert']['uploaded_by'] = $uploader->first_name . ' ' . $uploader->last_name;
            }
        }
        
        if ($paymentProof && $paymentProof->locational_clearance_uploaded_by) {
            $uploader = User::find($paymentProof->locational_clearance_uploaded_by);
            if ($uploader) {
                $certificates['locational_clearance']['uploaded_by'] = $uploader->first_name . ' ' . $uploader->last_name;
            }
        }
        
        // ========== BUILDING PERMIT ASSESSMENT DATA ==========
        $buildingAssessment = null;
        if ($assessment) {
            $buildingAssessment = [
                'id' => $assessment->id,
                'line_grade' => $assessment->line_grade ?? 0,
                'building_fee' => $assessment->building_fee ?? 0,
                'sanitary_fee' => $assessment->sanitary_fee ?? 0,
                'mechanical_fee' => $assessment->mechanical_fee ?? 0,
                'electrical_fee' => $assessment->electrical_fee ?? 0,
                'penalties_fines' => $assessment->penalties_fines ?? 0,
                'total_amount' => $assessment->total_amount ?? 0,
                'assessment_notes' => $assessment->assessment_notes,
                'additional_fees' => $assessment->additional_fees ? (is_string($assessment->additional_fees) ? json_decode($assessment->additional_fees, true) : $assessment->additional_fees) : [],
                'has_assessment' => ($assessment->total_amount > 0 || $assessment->line_grade > 0 || $assessment->building_fee > 0),
                'assessed_by_name' => $assessment->assessed_by ? User::where('id', $assessment->assessed_by)->value('first_name') . ' ' . User::where('id', $assessment->assessed_by)->value('last_name') : null,
                'assessed_at' => $assessment->assessed_at,
            ];
        }
        
        // Build the complete response
        return [
            'application' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
                'building_permit_number' => $application->building_permit_number,
                'permit_remarks' => $application->permit_remarks,
                'applicant_name' => $applicantName,
                'email' => $application->user ? $application->user->email : null,
                'phone' => $application->user ? $application->user->phone_number : null,
                'address' => $application->user ? $application->user->address : null,
                'hard_copy_received' => $application->hard_copy_received ?? false,
                'hard_copy_received_at' => $application->hard_copy_received_at,
                'hardcopy_submission_date' => $application->hardcopy_submission_date,
                'hardcopy_instructions' => $application->hardcopy_instructions,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'last_updated_by_name' => $application->lastUpdatedBy ? $application->lastUpdatedBy->first_name . ' ' . $application->lastUpdatedBy->last_name : null,
                'last_updated_by_role' => $application->lastUpdatedBy ? $application->lastUpdatedBy->role : null,
                'rejection_reason' => $application->rejection_reason,
                'admin_notes' => $application->admin_notes,
                
                // Project Information
                'project_title' => $application->project_title,
                'project_location' => $application->project_location,
                'project_type' => $application->project_type,
                'project_description' => $application->project_description,
                'lot_area' => $application->lot_area,
                'floor_area' => $application->floor_area,
                'num_floors' => $application->num_floors,
                'estimated_cost' => $application->estimated_cost,
                
                // Professional Information
                'architect_name' => $application->architect_name,
                'architect_license' => $application->architect_license,
                'engineer_name' => $application->engineer_name,
                'engineer_license' => $application->engineer_license,
                'electrical_engineer_name' => $application->electrical_engineer_name,
                'electrical_engineer_license' => $application->electrical_engineer_license,
                'sanitary_engineer_name' => $application->sanitary_engineer_name,
                'sanitary_engineer_license' => $application->sanitary_engineer_license,
                
                // CPDO Status
                'cpdo_status' => $application->cpdo_status ?? 'pending',
                'cpdo_remarks' => $application->cpdo_remarks,
                'cpdo_approved_by' => $application->cpdo_approved_by,
                'cpdo_approved_at' => $application->cpdo_approved_at,
                
                // CPDO Assessment (full details)
                'cpdo_assessment' => $cpdoAssessment,
                
                // Document Links
                'document_links' => $documentLinks,
                
                // Archive info
                'is_archived' => $application->is_archived ?? false,
                'archived_at' => $application->archived_at,
                'archive_reason' => $application->archive_reason,
            ],
            
            'assessment' => $buildingAssessment,
            
            'bfp_data' => $bfpData ? [
                'fsec_link' => $bfpData->fsec_link,
                'fsec_filename' => $bfpData->fsec_filename,
                'fsec_uploaded_at' => $bfpData->fsec_uploaded_at,
                'bfp_comments' => $bfpData->bfp_comments,
                'bfp_comments_updated_at' => $bfpData->bfp_comments_updated_at,
                'bfp_user_name' => $bfpData->bfpUser ? $bfpData->bfpUser->first_name . ' ' . $bfpData->bfpUser->last_name : null,
            ] : null,
            
            'ownership_data' => $ownership ? [
                'is_owner' => $ownership->is_owner,
                'tct_link' => $ownership->tct_link,
                'tax_declaration_link' => $ownership->tax_declaration_link,
                'current_tax_receipt_link' => $ownership->current_tax_receipt_link,
                'spa_link' => $ownership->spa_link,
            ] : null,
            
            'payment_proof' => $paymentProof ? [
                'id' => $paymentProof->id,
                'or_link' => $paymentProof->or_link,
                'status' => $paymentProof->status,
                'zoning_cert_link' => $paymentProof->zoning_cert_link,
                'locational_clearance_link' => $paymentProof->locational_clearance_link,
                'zoning_cert_uploaded_at' => $paymentProof->zoning_cert_uploaded_at,
                'locational_clearance_uploaded_at' => $paymentProof->locational_clearance_uploaded_at,
                'zoning_cert_uploaded_by' => $paymentProof->zoning_cert_uploaded_by,
                'locational_clearance_uploaded_by' => $paymentProof->locational_clearance_uploaded_by,
            ] : null,
            
            'certificates' => $certificates,
            
            'recent_activities' => $activities->map(function($activity) {
                $reviewerName = 'System';
                $reviewerPosition = null;
                if ($activity->reviewer) {
                    $reviewerName = $activity->reviewer->first_name . ' ' . $activity->reviewer->last_name;
                    if ($activity->reviewer->profile) {
                        $reviewerPosition = $activity->reviewer->profile->position;
                    }
                }
                
                return [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'action_display' => $this->getActionDisplayText($activity->action),
                    'old_status' => $activity->old_status,
                    'new_status' => $activity->new_status,
                    'remarks' => $activity->remarks,
                    'reviewer_name' => $reviewerName,
                    'reviewer_position' => $reviewerPosition,
                    'created_at' => $activity->created_at,
                    'time_ago' => $activity->created_at ? $activity->created_at->diffForHumans() : null,
                ];
            }),
            
            'user_permissions' => [
                'is_monitoring' => $isMonitoring,
                'is_bfp' => $isBFP,
                'is_cpdo' => $isCPDO,
                'is_engineer' => $isEngineer,
                'is_architect' => $isArchitect,
                'is_assessor' => $isAssessor,
                'is_treasurer' => $isTreasurer,
                'can_verify_documents' => !$isMonitoring && ($isEngineer || $isArchitect),
                'can_mark_hard_copy' => !$isMonitoring && ($isEngineer || $isArchitect),
                'can_manage_verification' => !$isMonitoring && ($isEngineer || $isArchitect),
                'can_archive' => !$isMonitoring,
                'can_request_missing_documents' => !$isMonitoring,
                'can_upload_certificates' => !$isMonitoring && $isCPDO,
                'can_upload_fsec' => !$isMonitoring && $isBFP,
                'can_add_bfp_comments' => !$isMonitoring && $isBFP,
                'can_edit_cpdo_assessment' => !$isMonitoring && $isCPDO,
                'can_submit_cpdo_decision' => !$isMonitoring && $isCPDO,
                'can_verify_ownership_tct' => !$isMonitoring && $isCPDO,
                'can_verify_ownership_tax' => !$isMonitoring && $isAssessor,
                'can_verify_ownership_receipt' => !$isMonitoring && $isTreasurer,
                'can_verify_ownership_spa' => !$isMonitoring && ($isCPDO || $isAssessor || $isTreasurer),
            ],
            
            'user_info' => [
                'id' => auth()->id(),
                'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'position' => $userPosition,
                'email' => auth()->user()->email,
            ]
        ];
    }
    
    public function clearCache($id)
    {
        try {
            $cacheKey = "fast_load_app_{$id}_user_" . auth()->id();
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
    
    private function getActionDisplayText($action)
    {
        $actionMap = [
            'application_submitted' => 'Application Submitted',
            'status_updated' => 'Status Updated',
            'document_verified' => 'Document Verified',
            'document_reset' => 'Verification Reset',
            'batch_reset_all' => 'Batch Reset All Documents',
            'hard_copy_received' => 'Hard Copy Received',
            'missing_documents_requested' => 'Missing Documents Requested',
            'note_added' => 'Note Added',
            'application_created' => 'Application Created',
            'application_deleted' => 'Application Deleted',
            'application_archived' => 'Application Archived',
            'application_restored' => 'Application Restored',
            'assessment_saved' => 'Assessment Saved',
            'assessment_completed' => 'Assessment Completed',
            'fsec_uploaded' => 'FSEC Document Uploaded',
            'fsec_deleted' => 'FSEC Document Deleted',
            'bfp_comments_added' => 'BFP Comments Added',
            'ownership_document_verified' => 'Ownership Document Verified',
            'ownership_document_unverified' => 'Ownership Document Unverified',
            'cpdo_approved' => 'CPDO Approved',
            'cpdo_rejected' => 'CPDO Rejected',
            'cpdo_assessment_saved' => 'CPDO Assessment Saved',
        ];
        
        return $actionMap[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }
    /**
 * Clear application cache for all users (staff only)
 * This ensures everyone sees the updated status after CPDO decision
 */
public function clearApplicationCache($id)
{
    try {
        // Only staff and admin can clear cache
        if (!in_array(auth()->user()->role, ['staff', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $count = 0;
        
        // Get all staff and admin users
        $staffUsers = User::whereIn('role', ['staff', 'admin'])->get();
        
        foreach ($staffUsers as $user) {
            $cacheKey = "fast_load_app_{$id}_user_{$user->id}";
            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
                $count++;
            }
        }
        
        // Also clear for the current user (redundant but safe)
        $currentCacheKey = "fast_load_app_{$id}_user_" . auth()->id();
        if (Cache::has($currentCacheKey)) {
            Cache::forget($currentCacheKey);
        }
        
        // Also clear for the applicant if they have a staff role
        $application = ApplicationDocument::find($id);
        if ($application && $application->user_id) {
            $applicantCacheKey = "fast_load_app_{$id}_user_{$application->user_id}";
            if (Cache::has($applicantCacheKey)) {
                Cache::forget($applicantCacheKey);
                $count++;
            }
        }
        
        Log::info('Application cache cleared', [
            'application_id' => $id,
            'cleared_by' => auth()->id(),
            'entries_cleared' => $count
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} cache entries for application {$id}"
        ]);
        
    } catch (\Exception $e) {
        Log::error('Failed to clear application cache: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to clear cache: ' . $e->getMessage()
        ], 500);
    }
}
}