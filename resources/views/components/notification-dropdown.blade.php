@props(['user' => null])

<!-- Notification Dropdown Component -->
<div class="relative" x-data="notificationComponent()" x-init="init()" @click.away="open = false">
    <!-- Notification Bell Button -->
    <button @click="toggleDropdown()" class="relative h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all duration-200 focus:outline-none">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Notification Badge -->
        <template x-if="unreadCount > 0">
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center shadow-sm animate-pulse">
                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </span>
        </template>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-[380px] sm:w-[420px] md:w-[480px] bg-white rounded-xl shadow-lg border border-gray-100 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
            <div class="flex items-center gap-2">
                <template x-if="notifications.length > 0">
                    <button @click="markAllAsRead()" class="text-xs text-gray-500 hover:text-[#155386] transition">
                        Mark all read
                    </button>
                </template>
                <button @click="refreshNotifications()" class="text-gray-400 hover:text-[#155386] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notification List -->
        <div class="max-h-[450px] overflow-y-auto" x-show="!loading">
            <template x-if="notifications.length === 0">
                <!-- Empty State -->
                <div class="px-5 py-10 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">No notifications yet</p>
                    <p class="text-xs text-gray-400 mt-1">We'll notify you when something arrives</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="flex gap-4 px-5 py-4 hover:bg-gray-50 transition cursor-pointer" 
                     :class="{ 'bg-blue-50/20': !notification.read }"
                     @click="markAsRead(notification.id, notification.link)">
                    <!-- Icon based on type -->
                    <div class="flex-shrink-0">
                        <div x-show="notification.type === 'application_submitted'" class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'status_changed'" class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'admin_note'" class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div x-show="notification.type === 'hard_copy_request'" class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div x-show="!notification.type || notification.type === 'info'" class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">
                            <span class="font-medium" x-text="notification.title"></span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                        <p x-show="notification.details" class="text-xs text-gray-500 mt-1" x-text="notification.details"></p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-xs text-gray-400" x-text="notification.time"></span>
                            <span x-show="!notification.read_at" class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="px-5 py-10 text-center">
            <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-2">Loading notifications...</p>
        </div>

        <!-- Footer -->
        <template x-if="notifications.length > 0">
            <div class="border-t border-gray-100 px-5 py-2.5 text-center">
                <a :href="`/${userRole}/notifications`" class="text-sm text-[#155386] hover:underline font-medium">
                    View all notifications
                </a>
            </div>
        </template>
    </div>
</div>

<script>
function notificationComponent() {
    return {
        open: false,
        loading: true,
        notifications: [],
        unreadCount: 0,
        userRole: '{{ auth()->user()->role ?? "guest" }}',
        pollInterval: null,

        init() {
            this.loadNotifications();
            // Poll for new notifications every 15 seconds
            this.pollInterval = setInterval(() => {
                this.checkForNewNotifications();
            }, 15000);
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
        },

        toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                this.loadNotifications();
            }
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications');
                const data = await response.json();
                
                if (data.success) {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async checkForNewNotifications() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();
                
                if (response.ok && data.count > this.unreadCount) {
                    // New notifications arrived
                    this.unreadCount = data.count;
                    if (this.open) {
                        this.loadNotifications();
                    }
                }
            } catch (error) {
                console.error('Failed to check notifications:', error);
            }
        },

        async markAsRead(notificationId, link = null) {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                }

                // Redirect if link exists
                if (link) {
                    window.location.href = link;
                }
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        refreshNotifications() {
            this.loadNotifications();
        }
    }
}
</script>