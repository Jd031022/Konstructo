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

// Staff UI and API Routes
Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
    // View routes (return HTML)
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');
    
    // IMPORTANT: This route must come BEFORE the API route with {id}
    // View route for application details - returns HTML
    Route::get('/application-details/{id}', function ($id) {
        return view('staff.application-details', ['applicationId' => $id]);
    })->name('application.details');
    
    Route::get('/applications', function () {
        return view('staff.applications');
    })->name('applications');
    
    // Staff Applications API Routes (return JSON)
    Route::get('/applications/data', [App\Http\Controllers\Staff\ApplicationController::class, 'index'])
        ->name('applications.data');
    
    // This is the API endpoint for getting application data as JSON
    Route::get('/applications/{id}', [App\Http\Controllers\Staff\ApplicationController::class, 'show'])
        ->name('applications.show');
    
    Route::post('/applications', [App\Http\Controllers\Staff\ApplicationController::class, 'store'])
        ->name('applications.store');
    
    Route::put('/applications/{id}/status', [App\Http\Controllers\Staff\ApplicationController::class, 'updateStatus'])
        ->name('applications.status');
    
    Route::delete('/applications/{id}', [App\Http\Controllers\Staff\ApplicationController::class, 'destroy'])
        ->name('applications.destroy');
    
    Route::get('/applications/export', [App\Http\Controllers\Staff\ApplicationController::class, 'export'])
        ->name('applications.export');

});

// Applicant UI Routes
Route::prefix('applicant')->name('applicant.')->middleware(['auth'])->group(function () {
    // View routes
    Route::get('/applications', function () {
        return view('applicant.applications');
    })->name('applications');
    
    Route::get('/dashboard', function () {
        return view('applicant.dashboard');
    })->name('dashboard');
    
    Route::get('/application-details', function () {
        return view('applicant.application-details');
    })->name('application.details');
    
    Route::get('/application/step1', function () {
        return view('applicant.application.step1');
    })->name('application.step1');
    
    Route::get('/application/step2', function () {
        return view('applicant.application.step2');
    })->name('application.step2');
    
    Route::get('/application/step3', function () {
        return view('applicant.application.step3');
    })->name('application.step3');
    
    // Application Document API Routes
    Route::post('/application/store-link', [App\Http\Controllers\ApplicationDocumentController::class, 'storeLink'])
        ->name('application.store-link');
    
    Route::get('/application/status', [App\Http\Controllers\ApplicationDocumentController::class, 'checkStatus'])
        ->name('application.status');

    Route::get('/application/limit-info', [App\Http\Controllers\ApplicationDocumentController::class, 'getApplicationLimitInfo'])
        ->name('application.limit-info');
    
    Route::get('/application/details', [App\Http\Controllers\ApplicationDocumentController::class, 'getApplicationDetails'])
        ->name('application.details');
    
    Route::get('/application/debug', [App\Http\Controllers\ApplicationDocumentController::class, 'debug'])
        ->name('application.debug');
    
    Route::post('/application/create-draft', [App\Http\Controllers\ApplicationDocumentController::class, 'createDraft'])
        ->name('application.create-draft');
    
    Route::post('/application/submit', [App\Http\Controllers\ApplicationDocumentController::class, 'submitApplication'])
        ->name('application.submit');
    
    // Applications Management API Routes
    Route::get('/applications/data', [App\Http\Controllers\Applicant\ApplicationController::class, 'index'])
        ->name('applications.data');
    
    Route::get('/applications/{id}', [App\Http\Controllers\Applicant\ApplicationController::class, 'show'])
        ->name('applications.show');
    
    Route::delete('/applications/{id}', [App\Http\Controllers\Applicant\ApplicationController::class, 'destroy'])
        ->name('applications.destroy');
    
    Route::get('/applications/stats', [App\Http\Controllers\Applicant\ApplicationController::class, 'getStats'])
        ->name('applications.stats');
});

// Dashboard route with role-based redirect
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    
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

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    // Admin User Management Routes
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::get('/users/list', [App\Http\Controllers\Admin\UserController::class, 'getUsers'])->name('users.list');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'getUser'])->name('users.get');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.delete');
    Route::post('/users/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

});

Route::get('/profile/profile', function () {
    return view('profile.profile');
});

// Test JSON route
Route::get('/test-json', function() {
    if (!auth()->check()) {
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    $user = auth()->user();
    
    return response()->json([
        'success' => true,
        'message' => 'JSON is working',
        'user' => $user->email,
        'user_id' => $user->id,
        'user_role' => $user->role,
        'authenticated' => true
    ]);
})->middleware('auth');