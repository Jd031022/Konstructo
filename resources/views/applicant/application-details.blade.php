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

        <!-- Hard Copy Submission Date Notice (when approved with date) -->
        <div id="hardcopy-submission-notice" class="mb-6 p-4 bg-green-100 border-l-4 border-green-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hard Copy Submission Schedule</h4>
                    <p class="text-sm text-gray-700 mt-1">Please submit your hard copy documents to the Office of the Building Official (OBO) on:</p>
                    <p id="hardcopy-submission-date" class="text-md font-bold text-green-700 mt-1"></p>
                    <p id="hardcopy-instructions" class="text-sm text-gray-600 mt-2"></p>
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

        <!-- Assessment Notice (Building Permit Fee) -->
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

        <!-- Ownership Status Card -->
        <div id="ownership-status-card" class="mb-6 p-4 bg-teal-50 border-l-4 border-teal-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Property Ownership Status</h4>
                    <p id="ownership-status-text" class="text-sm text-gray-700 mt-1">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Ownership Document Remarks Notice -->
        <div id="ownership-remarks-notice" class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <h4 class="font-semibold text-gray-800">📝 Action Required: Document Clarification Needed</h4>
                        <span id="remarks-count-badge" class="text-xs px-2 py-1 bg-amber-200 text-amber-800 rounded-full font-medium">0 remarks</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">Our staff has requested clarifications on your ownership documents. Please review the remarks below.</p>
                    
                    <!-- Remarks List Container -->
                    <div id="ownership-remarks-list" class="mt-3 space-y-3 max-h-80 overflow-y-auto">
                        <!-- Remarks will be dynamically inserted here -->
                    </div>
                    
                    <!-- Instruction Box -->
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">How to Respond:</p>
                                <p class="text-xs text-blue-700 mt-1">
                                    Please use the <strong>Chat Feature</strong> to communicate directly with the staff member who reviewed your document. 
                                    Check your email to see who reviewed it, or look at the remarks above for the staff name.
                                </p>
                                <button onclick="openChatWithStaff()" class="mt-2 inline-flex items-center gap-1 text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Open Chat
                                </button>
                            </div>
                        </div>
                    </div>
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
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Professional Information</h2>
                        <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Licensed Professionals</span>
                    </div>
                    
                    <!-- Architect -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Architect</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p id="info-architect-name" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">License Number</p>
                                <p id="info-architect-license" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Civil Engineer -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Civil Engineer</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p id="info-engineer-name" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">License Number</p>
                                <p id="info-engineer-license" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Electrical Engineer -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Professional Electrical Engineer</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p id="info-electrical-name" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">License Number</p>
                                <p id="info-electrical-license" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sanitary Engineer / Master Plumber -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-700">Sanitary Engineer / Master Plumber</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p id="info-sanitary-name" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">License Number</p>
                                <p id="info-sanitary-license" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff/Reviewers List Card - Collapsible -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <button onclick="toggleReviewersSection()" class="w-full flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Application Reviewers</h2>
                            <span id="reviewer-count" class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">0</span>
                        </div>
                        <div id="reviewers-chevron" class="transition-transform duration-300">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    
                    <div id="reviewers-collapsible-content" class="mt-4 hidden transition-all duration-300 ease-in-out">
                        <div id="reviewers-container" class="space-y-4">
                            <div class="flex items-center justify-center p-8">
                                <svg class="animate-spin h-6 w-6 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Drive Documents Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Submitted Documents
                    </h2>

                    <!-- Ownership Documents Section (Step 1) -->
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-800 border-b pb-2 mb-3 text-teal-700">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Step 1: Ownership Documents
                            </span>
                        </h3>
                        <div id="ownership-documents-list" class="space-y-3">
                            <div class="text-center py-4 text-gray-500">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm">Loading ownership documents...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Other Documents Section (Step 2) -->
                    <div>
                        <h3 class="text-md font-semibold text-gray-800 border-b pb-2 mb-3 text-blue-700">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Step 2: Project Documents
                            </span>
                        </h3>
                        <div id="documents-list" class="space-y-3">
                            <div class="text-center py-4 text-gray-500">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm">Loading project documents...</p>
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
                    
                    <!-- CPDO STATUS AND ASSESSMENT CARD (Combined) -->
                    <div id="cpdo-card" class="bg-white rounded-2xl shadow-sm border border-orange-200 p-6 animate-fade-in">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">CPDO Verification</h2>
                        </div>
                        
                        <!-- CPDO Status Display -->
                        <div id="cpdo-status-display" class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">CPDO Status:</span>
                                <span id="cpdo-status-badge" class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            </div>
                            <div id="cpdo-remarks-display" class="mt-2 text-xs text-gray-500 hidden">
                                <span class="font-medium">Remarks:</span>
                                <span id="cpdo-remarks-text"></span>
                            </div>
                            <div id="cpdo-approved-info" class="mt-2 text-xs text-gray-500 hidden">
                                <span class="font-medium">Decision made by:</span>
                                <span id="cpdo-approved-by"></span>
                                <span class="font-medium ml-2">on:</span>
                                <span id="cpdo-approved-at"></span>
                            </div>
                        </div>
                        
                        <!-- CPDO Assessment Details (shown when assessment exists) -->
                        <div id="cpdo-assessment-section" class="mt-4 pt-4 border-t border-orange-200">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-800">CPDO Fee Assessment</h3>
                            </div>
                            <div id="cpdo-assessment-details" class="space-y-2">
                                <div class="text-center py-4 text-gray-500 text-sm">
                                    <svg class="w-8 h-8 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p>Loading assessment details...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EXTERNAL SERVICES CARDS - BFP and Payment Links -->
                    <!-- BFP Website Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-6 animate-fade-in">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Bureau of Fire Protection (BFP)</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Fire Safety Evaluation Clearance (FSEC)</p>
                            </div>
                        </div>
                        <a href="https://fsis.e-bfp.com/" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Go to BFP e-FSIS Portal
                        </a>
                        <p class="text-xs text-gray-500 mt-3">
                            Apply for Fire Safety Evaluation Clearance (FSEC) through the BFP online portal.
                        </p>
                    </div>

                    <!-- Payment Portal Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-green-200 p-6 animate-fade-in">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">Payment Portal</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Pay your assessment fees online</p>
                            </div>
                        </div>
                        <a href="https://filipizen.com/partners/albay_ligao" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Pay Online via Filipizen
                        </a>
                        <p class="text-xs text-gray-500 mt-3">
                            Pay your building permit fees securely through the online payment portal.
                        </p>
                    </div>

                    <!-- Payment Proof (OR Upload) -->
                    <div id="payment-proof-card" class="bg-white rounded-2xl shadow-sm border border-green-200 p-6 animate-fade-in hidden">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Payment Proof (Official Receipt)</h2>
                            <span id="payment-status-badge" class="ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">After completing your payment, please upload your Official Receipt (OR) here.</p>
                        
                        <!-- Payment Proof Display (when already uploaded) -->
                        <div id="payment-proof-display" class="hidden">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Uploaded OR:</span>
                                    <span id="payment-status-text" class="text-xs px-2 py-1 rounded-full"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <a id="payment-proof-link" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">View Official Receipt</a>
                                </div>
                                <div id="payment-rejection-reason" class="mt-3 p-2 bg-red-50 rounded-lg hidden">
                                    <p class="text-xs text-red-600"><strong>Rejection Reason:</strong> <span id="rejection-reason-text"></span></p>
                                </div>
                            </div>
                            <button onclick="showPaymentProofForm()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">Update OR Link</button>
                        </div>
                        
                        <!-- Payment Proof Form -->
                        <div id="payment-proof-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Google Drive Link to OR <span class="text-red-500">*</span></label>
                                <input type="url" 
                                       id="or-link" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" 
                                       placeholder="https://drive.google.com/file/d/...">
                                <p class="text-xs text-gray-400 mt-1">Paste the Google Drive link to your Official Receipt</p>
                            </div>
                            
                            <button onclick="uploadPaymentProof()" id="upload-or-btn" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                Upload Official Receipt
                            </button>
                        </div>
                    </div>

                    <!-- Current Status Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span id="current-status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium transition-all duration-500">Pending Review</span>
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

                        <!-- Hard Copy Submission Date Display (when approved) -->
                        <div id="hardcopy-submission-info" class="mt-4 p-3 bg-green-50 rounded-lg hidden transition-all duration-500">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-xs font-semibold text-green-700">Hard Copy Submission Schedule</span>
                            </div>
                            <p id="sidebar-submission-date" class="text-sm font-bold text-green-700"></p>
                            <p id="sidebar-submission-instructions" class="text-xs text-gray-600 mt-1"></p>
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

