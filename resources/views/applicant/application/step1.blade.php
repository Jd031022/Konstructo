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
                <a href="/applicant/applications" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm">View My Applications</a>
            </div>
        </div>
    </div>

    <div id="limit-warning-container"></div>

    <!-- Application Number Banner -->
    <div id="application-number-banner" class="mb-6 hidden">
        <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Your Application Number</p>
                        <p class="text-2xl font-bold text-white font-mono" id="application-number">2025000001</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="copyApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Copy Number
                    </button>
                    <span class="text-white/60 text-sm">Keep this for reference</span>
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Important:</span> Use this number when submitting hard copies to OBO.
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
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-medium text-gray-400">Upload Documents</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-medium text-gray-400">Review & Submit</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Instructions -->
        <div class="p-8 pt-10">
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    How to Complete Your Application
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Step-by-Step Guide:</h4>
                        <ol class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span><span>Select the forms you need from the checklist below</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span><span>Click "Edit PDF" on the Application Letter to type on it</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span><span>Click on the document to place text, drag to move</span></li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">PDF Editor Controls:</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-green-600 text-white rounded-full flex items-center justify-center text-xs">✏</span><span>Click on PDF to add a text box at that exact spot</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">↔</span><span>Click placed text to edit; drag to reposition</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs">↵</span><span>Press Enter to confirm, Escape to cancel</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms Checklist -->
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
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border-2 border-[#155386] transition relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-[#155386] text-white text-xs px-3 py-1 rounded-bl-lg font-medium">Interactive PDF</div>
                    <input type="checkbox" id="form-appletter" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-appletter" class="font-medium text-gray-800 cursor-pointer">Application Letter</label>
                        <p class="text-xs text-gray-500">Click "Edit PDF" to type directly on the document</p>
                        <div class="mt-2">
                            <button type="button" onclick="openPdfEditor()" class="inline-flex items-center text-sm bg-[#155386] text-white px-3 py-1.5 rounded-lg hover:bg-[#1F363D] transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Edit PDF
                            </button>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">PDF Form</span>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-building-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-building-permit" class="font-medium text-gray-800 cursor-pointer">Building Permit Application</label><p class="text-xs text-gray-500">Main application form</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sign-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-sign-permit" class="font-medium text-gray-800 cursor-pointer">Sign Permit Application</label><p class="text-xs text-gray-500">For signage and billboards</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-architectural-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-architectural-permit" class="font-medium text-gray-800 cursor-pointer">Architectural Permit</label><p class="text-xs text-gray-500">For architectural works</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-mechanical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-mechanical-permit" class="font-medium text-gray-800 cursor-pointer">Mechanical Permit</label><p class="text-xs text-gray-500">For mechanical installations</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electrical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-electrical-permit" class="font-medium text-gray-800 cursor-pointer">Electrical Permit</label><p class="text-xs text-gray-500">For electrical works</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electronics-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-electronics-permit" class="font-medium text-gray-800 cursor-pointer">Electronics Permit</label><p class="text-xs text-gray-500">For electronics systems</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sanitary-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-sanitary-permit" class="font-medium text-gray-800 cursor-pointer">Sanitary/Plumbing Permit</label><p class="text-xs text-gray-500">For plumbing and sanitary works</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-demolition-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-demolition-permit" class="font-medium text-gray-800 cursor-pointer">Demolition Permit</label><p class="text-xs text-gray-500">For demolition works</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-civil-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-civil-permit" class="font-medium text-gray-800 cursor-pointer">Civil/Structural Permit</label><p class="text-xs text-gray-500">For structural works</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-fencing-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1"><label for="form-fencing-permit" class="font-medium text-gray-800 cursor-pointer">Fencing Permit</label><p class="text-xs text-gray-500">For fencing construction</p></div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
            </div>

            <!-- Download Button -->
            <div class="flex items-center justify-between p-6 bg-blue-50 rounded-xl border border-blue-200">
                <div>
                    <p class="font-medium text-gray-800" id="selected-count">0 forms selected</p>
                    <p class="text-sm text-gray-600">Download all selected forms as individual PDFs</p>
                </div>
                <button onclick="downloadSelectedForms()" id="download-btn"
                        class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download Selected (<span id="download-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Next Step -->
        <div class="p-8 pt-0 flex justify-end">
            <a href="#" id="next-step-btn" onclick="goToStep2(event)"
               class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Next Step: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>
</div>

{{-- ══════════ MODALS ══════════ --}}

<!-- DPA -->
<div id="dpa-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4 py-8 hidden" style="backdrop-filter:blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <h3 class="text-xl font-bold">Data Privacy Act Compliance</h3>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto space-y-4 text-sm text-gray-600">
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600"><p class="font-medium text-blue-800">Republic Act No. 10173 (Data Privacy Act of 2012)</p></div>
                <p>In compliance with RA 10173, we inform you that your personal information will be collected and processed solely for your building permit application, shared only with relevant government offices, protected with appropriate security measures, and retained only as long as legally required.</p>
                <p>By proceeding you agree to these terms.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                <button onclick="declineDPA()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm">Decline</button>
                <button onclick="acceptDPA()" class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm">I Agree & Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Save Draft -->
<div id="save-draft-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Save as Draft?</h3>
            <p class="text-sm text-gray-600 mb-6">Application <span id="draft-app-number" class="font-mono font-bold text-blue-600"></span> will be saved. Continue later from your applications page.</p>
            <div class="flex flex-col gap-3">
                <button onclick="saveDraftAndContinue()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition text-sm">Yes, Save as Draft</button>
                <button onclick="discardDraftAndContinue()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-medium transition text-sm">No, Discard Progress</button>
                <button onclick="closeDraftModal()" class="text-sm text-gray-500 hover:text-gray-700">Cancel, Stay on this page</button>
            </div>
        </div>
    </div>
</div>

<!-- Error -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
            <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
            <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
        </div>
    </div>
</div>

<!-- Success -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
            <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
            <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     PDF EDITOR MODAL

     HOW ACCURATE COORDINATES WORK:
     ────────────────────────────────
     Browser side:
       • #pdf-viewer-wrapper is exactly IFRAME_W × IFRAME_H px (800×1100).
       • #text-overlay is absolutely positioned inset-0 over the iframe.
       • pointer-events:none on the iframe so ALL clicks reach the overlay.
       • We use e.offsetX / e.offsetY which give px relative to the overlay
         element's own top-left — scroll-independent and exact.
       • Text divs use line-height:1, padding:0, margin:0 so the div's
         top-left == where the text visually starts.
       • We store x,y as the div's top-left in overlay pixels.

     PHP side (ApplicationController@saveEditedPdf):
       • Receives iframe_width=800, iframe_height=1100.
       • Gets actual PDF mm dims from FPDI getTemplateSize().
       • scaleX = pdfW_mm / 800,  scaleY = pdfH_mm / 1100
       • pdfX = overlayX * scaleX
       • pdfY = overlayY * scaleY
       • Font px → pt: fontSize_pt = fontSize_px * 0.75  (96dpi→72dpi)
       • Uses SetXY($pdfX, $pdfY) then Cell(0, cellH, text)
         where cellH = fontSize_pt * 0.3528  (pt→mm, 1pt=0.3528mm)
         This makes TCPDF render text whose top-of-cell aligns with pdfY,
         matching where the browser div's top-of-text is.
══════════════════════════════════════════════════════════════════ --}}
<div id="pdf-editor-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:50; flex-direction:column;">

    <!-- Header -->
    <div style="flex-shrink:0; background:linear-gradient(to right,#155386,#1F363D); color:#fff; padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,.4);">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span style="font-size:1.1rem;font-weight:700;">PDF Editor — click on the document to place text</span>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button onclick="saveEditedPDF()" id="save-pdf-btn" style="background:#16a34a;color:#fff;border:none;padding:.5rem 1rem;border-radius:.5rem;cursor:pointer;display:flex;align-items:center;gap:.4rem;font-size:.9rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Save & Download PDF
            </button>
            <button onclick="closePdfEditor()" style="background:#4b5563;color:#fff;border:none;padding:.5rem 1rem;border-radius:.5rem;cursor:pointer;font-size:.9rem;">Close</button>
        </div>
    </div>

    <!-- Toolbar -->
    <div style="flex-shrink:0; background:#111827; color:#fff; padding:.6rem 1.5rem; display:flex; flex-wrap:wrap; align-items:center; gap:.75rem;">
        <span style="color:#9ca3af;font-size:.7rem;letter-spacing:.08em;">COLOR:</span>
        <button onclick="setTextColor('#000000')" title="Black"  style="width:1.6rem;height:1.6rem;background:#000;border:2px solid transparent;border-radius:.25rem;cursor:pointer;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='transparent'"></button>
        <button onclick="setTextColor('#1e3a8a')" title="Blue"   style="width:1.6rem;height:1.6rem;background:#1e3a8a;border:2px solid transparent;border-radius:.25rem;cursor:pointer;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='transparent'"></button>
        <button onclick="setTextColor('#991b1b')" title="Red"    style="width:1.6rem;height:1.6rem;background:#991b1b;border:2px solid transparent;border-radius:.25rem;cursor:pointer;" onmouseover="this.style.borderColor='#fff'" onmouseout="this.style.borderColor='transparent'"></button>
        <div style="width:1px;height:1.25rem;background:#374151;margin:0 .25rem;"></div>
        <span style="color:#9ca3af;font-size:.7rem;letter-spacing:.08em;">SIZE:</span>
        <button onclick="setFontSize(8)"  class="fs-btn" style="padding:.2rem .5rem;background:#374151;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">8</button>
        <button onclick="setFontSize(10)" class="fs-btn" style="padding:.2rem .5rem;background:#374151;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">10</button>
        <button onclick="setFontSize(12)" class="fs-btn" style="padding:.2rem .5rem;background:#6b7280;color:#fff;border:2px solid #fff;border-radius:.25rem;cursor:pointer;font-size:.75rem;">12</button>
        <button onclick="setFontSize(14)" class="fs-btn" style="padding:.2rem .5rem;background:#374151;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">14</button>
        <button onclick="setFontSize(16)" class="fs-btn" style="padding:.2rem .5rem;background:#374151;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">16</button>
        <button onclick="setFontSize(18)" class="fs-btn" style="padding:.2rem .5rem;background:#374151;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">18</button>
        <div style="width:1px;height:1.25rem;background:#374151;margin:0 .25rem;"></div>
        <button onclick="deleteSelectedText()" style="padding:.2rem .75rem;background:#b91c1c;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">Delete Selected</button>
        <button onclick="clearAllText()" style="padding:.2rem .75rem;background:#c2410c;color:#fff;border:none;border-radius:.25rem;cursor:pointer;font-size:.75rem;">Clear All</button>
        <span style="margin-left:auto;font-size:.75rem;color:#9ca3af;">Selected: <span id="selected-text-label" style="color:#fff;font-family:monospace;">none</span></span>
    </div>

    <!-- Scroll area -->
    <div style="flex:1; overflow:auto; background:#374151; display:flex; align-items:flex-start; justify-content:center; padding:2rem;" id="pdf-scroll-area">
        <!--
            CRITICAL: wrapper is EXACTLY 800×1100px — no padding, no border.
            The overlay (inset-0 absolute) captures clicks via offsetX/offsetY
            which are coords relative to the overlay's own origin (= PDF top-left).
        -->
        <div id="pdf-viewer-wrapper" style="position:relative; width:800px; height:1100px; flex-shrink:0; box-shadow:0 25px 60px rgba(0,0,0,.6);">

            <!-- PDF iframe: pointer-events:none so clicks go to overlay -->
            <iframe id="pdf-iframe"
                src="/downloads/application-letter.pdf#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                style="position:absolute; top:0; left:0; width:800px; height:1100px; border:none; pointer-events:none; display:block; background:#fff;"
                tabindex="-1">
            </iframe>

            <!-- Overlay: crosshair cursor, captures all clicks -->
            <div id="text-overlay"
                style="position:absolute; top:0; left:0; width:800px; height:1100px; cursor:crosshair; z-index:10; overflow:hidden;">
            </div>
        </div>
    </div>
