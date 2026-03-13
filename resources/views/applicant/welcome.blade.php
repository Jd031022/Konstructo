@extends('layouts.app')

@section('title', 'Konstructo')

@section('content')

<!-- HERO SECTION -->
<section class="relative min-h-screen bg-gray-100 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img 
            src="{{ asset('images/cover.jpg') }}" 
            alt="City Hall Background"
            class="w-full h-full object-cover"
        >
    </div>

   <!-- NAVBAR - Fixed position with auto-hide on scroll -->
<nav id="main-navbar" class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-10 py-4 bg-white/70 backdrop-blur-md shadow-sm transition-transform duration-300 w-full">
    <!-- Left Spacer -->
    <div class="w-[180px]"></div>

    <!-- Center Menu -->
    <ul class="flex gap-8 text-sm font-medium text-gray-700">
         <li>
            <a href="#home" class="hover:text-[#40798C] transition">Home</a>
        </li>
        <li>
            <a href="#services" class="hover:text-[#40798C] transition">Services</a>
        </li>
        <li>
            <a href="#community" class="hover:text-[#40798C] transition">Community</a>
        </li>
        <li>
            <a href="#about" class="hover:text-[#40798C] transition">About</a>
        </li>
    </ul>

    <!-- Right Buttons -->
    <div class="flex items-center gap-3 w-[180px] justify-end">
        <a href="{{ route('login') }}" 
           class="px-4 py-1.5 text-sm rounded-full bg-white hover:bg-gray-100 transition shadow-sm border border-gray-200">
            Log in
        </a>
        <a href="{{ route('register') }}" 
           class="px-5 py-1.5 text-sm rounded-full bg-gradient-to-r from-[#155386] to-[#40798C] text-white hover:from-[#1F363D] hover:to-[#1F363D] shadow-sm transition-all duration-300 hover:shadow-lg">
            Sign up
        </a>
    </div>
</nav>

    <!-- HERO CONTENT -->
<div id="home" class="relative z-10 flex items-center justify-center min-h-[calc(100vh-80px)]">
    <div class="flex flex-col items-center text-center">
        <img src="{{ asset('images/ligao-seal.png') }}" alt="City Seal" class="w-[30%] drop-shadow-lg shadow-sm animate-float-glow">
        <div class="flex gap-4 mt-8">
            <a href="{{ route('register') }}" class="px-8 py-3 bg-gradient-to-r from-[#155386] to-[#40798C] text-white rounded-xl hover:from-[#1F363D] hover:to-[#1F363D] transition-all duration-300 shadow-lg hover:shadow-xl font-medium">
                Get Started
            </a>
            <a href="#services" class="px-8 py-3 bg-white/20 backdrop-blur-sm text-black rounded-xl hover:bg-white/30 transition-all duration-300 font-medium border-2 border-white/80 hover:border-white/80">
                Learn More
            </a>
        </div>
    </div>
</div>

</section>

