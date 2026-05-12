@extends('layouts.dashboard')

@section('title', 'Application Details - Staff View')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="/staff/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Applications List
        </a>
        
        <div class="flex items-center gap-3">
            <button onclick="exportAsPDF()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Export PDF
            </button>
            <button onclick="openArchiveModal()" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                Archive Application
            </button>
        </div>
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
        <a href="/staff/applications" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">Back to Applications</a>
    </div>

    <!-- Application Content -->
    <div id="application-content" class="hidden">

        <!-- Hard Copy Notice -->
        <div id="hardcopy-notice" class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hard Copy Pending</h4>
                    <p class="text-sm text-gray-700 mt-1">Physical documents have not been received yet.</p>
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
                    <p class="text-sm text-gray-700 mt-1">Physical documents have been received and verified.</p>
                </div>
            </div>
        </div>

        <!-- Assessment Notice -->
        <div id="assessment-notice" class="mb-6 p-4 bg-purple-100 border-l-4 border-purple-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Assessment Completed</h4>
                    <p id="assessment-total" class="text-sm text-gray-700 mt-1">Total Building Permit Fee: ₱0.00</p>
                </div>
            </div>
        </div>

        <!-- CPDO Notice (when pending) -->
        <div id="cpdo-pending-notice" class="mb-6 p-4 bg-orange-100 border-l-4 border-orange-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">CPDO Approval Pending</h4>
                    <p class="text-sm text-gray-700 mt-1">CPDO is reviewing the application. Document verification for other departments is disabled until CPDO approves. Ownership document verification (Step 1) is still available.</p>
                </div>
            </div>
        </div>

        <!-- CPDO Rejected Notice -->
        <div id="cpdo-rejected-notice" class="mb-6 p-4 bg-red-100 border-l-4 border-red-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">CPDO Rejected</h4>
                    <p id="cpdo-rejection-reason" class="text-sm text-gray-700 mt-1">This application has been rejected by CPDO.</p>
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

        <!-- Application Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-fade-in">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white text-xl font-bold">BP</div>
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <h1 class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                            <span id="status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium transition-all duration-500">Pending Review</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Application Number</span>
                                <span id="application-number" class="font-mono font-medium text-[#155386]"></span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Submitted</span>
                                <span id="submitted-date" class="font-medium text-gray-700"></span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Last Updated</span>
                                <span id="updated-date" class="font-medium text-gray-700"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Timeline -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Application Progress</h2>
            </div>
            
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-600">Overall Completion</span>
                    <div class="flex items-center gap-2">
                        <span id="progress-percentage" class="font-semibold text-[#155386] transition-all duration-500">0%</span>
                        <span class="flex items-center gap-1 text-xs text-green-600 font-medium">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                            <span class="ml-3">Live</span>
                        </span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden relative">
                    <div id="progress-bar" class="absolute inset-0 bg-gradient-to-r from-[#155386] to-[#40798C] h-full rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200"></div>
                <div id="progress-line" class="absolute top-5 left-0 w-0 h-0.5 bg-[#155386] transition-all duration-700 ease-out" style="width: 0%"></div>
                
                <div class="relative flex justify-between">
                    @php
                        $steps = [
                            'submitted' => 'Submitted',
                            'under-review' => 'Under Review',
                            'verification' => 'Document Verification',
                            'assessment' => 'Assessment',
                            'approval' => 'Approval',
                            'release' => 'For Release'
                        ];
                    @endphp
                    
                    @foreach($steps as $key => $label)
                    <div id="step-{{ $key }}" class="text-center step-item">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                            <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($key === 'submitted')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                @elseif($key === 'under-review')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                @elseif($key === 'verification')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                @elseif($key === 'assessment')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                                @elseif($key === 'approval')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                @endif
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-400 transition-all duration-500">{{ $label }}</p>
                        <p id="step-{{ $key }}-date" class="text-xs text-gray-400 transition-all duration-500"></p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Project Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Project Information</h2>
                        <span id="project-type-badge" class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full capitalize"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Project Title</p>
                            <p id="project-title" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Project Location</p>
                            <p id="project-location" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Project Description</p>
                            <p id="project-description" class="text-sm text-gray-600">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Lot Area</p>
                            <p id="lot-area" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Floor Area</p>
                            <p id="floor-area" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Number of Floors</p>
                            <p id="num-floors" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Estimated Cost</p>
                            <p id="estimated-cost" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                    </div>
                </div>

                <!-- Owner/Applicant Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Owner/Applicant Information</h2>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400">Full Name</p>
                            <p id="applicant-name" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email Address</p>
                            <p id="applicant-email" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Contact Number</p>
                            <p id="applicant-phone" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Address</p>
                            <p id="applicant-address" class="text-sm font-medium text-gray-800"></p>
                        </div>
                    </div>
                </div>

                <!-- Professional Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Professional Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400">Architect's Name</p>
                            <p id="architect-name" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Architect's License Number</p>
                            <p id="architect-license" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Civil Engineer's Name</p>
                            <p id="engineer-name" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Engineer's License Number</p>
                            <p id="engineer-license" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Electrical Engineer's Name</p>
                            <p id="electrical-engineer-name" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Electrical Engineer's License</p>
                            <p id="electrical-engineer-license" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Sanitary Engineer's Name</p>
                            <p id="sanitary-engineer-name" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Sanitary Engineer's License</p>
                            <p id="sanitary-engineer-license" class="text-sm font-medium text-gray-800">Not provided</p>
                        </div>
                    </div>
                </div>

                <!-- Ownership Documents Card (Step 1) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-800">Step 1: Ownership Documents</h2>
                    </div>
                    <div id="ownership-documents-list" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm">Loading ownership documents...</p>
                        </div>
                    </div>
                </div>

                <!-- Document Checklist Card (Step 2) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Step 2: Project Documents</h2>
                        </div>
                        <div id="verification-stats-container" class="flex items-center gap-2">
                            <span id="verified-count" class="text-sm font-semibold text-green-600">0</span>
                            <span class="text-sm text-gray-400">/</span>
                            <span id="total-count" class="text-sm font-semibold text-gray-600">0</span>
                            <span class="text-xs text-gray-500">verified</span>
                        </div>
                    </div>
                    
                    <div id="documents-checklist" class="space-y-3 max-h-[500px] overflow-y-auto pr-2 mb-4">
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm">Loading documents...</p>
                        </div>
                    </div>
                    
                    <div id="verification-actions-container" class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap justify-between gap-3">
                        <button onclick="toggleMissingDocumentsDropdown()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Request Missing Documents
                        </button>
                        <div class="flex gap-2">
                            <button onclick="resetDocumentVerification()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">Reset All</button>
                            <button onclick="saveDocumentVerification()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">Save Progress</button>
                        </div>
                    </div>
                </div>

                <!-- Staff Guidelines Card -->
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 animate-fade-in">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Staff Guidelines</h4>
                            <p class="text-sm text-gray-600">
                                <strong>Step 1 (Ownership Documents):</strong> 
                                <span class="text-green-600">CPDO</span> can verify TCT/Deed of Sale. 
                                <span class="text-purple-600">Assessor</span> can verify Tax Declaration. 
                                <span class="text-orange-600">Treasurer</span> can verify Current Tax Receipt. 
                                <span class="text-blue-600">Special Power of Attorney (SPA)</span> can be verified by CPDO, Assessor, OR Treasurer.<br>
                                <strong>Step 2 (Project Documents):</strong> Click "View" to review each document. Only Engineers and Architects can verify documents. Other roles can only view documents. CPDO must approve first before Step 2 verification begins.<br>
                                <strong>Hard Copy Check:</strong> Only Engineers and Architects can mark hard copy as received.<br>
                                <strong>CPDO Decision:</strong> Only CPDO staff can approve or reject applications. Once submitted, the decision is final and cannot be changed.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Communication Info Card -->
                <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 animate-fade-in">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">Need to communicate with the applicant?</h4>
                            <p class="text-sm text-gray-600">If you have concerns about the documents or need additional information, please use the <strong>chat feature</strong> to communicate directly with the applicant.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    
                    <!-- CPDO VERIFICATION CARD -->
                    <div id="cpdo-card" class="bg-white rounded-2xl shadow-sm border border-orange-200 p-6 animate-fade-in">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">CPDO Verification</h2>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">The City Planning and Development Office (CPDO) must review and approve all documents before other departments can proceed with verification. <strong class="text-red-600">Once submitted, the decision is FINAL and cannot be changed.</strong></p>
                        
                        <div id="cpdo-status-display" class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Current Status:</span>
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
                        
                        <!-- Form for new decision (when pending) - Only CPDO can submit -->
                        <div id="cpdo-form" class="hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Decision <span class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="cpdo_decision" value="approved" class="cpdo-radio h-4 w-4 text-green-600">
                                        <span class="text-sm text-green-600 font-medium">Approve</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="cpdo_decision" value="rejected" class="cpdo-radio h-4 w-4 text-red-600">
                                        <span class="text-sm text-red-600 font-medium">Reject</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Reason <span id="remarks-required-star" class="text-red-500 hidden">*</span></label>
                                <textarea id="cpdo-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm" placeholder="Enter remarks or reason for rejection..."></textarea>
                            </div>
                            
                            <button onclick="openCPDOConfirmationModal()" id="cpdo-submit-btn" class="w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium">Submit Decision</button>
                        </div>
                        
                        <div id="cpdo-pending-message" class="hidden p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <div class="flex items-center gap-2 text-sm text-orange-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Waiting for CPDO approval. Step 2 document verification is disabled until CPDO approves. Step 1 ownership verification is still available.</span>
                            </div>
                        </div>
                        
                        <div id="cpdo-rejected-message" class="hidden p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-2 text-sm text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>This application has been rejected by CPDO.</span>
                            </div>
                        </div>
                        
                        <div id="cpdo-approved-message" class="hidden p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center gap-2 text-sm text-green-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>This application has been approved by CPDO. Document verification can now proceed.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Card -->
                    <div id="status-update-card" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
                        
                        <div class="space-y-4">
                            <div id="current-status-card" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs text-gray-500 mb-1">Current Status</p>
                                <p id="current-status" class="text-lg font-semibold text-yellow-600">Pending Review</p>
                            </div>

                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Hard Copy Received</span>
                                    <input type="checkbox" id="hardcopy-checkbox" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                </label>
                                <p id="hardcopy-permission-warning" class="text-xs text-red-500 mt-1 hidden">Only Engineers and Architects can mark hard copy as received.</p>
                            </div>

                            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Documents Verified:</span>
                                    <span id="summary-verified" class="text-sm font-semibold text-green-600">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending Verification:</span>
                                    <span id="summary-pending" class="text-sm font-semibold text-yellow-600">0</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div id="verification-progress-bar" class="bg-green-500 h-2 rounded-full transition-all" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                @php
                                    $statusOptions = [
                                        'under-review' => ['Under Review', 'purple', ['engineer', 'architect', 'cpdo', 'administrative_aide']],
                                        'document-verification' => ['Document Verification', 'purple', ['engineer', 'architect', 'cpdo', 'administrative_aide']],
                                        'for-assessment' => ['For Assessment', 'indigo', ['engineer']],
                                        'approved' => ['Approved', 'green', ['engineer']],
                                        'rejected' => ['Rejected', 'red', ['engineer']],
                                        'for-release' => ['For Release', 'blue', ['engineer']],
                                        'verified' => ['Completed', 'emerald', ['engineer']]
                                    ];
                                @endphp
                                
                                @foreach($statusOptions as $value => [$label, $color, $allowedPositions])
                                <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200 status-option status-option-{{ $value }}" data-allowed-positions='@json($allowedPositions)'>
                                    <input type="radio" name="status" value="{{ $value }}" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                    <span class="ml-3 text-sm font-medium text-{{ $color }}-600">{{ $label }}</span>
                                    <span class="ml-auto text-xs text-gray-400 status-restricted-badge hidden">(Restricted)</span>
                                </label>
                                @endforeach
                            </div>

                            <div id="status-restriction-notice" class="hidden p-3 bg-yellow-50 rounded-lg border border-yellow-200 text-sm text-yellow-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Only Engineers can change status to For Assessment, Approved, Rejected, For Release, and Completed.</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes</label>
                                <textarea id="status-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add remarks or notes about this application..."></textarea>
                                <p class="text-xs text-gray-400 mt-1">Remarks will be saved to activity log.</p>
                            </div>

                            <button onclick="updateStatus()" id="update-status-btn" class="w-full px-4 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">Update Status</button>
                        </div>
                    </div>

                    <!-- CPDO FEE ASSESSMENT CARD -->
                    <div id="cpdo-assessment-card" class="bg-white rounded-2xl shadow-sm border border-indigo-200 p-6 animate-fade-in hidden">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">CPDO Fee Assessment</h2>
                            <span id="cpdo-assessment-status" class="ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                            <span id="cpdo-edit-badge" class="ml-2 text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full hidden">Edit Mode</span>
                        </div>
                        
                        <!-- Certificates Upload Section -->
                        <div id="certificates-section" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <h3 class="text-md font-semibold text-gray-800">Required Certificates</h3>
                                <span id="cert-upload-permission-badge" class="text-xs text-gray-500">(View Only)</span>
                            </div>
                            
                            <!-- Zoning Certificate Section -->
                            <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-gray-700">Zoning Certificate</h4>
                                    </div>
                                    <span id="zoning-cert-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Not Uploaded</span>
                                </div>
                                
                                <div id="zoning-cert-display" class="hidden">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a id="zoning-cert-link" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">View Zoning Certificate</a>
                                        </div>
                                        <button id="zoning-cert-remove-btn" onclick="removeCertificate('zoning_cert')" class="text-xs text-red-500 hover:text-red-700 hidden">Remove</button>
                                    </div>
                                    <div id="zoning-cert-meta" class="mt-2 text-xs text-gray-400"></div>
                                </div>
                                
                                <div id="zoning-cert-form" class="space-y-3 hidden">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Drive Link to Zoning Certificate <span class="text-red-500">*</span></label>
                                        <div class="flex gap-2">
                                            <input type="url" id="zoning-cert-link-input" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://drive.google.com/file/d/...">
                                            <button onclick="uploadCertificate('zoning_cert')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">Upload</button>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Only CPDO can upload this certificate</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Locational Clearance Section -->
                            <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-gray-700">Locational Clearance</h4>
                                    </div>
                                    <span id="locational-status" class="text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Not Uploaded</span>
                                </div>
                                
                                <div id="locational-display" class="hidden">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <a id="locational-link" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">View Locational Clearance</a>
                                        </div>
                                        <button id="locational-remove-btn" onclick="removeCertificate('locational_clearance')" class="text-xs text-red-500 hover:text-red-700 hidden">Remove</button>
                                    </div>
                                    <div id="locational-meta" class="mt-2 text-xs text-gray-400"></div>
                                </div>
                                
                                <div id="locational-form" class="space-y-3 hidden">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Drive Link to Locational Clearance <span class="text-red-500">*</span></label>
                                        <div class="flex gap-2">
                                            <input type="url" id="locational-link-input" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="https://drive.google.com/file/d/...">
                                            <button onclick="uploadCertificate('locational_clearance')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Upload</button>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Only CPDO can upload this certificate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Display existing assessment -->
                        <div id="cpdo-assessment-display" class="hidden">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Assessment Date:</span>
                                    <span id="display-assessment-date" class="text-sm text-gray-600"></span>
                                </div>
                                <div class="border-t border-gray-200 my-3"></div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Zonal/Location Permit Fee:</span>
                                        <span id="display-zonal-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">PALC Fee:</span>
                                        <span id="display-palc-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Development Permit Fee:</span>
                                        <span id="display-dev-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Alteration Permit Fee:</span>
                                        <span id="display-alt-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Site/Zoning Certificate:</span>
                                        <span id="display-zoning-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div id="display-cpdo-additional-fees-container" class="space-y-1"></div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between font-semibold">
                                            <span>Total CPDO Fees:</span>
                                            <span id="display-total-cpdo" class="text-indigo-600">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="display-cpdo-notes" class="mt-3 p-2 bg-gray-100 rounded text-sm text-gray-600 hidden">
                                    <span class="font-medium">Notes:</span>
                                    <span id="display-notes-text"></span>
                                </div>
                                <div class="mt-3 text-xs text-gray-400">
                                    Assessed by: <span id="display-assessed-by"></span> on <span id="display-assessed-at"></span>
                                </div>
                            </div>
                            <button id="edit-cpdo-assessment-btn" onclick="editCPDOAssessment()" class="w-full px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition text-sm font-medium hidden">Edit Assessment</button>
                        </div>
                        
                        <!-- Assessment Form -->
                        <div id="cpdo-assessment-form" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Date <span class="text-red-500">*</span></label>
                                <input type="date" id="cpdo-assessment-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-2">Applicant Information (Auto-filled)</p>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Client Name:</span>
                                        <span id="cpdo-client-name" class="font-medium block"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Address:</span>
                                        <span id="cpdo-client-address" class="font-medium block"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Zonal/Location Permit Fee</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Locational Clearance (₱)</label>
                                        <input type="number" id="cpdo-zonal-fee" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateCPDOTotal()">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">PALC (₱)</label>
                                        <input type="number" id="cpdo-palc-fee" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateCPDOTotal()">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Development Permit (₱)</label>
                                        <input type="number" id="cpdo-dev-fee" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateCPDOTotal()">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Alteration Permit (₱)</label>
                                        <input type="number" id="cpdo-alt-fee" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateCPDOTotal()">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Certifications/Clearance</h4>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Site/Zoning Certificate (₱)</label>
                                    <input type="number" id="cpdo-zoning-fee" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateCPDOTotal()">
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-semibold text-gray-700">Additional Fees</label>
                                    <button type="button" onclick="addCPDODynamicFee()" class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add Fee
                                    </button>
                                </div>
                                <div id="cpdo-dynamic-fees-container" class="space-y-2"></div>
                            </div>
                            
                            <div class="p-3 bg-indigo-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-indigo-700">Total CPDO Fees:</span>
                                    <span class="text-xl font-bold text-indigo-700">₱<span id="cpdo-total-display">0.00</span></span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Notes</label>
                                <textarea id="cpdo-assessment-notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Add any notes about this assessment..."></textarea>
                            </div>
                            
                            <button onclick="saveCPDOAssessment()" id="save-cpdo-assessment-btn" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">Save CPDO Assessment</button>
                            <button onclick="cancelCPDOEdit()" id="cancel-cpdo-edit-btn" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium hidden">Cancel Edit</button>
                        </div>
                        
                        <div id="cpdo-no-assessment-message" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                            </svg>
                            <p class="text-sm">No CPDO fee assessment yet</p>
                            <p class="text-xs text-gray-400 mt-1">The CPDO will create this assessment when ready</p>
                        </div>
                    </div>

                    <!-- BUILDING PERMIT FEE ASSESSMENT CARD -->
                    <div id="building-permit-fee-card" class="bg-white rounded-2xl shadow-sm border border-blue-200 p-6 animate-fade-in hidden">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Building Permit Fee Assessment</h2>
                            <span id="building-fee-status" class="ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                        </div>
                        
                        <div id="building-assessment-display" class="hidden">
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="border-b border-gray-200 pb-2 mb-3">
                                    <span class="text-sm font-medium text-gray-700">Fee Breakdown</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Line Grade:</span>
                                        <span id="display-building-line-grade" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Building Fee:</span>
                                        <span id="display-building-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Sanitary/Plumbing Fee:</span>
                                        <span id="display-sanitary-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Mechanical Fee:</span>
                                        <span id="display-mechanical-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Electrical Fee:</span>
                                        <span id="display-electrical-fee" class="font-medium">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Penalties/Fines:</span>
                                        <span id="display-penalties-fees" class="font-medium">₱0.00</span>
                                    </div>
                                    <div id="display-building-additional-fees-container" class="space-y-1 border-t border-gray-100 pt-2 mt-2"></div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between font-semibold">
                                            <span>Total Building Permit Fee:</span>
                                            <span id="display-total-building" class="text-blue-600">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="display-building-notes" class="mt-3 p-2 bg-gray-100 rounded text-sm text-gray-600 hidden">
                                    <span class="font-medium">Notes:</span>
                                    <span id="display-building-notes-text"></span>
                                </div>
                                <div class="mt-3 text-xs text-gray-400">
                                    Assessed by: <span id="display-building-assessed-by"></span> on <span id="display-building-assessed-at"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="building-no-assessment-message" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                            </svg>
                            <p class="text-sm">No building permit fee assessment yet</p>
                            <p class="text-xs text-gray-400 mt-1">The engineer will create this assessment when the application is ready</p>
                        </div>
                    </div>

                    <!-- OR Display Section -->
                    <div id="or-display-section" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-md font-semibold text-gray-800">Official Receipt (OR)</h3>
                            <span class="text-xs text-gray-500 ml-auto">Applicant Uploaded</span>
                        </div>
                        
                        <div id="or-loading" class="text-center py-3">
                            <svg class="animate-spin h-6 w-6 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-xs text-gray-500 mt-1">Loading OR information...</p>
                        </div>
                        
                        <div id="or-content" class="hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">OR Link:</span>
                                </div>
                                <a id="or-link-display" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">View Official Receipt</a>
                            </div>
                            <div id="or-upload-info" class="mt-2 text-xs text-gray-500">
                                Uploaded by applicant
                            </div>
                        </div>
                        
                        <div id="or-empty-message" class="hidden text-center py-3">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-xs text-gray-500">No Official Receipt uploaded yet</p>
                            <p class="text-xs text-gray-400 mt-1">The applicant will upload the OR after CPDO assessment is completed</p>
                        </div>
                    </div>

                    <!-- BFP FSEC Section -->
                    <div id="bfp-section" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in hidden">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Fire Safety Evaluation Clearance (FSEC)</h2>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">Upload the Fire Safety Evaluation Clearance for this building permit application.</p>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center gap-3">
                                    <input type="file" id="fsec-file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <button onclick="document.getElementById('fsec-file').click()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Upload FSEC
                                    </button>
                                    <span id="fsec-filename" class="text-sm text-gray-500">No file selected</span>
                                </div>
                                
                                <div id="existing-fsec-container" class="hidden">
                                    <p class="text-xs text-gray-500 mb-1">Current FSEC Document:</p>
                                    <div class="flex items-center gap-2">
                                        <a id="fsec-link" href="#" target="_blank" class="text-sm text-red-600 hover:text-red-800 underline flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                            </svg>
                                            View Current FSEC
                                        </a>
                                        <button onclick="deleteFSEC()" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                    </div>
                                    <p id="fsec-upload-date" class="text-xs text-gray-400 mt-1"></p>
                                </div>
                                
                                <div id="fsec-upload-status" class="hidden text-sm"></div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">BFP Comments / Recommendations</label>
                                <textarea id="bfp-comments" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm" placeholder="Add any comments or recommendations regarding fire safety compliance..."></textarea>
                                <div class="flex justify-end mt-2">
                                    <button onclick="saveBFPComments()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">Save Comments</button>
                                </div>
                                <div id="bfp-comments-display" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                                    <p class="text-xs text-gray-500 mb-1">Previous Comments:</p>
                                    <p id="bfp-comments-text" class="text-sm text-gray-700"></p>
                                    <p id="bfp-comments-date" class="text-xs text-gray-400 mt-1"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Activity Log</h2>
                            <span class="text-xs text-gray-400">Recent activities</span>
                        </div>
                        <div id="activity-log" class="space-y-3 min-h-[250px]">
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm">Loading activities...</p>
                            </div>
                        </div>
                        <button onclick="loadFullActivityHistory()" class="mt-4 text-sm text-[#155386] hover:text-[#40798C] font-medium w-full text-center inline-block py-2 border-t border-gray-100 hover:bg-gray-50 transition rounded-b-lg">View Full History →</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MISSING DOCUMENTS DROPDOWN MODAL -->
