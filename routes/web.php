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

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
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
    
    // View route for application details - returns HTML
    Route::get('/application-details/{id}', function ($id) {
        return view('staff.application-details', ['applicationId' => $id]);
    })->name('application.details');
    
    Route::get('/applications', function () {
        return view('staff.applications');
    })->name('applications');
    
    // Staff Dashboard API Routes (return JSON)
    Route::get('/applications/stats', [App\Http\Controllers\Staff\DashboardController::class, 'getStats'])
        ->name('applications.stats');
    
    Route::get('/applications/weekly-trend', [App\Http\Controllers\Staff\DashboardController::class, 'getWeeklyTrend'])
        ->name('applications.weekly-trend');
    
    Route::get('/applications/recent-activities', [App\Http\Controllers\Staff\DashboardController::class, 'getRecentActivities'])
        ->name('applications.recent-activities');
    
    Route::get('/applications/upcoming-deadlines', [App\Http\Controllers\Staff\DashboardController::class, 'getUpcomingDeadlines'])
        ->name('applications.upcoming-deadlines');
    
    // Staff Applications API Routes (return JSON)
    Route::get('/applications/data', [App\Http\Controllers\Staff\ApplicationController::class, 'index'])
        ->name('applications.data');
    
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
    
    // NEW: Staff review activities routes
    Route::post('/applications/{id}/note', [App\Http\Controllers\Staff\ApplicationController::class, 'addNote'])
        ->name('applications.note');
    
    Route::post('/applications/{id}/verify-documents', [App\Http\Controllers\Staff\ApplicationController::class, 'verifyDocuments'])
        ->name('applications.verify-documents');
    
    Route::get('/applications/{id}/review-activities', [App\Http\Controllers\Staff\ApplicationController::class, 'getReviewActivities'])
        ->name('applications.review-activities');
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
    
    // FIXED: Added ID parameter to application details route
    Route::get('/application-details/{id}', function ($id) {
        return view('applicant.application-details', ['applicationId' => $id]);
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
    
    // NEW: Applicant view review activities route
    Route::get('/applications/{id}/review-activities', [App\Http\Controllers\Applicant\ApplicationController::class, 'getReviewActivities'])
        ->name('applications.review-activities');
        // Add this inside your applicant routes group
Route::get('/applications/{id}/debug-review', [App\Http\Controllers\Applicant\ApplicationController::class, 'debugReviewActivities'])
    ->name('applications.debug-review');
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