</div>

<script>
'use strict';

// ─── Constants (must match iframe dimensions above) ───────────────────────────
const IFRAME_W = 800;
const IFRAME_H = 1100;

const formCheckboxes = [
    { id:'form-appletter',            name:'Application Letter',          file:'application-letter.pdf',          isAppLetter:true, isInteractive:true },
    { id:'form-building-permit',      name:'Building Permit Application', file:'building-permit-application.pdf' },
    { id:'form-sign-permit',          name:'Sign Permit Application',     file:'sign-permit-application.pdf' },
    { id:'form-architectural-permit', name:'Architectural Permit',        file:'architectural-permit.pdf' },
    { id:'form-mechanical-permit',    name:'Mechanical Permit',           file:'mechanical-permit.pdf' },
    { id:'form-electrical-permit',    name:'Electrical Permit',           file:'electrical-permit.pdf' },
    { id:'form-electronics-permit',   name:'Electronics Permit',          file:'electronics-permit.pdf' },
    { id:'form-sanitary-permit',      name:'Sanitary/Plumbing Permit',    file:'sanitary-plumbing-permit.pdf' },
    { id:'form-demolition-permit',    name:'Demolition Permit',           file:'demolition-permit.pdf' },
    { id:'form-civil-permit',         name:'Civil/Structural Permit',     file:'civil-structural-permit.pdf' },
    { id:'form-fencing-permit',       name:'Fencing Permit',              file:'fencing-permit.pdf' },
];

// ─── State ────────────────────────────────────────────────────────────────────
let textElements   = [];
let selectedTextId = null;
let editingTextId  = null;
let editingInput   = null;
let currentColor   = '#000000';
let currentFontSize= 12;

let applicationNumberGenerated = false;
let currentApplicationId       = null;
let currentApplicationNumber   = null;
let pendingNavigationUrl       = null;
let dpaAccepted                = false;

// ─── DPA ─────────────────────────────────────────────────────────────────────
function checkDPAStatus(){ dpaAccepted = localStorage.getItem('dpa_consent')==='accepted'; return dpaAccepted; }
function showDPAModalIfNeeded(){ if(!checkDPAStatus()){ document.getElementById('dpa-modal').classList.remove('hidden'); document.body.style.overflow='hidden'; } }
function acceptDPA(){ localStorage.setItem('dpa_consent','accepted'); dpaAccepted=true; document.getElementById('dpa-modal').classList.add('hidden'); document.body.style.overflow='auto'; showSuccessModal('Thank you. You may now proceed.'); }
function declineDPA(){ localStorage.removeItem('dpa_consent'); document.getElementById('dpa-modal').classList.add('hidden'); document.body.style.overflow='auto'; showErrorModal('You must accept the Data Privacy Act terms to proceed.'); setTimeout(()=>{ window.location.href='/applicant/dashboard'; },3000); }

// ─── CSRF ─────────────────────────────────────────────────────────────────────
function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}'; }

// ─── Application number ───────────────────────────────────────────────────────
async function generateAndSaveApplicationNumber(){
    if(applicationNumberGenerated) return currentApplicationNumber;
    try{
        const dr=await fetch('/applicant/application/create-draft',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'}});
        const dd=await dr.json();
        if(dd.limit_reached){showErrorModal(dd.message);return null;}
        if(!dd.success){throw new Error(dd.message);}
        currentApplicationId=dd.data.id;
        const gr=await fetch('/applicant/application/generate-number',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},body:JSON.stringify({application_id:currentApplicationId})});
        const gd=await gr.json();
        if(!gd.success){throw new Error(gd.message);}
        currentApplicationNumber=gd.data.application_number;
        applicationNumberGenerated=true;
        document.getElementById('application-number').textContent=currentApplicationNumber;
        document.getElementById('application-number-banner').classList.remove('hidden');
        sessionStorage.setItem('konstructo_current_app_id',currentApplicationId);
        sessionStorage.setItem('konstructo_current_app_number',currentApplicationNumber);
        return currentApplicationNumber;
    }catch(e){ showErrorModal('Failed to generate application number: '+e.message); return null; }
}

// ─── Editor open/close ────────────────────────────────────────────────────────
async function openPdfEditor(){
    if(!applicationNumberGenerated){ const n=await generateAndSaveApplicationNumber(); if(!n) return; }
    loadSavedTextElements();
    const m=document.getElementById('pdf-editor-modal');
    m.style.display='flex';
    document.body.style.overflow='hidden';
    refreshTextOverlay();
}
function closePdfEditor(){
    if(editingInput) finishEditing(null);
    document.getElementById('pdf-editor-modal').style.display='none';
    document.body.style.overflow='auto';
}

// ─── Render overlay ──────────────────────────────────────────────────────────
function refreshTextOverlay(){
    const overlay=document.getElementById('text-overlay');
    if(!overlay) return;
    overlay.querySelectorAll('.pdf-text-el').forEach(el=>el.remove());
    textElements.forEach(t=>mountTextEl(t));
    updateSelectedLabel();
}

function mountTextEl(text){
    const overlay=document.getElementById('text-overlay');
    const div=document.createElement('div');
    div.className='pdf-text-el';
    div.setAttribute('data-id',text.id);
    div.textContent=text.content;
    const sel=(text.id===selectedTextId);

    /*
     * CRITICAL CSS for accurate positioning:
     * - line-height:1          → div height = font px, no extra leading
     * - padding:0; margin:0    → top of div = top of rendered text
     * - white-space:nowrap     → single-line, width follows content
     * These ensure the visual top-left of the text matches text.x / text.y
     * which is what PHP uses for SetXY().
     */
    div.style.cssText=
        'position:absolute;'
       +'left:'+text.x+'px;'
       +'top:'+text.y+'px;'
       +'color:'+text.color+';'
       +'font-size:'+text.fontSize+'px;'
       +'font-family:Arial,Helvetica,sans-serif;'
       +'line-height:1;'
       +'padding:0;'
       +'margin:0;'
       +'border:1px solid '+(sel?'#3b82f6':'transparent')+';'
       +'border-radius:2px;'
       +'white-space:nowrap;'
       +'user-select:none;'
       +'cursor:move;'
       +'z-index:20;'
       +'background:'+(sel?'rgba(59,130,246,.08)':'transparent')+';';

    div.addEventListener('click',e=>{ e.stopPropagation(); selectedTextId=text.id; refreshTextOverlay(); startEditing(text.id); });

    // Drag
    let drag=false,ox,oy,sx,sy;
    div.addEventListener('mousedown',e=>{
        if(editingInput) return;
        e.preventDefault(); e.stopPropagation();
        drag=true; ox=e.clientX; oy=e.clientY; sx=text.x; sy=text.y;
        div.style.opacity='0.5';
    });
    document.addEventListener('mousemove',e=>{
        if(!drag) return;
        text.x=Math.max(0,Math.min(sx+(e.clientX-ox),IFRAME_W-2));
        text.y=Math.max(0,Math.min(sy+(e.clientY-oy),IFRAME_H-2));
        div.style.left=text.x+'px';
        div.style.top=text.y+'px';
    });
    document.addEventListener('mouseup',()=>{ if(!drag) return; drag=false; div.style.opacity='1'; saveTextElements(); });

    overlay.appendChild(div);
}

// ─── Inline edit ─────────────────────────────────────────────────────────────
function startEditing(id){
    if(editingInput) finishEditing();
    const text=textElements.find(t=>t.id===id);
    if(!text) return;
    const div=document.querySelector(`.pdf-text-el[data-id="${id}"]`);
    if(!div)  return;
    editingTextId=id;
    div.style.visibility='hidden';

    const inp=document.createElement('input');
    inp.type='text';
    inp.value=(text.content==='Click to edit')?'':text.content;

    /*
     * Input CSS mirrors the div CSS exactly:
     * same font-size, font-family, line-height:1, padding:0
     * so the input visually overlays the div at the same position.
     */
    inp.style.cssText=
        'position:absolute;'
       +'left:'+text.x+'px;'
       +'top:'+text.y+'px;'
       +'font-size:'+text.fontSize+'px;'
       +'font-family:Arial,Helvetica,sans-serif;'
       +'line-height:1;'
       +'color:'+text.color+';'
       +'padding:0;'
       +'margin:0;'
       +'border:0;'
       +'border-bottom:2px solid #3b82f6;'
       +'background:rgba(255,255,255,0.92);'
       +'z-index:30;'
       +'outline:none;'
       +'min-width:80px;'
       +'box-sizing:content-box;';

    const autoSize=()=>{
        const s=document.createElement('span');
        s.style.cssText='visibility:hidden;position:fixed;white-space:pre;font-size:'+text.fontSize+'px;font-family:Arial,Helvetica,sans-serif;line-height:1;';
        s.textContent=inp.value||'M';
        document.body.appendChild(s);
        inp.style.width=Math.max(80,s.offsetWidth+20)+'px';
        document.body.removeChild(s);
    };
    autoSize();
    inp.addEventListener('input',autoSize);
    inp.addEventListener('keydown',e=>{ if(e.key==='Enter'){e.preventDefault();finishEditing(inp.value);} if(e.key==='Escape') finishEditing(null); });
    inp.addEventListener('blur',()=>setTimeout(()=>finishEditing(inp.value),80));

    document.getElementById('text-overlay').appendChild(inp);
    editingInput=inp;
    inp.focus(); inp.select();
}

function finishEditing(newValue){
    if(!editingInput) return;
    const text=textElements.find(t=>t.id===editingTextId);
    if(newValue!==null && typeof newValue==='string' && newValue.trim()!==''){
        if(text) text.content=newValue.trim();
    } else {
        textElements=textElements.filter(t=>t.id!==editingTextId);
        if(selectedTextId===editingTextId) selectedTextId=null;
    }
    editingInput.remove(); editingInput=null; editingTextId=null;
    saveTextElements(); refreshTextOverlay();
}

// ─── Create text on click ─────────────────────────────────────────────────────
function handleOverlayClick(e){
    if(e.target.id!=='text-overlay') return;  // ignore clicks on child text divs
    if(editingInput) return;

    /*
     * e.offsetX / e.offsetY = px from the overlay element's OWN top-left.
     * Because the overlay is position:absolute, top:0, left:0 over the
     * 800×1100 wrapper, these values directly equal the PDF pixel coordinates.
     * No viewport math, no scroll adjustment needed.
     */
    const x=Math.round(e.offsetX);
    const y=Math.round(e.offsetY);

    const t={
        id:Date.now(),
        content:'Click to edit',
        x:Math.max(0,Math.min(x,IFRAME_W-10)),
        y:Math.max(0,Math.min(y,IFRAME_H-10)),
        color:currentColor,
        fontSize:currentFontSize,
    };
    textElements.push(t);
    selectedTextId=t.id;
    saveTextElements();
    refreshTextOverlay();
    setTimeout(()=>startEditing(t.id),20);
}

// ─── Toolbar ─────────────────────────────────────────────────────────────────
function setTextColor(c){
    currentColor=c;
    if(selectedTextId){ const t=textElements.find(t=>t.id===selectedTextId); if(t){t.color=c;saveTextElements();refreshTextOverlay();} }
}
function setFontSize(s){
    currentFontSize=s;
    document.querySelectorAll('.fs-btn').forEach(b=>{
        const active=parseInt(b.textContent)===s;
        b.style.background=active?'#6b7280':'#374151';
        b.style.border=active?'2px solid #fff':'none';
    });
    if(selectedTextId){ const t=textElements.find(t=>t.id===selectedTextId); if(t){t.fontSize=s;saveTextElements();refreshTextOverlay();} }
}
function deleteSelectedText(){
    if(!selectedTextId){showErrorModal('Click a text element first to select it.');return;}
    if(editingInput) finishEditing(null);
    textElements=textElements.filter(t=>t.id!==selectedTextId);
    selectedTextId=null; saveTextElements(); refreshTextOverlay();
}
function clearAllText(){
    if(!confirm('Clear all text?')) return;
    if(editingInput) finishEditing(null);
    textElements=[]; selectedTextId=null; saveTextElements(); refreshTextOverlay();
}
function updateSelectedLabel(){
    const el=document.getElementById('selected-text-label');
    if(!el) return;
    const t=selectedTextId?textElements.find(t=>t.id===selectedTextId):null;
    el.textContent=t?'"'+t.content.substring(0,22)+(t.content.length>22?'…':'')+'"':'none';
}

