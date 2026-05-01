<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

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
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property string $role
 * @property \Carbon\Carbon|null $email_verified_at
 * @property string $approval_status
 * @property string|null $rejection_reason
 * @property \Carbon\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\UserProfile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|ActivityLog[] $activityLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|LoginAttempt[] $loginAttempts
 * @property-read \Illuminate\Database\Eloquent\Collection|UserSession[] $sessions
 * @property-read ActivityLog|null $latestActivity
 * @property-read \Illuminate\Database\Eloquent\Collection|ApplicationDocument[] $applicationDocuments
 * @property-read \Illuminate\Database\Eloquent\Collection|Application[] $applications
 * @property-read \Illuminate\Database\Eloquent\Collection|ApplicationDocument[] $assignedDocuments
 * @property-read User|null $approver
 * 
 * @method bool isAdmin()
 * @method bool isStaff()
 * @method bool isApplicant()
 * @method bool hasRole(string $role)
 * @method bool isApproved()
 * @method bool isPending()
 * @method bool isRejected()
 * @method bool canLogin()
 * @method string getFullNameAttribute()
 * @method string getInitialsAttribute()
 * @method string getRoleBadgeColorAttribute()
 * @method string getStatusBadgeColorAttribute()
 * @method string getApprovalStatusBadgeAttribute()
 * @method string getAvatarUrlAttribute()
 */
class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable, LogActivity; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'avatar',
        'remember_token',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'full_name',
        'initials',
        'avatar_url',
        'approval_status_badge',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }
