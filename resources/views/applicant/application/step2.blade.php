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
                <p class="text-l text-gray-600">Upload each document to Google Drive and provide individual shareable links below. All original hard copies must be submitted to our office.</p>
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
                <p class="text-sm text-gray-700 mt-1">The Google Drive links are for pre-verification purposes. <span class="font-semibold">You must submit the original hard copies</span> of ALL documents to the Office of the Building Official (OBO) for final processing.</p>
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

        <!-- Document Links Section -->
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Document Links</h2>
            
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
                            <li>Upload each document individually to Google Drive</li>
                            <li>For each document, set sharing permission to <span class="font-semibold">"Anyone with the link can view"</span></li>
                            <li>Copy each shareable link and paste it in the corresponding field below</li>
                            <li>Make sure each link is accessible and working</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Application Number Display -->
            <div id="application-number-display" class="mb-6 hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-lg p-4 text-white">
                    <p class="text-sm opacity-90">Your Application Number</p>
                    <p class="text-2xl font-bold font-mono" id="display-application-number"></p>
                </div>
            </div>

            <!-- Documents Grid -->
            <form id="documents-form" class="space-y-6">
                @csrf
                
                <!-- Required Documents -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Required Documents</h3>
                    
                    <!-- Application Letter -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Application Letter <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Request letter addressed to the Building Official</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="app_letter_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('app_letter_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Building Permit Forms -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Building Permit Forms <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Completed BP Form (BP 102, BP 103, etc.)</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="bp_forms_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('bp_forms_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Architectural Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Architectural Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Signed and sealed by licensed architect</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="arch_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('arch_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Civil/Structural Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Civil/Structural Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Signed and sealed by licensed civil engineer</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="structural_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('structural_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Electrical Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Electrical Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Signed and sealed by licensed electrical engineer</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="electrical_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('electrical_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sanitary/Plumbing Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sanitary/Plumbing Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Signed and sealed by licensed sanitary/plumbing engineer</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="plumbing_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('plumbing_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mechanical Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Mechanical Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Signed and sealed by licensed mechanical engineer</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="mechanical_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('mechanical_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fencing Plans -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fencing Plans (5 sets) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">If applicable, signed and sealed</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="fencing_plans_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('fencing_plans_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Proof of Ownership -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Proof of Ownership (2 copies) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">TCT, Tax Declaration, or Contract of Lease</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="ownership_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('ownership_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bill of Materials -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Bill of Materials (5 copies) <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Detailed list of materials with quantities</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="bom_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('bom_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Structural Design Analysis -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Structural Design Analysis <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Computation and analysis signed by civil engineer</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="structural_analysis_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('structural_analysis_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barangay Clearance -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Barangay Clearance <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Clearance from the barangay where project is located</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="barangay_clearance_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('barangay_clearance_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Valid ID -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Valid ID <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500">Government-issued ID of applicant/representative</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="valid_id_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('valid_id_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Optional Documents -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Optional Documents</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-4">For contractors with PCAB license</p>
                    
                    <!-- CSHP from DOLE -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    CSHP from DOLE (Optional)
                                </label>
                                <p class="text-xs text-gray-500">Construction Safety and Health Program</p>
                            </div>
                            <div class="flex-1">
                                <input type="url" 
                                       id="cshp_link" 
                                       placeholder="https://drive.google.com/file/d/..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm">
                            </div>
                            <div>
                                <button type="button" onclick="testLink('cshp_link')" class="text-xs text-[#155386] hover:text-[#40798C]">Test Link</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Progress Indicator -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Documents Upload Progress</span>
                    <span class="text-sm font-bold text-[#155386]" id="upload-progress">0/13</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-[#155386] h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2" id="progress-message">Please provide links for all required documents</p>
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
            
            <button onclick="saveAllDocumentLinks()" 
                    id="proceed-btn"
                    class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Save & Proceed to Review
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

    </div>

</div>

<!-- Modals -->
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
    // Define all document fields
    const documentFields = [
        'app_letter_link',
        'bp_forms_link',
        'arch_plans_link',
        'structural_plans_link',
        'electrical_plans_link',
        'plumbing_plans_link',
        'mechanical_plans_link',
        'fencing_plans_link',
        'ownership_link',
        'bom_link',
        'structural_analysis_link',
        'barangay_clearance_link',
        'valid_id_link'
    ];
    
    const optionalFields = ['cshp_link'];
    
    // Update progress bar
    function updateProgress() {
        let filledCount = 0;
        documentFields.forEach(field => {
            const value = document.getElementById(field)?.value.trim();
            if (value && value !== '') {
                filledCount++;
            }
        });
        
        const totalRequired = documentFields.length;
        const percentage = (filledCount / totalRequired) * 100;
        
        document.getElementById('upload-progress').textContent = `${filledCount}/${totalRequired}`;
        document.getElementById('progress-bar').style.width = `${percentage}%`;
        
        if (filledCount === totalRequired) {
            document.getElementById('progress-message').innerHTML = '<span class="text-green-600">✓ All required documents have links! You can now proceed.</span>';
        } else {
            document.getElementById('progress-message').innerHTML = `<span class="text-yellow-600">⚠️ Please provide links for ${totalRequired - filledCount} more document(s)</span>`;
        }
    }
    
    // Test individual link
    function testLink(fieldId) {
        const link = document.getElementById(fieldId).value.trim();
        
        if (!link) {
            showErrorModal('Please enter a Google Drive link first.');
            return;
        }
        
        const isGoogleDriveLink = link.includes('drive.google.com') || link.includes('docs.google.com');
        
        if (!isGoogleDriveLink) {
            showErrorModal('Please enter a valid Google Drive link.');
            return;
        }
        
        showSuccessModal('Link format is valid! Make sure sharing is set to "Anyone with the link".');
    }
    
    // Save all document links
    async function saveAllDocumentLinks() {
        // Validate all required fields
        const missingFields = [];
        documentFields.forEach(field => {
            const value = document.getElementById(field)?.value.trim();
            if (!value) {
                const label = document.querySelector(`label[for="${field}"]`)?.innerText || field;
                missingFields.push(label);
            }
        });
        
        if (missingFields.length > 0) {
            showErrorModal(`Please provide links for the following required documents:\n${missingFields.join('\n')}`);
            return;
        }
        
        // Collect all links
        const documentLinks = {};
        documentFields.forEach(field => {
            documentLinks[field] = document.getElementById(field).value.trim();
        });
        optionalFields.forEach(field => {
            const value = document.getElementById(field)?.value.trim();
            if (value) {
                documentLinks[field] = value;
            }
        });
        
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
            const urlParams = new URLSearchParams(window.location.search);
            const applicationId = urlParams.get('id');
            
            const requestData = {
                document_links: documentLinks,
                application_id: applicationId || null
            };
            
            const response = await fetch('{{ route("applicant.application.store-links") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData)
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                if (data.data && data.data.application_number) {
                    localStorage.setItem('konstructo_app_number', data.data.application_number);
                }
                
                showSuccessModal('All documents saved successfully! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = '/applicant/application/step3';
                }, 2000);
            } else {
                showErrorModal(data.message || 'Failed to save documents. Please try again.');
                proceedBtn.innerHTML = originalText;
                proceedBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('An error occurred. Please try again.');
            proceedBtn.innerHTML = originalText;
            proceedBtn.disabled = false;
        }
    }
    
    // Load existing data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners for all input fields to update progress
        documentFields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.addEventListener('input', updateProgress);
            }
        });
        
        updateProgress();
        
        // Load existing application data
        const urlParams = new URLSearchParams(window.location.search);
        const applicationId = urlParams.get('id');
        
        if (applicationId) {
            loadExistingData(applicationId);
        }
    });
    
    async function loadExistingData(applicationId) {
        try {
            const response = await fetch(`/applicant/application-details/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data && data.data.document_links) {
                const links = data.data.document_links;
                
                // Populate all fields
                documentFields.forEach(field => {
                    if (links[field]) {
                        document.getElementById(field).value = links[field];
                    }
                });
                optionalFields.forEach(field => {
                    if (links[field]) {
                        document.getElementById(field).value = links[field];
                    }
                });
                
                updateProgress();
                
                // Show application number
                if (data.data.application_number) {
                    const appNumberDisplay = document.getElementById('application-number-display');
                    document.getElementById('display-application-number').textContent = data.data.application_number;
                    appNumberDisplay.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading existing data:', error);
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
</style>
@endsection