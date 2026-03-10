@extends('layouts.dashboard')

@section('title', 'Application Details - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Back Button -->
    <div>
        <a href="/user/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to My Applications
        </a>
    </div>

    <!-- Hard Copy Notice (if application is pending/approved) -->
    @if(true) <!-- Add condition to show only if hard copies not yet submitted -->
    <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-600 rounded-r-lg">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">Hard Copy Submission Required</h4>
                <p class="text-sm text-gray-700 mt-1">Please submit the original hard copies of ALL documents to the Office of the Building Official (OBO) to complete your application.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Application Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white text-xl font-bold">
                    BP
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending Review</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-gray-500">Application ID: <span class="font-mono font-medium text-[#155386]">APP-2025-001</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Submitted: <span class="font-medium text-gray-700">May 5, 2025</span></span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Last Updated: <span class="font-medium text-gray-700">May 7, 2025</span></span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Application
                </button>
                <button class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Contact Support
                </button>
            </div>
        </div>
    </div>

    <!-- Progress Timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Application Progress</h2>
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-600">Overall Completion</span>
                <span class="font-semibold text-[#155386]">65%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-[#155386] to-[#40798C] h-3 rounded-full" style="width: 65%"></div>
            </div>
        </div>

        <!-- Timeline Steps -->
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute top-5 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-5 left-0 w-2/3 h-0.5 bg-[#155386]"></div>
            
            <!-- Steps -->
            <div class="relative flex justify-between">
                <!-- Step 1: Submitted -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-800">Submitted</p>
                    <p class="text-xs text-gray-500">May 5, 2025</p>
                </div>
                
                <!-- Step 2: Under Review -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-800">Under Review</p>
                    <p class="text-xs text-gray-500">In Progress</p>
                </div>
                
                <!-- Step 3: Document Verification -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Document Verification</p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
                
                <!-- Step 4: Approval -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Approval</p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
                
                <!-- Step 5: Release -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-400">For Release</p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column - Application Details -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Applicant Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Applicant Information</h2>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400">Full Name</p>
                        <p class="text-sm font-medium text-gray-800">Juan Dela Cruz</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Email Address</p>
                        <p class="text-sm font-medium text-gray-800">juan.delacruz@email.com</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Contact Number</p>
                        <p class="text-sm font-medium text-gray-800">0917 123 4567</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date of Birth</p>
                        <p class="text-sm font-medium text-gray-800">January 15, 1990</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-400">Address</p>
                        <p class="text-sm font-medium text-gray-800">123 Rizal St., Brgy. San Jose, Legazpi City</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Tax Identification No.</p>
                        <p class="text-sm font-medium text-gray-800">123-456-789-000</p>
                    </div>
                </div>
            </div>

            <!-- Project Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <p class="text-xs text-gray-400">Location</p>
                        <p class="text-sm font-medium text-gray-800">Lot 5, Block 3, Brgy. San Jose, Legazpi City</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lot Area</p>
                        <p class="text-sm font-medium text-gray-800">200 sqm</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Zoning Classification</p>
                        <p class="text-sm font-medium text-gray-800">Residential - R2</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Occupancy Type</p>
                        <p class="text-sm font-medium text-gray-800">Single Family</p>
                    </div>
                </div>
            </div>

            <!-- Required Documents Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Uploaded Documents</h2>
                    <span class="text-sm text-gray-500">13/13 Uploaded</span>
                </div>
                
                <div class="space-y-3">
                    <!-- Application Letter -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Application Letter</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Building Permit Forms -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Building Permit Forms</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Architectural Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Architectural Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Civil/Structural Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Civil/Structural Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Electrical Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Electrical Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Sanitary/Plumbing Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Sanitary/Plumbing Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Mechanical Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Mechanical Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Fencing Plans -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Fencing Plans (5 sets)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Proof of Ownership -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Proof of Ownership (2 copies)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Bill of Materials -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Bill of Materials (5 copies)</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Structural Design Analysis -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Structural Design Analysis</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Barangay Clearance -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Barangay Clearance</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>

                    <!-- Valid ID -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Valid ID</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium">Uploaded</span>
                    </div>
                </div>

                <!-- Hard Copy Status -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Hard Copy Submission</p>
                            <p class="text-xs text-gray-600 mt-1">All uploaded documents require original hard copy submission to the Office of the Building Official (OBO).</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                                <span class="text-xs text-gray-500">Awaiting hard copies</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSHP (Optional) -->
                <div class="mt-3 p-3 bg-gray-50 rounded-lg opacity-75">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-xs text-gray-500">i</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500">CSHP from DOLE (Optional - For Contractors with PCAB)</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-6 flex justify-end">
                    <button class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Download All Documents
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Status & Updates -->
        <div class="lg:col-span-1 space-y-8">

            <!-- Current Status Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending Review</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Queue Position:</span>
                        <span class="text-sm font-medium text-gray-800">#24 of 156</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Estimated Review:</span>
                        <span class="text-sm font-medium text-gray-800">3-5 business days</span>
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
                                <p class="text-sm font-semibold text-gray-800">May 20, 2025</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hard Copy Status in Sidebar -->
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <span class="text-xs font-medium text-gray-700">Hard Copy Status:</span>
                        <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Submit originals to OBO</p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-2">
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Send Message to Reviewer
                    </button>
                    <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Request for Assistance
                    </button>
                </div>
            </div>

            <!-- Activity Log Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Activity Log</h2>
                
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="h-3 w-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Documents uploaded</p>
                            <p class="text-xs text-gray-400">May 7, 2025 • 10:30 AM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Application under review</p>
                            <p class="text-xs text-gray-400">May 6, 2025 • 2:15 PM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="h-3 w-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Application submitted</p>
                            <p class="text-xs text-gray-400">May 5, 2025 • 9:45 AM</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="h-3 w-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Payment confirmed</p>
                            <p class="text-xs text-gray-400">May 5, 2025 • 9:30 AM</p>
                        </div>
                    </div>
                </div>
                
                <button class="mt-4 text-sm text-[#155386] hover:text-[#40798C] font-medium">
                    View Full History →
                </button>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
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
@endsection