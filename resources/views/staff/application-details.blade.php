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

        <!-- BFP FSEC Section - Only visible to BFP staff -->
        <div id="bfp-section" class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-r-lg hidden animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">Fire Safety Evaluation Clearance (FSEC)</h4>
                    <p class="text-sm text-gray-700 mt-1">Upload the Fire Safety Evaluation Clearance for this building permit application.</p>
                    
                    <!-- FSEC Upload Section -->
                    <div class="mt-4 space-y-4">
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
                            
                            <!-- Existing FSEC Display -->
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
                        
                        <!-- BFP Additional Comments -->
                        <div class="mt-4">
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
                    </div>
                </div>

                <!-- Document Checklist Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Document Checklist</h2>
                        <div class="flex items-center gap-2">
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

                    <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap justify-between gap-3">
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
                            <p class="text-sm text-gray-600">Click "View" to review each document. Check the checkbox to mark as verified. When ready to proceed with assessment, select "For Assessment" status to compute fees.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-8">
                    <!-- Status Update Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
                        
                        <div class="space-y-4">
                            <div id="current-status-card" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs text-gray-500 mb-1">Current Status</p>
                                <p id="current-status" class="text-lg font-semibold text-yellow-600">Pending Review</p>
                            </div>

                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Hard Copy Received</span>
                                    <input type="checkbox" id="hardcopy-checkbox" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]" onchange="updateHardCopyStatus(this.checked)">
                                </label>
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
                                        'under-review' => ['Under Review', 'purple'],
                                        'document-verification' => ['Document Verification', 'purple'],
                                        'for-assessment' => ['For Assessment', 'indigo'],
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

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes</label>
                                <textarea id="status-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add remarks or notes about this application..."></textarea>
                            </div>

                            <button onclick="updateStatus()" class="w-full px-4 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">Update Status</button>
                        </div>
                    </div>

                    <!-- Activity Log Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Activity Log</h2>
                            <span class="text-xs text-gray-400">Last 5 activities</span>
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

<!-- Assessment Fee Modal -->
<div id="assessment-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
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
            
            <div class="p-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Line Grade (₱)</label><input type="number" id="line-grade" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Building Fee (₱)</label><input type="number" id="building-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Sanitary/Plumbing Fee (₱)</label><input type="number" id="sanitary-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Mechanical Fee (₱)</label><input type="number" id="mechanical-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Electrical Fee (₱)</label><input type="number" id="electrical-fee" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Others (₱)</label><input type="number" id="others-amount" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                </div>
                
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Others Description</label><input type="text" id="others-description" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="Specify what the 'Others' fee is for"></div>
                
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Penalties/Fines (₱)</label><input type="number" id="penalties-fines" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="0.00" oninput="calculateTotal()"></div>
                
                <div class="p-3 bg-indigo-50 rounded-xl">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-indigo-700">Total Building Permit Fee:</span>
                        <span class="text-2xl font-bold text-indigo-700">₱<span id="total-amount-display">0.00</span></span>
                    </div>
                </div>
                
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Assessment Notes</label><textarea id="assessment-notes" rows="2" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="Add any notes about this assessment..."></textarea></div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closeAssessmentModal()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                <button onclick="saveAssessment()" id="save-assessment-btn" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Save Assessment & Mark as For Assessment</button>
            </div>
        </div>
    </div>
</div>

<!-- Missing Documents Dropdown -->
<div id="missing-documents-dropdown" class="hidden fixed left-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 z-50" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="p-4">
        <div class="flex justify-between mb-3">
            <h3 class="font-semibold">Select Missing Documents</h3>
            <button onclick="toggleMissingDocumentsDropdown()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <input type="text" id="document-search" placeholder="Search..." class="w-full px-3 py-2 border rounded-lg text-sm mb-3" onkeyup="filterMissingDocuments()">
        <div id="missing-docs-list" class="space-y-2 max-h-64 overflow-y-auto"></div>
        <textarea id="document-request-remarks" rows="2" class="w-full mt-3 px-3 py-2 border rounded-lg text-sm" placeholder="Remarks (Optional)"></textarea>
        <div class="mt-3 flex justify-end gap-2">
            <button onclick="clearSelectedMissingDocuments()" class="px-3 py-1 text-sm text-gray-600">Clear</button>
            <button onclick="sendDocumentRequest()" class="px-4 py-1 bg-[#155386] text-white rounded-lg text-sm">Send Request</button>
        </div>
    </div>
