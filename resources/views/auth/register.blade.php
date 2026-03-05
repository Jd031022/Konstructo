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
                                        <input type="tel" id="phone" name="phone" placeholder="9123456789" value="{{ old('phone') }}" required
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Format: 9123456789 (10 digits)</p>
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
                                        • Minimum 8 characters<br>
                                        • Maximum 16 characters<br>
                                        • At least 1 capital letter<br>
                                        • At least 1 number<br>
                                        • At least 1 special character
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
        
        <button type="submit" 
            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                   hover:bg-green-700 transition-all duration-200
                   shadow-lg hover:shadow-xl flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Create Account
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

<!-- JavaScript for Multi-Step Form -->
<script>
let currentStep = 1;

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
    
    const phone = document.getElementById('phone').value;
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
        const required = ['first_name', 'last_name', 'phone', 'zip_code', 'address'];
        for (let field of required) {
            const input = document.getElementById(field);
            if (!input.value) {
                alert('Please fill in all required fields in Personal Information');
                input.focus();
                return;
            }
        }
    }
    
    if (currentStep === 2) {
        const required = ['email', 'username', 'password'];
        for (let field of required) {
            const input = document.getElementById(field);
            if (!input.value) {
                alert('Please fill in all required fields in Account Details');
                input.focus();
                return;
            }
        }
        
        // Validate password match
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        if (password !== confirm) {
            alert('Passwords do not match!');
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

<!-- Styles for multi-step form -->
<style>
.step {
    transition: all 0.3s ease;
}
</style>
@endsection