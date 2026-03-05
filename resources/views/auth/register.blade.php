@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')

<!-- Background Image Container -->
<div class="relative min-h-screen">
    <!-- Background Image -->
    <div class="fixed inset-0 z-0">
        <img 
            src="{{ asset('images/cover.jpg') }}" 
            alt="Background" 
            class="w-full h-full object-cover opacity-20"
        >
        <!-- Optional overlay for better contrast -->
        <div class="absolute inset-0 bg-white/30 backdrop-blur-[2px]"></div>
    </div>

    <!-- Content Container (scrollable) -->
    <div class="relative z-10 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header - OUTSIDE the white container (semi-transparent as you had it) -->
            <div class="text-center mb-8 bg-white/80 rounded-2xl p-6">
                <h1 class="text-3xl font-bold text-gray-900">Sign Up</h1>
                <p class="text-gray-600 mt-2">Create your account to get started with Konstructo</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Progress Bar inside white container -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between relative">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative z-10" id="step1-indicator">
                            <div class="w-14 h-14 rounded-full bg-[#40798C] text-black flex items-center justify-center font-bold text-xl shadow-xl transform transition-all duration-300 hover:scale-110" id="step1-circle" style="padding: 0.5rem;">
                                1
                            </div>
                            <span class="mt-3 text-sm font-semibold text-[#40798C] bg-white px-3 py-1 rounded-full shadow-sm" id="step1-text">Personal Info</span>
                            <div class="w-1 h-8 bg-[#40798C] mt-1 rounded-full" id="step1-line" style="display: none;"></div>
                        </div>
                        
                        <!-- Line 1 -->
                        <div class="flex-1 h-1.5 mx-2 bg-gradient-to-r from-[#1F363D] to-[#CFE0C3] rounded-full relative top-[-12px]" id="line-1">
                            <div class="h-1.5 bg-[#1F363D] rounded-full" id="progress-line-1" style="width: 0%"></div>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative z-10" id="step2-indicator">
                            <div class="w-14 h-14 rounded-full bg-[#CFE0C3] text-[#1F363D] flex items-center justify-center font-bold text-xl shadow-md transform transition-all duration-300 hover:scale-110" id="step2-circle" style="padding: 0.5rem;">
                                2
                            </div>
                            <span class="mt-3 text-sm font-medium text-[#40798C] bg-white px-3 py-1 rounded-full shadow-sm" id="step2-text">Account Details</span>
                            <div class="w-1 h-8 bg-[#CFE0C3] mt-1 rounded-full" id="step2-line" style="display: none;"></div>
                        </div>
                        
                        <!-- Line 2 -->
                        <div class="flex-1 h-1.5 mx-2 bg-[#CFE0C3] rounded-full relative top-[-12px]"></div>
                        
                        <!-- Step 3 -->
                        <div class="flex flex-col items-center relative z-10" id="step3-indicator">
                            <div class="w-14 h-14 rounded-full bg-[#CFE0C3] text-[#1F363D] flex items-center justify-center font-bold text-xl shadow-md transform transition-all duration-300 hover:scale-110" id="step3-circle" style="padding: 0.5rem;">
                                3
                            </div>
                            <span class="mt-3 text-sm font-medium text-[#40798C] bg-white px-3 py-1 rounded-full shadow-sm" id="step3-text">Review & Submit</span>
                            <div class="w-1 h-8 bg-[#CFE0C3] mt-1 rounded-full" id="step3-line" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Form inside white container -->
                <div class="p-8">
                    <!-- Success/Error Messages -->
                    <div id="success-message" class="hidden mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg"></div>
                    <div id="error-message" class="hidden mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg"></div>
                    <div id="validation-errors" class="hidden mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul id="error-list" class="list-disc list-inside"></ul>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registration-form">
                        @csrf

                        <!-- STEP 1: PERSONAL INFORMATION -->
                        <div id="step-1" class="step">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Personal Information</h2>
                            
                            <div class="space-y-6">
                                <!-- Name Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="first_name" placeholder="First Name" name="first_name" value="{{ old('first_name') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                        <input type="text" id="last_name" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Middle Name & Suffix -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                        <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name" value="{{ old('middle_name') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                                        <select id="suffix" name="suffix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">None</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">+63</span>
                                        <input type="tel" id="phone_number" name="phone_number" placeholder="9123456789" value="{{ old('phone_number') }}" required
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Format: 9123456789 (must start with 09 and be 11 digits)</p>
                                </div>

                                <!-- Address Row -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zip Code <span class="text-red-500">*</span></label>
                                        <input type="text" id="zip_code" placeholder="Zip Code" name="zip_code" value="{{ old('zip_code') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                        <input type="text" id="address" placeholder="Address" name="address" value="{{ old('address') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Next Button -->
                            <div class="mt-8 flex justify-end">
                                <button type="button" onclick="nextStep()" 
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                                           hover:bg-blue-700 transition-all duration-200
                                           shadow-lg hover:shadow-xl flex items-center gap-2">
                                    Next
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: ACCOUNT DETAILS -->
                        <div id="step-2" class="step hidden">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Account Details</h2>
                            
                            <div class="space-y-6">
                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" id="email" placeholder="Email Address" name="email" value="{{ old('email') }}" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Username -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                                    <input type="text" id="username" placeholder="Username" name="username" value="{{ old('username') }}" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Letters, numbers, dashes and underscores only</p>
                                </div>

                                <!-- Password Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                        <input type="password" id="password" placeholder="Password" name="password" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                                        <input type="password" id="password_confirmation" placeholder="Confirm Password" name="password_confirmation" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Password Requirements -->
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-700">Password requirements:</span><br>
                                        • 8-16 characters<br>
                                        • At least 1 uppercase letter<br>
                                        • At least 1 number<br>
                                        • At least 1 special character (@$!%*?&)
                                    </p>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="prevStep()" 
                                    class="px-6 py-3 border border-gray-300 rounded-lg font-semibold 
                                           text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                
                                <button type="button" onclick="nextStep()" 
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                                           hover:bg-blue-700 transition-all duration-200
                                           shadow-lg hover:shadow-xl flex items-center gap-2">
                                    Next
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: REVIEW & SUBMIT -->
                        <div id="step-3" class="step hidden">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Review Your Information</h2>
                            
                            <div class="space-y-6">
                                <!-- Personal Info Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3 flex items-center">
                                        <svg class="w-4 h-4 text-[#40798C] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Personal Information
                                    </h3>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div><span class="text-gray-500">Name:</span> <span id="review-name" class="font-medium"></span></div>
                                        <div><span class="text-gray-500">Phone:</span> <span id="review-phone" class="font-medium"></span></div>
                                        <div class="col-span-2"><span class="text-gray-500">Address:</span> <span id="review-address" class="font-medium"></span></div>
                                    </div>
                                </div>

                                <!-- Account Info Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-medium text-gray-700 mb-3 flex items-center">
                                        <svg class="w-4 h-4 text-[#40798C] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Account Information
                                    </h3>
                                    <div class="grid grid-cols-1 gap-2 text-sm">
                                        <div><span class="text-gray-500">Email:</span> <span id="review-email" class="font-medium"></span></div>
                                        <div><span class="text-gray-500">Username:</span> <span id="review-username" class="font-medium"></span></div>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="flex items-start">
                                    <input type="checkbox" id="terms" name="terms" required class="mt-1 w-4 h-4 text-[#40798C] rounded border-gray-300 focus:ring-[#40798C]">
                                    <label for="terms" class="ml-2 text-sm text-gray-600">
                                        By continuing, you agree to our 
                                        <a href="#" class="text-[#40798C] hover:underline">Terms of Service</a> and 
                                        <a href="#" class="text-[#40798C] hover:underline">Privacy Policy</a>.
                                    </label>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="mt-8 flex justify-between">
                                <button type="button" onclick="prevStep()" 
                                    class="px-6 py-3 border border-gray-300 rounded-lg font-semibold 
                                           text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                
                                <button type="submit" id="submit-btn"
                                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                                           hover:bg-green-700 transition-all duration-200
                                           shadow-lg hover:shadow-xl flex items-center justify-center gap-2 min-w-[180px]">
                                    <span class="inline-flex items-center gap-2" id="button-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Create Account
                                    </span>
                                    <span class="hidden" id="button-spinner">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Login Link (outside white container) -->
            <p class="text-center mt-6 text-sm text-gray-600 bg-white/80 backdrop-blur-sm rounded-lg p-3">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Log in here</a>
            </p>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verification-modal" class="fixed inset-0 bg-white overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center">
                <!-- Close button -->
                <button onclick="closeVerificationModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Mail Icon -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-4">
                    <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h3>
                <p class="text-gray-600 mb-2">
                    We've sent a 6-digit verification code to
                </p>
                <p class="text-sm font-semibold text-blue-600 mb-6" id="modal-email"></p>
                
                <!-- Verification Code Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Enter the 6-digit code
                    </label>
                    <div class="flex gap-2 justify-center">
                        <input type="text" id="code1" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeyup="moveToNext(this, 'code2')">
                        <input type="text" id="code2" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeyup="moveToNext(this, 'code3')">
                        <input type="text" id="code3" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeyup="moveToNext(this, 'code4')">
                        <input type="text" id="code4" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeyup="moveToNext(this, 'code5')">
                        <input type="text" id="code5" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onkeyup="moveToNext(this, 'code6')">
                        <input type="text" id="code6" maxlength="1" 
                            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    
                    <div id="verification-error" class="text-sm text-red-600 mt-2 hidden"></div>
                    <div id="verification-success" class="text-sm text-green-600 mt-2 hidden"></div>
                </div>
                
                <!-- Verify Button -->
                <button onclick="verifyEmail()" id="verify-btn"
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold 
                           hover:bg-blue-700 transition-all duration-200
                           shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mb-4">
                    <span id="verify-btn-text">Verify Email</span>
                    <span id="verify-btn-spinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                
                <!-- Resend Code -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Didn't receive the code? 
                        <button onclick="resendCode()" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                            Resend Code
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Loading Overlay (for major operations) -->
<div id="global-loading" class="fixed inset-0 bg-black bg-opacity-30 overflow-y-auto h-full w-full hidden z-[100]">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-40">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
            <p class="text-white text-sm font-medium" id="global-loading-text">Processing...</p>
        </div>
    </div>
</div>

<!-- JavaScript for Multi-Step Form and Verification -->
<script>
let currentStep = 1;
let registeredEmail = '';
let registeredUserId = '';

function updateProgress() {
    // Update circles
    for (let i = 1; i <= 3; i++) {
        const circle = document.getElementById(`step${i}-circle`);
        const text = document.getElementById(`step${i}-text`);
        
        if (i < currentStep) {
            // Completed steps - GREEN
            circle.className = 'w-14 h-14 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xl shadow-xl';
            circle.innerHTML = '✓';
            text.className = 'mt-3 text-sm font-semibold text-green-600 bg-white px-3 py-1 rounded-full shadow-sm';
        } else if (i === currentStep) {
            // Current step - #40798C
            circle.className = 'w-14 h-14 rounded-full bg-[#40798C] text-white flex items-center justify-center font-bold text-xl shadow-xl transform scale-110';
            circle.innerHTML = i;
            text.className = 'mt-3 text-sm font-semibold text-[#40798C] bg-white px-3 py-1 rounded-full shadow-md';
        } else {
            // Future steps
            circle.className = 'w-14 h-14 rounded-full bg-[#CFE0C3] text-[#1F363D] flex items-center justify-center font-bold text-xl shadow-md';
            circle.innerHTML = i;
            text.className = 'mt-3 text-sm font-medium text-[#40798C] bg-white px-3 py-1 rounded-full shadow-sm';
        }
        
        // Add padding to all circles
        circle.style.padding = '0.5rem';
    }
    
    // Update progress lines
    const progressLine1 = document.getElementById('progress-line-1');
    if (progressLine1) {
        if (currentStep === 1) {
            progressLine1.style.width = '0%';
        } else if (currentStep === 2) {
            progressLine1.style.width = '100%';
        } else if (currentStep === 3) {
            progressLine1.style.width = '100%';
        }
    }
    
    // Update line colors
    const line1 = document.getElementById('line-1');
    if (line1) {
        if (currentStep >= 2) {
            line1.className = 'flex-1 h-1.5 mx-2 bg-gradient-to-r from-green-500 to-[#CFE0C3] rounded-full relative top-[-12px]';
        } else {
            line1.className = 'flex-1 h-1.5 mx-2 bg-gradient-to-r from-[#40798C] to-[#CFE0C3] rounded-full relative top-[-12px]';
        }
    }
}

function updateReviewSummary() {
    // Update personal info summary
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const middleName = document.getElementById('middle_name').value;
    const suffix = document.getElementById('suffix').value;
    
    let fullName = firstName + ' ' + lastName;
    if (middleName) fullName = firstName + ' ' + middleName + ' ' + lastName;
    if (suffix) fullName += ', ' + suffix;
    
    document.getElementById('review-name').textContent = fullName;
    
    const phone = document.getElementById('phone_number').value;
    document.getElementById('review-phone').textContent = '+63 ' + phone;
    
    const address = document.getElementById('address').value + ', ' + document.getElementById('zip_code').value;
    document.getElementById('review-address').textContent = address;
    
    // Update account info summary
    document.getElementById('review-email').textContent = document.getElementById('email').value;
    document.getElementById('review-username').textContent = document.getElementById('username').value;
}

function nextStep() {
    // Validate current step before proceeding
    if (currentStep === 1) {
        const required = ['first_name', 'last_name', 'phone_number', 'zip_code', 'address'];
        for (let field of required) {
            const input = document.getElementById(field);
            if (!input.value) {
                showError('Please fill in all required fields in Personal Information');
                input.focus();
                return;
            }
        }
        
        // Validate phone number format
        const phone = document.getElementById('phone_number').value;
        if (!phone.match(/^09\d{9}$/)) {
            showError('Phone number must start with 09 and be 11 digits');
            return;
        }
    }
   if (currentStep === 2) {
    const required = ['email', 'username', 'password'];
    for (let field of required) {
        const input = document.getElementById(field);
        if (!input.value) {
            showError('Please fill in all required fields in Account Details');
            input.focus();
            return;
        }
    }
    
    // Validate email format
    const email = document.getElementById('email').value;
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showError('Please enter a valid email address');
        return;
    }
    
    // Validate username format
    const username = document.getElementById('username').value;
    if (!username.match(/^[a-zA-Z0-9_-]+$/)) {
        showError('Username may only contain letters, numbers, dashes and underscores');
        return;
    }
    
    // Validate password with exact backend requirements
    const password = document.getElementById('password').value;
    
    // Check length first
    if (password.length < 8 || password.length > 16) {
        showError('Password must be between 8 and 16 characters');
        return;
    }
    
    // Check for uppercase
    if (!/[A-Z]/.test(password)) {
        showError('Password must contain at least one uppercase letter');
        return;
    }
    
    // Check for number
    if (!/[0-9]/.test(password)) {
        showError('Password must contain at least one number');
        return;
    }
    
    // Check for special character - IMPORTANT: Make sure this regex matches exactly
    if (!/[@$!%*?&]/.test(password)) {
        showError('Password must contain at least one special character (@$!%*?&)');
        return;
    }
    
    // Validate password match
    const confirm = document.getElementById('password_confirmation').value;
    if (password !== confirm) {
        showError('Passwords do not match!');
        return;
    }
}
    if (currentStep < 3) {
        // Hide current step
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        
        // Show next step
        currentStep++;
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        
        // If moving to step 3, update review summary
        if (currentStep === 3) {
            updateReviewSummary();
        }
        
        updateProgress();
        
        // Scroll to top of form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    if (currentStep > 1) {
        // Hide current step
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        
        // Show previous step
        currentStep--;
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        
        updateProgress();
        
        // Scroll to top of form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    
    setTimeout(() => {
        errorDiv.classList.add('hidden');
    }, 5000);
}

function showSuccess(message) {
    const successDiv = document.getElementById('success-message');
    successDiv.textContent = message;
    successDiv.classList.remove('hidden');
    
    setTimeout(() => {
        successDiv.classList.add('hidden');
    }, 5000);
}

function showValidationErrors(errors) {
    const errorList = document.getElementById('error-list');
    const errorDiv = document.getElementById('validation-errors');
    
    errorList.innerHTML = '';
    for (let field in errors) {
        errors[field].forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
    }
    
    errorDiv.classList.remove('hidden');
    
    setTimeout(() => {
        errorDiv.classList.add('hidden');
    }, 5000);
}

// Show loading on submit button
function showButtonLoading() {
    const buttonText = document.getElementById('button-text');
    const buttonSpinner = document.getElementById('button-spinner');
    const submitBtn = document.getElementById('submit-btn');
    
    buttonText.classList.add('hidden');
    buttonSpinner.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
}

// Hide loading on submit button
function hideButtonLoading() {
    const buttonText = document.getElementById('button-text');
    const buttonSpinner = document.getElementById('button-spinner');
    const submitBtn = document.getElementById('submit-btn');
    
    buttonText.classList.remove('hidden');
    buttonSpinner.classList.add('hidden');
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
}

// Show global loading overlay
function showGlobalLoading(message = 'Processing...') {
    document.getElementById('global-loading-text').textContent = message;
    document.getElementById('global-loading').classList.remove('hidden');
}

// Hide global loading overlay
function hideGlobalLoading() {
    document.getElementById('global-loading').classList.add('hidden');
}

// Show verify button loading
function showVerifyButtonLoading() {
    const btnText = document.getElementById('verify-btn-text');
    const btnSpinner = document.getElementById('verify-btn-spinner');
    const verifyBtn = document.getElementById('verify-btn');
    
    btnText.classList.add('hidden');
    btnSpinner.classList.remove('hidden');
    verifyBtn.disabled = true;
    verifyBtn.classList.add('opacity-75', 'cursor-not-allowed');
}

// Hide verify button loading
function hideVerifyButtonLoading() {
    const btnText = document.getElementById('verify-btn-text');
    const btnSpinner = document.getElementById('verify-btn-spinner');
    const verifyBtn = document.getElementById('verify-btn');
    
    btnText.classList.remove('hidden');
    btnSpinner.classList.add('hidden');
    verifyBtn.disabled = false;
    verifyBtn.classList.remove('opacity-75', 'cursor-not-allowed');
}

// Close verification modal
function closeVerificationModal() {
    document.getElementById('verification-modal').classList.add('hidden');
}

// Handle form submission
document.getElementById('registration-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!document.getElementById('terms').checked) {
        showError('Please agree to the Terms of Service and Privacy Policy');
        return;
    }
    
    // Show loading on button
    showButtonLoading();
    
    // Collect form data
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Registration successful
            registeredEmail = data.email;
            registeredUserId = data.user.id;
            
            // Hide button loading
            hideButtonLoading();
            
            // Set email in verification modal
            document.getElementById('modal-email').textContent = registeredEmail;
            
            // Clear any previous verification inputs
            for (let i = 1; i <= 6; i++) {
                document.getElementById(`code${i}`).value = '';
            }
            
            // Show verification modal directly
            document.getElementById('verification-modal').classList.remove('hidden');
            
            showSuccess('Registration successful! Please check your email for verification code.');
            
        } else {
            hideButtonLoading();
            
            if (data.errors) {
                showValidationErrors(data.errors);
            } else {
                showError(data.message || 'Registration failed. Please try again.');
            }
        }
    } catch (error) {
        hideButtonLoading();
        showError('An error occurred. Please try again.');
        console.error('Error:', error);
    }
});

