<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Public routes
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // Added name here
});

Route::post('/send-verification', [VerificationController::class, 'sendCode'])->name('verification.send');
Route::post('/verify-email', [RegisterController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/resend-verification', [RegisterController::class, 'resendVerificationCode'])->name('verification.resend');

// Add these routes to your web.php
Route::post('/forgot-password/send-code', [PasswordResetController::class, 'sendCode']);
Route::post('/forgot-password/verify-code', [PasswordResetController::class, 'verifyCode']);
Route::post('/forgot-password/reset', [PasswordResetController::class, 'resetPassword']);
Route::post('/forgot-password/resend-code', [PasswordResetController::class, 'resendCode']);

// Staff UI routes
Route::get('/staff/dashboard', function () {
    return view('staff.dashboard');
});
Route::get('/staff/users', function () {
    return view('staff.users');
});

Route::get('/staff/application.details', function () {
    return view('staff.application-details');
});

Route::get('/staff/applications', function () {
    return view('staff.applications');
});

Route::get('/staff/settings', function () {
    return view('staff.settings');
});


// User Routes for UI 
Route::get('/user/applications', function () {
    return view('user.applications');
});

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
});

Route::get('/user/application-details', function () {
    return view('user.application-details');
});

Route::get('/user/application/step1', function () {
    return view('user.application.step1');
});

Route::get('/user/application/step2', function () {
    return view('user.application.step2');
});

Route::get('/user/application/step3', function () {
    return view('user.application.step3');
});

// Routes for admin UI
Route::get('/admin/settings', function () {
    return view('admin.settings');
});

Route::get('/admin/users', function () {
    return view('admin.users');
});