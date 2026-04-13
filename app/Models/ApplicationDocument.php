<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $table = 'application_documents';

 protected $fillable = [
        // Core fields
        'user_id',
        'application_number',
        'status',
        
        // Document fields
        'google_drive_link',
        'document_links',
        'pdf_annotations',
        
        // Admin/Staff fields
        'admin_notes',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'hard_copy_received',
        'hard_copy_received_at',
        'last_updated_by',
        
        // Archive fields
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason',
        
        // Step 1: Project Information
        'project_title',
        'project_location',
        'project_type',
        'lot_area',
        'floor_area',
        'num_floors',
        'estimated_cost',
        'project_description',
        
        // Step 1: Owner/Applicant Information
        'owner_name',
        'owner_address',
        'contact_number',
        'owner_email',
        
        // Step 1: Professional Information - Architect
        'architect_name',
        'architect_license',
        
        // Step 1: Professional Information - Civil Engineer
        'engineer_name',
        'engineer_license',
        
        // Step 1: Professional Information - Electrical Engineer (NEW)
        'electrical_engineer_name',
        'electrical_engineer_license',
        
        // Step 1: Professional Information - Sanitary Engineer / Master Plumber (NEW)
        'sanitary_engineer_name',
        'sanitary_engineer_license',
        
        // Step Completion Tracking
        'step1_completed',
        'step1_completed_at',
        'step2_completed',
        'step2_completed_at',
        'step3_completed',
        'step3_completed_at',
    ];

    protected $casts = [
        // Date/Timestamp fields
        'verified_at' => 'datetime',
        'hard_copy_received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime',
        'step1_completed_at' => 'datetime',
        'step2_completed_at' => 'datetime',
        'step3_completed_at' => 'datetime',
        
        // Boolean fields
        'hard_copy_received' => 'boolean',
        'is_archived' => 'boolean',
        'step1_completed' => 'boolean',
        'step2_completed' => 'boolean',
        'step3_completed' => 'boolean',
        
        // JSON/Array fields
        'document_links' => 'array',
        'pdf_annotations' => 'array',
        
        // Decimal fields
        'lot_area' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
    ];

    /**
     * Get the user that owns the application documents
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified the documents
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who last updated the application
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Get the user who archived the application
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Get the basic requirements for this application
     */
    public function basicRequirement()
    {
        return $this->hasOne(BasicRequirement::class, 'application_id');
    }

    /**
     * Get the review activities for this application
     */
    public function reviewActivities()
    {
        return $this->hasMany(ApplicationReviewActivity::class, 'application_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest review activity for this application
     */
    public function latestReviewActivity()
    {
        return $this->hasOne(ApplicationReviewActivity::class, 'application_id')->latestOfMany();
    }

    /**
     * Get the assessment fee for this application
     */
    public function assessmentFee()
    {
        return $this->hasOne(AssessmentFee::class, 'application_id');
    }

    /**
     * Check if basic requirements are approved for this application
     */
    public function hasApprovedBasicRequirements()
    {
        return $this->basicRequirement && $this->basicRequirement->status === 'approved';
    }

    /**
     * Check if basic requirements are pending for this application
     */
    public function hasPendingBasicRequirements()
    {
        return $this->basicRequirement && $this->basicRequirement->status === 'pending';
    }

    /**
     * Check if basic requirements are rejected for this application
     */
    public function hasRejectedBasicRequirements()
    {
        return $this->basicRequirement && $this->basicRequirement->status === 'rejected';
    }

    /**
     * Check if basic requirements are not submitted for this application
     */
    public function hasNoBasicRequirements()
    {
        return !$this->basicRequirement;
    }

    /**
     * Get basic requirements status for display
     */
    public function getBasicRequirementsStatus()
    {
        if (!$this->basicRequirement) {
            return 'not_submitted';
        }
        return $this->basicRequirement->status;
    }

    /**
     * Get basic requirements status text for display
     */
    public function getBasicRequirementsStatusText()
    {
        if (!$this->basicRequirement) {
            return 'Not Submitted';
        }
        return $this->basicRequirement->getStatusDisplayAttribute();
    }

    /**
     * Get basic requirements status color for display
     */
    public function getBasicRequirementsStatusColor()
    {
        if (!$this->basicRequirement) {
            return 'bg-gray-100 text-gray-600';
        }
        return $this->basicRequirement->getStatusColorAttribute();
    }

    /**
     * Scope a query to only include pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include verified applications
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope a query to only include rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include draft applications
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include under review applications
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under-review');
    }

    /**
     * Scope a query to only include document verification applications
     */
    public function scopeDocumentVerification($query)
    {
        return $query->where('status', 'document-verification');
    }

    /**
     * Scope a query to only include for assessment applications
     */
    public function scopeForAssessment($query)
    {
        return $query->where('status', 'for-assessment');
    }

    /**
     * Scope a query to only include approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include for release applications
     */
    public function scopeForRelease($query)
    {
        return $query->where('status', 'for-release');
    }

    /**
     * Scope a query to only include submitted applications (non-draft)
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', [
            'pending', 
            'under-review', 
            'document-verification', 
            'for-assessment',
            'approved', 
            'for-release', 
            'verified', 
            'rejected'
        ]);
    }

    /**
     * Scope a query to only include active applications (not rejected or draft)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'pending', 
            'under-review', 
            'document-verification', 
            'for-assessment',
            'approved', 
            'for-release', 
            'verified'
        ]);
    }

    /**
     * Scope a query to only include applications with approved basic requirements
     */
    public function scopeWithApprovedBasicRequirements($query)
    {
        return $query->whereHas('basicRequirement', function($q) {
            $q->where('status', 'approved');
        });
    }

    /**
     * Scope a query to only include applications with pending basic requirements
     */
    public function scopeWithPendingBasicRequirements($query)
    {
        return $query->whereHas('basicRequirement', function($q) {
            $q->where('status', 'pending');
        });
    }

    /**
     * Scope a query to only include applications without basic requirements
     */
    public function scopeWithoutBasicRequirements($query)
    {
        return $query->whereDoesntHave('basicRequirement');
    }

    /**
     * Check if application is verified
     */
    public function isVerified()
    {
        return $this->status === 'verified';
    }

    /**
     * Check if application is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application is draft
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if application is under review
     */
    public function isUnderReview()
    {
        return $this->status === 'under-review';
    }

    /**
     * Check if application is in document verification
     */
    public function isDocumentVerification()
    {
        return $this->status === 'document-verification';
    }

    /**
     * Check if application is for assessment
     */
    public function isForAssessment()
    {
        return $this->status === 'for-assessment';
    }

    /**
     * Check if application is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is for release
     */
    public function isForRelease()
    {
        return $this->status === 'for-release';
    }

    /**
     * Check if hard copy is received
     */
    public function isHardCopyReceived()
    {
        return $this->hard_copy_received === true;
    }

    /**
     * Check if application is submitted (not draft)
     */
    public function isSubmitted()
    {
        return !in_array($this->status, ['draft']);
    }

    /**
     * Check if application can be edited
     */
    public function isEditable()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Check if application can be deleted
     */
    public function isDeletable()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if user can proceed to application steps
     */
    public function canProceedToSteps()
    {
        return $this->hasApprovedBasicRequirements();
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'draft' => 'bg-gray-100 text-gray-800',
            'under-review' => 'bg-purple-100 text-purple-800',
            'document-verification' => 'bg-indigo-100 text-indigo-800',
            'for-assessment' => 'bg-indigo-100 text-indigo-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'for-release' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status text for display
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'draft' => 'Draft',
            'under-review' => 'Under Review',
            'document-verification' => 'Document Verification',
            'for-assessment' => 'For Assessment',
            'approved' => 'Approved',
            'for-release' => 'For Release',
            default => ucfirst(str_replace('-', ' ', $this->status))
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            'draft' => 'gray',
            'under-review' => 'purple',
            'document-verification' => 'indigo',
            'for-assessment' => 'indigo',
            'approved' => 'emerald',
            'for-release' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get progress percentage based on status
     */
    public function getProgressPercentage()
    {
        return match($this->status) {
            'draft' => 0,
            'pending' => 20,
            'under-review' => 35,
            'document-verification' => 50,
            'for-assessment' => 65,
            'approved' => 80,
            'for-release' => 95,
            'verified' => 100,
            'rejected' => 100,
            default => 0
        };
    }

    /**
     * Mark documents as verified
     */
    public function markAsVerified($adminId, $notes = null)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
            'rejection_reason' => null,
            'last_updated_by' => $adminId
        ]);

        return $oldStatus;
    }

    /**
     * Mark documents as rejected
     */
    public function markAsRejected($reason, $adminId = null, $notes = null)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'verified_by' => $adminId,
            'admin_notes' => $notes,
            'verified_at' => null,
            'last_updated_by' => $adminId
        ]);

        return $oldStatus;
    }

    /**
     * Mark as draft (for new applications)
     */
    public function markAsDraft()
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'draft'
        ]);

        return $oldStatus;
    }

    /**
     * Mark as for assessment
     */
    public function markAsForAssessment($userId = null, $remarks = null)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'for-assessment',
            'last_updated_by' => $userId,
            'admin_notes' => $remarks ? $this->admin_notes . "\n\n" . $remarks : $this->admin_notes
        ]);

        return $oldStatus;
    }

    /**
     * Update status with tracking
     */
    public function updateStatus($newStatus, $userId = null, $remarks = null)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => $newStatus,
            'last_updated_by' => $userId
        ]);

        return $oldStatus;
    }

    /**
     * Mark hard copy as received
     */
    public function markHardCopyReceived($userId)
    {
        $oldStatus = $this->hard_copy_received;
        
        $this->update([
            'hard_copy_received' => true,
            'hard_copy_received_at' => now(),
            'last_updated_by' => $userId
        ]);

        return $oldStatus;
    }

    /**
     * Generate a unique application number
     */
    public static function generateApplicationNumber()
    {
        $year = date('Y');
        do {
            $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $applicationNumber = $year . $random;
        } while (self::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

    /**
     * Get the formatted created date
     */
    public function getFormattedCreatedAt()
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : null;
    }

    /**
     * Get the formatted updated date
     */
    public function getFormattedUpdatedAt()
    {
        return $this->updated_at ? $this->updated_at->format('M d, Y h:i A') : null;
    }

    /**
     * Get the formatted verified date
     */
    public function getFormattedVerifiedAt()
    {
        return $this->verified_at ? $this->verified_at->format('M d, Y h:i A') : null;
    }

    /**
     * Get the formatted hard copy received date
     */
    public function getFormattedHardCopyReceivedAt()
    {
        return $this->hard_copy_received_at ? $this->hard_copy_received_at->format('M d, Y h:i A') : null;
    }

    /**
     * Get the verifier name
     */
    public function getVerifierName()
    {
        return $this->verifier ? $this->verifier->first_name . ' ' . $this->verifier->last_name : null;
    }

    /**
     * Get the last updated by name
     */
    public function getLastUpdatedByName()
    {
        return $this->lastUpdatedBy ? $this->lastUpdatedBy->first_name . ' ' . $this->lastUpdatedBy->last_name : null;
    }

    /**
     * Get the applicant name
     */
    public function getApplicantName()
    {
        return $this->user ? $this->user->first_name . ' ' . $this->user->last_name : null;
    }

    /**
     * Get the applicant email
     */
    public function getApplicantEmail()
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get the applicant phone
     */
    public function getApplicantPhone()
    {
        return $this->user ? $this->user->phone_number : null;
    }

    /**
     * Check if user can submit this application
     */
    public function canBeSubmitted()
    {
        return $this->status === 'draft' && $this->google_drive_link !== null;
    }

    /**
     * Check if user can add Google Drive link
     */
    public function canAddLink()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Get the next available statuses for workflow
     */
    public function getNextPossibleStatuses()
    {
        return match($this->status) {
            'pending' => ['under-review', 'rejected'],
            'under-review' => ['document-verification', 'rejected'],
            'document-verification' => ['for-assessment', 'rejected'],
            'for-assessment' => ['approved', 'rejected'],
            'approved' => ['for-release'],
            'for-release' => ['verified'],
            default => []
        };
    }
    
    /**
     * Set document links as JSON
     */
    public function setDocumentLinksAttribute($value)
    {
        $this->attributes['document_links'] = json_encode($value);
    }

    /**
     * Get document links as array
     */
    public function getDocumentLinksAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get total assessment fee if exists
     */
    public function getTotalAssessmentFee()
    {
        if ($this->assessmentFee && $this->assessmentFee->total_amount) {
            return $this->assessmentFee->total_amount;
        }
        return null;
    }

    /**
     * Check if assessment has been completed
     */
    public function hasAssessment()
    {
        return $this->assessmentFee !== null && $this->assessmentFee->total_amount !== null;
    }

    /**
     * Get formatted assessment total
     */
    public function getFormattedAssessmentTotal()
    {
        $total = $this->getTotalAssessmentFee();
        if ($total) {
            return '₱' . number_format($total, 2);
        }
        return 'Not assessed yet';
    }
}