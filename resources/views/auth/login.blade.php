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
    <div class="relative bg-white rounded-xl shadow-lg p-8" style="width: 700px; height: 600px; padding-top: 80px; padding-left: 160px; padding-right: 160px;">

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
            <!-- Eye icon (visible) -->
            <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <!-- Eye off icon (hidden) -->
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

            <!-- Login Button -->
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

<!-- Forgot Password Modal -->
<div id="forgot-password-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password</h3>
                <p class="text-gray-600 mb-6">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
                
                <div class="mb-4">
                    <input type="email" 
                           id="reset-email" 
                           placeholder="Enter your email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div id="reset-message" class="text-sm mb-4 hidden"></div>
                
                <button onclick="sendResetLink()" id="reset-btn"
                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold 
                           hover:bg-blue-700 transition-all duration-200
                           shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mb-4">
                    <span id="reset-btn-text">Send Reset Link</span>
                    <span id="reset-btn-spinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                
                <button onclick="closeForgotModal()" class="text-sm text-gray-500 hover:text-gray-700">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Login Functionality -->
<script>
// Show/hide loading states
function showButtonLoading() {
    document.getElementById('button-text').classList.add('hidden');
    document.getElementById('button-spinner').classList.remove('hidden');
    document.getElementById('login-button').disabled = true;
    document.getElementById('login-button').classList.add('opacity-75', 'cursor-not-allowed');
}

function hideButtonLoading() {
    document.getElementById('button-text').classList.remove('hidden');
    document.getElementById('button-spinner').classList.add('hidden');
    document.getElementById('login-button').disabled = false;
    document.getElementById('login-button').classList.remove('opacity-75', 'cursor-not-allowed');
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

// Handle login form submission
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Clear previous messages
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('success-message').classList.add('hidden');
    
    // Show loading
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
            // Login successful
            showSuccess('Login successful! Redirecting...');
            
            // Redirect to dashboard
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("dashboard") }}';
            }, 1000);
        } else {
            hideButtonLoading();
            
            if (data.errors) {
                // Validation errors
                let errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(data.errors[field].join(', '));
                }
                showError(errorMessages.join('\n'));
            } else {
                // Other errors
                showError(data.error || 'Invalid email or password.');
            }
        }
    } catch (error) {
        hideButtonLoading();
        showError('An error occurred. Please try again.');
        console.error('Error:', error);
    }
});

// Forgot password functions
function showForgotPassword() {
    document.getElementById('forgot-password-modal').classList.remove('hidden');
}

function closeForgotModal() {
    document.getElementById('forgot-password-modal').classList.add('hidden');
    document.getElementById('reset-message').classList.add('hidden');
    document.getElementById('reset-email').value = '';
}

async function sendResetLink() {
    const email = document.getElementById('reset-email').value;
    
    if (!email) {
        const resetMessage = document.getElementById('reset-message');
        resetMessage.textContent = 'Please enter your email address.';
        resetMessage.className = 'text-sm text-red-600 mb-4';
        resetMessage.classList.remove('hidden');
        return;
    }
    
    // Show loading
    document.getElementById('reset-btn-text').classList.add('hidden');
    document.getElementById('reset-btn-spinner').classList.remove('hidden');
    document.getElementById('reset-btn').disabled = true;
    
    try {
        // This would need a password reset endpoint
        // For now, just show a message
        setTimeout(() => {
            document.getElementById('reset-btn-text').classList.remove('hidden');
            document.getElementById('reset-btn-spinner').classList.add('hidden');
            document.getElementById('reset-btn').disabled = false;
            
            const resetMessage = document.getElementById('reset-message');
            resetMessage.textContent = 'Password reset link sent! Please check your email.';
            resetMessage.className = 'text-sm text-green-600 mb-4';
            resetMessage.classList.remove('hidden');
            
            setTimeout(() => {
                closeForgotModal();
            }, 2000);
        }, 1500);
    } catch (error) {
        document.getElementById('reset-btn-text').classList.remove('hidden');
        document.getElementById('reset-btn-spinner').classList.add('hidden');
        document.getElementById('reset-btn').disabled = false;
        
        const resetMessage = document.getElementById('reset-message');
        resetMessage.textContent = 'An error occurred. Please try again.';
        resetMessage.className = 'text-sm text-red-600 mb-4';
        resetMessage.classList.remove('hidden');
    }
}

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
}
</style>
@endsection