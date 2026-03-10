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
            <button onclick="exportApplications()" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
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
                       id="search-input"
                       placeholder="Search by application number or applicant name..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
        
            <!-- Status Filter -->
            <select id="status-filter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">All Status</option>
                <option value="pending">Pending Review</option>
                <option value="under-review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="for-release">For Release</option>
                <option value="verified">Completed</option>
            </select>
            
            <!-- Filter Button -->
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
            
            <!-- Reset Button -->
            <button onclick="resetFilters()" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium">
                Reset
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading applications...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mt-4">No applications found</h3>
        <p class="text-gray-500 mt-2">There are no applications matching your criteria.</p>
        <button onclick="resetFilters()" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
            Clear Filters
        </button>
    </div>

    <!-- Applications Table -->
    <div id="applications-table-container" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Application Number</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Applicant</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Date Submitted</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody id="applications-table-body" class="divide-y divide-gray-100">
                    <!-- Applications will be loaded dynamically -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p id="pagination-info" class="text-sm text-gray-500"></p>
            <div class="flex items-center gap-2" id="pagination-controls">
                <!-- Pagination buttons will be loaded dynamically -->
            </div>
        </div>
    </div>

</div>

<!-- New Application Modal -->
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
                    <form id="new-application-form" onsubmit="submitNewApplication(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Applicant Information -->
                            <div class="md:col-span-2">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4">Applicant Information</h4>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="first-name"
                                       required
                                       placeholder="e.g., Juan"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="last-name"
                                       required
                                       placeholder="e.g., Dela Cruz"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       id="email"
                                       required
                                       placeholder="juandelacruz@email.com"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                                <input type="tel" 
                                       id="phone"
                                       required
                                       placeholder="0917 123 4567"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <!-- Project Information -->
                            <div class="md:col-span-2 mt-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-4">Project Information</h4>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Project Address <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="address"
                                       required
                                       placeholder="e.g., Brgy. San Jose, Legazpi City"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Project Type <span class="text-red-500">*</span></label>
                                <select id="project-type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="industrial">Industrial</option>
                                    <option value="renovation">Renovation</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Google Drive Link <span class="text-red-500">*</span></label>
                                <input type="url" 
                                       id="google-drive-link"
                                       required
                                       placeholder="https://drive.google.com/drive/folders/..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent placeholder:text-gray-400">
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
                                Create Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div id="edit-status-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold">Update Status</h3>
                    <button onclick="closeStatusModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6">
                    <form id="update-status-form" onsubmit="updateStatus(event)">
                        <input type="hidden" id="status-application-id">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Number</label>
                            <p id="status-app-number" class="text-sm font-mono bg-gray-50 p-3 rounded-lg"></p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Status <span class="text-red-500">*</span></label>
                            <select id="new-status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                                <option value="pending">Pending Review</option>
                                <option value="under-review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="for-release">For Release</option>
                                <option value="verified">Completed</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
                            <textarea id="status-remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent" placeholder="Add any remarks or notes..."></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" onclick="closeStatusModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Application</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this application? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" id="confirm-delete-btn" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
                <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
                <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
                <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    let applications = [];
    let filteredApplications = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let deleteId = null;

    // Load applications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadApplications();
        setupModals();
    });

    // Load applications from API
    async function loadApplications() {
        try {
            const response = await fetch('/staff/applications/data', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                applications = data.applications;
                applyFilters();
            } else {
                showErrorModal('Failed to load applications');
            }
        } catch (error) {
            console.error('Error loading applications:', error);
            showErrorModal('Failed to load applications');
        } finally {
            document.getElementById('loading-state').classList.add('hidden');
        }
    }

    // Apply filters
    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        
        filteredApplications = applications.filter(app => {
            const matchesSearch = app.application_number.toLowerCase().includes(searchTerm) || 
                                 (app.applicant_name && app.applicant_name.toLowerCase().includes(searchTerm));
            const matchesStatus = !statusFilter || app.status === statusFilter;
            return matchesSearch && matchesStatus;
        });
        
        currentPage = 1;
        displayApplications();
    }

    // Reset filters
    function resetFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('status-filter').value = '';
        applyFilters();
    }

    // Display applications
    function displayApplications() {
        const tableContainer = document.getElementById('applications-table-container');
        const emptyState = document.getElementById('empty-state');
        const tableBody = document.getElementById('applications-table-body');
        
        if (filteredApplications.length === 0) {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }
        
        tableContainer.classList.remove('hidden');
        emptyState.classList.add('hidden');
        
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedApps = filteredApplications.slice(start, end);
        
        tableBody.innerHTML = paginatedApps.map(app => createApplicationRow(app)).join('');
        
        updatePagination();
    }

    // Create application table row
    function createApplicationRow(app) {
        const initials = app.applicant_name ? 
            app.applicant_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 
            'NA';
        
        const statusColors = {
            'pending': 'bg-yellow-100 text-yellow-600',
            'under-review': 'bg-purple-100 text-purple-600',
            'approved': 'bg-green-100 text-green-600',
            'rejected': 'bg-red-100 text-red-600',
            'for-release': 'bg-blue-100 text-blue-600',
            'verified': 'bg-emerald-100 text-emerald-600'
        };
        
        const statusText = {
            'pending': 'Pending Review',
            'under-review': 'Under Review',
            'approved': 'Approved',
            'rejected': 'Rejected',
            'for-release': 'For Release',
            'verified': 'Completed'
        };
        
        const date = new Date(app.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        const gradientColors = [
            'from-[#155386] to-[#40798C]',
            'from-[#40798C] to-[#70A9A1]',
            'from-[#70A9A1] to-[#9EC5CB]',
            'from-[#9EC5CB] to-[#B8D8E3]'
        ];
        
        const randomGradient = gradientColors[app.id % gradientColors.length];
        
        return `
            <tr class="hover:bg-gray-50 transition">
                <td class="py-4 px-6">
                    <span class="font-mono text-sm font-medium text-[#155386]">${app.application_number}</span>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-r ${randomGradient} rounded-full flex items-center justify-center text-white text-xs font-bold">
                            ${initials}
                        </div>
                        <div>
                            <span class="font-medium text-gray-800">${app.applicant_name || 'N/A'}</span>
                            <p class="text-xs text-gray-500">${app.email || ''}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 text-sm text-gray-500">${formattedDate}</td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 ${statusColors[app.status] || 'bg-gray-100 text-gray-600'} rounded-full text-xs font-medium whitespace-nowrap">
                        ${statusText[app.status] || app.status}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <!-- View Details - Redirects to application-details page -->
                        <a href="/staff/application-details/${app.id}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <button onclick="openStatusModal(${app.id}, '${app.application_number}', '${app.status}')" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Update Status">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button onclick="openDeleteModal(${app.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    // Update pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
        const paginationInfo = document.getElementById('pagination-info');
        const paginationControls = document.getElementById('pagination-controls');
        
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredApplications.length);
        paginationInfo.textContent = `Showing ${start} to ${end} of ${filteredApplications.length} applications`;
        
        let controlsHtml = '';
        
        // Previous button
        controlsHtml += `
            <button onclick="changePage(${currentPage - 1})" 
                class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === 1 ? 'disabled' : ''}>
                Previous
            </button>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                controlsHtml += `<button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">${i}</button>`;
            } else {
                controlsHtml += `<button onclick="changePage(${i})" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">${i}</button>`;
            }
        }
        
        // Next button
        controlsHtml += `
            <button onclick="changePage(${currentPage + 1})" 
                class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === totalPages ? 'disabled' : ''}>
                Next
            </button>
        `;
        
        paginationControls.innerHTML = controlsHtml;
    }

    // Change page
    function changePage(page) {
        const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayApplications();
    }

    // Open status modal
    function openStatusModal(id, appNumber, currentStatus) {
        document.getElementById('status-application-id').value = id;
        document.getElementById('status-app-number').textContent = appNumber;
        document.getElementById('new-status').value = currentStatus;
        document.getElementById('edit-status-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close status modal
    function closeStatusModal() {
        document.getElementById('edit-status-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Update status
    async function updateStatus(event) {
        event.preventDefault();
        
        const id = document.getElementById('status-application-id').value;
        const status = document.getElementById('new-status').value;
        const remarks = document.getElementById('status-remarks').value;
        
        try {
            const response = await fetch(`/staff/applications/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status, remarks })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Application status updated successfully');
                closeStatusModal();
                loadApplications();
            } else {
                showErrorModal(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showErrorModal('Failed to update status');
        }
    }

    // Open delete modal
    function openDeleteModal(id) {
        deleteId = id;
        document.getElementById('delete-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close delete modal
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteId = null;
    }

    // Confirm delete
    async function confirmDelete() {
        if (!deleteId) return;
        
        const btn = document.getElementById('confirm-delete-btn');
        btn.innerHTML = 'Deleting...';
        btn.disabled = true;
        
        try {
            const response = await fetch(`/staff/applications/${deleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Application deleted successfully');
                closeDeleteModal();
                loadApplications();
            } else {
                showErrorModal(data.message || 'Failed to delete application');
            }
        } catch (error) {
            console.error('Error deleting application:', error);
            showErrorModal('Failed to delete application');
        } finally {
            btn.innerHTML = 'Delete';
            btn.disabled = false;
        }
    }

    // Submit new application
    async function submitNewApplication(event) {
        event.preventDefault();
        
        const formData = {
            first_name: document.getElementById('first-name').value,
            last_name: document.getElementById('last-name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            google_drive_link: document.getElementById('google-drive-link').value
        };
        
        // Note: project_type is not included as it's not in the database
        
        try {
            const response = await fetch('/staff/applications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Application created successfully');
                closeNewApplicationModal();
                loadApplications();
                document.getElementById('new-application-form').reset();
            } else {
                showErrorModal(data.message || 'Failed to create application');
            }
        } catch (error) {
            console.error('Error creating application:', error);
            showErrorModal('Failed to create application');
        }
    }

    // Export applications
    function exportApplications() {
        window.location.href = '/staff/applications/export';
    }

    // Modal functions
    function openNewApplicationModal() {
        document.getElementById('new-application-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeNewApplicationModal() {
        document.getElementById('new-application-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showSuccessModal(message) {
        document.getElementById('success-modal-message').textContent = message;
        document.getElementById('success-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    }

    function closeSuccessModal() {
        document.getElementById('success-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showErrorModal(message) {
        document.getElementById('error-modal-message').textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Setup modals
    function setupModals() {
        const modals = ['new-application-modal', 'edit-status-modal', 'delete-modal', 'error-modal', 'success-modal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        if (modalId === 'new-application-modal') closeNewApplicationModal();
                        if (modalId === 'edit-status-modal') closeStatusModal();
                        if (modalId === 'delete-modal') closeDeleteModal();
                        if (modalId === 'error-modal') closeErrorModal();
                        if (modalId === 'success-modal') closeSuccessModal();
                    }
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNewApplicationModal();
                closeStatusModal();
                closeDeleteModal();
                closeErrorModal();
                closeSuccessModal();
            }
        });
    }
</script>

<!-- Styles -->
<style>
    /* Modal animations */
    #new-application-modal,
    #edit-status-modal,
    #delete-modal,
    #error-modal,
    #success-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #new-application-modal .bg-white,
    #edit-status-modal .bg-white,
    #delete-modal .bg-white,
    #error-modal .bg-white,
    #success-modal .bg-white {
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

    /* Spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Custom scrollbar for modals */
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