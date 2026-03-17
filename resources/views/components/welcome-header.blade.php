<div class="bg-gradient-to-r from-[#155386] to-[#1F363D] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <!-- Profile Avatar - dynamically updates -->
                <div id="header-avatar-container" class="h-10 w-10 rounded-full overflow-hidden border-2 border-white/30 flex-shrink-0">
                    @auth
                        @php
                            $avatarPath = Auth::user()->avatar;
                            $fullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                            
                            if (!empty($avatarPath)) {
                                $avatarUrl = asset('storage/' . $avatarPath);
                            } else {
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&size=40&background=ffffff&color=155386&bold=true";
                            }
                        @endphp
                        
                        <img src="{{ $avatarUrl }}" 
                             alt="{{ $fullName }}" 
                             id="header-avatar-image"
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}') + '&size=40&background=ffffff&color=155386&bold=true';">
                    @else
                        <div class="h-full w-full bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endauth
                </div>
                
                <!-- Name and Role/Position - smaller text -->
                <div>
                    @auth
                        <h1 class="text-xl sm:text-2xl font-bold">Welcome, {{ Auth::user()->first_name }}!</h1>
                        
                        {{-- Show position for staff, otherwise show role --}}
                        @if(Auth::user()->isStaff() && Auth::user()->profile && Auth::user()->profile->position)
                            <p class="text-white-500 text-sm">
                                {{ Auth::user()->profile->position_display ?? ucfirst(str_replace('_', ' ', Auth::user()->profile->position)) }}
                            </p>
                        @else
                            <p class="text-white-500 text-sm capitalize">{{ Auth::user()->role }}</p>
                        @endif
                    @else
                        <h1 class="text-xl sm:text-2xl font-bold">Welcome, Guest!</h1>
                        <p class="text-white-500 text-sm">Visitor</p>
                    @endauth
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Date and Time Display - Now visible on all screens -->
                <div class="text-right">
                    <div class="text-base sm:text-lg font-semibold" id="current-time"></div>
                    <div class="text-xs sm:text-sm" id="current-date"></div>
                </div>
                
                <!-- Notification Component -->
                <x-notification-dropdown :notifications="getSampleNotifications()" />
            </div>
        </div>
    </div>
</div>

<script>
// Function to update header avatar
function updateHeaderAvatar() {
    @auth
    fetch('{{ route("profile.avatar.info") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.avatar_url) {
            const headerAvatar = document.getElementById('header-avatar-image');
            if (headerAvatar) {
                // Add timestamp to prevent caching
                headerAvatar.src = data.avatar_url + '?v=' + new Date().getTime();
            }
        }
    })
    .catch(error => console.error('Error updating avatar:', error));
    @endauth
}

// Listen for avatar updates from profile page
document.addEventListener('DOMContentLoaded', function() {
    // Check for avatar update every 3 seconds (polling)
    // This ensures the header updates when profile page changes avatar
    setInterval(updateHeaderAvatar, 3000);
    
    // Also listen for custom events (if you want to trigger update immediately after upload)
    window.addEventListener('avatarUpdated', function() {
        updateHeaderAvatar();
    });
    
    // Optional: Listen for storage events (if multiple tabs open)
    window.addEventListener('storage', function(e) {
        if (e.key === 'avatar_updated') {
            updateHeaderAvatar();
        }
    });
});

// Update date and time functions
function updateDateTime() {
    const now = new Date();
    
    // Philippines time (UTC+8)
    const options = {
        timeZone: 'Asia/Manila',
        hour12: true
    };
    
    // Format time with seconds
    const timeString = now.toLocaleTimeString('en-US', {
        ...options,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Format date
    const dateString = now.toLocaleDateString('en-US', {
        ...options,
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('current-time').textContent = timeString;
    document.getElementById('current-date').textContent = dateString;
}

// Update immediately
updateDateTime();

// Update every second for real-time seconds
setInterval(updateDateTime, 1000);
</script>

<style>
/* Optional: Add any additional styling if needed */
.text-white-500 {
    color: rgba(255, 255, 255, 0.7);
}

/* Custom scrollbar for notification dropdown */
.max-h-96::-webkit-scrollbar {
    width: 4px;
}

.max-h-96::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.max-h-96::-webkit-scrollbar-thumb {
    background: #155386;
    border-radius: 4px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #40798C;
}

/* For smaller screens, you can make the date/time more compact */
@media (max-width: 640px) {
    #current-time {
        font-size: 0.875rem;
    }
    #current-date {
        font-size: 0.75rem;
    }
}
</style>

<!-- Helper function to provide sample notifications -->
@php
function getSampleNotifications() {
    return [
        [
            'type' => 'application',
            'actor' => 'Building Official',
            'action' => 'updated your application status',
            'details' => 'Your building permit application is now under review',
            'time' => '5 minutes ago',
            'read' => false,
            'priority' => 'high'
        ],
        [
            'type' => 'success',
            'actor' => 'System',
            'action' => 'approved your application',
            'details' => 'Building permit APP-2025-001 has been approved',
            'time' => '2 hours ago',
            'read' => false,
            'priority' => 'high'
        ],
        [
            'type' => 'message',
            'actor' => 'Support Team',
            'action' => 'sent you a message',
            'details' => 'Regarding your document requirements',
            'time' => '1 day ago',
            'read' => true,
            'priority' => 'medium'
        ],
        [
            'type' => 'reminder',
            'actor' => 'System',
            'action' => 'reminder: Submit requirements',
            'details' => 'Please submit your structural plans within 3 days',
            'time' => '2 days ago',
            'read' => true,
            'priority' => 'medium'
        ],
        [
            'type' => 'application',
            'actor' => 'Reviewer',
            'action' => 'requested changes',
            'details' => 'Please update your architectural plans',
            'time' => '3 days ago',
            'read' => true,
            'priority' => 'low'
        ],
    ];
}
@endphp