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
                    <p class="text-sm text-gray-700 mt-1">After approval of your application, please submit the original hard copies of ALL documents to the Office of the Building Official (OBO) to complete your application.</p>
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

        <!-- Assessment Notice -->
        <div id="assessment-notice" class="mb-6 p-4 bg-indigo-100 border-l-4 border-indigo-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Building Permit Fee Assessment</h4>
                    <p id="assessment-total" class="text-sm text-gray-700 mt-1">Total Fee: ₱0.00</p>
                    <p class="text-xs text-gray-500 mt-1">Please prepare the exact amount for payment</p>
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
                            <h1 id="project-title" class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                            <span id="status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium transition-all duration-500">Pending Review</span>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Application Number</span>
                                <span id="application-number" class="font-mono font-medium text-[#155386]"></span>
                            </div>
                            <span class="text-gray-300 hidden sm:inline">|</span>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Submitted</span>
                                <span id="submitted-date" class="font-medium text-gray-700"></span>
                            </div>
                            <span class="text-gray-300 hidden sm:inline">|</span>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Last Updated</span>
                                <span id="updated-date" class="font-medium text-gray-700"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="downloadApplicationSummary()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Summary
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
                
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                    <div id="progress-bar" class="absolute inset-0 bg-gradient-to-r from-[#155386] to-[#40798C] h-full rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
                    <div class="absolute inset-0 h-full w-full overflow-hidden">
                        <div id="animated-loading-overlay" class="h-full w-full loading-progress-animation"></div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3" />
                    </svg>
                    Real-time updates active - Application status is being monitored
                </p>
            </div>

            <div class="relative overflow-x-auto pb-4">
                <div class="relative min-w-[600px]">
                    <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200"></div>
                    <div id="progress-line" class="absolute top-5 left-0 w-0 h-0.5 bg-[#155386] transition-all duration-700 ease-out" style="width: 0%"></div>
                    <div class="absolute top-5 left-0 w-full h-0.5 overflow-hidden">
                        <div class="w-full h-full loading-line-animation"></div>
                    </div>
                    
                    <div class="relative flex justify-between">
                        <div id="step-submitted" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">1</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">Submitted</p>
                            <p id="step-submitted-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                        <div id="step-under-review" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">2</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">Under Review</p>
                            <p id="step-under-review-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                        <div id="step-verification" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">3</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">Document Verification</p>
                            <p id="step-verification-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                        <div id="step-assessment" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">4</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">Assessment</p>
                            <p id="step-assessment-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                        <div id="step-approval" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">5</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">Approval</p>
                            <p id="step-approval-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                        <div id="step-release" class="text-center step-item">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                                <span class="text-lg font-bold text-gray-400 step-icon">6</span>
                            </div>
                            <p class="text-sm font-medium text-gray-400 transition-all duration-500">For Release</p>
                            <p id="step-release-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Project Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Project Title</p>
                            <p id="info-project-title" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Project Type</p>
                            <p id="info-project-type" class="text-sm font-medium text-gray-800 capitalize">-</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Project Location</p>
                            <p id="info-project-location" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Lot Area</p>
                            <p id="info-lot-area" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Floor Area</p>
                            <p id="info-floor-area" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Number of Floors</p>
                            <p id="info-num-floors" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Estimated Cost</p>
                            <p id="info-estimated-cost" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Project Description</p>
                            <p id="info-description" class="text-sm text-gray-600">-</p>
                        </div>
                    </div>
                </div>

                <!-- Owner Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Owner/Applicant Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Full Name</p>
                            <p id="info-owner-name" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Contact Number</p>
                            <p id="info-contact-number" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Address</p>
                            <p id="info-owner-address" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email Address</p>
                            <p id="info-owner-email" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <!-- Professional Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Professional Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Architect's Name</p>
                            <p id="info-architect-name" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Architect's License</p>
                            <p id="info-architect-license" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Civil Engineer's Name</p>
                            <p id="info-engineer-name" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Engineer's License</p>
                            <p id="info-engineer-license" class="text-sm font-medium text-gray-800">-</p>
                        </div>
                    </div>
                </div>

                <!-- Staff/Reviewers List Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Application Reviewers</h2>
                    <div id="reviewers-container" class="space-y-4">
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
                                <div class="flex flex-wrap items-center gap-2 mt-1">
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

                <!-- Important Notes Card -->
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
                                <li>Processing time may take 20 working days upon complete submission</li>
                                <li>For urgent concerns, please contact the Building Official's office directly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-8">
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
                                <span id="estimated-time" class="text-sm font-medium text-gray-800">20 working days</span>
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

                        <!-- Assessment Fee Card -->
                        <div id="assessment-fee-card" class="mt-4 p-3 bg-indigo-50 rounded-lg hidden transition-all duration-500">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                                    <span class="text-xs font-medium text-gray-700">Building Permit Fee:</span>
                                </div>
                                <span id="assessment-fee-amount" class="text-sm font-bold text-indigo-700">₱0.00</span>
                            </div>
                            <div id="assessment-fee-details" class="mt-2 text-xs text-gray-500 space-y-1">
                                <!-- Fee breakdown will be shown here -->
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Please prepare the exact amount for payment at the OBO.</p>
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

                        <!-- FSEC Document Card (Fire Safety Evaluation Clearance) -->
                        <div id="fsec-card" class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200 transition-all duration-500 hidden">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-red-700">Fire Safety Evaluation Clearance (FSEC)</span>
                            </div>
                            <div id="fsec-content">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span id="fsec-filename" class="text-xs text-gray-600">No document uploaded yet</span>
                                    </div>
                                    <a id="fsec-link" href="#" target="_blank" class="text-xs text-red-600 hover:text-red-800 underline flex items-center gap-1 opacity-50 pointer-events-none">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                        </svg>
                                        View
                                    </a>
                                </div>
                                <div id="fsec-upload-info" class="mt-2">
                                    <p id="fsec-upload-date" class="text-xs text-gray-400"></p>
                                    <p id="fsec-uploaded-by" class="text-xs text-gray-400"></p>
                                </div>
                            </div>
                        </div>

                        <!-- BFP Comments Card -->
                        <div id="bfp-comments-card" class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-200 transition-all duration-500 hidden">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-amber-700">BFP Comments</span>
                            </div>
                            <div id="bfp-comments-content">
                                <p id="bfp-comments-text" class="text-xs text-gray-600 italic">No comments yet</p>
                                <div class="mt-2">
                                    <p id="bfp-comments-date" class="text-xs text-gray-400"></p>
                                    <p id="bfp-comments-by" class="text-xs text-gray-400"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-2">
                            <a href="mailto:obo@legazpi.gov.ph?subject=Building Permit Application Inquiry - {{ $applicationId ?? '' }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Email OBO Support
                            </a>
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
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Loading activities...</p>
                            </div>
                        </div>
                        <button onclick="viewFullHistory()" class="mt-4 text-sm text-[#155386] hover:text-[#40798C] font-medium w-full text-center inline-block py-2 border-t border-gray-100 hover:bg-gray-50 transition rounded-b-lg">
                            View Full History →
                        </button>
                    </div>
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