<div id="missing-documents-dropdown" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-96 max-w-full mx-4">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold text-lg">Request Missing Documents</h3>
            <button onclick="toggleMissingDocumentsDropdown()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-4">
            <input type="text" id="document-search" placeholder="Search documents..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-3">
            <div id="missing-docs-list" class="space-y-2 max-h-64 overflow-y-auto mb-3"></div>
            <textarea id="document-request-remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Remarks / Additional instructions (Optional)"></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="clearSelectedMissingDocuments()" class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Clear All</button>
                <button onclick="sendDocumentRequest()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] text-sm font-medium">Send Request</button>
            </div>
        </div>
    </div>
</div>

<!-- CPDO Confirmation Modal -->
<div id="cpdo-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Confirm CPDO Decision</h3>
                </div>
                <button onclick="closeCPDOConfirmationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="font-semibold text-red-800">⚠️ Warning: This action is FINAL</h4>
                            <p class="text-sm text-red-700 mt-1">Once you submit your decision, it cannot be changed or edited. Please review your decision carefully before confirming.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Decision Summary:</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Decision:</span>
                            <span id="confirm-decision-text" class="text-sm font-bold text-orange-600">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Remarks:</span>
                            <span id="confirm-remarks-text" class="text-sm text-gray-700">-</span>
                        </div>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 text-center">By confirming, you acknowledge that this decision is final and cannot be reversed.</p>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeCPDOConfirmationModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Cancel</button>
                <button onclick="confirmCPDODecision()" id="confirm-cpdo-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Yes, Submit Final Decision</button>
            </div>
        </div>
    </div>
</div>

<!-- Assessment Fee Modal -->
<div id="assessment-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Building Permit Fee Assessment</h3>
                </div>
                <button onclick="closeAssessmentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Line Grade (₱)</label><input type="number" id="line-grade" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Building Fee (₱)</label><input type="number" id="building-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Sanitary/Plumbing Fee (₱)</label><input type="number" id="sanitary-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Mechanical Fee (₱)</label><input type="number" id="mechanical-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Electrical Fee (₱)</label><input type="number" id="electrical-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Penalties/Fines (₱)</label><input type="number" id="penalties-fines" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-sm font-semibold text-gray-700">Additional Fees</label>
                        <button type="button" onclick="addDynamicFee()" class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Fee
                        </button>
                    </div>
                    <div id="dynamic-fees-container" class="space-y-2"></div>
                </div>

                <div class="p-3 bg-indigo-50 rounded-xl mt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-indigo-700">Total Building Permit Fee:</span>
                        <span class="text-2xl font-bold text-indigo-700">₱<span id="total-amount-display">0.00</span></span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Notes</label>
                    <textarea id="assessment-notes" rows="2" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="Add any notes about this assessment..."></textarea>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeAssessmentModal()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                <button onclick="openFinalReviewModal()" id="review-assessment-btn" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Save Assessment & Mark as For Assessment</button>
            </div>
        </div>
    </div>
</div>

<!-- Final Review Modal -->
<div id="final-review-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Final Review of Fees</h3>
                </div>
                <button onclick="closeFinalReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-800">Please review all fee details below before submitting. Once confirmed, the application status will be updated to "For Assessment" and the applicant will be notified.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">Fee Breakdown Summary</h4>
                    </div>
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Line Grade:</span>
                            <span class="font-medium">₱<span id="review-line-grade">0.00</span></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Building Fee:</span>
                            <span class="font-medium">₱<span id="review-building-fee">0.00</span></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Sanitary/Plumbing Fee:</span>
                            <span class="font-medium">₱<span id="review-sanitary-fee">0.00</span></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Mechanical Fee:</span>
                            <span class="font-medium">₱<span id="review-mechanical-fee">0.00</span></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Electrical Fee:</span>
                            <span class="font-medium">₱<span id="review-electrical-fee">0.00</span></span>
                        </div>
                        <div class="flex justify-between py-1 text-sm">
                            <span class="text-gray-600">Penalties/Fines:</span>
                            <span class="font-medium">₱<span id="review-penalties-fines">0.00</span></span>
                        </div>
                        
                        <div id="review-additional-fees-container" class="space-y-1 mt-2 pt-2 border-t border-gray-100"></div>
                        
                        <div class="flex justify-between py-2 mt-2 pt-2 border-t border-gray-200">
                            <span class="font-bold text-gray-900">TOTAL AMOUNT:</span>
                            <span class="text-xl font-bold text-indigo-600">₱<span id="review-total-amount">0.00</span></span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Assessment Notes</p>
                    <p id="review-assessment-notes" class="text-sm text-gray-700">No notes provided</p>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeFinalReviewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Back to Edit</button>
                <button onclick="confirmSaveAssessment()" id="confirm-assessment-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Confirm & Submit Assessment</button>
            </div>
        </div>
    </div>
</div>

<!-- Submitting Modal -->
<div id="submitting-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl text-center">
            <div class="p-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Processing</h3>
                <p id="submitting-message" class="text-sm text-gray-600">Please wait while we process your request...</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl text-center">
            <div class="p-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 id="success-title" class="text-lg font-bold text-gray-900 mb-2">Success!</h3>
                <p id="success-message" class="text-gray-600">Operation completed successfully.</p>
            </div>
            <div class="p-4 border-t border-gray-200">
                <button onclick="closeSuccessModal()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl text-center">
            <div class="p-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 id="error-title" class="text-lg font-bold text-gray-900 mb-2">Error!</h3>
                <p id="error-message" class="text-gray-600">An error occurred. Please try again.</p>
            </div>
            <div class="p-4 border-t border-gray-200">
                <button onclick="closeErrorModal()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Verify Document Modal -->
<div id="verify-doc-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Verify Document</h3>
                </div>
                <button onclick="closeVerifyDocModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-yellow-50 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-sm text-yellow-800">Please confirm that you have reviewed this document and it meets the requirements.</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Document</p>
                    <p id="verify-doc-name" class="text-gray-800 font-medium"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Document Link</p>
                    <a id="verify-doc-link" href="#" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">Open Document</a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verification Notes (Optional)</label>
                    <textarea id="verify-doc-notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Add any notes about this verification..."></textarea>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeVerifyDocModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Cancel</button>
                <button onclick="confirmVerifyDocument()" id="confirm-verify-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Confirm Verification</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div id="archive-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Archive Application</h3>
                </div>
                <button onclick="closeArchiveModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Archive Application</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Are you sure you want to archive this application? Archived applications can be restored later if needed.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Archiving (Optional)</label>
                    <textarea id="archive-reason" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Enter reason for archiving..."></textarea>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeArchiveModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Cancel</button>
                <button onclick="confirmArchiveApplication()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm font-medium">Yes, Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Hard Copy Submission Date Modal -->
<div id="hardcopy-date-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Set Hard Copy Submission Date</h3>
                </div>
                <button onclick="closeHardCopyDateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 rounded-lg p-3 mb-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-800">Please set the date when the applicant can submit their hard copies to the Engineering Office.</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Submission Date <span class="text-red-500">*</span></label>
                    <input type="date" id="hardcopy-submission-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" min="{{ date('Y-m-d') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time (Optional)</label>
                    <input type="time" id="hardcopy-submission-time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Instructions (Optional)</label>
                    <textarea id="hardcopy-instructions" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" placeholder="e.g., Bring valid ID, pay the assessment fee, etc."></textarea>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeHardCopyDateModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                <button onclick="confirmApprovalWithDate()" id="confirm-approval-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Confirm Approval & Send Notification</button>
            </div>
        </div>
    </div>
</div>

<!-- Ownership Document Remark Modal -->
<div id="ownership-remark-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Add Remark / Clarification</h3>
                </div>
                <button onclick="closeOwnershipRemarkModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 rounded-lg p-3 mb-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Document: <span id="remark-doc-name" class="font-bold"></span></p>
                            <p class="text-xs text-blue-700 mt-1">Add a remark or clarification request for this document. The applicant will be notified and can respond with updated documents.</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Clarification Request <span class="text-red-500">*</span></label>
                    <textarea id="ownership-remark-text" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" placeholder="E.g., The TCT document is blurry, please resubmit a clearer copy.&#10;The Tax Declaration date doesn't match the property records, please verify.&#10;SPA document missing notary seal, please provide a properly notarized copy."></textarea>
                    <p class="text-xs text-gray-400 mt-1">This remark will be sent to the applicant and logged in the activity history.</p>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeOwnershipRemarkModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                <button onclick="submitOwnershipRemark()" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-medium">Send Remark to Applicant</button>
            </div>
        </div>
    </div>
</div>

<!-- View Ownership Remarks Modal -->
<div id="view-remarks-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Document Remarks History</h3>
                </div>
                <button onclick="closeViewRemarksModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div id="remarks-history-container" class="space-y-3">
                    <div class="text-center py-4 text-gray-500">
                        <p class="text-sm">No remarks yet for this document.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeViewRemarksModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Building Permit Number Modal -->
