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
    'telephone',
    'email',
    'alternative_email',
    'zip_code',
    'address',
    'house_number',
    'street',
    'barangay',
    'city',
    'province',
    'username',
    'password',
    'email_verified_at',
    'role',
    'date_of_birth',
    'place_of_birth',
    'gender',
    'civil_status',
    'citizenship',
    'tin',
    'last_login_at',
    'password_changed_at',
    'two_factor_secret',
    'two_factor_enabled',
    'avatar', 
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
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
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
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
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
            'engineer', 'staff' => 'blue',
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

    // ========== APPLICATION RELATIONSHIPS ==========

    /**
     * Get all applications for the user (as applicant)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Get applications assigned to the user (as staff/engineer)
     */
    public function assignedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'assigned_to');
    }

    /**
     * Get pending applications (for applicant)
     */
    public function pendingApplications()
    {
        return $this->applications()->where('status', 'pending');
    }

    /**
     * Get approved applications (for applicant)
     */
    public function approvedApplications()
    {
        return $this->applications()->where('status', 'approved');
    }

    /**
     * Get rejected applications (for applicant)
     */
    public function rejectedApplications()
    {
        return $this->applications()->where('status', 'rejected');
    }

    /**
     * Get pending reviews (for staff)
     */
    public function pendingReviews()
    {
        return $this->assignedApplications()->where('status', 'pending');
    }

    /**
     * Get completed reviews (for staff)
     */
    public function completedReviews()
    {
        return $this->assignedApplications()->whereIn('status', ['approved', 'rejected']);
    }

    // ========== ADDITIONAL FIELDS ==========

    /**
     * Get last login time
     */
    public function getLastLoginAtAttribute()
    {
        return $this->sessions()->latest()->first()?->last_activity;
    }

    /**
     * Check if two-factor authentication is enabled
     */
    public function getTwoFactorEnabledAttribute(): bool
    {
        return !is_null($this->two_factor_secret);
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

    /**
 * Get the avatar URL.
 */
public function getAvatarUrlAttribute(): string
{
    if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
        return asset('storage/' . $this->avatar);
    }
    
    // Return a default avatar URL or generate initials avatar
    return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
}
}