<!-- Features Section -->
<section class="py-20 px-4 bg-white text-gray-800">
    <div class="max-w-6xl mx-auto text-center">
        <div class="inline-block px-4 py-1 bg-[#155386]/10 rounded-full text-[#155386] text-sm font-medium mb-4">
            Why Choose Konstructo
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Smart Infrastructure Oversight</h2>
        <p class="mb-12 max-w-2xl mx-auto text-gray-600">
            Discover how <b class="text-[#155386]">Konstructo</b> transforms city project monitoring through transparency, real-time updates, and a platform designed for efficient governance.
        </p>

        <!-- Icon Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-12 justify-items-center text-center">
            <!-- Feature 1 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Collaborative Project Oversight</h3>
                <p class="text-gray-600">Konstructo ensures that city engineers, project teams, and LGU leaders collaborate seamlessly to track project progress, budget utilization, and timelines.</p>
            </div>

            <!-- Feature 2 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Accessible</h3>
                <p class="text-gray-600">Open to city officials, engineers, planners, and authorized stakeholders — everyone involved in infrastructure management has the information they need.</p>
            </div>

            <!-- Feature 3 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Free for All</h3>
                <p class="text-gray-600">The platform is built to support LGU operations without licensing costs, helping cities manage resources efficiently.</p>
            </div>

            <!-- Feature 4 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">User-Friendly</h3>
                <p class="text-gray-600">Intuitive dashboard design with easy-to-read metrics, clear status indicators, and interactive project timelines.</p>
            </div>

            <!-- Feature 5 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Adaptive</h3>
                <p class="text-gray-600">Accessible on desktop, tablet, or mobile devices — so users can monitor projects from the office or on-site.</p>
            </div>

            <!-- Feature 6 -->
            <div class="flex flex-col items-center text-center max-w-xs group">
                <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Scalable</h3>
                <p class="text-gray-600">Built to grow with your city's infrastructure portfolio, from local barangay projects to large-scale city initiatives.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="bg-gray-50 py-20 px-6 md:px-20">
    <div class="max-w-7xl mx-auto text-center mb-12">
        <div class="inline-block px-4 py-1 bg-[#155386]/10 rounded-full text-[#155386] text-sm font-medium mb-4">
            Our Services
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-3 text-gray-800">Services Offered</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Explore Konstructo services. The platform provides key services for city infrastructure management and public engagement:
        </p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 max-w-7xl mx-auto">

        <!-- Building Permit Card -->
        <div class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-[#155386] to-[#40798C]"></div>
            <div class="p-8">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Building Permit</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4">Apply for building permit. Manage and track your application in one place.</p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Online application</span>
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Track progress in real-time</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center text-[#155386] font-medium group-hover:translate-x-2 transition-transform">
                    Apply Now 
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Coming Soon Card 1 -->
        <div class="group bg-white rounded-3xl shadow-lg opacity-75 overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-gray-300 to-gray-400"></div>
            <div class="p-8">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">Coming Soon</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Exciting new services coming soon. Stay tuned for updates!</p>
                <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></span>
                    <span>Estimated launch: Q2 2025</span>
                </div>
            </div>
        </div>

        <!-- Coming Soon Card 2 -->
        <div class="group bg-white rounded-3xl shadow-lg opacity-75 overflow-hidden">
            <div class="h-2 bg-gradient-to-r from-gray-300 to-gray-400"></div>
            <div class="p-8">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">Coming Soon</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Exciting new services coming soon. Stay tuned for updates!</p>
                <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></span>
                    <span>Estimated launch: Q3 2025</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section: Learning Path with Image Overlay -->
<section class="relative">
    <img src="{{ asset('images/cover2.jpg') }}" class="w-full h-[420px] object-cover" alt="Learning Group">
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/50 flex flex-col justify-center px-6 md:px-20 text-white">
        <div class="max-w-2xl">
            <div class="inline-block px-4 py-1 bg-white/20 rounded-full text-white text-sm font-medium mb-4 backdrop-blur-sm">
                Our Community
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Building Better Cities Together</h2>
            <p class="text-lg leading-relaxed mb-4">
                Konstructo thrives on collaboration between engineers, city planners, and decision-makers. The platform encourages shared knowledge and accountability.
            </p>
            <p class="text-white/80">
                Connect with project engineers and departments. Share updates, progress photos, and reports. Collaborate on approvals and validations.
            </p>
        </div>
    </div>
</section>

<section id="community" class="bg-white py-20 px-6 md:px-20"> 
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <!-- Image -->
        <div class="rounded-3xl overflow-hidden shadow-2xl">
            <img src="{{ asset('images/cover2.jpg') }}" alt="Community Collaboration" class="w-full h-[400px] object-cover hover:scale-105 transition-transform duration-700">
        </div>
        
        <!-- Text -->
        <div class="flex flex-col justify-center">
            <div class="inline-block px-4 py-1 bg-[#155386]/10 rounded-full text-[#155386] text-sm font-medium mb-4 w-fit">
                Stakeholder Network
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Connected Governance</h2>
            <p class="text-gray-600 text-lg mb-6">
                Konstructo connects all key players in infrastructure development—from city planners and engineers to barangay officials and community representatives—creating a unified platform for collaborative governance.
            </p>
            <ul class="space-y-3">
                <li class="flex items-center gap-3 text-gray-600">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>Connect with project engineers and department heads</span>
                </li>
                <li class="flex items-center gap-3 text-gray-600">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>Share real-time progress updates and documentation</span>
                </li>
                <li class="flex items-center gap-3 text-gray-600">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>Collaborate on approvals and quality validation</span>
                </li>
                <li class="flex items-center gap-3 text-gray-600">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>Engage with community stakeholders for feedback</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="bg-gray-50 py-20 px-6 md:px-20">
    <div class="max-w-5xl mx-auto text-center">
        <div class="inline-block px-4 py-1 bg-[#155386]/10 rounded-full text-[#155386] text-sm font-medium mb-4">
            About Us
        </div>
        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">About <span class="text-[#155386]">Konstructo</span></h2>
        <p class="text-gray-600 text-lg max-w-3xl mx-auto mb-8">
            Konstructo for local government units. The system supports transparency, accountability, and efficient project tracking through real-time dashboards, automated DSS-AI progress assessment, and secure document management.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-12">
            <div class="bg-white shadow-xl rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-[#155386] mb-2">0</h3>
                <p class="text-gray-600 font-semibold">Infrastructure Projects</p>
                <p class="text-gray-400 text-sm">Tracked & Monitored</p>
            </div>
            <div class="bg-white shadow-xl rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-[#155386] mb-2">0</h3>
                <p class="text-gray-600 font-semibold">Partner LGUs</p>
                <p class="text-gray-400 text-sm">Across Bicol Region</p>
            </div>
            <div class="bg-white shadow-xl rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 group">
                <div class="w-16 h-16 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-[#155386] mb-2">0</h3>
                <p class="text-gray-600 font-semibold">Faster Reporting</p>
                <p class="text-gray-400 text-sm">Compared to Manual Systems</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 pt-16">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 px-6 md:px-20">
        <!-- Brand -->
        <div>
            <h3 class="text-white text-2xl font-bold mb-4">Konstructo</h3>
            <p class="text-sm text-gray-400 leading-relaxed">
                Smart infrastructure oversight for modern cities. Building better communities through technology and collaboration.
            </p>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-white font-semibold mb-4">Quick Links</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="#home" class="text-gray-400 hover:text-white transition">Home</a></li>
                <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                <li><a href="#community" class="text-gray-400 hover:text-white transition">Community</a></li>
                <li><a href="#about" class="text-gray-400 hover:text-white transition">About</a></li>
            </ul>
        </div>

        <!-- Contact -->
        <div>
            <h3 class="text-white font-semibold mb-4">Contact Us</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center gap-2">
                    <span class="text-[#40798C]">📞</span>
                    <span>(+63) 912-345-6789</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-[#40798C]">📍</span>
                    <span>123 Albay Road, Legazpi City</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-[#40798C]">✉️</span>
                    <span>support@konstructo.org</span>
                </li>
            </ul>
        </div>

        <!-- Follow Us -->
        <div>
            <h3 class="text-white font-semibold mb-4">Follow Us</h3>
            <div class="flex gap-3 mb-4">
                <a href="#" class="w-10 h-10 bg-[#40798C]/20 rounded-xl flex items-center justify-center text-[#40798C] hover:bg-[#40798C] hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 5 3.66 9.13 8.44 9.88v-6.99h-2.54v-2.89h2.54V9.41c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.45h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.89h-2.34v6.99C18.34 21.13 22 17 22 12z"/>
                    </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-[#40798C]/20 rounded-xl flex items-center justify-center text-[#40798C] hover:bg-[#40798C] hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.31l-5.214-6.82-5.97 6.82H1.816l7.73-8.836L1.308 2.25h6.972l4.713 6.231 5.251-6.231z"/>
                    </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-[#40798C]/20 rounded-xl flex items-center justify-center text-[#40798C] hover:bg-[#40798C] hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3A2 2 0 0 1 21 5V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H19M8.34 17V10.67H6V17H8.34M7.17 9.67A1.17 1.17 0 1 0 7.17 7.33 1.17 1.17 0 0 0 7.17 9.67M18 17V13.22C18 11.09 16.66 10.5 15.27 10.5C14.25 10.5 13.65 11 13.41 11.46H13.36V10.67H11V17H13.34V13.47C13.34 12.79 13.73 12.33 14.39 12.33C15.05 12.33 15.34 12.79 15.34 13.47V17H18Z"/>
                    </svg>
                </a>
            </div>
            <p class="text-xs text-gray-500">Stay connected with our advocacy stories and community impact.</p>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-6 md:px-20 py-6 flex flex-col md:flex-row justify-between text-sm text-gray-500">
            <p>© 2025 Konstructo. All Rights Reserved.</p>
            <div class="space-x-6 mt-2 md:mt-0">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript for navbar hide/show on scroll -->
<script>
    let lastScrollTop = 0;
    const navbar = document.getElementById('main-navbar');
    const scrollThreshold = 10;

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (Math.abs(lastScrollTop - scrollTop) <= scrollThreshold) return;
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });
</script>

<style>
    @keyframes floatGlow {
        0% {
            transform: translateY(0px);
            filter: drop-shadow(0 0 5px rgba(64, 121, 140, 0.3));
        }
        50% {
            transform: translateY(-15px);
            filter: drop-shadow(0 0 20px rgba(64, 121, 140, 0.6));
        }
        100% {
            transform: translateY(0px);
            filter: drop-shadow(0 0 5px rgba(64, 121, 140, 0.3));
        }
    }
    
    .animate-float-glow {
        animation: floatGlow 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    .group:hover .group-hover\:scale-105 {
        transform: scale(1.05);
    }

    .group:hover .group-hover\:rotate-6 {
        transform: rotate(6deg);
    }

    .group:hover .group-hover\:translate-x-2 {
        transform: translateX(0.5rem);
    }

    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
</style>

@endsection