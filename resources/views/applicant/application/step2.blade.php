@extends('layouts.app')

@section('title', 'Upload Documents - Step 2')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-8">
        <a href="/applicant/application/step1" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Step 1
        </a>
    </div>

    <!-- Step Indicator - Step 2 -->
    <div class="mb-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 bg-[#155386] text-white rounded-full font-bold text-sm">2</div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Step 2: Upload Documents to Google Drive</h2>
                <p class="text-l text-gray-600">Upload all your documents to Google Drive and provide the shared link below. All original hard copies must be submitted to our office.</p>
            </div>
        </div>
    </div>

    <!-- Hard Copy Notice -->
    <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-600 rounded-r-lg">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800">Important Reminder</h4>
                <p class="text-sm text-gray-700 mt-1">The Google Drive link is for pre-verification purposes. <span class="font-semibold">You must submit the original hard copies</span> of ALL documents to the Office of the Building Official (OBO) for final processing.</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-medium text-gray-600">Download Forms</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-semibold text-[#155386]">Upload Documents</span>
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

        <!-- Google Drive Upload Section -->
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Google Drive Upload</h2>
            
            <!-- Instructions Card -->
            <div class="mb-8 p-6 bg-yellow-50 rounded-xl border border-yellow-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">How to Upload to Google Drive</h4>
                        <ol class="mt-2 text-sm text-gray-600 list-decimal list-inside space-y-1">
                            <li>Upload all your documents to Google Drive</li>
                            <li>Create a folder named: <span class="font-mono bg-gray-100 px-2 py-1 rounded">APP-[Last Name]-[Application ID]</span></li>
                            <li>Set the sharing permission to <span class="font-semibold">"Anyone with the link can view"</span></li>
                            <li>Copy the shareable link and paste it below</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Document Checklist Summary -->
            <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <h4 class="font-semibold text-gray-800 mb-3">Required Documents (13 items)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Application Letter</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Building Permit Forms</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Architectural Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Civil/Structural Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Electrical Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Sanitary/Plumbing Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Mechanical Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Fencing Plans (5 sets)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Proof of Ownership (2 copies)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Bill of Materials (5 copies)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Structural Design Analysis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Barangay Clearance</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Valid ID</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3 italic">*Optional: CSHP from DOLE (for contractors with PCAB)</p>
            </div>

            <!-- Application Number Display (if exists) -->
            <div id="application-number-display" class="mb-6 hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-lg p-4 text-white">
                    <p class="text-sm opacity-90">Your Application Number</p>
                    <p class="text-2xl font-bold font-mono" id="display-application-number"></p>
                </div>
            </div>

            <!-- Google Drive Link Input -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Google Drive Shareable Link <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <input type="url" 
                               id="gdrive-link" 
                               placeholder="https://drive.google.com/drive/folders/..." 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                        <button onclick="validateAndTestLink()" 
                                class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                            Test Link
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">Make sure the link is shared with "Anyone with the link can view" permission</p>
                </div>
            </div>

            <!-- Link Status -->
            <div class="mt-4 hidden" id="link-status">
                <!-- Status will be shown here -->
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="p-6 pt-0 flex justify-between items-center">
            <a href="/applicant/application/step1" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Previous: Download Forms
            </a>
            
            <button onclick="storeGoogleDriveLink()" 
                    id="proceed-btn"
                    class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Proceed to Review & Submit
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
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
// Store Google Drive link with better error handling
async function storeGoogleDriveLink() {
    const link = document.getElementById('gdrive-link').value.trim();
    const hardcopyConfirmed = document.getElementById('hardcopy-confirm')?.checked || false;
    
    // Debug: Log the values
    console.log('Link:', link);
    console.log('Hardcopy Confirmed:', hardcopyConfirmed);
    
    if (!link) {
        showErrorModal('Please enter your Google Drive link.');
        return;
    }

    // Basic validation for Google Drive links
    const isGoogleDriveLink = link.includes('drive.google.com') || 
                             link.includes('docs.google.com');
    
    if (!isGoogleDriveLink) {
        showErrorModal('Please enter a valid Google Drive link.');
        return;
    }
    

    // Show loading state
    const proceedBtn = document.getElementById('proceed-btn');
    const originalText = proceedBtn.innerHTML;
    proceedBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;
    proceedBtn.disabled = true;

    try {
        // Get application ID from URL if continuing a draft
        const urlParams = new URLSearchParams(window.location.search);
        const applicationId = urlParams.get('id');
        
        // Prepare the request data
        const requestData = {
            google_drive_link: link,
            hardcopy_confirmed: hardcopyConfirmed ? 1 : 0
        };
        
        // If we have an application ID, include it
        if (applicationId) {
            requestData.application_id = applicationId;
        }
        
        console.log('Sending data:', requestData);

        const response = await fetch('{{ route("applicant.application.store-link") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        });

        // Get the response data
        const data = await response.json();
        console.log('Response status:', response.status);
        console.log('Response data:', data);

        if (response.ok && data.success) {
            // Save application number to localStorage
            if (data.data && data.data.application_number) {
                localStorage.setItem('konstructo_app_number', data.data.application_number);
            }
            
            showSuccessModal('Documents link saved successfully! Redirecting...');
            
            // Update status display
            const statusDiv = document.getElementById('link-status');
            statusDiv.className = 'mt-4 p-4 bg-green-50 rounded-lg border border-green-200';
            statusDiv.innerHTML = `
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Documents submitted successfully!</p>
                        <p class="text-xs text-gray-600 mt-1">Application Number: <span class="font-mono font-bold">${data.data.application_number}</span></p>
                        <p class="text-xs text-gray-600">Status: <span class="capitalize">${data.data.status}</span></p>
                    </div>
                </div>
            `;
            statusDiv.classList.remove('hidden');
            
            // Redirect to step 3 after 2 seconds
            setTimeout(() => {
                window.location.href = '/applicant/application/step3';
            }, 2000);
        } else {
            // Handle validation errors
            if (data.errors) {
                const errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(data.errors[field].join(', '));
                }
                showErrorModal(errorMessages.join('\n'));
            } else {
                showErrorModal(data.message || 'Failed to save link. Please try again.');
            }
            // Restore button
            proceedBtn.innerHTML = originalText;
            proceedBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred. Please try again.');
        // Restore button
        proceedBtn.innerHTML = originalText;
        proceedBtn.disabled = false;
    }
}

    // Validate Google Drive link
    function validateAndTestLink() {
        const link = document.getElementById('gdrive-link').value.trim();
        
        if (!link) {
            showErrorModal('Please enter a Google Drive link.');
            return;
        }

        // Basic validation for Google Drive links
        const isGoogleDriveLink = link.includes('drive.google.com') || 
                                 link.includes('docs.google.com') ||
                                 link.includes('drive.google.com/file/d/') ||
                                 link.includes('drive.google.com/drive/folders/');
        
        if (!isGoogleDriveLink) {
            showErrorModal('Please enter a valid Google Drive link.');
            return;
        }

        // Check if link is publicly accessible (simulated)
        // In production, you might want to implement actual link validation
        
        showSuccessModal('Link is valid! Make sure sharing is set to "Anyone with the link".');
        
        // Show status
        const statusDiv = document.getElementById('link-status');
        statusDiv.className = 'mt-4 p-4 bg-green-50 rounded-lg border border-green-200';
        statusDiv.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-800">Link verified successfully!</p>
                    <p class="text-xs text-gray-600 mt-1">Please ensure all 13 required documents are in this Google Drive folder.</p>
                </div>
            </div>
        `;
        statusDiv.classList.remove('hidden');
    }

    // Load existing application data on page load
    document.addEventListener('DOMContentLoaded', async function() {
        // Get application ID from URL if continuing a draft
        const urlParams = new URLSearchParams(window.location.search);
        const applicationId = urlParams.get('id');
        
        console.log('Application ID from URL:', applicationId);
        
        // If we have an application ID, load that specific application
        if (applicationId) {
            // Use the details route with the ID in the path
            const endpoint = `/applicant/application-details/${applicationId}`;
            
            console.log('Fetching from endpoint:', endpoint);

            try {
                const response = await fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                console.log('Loaded application data:', data);
                
                if (data.success && data.data) {
                    // Pre-fill the form with existing data
                    if (data.data.google_drive_link) {
                        document.getElementById('gdrive-link').value = data.data.google_drive_link;
                    }
                    
                    // Show application number if exists
                    if (data.data.application_number) {
                        const appNumberDisplay = document.getElementById('application-number-display');
                        document.getElementById('display-application-number').textContent = data.data.application_number;
                        appNumberDisplay.classList.remove('hidden');
                        
                        // Also update localStorage
                        localStorage.setItem('konstructo_app_number', data.data.application_number);
                    }
                    
                    // Show status based on application status
                    const statusDiv = document.getElementById('link-status');
                    
                    if (data.data.status === 'pending') {
                        statusDiv.className = 'mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200';
                        statusDiv.innerHTML = `
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Documents under review</p>
                                    <p class="text-xs text-gray-600 mt-1">Application Number: <span class="font-mono font-bold">${data.data.application_number}</span></p>
                                    <p class="text-xs text-gray-600">Submitted on: ${data.data.submitted_at || 'N/A'}</p>
                                </div>
                            </div>
                        `;
                        statusDiv.classList.remove('hidden');
                        
                        // Disable the proceed button if already submitted
                        document.getElementById('proceed-btn').disabled = true;
                        document.getElementById('proceed-btn').classList.add('opacity-50', 'cursor-not-allowed');
                        
                    } else if (data.data.status === 'verified') {
                        statusDiv.className = 'mt-4 p-4 bg-green-50 rounded-lg border border-green-200';
                        statusDiv.innerHTML = `
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Documents verified!</p>
                                    <p class="text-xs text-gray-600 mt-1">Application Number: <span class="font-mono font-bold">${data.data.application_number}</span></p>
                                    <p class="text-xs text-gray-600">You may now proceed to the next step.</p>
                                </div>
                            </div>
                        `;
                        statusDiv.classList.remove('hidden');
                        
                    } else if (data.data.status === 'rejected') {
                        statusDiv.className = 'mt-4 p-4 bg-red-50 rounded-lg border border-red-200';
                        statusDiv.innerHTML = `
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Documents rejected</p>
                                    <p class="text-xs text-gray-600 mt-1">Reason: ${data.data.rejection_reason || 'Not specified'}</p>
                                    <p class="text-xs text-gray-600 mt-1">Please upload correct documents and resubmit.</p>
                                </div>
                            </div>
                        `;
                        statusDiv.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error loading application data:', error);
            }
        } else {
            // No application ID in URL - this is a new application
            console.log('No application ID found - this is a new application');
            // You might want to check if there's a draft in session or just start fresh
        }
    });

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
        
        // Auto-hide success modal after 3 seconds
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    }

    function closeSuccessModal() {
        document.getElementById('success-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
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

    /* Spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Disabled button styling */
    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
@endsection