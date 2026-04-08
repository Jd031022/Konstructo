@extends('layouts.app')

@section('title', 'Application - Step 1: Project Information')

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
                <h2 class="text-2xl font-semibold text-gray-800">Step 1: Project Information</h2>
                <p class="text-l text-gray-600">Please provide the basic information about your construction project</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-semibold text-[#155386]">Project Info</span>
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
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                <span class="text-sm font-medium text-gray-400">Review & Submit</span>
            </div>
        </div>
    </div>


    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form id="project-info-form" class="space-y-8">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">

                <!-- Project Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="project_title" 
                           id="project_title"
                           value="{{ $application->project_title ?? '' }}"
                           placeholder="e.g., Two-Storey Residential Building"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Give a descriptive name for your project</p>
                </div>

                <!-- Project Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="project_location" 
                           id="project_location"
                           value="{{ $application->project_location ?? '' }}"
                           placeholder="e.g., Brgy. San Jose, Legazpi City"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Complete address where the construction will take place</p>
                </div>

                <!-- Project Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project Type <span class="text-red-500">*</span>
                    </label>
                    <select name="project_type" id="project_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                        <option value="">Select Project Type</option>
                        <option value="residential" {{ $application->project_type == 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial" {{ $application->project_type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="industrial" {{ $application->project_type == 'industrial' ? 'selected' : '' }}>Industrial</option>
                        <option value="institutional" {{ $application->project_type == 'institutional' ? 'selected' : '' }}>Institutional</option>
                        <option value="mixed_use" {{ $application->project_type == 'mixed_use' ? 'selected' : '' }}>Mixed Use</option>
                        <option value="renovation" {{ $application->project_type == 'renovation' ? 'selected' : '' }}>Renovation</option>
                    </select>
                </div>

                <!-- Lot Area & Floor Area -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lot Area (sqm) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="lot_area" 
                               id="lot_area"
                               value="{{ $application->lot_area ?? '' }}"
                               placeholder="e.g., 250"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Floor Area (sqm) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="floor_area" 
                               id="floor_area"
                               value="{{ $application->floor_area ?? '' }}"
                               placeholder="e.g., 200"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    </div>
                </div>

                <!-- Number of Floors & Estimated Cost -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Floors <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="num_floors" 
                               id="num_floors"
                               value="{{ $application->num_floors ?? '1' }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Estimated Cost (PHP) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="estimated_cost" 
                               id="estimated_cost"
                               value="{{ $application->estimated_cost ?? '' }}"
                               placeholder="e.g., 5000000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    </div>
                </div>

                <!-- Project Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Project Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="project_description" 
                              id="project_description" 
                              rows="4"
                              placeholder="Briefly describe the scope of your construction project..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">{{ $application->project_description ?? '' }}</textarea>
                </div>

                <!-- Owner/Applicant Information -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Owner/Applicant Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="owner_name" 
                                   id="owner_name"
                                   value="{{ $application->owner_name ?? (Auth::user()->first_name . ' ' . Auth::user()->last_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Owner's Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="owner_address" 
                                   id="owner_address"
                                   value="{{ $application->owner_address ?? (Auth::user()->address ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   name="contact_number" 
                                   id="contact_number"
                                   value="{{ $application->contact_number ?? (Auth::user()->phone_number ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="owner_email" 
                                   id="owner_email"
                                   value="{{ $application->owner_email ?? Auth::user()->email }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Professional Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Architect's Name
                            </label>
                            <input type="text" 
                                   name="architect_name" 
                                   id="architect_name"
                                   value="{{ $application->architect_name ?? '' }}"
                                   placeholder="Name of licensed architect"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Architect's License Number
                            </label>
                            <input type="text" 
                                   name="architect_license" 
                                   id="architect_license"
                                   value="{{ $application->architect_license ?? '' }}"
                                   placeholder="PRC License Number"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Civil Engineer's Name
                            </label>
                            <input type="text" 
                                   name="engineer_name" 
                                   id="engineer_name"
                                   value="{{ $application->engineer_name ?? '' }}"
                                   placeholder="Name of licensed civil engineer"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Engineer's License Number
                            </label>
                            <input type="text" 
                                   name="engineer_license" 
                                   id="engineer_license"
                                   value="{{ $application->engineer_license ?? '' }}"
                                   placeholder="PRC License Number"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit" id="submit-btn"
                            class="inline-flex items-center px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium shadow-md">
                        Save & Continue to Step 2
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </form>
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

function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'; }

document.getElementById('project-info-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = ['project_title', 'project_location', 'project_type', 'lot_area', 'floor_area', 'num_floors', 'estimated_cost', 'project_description', 'owner_name', 'owner_address', 'contact_number', 'owner_email'];
    let hasError = false;
    
    for (const field of requiredFields) {
        const input = document.getElementById(field);
        if (!input || !input.value.trim()) {
            showErrorModal(`Please fill in all required fields. Missing: ${field.replace(/_/g, ' ')}`);
            input?.classList.add('border-red-500');
            hasError = true;
            break;
        } else {
            input?.classList.remove('border-red-500');
        }
    }
    
    if (hasError) return;
    
    // Validate email format
    const email = document.getElementById('owner_email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showErrorModal('Please enter a valid email address.');
        document.getElementById('owner_email').classList.add('border-red-500');
        return;
    }
    
    // Validate phone number (Philippines format)
    const phone = document.getElementById('contact_number').value;
    const phoneRegex = /^(09|\+639)\d{9}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        showErrorModal('Please enter a valid Philippine mobile number (e.g., 09171234567)');
        document.getElementById('contact_number').classList.add('border-red-500');
        return;
    }
    
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/applicant/application/save-project-info', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf(),
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessModal('Project information saved! Redirecting to Step 2...');
            setTimeout(() => {
                window.location.href = `/applicant/application/step2?id=${applicationId}`;
            }, 1500);
        } else {
            showErrorModal(data.message || 'Failed to save project information');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred. Please try again.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

function copyApplicationNumber(){
    const num = '{{ $application->application_number }}';
    if(num) {
        navigator.clipboard.writeText(num).then(()=>showSuccessModal('Application number copied!')).catch(()=>showErrorModal('Copy failed.'));
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

// Clear red borders when user starts typing
const inputs = document.querySelectorAll('input, textarea, select');
inputs.forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('border-red-500');
    });
});
</script>

<style>
.animate-spin{animation:spin 1s linear infinite;}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
button:disabled{cursor:not-allowed;opacity:.65;}
</style>
@endsection