<div id="building-permit-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Issue Building Permit Number</h3>
                </div>
                <button onclick="closeBuildingPermitModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="bg-yellow-50 rounded-lg p-3 mb-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium">Building Permit Number Required</p>
                            <p class="text-xs text-yellow-700 mt-1">Please enter a valid 10-digit Building Permit number for this application.</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Building Permit Number <span class="text-red-500">*</span></label>
                    <input type="text" id="building-permit-number" maxlength="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono" placeholder="Enter 10-digit permit number" oninput="validatePermitNumber(this)">
                    <p class="text-xs text-gray-400 mt-1">Must be exactly 10 digits (numbers only)</p>
                    <p id="permit-number-error" class="text-xs text-red-500 mt-1 hidden">Please enter exactly 10 digits (0-9 only)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes (Optional)</label>
                    <textarea id="permit-remarks" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Add any remarks about the permit issuance..."></textarea>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-gray-600">This permit number will be recorded and can be referenced later.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeBuildingPermitModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Cancel</button>
                <button onclick="confirmBuildingPermit()" id="confirm-permit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Confirm & Issue Permit</button>
            </div>
        </div>
    </div>
</div>

<script>
// Set current user info from Laravel
window.KonstructoUser = {
    id: {{ auth()->id() ?? 'null' }},
    name: '{{ auth()->user() ? auth()->user()->first_name . " " . auth()->user()->last_name : "Staff" }}',
    position: '{{ auth()->user() && auth()->user()->profile ? auth()->user()->profile->position : (auth()->user() ? auth()->user()->role : "staff") }}',
    email: '{{ auth()->user() ? auth()->user()->email : "" }}'
};

// Global variables
let applicationId = window.location.pathname.split('/').pop();
let currentApplication = null;
let documentVerificationStatus = {};
let currentAssessment = null;
let currentUserPosition = null;
let dynamicFees = [];
let feeRowCounter = 0;
let pendingApprovalStatus = null;
let currentOwnershipData = null;
let cpdoStatus = null;
let cpdoRemarks = null;
let cpdoApprovedBy = null;
let cpdoApprovedAt = null;
let reviewActivities = [];
let bfpData = null;
let pendingDocumentKey = null;
let pendingDocumentLink = null;
let pendingDocumentName = null;
let pendingCPDODecision = null;
let pendingCPDORemarks = null;
let ownershipRemarks = {};
let cpdoDynamicFees = [];
let cpdoFeeRowCounter = 0;
let existingCPDOAssessment = null;
let currentPaymentProof = null;
window.currentRemarkDocumentKey = null;
window.currentRemarkDocumentName = null;

// Documents list
const documentsList = [
    { key: 'app_letter_link', name: 'Application for Building Permit', category: 'Application Forms' },
    { key: 'bp_forms_link', name: 'Building Permit Forms', category: 'Application Forms' },
    { key: 'arch_plans_link', name: 'Architectural Plans', category: 'Plans' },
    { key: 'structural_plans_link', name: 'Structural Plans', category: 'Plans' },
    { key: 'electrical_plans_link', name: 'Electrical Plans', category: 'Plans' },
    { key: 'plumbing_plans_link', name: 'Plumbing Plans', category: 'Plans' },
    { key: 'mechanical_plans_link', name: 'Mechanical Plans', category: 'Plans' },
    { key: 'fencing_plans_link', name: 'Fencing Plans', category: 'Plans' },
    { key: 'bom_link', name: 'Bill of Materials', category: 'Supporting' },
    { key: 'structural_analysis_link', name: 'Structural Analysis', category: 'Supporting' },
    { key: 'barangay_clearance_link', name: 'Barangay Clearance', category: 'Supporting' },
    { key: 'valid_id_link', name: 'Valid ID', category: 'Supporting' },
    { key: 'cshp_link', name: 'CSHP from DOLE (Optional)', category: 'Supporting' },
    { key: 'ptr_license_link', name: 'PTR License No.', category: 'Supporting' },
    { key: 'zoning_compliance_link', name: 'Zoning Compliance', category: 'Supporting' },
    { key: 'geodetic_plan_link', name: 'Geodetic Plan', category: 'Supporting' }
];

const ownershipVerificationPermissions = {
    'tct_link': ['cpdo'],
    'tax_declaration_link': ['assessor'],
    'current_tax_receipt_link': ['treasurer'],
    'spa_link': ['cpdo', 'assessor', 'treasurer']
};

let ownershipVerificationStatus = {
    tct_link: false,
    tax_declaration_link: false,
    current_tax_receipt_link: false,
    spa_link: false
};

