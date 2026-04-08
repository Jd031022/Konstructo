@extends('layouts.app')

@section('title', 'Application - Step 4: Review & Submit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="/applicant/application/step3?id={{ $application->id }}" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Step 3: Upload Documents
        </a>
    </div>

    <!-- Step Indicator -->
    <div class="mb-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full font-bold text-sm">1</div>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Step 4: Review & Submit</h2>
                <p class="text-l text-gray-600">Review your application details and submit for processing</p>
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
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-medium text-gray-400">Download Forms</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-medium text-gray-400">Upload Docs</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                <span class="text-sm font-semibold text-[#155386]">Review & Submit</span>
            </div>
        </div>
    </div>

    <!-- Application Number Banner - Always visible in Step 4 -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-lg overflow-hidden animate-slide-down">
            <div class="px-6 py-4 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Your Application Number</p>
                        <p id="application-number-display" class="text-2xl font-bold text-white font-mono">{{ $application->application_number ?? 'Pending' }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="copyApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Copy Number
                    </button>
                    <button onclick="downloadApplicationNumber()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Save Number
                    </button>
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Important:</span> Keep this number for reference when submitting hard copies and for all future correspondence regarding this application.
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Summary Cards -->
        <div class="p-8 space-y-8">
            <!-- Project Information Summary -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Project Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Project Title</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->project_title ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Project Type</p>
                            <p class="text-sm font-medium text-gray-800 capitalize">{{ $application->project_type ?? 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Project Location</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->project_location ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Lot Area</p>
                            <p class="text-sm font-medium text-gray-800">{{ number_format($application->lot_area ?? 0, 2) }} sqm</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Floor Area</p>
                            <p class="text-sm font-medium text-gray-800">{{ number_format($application->floor_area ?? 0, 2) }} sqm</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Number of Floors</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->num_floors ?? '1' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Estimated Cost</p>
                            <p class="text-sm font-medium text-gray-800">₱ {{ number_format($application->estimated_cost ?? 0, 2) }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Project Description</p>
                            <p class="text-sm text-gray-600">{{ $application->project_description ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Information Summary -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Owner/Applicant Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Full Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->owner_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Contact Number</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->contact_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->owner_address ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email Address</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->owner_email ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information Summary -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        Professional Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Architect's Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->architect_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Architect's License Number</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->architect_license ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Civil Engineer's Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->engineer_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Engineer's License Number</p>
                            <p class="text-sm font-medium text-gray-800">{{ $application->engineer_license ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Checklist Summary -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Uploaded Documents
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="document-checklist-summary">
                        <div class="text-center py-4 text-gray-500 col-span-2">
                            <svg class="animate-spin h-6 w-6 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm mt-2">Loading documents...</p>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Hard Copy Submission Section -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Hard Copy Submission
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">Hard Copy Submission Requirements</p>
                                <p class="text-sm text-blue-700 mt-1">After submitting this online application, you must submit the following original hard copies to the Office of the Building Official (OBO):</p>
                                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                    <li>All accomplished forms (printed from Step 2)</li>
                                    <li>All supporting documents with original signatures</li>
                                    <li>Complete sets of plans and specifications</li>
                                    <li>Valid government-issued ID of the applicant/representative</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <label class="flex items-start gap-3 mb-4">
                        <input type="checkbox" id="hardcopy-checkbox" class="mt-1 h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                        <span class="text-sm text-gray-700">I/we understand and agree that I/we must submit the original hard copies of ALL required documents to the Office of the Building Official (OBO) within <span class="font-semibold">five (5) working days</span> after submitting this online application. Failure to do so may result in the rejection or cancellation of this application.</span>
                    </label>
                </div>
            </div>

            <!-- Declaration & Legal Consequences Section -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Declaration & Legal Consequences
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 rounded-lg p-4 mb-6 border border-yellow-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">Declaration of Truth and Accuracy</p>
                                <p class="text-sm text-yellow-700 mt-1">I/we certify that all the information provided in this application, including all attached documents and plans, are true, correct, and complete to the best of my/our knowledge and belief.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Consequences Checklist -->
                    <div class="bg-red-50 rounded-lg p-4 mb-6 border border-red-200">
                        <div class="flex items-start gap-3 mb-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-semibold text-red-800">Legal Consequences of False Information</p>
                        </div>
                        <div class="space-y-3 ml-8">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" id="legal-checkbox-1" class="legal-checkbox mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700">I/we understand that providing false or misleading information in this application constitutes <span class="font-semibold">perjury</span> under Philippine law (Revised Penal Code, Article 183).</span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" id="legal-checkbox-2" class="legal-checkbox mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700">I/we understand that any false statement may result in the <span class="font-semibold">immediate rejection and cancellation</span> of this application.</span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" id="legal-checkbox-3" class="legal-checkbox mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700">I/we understand that I/we may be subject to <span class="font-semibold">fines, penalties, and/or imprisonment</span> as prescribed by the National Building Code (PD 1096) and other applicable laws.</span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" id="legal-checkbox-4" class="legal-checkbox mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700">I/we understand that any violation may lead to <span class="font-semibold">administrative sanctions</span> including the suspension or revocation of permits.</span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" id="legal-checkbox-5" class="legal-checkbox mt-1 h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="text-sm text-gray-700">I/we understand that the building official has the right to conduct <span class="font-semibold">site inspections and verification</span> at any time.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Final Declaration -->
                    <label class="flex items-start gap-3 mb-6">
                        <input type="checkbox" id="declaration-checkbox" class="mt-1 h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                        <span class="text-sm text-gray-700">I/we hereby declare that I/we have read, understood, and agree to comply with all the terms, conditions, and legal requirements stated above. I/we confirm that all information provided is true and correct to the best of my/our knowledge.</span>
                    </label>

                    <div class="flex justify-end">
                        <button onclick="submitApplication()" id="submit-btn"
                                class="inline-flex items-center px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Application
                        </button>
                    </div>
                </div>
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

<script>
    const applicationId = {{ $application->id }};
    const applicationNumber = '{{ $application->application_number ?? '' }}';
    let documentLinks = {};

    function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'; }

    // Check if application number exists, show warning if missing
    document.addEventListener('DOMContentLoaded', async function() {
        if (!applicationNumber || applicationNumber === '') {
            showErrorModal('Warning: No application number has been generated yet. Please ensure you have completed Step 3 properly.');
        }
        await loadDocumentSummary();
    });

    async function loadDocumentSummary() {
        try {
            const response = await fetch(`/applicant/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data && data.data.document_links) {
                documentLinks = data.data.document_links;
                displayDocumentSummary(documentLinks);
            } else {
                displayEmptyDocuments();
            }
        } catch (error) {
            console.error('Error loading documents:', error);
            displayEmptyDocuments();
        }
    }

    function displayDocumentSummary(links) {
        const container = document.getElementById('document-checklist-summary');
        
        const documentNames = {
            'app_letter_link': 'Application Letter',
            'bp_forms_link': 'Building Permit Forms',
            'arch_plans_link': 'Architectural Plans',
            'structural_plans_link': 'Civil/Structural Plans',
            'electrical_plans_link': 'Electrical Plans',
            'plumbing_plans_link': 'Sanitary/Plumbing Plans',
            'mechanical_plans_link': 'Mechanical Plans',
            'fencing_plans_link': 'Fencing Plans',
            'ownership_link': 'Proof of Ownership',
            'bom_link': 'Bill of Materials',
            'structural_analysis_link': 'Structural Design Analysis',
            'barangay_clearance_link': 'Barangay Clearance',
            'valid_id_link': 'Valid ID'
        };
        
        let html = '';
        let hasDocuments = false;
        
        for (const [key, name] of Object.entries(documentNames)) {
            if (links[key] && links[key].trim() !== '') {
                hasDocuments = true;
                html += `
                    <div class="flex items-center gap-2 p-2 bg-green-50 rounded-lg border border-green-200">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-700">${escapeHtml(name)}</span>
                    </div>
                `;
            }
        }
        
        if (!hasDocuments) {
            html = `
                <div class="text-center py-8 text-gray-500 col-span-2">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm">No documents uploaded yet</p>
                    <p class="text-xs text-gray-400 mt-1">Please go back to Step 3 to upload your documents</p>
                </div>
            `;
        }
        
        container.innerHTML = html;
    }

    function displayEmptyDocuments() {
        const container = document.getElementById('document-checklist-summary');
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500 col-span-2">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">No documents uploaded yet</p>
                <p class="text-xs text-gray-400 mt-1">Please go back to Step 3 to upload your documents</p>
            </div>
        `;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function copyApplicationNumber() {
        if (applicationNumber && applicationNumber !== '') {
            navigator.clipboard.writeText(applicationNumber).then(() => {
                showSuccessModal('Application number copied to clipboard!');
            }).catch(() => {
                showErrorModal('Failed to copy application number.');
            });
        } else {
            showErrorModal('No application number available yet. Please complete Step 3 first.');
        }
    }

    function downloadApplicationNumber() {
        if (applicationNumber && applicationNumber !== '') {
            const content = `BUILDING PERMIT APPLICATION NUMBER
================================

Application Number: ${applicationNumber}
Date Generated: ${new Date().toLocaleDateString()}
Time Generated: ${new Date().toLocaleTimeString()}

Please keep this number for reference when:
- Submitting hard copies to OBO
- Checking application status
- All future correspondence

Application ID: ${applicationId}
Generated from: Konstrupto Building Permit System

--- End of Document ---`;

            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `application_number_${applicationNumber}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showSuccessModal('Application number saved as text file!');
        } else {
            showErrorModal('No application number available yet. Please complete Step 3 first.');
        }
    }

    async function submitApplication() {
        // Validate all required checkboxes
        const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
        const declarationCheckbox = document.getElementById('declaration-checkbox');
        const legalCheckboxes = document.querySelectorAll('.legal-checkbox');
        
        if (!hardcopyCheckbox.checked) {
            showErrorModal('Please acknowledge the hard copy submission requirement.');
            return;
        }
        
        if (!declarationCheckbox.checked) {
            showErrorModal('Please read and accept the declaration to proceed.');
            return;
        }
        
        // Check if all legal checkboxes are checked
        let allLegalChecked = true;
        legalCheckboxes.forEach((checkbox, index) => {
            if (!checkbox.checked) {
                allLegalChecked = false;
            }
        });
        
        if (!allLegalChecked) {
            showErrorModal('Please acknowledge all legal consequences by checking all boxes in the Legal Consequences section.');
            return;
        }
        
        // Check if application number exists
        if (!applicationNumber || applicationNumber === '') {
            showErrorModal('No application number has been generated. Please go back to Step 3 and save your documents first.');
            return;
        }
        
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Submitting...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('/applicant/application/submit', {
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
                showSuccessModal(`Application ${applicationNumber} submitted successfully! Redirecting to your applications...`);
                setTimeout(() => {
                    window.location.href = '/applicant/applications';
                }, 2000);
            } else {
                showErrorModal(data.message || 'Failed to submit application');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('An error occurred. Please try again.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

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
</script>

<style>
    .animate-spin{animation:spin 1s linear infinite;}
    @keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
    button:disabled{cursor:not-allowed;opacity:.65;}
    
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
    .animate-slide-down {
        animation: slideDown 0.5s ease-out;
    }
</style>
@endsection