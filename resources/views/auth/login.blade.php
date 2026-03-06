@extends('layouts.app')

@section('title', 'Login - Konstructo')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 relative">

    <!-- Background Illustration -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" 
             class="w-full h-full object-cover opacity-90 brightness-75" 
             alt="background">
    </div>

    <!-- Login Card -->
    <div class="relative bg-white rounded-xl shadow-lg p-8" style="width: 700px; min-height: 600px; padding: 80px 160px;">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo.png') }}" class="w-12 mb-2">
            <h1 class="text-xl font-semibold text-gray-700">Konstructo</h1>
            <p class="text-sm text-gray-500">Login to continue</p>
        </div>

        <!-- Error Message Display -->
        <div id="error-message" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm"></div>
        
        <!-- Success Message Display -->
        <div id="success-message" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm"></div>

        <!-- Login Form -->
        <form id="login-form" method="POST">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Email</label>
                <input type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email here"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Password</label>
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
            
            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between text-sm mb-4">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me
                </label>
                <a href="#" class="text-gray-400 hover:text-gray-600 text-xs" onclick="showForgotPassword()">
                    Forgot Password?
                </a>
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

<!-- Forgot Password Modal - Step 1: Request Code -->
<div id="forgot-password-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center">
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h3>
                <p class="text-gray-600 mb-6" id="modal-step-description">
                    Enter your email address to receive a verification code.
                </p>
                
                <!-- Step 1: Email Input -->
                <div id="step-email" class="step">
                    <div class="mb-4">
                        <input type="email" 
                               id="reset-email" 
                               placeholder="Enter your email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    
                    <button onclick="sendResetCode()" id="send-code-btn"
                        class="w-full bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold 
                               hover:bg-teal-800 transition-all duration-200
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
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">We've sent a 6-digit code to:</p>
                        <p class="text-sm font-semibold text-teal-700 mb-4" id="code-email-display"></p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter Code</label>
                        <div class="flex gap-2 justify-center mb-2">
                            <input type="text" id="code1" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code2')">
                            <input type="text" id="code2" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code3')">
                            <input type="text" id="code3" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code4')">
                            <input type="text" id="code4" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code5')">
                            <input type="text" id="code5" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="moveToNext(this, 'code6')">
                            <input type="text" id="code6" maxlength="1" 
                                class="code-input w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                onkeyup="if(this.value.length === 1) verifyCode()">
                        </div>
                        
                        <div class="flex justify-between mt-2">
                            <button onclick="resendCode()" class="text-sm text-teal-600 hover:text-teal-800">
                                Resend Code
                            </button>
                            <button onclick="backToEmail()" class="text-sm text-gray-500 hover:text-gray-700">
                                Change Email
                            </button>
                        </div>
                    </div>
                    
                    <button onclick="verifyCode()" id="verify-code-btn"
                        class="w-full bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold 
                               hover:bg-teal-800 transition-all duration-200
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
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 mb-2">
                            <input type="password" 
                                   id="confirm-password" 
                                   placeholder="Confirm New Password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
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
                        class="w-full bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold 
                               hover:bg-teal-800 transition-all duration-200
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
                
                <!-- Modal Message -->
                <div id="modal-message" class="text-sm hidden"></div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
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

// Login form submission
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('success-message').classList.add('hidden');
    showButtonLoading();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("login") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
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
            
            if (data.errors) {
                let errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(data.errors[field].join(', '));
                }
                showError(errorMessages.join('\n'));
            } else {
                showError(data.error || 'Invalid email or password.');
            }
        }
    } catch (error) {
        hideButtonLoading();
        showError('An error occurred. Please try again.');
    }
});

// Button loading states
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

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    setTimeout(() => errorDiv.classList.add('hidden'), 5000);
}

function showSuccess(message) {
    const successDiv = document.getElementById('success-message');
    successDiv.textContent = message;
    successDiv.classList.remove('hidden');
    setTimeout(() => successDiv.classList.add('hidden'), 5000);
}

