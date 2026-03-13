@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')

<!-- Main Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Limit Reached Warning -->
    <div id="limit-warning" class="mb-8 hidden">
        <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-lg">Application Limit Reached</p>
                    <p class="text-white/90 text-sm mb-4">You have reached the maximum limit of 3 applications. Please complete or delete existing applications before creating a new one.</p>
                    <a href="/applicant/applications" class="inline-flex items-center px-4 py-2 bg-white text-red-600 rounded-xl hover:bg-white/90 transition font-medium text-sm">
                        Manage Applications
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Our Services</h1>
            <p class="text-gray-500 mt-1">Select a service to get started with your application</p>
        </div>
        <div class="hidden sm:block">
            <span class="text-sm text-gray-400">⚡ 24/7 Online Services</span>
        </div>
    </div>

    <!-- Services Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Building Permit Card -->
        <div class="group relative" id="building-permit-card">
            <!-- Limit Badge -->
            <div id="limit-badge" class="absolute -top-3 -right-3 z-10 hidden">
                <span class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg">Limit Reached</span>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <!-- Top Accent -->
                <div class="h-2 bg-gradient-to-r from-[#155386] to-[#40798C]"></div>
                
                <!-- Card Content -->
                <div class="p-8">
                    <!-- Icon Circle -->
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#155386] to-[#40798C] rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Building Permit</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">
                        Apply for new building permits, renovations, and construction projects. Fast-track processing available.
                    </p>

                    <!-- Features List -->
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
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Digital copy of permit</span>
                        </li>
                    </ul>
                    <!-- Apply Button Container -->
                    <div id="apply-button-container" class="mt-4">
                        <a href="{{ route('applicant.building-permit.preview') }}" id="apply-button">
                            <button class="w-full bg-gray-50 text-[#155386] py-3 rounded-xl hover:bg-[#155386] hover:text-white transition-all duration-300 font-medium border-2 border-[#155386]/20 hover:border-transparent group-hover:shadow-lg">
                                Start Application
                                <svg class="w-4 h-4 inline ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </a>
                    </div>
                    <!-- Disabled State -->
                    <div id="disabled-apply-message" class="mt-4 hidden">
                        <div class="bg-red-50 rounded-xl p-4 text-center">
                            <p class="text-sm font-medium text-red-800 mb-2">Application limit reached</p>
                            <a href="/applicant/applications" class="inline-block text-sm text-[#155386] hover:underline font-medium">
                                Manage applications →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon Card 1 -->
        <div class="group relative opacity-75">
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
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
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        We're working on something exciting. Check back later for new services.
                    </p>
                    <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></span>
                        <span>Estimated launch: Q2 2025</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon Card 2 -->
        <div class="group relative opacity-75">
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
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
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        New services are in development. Stay tuned for updates.
                    </p>
                    <div class="inline-flex items-center gap-2 text-sm text-gray-400">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></span>
                        <span>Estimated launch: Q3 2025</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chatbot -->
<x-chatbot />

<!-- JavaScript -->
<script>
    const APPLICATION_LIMIT = 3;

    document.addEventListener('DOMContentLoaded', async function() {
        await checkApplicationLimit();
        await loadApplicationStats();
    });

    async function checkApplicationLimit() {
        try {
            const response = await fetch('/applicant/application/limit-info', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const canApply = data.data.can_apply;
                
                if (!canApply) {
                    document.getElementById('limit-warning').classList.remove('hidden');
                    document.getElementById('limit-badge').classList.remove('hidden');
                    document.getElementById('apply-button-container').classList.add('hidden');
                    document.getElementById('disabled-apply-message').classList.remove('hidden');
                } else {
                    document.getElementById('limit-warning').classList.add('hidden');
                    document.getElementById('limit-badge').classList.add('hidden');
                    document.getElementById('apply-button-container').classList.remove('hidden');
                    document.getElementById('disabled-apply-message').classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error checking application limit:', error);
        }
    }

    async function loadApplicationStats() {
        try {
            const response = await fetch('/applicant/applications/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const stats = await response.json();
            
            document.getElementById('total-apps').textContent = stats.total || 0;
            document.getElementById('pending-apps').textContent = stats.pending || 0;
            document.getElementById('remaining-apps').textContent = Math.max(0, APPLICATION_LIMIT - (stats.total || 0));
            
        } catch (error) {
            console.error('Error loading application stats:', error);
        }
    }
</script>

<style>
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Card hover effects */
    .group:hover .group-hover\:shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .group:hover .group-hover\:rotate-6 {
        transform: rotate(6deg);
    }

    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(0.25rem);
    }

    /* Pulse animation for coming soon */
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

    /* Limit badge animation */
    #limit-badge {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection