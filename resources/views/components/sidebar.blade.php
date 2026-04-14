<aside id="sidebar" class="md:flex w-20 bg-white min-h-screen h-full shadow-md fixed top-0 left-0 flex flex-col items-start py-6 overflow-y-auto transition-all duration-300 rounded-tr-2xl rounded-br-2xl scrollbar-hide z-40 -translate-x-full md:translate-x-0 hidden md:flex sidebar-hover">

    <!-- Close button for mobile -->
    <div id="mobile-close" class="md:hidden absolute top-4 right-4 cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </div>

    <!-- Logo with Konstructo Name (hover to expand) -->
    <div class="mb-10 px-6 w-full flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#155386] min-w-8" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span class="text-lg font-bold whitespace-nowrap sidebar-text transition-opacity duration-300">
            <span class="text-[#155386]">Konstr</span><span class="text-[rgb(64,121,140)]">ucto</span>
        </span>
    </div>

    <!-- Navigation - Role-based routes -->
    <nav class="flex flex-col items-start gap-6 flex-1 w-full px-4">
        
        <!-- Home/Dashboard - Common for all roles -->
        <a href="/{{ auth()->user()->role }}/dashboard" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('*/dashboard') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Dashboard</span>
        </a>

        <!-- ROLE-SPECIFIC ROUTES -->
        
        <!-- ADMIN ROUTES -->
        @if(auth()->user()->role === 'admin')
            <!-- User Management -->
            <a href="/admin/users" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('admin/users*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">User Management</span>
            </a>

            <!-- All Applications -->
            <a href="/admin/applications" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('admin/applications*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">All Applications</span>
            </a>

            <!-- Archive -->
            <a href="/admin/archived-applications" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('admin/archived-applications*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Archive</span>
            </a>

            <!-- System Settings (Admin only) -->
            <a href="/admin/settings" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('admin/settings*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">System Settings</span>
            </a>

        <!-- STAFF ROUTES -->
        @elseif(auth()->user()->role === 'staff')
            <!-- Applications -->
            <a href="/staff/applications" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('staff/applications*') && !request()->is('staff/archived-applications*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Applications</span>
            </a>

            <!-- Archive -->
            <a href="/staff/archived-applications" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('staff/archived-applications*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Archive</span>
            </a>

        <!-- APPLICANT ROUTES -->
        @elseif(auth()->user()->role === 'applicant')
            <!-- My Applications -->
            <a href="/applicant/applications" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('applicant/applications*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Applications</span>
            </a>
        @endif

        <!-- DIVIDER - Visible to all roles -->
        <div class="w-full border-t border-gray-200 my-2"></div>

        <!-- CHAT - Universal link for ALL ROLES -->
        <a href="{{ route('chat') }}" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('chat') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition relative">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Messages</span>
        </a>

        <!-- PROFILE - Universal link for ALL ROLES -->
        <a href="/profile" class="w-full flex items-center gap-4 p-2 rounded-xl {{ request()->is('profile*') ? 'bg-[#155386] text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Profile</span>
        </a>

    </nav>

    <!-- Logout -->
    <div class="mt-auto pt-6 w-full px-4">
        <button onclick="showLogoutModal()" 
           class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="text-sm font-medium whitespace-nowrap sidebar-text transition-opacity duration-300">Logout</span>
        </button>
    </div>

</aside>

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>


<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Logout Confirmation</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to log out of your account?
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="logout()" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                        Yes, Logout
                    </button>
                    <button onclick="closeLogoutModal()" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<!-- JavaScript for toggle functionality and logout modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileClose = document.getElementById('mobile-close');
        const mobileOverlay = document.getElementById('mobile-overlay');
        let isMobileOpen = false;

        // Check if we're on mobile
        function isMobile() {
            return window.innerWidth < 768; // md breakpoint
        }

        // Mobile menu toggle
        function toggleMobileSidebar(e) {
            if (e) e.stopPropagation(); // Prevent event bubbling
            if (!isMobile()) return;

            if (isMobileOpen) {
                // Close mobile sidebar
                sidebar.classList.remove('translate-x-0', 'flex', 'mobile-open');
                sidebar.classList.add('hidden', '-translate-x-full');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
                isMobileOpen = false;
            } else {
                // Open mobile sidebar
                sidebar.classList.remove('hidden', '-translate-x-full');
                sidebar.classList.add('flex', 'translate-x-0', 'mobile-open');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                isMobileOpen = true;
            }
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
        }
        
        if (mobileClose) {
            mobileClose.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMobileSidebar();
            });
        }
        
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMobileSidebar();
            });
        }

        // Click outside to close mobile sidebar
        document.addEventListener('click', function(e) {
            if (isMobile() && isMobileOpen && sidebar && mobileMenuBtn) {
                if (!sidebar.contains(e.target) && e.target !== mobileMenuBtn) {
                    toggleMobileSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const nowMobile = isMobile();

            if (nowMobile) {
                // Switching to mobile
                if (!isMobileOpen && sidebar) {
                    sidebar.classList.add('hidden', '-translate-x-full');
                    sidebar.classList.remove('flex', 'translate-x-0');
                }
            } else {
                // Switching to desktop
                if (sidebar) {
                    sidebar.classList.add('flex', 'w-20');
                    sidebar.classList.remove('hidden', '-translate-x-full', 'translate-x-0');
                }
                // Close mobile overlay if open
                if (isMobileOpen && mobileOverlay) {
                    mobileOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    isMobileOpen = false;
                }
            }
        });

        // Initialize on load
        if (isMobile() && sidebar) {
            // Ensure mobile state is correct
            sidebar.classList.add('hidden', '-translate-x-full');
            sidebar.classList.remove('flex', 'translate-x-0');
        } else if (sidebar) {
            // Ensure desktop state is correct
            sidebar.classList.add('flex', 'w-20');
            sidebar.classList.remove('hidden');
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
                if (isMobile() && isMobileOpen) {
                    toggleMobileSidebar();
                }
            }
        });

        // Close modal when clicking outside
        const modal = document.getElementById('logout-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeLogoutModal();
                }
            });
        }
    });

    // Logout modal functions
    function showLogoutModal() {
        const modal = document.getElementById('logout-modal');
        if (modal) {
            modal.classList.remove('hidden');
            // Prevent scrolling on body when modal is open
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logout-modal');
        if (modal) {
            modal.classList.add('hidden');
            // Re-enable scrolling
            document.body.style.overflow = 'auto';
        }
    }

    function logout() {
        const modal = document.getElementById('logout-modal');
        const modalContent = modal ? modal.querySelector('.bg-white') : null;
        
        if (modalContent) {
            // Show loading state on button
            modalContent.innerHTML = `
                <div class="text-center p-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Logging out...</h3>
                    <p class="text-sm text-gray-600">Please wait while we securely log you out.</p>
                </div>
            `;
        }
        
        // Submit the logout form after a brief delay (for UX)
        setTimeout(() => {
            const form = document.getElementById('logout-form');
            if (form) {
                form.submit();
            }
        }, 500);
    }
</script>

<style>
    /* Smooth transitions for width change */
    #sidebar {
        transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    #sidebar span {
        transition: opacity 0.3s ease-in-out;
    }

    /* Mobile sidebar positioning */
    @media (max-width: 767px) {
        #sidebar {
            z-index: 60; /* Higher than overlay */
        }
        
        #sidebar.mobile-open {
            width: 280px;
        }
    }

    /* Ensure icons don't shrink */
    .min-w-6 {
        min-width: 1.5rem;
    }
    
    .min-w-8 {
        min-width: 2rem;
    }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    /* Optional: Add a subtle shadow on the rounded edges for depth */
    #sidebar {
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Modal animations */
    #logout-modal {
        transition: opacity 0.2s ease-in-out;
        z-index: 70; /* Higher than mobile sidebar z-index of 60 */
    }
    
    #logout-modal .bg-white {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Pulse animation for chat notification */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2);
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>