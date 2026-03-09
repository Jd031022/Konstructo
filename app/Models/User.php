<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class User
 * 
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string|null $suffix
 * @property string $phone_number
 * @property string $email
 * @property string $zip_code
 * @property string $address
 * @property string $username
 * @property string $password
 * @property string|null $remember_token
 * @property string $role
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|ActivityLog[] $activityLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|LoginAttempt[] $loginAttempts
 * @property-read \Illuminate\Database\Eloquent\Collection|UserSession[] $sessions
 * @property-read ActivityLog|null $latestActivity
 * 
 * @method bool isAdmin()
 * @method bool isEngineer()
 * @method bool isApplicant()
 * @method bool hasRole(string $role)
 * @method string getFullNameAttribute()
 * @method string getInitialsAttribute()
 * @method string getRoleBadgeColorAttribute()
 * @method string getStatusBadgeColorAttribute()
 */
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
        'role',
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
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is engineer
     */
    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    /**
     * Check if user is applicant
     */
    public function isApplicant(): bool
    {
        return $this->role === 'applicant';
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'purple',
            'engineer' => 'blue',
            'applicant' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return $this->email_verified_at ? 'green' : 'yellow';
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        $nameParts = [];
        
        if ($this->first_name) {
            $nameParts[] = $this->first_name;
        }
        
        if ($this->middle_name) {
            $nameParts[] = $this->middle_name;
        }
        
        if ($this->last_name) {
            $nameParts[] = $this->last_name;
        }
        
        $fullName = implode(' ', $nameParts);
        
        if ($this->suffix) {
            $fullName .= ', ' . $this->suffix;
        }
        
        return $fullName ?: $this->email;
    }

    /**
     * Get user initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $initials = '';
        
        if ($this->first_name) {
            $initials .= strtoupper(substr($this->first_name, 0, 1));
        }
        
        if ($this->last_name) {
            $initials .= strtoupper(substr($this->last_name, 0, 1));
        }
        
        return $initials ?: 'U';
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * Get the login attempts for the user.
     */
    public function loginAttempts(): HasMany
    {
        return $this->hasMany(LoginAttempt::class, 'user_id');
    }

    /**
     * Get the sessions for the user.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    /**
     * Get the user's latest activity.
     */
    public function latestActivity(): HasOne
    {
        return $this->hasOne(ActivityLog::class, 'user_id')->latest();
    }

    /**
     * Check if user has active session
     */
    public function hasActiveSession(): bool
    {
        return $this->sessions()->where('is_active', true)->exists();
    }

    /**
     * Get current session
     */
    public function currentSession(): ?UserSession
    {
        return $this->sessions()
            ->where('session_id', session()->getId())
            ->where('is_active', true)
            ->first();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // AUTO-LOG WHEN USER IS CREATED
        static::created(function (User $user) {
            // Prevent recursive logging
            if (!isset($user->logging)) {
                $user->logging = true;
                $user->logActivity(
                    'account_created',
                    'User account was created',
                    ['method' => 'registration', 'role' => $user->role]
                );
                unset($user->logging);
            }
        });

        // AUTO-LOG WHEN USER IS UPDATED
        static::updated(function (User $user) {
            if (!isset($user->logging)) {
                $changes = $user->getChanges();
                
                unset($changes['updated_at']);
                
                if (!empty($changes)) {
                    $user->logging = true;
                    $changedFields = [];
                    foreach (array_keys($changes) as $field) {
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
                    unset($user->logging);
                }
            }
        });

        // AUTO-LOG WHEN USER IS DELETED
        static::deleted(function (User $user) {
            if (!isset($user->logging)) {
                $user->logging = true;
                $user->logActivity(
                    'account_deleted',
                    'User account was deleted'
                );
                unset($user->logging);
            }
        });
    }
}