<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'status',
        'data',
        // add other fields as needed
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user (applicant) who owns this application
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff member assigned to this application
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}