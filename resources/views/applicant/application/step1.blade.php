@extends('layouts.app')

@section('title', 'Application')

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
<!-- Application Number Banner - Hidden by default, shows when app letter is downloaded -->
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
                        <p class="text-2xl font-bold text-white font-mono" id="application-number">APP-2025-001</p>
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

        <!-- Instructions Card (Moved to full width) -->
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
               class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Next Step: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

    </div>

</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <!-- Error Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
                
                <button onclick="closeErrorModal()" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
                <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
                
                <button onclick="closeSuccessModal()" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Flag to track if application number has been generated
    let applicationNumberGenerated = false;
    
    // Generate a random application number (in production, this would come from the server)
    function generateApplicationNumber() {
        const prefix = 'APP';
        const year = new Date().getFullYear();
        const random = Math.floor(1000 + Math.random() * 9000);
        return `${prefix}-${year}-${random}`;
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

    // Update selected count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) count++;
        });
        
        document.getElementById('selected-count').textContent = `${count} form${count !== 1 ? 's' : ''} selected`;
        document.getElementById('download-count').textContent = count;
        
        // Enable/disable download button
        const downloadBtn = document.getElementById('download-btn');
        if (count > 0) {
            downloadBtn.disabled = false;
        } else {
            downloadBtn.disabled = true;
        }
    }

    // Select all forms
    function selectAllForms() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        updateSelectedCount();
    }

    // Deselect all forms
    function deselectAllForms() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        updateSelectedCount();
    }

    // Show application number banner
    function showApplicationNumber() {
        if (!applicationNumberGenerated) {
            const appNumber = generateApplicationNumber();
            document.getElementById('application-number').textContent = appNumber;
            document.getElementById('application-number-banner').classList.remove('hidden');
            applicationNumberGenerated = true;
            
            // Also store in localStorage so it persists across page refreshes
            localStorage.setItem('konstructo_app_number', appNumber);
        }
    }

    // Copy application number to clipboard
    function copyApplicationNumber() {
        const appNumber = document.getElementById('application-number').textContent;
        navigator.clipboard.writeText(appNumber).then(() => {
            showSuccessModal('Application number copied to clipboard!');
        }).catch(() => {
            showErrorModal('Failed to copy application number.');
        });
    }

    // Download selected forms
    function downloadSelectedForms() {
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

        // Show application number if Application Letter is being downloaded
        if (hasAppLetter) {
            showApplicationNumber();
        }

        // Simulate downloading each selected form
        selectedForms.forEach((form, i) => {
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = `/downloads/${form.file}`;
                link.download = form.file;
                link.click();
                
                if (i === selectedForms.length - 1) {
                    showSuccessModal(`Downloaded ${selectedForms.length} form${selectedForms.length !== 1 ? 's' : ''} successfully!`);
                }
            }, i * 500);
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
    }

    function closeSuccessModal() {
        document.getElementById('success-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Add event listeners to checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.form-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        // Initialize count
        updateSelectedCount();

        // Check if application number exists in localStorage
        const savedAppNumber = localStorage.getItem('konstructo_app_number');
        if (savedAppNumber) {
            document.getElementById('application-number').textContent = savedAppNumber;
            document.getElementById('application-number-banner').classList.remove('hidden');
            applicationNumberGenerated = true;
        }

        // Modal close handlers
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        
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

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeErrorModal();
                closeSuccessModal();
            }
        });
    });
</script>

<style>
    /* Modal animations */
    #error-modal, #success-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #error-modal .bg-white, #success-modal .bg-white {
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

    /* Form checkbox styling */
    .form-checkbox {
        cursor: pointer;
    }

    .form-checkbox:checked + div {
        border-color: #155386;
    }
    
    /* Animation for banner */
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
</style>
@endsection