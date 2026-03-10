@extends('layouts.app')

@section('title', 'New Application - Step 1')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Back Button -->
    <div>
        <a href="/user/applications" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to My Applications
        </a>
    </div>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">New Application</h1>
        <p class="text-gray-500 text-sm mt-1">Complete the steps below to submit your building permit application</p>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200"></div>
            <div class="absolute top-5 left-0 w-1/4 h-1 bg-[#155386]"></div>
            
            <!-- Steps -->
            <div class="relative flex justify-between">
                <!-- Step 1: Applicant Info (Active) -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-[#155386] rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <span class="text-white font-bold text-sm">1</span>
                    </div>
                    <p class="text-sm font-semibold text-[#155386]">Applicant Info</p>
                    <p class="text-xs text-gray-400">Step 1 of 4</p>
                </div>
                
                <!-- Step 2: Project Details -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <span class="text-gray-600 font-bold text-sm">2</span>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Project Details</p>
                    <p class="text-xs text-gray-400">Step 2 of 4</p>
                </div>
                
                <!-- Step 3: Document Upload -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <span class="text-gray-600 font-bold text-sm">3</span>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Document Upload</p>
                    <p class="text-xs text-gray-400">Step 3 of 4</p>
                </div>
                
                <!-- Step 4: Review & Submit -->
                <div class="text-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-2 relative z-10">
                        <span class="text-gray-600 font-bold text-sm">4</span>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Review & Submit</p>
                    <p class="text-xs text-gray-400">Step 4 of 4</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1 Form - Applicant Information -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Form Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white">
            <h2 class="text-xl font-bold">Step 1: Applicant Information</h2>
            <p class="text-sm text-white/80 mt-1">Please provide your personal details</p>
        </div>
        
        <!-- Form Body -->
        <div class="p-6">
            <form id="applicant-form" action="/user/application/step2" method="POST">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Juan"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                        </div>
                        
                        <!-- Middle Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Middle Name <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Santos"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Dela Cruz"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                        </div>
                        
                        <!-- Suffix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Suffix <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                                <option value="">Select Suffix</option>
                                <option value="Jr.">Jr.</option>
                                <option value="Sr.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>
                        
                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- Place of Birth -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Place of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Legazpi City"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                        </div>
                        
                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <!-- Civil Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Civil Status <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white" required>
                                <option value="">Select Civil Status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        
                        <!-- Citizenship -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Citizenship <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Filipino"
                                   value="Filipino"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                        </div>
                        
                        <!-- TIN -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax Identification No. (TIN) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="XXX-XXX-XXX-000"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Contact Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   placeholder="juandelacruz@email.com"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                   required>
                            <p class="text-xs text-gray-400 mt-1">We'll send application updates to this email</p>
                        </div>
                        
                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">+63</span>
                                <input type="tel" 
                                       placeholder="917 123 4567"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400"
                                       required>
                            </div>
                        </div>
                        
                        <!-- Telephone Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Telephone Number <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="tel" 
                                   placeholder="e.g., 123-4567"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                        </div>
                    </div>
                </div>
                
                <!-- Address Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Address Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- House/Unit Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                House/Unit No. <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., 123"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- Street -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Street <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Rizal St."
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- Barangay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., San Jose"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- City/Municipality -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                City/Municipality <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Legazpi City"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- Province -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Albay"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                        
                        <!-- Zip Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Zip Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., 4500"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                   required>
                        </div>
                    </div>
                </div>
                
                <!-- Other Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Other Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Application Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Are you applying as: <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white" required>
                                <option value="">Select</option>
                                <option value="owner">Owner</option>
                                <option value="representative">Authorized Representative</option>
                                <option value="contractor">Contractor</option>
                            </select>
                        </div>
                        
                        <!-- Representative Name (conditional) -->
                        <div class="hidden" id="representative-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name of Representative <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   placeholder="e.g., Maria Santos"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Authorization Letter Upload (conditional) -->
                        <div class="hidden" id="authorization-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Authorization Letter <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" class="mt-1 h-4 w-4 text-[#155386] border-gray-300 rounded focus:ring-[#155386]" required>
                        <span class="text-sm text-gray-600">
                            I hereby certify that the information provided is true and correct to the best of my knowledge. I understand that any false statement may result in the denial or revocation of my application. 
                            <a href="#" class="text-[#155386] hover:underline">Read Terms and Conditions</a>
                        </span>
                    </label>
                </div>
                
                <!-- Form Navigation -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <a href="/user/applications" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </a>
                    <div class="flex gap-3">
                        <button type="button" onclick="saveAsDraft()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Save as Draft
                        </button>
                        <button type="submit" class="px-6 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium">
                            Next: Project Details
                            <svg class="inline-block h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
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
                <h4 class="font-semibold text-gray-800 mb-1">Need Help?</h4>
                <p class="text-sm text-gray-600 mb-2">If you have questions about the application process, you can:</p>
                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>Call our support hotline: (052) 123-4567</li>
                    <li>Email: building.permit@konstructo.gov.ph</li>
                    <li>Visit our office: 2/F City Hall, Legazpi City</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- JavaScript for conditional fields -->
<script>
    // Show/hide representative fields based on selection
    document.addEventListener('DOMContentLoaded', function() {
        const applicationType = document.querySelector('select[name="application_type"]');
        
        if (applicationType) {
            applicationType.addEventListener('change', function() {
                const repField = document.getElementById('representative-field');
                const authField = document.getElementById('authorization-field');
                
                if (this.value === 'representative') {
                    repField.classList.remove('hidden');
                    authField.classList.remove('hidden');
                } else {
                    repField.classList.add('hidden');
                    authField.classList.add('hidden');
                }
            });
        }
    });

    // Save as draft function
    function saveAsDraft() {
        // Here you would save the form data as draft
        alert('Application saved as draft. You can continue later.');
        // window.location.href = '/user/applications';
    }

    // Form validation
    document.getElementById('applicant-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add your validation logic here
        
        // If validation passes, proceed to next step
        window.location.href = '/user/application/step2';
    });
</script>
@endsection