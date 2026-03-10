@extends('layouts.dashboard')

@section('title', 'Application Details - Staff - Konstructo')

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
            <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Application
            </button>
        </div>
    </div>

    <!-- Hard Copy Status Banner -->
    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-200">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-gray-800">Hard Copy Submission Status</h4>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">Applicant has confirmed submission of hard copies. Awaiting physical documents at OBO.</p>
            </div>
        </div>
    </div>

    <!-- Application Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white text-xl font-bold">
                    BP
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending Review</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <span class="text-gray-500">Application ID: <span class="font-mono font-medium text-[#155386]">APP-2025-001</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Submitted: <span class="font-medium text-gray-700">May 5, 2025</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Last Updated: <span class="font-medium text-gray-700">May 7, 2025</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Queue Position: <span class="font-medium text-gray-700">#24 of 156</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column - Applicant & Project Details -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Applicant Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Applicant Information</h2>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-400">Full Name</p>
                        <p class="text-sm font-medium text-gray-800">Juan Santos Dela Cruz</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date of Birth</p>
                        <p class="text-sm font-medium text-gray-800">January 15, 1990 (35 years old)</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Place of Birth</p>
                        <p class="text-sm font-medium text-gray-800">Legazpi City, Albay</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Gender</p>
                        <p class="text-sm font-medium text-gray-800">Male</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Civil Status</p>
                        <p class="text-sm font-medium text-gray-800">Married</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Citizenship</p>
                        <p class="text-sm font-medium text-gray-800">Filipino</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">TIN</p>
                        <p class="text-sm font-medium text-gray-800">123-456-789-000</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email Address</p>
                        <p class="text-sm font-medium text-gray-800">juan.delacruz@email.com</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Contact Number</p>
                        <p class="text-sm font-medium text-gray-800">0917 123 4567</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-400">Address</p>
                        <p class="text-sm font-medium text-gray-800">123 Rizal St., Brgy. San Jose, Legazpi City, Albay 4500</p>
                    </div>
                </div>
            </div>

            <!-- Project Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-400">Project Name</p>
                        <p class="text-sm font-medium text-gray-800">Two-Storey Residential Building</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Project Type</p>
                        <p class="text-sm font-medium text-gray-800">Residential - New Construction</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Total Floor Area</p>
                        <p class="text-sm font-medium text-gray-800">120 sqm</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Estimated Cost</p>
                        <p class="text-sm font-medium text-gray-800">₱ 1,500,000.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lot Area</p>
                        <p class="text-sm font-medium text-gray-800">200 sqm</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Zoning Classification</p>
                        <p class="text-sm font-medium text-gray-800">Residential - R2</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-400">Project Location</p>
                        <p class="text-sm font-medium text-gray-800">Lot 5, Block 3, Brgy. San Jose, Legazpi City, Albay</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Occupancy Type</p>
                        <p class="text-sm font-medium text-gray-800">Single Family</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Number of Storeys</p>
                        <p class="text-sm font-medium text-gray-800">2 Storeys</p>
                    </div>
                </div>
            </div>

            <!-- Google Drive Documents Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Google Drive Documents</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">13/13 Uploaded</span>
                    </div>
                </div>
                
                <!-- Google Drive Link Section -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Google Drive Folder</p>
                            <div class="flex items-center gap-2 mt-1">
                                <a href="#" class="text-[#155386] hover:underline text-sm flex items-center gap-1" onclick="window.open('https://drive.google.com/drive/folders/1a2b3c4d5e6f7g8h9i0j', '_blank')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    View Folder
                                </a>
                                <span class="text-gray-300">|</span>
                                <button class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1" onclick="copyToClipboard('https://drive.google.com/drive/folders/1a2b3c4d5e6f7g8h9i0j')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Copy Link
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Folder Name: <span class="font-mono">APP-Dela Cruz-001</span></p>
                        </div>
                    </div>
                </div>

                <!-- Document Checklist (Collapsible) -->
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between cursor-pointer" onclick="toggleDocuments()">
                        <h4 class="font-medium text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            View Document Checklist (13 items)
                        </h4>
                        <svg id="chevron-icon" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div id="document-checklist" class="hidden p-4 bg-white border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Application Letter</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Building Permit Forms</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Architectural Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Civil/Structural Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Electrical Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Sanitary/Plumbing Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mechanical Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Fencing Plans (5 sets)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Proof of Ownership (2 copies)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Bill of Materials (5 copies)</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Structural Design Analysis</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Barangay Clearance</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Valid ID</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-4 italic">*Optional: CSHP from DOLE (for contractors with PCAB)</p>
                    </div>
                </div>

                <!-- Document Actions -->
                <div class="mt-6 flex justify-end gap-3">
                    <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                        Request Missing Documents
                    </button>
                    <button class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                        Verify All Documents
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Status & Actions -->
        <div class="lg:col-span-1 space-y-8">

            <!-- Status Update Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
                
                <div class="space-y-4">
                    <!-- Current Status -->
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-xs text-gray-500 mb-1">Current Status</p>
                        <p class="text-lg font-semibold text-yellow-600">Pending Review</p>
                        <p class="text-xs text-gray-500 mt-2">Queue Position: #24 of 156</p>
                    </div>

                    <!-- Hard Copy Status Toggle -->
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <label class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Hard Copy Received</span>
                            <input type="checkbox" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Mark when applicant submits physical documents</p>
                    </div>

                    <!-- Google Drive Access Status -->
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Google Drive Access</span>
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Working</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Last accessed: May 10, 2025</p>
                    </div>

                    <!-- Status Options -->
                    <div class="space-y-2">
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <input type="radio" name="status" class="h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                            <span class="ml-3 text-sm font-medium text-gray-700">Under Review</span>
                        </label>
                        
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <input type="radio" name="status" class="h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                            <span class="ml-3 text-sm font-medium text-green-600">Approved</span>
                        </label>
                        
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <input type="radio" name="status" class="h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                            <span class="ml-3 text-sm font-medium text-red-600">Rejected</span>
                        </label>
                        
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <input type="radio" name="status" class="h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                            <span class="ml-3 text-sm font-medium text-blue-600">For Release</span>
                        </label>
                        
                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                            <input type="radio" name="status" class="h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                            <span class="ml-3 text-sm font-medium text-purple-600">Completed</span>
                        </label>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes</label>
                        <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add remarks or notes about this application..."></textarea>
                    </div>

                    <!-- Update Button -->
                    <button class="w-full px-4 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">
                        Update Status
                    </button>
                </div>
            </div>

            <!-- Application Timeline Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Application Timeline</h2>
                
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Application Submitted</p>
                            <p class="text-xs text-gray-500">May 5, 2025 • 9:45 AM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Payment Confirmed</p>
                            <p class="text-xs text-gray-500">May 5, 2025 • 10:30 AM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Under Review</p>
                            <p class="text-xs text-gray-500">May 6, 2025 • 2:15 PM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-xs text-gray-500">4</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-400">Document Verification</p>
                            <p class="text-xs text-gray-400">Pending</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-xs text-gray-500">5</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-400">Approval</p>
                            <p class="text-xs text-gray-400">Pending</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-xs text-gray-500">6</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-400">For Release</p>
                            <p class="text-xs text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>
                
                <button class="mt-4 text-sm text-[#155386] hover:underline font-medium w-full text-center">
                    View Full Timeline
                </button>
            </div>

            <!-- Staff Notes Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Staff Notes</h2>
                
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">May 7, 2025 • 10:30 AM</p>
                        <p class="text-sm text-gray-700">Initial review completed. All documents present in Google Drive.</p>
                        <p class="text-xs text-gray-500 mt-1">— Maria Santos, Evaluator</p>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">May 6, 2025 • 3:45 PM</p>
                        <p class="text-sm text-gray-700">Verified Google Drive access. All 13 documents uploaded.</p>
                        <p class="text-xs text-gray-500 mt-1">— John Doe, Engineer</p>
                    </div>
                </div>
                
                <!-- Add Note -->
                <div class="mt-4">
                    <textarea rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add a note..."></textarea>
                    <button class="mt-2 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm w-full">
                        Add Note
                    </button>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
                
                <div class="space-y-2">
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span class="text-sm">Send Message to Applicant</span>
                    </button>
                    
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm">Email Applicant</span>
                    </button>
                    
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">Schedule Review</span>
                    </button>
                    
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="text-sm">Download All Documents</span>
                    </button>
                    
                    <button class="w-full flex items-center gap-3 px-4 py-3 text-left text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="text-sm">Delete Application</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Staff Guidelines</h4>
                <p class="text-sm text-gray-600">
                    Access the Google Drive folder to review documents. Verify all 13 required items before updating status. Track hard copy submission separately.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Copy to Clipboard Function -->
<script>
    function toggleDocuments() {
        const checklist = document.getElementById('document-checklist');
        const chevron = document.getElementById('chevron-icon');
        
        if (checklist.classList.contains('hidden')) {
            checklist.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            checklist.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Link copied to clipboard!');
        }, function() {
            alert('Failed to copy link.');
        });
    }
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endsection