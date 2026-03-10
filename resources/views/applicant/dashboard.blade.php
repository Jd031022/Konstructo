@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')

<!-- Main Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Limit Reached Warning (shown when at limit) -->
    <div id="limit-warning" class="mb-8 hidden">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg" role="alert">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-bold">Application Limit Reached</p>
                    <p class="text-sm">You have reached the maximum limit of 3 applications. Please complete or delete existing applications before creating a new one.</p>
                    <div class="mt-3">
                        <a href="/applicant/applications" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                            Manage Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Title -->
    <div class="text-center mb-10">
        <h1 class="text-2xl font-semibold">Services offered</h1>
        <p class="text-gray-500 max-w-2xl mx-auto mt-2">
        Explore our range of municipal services available online. Apply, track, and manage your applications with ease.
        </p>
    </div>

    <!-- Services Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">

        <!-- Building Permit -->
        <div class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition relative" id="building-permit-card">
            <!-- Limit indicator badge (shown when at limit) -->
            <div id="limit-badge" class="absolute top-2 right-2 hidden">
                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">Limit Reached</span>
            </div>

            <img src="{{ asset('images/bp.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Building Permit
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Apply for building permit. Manage and track your application.
            </p>

            <!-- Apply Button Container -->
            <div id="apply-button-container" class="mt-4">
                <a href="/applicant/application/step1?new=true" id="apply-button">
                    <button class="w-full bg-[#155386] text-white py-2 rounded-full hover:bg-[#1F363D] transition">
                        Apply
                    </button>
                </a>
            </div>

            <!-- Disabled Apply Message (shown when at limit) -->
            <div id="disabled-apply-message" class="mt-4 hidden">
                <div class="text-center">
                    <p class="text-sm text-red-600 mb-2">Application limit reached</p>
                    <a href="/applicant/applications" class="inline-block text-sm text-[#155386] hover:underline">
                        Manage existing applications
                    </a>
                </div>
            </div>
        </div>

        <!-- Coming Soon -->
        <div class="bg-white rounded-xl shadow-md p-4 opacity-75">
            <img src="{{ asset('images/cm.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Coming soon
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Exciting new services coming soon. Stay tuned for updates!
            </p>
            
            <button class="mt-4 w-full bg-gray-300 text-gray-500 py-2 rounded-full cursor-not-allowed" disabled>
                Unavailable
            </button>
        </div>

        <!-- Coming Soon -->
        <div class="bg-white rounded-xl shadow-md p-4 opacity-75">
            <img src="{{ asset('images/cm.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Coming soon
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Exciting new services coming soon. Stay tuned for updates!
            </p>
            
            <button class="mt-4 w-full bg-gray-300 text-gray-500 py-2 rounded-full cursor-not-allowed" disabled>
                Unavailable
            </button>
        </div>

    </div>
    

</div>

<!-- Chatbot (Floating Bottom Right) -->
<x-chatbot />

<!-- JavaScript -->
<script>
    const APPLICATION_LIMIT = 3;

    document.addEventListener('DOMContentLoaded', async function() {
        await checkApplicationLimit();
        await loadApplicationStats();
    });

    // Check application limit and update UI
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
                const current = data.data.current;
                const remaining = data.data.remaining;
                
                // Show/hide limit warning and update UI based on limit status
                if (!canApply) {
                    // Show limit warning
                    document.getElementById('limit-warning').classList.remove('hidden');
                    
                    // Show limit badge on card
                    document.getElementById('limit-badge').classList.remove('hidden');
                    
                    // Hide apply button, show disabled message
                    document.getElementById('apply-button-container').classList.add('hidden');
                    document.getElementById('disabled-apply-message').classList.remove('hidden');
                    
                    // Update apply button to be disabled
                    const applyBtn = document.getElementById('apply-button');
                    if (applyBtn) {
                        applyBtn.classList.add('pointer-events-none');
                        applyBtn.setAttribute('aria-disabled', 'true');
                    }
                } else {
                    // Hide limit warning if under limit
                    document.getElementById('limit-warning').classList.add('hidden');
                    
                    // Hide limit badge
                    document.getElementById('limit-badge').classList.add('hidden');
                    
                    // Show apply button, hide disabled message
                    document.getElementById('apply-button-container').classList.remove('hidden');
                    document.getElementById('disabled-apply-message').classList.add('hidden');
                    
                    // Enable apply button
                    const applyBtn = document.getElementById('apply-button');
                    if (applyBtn) {
                        applyBtn.classList.remove('pointer-events-none');
                        applyBtn.removeAttribute('aria-disabled');
                    }
                }
            }
        } catch (error) {
            console.error('Error checking application limit:', error);
        }
    }

    // Load application statistics
    async function loadApplicationStats() {
        try {
            const response = await fetch('/applicant/applications/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const stats = await response.json();
            
            // Update quick stats banner
            document.getElementById('total-apps').textContent = stats.total || 0;
            document.getElementById('pending-apps').textContent = stats.pending || 0;
            document.getElementById('remaining-apps').textContent = Math.max(0, APPLICATION_LIMIT - (stats.total || 0));
            
        } catch (error) {
            console.error('Error loading application stats:', error);
        }
    }
</script>

<style>
    /* Card hover effects */
    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Limit badge animation */
    #limit-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
        100% {
            opacity: 1;
        }
    }
</style>

@endsection