// Modal controls
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

function closeForgotModal() {
    document.getElementById('forgot-password-modal').classList.add('hidden');
    resetEmail = '';
    resetToken = '';
    clearCodeInputs();
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
    modalMsg.className = `text-sm ${isError ? 'text-red-600' : 'text-green-600'} mt-2`;
    modalMsg.classList.remove('hidden');
}

// Step 1: Send reset code
async function sendResetCode() {
    const email = document.getElementById('reset-email').value;
    
    if (!email) {
        showModalMessage('Please enter your email address');
        return;
    }
    
    // Show loading
    document.getElementById('send-code-text').classList.add('hidden');
    document.getElementById('send-code-spinner').classList.remove('hidden');
    document.getElementById('send-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/send-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email })
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
        } else {
            showModalMessage(data.error || data.message || 'Failed to send code');
        }
    } catch (error) {
        document.getElementById('send-code-text').classList.remove('hidden');
        document.getElementById('send-code-spinner').classList.add('hidden');
        document.getElementById('send-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

// Step 2: Verify code
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
    
    // Show loading
    document.getElementById('verify-code-text').classList.add('hidden');
    document.getElementById('verify-code-spinner').classList.remove('hidden');
    document.getElementById('verify-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/verify-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                email: resetEmail, 
                code 
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
        } else {
            showModalMessage(data.error || 'Invalid or expired code');
        }
    } catch (error) {
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

// Step 3: Reset password
async function resetPassword() {
    const password = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;
    
    // Validation
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
    
    // Get the code from inputs
    const code = 
        document.getElementById('code1').value +
        document.getElementById('code2').value +
        document.getElementById('code3').value +
        document.getElementById('code4').value +
        document.getElementById('code5').value +
        document.getElementById('code6').value;
    
    // Show loading
    document.getElementById('reset-password-text').classList.add('hidden');
    document.getElementById('reset-password-spinner').classList.remove('hidden');
    document.getElementById('reset-password-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/reset', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
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
                closeForgotModal();
                showSuccess('Password reset successful! You can now login with your new password.');
            }, 2000);
        } else {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showModalMessage(errorMessages);
            } else {
                showModalMessage(data.error || 'Failed to reset password');
            }
        }
    } catch (error) {
        document.getElementById('reset-password-text').classList.remove('hidden');
        document.getElementById('reset-password-spinner').classList.add('hidden');
        document.getElementById('reset-password-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

// Resend code
async function resendCode() {
    if (!resetEmail) return;
    
    document.getElementById('verify-code-text').classList.add('hidden');
    document.getElementById('verify-code-spinner').classList.remove('hidden');
    document.getElementById('verify-code-btn').disabled = true;
    clearModalMessage();
    
    try {
        const response = await fetch('/forgot-password/resend-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
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
        } else {
            showModalMessage(data.error || 'Failed to resend code');
        }
    } catch (error) {
        document.getElementById('verify-code-text').classList.remove('hidden');
        document.getElementById('verify-code-spinner').classList.add('hidden');
        document.getElementById('verify-code-btn').disabled = false;
        showModalMessage('An error occurred. Please try again.');
    }
}

// Navigation
function backToEmail() {
    document.getElementById('step-code').classList.add('hidden');
    document.getElementById('step-email').classList.remove('hidden');
    clearCodeInputs();
    clearModalMessage();
}

// Code input navigation
function moveToNext(current, nextId) {
    if (current.value.length === 1) {
        document.getElementById(nextId)?.focus();
    }
}

// Password validation on input
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

// Initialize password validation when step 3 loads
</script>

<style>
/* Animation for spinner */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Disabled button styling */
button:disabled {
    cursor: not-allowed;
    opacity: 0.75;
}

/* Code input styling */
.code-input {
    -moz-appearance: textfield;
}
.code-input::-webkit-outer-spin-button,
.code-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Step transitions */
.step {
    transition: all 0.3s ease;
}
</style>
@endsection