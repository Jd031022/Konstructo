@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 relative">

    <!-- Background Illustration -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" 
             class="w-full h-full object-cover" 
             alt="background">
        <div class="absolute inset-0 backdrop-blur-[2px] bg-white/10"></div>
    </div>

    <!-- Login Card -->
    <div class="relative bg-white/70 backdrop-blur-sm rounded-xl shadow-lg p-8" style="width: 500px; min-height: auto; padding: 90px 70px;">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo.png') }}" class="w-12 mb-2">
            <div class="flex">
                <h1 class="text-xl font-semibold text-[#155386]">Konstr</h1>
                <h1 class="text-xl font-semibold text-[#40798C]">ucto</h1>
            </div>
            <p class="text-sm text-gray-500">Login to continue</p>
        </div>

        <!-- Error Message Display -->
        <div id="error-message" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm"></div>
        
        <!-- Success Message Display -->
        <div id="success-message" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm"></div>
        
        <!-- Warning Message Display (for approval status) -->
        <div id="warning-message" class="hidden mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg text-sm"></div>

        <!-- Login Form -->
        <form id="login-form" method="POST">
            @csrf

            <!-- Email/Username -->
            <div class="mb-4">
                <label class="block text-sm text-black mb-1">Email or Username</label>
                <input type="text"
                    id="login"
                    name="login"
                    value="{{ old('login') }}"
                    placeholder="Enter your email or username"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm text-black mb-1">Password</label>
                <div class="relative">
                    <input type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password here"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600 pr-10"
                        required>
                    <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600 hover:text-gray-800 focus:outline-none"
                        tabindex="-1">
                        <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="flex items-center justify-between text-sm mb-4">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me
                </label>
            </div>

            <!-- Login -->
            <button type="submit"
                id="login-button"
                class="w-full bg-[#155386] text-white py-2 rounded-md text-sm font-medium transition flex items-center justify-center gap-2">
                <span id="button-text">Login</span>
                <span id="button-spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            <div class="mt-3 text-right">
                <a href="#" class="text-sm text-gray-500 hover:text-[#155386] transition" onclick="showForgotPassword()">
                    Forgot Password?
                </a>
            </div>

            <!-- Register -->
            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-[#155386] font-medium hover:underline">
                    Sign up here.
                </a>
            </p>
        </form>
    </div>
</div>

<!-- Pending Approval Modal -->
<div id="pending-approval-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Account Pending Approval</h3>
                        <p class="text-white/80 text-sm">Verification Required</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Your account is pending admin approval</h4>
                    <p class="text-gray-600 text-sm">
                        Thank you for registering! Your account is currently being reviewed by our administrators.
                        You will receive a notification once your account is approved.
                    </p>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4 mb-6 border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium mb-1">What happens next?</p>
                            <p class="text-xs text-yellow-700">Our team will review your account details. This process usually takes 1-2 business days. You will receive an email notification once your account is approved.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-3">
                    <button onclick="closePendingApprovalModal()" 
                            class="w-full bg-gradient-to-r from-[#155386] to-[#1F363D] text-white px-4 py-2 rounded-lg font-medium hover:shadow-lg transition-all duration-300">
                        I Understand
                    </button>
                    
                    <a href="/" 
                       class="w-full text-center border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-all duration-300">
                        Return to Homepage
                    </a>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    Need help? Contact our support team at 
                    <a href="mailto:support@konstructo.com" class="text-[#155386] hover:underline">support@konstructo.com</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Rejected Account Modal -->
<div id="rejected-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Account Rejected</h3>
                        <p class="text-white/80 text-sm">Application Denied</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Account Registration Rejected</h4>
                    <p class="text-gray-600 text-sm">
                        We regret to inform you that your account registration has been rejected by our administrators.
                    </p>
                </div>
                
                <div class="bg-red-50 rounded-lg p-4 mb-6 border border-red-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-red-800 font-medium mb-1">Reason for Rejection</p>
                            <p class="text-sm text-red-700" id="rejection-reason-display">Your application did not meet the required criteria. Please contact support for more information.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ route('register') }}" 
                       class="w-full bg-gradient-to-r from-[#155386] to-[#1F363D] text-white px-4 py-2 rounded-lg font-medium hover:shadow-lg transition-all duration-300 text-center">
                        Create New Account
                    </a>
                    
                    <a href="/" 
                       class="w-full text-center border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-all duration-300">
                        Return to Homepage
                    </a>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Need help? Contact us at support@konstructo.com</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgot-password-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-[#155386] to-[#40798C] px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Reset Password</h3>
                <button onclick="closeForgotPasswordModal()" class="text-white hover:text-gray-200 transition p-1 rounded-full hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-center" id="modal-step-description">
                    Enter your email address to receive a verification code.
                </p>
                
                <!-- Step 1: Email Input -->
                <div id="step-email" class="step">
                    <div class="mb-4">
                        <input type="email" 
                               id="reset-email" 
                               placeholder="Enter your email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386]">
                    </div>
                    
                    <button onclick="sendResetCode()" id="send-code-btn"
                        class="w-full bg-gradient-to-r from-[#155386] to-[#40798C] text-white px-6 py-3 rounded-lg font-semibold 
                               hover:from-[#1F363D] hover:to-[#1F363D] transition-all duration-200
                               shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mb-4">
                        <span id="send-code-text">Send Reset Code</span>
                        <span id="send-code-spinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <!-- Step 2: Code Verification -->
                <div id="step-code" class="step hidden">
                    <div class="mb-4 text-center">
                        <p class="text-sm text-gray-600 mb-2">We've sent a 6-digit code to:</p>
                        <p class="text-sm font-semibold text-[#155386] mb-4" id="code-email-display"></p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter Code</label>
                        <div class="flex gap-2 justify-center mb-2">
                            <input type="text" id="code1" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code2')">
                            <input type="text" id="code2" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code3')">
                            <input type="text" id="code3" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code4')">
                            <input type="text" id="code4" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code5')">
                            <input type="text" id="code5" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code6')">
                            <input type="text" id="code6" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="if(this.value.length === 1) verifyCode()">
                        </div>
                        
                        <div class="flex justify-between mt-2">
                            <button onclick="resendCode()" class="text-sm text-[#155386] hover:text-[#40798C]">
                                Resend Code
                            </button>
                            <button onclick="backToEmail()" class="text-sm text-gray-500 hover:text-gray-700">
                                Change Email
                            </button>
                        </div>
                    </div>
                    
                    <button onclick="verifyCode()" id="verify-code-btn"
                        class="w-full bg-gradient-to-r from-[#155386] to-[#40798C] text-white px-6 py-3 rounded-lg font-semibold 
                               hover:from-[#1F363D] hover:to-[#1F363D] transition-all duration-200
                               shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mb-4">
                        <span id="verify-code-text">Verify Code</span>
                        <span id="verify-code-spinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <!-- Step 3: New Password -->
                <div id="step-password" class="step hidden">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">Enter your new password</p>
                        
                        <div class="mb-3">
                            <input type="password" 
                                   id="new-password" 
                                   placeholder="New Password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] mb-2">
                            <input type="password" 
                                   id="confirm-password" 
                                   placeholder="Confirm New Password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386]">
                        </div>
                        
                        <div class="text-xs text-left text-gray-500 p-2 bg-gray-50 rounded">
                            <p class="font-medium mb-1">Password must contain:</p>
                            <ul class="list-disc list-inside">
                                <li id="req-length" class="text-gray-400">8-16 characters</li>
                                <li id="req-uppercase" class="text-gray-400">At least 1 uppercase letter</li>
                                <li id="req-number" class="text-gray-400">At least 1 number</li>
                                <li id="req-special" class="text-gray-400">At least 1 special character (@$!%*?&)</li>
                                <li id="req-match" class="text-gray-400">Passwords match</li>
                            </ul>
                        </div>
                    </div>
                    
                    <button onclick="resetPassword()" id="reset-password-btn"
                        class="w-full bg-gradient-to-r from-[#155386] to-[#40798C] text-white px-6 py-3 rounded-lg font-semibold 
                               hover:from-[#1F363D] hover:to-[#1F363D] transition-all duration-200
                               shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mb-4">
                        <span id="reset-password-text">Reset Password</span>
                        <span id="reset-password-spinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                
                <div id="modal-message" class="text-sm hidden"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Get CSRF token helper function
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           document.querySelector('input[name="_token"]')?.value;
}

// Rejected Modal functions
let rejectionReason = '';

function showRejectedModal(reason) {
    const modal = document.getElementById('rejected-modal');
    const reasonDisplay = document.getElementById('rejection-reason-display');
    if (reason) {
        reasonDisplay.textContent = reason;
    } else {
        reasonDisplay.textContent = 'Your application did not meet the required criteria. Please contact support for more information.';
    }
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function refreshLoginForm() {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.reset();
    }

    const errorDiv = document.getElementById('error-message');
    const warningDiv = document.getElementById('warning-message');
    const successDiv = document.getElementById('success-message');

    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
    if (warningDiv) {
        warningDiv.classList.add('hidden');
        warningDiv.textContent = '';
    }
    if (successDiv) {
        successDiv.classList.add('hidden');
        successDiv.textContent = '';
    }

    const loginBtn = document.getElementById('login-button');
    if (loginBtn) {
        loginBtn.disabled = false;
    }
}

function closeRejectedModal() {
    const modal = document.getElementById('rejected-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    refreshLoginForm();
    window.location.reload();
}

// Pending Approval Modal functions
function showPendingApprovalModal() {
    const modal = document.getElementById('pending-approval-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePendingApprovalModal() {
    const modal = document.getElementById('pending-approval-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    refreshLoginForm();
    window.location.reload();
}

// Password visibility toggle
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

// Enhanced message display functions
function showError(message) {
    const errorDiv = document.getElementById('error-message');
    const warningDiv = document.getElementById('warning-message');
    const successDiv = document.getElementById('success-message');
    
    errorDiv.classList.add('hidden');
    warningDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    setTimeout(() => errorDiv.classList.add('hidden'), 8000);
}

function showWarning(message) {
    const errorDiv = document.getElementById('error-message');
    const warningDiv = document.getElementById('warning-message');
    const successDiv = document.getElementById('success-message');
    
    errorDiv.classList.add('hidden');
    warningDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    warningDiv.textContent = message;
    warningDiv.classList.remove('hidden');
    setTimeout(() => warningDiv.classList.add('hidden'), 8000);
}

function showSuccess(message) {
    const errorDiv = document.getElementById('error-message');
    const warningDiv = document.getElementById('warning-message');
    const successDiv = document.getElementById('success-message');
    
    errorDiv.classList.add('hidden');
    warningDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    successDiv.textContent = message;
    successDiv.classList.remove('hidden');
    setTimeout(() => successDiv.classList.add('hidden'), 5000);
}

// Login form submission
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('warning-message').classList.add('hidden');
    document.getElementById('success-message').classList.add('hidden');
    showButtonLoading();
    
    const formData = new FormData(this);
    const loginValue = formData.get('login');
    const passwordValue = formData.get('password');
    
    try {
        const response = await fetch('{{ route("login") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                login: loginValue,
                password: passwordValue,
                remember: formData.get('remember') === 'on'
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showSuccess('Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("dashboard") }}';
            }, 1000);
        } else {
            hideButtonLoading();
            
            if (data.error && data.error.includes('rejected')) {
                showRejectedModal(data.rejection_reason || null);
                return;
            }
            
            if (data.error && data.error.includes('pending admin approval')) {
                showPendingApprovalModal();
                return;
            }
            
            if (data.redirect) {
                showWarning(data.error || 'Your account requires admin approval.');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
                return;
            }
            
            if (data.errors) {
                let errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(data.errors[field].join(', '));
                }
                showError(errorMessages.join('\n'));
            } else if (data.error) {
                const errorMessage = data.error;
                
                if (errorMessage.includes('rejected')) {
                    showRejectedModal(data.rejection_reason || null);
                } else if (errorMessage.includes('pending admin approval')) {
                    showPendingApprovalModal();
                } else if (errorMessage.includes('verify your email')) {
                    showWarning(errorMessage);
                    setTimeout(() => {
                        if (confirm('Would you like to resend the verification email?')) {
                            resendVerification(loginValue);
                        }
                    }, 1000);
                } else {
                    showError(errorMessage);
                }
            } else {
                showError('Invalid email/username or password.');
            }
        }
    } catch (error) {
        hideButtonLoading();
        showError('An error occurred. Please try again.');
        console.error('Login error:', error);
    }
});

async function resendVerification(email) {
    try {
        const response = await fetch('/resend-verification', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showSuccess('Verification email resent! Please check your inbox.');
        } else {
            showError(data.message || 'Failed to resend verification email.');
        }
    } catch (error) {
        showError('An error occurred. Please try again.');
    }
}

function showButtonLoading() {
    document.getElementById('button-text').classList.add('hidden');
    document.getElementById('button-spinner').classList.remove('hidden');
    document.getElementById('login-button').disabled = true;
}

function hideButtonLoading() {
    document.getElementById('button-text').classList.remove('hidden');
    document.getElementById('button-spinner').classList.add('hidden');
    document.getElementById('login-button').disabled = false;
}

// Forgot Password Modal variables
let resetEmail = '';
let resetToken = '';

function showForgotPassword() {
    document.getElementById('forgot-password-modal').classList.remove('hidden');
    document.getElementById('step-email').classList.remove('hidden');
    document.getElementById('step-code').classList.add('hidden');
    document.getElementById('step-password').classList.add('hidden');
    document.getElementById('reset-email').value = '';
    clearModalMessage();
}

function closeForgotPasswordModal() {
    document.getElementById('forgot-password-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    document.getElementById('step-email').classList.remove('hidden');
    document.getElementById('step-code').classList.add('hidden');
    document.getElementById('step-password').classList.add('hidden');
    
    document.getElementById('reset-email').value = '';
    for (let i = 1; i <= 6; i++) {
        const codeInput = document.getElementById(`code${i}`);
        if (codeInput) codeInput.value = '';
    }
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-password').value = '';
    
    const modalMessage = document.getElementById('modal-message');
    modalMessage.classList.add('hidden');
    modalMessage.innerHTML = '';
    
    resetEmail = '';
    resetToken = '';
}

function clearCodeInputs() {
    for (let i = 1; i <= 6; i++) {
        const input = document.getElementById(`code${i}`);
        if (input) input.value = '';
    }
}

function clearModalMessage() {
    const modalMsg = document.getElementById('modal-message');
    modalMsg.classList.add('hidden');
    modalMsg.textContent = '';
}

function showModalMessage(message, isError = true) {
    const modalMsg = document.getElementById('modal-message');
    modalMsg.textContent = message;
    modalMsg.className = `text-sm ${isError ? 'text-red-600' : 'text-green-600'} mt-2 text-center`;
    modalMsg.classList.remove('hidden');
}

async function sendResetCode() {
    const email = document.getElementById('reset-email').value;
    
    if (!email) {
        showModalMessage('Please enter your email address');
        return;
    }
    
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showModalMessage('Security token missing. Please refresh the page.');
        return;
    }
    
    document.getElementById('send-code-text').classList.add('hidden');
    document.getElementById('send-code-spinner').classList.remove('hidden');
    document.getElementById('send-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/send-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        document.getElementById('send-code-text').classList.remove('hidden');
        document.getElementById('send-code-spinner').classList.add('hidden');
        document.getElementById('send-code-btn').disabled = false;
        
        if (response.ok) {
            resetEmail = email;
            document.getElementById('code-email-display').textContent = email;
            document.getElementById('step-email').classList.add('hidden');
            document.getElementById('step-code').classList.remove('hidden');
            document.getElementById('code1').focus();
            showModalMessage('Code sent! Check your email.', false);
        } else if (response.status === 419) {
            showModalMessage('Session expired. Please refresh the page and try again.');
            setTimeout(() => location.reload(), 2000);
        } else {
            const errorMsg = data.error || data.message || 'Failed to send code';
            showModalMessage(errorMsg, true);
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('send-code-text').classList.remove('hidden');
        document.getElementById('send-code-spinner').classList.add('hidden');
        document.getElementById('send-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

async function verifyCode() {
    const code = 
        document.getElementById('code1').value +
        document.getElementById('code2').value +
        document.getElementById('code3').value +
        document.getElementById('code4').value +
        document.getElementById('code5').value +
        document.getElementById('code6').value;
    
    if (code.length !== 6) {
        showModalMessage('Please enter the 6-digit code');
        return;
    }
    
    const csrfToken = getCsrfToken();
    
    document.getElementById('verify-code-text').classList.add('hidden');
    document.getElementById('verify-code-spinner').classList.remove('hidden');
    document.getElementById('verify-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/verify-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                email: resetEmail, 
                code: code 
            })
        });
        
        const data = await response.json();
        
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        
        if (response.ok) {
            resetToken = data.token;
            document.getElementById('step-code').classList.add('hidden');
            document.getElementById('step-password').classList.remove('hidden');
            setupPasswordValidation();
            showModalMessage('Code verified! Enter your new password.', false);
        } else if (response.status === 419) {
            showModalMessage('Session expired. Please refresh the page and try again.');
            setTimeout(() => location.reload(), 2000);
        } else {
            showModalMessage(data.error || 'Invalid or expired code');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

async function resendCode() {
    if (!resetEmail) return;
    
    const csrfToken = getCsrfToken();
    
    document.getElementById('verify-code-text').classList.add('hidden');
    document.getElementById('verify-code-spinner').classList.remove('hidden');
    document.getElementById('verify-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/resend-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ email: resetEmail })
        });
        
        const data = await response.json();
        
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        
        if (response.ok) {
            clearCodeInputs();
            document.getElementById('code1').focus();
            showModalMessage('New code sent!', false);
        } else if (response.status === 419) {
            showModalMessage('Session expired. Please refresh the page and try again.');
            setTimeout(() => location.reload(), 2000);
        } else {
            showModalMessage(data.error || 'Failed to resend code');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

async function resetPassword() {
    const password = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;
    
    if (!password || !confirm) {
        showModalMessage('Please enter and confirm your new password');
        return;
    }
    
    if (password.length < 8 || password.length > 16) {
        showModalMessage('Password must be between 8 and 16 characters');
        return;
    }
    
    if (!/[A-Z]/.test(password)) {
        showModalMessage('Password must contain at least one uppercase letter');
        return;
    }
    
    if (!/[0-9]/.test(password)) {
        showModalMessage('Password must contain at least one number');
        return;
    }
    
    if (!/[@$!%*?&]/.test(password)) {
        showModalMessage('Password must contain at least one special character (@$!%*?&)');
        return;
    }
    
    if (password !== confirm) {
        showModalMessage('Passwords do not match');
        return;
    }
    
    const code = 
        document.getElementById('code1').value +
        document.getElementById('code2').value +
        document.getElementById('code3').value +
        document.getElementById('code4').value +
        document.getElementById('code5').value +
        document.getElementById('code6').value;
    
    const csrfToken = getCsrfToken();
    
    document.getElementById('reset-password-text').classList.add('hidden');
    document.getElementById('reset-password-spinner').classList.remove('hidden');
    document.getElementById('reset-password-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/reset', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email: resetEmail,
                code: code,
                token: resetToken,
                password: password,
                password_confirmation: confirm
            })
        });
        
        const data = await response.json();
        
        document.getElementById('reset-password-text').classList.remove('hidden');
        document.getElementById('reset-password-spinner').classList.add('hidden');
        document.getElementById('reset-password-btn').disabled = false;
        
        if (response.ok) {
            showModalMessage('Password reset successfully! Redirecting to login...', false);
            setTimeout(() => {
                closeForgotPasswordModal();
                showSuccess('Password reset successful! You can now login with your new password.');
            }, 2000);
        } else if (response.status === 419) {
            showModalMessage('Session expired. Please refresh the page and try again.');
            setTimeout(() => location.reload(), 2000);
        } else {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showModalMessage(errorMessages);
            } else {
                showModalMessage(data.error || 'Failed to reset password');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('reset-password-text').classList.remove('hidden');
        document.getElementById('reset-password-spinner').classList.add('hidden');
        document.getElementById('reset-password-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

function backToEmail() {
    document.getElementById('step-code').classList.add('hidden');
    document.getElementById('step-email').classList.remove('hidden');
    clearCodeInputs();
    clearModalMessage();
}

function moveToNext(current, nextId) {
    if (current.value.length === 1) {
        document.getElementById(nextId)?.focus();
    }
}

function setupPasswordValidation() {
    const password = document.getElementById('new-password');
    const confirm = document.getElementById('confirm-password');
    
    function validatePassword() {
        const pwd = password.value;
        const conf = confirm.value;
        
        document.getElementById('req-length').className = pwd.length >= 8 && pwd.length <= 16 ? 'text-green-600' : 'text-gray-400';
        document.getElementById('req-uppercase').className = /[A-Z]/.test(pwd) ? 'text-green-600' : 'text-gray-400';
        document.getElementById('req-number').className = /[0-9]/.test(pwd) ? 'text-green-600' : 'text-gray-400';
        document.getElementById('req-special').className = /[@$!%*?&]/.test(pwd) ? 'text-green-600' : 'text-gray-400';
        document.getElementById('req-match').className = pwd && conf && pwd === conf ? 'text-green-600' : 'text-gray-400';
    }
    
    password.addEventListener('input', validatePassword);
    confirm.addEventListener('input', validatePassword);
}

// Close modals when clicking outside
document.getElementById('pending-approval-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePendingApprovalModal();
    }
});

document.getElementById('rejected-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectedModal();
    }
});

document.getElementById('forgot-password-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeForgotPasswordModal();
    }
});

// Check for session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showSuccess('{{ session('success') }}');
    @endif
    @if(session('error'))
        showError('{{ session('error') }}');
    @endif
    @if(session('warning'))
        showWarning('{{ session('warning') }}');
    @endif
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

button:disabled {
    cursor: not-allowed;
    opacity: 0.75;
}

.code-input {   
    -moz-appearance: textfield;
}
.code-input::-webkit-outer-spin-button,
.code-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.step {
    transition: all 0.3s ease;
}

#warning-message {
    border-left: 4px solid #eab308;
}

#error-message {
    border-left: 4px solid #ef4444;
}

#success-message {
    border-left: 4px solid #22c55e;
}

#pending-approval-modal,
#rejected-modal {
    transition: opacity 0.2s ease-in-out;
}

#pending-approval-modal .bg-white,
#rejected-modal .bg-white {
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
</style>
@endsection