<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- ========================================== -->
    <!-- BASIC META TAGS                           -->
    <!-- ========================================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Konstructo')</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Custom scrollbar styling */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .main-content::-webkit-scrollbar-thumb {
            background: #40798C;
            border-radius: 4px;
        }
        
        .main-content::-webkit-scrollbar-thumb:hover {
            background: #1F363D;
        }

        /* Sidebar margin classes for main content */
        .sidebar-margin {
            margin-left: 0; /* No margin on mobile */
        }

        @media (min-width: 768px) {
            .sidebar-margin {
                margin-left: 5rem; /* w-20 = 5rem (80px) on desktop */
            }
        }

        /* Sidebar hover expansion */
        .sidebar-hover {
            transition: width 0.3s ease-in-out;
        }

        .sidebar-hover:hover {
            width: 16rem; /* w-64 = 16rem (256px) */
        }

        /* Sidebar text visibility */
        .sidebar-text {
            opacity: 1; /* default for mobile */
        }

        @media (min-width: 768px) {
            .sidebar-text {
                opacity: 0;
            }
        }

        /* Show all sidebar text when sidebar is hovered */
        .sidebar-hover:hover .sidebar-text {
            opacity: 1;
        }

        /* Adjust main content margin on desktop hover */
        @media (min-width: 768px) {
            .sidebar-hover:hover ~ #main-content-wrapper {
                margin-left: 16rem; /* w-64 = 16rem (256px) */
            }
        }

        /* Smooth transition for margin */
        #main-content-wrapper {
            transition: margin-left 0.3s ease-in-out;
        }
    </style>

    <!-- ========================================== -->
    <!-- CONTENT SECURITY POLICY                    -->
    <!-- ========================================== -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: http: data: 'unsafe-inline' 'unsafe-eval'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net http://localhost:5173 https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.tailwindcss.com http://localhost:5173; font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com data:; img-src 'self' data: https: http:; connect-src 'self' http://localhost:5173 ws://localhost:5173 wss://localhost:5173 https: https://cdn.jsdelivr.net; base-uri 'self'; form-action 'self';">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            'sans': ['Poppins', 'sans-serif'],
                        },
                    },
                },
            },
        };
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Trix Editor -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs"></script>

    <!-- Laravel Vite -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 flex min-h-screen overflow-hidden">
    <!-- Sidebar Component -->
    <x-sidebar />

    <!-- Main Content Area - Flex column with header and scrollable content -->
    <div id="main-content-wrapper" class="flex-1 flex flex-col overflow-hidden sidebar-margin">
        <!-- Welcome Header Component -->
        <x-welcome-header :name="Auth::user()->first_name ?? 'Guest'" :role="Auth::user()->role ?? 'User'" />
        
        <!-- Scrollable Content Area -->
        <main class="flex-1 overflow-y-auto main-content p-6">
            @yield('content')
        </main>
    </div>

    <!-- Logout Modal -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
        <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="text-center">
                    <!-- Logout Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Logout Confirmation</h3>
                    <p class="text-sm text-gray-600 mb-6">Are you sure you want to logout?</p>
                    
                    <div class="flex gap-3">
                        <button onclick="closeLogoutModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                            Cancel
                        </button>
                        <button onclick="logout()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <!-- Initialize Lucide Icons and Storage Management -->
    <script>
        // Clear all application storage
        function clearApplicationStorage() {
            // Session storage items
            sessionStorage.removeItem('konstructo_current_app_number');
            sessionStorage.removeItem('konstructo_just_generated');
            
            // Local storage items
            localStorage.removeItem('konstructo_app_number');
            localStorage.removeItem('konstructo_last_app_number');
            localStorage.removeItem('konstructo_last_app_timestamp');
            
            console.log('Application storage cleared');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Check for clear_storage flag from server - using Laravel's json directive
            let shouldClearStorage = {{ session('clear_storage') ? 'true' : 'false' }};
            
            if (shouldClearStorage) {
                clearApplicationStorage();
                console.log('Storage cleared on logout (session flash)');
            }
            
            // Check for clear_storage in URL parameter (optional)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('clear_storage') === 'true') {
                clearApplicationStorage();
                // Remove the parameter from URL without refreshing
                const url = new URL(window.location);
                url.searchParams.delete('clear_storage');
                window.history.replaceState({}, '', url);
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeLogoutModal();
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
                document.body.style.overflow = 'hidden';
            }
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logout-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function logout() {
            const modal = document.getElementById('logout-modal');
            const modalContent = modal.querySelector('.bg-white');
            
            // Show loading state
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
            
            // Clear storage before submitting logout form
            clearApplicationStorage();
            
            // Set a flag in localStorage to notify other tabs
            localStorage.setItem('konstructo_logout', Date.now().toString());
            
            // Submit the logout form
            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 500);
        }

        // Listen for storage events to sync logout across tabs
        window.addEventListener('storage', function(e) {
            if (e.key === 'konstructo_logout') {
                clearApplicationStorage();
                window.location.href = '/login';
            }
        });

        // Function to handle AJAX logout with storage clearing (alternative method)
        window.handleAjaxLogout = async function() {
            try {
                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getContent(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Clear storage
                    clearApplicationStorage();
                    
                    // Set a flag in localStorage to notify other tabs
                    localStorage.setItem('konstructo_logout', Date.now().toString());
                    
                    // Redirect to login page
                    window.location.href = '/login';
                } else {
                    console.error('Logout failed');
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Fallback redirect
                window.location.href = '/login';
            }
        };
        
        // Sidebar hover effect for main content margin adjustment
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content-wrapper');
            
            if (sidebar && mainContent) {
                // Only apply hover effect on desktop (md and above)
                if (window.innerWidth >= 768) {
                    sidebar.addEventListener('mouseenter', function() {
                        mainContent.style.marginLeft = '16rem'; // w-64
                    });
                    
                    sidebar.addEventListener('mouseleave', function() {
                        mainContent.style.marginLeft = '5rem'; // w-20
                    });
                }
                
                // Update on window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        // Reset to collapsed state
                        mainContent.style.marginLeft = '5rem';
                    } else {
                        // Mobile: no margin
                        mainContent.style.marginLeft = '0';
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>