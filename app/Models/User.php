<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'phone_number',
        'email',
        'zip_code',
        'address',
        'username',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // AUTO-LOG WHEN USER IS CREATED
        static::created(function ($user) {
            $user->logActivity(
                'account_created',
                'User account was created',
                ['method' => 'registration']
            );
        });

        // AUTO-LOG WHEN USER IS UPDATED
        static::updated(function ($user) {
            $changes = $user->getChanges();
            $original = $user->getOriginal();
            
            // Don't log timestamps
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                $changedFields = [];
                foreach ($changes as $field => $newValue) {
                    $oldValue = $original[$field] ?? null;
                    
                    // Special handling for password (don't log the actual values)
                    if ($field === 'password') {
                        $changedFields[] = 'password (changed)';
                    } else {
                        $changedFields[] = $field;
                    }
                }
                
                $user->logActivity(
                    'profile_updated',
                    'Updated: ' . implode(', ', $changedFields),
                    ['changes' => array_keys($changes)]
                );
            }
        });

        // AUTO-LOG WHEN USER IS DELETED
        static::deleted(function ($user) {
            $user->logActivity(
                'account_deleted',
                'User account was deleted'
            );
        });

       /*
       static::restored(function ($user) {
            $user->logActivity(
                'account_restored',
                'User account was restored'
            );
        });
        */
        
    }

    // Relationships
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    // Get latest activity
    public function latestActivity()
    {
        return $this->activityLogs()->latest()->first();
    }

    // Check if user has active session
    public function hasActiveSession()
    {
        return $this->sessions()->where('is_active', true)->exists();
    }

    // Get current session
    public function currentSession()
    {
        return $this->sessions()
            ->where('session_id', session()->getId())
            ->where('is_active', true)
            ->first();
    }
}