// Add to User model
public function paymentProofs()
{
    return $this->hasMany(PaymentProof::class, 'user_id');
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
     * Get all applications for the user (from applications table)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Get all application documents for the user (from application_documents table)
     */
    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'user_id');
    }

    /**
     * Get documents assigned to the user (as staff/engineer)
     */
    public function assignedDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'assigned_to');
    }

    /**
     * Get the user who approved/rejected this user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ========== ROLE CHECK METHODS ==========

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

    // ========== APPROVAL STATUS METHODS ==========

    /**
     * Check if user account is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if user account is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if user account is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Check if user can login (email verified AND approved for applicants, always true for admin/staff)
     */
    public function canLogin(): bool
    {
        // Admin and staff can always login if email is verified
        if ($this->isAdmin() || $this->isStaff()) {
            return !is_null($this->email_verified_at);
        }
        
        // Applicants need email verified AND approval status approved
        return !is_null($this->email_verified_at) && $this->isApproved();
    }

    /**
     * Get approval status badge HTML
     */
    public function getApprovalStatusBadgeAttribute(): string
    {
        $badges = [
            'approved' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>',
            'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>',
            'rejected' => '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Rejected</span>',
        ];
        
        return $badges[$this->approval_status] ?? $badges['pending'];
    }

    /**
     * Approve user account
     */
    public function approve(int $adminId): bool
    {
        return $this->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject user account
     */
    public function reject(int $adminId, ?string $reason = null): bool
    {
        return $this->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Get pending applicants
     */
    public static function getPendingApplicants()
    {
        return self::where('role', 'applicant')
                   ->where('approval_status', 'pending')
                   ->orderBy('created_at', 'asc')
                   ->get();
    }

    // ========== POSITION METHODS ==========

    /**
     * Get the user's position (if staff).
     */
    public function getPositionAttribute(): ?string
    {
        return $this->role ?? $this->position ?? 'Staff';
    }

    /**
     * Get the user's position display name.
     */
    public function getPositionDisplayAttribute(): ?string
    {
        return $this->profile?->position_display;
    }

    /**
     * Get the user's position badge color.
     */
    public function getPositionColorAttribute(): ?string
    {
        return $this->profile?->position_color;
    }

    /**
     * Get the user's position badge HTML.
     */
    public function getPositionBadgeAttribute(): ?string
    {
        return $this->profile?->position_badge;
    }

    /**
     * Check if user has position set (for staff).
     */
    public function hasPosition(): bool
    {
        return $this->isStaff() && $this->profile && !is_null($this->profile->position);
    }

    /**
     * Check if user needs to set position.
     */
    public function needsPosition(): bool
    {
        return $this->isStaff() && (!$this->profile || is_null($this->profile->position));
    }

    /**
     * Check if user is engineer.
     */
    public function isEngineer(): bool
    {
        return $this->isStaff() && $this->profile?->isEngineer();
    }

    /**
     * Check if user is architect.
     */
    public function isArchitect(): bool
    {
        return $this->isStaff() && $this->profile?->isArchitect();
    }

    /**
     * Check if user is BFP.
     */
    public function isBFP(): bool
    {
        return $this->isStaff() && $this->profile?->isBFP();
    }

    /**
     * Check if user is administrative aide.
     */
    public function isAdministrativeAide(): bool
    {
        return $this->isStaff() && $this->profile?->isAdministrativeAide();
    }

    /**
     * Check if user has technical position (Engineer or Architect).
     */
    public function isTechnical(): bool
    {
        return $this->isStaff() && $this->profile?->isTechnical();
    }

    /**
     * Update user's position.
     */
    public function updatePosition(string $position): bool
    {
        if (!$this->isStaff()) {
            return false;
        }

        $this->ensureProfileExists();
        
        return $this->profile()->update(['position' => $position]);
    }

    // ========== APPLICATION DOCUMENT QUERY METHODS ==========

    /**
     * Get draft documents (for applicant)
     */
    public function draftDocuments()
    {
        return $this->applicationDocuments()->where('status', 'draft');
    }

    /**
     * Get pending documents (for applicant)
     */
    public function pendingDocuments()
    {
        return $this->applicationDocuments()->where('status', 'pending');
    }

    /**
     * Get verified documents (for applicant)
     */
    public function verifiedDocuments()
    {
        return $this->applicationDocuments()->where('status', 'verified');
    }

    /**
     * Get approved documents (for applicant)
     */
    public function approvedDocuments()
    {
        return $this->applicationDocuments()->where('status', 'approved');
    }

    /**
     * Get rejected documents (for applicant)
     */
    public function rejectedDocuments()
    {
        return $this->applicationDocuments()->where('status', 'rejected');
    }

    /**
     * Get pending reviews (for staff)
     */
    public function pendingReviews()
    {
        return $this->assignedDocuments()->where('status', 'pending');
    }

    /**
     * Get completed reviews (for staff)
     */
    public function completedReviews()
    {
        return $this->assignedDocuments()->whereIn('status', ['approved', 'rejected', 'verified']);
    }

    // ========== SESSION METHODS ==========

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

    // ========== ATTRIBUTE ACCESSORS ==========

    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'purple',
            'staff' => 'blue',
            'applicant' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get status badge color (email verification status)
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
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar) . '?v=' . time();
        }
        
        // Return a default avatar URL with user's name
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . 
               '&size=96&background=155386&color=fff&bold=true';
    }

    /**
     * Get last login time from profile
     */
    public function getLastLoginAtAttribute()
    {
        return $this->profile?->last_login_at;
    }

    /**
     * Get password changed at from profile
     */
    public function getPasswordChangedAtAttribute()
    {
        return $this->profile?->password_changed_at;
    }

    /**
     * Check if two-factor authentication is enabled from profile
     */
    public function getTwoFactorEnabledAttribute(): bool
    {
        return $this->profile?->two_factor_enabled ?? false;
    }

    /**
     * Get two-factor secret from profile
     */
    public function getTwoFactorSecretAttribute()
    {
        return $this->profile?->two_factor_secret;
    }

    // ========== PROFILE HELPER METHODS ==========

    /**
     * Ensure profile exists
     */
    public function ensureProfileExists(): void
    {
        if (!$this->profile) {
            $this->profile()->create();
        }
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->ensureProfileExists();
        $this->profile()->update(['last_login_at' => now()]);
    }

    /**
     * Update password changed timestamp
     */
    public function updatePasswordChanged(): void
    {
        $this->ensureProfileExists();
        $this->profile()->update(['password_changed_at' => now()]);
    }

    // ========== NOTIFICATION METHODS ==========

    /**
     * Get role-based notifications
     */
    public function getRoleBasedNotificationsAttribute()
    {
        return $this->notifications()
            ->where('data->for_role', $this->role)
            ->orWhere('data->for_role', 'all')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'icon' => $data['icon'] ?? 'info',
                    'actor' => $this->getNotificationActor($data),
                    'action' => $data['action'] ?? 'notification',
                    'details' => $data['details'] ?? $data['message'] ?? null,
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => !is_null($notification->read_at),
                    'application_id' => $data['application_id'] ?? null,
                    'metadata' => $data
                ];
            });
    }

    /**
     * Get unread count for role-specific notifications
     */
    public function getRoleBasedUnreadCountAttribute()
    {
        return $this->notifications()
            ->whereNull('read_at')
            ->where(function($query) {
                $query->where('data->for_role', $this->role)
                      ->orWhere('data->for_role', 'all');
            })
            ->count();
    }

    /**
     * Determine notification actor based on data
     */
    private function getNotificationActor($data)
    {
        if (isset($data['staff_name'])) {
            return $data['staff_name'];
        }
        
        if (isset($data['applicant_name'])) {
            return $data['applicant_name'];
        }
        
        if (isset($data['actor_name'])) {
            return $data['actor_name'];
        }
        
        return 'System';
    }

    // ========== SCOPES ==========

    /**
     * Scope a query to only include pending applicants.
     */
    public function scopePendingApplicants($query)
    {
        return $query->where('role', 'applicant')
                    ->where('approval_status', 'pending');
    }

    /**
     * Scope a query to only include approved applicants.
     */
    public function scopeApprovedApplicants($query)
    {
        return $query->where('role', 'applicant')
                    ->where('approval_status', 'approved');
    }

    /**
     * Scope a query to only include rejected applicants.
     */
    public function scopeRejectedApplicants($query)
    {
        return $query->where('role', 'applicant')
                    ->where('approval_status', 'rejected');
    }

    /**
     * Scope a query to only include users who can login.
     */
    public function scopeCanLogin($query)
    {
        return $query->where(function($q) {
            $q->where(function($sub) {
                $sub->whereIn('role', ['admin', 'staff'])
                    ->whereNotNull('email_verified_at');
            })->orWhere(function($sub) {
                $sub->where('role', 'applicant')
                    ->whereNotNull('email_verified_at')
                    ->where('approval_status', 'approved');
            });
        });
    }

    /**
     * Scope a query to only include staff who need position.
     */
    public function scopeStaffNeedsPosition($query)
    {
        return $query->where('role', 'staff')
                    ->whereHas('profile', function ($q) {
                        $q->whereNull('position')
                          ->orWhere('position', '');
                    })
                    ->orWhereDoesntHave('profile');
    }

    /**
     * Scope a query to only include staff with specific position.
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('role', 'staff')
                    ->whereHas('profile', function ($q) use ($position) {
                        $q->where('position', $position);
                    });
    }

    /**
     * Scope a query to only include staff with technical positions.
     */
    public function scopeTechnicalStaff($query)
    {
        return $query->where('role', 'staff')
                    ->whereHas('profile', function ($q) {
                        $q->whereIn('position', ['engineer', 'architect']);
                    });
    }

    /**
     * Scope a query to only include staff with position set.
     */
    public function scopeHasPosition($query)
    {
        return $query->where('role', 'staff')
                    ->whereHas('profile', function ($q) {
                        $q->whereNotNull('position')
                          ->where('position', '!=', '');
                    });
    }

    // ========== BOOT METHOD ==========

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Create profile for new user
        static::created(function (User $user) {
            $user->profile()->create();
            
            // AUTO-LOG WHEN USER IS CREATED
            if (!isset($user->logging)) {
                $user->logging = true;
                $user->logActivity(
                    'account_created',
                    'User account was created',
                    ['method' => 'registration', 'role' => $user->role, 'approval_status' => $user->approval_status]
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

        // AUTO-LOG WHEN USER IS DELETED (profile will auto-delete due to cascade)
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
 * Get conversations for the user.
 */
public function conversations()
{
    return $this->belongsToMany(Conversation::class, 'conversation_participants')
                ->withPivot('last_read_at')
                ->withTimestamps();
}

/**
 * Get messages sent by the user.
 */
public function messages()
{
    return $this->hasMany(Message::class);
}

/**
 * Get the user's full name for chat display.
 */
public function getChatNameAttribute(): string
{
    return $this->full_name; // This uses your existing full_name accessor
}

/**
 * Get the user's avatar initial for chat.
 */
public function getChatAvatarAttribute(): string
{
    return $this->initials; // This uses your existing initials accessor
}
}