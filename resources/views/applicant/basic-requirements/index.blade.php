{{-- resources/views/applicant/basic-requirements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Basic Requirements')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Basic Requirements</h1>
        <p class="text-gray-600 mt-2">Please submit the required documents to proceed with your application.</p>
    </div>

    @if($basicRequirement && $basicRequirement->status === 'pending')
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-yellow-800">Requirements Pending Review</p>
                    <p class="text-sm text-yellow-700">Your submitted requirements are being reviewed by staff. You will be notified once approved.</p>
                    <p class="text-xs text-yellow-600 mt-1">Submitted on: {{ $basicRequirement->submitted_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($basicRequirement && $basicRequirement->status === 'rejected')
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-red-800">Requirements Rejected</p>
                    <p class="text-sm text-red-700 mt-1">Your requirements were rejected. Please review the reason below and resubmit.</p>
                    <div class="mt-2 p-3 bg-white rounded-lg border border-red-200">
                        <p class="text-sm font-medium text-red-800">Reason for rejection:</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $basicRequirement->rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-semibold text-gray-800">Document Submission</h2>
            <p class="text-sm text-gray-600 mt-1">Please provide Google Drive links to your documents (make sure sharing is set to "Anyone with the link can view")</p>
        </div>

        <form id="basic-requirements-form" class="p-6 space-y-6">
            @csrf
            
            <!-- Property Ownership Status -->
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                <label class="block font-semibold text-gray-800 mb-3">Are you the property owner?</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_owner" value="1" class="w-4 h-4 text-[#155386]" {{ (!isset($basicRequirement) || $basicRequirement->is_owner) ? 'checked' : '' }}>
                        <span class="text-gray-700">Yes, I am the owner</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_owner" value="0" class="w-4 h-4 text-[#155386]" {{ (isset($basicRequirement) && !$basicRequirement->is_owner) ? 'checked' : '' }}>
                        <span class="text-gray-700">No, I am authorized representative</span>
                    </label>
                </div>
            </div>

            <!-- Proof of Ownership Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Proof of Ownership
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Transfer Certificate of Title (TCT) <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="tct_link" id="tct_link" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#155386] focus:border-[#155386]"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->tct_link ?? '' }}"
                               {{ ($basicRequirement && $basicRequirement->status === 'pending') ? 'disabled' : '' }}>
                        <p class="text-xs text-gray-500 mt-1">Provide a Google Drive link to the Certified True Copy of TCT</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tax Declaration <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="tax_declaration_link" id="tax_declaration_link" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#155386] focus:border-[#155386]"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->tax_declaration_link ?? '' }}"
                               {{ ($basicRequirement && $basicRequirement->status === 'pending') ? 'disabled' : '' }}>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Tax Receipt <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="current_tax_receipt_link" id="current_tax_receipt_link" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#155386] focus:border-[#155386]"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->current_tax_receipt_link ?? '' }}"
                               {{ ($basicRequirement && $basicRequirement->status === 'pending') ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>

            <!-- Authorization Documents (conditional) -->
            <div id="authorization-section" class="{{ (isset($basicRequirement) && !$basicRequirement->is_owner) ? '' : 'hidden' }}">
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Authorization Documents
                    </h3>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800 flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Since you are not the property owner, you need to provide authorization documents.</span>
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notarized Deed of Sale <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="deed_of_sale_link" id="deed_of_sale_link" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#155386] focus:border-[#155386]"
                                   placeholder="https://drive.google.com/file/d/..." 
                                   value="{{ $basicRequirement->deed_of_sale_link ?? '' }}"
                                   {{ ($basicRequirement && $basicRequirement->status === 'pending') ? 'disabled' : '' }}>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Special Power of Attorney (SPA) <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="spa_link" id="spa_link" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#155386] focus:border-[#155386]"
                                   placeholder="https://drive.google.com/file/d/..." 
                                   value="{{ $basicRequirement->spa_link ?? '' }}"
                                   {{ ($basicRequirement && $basicRequirement->status === 'pending') ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                @if(!$basicRequirement || $basicRequirement->status !== 'pending')
                    <button type="submit" id="submit-btn" 
                            class="px-8 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition font-medium shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Submit Requirements
                    </button>
                @endif
                
                @if($basicRequirement && $basicRequirement->status === 'approved')
                    <a href="{{ route('applicant.application.step1') }}" 
                       class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Proceed to Step 1
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
        <p class="text-sm text-gray-600">For assistance with your application, please contact:</p>
        <div class="mt-2 text-sm text-gray-600">
            <p>📞 Phone: (02) 1234-5678</p>
            <p>📧 Email: obo@konstructo.gov.ph</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isOwnerRadios = document.querySelectorAll('input[name="is_owner"]');
    const authorizationSection = document.getElementById('authorization-section');
    const form = document.getElementById('basic-requirements-form');
    const submitBtn = document.getElementById('submit-btn');

    // Toggle authorization section based on owner status
    function toggleAuthorizationSection() {
        const isOwner = document.querySelector('input[name="is_owner"]:checked');
        if (isOwner && isOwner.value === '0') {
            authorizationSection.classList.remove('hidden');
        } else {
            authorizationSection.classList.add('hidden');
        }
    }

    isOwnerRadios.forEach(radio => {
        radio.addEventListener('change', toggleAuthorizationSection);
    });

    toggleAuthorizationSection();

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate Google Drive links
        const driveLinks = [
            'tct_link',
            'tax_declaration_link',
            'current_tax_receipt_link'
        ];
        
        const isOwner = document.querySelector('input[name="is_owner"]:checked');
        if (isOwner && isOwner.value === '0') {
            driveLinks.push('deed_of_sale_link', 'spa_link');
        }
        
        let hasError = false;
        for (const linkId of driveLinks) {
            const input = document.getElementById(linkId);
            if (input && input.value.trim()) {
                if (!isValidGoogleDriveLink(input.value.trim())) {
                    showError('Please provide a valid Google Drive link for ' + input.previousElementSibling.innerText);
                    input.classList.add('border-red-500');
                    hasError = true;
                } else {
                    input.classList.remove('border-red-500');
                }
            } else if (input && input.required !== false) {
                showError('Please fill in all required fields');
                input.classList.add('border-red-500');
                hasError = true;
            }
        }
        
        if (hasError) return;
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Submitting...
        `;
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("applicant.basic-requirements.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                if (data.errors) {
                    let errorMessages = [];
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessages.push(messages.join(', '));
                        const input = document.getElementById(field);
                        if (input) input.classList.add('border-red-500');
                    }
                    showError(errorMessages.join('\n'));
                } else {
                    showError(data.message || 'An error occurred');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Submit Requirements
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Submit Requirements
            `;
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
    
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
        errorDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 5000);
    }
    
    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
        successDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 5000);
    }
    
    // Clear red borders when user starts typing
    const inputs = document.querySelectorAll('input[type="url"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('border-red-500');
        });
    });
});

@if(session('success'))
    showSuccess('{{ session('success') }}');
@endif

@if(session('error'))
    showError('{{ session('error') }}');
@endif
</script>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endsection