// Move to next input field
function moveToNext(current, nextId) {
    if (current.value.length === 1) {
        document.getElementById(nextId)?.focus();
    }
}

// Verify email
async function verifyEmail() {
    // Collect code
    let code = '';
    for (let i = 1; i <= 6; i++) {
        code += document.getElementById(`code${i}`).value;
    }
    
    if (code.length !== 6) {
        document.getElementById('verification-error').textContent = 'Please enter the 6-digit code';
        document.getElementById('verification-error').classList.remove('hidden');
        return;
    }
    
    // Hide any previous messages
    document.getElementById('verification-error').classList.add('hidden');
    document.getElementById('verification-success').classList.add('hidden');
    
    // Show verify button loading
    showVerifyButtonLoading();
    
    try {
        const response = await fetch('/verify-email', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: registeredEmail,
                code: code
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            document.getElementById('verification-success').textContent = 'Email verified successfully! Redirecting to login...';
            document.getElementById('verification-success').classList.remove('hidden');
            
            // Show global loading before redirect
            showGlobalLoading('Verification successful! Redirecting...');
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            hideVerifyButtonLoading();
            document.getElementById('verification-error').textContent = data.message || 'Invalid verification code';
            document.getElementById('verification-error').classList.remove('hidden');
        }
    } catch (error) {
        hideVerifyButtonLoading();
        document.getElementById('verification-error').textContent = 'An error occurred. Please try again.';
        document.getElementById('verification-error').classList.remove('hidden');
        console.error('Error:', error);
    }
}

