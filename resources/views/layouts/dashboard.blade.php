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
       <div id="main-content-wrapper" class="flex-1 flex flex-col overflow-hidden sidebar-collapsed">
        <!-- Welcome Header Component (with optional parameters) -->
        <x-welcome-header :name="Auth::user()->name ?? 'Guest'" :role="Auth::user()->role ?? 'User'" />
        
        <!-- Scrollable Content Area -->
        <main class="flex-1 overflow-y-auto main-content p-6">
            @yield('content')
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const burgerMenu = document.getElementById('burger-menu');
            const mainContent = document.getElementById('main-content-wrapper');
            let isExpanded = false;

            burgerMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                
                if (!isExpanded) {
                    // Expand sidebar
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-48');
                    
                    // Adjust main content margin
                    mainContent.classList.remove('sidebar-collapsed');
                    mainContent.classList.add('sidebar-expanded');
                    
                    // Show all text labels with animation
                    const labels = sidebar.querySelectorAll('span');
                    labels.forEach(label => {
                        label.classList.remove('opacity-0');
                        label.classList.add('opacity-100');
                    });
                    
                    isExpanded = true;
                } else {
                    // Collapse sidebar
                    sidebar.classList.remove('w-48');
                    sidebar.classList.add('w-20');
                    
                    // Adjust main content margin
                    mainContent.classList.remove('sidebar-expanded');
                    mainContent.classList.add('sidebar-collapsed');
                    
                    // Hide all text labels
                    const labels = sidebar.querySelectorAll('span');
                    labels.forEach(label => {
                        label.classList.remove('opacity-100');
                        label.classList.add('opacity-0');
                    });
                    
                    isExpanded = false;
                }
            });

            // Optional: Click outside to collapse
            document.addEventListener('click', function(e) {
                if (isExpanded && !sidebar.contains(e.target)) {
                    // Collapse sidebar
                    sidebar.classList.remove('w-48');
                    sidebar.classList.add('w-20');
                    
                    // Adjust main content margin
                    mainContent.classList.remove('sidebar-expanded');
                    mainContent.classList.add('sidebar-collapsed');
                    
                    const labels = sidebar.querySelectorAll('span');
                    labels.forEach(label => {
                        label.classList.remove('opacity-100');
                        label.classList.add('opacity-0');
                    });
                    
                    isExpanded = false;
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>