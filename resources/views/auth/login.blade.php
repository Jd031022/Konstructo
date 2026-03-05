@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100 relative">

    <!-- Background Illustration -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" 
             class="w-full h-full object-cover opacity-90" 
             alt="background">
    </div>

    <!-- Login Card with custom dimensions and margins -->
   <div class="relative bg-white rounded-xl shadow-lg p-8" style="width: 800px; height: 600px; padding-top: 80px; padding-left: 160px; padding-right: 160px;">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo.png') }}" class="w-12 mb-2">
            <h1 class="text-xl font-semibold text-gray-700">Konstructo</h1>
            <p class="text-sm text-gray-500">Login to continue</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Email</label>
                <input type="email"
                    name="email"
                    placeholder="Enter your email here"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Password</label>
                <input type="password"
                    name="password"
                    placeholder="Enter your password here"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600">
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between text-sm mb-4">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me
                </label>

            </div>

            <!-- Login Button -->
            <button
                class="w-full bg-teal-700 hover:bg-teal-800 text-white py-2 rounded-md text-sm font-medium transition">
                Login
            </button>

            <a href="#" class="text-gray-400 hover:text-gray-600 text-xs">
                    Forgot Password?
                </a>

            <!-- Register -->
            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:underline">
                    Sign up here.
                </a>
            </p>

        </form>
    </div>

</div>

@endsection