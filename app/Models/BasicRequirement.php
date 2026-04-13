<?php
// app/Models/BasicRequirement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasicRequirement extends Model
{
    protected $table = 'basic_requirements';

    protected $fillable = [
        'user_id',
        'application_id',
        'tct_link',
        'tax_declaration_link',
        'current_tax_receipt_link',
        'spa_link',
        'status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'approved_by',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
        // New columns for document verification
        'tct_checked',
        'tax_declaration_checked',
        'tax_receipt_checked',
        'auto_approved_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'auto_approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tct_checked' => 'boolean',
        'tax_declaration_checked' => 'boolean',
        'tax_receipt_checked' => 'boolean',
    ];

    /**
     * Get the user who owns this basic requirement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the application this basic requirement belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    /**
     * Get the approver who approved this requirement
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the reviewer who reviewed this requirement
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the requirement is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the requirement is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the requirement is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if all documents are verified
     */
    public function isAllDocumentsVerified(): bool
    {
        return $this->tct_checked && 
               $this->tax_declaration_checked && 
               $this->tax_receipt_checked;
    }

    /**
     * Check if TCT is verified
     */
    public function isTctVerified(): bool
    {
        return $this->tct_checked;
    }

    /**
     * Check if Tax Declaration is verified
     */
    public function isTaxDeclarationVerified(): bool
    {
        return $this->tax_declaration_checked;
    }

    /**
     * Check if Tax Receipt is verified
     */
    public function isTaxReceiptVerified(): bool
    {
        return $this->tax_receipt_checked;
    }

    /**
     * Get verification progress percentage
     */
    public function getVerificationProgressAttribute(): int
    {
        $verified = 0;
        $total = 3;
        
        if ($this->tct_checked) $verified++;
        if ($this->tax_declaration_checked) $verified++;
        if ($this->tax_receipt_checked) $verified++;
        
        return round(($verified / $total) * 100);
    }

    /**
     * Mark the requirement as approved
     */
    public function markAsApproved($userId, $notes = null, $autoApprove = false)
    {
        $data = [
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
            'reviewed_at' => now(),
            'reviewed_by' => $userId,
            'admin_notes' => $notes,
            'rejection_reason' => null
        ];
        
        if ($autoApprove) {
            $data['auto_approved_at'] = now();
        }
        
        $this->update($data);
        
        // Update the associated application
        if ($this->application_id) {
            $this->application?->update([
                'basic_requirements_approved_at' => now(),
                'basic_requirements_approved_by' => $userId,
                'last_updated_by' => $userId
            ]);
        }
        
        return $this;
    }

    /**
     * Mark the requirement as rejected
     */
    public function markAsRejected($reason, $userId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by' => $userId,
            'admin_notes' => $notes,
            'approved_at' => null,
            'approved_by' => null,
            'auto_approved_at' => null
        ]);
        
        // Update the associated application
        if ($this->application_id) {
            $this->application?->update([
                'basic_requirements_approved_at' => null,
                'basic_requirements_approved_by' => null,
                'rejection_reason' => $reason,
                'last_updated_by' => $userId
            ]);
        }
        
        return $this;
    }

    /**
     * Update document verification status
     */
    public function updateDocumentVerification($documentType, $checked, $userId = null)
    {
        $allowedTypes = ['tct', 'tax_declaration', 'tax_receipt'];
        
        if (!in_array($documentType, $allowedTypes)) {
            throw new \InvalidArgumentException('Invalid document type');
        }
        
        $column = $documentType === 'tct' ? 'tct_checked' : 
                  ($documentType === 'tax_declaration' ? 'tax_declaration_checked' : 'tax_receipt_checked');
        
        $this->update([$column => $checked]);
        
        // If all documents are verified and status is pending, trigger auto-approval
        if ($this->isAllDocumentsVerified() && $this->isPending()) {
            return ['auto_approve' => true, 'message' => 'All documents verified. Ready for auto-approval.'];
        }
        
        return ['auto_approve' => false, 'message' => 'Verification status updated.'];
    }

    /**
     * Reset document verification status (useful when rejecting and resubmitting)
     */
    public function resetVerificationStatus()
    {
        $this->update([
            'tct_checked' => false,
            'tax_declaration_checked' => false,
            'tax_receipt_checked' => false,
            'auto_approved_at' => null
        ]);
        
        return $this;
    }

    /**
     * Check if the requirement is for an application
     */
    public function hasApplication(): bool
    {
        return !is_null($this->application_id);
    }

    /**
     * Check if it was auto-approved
     */
    public function wasAutoApproved(): bool
    {
        return !is_null($this->auto_approved_at);
    }

    /**
     * Get the display status text
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the status badge color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted submitted date
     */
    public function getFormattedSubmittedAt(): string
    {
        return $this->submitted_at ? $this->submitted_at->format('M d, Y h:i A') : 'N/A';
    }

    /**
     * Get formatted approved date
     */
    public function getFormattedApprovedAt(): string
    {
        return $this->approved_at ? $this->approved_at->format('M d, Y h:i A') : 'N/A';
    }

    /**
     * Get formatted reviewed date
     */
    public function getFormattedReviewedAt(): string
    {
        return $this->reviewed_at ? $this->reviewed_at->format('M d, Y h:i A') : 'N/A';
    }

    /**
     * Get formatted auto-approved date
     */
    public function getFormattedAutoApprovedAt(): string
    {
        return $this->auto_approved_at ? $this->auto_approved_at->format('M d, Y h:i A') : 'N/A';
    }

    /**
     * Get the applicant's full name
     */
    public function getApplicantNameAttribute(): string
    {
        return $this->user ? $this->user->first_name . ' ' . $this->user->last_name : 'Unknown';
    }

    /**
     * Get the applicant's email
     */
    public function getApplicantEmailAttribute(): string
    {
        return $this->user ? $this->user->email : 'Unknown';
    }

    /**
     * Get the application number
     */
    public function getApplicationNumberAttribute(): ?string
    {
        return $this->application ? $this->application->application_number : null;
    }

    /**
     * Get verification summary
     */
    public function getVerificationSummaryAttribute(): array
    {
        return [
            'tct' => $this->tct_checked,
            'tax_declaration' => $this->tax_declaration_checked,
            'tax_receipt' => $this->tax_receipt_checked,
            'progress' => $this->verification_progress,
            'all_verified' => $this->isAllDocumentsVerified()
        ];
    }

    /**
     * Scope for approved requirements
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending requirements
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for rejected requirements
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for fully verified requirements
     */
    public function scopeFullyVerified($query)
    {
        return $query->where('tct_checked', true)
                     ->where('tax_declaration_checked', true)
                     ->where('tax_receipt_checked', true);
    }

    /**
     * Scope for auto-approved requirements
     */
    public function scopeAutoApproved($query)
    {
        return $query->whereNotNull('auto_approved_at');
    }

    /**
     * Scope for manually approved requirements
     */
    public function scopeManuallyApproved($query)
    {
        return $query->where('status', 'approved')->whereNull('auto_approved_at');
    }

    /**
     * Scope for requirements belonging to a specific application
     */
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }

    /**
     * Scope for requirements belonging to a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for requirements submitted today
     */
    public function scopeSubmittedToday($query)
    {
        return $query->whereDate('submitted_at', today());
    }

    /**
     * Scope for requirements approved today
     */
    public function scopeApprovedToday($query)
    {
        return $query->whereDate('approved_at', today());
    }

    /**
     * Scope for requirements rejected today
     */
    public function scopeRejectedToday($query)
    {
        return $query->whereDate('reviewed_at', today())->where('status', 'rejected');
    }

    /**
     * Scope for requirements with pending status and older than specified days
     */
    public function scopePendingOlderThan($query, $days)
    {
        return $query->where('status', 'pending')
            ->where('submitted_at', '<', now()->subDays($days));
    }

    /**
     * Scope for requirements where TCT is not yet verified
     */
    public function scopeTctNotVerified($query)
    {
        return $query->where('tct_checked', false);
    }

    /**
     * Scope for requirements where Tax Declaration is not yet verified
     */
    public function scopeTaxDeclarationNotVerified($query)
    {
        return $query->where('tax_declaration_checked', false);
    }

    /**
     * Scope for requirements where Tax Receipt is not yet verified
     */
    public function scopeTaxReceiptNotVerified($query)
    {
        return $query->where('tax_receipt_checked', false);
    }
}