// ========== HELPER FUNCTIONS ==========
function formatCurrency(value) {
    if (value === null || value === undefined) return '₱0.00';
    const num = parseFloat(value);
    if (isNaN(num)) return '₱0.00';
    return '₱' + num.toFixed(2);
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ========== MONITORING ROLE FUNCTIONS ==========
function isMonitoringRole() {
    return currentUserPosition === 'monitoring';
}

// ========== PERMISSION FUNCTIONS ==========
function canVerifyDocuments() {
    if (isMonitoringRole()) return false;
    return ['engineer', 'architect'].includes(currentUserPosition);
}

function canMarkHardCopy() {
    if (isMonitoringRole()) return false;
    return ['engineer', 'architect'].includes(currentUserPosition);
}

function canManageVerification() {
    if (isMonitoringRole()) return false;
    return ['engineer', 'architect'].includes(currentUserPosition);
}

function canVerifyOwnershipDocument(documentKey) {
    if (isMonitoringRole()) return false;
    return (ownershipVerificationPermissions[documentKey] || []).includes(currentUserPosition);
}

function isCPDOUser() {
    if (isMonitoringRole()) return false;
    return currentUserPosition?.toLowerCase() === 'cpdo';
}

function checkStatusPermission(statusValue) {
    const monitoringAllowed = ['under-review', 'document-verification', 'for-assessment', 'approved', 'rejected', 'for-release', 'verified'];
    
    if (isMonitoringRole()) {
        if (monitoringAllowed.includes(statusValue)) return true;
        showErrorModal('Permission Denied', 'Monitoring staff can only update status to allowed statuses.');
        return false;
    }
    
    const restrictedStatuses = ['for-assessment', 'approved', 'rejected', 'for-release', 'verified'];
    if (restrictedStatuses.includes(statusValue) && currentUserPosition !== 'engineer') {
        showErrorModal('Permission Denied', 'Only Engineers can change these statuses.');
        return false;
    }
    
    if (cpdoStatus !== 'approved') {
        showErrorModal('CPDO Approval Required', 'CPDO approval is required before changing application status.');
        return false;
    }
    return true;
}

// ========== MODAL FUNCTIONS ==========
function showSuccessModal(title, message) {
    const successTitle = document.getElementById('success-title');
    const successMessage = document.getElementById('success-message');
    if (successTitle) successTitle.textContent = title;
    if (successMessage) successMessage.textContent = message;
    const modal = document.getElementById('success-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => closeSuccessModal(), 3000);
}

function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showErrorModal(title, message) {
    const errorTitle = document.getElementById('error-title');
    const errorMessage = document.getElementById('error-message');
    if (errorTitle) errorTitle.textContent = title;
    if (errorMessage) errorMessage.textContent = message;
    const modal = document.getElementById('error-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeErrorModal() {
    const modal = document.getElementById('error-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showSubmittingModal(message = 'Please wait...') {
    const submittingMessage = document.getElementById('submitting-message');
    if (submittingMessage) submittingMessage.textContent = message;
    const modal = document.getElementById('submitting-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSubmittingModal() {
    const modal = document.getElementById('submitting-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// ========== MISSING DOCUMENTS FUNCTIONS ==========
function toggleMissingDocumentsDropdown() {
    const dropdown = document.getElementById('missing-documents-dropdown');
    if (!dropdown) {
        console.error('Missing documents dropdown not found');
        return;
    }
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        loadMissingDocumentsList();
        const searchInput = document.getElementById('document-search');
        if (searchInput) searchInput.value = '';
        clearSelectedMissingDocuments();
        const remarksTextarea = document.getElementById('document-request-remarks');
        if (remarksTextarea) remarksTextarea.value = '';
        document.body.style.overflow = 'hidden';
    } else {
        dropdown.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function loadMissingDocumentsList() {
    const container = document.getElementById('missing-docs-list');
    if (!container) return;
    
    let html = '';
    let categories = {};
    
    documentsList.forEach(doc => {
        if (!categories[doc.category]) categories[doc.category] = [];
        categories[doc.category].push(doc);
    });
    
    for (const [category, docs] of Object.entries(categories)) {
        html += `<div class="mb-3">
            <p class="text-xs font-semibold text-gray-500 mb-1">${escapeHtml(category)}</p>`;
        docs.forEach(doc => {
            html += `<label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                <input type="checkbox" class="missing-doc-checkbox mr-3" data-doc-name="${escapeHtml(doc.name)}" data-doc-key="${escapeHtml(doc.key)}">
                <span class="text-sm text-gray-700">${escapeHtml(doc.name)}</span>
            </label>`;
        });
        html += `</div>`;
    }
    
    container.innerHTML = html || '<div class="text-center py-4 text-gray-500">No documents available</div>';
    
    const searchInput = document.getElementById('document-search');
    if (searchInput) {
        searchInput.onkeyup = function() { filterMissingDocuments(); };
    }
}

function filterMissingDocuments() {
    const searchTerm = document.getElementById('document-search')?.value.toLowerCase() || '';
    const checkboxes = document.querySelectorAll('.missing-doc-checkbox');
    
    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('label');
        const text = label?.textContent.toLowerCase() || '';
        if (label) {
            if (searchTerm === '' || text.includes(searchTerm)) {
                label.style.display = 'flex';
            } else {
                label.style.display = 'none';
            }
        }
    });
}

function clearSelectedMissingDocuments() {
    const checkboxes = document.querySelectorAll('.missing-doc-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
}

async function sendDocumentRequest() {
    const selectedCheckboxes = document.querySelectorAll('.missing-doc-checkbox:checked');
    const selectedDocuments = Array.from(selectedCheckboxes).map(cb => ({
        name: cb.getAttribute('data-doc-name'),
        key: cb.getAttribute('data-doc-key')
    }));
    
    if (selectedDocuments.length === 0) {
        showErrorModal('No Documents Selected', 'Please select at least one document to request.');
        return;
    }
    
    const remarksTextarea = document.getElementById('document-request-remarks');
    const remarks = remarksTextarea ? remarksTextarea.value : '';
    
    toggleMissingDocumentsDropdown();
    showSubmittingModal('Sending document request...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/request-missing-documents`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                documents: selectedDocuments.map(d => d.name),
                document_keys: selectedDocuments.map(d => d.key),
                remarks: remarks 
            })
        });
        
        const data = await response.json();
        closeSubmittingModal();
        
        if (response.ok && data.success) {
            showSuccessModal('Request Sent', `Request for ${selectedDocuments.length} document(s) has been sent to the applicant.`);
            refreshActivityLog();
        } else {
            showErrorModal('Request Failed', data.message || 'Failed to send request. Please try again.');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Error sending request:', error);
        showErrorModal('Error', 'Network error. Please check your connection and try again.');
    }
}

// ========== LOCAL STORAGE FUNCTIONS ==========
function loadDocumentVerificationStatus() {
    const saved = localStorage.getItem(`doc_verification_${applicationId}`);
    if (saved) {
        try { documentVerificationStatus = JSON.parse(saved); } catch(e) { documentVerificationStatus = {}; }
    }
}

function saveDocumentVerificationStatus() {
    localStorage.setItem(`doc_verification_${applicationId}`, JSON.stringify(documentVerificationStatus));
    updateVerificationStats();
}

function loadOwnershipVerificationStatus() {
    const saved = localStorage.getItem(`ownership_verification_${applicationId}`);
    if (saved) {
        try { ownershipVerificationStatus = JSON.parse(saved); } catch(e) { ownershipVerificationStatus = { tct_link: false, tax_declaration_link: false, current_tax_receipt_link: false, spa_link: false }; }
    }
}

function saveOwnershipVerificationStatus() {
    localStorage.setItem(`ownership_verification_${applicationId}`, JSON.stringify(ownershipVerificationStatus));
}

function loadOwnershipRemarks() {
    const saved = localStorage.getItem(`ownership_remarks_${applicationId}`);
    if (saved) {
        try { ownershipRemarks = JSON.parse(saved); } catch(e) { ownershipRemarks = {}; }
    }
}

function saveOwnershipRemarks() {
    localStorage.setItem(`ownership_remarks_${applicationId}`, JSON.stringify(ownershipRemarks));
}

// ========== UI UPDATE FUNCTIONS ==========
function updateVerificationStats() {
    let verified = 0;
    documentsList.forEach(doc => { if (documentVerificationStatus[doc.key]?.verified) verified++; });
    
    const elements = {
        'verified-count': verified,
        'total-count': documentsList.length,
        'summary-verified': verified,
        'summary-pending': documentsList.length - verified
    };
    
    Object.entries(elements).forEach(([id, value]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    });
    
    const progressBar = document.getElementById('verification-progress-bar');
    if (progressBar) progressBar.style.width = (verified / documentsList.length) * 100 + '%';
}

function updateStatusUI(status) {
    const config = {
        'pending': 'yellow', 'under-review': 'purple', 'document-verification': 'purple',
        'for-assessment': 'indigo', 'approved': 'green', 'rejected': 'red', 'for-release': 'blue', 'verified': 'emerald'
    };
    const color = config[status] || 'gray';
    const textMap = { 'for-assessment': 'For Assessment', 'document-verification': 'Document Verification', 'under-review': 'Under Review', 'for-release': 'For Release' };
    const text = textMap[status] || status.replace('-', ' ');
    
    const statusBadge = document.getElementById('status-badge');
    if (statusBadge) {
        statusBadge.className = `px-3 py-1 bg-${color}-100 text-${color}-600 rounded-full text-xs font-medium`;
        statusBadge.textContent = text;
    }
    
    const currentStatus = document.getElementById('current-status');
    if (currentStatus) currentStatus.textContent = text;
    
    const currentStatusCard = document.getElementById('current-status-card');
    if (currentStatusCard) currentStatusCard.className = `p-4 bg-${color}-50 rounded-lg border border-${color}-200`;
}

function updateTimeline(status) {
    const steps = ['submitted', 'under-review', 'verification', 'assessment', 'approval', 'release'];
    const stepMap = { 'pending': 0, 'under-review': 1, 'document-verification': 2, 'for-assessment': 3, 'approved': 4, 'for-release': 5, 'verified': 5, 'rejected': -1 };
    const currentIndex = stepMap[status] ?? -1;
    
    steps.forEach((step, index) => {
        const el = document.getElementById(`step-${step}`);
        if (!el) return;
        const circle = el.querySelector('.w-10.h-10');
        const text = el.querySelector('.text-sm');
        
        if (index <= currentIndex) {
            if (circle) {
                circle.className = 'w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2';
                circle.innerHTML = '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            }
            if (text) text.className = 'text-sm font-medium text-gray-800';
            if (index === currentIndex) {
                const dateEl = document.getElementById(`step-${step}-date`);
                if (dateEl) dateEl.textContent = 'In Progress';
            }
        } else {
            if (circle) {
                circle.className = 'w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2';
                circle.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            }
            if (text) text.className = 'text-sm font-medium text-gray-400';
        }
    });
    
    const progressLine = document.getElementById('progress-line');
    if (progressLine) progressLine.style.width = ((currentIndex + 1) / steps.length) * 100 + '%';
}

function updateProgress(status) {
    const progressMap = { 'draft': 0, 'pending': 20, 'under-review': 35, 'document-verification': 50, 'for-assessment': 65, 'approved': 80, 'for-release': 95, 'verified': 100, 'rejected': 100 };
    const progress = progressMap[status] || 0;
    
    const progressPercentage = document.getElementById('progress-percentage');
    if (progressPercentage) progressPercentage.textContent = progress + '%';
    
    const progressBar = document.getElementById('progress-bar');
    if (progressBar) progressBar.style.width = progress + '%';
}

function updateHardCopyStatus(received) {
    const hardcopyNotice = document.getElementById('hardcopy-notice');
    const hardcopyReceivedNotice = document.getElementById('hardcopy-received-notice');
    const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
    
    if (hardcopyNotice) hardcopyNotice.classList.toggle('hidden', received);
    if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.toggle('hidden', !received);
    if (hardcopyCheckbox) hardcopyCheckbox.checked = received;
    
    if (canMarkHardCopy() && received !== undefined) {
        const csrfToken = getCsrfToken();
        fetch(`/staff/applications/${applicationId}/hardcopy-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ hardcopy_received: received })
        }).catch(err => console.error('Error saving hard copy status:', err));
    }
}

function updateCPDOUI() {
    const isCPDO = currentUserPosition === 'cpdo';
    const cpdoForm = document.getElementById('cpdo-form');
    const pendingMessage = document.getElementById('cpdo-pending-message');
    const rejectedMessage = document.getElementById('cpdo-rejected-message');
    const approvedMessage = document.getElementById('cpdo-approved-message');
    const statusUpdateCard = document.getElementById('status-update-card');
    const statusBadge = document.getElementById('cpdo-status-badge');
    const remarksDisplay = document.getElementById('cpdo-remarks-display');
    const remarksText = document.getElementById('cpdo-remarks-text');
    const approvedInfo = document.getElementById('cpdo-approved-info');
    
    if (cpdoStatus === 'approved') {
        if (statusBadge) { statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-700'; statusBadge.textContent = 'Approved'; }
        if (remarksDisplay && cpdoRemarks) { remarksDisplay.classList.remove('hidden'); if (remarksText) remarksText.textContent = cpdoRemarks; }
        if (approvedInfo && cpdoApprovedBy) approvedInfo.classList.remove('hidden');
        if (cpdoForm) cpdoForm.classList.add('hidden');
        if (pendingMessage) pendingMessage.classList.add('hidden');
        if (rejectedMessage) rejectedMessage.classList.add('hidden');
        if (approvedMessage) approvedMessage.classList.remove('hidden');
        if (statusUpdateCard) statusUpdateCard.classList.remove('opacity-50');
    } else if (cpdoStatus === 'rejected') {
        if (statusBadge) { statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-700'; statusBadge.textContent = 'Rejected'; }
        if (remarksDisplay && cpdoRemarks) { remarksDisplay.classList.remove('hidden'); if (remarksText) remarksText.textContent = cpdoRemarks; }
        if (cpdoForm) cpdoForm.classList.add('hidden');
        if (pendingMessage) pendingMessage.classList.add('hidden');
        if (rejectedMessage) rejectedMessage.classList.remove('hidden');
        if (approvedMessage) approvedMessage.classList.add('hidden');
        if (statusUpdateCard) statusUpdateCard.classList.add('opacity-50');
    } else {
        if (statusBadge) { statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700'; statusBadge.textContent = 'Pending'; }
        if (remarksDisplay) remarksDisplay.classList.add('hidden');
        if (approvedInfo) approvedInfo.classList.add('hidden');
        if (rejectedMessage) rejectedMessage.classList.add('hidden');
        if (approvedMessage) approvedMessage.classList.add('hidden');
        if (pendingMessage) pendingMessage.classList.remove('hidden');
        if (isCPDO && cpdoForm) { cpdoForm.classList.remove('hidden'); if (pendingMessage) pendingMessage.classList.add('hidden'); }
        else if (cpdoForm) cpdoForm.classList.add('hidden');
        if (statusUpdateCard && !isCPDO) statusUpdateCard.classList.add('opacity-50');
    }
}

function applyStatusRestrictions() {
    const isEngineer = currentUserPosition === 'engineer';
    const restrictedStatuses = ['for-assessment', 'approved', 'rejected', 'for-release', 'verified'];
    const statusRadios = document.querySelectorAll('.status-radio');
    
    statusRadios.forEach(radio => {
        const statusValue = radio.value;
        const parentLabel = radio.closest('.status-option');
        const restrictedBadge = parentLabel?.querySelector('.status-restricted-badge');
        
        if (restrictedStatuses.includes(statusValue)) {
            if (!isEngineer && !isMonitoringRole()) {
                radio.disabled = true;
                if (parentLabel) parentLabel.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-50');
                if (restrictedBadge) restrictedBadge.classList.remove('hidden');
            } else {
                radio.disabled = false;
                if (parentLabel) parentLabel.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-50');
                if (restrictedBadge) restrictedBadge.classList.add('hidden');
            }
        }
    });
    
    const restrictionNotice = document.getElementById('status-restriction-notice');
    if (restrictionNotice) {
        restrictionNotice.classList.toggle('hidden', isEngineer || isMonitoringRole());
    }
}

function applyHardCopyPermission() {
    const hardCopyCheckbox = document.getElementById('hardcopy-checkbox');
    const warningText = document.getElementById('hardcopy-permission-warning');
    const canMark = canMarkHardCopy();
    
    if (hardCopyCheckbox) hardCopyCheckbox.disabled = !canMark;
    if (warningText) warningText.classList.toggle('hidden', canMark);
}

function applyVerificationUIRestrictions() {
    const adminButtons = document.getElementById('admin-verification-buttons');
    const statsContainer = document.getElementById('verification-stats-container');
    const canManage = canManageVerification();
    
    if (adminButtons) adminButtons.classList.toggle('hidden', !canManage);
    if (statsContainer) statsContainer.classList.toggle('hidden', !canManage);
}

function applyMonitoringRestrictions() {
    if (!isMonitoringRole()) return;
    
    const verificationActions = document.getElementById('verification-actions-container');
    if (verificationActions) verificationActions.classList.add('hidden');
    
    document.querySelectorAll('.ownership-verify-checkbox').forEach(cb => {
        cb.disabled = true;
        cb.closest('label')?.classList.add('opacity-50', 'cursor-not-allowed');
    });
    
    document.querySelectorAll('[onclick*="openOwnershipRemarkModal"]').forEach(btn => {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    });
    
    const missingDocsBtn = document.querySelector('[onclick="toggleMissingDocumentsDropdown()"]');
    if (missingDocsBtn) {
        missingDocsBtn.disabled = true;
        missingDocsBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
    if (hardcopyCheckbox) hardcopyCheckbox.disabled = true;
    
    const archiveBtn = document.getElementById('archive-btn');
    if (archiveBtn) {
        archiveBtn.disabled = true;
        archiveBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    const fsecUploadBtn = document.querySelector('[onclick*="document.getElementById(\'fsec-file\').click()"]');
    if (fsecUploadBtn) fsecUploadBtn.disabled = true;
    
    const bfpCommentsBtn = document.querySelector('[onclick="saveBFPComments()"]');
    if (bfpCommentsBtn) bfpCommentsBtn.disabled = true;
    
    const cpdoForm = document.getElementById('cpdo-form');
    if (cpdoForm) cpdoForm.classList.add('opacity-50');
    document.querySelectorAll('input[name="cpdo_decision"]').forEach(radio => radio.disabled = true);
    const cpdoSubmitBtn = document.getElementById('cpdo-submit-btn');
    if (cpdoSubmitBtn) cpdoSubmitBtn.disabled = true;
    
    const editCPDOBtn = document.getElementById('edit-cpdo-assessment-btn');
    if (editCPDOBtn) editCPDOBtn.style.display = 'none';
    
    const zoningUploadBtn = document.querySelector('#zoning-cert-form button');
    if (zoningUploadBtn) zoningUploadBtn.disabled = true;
    const locationalUploadBtn = document.querySelector('#locational-form button');
    if (locationalUploadBtn) locationalUploadBtn.disabled = true;
    
    const resetBtn = document.querySelector('[onclick="resetDocumentVerification()"]');
    if (resetBtn) resetBtn.disabled = true;
    const saveProgressBtn = document.querySelector('[onclick="saveDocumentVerification()"]');
    if (saveProgressBtn) saveProgressBtn.disabled = true;
    
    const reviewAssessmentBtn = document.getElementById('review-assessment-btn');
    if (reviewAssessmentBtn) reviewAssessmentBtn.disabled = true;
    
    console.log('✅ Monitoring restrictions applied');
}

// ========== DISPLAY FUNCTIONS ==========
function displayApplicationDetails() {
    if (!currentApplication) return;
    
    const appNumber = document.getElementById('application-number');
    if (appNumber) appNumber.textContent = currentApplication.application_number || 'N/A';
    
    const submittedDate = document.getElementById('submitted-date');
    if (submittedDate && currentApplication.created_at) {
        submittedDate.textContent = new Date(currentApplication.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }
    
    const updatedDate = document.getElementById('updated-date');
    if (updatedDate && currentApplication.updated_at) {
        updatedDate.textContent = new Date(currentApplication.updated_at).toLocaleDateString();
    }
    
    updateStatusUI(currentApplication.status);
    
    const applicantName = document.getElementById('applicant-name');
    if (applicantName) applicantName.textContent = currentApplication.applicant_name || 'N/A';
    const applicantEmail = document.getElementById('applicant-email');
    if (applicantEmail) applicantEmail.textContent = currentApplication.email || 'N/A';
    const applicantPhone = document.getElementById('applicant-phone');
    if (applicantPhone) applicantPhone.textContent = currentApplication.phone || 'N/A';
    const applicantAddress = document.getElementById('applicant-address');
    if (applicantAddress) applicantAddress.textContent = currentApplication.address || 'N/A';
    
    document.querySelectorAll('.status-radio').forEach(radio => {
        if (radio.value === currentApplication.status) radio.checked = true;
    });
}

function displayProjectInformation(app) {
    const fields = {
        'project-title': app.project_title || 'Not provided',
        'project-location': app.project_location || 'Not provided',
        'project-description': app.project_description || 'Not provided',
        'project-type-badge': app.project_type || 'Not specified',
        'lot-area': app.lot_area ? `${parseFloat(app.lot_area).toLocaleString()} sqm` : 'Not provided',
        'floor-area': app.floor_area ? `${parseFloat(app.floor_area).toLocaleString()} sqm` : 'Not provided',
        'num-floors': app.num_floors || 'Not provided',
        'estimated-cost': app.estimated_cost ? `₱ ${parseFloat(app.estimated_cost).toLocaleString()}` : 'Not provided',
        'architect-name': app.architect_name || 'Not provided',
        'architect-license': app.architect_license || 'Not provided',
        'engineer-name': app.engineer_name || 'Not provided',
        'engineer-license': app.engineer_license || 'Not provided',
        'electrical-engineer-name': app.electrical_engineer_name || 'Not provided',
        'electrical-engineer-license': app.electrical_engineer_license || 'Not provided',
        'sanitary-engineer-name': app.sanitary_engineer_name || 'Not provided',
        'sanitary-engineer-license': app.sanitary_engineer_license || 'Not provided'
    };
    
    Object.entries(fields).forEach(([id, value]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    });
}

function displayReviewActivities(activities) {
    const container = document.getElementById('activity-log');
    if (!container) return;
    
    if (!activities?.length) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No activity yet</div>';
        return;
    }
    
    let html = '';
    activities.slice(0, 3).forEach(a => {
        const date = new Date(a.created_at);
        const diffMins = Math.floor((new Date() - date) / 60000);
        let timeAgo = diffMins < 1 ? 'just now' : diffMins < 60 ? diffMins + ' min ago' : diffMins < 1440 ? Math.floor(diffMins / 60) + ' hours ago' : Math.floor(diffMins / 1440) + ' days ago';
        
        let iconColor = 'blue', iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
        if (a.action_type === 'document_verified' || a.action_type === 'ownership_document_verified') {
            iconColor = 'green';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
        } else if (a.action_type === 'document_reset' || a.action_type === 'batch_reset_all' || a.action_type === 'ownership_document_unverified') {
            iconColor = 'red';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
        }
        
        html += `<div class="flex gap-3 p-3 border-b border-gray-100 hover:bg-gray-50 transition rounded-lg">
            <div class="w-8 h-8 bg-${iconColor}-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-${iconColor}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <p class="text-sm font-medium text-gray-800">${escapeHtml(a.action_display || a.action)}</p>
                    <span class="text-xs text-gray-400">${timeAgo}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">By: ${escapeHtml(a.reviewer_name || 'System')} ${a.reviewer_position ? `(${a.reviewer_position})` : ''}</p>
                ${a.remarks ? `<p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded">"${escapeHtml(a.remarks.substring(0, 200))}"</p>` : ''}
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function displayOwnershipInfo() {
    if (!currentOwnershipData) return;
    const ownershipCard = document.getElementById('ownership-status-card');
    const ownershipStatusText = document.getElementById('ownership-status-text');
    if (ownershipCard && ownershipStatusText) {
        ownershipCard.classList.remove('hidden');
        ownershipStatusText.innerHTML = currentOwnershipData.is_owner == 1 
            ? '<span class="font-medium text-teal-700">Property Owner</span> - Applicant is registered as the property owner.'
            : '<span class="font-medium text-teal-700">Authorized Representative</span> - Applicant has provided a Special Power of Attorney (SPA).';
    }
}

function displayOwnershipDocuments() {
    const container = document.getElementById('ownership-documents-list');
    if (!currentOwnershipData) {
        displayEmptyOwnershipDocuments();
        return;
    }
    
    let html = '';
    let hasDocuments = false;
    const documentNamesMap = { 
        'tct_link': 'TCT / Deed of Sale', 
        'tax_declaration_link': 'Tax Declaration', 
        'current_tax_receipt_link': 'Current Tax Receipt', 
        'spa_link': 'Special Power of Attorney (SPA)' 
    };
    const ownershipLinks = { 
        'tct_link': currentOwnershipData.tct_link, 
        'tax_declaration_link': currentOwnershipData.tax_declaration_link, 
        'current_tax_receipt_link': currentOwnershipData.current_tax_receipt_link, 
        'spa_link': currentOwnershipData.spa_link 
    };
    
    for (const [key, value] of Object.entries(ownershipLinks)) {
        if (value && value.trim() !== '') {
            hasDocuments = true;
            const docName = documentNamesMap[key];
            const isVerified = ownershipVerificationStatus?.[key] || false;
            const canVerify = canVerifyOwnershipDocument(key);
            const hasRemark = ownershipRemarks[key]?.length > 0;
            
            let verifyInfo = '';
            if (key === 'tct_link') verifyInfo = '<span class="text-xs text-gray-400 ml-2">(CPDO only)</span>';
            else if (key === 'tax_declaration_link') verifyInfo = '<span class="text-xs text-gray-400 ml-2">(Assessor only)</span>';
            else if (key === 'current_tax_receipt_link') verifyInfo = '<span class="text-xs text-gray-400 ml-2">(Treasurer only)</span>';
            else if (key === 'spa_link') verifyInfo = '<span class="text-xs text-gray-400 ml-2">(CPDO/Assessor/Treasurer)</span>';
            
            const spaBadgeHtml = key === 'spa_link' ? '<span class="ml-2 text-xs px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded-full">Authorization</span>' : '';
            
            let remarkPreview = '';
            if (hasRemark) {
                const latestRemark = ownershipRemarks[key][ownershipRemarks[key].length - 1];
                remarkPreview = `<div class="mt-1 text-xs text-amber-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="italic">"${escapeHtml(latestRemark.remark.substring(0, 80))}${latestRemark.remark.length > 80 ? '...' : ''}"</span>
                    <button onclick="viewFullRemarksHistory('${key}', '${escapeHtml(docName)}')" class="text-blue-500 hover:text-blue-700 underline ml-1">View all</button>
                </div>`;
            }
            
            html += `<div class="flex flex-col p-3 ${isVerified ? 'bg-green-50 border border-green-200' : 'bg-teal-50'} rounded-lg hover:bg-teal-100 transition group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 ${isVerified ? 'bg-green-200' : 'bg-teal-200'} rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 ${isVerified ? 'text-green-700' : 'text-teal-700'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center flex-wrap gap-1">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(docName)}</p>
                                ${spaBadgeHtml}${verifyInfo}
                                ${isVerified ? '<span class="ml-2 text-xs px-1.5 py-0.5 bg-green-100 text-green-600 rounded-full">Verified</span>' : ''}
                                ${hasRemark ? '<span class="ml-2 text-xs px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full">Has Remarks</span>' : ''}
                            </div>
                            <p class="text-xs text-gray-500 truncate">${escapeHtml(value.length > 60 ? value.substring(0, 60) + '...' : value)}</p>
                            ${remarkPreview}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        ${canVerify ? `<button onclick="openOwnershipRemarkModal('${key}', '${escapeHtml(docName)}')" class="text-amber-600 hover:text-amber-800 text-sm flex items-center gap-1 px-2 py-1 rounded hover:bg-amber-50 transition" title="Add remark/clarification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-xs">Remark</span>
                        </button>
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" class="ownership-verify-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500" data-doc-key="${key}" onchange="toggleOwnershipVerification('${key}', this.checked)" ${isVerified ? 'checked' : ''}>
                            <span class="text-xs text-gray-600">Verify</span>
                        </label>` : isVerified ? `<div class="flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg><span class="text-xs text-green-600">Verified</span></div>` : ''}
                        <a href="${escapeHtml(value)}" target="_blank" rel="noopener noreferrer" class="text-teal-700 hover:text-teal-900 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            <span class="hidden sm:inline">View</span>
                        </a>
                    </div>
                </div>
            </div>`;
        }
    }
    
    if (!hasDocuments) {
        displayEmptyOwnershipDocuments();
    } else if (container) {
        container.innerHTML = html;
    }
}

function displayEmptyOwnershipDocuments() {
    const container = document.getElementById('ownership-documents-list');
    if (container) {
        container.innerHTML = `<div class="text-center py-6 text-gray-500"><svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg><p class="text-sm">No ownership documents uploaded yet</p><p class="text-xs text-gray-400 mt-1">Applicant has not completed Step 1: Ownership Verification</p></div>`;
    }
}

function showEmptyDocuments() {
    const container = document.getElementById('documents-checklist');
    if (container) container.innerHTML = '<div class="text-center py-8 text-gray-500">No documents uploaded yet</div>';
    updateVerificationStats();
}

function displayDocumentChecklist(documents) {
    const container = document.getElementById('documents-checklist');
    if (!container) return;
    
    let categories = {};
    const canVerify = canVerifyDocuments();
    const cpdoApproved = cpdoStatus === 'approved';
    const canManage = canManageVerification();
    
    documentsList.forEach(doc => {
        if (documents[doc.key] && documents[doc.key].trim() !== '' && documents[doc.key] !== 'undefined') {
            if (!categories[doc.category]) categories[doc.category] = [];
            categories[doc.category].push({ ...doc, link: documents[doc.key], isVerified: documentVerificationStatus[doc.key]?.verified || false });
        }
    });
    
    if (Object.keys(categories).length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">No documents uploaded yet</div>';
        updateVerificationStats();
        return;
    }
    
    let html = '';
    for (const [category, docs] of Object.entries(categories)) {
        html += `<div class="mb-4"><h3 class="text-sm font-semibold mb-2 border-b pb-1">${escapeHtml(category)}</h3><div class="space-y-2">`;
        docs.forEach(doc => {
            const isVerified = doc.isVerified;
            const showVerifyButton = !isVerified && doc.link && cpdoApproved && canVerify;
            const showViewButton = doc.link && doc.link !== 'undefined';
            const showResetButton = isVerified && canManage;
            
            html += `<div data-doc-key="${doc.key}" class="flex justify-between items-center p-2 rounded-lg ${isVerified ? 'bg-green-50' : 'bg-gray-50'}">
                <div class="flex items-center gap-2 flex-1">
                    <span class="text-sm ${isVerified ? 'line-through text-gray-500' : ''}">${escapeHtml(doc.name)}</span>
                    ${isVerified ? '<span class="text-xs text-green-600">✓ Verified</span>' : ''}
                </div>
                <div class="flex gap-2">
                    ${showViewButton ? `<a href="${escapeHtml(doc.link)}" target="_blank" class="px-2 py-1 text-xs rounded bg-[#155386] text-white hover:bg-[#40798C]">View</a>` : '<span class="text-xs text-gray-400">No file</span>'}
                    ${showVerifyButton ? `<button onclick="openVerifyDocModal('${doc.key}', '${escapeHtml(doc.name)}', '${escapeHtml(doc.link)}')" class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700">Verify</button>` : ''}
                    ${showResetButton ? `<button onclick="resetSingleDocumentVerification('${doc.key}', '${escapeHtml(doc.name)}')" class="px-2 py-1 text-xs rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Reset verification"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>Reset</button>` : ''}
                </div>
            </div>`;
        });
        html += `</div></div>`;
    }
    container.innerHTML = html;
    updateVerificationStats();
}

function renderBuildingPermitAssessment() {
    const card = document.getElementById('building-permit-fee-card');
    const displayDiv = document.getElementById('building-assessment-display');
    const noAssessmentDiv = document.getElementById('building-no-assessment-message');
    const statusBadge = document.getElementById('building-fee-status');
    
    if (!card || !currentAssessment) return;
    
    const hasAssessment = (parseFloat(currentAssessment.total_amount) > 0) || (parseFloat(currentAssessment.line_grade) > 0) || (parseFloat(currentAssessment.building_fee) > 0);
    
    card.classList.remove('hidden');
    
    if (hasAssessment) {
        if (displayDiv) displayDiv.classList.remove('hidden');
        if (noAssessmentDiv) noAssessmentDiv.classList.add('hidden');
        if (statusBadge) {
            statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
            statusBadge.textContent = 'Completed';
        }
        
        const fields = ['building-line-grade', 'building-fee', 'sanitary-fee', 'mechanical-fee', 'electrical-fee', 'penalties-fees', 'total-building'];
        const dataFields = ['line_grade', 'building_fee', 'sanitary_fee', 'mechanical_fee', 'electrical_fee', 'penalties_fines', 'total_amount'];
        
        fields.forEach((field, i) => {
            const el = document.getElementById(`display-${field}`);
            if (el) el.textContent = formatCurrency(currentAssessment[dataFields[i]]);
        });
        
        const assessedByName = document.getElementById('display-building-assessed-by');
        if (assessedByName && currentAssessment.assessed_by_name) assessedByName.textContent = currentAssessment.assessed_by_name;
    }
}

function renderCPDOAssessmentCard() {
    const card = document.getElementById('cpdo-assessment-card');
    if (!card) return;
    
    card.classList.remove('hidden');
    
    const isCPDO = isCPDOUser();
    const hasAssessment = currentApplication?.cpdo_assessment?.has_assessment === true;
    
    console.log('renderCPDOAssessmentCard - isCPDO:', isCPDO);
    
    const displayDiv = document.getElementById('cpdo-assessment-display');
    const formDiv = document.getElementById('cpdo-assessment-form');
    const noAssessmentDiv = document.getElementById('cpdo-no-assessment-message');
    const statusBadge = document.getElementById('cpdo-assessment-status');
    const editBtn = document.getElementById('edit-cpdo-assessment-btn');
    
    const certPermissionBadge = document.getElementById('cert-upload-permission-badge');
    if (certPermissionBadge) {
        certPermissionBadge.textContent = isCPDO ? '(CPDO - Can Upload/Edit)' : '(View Only)';
        certPermissionBadge.className = isCPDO ? 'text-xs text-green-600 font-medium' : 'text-xs text-gray-500';
    }
    
    const cpdoAssessment = currentApplication?.cpdo_assessment || {};
    const assessmentExists = cpdoAssessment.assessment_date || cpdoAssessment.total_cpdo_amount > 0;
    
    if (assessmentExists) {
        if (displayDiv) displayDiv.classList.remove('hidden');
        if (formDiv) formDiv.classList.add('hidden');
        if (noAssessmentDiv) noAssessmentDiv.classList.add('hidden');
        if (statusBadge) {
            statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
            statusBadge.textContent = 'Completed';
        }
        
        setElementText('display-assessment-date', cpdoAssessment.assessment_date);
        setElementText('display-zonal-fee', formatCurrency(cpdoAssessment.zonal_location_fee));
        setElementText('display-palc-fee', formatCurrency(cpdoAssessment.palc_fee));
        setElementText('display-dev-fee', formatCurrency(cpdoAssessment.development_permit_fee));
        setElementText('display-alt-fee', formatCurrency(cpdoAssessment.alteration_permit_fee));
        setElementText('display-zoning-fee', formatCurrency(cpdoAssessment.site_zoning_certificate_fee));
        setElementText('display-total-cpdo', formatCurrency(cpdoAssessment.total_cpdo_amount));
        
        if (editBtn) {
            editBtn.classList.toggle('hidden', !isCPDO);
        }
        
    } else if (isCPDO) {
        if (displayDiv) displayDiv.classList.add('hidden');
        if (formDiv) formDiv.classList.remove('hidden');
        if (noAssessmentDiv) noAssessmentDiv.classList.add('hidden');
        if (statusBadge) {
            statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full';
            statusBadge.textContent = 'Not Created';
        }
        
        const dateInput = document.getElementById('cpdo-assessment-date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
        
        const clientNameSpan = document.getElementById('cpdo-client-name');
        const clientAddressSpan = document.getElementById('cpdo-client-address');
        if (clientNameSpan) clientNameSpan.textContent = currentApplication?.applicant_name || 'N/A';
        if (clientAddressSpan) clientAddressSpan.textContent = currentApplication?.address || 'N/A';
        
    } else {
        if (displayDiv) displayDiv.classList.add('hidden');
        if (formDiv) formDiv.classList.add('hidden');
        if (noAssessmentDiv) noAssessmentDiv.classList.remove('hidden');
        if (statusBadge) {
            statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full';
            statusBadge.textContent = 'Not Created';
        }
    }
    
    function setElementText(id, value, defaultValue = 'N/A') {
        const el = document.getElementById(id);
        if (el) el.textContent = value || defaultValue;
    }
}

function renderCertificatesSection() {
    const isCPDO = isCPDOUser();
    
    console.log('renderCertificatesSection - isCPDO:', isCPDO);
    console.log('currentPaymentProof:', currentPaymentProof);
    
    const certPermissionBadge = document.getElementById('cert-upload-permission-badge');
    if (certPermissionBadge) {
        certPermissionBadge.textContent = isCPDO ? '(CPDO - Can Upload/Remove)' : '(View Only)';
        certPermissionBadge.className = isCPDO ? 'text-xs text-green-600 font-medium' : 'text-xs text-gray-500';
    }
    
    const zoningCertLink = currentPaymentProof?.zoning_cert_link;
    const locationalClearanceLink = currentPaymentProof?.locational_clearance_link;
    
    const zoningDisplay = document.getElementById('zoning-cert-display');
    const zoningForm = document.getElementById('zoning-cert-form');
    const zoningStatus = document.getElementById('zoning-cert-status');
    const zoningLink = document.getElementById('zoning-cert-link');
    const zoningRemoveBtn = document.getElementById('zoning-cert-remove-btn');
    
    if (zoningCertLink) {
        if (zoningDisplay) {
            zoningDisplay.classList.remove('hidden');
            const linkText = zoningCertLink.length > 50 ? zoningCertLink.substring(0, 50) + '...' : zoningCertLink;
            if (zoningLink) {
                zoningLink.href = zoningCertLink;
                zoningLink.textContent = linkText;
            }
        }
        if (zoningForm) zoningForm.classList.add('hidden');
        if (zoningStatus) {
            zoningStatus.className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
            zoningStatus.textContent = 'Uploaded';
        }
        if (zoningRemoveBtn) {
            zoningRemoveBtn.classList.toggle('hidden', !isCPDO);
        }
    } else {
        if (zoningDisplay) zoningDisplay.classList.add('hidden');
        if (zoningStatus) {
            zoningStatus.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full';
            zoningStatus.textContent = 'Not Uploaded';
        }
        if (zoningForm) {
            zoningForm.classList.toggle('hidden', !isCPDO);
        }
        if (zoningRemoveBtn) zoningRemoveBtn.classList.add('hidden');
    }
    
    const locationalDisplay = document.getElementById('locational-display');
    const locationalForm = document.getElementById('locational-form');
    const locationalStatus = document.getElementById('locational-status');
    const locationalLink = document.getElementById('locational-link');
    const locationalRemoveBtn = document.getElementById('locational-remove-btn');
    
    if (locationalClearanceLink) {
        if (locationalDisplay) {
            locationalDisplay.classList.remove('hidden');
            const linkText = locationalClearanceLink.length > 50 ? locationalClearanceLink.substring(0, 50) + '...' : locationalClearanceLink;
            if (locationalLink) {
                locationalLink.href = locationalClearanceLink;
                locationalLink.textContent = linkText;
            }
        }
        if (locationalForm) locationalForm.classList.add('hidden');
        if (locationalStatus) {
            locationalStatus.className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
            locationalStatus.textContent = 'Uploaded';
        }
        if (locationalRemoveBtn) {
            locationalRemoveBtn.classList.toggle('hidden', !isCPDO);
        }
    } else {
        if (locationalDisplay) locationalDisplay.classList.add('hidden');
        if (locationalStatus) {
            locationalStatus.className = 'text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full';
            locationalStatus.textContent = 'Not Uploaded';
        }
        if (locationalForm) {
            locationalForm.classList.toggle('hidden', !isCPDO);
        }
        if (locationalRemoveBtn) locationalRemoveBtn.classList.add('hidden');
    }
}

function renderCPDOAssessment() {
    if (!bfpData) return;
    
    if (bfpData.fsec_link) {
        const existingFsecContainer = document.getElementById('existing-fsec-container');
        const fsecLink = document.getElementById('fsec-link');
        const fsecFilename = document.getElementById('fsec-filename');
        const fsecUploadDate = document.getElementById('fsec-upload-date');
        
        if (existingFsecContainer) existingFsecContainer.classList.remove('hidden');
        if (fsecLink) fsecLink.href = bfpData.fsec_link;
        if (fsecFilename && bfpData.fsec_filename) fsecFilename.textContent = bfpData.fsec_filename;
        if (fsecUploadDate && bfpData.fsec_uploaded_at) fsecUploadDate.textContent = 'Uploaded: ' + new Date(bfpData.fsec_uploaded_at).toLocaleDateString();
    }
    
    if (bfpData.bfp_comments) {
        const bfpCommentsDisplay = document.getElementById('bfp-comments-display');
        const bfpCommentsText = document.getElementById('bfp-comments-text');
        const bfpCommentsDate = document.getElementById('bfp-comments-date');
        const bfpCommentsInput = document.getElementById('bfp-comments');
        
        if (bfpCommentsDisplay) bfpCommentsDisplay.classList.remove('hidden');
        if (bfpCommentsText) bfpCommentsText.textContent = bfpData.bfp_comments;
        if (bfpCommentsDate && bfpData.bfp_comments_updated_at) bfpCommentsDate.textContent = 'Last updated: ' + new Date(bfpData.bfp_comments_updated_at).toLocaleString();
        if (bfpCommentsInput) bfpCommentsInput.value = bfpData.bfp_comments;
    }
}

async function loadCPDOAssessment() {
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/cpdo-assessment`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        
        const card = document.getElementById('cpdo-assessment-card');
        if (!card) return;
        card.classList.remove('hidden');
        
        const isCPDO = currentUserPosition === 'cpdo';
        const certPermissionBadge = document.getElementById('cert-upload-permission-badge');
        if (certPermissionBadge) {
            certPermissionBadge.textContent = isCPDO ? '(CPDO - Can Upload)' : '(View Only)';
            certPermissionBadge.className = isCPDO ? 'text-xs text-green-600' : 'text-xs text-gray-500';
        }
        
        const editBtn = document.getElementById('edit-cpdo-assessment-btn');
        if (editBtn) editBtn.classList.toggle('hidden', !isCPDO);
        
        if (data.success && data.data && data.data.assessment_date) {
            existingCPDOAssessment = data.data;
            document.getElementById('cpdo-assessment-display')?.classList.remove('hidden');
            document.getElementById('cpdo-assessment-form')?.classList.add('hidden');
            document.getElementById('cpdo-no-assessment-message')?.classList.add('hidden');
            
            document.getElementById('display-assessment-date').textContent = data.data.assessment_date || 'N/A';
            document.getElementById('display-zonal-fee').textContent = formatCurrency(data.data.zonal_location_fee);
            document.getElementById('display-palc-fee').textContent = formatCurrency(data.data.palc_fee);
            document.getElementById('display-dev-fee').textContent = formatCurrency(data.data.development_permit_fee);
            document.getElementById('display-alt-fee').textContent = formatCurrency(data.data.alteration_permit_fee);
            document.getElementById('display-zoning-fee').textContent = formatCurrency(data.data.site_zoning_certificate_fee);
            document.getElementById('display-total-cpdo').textContent = formatCurrency(data.data.total_cpdo_amount);
            
            const statusBadge = document.getElementById('cpdo-assessment-status');
            if (statusBadge) {
                statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
                statusBadge.textContent = 'Completed';
            }
        } else {
            document.getElementById('cpdo-assessment-display')?.classList.add('hidden');
            document.getElementById('cpdo-assessment-form')?.classList.add('hidden');
            document.getElementById('cpdo-no-assessment-message')?.classList.remove('hidden');
            
            const statusBadge = document.getElementById('cpdo-assessment-status');
            if (statusBadge) {
                statusBadge.className = 'ml-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full';
                statusBadge.textContent = 'Pending';
            }
            
            if (isCPDO) {
                document.getElementById('cpdo-assessment-form')?.classList.remove('hidden');
                document.getElementById('cpdo-no-assessment-message')?.classList.add('hidden');
            }
        }
    } catch(error) {
        console.error('Error loading CPDO assessment:', error);
    }
}

async function loadPaymentProof() {
    if (!applicationId) return;
    
    const loadingDiv = document.getElementById('or-loading');
    const contentDiv = document.getElementById('or-content');
    const emptyDiv = document.getElementById('or-empty-message');
    if (!loadingDiv || !contentDiv || !emptyDiv) return;
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/payment-proof`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data && data.data.or_link) {
                currentPaymentProof = data.data;
                loadingDiv.classList.add('hidden');
                contentDiv.classList.remove('hidden');
                emptyDiv.classList.add('hidden');
                
                const orLinkDisplay = document.getElementById('or-link-display');
                if (orLinkDisplay && currentPaymentProof.or_link) {
                    orLinkDisplay.href = currentPaymentProof.or_link;
                    orLinkDisplay.textContent = currentPaymentProof.or_link.length > 50 ? 
                        currentPaymentProof.or_link.substring(0, 50) + '...' : currentPaymentProof.or_link;
                }
                return;
            }
        }
        loadingDiv.classList.add('hidden');
        contentDiv.classList.add('hidden');
        emptyDiv.classList.remove('hidden');
    } catch (error) {
        console.error('Error loading payment proof:', error);
        loadingDiv.classList.add('hidden');
        emptyDiv.classList.remove('hidden');
    }
}

function getCurrentUserInfo() {
    if (window.KonstructoUser?.id) return window.KonstructoUser;
    
    const headerWelcome = document.querySelector('h1')?.textContent || '';
    let firstName = headerWelcome.includes('Welcome,') ? headerWelcome.replace('Welcome,', '').replace('!', '').trim() : '';
    const headerUserName = document.querySelector('.text-white-500.text-sm')?.textContent || 'Staff';
    
    return {
        id: null,
        name: firstName || 'Staff User',
        position: headerUserName || currentUserPosition || 'staff',
        email: null
    };
}

// ========== API CALL FUNCTIONS ==========
async function processStatusUpdate(status, additionalData = {}) {
    const btn = document.getElementById('update-status-btn');
    const original = btn ? btn.innerHTML : '';
    if (btn) { btn.innerHTML = 'Updating...'; btn.disabled = true; }
    
    const remarks = document.getElementById('status-remarks')?.value || '';
    showSubmittingModal('Updating application status...');
    
    try {
        const csrfToken = getCsrfToken();
        const payload = { 
            status: status, 
            remarks: remarks, 
            hardcopy_received: document.getElementById('hardcopy-checkbox')?.checked || false, 
            ...additionalData 
        };
        
        const response = await fetch(`/staff/applications/${applicationId}/status`, {
            method: 'PUT', 
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        
        closeSubmittingModal();
        
        if (data.success) {
            await fetch(`/staff/fast-load/clear-cache/${applicationId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).catch(err => console.error('Cache clear error:', err));
            
            let successMessage = 'Application status has been updated successfully.';
            if (additionalData.building_permit_number) successMessage = `Building Permit #${additionalData.building_permit_number} has been issued successfully.`;
            showSuccessModal('Status Updated', successMessage);
            
            setTimeout(() => location.reload(), 1500);
        } else { 
            showErrorModal('Update Failed', data.message || 'Failed to update status'); 
        }
    } catch(error) { 
        closeSubmittingModal(); 
        console.error('Error:', error); 
        showErrorModal('Error', 'Error updating status'); 
    } finally { 
        if (btn) { btn.innerHTML = original; btn.disabled = false; }
    }
}

async function updateStatus() {
    const selected = document.querySelector('input[name="status"]:checked');
    if (!selected) { showErrorModal('No Status Selected', 'Please select a status'); return; }
    if (!checkStatusPermission(selected.value)) return;
    
    if (selected.value === 'for-release') { openBuildingPermitModal(); return; }
    if (selected.value === 'approved') { openHardCopyDateModal(); return; }
    if (selected.value === 'for-assessment') { openAssessmentModal(); return; }
    await processStatusUpdate(selected.value);
}

async function confirmApprovalWithDate() {
    const submissionDate = document.getElementById('hardcopy-submission-date')?.value;
    if (!submissionDate) { showErrorModal('Date Required', 'Please select a submission date.'); return; }
    const submissionTime = document.getElementById('hardcopy-submission-time')?.value || '';
    const instructions = document.getElementById('hardcopy-instructions')?.value || '';
    let submissionDateTime = submissionDate;
    if (submissionTime) submissionDateTime = `${submissionDate} ${submissionTime}`;
    closeHardCopyDateModal();
    await processStatusUpdate('approved', { hardcopy_submission_date: submissionDateTime, hardcopy_instructions: instructions });
}

async function confirmBuildingPermit() {
    const permitNumber = document.getElementById('building-permit-number')?.value;
    const remarks = document.getElementById('permit-remarks')?.value || '';
    if (!permitNumber || permitNumber.length !== 10 || !/^\d{10}$/.test(permitNumber)) {
        const errorDiv = document.getElementById('permit-number-error');
        if (errorDiv) {
            errorDiv.classList.remove('hidden');
            errorDiv.textContent = 'Please enter exactly 10 digits (0-9 only)';
        }
        return;
    }
    closeBuildingPermitModal();
    await processStatusUpdate('for-release', { building_permit_number: permitNumber, permit_remarks: remarks });
}

async function saveAssessment() {
    updateDynamicFeesArray();
    const standardTotal = (parseFloat(document.getElementById('line-grade')?.value) || 0) + (parseFloat(document.getElementById('building-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('sanitary-fee')?.value) || 0) + (parseFloat(document.getElementById('mechanical-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('electrical-fee')?.value) || 0) + (parseFloat(document.getElementById('penalties-fines')?.value) || 0);
    const dynamicTotal = getDynamicFeesTotal();
    const total = standardTotal + dynamicTotal;
    const additionalFees = dynamicFees.map(fee => ({ description: fee.description, amount: fee.amount })).filter(fee => fee.description.trim() !== '' || fee.amount > 0);
    
    const data = {
        line_grade: parseFloat(document.getElementById('line-grade')?.value) || null,
        building_fee: parseFloat(document.getElementById('building-fee')?.value) || null,
        sanitary_fee: parseFloat(document.getElementById('sanitary-fee')?.value) || null,
        mechanical_fee: parseFloat(document.getElementById('mechanical-fee')?.value) || null,
        electrical_fee: parseFloat(document.getElementById('electrical-fee')?.value) || null,
        penalties_fines: parseFloat(document.getElementById('penalties-fines')?.value) || null,
        total_amount: total,
        assessment_notes: document.getElementById('assessment-notes')?.value || null,
        additional_fees: additionalFees
    };
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/assessment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            closeAssessmentModal();
            showSuccessModal('Assessment Saved', 'Assessment saved successfully!');
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorModal('Save Failed', result.message || 'Failed to save assessment');
        }
    } catch(error) {
        showErrorModal('Error', 'Failed to save assessment: ' + error.message);
    }
}

async function confirmSaveAssessment() {
    closeFinalReviewModal();
    showSubmittingModal('Saving assessment and updating status...');
    try { await saveAssessment(); } finally { closeSubmittingModal(); }
}

async function toggleOwnershipVerification(documentKey, isChecked) {
    if (!canVerifyOwnershipDocument(documentKey)) {
        showErrorModal('Permission Denied', 'You do not have permission to verify this document.');
        return;
    }
    showSubmittingModal('Updating verification status...');
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/verify-ownership-document`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ document_key: documentKey, verified: isChecked })
        });
        const data = await response.json();
        closeSubmittingModal();
        if (data.success) {
            ownershipVerificationStatus[documentKey] = isChecked;
            saveOwnershipVerificationStatus();
            if (currentOwnershipData) displayOwnershipDocuments();
            showSuccessModal('Verification Updated', data.message);
            refreshActivityLog();
        } else {
            showErrorModal('Update Failed', data.message || 'Failed to update verification');
        }
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Error updating verification');
    }
}

async function confirmVerifyDocument() {
    if (pendingDocumentKey) {
        const notes = document.getElementById('verify-doc-notes')?.value || '';
        const documentName = pendingDocumentName;
        const currentUser = getCurrentUserInfo();
        
        documentVerificationStatus[pendingDocumentKey] = {
            verified: true,
            verified_at: new Date().toISOString(),
            notes: notes,
            verified_by: currentUser.name,
            verified_by_position: currentUser.position,
            verified_by_id: currentUser.id
        };
        saveDocumentVerificationStatus();
        showSubmittingModal('Logging verification...');
        
        const csrfToken = getCsrfToken();
        const logMessage = notes ? `${currentUser.position} verified document: "${documentName}" - Notes: ${notes.substring(0, 200)}` : `${currentUser.position} verified document: "${documentName}"`;
        
        fetch(`/staff/applications/${applicationId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                note: logMessage,
                action_type: 'document_verified',
                metadata: {
                    document_key: pendingDocumentKey,
                    document_name: documentName,
                    verified_by: currentUser.name,
                    verified_by_position: currentUser.position,
                    notes: notes || null
                }
            })
        }).finally(() => {
            closeSubmittingModal();
            if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
            closeVerifyDocModal();
            showSuccessModal('Document Verified', `"${documentName}" has been marked as verified by ${currentUser.position}.`);
            refreshActivityLog();
        });
    }
}

async function resetSingleDocumentVerification(documentKey, documentName) {
    if (!canManageVerification()) {
        showErrorModal('Permission Denied', 'Only Engineers and Architects can reset verification status.');
        return;
    }
    showSubmittingModal('Resetting verification...');
    try {
        const csrfToken = getCsrfToken();
        await fetch(`/staff/applications/${applicationId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: `Reset verification status for document: "${documentName}"`, action_type: 'document_reset' })
        });
        closeSubmittingModal();
        delete documentVerificationStatus[documentKey];
        saveDocumentVerificationStatus();
        if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
        showSuccessModal('Reset Complete', `Verification for "${documentName}" has been reset.`);
        refreshActivityLog();
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Failed to reset verification');
    }
}

async function resetAllVerifiedDocuments() {
    if (!canManageVerification()) {
        showErrorModal('Permission Denied', 'Only Engineers and Architects can reset verification status.');
        return;
    }
    const verifiedKeys = Object.keys(documentVerificationStatus).filter(key => documentVerificationStatus[key]?.verified === true);
    if (verifiedKeys.length === 0) {
        showErrorModal('No Verified Documents', 'There are no verified documents to reset.');
        return;
    }
    showSubmittingModal('Resetting all verified documents...');
    try {
        const csrfToken = getCsrfToken();
        await fetch(`/staff/applications/${applicationId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: `Batch reset ALL ${verifiedKeys.length} verified document(s)`, action_type: 'batch_reset_all' })
        });
        closeSubmittingModal();
        verifiedKeys.forEach(key => { delete documentVerificationStatus[key]; });
        saveDocumentVerificationStatus();
        if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
        showSuccessModal('Reset Complete', `Reset all ${verifiedKeys.length} verified document(s).`);
        refreshActivityLog();
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Failed to reset documents');
    }
}

