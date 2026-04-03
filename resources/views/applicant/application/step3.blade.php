@extends('layouts.app')

@section('title', 'Review & Submit - Step 3')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-8">
        <a href="/applicant/application/step2?id={{ $application->id }}" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Upload Documents
        </a>
    </div>

    <!-- Step Indicator - Step 3 -->
    <div class="mb-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 bg-[#155386] text-white rounded-full font-bold text-sm">3</div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Step 3: Review & Submit</h2>
                <p class="text-l text-gray-600">Review your downloaded forms and Google Drive documents before final submission</p>
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
                <p class="text-sm text-gray-700 mt-1">After online submission, <span class="font-semibold">you must submit the original hard copies of ALL required documents</span> to the Office of the Building Official (OBO) for final processing.</p>
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
                <span class="text-sm font-medium text-gray-600">Google Drive Upload</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-semibold text-[#155386]">Review & Submit</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Review Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white">
            <h2 class="text-2xl font-bold">Review Your Application</h2>
            <p class="text-white/80 text-sm mt-1">Please verify all information before submitting</p>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="p-12 text-center">
            <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">Loading your application...</p>
        </div>

        <!-- Review Content (initially hidden) -->
        <div id="review-content" class="hidden">
            <div class="p-8 space-y-8">

                <!-- Application Number Banner -->
                <div id="application-number-banner" class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-lg p-4 text-white">
                    <p class="text-sm opacity-90">Your Application Number</p>
                    <p class="text-2xl font-bold font-mono" id="display-application-number"></p>
                </div>

                <!-- Step 1: Downloaded Forms Summary -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Downloaded Forms</h3>
                        <a href="/applicant/application/step1?id={{ request()->get('id') }}" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div id="downloaded-forms-container" class="space-y-2">
                            <!-- Downloaded forms will be loaded dynamically here -->
                        </div>
                    </div>
                </div>

                <!-- Google Drive Documents Review -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Google Drive Documents</h3>
                        <a href="/applicant/application/step2?id={{ request()->get('id') }}" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                    
                    <div id="drive-link-display" class="bg-gray-50 rounded-xl p-6">
                        <div id="document-links-container" class="space-y-3">
                            <!-- Document links will be loaded dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Required Documents Reminder -->
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Required Documents Reminder</p>
                            <p class="text-xs text-gray-600 mt-1">Please ensure all 13 required documents are uploaded to your Google Drive folder before submitting.</p>
                        </div>
                    </div>
                </div>

                <!-- Declaration Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Declaration</h4>
                                <p class="text-sm text-gray-600 mt-1">I hereby certify that the information provided and documents uploaded to Google Drive are true and correct to the best of my knowledge. I understand that any false statement or misrepresentation may result in the denial or revocation of my application and may subject me to legal consequences.</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex items-center gap-3">
                            <input type="checkbox" id="agree-checkbox" class="h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                            <label for="agree-checkbox" class="text-sm text-gray-700">I agree to the terms and conditions and confirm that all information provided is accurate</label>
                        </div>
                    </div>
                </div>

                <!-- Hard Copy Confirmation Section -->
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 text-lg">Hard Copy Submission Confirmation</h4>
                            <p class="text-sm text-gray-600 mt-1">I confirm that all documents are uploaded to Google Drive and I will submit the original hard copies to the Office of the Building Official (OBO) for final processing.</p>
                            
                            <div class="mt-4 flex items-center gap-3">
                                <input type="checkbox" id="hardcopy-confirm" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                                <label for="hardcopy-confirm" class="text-sm font-medium text-gray-700">I confirm that I will submit all original hard copies to the OBO</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Summary -->
                <div class="bg-[#155386]/5 rounded-xl p-6 border border-[#155386]/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Processing Time</p>
                            <p class="text-lg font-semibold text-gray-800">7-10 business days</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Application Number</p>
                            <p class="text-lg font-semibold text-gray-800 font-mono" id="summary-app-number">-</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="p-8 pt-0 flex justify-end">
                <button onclick="submitApplication()" 
                        id="submit-button"
                        class="inline-flex items-center px-10 py-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-semibold text-lg shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Submit Application
                </button>
            </div>
        </div>

    </div>

    <!-- Help Card -->
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mt-8">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Final Review</h4>
                <p class="text-sm text-gray-600">
                    Please double-check all information before submitting. After online submission, bring all original hard copies to the OBO.
                </p>
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
    let applicationData = null;
    let currentApplicationId = null;
    let documentLinks = {};

    // Get application ID from URL
    function getApplicationId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    // Go back to Step 2 with application ID
    function goBack() {
        const appId = getApplicationId();
        if (appId) {
            window.location.href = `/applicant/application/step2?id=${appId}`;
        } else {
            window.location.href = '/applicant/application/step2';
        }
    }

    // Load application data on page load
    document.addEventListener('DOMContentLoaded', function() {
        currentApplicationId = getApplicationId();
        
        if (!currentApplicationId) {
            showErrorModal('Application ID not found. Please start from Step 1.');
            setTimeout(() => {
                window.location.href = '/applicant/applications';
            }, 2000);
            return;
        }
        
        loadApplicationData();
        
        // Modal close handlers
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeErrorModal();
                closeSuccessModal();
            }
        });
    });

    // Load application data from API
    async function loadApplicationData() {
        try {
            console.log('Loading application data for ID:', currentApplicationId);
            
            const response = await fetch(`/applicant/application/details?application_id=${currentApplicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            console.log('Application data:', result);
            
            if (result.success && result.data) {
                applicationData = result.data;
                
                // Check the status - only redirect if it's already pending/verified
                if (applicationData.status === 'pending' || applicationData.status === 'verified') {
                    showErrorModal('This application has already been submitted.');
                    setTimeout(() => {
                        window.location.href = '/applicant/applications';
                    }, 2000);
                    return;
                }
                
                // Display the data
                displayApplicationData();
            } else {
                showErrorModal('No application data found. Please complete Step 2 first.');
                setTimeout(() => {
                    window.location.href = `/applicant/application/step2?id=${currentApplicationId}`;
                }, 2000);
            }
        } catch (error) {
            console.error('Error loading application data:', error);
            showErrorModal('Failed to load application data. Please try again.');
        }
    }

    // Display application data
    function displayApplicationData() {
        // Hide loading state
        document.getElementById('loading-state').classList.add('hidden');
        
        // Show review content
        document.getElementById('review-content').classList.remove('hidden');
        
        // Display application number
        if (applicationData.application_number) {
            document.getElementById('display-application-number').textContent = applicationData.application_number;
            document.getElementById('summary-app-number').textContent = applicationData.application_number;
        } else {
            document.getElementById('display-application-number').textContent = 'Pending';
            document.getElementById('summary-app-number').textContent = 'Pending';
        }
        
        // Get document links
        documentLinks = applicationData.document_links || {};
        
        // Display document links
        displayDocumentLinks();
        
        // Display downloaded forms
        displayDownloadedForms();
        
        // Set up checkbox listeners
        setupCheckboxListeners();
    }

    // Display document links
    function displayDocumentLinks() {
        const container = document.getElementById('document-links-container');
        
        // Define document names for display
        const documentNames = {
            'app_letter_link': 'Application Letter',
            'bp_forms_link': 'Building Permit Forms',
            'arch_plans_link': 'Architectural Plans (5 sets)',
            'structural_plans_link': 'Civil/Structural Plans (5 sets)',
            'electrical_plans_link': 'Electrical Plans (5 sets)',
            'plumbing_plans_link': 'Sanitary/Plumbing Plans (5 sets)',
            'mechanical_plans_link': 'Mechanical Plans (5 sets)',
            'fencing_plans_link': 'Fencing Plans (5 sets)',
            'ownership_link': 'Proof of Ownership (2 copies)',
            'bom_link': 'Bill of Materials (5 copies)',
            'structural_analysis_link': 'Structural Design Analysis',
            'barangay_clearance_link': 'Barangay Clearance',
            'valid_id_link': 'Valid ID'
        };
        
        // Count how many documents are uploaded
        let uploadedCount = 0;
        let documentHtml = '';
        
        if (documentLinks && Object.keys(documentLinks).length > 0) {
            for (const [key, value] of Object.entries(documentLinks)) {
                if (value && value.trim()) {
                    uploadedCount++;
                    const displayName = documentNames[key] || key.replace(/_/g, ' ').replace(/_link$/, '');
                    documentHtml += `
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">${displayName}</p>
                                    <p class="text-xs text-gray-500 truncate max-w-md">${value.substring(0, 50)}${value.length > 50 ? '...' : ''}</p>
                                </div>
                            </div>
                            <a href="${value}" target="_blank" class="text-xs text-[#155386] hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                View
                            </a>
                        </div>
                    `;
                }
            }
        }
        
        if (uploadedCount === 0) {
            container.innerHTML = `
                <div class="p-4 bg-white rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">No documents have been uploaded to Google Drive yet.</p>
                    <a href="/applicant/application/step2?id=${currentApplicationId}" class="text-xs text-[#155386] hover:underline mt-2 inline-block">Go to Step 2 to upload documents</a>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="mb-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-700">${uploadedCount} out of 13 required documents uploaded</p>
                    </div>
                </div>
                ${documentHtml}
            `;
        }
    }

    // Display downloaded forms
    function displayDownloadedForms() {
        const container = document.getElementById('downloaded-forms-container');
        
        container.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Application Letter</p>
                </div>
                <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Downloaded</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Building Permit Forms</p>
                </div>
                <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Downloaded</span>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Additional forms can be downloaded in Step 1 if needed.
            </div>
        `;
    }

    // Setup checkbox listeners
    function setupCheckboxListeners() {
        const agreeCheckbox = document.getElementById('agree-checkbox');
        const hardcopyCheckbox = document.getElementById('hardcopy-confirm');
        
        if (agreeCheckbox) {
            agreeCheckbox.addEventListener('change', updateSubmitButton);
        }
        
        if (hardcopyCheckbox) {
            hardcopyCheckbox.addEventListener('change', updateSubmitButton);
        }

        updateSubmitButton();
    }

    // Enable submit button only when both checkboxes are checked and documents are uploaded
    function updateSubmitButton() {
        const agreeChecked = document.getElementById('agree-checkbox').checked;
        const hardcopyChecked = document.getElementById('hardcopy-confirm')?.checked || false;
        
        // Check if documents are uploaded
        const hasDocuments = documentLinks && Object.keys(documentLinks).length > 0;
        
        const submitBtn = document.getElementById('submit-button');
        submitBtn.disabled = !(agreeChecked && hardcopyChecked && hasDocuments);
        
        // Update button text based on document status
        if (!hasDocuments) {
            submitBtn.title = 'Please upload your documents in Step 2 first';
        }
    }

    // Submit application
    async function submitApplication() {
        const agreeChecked = document.getElementById('agree-checkbox').checked;
        const hardcopyChecked = document.getElementById('hardcopy-confirm')?.checked || false;
        
        if (!agreeChecked) {
            showErrorModal('Please agree to the terms and conditions.');
            return;
        }
        
        if (!hardcopyChecked) {
            showErrorModal('Please confirm that you will submit all original hard copies to the OBO.');
            return;
        }

        // Show loading state
        const button = document.getElementById('submit-button');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting...
        `;
        button.disabled = true;

        try {
            const response = await fetch('/applicant/application/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    application_id: currentApplicationId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessModal('Application submitted successfully! Please bring all original hard copies to the OBO for final processing.');
                
                // Clear session storage
                sessionStorage.removeItem('konstructo_current_app_id');
                sessionStorage.removeItem('konstructo_current_app_number');
                
                // Redirect to applications list
                setTimeout(() => {
                    window.location.href = '/applicant/applications';
                }, 3000);
            } else {
                showErrorModal(result.message || 'Failed to submit application. Please try again.');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        } catch (error) {
            console.error('Error submitting application:', error);
            showErrorModal('Failed to submit application. Please try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        }
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
</script>

<style>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

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

.break-all {
    word-break: break-all;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.max-w-md {
    max-width: 28rem;
}
</style>
@endsection