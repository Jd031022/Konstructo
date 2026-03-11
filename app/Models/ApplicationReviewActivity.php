<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationReviewActivity extends Model
{
    use HasFactory;

    protected $table = 'application_review_activities';

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'action',
        'old_status',
        'new_status',
        'remarks',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the application that owns the activity
     */
    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    /**
     * Get the reviewer who performed the action
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get action display text
     */
    public function getActionDisplayAttribute()
    {
        return match($this->action) {
            'status_updated' => 'Status Updated',
            'note_added' => 'Note Added',
            'document_verified' => 'Documents Verified',
            'hard_copy_received' => 'Hard Copy Received',
            'application_created' => 'Application Created',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Get action icon class
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'status_updated' => 'text-blue-600',
            'note_added' => 'text-yellow-600',
            'document_verified' => 'text-green-600',
            'hard_copy_received' => 'text-purple-600',
            'application_created' => 'text-emerald-600',
            default => 'text-gray-600'
        };
    }
}