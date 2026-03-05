@extends('layouts.guest')

@section('title', 'Verify Email - Konstructo')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verify your Email</h1>
            <p class="text-gray-600 text-sm">
                jethroxxx@example.com
            </p>
        </div>

        <!-- Verification Form -->
        <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
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
                        class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
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

            <!-- Verify Button - Updated with your exact button styling and directs to verification.send -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold 
                       hover:bg-green-700 transition-all duration-200
                       shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Verify
            </button>
        </form>

        <!-- Footer Links -->
        <div class="mt-6 space-y-4">
            <!-- Resend Code -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Didn't receive the code? 
                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors">
                            Resend Code
                        </button>
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

<!-- JavaScript for code inputs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input[type="text"]');
        
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
            });
        }
        
        // Handle backspace to move to previous input
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    });
</script>
@endsection

@push('styles')
<style>
    input[type="text"] {
        -moz-appearance: textfield;
    }
    input[type="text"]::-webkit-outer-spin-button,
    input[type="text"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush