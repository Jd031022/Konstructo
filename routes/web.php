<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use App\Http\Controllers\Applicant\ApplicationController;
use App\Models\ApplicationDocument;

Route::get('/', function () {
    return view('applicant.welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
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

// Staff UI and API Routes - ORDER MATTERS! Put specific routes FIRST
Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
    
    // ========== SPECIFIC API ROUTES FIRST (before wildcard routes) ==========
    Route::get('/applications/data', [App\Http\Controllers\Staff\ApplicationController::class, 'index'])->name('applications.data');
    Route::get('/applications/export', [App\Http\Controllers\Staff\ApplicationController::class, 'export'])->name('applications.export');
    Route::get('/applications/stats', [App\Http\Controllers\Staff\DashboardController::class, 'getStats'])->name('applications.stats');
    Route::get('/applications/weekly-trend', [App\Http\Controllers\Staff\DashboardController::class, 'getWeeklyTrend'])->name('applications.weekly-trend');
    Route::get('/applications/recent-activities', [App\Http\Controllers\Staff\DashboardController::class, 'getRecentActivities'])->name('applications.recent-activities');
    Route::get('/applications/upcoming-deadlines', [App\Http\Controllers\Staff\DashboardController::class, 'getUpcomingDeadlines'])->name('applications.upcoming-deadlines');
    Route::post('/applications/restore-multiple', [App\Http\Controllers\Staff\ApplicationController::class, 'restoreMultiple'])->name('applications.restore-multiple');
    
    // ========== POSITION MANAGEMENT ROUTES ==========
    Route::prefix('position')->name('position.')->group(function () {
        Route::post('/update', [App\Http\Controllers\Staff\PositionController::class, 'update'])->name('update');
        Route::get('/check', [App\Http\Controllers\Staff\PositionController::class, 'check'])->name('check');
        Route::get('/get', [App\Http\Controllers\Staff\PositionController::class, 'getPosition'])->name('get');
    });
    
    // ========== BFP ROUTES ==========
    Route::get('/position/check', [App\Http\Controllers\Staff\ApplicationController::class, 'getUserPosition']);
    Route::get('/applications/{id}/bfp-data', [App\Http\Controllers\Staff\ApplicationController::class, 'getBfpData']);
    Route::post('/applications/{id}/upload-fsec', [App\Http\Controllers\Staff\ApplicationController::class, 'uploadFSEC']);
    Route::delete('/applications/{id}/delete-fsec', [App\Http\Controllers\Staff\ApplicationController::class, 'deleteFSEC']);
    Route::post('/applications/{id}/bfp-comments', [App\Http\Controllers\Staff\ApplicationController::class, 'saveBFPComments']);
    
    // ========== CPDO ROUTES ==========
    Route::get('/applications/{id}/cpdo-status', [App\Http\Controllers\Staff\ApplicationController::class, 'getCPDOStatus'])->name('applications.cpdo-status');
    Route::post('/applications/{id}/cpdo-decision', [App\Http\Controllers\Staff\ApplicationController::class, 'submitCPDODecision'])->name('applications.cpdo-decision');
    Route::post('/applications/{id}/cpdo-assessment', [App\Http\Controllers\Staff\ApplicationController::class, 'saveCPDOAssessment'])->name('applications.cpdo-assessment');
    Route::get('/applications/{id}/cpdo-assessment', [App\Http\Controllers\Staff\ApplicationController::class, 'getCPDOAssessment'])->name('applications.get-cpdo-assessment');
    
    // ========== VIEW ROUTES ==========
    Route::get('/dashboard', function () { return view('staff.dashboard'); })->name('dashboard');
    Route::get('/application-details/{id}', function ($id) { return view('staff.application-details', ['applicationId' => $id]); })->name('application.details');
    Route::get('/applications/{id}/activity-history', function ($id) { return view('staff.activity-history', ['applicationId' => $id]); })->name('activity-history');
    Route::get('/applications', function () { return view('staff.applications'); })->name('applications');
    
    // ========== APPLICATIONS CRUD ROUTES (with ID - these come AFTER specific routes) ==========
    Route::get('/applications/{id}', [App\Http\Controllers\Staff\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications', [App\Http\Controllers\Staff\ApplicationController::class, 'store'])->name('applications.store');
    Route::put('/applications/{id}/status', [App\Http\Controllers\Staff\ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::delete('/applications/{id}', [App\Http\Controllers\Staff\ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/{id}/request-missing-documents', [App\Http\Controllers\Staff\ApplicationController::class, 'requestMissingDocuments']);
    Route::post('/applications/{id}/note', [App\Http\Controllers\Staff\ApplicationController::class, 'addNote'])->name('applications.note');
    Route::post('/applications/{id}/verify-documents', [App\Http\Controllers\Staff\ApplicationController::class, 'verifyDocuments'])->name('applications.verify-documents');
    Route::get('/applications/{id}/review-activities', [App\Http\Controllers\Staff\ApplicationController::class, 'getReviewActivities'])->name('applications.review-activities');
    Route::post('/applications/{id}/assessment', [App\Http\Controllers\Staff\ApplicationController::class, 'saveAssessment'])->name('applications.save-assessment');
    Route::get('/applications/{id}/assessment', [App\Http\Controllers\Staff\ApplicationController::class, 'getAssessment'])->name('applications.get-assessment');
    Route::post('/applications/{id}/archive', [App\Http\Controllers\Staff\ApplicationController::class, 'archive'])->name('applications.archive');
    Route::post('/applications/{id}/restore', [App\Http\Controllers\Staff\ApplicationController::class, 'restore'])->name('applications.restore');
    Route::get('/applications/{id}/ownership', [App\Http\Controllers\Staff\ApplicationController::class, 'getOwnershipData'])->name('applications.ownership');
    Route::post('/applications/{id}/verify-ownership-document', [App\Http\Controllers\Staff\ApplicationController::class, 'verifyOwnershipDocument'])->name('applications.verify-ownership-document');
    
    // ========== ARCHIVE ROUTES ==========
    Route::get('/archived-applications', function () { return view('staff.archived-applications'); })->name('archived-applications');
    Route::get('/archived-applications/data', [App\Http\Controllers\Staff\ApplicationController::class, 'getArchivedApplications'])->name('archived-applications.data');
    Route::get('/archived-applications/export', [App\Http\Controllers\Staff\ApplicationController::class, 'exportArchived'])->name('archived-applications.export');
    
    Route::get('/dashboard/export', [App\Http\Controllers\Staff\DashboardController::class, 'exportDashboard'])->name('dashboard.export');
});

// Applicant UI Routes
Route::prefix('applicant')->name('applicant.')->middleware(['auth'])->group(function () {
    // View routes
    Route::get('/applications', function () { return view('applicant.applications'); })->name('applications');
    Route::get('/dashboard', function () { return view('applicant.dashboard'); })->name('dashboard');
    Route::get('/buildingpermit-preview', function () { return view('applicant.buildingpermit-preview'); })->name('building-permit.preview');
    Route::get('/application-details/{id}', function ($id) { return view('applicant.application-details', ['applicationId' => $id]); })->name('application.details');
    Route::get('/applications/{id}/activity-history', function ($id) { return view('applicant.activity-history', ['applicationId' => $id]); })->name('activity-history');
    
    // ========== STEP ROUTES ==========
    Route::get('/application/step1', function (Request $request) {
        $user = Auth::user();
        $applicationId = $request->get('id');
        if (!$applicationId) {
            return redirect()->route('applicant.applications')->with('error', 'Application ID is required.');
        }
        $application = ApplicationDocument::where('user_id', $user->id)->where('id', $applicationId)->with('ownershipVerification')->first();
        if (!$application) {
            return redirect()->route('applicant.applications')->with('error', 'Application not found.');
        }
        return view('applicant.application.step1', compact('application'));
    })->name('application.step1');

    Route::get('/application/step2', function (Request $request) {
        $user = Auth::user();
        $applicationId = $request->get('id');
        if (!$applicationId) {
            return redirect()->route('applicant.applications')->with('error', 'Application ID is required.');
        }
        $application = ApplicationDocument::where('user_id', $user->id)->where('id', $applicationId)->with('ownershipVerification')->first();
        if (!$application) {
            return redirect()->route('applicant.applications')->with('error', 'Application not found.');
        }
        $ownership = $application->ownershipVerification;
        if (!$ownership) {
            return redirect()->route('applicant.application.step1', ['id' => $applicationId])->with('error', 'Please complete ownership verification first.');
        }
        if (empty($ownership->tct_link) || empty($ownership->tax_declaration_link) || empty($ownership->current_tax_receipt_link)) {
            return redirect()->route('applicant.application.step1', ['id' => $applicationId])->with('error', 'Please complete all required ownership documents.');
        }
        if (!$application->step1_completed) {
            return redirect()->route('applicant.application.step1', ['id' => $applicationId])->with('error', 'Please complete ownership verification first.');
        }
        return view('applicant.application.step2', compact('application'));
    })->name('application.step2');

    Route::get('/application/step3', function (Request $request) {
        $user = Auth::user();
        $applicationId = $request->get('id');
        if (!$applicationId) {
            return redirect()->route('applicant.applications')->with('error', 'Application ID is required.');
        }
        $application = ApplicationDocument::where('user_id', $user->id)->where('id', $applicationId)->first();
        if (!$application) {
            return redirect()->route('applicant.applications')->with('error', 'Application not found.');
        }
        return view('applicant.application.step3', compact('application'));
    })->name('application.step3');

    Route::get('/application/step4', function (Request $request) {
        $user = Auth::user();
        $applicationId = $request->get('id');
        if (!$applicationId) {
            return redirect()->route('applicant.applications')->with('error', 'Application ID is required.');
        }
        $application = ApplicationDocument::where('user_id', $user->id)->where('id', $applicationId)->first();
        if (!$application) {
            return redirect()->route('applicant.applications')->with('error', 'Application not found.');
        }
        return view('applicant.application.step4', compact('application'));
    })->name('application.step4');

    Route::get('/application/step5', function (Request $request) {
        $user = Auth::user();
        $applicationId = $request->get('id');
        if (!$applicationId) {
            return redirect()->route('applicant.applications')->with('error', 'Application ID is required.');
        }
        $application = ApplicationDocument::where('user_id', $user->id)->where('id', $applicationId)->first();
        if (!$application) {
            return redirect()->route('applicant.applications')->with('error', 'Application not found.');
        }
        return view('applicant.application.step5', compact('application'));
    })->name('application.step5');
    
    // ========== API ROUTES ==========
    Route::get('/applications/data', [ApplicationController::class, 'index'])->name('applications.data');
    Route::get('/applications/stats', [ApplicationController::class, 'getStats'])->name('applications.stats');
    Route::get('/applications/check-number', [ApplicationController::class, 'checkApplicationNumber'])->name('applications.check-number');
    Route::get('/applications/{id}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{id}/ownership', [ApplicationController::class, 'getOwnershipData'])->name('applications.ownership');
    Route::get('/applications/{id}/review-activities', [ApplicationController::class, 'getReviewActivities'])->name('applications.review-activities');
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    
    // Survey routes
    Route::post('/survey/submit', [ApplicationController::class, 'submitSurvey'])->name('survey.submit');
    Route::get('/survey/pending', [ApplicationController::class, 'checkPendingSurvey'])->name('survey.pending');
    Route::get('/survey/page1', function () {
        return redirect()->route('applicant.dashboard');
    })->name('survey.page1');
    Route::get('/survey/page2', function () {
        return redirect()->route('applicant.dashboard');
    })->name('survey.page2');
    
    // ========== APPLICATION DOCUMENT ROUTES ==========
    Route::post('/application/store-link', [App\Http\Controllers\ApplicationDocumentController::class, 'storeLink'])->name('application.store-link');
    Route::post('/application/store-links', [App\Http\Controllers\ApplicationDocumentController::class, 'storeLinks'])->name('application.store-links');
    Route::get('/application/status', [App\Http\Controllers\ApplicationDocumentController::class, 'checkStatus'])->name('application.status');
    Route::get('/application/limit-info', [ApplicationController::class, 'getLimitInfo'])->name('application.limit-info');
    Route::get('/application/details', [App\Http\Controllers\ApplicationDocumentController::class, 'getApplicationDetails'])->name('application.details');
    Route::get('/application/debug', [App\Http\Controllers\ApplicationDocumentController::class, 'debug'])->name('application.debug');
    Route::post('/application/create-draft', [ApplicationController::class, 'createDraft'])->name('application.create-draft');
    Route::post('/application/submit', [App\Http\Controllers\ApplicationDocumentController::class, 'submitApplication'])->name('application.submit');
    Route::post('/application/generate-number', [ApplicationController::class, 'generateApplicationNumber'])->name('application.generate-number');
    Route::get('/application/{id}/check-number', [ApplicationController::class, 'checkApplicationHasNumber'])->name('application.check-number');
    Route::post('/application/save-edited-pdf', [ApplicationController::class, 'saveEditedPdf'])->name('application.save-edited-pdf');
    Route::post('/application/save-ownership', [ApplicationController::class, 'saveOwnership'])->name('application.save-ownership');
    Route::post('/application/save-project-info', [ApplicationController::class, 'saveProjectInfo'])->name('application.save-project-info');
    Route::post('/application/step3/complete', [ApplicationController::class, 'completeStep2'])->name('application.step3.complete');
    Route::post('/application/step4/complete', [ApplicationController::class, 'completeStep3'])->name('application.step4.complete');
    
    // ========== ACCOUNT STATUS ROUTE ==========
    Route::get('/account-status', function () {
        $user = Auth::user();
        if ($user && $user->role === 'applicant') {
            $status = $user->approval_status ?? 'pending';
            $rejectionReason = $user->rejection_reason ?? null;
            session(['account_status' => $status]);
            session(['rejection_reason' => $rejectionReason]);
            return view('applicant.account-status', ['account_status' => $status, 'rejection_reason' => $rejectionReason]);
        }
        return redirect()->route('dashboard');
    })->name('account-status');
    
    // ========== PRINT ROUTE - ONLY PRINT ROUTE HERE ==========
    Route::get('/print-receipt/{id}', [ApplicationController::class, 'printCPDOAssessment'])->name('print-receipt');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
    Route::get('/settings', function () { return view('admin.settings'); })->name('settings');
    Route::get('/applications', function () { return view('admin.applications'); })->name('applications');
    Route::get('/applications/{id}/activity-history', function ($id) { return view('admin.activity-history', ['applicationId' => $id]); })->name('activity-history');
    Route::get('/archived-applications', function () { return view('admin.archived-applications'); })->name('archived-applications');
    Route::get('/archived-applications/data', [App\Http\Controllers\Admin\ApplicationController::class, 'getArchivedApplications'])->name('archived-applications.data');
    Route::get('/archived-applications/export', [App\Http\Controllers\Admin\ApplicationController::class, 'exportArchived'])->name('archived-applications.export');
    Route::post('/applications/{id}/restore', [App\Http\Controllers\Admin\ApplicationController::class, 'restoreArchivedApplication'])->name('applications.restore');
    Route::post('/applications/restore-multiple', [App\Http\Controllers\Admin\ApplicationController::class, 'restoreMultipleArchivedApplications'])->name('applications.restore-multiple');
    Route::delete('/applications/{id}/permanent-delete', [App\Http\Controllers\Admin\ApplicationController::class, 'permanentDelete'])->name('applications.permanent-delete');
    Route::get('/dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/trend', [App\Http\Controllers\Admin\DashboardController::class, 'getTrend'])->name('dashboard.trend');
    Route::get('/users/stats', [App\Http\Controllers\Admin\UserController::class, 'getStats'])->name('users.stats');
    Route::get('/staff/performance', [App\Http\Controllers\Admin\StaffPerformanceController::class, 'getPerformance'])->name('staff.performance');
    Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/dashboard/export', [App\Http\Controllers\Admin\DashboardController::class, 'exportDashboard'])->name('dashboard.export');
    Route::get('/users/export', [App\Http\Controllers\Admin\UserController::class, 'exportUsers'])->name('users.export');
    Route::get('/archived-applications/export', [App\Http\Controllers\Admin\ApplicationController::class, 'exportArchivedApplications'])->name('archived-applications.export');
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::get('/users/list', [App\Http\Controllers\Admin\UserController::class, 'getUsers'])->name('users.list');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'getUser'])->name('users.get');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.delete');
    Route::post('/users/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{id}/approve', [App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [App\Http\Controllers\Admin\UserController::class, 'reject'])->name('users.reject');
    Route::get('/pending-applicants', [App\Http\Controllers\Admin\UserController::class, 'getPendingApplicants'])->name('pending-applicants');
    Route::get('/applications/data', [App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.data');
    Route::get('/applications/export', [App\Http\Controllers\Admin\ApplicationController::class, 'export'])->name('applications.export');
    Route::get('/applications/{id}', [App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('applications.show');
    Route::put('/applications/{id}/status', [App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::delete('/applications/{id}', [App\Http\Controllers\Admin\ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::post('/applications/{id}/archive', [App\Http\Controllers\Admin\ApplicationController::class, 'archive'])->name('applications.archive');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/logs/export', [SettingsController::class, 'exportLogs'])->name('logs.export');
});

// Dashboard route with role-based redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user) {
        if ($user->isApplicant()) {
            if (!$user->canLogin()) {
                if ($user->isPending()) {
                    return redirect()->route('applicant.account-status')->with('warning', 'Your account is pending admin approval.');
                } elseif ($user->isRejected()) {
                    return redirect()->route('applicant.account-status')->with('error', 'Your account has been rejected.');
                } elseif (is_null($user->email_verified_at)) {
                    return redirect()->route('login')->with('error', 'Please verify your email address first.');
                }
            }
        }
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'applicant' => redirect()->route('applicant.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::get('/profile/profile', function () { return view('profile.profile'); });
Route::get('/profile/avatar-info', [App\Http\Controllers\ProfileController::class, 'getAvatarInfo'])->name('profile.avatar.info');

// Test routes
Route::get('/test-gmail', function() {
    try {
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $result = [
            'credentials_exist' => [
                'refresh_token' => !empty($refreshToken),
                'client_id' => !empty($clientId),
                'client_secret' => !empty($clientSecret)
            ],
            'refresh_token_length' => strlen($refreshToken ?? ''),
            'refresh_token_prefix' => substr($refreshToken ?? '', 0, 10) . '...'
        ];
        try {
            $client = new Google_Client();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setAccessType('offline');
            $client->refreshToken($refreshToken);
            $token = $client->fetchAccessTokenWithRefreshToken();
            if (isset($token['error'])) {
                $result['token_error'] = $token['error'];
                $result['token_error_description'] = $token['error_description'] ?? 'No description';
            } else {
                $result['token_success'] = true;
                $result['has_access_token'] = isset($token['access_token']);
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::get('/print-receipt/{id}', [ApplicationController::class, 'printCPDOAssessment'])->name('print-receipt');
Route::get('/chat', function () { return view('chat.index'); })->middleware(['auth'])->name('chat');

Route::get('/conversations', [ConversationController::class, 'index']);
Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
Route::post('/conversations', [ConversationController::class, 'store']);
Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);
Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
Route::post('/conversations/create', [ConversationController::class, 'createOnly'])->middleware('auth');

Route::get('/users/list', function() {
    return App\Models\User::where('id', '!=', auth()->id())
        ->select('id', 'first_name', 'last_name', 'email')
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'initials' => $user->initials,
                'avatar_url' => $user->avatar_url
            ];
        });
})->middleware('auth');

Route::get('/test-fpdf', function() {
    try {
        $fpdfExists = class_exists('FPDF');
        $fpdiExists = class_exists('setasign\Fpdi\Fpdi');
        $result = [
            'fpdf_class_exists' => $fpdfExists,
            'fpdi_class_exists' => $fpdiExists,
            'fpdf_file_exists' => file_exists('vendor/setasign/fpdf/fpdf.php'),
            'fpdi_file_exists' => file_exists('vendor/setasign/fpdi/src/Fpdi.php')
        ];
        if ($fpdfExists) {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, 'Hello World!');
            $outputPath = storage_path('app/temp/test.pdf');
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            $pdf->Output('F', $outputPath);
            $result['pdf_created'] = file_exists($outputPath);
        }
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
    
});