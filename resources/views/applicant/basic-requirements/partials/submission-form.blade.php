<!-- Submission Form -->
<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Document Submission</h2>
                <p class="text-white/80 text-sm mt-0.5">Please provide Google Drive links to your documents</p>
            </div>
        </div>
    </div>

    <form id="basic-requirements-form" class="p-6 space-y-8">
        @csrf
        
        <!-- Property Ownership Status Card -->
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
                    <input type="radio" name="is_owner" value="1" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" {{ (!isset($basicRequirement) || $basicRequirement->is_owner) ? 'checked' : '' }}>
                    <span class="text-gray-700 group-hover:text-[#155386] transition">Yes, I am the owner</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="radio" name="is_owner" value="0" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" {{ (isset($basicRequirement) && !$basicRequirement->is_owner) ? 'checked' : '' }}>
                    <span class="text-gray-700 group-hover:text-[#155386] transition">No, I am authorized representative</span>
                </label>
            </div>
        </div>

        <!-- Proof of Ownership Section -->
        <div>
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Proof of Ownership</h3>
            </div>
            
            <div class="space-y-5">
                <!-- TCT Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Transfer Certificate of Title (TCT)
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102" />
                            </svg>
                        </div>
                        <input type="url" name="tct_link" id="tct_link" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->tct_link ?? '' }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Provide a Google Drive link to the Certified True Copy of TCT</p>
                </div>

                <!-- Tax Declaration Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tax Declaration
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <input type="url" name="tax_declaration_link" id="tax_declaration_link" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->tax_declaration_link ?? '' }}">
                    </div>
                </div>

                <!-- Current Tax Receipt Link -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Tax Receipt
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <input type="url" name="current_tax_receipt_link" id="current_tax_receipt_link" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                               placeholder="https://drive.google.com/file/d/..." 
                               value="{{ $basicRequirement->current_tax_receipt_link ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Authorization Documents (Conditional) -->
        <div id="authorization-section" class="{{ (isset($basicRequirement) && !$basicRequirement->is_owner) ? '' : 'hidden' }}">
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
                        <p class="text-sm text-yellow-800">Since you are not the property owner, you need to provide authorization documents from the registered owner.</p>
                    </div>
                </div>
                
                <div class="space-y-5">
                    <!-- Deed of Sale Link -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notarized Deed of Sale
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                            </div>
                            <input type="url" name="deed_of_sale_link" id="deed_of_sale_link" 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-[#155386] transition"
                                   placeholder="https://drive.google.com/file/d/..." 
                                   value="{{ $basicRequirement->deed_of_sale_link ?? '' }}">
                        </div>
                    </div>

                    <!-- SPA Link -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Special Power of Attorney (SPA)
                            <span class="text-red-500">*</span>
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
                                   value="{{ $basicRequirement->spa_link ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
            <button type="submit" id="submit-btn" 
                    class="px-8 py-3 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold flex items-center justify-center gap-2 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Submit Requirements for Review
            </button>
        </div>
    </form>
</div>

<script>
// Form submission logic for the submission form
document.addEventListener('DOMContentLoaded', function() {
    const isOwnerRadios = document.querySelectorAll('input[name="is_owner"]');
    const authorizationSection = document.getElementById('authorization-section');
    const form = document.getElementById('basic-requirements-form');
    const submitBtn = document.getElementById('submit-btn');

    if (isOwnerRadios.length > 0 && authorizationSection) {
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
    }

    // Form submission
    if (form) {
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
                        showErrorToast('Please provide a valid Google Drive link');
                        input.classList.add('border-red-500');
                        hasError = true;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                } else if (input && input.required !== false) {
                    showErrorToast('Please fill in all required fields');
                    input.classList.add('border-red-500');
                    hasError = true;
                }
            }
            
            if (hasError) return;
            
            // Disable submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                `;
            }
            
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
                    showSuccessToast(data.message);
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
                        showErrorToast(errorMessages.join('\n'));
                    } else {
                        showErrorToast(data.message || 'An error occurred');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Submit Requirements for Review
                        `;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorToast('An error occurred. Please try again.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Submit Requirements for Review
                    `;
                }
            }
        });
    }
    
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
    
    function showErrorToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
    
    function showSuccessToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    // Clear red borders when user starts typing
    const inputs = document.querySelectorAll('input[type="url"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('border-red-500');
        });
    });
});
</script>