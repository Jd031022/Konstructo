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
                    <h1 class="text-xl sm:text-2xl font-bold">Welcome, {{ $name }}!</h1>
                    <p class="text-white-500 text-sm">{{ $role }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="text-base sm:text-lg font-semibold" id="current-time"></div>
                    <div class="text-xs sm:text-sm" id="current-date"></div>
                </div>
                <div class="h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
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
    
    document.getElementById('current-time').textContent = timeString;
    document.getElementById('current-date').textContent = dateString;
}

// Update immediately
updateDateTime();

// Update every second for real-time seconds
setInterval(updateDateTime, 1000);
</script>