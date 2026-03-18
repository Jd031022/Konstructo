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
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <button onclick="exportAsPDF()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </button>
            <button onclick="archiveApplication()" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
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
        <a href="/staff/applications" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
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
                    <h4 class="font-semibold text-gray-800">Hard Copy Pending</h4>
                    <p class="text-sm text-gray-700 mt-1">Physical documents have not been received yet. Check the "Hard Copy Received" box when submitted.</p>
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
                        
                        <!-- Application Meta Information -->
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
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Queue Position:</span>
                    <span id="queue-position" class="text-sm font-semibold text-[#155386]">-</span>
                </div>
            </div>
            
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
                    <div id="progress-bar" class="absolute inset-0 bg-gradient-to-r from-[#155386] to-[#40798C] h-full rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
                    <div class="absolute inset-0 h-full w-full overflow-hidden">
                        <div id="animated-loading-overlay" class="h-full w-full loading-progress-animation"></div>
                    </div>
                </div>
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
                    @php
                        $steps = [
                            'submitted' => 'Submitted',
                            'under-review' => 'Under Review',
                            'verification' => 'Document Verification',
                            'approval' => 'Approval',
                            'release' => 'For Release'
                        ];
                    @endphp
                    
                    @foreach($steps as $key => $label)
                    <div id="step-{{ $key }}" class="text-center step-item">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500">
                            <svg class="h-5 w-5 text-gray-400 step-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($loop->first)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                @elseif($key === 'under-review')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                @elseif($key === 'verification')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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

            <!-- Estimated Completion -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Estimated Review Completion:</span>
                    <span id="estimated-time" class="font-semibold text-[#155386]">Calculating...</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-1">
                    <span class="text-gray-600">Target Release Date:</span>
                    <span id="target-release" class="font-semibold text-[#155386]">-</span>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column - Applicant Details -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Applicant Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Applicant Information</h2>
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

               <!-- Google Drive Documents Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Google Drive Documents</h2>
        <span id="drive-status" class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Working</span>
    </div>
    
    <!-- Google Drive Link Section -->
    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">Google Drive Folder</p>
                <div class="flex items-center gap-2 mt-1">
                    <a href="#" id="drive-link" class="text-[#155386] hover:underline text-sm flex items-center gap-1" target="_blank">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Folder
                    </a>
                    <span class="text-gray-300">|</span>
                    <button onclick="copyDriveLink()" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Copy Link
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Last accessed: <span id="last-accessed">Today</span></p>
            </div>
        </div>
    </div>

    <!-- Document Actions -->
    <div class="flex justify-end gap-3">
        <div class="relative">
            <button onclick="toggleMissingDocumentsDropdown()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Request Missing Documents
                <svg class="w-4 h-4 ml-2 transition-transform" id="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- Missing Documents Dropdown -->
            <div id="missing-documents-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Select Missing Documents</h3>
                        <button onclick="toggleMissingDocumentsDropdown()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="mb-3">
                        <input type="text" id="document-search" placeholder="Search documents..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent" onkeyup="filterDocuments()">
                    </div>
                    
                    <!-- Document Categories -->
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <!-- Application Forms -->
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Application Forms</p>
                            <div class="space-y-2">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Application for Building Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Sign Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Application for Architectural Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Mechanical Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Application for Electrical Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Electronics Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Application for Sanitary/Plumbing Permit</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Demolition Permit Form</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Application for Civil/Structural</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Fencing Permit</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Plans and Specifications -->
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Plans and Specifications</p>
                            <div class="space-y-2">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Architectural Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Civil/Structural Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Electrical Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Sanitary/Plumbing Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Mechanical Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Fencing Plans and Specifications (5 sets)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Bill of Materials (5 copies)</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Supporting Documents -->
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Supporting Documents</p>
                            <div class="space-y-2">
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Proof of Ownership (2 copies)</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Structural Design Analysis</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Locational/Zoning Clearance</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Barangay Clearance</span>
                                </label>
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" class="document-checkbox h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                    <span class="ml-3 text-sm text-gray-700">Certificate of Construction Safety and Health Program (CSHP) from DOLE (for Contractors w/ PCAB)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remarks Field -->
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                        <textarea id="document-request-remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent" placeholder="Add any additional instructions or notes for the applicant..."></textarea>
                    </div>
                    
                    <!-- Dropdown Actions -->
                    <div class="mt-4 pt-3 border-t border-gray-200 flex justify-end gap-2">
                        <button onclick="clearSelectedDocuments()" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800">
                            Clear All
                        </button>
                        <button onclick="sendDocumentRequest()" class="px-4 py-1.5 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                            Send Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <button onclick="showVerifyModal()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
            Verify Documents
        </button>
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
                                Access the Google Drive folder to review documents. Update status as you review. Check the "Hard Copy Received" box when physical documents are submitted.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Status & Actions -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    <!-- Status Update Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
                        
                        <div class="space-y-4">
                            <!-- Current Status -->
                            <div id="current-status-card" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs text-gray-500 mb-1">Current Status</p>
                                <p id="current-status" class="text-lg font-semibold text-yellow-600">Pending Review</p>
                            </div>

                            <!-- Hard Copy Status Checkbox -->
                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Hard Copy Received</span>
                                    <input type="checkbox" id="hardcopy-checkbox" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]" onchange="updateHardCopyStatus(this.checked)">
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Check this box when physical documents are received</p>
                            </div>

                            <!-- Queue Information -->
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Queue Position:</span>
                                    <span id="sidebar-queue" class="text-sm font-semibold text-[#155386]">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Ahead in Queue:</span>
                                    <span id="queue-ahead" class="text-sm font-semibold text-gray-700">-</span>
                                </div>
                            </div>

                            <!-- Status Options -->
                            <div class="space-y-2">
                                @php
                                    $statusOptions = [
                                        'under-review' => ['Under Review', 'purple'],
                                        'document-verification' => ['Document Verification', 'purple'],
                                        'approved' => ['Approved', 'green'],
                                        'rejected' => ['Rejected', 'red'],
                                        'for-release' => ['For Release', 'blue'],
                                        'verified' => ['Completed', 'emerald']
                                    ];
                                @endphp
                                
                                @foreach($statusOptions as $value => [$label, $color])
                                <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                    <input type="radio" name="status" value="{{ $value }}" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                    <span class="ml-3 text-sm font-medium text-{{ $color }}-600">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes</label>
                                <textarea id="status-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add remarks or notes about this application..."></textarea>
                            </div>

                            <!-- Update Button -->
                            <button onclick="updateStatus()" class="w-full px-4 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">
                                Update Status
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
                        
                        <a href="/staff/applications/{{ $applicationId }}/activity-history" 
                           class="mt-4 text-sm text-[#155386] hover:text-[#40798C] font-medium w-full text-center inline-block py-2 border-t border-gray-100 hover:bg-gray-50 transition rounded-b-lg">
                            View Full History →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Documents Confirmation Modal -->
