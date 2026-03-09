@extends('layouts.dashboard')

@section('title', 'My Applications - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Applications</h1>
            <p class="text-gray-500 text-sm mt-1">Track and manage your building permit applications</p>
        </div>
        
        <!-- New Application Button -->
        <button onclick="openNewApplicationModal()" 
            class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md hover:shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Application
        </button>
    </div>


    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       placeholder="Search by application ID or project name..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
            
            <!-- Status Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">All Status</option>
                <option value="pending">Pending Review</option>
                <option value="under-review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="for-release">For Release</option>
                <option value="completed">Completed</option>
            </select>
            
            <!-- Date Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">Date</option>
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>
            
            <!-- Filter Button -->
            <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Applications List -->
    <div class="space-y-4">
        <!-- Application Card 1 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <!-- Header with ID and Status -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white font-bold">
                            BP
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Building Permit Application</h3>
                            <p class="text-sm text-gray-500">APP-2025-001 • Submitted May 5, 2025</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending Review</span>
                        <span class="text-sm text-gray-400">Updated 2 days ago</span>
                    </div>
                </div>
                
                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Project Name</p>
                        <p class="text-sm font-medium text-gray-800">Two-Storey Residential Building</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Location</p>
                        <p class="text-sm font-medium text-gray-800">Brgy. San Jose, Legazpi City</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Project Type</p>
                        <p class="text-sm font-medium text-gray-800">Residential</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Documents</p>
                        <p class="text-sm font-medium text-gray-800">4/6 Uploaded</p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-gray-600">Application Progress</span>
                        <span class="text-[#155386] font-medium">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#155386] to-[#40798C] h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                    <a href="/user/application-details/1" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </a>
                    <button class="inline-flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </button>
                    <button class="inline-flex items-center px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Message
                    </button>
                </div>
            </div>
        </div>

        <!-- Application Card 2 - Approved -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <!-- Header with ID and Status -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white font-bold">
                            BP
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Building Permit Application</h3>
                            <p class="text-sm text-gray-500">APP-2025-002 • Submitted April 28, 2025</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Approved</span>
                        <span class="text-sm text-gray-400">Updated 5 days ago</span>
                    </div>
                </div>
                
                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Project Name</p>
                        <p class="text-sm font-medium text-gray-800">Commercial Building Renovation</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Location</p>
                        <p class="text-sm font-medium text-gray-800">Brgy. Centro, Legazpi City</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Project Type</p>
                        <p class="text-sm font-medium text-gray-800">Commercial</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Documents</p>
                        <p class="text-sm font-medium text-gray-800">6/6 Uploaded</p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-gray-600">Application Progress</span>
                        <span class="text-green-600 font-medium">100%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                    <a href="/user/application-details/2" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </a>
                    <button class="inline-flex items-center px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Download Certificate
                    </button>
                </div>
            </div>
        </div>

        <!-- Application Card 3 - Rejected -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <!-- Header with ID and Status -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-xl flex items-center justify-center text-white font-bold">
                            BP
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Building Permit Application</h3>
                            <p class="text-sm text-gray-500">APP-2025-003 • Submitted April 15, 2025</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">Rejected</span>
                        <span class="text-sm text-gray-400">Updated 2 weeks ago</span>
                    </div>
                </div>
                
                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Project Name</p>
                        <p class="text-sm font-medium text-gray-800">Warehouse Construction</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Location</p>
                        <p class="text-sm font-medium text-gray-800">Brgy. Bogtong, Legazpi City</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Project Type</p>
                        <p class="text-sm font-medium text-gray-800">Industrial</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Documents</p>
                        <p class="text-sm font-medium text-gray-800">3/6 Uploaded</p>
                    </div>
                </div>
                
                <!-- Rejection Reason -->
                <div class="mb-4 p-3 bg-red-50 rounded-lg">
                    <p class="text-xs text-red-600 font-medium">Rejection Reason:</p>
                    <p class="text-sm text-gray-600">Incomplete documents and incorrect zoning classification.</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                    <a href="/user/application-details/3" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </a>
                    <button class="inline-flex items-center px-3 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reapply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Showing 1 to 3 of 8 applications</p>
        <div class="flex items-center gap-2">
            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                Previous
            </button>
            <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">1</button>
            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">2</button>
            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">3</button>
            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">
                Next
            </button>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Application Tips</h4>
                <p class="text-sm text-gray-600">Make sure to upload all required documents to avoid delays. You can track the status of your application in real-time. For assistance, contact our support team.</p>
            </div>
        </div>
    </div>

</div>

<!-- New Application Modal (reuse your existing modal) -->
<!-- Include your existing new application modal here -->

<script>
function openNewApplicationModal() {
    document.getElementById('new-application-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNewApplicationModal() {
    document.getElementById('new-application-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>
@endsection