async function saveDocumentVerification() {
    if (!canManageVerification()) {
        showErrorModal('Permission Denied', 'Only Engineers and Architects can save verification progress.');
        return;
    }
    const verifiedCount = Object.keys(documentVerificationStatus).length;
    showSubmittingModal('Saving verification progress...');
    try {
        const csrfToken = getCsrfToken();
        await fetch(`/staff/applications/${applicationId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: `Verification progress saved: ${verifiedCount}/${documentsList.length} documents verified.` })
        });
        closeSubmittingModal();
        showSuccessModal('Progress Saved', 'Document verification progress saved successfully!');
        refreshActivityLog();
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Save Failed', 'Progress saved locally only');
    }
}

async function refreshActivityLog() {
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/review-activities`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await response.json();
        if (data.success && data.activities) {
            reviewActivities = data.activities;
            displayReviewActivities(reviewActivities);
        }
    } catch(error) {
        console.error('Error refreshing activity log:', error);
    }
}

async function confirmCPDODecision() {
    const decision = pendingCPDODecision;
    const remarks = pendingCPDORemarks;
    if (!decision) {
        showErrorModal('Error', 'No decision was selected. Please try again.');
        return;
    }
    closeCPDOConfirmationModal();
    const btn = document.getElementById('cpdo-submit-btn');
    const originalText = btn?.innerHTML || '';
    if (btn) { btn.innerHTML = 'Submitting...'; btn.disabled = true; }
    showSubmittingModal('Submitting CPDO decision...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/cpdo-decision`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ decision, remarks: remarks || '' })
        });
        const data = await response.json();
        closeSubmittingModal();
        if (data.success) {
            cpdoStatus = decision;
            cpdoRemarks = remarks;
            showSuccessModal('Decision Submitted', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showErrorModal('Submission Failed', data.message || 'Failed to submit decision');
        }
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Error submitting decision');
    } finally {
        if (btn) { btn.innerHTML = originalText; btn.disabled = false; }
        pendingCPDODecision = null;
        pendingCPDORemarks = null;
    }
}

