@extends('layouts.dashboard')

@section('title', 'Application Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Back Button -->
    <div>
        <a href="/applicant/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to My Applications
        </a>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading application details...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mt-4">Application Not Found</h3>
        <p class="text-gray-500 mt-2">The application you're looking for doesn't exist or has been deleted.</p>
        <a href="/applicant/applications" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
            Back to Applications
        </a>
    </div>

    <!-- Application Content (hidden initially) -->
    <div id="application-content" class="hidden">

        <!-- Hard Copy Notice (if hard copy not yet received) -->
        <div id="hardcopy-notice" class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hard Copy Submission Required</h4>
                    <p class="text-sm text-gray-700 mt-1">After the approval of your application, Please submit the original hard copies of ALL documents to the Office of the Building Official (OBO) to complete your application.</p>
                </div>
            </div>
        </div>

        <!-- Hard Copy Received Notice -->
        <div id="hardcopy-received-notice" class="mb-6 p-4 bg-green-100 border-l-4 border-green-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hard Copy Received</h4>
                    <p class="text-sm text-gray-700 mt-1">Your hard copy documents have been received and verified by the OBO.</p>
                </div>
            </div>
        </div>

        <!-- Document Verification Alert -->
        <div id="document-verification-alert" class="mb-6 p-4 bg-purple-100 border-l-4 border-purple-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Documents Under Verification</h4>
                    <p class="text-sm text-gray-700 mt-1">Your documents are currently being verified by our staff. This process may take 1-2 business days.</p>
                </div>
            </div>
        </div>

        <!-- Application Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white text-xl font-bold">
                        BP
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <h1 class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                            <span id="status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium transition-all duration-500">Pending Review</span>
                        </div>
                        
                        <!-- Application Meta Information - Inline with stacked dates -->
                        <div class="flex items-center gap-4 text-sm">
                            <!-- Application Number -->
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Application Number</span>
                                <span id="application-number" class="font-mono font-medium text-[#155386]"></span>
                            </div>
                            
                            <span class="text-gray-300">|</span>
                            
                            <!-- Submitted Date -->
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Submitted</span>
                                <span id="submitted-date" class="font-medium text-gray-700"></span>
                            </div>
                            
                            <span class="text-gray-300">|</span>
                            
                            <!-- Last Updated Date -->
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Last Updated</span>
                                <span id="updated-date" class="font-medium text-gray-700"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button onclick="downloadApplication()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Application
                    </button>
                    <button onclick="contactSupport()" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Contact Support
                    </button>
                </div>
            </div>
        </div>

       <!-- Progress Timeline -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-fade-in">
    <h2 class="text-lg font-semibold text-gray-800 mb-6">Application Progress</h2>
    
    <!-- Progress Bar with Loading Animation -->
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-gray-600">Overall Completion</span>
            <div class="flex items-center gap-2">
                <span id="progress-percentage" class="font-semibold text-[#155386] transition-all duration-500">0%</span>
                <span class="flex items-center gap-1 text-xs text-green-600 font-medium">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                    <span class="w-2 h-2 bg-green-500 rounded-full absolute"></span>
                    <span class="ml-3">Live</span>
                </span>
            </div>
        </div>
        
        <!-- Progress Bar Container -->
        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
            <!-- Static progress bar (actual completion) -->
            <div id="progress-bar" class="absolute inset-0 bg-gradient-to-r from-[#155386] to-[#40798C] h-full rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
            
            <!-- Animated loading overlay (continuous animation) -->
            <div class="absolute inset-0 h-full w-full overflow-hidden">
                <div id="animated-loading-overlay" class="h-full w-full loading-progress-animation"></div>
            </div>
        </div>
        
        <!-- Status Message -->
        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
            <svg class="w-3 h-3 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                <circle cx="4" cy="4" r="3" />
            </svg>
            Real-time updates active - Application status is being monitored
        </p>
    </div>

    <!-- Timeline Steps -->
    <div class="relative">
        <!-- Progress Line (background) -->
        <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200"></div>
        
        <!-- Active Progress Line -->
        <div id="progress-line" class="absolute top-5 left-0 w-0 h-0.5 bg-[#155386] transition-all duration-700 ease-out" style="width: 0%"></div>
        
        <!-- Animated Progress Line Overlay -->
        <div class="absolute top-5 left-0 w-full h-0.5 overflow-hidden">
            <div class="w-full h-full loading-line-animation"></div>
        </div>
        
        <!-- Steps -->
        <div class="relative flex justify-between">
            <!-- Step 1: Submitted -->
            <div id="step-submitted" class="text-center step-item">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                    <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400 transition-all duration-500">Submitted</p>
                <p id="step-submitted-date" class="text-xs text-gray-400 transition-all duration-500"></p>
            </div>
            
            <!-- Step 2: Under Review -->
            <div id="step-under-review" class="text-center step-item">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                    <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400 transition-all duration-500">Under Review</p>
                <p id="step-under-review-date" class="text-xs text-gray-400 transition-all duration-500"></p>
            </div>
            
            <!-- Step 3: Document Verification -->
            <div id="step-verification" class="text-center step-item">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                    <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400 transition-all duration-500">Document Verification</p>
                <p id="step-verification-date" class="text-xs text-gray-400 transition-all duration-500"></p>
            </div>
            
            <!-- Step 4: Approval -->
            <div id="step-approval" class="text-center step-item">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                    <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400 transition-all duration-500">Approval</p>
                <p id="step-approval-date" class="text-xs text-gray-400 transition-all duration-500"></p>
            </div>
            
            <!-- Step 5: Release -->
            <div id="step-release" class="text-center step-item">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                    <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400 transition-all duration-500">For Release</p>
                <p id="step-release-date" class="text-xs text-gray-400 transition-all duration-500"></p>
            </div>
        </div>
    </div>
</div>
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column - Staff Information, Google Drive, and Important Notes -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Staff/Reviewers List Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Application Reviewers</h2>
                    
                    <div id="reviewers-container" class="space-y-4">
                        <!-- Reviewers will be loaded dynamically -->
                        <div class="flex items-center justify-center p-8">
                            <svg class="animate-spin h-6 w-6 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Google Drive Documents Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Google Drive Documents
    </h2>
    
    <!-- Main Google Drive Folder Link -->
    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">Main Google Drive Folder</p>
                <div class="flex items-center gap-2 mt-1">
                    <a href="#" id="drive-link" class="text-[#155386] hover:underline text-sm flex items-center gap-1 break-all" target="_blank">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span id="drive-link-text">View Folder</span>
                    </a>
                    <span class="text-gray-300">|</span>
                    <button onclick="copyDriveLink()" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Copy Link
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">All required documents should be uploaded to this folder</p>
            </div>
        </div>
    </div>

    <!-- Individual Document Links -->
    <div class="space-y-4">
        <h3 class="text-md font-semibold text-gray-800 border-b pb-2">Submitted Documents</h3>
        
        <div id="documents-list" class="space-y-3">
            <!-- Documents will be loaded dynamically -->
            <div class="text-center py-8 text-gray-500">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm">Loading documents...</p>
            </div>
        </div>
    </div>

    <!-- Document Status -->
    <div class="mt-6 pt-4 border-t border-gray-100">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Document Status:</span>
            <span id="document-status" class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full transition-all duration-500">Not Available</span>
        </div>

        <!-- Document Verification Status -->
        <div id="document-verification-status" class="mt-4 p-3 bg-purple-50 rounded-lg hidden">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-purple-600 rounded-full animate-pulse"></div>
                <span class="text-xs font-medium text-gray-700">Verification Status:</span>
                <span id="verification-badge" class="text-xs px-2 py-0.5 bg-purple-100 text-purple-600 rounded-full">In Progress</span>
            </div>
            <p id="verification-message" class="text-xs text-gray-500 mt-1">Documents are being verified by staff</p>
        </div>
    </div>
</div>

                <!-- Important Notes Card - Moved here below Google Drive -->
                <div class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100 animate-fade-in">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Important Reminders</h4>
                            <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li>All uploaded documents require original hard copy submission to the Office of the Building Official (OBO)</li>
                                <li>You will receive an email notification for every update on your application</li>
                                <li>Processing time may take 7-10 business days upon complete submission</li>
                                <li>For urgent concerns, please contact the Building Official's office directly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Status & Updates -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    <!-- Current Status Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span id="current-status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium transition-all duration-500">Pending Review</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Queue Position:</span>
                                <span id="queue-position" class="text-sm font-medium text-gray-800">-</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Estimated Review:</span>
                                <span id="estimated-time" class="text-sm font-medium text-gray-800">3-5 business days</span>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Target Release Date</p>
                                        <p id="target-release" class="text-sm font-semibold text-gray-800">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hard Copy Status in Sidebar -->
                        <div id="hardcopy-status-sidebar" class="mt-4 p-3 bg-blue-50 rounded-lg transition-all duration-500">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                <span class="text-xs font-medium text-gray-700">Hard Copy Status:</span>
                                <span id="hardcopy-badge" class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full transition-all duration-500">Pending</span>
                            </div>
                            <p id="hardcopy-message" class="text-xs text-gray-500 mt-1">Submit originals to OBO</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-2">
                            <button onclick="sendMessage()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Send Message to Reviewer
                            </button>
                            <button onclick="requestAssistance()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Request for Assistance
                            </button>
                        </div>
                    </div>

<!-- Activity Log Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Activity Log</h2>
        <span class="text-xs text-gray-400">Last 3 activities</span>
    </div>
    
    <div id="activity-log" class="space-y-4 min-h-[200px]">
        <!-- Activities will be loaded dynamically -->
        <div class="text-center py-8 text-gray-500">
            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm">Loading activities...</p>
        </div>
    </div>
    
    <a href="/applicant/applications/{{ $applicationId }}/activity-history" 
       class="mt-4 text-sm text-[#155386] hover:text-[#40798C] font-medium w-full text-center inline-block py-2 border-t border-gray-100 hover:bg-gray-50 transition rounded-b-lg">
        View Full History →
    </a>
</div>
            </div>
        </div>
    </div>
</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
                <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
                <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
                <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Update Notification -->
<div id="update-notification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-500 translate-y-[-100px] z-50">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span id="notification-message">Application status updated!</span>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Get application ID from URL path
    function getApplicationIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        return pathParts[pathParts.length - 1];
    }
    
    let applicationId = getApplicationIdFromUrl();
    let currentApplication = null;
    let previousStatus = null;
    let updateCheckInterval = null;
    let previousDocumentVerification = false;

    // Load application details on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Application ID from URL:', applicationId);
        if (applicationId && applicationId !== 'application-details' && !isNaN(applicationId)) {
            loadApplicationDetails();
            // Start checking for updates every 15 seconds (more frequent for document verification)
            startRealTimeUpdates();
        } else {
            showError();
        }
        setupModals();
    });

    // Start real-time updates
    function startRealTimeUpdates() {
        // Check for updates every 15 seconds
        updateCheckInterval = setInterval(checkForUpdates, 15000);
    }

    // Check for updates
    async function checkForUpdates() {
        try {
            const response = await fetch(`/applicant/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    const newApplication = data.data;
                    
                    // Check if status changed
                    if (previousStatus && previousStatus !== newApplication.status) {
                        showUpdateNotification('Application status updated to ' + formatStatus(newApplication.status));
                        animateStatusChange();
                        
                        // Check if document verification started
                        if (newApplication.status === 'document-verification') {
                            showDocumentVerificationAlert();
                        }
                        
                        // Check if documents were verified
                        if (previousStatus === 'document-verification' && newApplication.status === 'approved') {
                            showUpdateNotification('Documents have been verified and approved!');
                        }
                    }
                    
                    // Update the application data
                    currentApplication = newApplication;
                    displayApplicationDetails();
                    
                    // Reload review activities if needed
                    if (previousStatus !== newApplication.status) {
                        loadReviewActivities();
                    }
                    
                    previousStatus = newApplication.status;
                }
            }
        } catch (error) {
            console.error('Error checking for updates:', error);
        }
    }

    // Show update notification
    function showUpdateNotification(message) {
        const notification = document.getElementById('update-notification');
        document.getElementById('notification-message').textContent = message;
        notification.style.transform = 'translateY(0)';
        
        setTimeout(() => {
            notification.style.transform = 'translateY(-100px)';
        }, 5000);
    }

    // Show document verification alert
    function showDocumentVerificationAlert() {
        const alert = document.getElementById('document-verification-alert');
        if (alert) {
            alert.classList.remove('hidden');
            setTimeout(() => {
                alert.classList.add('hidden');
            }, 10000); // Hide after 10 seconds
        }
    }

    // Animate status change
    function animateStatusChange() {
        const statusBadge = document.getElementById('status-badge');
        const currentStatusBadge = document.getElementById('current-status-badge');
        const progressBar = document.getElementById('progress-bar');
        const progressLine = document.getElementById('progress-line');
        
        // Add pulse animation
        statusBadge.classList.add('animate-pulse');
        currentStatusBadge.classList.add('animate-pulse');
        
        // Add scale animation to progress elements
        progressBar.classList.add('scale-animation');
        progressLine.classList.add('scale-animation');
        
        // Remove animations after they complete
        setTimeout(() => {
            statusBadge.classList.remove('animate-pulse');
            currentStatusBadge.classList.remove('animate-pulse');
            progressBar.classList.remove('scale-animation');
            progressLine.classList.remove('scale-animation');
        }, 1000);
    }

// Load application details from API
async function loadApplicationDetails() {
    try {
        console.log('Fetching application details for ID:', applicationId);
        
        const response = await fetch(`/applicant/applications/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`Network response was not ok: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Application data:', data);
        
        if (data.success) {
            currentApplication = data.data;
            previousStatus = currentApplication.status;
            displayApplicationDetails();
            
            // Load review activities
            loadReviewActivities();
            
            // Load individual documents
            if (currentApplication.document_links) {
                displayDocumentsList(currentApplication.document_links);
            } else {
                showEmptyDocuments();
            }
        } else {
            showErrorModal(data.message || 'Application not found');
            showError();
        }
    } catch (error) {
        console.error('Error loading application:', error);
        showErrorModal('Failed to load application details: ' + error.message);
        showError();
    }
}

    // Load review activities
    async function loadReviewActivities() {
        try {
            const response = await fetch(`/applicant/applications/${applicationId}/review-activities`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities) {
                    console.log('Review activities:', data.activities);
                    displayReviewActivities(data.activities);
                    displayAllReviewers(data.activities);
                } else {
                    showEmptyReviewers();
                    showEmptyActivities();
                }
            } else {
                console.error('Failed to load review activities');
                showEmptyReviewers();
                showEmptyActivities();
            }
        } catch (error) {
            console.error('Error loading review activities:', error);
            showEmptyReviewers();
            showEmptyActivities();
        }
    }

    // Display all reviewers who have worked on the application
    function displayAllReviewers(activities) {
        const reviewersContainer = document.getElementById('reviewers-container');
        
        if (!activities || activities.length === 0) {
            showEmptyReviewers();
            return;
        }
        
        // Group activities by reviewer
        const reviewerMap = new Map();
        
        activities.forEach(activity => {
            if (activity.reviewer && activity.reviewer.name !== 'System') {
                const reviewerId = activity.reviewer.id || activity.reviewer.name;
                
                if (!reviewerMap.has(reviewerId)) {
                    reviewerMap.set(reviewerId, {
                        id: reviewerId,
                        name: activity.reviewer.name,
                        role: activity.reviewer.role || 'Staff',
                        initials: activity.reviewer.initials || getInitials(activity.reviewer.name),
                        actions: [],
                        lastActionDate: null,
                        actionCount: 0
                    });
                }
                
                const reviewer = reviewerMap.get(reviewerId);
                reviewer.actions.push({
                    action: activity.action,
                    date: new Date(activity.created_at),
                    new_status: activity.new_status,
                    old_status: activity.old_status
                });
                reviewer.actionCount++;
                
                // Update last action date
                const actionDate = new Date(activity.created_at);
                if (!reviewer.lastActionDate || actionDate > reviewer.lastActionDate) {
                    reviewer.lastActionDate = actionDate;
                    reviewer.lastAction = activity.action;
                    reviewer.lastActionNewStatus = activity.new_status;
                }
            }
        });
        
        const reviewers = Array.from(reviewerMap.values()).sort((a, b) => {
            if (!a.lastActionDate) return 1;
            if (!b.lastActionDate) return -1;
            return b.lastActionDate - a.lastActionDate;
        });
        
        if (reviewers.length === 0) {
            showEmptyReviewers();
            return;
        }
        
        reviewersContainer.innerHTML = '';
        
        reviewers.forEach(reviewer => {
            // Determine status based on last action
            let statusText = 'Reviewed';
            let statusClass = 'bg-blue-100 text-blue-600';
            
            if (reviewer.lastAction) {
                if (reviewer.lastAction === 'status_updated') {
                    if (reviewer.lastActionNewStatus === 'approved') {
                        statusText = 'Approved';
                        statusClass = 'bg-green-100 text-green-600';
                    } else if (reviewer.lastActionNewStatus === 'rejected') {
                        statusText = 'Rejected';
                        statusClass = 'bg-red-100 text-red-600';
                    } else if (reviewer.lastActionNewStatus === 'under-review') {
                        statusText = 'Under Review';
                        statusClass = 'bg-purple-100 text-purple-600';
                    } else if (reviewer.lastActionNewStatus === 'for-release') {
                        statusText = 'For Release';
                        statusClass = 'bg-blue-100 text-blue-600';
                    } else if (reviewer.lastActionNewStatus === 'verified') {
                        statusText = 'Completed';
                        statusClass = 'bg-emerald-100 text-emerald-600';
                    } else if (reviewer.lastActionNewStatus === 'pending') {
                        statusText = 'Pending Review';
                        statusClass = 'bg-yellow-100 text-yellow-600';
                    } else {
                        statusText = 'Status Updated';
                        statusClass = 'bg-purple-100 text-purple-600';
                    }
                } else if (reviewer.lastAction === 'note_added') {
                    statusText = 'Added Note';
                    statusClass = 'bg-yellow-100 text-yellow-600';
                } else if (reviewer.lastAction === 'hard_copy_received') {
                    statusText = 'Received Hard Copy';
                    statusClass = 'bg-indigo-100 text-indigo-600';
                } else if (reviewer.lastAction === 'application_created') {
                    statusText = 'Created Application';
                    statusClass = 'bg-emerald-100 text-emerald-600';
                }
            }
            
            // Format time
            const timeAgo = reviewer.lastActionDate ? getTimeAgo(reviewer.lastActionDate) : 'No actions';
            const exactDateTime = reviewer.lastActionDate ? formatExactDateTime(reviewer.lastActionDate) : '';
            
            reviewersContainer.innerHTML += `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition animate-fade-in">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xl font-bold">
                        <span>${reviewer.initials}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-800">${reviewer.name}</p>
                                <p class="text-xs text-gray-500">${reviewer.role}</p>
                            </div>
                            <span class="text-xs px-2 py-1 ${statusClass} rounded-full whitespace-nowrap">${statusText}</span>
                        </div>
                        ${reviewer.lastActionDate ? `
                        <div class="mt-2">
                            <p class="text-xs text-gray-400">Last action ${timeAgo}</p>
                            <p class="text-xs text-gray-300 mt-1">Actions: ${reviewer.actionCount}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
    }

    // Show empty reviewers state
    function showEmptyReviewers() {
        const reviewersContainer = document.getElementById('reviewers-container');
        reviewersContainer.innerHTML = `
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg animate-fade-in">
                <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xl font-bold">
                    <span>OB</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Office of the Building Official</p>
                    <p class="text-xs text-gray-500">Reviewing Officer</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full">Waiting for review</span>
                    </div>
                </div>
            </div>
        `;
    }

    // Show empty activities state
    function showEmptyActivities() {
        const activityLog = document.getElementById('activity-log');
        activityLog.innerHTML = `
            <div class="text-center py-8 text-gray-500 animate-fade-in">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm">No activity yet</p>
            </div>
        `;
    }

    // Helper function to get initials from name
    function getInitials(name) {
        if (!name) return 'OB';
        return name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
    }

    // Display review activities (only show last 3)
function displayReviewActivities(activities) {
    const activityLog = document.getElementById('activity-log');
    
    if (!activities || activities.length === 0) {
        showEmptyActivities();
        return;
    }
    
    // Sort activities by date (newest first) and take only the first 3
    const sortedActivities = [...activities].sort((a, b) => 
        new Date(b.created_at) - new Date(a.created_at)
    ).slice(0, 3);
    
    let html = '';
    sortedActivities.forEach(activity => {
        const date = new Date(activity.created_at);
        const exactDateTime = formatExactDateTime(date);
        const timeAgo = getTimeAgo(date);
        
        // Determine icon based on action
        let iconColor = 'bg-blue-100';
        let iconTextColor = 'text-blue-600';
        let iconSvg = `
            <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        `;
        
        if (activity.action === 'status_updated') {
            if (activity.new_status && activity.new_status === 'approved') {
                iconColor = 'bg-green-100';
                iconTextColor = 'text-green-600';
                iconSvg = `
                    <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                `;
            } else if (activity.new_status && activity.new_status === 'rejected') {
                iconColor = 'bg-red-100';
                iconTextColor = 'text-red-600';
                iconSvg = `
                    <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                `;
            } else if (activity.new_status && activity.new_status === 'document-verification') {
                iconColor = 'bg-purple-100';
                iconTextColor = 'text-purple-600';
                iconSvg = `
                    <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;
            } else {
                iconColor = 'bg-purple-100';
                iconTextColor = 'text-purple-600';
            }
        } else if (activity.action === 'note_added') {
            iconColor = 'bg-yellow-100';
            iconTextColor = 'text-yellow-600';
            iconSvg = `
                <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            `;
        } else if (activity.action === 'hard_copy_received') {
            iconColor = 'bg-indigo-100';
            iconTextColor = 'text-indigo-600';
            iconSvg = `
                <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            `;
        } else if (activity.action === 'application_created') {
            iconColor = 'bg-emerald-100';
            iconTextColor = 'text-emerald-600';
            iconSvg = `
                <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            `;
        } else if (activity.action === 'document_verified') {
            iconColor = 'bg-green-100';
            iconTextColor = 'text-green-600';
            iconSvg = `
                <svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            `;
        }
        
        const reviewerName = activity.reviewer ? activity.reviewer.name : 'System';
        const reviewerRole = activity.reviewer ? activity.reviewer.role : '';
        
        // Format action display
        let actionDisplay = activity.action_display || activity.action;
        if (activity.action === 'status_updated') {
            if (activity.old_status && activity.new_status) {
                actionDisplay = `Status changed from ${formatStatus(activity.old_status)} to ${formatStatus(activity.new_status)}`;
            } else {
                actionDisplay = 'Status updated';
            }
        } else if (activity.action === 'document_verified') {
            actionDisplay = 'Documents Verified';
        } else {
            actionDisplay = actionDisplay.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }
        
        html += `
            <div class="flex gap-3 p-2 hover:bg-gray-50 rounded-lg transition animate-fade-in">
                <div class="w-8 h-8 ${iconColor} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    ${iconSvg}
                </div>
                <div class="flex-1">
                    <div class="flex flex-col">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-gray-800">${actionDisplay}</p>
                        </div>
                        ${activity.remarks ? `<p class="text-xs text-gray-600 mt-1">${activity.remarks}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium">${reviewerName}</span>
                            ${reviewerRole ? `<span class="text-gray-400"> • ${reviewerRole}</span>` : ''}
                        </p>
                        <div class="mt-1">
                            <p class="text-xs text-gray-400" title="${exactDateTime}">${timeAgo}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // If there are more than 3 activities, show a message
    if (activities.length > 3) {
        html += `
            <div class="text-center text-xs text-gray-400 pt-2">
                +${activities.length - 3} more activities
            </div>
        `;
    }
    
    activityLog.innerHTML = html;
}

    // Display application details
    function displayApplicationDetails() {
        // Hide loading, show content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('application-content').classList.remove('hidden');

        // Update application number and dates
        document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
        
        if (currentApplication.created_at) {
            const submittedDate = new Date(currentApplication.created_at + ' UTC');
            document.getElementById('submitted-date').textContent = submittedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            document.getElementById('step-submitted-date').textContent = submittedDate.toLocaleDateString('en-US', { 
                month: 'short', day: 'numeric' 
            });
        }
        
        if (currentApplication.updated_at) {
            const updatedDate = new Date(currentApplication.updated_at + ' UTC');
            document.getElementById('updated-date').textContent = updatedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
        }

        // Update status badge and progress
        updateStatusUI(currentApplication.status);
        updateTimeline(currentApplication.status);
        updateProgress(currentApplication.status);

        // Update Google Drive link
        if (currentApplication.google_drive_link) {
            const driveLink = document.getElementById('drive-link');
            driveLink.href = currentApplication.google_drive_link;
            document.getElementById('drive-link-text').textContent = currentApplication.google_drive_link.length > 50 ? 
                currentApplication.google_drive_link.substring(0, 50) + '...' : 
                currentApplication.google_drive_link;
            driveLink.classList.remove('pointer-events-none', 'text-gray-500');
            driveLink.classList.add('text-[#155386]');
            driveLink.setAttribute('target', '_blank');
            
            document.getElementById('document-status').textContent = 'Available';
            document.getElementById('document-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
        } else {
            const driveLink = document.getElementById('drive-link');
            driveLink.innerHTML = '<span class="text-gray-500">No link provided</span>';
            driveLink.href = '#';
            driveLink.classList.add('pointer-events-none', 'text-gray-500');
            driveLink.classList.remove('text-[#155386]');
            driveLink.removeAttribute('target');
            
            document.getElementById('document-status').textContent = 'Not Uploaded';
            document.getElementById('document-status').className = 'text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full';
        }

        // Update hard copy status
        updateHardCopyStatus(currentApplication.hard_copy_received);

        // Check if document verification is in progress
        if (currentApplication.status === 'document-verification') {
            showDocumentVerificationStatus(true);
        } else {
            showDocumentVerificationStatus(false);
        }

        loadIndividualDocuments();
    }

    // Show/hide document verification status
    function showDocumentVerificationStatus(show) {
        const verificationStatus = document.getElementById('document-verification-status');
        const verificationAlert = document.getElementById('document-verification-alert');
        
        if (show) {
            if (verificationStatus) verificationStatus.classList.remove('hidden');
            if (verificationAlert) verificationAlert.classList.remove('hidden');
        } else {
            if (verificationStatus) verificationStatus.classList.add('hidden');
            if (verificationAlert) verificationAlert.classList.add('hidden');
        }
    }

    // Helper function to format exact date and time
    function formatExactDateTime(date) {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
    }

    // Helper function to format status
    function formatStatus(status) {
        if (!status) return '';
        return status.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    // Helper function to get time ago
    function getTimeAgo(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return diffMins + ' minute' + (diffMins > 1 ? 's' : '') + ' ago';
        if (diffHours < 24) return diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
        if (diffDays < 7) return diffDays + ' day' + (diffDays > 1 ? 's' : '') + ' ago';
        
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    // Update status UI
    function updateStatusUI(status) {
        const statusBadge = document.getElementById('status-badge');
        const currentStatusBadge = document.getElementById('current-status-badge');
        
        const statusConfig = {
            'draft': { color: 'gray', text: 'Draft' },
            'pending': { color: 'yellow', text: 'Pending Review' },
            'under-review': { color: 'purple', text: 'Under Review' },
            'document-verification': { color: 'purple', text: 'Document Verification' },
            'approved': { color: 'green', text: 'Approved' },
            'for-release': { color: 'blue', text: 'For Release' },
            'verified': { color: 'emerald', text: 'Completed' },
            'rejected': { color: 'red', text: 'Rejected' }
        };

        const config = statusConfig[status] || { color: 'gray', text: status || 'Unknown' };
        
        statusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium transition-all duration-500`;
        statusBadge.textContent = config.text;
        
        currentStatusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium transition-all duration-500`;
        currentStatusBadge.textContent = config.text;
    }

    // Update timeline based on status
    function updateTimeline(status) {
        const steps = ['submitted', 'under-review', 'verification', 'approval', 'release'];
        const currentStepIndex = getStepIndex(status);
        
        steps.forEach((step, index) => {
            const stepElement = document.getElementById(`step-${step}`);
            if (!stepElement) return;
            
            const circle = stepElement.querySelector('.w-10.h-10');
            const text = stepElement.querySelector('.text-sm');
            const dateElement = document.getElementById(`step-${step}-date`);
            
            if (index <= currentStepIndex) {
                // Completed step with animation
                if (circle) {
                    circle.className = 'w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500 transform scale-animation';
                    circle.innerHTML = '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                }
                if (text) text.className = 'text-sm font-medium text-gray-800 transition-all duration-500';
                
                if (index === currentStepIndex) {
                    // Current step - show "In Progress" with pulsing effect
                    if (dateElement) {
                        dateElement.textContent = 'In Progress';
                        dateElement.className = 'text-xs text-[#155386] font-medium animate-pulse';
                    }
                    
                    // Add processing class for glow effect
                    stepElement.classList.add('step-processing');
                } else {
                    stepElement.classList.remove('step-processing');
                    
                    if (index === 0 && currentApplication?.created_at) {
                        // Submitted date
                        const date = new Date(currentApplication.created_at + ' UTC');
                        dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        dateElement.className = 'text-xs text-gray-500';
                    } else if (index === 1 && currentApplication?.under_review_at) {
                        // Under review date
                        const date = new Date(currentApplication.under_review_at + ' UTC');
                        dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        dateElement.className = 'text-xs text-gray-500';
                    } else if (index === 2 && currentApplication?.verification_at) {
                        // Verification date
                        const date = new Date(currentApplication.verification_at + ' UTC');
                        dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        dateElement.className = 'text-xs text-gray-500';
                    } else if (index === 3 && currentApplication?.approved_at) {
                        // Approval date
                        const date = new Date(currentApplication.approved_at + ' UTC');
                        dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        dateElement.className = 'text-xs text-gray-500';
                    } else if (index === 4 && currentApplication?.release_at) {
                        // Release date
                        const date = new Date(currentApplication.release_at + ' UTC');
                        dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        dateElement.className = 'text-xs text-gray-500';
                    }
                }
            } else {
                // Future step
                if (circle) {
                    circle.className = 'w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500';
                    circle.innerHTML = `<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>`;
                }
                if (text) text.className = 'text-sm font-medium text-gray-400 transition-all duration-500';
                if (dateElement) {
                    dateElement.textContent = '';
                    dateElement.className = 'text-xs text-gray-400';
                }
                stepElement.classList.remove('step-processing');
            }
        });

        // Update progress line width with animation
        const progressLine = document.getElementById('progress-line');
        if (progressLine) {
            const width = currentStepIndex >= 0 ? ((currentStepIndex + 1) / steps.length) * 100 : 0;
            progressLine.style.width = width + '%';
        }
    }

    // Get step index from status
    function getStepIndex(status) {
        const stepMap = {
            'draft': -1,
            'pending': 0,
            'under-review': 1,
            'document-verification': 2,
            'approved': 3,
            'for-release': 4,
            'verified': 4,
            'rejected': -1
        };
        return stepMap[status] !== undefined ? stepMap[status] : -1;
    }

    // Update progress based on status
    function updateProgress(status) {
        const progressMap = {
            'draft': 0,
            'pending': 20,
            'under-review': 40,
            'document-verification': 60,
            'approved': 80,
            'for-release': 90,
            'verified': 100,
            'rejected': 100
        };
        
        const progress = progressMap[status] || 0;
        document.getElementById('progress-percentage').textContent = progress + '%';
        document.getElementById('progress-bar').style.width = progress + '%';
    }

    // Update hard copy status
    function updateHardCopyStatus(received) {
        const hardcopyNotice = document.getElementById('hardcopy-notice');
        const hardcopyReceivedNotice = document.getElementById('hardcopy-received-notice');
        const hardcopyBadge = document.getElementById('hardcopy-badge');
        const hardcopyMessage = document.getElementById('hardcopy-message');
        const hardcopySidebar = document.getElementById('hardcopy-status-sidebar');
        
        if (received) {
            if (hardcopyNotice) hardcopyNotice.classList.add('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.remove('hidden');
            hardcopyBadge.textContent = 'Received';
            hardcopyBadge.className = 'text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full transition-all duration-500';
            hardcopyMessage.textContent = 'Hard copies received by OBO';
            hardcopySidebar.className = 'mt-4 p-3 bg-green-50 rounded-lg transition-all duration-500';
        } else {
            if (hardcopyNotice) hardcopyNotice.classList.remove('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.add('hidden');
            hardcopyBadge.textContent = 'Pending';
            hardcopyBadge.className = 'text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full transition-all duration-500';
            hardcopyMessage.textContent = 'Submit originals to OBO';
            hardcopySidebar.className = 'mt-4 p-3 bg-blue-50 rounded-lg transition-all duration-500';
        }
    }

    // Copy drive link
    function copyDriveLink() {
        const link = currentApplication?.google_drive_link;
        if (link) {
            navigator.clipboard.writeText(link).then(() => {
                showSuccessModal('Link copied to clipboard!');
            }).catch(() => {
                showErrorModal('Failed to copy link');
            });
        } else {
            showErrorModal('No link to copy');
        }
    }

    // Download application
    function downloadApplication() {
        showSuccessModal('Download feature coming soon');
    }

    // Contact support
    function contactSupport() {
        window.location.href = 'mailto:support@konstructo.com';
    }

    // Send message to reviewer
    function sendMessage() {
        showSuccessModal('Messaging feature coming soon');
    }

    // Request assistance
    function requestAssistance() {
        showSuccessModal('Assistance request sent');
    }

    // Show error state
    function showError() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
    }

    // Modal functions
    function showErrorModal(message) {
        document.getElementById('error-modal-message').textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showSuccessModal(message) {
        document.getElementById('success-modal-message').textContent = message;
        document.getElementById('success-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    }

    function closeSuccessModal() {
        document.getElementById('success-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Setup modals
    function setupModals() {
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        
        if (errorModal) {
            errorModal.addEventListener('click', function(e) {
                if (e.target === errorModal) closeErrorModal();
            });
        }
        
        if (successModal) {
            successModal.addEventListener('click', function(e) {
                if (e.target === successModal) closeSuccessModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeErrorModal();
                closeSuccessModal();
            }
        });
    }

    // Clean up interval when leaving the page
    window.addEventListener('beforeunload', function() {
        if (updateCheckInterval) {
            clearInterval(updateCheckInterval);
        }
    });
    // Load and display individual documents
async function loadIndividualDocuments() {
    try {
        const response = await fetch(`/applicant/application-details/${applicationId}/documents`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.documents) {
            displayDocumentsList(data.documents);
        } else {
            showEmptyDocuments();
        }
    } catch (error) {
        console.error('Error loading documents:', error);
        showEmptyDocuments();
    }
}

// Display documents list
function displayDocumentsList(documents) {
    const container = document.getElementById('documents-list');
    
    if (!documents || Object.keys(documents).length === 0) {
        showEmptyDocuments();
        return;
    }
    
    // Document names mapping
    const documentNames = {
        'app_letter_link': 'Application Letter',
        'bp_forms_link': 'Building Permit Forms',
        'arch_plans_link': 'Architectural Plans',
        'structural_plans_link': 'Civil/Structural Plans',
        'electrical_plans_link': 'Electrical Plans',
        'plumbing_plans_link': 'Sanitary/Plumbing Plans',
        'mechanical_plans_link': 'Mechanical Plans',
        'fencing_plans_link': 'Fencing Plans',
        'ownership_link': 'Proof of Ownership',
        'bom_link': 'Bill of Materials',
        'structural_analysis_link': 'Structural Design Analysis',
        'barangay_clearance_link': 'Barangay Clearance',
        'valid_id_link': 'Valid ID',
        'cshp_link': 'CSHP from DOLE (Optional)'
    };
    
    // Document icons mapping
    const documentIcons = {
        'app_letter_link': 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'bp_forms_link': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'arch_plans_link': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'structural_plans_link': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'electrical_plans_link': 'M13 10V3L4 14h7v7l9-11h-7z',
        'plumbing_plans_link': 'M5 3v4M3 5h4M6 17v4m-2-2h4M5 3v4M3 5h4M6 17v4m-2-2h4M12 2v20M2 12h20',
        'mechanical_plans_link': 'M12 4v16m8-8H4M12 4L8 8m4-4l4 4m-4 4l4 4m-4-4l-4 4',
        'fencing_plans_link': 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
        'ownership_link': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'bom_link': 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'structural_analysis_link': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'barangay_clearance_link': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'valid_id_link': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'cshp_link': 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'
    };
    
    let html = '';
    let hasDocuments = false;
    
    for (const [key, value] of Object.entries(documents)) {
        if (value && value.trim() !== '') {
            hasDocuments = true;
            const docName = documentNames[key] || key.replace(/_/g, ' ').replace(/_link$/, '').replace(/\b\w/g, l => l.toUpperCase());
            const iconPath = documentIcons[key] || 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
            
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">${docName}</p>
                            <p class="text-xs text-gray-400 truncate">${value.length > 60 ? value.substring(0, 60) + '...' : value}</p>
                        </div>
                    </div>
                    <a href="${value}" target="_blank" class="text-[#155386] hover:text-[#1F363D] text-sm flex items-center gap-1 flex-shrink-0 ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span class="hidden sm:inline">View</span>
                    </a>
                </div>
            `;
        }
    }
    
    if (!hasDocuments) {
        showEmptyDocuments();
    } else {
        container.innerHTML = html;
    }
}

// Show empty documents state
function showEmptyDocuments() {
    const container = document.getElementById('documents-list');
    container.innerHTML = `
        <div class="text-center py-8 text-gray-500 animate-fade-in">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-sm">No documents uploaded yet</p>
            <p class="text-xs text-gray-400 mt-1">Please upload your documents in Step 2</p>
        </div>
    `;
}
</script>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Modal animations */
    #error-modal, #success-modal {
        transition: opacity 0.2s ease-in-out;
    }

    #error-modal .bg-white, #success-modal .bg-white {
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Fade in animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    /* Slide down animation */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-down {
        animation: slideDown 0.3s ease-out;
    }

    /* Scale animation for progress updates */
    @keyframes scalePulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .scale-animation {
        animation: scalePulse 0.5s ease-in-out;
    }

    /* Pulse animation for status badges */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .animate-pulse {
        animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Bounce animation for notification */
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    .animate-bounce {
        animation: bounce 1s infinite;
    }

    /* Disable pointer events for disabled links */
    .pointer-events-none {
        pointer-events: none;
    }

    /* Link break styling */
    .break-all {
        word-break: break-all;
    }

    /* Hover effects */
    .hover\:bg-gray-100:hover {
        background-color: #f3f4f6;
    }

    /* Step item hover effect */
    .step-item {
        transition: transform 0.3s ease;
    }

    .step-item:hover {
        transform: translateY(-2px);
    }

    /* Transition for smooth updates */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .duration-500 {
        transition-duration: 500ms;
    }

    .duration-700 {
        transition-duration: 700ms;
    }

    .ease-out {
        transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
    /* Progress Bar Loading Animation */
@keyframes progressLoading {
    0% {
        transform: translateX(-100%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.1) 25%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0.1) 75%,
            transparent 100%
        );
    }
    25% {
        transform: translateX(-50%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.2) 25%,
            rgba(255, 255, 255, 0.5) 50%,
            rgba(255, 255, 255, 0.2) 75%,
            transparent 100%
        );
    }
    50% {
        transform: translateX(0%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.3) 25%,
            rgba(255, 255, 255, 0.8) 50%,
            rgba(255, 255, 255, 0.3) 75%,
            transparent 100%
        );
    }
    75% {
        transform: translateX(50%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.2) 25%,
            rgba(255, 255, 255, 0.5) 50%,
            rgba(255, 255, 255, 0.2) 75%,
            transparent 100%
        );
    }
    100% {
        transform: translateX(100%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.1) 25%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0.1) 75%,
            transparent 100%
        );
    }
}

.loading-progress-animation {
    position: relative;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%,
        rgba(255, 255, 255, 0.2) 25%,
        rgba(255, 255, 255, 0.6) 50%,
        rgba(255, 255, 255, 0.2) 75%,
        transparent 100%
    );
    background-size: 200% 100%;
    animation: progressLoading 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    mix-blend-mode: overlay;
}

/* Line Loading Animation */
@keyframes lineLoading {
    0% {
        transform: translateX(-100%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.1) 25%,
            rgba(255, 255, 255, 0.4) 50%,
            rgba(255, 255, 255, 0.1) 75%,
            transparent 100%
        );
    }
    50% {
        transform: translateX(0%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.2) 25%,
            rgba(255, 255, 255, 0.8) 50%,
            rgba(255, 255, 255, 0.2) 75%,
            transparent 100%
        );
    }
    100% {
        transform: translateX(100%);
        background: linear-gradient(90deg, 
            transparent 0%,
            rgba(255, 255, 255, 0.1) 25%,
            rgba(255, 255, 255, 0.4) 50%,
            rgba(255, 255, 255, 0.1) 75%,
            transparent 100%
        );
    }
}

.loading-line-animation {
    position: relative;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%,
        rgba(255, 255, 255, 0.2) 25%,
        rgba(255, 255, 255, 0.6) 50%,
        rgba(255, 255, 255, 0.2) 75%,
        transparent 100%
    );
    background-size: 200% 100%;
    animation: lineLoading 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    mix-blend-mode: overlay;
}

/* Ping animation for live indicator */
@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping {
    animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Step glow for current step */
@keyframes stepGlow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(21, 83, 134, 0.3);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 20px rgba(64, 121, 140, 0.6);
        transform: scale(1.05);
    }
}

.step-processing .w-10 {
    animation: stepGlow 2s ease-in-out infinite;
    border: 2px solid rgba(21, 83, 134, 0.3);
}
</style>
@endsection