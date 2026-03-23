<?php
// app/Models/BasicRequirement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasicRequirement extends Model
{
    protected $fillable = [
        'user_id',
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
        'approved_by'
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function markAsApproved($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
            'rejection_reason' => null
        ]);
    }

    public function markAsRejected($reason, $userId)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $userId,
            'approved_at' => null
        ]);
    }
}