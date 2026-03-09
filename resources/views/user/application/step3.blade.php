@extends('layouts.app')

@section('title', 'Review & Submit - Step 3 - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-8">
        <a href="/user/application/step2" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
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
                <p class="text-l text-gray-600">Review all your information and documents before final submission</p>
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
                <span class="text-sm font-medium text-gray-600">Upload Documents</span>
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

        <!-- Review Content -->
        <div class="p-8 space-y-8">

            <!-- Applicant Information Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Applicant Information</h3>
                    <a href="/user/application/step1" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </a>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-400">Full Name</p>
                            <p class="text-sm font-medium text-gray-800">Juan Santos Dela Cruz</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Date of Birth</p>
                            <p class="text-sm font-medium text-gray-800">January 15, 1990</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Place of Birth</p>
                            <p class="text-sm font-medium text-gray-800">Legazpi City, Albay</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Gender</p>
                            <p class="text-sm font-medium text-gray-800">Male</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Civil Status</p>
                            <p class="text-sm font-medium text-gray-800">Married</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Citizenship</p>
                            <p class="text-sm font-medium text-gray-800">Filipino</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">TIN</p>
                            <p class="text-sm font-medium text-gray-800">123-456-789-000</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email Address</p>
                            <p class="text-sm font-medium text-gray-800">juan.delacruz@email.com</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Contact Number</p>
                            <p class="text-sm font-medium text-gray-800">0917 123 4567</p>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <p class="text-xs text-gray-400">Address</p>
                            <p class="text-sm font-medium text-gray-800">123 Rizal St., Brgy. San Jose, Legazpi City, Albay 4500</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Information Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Project Information</h3>
                    <a href="/user/application/step1" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </a>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Project Name</p>
                            <p class="text-sm font-medium text-gray-800">Two-Storey Residential Building</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Project Type</p>
                            <p class="text-sm font-medium text-gray-800">Residential - New Construction</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total Floor Area</p>
                            <p class="text-sm font-medium text-gray-800">120 sqm</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Estimated Cost</p>
                            <p class="text-sm font-medium text-gray-800">₱ 1,500,000.00</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Lot Area</p>
                            <p class="text-sm font-medium text-gray-800">200 sqm</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Zoning Classification</p>
                            <p class="text-sm font-medium text-gray-800">Residential - R2</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-400">Project Location</p>
                            <p class="text-sm font-medium text-gray-800">Lot 5, Block 3, Brgy. San Jose, Legazpi City, Albay</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Uploaded Documents</h3>
                    <a href="/user/application/step2" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </a>
                </div>
                
                <!-- Required Documents -->
                <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                    <h4 class="font-medium text-gray-700 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Required Documents
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Application Form -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Application Form</p>
                                    <p class="text-xs text-gray-500">accomplished_form.pdf (2.4 MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">Signed application form dated May 5, 2025</p>
                                </div>
                            </div>
                        </div>

                        <!-- Proof of Ownership -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Proof of Ownership</p>
                                    <p class="text-xs text-gray-500">title_tct_123456.pdf (1.8 MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">TCT No. T-123456</p>
                                </div>
                            </div>
                        </div>

                        <!-- Architectural Plans -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Architectural Plans (5 sets)</p>
                                    <p class="text-xs text-gray-500">arch_plan_1.pdf, arch_plan_2.pdf, +3 more</p>
                                    <p class="text-xs text-gray-400 mt-1">Floor plans, elevations, sections</p>
                                </div>
                            </div>
                        </div>

                        <!-- Structural Plans -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Structural Plans (5 sets)</p>
                                    <p class="text-xs text-gray-500">struct_plan_1.pdf, struct_plan_2.pdf, +3 more</p>
                                    <p class="text-xs text-gray-400 mt-1">Foundation plans, framing details</p>
                                </div>
                            </div>
                        </div>

                        <!-- Valid ID -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Valid ID</p>
                                    <p class="text-xs text-gray-500">passport.jpg (0.8 MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">Passport No. P1234567, issued May 2020</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optional Documents -->
                <div class="bg-gray-50 rounded-xl p-6 mt-4">
                    <h4 class="font-medium text-gray-700 text-sm flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        Optional Documents
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Electrical Plans -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Electrical Plans</p>
                                    <p class="text-xs text-gray-500">electrical_plan.pdf (1.2 MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">Electrical layout, riser diagram</p>
                                </div>
                            </div>
                        </div>

                        <!-- Plumbing Plans -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Sanitary/Plumbing Plans</p>
                                    <p class="text-xs text-gray-500">plumbing_plan.pdf (1.1 MB)</p>
                                    <p class="text-xs text-gray-400 mt-1">Water supply, drainage layout</p>
                                </div>
                            </div>
                        </div>
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
                            <p class="text-sm text-gray-600 mt-1">I hereby certify that the information provided and documents uploaded are true and correct to the best of my knowledge. I understand that any false statement or misrepresentation may result in the denial or revocation of my application and may subject me to legal consequences.</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center gap-3">
                        <input type="checkbox" id="agree-checkbox" class="h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                        <label for="agree-checkbox" class="text-sm text-gray-700">I agree to the terms and conditions and confirm that all information provided is accurate</label>
                    </div>
                </div>
            </div>

            <!-- Application Summary -->
            <div class="bg-[#155386]/5 rounded-xl p-6 border border-[#155386]/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Application Fee</p>
                        <p class="text-2xl font-bold text-gray-800">₱ 2,500.00</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Processing Time</p>
                        <p class="text-lg font-semibold text-gray-800">7-10 business days</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Documents Submitted</p>
                        <p class="text-lg font-semibold text-gray-800">7/7 required</p>
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
                    Please double-check all information before submitting. Once submitted, you cannot edit your application.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- JavaScript -->
<script>
    // Enable submit button only when checkbox is checked
    document.getElementById('agree-checkbox').addEventListener('change', function() {
        document.getElementById('submit-button').disabled = !this.checked;
    });

    // Submit application function
    function submitApplication() {
        if (!document.getElementById('agree-checkbox').checked) {
            alert('Please agree to the terms and conditions.');
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

        // Simulate API call
        setTimeout(() => {
            // Show success message
            alert('Application submitted successfully! You will be redirected to track your application.');
            
            // Redirect to applications list
            window.location.href = '/user/application-details';
        }, 2000);
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
</style>
@endsection