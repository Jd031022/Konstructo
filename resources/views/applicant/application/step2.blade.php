@extends('layouts.app')

@section('title', 'Application - Step 2: Download Forms')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="/applicant/application/step1?id={{ $application->id }}" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Step 1: Project Information
        </a>
    </div>

    <!-- Step Indicator -->
    <div class="mb-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full font-bold text-sm">1</div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Step 2: Download Forms</h2>
                <p class="text-l text-gray-600">Select the forms you need and download them. Fill them out and upload in the next step.</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-medium text-gray-400">Project Info</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-semibold text-[#155386]">Download Forms</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-medium text-gray-400">Upload Docs</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
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
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span><span>Click "Download Selected" to download all chosen forms</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-[#155386] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span><span>Print and fill out the forms completely using black ink</span></li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Important Reminders:</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-green-600 text-white rounded-full flex items-center justify-center text-xs">1</span><span>Each form can only be downloaded once</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs">2</span><span>Sign the forms where required (blue ink preferred)</span></li>
                            <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs">3</span><span>Scan or take clear photos of the accomplished forms</span></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-white/50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600 flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span><span class="font-medium">Tip:</span> Once you download a form, it will be marked as downloaded and cannot be downloaded again.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Forms Checklist - Priority Order -->
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
                <!-- 1. Building Permit Application (Primary) -->
                <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border-2 border-[#155386] transition relative overflow-hidden">
                    <input type="checkbox" id="form-building-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-building-permit" class="font-medium text-gray-800 cursor-pointer">Building Permit Application</label>
                        <p class="text-xs text-gray-500">Main application form - Required for all construction projects</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- 3. Architectural Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-architectural-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-architectural-permit" class="font-medium text-gray-800 cursor-pointer">Architectural Permit</label>
                        <p class="text-xs text-gray-500">For architectural works - Signed and sealed by licensed architect</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- 4. Civil/Structural Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-civil-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-civil-permit" class="font-medium text-gray-800 cursor-pointer">Civil/Structural Permit</label>
                        <p class="text-xs text-gray-500">For structural works - Signed and sealed by licensed civil engineer</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- 5. Electrical Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electrical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electrical-permit" class="font-medium text-gray-800 cursor-pointer">Electrical Permit</label>
                        <p class="text-xs text-gray-500">For electrical works - Signed and sealed by licensed electrical engineer</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- 6. Sanitary/Plumbing Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sanitary-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sanitary-permit" class="font-medium text-gray-800 cursor-pointer">Sanitary/Plumbing Permit</label>
                        <p class="text-xs text-gray-500">For plumbing and sanitary works - Signed and sealed by licensed sanitary engineer or master plumber</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                        <!-- 2. Zoning Compliance -->
        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
            <input type="checkbox" id="form-zoning-compliance" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
            <div class="flex-1">
                <label for="form-zoning-compliance" class="font-medium text-gray-800 cursor-pointer">Zoning Compliance / Locational Clearance</label>
                <p class="text-xs text-gray-500">Certificate from the City Planning and Development Office - Verifies project complies with zoning regulations</p>
            </div>
            <span class="text-xs text-gray-400">PDF</span>
        </div>

                <!-- Divider for Optional Forms -->
                <div class="md:col-span-2 mt-2 mb-2">
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Optional Forms (if applicable to your project)</p>
                    </div>
                </div>

                <!-- Optional: Mechanical Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-mechanical-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-mechanical-permit" class="font-medium text-gray-800 cursor-pointer">Mechanical Permit</label>
                        <p class="text-xs text-gray-500">For mechanical installations (elevators, HVAC, etc.)</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Optional: Electronics Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-electronics-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-electronics-permit" class="font-medium text-gray-800 cursor-pointer">Electronics Permit</label>
                        <p class="text-xs text-gray-500">For electronics systems (fire alarms, security systems, etc.)</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Optional: Sign Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-sign-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-sign-permit" class="font-medium text-gray-800 cursor-pointer">Sign Permit Application</label>
                        <p class="text-xs text-gray-500">For signage and billboards</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Optional: Fencing Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-fencing-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-fencing-permit" class="font-medium text-gray-800 cursor-pointer">Fencing Permit</label>
                        <p class="text-xs text-gray-500">For fencing construction</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>

                <!-- Optional: Demolition Permit -->
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#155386] transition">
                    <input type="checkbox" id="form-demolition-permit" class="form-checkbox h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386] mt-0.5">
                    <div class="flex-1">
                        <label for="form-demolition-permit" class="font-medium text-gray-800 cursor-pointer">Demolition Permit</label>
                        <p class="text-xs text-gray-500">For demolition works</p>
                    </div>
                    <span class="text-xs text-gray-400">PDF</span>
                </div>
            </div>

            <!-- Download Button -->
            <div class="flex items-center justify-between p-6 bg-blue-50 rounded-xl border border-blue-200">
                <div>
                    <p class="font-medium text-gray-800" id="selected-count">0 forms selected</p>
                    <p class="text-sm text-gray-600">Download all selected forms as individual PDFs (each form can only be downloaded once)</p>
                </div>
                <button onclick="downloadSelectedForms()" id="download-btn"
                        class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download Selected (<span id="download-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Next Step Button -->
        <div class="p-8 pt-0 flex justify-end">
            <button onclick="markStep2Complete()" id="next-step-btn"
                    class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Continue to Step 3: Upload Documents
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
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

<script>
'use strict';

const applicationId = {{ $application->id }};

// Track downloaded forms to prevent re-download
let downloadedForms = JSON.parse(localStorage.getItem(`downloaded_forms_${applicationId}`) || '{}');

// Priority forms first, then optional (Added Zoning Compliance)
const formCheckboxes = [
    // Priority Forms (Required)
    { id:'form-building-permit',        name:'Building Permit Application',     file:'building-permit-application.pdf', priority: true },
    { id:'form-zoning-compliance',      name:'Zoning Compliance / Locational Clearance', file:'zoning-compliance.pdf', priority: true },
    { id:'form-architectural-permit',   name:'Architectural Permit',           file:'architectural-permit.pdf', priority: true },
    { id:'form-civil-permit',           name:'Civil/Structural Permit',        file:'civil-structural-permit.pdf', priority: true },
    { id:'form-electrical-permit',      name:'Electrical Permit',              file:'electrical-permit.pdf', priority: true },
    { id:'form-sanitary-permit',        name:'Sanitary/Plumbing Permit',       file:'sanitary-plumbing-permit.pdf', priority: true },
    // Optional Forms
    { id:'form-mechanical-permit',      name:'Mechanical Permit',              file:'mechanical-permit.pdf', priority: false },
    { id:'form-electronics-permit',     name:'Electronics Permit',             file:'electronics-permit.pdf', priority: false },
    { id:'form-sign-permit',            name:'Sign Permit Application',        file:'sign-permit-application.pdf', priority: false },
    { id:'form-fencing-permit',         name:'Fencing Permit',                 file:'fencing-permit.pdf', priority: false },
    { id:'form-demolition-permit',      name:'Demolition Permit',              file:'demolition-permit.pdf', priority: false }
];

function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'; }

// ─── Download Functions ───────────────────────────────────────────────────────
function downloadFile(filename){
    const a = document.createElement('a');
    a.href = `/downloads/${filename}?t=${Date.now()}`;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

async function downloadSelectedForms(){
    const selectedForms = [];
    
    formCheckboxes.forEach((form) => {
        const checkbox = document.getElementById(form.id);
        if(checkbox && checkbox.checked){
            if(downloadedForms[form.id]){
                showErrorModal(`${form.name} has already been downloaded. Each form can only be downloaded once.`);
                checkbox.checked = false;
                return;
            }
            selectedForms.push(form);
        }
    });
    
    if(selectedForms.length === 0){
        showErrorModal('Please select at least one form to download.');
        return;
    }
    
    const downloadBtn = document.getElementById('download-btn');
    if(downloadBtn) downloadBtn.disabled = true;
    
    showSuccessModal(`Downloading ${selectedForms.length} file(s)...`);
    
    for(const form of selectedForms){
        downloadedForms[form.id] = true;
        localStorage.setItem(`downloaded_forms_${applicationId}`, JSON.stringify(downloadedForms));
        
        const checkbox = document.getElementById(form.id);
        if(checkbox){
            checkbox.disabled = true;
            checkbox.checked = false;
            const parentDiv = checkbox.closest('.flex');
            if(parentDiv){
                parentDiv.classList.add('opacity-60', 'bg-gray-100');
                parentDiv.classList.remove('bg-gray-50', 'bg-gradient-to-r');
            }
        }
        
        downloadFile(form.file);
        await new Promise(r => setTimeout(r, 600));
    }
    
    updateSelectedCount();
    
    setTimeout(()=>{ 
        showSuccessModal('All selected files downloaded!'); 
        if(downloadBtn) downloadBtn.disabled = false;
    }, 600);
}

function selectAllForms(){
    formCheckboxes.forEach(form => {
        const cb = document.getElementById(form.id);
        if(cb && !downloadedForms[form.id]){
            cb.checked = true;
        }
    });
    updateSelectedCount();
}

function deselectAllForms(){
    formCheckboxes.forEach(form => {
        const cb = document.getElementById(form.id);
        if(cb){
            cb.checked = false;
        }
    });
    updateSelectedCount();
}

function updateSelectedCount(){
    let count = 0;
    formCheckboxes.forEach(form => {
        const cb = document.getElementById(form.id);
        if(cb && cb.checked) count++;
    });
    document.getElementById('selected-count').textContent = `${count} form${count !== 1 ? 's' : ''} selected`;
    document.getElementById('download-count').textContent = count;
    const downloadBtn = document.getElementById('download-btn');
    if(downloadBtn) downloadBtn.disabled = count === 0;
}

// ─── Mark Step 2 Complete and Continue ───────────────────────────────────────
async function markStep2Complete(){
    // Check if at least the Building Permit Application has been downloaded
    if(!downloadedForms['form-building-permit']){
        showErrorModal('Please download the Building Permit Application first before proceeding.');
        return;
    }
    
    const btn = document.getElementById('next-step-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
    btn.disabled = true;
    
    try {
        const response = await fetch(`/applicant/application/step2/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                application_id: applicationId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessModal('Step 2 completed! Redirecting to Step 3...');
            setTimeout(() => {
                window.location.href = `/applicant/application/step3?id=${applicationId}`;
            }, 1500);
        } else {
            showErrorModal(data.message || 'Failed to save progress');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred. Please try again.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// ─── Modal Functions ─────────────────────────────────────────────────────────
function showErrorModal(message){
    const modal = document.getElementById('error-modal');
    const messageEl = document.getElementById('error-modal-message');
    if(modal && messageEl){
        messageEl.textContent = message;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else { alert(message); }
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
        setTimeout(() => { closeSuccessModal(); }, 3000);
    } else { alert(message); }
}

function closeSuccessModal(){
    const modal = document.getElementById('success-modal');
    if(modal){
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// ─── Initialize Checkbox States ──────────────────────────────────────────────
function initializeCheckboxStates(){
    formCheckboxes.forEach(form => {
        const cb = document.getElementById(form.id);
        if(cb && downloadedForms[form.id]){
            cb.disabled = true;
            const parentDiv = cb.closest('.flex');
            if(parentDiv){
                parentDiv.classList.add('opacity-60', 'bg-gray-100');
                parentDiv.classList.remove('bg-gray-50', 'bg-gradient-to-r');
            }
        }
    });
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    initializeCheckboxStates();
    document.querySelectorAll('.form-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));
    updateSelectedCount();
});
</script>

<style>
.animate-spin{animation:spin 1s linear infinite;}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
button:disabled{cursor:not-allowed;opacity:.65;}
</style>
@endsection