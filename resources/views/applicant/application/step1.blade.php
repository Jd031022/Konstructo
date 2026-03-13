@extends('layouts.app')

@section('title', 'Application')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<!-- Back Button (Simple Version) -->
<div class="mb-8">
    <a href="javascript:history.back()" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
    </a>
</div>
    <!-- Limit Warning Banner (shown dynamically) -->
    <div id="limit-warning-container"></div>

    <!-- Application Number Banner - Hidden by default, only shows when app letter is downloaded -->
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
                
                <!-- Additional Tips -->
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
            <a href="/applicant/application/step2" 
               onclick="removeBeforeUnload()"
               class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Next Step: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
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
                    <button onclick="saveDraftAndContinue()" 
                            id="save-draft-btn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Yes, Save as Draft
                    </button>
                    
                    <button onclick="discardDraftAndContinue()" 
                            id="discard-draft-btn"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        No, Discard Progress
                    </button>
                    
                    <button onclick="closeDraftModal()" 
                            class="text-sm text-gray-500 hover:text-gray-700 mt-2">
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

<!-- JavaScript -->
<script>
    let applicationNumberGenerated = false;
    let downloadCount = 0;
    let totalFilesToDownload = 0;
    let pendingNavigationUrl = null;
    let currentDraftId = null;
    
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
        sessionStorage.removeItem('konstructo_current_app_number');
        sessionStorage.removeItem('konstructo_just_generated');
        localStorage.removeItem('konstructo_app_number');
        localStorage.removeItem('konstructo_last_app_number');
        localStorage.removeItem('konstructo_last_app_timestamp');
        console.log('Application storage cleared');
    }

    // Check if this is a new application from dashboard
    function isNewApplication() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('new') === 'true';
    }

    // Handle back button click
    function handleBackNavigation(event) {
        if (applicationNumberGenerated) {
            event.preventDefault();
            pendingNavigationUrl = '/applicant/applications';
            showDraftModal();
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
        if (applicationNumberGenerated) {
            e.preventDefault();
            e.returnValue = 'You have an unsaved application. Are you sure you want to leave?';
            return e.returnValue;
        }
    }

    // Save draft and continue
    async function saveDraftAndContinue() {
        const btn = document.getElementById('save-draft-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        btn.disabled = true;

        try {
            // The draft is already created when application number was generated
            closeDraftModal();
            
            const successModal = document.getElementById('success-modal');
            const successMessage = document.getElementById('success-modal-message');
            const okButton = successModal.querySelector('button');
            
            successMessage.textContent = 'Application saved as draft!';
            
            okButton.onclick = function() {
                closeSuccessModal();
                removeBeforeUnload();
                window.location.href = '/applicant/applications';
            };
            
            successModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                closeSuccessModal();
                removeBeforeUnload();
                window.location.href = '/applicant/applications';
            }, 2000);
        } catch (error) {
            console.error('Error saving draft:', error);
            showErrorModal('Failed to save draft. Please try again.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Discard draft and continue
    async function discardDraftAndContinue() {
        const btn = document.getElementById('discard-draft-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Discarding...
        `;
        btn.disabled = true;

        try {
            if (currentDraftId) {
                await fetch(`/applicant/applications/${currentDraftId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
            }
            
            clearApplicationStorage();
            closeDraftModal();
            removeBeforeUnload();
            
            if (pendingNavigationUrl) {
                window.location.href = pendingNavigationUrl;
            }
        } catch (error) {
            console.error('Error discarding draft:', error);
            closeDraftModal();
            removeBeforeUnload();
            if (pendingNavigationUrl) {
                window.location.href = pendingNavigationUrl;
            }
        }
    }

    // Check application limit before allowing new application
    async function checkApplicationLimit() {
        try {
            const response = await fetch('/applicant/application/limit-info', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const limitInfo = data.data;
                updateLimitDisplay(limitInfo);
                
                if (!limitInfo.can_apply) {
                    showErrorModal(`You have reached the maximum limit of ${limitInfo.limit} submitted applications. Please complete or delete existing applications before creating a new one.`);
                    
                    document.querySelectorAll('.form-checkbox').forEach(cb => {
                        cb.disabled = true;
                    });
                    
                    const downloadBtn = document.getElementById('download-btn');
                    downloadBtn.disabled = true;
                    downloadBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    
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

    // Update limit display
    function updateLimitDisplay(limitInfo) {
        const container = document.getElementById('limit-warning-container');
        
        const existingInfo = container.querySelector('.limit-info');
        if (existingInfo) {
            existingInfo.remove();
        }
        
        const infoDiv = document.createElement('div');
        infoDiv.className = 'limit-info mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm';
        infoDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>You have ${limitInfo.drafts} draft(s) and ${limitInfo.remaining} remaining slot(s) for submitted applications. You can have up to 3 pending applications.</span>
            </div>
        `;
        
        container.appendChild(infoDiv);
    }

    // Show limit warning banner
    function showLimitWarning(limitInfo) {
        const container = document.getElementById('limit-warning-container');
        
        const existingWarning = container.querySelector('.limit-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        const warningDiv = document.createElement('div');
        warningDiv.className = 'limit-warning mb-6';
        warningDiv.innerHTML = `
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold">Application Limit Reached</p>
                        <p class="text-sm">You have ${limitInfo.submitted} out of ${limitInfo.limit} submitted applications. Please complete or delete existing applications before creating a new one.</p>
                        <p class="text-xs mt-1">Note: Drafts (${limitInfo.drafts}) do not count toward your limit.</p>
                        <div class="mt-3">
                            <a href="/applicant/applications" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                View My Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(warningDiv);
    }

    // Check for existing applications on page load
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('DOM Content Loaded - Starting...');
        
        const isNew = isNewApplication();
        console.log('Is new application:', isNew);
        
        if (isNew) {
            clearApplicationStorage();
            applicationNumberGenerated = false;
            
            document.getElementById('application-number-banner').classList.add('hidden');
            
            const url = new URL(window.location);
            url.searchParams.delete('new');
            window.history.replaceState({}, '', url);
        }
        
        window.addEventListener('beforeunload', beforeUnloadHandler);
        
        await checkApplicationLimit();
        
        // Check if there's a draft in session storage from a previous session
        const savedAppNumber = sessionStorage.getItem('konstructo_current_app_number');
        if (savedAppNumber && !isNew) {
            document.getElementById('application-number').textContent = savedAppNumber;
            document.getElementById('application-number-banner').classList.remove('hidden');
            applicationNumberGenerated = true;
        }
        
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

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) count++;
        });
        
        document.getElementById('selected-count').textContent = `${count} form${count !== 1 ? 's' : ''} selected`;
        document.getElementById('download-count').textContent = count;
        
        const downloadBtn = document.getElementById('download-btn');
        downloadBtn.disabled = count === 0;
    }

    async function selectAllForms() {
        const canProceed = await checkApplicationLimit();
        if (!canProceed) return;
        
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        updateSelectedCount();
    }

    async function deselectAllForms() {
        const canProceed = await checkApplicationLimit();
        if (!canProceed) return;
        
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        updateSelectedCount();
    }

    async function showApplicationNumber() {
        const canProceed = await checkApplicationLimit();
        if (!canProceed) {
            return;
        }
        
        const appNumber = generateApplicationNumber();
        document.getElementById('application-number').textContent = appNumber;
        document.getElementById('application-number-banner').classList.remove('hidden');
        applicationNumberGenerated = true;
        
        try {
            const response = await fetch('/applicant/application/create-draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data.application_number) {
                sessionStorage.setItem('konstructo_current_app_number', data.data.application_number);
                currentDraftId = data.data.id;
                showSuccessModal('Application number generated successfully!');
            } else if (data.limit_reached) {
                showErrorModal(data.message);
                return;
            } else {
                sessionStorage.setItem('konstructo_current_app_number', appNumber);
                showSuccessModal('Application number generated successfully!');
            }
        } catch (error) {
            console.error('Error creating draft:', error);
            sessionStorage.setItem('konstructo_current_app_number', appNumber);
            showSuccessModal('Application number generated successfully!');
        }
    }

    function generateApplicationNumber() {
        const year = new Date().getFullYear();
        const randomDigits = Math.floor(100000 + Math.random() * 900000);
        return `${year}${randomDigits}`;
    }

    function copyApplicationNumber() {
        const appNumber = document.getElementById('application-number').textContent;
        navigator.clipboard.writeText(appNumber).then(() => {
            showSuccessModal('Application number copied to clipboard!');
        }).catch(() => {
            showErrorModal('Failed to copy application number.');
        });
    }

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
            showSuccessModal('All files downloaded successfully!');
        }
    }

    async function downloadSelectedForms() {
        const canProceed = await checkApplicationLimit();
        if (!canProceed) {
            return;
        }

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

        if (hasAppLetter) {
            await showApplicationNumber();
        }

        const downloadBtn = document.getElementById('download-btn');
        downloadBtn.disabled = true;
        
        totalFilesToDownload = selectedForms.length;
        downloadCount = 0;
        
        showSuccessModal(`Downloading ${selectedForms.length} file(s)...`);
        
        selectedForms.forEach((form, index) => {
            setTimeout(() => {
                downloadFile(form.file);
            }, index * 800);
        });
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

    function setupModals() {
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        const saveDraftModal = document.getElementById('save-draft-modal');
        
        if (errorModal) {
            errorModal.addEventListener('click', function(e) {
                if (e.target === errorModal) {
                    closeErrorModal();
                }
            });
        }
        
        if (successModal) {
            successModal.addEventListener('click', function(e) {
                if (e.target === successModal) {
                    closeSuccessModal();
                }
            });
        }

        if (saveDraftModal) {
            saveDraftModal.addEventListener('click', function(e) {
                if (e.target === saveDraftModal) {
                    closeDraftModal();
                }
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

    window.addEventListener('storage', function(e) {
        if (e.key === 'konstructo_logout') {
            clearApplicationStorage();
            window.location.href = '/login';
        }
    });
</script>

<style>
    #error-modal, #success-modal, #save-draft-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #error-modal .bg-white, #success-modal .bg-white, #save-draft-modal .bg-white {
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

    .form-checkbox {
        cursor: pointer;
    }

    .form-checkbox:checked + div {
        border-color: #155386;
    }
    
    #application-number-banner {
        animation: slideDown 0.5s ease-out;
    }
    
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

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
@endsection