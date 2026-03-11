<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_number',
        'google_drive_link',
        'status',
        'admin_notes',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'hard_copy_received'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'hard_copy_received' => 'boolean'
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
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-600',
            'verified' => 'bg-green-100 text-green-600',
            'rejected' => 'bg-red-100 text-red-600',
            'draft' => 'bg-gray-100 text-gray-600',
            default => 'bg-gray-100 text-gray-600'
        };
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'verified' => 'Approved',
            'rejected' => 'Rejected',
            'draft' => 'Draft',
            default => 'Unknown'
        };
    }

    /**
     * Mark documents as verified
     */
    public function markAsVerified($adminId, $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
            'rejection_reason' => null
        ]);
    }

    /**
     * Mark documents as rejected
     */
    public function markAsRejected($reason, $adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'verified_by' => $adminId,
            'admin_notes' => $notes,
            'verified_at' => null
        ]);
    }

    /**
     * Mark as draft (for new applications)
     */
    public function markAsDraft()
    {
        $this->update([
            'status' => 'draft'
        ]);
    }
}