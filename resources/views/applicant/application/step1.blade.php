@extends('layouts.app')

@section('title', 'Application - Step 1: Ownership Verification')

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
                <h2 class="text-2xl font-semibold text-gray-800">Step 1: Ownership Verification</h2>
                <p class="text-l text-gray-600">Please provide proof of ownership documents</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                <span class="text-sm font-semibold text-[#155386]">Ownership</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                <span class="text-sm font-medium text-gray-400">Project Info</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                <span class="text-sm font-medium text-gray-400">Download Forms</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                <span class="text-sm font-medium text-gray-400">Upload Docs</span>
                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">5</div>
                <span class="text-sm font-medium text-gray-400">Review & Submit</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form id="ownership-form" class="space-y-8">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">

                <!-- Property Ownership Status -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-8 h-8 bg-[#155386] rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Property Ownership Status</h3>
                            <p class="text-sm text-gray-600">Please indicate your relationship to the property</p>
                        </div>
                    </div>
                    <div class="flex gap-6 pl-11">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="is_owner" value="1" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" checked>
                            <span class="text-gray-700 group-hover:text-[#155386] transition">Yes, I am the owner</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="is_owner" value="0" class="w-4 h-4 text-[#155386] focus:ring-[#155386]">
                            <span class="text-gray-700 group-hover:text-[#155386] transition">No, I am authorized representative</span>
                        </label>
                    </div>
                </div>

                <!-- Proof of Ownership Documents -->
                <div>
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Proof of Ownership Documents</h3>
                    </div>
                    
                    <div class="space-y-5">
                        <!-- TCT / Deed of Sale Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                TCT / Deed of Sale <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Transfer Certificate of Title or Notarized Deed of Sale</p>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102" />
                                    </svg>
                                </div>
                                <input type="url" name="tct_link" id="tct_link" 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                                       placeholder="https://drive.google.com/file/d/..." 
                                       value="{{ $application->ownership->tct_link ?? '' }}"
                                       required>
                            </div>
                        </div>

                        <!-- Tax Declaration Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax Declaration <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Current Tax Declaration</p>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <input type="url" name="tax_declaration_link" id="tax_declaration_link" 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                                       placeholder="https://drive.google.com/file/d/..." 
                                       value="{{ $application->ownership->tax_declaration_link ?? '' }}"
                                       required>
                            </div>
                        </div>

                        <!-- Current Tax Receipt Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Current Tax Receipt <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">Proof of paid real property tax</p>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <input type="url" name="current_tax_receipt_link" id="current_tax_receipt_link" 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                                       placeholder="https://drive.google.com/file/d/..." 
                                       value="{{ $application->ownership->current_tax_receipt_link ?? '' }}"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Authorization Documents (Conditional) -->
                <div id="authorization-section" class="hidden">
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Authorization Documents</h3>
                        </div>
                        
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-4 mb-5 border border-yellow-200">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-yellow-800">Since you are not the property owner, you need to provide a Special Power of Attorney (SPA) from the registered owner.</p>
                            </div>
                        </div>
                        
                        <div class="space-y-5">
                            <!-- SPA Link -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Special Power of Attorney (SPA) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <input type="url" name="spa_link" id="spa_link" 
                                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                                           placeholder="https://drive.google.com/file/d/..." 
                                           value="{{ $application->ownership->spa_link ?? '' }}">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Required if you are an authorized representative</p>
                            </div>
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
    const applicationId = {{ $application->id }};

    function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'; }

    // Toggle authorization section based on owner status
    const isOwnerRadios = document.querySelectorAll('input[name="is_owner"]');
    const authorizationSection = document.getElementById('authorization-section');

    function toggleAuthorizationSection() {
        const isOwner = document.querySelector('input[name="is_owner"]:checked');
        if (authorizationSection) {
            const spaInput = document.getElementById('spa_link');
            
            if (isOwner && isOwner.value === '0') {
                authorizationSection.classList.remove('hidden');
                if (spaInput) spaInput.setAttribute('required', 'required');
            } else {
                authorizationSection.classList.add('hidden');
                if (spaInput) spaInput.removeAttribute('required');
            }
        }
    }

    if (isOwnerRadios.length > 0) {
        isOwnerRadios.forEach(radio => {
            radio.addEventListener('change', toggleAuthorizationSection);
        });
        toggleAuthorizationSection();
    }

    document.getElementById('ownership-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate required fields
        const requiredFields = [
            { id: 'tct_link', name: 'TCT / Deed of Sale' },
            { id: 'tax_declaration_link', name: 'Tax Declaration' },
            { id: 'current_tax_receipt_link', name: 'Current Tax Receipt' }
        ];
        
        const isOwner = document.querySelector('input[name="is_owner"]:checked');
        if (isOwner && isOwner.value === '0') {
            requiredFields.push(
                { id: 'spa_link', name: 'Special Power of Attorney (SPA)' }
            );
        }
        
        let hasError = false;
        for (const field of requiredFields) {
            const input = document.getElementById(field.id);
            if (!input || !input.value.trim()) {
                showErrorModal(`Please provide a link for: ${field.name}`);
                if (input) input.classList.add('border-red-500');
                hasError = true;
            } else if (!isValidGoogleDriveLink(input.value.trim())) {
                showErrorModal(`Please provide a valid Google Drive link for: ${field.name}`);
                input.classList.add('border-red-500');
                hasError = true;
            } else {
                input.classList.remove('border-red-500');
            }
        }
        
        if (hasError) return;
        
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/applicant/application/save-ownership', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Ownership documents saved! Redirecting to Step 2...');
                setTimeout(() => {
                    window.location.href = `/applicant/application/step2?id=${applicationId}`;
                }, 1500);
            } else {
                showErrorModal(data.message || 'Failed to save ownership documents');
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

    function isValidGoogleDriveLink(link) {
        const patterns = [
            /drive\.google\.com\/file\/d\//,
            /drive\.google\.com\/drive\/folders\//,
            /drive\.google\.com\/open\?id=/,
            /docs\.google\.com\/document\/d\//,
            /drive\.google\.com\/folderview\?id=/
        ];
        
        for (const pattern of patterns) {
            if (pattern.test(link)) return true;
        }
        
        return link.includes('drive.google.com') || link.includes('docs.google.com');
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
    const inputs = document.querySelectorAll('input');
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