async function submitOwnershipRemark() {
    const remarkText = document.getElementById('ownership-remark-text')?.value.trim();
    const docKey = window.currentRemarkDocumentKey;
    const docName = window.currentRemarkDocumentName;
    if (!remarkText) { showErrorModal('Remark Required', 'Please enter a remark or clarification request.'); return; }
    if (!docKey) { showErrorModal('Error', 'No document selected. Please try again.'); return; }
    closeOwnershipRemarkModal();
    showSubmittingModal('Sending remark to applicant...');
    
    try {
        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('document_key', docKey);
        formData.append('document_name', docName);
        formData.append('remark', remarkText);
        
        const response = await fetch(`/staff/applications/${applicationId}/ownership-remark`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        });
        const data = await response.json();
        closeSubmittingModal();
        if (response.ok && data.success) {
            if (!ownershipRemarks[docKey]) ownershipRemarks[docKey] = [];
            ownershipRemarks[docKey].push({
                document_key: docKey, document_name: docName, remark: remarkText,
                created_by: currentUserPosition || 'Staff', created_at: new Date().toISOString(), status: 'pending_response'
            });
            saveOwnershipRemarks();
            showSuccessModal('Remark Sent', `Your clarification request for "${docName}" has been sent to the applicant.`);
            if (currentOwnershipData) displayOwnershipDocuments();
            refreshActivityLog();
        } else {
            showErrorModal('Failed to Send Remark', data.message || 'Unknown error');
        }
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Failed to send remark: ' + (error.message || 'Please try again.'));
    }
}

function viewFullRemarksHistory(documentKey, documentName) {
    window.currentViewRemarksDocumentKey = documentKey;
    window.currentViewRemarksDocumentName = documentName;
    const container = document.getElementById('remarks-history-container');
    const remarks = ownershipRemarks[documentKey] || [];
    
    if (container) {
        if (remarks.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-gray-500"><p class="text-sm">No remarks yet for this document.</p></div>';
        } else {
            let html = '';
            remarks.forEach((remark) => {
                const date = new Date(remark.created_at);
                const formattedDate = date.toLocaleString();
                const statusBadge = remark.status === 'pending_response' ? '<span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Waiting Response</span>' : '<span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full">Resolved</span>';
                html += `<div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2"><span class="text-xs font-medium text-gray-700">${escapeHtml(remark.created_by_name || remark.created_by)}</span>${statusBadge}</div>
                        <span class="text-xs text-gray-400">${formattedDate}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">${escapeHtml(remark.remark)}</p>
                    ${remark.response ? `<div class="mt-2 pt-2 border-t border-gray-200">
                        <div class="flex items-start gap-2"><svg class="w-4 h-4 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        <div><p class="text-xs font-medium text-green-600">Applicant Response:</p><p class="text-sm text-gray-600">${escapeHtml(remark.response)}</p><p class="text-xs text-gray-400 mt-1">Responded: ${new Date(remark.responded_at).toLocaleString()}</p></div></div></div>` : ''}
                </div>`;
            });
            container.innerHTML = html;
        }
    }
    const modal = document.getElementById('view-remarks-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

async function confirmArchiveApplication() {
    const reason = document.getElementById('archive-reason')?.value || '';
    closeArchiveModal();
    showSubmittingModal('Archiving application...');
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/archive`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ reason })
        });
        closeSubmittingModal();
        if (response.ok) {
            showSuccessModal('Archived', 'Application has been archived successfully.');
            setTimeout(() => window.location.href = '/staff/applications', 1500);
        } else {
            const data = await response.json();
            showErrorModal('Archive Failed', data.message || 'Failed to archive application.');
        }
    } catch(e) {
        closeSubmittingModal();
        showErrorModal('Error', 'Failed to archive application.');
    }
}

