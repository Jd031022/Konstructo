@extends('layouts.dashboard')

@section('title', 'User Manual')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    <!-- PAGE HEADER WITH PRINT BUTTON -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">User Manual</h1>
                <p class="text-gray-600 mt-2">Comprehensive step-by-step guide for staff and administrators</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ asset('downloads/konstructo-manual.pdf') }}" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#0d3d5f] transition shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Download PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- NAVIGATION TABS -->
    <div class="max-w-7xl mx-auto mb-6 border-b border-gray-200 overflow-x-auto">
        <nav class="flex space-x-8" id="manualTabs">
            <button onclick="switchTab('getting-started')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition active whitespace-nowrap" data-tab="getting-started">
                Getting Started
            </button>
            <button onclick="switchTab('registration')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="registration">
    Account Registration
</button>
            <button onclick="switchTab('dashboard')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="dashboard">
                Dashboard Guide
            </button>
            <button onclick="switchTab('applications')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="applications">
                Applications
            </button>
            <button onclick="switchTab('staff-tasks')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="staff-tasks">
                Staff Tasks
            </button>
            <button onclick="switchTab('admin-tasks')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="admin-tasks">
                Admin Tasks
            </button>
            <button onclick="switchTab('faq')" class="tab-btn px-1 py-2 text-sm font-medium border-b-2 border-transparent hover:border-[#155386] transition whitespace-nowrap" data-tab="faq">
                FAQ
            </button>
        </nav>
    </div>

    <!-- CONTENT SECTIONS -->
    <div class="max-w-7xl mx-auto space-y-6">

