@extends('layouts.app')

@section('title', 'Application')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button with improved navigation handler -->
    <div class="mb-8">
        <a href="javascript:void(0)" onclick="handleBackNavigation(event)" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group" id="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <!-- Application Stats Banner -->
    <div id="application-stats-banner" class="mb-6 hidden">
        <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-xl p-4 text-white">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Application Status</p>
                        <p class="text-lg font-semibold">
                            <span id="stats-drafts">0</span> Draft<span id="draft-plural">s</span> • 
                            <span id="stats-remaining">3</span> Slot<span id="slot-plural">s</span> Remaining
                        </p>
                    </div>
                </div>
                <a href="/applicant/applications" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm">
                    View My Applications
                </a>
            </div>
        </div>
    </div>

    <!-- Limit Warning Container -->
    <div id="limit-warning-container"></div>

    <!-- Application Number Banner -->
    <div id="application-number-banner" class="mb-6 hidden">
        <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Your Application Number</p>
                        <p class="text-2xl font-bold text-white font-mono" id="application-number">2025000001</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="copyApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Copy Number
                    </button>
                    <span class="text-white/60 text-sm">Keep this for reference</span>
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Important:</span> Use this application number when submitting hard copies to OBO and for all future correspondence.
            </div>
        </div>
    </div>

    <!-- Step Indicator - Step 1 -->
    <div class="mb-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 bg-[#155386] text-white rounded-full font-bold text-sm">1</div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Step 1: Download Forms</h2>
                <p class="text-l text-gray-600">Select the forms you need and download them. Fill them out and upload in the next step.</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-semibold text-[#155386]">Download Forms</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-medium text-gray-400">Upload Documents</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-medium text-gray-400">Review & Submit</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Instructions Card -->
        <div class="p-8 pt-10">
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    How to Complete Your Application
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Step-by-Step Guide:</h4>
                        <ol class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <span>Select the forms you need from the checklist below</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <span>Click "Download Selected" to download all chosen forms</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span>Print and fill out the forms completely using black ink</span>
                            </li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Important Reminders:</h4>
                        <ol class="space-y-3 text-sm text-gray-600" start="4">
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                <span>Sign the forms where required (blue ink preferred)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">5</span>
                                <span>Scan or take clear photos of the accomplished forms</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">6</span>
                                <span>Proceed to Step 2 to upload your completed forms</span>
                            </li>
                        </ol>
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-white/50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600 flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><span class="font-medium">Tip:</span> Your application number will appear after downloading the Application Letter. Keep it for reference throughout the process.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Forms Checklist Section -->
        <div class="p-8 pt-0">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Building Permit Forms Checklist</h2>
                <div class="flex items-center gap-3">
                    <button onclick="selectAllForms()" class="text-sm text-[#155386] hover:underline">Select All</button>
                    <span class="text-gray-300">|</span>
                    <button onclick="deselectAllForms()" class="text-sm text-gray-500 hover:underline">Deselect All</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <!-- Application Letter (Special - triggers application number) -->
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border-2 border-[#155386] hover:border-[#155386] transition relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-[#155386] text-white text-xs px-3 py-1 rounded-bl-lg font-medium">
                        Generates Application #
                    </div>
                    <input type="checkbox" id="form-appletter" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-appletter" class="font-medium text-gray-800 cursor-pointer">Application Letter</label>
                        <p class="text-xs text-gray-500">From the owner - Downloading this generates your application number</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Building Permit Application -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-building-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-building-permit" class="font-medium text-gray-800 cursor-pointer">Building Permit Application</label>
                        <p class="text-xs text-gray-500">Main application form</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Sign Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sign-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sign-permit" class="font-medium text-gray-800 cursor-pointer">Sign Permit Application</label>
                        <p class="text-xs text-gray-500">For signage and billboards</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Architectural Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-architectural-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-architectural-permit" class="font-medium text-gray-800 cursor-pointer">Architectural Permit</label>
                        <p class="text-xs text-gray-500">For architectural works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Mechanical Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-mechanical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-mechanical-permit" class="font-medium text-gray-800 cursor-pointer">Mechanical Permit</label>
                        <p class="text-xs text-gray-500">For mechanical installations</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Electrical Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electrical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electrical-permit" class="font-medium text-gray-800 cursor-pointer">Electrical Permit</label>
                        <p class="text-xs text-gray-500">For electrical works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Electronics Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electronics-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electronics-permit" class="font-medium text-gray-800 cursor-pointer">Electronics Permit</label>
                        <p class="text-xs text-gray-500">For electronics systems</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Sanitary/Plumbing Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sanitary-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sanitary-permit" class="font-medium text-gray-800 cursor-pointer">Sanitary/Plumbing Permit</label>
                        <p class="text-xs text-gray-500">For plumbing and sanitary works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Demolition Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-demolition-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-demolition-permit" class="font-medium text-gray-800 cursor-pointer">Demolition Permit</label>
                        <p class="text-xs text-gray-500">For demolition works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Civil/Structural Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-civil-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-civil-permit" class="font-medium text-gray-800 cursor-pointer">Civil/Structural Permit</label>
                        <p class="text-xs text-gray-500">For structural works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Fencing Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-fencing-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-fencing-permit" class="font-medium text-gray-800 cursor-pointer">Fencing Permit</label>
                        <p class="text-xs text-gray-500">For fencing construction</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
            </div>

            <!-- Download All Selected Button -->
            <div class="flex items-center justify-between p-6 bg-blue-50 rounded-xl border border-blue-200">
                <div>
                    <p class="font-medium text-gray-800" id="selected-count">0 forms selected</p>
                    <p class="text-sm text-gray-600">Download all selected forms as individual PDFs</p>
                </div>
                <button onclick="downloadSelectedForms()" 
                        id="download-btn"
                        class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Selected (<span id="download-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Next Step Button -->
        <div class="p-8 pt-0 flex justify-end">
            <a href="#" 
               id="next-step-btn"
               onclick="goToStep2(event)"
               class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Next Step: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

    </div>

</div>

<!-- Data Privacy Act Consent Modal -->
<div id="dpa-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-2xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="text-xl font-bold">Data Privacy Act Compliance</h3>
                    </div>
                </div>
                
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600">
                            <p class="text-sm text-blue-800 font-medium">Republic Act No. 10173 (Data Privacy Act of 2012)</p>
                        </div>
                        
                        <div class="space-y-3 text-gray-600 text-sm">
                            <p>In compliance with the Data Privacy Act of 2012 (Republic Act No. 10173), we would like to inform you that:</p>
                            
                            <ul class="list-disc list-inside space-y-2 ml-2">
                                <li>The personal information you provide in this application will be collected, processed, and stored solely for the purpose of processing your building permit application.</li>
                                <li>Your information may be shared with relevant government agencies and offices involved in the approval process.</li>
                                <li>We implement appropriate security measures to protect your personal data from unauthorized access, use, or disclosure.</li>
                                <li>You have the right to access, correct, and request deletion of your personal information, subject to legal and regulatory requirements.</li>
                                <li>Your data will be retained only for as long as necessary to fulfill the purpose of this application and comply with legal obligations.</li>
                            </ul>
                            
                            <p class="mt-4">By proceeding with this application, you acknowledge that you have read, understood, and agree to the collection, processing, and storage of your personal information in accordance with the Data Privacy Act of 2012.</p>
                            
                            <p class="mt-2 text-xs text-gray-500">For any concerns regarding your data privacy, please contact our Data Protection Officer at dpo@konstructo.gov.ph or (02) 1234-5678.</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button onclick="declineDPA()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm">Decline</button>
                    <button onclick="acceptDPA()" class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        I Agree & Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Draft Modal -->
<div id="save-draft-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Save as Draft?</h3>
                <p class="text-sm text-gray-600 mb-2">
                    You have started an application with number: <span id="draft-app-number" class="font-mono font-bold text-blue-600"></span>
                </p>
                <p class="text-sm text-gray-600 mb-6">
                    Would you like to save your progress as a draft? You can continue later from your applications page.
                </p>
                
                <div class="flex flex-col gap-3">
                    <button onclick="saveDraftAndContinue()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Yes, Save as Draft
                    </button>
                    
                    <button onclick="discardDraftAndContinue()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        No, Discard Progress
                    </button>
                    
                    <button onclick="closeDraftModal()" class="text-sm text-gray-500 hover:text-gray-700 mt-2">
                        Cancel, Stay on this page
                    </button>
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

<script>
    let applicationNumberGenerated = false;
    let downloadCount = 0;
    let totalFilesToDownload = 0;
    let pendingNavigationUrl = null;
    let currentApplicationId = null;
    let currentApplicationNumber = null;
    let limitInfo = null;
    let dpaAccepted = false;
    
    // Check if DPA has been accepted before
    function checkDPAStatus() {
        const dpaConsent = localStorage.getItem('dpa_consent');
        if (dpaConsent === 'accepted') {
            dpaAccepted = true;
            return true;
        }
        return false;
    }
    
    // Show DPA modal if not accepted
    function showDPAModalIfNeeded() {
        if (!checkDPAStatus()) {
            const dpaModal = document.getElementById('dpa-modal');
            dpaModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            return false;
        }
        return true;
    }
    
    // Accept DPA
    function acceptDPA() {
        localStorage.setItem('dpa_consent', 'accepted');
        dpaAccepted = true;
        
        const modal = document.getElementById('dpa-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        showSuccessModal('Thank you for your consent. You may now proceed with your application.');
        enableApplicationFeatures();
    }
    
    // Decline DPA
    function declineDPA() {
        localStorage.removeItem('dpa_consent');
        
        const modal = document.getElementById('dpa-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        showErrorModal('You must accept the Data Privacy Act terms to proceed with your application.');
        disableApplicationFeatures();
        
        setTimeout(() => {
            window.location.href = '/applicant/dashboard';
        }, 3000);
    }
    
    // Enable application features after DPA acceptance
    function enableApplicationFeatures() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => { cb.disabled = false; });
        
        const downloadBtn = document.getElementById('download-btn');
        if (downloadBtn) downloadBtn.disabled = false;
    }
    
    // Disable application features if DPA declined
    function disableApplicationFeatures() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => { cb.disabled = true; });
        
        const downloadBtn = document.getElementById('download-btn');
        if (downloadBtn) downloadBtn.disabled = true;
    }
    
    // Form checklist functionality
    const formCheckboxes = [
        { id: 'form-appletter', name: 'Application Letter', file: 'application-letter.pdf', isAppLetter: true },
        { id: 'form-building-permit', name: 'Building Permit Application', file: 'building-permit-application.pdf' },
        { id: 'form-sign-permit', name: 'Sign Permit Application', file: 'sign-permit-application.pdf' },
        { id: 'form-architectural-permit', name: 'Architectural Permit', file: 'architectural-permit.pdf' },
        { id: 'form-mechanical-permit', name: 'Mechanical Permit', file: 'mechanical-permit.pdf' },
        { id: 'form-electrical-permit', name: 'Electrical Permit', file: 'electrical-permit.pdf' },
        { id: 'form-electronics-permit', name: 'Electronics Permit', file: 'electronics-permit.pdf' },
        { id: 'form-sanitary-permit', name: 'Sanitary/Plumbing Permit', file: 'sanitary-plumbing-permit.pdf' },
        { id: 'form-demolition-permit', name: 'Demolition Permit', file: 'demolition-permit.pdf' },
        { id: 'form-civil-permit', name: 'Civil/Structural Permit', file: 'civil-structural-permit.pdf' },
        { id: 'form-fencing-permit', name: 'Fencing Permit', file: 'fencing-permit.pdf' }
    ];

    // Clear all application storage
    function clearApplicationStorage() {
        sessionStorage.removeItem('konstructo_current_app_id');
        sessionStorage.removeItem('konstructo_current_app_number');
        localStorage.removeItem('konstructo_app_number');
        localStorage.removeItem('konstructo_last_app_id');
        console.log('Application storage cleared');
    }

    // Check if this is a new application from dashboard
    function isNewApplication() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('new') === 'true';
    }

    // Handle back button click
    function handleBackNavigation(event) {
        event.preventDefault();
        if (applicationNumberGenerated && currentApplicationId) {
            pendingNavigationUrl = '/applicant/applications';
            showDraftModal();
        } else {
            window.history.back();
        }
    }

    // Show draft modal
    function showDraftModal() {
        const appNumber = document.getElementById('application-number').textContent;
        document.getElementById('draft-app-number').textContent = appNumber;
        document.getElementById('save-draft-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close draft modal
    function closeDraftModal() {
        document.getElementById('save-draft-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        pendingNavigationUrl = null;
    }

    // Remove beforeunload listener
    function removeBeforeUnload() {
        window.removeEventListener('beforeunload', beforeUnloadHandler);
    }

    // Before unload handler
    function beforeUnloadHandler(e) {
        if (applicationNumberGenerated && currentApplicationId) {
            e.preventDefault();
            e.returnValue = 'You have an unsaved application. Are you sure you want to leave?';
            return e.returnValue;
        }
    }

    // Save draft and continue
    async function saveDraftAndContinue() {
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg> Saving...`;
        btn.disabled = true;

        try {
            closeDraftModal();
            
            showSuccessModal('Application saved as draft!');
            
            setTimeout(() => {
                closeSuccessModal();
                removeBeforeUnload();
                if (pendingNavigationUrl) {
                    window.location.href = pendingNavigationUrl;
                } else {
                    window.location.href = '/applicant/applications';
                }
            }, 1500);
            
        } catch (error) {
            console.error('Error saving draft:', error);
            showErrorModal('Failed to save draft. Please try again.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Discard draft and continue
    async function discardDraftAndContinue() {
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg> Discarding...`;
        btn.disabled = true;

        try {
            if (currentApplicationId) {
                await fetch(`/applicant/applications/${currentApplicationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).catch(err => console.log('Delete failed:', err));
            }
            
            clearApplicationStorage();
            applicationNumberGenerated = false;
            closeDraftModal();
            removeBeforeUnload();
            
            window.location.href = pendingNavigationUrl || '/applicant/applications';
            
        } catch (error) {
            console.error('Error discarding draft:', error);
            closeDraftModal();
            removeBeforeUnload();
            window.location.href = '/applicant/applications';
        }
    }

    // Update stats display
    function updateStatsDisplay(info) {
        const statsBanner = document.getElementById('application-stats-banner');
        statsBanner.classList.remove('hidden');
        
        document.getElementById('stats-drafts').textContent = info.drafts;
        document.getElementById('stats-remaining').textContent = info.remaining;
        
        document.getElementById('draft-plural').textContent = info.drafts === 1 ? '' : 's';
        document.getElementById('slot-plural').textContent = info.remaining === 1 ? '' : 's';
    }

    // Check application limit
    async function checkApplicationLimit() {
        try {
            const response = await fetch('/applicant/application/limit-info', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            
            const data = await response.json();
            
            if (data.success) {
                limitInfo = data.data;
                updateStatsDisplay(limitInfo);
                
                if (!limitInfo.can_apply) {
                    showErrorModal(`You have reached the maximum limit of ${limitInfo.limit} submitted applications.`);
                    disableApplicationFeatures();
                    showLimitWarning(limitInfo);
                    return false;
                }
                return true;
            }
            return true;
        } catch (error) {
            console.error('Error checking application limit:', error);
            return true;
        }
    }

    // Show limit warning
    function showLimitWarning(limitInfo) {
        const container = document.getElementById('limit-warning-container');
        container.innerHTML = `
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold">Application Limit Reached</p>
                        <p class="text-sm">You have ${limitInfo.submitted} out of ${limitInfo.limit} submitted applications.</p>
                        <div class="mt-3">
                            <a href="/applicant/applications" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">View My Applications</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
// Go to Step 2
async function goToStep2(event) {
    event.preventDefault();
    
    if (!applicationNumberGenerated || !currentApplicationId) {
        showErrorModal('Please download the Application Letter first to generate your application number.');
        return;
    }
    
    // Save the application ID to session storage before navigating
    sessionStorage.setItem('konstructo_current_app_id', currentApplicationId);
    sessionStorage.setItem('konstructo_current_app_number', currentApplicationNumber);
    
    // Remove beforeunload listener
    removeBeforeUnload();
    
    // Navigate to Step 2 with application ID
    window.location.href = `/applicant/application/step2?id=${currentApplicationId}`;
}

// Generate and save application number
async function generateAndSaveApplicationNumber() {
    if (applicationNumberGenerated) return currentApplicationNumber;
    
    try {
        // Create draft application first
        const draftResponse = await fetch('/applicant/application/create-draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const draftData = await draftResponse.json();
        
        if (draftData.success && draftData.data.id) {
            currentApplicationId = draftData.data.id;
            
            // Generate application number - using the correct route
            const generateResponse = await fetch('/applicant/application/generate-number', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ application_id: currentApplicationId })
            });
            
            const generateData = await generateResponse.json();
            
            if (generateData.success && generateData.data.application_number) {
                currentApplicationNumber = generateData.data.application_number;
                applicationNumberGenerated = true;
                
                // Update display
                document.getElementById('application-number').textContent = currentApplicationNumber;
                document.getElementById('application-number-banner').classList.remove('hidden');
                
                // Save to session storage
                sessionStorage.setItem('konstructo_current_app_id', currentApplicationId);
                sessionStorage.setItem('konstructo_current_app_number', currentApplicationNumber);
                
                return currentApplicationNumber;
            } else {
                throw new Error(generateData.message || 'Failed to generate application number');
            }
        } else if (draftData.limit_reached) {
            showErrorModal(draftData.message);
            return null;
        } else {
            throw new Error(draftData.message || 'Failed to create draft');
        }
    } catch (error) {
        console.error('Error generating application number:', error);
        showErrorModal('Failed to generate application number: ' + error.message);
        return null;
    }
}

    // Download file
    function downloadFile(filename) {
        const link = document.createElement('a');
        const timestamp = new Date().getTime();
        link.href = `/downloads/${filename}?t=${timestamp}`;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        downloadCount++;
        
        if (downloadCount === totalFilesToDownload) {
            downloadCount = 0;
            totalFilesToDownload = 0;
            document.getElementById('download-btn').disabled = false;
        }
    }

    // Download selected forms
    async function downloadSelectedForms() {
        if (!dpaAccepted) {
            showErrorModal('Please accept the Data Privacy Act terms first.');
            return;
        }
        
        const canProceed = await checkApplicationLimit();
        if (!canProceed) return;

        const selectedForms = [];
        const checkboxes = document.querySelectorAll('.form-checkbox');
        let hasAppLetter = false;
        
        checkboxes.forEach((cb, index) => {
            if (cb.checked) {
                selectedForms.push(formCheckboxes[index]);
                if (formCheckboxes[index].isAppLetter) {
                    hasAppLetter = true;
                }
            }
        });

        if (selectedForms.length === 0) {
            showErrorModal('Please select at least one form to download.');
            return;
        }

        // If Application Letter is selected, generate application number
        if (hasAppLetter && !applicationNumberGenerated) {
            const appNumber = await generateAndSaveApplicationNumber();
            if (!appNumber) {
                return;
            }
        }

        const downloadBtn = document.getElementById('download-btn');
        downloadBtn.disabled = true;
        
        totalFilesToDownload = selectedForms.length;
        downloadCount = 0;
        
        showSuccessModal(`Downloading ${selectedForms.length} file(s)...`);
        
        selectedForms.forEach((form, index) => {
            setTimeout(() => {
                downloadFile(form.file);
                
                if (downloadCount === totalFilesToDownload) {
                    setTimeout(() => {
                        showSuccessModal('All files downloaded successfully!');
                    }, 500);
                }
            }, index * 800);
        });
    }

    // Select all forms
    async function selectAllForms() {
        if (!dpaAccepted) {
            showErrorModal('Please accept the Data Privacy Act terms first.');
            return;
        }
        
        const canProceed = await checkApplicationLimit();
        if (!canProceed) return;
        
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => { cb.checked = true; });
        updateSelectedCount();
    }

    // Deselect all forms
    async function deselectAllForms() {
        if (!dpaAccepted) {
            showErrorModal('Please accept the Data Privacy Act terms first.');
            return;
        }
        
        const canProceed = await checkApplicationLimit();
        if (!canProceed) return;
        
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    }

    // Update selected count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        let count = 0;
        checkboxes.forEach(cb => { if (cb.checked) count++; });
        
        document.getElementById('selected-count').textContent = `${count} form${count !== 1 ? 's' : ''} selected`;
        document.getElementById('download-count').textContent = count;
        
        const downloadBtn = document.getElementById('download-btn');
        downloadBtn.disabled = count === 0;
    }

    // Copy application number
    function copyApplicationNumber() {
        const appNumber = document.getElementById('application-number').textContent;
        navigator.clipboard.writeText(appNumber).then(() => {
            showSuccessModal('Application number copied to clipboard!');
        }).catch(() => {
            showErrorModal('Failed to copy application number.');
        });
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
        const saveDraftModal = document.getElementById('save-draft-modal');
        const dpaModal = document.getElementById('dpa-modal');
        
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

        if (saveDraftModal) {
            saveDraftModal.addEventListener('click', function(e) {
                if (e.target === saveDraftModal) closeDraftModal();
            });
        }
        
        if (dpaModal) {
            dpaModal.addEventListener('click', function(e) {
                if (e.target === dpaModal) {}
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeErrorModal();
                closeSuccessModal();
                closeDraftModal();
            }
        });
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('DOM Content Loaded - Starting...');
        
        // Check DPA status
        const canProceed = showDPAModalIfNeeded();
        
        // Check for existing application in session storage
        const savedAppId = sessionStorage.getItem('konstructo_current_app_id');
        const savedAppNumber = sessionStorage.getItem('konstructo_current_app_number');
        
        if (savedAppId && savedAppNumber && !isNewApplication()) {
            currentApplicationId = savedAppId;
            currentApplicationNumber = savedAppNumber;
            applicationNumberGenerated = true;
            
            document.getElementById('application-number').textContent = savedAppNumber;
            document.getElementById('application-number-banner').classList.remove('hidden');
        }
        
        window.addEventListener('beforeunload', beforeUnloadHandler);
        
        await checkApplicationLimit();
        
        initializePage();
    });

    function initializePage() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        updateSelectedCount();
        setupModals();
    }

    window.addEventListener('storage', function(e) {
        if (e.key === 'konstructo_logout') {
            clearApplicationStorage();
            window.location.href = '/login';
        }
    });
</script>

<style>
    #error-modal, #success-modal, #save-draft-modal, #dpa-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #error-modal .bg-white, #success-modal .bg-white, #save-draft-modal .bg-white, #dpa-modal .bg-white {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .form-checkbox { cursor: pointer; }
    
    #application-number-banner { animation: slideDown 0.5s ease-out; }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin { animation: spin 1s linear infinite; }
    button:disabled { cursor: not-allowed; opacity: 0.7; }
</style>
@endsection