// ========== UI HELPER FUNCTIONS ==========
function updateDynamicFeesArray() {
    const rows = document.querySelectorAll('#dynamic-fees-container > div');
    dynamicFees = [];
    rows.forEach(row => {
        const descInput = row.querySelector('.dynamic-fee-desc');
        const amountInput = row.querySelector('.dynamic-fee-amount');
        if (descInput && amountInput) {
            dynamicFees.push({ id: row.id, description: descInput.value, amount: parseFloat(amountInput.value) || 0 });
        }
    });
}

function getDynamicFeesTotal() {
    return dynamicFees.reduce((total, fee) => total + (fee.amount || 0), 0);
}

function calculateTotal() {
    const standardTotal = (parseFloat(document.getElementById('line-grade')?.value) || 0) + (parseFloat(document.getElementById('building-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('sanitary-fee')?.value) || 0) + (parseFloat(document.getElementById('mechanical-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('electrical-fee')?.value) || 0) + (parseFloat(document.getElementById('penalties-fines')?.value) || 0);
    const dynamicTotal = getDynamicFeesTotal();
    const total = standardTotal + dynamicTotal;
    const totalDisplay = document.getElementById('total-amount-display');
    if (totalDisplay) totalDisplay.textContent = total.toFixed(2);
    return total;
}

function addDynamicFee(description = '', amount = 0) {
    const container = document.getElementById('dynamic-fees-container');
    const rowId = `dynamic-fee-${feeRowCounter++}`;
    const rowHtml = `<div id="${rowId}" class="flex gap-2 items-center p-2 bg-gray-50 rounded-lg">
        <input type="text" placeholder="Fee description" class="dynamic-fee-desc flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm" value="${escapeHtml(description)}" onchange="updateDynamicFeesArray()">
        <input type="number" step="0.01" placeholder="Amount" class="dynamic-fee-amount w-32 px-3 py-1.5 border border-gray-300 rounded-lg text-sm" value="${amount}" oninput="updateDynamicFeesArray(); calculateTotal()">
        <button type="button" onclick="removeDynamicFee('${rowId}')" class="text-red-500 hover:text-red-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
    </div>`;
    if (container) container.insertAdjacentHTML('beforeend', rowHtml);
    dynamicFees.push({ id: rowId, description, amount });
    calculateTotal();
}

function removeDynamicFee(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
        dynamicFees = dynamicFees.filter(fee => fee.id !== rowId);
        calculateTotal();
    }
}

function addCPDODynamicFee(description = '', amount = 0) {
    const container = document.getElementById('cpdo-dynamic-fees-container');
    const rowId = `cpdo-dynamic-fee-${cpdoFeeRowCounter++}`;
    const rowHtml = `<div id="${rowId}" class="flex gap-2 items-center p-2 bg-gray-50 rounded-lg">
        <input type="text" placeholder="Fee description" class="cpdo-dynamic-fee-desc flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm" value="${escapeHtml(description)}" onchange="updateCPDODynamicFeesArray()">
        <input type="number" step="0.01" placeholder="Amount" class="cpdo-dynamic-fee-amount w-32 px-3 py-1.5 border border-gray-300 rounded-lg text-sm" value="${amount}" oninput="updateCPDODynamicFeesArray(); calculateCPDOTotal()">
        <button type="button" onclick="removeCPDODynamicFee('${rowId}')" class="text-red-500 hover:text-red-700 p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </button>
    </div>`;
    if (container) container.insertAdjacentHTML('beforeend', rowHtml);
    cpdoDynamicFees.push({ id: rowId, description, amount });
    calculateCPDOTotal();
}

function removeCPDODynamicFee(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
        cpdoDynamicFees = cpdoDynamicFees.filter(fee => fee.id !== rowId);
        calculateCPDOTotal();
    }
}

function updateCPDODynamicFeesArray() {
    const rows = document.querySelectorAll('#cpdo-dynamic-fees-container > div');
    cpdoDynamicFees = [];
    rows.forEach(row => {
        const descInput = row.querySelector('.cpdo-dynamic-fee-desc');
        const amountInput = row.querySelector('.cpdo-dynamic-fee-amount');
        if (descInput && amountInput) {
            cpdoDynamicFees.push({ id: row.id, description: descInput.value, amount: parseFloat(amountInput.value) || 0 });
        }
    });
}

function getCPDODynamicFeesTotal() {
    return cpdoDynamicFees.reduce((total, fee) => total + (fee.amount || 0), 0);
}

