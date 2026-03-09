@props(['notifications' => []])

<!-- Notification Dropdown Component - Better Width -->
<div class="relative" x-data="{ open: false }">
    <!-- Notification Bell Button -->
    <button @click="open = !open" class="relative h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all duration-200 focus:outline-none">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Notification Badge -->
        @if(count($notifications) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center shadow-sm">
                {{ count($notifications) > 9 ? '9+' : count($notifications) }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu - Increased Width -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-[380px] sm:w-[420px] md:w-[480px] bg-white rounded-xl shadow-lg border border-gray-100 z-50"
         style="display: none;">
        
        <!-- Simple Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
            @if(count($notifications) > 0)
                <button class="text-xs text-gray-500 hover:text-[#155386] transition">
                    Mark all read
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-[450px] overflow-y-auto">
            @if(count($notifications) > 0)
                @foreach($notifications as $notification)
                    <div class="flex gap-4 px-5 py-4 hover:bg-gray-50 transition {{ !$notification['read'] ? 'bg-blue-50/20' : '' }} border-b border-gray-50 last:border-b-0">
                        <!-- Simple Icon -->
                        <div class="flex-shrink-0">
                            @if($notification['type'] === 'application')
                                <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'success')
                                <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'message')
                                <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                            @elseif($notification['type'] === 'reminder')
                                <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Simple Content - More room now -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                <span class="font-medium">{{ $notification['actor'] ?? 'System' }}</span>
                                <span class="text-gray-600"> {{ $notification['action'] }}</span>
                            </p>
                            @if(!empty($notification['details']))
                                <p class="text-sm text-gray-500 mt-1">{{ $notification['details'] }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs text-gray-400">{{ $notification['time'] }}</span>
                                @if(!$notification['read'])
                                    <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Simple Empty State - Wider -->
                <div class="px-5 py-10 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">No notifications yet</p>
                    <p class="text-xs text-gray-400 mt-1">We'll notify you when something arrives</p>
                </div>
            @endif
        </div>

        <!-- Simple Footer -->
        @if(count($notifications) > 0)
            <div class="border-t border-gray-100 px-5 py-2.5 text-center">
                <a href="/notifications" class="text-sm text-[#155386] hover:underline font-medium">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    // Alpine.js check
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js is required for the notification dropdown.');
    }
</script>

<style>
/* Simple scrollbar */
.max-h-\[450px\]::-webkit-scrollbar {
    width: 4px;
}

.max-h-\[450px\]::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.max-h-\[450px\]::-webkit-scrollbar-thumb {
    background: #155386;
    border-radius: 4px;
}

.max-h-\[450px\]::-webkit-scrollbar-thumb:hover {
    background: #40798C;
}
</style>