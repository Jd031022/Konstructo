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