<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', function () {
    return view('applicant.welcome');
});

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
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::post('/send-verification', [VerificationController::class, 'sendCode'])->name('verification.send');
Route::post('/verify-email', [RegisterController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/resend-verification', [RegisterController::class, 'resendVerificationCode'])->name('verification.resend');

// Password reset routes
Route::post('/forgot-password/send-code', [PasswordResetController::class, 'sendCode'])->name('password.send-code');
Route::post('/forgot-password/verify-code', [PasswordResetController::class, 'verifyCode'])->name('password.verify-code');
Route::post('/forgot-password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
Route::post('/forgot-password/resend-code', [PasswordResetController::class, 'resendCode'])->name('password.resend-code');

// Staff UI routes
Route::get('/staff/dashboard', function () {
    return view('staff.dashboard');
})->name('staff.dashboard');

Route::get('/staff/application-details', function () {
    return view('staff.application-details');
})->name('staff.application.details');

Route::get('/staff/applications', function () {
    return view('staff.applications');
})->name('staff.applications');

// User Routes for UI
Route::get('/applicant/applications', function () {
    return view('applicant.applications');
})->name('applicant.applications');

Route::get('/applicant/dashboard', function () {
    return view('applicant.dashboard');
})->name('applicant.dashboard');

Route::get('/applicant/application-details', function () {
    return view('applicant.application-details');
})->name('applicant.application.details');

Route::get('/applicant/application/step1', function () {
    return view('applicant.application.step1');
})->name('applicant.application.step1');

Route::get('/applicant/application/step2', function () {
    return view('applicant.application.step2');
})->name('applicant.application.step2');

Route::get('/applicant/application/step3', function () {
    return view('applicant.application.step3');
})->name('applicant.application.step3');

// Dashboard route with role-based redirect - FIXED VERSION
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user(); // Get the authenticated user
    
    if ($user) {
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'applicant' => redirect()->route('applicant.dashboard'),
            default => redirect()->route('login'),
        };
    }
    
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

// Add admin dashboard route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/settings', function () {
    return view('admin.settings');
})->name('admin.settings');

// Admin User Management Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('/users/list', [App\Http\Controllers\Admin\UserController::class, 'getUsers'])->name('admin.users.list');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'getUser'])->name('admin.users.get');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.delete');
    Route::post('/users/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::post('/users/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('admin.users.reset-password');
});

Route::get('/profile/profile', function () {
    return view('profile.profile');
});