</div>

<script>
    let applicationId = window.location.pathname.split('/').pop();
    let currentApplication = null;
    let documentVerificationStatus = {};
    let currentAssessment = null;
    let currentUserPosition = null;

    const documentsList = [
        { key: 'app_letter_link', name: 'Application for Building Permit', category: 'Application Forms' },
        { key: 'bp_forms_link', name: 'Building Permit Forms', category: 'Application Forms' },
        { key: 'arch_plans_link', name: 'Architectural Plans', category: 'Plans' },
        { key: 'structural_plans_link', name: 'Structural Plans', category: 'Plans' },
        { key: 'electrical_plans_link', name: 'Electrical Plans', category: 'Plans' },
        { key: 'plumbing_plans_link', name: 'Plumbing Plans', category: 'Plans' },
        { key: 'mechanical_plans_link', name: 'Mechanical Plans', category: 'Plans' },
        { key: 'fencing_plans_link', name: 'Fencing Plans', category: 'Plans' },
        { key: 'ownership_link', name: 'Proof of Ownership', category: 'Supporting' },
        { key: 'bom_link', name: 'Bill of Materials', category: 'Supporting' },
        { key: 'structural_analysis_link', name: 'Structural Analysis', category: 'Supporting' },
        { key: 'barangay_clearance_link', name: 'Barangay Clearance', category: 'Supporting' },
        { key: 'valid_id_link', name: 'Valid ID', category: 'Supporting' }
    ];

    document.addEventListener('DOMContentLoaded', function() {
        // Get current user position
        fetchCurrentUserPosition();
        
        if (applicationId && !isNaN(applicationId)) {
            loadApplicationDetails();
            loadReviewActivities();
            loadDocumentVerificationStatus();
            loadAssessmentData();
            loadBFPData();
        } else {
            showError();
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('missing-documents-dropdown');
            if (dropdown && !dropdown.contains(event.target) && !event.target.closest('button')?.innerHTML?.includes('Request Missing')) {
                dropdown.classList.add('hidden');
            }
        });
        
        // FSEC file input change handler
        document.getElementById('fsec-file').addEventListener('change', handleFSECUpload);
    });
    
    async function fetchCurrentUserPosition() {
        try {
            // First try to get from the position/check endpoint which returns position
            const response = await fetch('/staff/position/check');
            const data = await response.json();
            console.log('Position check response:', data);
            
            // The response might have a 'position' field or we need to get from user profile
            if (data.position) {
                currentUserPosition = data.position;
            } else if (data.needs_position === false) {
                // If needs_position is false, try to get from a different endpoint
                const userResponse = await fetch('/api/user/position');
                if (userResponse.ok) {
                    const userData = await userResponse.json();
                    currentUserPosition = userData.position;
                }
            }
            
            // Also check if we can get from the profile endpoint
            if (!currentUserPosition) {
                const profileResponse = await fetch('/profile/avatar-info');
                if (profileResponse.ok) {
                    const profileData = await profileResponse.json();
                    if (profileData.user && profileData.user.position) {
                        currentUserPosition = profileData.user.position;
                    }
                }
            }
            
            console.log('Current user position:', currentUserPosition);
            
            // Show BFP section if position is BFP (case insensitive)
            if (currentUserPosition && currentUserPosition.toUpperCase() === 'BFP') {
                document.getElementById('bfp-section').classList.remove('hidden');
                console.log('BFP section shown');
            } else {
                console.log('BFP section hidden. Position:', currentUserPosition);
            }
        } catch (error) {
            console.error('Error fetching user position:', error);
        }
    }

    async function loadBFPData() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}/bfp-data`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    // Display existing FSEC
                    if (data.data.fsec_link) {
                        document.getElementById('existing-fsec-container').classList.remove('hidden');
                        document.getElementById('fsec-link').href = data.data.fsec_link;
                        if (data.data.fsec_filename) {
                            document.getElementById('fsec-filename').textContent = data.data.fsec_filename;
                        }
                        if (data.data.fsec_uploaded_at) {
                            document.getElementById('fsec-upload-date').textContent = 'Uploaded: ' + new Date(data.data.fsec_uploaded_at).toLocaleDateString();
                        }
                    }
                    // Display existing BFP comments
                    if (data.data.bfp_comments) {
                        document.getElementById('bfp-comments-display').classList.remove('hidden');
                        document.getElementById('bfp-comments-text').textContent = data.data.bfp_comments;
                        if (data.data.bfp_comments_updated_at) {
                            document.getElementById('bfp-comments-date').textContent = 'Last updated: ' + new Date(data.data.bfp_comments_updated_at).toLocaleString();
                        }
                        document.getElementById('bfp-comments').value = data.data.bfp_comments;
                    }
                }
            }
        } catch (error) {
            console.error('Error loading BFP data:', error);
        }
    }

    async function handleFSECUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please upload PDF, JPG, or PNG files only.');
            event.target.value = '';
            return;
        }
        
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('File size must be less than 10MB.');
            event.target.value = '';
            return;
        }
        
        const formData = new FormData();
        formData.append('fsec_file', file);
        
        const statusDiv = document.getElementById('fsec-upload-status');
        statusDiv.classList.remove('hidden');
        statusDiv.innerHTML = '<span class="text-blue-600">Uploading...</span>';
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/upload-fsec`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                statusDiv.innerHTML = '<span class="text-green-600">✓ FSEC uploaded successfully!</span>';
                document.getElementById('fsec-filename').textContent = file.name;
                document.getElementById('existing-fsec-container').classList.remove('hidden');
                document.getElementById('fsec-link').href = data.link;
                document.getElementById('fsec-upload-date').textContent = 'Uploaded: ' + new Date().toLocaleDateString();
                setTimeout(() => statusDiv.innerHTML = '', 3000);
            } else {
                statusDiv.innerHTML = '<span class="text-red-600">✗ ' + (data.message || 'Upload failed') + '</span>';
            }
        } catch (error) {
            console.error('Error uploading FSEC:', error);
            statusDiv.innerHTML = '<span class="text-red-600">✗ Upload failed. Please try again.</span>';
        } finally {
            event.target.value = '';
            setTimeout(() => {
                if (statusDiv.innerHTML) statusDiv.innerHTML = '';
            }, 5000);
        }
    }
    
    async function deleteFSEC() {
        if (!confirm('Are you sure you want to delete the uploaded FSEC file?')) return;
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/delete-fsec`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('existing-fsec-container').classList.add('hidden');
                document.getElementById('fsec-filename').textContent = 'No file selected';
                alert('FSEC deleted successfully');
            } else {
                alert(data.message || 'Failed to delete FSEC');
            }
        } catch (error) {
            console.error('Error deleting FSEC:', error);
            alert('Failed to delete FSEC');
        }
    }
    
    async function saveBFPComments() {
        const comments = document.getElementById('bfp-comments').value;
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Saving...';
        btn.disabled = true;
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/bfp-comments`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ comments: comments })
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('bfp-comments-display').classList.remove('hidden');
                document.getElementById('bfp-comments-text').textContent = comments;
                document.getElementById('bfp-comments-date').textContent = 'Last updated: ' + new Date().toLocaleString();
                alert('Comments saved successfully');
            } else {
                alert(data.message || 'Failed to save comments');
            }
        } catch (error) {
            console.error('Error saving comments:', error);
            alert('Failed to save comments');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async function loadAssessmentData() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}/assessment`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentAssessment = data.data;
                    document.getElementById('assessment-notice')?.classList.remove('hidden');
                    document.getElementById('assessment-total').innerHTML = `Total Building Permit Fee: ₱${parseFloat(currentAssessment.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                }
            }
        } catch (error) { console.error('Error loading assessment:', error); }
    }

    // MODIFIED: Removed document verification requirement for assessment
    function openAssessmentModal() {
        // No longer checking if all documents are verified
        // Staff can proceed to assessment regardless of document verification status
        
        if (currentAssessment) {
            document.getElementById('line-grade').value = currentAssessment.line_grade || '';
            document.getElementById('building-fee').value = currentAssessment.building_fee || '';
            document.getElementById('sanitary-fee').value = currentAssessment.sanitary_fee || '';
            document.getElementById('mechanical-fee').value = currentAssessment.mechanical_fee || '';
            document.getElementById('electrical-fee').value = currentAssessment.electrical_fee || '';
            document.getElementById('others-amount').value = currentAssessment.others_amount || '';
            document.getElementById('others-description').value = currentAssessment.others_description || '';
            document.getElementById('penalties-fines').value = currentAssessment.penalties_fines || '';
            document.getElementById('assessment-notes').value = currentAssessment.assessment_notes || '';
            calculateTotal();
        } else {
            document.querySelectorAll('#assessment-modal input, #assessment-modal textarea').forEach(el => el.value = '');
            document.getElementById('total-amount-display').textContent = '0.00';
        }
        document.getElementById('assessment-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAssessmentModal() {
        document.getElementById('assessment-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function calculateTotal() {
        const total = (parseFloat(document.getElementById('line-grade').value) || 0) +
                      (parseFloat(document.getElementById('building-fee').value) || 0) +
                      (parseFloat(document.getElementById('sanitary-fee').value) || 0) +
                      (parseFloat(document.getElementById('mechanical-fee').value) || 0) +
                      (parseFloat(document.getElementById('electrical-fee').value) || 0) +
                      (parseFloat(document.getElementById('others-amount').value) || 0) +
                      (parseFloat(document.getElementById('penalties-fines').value) || 0);
        document.getElementById('total-amount-display').textContent = total.toFixed(2);
        return total;
    }

    async function saveAssessment() {
        const btn = document.getElementById('save-assessment-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Saving...';
        btn.disabled = true;
        
        const total = calculateTotal();
        const data = {
            line_grade: parseFloat(document.getElementById('line-grade').value) || null,
            building_fee: parseFloat(document.getElementById('building-fee').value) || null,
            sanitary_fee: parseFloat(document.getElementById('sanitary-fee').value) || null,
            mechanical_fee: parseFloat(document.getElementById('mechanical-fee').value) || null,
            electrical_fee: parseFloat(document.getElementById('electrical-fee').value) || null,
            others_amount: parseFloat(document.getElementById('others-amount').value) || null,
            others_description: document.getElementById('others-description').value || null,
            penalties_fines: parseFloat(document.getElementById('penalties-fines').value) || null,
            total_amount: total,
            assessment_notes: document.getElementById('assessment-notes').value || null
        };
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/assessment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                closeAssessmentModal();
                alert('Assessment saved successfully! Application status updated to "For Assessment".');
                location.reload();
            } else {
                alert(result.message || 'Failed to save assessment');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save assessment: ' + error.message);
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async function loadApplicationDetails() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await response.json();
            if (data.success) {
                currentApplication = data.data;
                displayApplicationDetails();
                updateTimeline(currentApplication.status);
                updateProgress(currentApplication.status);
                updateHardCopyStatus(currentApplication.hard_copy_received);
                if (currentApplication.document_links) displayDocumentChecklist(currentApplication.document_links);
                else showEmptyDocuments();
                calculateEstimatedTime();
                displayProjectInformation(currentApplication);
            } else showError();
        } catch (error) { console.error('Error:', error); showError(); }
    }

    function displayProjectInformation(app) {
        // Project Information
        document.getElementById('project-title').textContent = app.project_title || 'Not provided';
        document.getElementById('project-location').textContent = app.project_location || 'Not provided';
        document.getElementById('project-description').textContent = app.project_description || 'Not provided';
        document.getElementById('project-type-badge').textContent = app.project_type || 'Not specified';
        
        // Format numbers
        if (app.lot_area) {
            document.getElementById('lot-area').textContent = `${parseFloat(app.lot_area).toLocaleString()} sqm`;
        } else {
            document.getElementById('lot-area').textContent = 'Not provided';
        }
        
        if (app.floor_area) {
            document.getElementById('floor-area').textContent = `${parseFloat(app.floor_area).toLocaleString()} sqm`;
        } else {
            document.getElementById('floor-area').textContent = 'Not provided';
        }
        
        document.getElementById('num-floors').textContent = app.num_floors || 'Not provided';
        
        if (app.estimated_cost) {
            document.getElementById('estimated-cost').textContent = `₱ ${parseFloat(app.estimated_cost).toLocaleString()}`;
        } else {
            document.getElementById('estimated-cost').textContent = 'Not provided';
        }
        
        // Professional Information
        document.getElementById('architect-name').textContent = app.architect_name || 'Not provided';
        document.getElementById('architect-license').textContent = app.architect_license || 'Not provided';
        document.getElementById('engineer-name').textContent = app.engineer_name || 'Not provided';
        document.getElementById('engineer-license').textContent = app.engineer_license || 'Not provided';
    }

    function calculateEstimatedTime() {
        if (!currentApplication) return;
        const estimatedDate = new Date(new Date(currentApplication.created_at).getTime() + 14 * 24 * 60 * 60 * 1000);
        document.getElementById('estimated-time').textContent = estimatedDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        const releaseDate = new Date(estimatedDate.getTime() + 7 * 24 * 60 * 60 * 1000);
        document.getElementById('target-release').textContent = releaseDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    }

    async function loadReviewActivities() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}/review-activities`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities) displayReviewActivities(data.activities);
                else showEmptyActivities();
            } else showEmptyActivities();
        } catch (error) { console.error('Error:', error); showEmptyActivities(); }
    }

    function loadDocumentVerificationStatus() {
        const saved = localStorage.getItem(`doc_verification_${applicationId}`);
        if (saved) try { documentVerificationStatus = JSON.parse(saved); } catch(e) { documentVerificationStatus = {}; }
    }

    function saveDocumentVerificationStatus() {
        localStorage.setItem(`doc_verification_${applicationId}`, JSON.stringify(documentVerificationStatus));
        updateVerificationStats();
    }

    async function saveDocumentVerification() {
        const verifiedCount = Object.keys(documentVerificationStatus).length;
        try {
            await fetch(`/staff/applications/${applicationId}/add-note`, {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ note: `Verification progress: ${verifiedCount}/${documentsList.length} documents verified.` })
            });
            alert('Progress saved successfully!');
        } catch(error) { alert('Progress saved locally only'); }
    }

    function resetDocumentVerification() {
        if (confirm('Reset all verification statuses?')) {
            documentVerificationStatus = {};
            saveDocumentVerificationStatus();
            if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
            alert('Reset complete');
        }
    }

    function updateVerificationStats() {
        let verified = 0;
        documentsList.forEach(doc => { if (documentVerificationStatus[doc.key]) verified++; });
        document.getElementById('verified-count').textContent = verified;
        document.getElementById('total-count').textContent = documentsList.length;
        document.getElementById('summary-verified').textContent = verified;
        document.getElementById('summary-pending').textContent = documentsList.length - verified;
        document.getElementById('verification-progress-bar').style.width = (verified / documentsList.length) * 100 + '%';
    }

    function displayDocumentChecklist(documents) {
        const container = document.getElementById('documents-checklist');
        let html = '';
        let categories = {};
        documentsList.forEach(doc => {
            if (documents[doc.key] && documents[doc.key].trim()) {
                if (!categories[doc.category]) categories[doc.category] = [];
                categories[doc.category].push({ ...doc, link: documents[doc.key], isVerified: documentVerificationStatus[doc.key]?.verified || false });
            }
        });
        for (const [category, docs] of Object.entries(categories)) {
            html += `<div class="mb-4"><h3 class="text-sm font-semibold mb-2 border-b pb-1">${category}</h3><div class="space-y-2">`;
            docs.forEach(doc => {
                html += `<div data-doc-key="${doc.key}" class="flex justify-between items-center p-2 rounded-lg ${doc.isVerified ? 'bg-green-50' : 'bg-gray-50'}">
                    <div class="flex items-center gap-2 flex-1"><input type="checkbox" class="doc-checkbox" data-doc-key="${doc.key}" onchange="toggleDocumentVerification('${doc.key}', this.checked)" ${doc.isVerified ? 'checked disabled' : ''}>
                    <span class="text-sm">${doc.name}</span></div>
                    ${doc.link ? `<button onclick="openDocumentLink('${doc.key}', '${doc.link.replace(/'/g, "\\'")}')" class="px-2 py-1 text-xs rounded ${doc.isVerified ? 'bg-gray-300 cursor-not-allowed' : 'bg-[#155386] text-white'}">${doc.isVerified ? 'Viewed' : 'View'}</button>` : '<span class="text-xs text-gray-400">No file</span>'}
                </div>`;
            });
            html += `</div></div>`;
        }
        container.innerHTML = html || '<div class="text-center py-8">No documents uploaded</div>';
        updateVerificationStats();
    }

    function toggleDocumentVerification(key, checked) {
        if (checked) documentVerificationStatus[key] = { verified: true, verified_at: new Date().toISOString() };
        else delete documentVerificationStatus[key];
        saveDocumentVerificationStatus();
        if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
    }

    function openDocumentLink(key, link) {
        if (!documentVerificationStatus[key]?.verified) {
            documentVerificationStatus[key] = { verified: true, verified_at: new Date().toISOString() };
            saveDocumentVerificationStatus();
            if (currentApplication?.document_links) displayDocumentChecklist(currentApplication.document_links);
            updateVerificationStats();
        }
        window.open(link, '_blank');
    }

    function showEmptyDocuments() {
        document.getElementById('documents-checklist').innerHTML = '<div class="text-center py-8 text-gray-500">No documents uploaded yet</div>';
    }

    function displayApplicationDetails() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('application-content').classList.remove('hidden');
        document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
        if (currentApplication.created_at) {
            document.getElementById('submitted-date').textContent = new Date(currentApplication.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('step-submitted-date').textContent = new Date(currentApplication.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }
        if (currentApplication.updated_at) document.getElementById('updated-date').textContent = new Date(currentApplication.updated_at).toLocaleDateString();
        updateStatusUI(currentApplication.status);
        document.getElementById('applicant-name').textContent = currentApplication.applicant_name || 'N/A';
        document.getElementById('applicant-email').textContent = currentApplication.email || 'N/A';
        document.getElementById('applicant-phone').textContent = currentApplication.phone || 'N/A';
        document.getElementById('applicant-address').textContent = currentApplication.address || 'N/A';
        document.querySelectorAll('.status-radio').forEach(radio => { if (radio.value === currentApplication.status) radio.checked = true; });
    }

    function updateStatusUI(status) {
        const config = { 'pending': 'yellow', 'under-review': 'purple', 'document-verification': 'purple', 'for-assessment': 'indigo', 'approved': 'green', 'rejected': 'red', 'for-release': 'blue', 'verified': 'emerald' };
        const color = config[status] || 'gray';
        const textMap = { 'for-assessment': 'For Assessment', 'document-verification': 'Document Verification', 'under-review': 'Under Review', 'for-release': 'For Release' };
        const text = textMap[status] || status.replace('-', ' ');
        document.getElementById('status-badge').className = `px-3 py-1 bg-${color}-100 text-${color}-600 rounded-full text-xs font-medium`;
        document.getElementById('status-badge').textContent = text;
        document.getElementById('current-status').textContent = text;
        document.getElementById('current-status-card').className = `p-4 bg-${color}-50 rounded-lg border border-${color}-200`;
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
                circle.className = 'w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2';
                circle.innerHTML = '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                text.className = 'text-sm font-medium text-gray-800';
                if (index === currentIndex) {
                    const dateEl = document.getElementById(`step-${step}-date`);
                    if (dateEl) dateEl.textContent = 'In Progress';
                    el.classList.add('step-processing');
                } else {
                    el.classList.remove('step-processing');
                }
            } else {
                circle.className = 'w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2';
                circle.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                text.className = 'text-sm font-medium text-gray-400';
                el.classList.remove('step-processing');
            }
        });
        const progressWidth = currentIndex >= 0 ? ((currentIndex + 1) / steps.length) * 100 : 0;
        document.getElementById('progress-line').style.width = progressWidth + '%';
    }

    function updateProgress(status) {
        const progress = { 'draft': 0, 'pending': 20, 'under-review': 35, 'document-verification': 50, 'for-assessment': 65, 'approved': 80, 'for-release': 95, 'verified': 100, 'rejected': 100 }[status] || 0;
        document.getElementById('progress-percentage').textContent = progress + '%';
        document.getElementById('progress-bar').style.width = progress + '%';
    }

    function updateHardCopyStatus(received) {
        document.getElementById('hardcopy-notice').classList.toggle('hidden', received);
        document.getElementById('hardcopy-received-notice').classList.toggle('hidden', !received);
        document.getElementById('hardcopy-checkbox').checked = received;
    }

    // MODIFIED: updateStatus function now handles for-assessment without document check
    async function updateStatus() {
        const selected = document.querySelector('input[name="status"]:checked');
        if (!selected) { alert('Please select a status'); return; }
        
        // For assessment status - open modal without document verification check
        if (selected.value === 'for-assessment') { 
            openAssessmentModal(); 
            return; 
        }
        
        const btn = event.target;
        const original = btn.innerHTML;
        btn.innerHTML = 'Updating...';
        btn.disabled = true;
        try {
            const response = await fetch(`/staff/applications/${applicationId}/status`, {
                method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ status: selected.value, remarks: document.getElementById('status-remarks').value, hardcopy_received: document.getElementById('hardcopy-checkbox').checked })
            });
            const data = await response.json();
            if (data.success) {
                alert('Status updated successfully');
                location.reload();
            } else alert(data.message || 'Failed to update status');
        } catch(error) { alert('Error updating status'); }
        finally { btn.innerHTML = original; btn.disabled = false; }
    }

    function displayReviewActivities(activities) {
        const container = document.getElementById('activity-log');
        if (!activities?.length) { showEmptyActivities(); return; }
        let html = '';
        activities.slice(0, 5).forEach(a => {
            const date = new Date(a.created_at);
            const diffMins = Math.floor((new Date() - date) / 60000);
            const timeAgo = diffMins < 1 ? 'just now' : diffMins < 60 ? diffMins + ' min ago' : Math.floor(diffMins / 60) + ' hours ago';
            html += `<div class="flex gap-2 p-2 border-b"><div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div><div><p class="text-sm font-medium">${a.action_display || a.action}</p><p class="text-xs text-gray-500">${a.reviewer_name || 'System'} • ${timeAgo}</p>${a.remarks ? `<p class="text-xs text-gray-400 mt-1">${a.remarks.substring(0, 100)}</p>` : ''}</div></div>`;
        });
        container.innerHTML = html;
    }

    function showEmptyActivities() {
        document.getElementById('activity-log').innerHTML = '<div class="text-center py-8 text-gray-500">No activity yet</div>';
    }

    function loadFullActivityHistory() { window.location.href = `/staff/applications/${applicationId}/activity-history`; }
    function exportAsPDF() { window.location.href = `/staff/applications/${applicationId}/export-pdf`; }
    function archiveApplication() { if(confirm('Archive this application?')) fetch(`/staff/applications/${applicationId}/archive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.href = '/staff/applications'); }
    function showError() { document.getElementById('loading-state').classList.add('hidden'); document.getElementById('error-state').classList.remove('hidden'); }
    
    function toggleMissingDocumentsDropdown() {
        const dropdown = document.getElementById('missing-documents-dropdown');
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) loadMissingDocumentsList();
    }
    
    function loadMissingDocumentsList() {
        const container = document.getElementById('missing-docs-list');
        let html = '';
        let categories = {};
        documentsList.forEach(doc => {
            if (!categories[doc.category]) categories[doc.category] = [];
            categories[doc.category].push(doc);
        });
        for (const [category, docs] of Object.entries(categories)) {
            html += `<div class="mb-2"><p class="text-xs font-semibold text-gray-500">${category}</p>`;
            docs.forEach(doc => {
                html += `<label class="flex items-center p-1"><input type="checkbox" class="missing-doc-checkbox mr-2" data-doc-name="${doc.name}"><span class="text-sm">${doc.name}</span></label>`;
            });
            html += `</div>`;
        }
        container.innerHTML = html;
    }
    
    function filterMissingDocuments() {
        const search = document.getElementById('document-search').value.toLowerCase();
        document.querySelectorAll('.missing-doc-checkbox').forEach(cb => {
            const label = cb.closest('label');
            if (label && label.textContent.toLowerCase().includes(search)) label.style.display = 'flex';
            else if (label) label.style.display = 'none';
        });
    }
    
    function clearSelectedMissingDocuments() { document.querySelectorAll('.missing-doc-checkbox').forEach(c => c.checked = false); }
    
    async function sendDocumentRequest() {
        const selected = Array.from(document.querySelectorAll('.missing-doc-checkbox:checked')).map(cb => cb.getAttribute('data-doc-name'));
        if (selected.length === 0) { alert('Select at least one document'); return; }
        const remarks = document.getElementById('document-request-remarks').value;
        try {
            const response = await fetch(`/staff/applications/${applicationId}/request-missing-documents`, {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ documents: selected, remarks: remarks })
            });
            const data = await response.json();
            if (data.success) { alert('Request sent successfully'); toggleMissingDocumentsDropdown(); }
            else alert(data.message || 'Failed');
        } catch(error) { alert('Error sending request'); }
    }
</script>

<style>
    .rotate-180 { transform: rotate(180deg); }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .step-processing .w-10 { animation: stepGlow 2s ease-in-out infinite; }
    @keyframes stepGlow { 0%, 100% { box-shadow: 0 0 5px rgba(21,83,134,0.3); } 50% { box-shadow: 0 0 20px rgba(64,121,140,0.6); transform: scale(1.05); } }
    #assessment-modal .bg-white { animation: modalSlideIn 0.3s ease-out; }
    @keyframes modalSlideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .hidden { display: none; }
</style>
@endsection