<!-- GETTING STARTED TAB -->
<div id="getting-started" class="tab-content">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Getting Started</h2>
                <p class="text-gray-600 mt-1">Welcome to Konstructo - the building permit application management system</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">User Guide</span>
            </div>
        </div>

        <p class="text-gray-700 mb-8">This guide will help you navigate the system and perform your daily tasks efficiently.</p>

        <!-- Quick Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Your Role Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-5 hover:shadow-lg transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-[#155386] text-white flex items-center justify-center shadow-md group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-800">Your Role</h3>
                </div>
                <div class="text-sm text-gray-600 leading-relaxed">
                    @php
                        $position = auth()->user()->profile ? auth()->user()->profile->position : null;
                        $role = auth()->user()->role;
                    @endphp
                    @if($role === 'admin')
                        You are an <strong class="text-[#155386]">Administrator</strong>. You have full system access, can manage users, and configure settings.
                    @elseif($position === 'engineer')
                        You are an <strong class="text-[#155386]">Engineer</strong>. You can review applications, verify documents, conduct assessments, and make approval decisions.
                    @elseif($position === 'architect')
                        You are an <strong class="text-[#155386]">Architect</strong>. You can review and verify architectural plans and documents.
                    @elseif($position === 'cpdo')
                        You are a <strong class="text-[#155386]">CPDO Staff</strong>. You can verify ownership documents, approve applications, and upload certificates.
                    @elseif($position === 'assessor')
                        You are an <strong class="text-[#155386]">Assessor</strong>. You can verify Tax Declaration documents.
                    @elseif($position === 'treasurer')
                        You are a <strong class="text-[#155386]">Treasurer</strong>. You can verify Current Tax Receipts and manage payment orders.
                    @elseif($position === 'bfp')
                        You are a <strong class="text-[#155386]">BFP Staff</strong>. You can upload FSEC documents and add fire safety comments.
                    @elseif($position === 'mayor')
                        You are a <strong class="text-[#155386]">Mayor's Office Staff</strong>. You can view applications and view CPDO ratings.
                    @elseif($position === 'monitoring')
                        You are a <strong class="text-[#155386]">Monitoring Staff</strong>. You can update application statuses but cannot verify documents.
                    @elseif($position === 'administrative_aide')
                        You are an <strong class="text-[#155386]">Administrative Aide</strong>. You have basic view access to applications.
                    @else
                        You are a <strong class="text-[#155386]">Staff Member</strong>. Your access is configured by your assigned position.
                    @endif
                </div>
            </div>

            <!-- Main Features Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-5 hover:shadow-lg transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-[#40798C] text-white flex items-center justify-center shadow-md group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-800">Main Features</h3>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Access applications, dashboard analytics, payment tracking, client satisfaction surveys, and comprehensive reporting tools.
                </p>
            </div>

            <!-- Quick Tips Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-5 hover:shadow-lg transition group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-green-600 text-white flex items-center justify-center shadow-md group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-800">Quick Tips</h3>
                </div>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="text-[#155386] font-bold">•</span>
                        <span>Click the menu icon to expand the sidebar</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-[#155386] font-bold">•</span>
                        <span>Use search filters to find applications quickly</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-[#155386] font-bold">•</span>
                        <span>The system auto-refreshes every 60 seconds</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- NEW: Application Process Guide Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-[#155386] text-white flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-gray-800">5-Step Application Process</h3>
            </div>
            
            <div class="grid grid-cols-1 gap-5">
                <!-- Step 1: Ownership -->
                <div class="bg-gradient-to-r from-teal-50 to-white border border-teal-200 rounded-xl p-5 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">1</div>
                                <h4 class="font-semibold text-lg text-gray-800">Ownership Verification</h4>
                                <span class="text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full">Step 1</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Provide proof of property ownership with Google Drive links to required documents.</p>
                            <ul class="text-xs text-gray-500 space-y-1 ml-2">
                                <li class="flex items-center gap-1"><span class="text-teal-600">•</span> TCT / Deed of Sale</li>
                                <li class="flex items-center gap-1"><span class="text-teal-600">•</span> Tax Declaration</li>
                                <li class="flex items-center gap-1"><span class="text-teal-600">•</span> Current Tax Receipt</li>
                                <li class="flex items-center gap-1"><span class="text-teal-600">•</span> Special Power of Attorney (if representative)</li>
                            </ul>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-xs text-gray-500">Documents are verified by:</p>
                            <div class="flex flex-wrap gap-1 justify-center md:justify-end mt-1">
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">CPDO</span>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Assessor</span>
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Treasurer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Project Info -->
                <div class="bg-gradient-to-r from-blue-50 to-white border border-blue-200 rounded-xl p-5 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">2</div>
                                <h4 class="font-semibold text-lg text-gray-800">Project Information</h4>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Step 2</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Enter detailed information about your construction project.</p>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                                <span>• Project Title & Location</span>
                                <span>• Lot & Floor Area</span>
                                <span>• Number of Floors</span>
                                <span>• Estimated Cost</span>
                                <span class="col-span-2">• Owner & Professional Information (Architect, Engineer, etc.)</span>
                            </div>
                        </div>
                        <div class="text-center md:text-right">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Required for assessment</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Download Forms -->
                <div class="bg-gradient-to-r from-indigo-50 to-white border border-indigo-200 rounded-xl p-5 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">3</div>
                                <h4 class="font-semibold text-lg text-gray-800">Download Forms</h4>
                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Step 3</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Download required building permit forms, fill them out, and prepare for upload.</p>
                            <ul class="text-xs text-gray-500 space-y-1 ml-2">
                                <li class="flex items-center gap-1"><span class="text-indigo-600">•</span> Building Permit Application</li>
                                <li class="flex items-center gap-1"><span class="text-indigo-600">•</span> Zoning Compliance / Locational Clearance</li>
                                <li class="flex items-center gap-1"><span class="text-indigo-600">•</span> Architectural, Structural, Electrical, Plumbing Permits</li>
                                <li class="flex items-center gap-1"><span class="text-indigo-600">•</span> Optional: Mechanical, Electronics, Sign, Fencing, Demolition Permits</li>
                            </ul>
                        </div>
                        <div class="text-center md:text-right">
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Forms can only be downloaded once</span>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Upload Docs -->
                <div class="bg-gradient-to-r from-amber-50 to-white border border-amber-200 rounded-xl p-5 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-amber-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">4</div>
                                <h4 class="font-semibold text-lg text-gray-800">Upload Documents</h4>
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Step 4</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Upload all filled-out forms and supporting documents to Google Drive and provide shareable links.</p>
                            <ul class="text-xs text-gray-500 space-y-1 ml-2">
                                <li class="flex items-center gap-1"><span class="text-amber-600">•</span> Application Letter</li>
                                <li class="flex items-center gap-1"><span class="text-amber-600">•</span> Building Permit Forms</li>
                                <li class="flex items                                 <li class="flex items-center gap-1"><span class="text-amber-600">•</span> Zoning Compliance / Locational Clearance</li>
                                <li class="flex items-center gap-1"><span class="text-amber-600">•</span> Architectural, Structural, Electrical, Plumbing Plans</li>
                                <li class="flex items-center gap-1"><span class="text-amber-600">•</span> Bill of Materials, Barangay Clearance, Valid ID</li>
                                <li class="flex items-center gap-1"><span class="text-amber-600">•</span> PTR License (Current Year)</li>
                            </ul>
                        </div>
                        <div class="text-center md:text-right">
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Hard copies required later</span>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Review & Submit -->
                <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 rounded-xl p-5 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">5</div>
                                <h4 class="font-semibold text-lg text-gray-800">Review & Submit</h4>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Step 5</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Review all entered information, agree to legal declarations, and submit your application.</p>
                            <ul class="text-xs text-gray-500 space-y-1 ml-2">
                                <li class="flex items-center gap-1"><span class="text-green-600">•</span> Verify project and owner information</li>
                                <li class="flex items-center gap-1"><span class="text-green-600">•</span> Confirm all documents are uploaded</li>
                                <li class="flex items-center gap-1"><span class="text-green-600">•</span> Acknowledge hard copy submission requirement</li>
                                <li class="flex items-center gap-1"><span class="text-green-600">•</span> Accept legal declaration (perjury warning)</li>
                            </ul>
                        </div>
                        <div class="text-center md:text-right">
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Application number generated</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800 flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><strong class="font-semibold">Note:</strong> After submission, your application will be reviewed by staff. You will receive email notifications for status updates. Original hard copies must be submitted to the Office of the Building Official (OBO) within 5 working days.</span>
                </p>
            </div>
        </div>



        <!-- Two-Step Verification Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-[#155386] text-white flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-gray-800">Two-Step Verification Process</h3>
            </div>
            <p class="text-gray-700 mb-5">After submission, applications go through a structured two-step verification process:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Step 1 Card -->
                <div class="bg-white rounded-xl p-5 border border-blue-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">1</div>
                        <h4 class="font-semibold text-lg text-gray-800">Step 1: Ownership Documents</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Verified by specific roles:</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span><span class="font-medium text-green-700">CPDO</span> - TCT/Deed of Sale</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span><span class="font-medium text-purple-700">Assessor</span> - Tax Declaration</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span><span class="font-medium text-orange-700">Treasurer</span> - Current Tax Receipt</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span><span class="font-medium text-red-700">CPDO/Assessor/Treasurer</span> - Special Power of Attorney (SPA)</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Card -->
                <div class="bg-white rounded-xl p-5 border border-blue-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">2</div>
                        <h4 class="font-semibold text-lg text-gray-800">Step 2: Project Documents</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Verified by:</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span><span class="font-medium text-blue-700">Engineers & Architects</span> - Can verify documents</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                            <span><span class="font-medium text-gray-700">Other roles</span> - View-only access</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span><span class="font-medium text-orange-700">CPDO Approval</span> - Required before Step 2 begins</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Account Status & Login Guide -->
        <div class="mt-8 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-[#155386] text-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-xl text-gray-800">Account Registration Process</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold">1</div>
                                <span class="font-medium text-gray-800">Register Account</span>
                            </div>
                            <p class="text-xs text-gray-600">Fill out personal information and create account credentials</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</div>
                                <span class="font-medium text-gray-800">Verify Email</span>
                            </div>
                            <p class="text-xs text-gray-600">Enter 6-digit code sent to your email address</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">3</div>
                                <span class="font-medium text-gray-800">Admin Approval</span>
                            </div>
                            <p class="text-xs text-gray-600">Wait for admin to approve your account (1-2 business days)</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 text-center">You will receive email notifications for verification and approval status updates.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ACCOUNT REGISTRATION TAB -->
<div id="registration" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Account Registration</h2>
                <p class="text-gray-600 mt-1">Step-by-step guide to creating your Konstructo account</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-purple-100 text-purple-800 px-3 py-1 rounded-full">New User Guide</span>
            </div>
        </div>

        <p class="text-gray-700 mb-8">Follow these steps to register for a new account and start applying for building permits online.</p>

        <!-- Registration Process Flow -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-[#155386] text-white flex items-center justify-center text-xl font-bold mx-auto mb-2">1</div>
                <p class="font-medium text-gray-800">Fill Out Form</p>
                <p class="text-xs text-gray-500">Personal & Account Info</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-[#40798C] text-white flex items-center justify-center text-xl font-bold mx-auto mb-2">2</div>
                <p class="font-medium text-gray-800">Verify Email</p>
                <p class="text-xs text-gray-500">Enter 6-digit code</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center text-xl font-bold mx-auto mb-2">3</div>
                <p class="font-medium text-gray-800">Admin Approval</p>
                <p class="text-xs text-gray-500">1-2 business days</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-bold mx-auto mb-2">4</div>
                <p class="font-medium text-gray-800">Login & Apply</p>
                <p class="text-xs text-gray-500">Start applications</p>
            </div>
        </div>

        <!-- Step 1: Registration Form -->
        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition mb-6">
            <div class="border-b border-gray-200 bg-white px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                    <h3 class="font-semibold text-lg text-gray-800">Registration Form (3 Steps)</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">The registration process is divided into three easy steps:</p>

                <!-- Step 1.1: Personal Info -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">A</div>
                        <h4 class="font-medium text-gray-800">Step 1: Personal Information</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-8">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Required Fields</p>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• First Name & Last Name</li>
                                <li>• Middle Name (Optional)</li>
                                <li>• Suffix (Jr., Sr., II, etc.)</li>
                                <li>• Phone Number (must start with 09, 11 digits)</li>
                                <li>• Zip Code & Address</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-blue-600 mb-1">💡 Tip</p>
                            <p class="text-sm text-gray-700">Enter your legal name as it appears on government IDs. This will be used for verification purposes.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1.2: Account Details -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">B</div>
                        <h4 class="font-medium text-gray-800">Step 2: Account Details</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-8">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Required Fields</p>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Email Address (valid format)</li>
                                <li>• Username (letters, numbers, _, - only)</li>
                                <li>• Password (8-16 characters, uppercase, number, special char)</li>
                                <li>• Confirm Password</li>
                            </ul>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <p class="text-xs text-yellow-600 mb-1">⚠️ Password Requirements</p>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• 8-16 characters long</li>
                                <li>• At least 1 uppercase letter (A-Z)</li>
                                <li>• At least 1 number (0-9)</li>
                                <li>• At least 1 special character (@$!%*?&)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 1.3: Review & Submit -->
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">C</div>
                        <h4 class="font-medium text-gray-800">Step 3: Review & Submit</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-8">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Review Information</p>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Verify personal information summary</li>
                                <li>• Verify account details (email, username)</li>
                                <li>• Accept Terms of Service & Privacy Policy</li>
                                <li>• Click "Create Account" to submit</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs text-green-600 mb-1">✓ Progress Indicator</p>
                            <p class="text-sm text-gray-700">The progress bar at the top shows which step you're on. Completed steps turn green with a checkmark icon.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Step 2: Email Verification -->
        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition mb-6">
            <div class="border-b border-gray-200 bg-white px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#40798C] text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                    <h3 class="font-semibold text-lg text-gray-800">Email Verification</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">After submitting the registration form, you will receive a verification email.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="font-medium text-blue-800 mb-2">How to Verify:</p>
                        <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                            <li>Check your email inbox for "Verify Your Email" message</li>
                            <li>Enter the 6-digit verification code in the modal</li>
                            <li>Click "Verify Email" button</li>
                            <li>Wait for confirmation message</li>
                        </ol>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                        <p class="font-medium text-yellow-800 mb-2">Didn't receive the code?</p>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                            <li>Check your spam/junk folder</li>
                            <li>Click "Resend Code" to receive a new code</li>
                            <li>Ensure you entered the correct email address</li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>

        <!-- Step 3: Admin Approval -->
        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition mb-6">
            <div class="border-b border-gray-200 bg-white px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                    <h3 class="font-semibold text-lg text-gray-800">Admin Approval & Account Status</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium text-gray-800 mb-2">After Email Verification:</p>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                            <li>You will be redirected to Account Status page</li>
                            <li>Your account is marked as "Pending Approval"</li>
                            <li>Admin reviews your registration (1-2 business days)</li>
                            <li>You will receive email notification when approved</li>
                        </ul>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                        <p class="font-medium text-yellow-800 mb-2">Status Messages You May See:</p>
                        <ul class="text-sm text-gray-700 space-y-2">
                            <li><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> <strong>Pending Approval</strong> - Account waiting for admin review</span></li>
                            <li><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> <strong>Approved</strong> - Account ready to use, can login</span></li>
                            <li><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> <strong>Rejected</strong> - Registration denied (reason provided)</span></li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>

        <!-- Step 4: Login -->
        <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
            <div class="border-b border-gray-200 bg-white px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                    <h3 class="font-semibold text-lg text-gray-800">Logging In</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-medium text-gray-800 mb-2">Login Credentials:</p>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                            <li>Use either Email OR Username</li>
                            <li>Enter your password</li>
                            <li>Check "Remember me" for convenience</li>
                            <li>Click Login button</li>
                        </ul>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="font-medium text-blue-800 mb-2">Forgot Password?</p>
                        <p class="text-sm text-gray-700">Click "Forgot Password" on the login page to reset your password. You will receive a password reset link via email.</p>
                    </div>
                </div>


            </div>
        </div>

        <!-- Important Notes -->
        <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-800 mb-1">Important Notes:</p>
                    <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                        <li>Registration is free and required to apply for building permits</li>
                        <li>Each user must have a unique email address</li>
                        <li>Inactive accounts may be deactivated after prolonged inactivity</li>
                        <li>For registration issues, contact support@konstructo.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- DASHBOARD TAB -->
<div id="dashboard" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard Guide</h2>
                <p class="text-gray-600 mt-1">Real-time insights into system performance and key metrics</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Auto-refreshes every 60s</span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Section 1: Header and Controls -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                        <h3 class="font-semibold text-lg text-gray-800">Dashboard Header & Controls</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">The top section includes date filters, export options, and displays your assigned position.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div>
                                <div class="text-lg font-bold text-gray-800">Dashboard</div>
                                <div class="text-sm text-gray-500">Welcome back! Here's your applications overview.</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg shadow-sm text-sm hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>April 2026</span>
                                    </button>
                                </div>
                                <button class="px-4 py-2 bg-[#155386] text-white rounded-lg text-sm flex items-center gap-2 shadow-sm hover:bg-[#0e3d5c] transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <p class="text-sm text-yellow-800"><strong>Tip:</strong> Click the date picker to select Daily, Monthly, or Yearly views. The dashboard supports filtering by specific dates, months, or entire years.</p>
                    </div>


                </div>
            </div>

            <!-- Section 2: Statistics Cards -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                        <h3 class="font-semibold text-lg text-gray-800">Key Statistics Cards</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Four main metric cards display at the top showing system performance with trend indicators.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500 shadow-sm hover:shadow-md transition">
                                <p class="text-gray-500 text-sm">Total Applications</p>
                                <p class="text-2xl font-bold text-gray-800">156</p>
                                <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    12% increase
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition">
                                <p class="text-gray-500 text-sm">Pending Review</p>
                                <p class="text-2xl font-bold text-gray-800">24</p>
                                <p class="text-xs text-yellow-600 mt-1">Awaiting action</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 shadow-sm hover:shadow-md transition">
                                <p class="text-gray-500 text-sm">Completed</p>
                                <p class="text-2xl font-bold text-gray-800">98</p>
                                <p class="text-xs text-green-600 mt-1">63% completion rate</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500 shadow-sm hover:shadow-md transition">
                                <p class="text-gray-500 text-sm">For Release</p>
                                <p class="text-2xl font-bold text-gray-800">12</p>
                                <p class="text-xs text-purple-600 mt-1">Ready for pickup</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <p class="text-sm text-blue-800"><strong>Reading the Cards:</strong> Each card shows a metric with a trend indicator (up arrow green for increase, down arrow red for decrease). Yellow-border cards indicate pending items requiring attention.</p>
                    </div>


                </div>
            </div>

            <!-- Section 3: Charts -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                        <h3 class="font-semibold text-lg text-gray-800">Trend Charts & Analytics</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Visual charts display application trends and performance over time. You can switch between This Week, Last Week, This Month, Last Month, and This Year views.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="flex justify-end mb-4">
                            <select class="border border-gray-200 rounded-lg text-sm px-4 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-[#155386]">
                                <option>This Month</option>
                                <option>Last Month</option>
                                <option>This Year</option>
                            </select>
                        </div>
                        <div class="h-48 bg-white rounded-lg border border-gray-200 flex items-center justify-center mb-4">
                            <div class="text-center text-gray-500">
                                <div class="flex items-end justify-center gap-4 h-32">
                                    <div class="w-12 bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 80px;"></div>
                                    <div class="w-12 bg-gradient-to-t from-[#40798C] to-[#70A9A1] rounded-t-lg" style="height: 120px;"></div>
                                    <div class="w-12 bg-gradient-to-t from-[#70A9A1] to-[#9EC5CB] rounded-t-lg" style="height: 60px;"></div>
                                    <div class="w-12 bg-gradient-to-t from-[#9EC5CB] to-[#B8D8E3] rounded-t-lg" style="height: 100px;"></div>
                                </div>
                                <p class="text-xs mt-4">Jan | Feb | Mar | Apr</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <p class="text-sm text-green-800"><strong>Chart Navigation:</strong> Hover over data points to see exact values. Charts include summary statistics: Total, Average, Peak, and Growth percentage.</p>
                    </div>


                </div>
            </div>

            <!-- Section 4: Donut Chart - Application Status -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                        <h3 class="font-semibold text-lg text-gray-800">Application Status Distribution</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Donut chart shows the breakdown of applications by status, with status legend below.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="relative w-48 h-48">
                                <div class="w-48 h-48 rounded-full border-8 border-gray-200"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-800">63%</div>
                                        <div class="text-xs text-gray-500">complete</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-amber-500"></div>Pending</span><span>15% (24)</span></div>
                                <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-purple-500"></div>Under Review</span><span>25% (39)</span></div>
                                <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-emerald-500"></div>Approved</span><span>35% (55)</span></div>
                                <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-500"></div>For Release</span><span>8% (12)</span></div>
                                <div class="flex items-center justify-between text-sm"><span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div>Completed</span><span>10% (16)</span></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Section 5: Recent Activity -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">5</div>
                        <h3 class="font-semibold text-lg text-gray-800">Recent Activities Feed</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">A timeline showing the latest actions across the system in real-time. Includes document verifications, status changes, and remarks added.</p>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Application APP-2026-001 submitted</p>
                                    <p class="text-xs text-gray-400">2 hours ago by John Smith</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-200">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">TCT Document verified</p>
                                    <p class="text-xs text-gray-400">3 hours ago by Maria Garcia (CPDO)</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Status changed to Under Review</p>
                                    <p class="text-xs text-gray-400">5 hours ago by Staff Admin</p>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Section 6: Citizen Satisfaction -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">6</div>
                        <h3 class="font-semibold text-lg text-gray-800">Citizen Satisfaction Dashboard</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">View client feedback metrics including overall satisfaction rating, response rate, and service quality dimensions.</p>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-center mb-4">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-[#155386] to-[#40798C] text-white mb-2">
                                <span class="text-2xl font-bold">4.2</span>
                                <span class="text-sm">/5</span>
                            </div>
                            <div class="flex justify-center gap-0.5">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">156 survey responses</p>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Section 7: Staff Performance & Verification Queue -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">7</div>
                        <h3 class="font-semibold text-lg text-gray-800">Staff Performance & Verification Queue</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Track staff productivity and monitor document verification queues at a glance.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="font-medium text-gray-700 mb-3">Staff Performance</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-2 hover:bg-gray-100 rounded transition">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-[#155386] to-[#40798C] flex items-center justify-center text-white text-xs font-bold">JD</div>
                                        <div><p class="text-sm font-medium">John Doe</p><p class="text-xs text-gray-500">Engineer</p></div>
                                    </div>
                                    <div class="text-right"><p class="text-sm font-bold">24</p><p class="text-xs text-gray-400">this week</p></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="font-medium text-gray-700 mb-3">Verification Queue</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-2 bg-yellow-50 rounded"><span class="text-sm">Pending Verification</span><span class="text-lg font-bold text-yellow-600">24</span></div>
                                <div class="flex justify-between items-center p-2 bg-blue-50 rounded"><span class="text-sm">Under Review</span><span class="text-lg font-bold text-blue-600">12</span></div>
                                <div class="flex justify-between items-center p-2 bg-green-50 rounded"><span class="text-sm">Ready for Release</span><span class="text-lg font-bold text-green-600">8</span></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

        <!-- APPLICATIONS TAB -->
<div id="applications" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Applications Management</h2>
                <p class="text-gray-600 mt-1">Step-by-step guide to finding, viewing, and managing applications</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Staff Guide</span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Step 1: Accessing Applications -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                        <h3 class="font-semibold text-lg text-gray-800">Access Applications</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Navigate to the Applications section from the sidebar to view and manage all building permit applications.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                From the left sidebar:
                            </p>
                            <div class="ml-4 space-y-2 text-sm text-gray-600">
                                <div class="flex items-start gap-2">
                                    <span class="text-[#155386] font-bold">1.</span>
                                    <span>Locate the <strong class="bg-gray-200 px-1.5 py-0.5 rounded">Applications</strong> menu item (may show badge with pending count)</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-[#155386] font-bold">2.</span>
                                    <span>Click to open the Applications list page</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-[#155386] font-bold">3.</span>
                                    <span>The page will display all available applications with pagination (10/25/50/100 per page)</span>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Step 2: Searching & Filtering -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                        <h3 class="font-semibold text-lg text-gray-800">Search & Filter Applications</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Use powerful search and filtering tools to find specific applications. The table includes aging indicators for pending applications.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                                <input type="text" placeholder="Search by application number or applicant name..." class="col-span-2 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#155386]">
                                <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#155386]">
                                    <option>All Status</option>
                                    <option>Pending Review</option>
                                    <option>Under Review</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                </select>
                                <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#155386]">
                                    <option>All Aging Status</option>
                                    <option>New (0-2 days)</option>
                                    <option>Warning (3-5 days)</option>
                                    <option>Critical (6-10 days)</option>
                                    <option>Overdue (10+ days)</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button class="px-4 py-2 bg-[#155386] text-white rounded-lg text-sm hover:bg-[#0e3d5c] transition">Apply Filters</button>
                                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                            <p class="text-sm font-semibold text-blue-800 mb-2">Search By:</p>
                            <ul class="space-y-1 text-sm text-blue-700">
                                <li>- <strong>Application Number</strong> - e.g., "APP-2026-001"</li>
                                <li>- <strong>Applicant Name</strong> - e.g., "John Smith"</li>
                                <li>- <strong>Status</strong> - Pending, Under Review, Approved, Rejected, For Release, Completed</li>
                                <li>- <strong>Aging Status</strong> - Filter by how long applications have been pending</li>
                            </ul>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                            <p class="text-sm font-semibold text-yellow-800 mb-2">Aging Legend:</p>
                            <div class="space-y-1 text-sm">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span><span>Green - New (0-2 days)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-500"></span><span>Yellow - Warning (3-5 days)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-500"></span><span>Orange - Critical (6-10 days)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span><span>Red - Overdue (10+ days with pulsing effect)</span></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Step 3: Viewing Application Details -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                        <h3 class="font-semibold text-lg text-gray-800">View Application Details</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Click on any application row to view complete details and take action. Access is role-based — you'll only see actions you're permitted to perform.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="space-y-3">
                            <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition cursor-pointer group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-bold text-gray-800">APP-2026-001</p>
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending Review</span>
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">Aging: 5 days</span>
                                        </div>
                                        <p class="text-sm text-gray-600">John Smith - Residential Building Permit</p>
                                        <p class="text-xs text-gray-400 mt-1">123 Main Street, Barangay San Isidro</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#155386] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 text-center">Click any row to open application details</p>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                        <p class="text-sm font-semibold text-green-800 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Details Page Sections:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-green-700">
                            <ul class="space-y-1">
                                <li>- <strong>Project Information</strong> - Title, location, description, areas, cost</li>
                                <li>- <strong>Owner/Applicant Information</strong> - Name, email, contact, address</li>
                                <li>- <strong>Professional Information</strong> - Architects, engineers, license numbers</li>
                                <li>- <strong>Step 1: Ownership Documents</strong> - TCT, Tax Declaration, Tax Receipt, SPA</li>
                            </ul>
                            <ul class="space-y-1">
                                <li>- <strong>Step 2: Project Documents</strong> - Plans, forms, clearances</li>
                                <li>- <strong>Activity Log</strong> - Complete history with "View Full History" button</li>
                                <li>- <strong>Status Update Card</strong> - Role-based status change options</li>
                                <li>- <strong>Assessment Cards</strong> - Building Permit Fee and CPDO Fee assessments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Activity History -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                        <h3 class="font-semibold text-lg text-gray-800">View Full Activity History</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Click "View Full History" in the Activity Log card to see the complete timeline of all actions for an application.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Status changed from Pending to Under Review</p>
                                    <p class="text-xs text-gray-400">by Maria Garcia (Engineer) • 2 days ago</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">TCT Document verified</p>
                                    <p class="text-xs text-gray-400">by John Santos (CPDO) • 3 days ago</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Tax Declaration verified</p>
                                    <p class="text-xs text-gray-400">by Assessor Office • 1 day ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <button class="text-sm text-[#155386] hover:text-[#0e3d5c] font-medium flex items-center gap-1 mx-auto">View Full History →</button>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Step 5: Application Status Reference -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">5</div>
                        <h3 class="font-semibold text-lg text-gray-800">Application Status Reference</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Understanding application statuses helps you track progress and know what actions are needed.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending Review</span>
                            </div>
                            <p class="text-sm text-gray-700">Application submitted and awaiting initial staff review. No documents verified yet.</p>
                        </div>

                        <div class="border-l-4 border-purple-500 bg-purple-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Under Review</span>
                            </div>
                            <p class="text-sm text-gray-700">Staff is actively reviewing documents and conducting verification.</p>
                        </div>

                        <div class="border-l-4 border-indigo-500 bg-indigo-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-medium">For Assessment</span>
                            </div>
                            <p class="text-sm text-gray-700">Documents verified, awaiting fee assessment from engineer.</p>
                        </div>

                        <div class="border-l-4 border-blue-500 bg-blue-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">For Release</span>
                            </div>
                            <p class="text-sm text-gray-700">Approved with building permit number issued. Ready for pickup at Engineering Office.</p>
                        </div>

                        <div class="border-l-4 border-emerald-500 bg-emerald-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium">Completed</span>
                            </div>
                            <p class="text-sm text-gray-700">Permit released to applicant. Process complete. Record archived.</p>
                        </div>

                        <div class="border-l-4 border-red-500 bg-red-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                            </div>
                            <p class="text-sm text-gray-700">Application rejected. Applicant must resubmit with corrections.</p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <p class="text-sm text-yellow-800 flex items-start gap-2">
                            <span class="text-lg">!</span>
                            <span><strong>Note:</strong> Status update permissions are role-based. Only Engineers can update to For Assessment, Approved, Rejected, For Release, and Completed statuses.</span>
                        </p>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

       <!-- STAFF TASKS TAB -->
<div id="staff-tasks" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Staff Tasks & Workflows</h2>
                <p class="text-gray-600 mt-1">Detailed procedures for completing common staff responsibilities based on your role</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">9 Tasks</span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Task 1: Verify Ownership Documents (Step 1) -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                        <h3 class="font-semibold text-lg text-gray-800">Verify Ownership Documents (Step 1)</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Verify property ownership documents submitted by applicants. This step must be completed before document verification can begin.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">Role-based Verification Permissions:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                            <div class="bg-green-50 rounded-lg p-3 border-l-4 border-green-600">
                                <p class="font-medium text-green-700">CPDO</p>
                                <p class="text-sm text-green-600">Can verify TCT/Deed of Sale and SPA</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3 border-l-4 border-purple-600">
                                <p class="font-medium text-purple-700">Assessor</p>
                                <p class="text-sm text-purple-600">Can verify Tax Declaration</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3 border-l-4 border-orange-600">
                                <p class="font-medium text-orange-700">Treasurer</p>
                                <p class="text-sm text-orange-600">Can verify Current Tax Receipt and SPA</p>
                            </div>
                        </div>
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Open the application from Applications list</li>
                            <li>Scroll to <strong>"Step 1: Ownership Documents"</strong> section</li>
                            <li>Review each document you have permission to verify:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Check document authenticity and clarity</li>
                                    <li>Verify property details match application</li>
                                    <li>Check dates and notarization if required</li>
                                </ul>
                            </li>
                            <li>Click the <strong>checkbox</strong> next to the document to verify it</li>
                            <li>To request clarification: Click the <strong>Remark button</strong> and enter your comments</li>
                            <li>Remarks will be sent to applicant for response</li>
                            <li>All verification actions are logged in Activity History</li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <p class="text-sm text-green-800 font-semibold mb-2">Ownership Document Tips:</p>
                        <ul class="list-disc list-inside text-sm text-green-700 space-y-1">
                            <li>Check that property owner name matches applicant name (or SPA provided)</li>
                            <li>Verify tax declarations match current tax year</li>
                            <li>SPA must be notarized to be valid</li>
                            <li>Document remarks can be viewed by clicking "View all" next to the remark indicator</li>
                        </ul>
                    </div>


                </div>
            </div>

            <!-- Task 2: Document Verification (Step 2) -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                        <h3 class="font-semibold text-lg text-gray-800">Project Document Verification (Step 2)</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Review and verify project documents. <strong class="text-orange-600">Note: CPDO must approve first before Step 2 verification begins.</strong></p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">Role-based Document Verification Permissions:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-600">
                                <p class="font-medium text-blue-700">Engineers & Architects</p>
                                <p class="text-sm text-blue-600">Can verify all project documents</p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-3 border-l-4 border-gray-500">
                                <p class="font-medium text-gray-700">Other Roles</p>
                                <p class="text-sm text-gray-600">CPDO, Assessor, Treasurer, BFP, Mayor - View-only access</p>
                            </div>
                        </div>
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Open the application and ensure CPDO approval is complete (green notice visible)</li>
                            <li>Scroll to <strong>"Step 2: Project Documents"</strong> section</li>
                            <li>For each document:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Click <strong>"View"</strong> button to open the document</li>
                                    <li>Review document for completeness and accuracy</li>
                                    <li>Check signatures, dates, and required information</li>
                                </ul>
                            </li>
                            <li>Click <strong>"Verify"</strong> button for accepted documents</li>
                            <li>Add optional verification notes</li>
                            <li>To request missing documents: Click <strong>"Request Missing Documents"</strong> button</li>
                            <li>Select documents needed and add instructions</li>
                            <li>Applicant will be notified and can upload missing documents</li>
                        </ol>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <p class="text-sm text-blue-800 font-semibold mb-2">Document Categories:</p>
                        <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                            <li><strong>Application Forms</strong> - Building Permit Application forms</li>
                            <li><strong>Plans</strong> - Architectural, Structural, Electrical, Plumbing, Mechanical, Fencing plans</li>
                            <li><strong>Supporting Documents</strong> - Bill of Materials, Structural Analysis, Barangay Clearance, Valid ID, etc.</li>
                        </ul>
                    </div>


                </div>
            </div>

            <!-- Task 3: Hard Copy Receiving -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                        <h3 class="font-semibold text-lg text-gray-800">Hard Copy Receiving</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Only Engineers and Architects can mark hard copy documents as received.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Open the application details</li>
                            <li>Find <strong>"Hard Copy Received"</strong> checkbox in the Status Update card</li>
                            <li>Check the box when physical documents are received</li>
                            <li>A notice will appear indicating hard copy receipt status</li>
                            <li>Hard copy status is saved automatically and logged in activity history</li>
                        </ol>
                    </div>


                </div>
            </div>

            <!-- Task 4: Payment Assessment -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                        <h3 class="font-semibold text-lg text-gray-800">Payment Assessment (Treasurer Only)</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Manage and track payments for building permit applications.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Navigate to <strong>"Payment Assessments"</strong> section from sidebar</li>
                            <li>Filter applications by payment status (All/Paid/Unpaid/No Assessment)</li>
                            <li>Find the application needing payment tracking</li>
                            <li>View assessment amounts:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Building Permit Fee</li>
                                    <li>CPDO Fee</li>
                                    <li>Total Assessment</li>
                                </ul>
                            </li>
                            <li>If assessment is complete, click <strong>"Add Order Number"</strong> button</li>
                            <li>Enter Order Number, Payment Date, and optional notes</li>
                            <li>Save - Order number is recorded and visible to applicant</li>
                            <li>When applicant uploads payment proof (OR link), it appears in the table</li>
                            <li>Click <strong>"View OR"</strong> to verify payment receipt</li>
                            <li>Use <strong>"View Orders"</strong> button to see all payment orders for an application</li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <p class="text-sm text-green-800 font-semibold mb-2">Payment Tips:</p>
                        <ul class="list-disc list-inside text-sm text-green-700 space-y-1">
                            <li>Assessment must be complete (both fees calculated) before adding payment orders</li>
                            <li>Export reports using the "Export Report" button for accounting records</li>
                            <li>Summary cards show total assessments, pending OR uploads, and total collections</li>
                        </ul>
                    </div>


                </div>
            </div>

            <!-- Task 5: BFP Assessment -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">5</div>
                        <h3 class="font-semibold text-lg text-gray-800">BFP Fire Safety Assessment</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">BFP staff can upload FSEC documents and add fire safety comments.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Open application from list (BFP role sees BFP section)</li>
                            <li>Navigate to <strong>"Fire Safety Evaluation Clearance (FSEC)"</strong> section</li>
                            <li>Click <strong>"Upload FSEC"</strong> button to upload the certificate</li>
                            <li>Supported formats: PDF, JPG, PNG (max 10MB)</li>
                            <li>Add <strong>BFP Comments / Recommendations</strong> regarding fire safety compliance</li>
                            <li>Click <strong>"Save Comments"</strong> to save your assessment</li>
                            <li>View previously uploaded FSEC by clicking the link</li>
                            <li>Delete incorrect uploads using the Delete button</li>
                        </ol>
                    </div>


                </div>
            </div>

            <!-- Task 6: CPDO Assessment & Decision -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">6</div>
                        <h3 class="font-semibold text-lg text-gray-800">CPDO Assessment & Decision</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">CPDO staff can create fee assessments, upload certificates, and make final approval decisions.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">CPDO Responsibilities:</p>
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li><strong>Verify Ownership Documents:</strong> TCT/Deed of Sale and SPA (Step 1)</li>
                            <li><strong>Upload Certificates:</strong>
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Zoning Certificate (Google Drive link)</li>
                                    <li>Locational Clearance (Google Drive link)</li>
                                </ul>
                            </li>
                            <li><strong>Create Fee Assessment:</strong>
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Set Assessment Date</li>
                                    <li>Enter Zonal Location Fee, PALC Fee, Development Permit Fee, Alteration Permit Fee, Site/Zoning Certificate Fee</li>
                                    <li>Add additional fees as needed</li>
                                    <li>Add assessment notes and save</li>
                                </ul>
                            </li>
                            <li><strong>Make Final Decision:</strong>
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Select Approve or Reject</li>
                                    <li>Add remarks/reason (required for rejection)</li>
                                    <li><strong class="text-red-600">IMPORTANT: Decision is FINAL and cannot be changed!</strong></li>
                                    <li>Confirm decision in warning modal</li>
                                </ul>
                            </li>
                        </ol>
                    </div>

                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <p class="text-sm text-red-800"><strong>Critical:</strong> CPDO decisions are final and cannot be reversed. Double-check all documents and assessments before submitting.</p>
                    </div>


                </div>
            </div>

            <!-- Task 7: Engineer Assessment & Status Updates -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">7</div>
                        <h3 class="font-semibold text-lg text-gray-800">Engineer Assessment & Status Updates</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Engineers create building permit fee assessments and update application statuses.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li><strong>Create Building Permit Fee Assessment:</strong>
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>When status is ready, click "Save Assessment & Mark as For Assessment"</li>
                                    <li>Enter Line Grade, Building Fee, Sanitary Fee, Mechanical Fee, Electrical Fee, Penalties/Fines</li>
                                    <li>Add additional fees as needed</li>
                                    <li>Review summary and confirm</li>
                                </ul>
                            </li>
                            <li><strong>Update Application Status:</strong>
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Only Engineers can change to: For Assessment, Approved, Rejected, For Release, Completed</li>
                                    <li>When approving, set hard copy submission date and instructions</li>
                                    <li>When marking For Release, enter 10-digit Building Permit number</li>
                                    <li>Add remarks/notes for each status change</li>
                                </ul>
                            </li>
                        </ol>
                    </div>


                </div>
            </div>

            <!-- Task 8: Survey Management -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">8</div>
                        <h3 class="font-semibold text-lg text-gray-800">Survey & Feedback Review</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Monitor applicant satisfaction and service quality metrics (CPDO and Mayor can view CPDO-specific ratings).</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Go to <strong>"Surveys"</strong> section from sidebar</li>
                            <li>View two tabs:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li><strong>Satisfaction Surveys</strong> - General client feedback (all staff view)</li>
                                    <li><strong>CPDO Experience Ratings</strong> - CPDO-specific metrics (CPDO/Mayor only)</li>
                                </ul>
                            </li>
                            <li>Review statistics cards: total surveys, average rating, response rate</li>
                            <li>View satisfaction trend chart and rating distribution donut</li>
                            <li>Use filters to refine results by date range, client type, or sex</li>
                            <li>Click <strong>"View Details"</strong> on any survey to see complete response</li>
                            <li>Export survey data as CSV or PDF using export dropdown</li>
                        </ol>
                    </div>


                </div>
            </div>

            <!-- Task 9: Archive Applications -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">9</div>
                        <h3 class="font-semibold text-lg text-gray-800">Archive Applications</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Move completed applications to archive for better organization.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Open the completed application from Applications list</li>
                            <li>Click <strong>"Archive Application"</strong> button in the top right</li>
                            <li>Enter optional reason for archiving</li>
                            <li>Confirm - Application moves to Archived Applications</li>
                            <li>To restore: Go to <strong>"Archived Applications"</strong> section</li>
                            <li>Select applications and click <strong>"Restore Selected"</strong> or restore individually</li>
                        </ol>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADMIN TASKS TAB -->
<div id="admin-tasks" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Tasks & Administration</h2>
                <p class="text-gray-600 mt-1">Procedures for system administration and user management</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-purple-100 text-purple-800 px-3 py-1 rounded-full">Admin Only</span>
            </div>
        </div>

        <!-- Note about Admin Access -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-8">
            <p class="text-sm text-blue-800">Note: These features are only available to users with Administrator role.</p>
        </div>

        <div class="space-y-8">
            <!-- Task 1: User Management -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                        <h3 class="font-semibold text-lg text-gray-800">User Management</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Create, edit, and manage system users with role-based permissions.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">Adding a New User:</p>
                        <ol class="list-decimal list-inside space-y-2 text-gray-700">
                            <li>Go to "User Management" from admin sidebar</li>
                            <li>Click "New User" button</li>
                            <li>Fill in user details (name, email, phone, username)</li>
                            <li>Assign role:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li><strong>Admin</strong> - Full system access</li>
                                    <li><strong>Staff</strong> - Application processing access</li>
                                </ul>
                            </li>
                            <li>For Staff role, select position:
                                <ul class="list-disc list-inside ml-4 mt-2 text-gray-600">
                                    <li>Engineer, Architect, CPDO, Assessor, Treasurer, BFP, Mayor, Monitoring, Administrative Aide</li>
                                </ul>
                            </li>
                            <li>Generate temporary password (user must reset on first login)</li>
                            <li>Send credentials to user securely</li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <p class="text-sm text-green-800 font-semibold mb-2">Role Permissions Summary:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mt-2">
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></span><span><span class="font-medium">Engineers/Architects:</span> Full verification, status updates</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></span><span><span class="font-medium">CPDO:</span> Ownership docs, certificates, final approval</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-purple-500 mt-1.5"></span><span><span class="font-medium">Assessor:</span> Tax Declaration verification</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-orange-500 mt-1.5"></span><span><span class="font-medium">Treasurer:</span> Tax Receipt verification, payment orders</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-red-500 mt-1.5"></span><span><span class="font-medium">BFP:</span> FSEC upload, fire safety comments</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-gray-500 mt-1.5"></span><span><span class="font-medium">Monitoring:</span> Can update status, cannot verify documents</span></div>
                            <div class="flex items-start gap-2"><span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5"></span><span><span class="font-medium">Mayor/Admin Aide:</span> View applications, surveys</span></div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Task 2: System Settings -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                        <h3 class="font-semibold text-lg text-gray-800">System Settings Configuration</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Configure system-wide settings and view logs.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-sm transition">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <h4 class="font-semibold text-gray-800">System Logs</h4>
                            </div>
                            <p class="text-sm text-gray-600">View detailed system activity logs and user actions. Two tabs available:</p>
                            <ul class="list-disc list-inside text-sm text-gray-600 mt-2 ml-2">
                                <li><strong>System Logs</strong> - User logins/logouts, actions, IP addresses</li>
                                <li><strong>Application Review Logs</strong> - Document verifications, status changes</li>
                            </ul>
                            <p class="text-sm text-gray-600 mt-2">Export logs as Excel or PDF for audit purposes.</p>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-sm transition">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <h4 class="font-semibold text-gray-800">General Settings</h4>
                            </div>
                            <p class="text-sm text-gray-600">Configure system preferences including:</p>
                            <ul class="list-disc list-inside text-sm text-gray-600 mt-2 ml-2">
                                <li>System name and branding</li>
                                <li>Email notification settings</li>
                                <li>Application deadlines and fees</li>
                                <li>Document requirements</li>
                            </ul>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Task 3: Reports & Analytics -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                        <h3 class="font-semibold text-lg text-gray-800">Generate Reports & Analytics</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">Available Reports:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">Dashboard Export</p>
                                <p class="text-xs text-gray-500">Current metrics and charts snapshot</p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">Application Statistics</p>
                                <p class="text-xs text-gray-500">By status, date range, applicant type</p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">Payment Summary</p>
                                <p class="text-xs text-gray-500">Collection reports, pending payments</p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">Survey Analytics</p>
                                <p class="text-xs text-gray-500">Satisfaction trends, service quality metrics</p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">Staff Performance</p>
                                <p class="text-xs text-gray-500">Individual productivity and metrics</p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 bg-white hover:shadow-sm transition">
                                <p class="font-medium text-gray-800">System Logs</p>
                                <p class="text-xs text-gray-500">Audit trail of user actions</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">All reports can be exported as PDF, Excel, or CSV formats.</p>
                    </div>


                </div>
            </div>

            <!-- Task 4: Export Data -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="border-b border-gray-200 bg-white px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#155386] text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                        <h3 class="font-semibold text-lg text-gray-800">Export Data</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Export application data and reports from various sections.</p>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                        <p class="font-semibold text-gray-800 mb-3">Export Locations:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Dashboard</strong> - Click "Export Report" button at top</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Applications</strong> - Click "Export" dropdown (Excel or PDF)</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Archived Applications</strong> - Click "Export Report" button</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Payment Assessments</strong> - Click "Export Report" button</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Surveys</strong> - Use export dropdown (CSV or PDF)</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>Verified Ownership Documents</strong> - Click "Export to CSV" button</div>
                            <div class="flex items-center gap-2 text-sm text-gray-700"><span class="w-1.5 h-1.5 rounded-full bg-[#155386]"></span><strong>System Settings Logs</strong> - Use export dropdown (Excel or PDF)</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Exported files include current filters and pagination settings.</p>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ TAB -->
<div id="faq" class="tab-content hidden">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Frequently Asked Questions</h2>
                <p class="text-gray-600 mt-1">Quick answers to common questions about using the system</p>
            </div>
            <div class="hidden md:block">
                <span class="text-xs bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">12 FAQs</span>
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="flex flex-wrap gap-2 mb-6 pb-2 border-b border-gray-200">
            <button onclick="filterFaqs('all')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-[#155386] text-white transition">All Questions</button>
            <button onclick="filterFaqs('applications')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Applications</button>
            <button onclick="filterFaqs('verification')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Verification</button>
            <button onclick="filterFaqs('status')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Status & Updates</button>
            <button onclick="filterFaqs('reports')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Reports & Data</button>
            <button onclick="filterFaqs('account')" class="faq-filter-btn px-4 py-1.5 text-sm rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Account & Support</button>
        </div>

        <div class="space-y-3" id="faq-container">
            <!-- Category: Applications -->
            <div class="faq-item" data-category="applications">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>How do I search for a specific application?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">Go to the Applications section, use the search box at the top to search by application number or applicant name. You can also use the Status filter dropdown and Aging filter to refine results. The table auto-refreshes when you type.</p>
                </details>
            </div>

            <!-- Category: Verification -->
            <div class="faq-item" data-category="verification">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>What's the difference between Step 1 and Step 2 verification?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <div class="text-gray-700 mt-3 pl-6 space-y-2">
                        <p><strong class="text-teal-700">Step 1 (Ownership Documents):</strong> Verified by specific roles only. CPDO verifies TCT/Deed of Sale, Assessor verifies Tax Declaration, Treasurer verifies Current Tax Receipt. SPA can be verified by any of these three. Step 1 must be completed before moving to Step 2.</p>
                        <p><strong class="text-blue-700">Step 2 (Project Documents):</strong> CPDO must approve the application first. Only Engineers and Architects can verify documents. Other roles have view-only access. Document verification progress is saved locally and synced when saved.</p>
                    </div>
                </details>
            </div>

            <div class="faq-item" data-category="verification">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>What is the CPDO approval process?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <div class="text-gray-700 mt-3 pl-6">
                        <p class="mb-2">CPDO approval is required before Step 2 document verification can begin. The CPDO staff:</p>
                        <ol class="list-decimal list-inside space-y-1 ml-4">
                            <li>Verifies ownership documents (TCT and SPA)</li>
                            <li>Uploads Zoning Certificate and Locational Clearance (Google Drive links)</li>
                            <li>Creates fee assessment (Zonal Location, PALC, Development Permit, Alteration Permit, Zoning Certificate fees)</li>
                            <li>Makes final decision (Approve or Reject)</li>
                        </ol>
                        <p class="mt-2 text-red-600 font-medium">IMPORTANT: CPDO decisions are FINAL and cannot be changed after submission.</p>
                    </div>
                </details>
            </div>

            <div class="faq-item" data-category="verification">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>What should I do if an applicant has missing documents?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">Open the application, go to the Step 2: Project Documents section, and click "Request Missing Documents." Select which documents are needed, add a note explaining what's required, and submit. The applicant will receive an email notification and can upload the missing documents directly. For ownership documents, use the Remark button to request clarifications or updated copies.</p>
                </details>
            </div>

            <div class="faq-item" data-category="verification">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>How do hard copy submissions work?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">Hard copy documents are physical documents submitted to the Engineering Office. Only Engineers and Architects can mark hard copy as received. When an application is Approved, the staff member sets a submission date and instructions. The applicant brings their physical documents on the scheduled date, and staff checks the "Hard Copy Received" checkbox in the Status Update card. A green notice appears confirming receipt.</p>
                </details>
            </div>

            <!-- Category: Status & Updates -->
            <div class="faq-item" data-category="status">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>How do I update an application's status?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <div class="text-gray-700 mt-3 pl-6">
                        <p>Open the application details, scroll to the "Update Status" card on the right. Status options are role-restricted:</p>
                        <ul class="list-disc list-inside mt-2 ml-4 space-y-1">
                            <li><strong class="text-blue-600">Engineers</strong> can update to For Assessment, Approved, Rejected, For Release, Completed</li>
                            <li><strong class="text-gray-600">Other staff</strong> can update to Under Review and Document Verification</li>
                            <li><strong class="text-gray-600">Monitoring role</strong> can update to Under Review and Document Verification</li>
                        </ul>
                        <p class="mt-2">Click the radio button for the new status, add remarks/notes, and click "Update Status". The applicant will be automatically notified of status changes.</p>
                    </div>
                </details>
            </div>

            <div class="faq-item" data-category="status">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>How do I view activity history for an application?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">On the application details page, the Activity Log card in the right column shows the 3 most recent activities. Click "View Full History →" to see the complete timeline. The full history page includes filtering by activity type, pagination, and shows all actions with timestamps, reviewer names, and remarks.</p>
                </details>
            </div>

            <div class="faq-item" data-category="status">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>How do building permit numbers work?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">When an application is ready for release, the Engineer clicks "Update Status" to For Release. A modal appears requesting the 10-digit Building Permit number. Enter exactly 10 digits (numbers only), add optional remarks, and confirm. The permit number is recorded and displayed on the application. This completes the approval process.</p>
                </details>
            </div>

            <!-- Category: Reports & Data -->
            <div class="faq-item" data-category="reports">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>How do I export application data?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">In the Applications section, click the "Export" dropdown at the top right. Choose between Excel (.xlsx) or PDF format. The export includes your current filters (status, search, etc.) and all visible columns. The file will download to your computer and can be opened in spreadsheet applications.</p>
                </details>
            </div>

            <div class="faq-item" data-category="reports">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>What date ranges can I filter dashboard data by?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <div class="text-gray-700 mt-3 pl-6">
                        <p>The dashboard supports three view types:</p>
                        <ul class="list-disc list-inside mt-2 ml-4 space-y-1">
                            <li><strong>Daily</strong> - Select a specific date from the calendar picker</li>
                            <li><strong>Monthly</strong> - Select a month and year</li>
                            <li><strong>Yearly</strong> - Select a specific year</li>
                        </ul>
                        <p class="mt-2">Click the date selector at the top right, choose your view type, select the date/month/year, and click Apply. All charts and statistics will update to show data for that period. The dashboard auto-refreshes every 60 seconds.</p>
                    </div>
                </details>
            </div>

            <!-- Category: Account & Support -->
            <div class="faq-item" data-category="account">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636L9.172 14.828a4 4 0 105.656 5.656l7.556-8.21A5 5 0 0018.364 5.636z" />
                            </svg>
                            <span>How can I contact support if I need help?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <p class="text-gray-700 mt-3 pl-6">Use the Message feature from the sidebar to contact your system administrator. You can also check the system settings page for contact information and support email address. For urgent issues, contact your manager or IT support team directly.</p>
                </details>
            </div>

            <div class="faq-item" data-category="account">
                <details class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition group">
                    <summary class="font-semibold text-gray-800 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>How do I register for an account?</span>
                        </div>
                        <span class="transform transition-transform group-open:rotate-180 text-gray-400">▼</span>
                    </summary>
                    <div class="text-gray-700 mt-3 pl-6">
                        <p>Click "Sign Up" on the homepage or login page. Complete the 3-step registration form:</p>
                        <ol class="list-decimal list-inside mt-2 ml-4 space-y-1">
                            <li>Enter Personal Information (Name, Phone, Address)</li>
                            <li>Enter Account Details (Email, Username, Password)</li>
                            <li>Review information and submit</li>
                        </ol>
                        <p class="mt-2">After submission, check your email for a 6-digit verification code. Once verified, your account will be pending admin approval (1-2 business days).</p>
                    </div>
                </details>
            </div>
        </div>

        <!-- Still Need Help Section -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#155386] rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636L9.172 14.828a4 4 0 105.656 5.656l7.556-8.21A5 5 0 0018.364 5.636z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">Still have questions?</h3>
                        <p class="text-gray-600 text-sm">Can't find the answer you're looking for? Please contact our support team.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#0e3d5c] transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        support@konstructo.com
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterFaqs(category) {
    const items = document.querySelectorAll('.faq-item');
    const buttons = document.querySelectorAll('.faq-filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('bg-[#155386]', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-600');
    });
    
    const activeButton = event?.target;
    if (activeButton) {
        activeButton.classList.remove('bg-gray-100', 'text-gray-600');
        activeButton.classList.add('bg-[#155386]', 'text-white');
    } else {
        const allBtn = document.querySelector('.faq-filter-btn:first-child');
        if (allBtn) {
            allBtn.classList.remove('bg-gray-100', 'text-gray-600');
            allBtn.classList.add('bg-[#155386]', 'text-white');
        }
    }
    
    // Filter items
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
    </div>

    <!-- FOOTER HELP -->
    <div class="max-w-7xl mx-auto mt-8 bg-[#155386] text-white rounded-xl p-6">
        <div class="flex items-start gap-4">
            <svg class="w-6 h-6 min-w-6 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-semibold text-white mb-2">Need Additional Help?</h3>
                <p class="text-blue-100">If you can't find what you're looking for in this manual, please contact your system administrator via the Messages section or email your support team. For role-specific questions, reach out to your department head.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Detect before print event
window.addEventListener('beforeprint', function() {
    // Show all tabs when printing
    const allTabs = document.querySelectorAll('.tab-content');
    allTabs.forEach(tab => {
        tab.style.display = 'block !important';
        tab.style.visibility = 'visible !important';
        tab.style.opacity = '1 !important';
        tab.style.position = 'static !important';
        tab.style.height = 'auto !important';
        tab.style.width = '100% !important';
        tab.style.pageBreakAfter = 'always';
        tab.classList.remove('hidden');
    });
});

// Restore normal behavior after print
window.addEventListener('afterprint', function() {
    switchTab('getting-started');
});

function switchTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        btn.classList.remove('border-[#155386]', 'text-[#155386]');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Add active class to clicked button
    const activeButton = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.remove('border-transparent', 'text-gray-500');
        activeButton.classList.add('border-[#155386]', 'text-[#155386]');
    }

    // Smooth scroll to top of content
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Initialize first tab as active on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check URL hash for tab
    const hash = window.location.hash.substring(1);
    if (hash && ['getting-started', 'dashboard', 'applications', 'staff-tasks', 'admin-tasks', 'faq'].includes(hash)) {
        switchTab(hash);
    } else {
        const firstButton = document.querySelector('.tab-btn[data-tab="getting-started"]');
        if (firstButton) {
            firstButton.classList.remove('border-transparent', 'text-gray-500');
            firstButton.classList.add('border-[#155386]', 'text-[#155386]');
        }
    }
});

// Enhance details elements for FAQ with smooth animation
document.querySelectorAll('details').forEach(detail => {
    detail.addEventListener('toggle', function() {
        if (this.open) {
            this.classList.add('bg-blue-50', 'border-blue-200');
        } else {
            this.classList.remove('bg-blue-50', 'border-blue-200');
        }
    });
});

// Close other details when opening one (optional - uncomment if desired)
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'SUMMARY') {
        const openDetails = document.querySelectorAll('details[open]');
        openDetails.forEach(detail => {
            if (detail !== e.target.parentElement) {
                detail.removeAttribute('open');
                detail.classList.remove('bg-blue-50', 'border-blue-200');
            }
        });
    }
});
</script>

<style>
/* Smooth transitions for tab switching */
.tab-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Details/summary styling */
details {
    transition: all 0.2s ease;
}

details[open] {
    background-color: #eff6ff;
    border-color: #bfdbfe;
}

details[open] summary {
    margin-bottom: 1rem;
    border-bottom: 1px solid #bfdbfe;
    padding-bottom: 0.75rem;
    color: #155386;
    font-weight: 600;
}

/* Group hover transition for summary arrow */
.group-open\:rotate-180 {
    transition: transform 0.2s ease;
}

details[open] .group-open\:rotate-180 {
    transform: rotate(180deg);
}

/* Card hover effects */
.border-gray-200:hover {
    transition: all 0.2s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #155386;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #40798C;
}

/* KBD styling */
kbd {
    background-color: #f9fafb;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
    color: #1f2937;
    display: inline-block;
    font-family: monospace;
    font-size: 0.75rem;
    padding: 0.125rem 0.5rem;
}

/* ========== PRINT STYLES ========== */
@media print {
    /* Hide non-printable elements */
    .print-button,
    nav,
    .tab-btn,
    #manualTabs,
    .bg-blue-50.border.border-blue-200:last-of-type {
        display: none !important;
        visibility: hidden !important;
    }

    /* CRITICAL: Show ALL tab content during print - multiple override approaches */
    .tab-content {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        height: auto !important;
        width: 100% !important;
        page-break-after: always;
        clear: both;
    }

    /* Extra specific: force hidden tabs to show */
    div.tab-content.hidden {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        height: auto !important;
        width: 100% !important;
        page-break-after: always;
        clear: both;
    }

    /* Fallback: all elements with id starting with tab names */
    [id="getting-started"],
    [id="registration"],
    [id="dashboard"],
    [id="applications"],
    [id="staff-tasks"],
    [id="admin-tasks"],
    [id="faq"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        height: auto !important;
        width: 100% !important;
        page-break-after: always;
    }

    /* Avoid page break on last tab */
    .tab-content:last-of-type,
    [id="faq"] {
        page-break-after: avoid;
    }

    /* Page settings */
    body {
        margin: 0;
        padding: 10mm;
        background: white;
        color: #000;
    }

    html {
        margin: 0;
        padding: 0;
    }

    /* Remove shadows and effects for print */
    .shadow-sm,
    .shadow,
    .shadow-md,
    .shadow-lg,
    .hover\:shadow-lg,
    .hover\:shadow-md {
        box-shadow: none !important;
    }

    /* Keep borders visible */
    .border {
        border: 1px solid #999 !important;
    }

    /* Print-friendly colors */
    .bg-gradient-to-r,
    .bg-gradient-to-br,
    [class*="from-"],
    [class*="to-"] {
        background: white !important;
    }

    /* Colored boxes stay but with black text */
    .bg-blue-50,
    .bg-yellow-50,
    .bg-green-50,
    .bg-red-50,
    .bg-purple-50,
    .bg-gray-50,
    .bg-white {
        background: white !important;
        border: 1px solid #ccc !important;
    }

    /* Circle badges */
    .rounded-full {
        background-color: #f0f0f0 !important;
        color: #000 !important;
    }

    .w-8.h-8.rounded-full,
    .w-10.h-10.rounded-full,
    .w-12.h-12.rounded-full {
        background-color: #e0e0e0 !important;
        color: #000 !important;
    }

    /* Ensure text is readable */
    h1, h2, h3, h4, h5, h6 {
        color: #000 !important;
        page-break-after: avoid;
        orphans: 3;
        widows: 3;
    }

    h2 {
        border-top: 2px solid #000;
        padding-top: 0.5cm;
        margin-top: 1cm;
    }

    p, li, span {
        color: #000 !important;
    }

    p, li {
        orphans: 3;
        widows: 3;
    }

    /* Page break management */
    .space-y-6 > * {
        page-break-inside: avoid;
    }

    .space-y-8 > * {
        page-break-inside: avoid;
    }

    .grid {
        page-break-inside: avoid;
    }

    /* Remove background colors but keep structure */
    [class*="bg-"] {
        background: white !important;
        color: #000 !important;
    }

    /* Print-friendly images */
    img {
        max-width: 100%;
        page-break-inside: avoid;
        border: 1px solid #ccc;
    }

    /* Remove animations and transitions */
    * {
        animation: none !important;
        transition: none !important;
    }

    /* Remove hover states */
    button:hover,
    a:hover,
    div:hover {
        text-decoration: none !important;
        transform: none !important;
    }

    /* Heading sizes for print */
    .text-3xl {
        font-size: 20pt;
    }

    .text-2xl {
        font-size: 16pt;
    }

    .text-xl {
        font-size: 14pt;
    }

    .text-lg {
        font-size: 12pt;
    }

    .text-sm {
        font-size: 10pt;
    }

    .text-xs {
        font-size: 9pt;
    }

    /* Container width */
    .max-w-7xl {
        width: 100%;
        max-width: 100%;
    }

    .p-4, .p-6 {
        padding: 0.25in;
    }

    /* List formatting */
    ol, ul {
        margin-left: 20px;
    }

    /* Details elements (FAQ) */
    details {
        page-break-inside: avoid;
    }

    details[open] {
        background: white !important;
        border: 1px solid #ccc;
    }

    details summary {
        color: #000 !important;
    }

    /* Tables */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
        color: #000;
    }

    /* Remove scrollbar on print */
    ::-webkit-scrollbar {
        display: none;
    }

    /* Status badges and colored elements */
    [class*="bg-blue-"],
    [class*="bg-yellow-"],
    [class*="bg-green-"],
    [class*="bg-red-"],
    [class*="bg-purple-"],
    [class*="bg-orange-"],
    [class*="bg-amber-"],
    [class*="bg-teal-"],
    [class*="bg-indigo-"],
    [class*="bg-emerald-"] {
        background-color: white !important;
        border: 1px solid #999 !important;
        color: #000 !important;
    }

    /* Border colors */
    [class*="border-l-"] {
        border-left: 3px solid #000 !important;
    }

    [class*="border-t-"],
    [class*="border-b-"] {
        border-color: #999 !important;
    }
}

/* Print button styling */
.print-button {
    transition: all 0.2s ease;
}

.print-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(21, 83, 134, 0.3);
}
</style>
@endsection