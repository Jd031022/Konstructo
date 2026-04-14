@extends('layouts.dashboard')

@section('title', 'Building Permit Preview')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-8">
        <a href="javascript:history.back()" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-[#155386] to-[#40798C] rounded-3xl p-8 md:p-12 mb-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Building Permit Application</h1>
                <p class="text-white/80 text-lg">Everything you need to know before applying for a building permit</p>
            </div>
            <div class="flex gap-3">
                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-sm font-medium">Processing Time: 7-10 days</span>
                <span class="px-4 py-2 bg-green-500/30 backdrop-blur-sm rounded-xl text-sm font-medium">Online Application</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column - Main Information -->
        <div class="lg:col-span-2 space-y-8">

            <!-- What is Building Permit Card -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-[#155386]/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">What is a Building Permit?</h2>
                </div>
                <p class="text-gray-600 leading-relaxed mb-4">
                    A building permit is an official approval issued by the local government unit (LGU) that allows you to proceed with construction, renovation, or demolition of a building or structure. It ensures that your project complies with the National Building Code, zoning regulations, and safety standards.
                </p>
                <div class="bg-blue-50 border-l-4 border-[#155386] p-4 rounded-r-xl">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Important:</span> Construction without a permit may result in penalties, stop-work orders, or even demolition of unauthorized structures.
                    </p>
                </div>
            </div>

            <!-- Requirements Card -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-[#155386]/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Required Documents</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Requirements -->
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                            Basic Requirements
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Duly accomplished Building Permit Application Form</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Transfer Certificate of Title (TCT) or Condominium Certificate of Title (CCT)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Tax Declaration of the lot and building (if applicable)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Current Tax Receipt of Real Property Tax</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Lot Plan / Vicinity Map (duly signed by Geodetic Engineer)</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Technical Requirements -->
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                            Technical Requirements
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Architectural Plans (5 sets, signed and sealed by architect)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Structural Plans (signed and sealed by civil engineer)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Sanitary / Plumbing Plans (signed by sanitary engineer)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Electrical Plans (signed by professional electrical engineer)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">Construction Safety and Health Program</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Process Flow Card -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-[#155386]/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Application Process</h2>
                </div>

                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                    <!-- Steps -->
                    <div class="space-y-8">
                        <!-- Step 1 -->
                        <div class="relative flex gap-6">
                            <div class="w-16 h-16 bg-[#155386] rounded-2xl flex items-center justify-center shadow-lg z-10">
                                <span class="text-white font-bold text-xl">1</span>
                            </div>
                            <div class="flex-1 pt-2">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">Online Application</h3>
                                <p class="text-sm text-gray-500">Fill out the online application form and upload all required documents.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative flex gap-6">
                            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center z-10">
                                <span class="text-gray-600 font-bold text-xl">2</span>
                            </div>
                            <div class="flex-1 pt-2">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">Document Review</h3>
                                <p class="text-sm text-gray-500">OBO staff reviews your documents for completeness and compliance.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative flex gap-6">
                            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center z-10">
                                <span class="text-gray-600 font-bold text-xl">3</span>
                            </div>
                            <div class="flex-1 pt-2">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">Payment of Fees</h3>
                                <p class="text-sm text-gray-500">Pay the required building permit fees at the City Treasurer's Office.</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="relative flex gap-6">
                            <div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center z-10">
                                <span class="text-gray-600 font-bold text-xl">4</span>
                            </div>
                            <div class="flex-1 pt-2">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">Permit Issuance</h3>
                                <p class="text-sm text-gray-500">Building permit is released after approval and payment confirmation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Summary Card -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Application Summary</h2>

                <!-- Fee Estimate -->
                <div class="mb-6">
                    <p class="text-sm text-gray-500 mb-2">Estimated Fees</p>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Building Permit Fee</span>
                            <span class="font-medium">₱ 5,000 - 20,000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Location Clearance</span>
                            <span class="font-medium">₱ 500 - 1,000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Inspection Fee</span>
                            <span class="font-medium">₱ 1,000 - 3,000</span>
                        </div>
                        <div class="border-t border-gray-100 my-2 pt-2">
                            <div class="flex justify-between font-semibold">
                                <span class="text-gray-800">Total Estimate</span>
                                <span class="text-[#155386]">₱ 6,500 - 24,000</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">*Actual fees may vary based on project scope</p>
                </div>

                <!-- Validity -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-gray-800">Permit Validity</span>
                    </div>
                    <p class="text-sm text-gray-600">1 year from date of issuance, renewable annually</p>
                </div>

                <!-- Processing Info -->
                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Processing time: <span class="font-medium">7-10 working days</span></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Validity: <span class="font-medium">1 year</span></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Mode of payment: <span class="font-medium">Over-the-counter</span></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="#" id="proceed-application-btn" class="block w-full px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white rounded-xl hover:from-[#1F363D] hover:to-[#1F363D] transition-all duration-300 font-medium text-center shadow-lg hover:shadow-xl">
                        Proceed to Application
                    </a>
                    <button onclick="downloadChecklist()" class="block w-full px-6 py-4 border-2 border-[#155386] text-[#155386] rounded-xl hover:bg-[#155386]/5 transition-all duration-300 font-medium text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Checklist
                    </button>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <h3 class="font-semibold text-gray-800 mb-4">Need Help?</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>(052) 123-4567</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>obo@ligao.gov.ph</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>2F City Hall, Legazpi City</span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400">Office hours: Monday - Friday, 8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-8 bg-white rounded-3xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                    How long does it take to get a building permit?
                </h3>
                <p class="text-sm text-gray-500 pl-4">Processing typically takes 7-10 working days from complete submission of requirements.</p>
            </div>
            <div class="space-y-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                    Can I apply without hiring an architect?
                </h3>
                <p class="text-sm text-gray-500 pl-4">No, plans must be signed and sealed by licensed professionals (architect/engineer).</p>
            </div>
            <div class="space-y-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                    What if my documents are incomplete?
                </h3>
                <p class="text-sm text-gray-500 pl-4">You'll receive a notice of incomplete requirements and have 15 days to comply.</p>
            </div>
            <div class="space-y-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 bg-[#155386] rounded-full"></span>
                    Is the building permit renewable?
                </h3>
                <p class="text-sm text-gray-500 pl-4">Yes, if construction hasn't started within 1 year, the permit must be renewed.</p>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="mt-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-3xl p-8 text-white text-center shadow-xl">
        <h2 class="text-2xl font-bold mb-2">Ready to Start Your Application?</h2>
        <p class="text-white/80 mb-6">Begin your building permit application online today</p>
        <a href="#" id="cta-application-btn" class="inline-block px-8 py-4 bg-white text-[#155386] rounded-xl hover:bg-gray-100 transition-all duration-300 font-semibold shadow-lg">
            Apply Now
        </a>
    </div>
</div>

<script>
// CSRF Token Helper
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    return token || '{{ csrf_token() }}';
}

// Create draft and redirect to step 1
async function createDraftAndRedirect() {
    try {
        const response = await fetch('/applicant/application/create-draft', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data && data.data.id) {
            window.location.href = '/applicant/application/step1?id=' + data.data.id;
        } else if (data.limit_reached) {
            alert('You have reached the maximum limit of 3 applications.');
        } else {
            alert(data.message || 'Failed to create new application');
        }
    } catch (error) {
        console.error('Error creating draft:', error);
        alert('An error occurred. Please try again.');
    }
}

// Proceed to Application button
document.getElementById('proceed-application-btn')?.addEventListener('click', function(e) {
    e.preventDefault();
    createDraftAndRedirect();
});

// CTA Apply Now button
document.getElementById('cta-application-btn')?.addEventListener('click', function(e) {
    e.preventDefault();
    createDraftAndRedirect();
});

// Download Checklist Function
function downloadChecklist() {
    const checklist = [
        "BUILDING PERMIT APPLICATION CHECKLIST",
        "=====================================",
        "",
        "BASIC REQUIREMENTS:",
        "☐ Duly accomplished Building Permit Application Form",
        "☐ Transfer Certificate of Title (TCT) or Condominium Certificate of Title (CCT)",
        "☐ Tax Declaration of the lot and building",
        "☐ Current Tax Receipt of Real Property Tax",
        "☐ Lot Plan / Vicinity Map (duly signed by Geodetic Engineer)",
        "",
        "TECHNICAL REQUIREMENTS:",
        "☐ Architectural Plans (5 sets, signed and sealed by architect)",
        "☐ Structural Plans (signed and sealed by civil engineer)",
        "☐ Sanitary / Plumbing Plans (signed by sanitary engineer)",
        "☐ Electrical Plans (signed by professional electrical engineer)",
        "☐ Construction Safety and Health Program",
        "",
        "ADDITIONAL REQUIREMENTS (if applicable):",
        "☐ Locational Clearance",
        "☐ Environmental Compliance Certificate (ECC)",
        "☐ Fire Safety Evaluation Clearance",
        "",
        "NOTES:",
        "- All plans must be printed and bound",
        "- Bring original documents for verification",
        "- Pay fees at the City Treasurer's Office"
    ].join('\n');

    const blob = new Blob([checklist], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'building-permit-checklist.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    alert('Checklist downloaded successfully!');
}
</script>

<style>
    .sticky {
        position: sticky;
        top: 6rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    .max-h-\[450px\]::-webkit-scrollbar {
        width: 4px;
    }

    .max-h-\[450px\]::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .max-h-\[450px\]::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 4px;
    }
</style>
@endsection