<!-- CPDO Experience Rating Modal -->
<div id="cpdo-rating-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-2xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Rate Your CPDO Experience</h3>
                        <p class="text-sm opacity-90 mt-1">How would you rate the CPDO's service?</p>
                    </div>
                    <button onclick="closeCPDORatingModal()" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <form id="cpdo-rating-form" onsubmit="submitCPDORating(event)">
                        <input type="hidden" id="cpdo-application-id" value="">
                        
                        <!-- Star Rating -->
                        <div class="mb-8 text-center">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Overall CPDO Experience <span class="text-red-500">*</span></label>
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="setCPDORating(1)" class="rating-star p-2 transition-all duration-200 hover:scale-110">
                                    <svg class="w-10 h-10 text-gray-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="setCPDORating(2)" class="rating-star p-2 transition-all duration-200 hover:scale-110">
                                    <svg class="w-10 h-10 text-gray-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="setCPDORating(3)" class="rating-star p-2 transition-all duration-200 hover:scale-110">
                                    <svg class="w-10 h-10 text-gray-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="setCPDORating(4)" class="rating-star p-2 transition-all duration-200 hover:scale-110">
                                    <svg class="w-10 h-10 text-gray-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                                <button type="button" onclick="setCPDORating(5)" class="rating-star p-2 transition-all duration-200 hover:scale-110">
                                    <svg class="w-10 h-10 text-gray-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" id="cpdo-rating-value" required>
                            <p id="rating-error" class="text-xs text-red-500 mt-2 hidden">Please select a rating</p>
                        </div>

                        <!-- Rating Questions -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Processing Time <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 flex-wrap">
                                    <label class="flex items-center gap-2"><input type="radio" name="processing_time" value="5" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Excellent</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="processing_time" value="4" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Good</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="processing_time" value="3" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Average</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="processing_time" value="2" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Poor</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="processing_time" value="1" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Poor</span></label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Responsiveness <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 flex-wrap">
                                    <label class="flex items-center gap-2"><input type="radio" name="responsiveness" value="5" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Excellent</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="responsiveness" value="4" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Good</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="responsiveness" value="3" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Average</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="responsiveness" value="2" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Poor</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="responsiveness" value="1" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Poor</span></label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Clarity of Instructions <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 flex-wrap">
                                    <label class="flex items-center gap-2"><input type="radio" name="clarity" value="5" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Excellent</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="clarity" value="4" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Good</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="clarity" value="3" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Average</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="clarity" value="2" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Poor</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="clarity" value="1" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Poor</span></label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fairness of Assessment <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 flex-wrap">
                                    <label class="flex items-center gap-2"><input type="radio" name="fairness" value="5" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Excellent</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="fairness" value="4" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Good</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="fairness" value="3" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Average</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="fairness" value="2" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Poor</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="fairness" value="1" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Poor</span></label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Satisfaction with CPDO Service <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 flex-wrap">
                                    <label class="flex items-center gap-2"><input type="radio" name="overall_satisfaction" value="5" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Satisfied</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="overall_satisfaction" value="4" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Satisfied</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="overall_satisfaction" value="3" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Neutral</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="overall_satisfaction" value="2" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Dissatisfied</span></label>
                                    <label class="flex items-center gap-2"><input type="radio" name="overall_satisfaction" value="1" class="w-4 h-4 text-[#155386]"> <span class="text-sm">Very Dissatisfied</span></label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Comments / Suggestions (Optional)</label>
                                <textarea id="cpdo-comments" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent" placeholder="Share your experience with CPDO..."></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" onclick="closeCPDORatingModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Skip for Now
                            </button>
                            <button type="submit" id="submit-cpdo-rating-btn" class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm font-medium">
                                Submit Rating
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Remarks Modal (for viewing remarks on specific document) -->
<div id="document-remarks-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Document Remarks</h3>
                </div>
                <button onclick="closeDocumentRemarksModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Document: <span id="modal-doc-name" class="font-bold"></span></p>
                            <p class="text-xs text-blue-700 mt-1">Staff remarks and clarifications for this document are shown below.</p>
                        </div>
                    </div>
                </div>
                
                <div id="modal-remarks-list" class="space-y-3">
                    <!-- Remarks will be inserted here -->
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeDocumentRemarksModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Existing styles... */
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    
    #error-modal, #success-modal { transition: opacity 0.2s ease-in-out; }
    #error-modal .bg-white, #success-modal .bg-white { animation: modalSlideIn 0.3s ease-out; }
    @keyframes modalSlideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1); } }
    
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
    
    @keyframes pulse-amber {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.05); }
    }
    .remark-badge-pulse {
        animation: pulse-amber 2s ease-in-out infinite;
    }
    
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
    let currentOwnershipData = null;
    let currentCPDOAssessment = null;
    let cpdoStatus = null;
    let cpdoRemarks = null;
    let cpdoApprovedBy = null;
    let cpdoApprovedAt = null;
    let currentCPDORating = 0;
    let hasShownCPDORatingModal = false;
    
    // Ownership remarks storage
    let ownershipRemarks = {};
    let currentModalDocumentKey = null;
    let currentModalDocumentName = null;

    // Ownership document names mapping (Step 1)
    const ownershipDocumentNames = {
        'tct_link': 'TCT / Deed of Sale',
        'tax_declaration_link': 'Tax Declaration',
        'current_tax_receipt_link': 'Current Tax Receipt',
        'spa_link': 'Special Power of Attorney (SPA)'
    };

    // Load ownership remarks from API
