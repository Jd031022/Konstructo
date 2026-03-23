@extends('layouts.guest')

@section('title', 'Verify Email - Konstructo')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verify your Email</h1>
            <p class="text-gray-600 text-sm" id="email-display">
                {{ session('verification_email', Auth::user()->email ?? 'your email') }}
            </p>
        </div>

        <!-- Verification Form -->
        <form method="POST" action="{{ route('verification.send') }}" id="verification-form" class="space-y-6">
            @csrf
            
            <!-- Code Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Enter the 6-digit code we sent to your inbox
                </label>
                <div class="flex gap-2 justify-center">
                    @for($i = 0; $i < 6; $i++)
                    <input 
                        type="text" 
                        name="code[]"
                        maxlength="1"
                        class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        onkeyup="if(this.value.length === 1 && this.nextElementSibling) this.nextElementSibling.focus()"
                        required
                    >
                    @endfor
                </div>
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Verify Button -->
            <button 
                type="submit" 
                id="verify-btn"
                class="w-full bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                       hover:bg-green-700 transition-all duration-200
                       shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
            >
                <span id="verify-btn-text">Verify</span>
                <span id="verify-btn-spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </form>

        <!-- Footer Links -->
        <div class="mt-6 space-y-4">
            <!-- Resend Code -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Didn't receive the code? 
                    <form method="POST" action="{{ route('verification.send') }}" class="inline" id="resend-form">
                        @csrf
                        <button type="submit" id="resend-btn" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors">
                            Resend Code
                        </button>
                        <span id="resend-spinner" class="hidden">
                            <svg class="animate-spin inline h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </form>
                </p>
            </div>

            <!-- Wrong Email / Logout -->
            <div class="text-center border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-2">WRONG EMAIL ADDRESS?</p>
                <a 
                    href="{{ route('logout') }}" 
                    class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 font-semibold transition-colors"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Log Out & Return
                </a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 z-50">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toast-message">Email verified successfully!</span>
    </div>
</div>

<!-- Error Toast Notification -->
<div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 z-50">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span id="error-toast-message">Error message</span>
    </div>
</div>

<!-- JavaScript for code inputs and AJAX verification -->
<script>
    // Store the user email
    let userEmail = '{{ session('verification_email', Auth::user()->email ?? '') }}';
    
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.code-input');
        
        // Focus first input
        if (inputs.length > 0) {
            inputs[0].focus();
        }
        
        // Handle paste event
        const firstInput = inputs[0];
        if (firstInput) {
            firstInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/\D/g, '').split('');
                
                digits.forEach((digit, index) => {
                    if (inputs[index]) {
                        inputs[index].value = digit;
                        if (index === digits.length - 1) {
                            inputs[index].focus();
                        }
                    }
                });
                
                // Auto-submit if all 6 digits are filled
                if (digits.length === 6) {
                    setTimeout(() => {
                        document.getElementById('verification-form').dispatchEvent(new Event('submit'));
                    }, 100);
                }
            });
        }
        
        // Handle backspace to move to previous input
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            // Auto-submit when all inputs are filled
            input.addEventListener('input', function() {
                let allFilled = true;
                inputs.forEach(inp => {
                    if (inp.value.length === 0) allFilled = false;
                });
                if (allFilled) {
                    document.getElementById('verification-form').dispatchEvent(new Event('submit'));
                }
            });
        });
    });
    
    // Handle form submission with AJAX
    document.getElementById('verification-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Collect code from inputs
        const inputs = document.querySelectorAll('.code-input');
        let code = '';
        inputs.forEach(input => {
            code += input.value;
        });
        
        if (code.length !== 6) {
            showErrorToast('Please enter the complete 6-digit verification code.');
            return;
        }
        
        // Show loading state
        const verifyBtn = document.getElementById('verify-btn');
        const btnText = document.getElementById('verify-btn-text');
        const btnSpinner = document.getElementById('verify-btn-spinner');
        
        btnText.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        verifyBtn.disabled = true;
        
        try {
            const response = await fetch('/verify-email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: userEmail,
                    code: code
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Show success toast
                showSuccessToast('Email verified successfully! Redirecting to account status...');
                
                // Redirect to the URL provided by the server
                const redirectUrl = data.redirect || '/applicant/account-status';
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 2000);
            } else {
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                verifyBtn.disabled = false;
                
                showErrorToast(data.message || 'Invalid verification code. Please try again.');
                
                // Clear inputs on error
                inputs.forEach(input => {
                    input.value = '';
                });
                inputs[0].focus();
            }
        } catch (error) {
            btnText.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
            verifyBtn.disabled = false;
            showErrorToast('An error occurred. Please try again.');
            console.error('Error:', error);
        }
    });
    
    // Handle resend code with AJAX
    document.getElementById('resend-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const resendBtn = document.getElementById('resend-btn');
        const resendSpinner = document.getElementById('resend-spinner');
        
        resendBtn.classList.add('hidden');
        resendSpinner.classList.remove('hidden');
        
        try {
            const response = await fetch('/resend-verification', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: userEmail
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showSuccessToast('New verification code sent! Please check your email.');
                
                // Clear inputs for new code
                const inputs = document.querySelectorAll('.code-input');
                inputs.forEach(input => {
                    input.value = '';
                });
                inputs[0].focus();
            } else {
                showErrorToast(data.message || 'Failed to resend code. Please try again.');
            }
        } catch (error) {
            showErrorToast('An error occurred. Please try again.');
            console.error('Error:', error);
        } finally {
            resendBtn.classList.remove('hidden');
            resendSpinner.classList.add('hidden');
        }
    });
    
    // Show success toast
    function showSuccessToast(message) {
        const toast = document.getElementById('success-toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            toast.classList.remove('translate-x-0', 'opacity-100');
        }, 3000);
    }
    
    // Show error toast
    function showErrorToast(message) {
        const toast = document.getElementById('error-toast');
        const toastMessage = document.getElementById('error-toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            toast.classList.remove('translate-x-0', 'opacity-100');
        }, 3000);
    }
</script>

<style>
    /* Code input styling */
    .code-input {   
        -moz-appearance: textfield;
    }
    .code-input::-webkit-outer-spin-button,
    .code-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    /* Disabled button styling */
    button:disabled {
        cursor: not-allowed;
        opacity: 0.75;
    }
    
    /* Animation for spinner */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Toast animations */
    .translate-x-full {
        transform: translateX(100%);
    }
    
    .translate-x-0 {
        transform: translateX(0);
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>
@endsection