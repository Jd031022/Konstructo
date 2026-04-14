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

    <!-- Info Banner - Application number will be generated upon submission -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl shadow-lg overflow-hidden animate-slide-down">
            <div class="px-6 py-4 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm">Application Status</p>
                        <p class="text-xl font-bold text-white">Ready for Submission</p>
                    </div>
                </div>
                <div class="bg-white/20 px-4 py-2 rounded-lg text-white text-sm font-medium">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Your application number will be generated upon submission
                </div>
            </div>
            <div class="bg-white/10 px-6 py-2 text-sm text-white/90">
                <span class="font-medium">Note:</span> After clicking "Submit Application", you will receive an email with your unique application number. Please keep this number for all future correspondence.
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
        <div class="flex items-center justify-between">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                Professional Information
            </h3>
            <span class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-full">Licensed Professionals</span>
        </div>
    </div>
    <div class="p-6">
        <!-- Architect -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-700">Architect</h4>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                <div>
                    <p class="text-xs text-gray-500">Name</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->architect_name ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">License Number</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->architect_license ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Civil Engineer -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-700">Civil Engineer</h4>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                <div>
                    <p class="text-xs text-gray-500">Name</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->engineer_name ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">License Number</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->engineer_license ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Professional Electrical Engineer -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-700">Professional Electrical Engineer</h4>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                <div>
                    <p class="text-xs text-gray-500">Name</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->electrical_engineer_name ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">License Number</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->electrical_engineer_license ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Sanitary Engineer / Master Plumber -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-700">Sanitary Engineer / Master Plumber</h4>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-8">
                <div>
                    <p class="text-xs text-gray-500">Name</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->sanitary_engineer_name ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">License Number</p>
                    <p class="text-sm font-medium text-gray-800">{{ $application->sanitary_engineer_license ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Document Checklist Summary (Required + Optional) -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Uploaded Documents
                        </h3>
                        <div class="flex gap-2">
                            <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded-full" id="required-count-badge">0/0 Required</span>
                            <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full" id="optional-count-badge">0 Optional</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Required Documents Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                            Required Documents
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="required-documents-list">
                            <div class="text-center py-4 text-gray-500 col-span-2">
                                <svg class="animate-spin h-6 w-6 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm mt-2">Loading documents...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Documents Section -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            Optional Documents (If Uploaded)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="optional-documents-list">
                            <div class="text-center py-4 text-gray-500 col-span-2">
                                <p class="text-sm">No optional documents uploaded</p>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-4 italic">Note: Optional documents are not required for submission but may be requested by the Building Official if applicable to your project.</p>
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
            Declaration & Legal Acknowledgment
        </h3>
    </div>
    <div class="p-6">
        <!-- Combined Declaration with all acknowledgments -->
        <label class="flex items-start gap-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition cursor-pointer">
            <input type="checkbox" id="declaration-checkbox" class="mt-1 h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
            <div class="text-sm text-gray-700">
                <p class="font-semibold mb-2">I/we hereby acknowledge and agree to the following:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-2">
                    <li>The information provided in this application, including all attached documents and plans, is true, correct, and complete.</li>
                    <li>Providing false or misleading information constitutes <span class="font-semibold text-red-600">perjury</span> under Philippine law (Revised Penal Code, Article 183).</li>
                    <li>Any false statement may result in <span class="font-semibold text-red-600">immediate rejection and cancellation</span> of this application.</li>
                    <li>I/we may be subject to <span class="font-semibold text-red-600">fines, penalties, and/or imprisonment</span> as prescribed by the National Building Code (PD 1096) and other applicable laws.</li>
                    <li>Any violation may lead to <span class="font-semibold text-red-600">administrative sanctions</span> including suspension or revocation of permits.</li>
                    <li>The Building Official has the right to conduct <span class="font-semibold text-red-600">site inspections and verification</span> at any time.</li>
                </ul>
                <p class="font-semibold mt-3 text-[#155386]">I/we agree to comply with all terms, conditions, and legal requirements stated above.</p>
            </div>
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
    let documentLinks = {};

    function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'; }

    // Load document summary on page load
    document.addEventListener('DOMContentLoaded', async function() {
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
        // Required Documents (must be uploaded)
        const requiredDocuments = {
            'app_letter_link': 'Application Letter',
            'bp_forms_link': 'Building Permit Forms',
            'zoning_compliance_link': 'Zoning Compliance / Locational Clearance',
            'arch_plans_link': 'Architectural Plans',
            'structural_plans_link': 'Civil/Structural Plans',
            'geodetic_plan_link': 'Site Development Plan / Geodetic Plan',
            'electrical_plans_link': 'Electrical Plans',
            'plumbing_plans_link': 'Sanitary/Plumbing Plans',
            'bom_link': 'Bill of Materials',
            'barangay_clearance_link': 'Barangay Clearance',
            'valid_id_link': 'Valid ID',
            'ptr_license_link': 'PTR License No. (Current Year)'
        };
        
        // Optional Documents (only show if uploaded)
        const optionalDocuments = {
            'structural_analysis_link': 'Structural Design Analysis',
            'mechanical_plans_link': 'Mechanical Plans and Specifications',
            'fencing_plans_link': 'Fencing Plans and Specifications',
            'sign_permit_link': 'Sign Permit Application',
            'electronics_permit_link': 'Electronics Permit',
            'demolition_permit_link': 'Demolition Permit',
            'cshp_link': 'CSHP from DOLE'
        };
        
        // Display Required Documents
        const requiredContainer = document.getElementById('required-documents-list');
        let requiredHtml = '';
        let requiredCount = 0;
        
        for (const [key, name] of Object.entries(requiredDocuments)) {
            if (links[key] && links[key].trim() !== '') {
                requiredCount++;
                requiredHtml += `
                    <div class="flex items-center gap-2 p-2 bg-green-50 rounded-lg border border-green-200">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-700">${escapeHtml(name)}</span>
                    </div>
                `;
            }
        }
        
        if (requiredHtml === '') {
            requiredHtml = `
                <div class="text-center py-4 text-gray-500 col-span-2">
                    <p class="text-sm">No required documents uploaded yet</p>
                </div>
            `;
        }
        requiredContainer.innerHTML = requiredHtml;
        document.getElementById('required-count-badge').textContent = `${requiredCount}/${Object.keys(requiredDocuments).length} Required`;
        
        // Display Optional Documents (only if uploaded)
        const optionalContainer = document.getElementById('optional-documents-list');
        let optionalHtml = '';
        let optionalCount = 0;
        
        for (const [key, name] of Object.entries(optionalDocuments)) {
            if (links[key] && links[key].trim() !== '') {
                optionalCount++;
                optionalHtml += `
                    <div class="flex items-center gap-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-700">${escapeHtml(name)}</span>
                    </div>
                `;
            }
        }
        
        if (optionalHtml === '') {
            optionalHtml = `
                <div class="text-center py-4 text-gray-500 col-span-2">
                    <p class="text-sm">No optional documents uploaded</p>
                    <p class="text-xs text-gray-400 mt-1">Optional documents are not required for submission</p>
                </div>
            `;
        }
        optionalContainer.innerHTML = optionalHtml;
        document.getElementById('optional-count-badge').textContent = `${optionalCount} Optional`;
        
        // Check if all required documents are uploaded
        const totalRequired = Object.keys(requiredDocuments).length;
        if (requiredCount === totalRequired) {
            // All good - proceed is enabled
            console.log('All required documents uploaded');
        }
    }

    function displayEmptyDocuments() {
        const requiredContainer = document.getElementById('required-documents-list');
        requiredContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500 col-span-2">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">No documents uploaded yet</p>
                <p class="text-xs text-gray-400 mt-1">Please go back to Step 3 to upload your documents</p>
            </div>
        `;
        document.getElementById('optional-documents-list').innerHTML = `
            <div class="text-center py-4 text-gray-500 col-span-2">
                <p class="text-sm">No optional documents uploaded</p>
            </div>
        `;
        document.getElementById('required-count-badge').textContent = `0/0 Required`;
        document.getElementById('optional-count-badge').textContent = `0 Optional`;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

async function submitApplication() {
    // Validate all required checkboxes
    const hardcopyCheckbox = document.getElementById('hardcopy-checkbox');
    const declarationCheckbox = document.getElementById('declaration-checkbox');
    
    if (!hardcopyCheckbox.checked) {
        showErrorModal('Please acknowledge the hard copy submission requirement.');
        return;
    }
    
    if (!declarationCheckbox.checked) {
        showErrorModal('Please read and accept the Declaration & Legal Acknowledgment to proceed.');
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
            const newApplicationNumber = data.data?.application_number || 'generated';
            showSuccessModal(`Application ${newApplicationNumber} submitted successfully! You will receive an email with your application number. Redirecting to your applications...`);
            setTimeout(() => {
                window.location.href = '/applicant/applications';
            }, 3000);
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