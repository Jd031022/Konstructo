<div class="bg-gradient-to-r from-[#155386] to-[#1F363D] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <!-- Profile Avatar - slightly smaller -->
                <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <!-- Name and Role - smaller text -->
                <div>
                    @auth
                        <h1 class="text-xl sm:text-2xl font-bold">Welcome, {{ Auth::user()->first_name ?? Auth::user()->name }}!</h1>
                        <p class="text-white-500 text-sm">{{ $role ?? 'User' }}</p>
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
    
    // For mobile (optional - shorter format if you want)
    const mobileTimeString = now.toLocaleTimeString('en-US', {
        ...options,
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const mobileDateString = now.toLocaleDateString('en-US', {
        ...options,
        month: 'short',
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