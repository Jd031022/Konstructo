@extends('layouts.dashboard')

@section('title', 'Applications - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Applications</h1>
            <p class="text-gray-500 text-sm mt-1">Manage and review all building permit applications</p>
        </div>
        
        <!-- Export and Filter Buttons -->
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>
            <button onclick="openNewApplicationModal()" 
                class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Application
            </button>
        </div>
    </div>

        <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       placeholder="Search users by name, email, or role..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
        
            
            <!-- Status Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">All Status</option>
                <option value="active">Pending Review</option>
                <option value="under-review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="for-release">For Release</option>
                <option value="complete">Completed</option>
                <option value="terminated">Terminated</option>
            </select>
            
            <!-- Filter Button -->
            <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Application ID</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Applicant</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Project Name</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Type</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Date Submitted</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Application 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm font-medium text-[#155386]">APP-2025-001</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    JD
                                </div>
                                <span class="font-medium text-gray-800">Juan Dela Cruz</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">Residential Building</td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500">New Construction</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">May 5, 2025</td>
                        <td class="py-4 px-6">
    <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium whitespace-nowrap">Pending Review</span>
</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="/staff/application-details" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="editApplication(1)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="deleteApplication(1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Application 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm font-medium text-[#155386]">APP-2025-002</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    MS
                                </div>
                                <span class="font-medium text-gray-800">Maria Santos</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">Commercial Building</td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500">Renovation</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">May 4, 2025</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-medium">Under Review</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="/staff/application-details" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="editApplication(2)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Application 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm font-medium text-[#155386]">APP-2025-003</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    PR
                                </div>
                                <span class="font-medium text-gray-800">Pedro Reyes</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">Warehouse</td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500">New Construction</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">May 3, 2025</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Approved</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="/staff/application-details" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="editApplication(3)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Application 4 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm font-medium text-[#155386]">APP-2025-004</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-gray-700 text-xs font-bold">
                                    AL
                                </div>
                                <span class="font-medium text-gray-800">Anna Lopez</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">Residential Building</td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500">Renovation</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">May 2, 2025</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">Rejected</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="/staff/application-details" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Application 5 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm font-medium text-[#155386]">APP-2025-005</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    CG
                                </div>
                                <span class="font-medium text-gray-800">Carlos Gomez</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">Commercial Building</td>
                        <td class="py-4 px-6">
                            <span class="text-xs text-gray-500">New Construction</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">May 1, 2025</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">For Release</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="/staff/application-details" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button onclick="editApplication(5)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-gray-500">Showing 1 to 5 of 1,247 applications</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                    Previous
                </button>
                <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">1</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">2</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">3</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">4</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">5</button>
                <span class="text-gray-400">...</span>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">25</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>
    </div>

</div>

<div id="new-application-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-3xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Modal Header - Sticky -->
                <div class="sticky top-0 px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center z-10">
                    <h3 class="text-xl font-bold">New Application</h3>
                    <button onclick="closeNewApplicationModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body - Scrollable -->
                <div class="p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <form id="new-application-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Applicant Information -->
                            <div class="md:col-span-2">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4">Applicant Information</h4>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" 
                                       placeholder="e.g., Juan"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" 
                                       placeholder="e.g., Dela Cruz"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" 
                                       placeholder="juandelacruz@email.com"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="tel" 
                                       placeholder="0917 123 4567"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <!-- Project Information -->
                            <div class="md:col-span-2 mt-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4">Project Information</h4>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
                                <input type="text" 
                                       placeholder="e.g., Two-Storey Residential Building"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Project Type</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="renovation">Renovation</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <input type="text" 
                                       placeholder="e.g., Brgy. San Jose, Legazpi City"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <!-- Documents -->
                            <div class="md:col-span-2 mt-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4">Required Documents</h4>
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <!-- Document Type Dropdown -->
                                <div class="relative">
                                    <select id="document-select" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent appearance-none bg-white">
                                        <option value="" disabled selected>Select Document Type</option>
                                        <option value="Application Letter">Application Letter</option>
                                        <option value="Building Permit Forms">Building Permit Forms</option>
                                        <option value="Architectural Plans (5 sets)">Architectural Plans (5 sets)</option>
                                        <option value="Structural Plans (5 sets)">Structural Plans (5 sets)</option>
                                        <option value="Electrical Plans">Electrical Plans</option>
                                        <option value="Mechanical Plans">Mechanical Plans</option>
                                        <option value="Sanitary/Plumbing Plans">Sanitary/Plumbing Plans</option>
                                        <option value="Fire Safety Checklist">Fire Safety Checklist</option>
                                        <option value="Locational Clearance">Locational Clearance</option>
                                        <option value="Contractor's License">Contractor's License</option>
                                    </select>
                                    <!-- Dropdown arrow icon -->
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Selected Documents List -->
                                <div class="mt-4 space-y-2" id="selected-documents">
                                    <!-- Items will be added here dynamically -->
                                </div>

<div class="flex justify-end mt-3">
    <button type="button" onclick="addDocument()" 
        class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition shadow-sm text-sm font-medium">
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Document
    </button>
</div>
                            </div>

                            <!-- File Upload -->
                            <div class="md:col-span-2 mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Documents</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-[#155386] transition cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Drag and drop files here or <span class="text-[#155386] font-medium">browse</span></p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, PNG, JPG up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" onclick="closeNewApplicationModal()" 
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Applications -->
<script>
    function openNewApplicationModal() {
        document.getElementById('new-application-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeNewApplicationModal() {
        document.getElementById('new-application-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editApplication(id) {
        // Redirect to edit page or open edit modal
        window.location.href = `/applications/${id}/edit`;
    }

    function deleteApplication(id) {
        if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
            // API call to delete application
            console.log('Deleting application:', id);
            alert('Application has been deleted.');
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('new-application-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeNewApplicationModal();
                }
            });

            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeNewApplicationModal();
                }
            });
        }
    });

function addDocument() {
    const select = document.getElementById('document-select');
    const selectedValue = select.value;
    const selectedText = select.options[select.selectedIndex].text;
    
    if (!selectedValue) {
        alert('Please select a document type');
        return;
    }
    
    // Check if document already added
    const existingItems = document.querySelectorAll('#selected-documents .document-item');
    for (let item of existingItems) {
        if (item.dataset.value === selectedValue) {
            alert('This document has already been added');
            return;
        }
    }
    
    // Create new document item
    const container = document.getElementById('selected-documents');
    const newItem = document.createElement('div');
    newItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg document-item';
    newItem.dataset.value = selectedValue;
    
    newItem.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">${selectedText}</span>
        </div>
        <button type="button" onclick="removeDocument(this)" class="text-red-500 hover:text-red-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    
    container.appendChild(newItem);
    
    // Reset select
    select.value = '';
}

function removeDocument(button) {
    button.closest('.document-item').remove();
}
</script>

<!-- Add to existing styles -->
<style>
    /* Modal animations */
    #new-application-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #new-application-modal .bg-white {
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

    /* Custom scrollbar for modal */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #40798C;
    }
</style>
@endsection