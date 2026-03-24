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
        'deed_of_sale_link',
        'spa_link',
        'is_owner',
        'status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'approved_by',
        'reviewed_at',
        'reviewed_by',
        'admin_notes'
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Mark the requirement as approved
     */
    public function markAsApproved($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
            'reviewed_at' => now(),
            'reviewed_by' => $userId,
            'admin_notes' => $notes,
            'rejection_reason' => null
        ]);
        
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
            'approved_by' => null
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
            'pending' => 'bg-yellow-100 text-yellow-600',
            'approved' => 'bg-green-100 text-green-600',
            'rejected' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600'
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
}