async function loadOwnershipRemarks() {
    if (!applicationId) return;
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/applicant/applications/${applicationId}/ownership-remarks`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.remarks) {
                ownershipRemarks = data.remarks;
                // Also save to localStorage for caching
                localStorage.setItem(`applicant_ownership_remarks_${applicationId}`, JSON.stringify(ownershipRemarks));
            } else {
                // Try to load from localStorage as fallback
                const saved = localStorage.getItem(`applicant_ownership_remarks_${applicationId}`);
                if (saved) {
                    try {
                        ownershipRemarks = JSON.parse(saved);
                    } catch(e) {
                        ownershipRemarks = {};
                    }
                } else {
                    ownershipRemarks = {};
                }
            }
        } else {
            // Fallback to localStorage
            const saved = localStorage.getItem(`applicant_ownership_remarks_${applicationId}`);
            if (saved) {
                try {
                    ownershipRemarks = JSON.parse(saved);
                } catch(e) {
                    ownershipRemarks = {};
                }
            } else {
                ownershipRemarks = {};
            }
        }
    } catch (error) {
        console.error('Error loading ownership remarks:', error);
        // Fallback to localStorage
        const saved = localStorage.getItem(`applicant_ownership_remarks_${applicationId}`);
        if (saved) {
            try {
                ownershipRemarks = JSON.parse(saved);
            } catch(e) {
                ownershipRemarks = {};
            }
        } else {
            ownershipRemarks = {};
        }
    }
    
    // Display remarks notice if there are any pending remarks
    displayOwnershipRemarksNotice();
}

    // Save ownership remarks to localStorage
    function saveOwnershipRemarks() {
        localStorage.setItem(`applicant_ownership_remarks_${applicationId}`, JSON.stringify(ownershipRemarks));
    }

    // Display ownership remarks notice on the page
function displayOwnershipRemarksNotice() {
    const noticeContainer = document.getElementById('ownership-remarks-notice');
    const remarksListContainer = document.getElementById('ownership-remarks-list');
    const remarksCountBadge = document.getElementById('remarks-count-badge');
    
    if (!noticeContainer || !remarksListContainer) return;
    
    // Collect all pending remarks
    let allRemarks = [];
    let pendingCount = 0;
    
    for (const [docKey, remarks] of Object.entries(ownershipRemarks)) {
        if (remarks && remarks.length > 0) {
            remarks.forEach(remark => {
                if (remark.status === 'pending_response') {
                    pendingCount++;
                    allRemarks.push({
                        ...remark,
                        document_key: docKey,
                        document_name: remark.document_name || ownershipDocumentNames[docKey] || docKey.replace(/_/g, ' ').replace(/_link$/, '')
                    });
                }
            });
        }
    }
    
    // Sort by date (newest first)
    allRemarks.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    
    if (pendingCount > 0) {
        noticeContainer.classList.remove('hidden');
        remarksCountBadge.textContent = `${pendingCount} remark${pendingCount > 1 ? 's' : ''}`;
        
        // Build remarks HTML
        let remarksHtml = '';
        allRemarks.forEach(remark => {
            const date = new Date(remark.created_at);
            const formattedDate = date.toLocaleString();
            
            remarksHtml += `
                <div class="remark-item p-3 bg-white rounded-lg border border-amber-200 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                                ${escapeHtml(remark.document_name)}
                            </span>
                            <span class="text-xs text-gray-500">from ${escapeHtml(remark.created_by)}</span>
                        </div>
                        <span class="text-xs text-gray-400">${formattedDate}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">${escapeHtml(remark.remark)}</p>
                    <div class="flex gap-2 mt-3">
                        <button onclick="viewDocumentRemarks('${remark.document_key}', '${escapeHtml(remark.document_name)}')" 
                                class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Details
                        </button>
                        <button onclick="openChatWithStaff('${escapeHtml(remark.created_by)}')" 
                                class="text-xs text-green-600 hover:text-green-800 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Message Staff
                        </button>
                    </div>
                    ${remark.response ? `
                        <div class="mt-3 pt-2 border-t border-gray-100">
                            <div class="flex items-start gap-2">
                                <svg class="w-3 h-3 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <div>
                                    <p class="text-xs font-medium text-green-600">Your Response:</p>
                                    <p class="text-sm text-gray-600">${escapeHtml(remark.response)}</p>
                                    <p class="text-xs text-gray-400 mt-1">Responded: ${new Date(remark.responded_at).toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        });
        
        remarksListContainer.innerHTML = remarksHtml;
        
        // Also add a remark badge to the ownership documents list
        addRemarkBadgesToDocuments();
    } else {
        noticeContainer.classList.add('hidden');
    }
}

    // Add remark badges to ownership documents in the list
    function addRemarkBadgesToDocuments() {
        const documentItems = document.querySelectorAll('#ownership-documents-list .flex');
        
        documentItems.forEach(item => {
            const docNameText = item.querySelector('.text-sm.font-medium')?.textContent;
            if (docNameText) {
                let hasRemarks = false;
                for (const [docKey, remarks] of Object.entries(ownershipRemarks)) {
                    const docName = ownershipDocumentNames[docKey];
                    if (docName === docNameText || docKey.includes(docNameText.toLowerCase().replace(/\s+/g, '_'))) {
                        const pendingRemarks = remarks.filter(r => r.status === 'pending_response');
                        if (pendingRemarks.length > 0) {
                            hasRemarks = true;
                            break;
                        }
                    }
                }
                
                if (hasRemarks) {
                    if (!item.querySelector('.remark-badge')) {
                        const remarkBadge = document.createElement('span');
                        remarkBadge.className = 'remark-badge ml-2 text-xs px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full flex items-center gap-1 remark-badge-pulse';
                        remarkBadge.innerHTML = `
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Has Remarks
                        `;
                        const titleDiv = item.querySelector('.text-sm.font-medium');
                        if (titleDiv && titleDiv.parentElement) {
                            titleDiv.parentElement.insertBefore(remarkBadge, titleDiv.nextSibling);
                        }
                    }
                }
            }
        });
    }

   // View document remarks modal
function viewDocumentRemarks(documentKey, documentName) {
    currentModalDocumentKey = documentKey;
    currentModalDocumentName = documentName;
    
    document.getElementById('modal-doc-name').textContent = documentName;
    
    const modalRemarksList = document.getElementById('modal-remarks-list');
    const remarks = ownershipRemarks[documentKey] || [];
    
    if (remarks.length === 0) {
        modalRemarksList.innerHTML = '<div class="text-center py-4 text-gray-500">No remarks found for this document.</div>';
    } else {
        let html = '';
        remarks.forEach(remark => {
            const date = new Date(remark.created_at);
            const formattedDate = date.toLocaleString();
            const statusBadge = remark.status === 'pending_response' 
                ? '<span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Waiting Response</span>'
                : '<span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full">Resolved</span>';
            
            html += `
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-700">${escapeHtml(remark.created_by)}</span>
                            ${statusBadge}
                        </div>
                        <span class="text-xs text-gray-400">${formattedDate}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">${escapeHtml(remark.remark)}</p>
                    ${remark.response ? `
                        <div class="mt-3 pt-2 border-t border-gray-200">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <div>
                                    <p class="text-xs font-medium text-green-600">Your Response:</p>
                                    <p class="text-sm text-gray-600">${escapeHtml(remark.response)}</p>
                                    <p class="text-xs text-gray-400 mt-1">Responded: ${new Date(remark.responded_at).toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    <div class="mt-3 flex justify-end">
                        <button onclick="openChatWithStaff('${escapeHtml(remark.created_by)}')" 
                                class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Message ${escapeHtml(remark.created_by)}
                        </button>
                    </div>
                </div>
            `;
        });
        modalRemarksList.innerHTML = html;
    }
    
    document.getElementById('document-remarks-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

    // Close document remarks modal
    function closeDocumentRemarksModal() {
        document.getElementById('document-remarks-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentModalDocumentKey = null;
        currentModalDocumentName = null;
    }

    // Open chat with staff member
    function openChatWithStaff(staffName = null) {
        let targetStaffName = staffName;
        
        if (!targetStaffName) {
            for (const [docKey, remarks] of Object.entries(ownershipRemarks)) {
                const pendingRemark = remarks.find(r => r.status === 'pending_response');
                if (pendingRemark && pendingRemark.created_by_name) {
                    targetStaffName = pendingRemark.created_by_name;
                    break;
                }
            }
        }
        
        if (targetStaffName) {
            window.location.href = `/chat?staff=${encodeURIComponent(targetStaffName)}`;
        } else {
            window.location.href = '/chat';
        }
    }

    // Mark remark as responded
    function markRemarkAsResponded(documentKey, remarkIndex, responseText) {
        if (ownershipRemarks[documentKey] && ownershipRemarks[documentKey][remarkIndex]) {
            ownershipRemarks[documentKey][remarkIndex].response = responseText;
            ownershipRemarks[documentKey][remarkIndex].status = 'resolved';
            ownershipRemarks[documentKey][remarkIndex].responded_at = new Date().toISOString();
            saveOwnershipRemarks();
            displayOwnershipRemarksNotice();
            displayOwnershipDocuments(); // Refresh document list
        }
    }

    // Display ownership documents (Step 1)
    function displayOwnershipDocuments() {
        const container = document.getElementById('ownership-documents-list');
        
        if (!currentOwnershipData) {
            displayEmptyOwnershipDocuments();
            return;
        }
        
        let html = '';
        let hasDocuments = false;
        
        const ownershipLinks = {
            'tct_link': currentOwnershipData.tct_link,
            'tax_declaration_link': currentOwnershipData.tax_declaration_link,
            'current_tax_receipt_link': currentOwnershipData.current_tax_receipt_link,
            'spa_link': currentOwnershipData.spa_link
        };
        
        for (const [key, value] of Object.entries(ownershipLinks)) {
            if (value && value.trim() !== '') {
                hasDocuments = true;
                const docName = ownershipDocumentNames[key] || key.replace(/_/g, ' ').replace(/_link$/, '').replace(/\b\w/g, l => l.toUpperCase());
                
                const spaBadge = key === 'spa_link' ? 
                    '<span class="ml-2 text-xs px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded-full">Authorization</span>' : '';
                
                html += `
                    <div class="flex items-center justify-between p-3 bg-teal-50 rounded-lg hover:bg-teal-100 transition group">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-8 h-8 bg-teal-200 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center flex-wrap gap-1">
                                    <p class="text-sm font-medium text-gray-800">${escapeHtml(docName)}</p>
                                    ${spaBadge}
                                </div>
                                <p class="text-xs text-gray-500 truncate">${escapeHtml(value.length > 60 ? value.substring(0, 60) + '...' : value)}</p>
                            </div>
                        </div>
                        <a href="${escapeHtml(value)}" target="_blank" rel="noopener noreferrer" class="text-teal-700 hover:text-teal-900 text-sm flex items-center gap-1 flex-shrink-0 ml-2">
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
            displayEmptyOwnershipDocuments();
        } else {
            container.innerHTML = html;
            // Add remark badges after rendering
            addRemarkBadgesToDocuments();
        }
    }

    function displayEmptyOwnershipDocuments() {
        const container = document.getElementById('ownership-documents-list');
        container.innerHTML = `
            <div class="text-center py-6 text-gray-500 animate-fade-in">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-sm">No ownership documents uploaded yet</p>
                <p class="text-xs text-gray-400 mt-1">Please complete Step 1: Ownership Verification</p>
            </div>
        `;
    }

    // Ownership status info
    function displayOwnershipInfo() {
        if (!currentOwnershipData) return;
        
        const ownershipCard = document.getElementById('ownership-status-card');
        const ownershipStatusText = document.getElementById('ownership-status-text');
        
        if (ownershipCard && ownershipStatusText) {
            ownershipCard.classList.remove('hidden');
            
            if (currentOwnershipData.is_owner == 1) {
                ownershipStatusText.innerHTML = '<span class="font-medium text-teal-700">Property Owner</span> - You are registered as the property owner.';
            } else {
                ownershipStatusText.innerHTML = '<span class="font-medium text-teal-700">Authorized Representative</span> - You have provided a Special Power of Attorney (SPA).';
            }
        }
    }

    // Set star rating
    function setCPDORating(rating) {
        currentCPDORating = rating;
        document.getElementById('cpdo-rating-value').value = rating;
        
        const stars = document.querySelectorAll('#cpdo-rating-modal .rating-star');
        stars.forEach((star, index) => {
            const svg = star.querySelector('svg');
            if (index < rating) {
                svg.classList.add('text-yellow-400');
                svg.classList.remove('text-gray-300');
            } else {
                svg.classList.add('text-gray-300');
                svg.classList.remove('text-yellow-400');
            }
        });
        
        document.getElementById('rating-error')?.classList.add('hidden');
    }

    // Submit CPDO Rating
    async function submitCPDORating(event) {
        event.preventDefault();
        
        const rating = document.getElementById('cpdo-rating-value').value;
        if (!rating || rating === '0') {
            document.getElementById('rating-error')?.classList.remove('hidden');
            return;
        }
        
        const formData = {
            application_id: document.getElementById('cpdo-application-id').value,
            rating: parseInt(rating),
            processing_time: document.querySelector('input[name="processing_time"]:checked')?.value,
            responsiveness: document.querySelector('input[name="responsiveness"]:checked')?.value,
            clarity: document.querySelector('input[name="clarity"]:checked')?.value,
            fairness: document.querySelector('input[name="fairness"]:checked')?.value,
            overall_satisfaction: document.querySelector('input[name="overall_satisfaction"]:checked')?.value,
            comments: document.getElementById('cpdo-comments').value
        };
        
        const submitBtn = document.getElementById('submit-cpdo-rating-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        submitBtn.disabled = true;
        
        try {
            const csrfToken = getCsrfToken();
            const response = await fetch('/applicant/cpdo-rating/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Thank you for your feedback!');
                closeCPDORatingModal();
            } else {
                showErrorModal(data.message || 'Failed to submit rating');
            }
        } catch (error) {
            console.error('Error submitting CPDO rating:', error);
            showErrorModal('An error occurred. Please try again.');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Show CPDO Rating Modal
    function showCPDORatingModal(applicationId) {
        if (hasShownCPDORatingModal) return;
        
        document.getElementById('cpdo-application-id').value = applicationId;
        document.getElementById('cpdo-rating-value').value = '';
        currentCPDORating = 0;
        
        document.querySelectorAll('#cpdo-rating-modal input[type="radio"]').forEach(radio => radio.checked = false);
        document.getElementById('cpdo-comments').value = '';
        
        const stars = document.querySelectorAll('#cpdo-rating-modal .rating-star svg');
        stars.forEach(star => {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        });
        
        document.getElementById('cpdo-rating-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        hasShownCPDORatingModal = true;
    }

    // Close CPDO Rating Modal
    function closeCPDORatingModal() {
        document.getElementById('cpdo-rating-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Check if CPDO rating is needed
    function checkCPDORatingNeeded() {
        if (hasShownCPDORatingModal) return;
        
        fetch(`/applicant/cpdo-rating/check/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.has_rated) {
                setTimeout(() => {
                    showCPDORatingModal(applicationId);
                }, 1500);
            }
        })
        .catch(error => console.error('Error checking CPDO rating:', error));
    }

    // Toggle reviewers section
    function toggleReviewersSection() {
        const content = document.getElementById('reviewers-collapsible-content');
        const chevron = document.getElementById('reviewers-chevron');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    }

    // Load application details on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Application ID from URL:', applicationId);
        if (applicationId && !isNaN(applicationId)) {
            loadApplicationDetails();
            startRealTimeUpdates();
            checkPendingSurveys();
        } else {
            showError();
        }
        setupModals();
    });

    // Start real-time updates
    function startRealTimeUpdates() {
        updateCheckInterval = setInterval(checkForUpdates, 30000);
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

    // Load CPDO data
    async function loadCPDOData() {
        if (!applicationId) return;
        
        try {
            const csrfToken = getCsrfToken();
            
            const statusResponse = await fetch(`/staff/applications/${applicationId}/cpdo-status`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (statusResponse.ok) {
                const statusData = await statusResponse.json();
                if (statusData.success && statusData.data) {
                    cpdoStatus = statusData.data.status || 'pending';
                    cpdoRemarks = statusData.data.remarks || null;
                    cpdoApprovedBy = statusData.data.approved_by || null;
                    cpdoApprovedAt = statusData.data.approved_at || null;
                    displayCPDOStatus();
                }
            }
            
            const assessmentResponse = await fetch(`/staff/applications/${applicationId}/cpdo-assessment`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (assessmentResponse.ok) {
                const assessmentData = await assessmentResponse.json();
                if (assessmentData.success && assessmentData.data) {
                    currentCPDOAssessment = assessmentData.data;
                    displayCPDOAssessment();
                    
                    if (cpdoStatus === 'approved' || cpdoStatus === 'approved_by_cpdo') {
                        const paymentProofCard = document.getElementById('payment-proof-card');
                        if (paymentProofCard) {
                            paymentProofCard.classList.remove('hidden');
                            loadPaymentProof();
                        }
                    }
                }
            } else if (cpdoStatus === 'approved') {
                const paymentProofCard = document.getElementById('payment-proof-card');
                if (paymentProofCard) {
                    paymentProofCard.classList.remove('hidden');
                    loadPaymentProof();
                }
            }
        } catch (error) {
            console.error('Error loading CPDO data:', error);
            if (cpdoStatus === 'approved') {
                const paymentProofCard = document.getElementById('payment-proof-card');
                if (paymentProofCard) {
                    paymentProofCard.classList.remove('hidden');
                    loadPaymentProof();
                }
            }
        }
    }

    // Display CPDO Status
    function displayCPDOStatus() {
        const statusBadge = document.getElementById('cpdo-status-badge');
        const remarksDisplay = document.getElementById('cpdo-remarks-display');
        const remarksText = document.getElementById('cpdo-remarks-text');
        const approvedInfo = document.getElementById('cpdo-approved-info');
        const approvedByName = document.getElementById('cpdo-approved-by');
        const approvedAtDate = document.getElementById('cpdo-approved-at');
        
        if (!statusBadge) return;
        
        if (cpdoStatus === 'approved' || cpdoStatus === 'approved_by_cpdo') {
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-700';
            statusBadge.textContent = 'Approved';
            if (remarksText && cpdoRemarks) {
                remarksDisplay.classList.remove('hidden');
                remarksText.textContent = cpdoRemarks;
            }
            if (cpdoApprovedBy && cpdoApprovedAt) {
                approvedInfo.classList.remove('hidden');
                approvedByName.textContent = cpdoApprovedBy;
                approvedAtDate.textContent = new Date(cpdoApprovedAt).toLocaleString();
            }
        } else if (cpdoStatus === 'rejected') {
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-700';
            statusBadge.textContent = 'Rejected';
            if (remarksText && cpdoRemarks) {
                remarksDisplay.classList.remove('hidden');
                remarksText.textContent = cpdoRemarks;
            }
            if (cpdoApprovedBy && cpdoApprovedAt) {
                approvedInfo.classList.remove('hidden');
                approvedByName.textContent = cpdoApprovedBy;
                approvedAtDate.textContent = new Date(cpdoApprovedAt).toLocaleString();
            }
        } else {
            statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700';
            statusBadge.textContent = 'Pending';
            remarksDisplay.classList.add('hidden');
            approvedInfo.classList.add('hidden');
        }
    }

    // Display CPDO Assessment
    function displayCPDOAssessment() {
        if (!currentCPDOAssessment) return;
        
        const assessmentDetails = document.getElementById('cpdo-assessment-details');
        if (!assessmentDetails) return;
        
        let hasFees = false;
        let html = '<div class="space-y-2">';
        
        if (currentCPDOAssessment.assessment_date) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">Assessment Date:</span><span class="font-medium text-orange-700">${currentCPDOAssessment.assessment_date}</span></div>`;
            html += `<div class="border-t border-orange-200 my-2"></div>`;
            hasFees = true;
        }
        
        if (currentCPDOAssessment.zonal_location_fee && parseFloat(currentCPDOAssessment.zonal_location_fee) > 0) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">Locational Clearance:</span><span class="font-medium">₱${parseFloat(currentCPDOAssessment.zonal_location_fee).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
            hasFees = true;
        }
        if (currentCPDOAssessment.palc_fee && parseFloat(currentCPDOAssessment.palc_fee) > 0) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">PALC Fee:</span><span class="font-medium">₱${parseFloat(currentCPDOAssessment.palc_fee).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
            hasFees = true;
        }
        if (currentCPDOAssessment.development_permit_fee && parseFloat(currentCPDOAssessment.development_permit_fee) > 0) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">Development Permit:</span><span class="font-medium">₱${parseFloat(currentCPDOAssessment.development_permit_fee).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
            hasFees = true;
        }
        if (currentCPDOAssessment.alteration_permit_fee && parseFloat(currentCPDOAssessment.alteration_permit_fee) > 0) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">Alteration Permit:</span><span class="font-medium">₱${parseFloat(currentCPDOAssessment.alteration_permit_fee).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
            hasFees = true;
        }
        
        if (currentCPDOAssessment.site_zoning_certificate_fee && parseFloat(currentCPDOAssessment.site_zoning_certificate_fee) > 0) {
            html += `<div class="flex justify-between text-sm"><span class="text-gray-600">Site/Zoning Certificate:</span><span class="font-medium">₱${parseFloat(currentCPDOAssessment.site_zoning_certificate_fee).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
            hasFees = true;
        }
        
        if (currentCPDOAssessment.cpdo_additional_fees && currentCPDOAssessment.cpdo_additional_fees.length > 0) {
            html += `<div class="border-t border-orange-100 my-2 pt-2"><span class="text-xs font-semibold text-gray-600">Additional Fees:</span></div>`;
            currentCPDOAssessment.cpdo_additional_fees.forEach(fee => {
                if (fee.amount && parseFloat(fee.amount) > 0) {
                    const description = fee.description || 'Additional Fee';
                    html += `<div class="flex justify-between text-sm pl-2"><span class="text-gray-500">${escapeHtml(description)}:</span><span class="font-medium">₱${parseFloat(fee.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span></div>`;
                    hasFees = true;
                }
            });
        }
        
        if (currentCPDOAssessment.total_cpdo_amount && parseFloat(currentCPDOAssessment.total_cpdo_amount) > 0) {
            html += `
                <div class="border-t border-orange-200 mt-2 pt-2">
                    <div class="flex justify-between font-bold">
                        <span class="text-orange-800">Total CPDO Fees:</span>
                        <span class="text-orange-800">₱${parseFloat(currentCPDOAssessment.total_cpdo_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>
            `;
            hasFees = true;
        }
        
        if (currentCPDOAssessment.cpdo_assessment_notes) {
            html += `
                <div class="mt-3 pt-2 border-t border-orange-200">
                    <p class="text-xs text-gray-500 italic">"${escapeHtml(currentCPDOAssessment.cpdo_assessment_notes)}"</p>
                </div>
            `;
        }
        
        if (currentCPDOAssessment.cpdo_assessed_by) {
            html += `
                <div class="mt-2 text-xs text-gray-400">
                    Assessed by: ${escapeHtml(currentCPDOAssessment.cpdo_assessed_by)}${currentCPDOAssessment.cpdo_assessed_at ? ' on ' + new Date(currentCPDOAssessment.cpdo_assessed_at).toLocaleDateString() : ''}
                </div>
            `;
        }
        
        html += `</div>`;
        
        if (!hasFees) {
            html = '<div class="text-center py-4 text-gray-500 text-sm">No assessment fees have been added yet.</div>';
        }
        
        assessmentDetails.innerHTML = html;
    }

    // Load ownership data
    async function loadOwnershipData() {
        if (!applicationId) return;
        
        try {
            const response = await fetch(`/applicant/applications/${applicationId}/ownership`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentOwnershipData = data.data;
                    displayOwnershipInfo();
                    displayOwnershipDocuments();
                } else {
                    displayEmptyOwnershipDocuments();
                }
            } else {
                displayEmptyOwnershipDocuments();
            }
            
            // Load ownership remarks after ownership data
            loadOwnershipRemarks();
        } catch (error) {
            console.error('Error loading ownership data:', error);
            displayEmptyOwnershipDocuments();
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

    // Display BFP data
    function displayBFPData() {
        if (!currentBfpData) return;
        
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
            const assessmentNotice = document.getElementById('assessment-notice');
            const assessmentTotal = document.getElementById('assessment-total');
            if (assessmentNotice && assessmentTotal) {
                assessmentTotal.innerHTML = `Total Fee: ₱${parseFloat(currentAssessment.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                assessmentNotice.classList.remove('hidden');
            }
            
            const assessmentCard = document.getElementById('assessment-fee-card');
            const feeAmount = document.getElementById('assessment-fee-amount');
            const feeDetails = document.getElementById('assessment-fee-details');
            
            if (assessmentCard && feeAmount) {
                feeAmount.textContent = `₱${parseFloat(currentAssessment.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                assessmentCard.classList.remove('hidden');
                
                let breakdownHtml = '';
                
                if (currentAssessment.line_grade && parseFloat(currentAssessment.line_grade) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Line Grade:</span><span>₱${parseFloat(currentAssessment.line_grade).toLocaleString()}</span></div>`;
                }
                if (currentAssessment.building_fee && parseFloat(currentAssessment.building_fee) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Building Fee:</span><span>₱${parseFloat(currentAssessment.building_fee).toLocaleString()}</span></div>`;
                }
                if (currentAssessment.sanitary_fee && parseFloat(currentAssessment.sanitary_fee) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Sanitary/Plumbing:</span><span>₱${parseFloat(currentAssessment.sanitary_fee).toLocaleString()}</span></div>`;
                }
                if (currentAssessment.mechanical_fee && parseFloat(currentAssessment.mechanical_fee) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Mechanical Fee:</span><span>₱${parseFloat(currentAssessment.mechanical_fee).toLocaleString()}</span></div>`;
                }
                if (currentAssessment.electrical_fee && parseFloat(currentAssessment.electrical_fee) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Electrical Fee:</span><span>₱${parseFloat(currentAssessment.electrical_fee).toLocaleString()}</span></div>`;
                }
                if (currentAssessment.penalties_fines && parseFloat(currentAssessment.penalties_fines) > 0) {
                    breakdownHtml += `<div class="flex justify-between"><span>Penalties/Fines:</span><span>₱${parseFloat(currentAssessment.penalties_fines).toLocaleString()}</span></div>`;
                }
                
                if (currentAssessment.additional_fees && currentAssessment.additional_fees.length > 0) {
                    breakdownHtml += `<div class="border-t border-gray-200 my-2 pt-2"><span class="font-semibold text-gray-600">Additional Fees:</span></div>`;
                    currentAssessment.additional_fees.forEach(fee => {
                        if (fee.amount && parseFloat(fee.amount) > 0) {
                            const description = fee.description || 'Additional Fee';
                            breakdownHtml += `<div class="flex justify-between pl-2"><span class="text-gray-600">${escapeHtml(description)}:</span><span>₱${parseFloat(fee.amount).toLocaleString()}</span></div>`;
                        }
                    });
                }
                
                if (breakdownHtml) {
                    feeDetails.innerHTML = breakdownHtml;
                } else {
                    feeDetails.innerHTML = '<div class="text-center text-gray-500">Fee breakdown not available</div>';
                }
            }
        }
    }

    // Display hard copy submission info
    function displayHardCopySubmissionInfo(application) {
        const submissionNotice = document.getElementById('hardcopy-submission-notice');
        const submissionInfoSidebar = document.getElementById('hardcopy-submission-info');
        const submissionDateEl = document.getElementById('hardcopy-submission-date');
        const submissionInstructionsEl = document.getElementById('hardcopy-instructions');
        const sidebarDateEl = document.getElementById('sidebar-submission-date');
        const sidebarInstructionsEl = document.getElementById('sidebar-submission-instructions');
        
        if (application.hardcopy_submission_date) {
            let formattedDate = application.hardcopy_submission_date;
            try {
                const dateObj = new Date(application.hardcopy_submission_date);
                if (!isNaN(dateObj)) {
                    formattedDate = dateObj.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            } catch(e) {}
            
            if (submissionDateEl) submissionDateEl.textContent = formattedDate;
            if (sidebarDateEl) sidebarDateEl.textContent = formattedDate;
            
            if (application.hardcopy_instructions) {
                if (submissionInstructionsEl) submissionInstructionsEl.textContent = application.hardcopy_instructions;
                if (sidebarInstructionsEl) sidebarInstructionsEl.textContent = application.hardcopy_instructions;
            }
            
            if (submissionNotice) submissionNotice.classList.remove('hidden');
            if (submissionInfoSidebar) submissionInfoSidebar.classList.remove('hidden');
            
            const regularNotice = document.getElementById('hardcopy-notice');
            if (regularNotice) regularNotice.classList.add('hidden');
        } else {
            if (submissionNotice) submissionNotice.classList.add('hidden');
            if (submissionInfoSidebar) submissionInfoSidebar.classList.add('hidden');
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

    function showDocumentVerificationAlert() {
        const alert = document.getElementById('document-verification-alert');
        if (alert) {
            alert.classList.remove('hidden');
            setTimeout(() => {
                alert.classList.add('hidden');
            }, 10000);
        }
    }

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
                if (currentApplication.cpdo_status) {
                    cpdoStatus = currentApplication.cpdo_status;
                    cpdoRemarks = currentApplication.cpdo_remarks || null;
                }
                displayApplicationDetails();
                loadReviewActivities();
                loadAssessmentData();
                loadBFPData();
                loadOwnershipData();
                loadCPDOData();
                
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
        document.getElementById('info-electrical-name').textContent = app.electrical_engineer_name || 'Not provided';
        document.getElementById('info-electrical-license').textContent = app.electrical_engineer_license || 'Not provided';
        document.getElementById('info-sanitary-name').textContent = app.sanitary_engineer_name || 'Not provided';
        document.getElementById('info-sanitary-license').textContent = app.sanitary_engineer_license || 'Not provided';
        
        document.getElementById('project-title').textContent = app.project_title || 'Building Permit Application';
    }

    // Display project documents list (Step 2)
    const projectDocumentNames = {
        'app_letter_link': 'Application Letter',
        'bp_forms_link': 'Building Permit Forms',
        'arch_plans_link': 'Architectural Plans',
        'structural_plans_link': 'Civil/Structural Plans',
        'electrical_plans_link': 'Electrical Plans',
        'plumbing_plans_link': 'Sanitary/Plumbing Plans',
        'mechanical_plans_link': 'Mechanical Plans',
        'fencing_plans_link': 'Fencing Plans',
        'bom_link': 'Bill of Materials',
        'structural_analysis_link': 'Structural Design Analysis',
        'barangay_clearance_link': 'Barangay Clearance',
        'valid_id_link': 'Valid ID',
        'cshp_link': 'CSHP from DOLE (Optional)',
        'ptr_license_link': 'PTR License No.',
        'zoning_compliance_link': 'Zoning Compliance',
        'geodetic_plan_link': 'Geodetic Plan'
    };

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
                const docName = projectDocumentNames[key] || key.replace(/_/g, ' ').replace(/_link$/, '').replace(/\b\w/g, l => l.toUpperCase());
                
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
            updateDocumentStatus('Available');
        }
    }

    function showEmptyDocuments() {
        const container = document.getElementById('documents-list');
        container.innerHTML = `
            <div class="text-center py-6 text-gray-500 animate-fade-in">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">No project documents uploaded yet</p>
                <p class="text-xs text-gray-400 mt-1">Please complete Step 2: Project Information</p>
            </div>
        `;
        updateDocumentStatus('Not Uploaded');
    }

    function updateDocumentStatus(status) {
        const statusEl = document.getElementById('document-status');
        if (statusEl) {
            statusEl.textContent = status;
            if (status === 'Available') {
                statusEl.className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full transition-all duration-500';
            } else {
                statusEl.className = 'text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full transition-all duration-500';
            }
        }
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
        const reviewerCountSpan = document.getElementById('reviewer-count');
        
        if (!activities || activities.length === 0) {
            showEmptyReviewers();
            if (reviewerCountSpan) reviewerCountSpan.textContent = '0';
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
        
        if (reviewerCountSpan) reviewerCountSpan.textContent = reviewers.length;
        
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
                } else if (reviewer.lastAction === 'cpdo_assessment_saved') {
                    statusText = 'CPDO Assessment';
                    statusClass = 'bg-orange-100 text-orange-600';
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
        
        const content = document.getElementById('reviewers-collapsible-content');
        const chevron = document.getElementById('reviewers-chevron');
        if (reviewers.length > 0) {
            content.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

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
        const reviewerCountSpan = document.getElementById('reviewer-count');
        if (reviewerCountSpan) reviewerCountSpan.textContent = '0';
        
        const content = document.getElementById('reviewers-collapsible-content');
        const chevron = document.getElementById('reviewers-chevron');
        content.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

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
            } else if (activity.action === 'cpdo_assessment_saved') {
                iconColor = 'bg-orange-100';
                iconTextColor = 'text-orange-600';
                iconSvg = `<svg class="h-3 w-3 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>`;
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
            } else if (activity.action === 'cpdo_assessment_saved') {
                actionDisplay = 'CPDO Assessment Completed';
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
        displayHardCopySubmissionInfo(currentApplication);
        updateHardCopyStatus(currentApplication.hard_copy_received);

        if (currentApplication.status === 'document-verification') {
            showDocumentVerificationStatus(true);
        } else {
            showDocumentVerificationStatus(false);
        }
    }

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
            const hasSubmissionDate = currentApplication?.hardcopy_submission_date;
            if (!hasSubmissionDate) {
                if (hardcopyNotice) hardcopyNotice.classList.remove('hidden');
            }
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.add('hidden');
            if (hardcopyBadge) {
                hardcopyBadge.textContent = 'Pending';
                hardcopyBadge.className = 'text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full transition-all duration-500';
            }
            if (hardcopyMessage) hardcopyMessage.textContent = 'Submit originals to OBO';
            if (hardcopySidebar) hardcopySidebar.className = 'mt-4 p-3 bg-blue-50 rounded-lg transition-all duration-500';
        }
    }

    function downloadApplicationSummary() {
        if (!currentApplication) {
            showErrorModal('No application data to download');
            return;
        }
        
        let summary = `BUILDING PERMIT APPLICATION SUMMARY\n`;
        summary += `================================\n\n`;
        summary += `Application Number: ${currentApplication.application_number || 'N/A'}\n`;
        summary += `Status: ${formatStatusDisplay(currentApplication.status)}\n`;
        summary += `Submitted: ${currentApplication.submitted_at ? new Date(currentApplication.submitted_at).toLocaleDateString() : 'N/A'}\n\n`;
        
        if (currentApplication.hardcopy_submission_date) {
            summary += `HARD COPY SUBMISSION SCHEDULE\n`;
            summary += `=============================\n`;
            summary += `Submission Date: ${currentApplication.hardcopy_submission_date}\n`;
            if (currentApplication.hardcopy_instructions) {
                summary += `Instructions: ${currentApplication.hardcopy_instructions}\n`;
            }
            summary += `\n`;
        }
        
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
        
        if (currentAssessment && currentAssessment.total_amount) {
            summary += `BUILDING PERMIT FEE ASSESSMENT\n`;
            summary += `-----------------------------\n`;
            summary += `Total Fee: ₱${parseFloat(currentAssessment.total_amount).toLocaleString()}\n\n`;
        }
        
        if (currentCPDOAssessment && currentCPDOAssessment.total_cpdo_amount) {
            summary += `CPDO FEE ASSESSMENT\n`;
            summary += `-------------------\n`;
            summary += `Total CPDO Fees: ₱${parseFloat(currentCPDOAssessment.total_cpdo_amount).toLocaleString()}\n`;
            if (currentCPDOAssessment.assessment_date) {
                summary += `Assessment Date: ${currentCPDOAssessment.assessment_date}\n`;
            }
            summary += `\n`;
        }
        
        if (cpdoStatus) {
            summary += `CPDO STATUS\n`;
            summary += `-----------\n`;
            summary += `Status: ${cpdoStatus.toUpperCase()}\n`;
            if (cpdoRemarks) {
                summary += `Remarks: ${cpdoRemarks}\n`;
            }
            summary += `\n`;
        }
        
        summary += `Generated on: ${new Date().toLocaleString()}\n`;
        
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

    // Check for pending surveys
    async function checkPendingSurveys() {
        try {
            const response = await fetch('/applicant/survey/pending', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.pending_surveys && data.pending_surveys.length > 0) {
                    const firstSurvey = data.pending_surveys[0];
                    setTimeout(() => {
                        if (window.showSurveyModal) {
                            window.showSurveyModal(firstSurvey.id, firstSurvey.service_availed);
                        }
                    }, 2000);
                }
            }
        } catch (error) {
            console.error('Error checking pending surveys:', error);
        }
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

    // Payment Proof variables
    let currentPaymentProof = null;

    // Load payment proof data
    async function loadPaymentProof() {
        if (!applicationId) return;
        
        try {
            const csrfToken = getCsrfToken();
            const response = await fetch(`/applicant/payment-proof/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data && data.data.or_link) {
                    currentPaymentProof = data.data;
                    displayPaymentProof();
                    displayCPDOCertificates();
                    return;
                }
            }
            const form = document.getElementById('payment-proof-form');
            const display = document.getElementById('payment-proof-display');
            const statusBadge = document.getElementById('payment-status-badge');
            
            if (form) form.classList.remove('hidden');
            if (display) display.classList.add('hidden');
            if (statusBadge) {
                statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full';
                statusBadge.textContent = 'Not Uploaded';
            }
        } catch (error) {
            console.error('Error loading payment proof:', error);
            const form = document.getElementById('payment-proof-form');
            const display = document.getElementById('payment-proof-display');
            if (form) form.classList.remove('hidden');
            if (display) display.classList.add('hidden');
        }
    }

    // Display CPDO Certificates
    function displayCPDOCertificates() {
        if (!currentPaymentProof) return;
        
        const certificatesSection = document.getElementById('cpdo-certificates-section');
        const zoningContainer = document.getElementById('zoning-cert-container');
        const locationalContainer = document.getElementById('locational-cert-container');
        
        let hasAnyCertificate = false;
        
        if (currentPaymentProof.zoning_cert_link) {
            zoningContainer.classList.remove('hidden');
            document.getElementById('zoning-cert-link').href = currentPaymentProof.zoning_cert_link;
            
            let metaText = '';
            if (currentPaymentProof.zoning_cert_uploaded_at) {
                metaText += `Uploaded: ${new Date(currentPaymentProof.zoning_cert_uploaded_at).toLocaleString()}`;
            }
            if (currentPaymentProof.zoning_cert_uploader && currentPaymentProof.zoning_cert_uploader.full_name) {
                metaText += metaText ? ' by ' : 'By: ';
                metaText += currentPaymentProof.zoning_cert_uploader.full_name;
            }
            document.getElementById('zoning-cert-meta').textContent = metaText;
            hasAnyCertificate = true;
        } else {
            zoningContainer.classList.add('hidden');
        }
        
        if (currentPaymentProof.locational_clearance_link) {
            locationalContainer.classList.remove('hidden');
            document.getElementById('locational-cert-link').href = currentPaymentProof.locational_clearance_link;
            
            let metaText = '';
            if (currentPaymentProof.locational_clearance_uploaded_at) {
                metaText += `Uploaded: ${new Date(currentPaymentProof.locational_clearance_uploaded_at).toLocaleString()}`;
            }
            if (currentPaymentProof.locational_clearance_uploader && currentPaymentProof.locational_clearance_uploader.full_name) {
                metaText += metaText ? ' by ' : 'By: ';
                metaText += currentPaymentProof.locational_clearance_uploader.full_name;
            }
            document.getElementById('locational-cert-meta').textContent = metaText;
            hasAnyCertificate = true;
        } else {
            locationalContainer.classList.add('hidden');
        }
        
        if (hasAnyCertificate) {
            certificatesSection.classList.remove('hidden');
        } else {
            certificatesSection.classList.add('hidden');
        }
    }

    // Display payment proof
    function displayPaymentProof() {
        if (!currentPaymentProof) return;
        
        const statusBadge = document.getElementById('payment-status-badge');
        const statusText = document.getElementById('payment-status-text');
        const proofLink = document.getElementById('payment-proof-link');
        const rejectionDiv = document.getElementById('payment-rejection-reason');
        const form = document.getElementById('payment-proof-form');
        const display = document.getElementById('payment-proof-display');
        
        proofLink.href = currentPaymentProof.or_link;
        
        statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
        statusBadge.textContent = 'Uploaded ✓';
        statusText.className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
        statusText.textContent = 'Uploaded';
        
        rejectionDiv.classList.add('hidden');
        
        if (form) form.classList.add('hidden');
        if (display) display.classList.remove('hidden');
    }

    // Show payment proof form
    function showPaymentProofForm() {
        const form = document.getElementById('payment-proof-form');
        const display = document.getElementById('payment-proof-display');
        const orLinkInput = document.getElementById('or-link');
        
        if (orLinkInput && currentPaymentProof) {
            orLinkInput.value = currentPaymentProof.or_link || '';
        }
        if (form) form.classList.remove('hidden');
        if (display) display.classList.add('hidden');
    }

    // Upload payment proof
    async function uploadPaymentProof() {
        const orLink = document.getElementById('or-link').value.trim();
        
        if (!orLink) {
            showErrorModal('Missing Link', 'Please provide a Google Drive link to your Official Receipt.');
            return;
        }
        
        if (!orLink.includes('drive.google.com') && !orLink.includes('docs.google.com')) {
            showErrorModal('Invalid Link', 'Please provide a valid Google Drive link.');
            return;
        }
        
        const btn = document.getElementById('upload-or-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        btn.disabled = true;
        
        try {
            const csrfToken = getCsrfToken();
            const response = await fetch('/applicant/payment-proof/upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    or_link: orLink
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Success', 'Payment proof uploaded successfully! Our staff will verify it shortly.');
                await loadPaymentProof();
                const formEl = document.getElementById('payment-proof-form');
                const displayEl = document.getElementById('payment-proof-display');
                if (formEl) formEl.classList.add('hidden');
                if (displayEl) displayEl.classList.remove('hidden');
                
                checkCPDORatingNeeded();
            } else {
                showErrorModal('Upload Failed', data.message || 'Failed to upload payment proof');
            }
        } catch (error) {
            console.error('Error uploading payment proof:', error);
            showErrorModal('Error', 'Failed to upload payment proof');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endsection