// Resend verification code
async function resendCode() {
    document.getElementById('verification-error').classList.add('hidden');
    document.getElementById('verification-success').classList.add('hidden');
    
    try {
        const response = await fetch('/resend-verification', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: registeredEmail
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            document.getElementById('verification-success').textContent = 'New verification code sent!';
            document.getElementById('verification-success').classList.remove('hidden');
            
            // Clear code inputs
            for (let i = 1; i <= 6; i++) {
                document.getElementById(`code${i}`).value = '';
            }
            document.getElementById('code1').focus();
        } else {
            document.getElementById('verification-error').textContent = data.message || 'Failed to resend code';
            document.getElementById('verification-error').classList.remove('hidden');
        }
    } catch (error) {
        document.getElementById('verification-error').textContent = 'An error occurred. Please try again.';
        document.getElementById('verification-error').classList.remove('hidden');
        console.error('Error:', error);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const verificationModal = document.getElementById('verification-modal');
    
    if (event.target === verificationModal) {
        verificationModal.classList.add('hidden');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Fix for the broken phone input span
    const phoneSpan = document.querySelector('.rounded-l-lg.border.border-r-0');
    if (phoneSpan) {
        phoneSpan.className = 'inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm';
    }
});
</script>

<!-- Styles for multi-step form and modal -->
<style>
.step {
    transition: all 0.3s ease;
}

/* Modal animation */
#verification-modal, #global-loading {
    transition: opacity 0.3s ease;
}

/* Code input styling */
input[type="text"].text-center {
    -moz-appearance: textfield;
}
input[type="text"].text-center::-webkit-outer-spin-button,
input[type="text"].text-center::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Disabled button styling */
button:disabled {
    cursor: not-allowed;
}

/* Animation for spinner */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection