@extends('layouts.app')

@section('title', 'Application - Step 1')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="/applicant/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to My Applications
        </a>
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

    <!-- Application Number Banner - Will show if application already has a number -->
    @if($application->application_number)
    <div id="application-number-banner" class="mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Your Application Number</p>
                        <p class="text-2xl font-bold text-white font-mono" id="application-number">{{ $application->application_number }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="copyApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Copy Number
                    </button>
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Important:</span> Keep this number for reference when submitting hard copies.
            </div>
        </div>
    </div>
    @else
    <div id="application-number-banner" class="mb-6 hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Your Application Number</p>
                        <p class="text-2xl font-bold text-white font-mono" id="application-number">2026000000</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="copyApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Copy Number
                    </button>
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Important:</span> Keep this number for reference when submitting hard copies.
            </div>
        </div>
    </div>
    @endif

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
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span><span>Click "Edit PDF" on the Building Permit Application to type on it</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span><span>Your application number will be automatically generated when you open the PDF editor (if not already generated)</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">4</span><span>After editing, click "Save & Download PDF" to save your filled form</span></li>
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
                    <input type="checkbox" id="form-building-permit-app" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-building-permit-app" class="font-medium text-gray-800 cursor-pointer">Building Permit Application</label>
                        <p class="text-xs text-gray-500">Click "Edit PDF" to type directly on the document. Your application number will be generated here.</p>
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
            <button onclick="goToStep2()" id="next-step-btn"
                    class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Next Step: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
</div>

{{-- ══════════ MODALS ══════════ --}}

<!-- DPA Modal -->
<div id="dpa-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4 py-8" style="backdrop-filter:blur(4px); display: flex;">
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

<!-- Error Modal -->
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

<!-- Success Modal -->
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
     PDF EDITOR MODAL - Building Permit Application with Pagination
══════════════════════════════════════════════════════════════════ --}}
<div id="pdf-editor-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:50; flex-direction:column;">

    <!-- Header -->
    <div style="flex-shrink:0; background:linear-gradient(to right,#155386,#1F363D); color:#fff; padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,.4);">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span style="font-size:1.1rem;font-weight:700;">PDF Editor — Building Permit Application</span>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button onclick="saveEditedPDF()" id="save-pdf-btn" style="background:#16a34a;color:#fff;border:none;padding:.5rem 1rem;border-radius:.5rem;cursor:pointer;display:flex;align-items:center;gap:.4rem;font-size:.9rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Save & Download PDF
            </button>
            <button onclick="closePdfEditor()" style="background:#4b5563;color:#fff;border:none;padding:.5rem 1rem;border-radius:.5rem;cursor:pointer;font-size:.9rem;">Close</button>
        </div>
    </div>

    <!-- Page Navigation Toolbar -->
    <div style="flex-shrink:0; background:#1f2937; color:#fff; padding:.5rem 1.5rem; display:flex; align-items:center; justify-content:center; gap:1rem; border-bottom:1px solid #374151;">
        <button onclick="previousPage()" id="prev-page-btn" style="background:#374151; color:#fff; border:none; padding:.4rem 1rem; border-radius:.5rem; cursor:pointer; display:flex; align-items:center; gap:.5rem; font-size:.85rem; transition:all 0.2s;">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Previous
        </button>
        <span style="font-size:.9rem; font-weight:500;">
            Page <span id="current-page">1</span> of <span id="total-pages">1</span>
        </span>
        <button onclick="nextPage()" id="next-page-btn" style="background:#374151; color:#fff; border:none; padding:.4rem 1rem; border-radius:.5rem; cursor:pointer; display:flex; align-items:center; gap:.5rem; font-size:.85rem; transition:all 0.2s;">
            Next
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
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

    <!-- Scroll area with PDF viewer -->
    <div style="flex:1; overflow:auto; background:#374151; display:flex; align-items:flex-start; justify-content:center; padding:2rem;" id="pdf-scroll-area">
        <div id="pdf-viewer-wrapper" style="position:relative; width:800px; height:1100px; flex-shrink:0; box-shadow:0 25px 60px rgba(0,0,0,.6);">
            <iframe id="pdf-iframe"
                src="/downloads/building-permit-application.pdf#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                style="position:absolute; top:0; left:0; width:800px; height:1100px; border:none; pointer-events:none; display:block; background:#fff;"
                tabindex="-1">
            </iframe>
            <div id="text-overlay"
                style="position:absolute; top:0; left:0; width:800px; height:1100px; cursor:crosshair; z-index:10; overflow:hidden;">
            </div>
        </div>
    </div>
</div>

<script>
'use strict';

// ─── Constants ───────────────────────────────────────────────────────────────
const IFRAME_W = 800;
const IFRAME_H = 1100;

// Get application data from server
const existingApplicationNumber = @json($application->application_number);
const applicationId = @json($application->id);

// PDF Pagination
let currentPage = 1;
let totalPages = 1;
let pdfDoc = null;

const formCheckboxes = [
    { id:'form-building-permit-app',  name:'Building Permit Application', file:'building-permit-application.pdf', isInteractive:true },
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
let currentApplicationId       = applicationId;
let currentApplicationNumber   = existingApplicationNumber;
let dpaAccepted                = false;

// Set flag if number exists
if (existingApplicationNumber) {
    applicationNumberGenerated = true;
    console.log('Application number loaded from server:', existingApplicationNumber);
}

// ─── DPA FUNCTIONS ────────────────────────────────────────────────────────────
function checkDPAStatus(){ 
    dpaAccepted = localStorage.getItem('dpa_consent') === 'accepted'; 
    return dpaAccepted; 
}

function showDPAModalIfNeeded(){ 
    if(!checkDPAStatus()){ 
        const modal = document.getElementById('dpa-modal');
        if(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    } else {
        const modal = document.getElementById('dpa-modal');
        if(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
}

function acceptDPA(){ 
    localStorage.setItem('dpa_consent','accepted'); 
    dpaAccepted=true; 
    const modal = document.getElementById('dpa-modal');
    if(modal) {
        modal.style.display = 'none';
        document.body.style.overflow='auto';
    }
    showSuccessModal('Thank you. You may now proceed.'); 
}

function declineDPA(){ 
    localStorage.removeItem('dpa_consent'); 
    dpaAccepted=false; 
    const modal = document.getElementById('dpa-modal');
    if(modal) {
        modal.style.display = 'none';
        document.body.style.overflow='auto';
    }
    showErrorModal('You must accept the Data Privacy Act terms to proceed.'); 
    setTimeout(()=>{ 
        window.location.href='/applicant/dashboard'; 
    },3000); 
}

// ─── CSRF ─────────────────────────────────────────────────────────────────────
function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}'; }

// ─── PDF Pagination Functions ─────────────────────────────────────────────────
function initPDFPagination() {
    const pdfUrl = '/downloads/building-permit-application.pdf';
    
    if (typeof pdfjsLib === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
        script.onload = () => {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            loadPDF(pdfUrl);
        };
        document.head.appendChild(script);
    } else {
        loadPDF(pdfUrl);
    }
}

function loadPDF(url) {
    pdfjsLib.getDocument(url).promise.then(function(doc) {
        pdfDoc = doc;
        totalPages = doc.numPages;
        document.getElementById('total-pages').textContent = totalPages;
        updatePageButtons();
        loadPage(currentPage);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        totalPages = 1;
        document.getElementById('total-pages').textContent = '?';
    });
}

function loadPage(pageNum) {
    if (!pdfDoc) return;
    
    pdfDoc.getPage(pageNum).then(function(page) {
        const viewport = page.getViewport({ scale: 1.5 });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        
        const renderContext = {
            canvasContext: context,
            viewport: viewport
        };
        
        page.render(renderContext).promise.then(function() {
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            const iframe = document.getElementById('pdf-iframe');
            if (iframe) {
                const tempHtml = `<!DOCTYPE html><html><body style="margin:0;padding:0;"><img src="${dataUrl}" style="width:100%;height:auto;"></body></html>`;
                const blob = new Blob([tempHtml], { type: 'text/html' });
                const blobUrl = URL.createObjectURL(blob);
                iframe.src = blobUrl;
            }
        });
    });
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        document.getElementById('current-page').textContent = currentPage;
        updatePageButtons();
        loadPage(currentPage);
        loadTextElementsForPage(currentPage);
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        document.getElementById('current-page').textContent = currentPage;
        updatePageButtons();
        loadPage(currentPage);
        loadTextElementsForPage(currentPage);
    }
}

function updatePageButtons() {
    const prevBtn = document.getElementById('prev-page-btn');
    const nextBtn = document.getElementById('next-page-btn');
    
    if (prevBtn) prevBtn.disabled = currentPage <= 1;
    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
}

function loadTextElementsForPage(pageNum) {
    const pageKey = `page_${pageNum}`;
    if (textElementsByPage && textElementsByPage[pageKey]) {
        textElements = textElementsByPage[pageKey];
    } else {
        textElements = [];
    }
    refreshTextOverlay();
}

let textElementsByPage = {};

function saveTextElements() {
    const pageKey = `page_${currentPage}`;
    textElementsByPage[pageKey] = textElements;
    localStorage.setItem('pdf_text_elements_building_permit', JSON.stringify(textElementsByPage));
}

function loadSavedTextElements() {
    try {
        const saved = localStorage.getItem('pdf_text_elements_building_permit');
        if (saved) {
            textElementsByPage = JSON.parse(saved);
            const pageKey = `page_${currentPage}`;
            textElements = textElementsByPage[pageKey] || [];
        } else {
            textElementsByPage = {};
            textElements = [];
        }
    } catch(e) {
        textElementsByPage = {};
        textElements = [];
    }
}

// ─── Generate application number (ONLY if no existing number) ──────────
async function generateAndSaveApplicationNumber(){
    // If already have a number, just return it
    if(applicationNumberGenerated && currentApplicationId && currentApplicationNumber) {
        console.log('Already have application number:', currentApplicationNumber);
        return currentApplicationNumber;
    }
    
    try {
        if (!currentApplicationId) {
            showErrorModal('Application ID is missing.');
            return null;
        }
        
        // Generate new application number
        const gr = await fetch('/applicant/application/generate-number', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
            body: JSON.stringify({
                application_id: currentApplicationId
            })
        });
        const gd = await gr.json();
        
        if(!gd.success){
            throw new Error(gd.message);
        }
        
        currentApplicationNumber = gd.data.application_number;
        applicationNumberGenerated = true;
        
        // Show the banner
        const banner = document.getElementById('application-number-banner');
        const numberEl = document.getElementById('application-number');
        if (banner && numberEl) {
            numberEl.textContent = currentApplicationNumber;
            banner.classList.remove('hidden');
        }
        
        showSuccessModal(`Application number ${currentApplicationNumber} has been generated!`);
        
        return currentApplicationNumber;
    } catch(e){ 
        showErrorModal('Failed to generate application number: '+e.message); 
        return null; 
    }
}

// ─── Editor open/close ────────────────────────────────────────────────────────
async function openPdfEditor(){
    if(!checkDPAStatus()){
        showErrorModal('Please accept the Data Privacy Act terms first.');
        showDPAModalIfNeeded();
        return;
    }
    
    // Generate or get existing application number when opening the editor
    const appNumber = await generateAndSaveApplicationNumber();
    if(!appNumber) return;
    
    // Reset to page 1
    currentPage = 1;
    document.getElementById('current-page').textContent = '1';
    
    loadSavedTextElements();
    const m = document.getElementById('pdf-editor-modal');
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    initPDFPagination();
    refreshTextOverlay();
}

function closePdfEditor(){
    if(editingInput) finishEditing(null);
    document.getElementById('pdf-editor-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ─── Render overlay functions (keep the same as before) ────────────────────────
function refreshTextOverlay(){
    const overlay = document.getElementById('text-overlay');
    if(!overlay) return;
    overlay.querySelectorAll('.pdf-text-el').forEach(el => el.remove());
    textElements.forEach(t => mountTextEl(t));
    updateSelectedLabel();
}

function mountTextEl(text){
    const overlay = document.getElementById('text-overlay');
    const div = document.createElement('div');
    div.className = 'pdf-text-el';
    div.setAttribute('data-id', text.id);
    div.textContent = text.content;
    const sel = (text.id === selectedTextId);

    div.style.cssText =
        'position:absolute;'
       + 'left:' + text.x + 'px;'
       + 'top:' + text.y + 'px;'
       + 'color:' + text.color + ';'
       + 'font-size:' + text.fontSize + 'px;'
       + 'font-family:Arial,Helvetica,sans-serif;'
       + 'line-height:1;'
       + 'padding:0;'
       + 'margin:0;'
       + 'border:1px solid ' + (sel ? '#3b82f6' : 'transparent') + ';'
       + 'border-radius:2px;'
       + 'white-space:nowrap;'
       + 'user-select:none;'
       + 'cursor:move;'
       + 'z-index:20;'
       + 'background:' + (sel ? 'rgba(59,130,246,.08)' : 'transparent') + ';';

    div.addEventListener('click', e => { e.stopPropagation(); selectedTextId = text.id; refreshTextOverlay(); startEditing(text.id); });

    let drag = false, ox, oy, sx, sy;
    div.addEventListener('mousedown', e => {
        if(editingInput) return;
        e.preventDefault(); e.stopPropagation();
        drag = true; ox = e.clientX; oy = e.clientY; sx = text.x; sy = text.y;
        div.style.opacity = '0.5';
    });
    document.addEventListener('mousemove', e => {
        if(!drag) return;
        text.x = Math.max(0, Math.min(sx + (e.clientX - ox), IFRAME_W - 2));
        text.y = Math.max(0, Math.min(sy + (e.clientY - oy), IFRAME_H - 2));
        div.style.left = text.x + 'px';
        div.style.top = text.y + 'px';
    });
    document.addEventListener('mouseup', () => { if(!drag) return; drag = false; div.style.opacity = '1'; saveTextElements(); });

    overlay.appendChild(div);
}

function startEditing(id){
    if(editingInput) finishEditing();
    const text = textElements.find(t => t.id === id);
    if(!text) return;
    const div = document.querySelector(`.pdf-text-el[data-id="${id}"]`);
    if(!div) return;
    editingTextId = id;
    div.style.visibility = 'hidden';

    const inp = document.createElement('input');
    inp.type = 'text';
    inp.value = (text.content === 'Click to edit') ? '' : text.content;

    inp.style.cssText =
        'position:absolute;'
       + 'left:' + text.x + 'px;'
       + 'top:' + text.y + 'px;'
       + 'font-size:' + text.fontSize + 'px;'
       + 'font-family:Arial,Helvetica,sans-serif;'
       + 'line-height:1;'
       + 'color:' + text.color + ';'
       + 'padding:0;'
       + 'margin:0;'
       + 'border:0;'
       + 'border-bottom:2px solid #3b82f6;'
       + 'background:rgba(255,255,255,0.92);'
       + 'z-index:30;'
       + 'outline:none;'
       + 'min-width:80px;'
       + 'box-sizing:content-box;';

    const autoSize = () => {
        const s = document.createElement('span');
        s.style.cssText = 'visibility:hidden;position:fixed;white-space:pre;font-size:' + text.fontSize + 'px;font-family:Arial,Helvetica,sans-serif;line-height:1;';
        s.textContent = inp.value || 'M';
        document.body.appendChild(s);
        inp.style.width = Math.max(80, s.offsetWidth + 20) + 'px';
        document.body.removeChild(s);
    };
    autoSize();
    inp.addEventListener('input', autoSize);
    inp.addEventListener('keydown', e => { if(e.key === 'Enter'){ e.preventDefault(); finishEditing(inp.value); } if(e.key === 'Escape') finishEditing(null); });
    inp.addEventListener('blur', () => setTimeout(() => finishEditing(inp.value), 80));

    document.getElementById('text-overlay').appendChild(inp);
    editingInput = inp;
    inp.focus(); inp.select();
}

function finishEditing(newValue){
    if(!editingInput) return;
    const text = textElements.find(t => t.id === editingTextId);
    if(newValue !== null && typeof newValue === 'string' && newValue.trim() !== ''){
        if(text) text.content = newValue.trim();
    } else {
        textElements = textElements.filter(t => t.id !== editingTextId);
        if(selectedTextId === editingTextId) selectedTextId = null;
    }
    editingInput.remove(); editingInput = null; editingTextId = null;
    saveTextElements(); refreshTextOverlay();
}

function handleOverlayClick(e){
    if(e.target.id !== 'text-overlay') return;
    if(editingInput) return;

    const x = Math.round(e.offsetX);
    const y = Math.round(e.offsetY);

    const t = {
        id: Date.now(),
        content: 'Click to edit',
        x: Math.max(0, Math.min(x, IFRAME_W - 10)),
        y: Math.max(0, Math.min(y, IFRAME_H - 10)),
        color: currentColor,
        fontSize: currentFontSize,
        page: currentPage
    };
    textElements.push(t);
    selectedTextId = t.id;
    saveTextElements();
    refreshTextOverlay();
    setTimeout(() => startEditing(t.id), 20);
}

function setTextColor(c){
    currentColor = c;
    if(selectedTextId){ const t = textElements.find(t => t.id === selectedTextId); if(t){ t.color = c; saveTextElements(); refreshTextOverlay(); } }
}
function setFontSize(s){
    currentFontSize = s;
    document.querySelectorAll('.fs-btn').forEach(b => {
        const active = parseInt(b.textContent) === s;
        b.style.background = active ? '#6b7280' : '#374151';
        b.style.border = active ? '2px solid #fff' : 'none';
    });
    if(selectedTextId){ const t = textElements.find(t => t.id === selectedTextId); if(t){ t.fontSize = s; saveTextElements(); refreshTextOverlay(); } }
}
function deleteSelectedText(){
    if(!selectedTextId){ showErrorModal('Click a text element first to select it.'); return; }
    if(editingInput) finishEditing(null);
    textElements = textElements.filter(t => t.id !== selectedTextId);
    selectedTextId = null; saveTextElements(); refreshTextOverlay();
}
function clearAllText(){
    if(!confirm('Clear all text on current page?')) return;
    if(editingInput) finishEditing(null);
    textElements = []; 
    selectedTextId = null; 
    saveTextElements(); 
    refreshTextOverlay();
}
function updateSelectedLabel(){
    const el = document.getElementById('selected-text-label');
    if(!el) return;
    const t = selectedTextId ? textElements.find(t => t.id === selectedTextId) : null;
    el.textContent = t ? '"' + t.content.substring(0,22) + (t.content.length > 22 ? '…' : '') + '"' : 'none';
}

async function callSaveEndpoint(){
    const allTextElements = [];
    for (const pageKey in textElementsByPage) {
        const pageTexts = textElementsByPage[pageKey];
        if (pageTexts && pageTexts.length) {
            allTextElements.push(...pageTexts);
        }
    }
    
    const res = await fetch('/applicant/application/save-edited-pdf', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf(),'Accept':'application/json'},
        body: JSON.stringify({
            text_elements: allTextElements,
            application_number: currentApplicationNumber,
            iframe_width: IFRAME_W,
            iframe_height: IFRAME_H,
            total_pages: totalPages,
            template: 'building-permit-application'
        }),
    });
    if(!res.ok){ const e = await res.json().catch(()=>({})); throw new Error(e.error||'Server error'); }
    return res.blob();
}
function triggerDownload(blob,name){
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = name;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
async function saveEditedPDF(){
    const btn = document.getElementById('save-pdf-btn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin" style="width:1rem;height:1rem;display:inline;margin-right:.4rem;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Saving…';
    btn.disabled = true;
    try{
        const blob = await callSaveEndpoint();
        triggerDownload(blob, `building-permit-application-${currentApplicationNumber || 'filled'}.pdf`);
        showSuccessModal('PDF downloaded successfully!');
    }catch(e){ showErrorModal('Failed to save PDF: '+e.message); }
    finally{ btn.innerHTML = orig; btn.disabled = false; }
}

async function downloadSelectedForms(){
    if(!dpaAccepted){
        showErrorModal('Please accept the Data Privacy Act terms first.');
        showDPAModalIfNeeded();
        return;
    }
    
    const sel = []; let hasInteractive = false;
    document.querySelectorAll('.form-checkbox').forEach((cb,i)=>{ if(cb.checked){ sel.push(formCheckboxes[i]); if(formCheckboxes[i].isInteractive) hasInteractive = true; } });
    if(!sel.length){ showErrorModal('Please select at least one form.'); return; }
    
    if(hasInteractive && !applicationNumberGenerated){ 
        if(!await generateAndSaveApplicationNumber()) return; 
    }
    
    const btn = document.getElementById('download-btn');
    if(btn) btn.disabled = true;
    showSuccessModal(`Downloading ${sel.length} file(s)…`);
    for(const form of sel){
        if(form.isInteractive){
            try{ const blob = await callSaveEndpoint(); triggerDownload(blob, `building-permit-application-${currentApplicationNumber || 'filled'}.pdf`); }
            catch{ downloadFile(form.file); }
        }else{ downloadFile(form.file); }
        await new Promise(r => setTimeout(r, 600));
    }
    setTimeout(()=>{ showSuccessModal('All files downloaded!'); if(btn) btn.disabled = false; }, 600);
}
function downloadFile(filename){
    const a = document.createElement('a');
    a.href = `/downloads/${filename}?t=${Date.now()}`; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
}

function selectAllForms(){ document.querySelectorAll('.form-checkbox').forEach(cb => cb.checked = true); updateSelectedCount(); }
function deselectAllForms(){ document.querySelectorAll('.form-checkbox').forEach(cb => cb.checked = false); updateSelectedCount(); }
function updateSelectedCount(){
    let n = 0; document.querySelectorAll('.form-checkbox').forEach(cb => { if(cb.checked) n++; });
    document.getElementById('selected-count').textContent = `${n} form${n !== 1 ? 's' : ''} selected`;
    document.getElementById('download-count').textContent = n;
    document.getElementById('download-btn').disabled = n === 0;
}
function copyApplicationNumber(){
    const n = document.getElementById('application-number')?.textContent;
    if(!n) return;
    navigator.clipboard.writeText(n).then(()=>showSuccessModal('Copied!')).catch(()=>showErrorModal('Copy failed.'));
}

// ─── Go to Step 2 ────────────────────────────────────────────────────
function goToStep2() {
    if (!currentApplicationId) {
        showErrorModal('Application ID not found. Please go back to applications.');
        return;
    }
    
    // Check if application number has been generated or loaded
    if (!applicationNumberGenerated || !currentApplicationNumber) {
        showErrorModal('Please open and edit the Building Permit Application form first to generate your application number.');
        return;
    }
    
    window.location.href = `/applicant/application/step2?id=${currentApplicationId}`;
}

function showErrorModal(message){
    const modal = document.getElementById('error-modal');
    const messageEl = document.getElementById('error-modal-message');
    if(modal && messageEl){
        messageEl.textContent = message;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        alert(message);
    }
}

function closeErrorModal(){
    const modal = document.getElementById('error-modal');
    if(modal){
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showSuccessModal(message){
    const modal = document.getElementById('success-modal');
    const messageEl = document.getElementById('success-modal-message');
    if(modal && messageEl){
        messageEl.textContent = message;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    } else {
        alert(message);
    }
}

function closeSuccessModal(){
    const modal = document.getElementById('success-modal');
    if(modal){
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    // Show DPA modal if not accepted yet
    showDPAModalIfNeeded();
    
    document.querySelectorAll('.form-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));
    updateSelectedCount();
    const overlay = document.getElementById('text-overlay');
    if(overlay) overlay.addEventListener('click', handleOverlayClick);
    
    // If application number exists from server, show the banner
    if (existingApplicationNumber) {
        const banner = document.getElementById('application-number-banner');
        const numberEl = document.getElementById('application-number');
        if (banner && numberEl) {
            numberEl.textContent = existingApplicationNumber;
            banner.classList.remove('hidden');
        }
        console.log('Application number loaded from server:', existingApplicationNumber);
    }
});
</script>

<style>
.animate-spin{animation:spin 1s linear infinite;}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
button:disabled{cursor:not-allowed;opacity:.65;}
.pdf-text-el:hover{border-color:#3b82f6!important;}
#prev-page-btn:disabled, #next-page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endsection