<script>
    // CSRF token helper
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) console.warn('CSRF token meta tag not found');
        return token || '{{ csrf_token() }}';
    }

    // Get application ID from URL path
    function getApplicationIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const lastPart = pathParts[pathParts.length - 1];
        // Check if last part is numeric
        if (lastPart && !isNaN(lastPart)) {
            return lastPart;
        }
        return null;
    }
    
    let applicationId = getApplicationIdFromUrl();
    let currentApplication = null;
    let previousStatus = null;
    let updateCheckInterval = null;
    let currentAssessment = null;
    let currentBfpData = null;

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

    // Load application details on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Application ID from URL:', applicationId);
        if (applicationId && !isNaN(applicationId)) {
            loadApplicationDetails();
            startRealTimeUpdates();
        } else {
            showError();
        }
        setupModals();
    });

    // Start real-time updates
    function startRealTimeUpdates() {
        updateCheckInterval = setInterval(checkForUpdates, 30000); // Check every 30 seconds
    }

    // Check for updates
    async function checkForUpdates() {
        if (!applicationId) return;
        
        try {
            const response = await fetch(`/applicant/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    const newApplication = data.data;
                    
                    if (previousStatus && previousStatus !== newApplication.status) {
                        showUpdateNotification('Application status updated to ' + formatStatusDisplay(newApplication.status));
                        animateStatusChange();
                        
                        if (newApplication.status === 'document-verification') {
                            showDocumentVerificationAlert();
                        }
                        
                        if (previousStatus === 'document-verification' && newApplication.status === 'approved') {
                            showUpdateNotification('Documents have been verified and approved!');
                        }
                    }
                    
                    currentApplication = newApplication;
                    displayApplicationDetails();
                    
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

    // Load BFP data
    async function loadBFPData() {
        if (!applicationId) return;
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/bfp-data`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentBfpData = data.data;
                    displayBFPData();
                }
            }
        } catch (error) {
            console.error('Error loading BFP data:', error);
        }
    }

    // Display BFP data (FSEC and comments)
    function displayBFPData() {
        if (!currentBfpData) return;
        
        // FSEC Card
        const fsecCard = document.getElementById('fsec-card');
        if (currentBfpData.fsec_link) {
            fsecCard.classList.remove('hidden');
            const fsecLink = document.getElementById('fsec-link');
            const fsecFilename = document.getElementById('fsec-filename');
            const fsecUploadDate = document.getElementById('fsec-upload-date');
            const fsecUploadedBy = document.getElementById('fsec-uploaded-by');
            
            fsecLink.href = currentBfpData.fsec_link;
            fsecLink.classList.remove('opacity-50', 'pointer-events-none');
            fsecFilename.textContent = currentBfpData.fsec_filename || 'FSEC Document';
            
            if (currentBfpData.fsec_uploaded_at) {
                const uploadDate = new Date(currentBfpData.fsec_uploaded_at);
                fsecUploadDate.textContent = `Uploaded: ${uploadDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}`;
            }
            
            if (currentBfpData.bfp_user_name) {
                fsecUploadedBy.textContent = `Uploaded by: ${currentBfpData.bfp_user_name}`;
            }
        } else {
            fsecCard.classList.add('hidden');
        }
        
        // BFP Comments Card
        const bfpCommentsCard = document.getElementById('bfp-comments-card');
        if (currentBfpData.bfp_comments && currentBfpData.bfp_comments.trim() !== '') {
            bfpCommentsCard.classList.remove('hidden');
            const commentsText = document.getElementById('bfp-comments-text');
            const commentsDate = document.getElementById('bfp-comments-date');
            const commentsBy = document.getElementById('bfp-comments-by');
            
            commentsText.textContent = currentBfpData.bfp_comments;
            
            if (currentBfpData.bfp_comments_updated_at) {
                const updateDate = new Date(currentBfpData.bfp_comments_updated_at);
                commentsDate.textContent = `Updated: ${updateDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}`;
            }
            
            if (currentBfpData.bfp_user_name) {
                commentsBy.textContent = `By: ${currentBfpData.bfp_user_name}`;
            }
        } else {
            bfpCommentsCard.classList.add('hidden');
        }
    }

    // Load assessment data
    async function loadAssessmentData() {
        if (!applicationId) return;
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/assessment`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentAssessment = data.data;
                    displayAssessmentInfo();
                }
            }
        } catch (error) {
            console.error('Error loading assessment:', error);
        }
    }

    // Display assessment information
    function displayAssessmentInfo() {
        if (currentAssessment && currentAssessment.total_amount) {
            // Show assessment notice
            const assessmentNotice = document.getElementById('assessment-notice');
            const assessmentTotal = document.getElementById('assessment-total');
            if (assessmentNotice && assessmentTotal) {
                assessmentTotal.innerHTML = `Total Fee: ₱${parseFloat(currentAssessment.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                assessmentNotice.classList.remove('hidden');
            }
            
            // Show assessment fee card in sidebar
            const assessmentCard = document.getElementById('assessment-fee-card');
            const feeAmount = document.getElementById('assessment-fee-amount');
            const feeDetails = document.getElementById('assessment-fee-details');
            
            if (assessmentCard && feeAmount) {
                feeAmount.textContent = `₱${parseFloat(currentAssessment.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                assessmentCard.classList.remove('hidden');
                
                // Build fee breakdown
                let breakdownHtml = '';
                if (currentAssessment.line_grade) breakdownHtml += `<div class="flex justify-between"><span>Line Grade:</span><span>₱${parseFloat(currentAssessment.line_grade).toLocaleString()}</span></div>`;
                if (currentAssessment.building_fee) breakdownHtml += `<div class="flex justify-between"><span>Building Fee:</span><span>₱${parseFloat(currentAssessment.building_fee).toLocaleString()}</span></div>`;
                if (currentAssessment.sanitary_fee) breakdownHtml += `<div class="flex justify-between"><span>Sanitary/Plumbing:</span><span>₱${parseFloat(currentAssessment.sanitary_fee).toLocaleString()}</span></div>`;
                if (currentAssessment.mechanical_fee) breakdownHtml += `<div class="flex justify-between"><span>Mechanical Fee:</span><span>₱${parseFloat(currentAssessment.mechanical_fee).toLocaleString()}</span></div>`;
                if (currentAssessment.electrical_fee) breakdownHtml += `<div class="flex justify-between"><span>Electrical Fee:</span><span>₱${parseFloat(currentAssessment.electrical_fee).toLocaleString()}</span></div>`;
                if (currentAssessment.others_amount) breakdownHtml += `<div class="flex justify-between"><span>${currentAssessment.others_description || 'Others'}:</span><span>₱${parseFloat(currentAssessment.others_amount).toLocaleString()}</span></div>`;
                if (currentAssessment.penalties_fines) breakdownHtml += `<div class="flex justify-between"><span>Penalties/Fines:</span><span>₱${parseFloat(currentAssessment.penalties_fines).toLocaleString()}</span></div>`;
                
                if (breakdownHtml) {
                    feeDetails.innerHTML = breakdownHtml;
                } else {
                    feeDetails.innerHTML = '<div class="text-center text-gray-500">Fee breakdown not available</div>';
                }
            }
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
            }, 10000);
        }
    }

    // Animate status change
    function animateStatusChange() {
        const statusBadge = document.getElementById('status-badge');
        const currentStatusBadge = document.getElementById('current-status-badge');
        const progressBar = document.getElementById('progress-bar');
        const progressLine = document.getElementById('progress-line');
        
        if (statusBadge) statusBadge.classList.add('animate-pulse');
        if (currentStatusBadge) currentStatusBadge.classList.add('animate-pulse');
        if (progressBar) progressBar.classList.add('scale-animation');
        if (progressLine) progressLine.classList.add('scale-animation');
        
        setTimeout(() => {
            if (statusBadge) statusBadge.classList.remove('animate-pulse');
            if (currentStatusBadge) currentStatusBadge.classList.remove('animate-pulse');
            if (progressBar) progressBar.classList.remove('scale-animation');
            if (progressLine) progressLine.classList.remove('scale-animation');
        }, 1000);
    }

    // Load application details from API
    async function loadApplicationDetails() {
        if (!applicationId) {
            showError();
            return;
        }
        
        try {
            console.log('Fetching application details for ID:', applicationId);
            
            const response = await fetch(`/applicant/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Application data:', data);
            
            if (data.success && data.data) {
                currentApplication = data.data;
                previousStatus = currentApplication.status;
                displayApplicationDetails();
                loadReviewActivities();
                loadAssessmentData();
                loadBFPData();
                
                // Display documents from the document_links
                if (currentApplication.document_links && Object.keys(currentApplication.document_links).length > 0) {
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

    // Display project information
    function displayProjectInfo(app) {
        document.getElementById('info-project-title').textContent = app.project_title || 'Not provided';
        document.getElementById('info-project-type').textContent = app.project_type || 'Not provided';
        document.getElementById('info-project-location').textContent = app.project_location || 'Not provided';
        document.getElementById('info-lot-area').textContent = app.lot_area ? `${parseFloat(app.lot_area).toLocaleString()} sqm` : 'Not provided';
        document.getElementById('info-floor-area').textContent = app.floor_area ? `${parseFloat(app.floor_area).toLocaleString()} sqm` : 'Not provided';
        document.getElementById('info-num-floors').textContent = app.num_floors || 'Not provided';
        document.getElementById('info-estimated-cost').textContent = app.estimated_cost ? `₱${parseFloat(app.estimated_cost).toLocaleString()}` : 'Not provided';
        document.getElementById('info-description').textContent = app.project_description || 'Not provided';
        
        document.getElementById('info-owner-name').textContent = app.owner_name || 'Not provided';
        document.getElementById('info-contact-number').textContent = app.contact_number || 'Not provided';
        document.getElementById('info-owner-address').textContent = app.owner_address || 'Not provided';
        document.getElementById('info-owner-email').textContent = app.owner_email || 'Not provided';
        
        document.getElementById('info-architect-name').textContent = app.architect_name || 'Not provided';
        document.getElementById('info-architect-license').textContent = app.architect_license || 'Not provided';
        document.getElementById('info-engineer-name').textContent = app.engineer_name || 'Not provided';
        document.getElementById('info-engineer-license').textContent = app.engineer_license || 'Not provided';
        
        document.getElementById('project-title').textContent = app.project_title || 'Building Permit Application';
    }

    // Display documents list
    function displayDocumentsList(documents) {
        const container = document.getElementById('documents-list');
        
        if (!documents || Object.keys(documents).length === 0) {
            showEmptyDocuments();
            return;
        }
        
        let html = '';
        let hasDocuments = false;
        
        for (const [key, value] of Object.entries(documents)) {
            if (value && value.trim() !== '') {
                hasDocuments = true;
                const docName = documentNames[key] || key.replace(/_/g, ' ').replace(/_link$/, '').replace(/\b\w/g, l => l.toUpperCase());
                
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">${escapeHtml(docName)}</p>
                                <p class="text-xs text-gray-400 truncate">${escapeHtml(value.length > 60 ? value.substring(0, 60) + '...' : value)}</p>
                            </div>
                        </div>
                        <a href="${escapeHtml(value)}" target="_blank" rel="noopener noreferrer" class="text-[#155386] hover:text-[#1F363D] text-sm flex items-center gap-1 flex-shrink-0 ml-2">
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
            document.getElementById('document-status').textContent = 'Available';
            document.getElementById('document-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
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
        document.getElementById('document-status').textContent = 'Not Uploaded';
        document.getElementById('document-status').className = 'text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full';
    }

    // Load review activities
    async function loadReviewActivities() {
        if (!applicationId) return;
        
        try {
            const response = await fetch(`/applicant/applications/${applicationId}/review-activities`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities) {
                    displayReviewActivities(data.activities);
                    displayAllReviewers(data.activities);
                } else {
                    showEmptyReviewers();
                    showEmptyActivities();
                }
            } else {
                showEmptyReviewers();
                showEmptyActivities();
            }
        } catch (error) {
            console.error('Error loading review activities:', error);
            showEmptyReviewers();
            showEmptyActivities();
        }
    }

    // Display all reviewers
    function displayAllReviewers(activities) {
        const reviewersContainer = document.getElementById('reviewers-container');
        
        if (!activities || activities.length === 0) {
            showEmptyReviewers();
            return;
        }
        
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
                } else if (reviewer.lastAction === 'fsec_uploaded') {
                    statusText = 'Uploaded FSEC';
                    statusClass = 'bg-red-100 text-red-600';
                } else if (reviewer.lastAction === 'bfp_comments_added') {
                    statusText = 'Added Comments';
                    statusClass = 'bg-amber-100 text-amber-600';
                }
            }
            
            const timeAgo = reviewer.lastActionDate ? getTimeAgo(reviewer.lastActionDate) : 'No actions';
            
            reviewersContainer.innerHTML += `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition animate-fade-in">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xl font-bold">
                        <span>${escapeHtml(reviewer.initials)}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start flex-wrap gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(reviewer.name)}</p>
                                <p class="text-xs text-gray-500">${escapeHtml(reviewer.role)}</p>
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

    // Show empty reviewers
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

    // Show empty activities
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

    // Display review activities
    function displayReviewActivities(activities) {
        const activityLog = document.getElementById('activity-log');
        
        if (!activities || activities.length === 0) {
            showEmptyActivities();
            return;
        }
        
        const sortedActivities = [...activities].sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at)
        ).slice(0, 3);
        
        let html = '';
        sortedActivities.forEach(activity => {
            const date = new Date(activity.created_at);
            const exactDateTime = formatExactDateTime(date);
            const timeAgo = getTimeAgo(date);
            
            let iconColor = 'bg-blue-100';
            let iconTextColor = 'text-blue-600';
            let iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>`;
            
            if (activity.action === 'status_updated') {
                if (activity.new_status && activity.new_status === 'approved') {
                    iconColor = 'bg-green-100';
                    iconTextColor = 'text-green-600';
                    iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
                } else if (activity.new_status && activity.new_status === 'rejected') {
                    iconColor = 'bg-red-100';
                    iconTextColor = 'text-red-600';
                    iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;
                } else if (activity.new_status && activity.new_status === 'document-verification') {
                    iconColor = 'bg-purple-100';
                    iconTextColor = 'text-purple-600';
                    iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                } else if (activity.new_status && activity.new_status === 'for-assessment') {
                    iconColor = 'bg-indigo-100';
                    iconTextColor = 'text-indigo-600';
                    iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>`;
                } else {
                    iconColor = 'bg-purple-100';
                    iconTextColor = 'text-purple-600';
                }
            } else if (activity.action === 'note_added') {
                iconColor = 'bg-yellow-100';
                iconTextColor = 'text-yellow-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>`;
            } else if (activity.action === 'hard_copy_received') {
                iconColor = 'bg-indigo-100';
                iconTextColor = 'text-indigo-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`;
            } else if (activity.action === 'application_created') {
                iconColor = 'bg-emerald-100';
                iconTextColor = 'text-emerald-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>`;
            } else if (activity.action === 'document_verified') {
                iconColor = 'bg-green-100';
                iconTextColor = 'text-green-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>`;
            } else if (activity.action === 'fsec_uploaded') {
                iconColor = 'bg-red-100';
                iconTextColor = 'text-red-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>`;
            } else if (activity.action === 'bfp_comments_added') {
                iconColor = 'bg-amber-100';
                iconTextColor = 'text-amber-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>`;
            }
            
            const reviewerName = activity.reviewer ? activity.reviewer.name : 'System';
            const reviewerRole = activity.reviewer ? activity.reviewer.role : '';
            
            let actionDisplay = activity.action_display || activity.action;
            if (activity.action === 'status_updated') {
                if (activity.old_status && activity.new_status) {
                    actionDisplay = `Status changed from ${formatStatusDisplay(activity.old_status)} to ${formatStatusDisplay(activity.new_status)}`;
                } else {
                    actionDisplay = 'Status updated';
                }
            } else if (activity.action === 'document_verified') {
                actionDisplay = 'Documents Verified';
            } else if (activity.action === 'fsec_uploaded') {
                actionDisplay = 'FSEC Document Uploaded';
            } else if (activity.action === 'bfp_comments_added') {
                actionDisplay = 'BFP Comments Added';
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
                            <div class="flex justify-between items-start flex-wrap gap-1">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(actionDisplay)}</p>
                            </div>
                            ${activity.remarks ? `<p class="text-xs text-gray-600 mt-1">${escapeHtml(activity.remarks)}</p>` : ''}
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="font-medium">${escapeHtml(reviewerName)}</span>
                                ${reviewerRole ? `<span class="text-gray-400"> • ${escapeHtml(reviewerRole)}</span>` : ''}
                            </p>
                            <div class="mt-1">
                                <p class="text-xs text-gray-400" title="${exactDateTime}">${timeAgo}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        if (activities.length > 3) {
            html += `<div class="text-center text-xs text-gray-400 pt-2">+${activities.length - 3} more activities</div>`;
        }
        
        activityLog.innerHTML = html;
    }

    // Display application details
    function displayApplicationDetails() {
        if (!currentApplication) return;
        
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('application-content').classList.remove('hidden');

        // Display project information
        displayProjectInfo(currentApplication);

        document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
        
        if (currentApplication.submitted_at || currentApplication.created_at) {
            const submittedDate = new Date((currentApplication.submitted_at || currentApplication.created_at) + ' UTC');
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

        updateStatusUI(currentApplication.status);
        updateTimeline(currentApplication.status);
        updateProgress(currentApplication.status);

        // Update Google Drive main link
        if (currentApplication.google_drive_link) {
            const driveLink = document.getElementById('drive-link');
            driveLink.href = currentApplication.google_drive_link;
            const linkText = document.getElementById('drive-link-text');
            linkText.textContent = currentApplication.google_drive_link.length > 50 ? 
                currentApplication.google_drive_link.substring(0, 50) + '...' : 
                currentApplication.google_drive_link;
            driveLink.classList.remove('pointer-events-none', 'text-gray-500');
            driveLink.classList.add('text-[#155386]');
            driveLink.setAttribute('target', '_blank');
            driveLink.setAttribute('rel', 'noopener noreferrer');
            
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

        updateHardCopyStatus(currentApplication.hard_copy_received);

        if (currentApplication.status === 'document-verification') {
            showDocumentVerificationStatus(true);
        } else {
            showDocumentVerificationStatus(false);
        }
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

    // Helper functions
    function getInitials(name) {
        if (!name) return 'OB';
        return name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatExactDateTime(date) {
        if (!(date instanceof Date) || isNaN(date)) return '';
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
        });
    }

    function formatStatusDisplay(status) {
        if (!status) return '';
        const statusMap = {
            'for-assessment': 'For Assessment',
            'under-review': 'Under Review',
            'document-verification': 'Document Verification',
            'for-release': 'For Release'
        };
        if (statusMap[status]) return statusMap[status];
        return status.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function getTimeAgo(date) {
        if (!(date instanceof Date) || isNaN(date)) return 'Unknown';
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

    function updateStatusUI(status) {
        const statusBadge = document.getElementById('status-badge');
        const currentStatusBadge = document.getElementById('current-status-badge');
        
        const statusConfig = {
            'draft': { color: 'gray', text: 'Draft' },
            'pending': { color: 'yellow', text: 'Pending Review' },
            'under-review': { color: 'purple', text: 'Under Review' },
            'document-verification': { color: 'purple', text: 'Document Verification' },
            'for-assessment': { color: 'indigo', text: 'For Assessment' },
            'approved': { color: 'green', text: 'Approved' },
            'for-release': { color: 'blue', text: 'For Release' },
            'verified': { color: 'emerald', text: 'Completed' },
            'rejected': { color: 'red', text: 'Rejected' }
        };

        const config = statusConfig[status] || { color: 'gray', text: status || 'Unknown' };
        
        if (statusBadge) {
            statusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium transition-all duration-500`;
            statusBadge.textContent = config.text;
        }
        
        if (currentStatusBadge) {
            currentStatusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium transition-all duration-500`;
            currentStatusBadge.textContent = config.text;
        }
    }

    function updateTimeline(status) {
        const steps = ['submitted', 'under-review', 'verification', 'assessment', 'approval', 'release'];
        const stepMap = {
            'draft': -1,
            'pending': 0,
            'under-review': 1,
            'document-verification': 2,
            'for-assessment': 3,
            'approved': 4,
            'for-release': 5,
            'verified': 5,
            'rejected': -1
        };
        const currentStepIndex = stepMap[status] !== undefined ? stepMap[status] : -1;
        
        steps.forEach((step, index) => {
            const stepElement = document.getElementById(`step-${step}`);
            if (!stepElement) return;
            
            const circle = stepElement.querySelector('.w-10.h-10');
            const icon = stepElement.querySelector('.step-icon');
            const text = stepElement.querySelector('.text-sm');
            const dateElement = document.getElementById(`step-${step}-date`);
            
            if (index <= currentStepIndex) {
                if (circle) {
                    circle.className = 'w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500 transform scale-animation';
                }
                if (icon) {
                    icon.className = 'text-lg font-bold text-white step-icon';
                }
                if (text) text.className = 'text-sm font-medium text-gray-800 transition-all duration-500';
                
                if (index === currentStepIndex) {
                    if (dateElement) {
                        dateElement.textContent = 'In Progress';
                        dateElement.className = 'text-xs text-[#155386] font-medium animate-pulse';
                    }
                    stepElement.classList.add('step-processing');
                } else {
                    stepElement.classList.remove('step-processing');
                    
                    // Try to get completion date from application if available
                    if (index === 0 && currentApplication?.submitted_at) {
                        const date = new Date(currentApplication.submitted_at + ' UTC');
                        if (!isNaN(date)) {
                            dateElement.textContent = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            dateElement.className = 'text-xs text-gray-500';
                        }
                    }
                }
            } else {
                if (circle) {
                    circle.className = 'w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500';
                }
                if (icon) {
                    icon.className = 'text-lg font-bold text-gray-400 step-icon';
                }
                if (text) text.className = 'text-sm font-medium text-gray-400 transition-all duration-500';
                if (dateElement) {
                    dateElement.textContent = '';
                    dateElement.className = 'text-xs text-gray-400';
                }
                stepElement.classList.remove('step-processing');
            }
        });

        const progressLine = document.getElementById('progress-line');
        if (progressLine) {
            const width = currentStepIndex >= 0 ? ((currentStepIndex + 1) / steps.length) * 100 : 0;
            progressLine.style.width = width + '%';
        }
    }

    function updateProgress(status) {
        const progressMap = {
            'draft': 0,
            'pending': 20,
            'under-review': 35,
            'document-verification': 50,
            'for-assessment': 65,
            'approved': 80,
            'for-release': 95,
            'verified': 100,
            'rejected': 100
        };
        
        const progress = progressMap[status] || 0;
        const progressPercent = document.getElementById('progress-percentage');
        const progressBar = document.getElementById('progress-bar');
        
        if (progressPercent) progressPercent.textContent = progress + '%';
        if (progressBar) progressBar.style.width = progress + '%';
    }

    function updateHardCopyStatus(received) {
        const hardcopyNotice = document.getElementById('hardcopy-notice');
        const hardcopyReceivedNotice = document.getElementById('hardcopy-received-notice');
        const hardcopyBadge = document.getElementById('hardcopy-badge');
        const hardcopyMessage = document.getElementById('hardcopy-message');
        const hardcopySidebar = document.getElementById('hardcopy-status-sidebar');
        
        if (received) {
            if (hardcopyNotice) hardcopyNotice.classList.add('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.remove('hidden');
            if (hardcopyBadge) {
                hardcopyBadge.textContent = 'Received';
                hardcopyBadge.className = 'text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full transition-all duration-500';
            }
            if (hardcopyMessage) hardcopyMessage.textContent = 'Hard copies received by OBO';
            if (hardcopySidebar) hardcopySidebar.className = 'mt-4 p-3 bg-green-50 rounded-lg transition-all duration-500';
        } else {
            if (hardcopyNotice) hardcopyNotice.classList.remove('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.add('hidden');
            if (hardcopyBadge) {
                hardcopyBadge.textContent = 'Pending';
                hardcopyBadge.className = 'text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full transition-all duration-500';
            }
            if (hardcopyMessage) hardcopyMessage.textContent = 'Submit originals to OBO';
            if (hardcopySidebar) hardcopySidebar.className = 'mt-4 p-3 bg-blue-50 rounded-lg transition-all duration-500';
        }
    }

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

    function downloadApplicationSummary() {
        if (!currentApplication) {
            showErrorModal('No application data to download');
            return;
        }
        
        // Create a simple text summary
        let summary = `BUILDING PERMIT APPLICATION SUMMARY\n`;
        summary += `================================\n\n`;
        summary += `Application Number: ${currentApplication.application_number || 'N/A'}\n`;
        summary += `Status: ${formatStatusDisplay(currentApplication.status)}\n`;
        summary += `Submitted: ${currentApplication.submitted_at ? new Date(currentApplication.submitted_at).toLocaleDateString() : 'N/A'}\n\n`;
        summary += `PROJECT INFORMATION\n`;
        summary += `------------------\n`;
        summary += `Title: ${currentApplication.project_title || 'N/A'}\n`;
        summary += `Type: ${currentApplication.project_type || 'N/A'}\n`;
        summary += `Location: ${currentApplication.project_location || 'N/A'}\n`;
        summary += `Lot Area: ${currentApplication.lot_area ? currentApplication.lot_area + ' sqm' : 'N/A'}\n`;
        summary += `Floor Area: ${currentApplication.floor_area ? currentApplication.floor_area + ' sqm' : 'N/A'}\n`;
        summary += `Floors: ${currentApplication.num_floors || 'N/A'}\n`;
        summary += `Estimated Cost: ${currentApplication.estimated_cost ? '₱' + parseFloat(currentApplication.estimated_cost).toLocaleString() : 'N/A'}\n\n`;
        summary += `OWNER INFORMATION\n`;
        summary += `-----------------\n`;
        summary += `Name: ${currentApplication.owner_name || 'N/A'}\n`;
        summary += `Contact: ${currentApplication.contact_number || 'N/A'}\n`;
        summary += `Email: ${currentApplication.owner_email || 'N/A'}\n\n`;
        summary += `Generated on: ${new Date().toLocaleString()}\n`;
        
        // Create download
        const blob = new Blob([summary], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `application_${currentApplication.application_number || 'summary'}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showSuccessModal('Application summary downloaded!');
    }

    function contactSupport() {
        window.location.href = 'mailto:obo@legazpi.gov.ph?subject=Building Permit Application Inquiry - ' + (currentApplication?.application_number || '');
    }

    function requestAssistance() {
        showSuccessModal('Assistance request has been sent. A staff member will contact you within 1-2 business days.');
    }

    function viewFullHistory() {
        if (applicationId) {
            window.location.href = `/applicant/applications/${applicationId}/activity-history`;
        } else {
            showErrorModal('Unable to view history');
        }
    }

    function showError() {
        const loadingState = document.getElementById('loading-state');
        const errorState = document.getElementById('error-state');
        const applicationContent = document.getElementById('application-content');
        
        if (loadingState) loadingState.classList.add('hidden');
        if (applicationContent) applicationContent.classList.add('hidden');
        if (errorState) errorState.classList.remove('hidden');
    }

    function showErrorModal(message) {
        const messageEl = document.getElementById('error-modal-message');
        if (messageEl) messageEl.textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showSuccessModal(message) {
        const messageEl = document.getElementById('success-modal-message');
        if (messageEl) messageEl.textContent = message;
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

    window.addEventListener('beforeunload', function() {
        if (updateCheckInterval) {
            clearInterval(updateCheckInterval);
        }
    });
</script>

<style>
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    
    #error-modal, #success-modal { transition: opacity 0.2s ease-in-out; }
    #error-modal .bg-white, #success-modal .bg-white { animation: modalSlideIn 0.3s ease-out; }
    @keyframes modalSlideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    
    @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
    
    @keyframes scalePulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    .scale-animation { animation: scalePulse 0.5s ease-in-out; }
    
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
    .animate-pulse { animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    .animate-bounce { animation: bounce 1s infinite; }
    
    @keyframes progressLoading { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .loading-progress-animation { position: relative; width: 100%; height: 100%; background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0.2) 75%, transparent 100%); background-size: 200% 100%; animation: progressLoading 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite; mix-blend-mode: overlay; }
    
    @keyframes lineLoading { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .loading-line-animation { position: relative; width: 100%; height: 100%; background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0.2) 75%, transparent 100%); background-size: 200% 100%; animation: lineLoading 2s cubic-bezier(0.4, 0, 0.2, 1) infinite; mix-blend-mode: overlay; }
    
    @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
    .animate-ping { animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite; }
    
    @keyframes stepGlow { 0%, 100% { box-shadow: 0 0 5px rgba(21,83,134,0.3); transform: scale(1); } 50% { box-shadow: 0 0 20px rgba(64,121,140,0.6); transform: scale(1.05); } }
    .step-processing .w-10 { animation: stepGlow 2s ease-in-out infinite; border: 2px solid rgba(21,83,134,0.3); }
    
    .pointer-events-none { pointer-events: none; }
    .break-all { word-break: break-all; }
    .step-item { transition: transform 0.3s ease; }
    .step-item:hover { transform: translateY(-2px); }
    .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }
    .duration-500 { transition-duration: 500ms; }
    .duration-700 { transition-duration: 700ms; }
    .ease-out { transition-timing-function: cubic-bezier(0, 0, 0.2, 1); }
    .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .min-w-0 { min-width: 0; }
    .hidden { display: none; }
    .sticky { position: sticky; }
    .top-8 { top: 2rem; }
    .overflow-x-auto { overflow-x: auto; }
    .min-w-[600px] { min-width: 600px; }
</style>
@endsection