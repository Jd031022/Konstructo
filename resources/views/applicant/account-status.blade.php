@extends('layouts.app')

@section('title', 'Account Status')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full mx-auto">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all duration-500 hover:shadow-2xl">
            <!-- Header with dynamic gradient based on status -->
            @php
                $user = Auth::user();
                $status = $user->approval_status ?? 'pending';
                $rejectionReason = $user->rejection_reason ?? null;
                
                $statusColors = [
                    'approved' => 'from-green-500 to-green-600',
                    'rejected' => 'from-red-500 to-red-600',
                    'pending' => 'from-yellow-500 to-yellow-600'
                ];
                $statusIcons = [
                    'approved' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                    'rejected' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                    'pending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
                ];
                $statusMessages = [
                    'approved' => 'Account Approved',
                    'rejected' => 'Account Rejected',
                    'pending' => 'Pending Approval'
                ];
                $statusDescriptions = [
                    'approved' => 'Congratulations! Your account has been approved. You can now log in and start using the system.',
                    'rejected' => 'We regret to inform you that your account application has been rejected.',
                    'pending' => 'Your account is currently pending approval from the administrator. You will receive a notification once your account is approved.'
                ];
                $gradientClass = $statusColors[$status] ?? 'from-gray-500 to-gray-600';
                $iconPath = $statusIcons[$status] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                $title = $statusMessages[$status] ?? 'Account Status';
                $description = $statusDescriptions[$status] ?? 'Please check your account status.';
            @endphp
            
            <div class="bg-gradient-to-r {{ $gradientClass }} px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white bg-opacity-20 backdrop-blur-sm mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $iconPath !!}
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ $title }}</h1>
                <p class="text-white text-opacity-90 text-sm">
                    {{ $description }}
                </p>
            </div>
            
            <!-- Body Content -->
            <div class="p-6 space-y-6">
                <!-- User Info -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Account Holder</p>
                            <p class="font-medium text-gray-800">{{ $user->first_name }} {{ $user->last_name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email Address</p>
                            <p class="font-medium text-gray-800">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Status Specific Content -->
                @if($status == 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-green-800 font-medium mb-1">What's next?</p>
                                <p class="text-xs text-green-700">You can now log in to your account and start submitting applications. Click the button below to proceed to the login page.</p>
                            </div>
                        </div>
                    </div>
                @elseif($status == 'rejected')
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-red-800 font-medium mb-1">Reason for Rejection</p>
                                <p class="text-xs text-red-700">
                                    {{ $rejectionReason ?? 'Your account application did not meet the required criteria. Please contact support for more information.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-600 mb-3">Need assistance? You can:</p>
                        <div class="space-y-2">
                            <a href="mailto:support@konstructo.com" class="flex items-center gap-2 text-sm text-[#155386] hover:text-[#40798C] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Contact Support
                            </a>
                            <a href="/" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Return to Homepage
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-800 font-medium mb-1">What happens next?</p>
                                <p class="text-xs text-yellow-700">Our team will review your account details. This process usually takes 1-2 business days. You will receive an email notification once your account is approved.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if($status == 'approved')
                        <a href="{{ route('login') }}" 
                           class="block w-full text-center bg-gradient-to-r from-[#155386] to-[#40798C] text-white py-3 rounded-xl font-medium hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            Proceed to Login
                        </a>
                    @elseif($status == 'rejected')
                        <a href="{{ route('register') }}" 
                           class="block w-full text-center bg-gradient-to-r from-[#155386] to-[#40798C] text-white py-3 rounded-xl font-medium hover:shadow-lg transition-all duration-300">
                            Create New Account
                        </a>
                    @else
                        <!-- For pending status, show logout button instead of check status again -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-center bg-gradient-to-r from-[#155386] to-[#40798C] text-white py-3 rounded-xl font-medium hover:shadow-lg transition-all duration-300">
                                Logout & Return to Login
                            </button>
                        </form>
                    @endif
                    
                    <a href="/" 
                       class="block w-full text-center border border-gray-300 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-50 transition-all duration-300">
                        Back to Homepage
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    Need help? Contact our support team at 
                    <a href="mailto:support@konstructo.com" class="text-[#155386] hover:underline">support@konstructo.com</a>
                </p>
            </div>
        </div>
        
        <!-- Additional Info Card -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">
                © {{ date('Y') }} Konstructo. All rights reserved.
            </p>
        </div>
    </div>
</div>

<script>
    // Auto-refresh for pending status every 30 seconds to check if approved
    @if($status == 'pending')
        let refreshInterval = setInterval(() => {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Check if status changed by looking for the header text
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const headerText = doc.querySelector('h1')?.textContent || '';
                const currentHeader = '{{ $title }}';
                
                if (headerText !== currentHeader) {
                    clearInterval(refreshInterval);
                    location.reload();
                }
            })
            .catch(error => console.error('Error checking status:', error));
        }, 30000); // Check every 30 seconds
        
        // Clear interval on page unload
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    @endif
</script>

<style>
    /* Smooth animations */
    .transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }
    
    /* Pulse animation for pending status */
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
    
    /* Custom scrollbar for modals if needed */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #40798C;
    }
</style>
@endsection