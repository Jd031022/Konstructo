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

    <!-- Announcement Toast -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-4 right-4 max-w-sm w-full bg-white rounded-xl shadow-2xl border-l-4 z-[9999]"
         :class="{
             'border-blue-500': toastColor === 'blue',
             'border-green-500': toastColor === 'green',
             'border-yellow-500': toastColor === 'yellow',
             'border-red-500': toastColor === 'red',
             'border-purple-500': !['blue','green','yellow','red'].includes(toastColor)
         }">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                         :class="{
                             'bg-blue-100': toastColor === 'blue',
                             'bg-green-100': toastColor === 'green',
                             'bg-yellow-100': toastColor === 'yellow',
                             'bg-red-100': toastColor === 'red',
                             'bg-purple-100': !['blue','green','yellow','red'].includes(toastColor)
                         }">
                        <svg class="w-5 h-5" 
                             :class="{
                                 'text-blue-600': toastColor === 'blue',
                                 'text-green-600': toastColor === 'green',
                                 'text-yellow-600': toastColor === 'yellow',
                                 'text-red-600': toastColor === 'red',
                                 'text-purple-600': !['blue','green','yellow','red'].includes(toastColor)
                             }" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900" x-text="toastTitle"></p>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="toastMessage"></p>
                    <div class="mt-2 flex justify-end">
                        <button @click="showToast = false" class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                            Dismiss
                        </button>
                    </div>
                </div>
                <button @click="showToast = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

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
                     @click="handleNotificationClick(notification)">
                    <!-- Icon based on type -->
                    <div class="flex-shrink-0">
                        <!-- Staff View Icons -->
                        <div x-show="userRole === 'staff' || userRole === 'admin'">
                            <!-- New Application Submitted (Staff) -->
                            <div x-show="notification.type === 'application_submitted'" class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <!-- Staff Status Change Notification -->
                            <div x-show="notification.type === 'staff_status_change'" class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4" />
                                </svg>
                            </div>
                            <!-- New Announcement (Staff/Admin) -->
                            <div x-show="notification.type === 'new_announcement'" 
                                 :class="'w-9 h-9 rounded-full flex items-center justify-center ' + 
                                          (notification.announcement_color === 'blue' ? 'bg-blue-100' : 
                                           notification.announcement_color === 'green' ? 'bg-green-100' : 
                                           notification.announcement_color === 'yellow' ? 'bg-yellow-100' : 
                                           notification.announcement_color === 'red' ? 'bg-red-100' : 'bg-purple-100')">
                                <svg class="w-5 h-5" :class="notification.announcement_color === 'blue' ? 'text-blue-600' : 
                                                               notification.announcement_color === 'green' ? 'text-green-600' : 
                                                               notification.announcement_color === 'yellow' ? 'text-yellow-600' : 
                                                               notification.announcement_color === 'red' ? 'text-red-600' : 'text-purple-600'" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Applicant View Icons -->
                        <div x-show="userRole === 'applicant'">
                            <!-- Status Changed (Applicant) -->
                            <div x-show="notification.type === 'status_changed'" class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <!-- Admin Note (Applicant) -->
                            <div x-show="notification.type === 'admin_note'" class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <!-- Hard Copy Received (Applicant) -->
                            <div x-show="notification.type === 'hard_copy_received'" class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                            </div>
                            <!-- New Announcement (Applicant) -->
                            <div x-show="notification.type === 'new_announcement'" 
                                 :class="'w-9 h-9 rounded-full flex items-center justify-center ' + 
                                          (notification.announcement_color === 'blue' ? 'bg-blue-100' : 
                                           notification.announcement_color === 'green' ? 'bg-green-100' : 
                                           notification.announcement_color === 'yellow' ? 'bg-yellow-100' : 
                                           notification.announcement_color === 'red' ? 'bg-red-100' : 'bg-purple-100')">
                                <svg class="w-5 h-5" :class="notification.announcement_color === 'blue' ? 'text-blue-600' : 
                                                               notification.announcement_color === 'green' ? 'text-green-600' : 
                                                               notification.announcement_color === 'yellow' ? 'text-yellow-600' : 
                                                               notification.announcement_color === 'red' ? 'text-red-600' : 'text-purple-600'" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Fallback Icon -->
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
                <a :href="getNotificationsUrl()" class="text-sm text-[#155386] hover:underline font-medium">
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
        
        // Toast properties
        showToast: false,
        toastMessage: '',
        toastTitle: '',
        toastColor: 'blue',
        toastShownForIds: [], // Track which announcement toasts have been shown

        init() {
            console.log('Notification component initializing for role:', this.userRole);
            
            // Load toast history from localStorage
            const stored = localStorage.getItem('konstructo_toast_shown');
            if (stored) {
                try {
                    this.toastShownForIds = JSON.parse(stored);
                    console.log('Loaded toast history:', this.toastShownForIds);
                } catch (e) {
                    console.error('Failed to parse toast history:', e);
                    this.toastShownForIds = [];
                }
            } else {
                this.toastShownForIds = [];
                console.log('No toast history found');
            }

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
                console.log('Loading notifications...');
                const response = await fetch('/notifications');
                const data = await response.json();
                
                if (data.success) {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    console.log('Notifications loaded:', this.notifications.length);
                    
                    // Log all notifications for debugging
                    console.log('All notifications:', this.notifications.map(n => ({
                        id: n.id,
                        type: n.type,
                        title: n.title,
                        created_at: n.created_at
                    })));
                    
                    // Show toasts for any unread announcements from the last 24 hours
                    // Add a small delay to ensure component is ready
                    setTimeout(() => {
                        this.showRecentAnnouncementToasts();
                    }, 500);
                }
            } catch (error) {
                console.error('Failed to load notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        showRecentAnnouncementToasts() {
            console.log('Checking for recent announcement toasts...');
            console.log('Current time:', new Date().toISOString());
            
            const oneDayAgo = new Date();
            oneDayAgo.setDate(oneDayAgo.getDate() - 1);
            
            console.log('One day ago:', oneDayAgo.toISOString());
            console.log('Toast history:', this.toastShownForIds);
            
            // Log each notification for debugging (with safe date handling)
            this.notifications.forEach(notif => {
                if (notif.type === 'new_announcement') {
                    let dateStr = 'Invalid Date';
                    let isValid = false;
                    let notifDate = null;
                    
                    if (notif.created_at) {
                        notifDate = new Date(notif.created_at);
                        if (!isNaN(notifDate.getTime())) {
                            dateStr = notifDate.toISOString();
                            isValid = true;
                        }
                    }
                    
                    console.log(`Announcement ${notif.id}:`, {
                        date: dateStr,
                        isValid: isValid,
                        isRecent: isValid ? notifDate > oneDayAgo : false,
                        isUnread: !notif.read_at,
                        alreadyShown: this.toastShownForIds.includes(notif.id)
                    });
                }
            });
            
            // Find unread announcements from the last 24 hours
            const recentAnnouncements = this.notifications.filter(notif => {
                if (notif.type !== 'new_announcement') return false;
                if (notif.read_at) return false; // Already read
                if (this.toastShownForIds.includes(notif.id)) return false; // Already shown
                
                // Check if created_at exists and is valid
                if (!notif.created_at) return false;
                
                const notifDate = new Date(notif.created_at);
                if (isNaN(notifDate.getTime())) return false; // Invalid date
                
                const isRecent = notifDate > oneDayAgo;
                return isRecent;
            });
            
            console.log('Found recent announcements:', recentAnnouncements.length);
            
            if (recentAnnouncements.length === 0) {
                console.log('No recent announcements to show');
                
                // FOR TESTING: Force show the first announcement if any exist
                const firstAnnouncement = this.notifications.find(n => n.type === 'new_announcement');
                if (firstAnnouncement && !this.toastShownForIds.includes(firstAnnouncement.id)) {
                    console.log('FORCE SHOWING FIRST ANNOUNCEMENT FOR TESTING');
                    setTimeout(() => {
                        this.showAnnouncementToast(firstAnnouncement);
                        this.toastShownForIds.push(firstAnnouncement.id);
                        localStorage.setItem('konstructo_toast_shown', JSON.stringify(this.toastShownForIds));
                    }, 1000);
                }
                return;
            }
            
            // Show each announcement with a delay
            recentAnnouncements.forEach((notif, index) => {
                setTimeout(() => {
                    console.log('Showing toast for announcement:', notif.id);
                    this.showAnnouncementToast(notif);
                    this.toastShownForIds.push(notif.id);
                    localStorage.setItem('konstructo_toast_shown', JSON.stringify(this.toastShownForIds));
                }, index * 1000); // Show one per second
            });
        },

        async checkForNewNotifications() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();
                
                if (response.ok && data.count > this.unreadCount) {
                    console.log('New notifications detected:', data.count - this.unreadCount);
                    
                    // New notifications arrived
                    const newCount = data.count - this.unreadCount;
                    this.unreadCount = data.count;
                    
                    // Load new notifications to check for announcements
                    if (newCount > 0) {
                        const notifResponse = await fetch('/notifications?limit=' + newCount);
                        const notifData = await notifResponse.json();
                        
                        if (notifData.success) {
                            // Get only the new notifications (not already in our list)
                            const existingIds = this.notifications.map(n => n.id);
                            const newNotifs = notifData.notifications.filter(n => !existingIds.includes(n.id));
                            
                            console.log('New notifications:', newNotifs.length);
                            
                            newNotifs.forEach(notif => {
                                if (notif.type === 'new_announcement') {
                                    console.log('New announcement detected:', notif.id);
                                    this.showAnnouncementToast(notif);
                                    this.toastShownForIds.push(notif.id);
                                    localStorage.setItem('konstructo_toast_shown', JSON.stringify(this.toastShownForIds));
                                }
                            });
                            
                            // Update notifications list if dropdown is open
                            if (this.open) {
                                this.notifications = [...newNotifs, ...this.notifications];
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Failed to check notifications:', error);
            }
        },

        showAnnouncementToast(notification) {
            console.log('showAnnouncementToast called with:', notification);
            
            this.toastTitle = notification.title || 'New Announcement';
            this.toastMessage = notification.message || '';
            this.toastColor = notification.announcement_color || 'blue';
            this.showToast = true;
            
            console.log('Toast set to show:', {
                title: this.toastTitle,
                message: this.toastMessage,
                color: this.toastColor,
                showToast: this.showToast
            });
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                console.log('Auto-hiding toast');
                this.showToast = false;
            }, 5000);
        },

        // Handle notification click with proper redirect based on type and role
        async handleNotificationClick(notification) {
            console.log('Clicked notification:', notification);
            
            // Determine the redirect URL based on notification type and user role
            let redirectUrl = null;
            
            if (this.userRole === 'applicant') {
                // Applicant notifications
                if (notification.type === 'status_changed' || 
                    notification.type === 'admin_note' || 
                    notification.type === 'hard_copy_received' ||
                    notification.type === 'hard_copy_request') {
                    
                    if (notification.application_id) {
                        redirectUrl = `/applicant/application-details/${notification.application_id}`;
                    }
                } else if (notification.type === 'new_announcement') {
                    redirectUrl = '/applicant/dashboard';
                }
            } 
            else if (this.userRole === 'staff' || this.userRole === 'admin') {
                // Staff/Admin notifications
                if (notification.type === 'application_submitted') {
                    redirectUrl = '/staff/applications';
                } 
                else if (notification.type === 'staff_status_change') {
                    if (notification.application_id) {
                        redirectUrl = `/staff/application-details/${notification.application_id}`;
                    }
                } else if (notification.type === 'new_announcement') {
                    redirectUrl = '/staff/dashboard';
                }
            }
            
            // If no specific redirect URL, try using notification.link
            if (!redirectUrl && notification.link) {
                redirectUrl = notification.link;
            }
            
            // Mark as read and redirect
            await this.markAsRead(notification.id, redirectUrl);
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
                    
                    // Also clear from localStorage since they're now read
                    this.toastShownForIds = [];
                    localStorage.removeItem('konstructo_toast_shown');
                }
            } catch (error) {
                console.error('Failed to mark all as read:', error);
            }
        },

        refreshNotifications() {
            this.loadNotifications();
        },
        
        // Get the appropriate "View all notifications" URL based on user role
        getNotificationsUrl() {
            if (this.userRole === 'applicant') {
                return '/applicant/notifications';
            } else if (this.userRole === 'staff' || this.userRole === 'admin') {
                return '/staff/notifications';
            }
            return '/notifications';
        }
    }
}
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
<style>
/* Toast positioning and z-index */
.fixed.bottom-4.right-4 {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    z-index: 9999 !important;
}

/* Animation for toast */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-slide-in {
    animation: slideIn 0.3s ease-out;
}

/* Line clamp for long messages */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Ensure toast is above everything */
.z-\[9999\] {
    z-index: 9999;
}
</style>