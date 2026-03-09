@extends('layouts.app')

@section('title', 'Upload Documents - Step 2 - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-8">
        <a href="/user/application/step1" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
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
                <h2 class="text-2xl font-semibold text-gray-800">Step 2: Upload Documents</h2>
                <p class="text-l text-gray-600">Upload the required documents. Some documents must be submitted as hard copy to our office.</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-medium text-gray-600">Download Form</span>
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

        <!-- Upload Documents Section -->
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Upload Required Documents</h2>
            
            <!-- Legend -->
            <div class="flex items-center gap-6 mb-6 p-4 bg-gray-50 rounded-lg text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-red-500 rounded-full"></span>
                    <span class="text-gray-600">Required (Upload)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-blue-500 rounded-full"></span>
                    <span class="text-gray-600">Required (Hard Copy to OBO)</span>
                </div>
            </div>

            <!-- Application Letter (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Application Letter <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From the owner</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('appletter-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PDF only (Max: 10MB)</p>
                            <input type="file" id="appletter-upload" class="hidden" accept=".pdf">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Application letter dated May 5, 2025"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-appletter-files"></div>
            </div>

            <!-- Building Permit Forms (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Building Permit Forms <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">Application for Building Permit, Sign Permit, Application for Architectural Permit, Mechanical Permit, Application for Electrical Permit, Electronics Permit, Application for Sanitary/Plumbing Permit, Demolition Permit Form, Application for Civil/Structural, Fencing Permit</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('permitforms-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files</p>
                            <p class="text-xs text-gray-400 mt-1">PDF only (Max: 10MB each)</p>
                            <input type="file" id="permitforms-upload" class="hidden" accept=".pdf" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Complete set of permit forms"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-permitforms-files"></div>
            </div>

            <!-- Architectural Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Architectural Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Geodetic Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('architectural-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="architectural-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Floor plans, elevations, sections by Engr. Santos"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-architectural-files"></div>
            </div>

            <!-- Civil/Structural Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Civil/Structural Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Civil/Structural Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('structural-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="structural-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Foundation plans, framing details by Engr. Cruz"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-structural-files"></div>
            </div>

            <!-- Electrical Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Electrical Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Professional Electrical Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('electrical-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="electrical-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Electrical layout, riser diagram by Engr. Reyes"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-electrical-files"></div>
            </div>

            <!-- Sanitary/Plumbing Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Sanitary/Plumbing Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Sanitary Engineer/Master Plumber</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('plumbing-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="plumbing-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Water supply, drainage layout by Master Plumber Santos"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-plumbing-files"></div>
            </div>

            <!-- Mechanical Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Mechanical Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Professional Mechanical Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('mechanical-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="mechanical-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., HVAC plans, mechanical details by Engr. Gomez"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-mechanical-files"></div>
            </div>

            <!-- Fencing Plans (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Fencing Plans and Specifications (5 sets) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Architect or Civil Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('fencing-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 sets)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="fencing-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Fencing layout, specifications"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-fencing-files"></div>
            </div>

            <!-- Proof of Ownership (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Proof of Ownership (2 copies) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Land Registration Authority/Assessor's Office/Treasurer's Office</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('proof-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (2 copies)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="proof-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., TCT No. T-123456, Tax Declaration No. TD-789"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-proof-files"></div>
            </div>

            <!-- Bill of Materials (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Bill of Materials (5 copies) <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Architect or Civil Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('bom-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload multiple files (5 copies)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB each)</p>
                            <input type="file" id="bom-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Detailed bill of materials by Arch. Santos"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-bom-files"></div>
            </div>

            <!-- Structural Design Analysis (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Structural Design Analysis <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From Duly Licensed and Registered Structural Engineer</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('analysis-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PDF only (Max: 10MB)</p>
                            <input type="file" id="analysis-upload" class="hidden" accept=".pdf">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Structural analysis by Engr. Cruz"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-analysis-files"></div>
            </div>

            <!-- Locational/Zoning Clearance (Hard Copy to OBO) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Locational/Zoning Clearance <span class="text-blue-500 text-sm ml-1">*Submit Hard Copy to OBO</span></h3>
                        <p class="text-sm text-gray-500">From City Planning and Development Office - Please submit the original hard copy to our office</p>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">This document must be submitted as hard copy to the Office of the Building Official (OBO)</p>
                            <p class="text-xs text-gray-600 mt-1">Please bring the original Locational/Zoning Clearance when you visit our office.</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <input type="checkbox" id="zoning-confirm" class="h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                        <label for="zoning-confirm" class="text-sm text-gray-600">I confirm that I will submit the hard copy of Locational/Zoning Clearance to the OBO</label>
                    </div>
                </div>
            </div>

            <!-- Barangay Clearance (Required - Upload) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-red-600 text-xs font-bold">!</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Barangay Clearance <span class="text-red-500 text-sm ml-1">*Required</span></h3>
                        <p class="text-sm text-gray-500">From the particular Barangay where the project is located</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('barangay-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (Max: 10MB)</p>
                            <input type="file" id="barangay-upload" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., Barangay Clearance from Brgy. San Jose"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-barangay-files"></div>
            </div>

            <!-- CSHP from DOLE (For Contractors w/ PCAB) -->
            <div class="mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-start gap-2 mb-4">
                    <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-gray-500 text-xs font-bold">i</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Certificate of Construction Safety and Health Program (CSHP) from DOLE</h3>
                        <p class="text-sm text-gray-500">For Contractors with PCAB - From DOLE</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#155386] transition cursor-pointer" onclick="document.getElementById('cshp-upload').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload or drag and drop (if applicable)</p>
                            <p class="text-xs text-gray-400 mt-1">PDF only (Max: 10MB)</p>
                            <input type="file" id="cshp-upload" class="hidden" accept=".pdf">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment Description <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., CSHP certificate from DOLE"></textarea>
                    </div>
                </div>
                <div class="mt-4 space-y-2" id="uploaded-cshp-files"></div>
            </div>

            <!-- Upload Progress Summary -->
            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-700">Upload Progress:</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700" id="upload-count">0/14 required documents uploaded</span>
                        <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full" id="upload-percent">0% complete</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-[#155386] h-2 rounded-full" id="upload-progress-bar" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">*Locational/Zoning Clearance must be submitted as hard copy to OBO</p>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="p-6 pt-0 flex justify-between items-center">
            <a href="/user/application/step1" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Previous: Download Form
            </a>
            
            <button onclick="validateAndProceed()" 
                    id="proceed-btn"
                    class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                Proceed to Review & Submit
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
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
                <h4 class="font-semibold text-gray-800 mb-1">Need Help with Documents?</h4>
                <p class="text-sm text-gray-600">
                    📞 (052) 123-4567 | ✉️ documents@konstructo.gov.ph
                </p>
                <p class="text-xs text-gray-500 mt-1">Our staff can assist you with document requirements. Remember: Locational/Zoning Clearance must be submitted as hard copy.</p>
            </div>
        </div>
    </div>

</div>

<!-- JavaScript for File Uploads and Progress Tracking -->
<script>
    // Track uploaded files count
    let uploadedCount = 0;
    const totalRequired = 14; // Total number of required documents (excluding CSHP which is optional)

    // Helper function to handle file uploads for each section
    function setupFileUpload(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        
        if (input) {
            input.addEventListener('change', function(e) {
                // Remove previous files count for this section
                const previousFiles = container.children.length;
                
                Array.from(e.target.files).forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                    fileItem.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">${file.name}</span>
                            <span class="text-xs text-gray-400">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                        </div>
                        <button onclick="this.parentElement.remove(); updateUploadCount()" class="text-red-500 hover:text-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    container.appendChild(fileItem);
                });
                
                updateUploadCount();
            });
        }
    }

    // Update upload progress
    function updateUploadCount() {
        // Count sections that have at least one file
        const sections = [
            'uploaded-appletter-files',
            'uploaded-permitforms-files',
            'uploaded-architectural-files',
            'uploaded-structural-files',
            'uploaded-electrical-files',
            'uploaded-plumbing-files',
            'uploaded-mechanical-files',
            'uploaded-fencing-files',
            'uploaded-proof-files',
            'uploaded-bom-files',
            'uploaded-analysis-files',
            'uploaded-barangay-files'
        ];
        
        let completed = 0;
        sections.forEach(sectionId => {
            const container = document.getElementById(sectionId);
            if (container && container.children.length > 0) {
                completed++;
            }
        });
        
        // Check if zoning confirmation is checked
        const zoningChecked = document.getElementById('zoning-confirm')?.checked || false;
        if (zoningChecked) {
            completed++; // Count zoning as completed if confirmed
        }
        
        uploadedCount = completed;
        const percent = Math.round((uploadedCount / totalRequired) * 100);
        
        document.getElementById('upload-count').textContent = `${uploadedCount}/${totalRequired} required documents uploaded`;
        document.getElementById('upload-percent').textContent = `${percent}% complete`;
        document.getElementById('upload-progress-bar').style.width = `${percent}%`;
    }

    // Validate before proceeding
    function validateAndProceed() {
        const zoningChecked = document.getElementById('zoning-confirm')?.checked || false;
        
        // Count uploaded required sections
        const sections = [
            'uploaded-appletter-files',
            'uploaded-permitforms-files',
            'uploaded-architectural-files',
            'uploaded-structural-files',
            'uploaded-electrical-files',
            'uploaded-plumbing-files',
            'uploaded-mechanical-files',
            'uploaded-fencing-files',
            'uploaded-proof-files',
            'uploaded-bom-files',
            'uploaded-analysis-files',
            'uploaded-barangay-files'
        ];
        
        let completed = 0;
        sections.forEach(sectionId => {
            const container = document.getElementById(sectionId);
            if (container && container.children.length > 0) {
                completed++;
            }
        });
        
        if (completed < sections.length) {
            alert(`Please upload all required documents. You still need to upload ${sections.length - completed} more document(s).`);
            return;
        }
        
        if (!zoningChecked) {
            alert('Please confirm that you will submit the hard copy of Locational/Zoning Clearance to the OBO.');
            return;
        }
        
        // All good, proceed to next step
        window.location.href = '/user/application/step3';
    }

    // Initialize file upload handlers
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload('appletter-upload', 'uploaded-appletter-files');
        setupFileUpload('permitforms-upload', 'uploaded-permitforms-files');
        setupFileUpload('architectural-upload', 'uploaded-architectural-files');
        setupFileUpload('structural-upload', 'uploaded-structural-files');
        setupFileUpload('electrical-upload', 'uploaded-electrical-files');
        setupFileUpload('plumbing-upload', 'uploaded-plumbing-files');
        setupFileUpload('mechanical-upload', 'uploaded-mechanical-files');
        setupFileUpload('fencing-upload', 'uploaded-fencing-files');
        setupFileUpload('proof-upload', 'uploaded-proof-files');
        setupFileUpload('bom-upload', 'uploaded-bom-files');
        setupFileUpload('analysis-upload', 'uploaded-analysis-files');
        setupFileUpload('barangay-upload', 'uploaded-barangay-files');
        setupFileUpload('cshp-upload', 'uploaded-cshp-files');
        
        // Update progress when zoning checkbox is clicked
        document.getElementById('zoning-confirm')?.addEventListener('change', updateUploadCount);
    });
</script>
@endsection