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
    <link rel="icon" href="{{ asset('images/turo_moko_logo.png') }}" type="image/png">
    
    <!-- ========================================== -->
    <!-- FIXED CONTENT SECURITY POLICY (CSP)        -->
    <!-- NO COMMENTS ALLOWED INSIDE CSP!            -->
    <!-- ========================================== -->
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self' https: http: data: 'unsafe-inline' 'unsafe-eval';
        script-src 'self' 'unsafe-inline' 'unsafe-eval' 
            https://cdn.tailwindcss.com 
            https://cdnjs.cloudflare.com 
            https://unpkg.com 
            https://cdn.jsdelivr.net
            http://localhost:5173 
            https://fonts.googleapis.com;
        style-src 'self' 'unsafe-inline' 
            https://fonts.googleapis.com 
            https://cdnjs.cloudflare.com 
            https://cdn.tailwindcss.com
            http://localhost:5173;
        font-src 'self' https://fonts.gstatic.com data:;
        img-src 'self' data: https: http:;
        connect-src 'self' http://localhost:5173 ws://localhost:5173 wss://localhost:5173 https:;
        base-uri 'self';
        form-action 'self';
    ">
    
    <!-- ========================================== -->
    <!-- GOOGLE FONTS - Poppins                     -->
    <!-- ========================================== -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ========================================== -->
    <!-- TAILWIND CSS - CDN Version                  -->
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
    <!-- VITE - For Laravel Vite (if needed)         -->
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
    
    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>