<div id="verify-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Verify Documents</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to mark these documents as verified? 
                    This will update the application status to <strong class="text-purple-600">Document Verification</strong> 
                    and notify the applicant.
                </p>
                
                <!-- Document Verification Checklist Summary -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg text-left">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Documents to verify:</p>
                    <div id="verification-document-list" class="max-h-32 overflow-y-auto text-sm text-gray-600">
                        Loading documents...
                    </div>
                </div>
                
                <!-- Optional remarks field -->
                <div class="mb-6 text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
                    <textarea id="verify-remarks" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add any notes about document verification..."></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button onclick="closeVerifyModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Cancel
                    </button>
                    <button onclick="confirmVerify()" id="confirm-verify-btn" class="flex-1 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                        Confirm Verification
                    </button>
                </div>
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
    let selectedDocuments = [];
    let updateCheckInterval = null;
    let requiredDocuments = [
        'Application for Building Permit',
        'Architectural Plans and Specifications (5 sets)',
        'Civil/Structural Plans and Specifications (5 sets)',
        'Electrical Plans and Specifications (5 sets)',
        'Sanitary/Plumbing Plans and Specifications (5 sets)',
        'Proof of Ownership (2 copies)',
        'Locational/Zoning Clearance',
        'Barangay Clearance'
    ];

    // Load application details on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Application ID from URL:', applicationId);
        if (applicationId && applicationId !== 'application-details' && !isNaN(applicationId)) {
            loadApplicationDetails();
            loadReviewActivities();
            loadQueuePosition();
            calculateEstimatedTime();
            // Start checking for updates every 15 seconds
            startRealTimeUpdates();
        } else {
            showError();
        }
        setupModals();
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('missing-documents-dropdown');
            const button = event.target.closest('button');
            
            if (dropdown && !dropdown.contains(event.target) && !button?.onclick?.toString().includes('toggleMissingDocumentsDropdown')) {
                dropdown.classList.add('hidden');
                document.getElementById('dropdown-arrow')?.classList.remove('rotate-180');
            }
        });
    });

    // Start real-time updates
    function startRealTimeUpdates() {
        // Check for updates every 15 seconds
        updateCheckInterval = setInterval(checkForUpdates, 15000);
    }

    // Check for updates
    async function checkForUpdates() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}`, {
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
                        
                        // Save timestamp for the new status
                        saveStatusTimestamp(newApplication.status);
                    }
                    
                    // Check if hard copy status changed
                    if (currentApplication && currentApplication.hard_copy_received !== newApplication.hard_copy_received) {
                        updateHardCopyStatus(newApplication.hard_copy_received);
                    }
                    
                    // Update the application data
                    currentApplication = newApplication;
                    displayApplicationDetails();
                    
                    // Reload activities if needed
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

    // Save timestamp for status changes
    function saveStatusTimestamp(status) {
        const timestampField = {
            'under-review': 'under_review_at',
            'document-verification': 'verification_at',
            'approved': 'approved_at',
            'for-release': 'release_at'
        }[status];
        
        if (timestampField && currentApplication) {
            currentApplication[timestampField] = new Date().toISOString();
            updateTimeline(currentApplication.status);
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

    // Animate status change
    function animateStatusChange() {
        const statusBadge = document.getElementById('status-badge');
        const currentStatusBadge = document.getElementById('current-status');
        const progressBar = document.getElementById('progress-bar');
        const progressLine = document.getElementById('progress-line');
        
        statusBadge.classList.add('animate-pulse');
        currentStatusBadge.classList.add('animate-pulse');
        progressBar.classList.add('scale-animation');
        progressLine.classList.add('scale-animation');
        
        setTimeout(() => {
            statusBadge.classList.remove('animate-pulse');
            currentStatusBadge.classList.remove('animate-pulse');
            progressBar.classList.remove('scale-animation');
            progressLine.classList.remove('scale-animation');
        }, 1000);
    }

    // Load queue position
    async function loadQueuePosition() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}/queue-position`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    document.getElementById('queue-position').textContent = data.position || '-';
                    document.getElementById('sidebar-queue').textContent = data.position || '-';
                    document.getElementById('queue-ahead').textContent = data.ahead || '0';
                }
            }
        } catch (error) {
            console.error('Error loading queue position:', error);
        }
    }

    // Calculate estimated completion time
    function calculateEstimatedTime() {
        const averageProcessingDays = {
            'pending': 3,
            'under-review': 5,
            'document-verification': 2,
            'approved': 1,
            'for-release': 1,
            'rejected': 0,
            'verified': 0
        };
        
        if (!currentApplication) return;
        
        const startDate = new Date(currentApplication.created_at);
        const status = currentApplication.status;
        const estimatedDays = averageProcessingDays[status] || 5;
        
        const estimatedDate = new Date(startDate);
        estimatedDate.setDate(startDate.getDate() + estimatedDays);
        
        document.getElementById('estimated-time').textContent = estimatedDate.toLocaleDateString('en-US', { 
            month: 'long', day: 'numeric', year: 'numeric' 
        });
        
        // Calculate target release (add 7 days for release processing)
        const releaseDate = new Date(estimatedDate);
        releaseDate.setDate(estimatedDate.getDate() + 7);
        
        document.getElementById('target-release').textContent = releaseDate.toLocaleDateString('en-US', { 
            month: 'long', day: 'numeric', year: 'numeric' 
        });
    }

    // Load application details from API
    async function loadApplicationDetails() {
        try {
            console.log('Fetching application details for ID:', applicationId);
            
            const response = await fetch(`/staff/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            console.log('Application data:', data);
            
            if (data.success) {
                currentApplication = data.data;
                previousStatus = currentApplication.status;
                displayApplicationDetails();
                updateTimeline(currentApplication.status);
                updateProgress(currentApplication.status);
                updateHardCopyStatus(currentApplication.hard_copy_received);
                calculateEstimatedTime();
            } else {
                showError();
            }
        } catch (error) {
            console.error('Error loading application:', error);
            showError();
        }
    }

    // Load document checklist
    function loadDocumentChecklist() {
        const checklist = document.getElementById('document-checklist');
        
        // In a real implementation, this would come from the API
        // For now, we'll simulate with sample data
        let html = '';
        requiredDocuments.forEach((doc, index) => {
            const verified = Math.random() > 0.5; // Simulate verification status
            html += `
                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-100">
                    <span class="text-sm text-gray-700">${doc}</span>
                    <span class="text-xs px-2 py-1 ${verified ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'} rounded-full">
                        ${verified ? 'Verified ✓' : 'Pending'}
                    </span>
                </div>
            `;
        });
        
        checklist.innerHTML = html;
    }

    // Load review activities
    async function loadReviewActivities() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}/review-activities`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities) {
                    displayReviewActivities(data.activities);
                } else {
                    showEmptyActivities();
                }
            } else {
                showEmptyActivities();
            }
        } catch (error) {
            console.error('Error loading review activities:', error);
            showEmptyActivities();
        }
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
            const timeAgo = getTimeAgo(date);
            
            // Determine icon based on action
            let iconColor = 'bg-blue-100';
            let iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            
            if (activity.action === 'status_updated') {
                if (activity.new_status === 'approved') {
                    iconColor = 'bg-green-100';
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />`;
                } else if (activity.new_status === 'rejected') {
                    iconColor = 'bg-red-100';
                    iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
                }
            } else if (activity.action === 'hard_copy_received') {
                iconColor = 'bg-indigo-100';
                iconSvg = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />`;
            }
            
            const reviewerName = activity.reviewer ? activity.reviewer.name : 'System';
            
            html += `
                <div class="flex gap-3 p-2 hover:bg-gray-50 rounded-lg transition animate-fade-in">
                    <div class="w-8 h-8 ${iconColor} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">${activity.action_display || activity.action}</p>
                        <p class="text-xs text-gray-500 mt-1">by ${reviewerName}</p>
                        <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                    </div>
                </div>
            `;
        });
        
        activityLog.innerHTML = html;
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

    // Display application details
    function displayApplicationDetails() {
        // Hide loading, show content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('application-content').classList.remove('hidden');

        // Update application number and dates
        document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
        
        if (currentApplication.created_at) {
            const submittedDate = new Date(currentApplication.created_at);
            document.getElementById('submitted-date').textContent = submittedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            document.getElementById('step-submitted-date').textContent = submittedDate.toLocaleDateString('en-US', { 
                month: 'short', day: 'numeric' 
            });
        }
        
        if (currentApplication.updated_at) {
            const updatedDate = new Date(currentApplication.updated_at);
            document.getElementById('updated-date').textContent = updatedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
        }

        // Update status badge
        updateStatusUI(currentApplication.status);

        // Update applicant information
        document.getElementById('applicant-name').textContent = currentApplication.applicant_name || 'N/A';
        document.getElementById('applicant-email').textContent = currentApplication.email || 'N/A';
        document.getElementById('applicant-phone').textContent = currentApplication.phone || 'N/A';
        document.getElementById('applicant-address').textContent = currentApplication.address || 'N/A';

        // Update Google Drive link
        if (currentApplication.google_drive_link) {
            const driveLink = document.getElementById('drive-link');
            driveLink.href = currentApplication.google_drive_link;
            driveLink.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg> View Folder';
            driveLink.classList.remove('pointer-events-none', 'text-gray-500');
            driveLink.classList.add('text-[#155386]');
            driveLink.setAttribute('target', '_blank');
            
            document.getElementById('drive-status').textContent = 'Active';
            document.getElementById('drive-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
        } else {
            const driveLink = document.getElementById('drive-link');
            driveLink.innerHTML = '<span class="text-gray-500">No link provided</span>';
            driveLink.href = '#';
            driveLink.classList.add('pointer-events-none', 'text-gray-500');
            driveLink.classList.remove('text-[#155386]');
            driveLink.removeAttribute('target');
            
            document.getElementById('drive-status').textContent = 'No Link';
            document.getElementById('drive-status').className = 'text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full';
        }

        // Set current status in radio buttons
        const radios = document.querySelectorAll('.status-radio');
        radios.forEach(radio => {
            if (radio.value === currentApplication.status) {
                radio.checked = true;
            }
        });
    }

    // Update status UI
    function updateStatusUI(status) {
        const statusBadge = document.getElementById('status-badge');
        const currentStatus = document.getElementById('current-status');
        const statusCard = document.getElementById('current-status-card');
        
        const statusConfig = {
            'pending': { color: 'yellow', text: 'Pending Review' },
            'under-review': { color: 'purple', text: 'Under Review' },
            'document-verification': { color: 'purple', text: 'Document Verification' },
            'approved': { color: 'green', text: 'Approved' },
            'rejected': { color: 'red', text: 'Rejected' },
            'for-release': { color: 'blue', text: 'For Release' },
            'verified': { color: 'emerald', text: 'Completed' }
        };

        const config = statusConfig[status] || { color: 'gray', text: status || 'Unknown' };
        
        statusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium transition-all duration-500`;
        statusBadge.textContent = config.text;
        
        if (currentStatus) {
            currentStatus.textContent = config.text;
        }
        
        if (statusCard) {
            statusCard.className = `p-4 bg-${config.color}-50 rounded-lg border border-${config.color}-200 transition-all duration-500`;
        }
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
                // Completed step
                if (circle) {
                    circle.className = 'w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10 transition-all duration-500';
                    circle.innerHTML = '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                }
                if (text) text.className = 'text-sm font-medium text-gray-800 transition-all duration-500';
                
                if (index === currentStepIndex && currentApplication) {
                    // Current step - show "In Progress"
                    dateElement.textContent = 'In Progress';
                    dateElement.className = 'text-xs text-[#155386] font-medium animate-pulse';
                    stepElement.classList.add('step-processing');
                } else {
                    stepElement.classList.remove('step-processing');
                    
                    // Show actual date if available
                    const dateField = {
                        0: 'created_at',
                        1: 'under_review_at',
                        2: 'verification_at',
                        3: 'approved_at',
                        4: 'release_at'
                    }[index];
                    
                    if (currentApplication && currentApplication[dateField]) {
                        const date = new Date(currentApplication[dateField]);
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
                dateElement.textContent = '';
                dateElement.className = 'text-xs text-gray-400';
                stepElement.classList.remove('step-processing');
            }
        });

        // Update progress line width
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
        const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
        
        if (received) {
            if (hardcopyNotice) hardcopyNotice.classList.add('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.remove('hidden');
            if (hardcopyCheckbox) hardcopyCheckbox.checked = true;
        } else {
            if (hardcopyNotice) hardcopyNotice.classList.remove('hidden');
            if (hardcopyReceivedNotice) hardcopyReceivedNotice.classList.add('hidden');
            if (hardcopyCheckbox) hardcopyCheckbox.checked = false;
        }
    }

    // Update status
    async function updateStatus() {
        const selectedRadio = document.querySelector('input[name="status"]:checked');
        if (!selectedRadio) {
            showErrorModal('Please select a status');
            return;
        }

        const status = selectedRadio.value;
        const remarks = document.getElementById('status-remarks').value;
        const hardcopyReceived = document.getElementById('hardcopy-checkbox').checked;

        // Show loading state on button
        const updateBtn = event.target;
        const originalText = updateBtn.innerHTML;
        updateBtn.innerHTML = 'Updating...';
        updateBtn.disabled = true;

        try {
            const response = await fetch(`/staff/applications/${applicationId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    status, 
                    remarks,
                    hardcopy_received: hardcopyReceived 
                })
            });

            const data = await response.json();

            if (data.success) {
                showSuccessModal('Status updated successfully');
                updateStatusUI(status);
                updateTimeline(status);
                updateProgress(status);
                updateHardCopyStatus(hardcopyReceived);
                
                // Save timestamp
                saveStatusTimestamp(status);
                
                // Clear remarks field
                document.getElementById('status-remarks').value = '';
                
                // Reload activities
                loadReviewActivities();
            } else {
                showErrorModal(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showErrorModal('Failed to update status. Please try again.');
        } finally {
            // Restore button
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }
    }

    // Toggle missing documents dropdown
    function toggleMissingDocumentsDropdown() {
        const dropdown = document.getElementById('missing-documents-dropdown');
        const arrow = document.getElementById('dropdown-arrow');
        
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Filter documents by search
    function filterDocuments() {
        const searchTerm = document.getElementById('document-search').value.toLowerCase();
        const labels = document.querySelectorAll('#missing-documents-dropdown .space-y-2 label');
        
        labels.forEach(label => {
            const text = label.querySelector('span').textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                label.style.display = 'flex';
            } else {
                label.style.display = 'none';
            }
        });
        
        // Hide empty categories
        const categories = document.querySelectorAll('#missing-documents-dropdown .space-y-3 > div');
        categories.forEach(category => {
            const visibleItems = category.querySelectorAll('label[style="display: flex;"]').length;
            if (visibleItems === 0) {
                category.style.display = 'none';
            } else {
                category.style.display = 'block';
            }
        });
    }

    // Clear all selected documents
    function clearSelectedDocuments() {
        const checkboxes = document.querySelectorAll('.document-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectedDocuments = [];
    }

    // Send document request
    async function sendDocumentRequest() {
        selectedDocuments = [];
        const checkboxes = document.querySelectorAll('.document-checkbox:checked');
        
        checkboxes.forEach(checkbox => {
            const label = checkbox.closest('label');
            const documentName = label.querySelector('span').textContent;
            selectedDocuments.push(documentName);
        });
        
        if (selectedDocuments.length === 0) {
            showErrorModal('Please select at least one document to request');
            return;
        }
        
        const remarks = document.getElementById('document-request-remarks')?.value || '';
        
        const requestBtn = event.target;
        const originalText = requestBtn.innerHTML;
        requestBtn.innerHTML = 'Sending...';
        requestBtn.disabled = true;
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/request-missing-documents`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    documents: selectedDocuments,
                    remarks: remarks
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal(`Request sent for ${selectedDocuments.length} document(s).`);
                clearSelectedDocuments();
                toggleMissingDocumentsDropdown();
                document.getElementById('document-request-remarks').value = '';
            } else {
                showErrorModal(data.message || 'Failed to send request');
            }
        } catch (error) {
            console.error('Error sending document request:', error);
            showErrorModal('Failed to send request. Please try again.');
        } finally {
            requestBtn.innerHTML = originalText;
            requestBtn.disabled = false;
        }
    }

    // Show verify modal
    function showVerifyModal() {
        // Load documents for verification list
        const documentList = document.getElementById('verification-document-list');
        let docHtml = '<ul class="list-disc list-inside text-sm">';
        requiredDocuments.forEach(doc => {
            docHtml += `<li>${doc}</li>`;
        });
        docHtml += '</ul>';
        documentList.innerHTML = docHtml;
        
        document.getElementById('verify-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close verify modal
    function closeVerifyModal() {
        document.getElementById('verify-modal').classList.add('hidden');
        document.getElementById('verify-remarks').value = '';
        document.body.style.overflow = 'auto';
    }

    // Confirm verify
    async function confirmVerify() {
        const verifyBtn = document.getElementById('confirm-verify-btn');
        const originalText = verifyBtn.innerHTML;
        verifyBtn.innerHTML = 'Verifying...';
        verifyBtn.disabled = true;
        
        try {
            const status = 'document-verification';
            const remarks = document.getElementById('verify-remarks').value || 'Documents verified by staff';
            const hardcopyReceived = document.getElementById('hardcopy-checkbox').checked;
            
            const response = await fetch(`/staff/applications/${applicationId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    status: status,
                    remarks: remarks,
                    hardcopy_received: hardcopyReceived
                })
            });

            const data = await response.json();

            if (data.success) {
                closeVerifyModal();
                showSuccessModal('Documents verification started. Status updated to Document Verification.');
                updateStatusUI(status);
                updateTimeline(status);
                updateProgress(status);
                saveStatusTimestamp(status);
                
                // Update radio button
                const radios = document.querySelectorAll('.status-radio');
                radios.forEach(radio => {
                    if (radio.value === status) {
                        radio.checked = true;
                    }
                });
            } else {
                showErrorModal(data.message || 'Failed to verify documents');
            }
        } catch (error) {
            console.error('Error verifying documents:', error);
            showErrorModal('Failed to verify documents. Please try again.');
        } finally {
            verifyBtn.innerHTML = originalText;
            verifyBtn.disabled = false;
        }
    }

    // Format status for display
    function formatStatus(status) {
        if (!status) return '';
        return status.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    // Get time ago string
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

    // Export as PDF
    function exportAsPDF() {
        window.location.href = `/staff/applications/${applicationId}/export-pdf`;
    }

    // Archive functions
    function archiveApplication() {
        document.getElementById('archive-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeArchiveModal() {
        document.getElementById('archive-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    async function confirmArchive() {
        const btn = document.getElementById('confirm-archive-btn');
        btn.innerHTML = 'Archiving...';
        btn.disabled = true;

        try {
            const response = await fetch(`/staff/applications/${applicationId}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showSuccessModal('Application archived successfully');
                setTimeout(() => {
                    window.location.href = '/staff/applications';
                }, 1500);
            } else {
                showErrorModal(data.message || 'Failed to archive application');
            }
        } catch (error) {
            console.error('Error archiving application:', error);
            showErrorModal('Failed to archive application');
        } finally {
            btn.innerHTML = 'Archive';
            btn.disabled = false;
            closeArchiveModal();
        }
    }

    // Show error state
    function showError() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
    }

    // Modal functions
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

    function showErrorModal(message) {
        document.getElementById('error-modal-message').textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Setup modals
    function setupModals() {
        const archiveModal = document.getElementById('archive-modal');
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        const verifyModal = document.getElementById('verify-modal');
        
        if (archiveModal) {
            archiveModal.addEventListener('click', function(e) {
                if (e.target === archiveModal) closeArchiveModal();
            });
        }
        
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
        
        if (verifyModal) {
            verifyModal.addEventListener('click', function(e) {
                if (e.target === verifyModal) closeVerifyModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeArchiveModal();
                closeErrorModal();
                closeSuccessModal();
                closeVerifyModal();
            }
        });
    }

    // Clean up interval when leaving the page
    window.addEventListener('beforeunload', function() {
        if (updateCheckInterval) {
            clearInterval(updateCheckInterval);
        }
    });
</script>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }

    /* Modal animations */
    #archive-modal, #error-modal, #success-modal, #missing-documents-dropdown, #verify-modal {
        transition: opacity 0.2s ease-in-out;
    }

    #archive-modal .bg-white, #error-modal .bg-white, #success-modal .bg-white, #missing-documents-dropdown .bg-white, #verify-modal .bg-white {
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

    /* Spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
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

    /* Scale animation */
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

    /* Bounce animation */
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

    /* Pulse animation */
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

    /* Step item hover effect */
    .step-item {
        transition: transform 0.3s ease;
    }

    .step-item:hover {
        transform: translateY(-2px);
    }

    /* Disable pointer events */
    .pointer-events-none {
        pointer-events: none;
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
</style>
@endsection