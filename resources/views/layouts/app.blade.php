<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <!-- ========================================== -->
    <!-- BASIC META TAGS                           -->
    <!-- ========================================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Konstructo')</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    

    @auth
    @php
        // Only create token if it doesn't exist in session
        if (!session()->has('api_token')) {
            $token = auth()->user()->createToken('api-token')->plainTextToken;
            session()->put('api_token', $token);
        }
    @endphp
    <meta name="api-token" content="{{ session('api_token') }}">
@endauth

        <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        
        /* Apply Poppins globally */
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Or more specifically */
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    
    <!-- ========================================== -->
    <!-- CONTENT SECURITY POLICY (CSP)              -->
    <!-- ========================================== -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: http: data: 'unsafe-inline' 'unsafe-eval'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://unpkg.com https://cdn.jsdelivr.net http://localhost:5173 https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.tailwindcss.com http://localhost:5173; font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com data:; img-src 'self' data: https: http:; connect-src 'self' http://localhost:5173 ws://localhost:5173 wss://localhost:5173 https: https://cdn.jsdelivr.net; base-uri 'self'; form-action 'self';">
    
    <!-- ========================================== -->
    <!-- GOOGLE FONTS - Poppins (All Weights)       -->
    <!-- ========================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- ========================================== -->
    <!-- TAILWIND CSS CONFIGURATION                  -->
    <!-- ========================================== -->
    <script>
        // Configure Tailwind to use Poppins
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
    
    <!-- ========================================== -->
    <!-- TAILWIND CSS - CDN Version                  -->
    <!-- ========================================== -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ========================================== -->
    <!-- TRIX EDITOR - Rich Text Editor              -->
    <!-- ========================================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>
    
    <!-- ========================================== -->
    <!-- LUCIDE ICONS - Icon Library                 -->
    <!-- ========================================== -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- ========================================== -->
    <!-- ALPINE.JS - For interactivity               -->
    <!-- ========================================== -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs"></script>
    
    <!-- ========================================== -->
    <!-- VITE - For Laravel Vite                     -->
    <!-- ========================================== -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
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

        // Check for pending surveys (only for applicants)
        @auth
            @if(auth()->user()->role === 'applicant')
                setTimeout(checkPendingSurveys, 3000); // Delay to ensure everything is loaded
            @endif
        @endauth

        // Function to check for pending surveys
        async function checkPendingSurveys() {
            try {
                const response = await fetch('/applicant/survey/pending', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.pending_surveys && data.pending_surveys.length > 0) {
                        // Show survey modal for the first pending survey
                        const firstSurvey = data.pending_surveys[0];
                        if (window.showSurveyModal) {
                            window.showSurveyModal(firstSurvey.id, firstSurvey.service_availed);
                        }
                    }
                }
            } catch (error) {
                console.error('Error checking pending surveys:', error);
            }
        }

        // Listen for logout events from AJAX requests
        window.addEventListener('user-logout', function() {
            clearApplicationStorage();
        });

        // Optional: Listen for storage events to sync logout across tabs
        window.addEventListener('storage', function(e) {
            if (e.key === 'konstructo_logout') {
                clearApplicationStorage();
                window.location.href = '/login';
            }
        });

        // Function to handle logout with storage clearing (for AJAX logout)
        window.handleLogout = async function() {
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
                    
                    // Dispatch custom event for other listeners
                    window.dispatchEvent(new Event('user-logout'));
                    
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
    </script>
    
    @include('partials.survey-modal')
    
    @stack('scripts')
</body>
</html>