function calculateCPDOTotal() {
    const standardTotal = (parseFloat(document.getElementById('cpdo-zonal-fee')?.value) || 0) + (parseFloat(document.getElementById('cpdo-palc-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('cpdo-dev-fee')?.value) || 0) + (parseFloat(document.getElementById('cpdo-alt-fee')?.value) || 0) +
                          (parseFloat(document.getElementById('cpdo-zoning-fee')?.value) || 0);
    const dynamicTotal = getCPDODynamicFeesTotal();
    const total = standardTotal + dynamicTotal;
    const totalDisplay = document.getElementById('cpdo-total-display');
    if (totalDisplay) totalDisplay.textContent = total.toFixed(2);
    return total;
}

async function saveCPDOAssessment() {
    if (!isCPDOUser()) {
        showErrorModal('Permission Denied', 'Only CPDO staff can save the CPDO assessment.');
        return;
    }
    const assessmentDate = document.getElementById('cpdo-assessment-date')?.value;
    if (!assessmentDate) {
        showErrorModal('Missing Date', 'Please select an assessment date.');
        return;
    }
    updateCPDODynamicFeesArray();
    const additionalFees = cpdoDynamicFees.map(fee => ({ description: fee.description, amount: fee.amount })).filter(fee => fee.description.trim() !== '' || fee.amount > 0);
    const total = calculateCPDOTotal();
    const data = {
        assessment_date: assessmentDate,
        zonal_location_fee: parseFloat(document.getElementById('cpdo-zonal-fee')?.value) || null,
        palc_fee: parseFloat(document.getElementById('cpdo-palc-fee')?.value) || null,
        development_permit_fee: parseFloat(document.getElementById('cpdo-dev-fee')?.value) || null,
        alteration_permit_fee: parseFloat(document.getElementById('cpdo-alt-fee')?.value) || null,
        site_zoning_certificate_fee: parseFloat(document.getElementById('cpdo-zoning-fee')?.value) || null,
        total_cpdo_amount: total,
        cpdo_assessment_notes: document.getElementById('cpdo-assessment-notes')?.value || null,
        cpdo_additional_fees: additionalFees
    };
    
    const btn = document.getElementById('save-cpdo-assessment-btn');
    const originalText = btn?.innerHTML || '';
    if (btn) { btn.innerHTML = 'Saving...'; btn.disabled = true; }
    showSubmittingModal('Saving CPDO assessment...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/cpdo-assessment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        closeSubmittingModal();
        if (result.success) {
            showSuccessModal('Assessment Saved', 'CPDO assessment saved successfully!');
            await loadCPDOAssessment();
            cancelCPDOEdit();
        } else {
            showErrorModal('Save Failed', result.message || 'Failed to save assessment');
        }
    } catch(error) {
        closeSubmittingModal();
        showErrorModal('Error', 'Failed to save assessment: ' + error.message);
    } finally {
        if (btn) { btn.innerHTML = originalText; btn.disabled = false; }
    }
}

function editCPDOAssessment() {
    if (!isCPDOUser()) {
        showErrorModal('Permission Denied', 'Only CPDO staff can edit the CPDO assessment.');
        return;
    }
    if (existingCPDOAssessment) {
        document.getElementById('cpdo-assessment-date').value = existingCPDOAssessment.assessment_date || '';
        document.getElementById('cpdo-zonal-fee').value = existingCPDOAssessment.zonal_location_fee || '';
        document.getElementById('cpdo-palc-fee').value = existingCPDOAssessment.palc_fee || '';
        document.getElementById('cpdo-dev-fee').value = existingCPDOAssessment.development_permit_fee || '';
        document.getElementById('cpdo-alt-fee').value = existingCPDOAssessment.alteration_permit_fee || '';
        document.getElementById('cpdo-zoning-fee').value = existingCPDOAssessment.site_zoning_certificate_fee || '';
        document.getElementById('cpdo-assessment-notes').value = existingCPDOAssessment.cpdo_assessment_notes || '';
        
        const container = document.getElementById('cpdo-dynamic-fees-container');
        if (container) container.innerHTML = '';
        cpdoDynamicFees = [];
        cpdoFeeRowCounter = 0;
        if (existingCPDOAssessment.cpdo_additional_fees?.length) {
            existingCPDOAssessment.cpdo_additional_fees.forEach(fee => addCPDODynamicFee(fee.description, fee.amount));
        }
        calculateCPDOTotal();
    }
    document.getElementById('cpdo-assessment-form')?.classList.remove('hidden');
    document.getElementById('cpdo-assessment-display')?.classList.add('hidden');
    document.getElementById('cpdo-no-assessment-message')?.classList.add('hidden');
    document.getElementById('edit-cpdo-assessment-btn')?.classList.add('hidden');
    document.getElementById('cancel-cpdo-edit-btn')?.classList.remove('hidden');
    document.getElementById('cpdo-edit-badge')?.classList.remove('hidden');
}

function cancelCPDOEdit() {
    document.getElementById('cpdo-assessment-form')?.classList.add('hidden');
    document.getElementById('cpdo-assessment-display')?.classList.remove('hidden');
    document.getElementById('edit-cpdo-assessment-btn')?.classList.remove('hidden');
    document.getElementById('cancel-cpdo-edit-btn')?.classList.add('hidden');
    document.getElementById('cpdo-edit-badge')?.classList.add('hidden');
    if (!existingCPDOAssessment) {
        document.getElementById('cpdo-assessment-display')?.classList.add('hidden');
        document.getElementById('cpdo-no-assessment-message')?.classList.remove('hidden');
        document.getElementById('edit-cpdo-assessment-btn')?.classList.add('hidden');
    }
}

async function uploadCertificate(type) {
    if (!isCPDOUser()) {
        showErrorModal('Permission Denied', 'Only CPDO staff can upload certificates.');
        return;
    }
    
    const inputId = type === 'zoning_cert' ? 'zoning-cert-link-input' : 'locational-link-input';
    const button = document.querySelector(type === 'zoning_cert' ? '#zoning-cert-form button' : '#locational-form button');
    const link = document.getElementById(inputId)?.value.trim();
    
    if (!link) {
        showErrorModal('Link Required', 'Please provide a Google Drive link to the certificate.');
        return;
    }
    if (!link.includes('drive.google.com') && !link.includes('docs.google.com')) {
        showErrorModal('Invalid Link', 'Please provide a valid Google Drive link.');
        return;
    }
    
    const originalText = button?.innerHTML || '';
    if (button) { 
        button.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>'; 
        button.disabled = true; 
    }
    showSubmittingModal(`Uploading ${type === 'zoning_cert' ? 'Zoning Certificate' : 'Locational Clearance'}...`);
    
    try {
        const csrfToken = getCsrfToken();
        let paymentProofId = currentPaymentProof?.id;
        
        if (!paymentProofId) {
            const createResponse = await fetch(`/staff/applications/${applicationId}/create-payment-proof`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const createData = await createResponse.json();
            if (createData.success && createData.data) {
                paymentProofId = createData.data.id;
                currentPaymentProof = createData.data;
            } else {
                throw new Error(createData.message || 'Failed to create payment proof record');
            }
        }
        
        const response = await fetch(`/staff/payment-proof/${paymentProofId}/upload-certificate`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ type, link })
        });
        
        const data = await response.json();
        closeSubmittingModal();
        
        if (data.success) {
            if (data.data) {
                currentPaymentProof = data.data;
            } else {
                const refreshResponse = await fetch(`/staff/applications/${applicationId}/payment-proof`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const refreshData = await refreshResponse.json();
                if (refreshData.success && refreshData.data) {
                    currentPaymentProof = refreshData.data;
                }
            }
            
            document.getElementById(inputId).value = '';
            renderCertificatesSection();
            renderCPDOAssessmentCard();
            
            showSuccessModal('Upload Successful', data.message);
        } else {
            showErrorModal('Upload Failed', data.message || 'Failed to upload certificate');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Error uploading certificate:', error);
        showErrorModal('Error', 'Failed to upload certificate: ' + (error.message || 'Please try again.'));
    } finally {
        if (button) { 
            button.innerHTML = originalText; 
            button.disabled = false; 
        }
    }
}

async function removeCertificate(type) {
    if (!isCPDOUser()) {
        showErrorModal('Permission Denied', 'Only CPDO staff can remove certificates.');
        return;
    }
    
    if (!currentPaymentProof) {
        showErrorModal('Error', 'No certificate found to remove');
        return;
    }
    
    const confirmMsg = type === 'zoning_cert' 
        ? 'Are you sure you want to remove the Zoning Certificate? This action cannot be undone.' 
        : 'Are you sure you want to remove the Locational Clearance? This action cannot be undone.';
    
    if (!confirm(confirmMsg)) return;
    
    showSubmittingModal('Removing certificate...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/payment-proof/${currentPaymentProof.id}/remove-certificate`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ type })
        });
        
        const data = await response.json();
        closeSubmittingModal();
        
        if (data.success) {
            const refreshResponse = await fetch(`/staff/applications/${applicationId}/payment-proof`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const refreshData = await refreshResponse.json();
            if (refreshData.success && refreshData.data) {
                currentPaymentProof = refreshData.data;
            } else {
                currentPaymentProof = null;
            }
            
            renderCertificatesSection();
            
            showSuccessModal('Removed', `${type === 'zoning_cert' ? 'Zoning Certificate' : 'Locational Clearance'} has been removed successfully.`);
        } else {
            showErrorModal('Remove Failed', data.message || 'Failed to remove certificate');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Error removing certificate:', error);
        showErrorModal('Error', 'Failed to remove certificate. Please try again.');
    }
}

function openVerifyDocModal(documentKey, documentName, documentLink) {
    if (!canVerifyDocuments()) {
        showErrorModal('Permission Denied', 'Only Engineers and Architects can verify documents.');
        return;
    }
    pendingDocumentKey = documentKey;
    pendingDocumentName = documentName;
    pendingDocumentLink = documentLink;
    document.getElementById('verify-doc-name').textContent = documentName;
    document.getElementById('verify-doc-link').href = documentLink;
    document.getElementById('verify-doc-notes').value = '';
    const modal = document.getElementById('verify-doc-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeVerifyDocModal() {
    const modal = document.getElementById('verify-doc-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    pendingDocumentKey = null;
}

function openHardCopyDateModal() {
    const dateInput = document.getElementById('hardcopy-submission-date');
    if (dateInput) dateInput.value = '';
    const timeInput = document.getElementById('hardcopy-submission-time');
    if (timeInput) timeInput.value = '';
    const instructionsInput = document.getElementById('hardcopy-instructions');
    if (instructionsInput) instructionsInput.value = '';
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    if (dateInput) dateInput.min = tomorrow.toISOString().split('T')[0];
    const modal = document.getElementById('hardcopy-date-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeHardCopyDateModal() {
    const modal = document.getElementById('hardcopy-date-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    pendingApprovalStatus = null;
}

function openBuildingPermitModal() {
    const permitInput = document.getElementById('building-permit-number');
    if (permitInput) permitInput.value = '';
    const remarksInput = document.getElementById('permit-remarks');
    if (remarksInput) remarksInput.value = '';
    const errorDiv = document.getElementById('permit-number-error');
    if (errorDiv) errorDiv.classList.add('hidden');
    const modal = document.getElementById('building-permit-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBuildingPermitModal() {
    const modal = document.getElementById('building-permit-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    pendingApprovalStatus = null;
}

function openAssessmentModal() {
    if (currentAssessment) {
        document.getElementById('line-grade').value = currentAssessment.line_grade || '';
        document.getElementById('building-fee').value = currentAssessment.building_fee || '';
        document.getElementById('sanitary-fee').value = currentAssessment.sanitary_fee || '';
        document.getElementById('mechanical-fee').value = currentAssessment.mechanical_fee || '';
        document.getElementById('electrical-fee').value = currentAssessment.electrical_fee || '';
        document.getElementById('penalties-fines').value = currentAssessment.penalties_fines || '';
        document.getElementById('assessment-notes').value = currentAssessment.assessment_notes || '';
        if (currentAssessment.additional_fees) {
            try { loadDynamicFeesFromData(JSON.parse(currentAssessment.additional_fees)); } catch(e) { loadDynamicFeesFromData([]); }
        } else { loadDynamicFeesFromData([]); }
        calculateTotal();
    } else {
        document.querySelectorAll('#assessment-modal input, #assessment-modal textarea').forEach(el => el.value = '');
        loadDynamicFeesFromData([]);
        document.getElementById('total-amount-display').textContent = '0.00';
    }
    const modal = document.getElementById('assessment-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAssessmentModal() {
    const modal = document.getElementById('assessment-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openFinalReviewModal() {
    const lineGrade = parseFloat(document.getElementById('line-grade')?.value) || 0;
    const buildingFee = parseFloat(document.getElementById('building-fee')?.value) || 0;
    const sanitaryFee = parseFloat(document.getElementById('sanitary-fee')?.value) || 0;
    const mechanicalFee = parseFloat(document.getElementById('mechanical-fee')?.value) || 0;
    const electricalFee = parseFloat(document.getElementById('electrical-fee')?.value) || 0;
    const penaltiesFines = parseFloat(document.getElementById('penalties-fines')?.value) || 0;
    const total = calculateTotal();
    const assessmentNotes = document.getElementById('assessment-notes')?.value || 'No notes provided';
    
    document.getElementById('review-line-grade').textContent = lineGrade.toFixed(2);
    document.getElementById('review-building-fee').textContent = buildingFee.toFixed(2);
    document.getElementById('review-sanitary-fee').textContent = sanitaryFee.toFixed(2);
    document.getElementById('review-mechanical-fee').textContent = mechanicalFee.toFixed(2);
    document.getElementById('review-electrical-fee').textContent = electricalFee.toFixed(2);
    document.getElementById('review-penalties-fines').textContent = penaltiesFines.toFixed(2);
    document.getElementById('review-total-amount').textContent = total.toFixed(2);
    document.getElementById('review-assessment-notes').textContent = assessmentNotes;
    
    updateDynamicFeesArray();
    const container = document.getElementById('review-additional-fees-container');
    if (container) {
        container.innerHTML = '';
        if (dynamicFees.length > 0) {
            dynamicFees.forEach(fee => {
                if (fee.description.trim() || fee.amount > 0) {
                    container.innerHTML += `<div class="flex justify-between py-1 text-sm"><span class="text-gray-600">${escapeHtml(fee.description) || 'Additional Fee'}:</span><span class="font-medium">₱${(fee.amount || 0).toFixed(2)}</span></div>`;
                }
            });
        } else {
            container.innerHTML = '<div class="text-center py-2 text-gray-500 text-sm">No additional fees added</div>';
        }
    }
    const modal = document.getElementById('final-review-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFinalReviewModal() {
    const modal = document.getElementById('final-review-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openCPDOConfirmationModal() {
    const selected = document.querySelector('input[name="cpdo_decision"]:checked');
    if (!selected) {
        showErrorModal('Incomplete Selection', 'Please select Approve or Reject');
        return;
    }
    const decision = selected.value;
    const remarks = document.getElementById('cpdo-remarks')?.value || '';
    if (decision === 'rejected' && !remarks.trim()) {
        showErrorModal('Reason Required', 'Please provide a reason for rejection');
        return;
    }
    pendingCPDODecision = decision;
    pendingCPDORemarks = remarks;
    
    // Update confirmation modal text
    const confirmDecisionText = document.getElementById('confirm-decision-text');
    const confirmRemarksText = document.getElementById('confirm-remarks-text');
    if (confirmDecisionText) confirmDecisionText.textContent = decision === 'approved' ? 'APPROVE' : 'REJECT';
    if (confirmRemarksText) confirmRemarksText.textContent = remarks || 'No remarks provided';
    
    const modal = document.getElementById('cpdo-confirmation-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCPDOConfirmationModal() {
    const modal = document.getElementById('cpdo-confirmation-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    pendingCPDODecision = null;
    pendingCPDORemarks = null;
}

function openOwnershipRemarkModal(documentKey, documentName) {
    window.currentRemarkDocumentKey = documentKey;
    window.currentRemarkDocumentName = documentName;
    const remarkDocName = document.getElementById('remark-doc-name');
    if (remarkDocName) remarkDocName.textContent = documentName;
    const remarkText = document.getElementById('ownership-remark-text');
    if (remarkText) remarkText.value = '';
    const modal = document.getElementById('ownership-remark-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeOwnershipRemarkModal() {
    const modal = document.getElementById('ownership-remark-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    window.currentRemarkDocumentKey = null;
    window.currentRemarkDocumentName = null;
}

function closeViewRemarksModal() {
    const modal = document.getElementById('view-remarks-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    window.currentViewRemarksDocumentKey = null;
    window.currentViewRemarksDocumentName = null;
}

function loadDynamicFeesFromData(feesData) {
    const container = document.getElementById('dynamic-fees-container');
    if (container) container.innerHTML = '';
    dynamicFees = [];
    feeRowCounter = 0;
    if (feesData && feesData.length > 0) feesData.forEach(fee => addDynamicFee(fee.description, fee.amount));
}

function resetDocumentVerification() {
    if (!canManageVerification()) {
        showErrorModal('Permission Denied', 'Only Engineers and Architects can reset verification progress.');
        return;
    }
    if (confirm('⚠️ WARNING: This will reset ALL document verification statuses. This action will be logged. Are you sure?')) {
        resetAllVerifiedDocuments();
    }
}

function openArchiveModal() {
    const archiveReason = document.getElementById('archive-reason');
    if (archiveReason) archiveReason.value = '';
    const modal = document.getElementById('archive-modal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeArchiveModal() {
    const modal = document.getElementById('archive-modal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function validatePermitNumber(input) {
    const value = input.value;
    const errorDiv = document.getElementById('permit-number-error');
    input.value = value.replace(/[^0-9]/g, '');
    if (input.value.length === 10) {
        if (errorDiv) errorDiv.classList.add('hidden');
        return true;
    } else {
        if (errorDiv) errorDiv.classList.remove('hidden');
        return false;
    }
}

function exportAsPDF() {
    window.location.href = `/staff/applications/${applicationId}/export-pdf`;
}

function loadFullActivityHistory() {
    window.location.href = `/staff/applications/${applicationId}/activity-history`;
}

function showError() {
    const loadingState = document.getElementById('loading-state');
    const errorState = document.getElementById('error-state');
    if (loadingState) loadingState.classList.add('hidden');
    if (errorState) errorState.classList.remove('hidden');
}

async function handleFSECUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        showErrorModal('Invalid File Type', 'Please upload PDF, JPG, or PNG files only.');
        event.target.value = '';
        return;
    }
    
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        showErrorModal('File Too Large', 'File size must be less than 10MB.');
        event.target.value = '';
        return;
    }
    
    const formData = new FormData();
    formData.append('fsec_file', file);
    showSubmittingModal('Uploading FSEC document...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/upload-fsec`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });
        const data = await response.json();
        closeSubmittingModal();
        if (data.success) {
            showSuccessModal('Upload Successful', 'FSEC document has been uploaded successfully.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showErrorModal('Upload Failed', data.message || 'Failed to upload FSEC document.');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Upload error:', error);
        showErrorModal('Upload Error', 'An error occurred while uploading.');
    } finally {
        event.target.value = '';
    }
}

async function deleteFSEC() {
    if (!confirm('Are you sure you want to delete the uploaded FSEC file?')) return;
    
    showSubmittingModal('Deleting FSEC document...');
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/delete-fsec`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        closeSubmittingModal();
        if (data.success) {
            showSuccessModal('Deleted', 'FSEC document deleted successfully.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showErrorModal('Delete Failed', data.message || 'Failed to delete FSEC');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Delete error:', error);
        showErrorModal('Error', 'Failed to delete FSEC document.');
    }
}

async function saveBFPComments() {
    const comments = document.getElementById('bfp-comments')?.value || '';
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Saving...';
    btn.disabled = true;
    showSubmittingModal('Saving comments...');
    
    try {
        const csrfToken = getCsrfToken();
        const response = await fetch(`/staff/applications/${applicationId}/bfp-comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ comments })
        });
        const data = await response.json();
        closeSubmittingModal();
        if (data.success) {
            showSuccessModal('Comments Saved', 'Your comments have been saved successfully.');
            refreshActivityLog();
        } else {
            showErrorModal('Save Failed', data.message || 'Failed to save comments');
        }
    } catch(error) {
        closeSubmittingModal();
        console.error('Save comments error:', error);
        showErrorModal('Error', 'Failed to save comments.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// ========== MAIN LOAD FUNCTION ==========
async function loadAllData() {
    const loadingState = document.getElementById('loading-state');
    const contentDiv = document.getElementById('application-content');
    
    try {
        const startTime = performance.now();
        
        const response = await fetch(`/staff/fast-load/application/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
        
        const result = await response.json();
        const loadTime = performance.now() - startTime;
        console.log(`✅ FastLoad completed in ${loadTime.toFixed(0)}ms | From cache: ${result.from_cache}`);
        
        if (result.success && result.data) {
            const data = result.data;
            
            // Set current user position and permissions
            currentUserPosition = data.user_info.position;
            window.KonstructoUser = data.user_info;
            
            // Set application data (includes cpdo_assessment)
            currentApplication = data.application;
            currentAssessment = data.assessment;
            bfpData = data.bfp_data;
            currentOwnershipData = data.ownership_data;
            currentPaymentProof = data.payment_proof;
            reviewActivities = data.recent_activities || [];
            
            // Set CPDO status from application
            cpdoStatus = currentApplication.cpdo_status || 'pending';
            cpdoRemarks = currentApplication.cpdo_remarks;
            cpdoApprovedBy = currentApplication.cpdo_approved_by;
            cpdoApprovedAt = currentApplication.cpdo_approved_at;
            
            // Store CPDO assessment data
            window.cpdoAssessmentData = currentApplication.cpdo_assessment || {};
            
            console.log('CPDO Assessment Data:', window.cpdoAssessmentData);
            console.log('Has CPDO Assessment:', window.cpdoAssessmentData.has_assessment);
            
            // Load local storage data
            loadDocumentVerificationStatus();
            loadOwnershipVerificationStatus();
            loadOwnershipRemarks();
            
            // Render everything
            renderAllData();
            
        } else {
            console.error('Failed to load data:', result);
            showErrorModal('Error', result.message || 'Failed to load application');
        }
        
    } catch (error) {
        console.error('Error loading data:', error);
        showError();
    } finally {
        if (loadingState) loadingState.classList.add('hidden');
        if (contentDiv) contentDiv.classList.remove('hidden');
    }
}

function renderAllData() {
    console.log('========== RENDER ALL DATA DEBUG ==========');
    console.log('currentUserPosition:', currentUserPosition);
    console.log('isCPDOUser() result:', isCPDOUser());
    
    // Hide archive button for non-engineers
    const archiveBtn = document.querySelector('button[onclick="openArchiveModal()"]');
    if (archiveBtn && currentUserPosition !== 'engineer') {
        archiveBtn.style.display = 'none';
    }
    
    loadPaymentProof();
    
    if (currentApplication) {
        displayApplicationDetails();
        updateTimeline(currentApplication.status);
        updateProgress(currentApplication.status);
        updateHardCopyStatus(currentApplication.hard_copy_received);
        if (currentApplication.document_links && Object.keys(currentApplication.document_links).length > 0) {
            displayDocumentChecklist(currentApplication.document_links);
        } else {
            showEmptyDocuments();
        }
        displayProjectInformation(currentApplication);
    }
    
    if (reviewActivities && reviewActivities.length > 0) {
        displayReviewActivities(reviewActivities);
    } else {
        const activityLog = document.getElementById('activity-log');
        if (activityLog) activityLog.innerHTML = '<div class="text-center py-8 text-gray-500">No activity yet</div>';
    }
    
    if (currentOwnershipData && Object.keys(currentOwnershipData).length > 0) {
        displayOwnershipInfo();
        displayOwnershipDocuments();
    } else {
        displayEmptyOwnershipDocuments();
    }
    
    // Show building permit assessment
    renderBuildingPermitAssessment();
    
    // Show CPDO assessment card
    renderCPDOAssessmentCard();
    
    // Show certificates upload section
    renderCertificatesSection();
    
    updateCPDOUI();
    applyStatusRestrictions();
    applyHardCopyPermission();
    applyVerificationUIRestrictions();
    applyMonitoringRestrictions();
    
    if (currentUserPosition && currentUserPosition.toUpperCase() === 'BFP') {
        const bfpSection = document.getElementById('bfp-section');
        if (bfpSection) bfpSection.classList.remove('hidden');
    }
}

// ========== EVENT LISTENERS ==========
document.addEventListener('DOMContentLoaded', function() {
    if (applicationId && !isNaN(applicationId)) {
        loadAllData();
    } else {
        showError();
    }
    
    const fsecFile = document.getElementById('fsec-file');
    if (fsecFile) fsecFile.addEventListener('change', handleFSECUpload);
    
    const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
    if (hardcopyCheckbox) {
        hardcopyCheckbox.addEventListener('change', function(e) {
            if (!canMarkHardCopy()) {
                e.preventDefault();
                showErrorModal('Permission Denied', 'Only Engineers and Architects can mark hard copy as received.');
                this.checked = !this.checked;
                return;
            }
            updateHardCopyStatus(this.checked);
        });
    }
    
    const cpdoRadios = document.querySelectorAll('input[name="cpdo_decision"]');
    cpdoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const remarksRequiredStar = document.getElementById('remarks-required-star');
            const cpdoRemarks = document.getElementById('cpdo-remarks');
            if (this.value === 'rejected') {
                if (remarksRequiredStar) remarksRequiredStar.classList.remove('hidden');
                if (cpdoRemarks) cpdoRemarks.required = true;
                if (cpdoRemarks) cpdoRemarks.placeholder = 'Please provide a reason for rejection...';
            } else {
                if (remarksRequiredStar) remarksRequiredStar.classList.add('hidden');
                if (cpdoRemarks) cpdoRemarks.required = false;
                if (cpdoRemarks) cpdoRemarks.placeholder = 'Enter remarks (optional)...';
            }
        });
    });
});
</script>

<style>
/* CPDO Modal specific styles */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.rating-star:hover svg {
    transform: scale(1.1);
}

.animate-modal-slide-up {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.rotate-180 { transform: rotate(180deg); }
.animate-spin { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-down { animation: slideDown 0.3s ease-out; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.step-processing .w-10 { animation: stepGlow 2s ease-in-out infinite; }
@keyframes stepGlow { 0%, 100% { box-shadow: 0 0 5px rgba(21,83,134,0.3); } 50% { box-shadow: 0 0 20px rgba(64,121,140,0.6); transform: scale(1.05); } }

#assessment-modal .bg-white, 
#hardcopy-date-modal .bg-white, 
#final-review-modal .bg-white, 
#success-modal .bg-white, 
#error-modal .bg-white, 
#verify-doc-modal .bg-white, 
#archive-modal .bg-white, 
#submitting-modal .bg-white, 
#cpdo-confirmation-modal .bg-white,
#missing-documents-dropdown .bg-white { 
    animation: modalSlideIn 0.3s ease-out; 
}

@keyframes modalSlideIn { 
    from { transform: translateY(-20px); opacity: 0; } 
    to { transform: translateY(0); opacity: 1; } 
}

.hidden { display: none; }
.status-option.disabled, .status-radio:disabled { cursor: not-allowed; opacity: 0.5; }
.status-restricted-badge { font-size: 10px; color: #9ca3af; }
.opacity-50 { opacity: 0.5; }
</style>
@endsection