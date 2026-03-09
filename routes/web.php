<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', function () {
    return view('user.welcome');
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

Route::get('/staff/users', function () {
    return view('staff.users');
})->name('staff.users');

Route::get('/staff/application-details', function () {
    return view('staff.application-details');
})->name('staff.application.details');

Route::get('/staff/applications', function () {
    return view('staff.applications');
})->name('staff.applications');

Route::get('/staff/settings', function () {
    return view('staff.settings');
})->name('staff.settings');

// User Routes for UI
Route::get('/user/applications', function () {
    return view('user.applications');
})->name('user.applications');

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::get('/user/application-details', function () {
    return view('user.application-details');
})->name('user.application.details');

Route::get('/user/application/step1', function () {
    return view('user.application.step1');
})->name('user.application.step1');

Route::get('/user/application/step2', function () {
    return view('user.application.step2');
})->name('user.application.step2');

Route::get('/user/application/step3', function () {
    return view('user.application.step3');
})->name('user.application.step3');

// Dashboard route with role-based redirect
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
      $typedUser = $user;
    
    if ($user) {
        return match($user->role) {
            'admin' => redirect()->route('admin.users'),
            'engineer' => redirect()->route('staff.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
    
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

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