// ─── Persistence ──────────────────────────────────────────────────────────────
function saveTextElements(){ try{localStorage.setItem('pdf_text_elements',JSON.stringify(textElements));}catch(_){} }
function loadSavedTextElements(){ try{const s=localStorage.getItem('pdf_text_elements');textElements=s?JSON.parse(s):[];}catch(_){textElements=[];} }

// ─── Save / download PDF ──────────────────────────────────────────────────────
async function callSaveEndpoint(){
    const res=await fetch('/applicant/application/save-edited-pdf',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
        body:JSON.stringify({
            text_elements:textElements,
            application_number:currentApplicationNumber,
            iframe_width:IFRAME_W,
            iframe_height:IFRAME_H,
        }),
    });
    if(!res.ok){const e=await res.json().catch(()=>({}));throw new Error(e.error||'Server error');}
    return res.blob();
}
function triggerDownload(blob,name){
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url;a.download=name;
    document.body.appendChild(a);a.click();document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
async function saveEditedPDF(){
    const btn=document.getElementById('save-pdf-btn');
    const orig=btn.innerHTML;
    btn.innerHTML='<svg class="animate-spin" style="width:1rem;height:1rem;display:inline;margin-right:.4rem;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Saving…';
    btn.disabled=true;
    try{
        const blob=await callSaveEndpoint();
        triggerDownload(blob,`application-letter-${currentApplicationNumber||'filled'}.pdf`);
        showSuccessModal('PDF downloaded successfully!');
    }catch(e){ showErrorModal('Failed to save PDF: '+e.message); }
    finally{ btn.innerHTML=orig;btn.disabled=false; }
}

// ─── Download forms ───────────────────────────────────────────────────────────
async function downloadSelectedForms(){
    if(!dpaAccepted){showErrorModal('Please accept the Data Privacy Act terms first.');return;}
    if(!await checkApplicationLimit()) return;
    const sel=[];let hasLetter=false;
    document.querySelectorAll('.form-checkbox').forEach((cb,i)=>{ if(cb.checked){sel.push(formCheckboxes[i]);if(formCheckboxes[i].isAppLetter)hasLetter=true;} });
    if(!sel.length){showErrorModal('Please select at least one form.');return;}
    if(hasLetter&&!applicationNumberGenerated){ if(!await generateAndSaveApplicationNumber()) return; }
    const btn=document.getElementById('download-btn');
    if(btn) btn.disabled=true;
    showSuccessModal(`Downloading ${sel.length} file(s)…`);
    for(const form of sel){
        if(form.isInteractive&&textElements.length>0){
            try{const blob=await callSaveEndpoint();triggerDownload(blob,`application-letter-${currentApplicationNumber||'filled'}.pdf`);}
            catch{downloadFile(form.file);}
        }else{ downloadFile(form.file); }
        await new Promise(r=>setTimeout(r,600));
    }
    setTimeout(()=>{ showSuccessModal('All files downloaded!'); if(btn) btn.disabled=false; },600);
}
function downloadFile(filename){
    const a=document.createElement('a');
    a.href=`/downloads/${filename}?t=${Date.now()}`;a.download=filename;
    document.body.appendChild(a);a.click();document.body.removeChild(a);
}

// ─── UI helpers ───────────────────────────────────────────────────────────────
function selectAllForms(){ document.querySelectorAll('.form-checkbox').forEach(cb=>cb.checked=true); updateSelectedCount(); }
function deselectAllForms(){ document.querySelectorAll('.form-checkbox').forEach(cb=>cb.checked=false); updateSelectedCount(); }
function updateSelectedCount(){
    let n=0; document.querySelectorAll('.form-checkbox').forEach(cb=>{if(cb.checked)n++;});
    document.getElementById('selected-count').textContent=`${n} form${n!==1?'s':''} selected`;
    document.getElementById('download-count').textContent=n;
    document.getElementById('download-btn').disabled=n===0;
}
function copyApplicationNumber(){
    const n=document.getElementById('application-number')?.textContent;
    if(!n) return;
    navigator.clipboard.writeText(n).then(()=>showSuccessModal('Copied!')).catch(()=>showErrorModal('Copy failed.'));
}
function goToStep2(e){
    e.preventDefault();
    if(!applicationNumberGenerated||!currentApplicationId){ showErrorModal('Please fill out the Application Letter first to generate your application number.'); return; }
    sessionStorage.setItem('konstructo_current_app_id',currentApplicationId);
    sessionStorage.setItem('konstructo_current_app_number',currentApplicationNumber);
    window.location.href=`/applicant/application/step2?id=${currentApplicationId}`;
}
function handleBackNavigation(e){
    e.preventDefault();
    if(applicationNumberGenerated&&currentApplicationId){ pendingNavigationUrl='/applicant/applications'; showDraftModal(); }
    else window.history.back();
}
function showDraftModal(){
    document.getElementById('draft-app-number').textContent=document.getElementById('application-number')?.textContent||'';
    document.getElementById('save-draft-modal').classList.remove('hidden');
    document.body.style.overflow='hidden';
}
function closeDraftModal(){ document.getElementById('save-draft-modal').classList.add('hidden'); document.body.style.overflow='auto'; pendingNavigationUrl=null; }
async function saveDraftAndContinue(){ closeDraftModal(); showSuccessModal('Saved as draft!'); setTimeout(()=>{ window.location.href=pendingNavigationUrl||'/applicant/applications'; },1500); }
async function discardDraftAndContinue(){
    if(currentApplicationId){ await fetch(`/applicant/applications/${currentApplicationId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf(),'Accept':'application/json'}}).catch(()=>{}); }
    closeDraftModal(); window.location.href=pendingNavigationUrl||'/applicant/applications';
}
async function checkApplicationLimit(){
    try{
        const r=await fetch('/applicant/application/limit-info',{headers:{'Accept':'application/json','X-CSRF-TOKEN':csrf()}});
        const d=await r.json();
        if(d.success){
            document.getElementById('application-stats-banner').classList.remove('hidden');
            document.getElementById('stats-drafts').textContent=d.data.drafts;
            document.getElementById('stats-remaining').textContent=d.data.remaining;
            document.getElementById('draft-plural').textContent=d.data.drafts===1?'':'s';
            document.getElementById('slot-plural').textContent=d.data.remaining===1?'':'s';
            if(!d.data.can_apply){showErrorModal(`Maximum of ${d.data.limit} submitted applications reached.`);return false;}
        }
        return true;
    }catch{return true;}
}
function showErrorModal(msg){ document.getElementById('error-modal-message').textContent=msg; document.getElementById('error-modal').classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeErrorModal(){ document.getElementById('error-modal').classList.add('hidden'); document.body.style.overflow='auto'; }
function showSuccessModal(msg){ document.getElementById('success-modal-message').textContent=msg; document.getElementById('success-modal').classList.remove('hidden'); document.body.style.overflow='hidden'; setTimeout(closeSuccessModal,3000); }
function closeSuccessModal(){ document.getElementById('success-modal').classList.add('hidden'); document.body.style.overflow='auto'; }

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded',()=>{
    showDPAModalIfNeeded();
    const sid=sessionStorage.getItem('konstructo_current_app_id');
    const sn=sessionStorage.getItem('konstructo_current_app_number');
    if(sid&&sn){
        currentApplicationId=sid; currentApplicationNumber=sn; applicationNumberGenerated=true;
        document.getElementById('application-number').textContent=sn;
        document.getElementById('application-number-banner').classList.remove('hidden');
    }
    checkApplicationLimit();
    document.querySelectorAll('.form-checkbox').forEach(cb=>cb.addEventListener('change',updateSelectedCount));
    updateSelectedCount();
    const overlay=document.getElementById('text-overlay');
    if(overlay) overlay.addEventListener('click',handleOverlayClick);
});
</script>

<style>
.animate-spin{animation:spin 1s linear infinite;}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
button:disabled{cursor:not-allowed;opacity:.65;}
.pdf-text-el:hover{border-color:#3b82f6!important;}
</style>
@endsection