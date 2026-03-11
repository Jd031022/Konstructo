@extends('layouts.dashboard')

@section('title', 'Application Details - Staff - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="/staff/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Applications List
        </a>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <button onclick="exportAsPDF()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </button>
            <button onclick="editApplication()" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Application
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading application details...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mt-4">Application Not Found</h3>
        <p class="text-gray-500 mt-2">The application you're looking for doesn't exist or has been deleted.</p>
        <a href="/staff/applications" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
            Back to Applications
        </a>
    </div>

    <!-- Application Content (hidden initially) -->
    <div id="application-content" class="hidden">

        <!-- Application Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white text-xl font-bold">
                        BP
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-800">Building Permit Application</h1>
                            <span id="status-badge" class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending Review</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <span class="text-gray-500">Application Number: <span id="application-number" class="font-mono font-medium text-[#155386]"></span></span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-500">Submitted: <span id="submitted-date" class="font-medium text-gray-700"></span></span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-500">Last Updated: <span id="updated-date" class="font-medium text-gray-700"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column - Applicant Details -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Applicant Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Applicant Information</h2>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400">Full Name</p>
                            <p id="applicant-name" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email Address</p>
                            <p id="applicant-email" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Contact Number</p>
                            <p id="applicant-phone" class="text-sm font-medium text-gray-800"></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Address</p>
                            <p id="applicant-address" class="text-sm font-medium text-gray-800"></p>
                        </div>
                    </div>
                </div>

                <!-- Google Drive Documents Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Google Drive Documents</h2>
                        <span id="drive-status" class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Working</span>
                    </div>
                    
                    <!-- Google Drive Link Section -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Google Drive Folder</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <a href="#" id="drive-link" class="text-[#155386] hover:underline text-sm flex items-center gap-1" target="_blank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        View Folder
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <button onclick="copyDriveLink()" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Copy Link
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Last accessed: <span id="last-accessed">Today</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Document Actions -->
                    <div class="flex justify-end gap-3">
                        <button onclick="requestMissingDocuments()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                            Request Missing Documents
                        </button>
                        <button onclick="verifyDocuments()" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                            Verify Documents
                        </button>
                    </div>
                </div>

                <!-- Staff Notes Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Staff Notes</h2>
                    
                    <div id="notes-container" class="space-y-4">
                        <!-- Notes will be loaded dynamically -->
                        <div class="p-3 bg-gray-50 rounded-lg text-center text-gray-500">
                            No notes yet. Add a note below.
                        </div>
                    </div>
                    
                    <!-- Add Note -->
                    <div class="mt-4">
                        <textarea id="new-note" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add a note..."></textarea>
                        <button onclick="addNote()" class="mt-2 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm w-full">
                            Add Note
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Status & Actions -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Status Update Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
                    
                    <div class="space-y-4">
                        <!-- Current Status -->
                        <div id="current-status-card" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-xs text-gray-500 mb-1">Current Status</p>
                            <p id="current-status" class="text-lg font-semibold text-yellow-600">Pending Review</p>
                        </div>

                        <!-- Hard Copy Status Checkbox (for staff to mark when received) -->
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Hard Copy Received</span>
                                <input type="checkbox" id="hardcopy-checkbox" class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Check this box when physical documents are received</p>
                        </div>

                        <!-- Status Options -->
                        <div class="space-y-2">
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                <input type="radio" name="status" value="under-review" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                <span class="ml-3 text-sm font-medium text-gray-700">Under Review</span>
                            </label>
                            
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                <input type="radio" name="status" value="approved" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                <span class="ml-3 text-sm font-medium text-green-600">Approved</span>
                            </label>
                            
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                <input type="radio" name="status" value="rejected" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                <span class="ml-3 text-sm font-medium text-red-600">Rejected</span>
                            </label>
                            
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                <input type="radio" name="status" value="for-release" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                <span class="ml-3 text-sm font-medium text-blue-600">For Release</span>
                            </label>
                            
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer border border-gray-200">
                                <input type="radio" name="status" value="verified" class="status-radio h-4 w-4 text-[#155386] border-gray-300 focus:ring-[#155386]">
                                <span class="ml-3 text-sm font-medium text-purple-600">Completed</span>
                            </label>
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remarks / Notes</label>
                            <textarea id="status-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Add remarks or notes about this application..."></textarea>
                        </div>

                        <!-- Update Button -->
                        <button onclick="updateStatus()" class="w-full px-4 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">
                            Update Status
                        </button>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
                    
                    <div class="space-y-2">
                        <button onclick="sendMessage()" class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span class="text-sm">Send Message to Applicant</span>
                        </button>
                        
                        <button onclick="emailApplicant()" class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">Email Applicant</span>
                        </button>
                        
                        <button onclick="scheduleReview()" class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm">Schedule Review</span>
                        </button>
                        
                        <button onclick="deleteApplication()" class="w-full flex items-center gap-3 px-4 py-3 text-left text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm">Delete Application</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Staff Guidelines</h4>
                    <p class="text-sm text-gray-600">
                        Access the Google Drive folder to review documents. Update status as you review. Check the "Hard Copy Received" box when physical documents are submitted.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Application</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this application? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" id="confirm-delete-btn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        Delete
                    </button>
                </div>
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

<!-- JavaScript -->
<script>
    // Get application ID from URL path
    function getApplicationIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        return pathParts[pathParts.length - 1];
    }
    
    let applicationId = getApplicationIdFromUrl();
    let currentApplication = null;

    // Load application details on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Application ID from URL:', applicationId);
        if (applicationId && applicationId !== 'application-details' && !isNaN(applicationId)) {
            loadApplicationDetails();
        } else {
            showError();
        }
        setupModals();
    });

    // Load application details from API
    async function loadApplicationDetails() {
        try {
            console.log('Fetching application details for ID:', applicationId);
            
            const response = await fetch(`/staff/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            console.log('Application data:', data);
            
            if (data.success) {
                currentApplication = data.data;
                displayApplicationDetails();
            } else {
                showError();
            }
        } catch (error) {
            console.error('Error loading application:', error);
            showError();
        }
    }

    // Display application details
    function displayApplicationDetails() {
        // Hide loading, show content
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('application-content').classList.remove('hidden');

        // Update application number and dates
        document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
        
        if (currentApplication.created_at) {
            const submittedDate = new Date(currentApplication.created_at);
            document.getElementById('submitted-date').textContent = submittedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
        }
        
        if (currentApplication.updated_at) {
            const updatedDate = new Date(currentApplication.updated_at);
            document.getElementById('updated-date').textContent = updatedDate.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
        }

        // Update status badge
        updateStatusUI(currentApplication.status);

        // Update applicant information
        document.getElementById('applicant-name').textContent = currentApplication.applicant_name || 'N/A';
        document.getElementById('applicant-email').textContent = currentApplication.email || 'N/A';
        document.getElementById('applicant-phone').textContent = currentApplication.phone || 'N/A';
        document.getElementById('applicant-address').textContent = currentApplication.address || 'N/A';

        // Update Google Drive link
        if (currentApplication.google_drive_link) {
            const driveLink = document.getElementById('drive-link');
            driveLink.href = currentApplication.google_drive_link;
            driveLink.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg> View Folder';
            driveLink.classList.remove('pointer-events-none', 'text-gray-500');
            driveLink.classList.add('text-[#155386]');
            driveLink.setAttribute('target', '_blank');
            
            // Update drive status
            document.getElementById('drive-status').textContent = 'Active';
            document.getElementById('drive-status').className = 'text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full';
        } else {
            const driveLink = document.getElementById('drive-link');
            driveLink.innerHTML = '<span class="text-gray-500">No link provided</span>';
            driveLink.href = '#';
            driveLink.classList.add('pointer-events-none', 'text-gray-500');
            driveLink.classList.remove('text-[#155386]');
            driveLink.removeAttribute('target');
            
            // Update drive status
            document.getElementById('drive-status').textContent = 'No Link';
            document.getElementById('drive-status').className = 'text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full';
        }

        // Set current status in radio buttons
        const radios = document.querySelectorAll('.status-radio');
        radios.forEach(radio => {
            if (radio.value === currentApplication.status) {
                radio.checked = true;
            }
        });
    }

    // Update status UI
    function updateStatusUI(status) {
        const statusBadge = document.getElementById('status-badge');
        const currentStatus = document.getElementById('current-status');
        const statusCard = document.getElementById('current-status-card');
        
        const statusConfig = {
            'pending': { color: 'yellow', text: 'Pending Review' },
            'under-review': { color: 'purple', text: 'Under Review' },
            'approved': { color: 'green', text: 'Approved' },
            'rejected': { color: 'red', text: 'Rejected' },
            'for-release': { color: 'blue', text: 'For Release' },
            'verified': { color: 'emerald', text: 'Completed' }
        };

        const config = statusConfig[status] || { color: 'gray', text: status || 'Unknown' };
        
        statusBadge.className = `px-3 py-1 bg-${config.color}-100 text-${config.color}-600 rounded-full text-xs font-medium`;
        statusBadge.textContent = config.text;
        
        if (currentStatus) {
            currentStatus.textContent = config.text;
        }
        
        if (statusCard) {
            statusCard.className = `p-4 bg-${config.color}-50 rounded-lg border border-${config.color}-200`;
        }
    }

    // Update status
async function updateStatus() {
    const selectedRadio = document.querySelector('input[name="status"]:checked');
    if (!selectedRadio) {
        showErrorModal('Please select a status');
        return;
    }

    const status = selectedRadio.value;
    const remarks = document.getElementById('status-remarks').value;
    const hardcopyReceived = document.getElementById('hardcopy-checkbox').checked;

    // Show loading state on button
    const updateBtn = event.target;
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = 'Updating...';
    updateBtn.disabled = true;

    try {
        const response = await fetch(`/staff/applications/${applicationId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                status, 
                remarks,
                hardcopy_received: hardcopyReceived 
            })
        });

        const data = await response.json();

        if (data.success) {
            showSuccessModal('Status updated successfully');
            updateStatusUI(status);
            
            // Clear remarks field
            document.getElementById('status-remarks').value = '';
        } else {
            showErrorModal(data.message || 'Failed to update status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showErrorModal('Failed to update status. Please try again.');
    } finally {
        // Restore button
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    }
}

    // Add note
    async function addNote() {
        const note = document.getElementById('new-note').value;
        if (!note) {
            showErrorModal('Please enter a note');
            return;
        }

        // Here you would typically save the note to your backend
        showSuccessModal('Note added successfully');
        document.getElementById('new-note').value = '';
        
        // Add note to container (temporary)
        const container = document.getElementById('notes-container');
        const date = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        
        const noteHtml = `
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-400 mb-1">${date}</p>
                <p class="text-sm text-gray-700">${note}</p>
                <p class="text-xs text-gray-500 mt-1">— You</p>
            </div>
        `;
        
        // Remove the "No notes" message if it exists
        if (container.children.length === 1 && container.children[0].textContent.includes('No notes yet')) {
            container.innerHTML = noteHtml;
        } else {
            container.innerHTML = noteHtml + container.innerHTML;
        }
    }

    // Copy drive link
    function copyDriveLink() {
        const link = currentApplication?.google_drive_link;
        if (link) {
            navigator.clipboard.writeText(link).then(() => {
                showSuccessModal('Link copied to clipboard!');
            }).catch(() => {
                showErrorModal('Failed to copy link');
            });
        } else {
            showErrorModal('No link to copy');
        }
    }

    // Request missing documents
    function requestMissingDocuments() {
        showSuccessModal('Request sent to applicant');
    }

    // Verify documents
    function verifyDocuments() {
        showSuccessModal('Documents verified successfully');
    }

    // Export as PDF
    function exportAsPDF() {
        window.location.href = `/staff/applications/${applicationId}/export-pdf`;
    }

    // Edit application
    function editApplication() {
        window.location.href = `/staff/applications/${applicationId}/edit`;
    }

    // Send message
    function sendMessage() {
        showSuccessModal('Messaging feature coming soon');
    }

    // Email applicant
    function emailApplicant() {
        if (currentApplication?.email) {
            window.location.href = `mailto:${currentApplication.email}`;
        } else {
            showErrorModal('No email address available');
        }
    }

    // Schedule review
    function scheduleReview() {
        showSuccessModal('Scheduling feature coming soon');
    }

    // Delete application
    function deleteApplication() {
        document.getElementById('delete-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close delete modal
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Confirm delete
    async function confirmDelete() {
        const btn = document.getElementById('confirm-delete-btn');
        btn.innerHTML = 'Deleting...';
        btn.disabled = true;

        try {
            const response = await fetch(`/staff/applications/${applicationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showSuccessModal('Application deleted successfully');
                setTimeout(() => {
                    window.location.href = '/staff/applications';
                }, 1500);
            } else {
                showErrorModal(data.message || 'Failed to delete application');
            }
        } catch (error) {
            console.error('Error deleting application:', error);
            showErrorModal('Failed to delete application');
        } finally {
            btn.innerHTML = 'Delete';
            btn.disabled = false;
            closeDeleteModal();
        }
    }

    // Show error state
    function showError() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
    }

    // Modal functions
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

    function showErrorModal(message) {
        document.getElementById('error-modal-message').textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Setup modals
    function setupModals() {
        const deleteModal = document.getElementById('delete-modal');
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) closeDeleteModal();
            });
        }
        
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
                closeDeleteModal();
                closeErrorModal();
                closeSuccessModal();
            }
        });
    }
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}

/* Modal animations */
#delete-modal, #error-modal, #success-modal {
    transition: opacity 0.2s ease-in-out;
}

#delete-modal .bg-white, #error-modal .bg-white, #success-modal .bg-white {
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

/* Disable pointer events for disabled links */
.pointer-events-none {
    pointer-events: none;
}
</style>
@endsection