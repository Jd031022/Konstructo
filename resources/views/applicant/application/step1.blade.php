@extends('layouts.app')

@section('title', 'Application - Step 1')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
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

    <!-- Step Indicator -->
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
                                <span>For Application Letter, click "Edit PDF" to type directly on the PDF</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span>Click anywhere to add text, click text to edit, drag to move</span>
                            </li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">PDF Editor Controls:</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">✏️</span>
                                <span>Click anywhere to add new text box</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">🖱️</span>
                                <span>Click text to edit directly, drag to reposition</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold">⌨️</span>
                                <span>Press Enter to finish editing, ESC to cancel</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-white/50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600 flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><span class="font-medium">Tip:</span> Click "Edit PDF" to open the editor. Click anywhere to add text, then click the text to start typing directly!</span>
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
                <!-- Application Letter (Special - with PDF editor) -->
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border-2 border-[#155386] hover:border-[#155386] transition relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-[#155386] text-white text-xs px-3 py-1 rounded-bl-lg font-medium">
                        Interactive PDF
                    </div>
                    <input type="checkbox" id="form-appletter" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-appletter" class="font-medium text-gray-800 cursor-pointer">Application Letter</label>
                        <p class="text-xs text-gray-500">Click "Edit PDF" to type directly on the document</p>
                        <div class="mt-2 flex items-center gap-3">
                            <button type="button" onclick="openPdfEditor()" class="inline-flex items-center text-sm bg-[#155386] text-white px-3 py-1.5 rounded-lg hover:bg-[#1F363D] transition group">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit PDF
                            </button>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">PDF Form</span>
                </div>

                <!-- Other Forms -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-building-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-building-permit" class="font-medium text-gray-800 cursor-pointer">Building Permit Application</label>
                        <p class="text-xs text-gray-500">Main application form</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sign-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sign-permit" class="font-medium text-gray-800 cursor-pointer">Sign Permit Application</label>
                        <p class="text-xs text-gray-500">For signage and billboards</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-architectural-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-architectural-permit" class="font-medium text-gray-800 cursor-pointer">Architectural Permit</label>
                        <p class="text-xs text-gray-500">For architectural works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-mechanical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-mechanical-permit" class="font-medium text-gray-800 cursor-pointer">Mechanical Permit</label>
                        <p class="text-xs text-gray-500">For mechanical installations</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electrical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electrical-permit" class="font-medium text-gray-800 cursor-pointer">Electrical Permit</label>
                        <p class="text-xs text-gray-500">For electrical works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electronics-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electronics-permit" class="font-medium text-gray-800 cursor-pointer">Electronics Permit</label>
                        <p class="text-xs text-gray-500">For electronics systems</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sanitary-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sanitary-permit" class="font-medium text-gray-800 cursor-pointer">Sanitary/Plumbing Permit</label>
                        <p class="text-xs text-gray-500">For plumbing and sanitary works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-demolition-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-demolition-permit" class="font-medium text-gray-800 cursor-pointer">Demolition Permit</label>
                        <p class="text-xs text-gray-500">For demolition works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-civil-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-civil-permit" class="font-medium text-gray-800 cursor-pointer">Civil/Structural Permit</label>
                        <p class="text-xs text-gray-500">For structural works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

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

<!-- All Modals (same as before, keep them) -->
<div id="dpa-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4 py-8 hidden" style="backdrop-filter: blur(4px);">
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

<!-- PDF Editor Modal -->
<div id="pdf-editor-modal" class="fixed inset-0 bg-black bg-opacity-95 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative w-full h-full flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] text-white px-6 py-4 flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h3 class="text-xl font-bold">PDF Editor - Click anywhere to add text</h3>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="saveEditedPDF()" id="save-pdf-btn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Save & Download PDF
                </button>
                <button onclick="closePdfEditor()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    Close
                </button>
            </div>
        </div>
        
        <!-- PDF Viewer Container -->
        <div class="flex-1 overflow-auto bg-gray-800 p-4 relative" id="pdf-editor-container">
            <div id="pdf-viewer-wrapper" class="relative mx-auto" style="width: fit-content;">
                <iframe id="pdf-iframe" src="/downloads/application-letter.pdf" class="bg-white shadow-2xl" style="width: 800px; height: 1100px; border: none;"></iframe>
                <div id="text-overlay" class="absolute top-0 left-0 w-full h-full" style="pointer-events: auto;"></div>
            </div>
        </div>
        
        <!-- Toolbar -->
        <div class="bg-gray-900 text-white px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="setTextColor('black')" class="px-3 py-1 bg-black rounded hover:bg-gray-700 text-sm">Black</button>
                <button onclick="setTextColor('blue')" class="px-3 py-1 bg-blue-600 rounded hover:bg-blue-700 text-sm">Blue</button>
                <button onclick="setTextColor('red')" class="px-3 py-1 bg-red-600 rounded hover:bg-red-700 text-sm">Red</button>
                <div class="w-px h-6 bg-gray-600"></div>
                <button onclick="setFontSize(12)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 text-sm">12px</button>
                <button onclick="setFontSize(14)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 text-sm">14px</button>
                <button onclick="setFontSize(16)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 text-sm">16px</button>
                <button onclick="setFontSize(18)" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 text-sm">18px</button>
                <div class="w-px h-6 bg-gray-600"></div>
                <button onclick="deleteSelectedText()" id="delete-text-btn" class="px-3 py-1 bg-red-600 rounded hover:bg-red-700 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Selected
                </button>
                <button onclick="clearAllText()" class="px-3 py-1 bg-orange-600 rounded hover:bg-orange-700 text-sm">Clear All</button>
            </div>
            <div class="text-sm text-gray-400">
                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg> Click to add text</span>
                <span class="inline-flex items-center gap-1 ml-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Drag to reposition</span>
                <span class="inline-flex items-center gap-1 ml-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Click text to edit directly</span>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize variables
let textElements = [];
let editingTextId = null;
let editingInput = null;
let currentTextColor = '#000000';
let currentFontSize = 14;
let selectedTextId = null;

let applicationNumberGenerated = false;
let downloadCount = 0;
let totalFilesToDownload = 0;
let pendingNavigationUrl = null;
let currentApplicationId = null;
let currentApplicationNumber = null;
let limitInfo = null;
let dpaAccepted = false;

const formCheckboxes = [
    { id: 'form-appletter', name: 'Application Letter', file: 'application-letter.pdf', isAppLetter: true, isInteractive: true },
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

// DPA Functions
function checkDPAStatus() {
    const dpaConsent = localStorage.getItem('dpa_consent');
    if (dpaConsent === 'accepted') {
        dpaAccepted = true;
        return true;
    }
    return false;
}

function showDPAModalIfNeeded() {
    if (!checkDPAStatus()) {
        const dpaModal = document.getElementById('dpa-modal');
        if (dpaModal) {
            dpaModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        return false;
    }
    return true;
}

function acceptDPA() {
    localStorage.setItem('dpa_consent', 'accepted');
    dpaAccepted = true;
    const modal = document.getElementById('dpa-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    showSuccessModal('Thank you for your consent. You may now proceed with your application.');
}

function declineDPA() {
    localStorage.removeItem('dpa_consent');
    const modal = document.getElementById('dpa-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    showErrorModal('You must accept the Data Privacy Act terms to proceed with your application.');
    setTimeout(() => {
        window.location.href = '/applicant/dashboard';
    }, 3000);
}

// Application Functions
async function generateAndSaveApplicationNumber() {
    if (applicationNumberGenerated) return currentApplicationNumber;
    
    try {
        const draftResponse = await fetch('/applicant/application/create-draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const draftData = await draftResponse.json();
        
        if (draftData.success && draftData.data.id) {
            currentApplicationId = draftData.data.id;
            
            const generateResponse = await fetch('/applicant/application/generate-number', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ application_id: currentApplicationId })
            });
            
            const generateData = await generateResponse.json();
            
            if (generateData.success && generateData.data.application_number) {
                currentApplicationNumber = generateData.data.application_number;
                applicationNumberGenerated = true;
                
                const appNumberEl = document.getElementById('application-number');
                const bannerEl = document.getElementById('application-number-banner');
                if (appNumberEl) appNumberEl.textContent = currentApplicationNumber;
                if (bannerEl) bannerEl.classList.remove('hidden');
                
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

// PDF Editor Functions
async function openPdfEditor() {
    if (!applicationNumberGenerated) {
        const appNumber = await generateAndSaveApplicationNumber();
        if (!appNumber) {
            return;
        }
    }
    
    const modal = document.getElementById('pdf-editor-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    loadSavedTextElements();
    refreshTextOverlay();
}

function refreshTextOverlay() {
    const overlay = document.getElementById('text-overlay');
    if (!overlay) return;
    
    overlay.innerHTML = '';
    
    textElements.forEach((text) => {
        addTextElementToOverlay(text);
    });
}

function addTextElementToOverlay(text) {
    const overlay = document.getElementById('text-overlay');
    if (!overlay) return;
    
    const div = document.createElement('div');
    div.className = 'pdf-text-element';
    div.textContent = text.content;
    div.style.position = 'absolute';
    div.style.left = text.x + 'px';
    div.style.top = text.y + 'px';
    div.style.color = text.color;
    div.style.fontSize = text.fontSize + 'px';
    div.style.fontFamily = 'Arial, sans-serif';
    div.style.cursor = 'move';
    div.style.padding = '2px 4px';
    div.style.backgroundColor = 'transparent';
    div.style.border = text.id === selectedTextId ? '1px solid #0066ff' : '1px solid transparent';
    div.style.borderRadius = '2px';
    div.style.whiteSpace = 'nowrap';
    div.style.pointerEvents = 'auto';
    div.style.zIndex = '1000';
    div.setAttribute('data-id', text.id);
    
    // Click to select and edit
    div.addEventListener('click', (e) => {
        e.stopPropagation();
        selectedTextId = text.id;
        refreshTextOverlay();
        startEditing(text.id);
    });
    
    // Drag functionality
    let isDragging = false;
    let startX, startY, startLeft, startTop;
    
    div.addEventListener('mousedown', (e) => {
        if (e.target === div && !editingInput) {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            startLeft = parseInt(div.style.left);
            startTop = parseInt(div.style.top);
            div.style.cursor = 'grabbing';
            div.style.opacity = '0.7';
            e.preventDefault();
        }
    });
    
    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        const newLeft = startLeft + dx;
        const newTop = startTop + dy;
        
        div.style.left = newLeft + 'px';
        div.style.top = newTop + 'px';
        
        const textElement = textElements.find(t => t.id == text.id);
        if (textElement) {
            textElement.x = newLeft;
            textElement.y = newTop;
        }
    });
    
    document.addEventListener('mouseup', () => {
        if (isDragging) {
            isDragging = false;
            div.style.cursor = 'move';
            div.style.opacity = '1';
            saveTextElements();
        }
    });
    
    overlay.appendChild(div);
}

function startEditing(id) {
    if (editingInput) {
        finishEditing();
    }
    
    const text = textElements.find(t => t.id == id);
    if (!text) return;
    
    const div = document.querySelector(`.pdf-text-element[data-id="${id}"]`);
    if (!div) return;
    
    editingTextId = id;
    
    const input = document.createElement('input');
    input.type = 'text';
    input.value = text.content;
    input.style.position = 'absolute';
    input.style.left = div.style.left;
    input.style.top = div.style.top;
    input.style.width = Math.max(100, div.offsetWidth) + 'px';
    input.style.fontSize = text.fontSize + 'px';
    input.style.fontFamily = 'Arial, sans-serif';
    input.style.color = text.color;
    input.style.padding = '2px 4px';
    input.style.border = '2px solid #0066ff';
    input.style.borderRadius = '2px';
    input.style.backgroundColor = 'white';
    input.style.zIndex = '1001';
    input.style.outline = 'none';
    
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            finishEditing(input.value);
        } else if (e.key === 'Escape') {
            finishEditing(null);
        }
    });
    
    input.addEventListener('blur', () => {
        finishEditing(input.value);
    });
    
    div.style.display = 'none';
    
    const overlay = document.getElementById('text-overlay');
    overlay.appendChild(input);
    editingInput = input;
    input.focus();
    input.select();
}

function finishEditing(newValue) {
    if (editingInput) {
        const text = textElements.find(t => t.id == editingTextId);
        if (text && newValue !== null && newValue !== undefined && newValue.trim() !== '') {
            text.content = newValue;
        } else if (newValue === '' || newValue === null) {
            textElements = textElements.filter(t => t.id != editingTextId);
            if (selectedTextId == editingTextId) selectedTextId = null;
        }
        
        editingInput.remove();
        editingInput = null;
        
        const div = document.querySelector(`.pdf-text-element[data-id="${editingTextId}"]`);
        if (div) {
            div.style.display = 'block';
            if (text) {
                div.textContent = text.content;
            } else {
                div.remove();
            }
        }
        
        editingTextId = null;
        saveTextElements();
        refreshTextOverlay();
    }
}

function createNewTextAtPosition(e) {
    const overlay = document.getElementById('text-overlay');
    if (!overlay) return;
    
    const rect = overlay.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const newText = {
        id: Date.now(),
        content: 'Click to edit',
        x: x,
        y: y,
        color: currentTextColor,
        fontSize: currentFontSize
    };
    
    textElements.push(newText);
    saveTextElements();
    refreshTextOverlay();
    
    setTimeout(() => {
        startEditing(newText.id);
    }, 50);
}

function deleteSelectedText() {
    if (selectedTextId) {
        textElements = textElements.filter(t => t.id != selectedTextId);
        selectedTextId = null;
        if (editingInput) finishEditing(null);
        saveTextElements();
        refreshTextOverlay();
        showSuccessModal('Text deleted');
    } else {
        showErrorModal('Click on text first to select it');
    }
}

function clearAllText() {
    if (confirm('Are you sure you want to clear all text?')) {
        textElements = [];
        selectedTextId = null;
        if (editingInput) finishEditing(null);
        saveTextElements();
        refreshTextOverlay();
        showSuccessModal('All text cleared');
    }
}

function setTextColor(color) {
    currentTextColor = color;
    if (selectedTextId) {
        const text = textElements.find(t => t.id == selectedTextId);
        if (text) {
            text.color = color;
            saveTextElements();
            refreshTextOverlay();
            if (editingTextId == selectedTextId) {
                setTimeout(() => {
                    startEditing(selectedTextId);
                }, 50);
            }
        }
    }
}

function setFontSize(size) {
    currentFontSize = size;
    if (selectedTextId) {
        const text = textElements.find(t => t.id == selectedTextId);
        if (text) {
            text.fontSize = size;
            saveTextElements();
            refreshTextOverlay();
            if (editingTextId == selectedTextId) {
                setTimeout(() => {
                    startEditing(selectedTextId);
                }, 50);
            }
        }
    }
}

function saveTextElements() {
    localStorage.setItem('pdf_text_elements', JSON.stringify(textElements));
}

function loadSavedTextElements() {
    const saved = localStorage.getItem('pdf_text_elements');
    if (saved) {
        try {
            textElements = JSON.parse(saved);
        } catch (e) {
            textElements = [];
        }
    }
}

async function saveEditedPDF() {
    const saveBtn = document.getElementById('save-pdf-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg> Saving...`;
    saveBtn.disabled = true;
    
    try {
        const formData = {
            text_elements: textElements,
            application_number: currentApplicationNumber
        };
        
        const response = await fetch('/applicant/application/save-edited-pdf', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `application-letter-${currentApplicationNumber || 'filled'}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showSuccessModal('PDF saved and downloaded successfully!');
        } else {
            const error = await response.json();
            throw new Error(error.message || 'Failed to save PDF');
        }
    } catch (error) {
        console.error('Error saving PDF:', error);
        showErrorModal('Failed to save PDF: ' + error.message);
    } finally {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
}

function closePdfEditor() {
    if (editingInput) finishEditing(null);
    const modal = document.getElementById('pdf-editor-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Download Functions
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

    if (hasAppLetter && !applicationNumberGenerated) {
        const appNumber = await generateAndSaveApplicationNumber();
        if (!appNumber) {
            return;
        }
    }

    const downloadBtn = document.getElementById('download-btn');
    if (downloadBtn) downloadBtn.disabled = true;
    
    totalFilesToDownload = selectedForms.length;
    downloadCount = 0;
    
    showSuccessModal(`Downloading ${selectedForms.length} file(s)...`);
    
    for (let i = 0; i < selectedForms.length; i++) {
        const form = selectedForms[i];
        if (form.isAppLetter && form.isInteractive) {
            if (textElements.length > 0) {
                await downloadEditedPDF();
            } else {
                downloadFile(form.file);
            }
        } else {
            downloadFile(form.file);
        }
        
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    setTimeout(() => {
        showSuccessModal('All files downloaded successfully!');
        if (downloadBtn) downloadBtn.disabled = false;
    }, 1000);
}

async function downloadEditedPDF() {
    try {
        const formData = {
            text_elements: textElements,
            application_number: currentApplicationNumber
        };
        
        const response = await fetch('/applicant/application/save-edited-pdf', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `application-letter-${currentApplicationNumber || 'filled'}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        } else {
            downloadFile('application-letter.pdf');
        }
    } catch (error) {
        console.error('Error downloading edited PDF:', error);
        downloadFile('application-letter.pdf');
    }
    
    downloadCount++;
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
}

// Utility Functions
function selectAllForms() {
    const checkboxes = document.querySelectorAll('.form-checkbox');
    checkboxes.forEach(cb => { cb.checked = true; });
    updateSelectedCount();
}

function deselectAllForms() {
    const checkboxes = document.querySelectorAll('.form-checkbox');
    checkboxes.forEach(cb => { cb.checked = false; });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.form-checkbox');
    let count = 0;
    checkboxes.forEach(cb => { if (cb.checked) count++; });
    
    const selectedCountEl = document.getElementById('selected-count');
    const downloadCountEl = document.getElementById('download-count');
    const downloadBtn = document.getElementById('download-btn');
    
    if (selectedCountEl) selectedCountEl.textContent = `${count} form${count !== 1 ? 's' : ''} selected`;
    if (downloadCountEl) downloadCountEl.textContent = count;
    if (downloadBtn) downloadBtn.disabled = count === 0;
}

function copyApplicationNumber() {
    const appNumber = document.getElementById('application-number')?.textContent;
    if (appNumber) {
        navigator.clipboard.writeText(appNumber).then(() => {
            showSuccessModal('Application number copied to clipboard!');
        }).catch(() => {
            showErrorModal('Failed to copy application number.');
        });
    }
}

function goToStep2(event) {
    event.preventDefault();
    
    if (!applicationNumberGenerated || !currentApplicationId) {
        showErrorModal('Please fill out the Application Letter first to generate your application number.');
        return;
    }
    
    sessionStorage.setItem('konstructo_current_app_id', currentApplicationId);
    sessionStorage.setItem('konstructo_current_app_number', currentApplicationNumber);
    
    window.location.href = `/applicant/application/step2?id=${currentApplicationId}`;
}

function handleBackNavigation(event) {
    event.preventDefault();
    if (applicationNumberGenerated && currentApplicationId) {
        pendingNavigationUrl = '/applicant/applications';
        showDraftModal();
    } else {
        window.history.back();
    }
}

function showDraftModal() {
    const appNumber = document.getElementById('application-number')?.textContent;
    const draftAppNumber = document.getElementById('draft-app-number');
    if (draftAppNumber && appNumber) draftAppNumber.textContent = appNumber;
    
    const modal = document.getElementById('save-draft-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeDraftModal() {
    const modal = document.getElementById('save-draft-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    pendingNavigationUrl = null;
}

async function saveDraftAndContinue() {
    closeDraftModal();
    showSuccessModal('Application saved as draft!');
    setTimeout(() => {
        if (pendingNavigationUrl) {
            window.location.href = pendingNavigationUrl;
        } else {
            window.location.href = '/applicant/applications';
        }
    }, 1500);
}

async function discardDraftAndContinue() {
    if (currentApplicationId) {
        await fetch(`/applicant/applications/${currentApplicationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).catch(err => console.log('Delete failed:', err));
    }
    closeDraftModal();
    window.location.href = pendingNavigationUrl || '/applicant/applications';
}

async function checkApplicationLimit() {
    try {
        const response = await fetch('/applicant/application/limit-info', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            limitInfo = data.data;
            updateStatsDisplay(limitInfo);
            
            if (!limitInfo.can_apply) {
                showErrorModal(`You have reached the maximum limit of ${limitInfo.limit} submitted applications.`);
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

function updateStatsDisplay(info) {
    const statsBanner = document.getElementById('application-stats-banner');
    if (statsBanner) statsBanner.classList.remove('hidden');
    
    const statsDrafts = document.getElementById('stats-drafts');
    const statsRemaining = document.getElementById('stats-remaining');
    const draftPlural = document.getElementById('draft-plural');
    const slotPlural = document.getElementById('slot-plural');
    
    if (statsDrafts) statsDrafts.textContent = info.drafts;
    if (statsRemaining) statsRemaining.textContent = info.remaining;
    if (draftPlural) draftPlural.textContent = info.drafts === 1 ? '' : 's';
    if (slotPlural) slotPlural.textContent = info.remaining === 1 ? '' : 's';
}

function showErrorModal(message) {
    const messageEl = document.getElementById('error-modal-message');
    const modal = document.getElementById('error-modal');
    if (messageEl) messageEl.textContent = message;
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeErrorModal() {
    const modal = document.getElementById('error-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showSuccessModal(message) {
    const messageEl = document.getElementById('success-modal-message');
    const modal = document.getElementById('success-modal');
    if (messageEl) messageEl.textContent = message;
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Setup click handler for adding new text
document.addEventListener('DOMContentLoaded', function() {
    showDPAModalIfNeeded();
    
    const savedAppId = sessionStorage.getItem('konstructo_current_app_id');
    const savedAppNumber = sessionStorage.getItem('konstructo_current_app_number');
    
    if (savedAppId && savedAppNumber) {
        currentApplicationId = savedAppId;
        currentApplicationNumber = savedAppNumber;
        applicationNumberGenerated = true;
        
        const appNumberEl = document.getElementById('application-number');
        const bannerEl = document.getElementById('application-number-banner');
        if (appNumberEl) appNumberEl.textContent = savedAppNumber;
        if (bannerEl) bannerEl.classList.remove('hidden');
    }
    
    checkApplicationLimit();
    
    const checkboxes = document.querySelectorAll('.form-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    
    updateSelectedCount();
    
    // Set up click handler for overlay
    setInterval(function() {
        const overlay = document.getElementById('text-overlay');
        if (overlay && !overlay.hasClickHandler) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    createNewTextAtPosition(e);
                }
            });
            overlay.hasClickHandler = true;
        }
    }, 100);
});
</script>

<style>
    #error-modal, #success-modal, #save-draft-modal, #dpa-modal, #pdf-editor-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .pdf-text-element {
        user-select: none;
        transition: all 0.1s ease;
    }
    
    .pdf-text-element:hover {
        border-color: #0066ff !important;
        background-color: rgba(0, 100, 255, 0.05) !important;
    }
    
    #pdf-viewer-wrapper {
        position: relative;
        display: inline-block;
    }
    
    #pdf-iframe {
        display: block;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    
    #text-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: auto;
    }
</style>
@endsection