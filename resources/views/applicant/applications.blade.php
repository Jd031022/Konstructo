@extends('layouts.dashboard')

@section('title', 'My Applications - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Applications</h1>
            <p class="text-gray-500 text-sm mt-1">Track and manage your building permit applications (Maximum 3 applications)</p>
        </div>
        
        <!-- New Application Button -->
        <a href="/applicant/application/step1" 
            id="new-application-btn"
            class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md hover:shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Application
        </a>
    </div>

    <!-- Application Limit Status (shown if user has applications) -->
    <div id="application-limit-container" class="mb-8 hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Your Applications Summary</h4>
                        <p class="text-sm text-gray-500">You have <span id="application-count">0</span> out of 3 applications</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2.5">
                        <div id="application-progress-bar" class="bg-[#155386] h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    <span id="remaining-slots" class="text-sm font-medium text-gray-600">3 slots left</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Limit Warning -->
    <div id="limit-warning" class="hidden">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-8" role="alert">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-bold">Application Limit Reached</p>
                    <p class="text-sm">You have reached the maximum limit of 3 applications. Please complete or delete existing applications before creating a new one.</p>
                </div>
            </div>
        </div>
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
                       id="search-input"
                       placeholder="Search by application number..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
            
            <!-- Status Filter -->
            <select id="status-filter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending Review</option>
                <option value="verified">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            
            <!-- Sort Filter -->
            <select id="sort-filter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>
            
            <!-- Filter Button -->
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading your applications...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mt-4">No applications yet</h3>
        <p class="text-gray-500 mt-2">Start your first building permit application today.</p>
        <a href="/applicant/application/step1" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Start New Application
        </a>
    </div>

    <!-- Applications List -->
    <div id="applications-list" class="space-y-4 hidden">
        <!-- Applications will be loaded dynamically -->
    </div>

    <!-- Pagination -->
    <div id="pagination" class="hidden flex items-center justify-between">
        <p id="pagination-info" class="text-sm text-gray-500"></p>
        <div class="flex items-center gap-2">
            <button id="prev-page" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                Previous
            </button>
            <span id="page-numbers" class="flex items-center gap-2"></span>
            <button id="next-page" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                Next
            </button>
        </div>
    </div>

    <!-- Quick Stats Banner -->
    <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] rounded-2xl p-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div>
                <p class="text-3xl font-bold" id="total-apps">0</p>
                <p class="text-sm opacity-90">Total Applications</p>
            </div>
            <div>
                <p class="text-3xl font-bold" id="pending-apps">0</p>
                <p class="text-sm opacity-90">Pending Review</p>
            </div>
            <div>
                <p class="text-3xl font-bold" id="remaining-apps">3</p>
                <p class="text-sm opacity-90">Remaining Slots</p>
            </div>
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
                <p class="text-sm text-gray-600">Your application number is generated when you download the Application Letter. Keep it for reference. Upload all 13 required documents to Google Drive and submit hard copies to OBO for final processing.</p>
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
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Draft</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this draft application? This action cannot be undone.</p>
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

<!-- JavaScript -->
<script>
    let applications = [];
    let filteredApplications = [];
    let currentPage = 1;
    const itemsPerPage = 5;
    let deleteId = null;
    const APPLICATION_LIMIT = 3;

    // Load applications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadApplications();
        loadStats();
        checkApplicationLimit();
        setupEventListeners();
        setupModals();
    });

    // Check application limit and update UI
    async function checkApplicationLimit() {
        try {
            const response = await fetch('/applicant/application/limit-info', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const canApply = data.data.can_apply;
                const current = data.data.current;
                const remaining = data.data.remaining;
                
                // Update progress bar and count
                updateProgressBar(current);
                document.getElementById('application-count').textContent = current;
                document.getElementById('remaining-slots').textContent = `${remaining} slot${remaining !== 1 ? 's' : ''} left`;
                
                // Show/hide limit container if user has applications
                if (current > 0) {
                    document.getElementById('application-limit-container').classList.remove('hidden');
                }
                
                // Show/hide limit warning
                const limitWarning = document.getElementById('limit-warning');
                const newAppBtn = document.getElementById('new-application-btn');
                
                if (!canApply) {
                    limitWarning.classList.remove('hidden');
                    if (newAppBtn) {
                        newAppBtn.classList.add('opacity-50', 'pointer-events-none');
                        newAppBtn.setAttribute('disabled', 'disabled');
                        newAppBtn.setAttribute('title', 'Application limit reached (max 3)');
                    }
                } else {
                    limitWarning.classList.add('hidden');
                    if (newAppBtn) {
                        newAppBtn.classList.remove('opacity-50', 'pointer-events-none');
                        newAppBtn.removeAttribute('disabled');
                        newAppBtn.removeAttribute('title');
                    }
                }
            }
        } catch (error) {
            console.error('Error checking application limit:', error);
        }
    }

    // Update progress bar
    function updateProgressBar(current) {
        const percentage = (current / APPLICATION_LIMIT) * 100;
        const progressBar = document.getElementById('application-progress-bar');
        progressBar.style.width = `${Math.min(percentage, 100)}%`;
        
        // Change color based on percentage
        if (percentage >= 100) {
            progressBar.classList.remove('bg-[#155386]');
            progressBar.classList.add('bg-red-600');
        } else if (percentage >= 66) {
            progressBar.classList.remove('bg-[#155386]');
            progressBar.classList.add('bg-yellow-600');
        } else {
            progressBar.classList.remove('bg-red-600', 'bg-yellow-600');
            progressBar.classList.add('bg-[#155386]');
        }
    }

    // Load applications from server
    async function loadApplications() {
        try {
            console.log('Loading applications...');
            
            const response = await fetch('/applicant/applications/data', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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

    // Load statistics
    async function loadStats() {
        try {
            const response = await fetch('/applicant/applications/stats');
            const stats = await response.json();
            
            // Update quick stats banner
            document.getElementById('total-apps').textContent = stats.total || 0;
            document.getElementById('pending-apps').textContent = stats.pending || 0;
            document.getElementById('remaining-apps').textContent = Math.max(0, APPLICATION_LIMIT - (stats.total || 0));
            
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Apply filters
    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const sortFilter = document.getElementById('sort-filter').value;
        
        filteredApplications = applications.filter(app => {
            const matchesSearch = app.application_number.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusFilter || app.status === statusFilter;
            return matchesSearch && matchesStatus;
        });
        
        // Sort applications
        filteredApplications.sort((a, b) => {
            const dateA = new Date(a.created_at);
            const dateB = new Date(b.created_at);
            return sortFilter === 'newest' ? dateB - dateA : dateA - dateB;
        });
        
        currentPage = 1;
        displayApplications();
    }

    // Display applications
    function displayApplications() {
        const listContainer = document.getElementById('applications-list');
        const emptyState = document.getElementById('empty-state');
        const pagination = document.getElementById('pagination');
        
        if (filteredApplications.length === 0) {
            listContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            pagination.classList.add('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        listContainer.classList.remove('hidden');
        
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedApps = filteredApplications.slice(start, end);
        
        listContainer.innerHTML = paginatedApps.map(app => createApplicationCard(app)).join('');
        
        updatePagination();
    }

    // Create application card HTML
    function createApplicationCard(app) {
        const statusColors = {
            'draft': 'bg-gray-100 text-gray-600',
            'pending': 'bg-yellow-100 text-yellow-600',
            'verified': 'bg-green-100 text-green-600',
            'rejected': 'bg-red-100 text-red-600'
        };
        
        const statusText = {
            'draft': 'Draft',
            'pending': 'Pending Review',
            'verified': 'Approved',
            'rejected': 'Rejected'
        };
        
        const progressColors = {
            'draft': 'from-gray-400 to-gray-500',
            'pending': 'from-[#155386] to-[#40798C]',
            'verified': 'from-green-500 to-green-600',
            'rejected': 'from-red-500 to-red-600'
        };
        
        const progress = app.status === 'draft' ? 25 : (app.status === 'pending' ? 65 : 100);
        
        const date = new Date(app.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        const actionButtons = app.status === 'draft' ? `
            <a href="/applicant/application/step2" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Continue
            </a>
            <button onclick="openDeleteModal(${app.id})" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Draft
            </button>
        ` : `
            <a href="/applicant/application-details/${app.id}" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Details
            </a>
            ${app.status === 'verified' ? `
            <button class="inline-flex items-center px-3 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Download Certificate
            </button>
            ` : ''}
            ${app.status === 'rejected' ? `
            <a href="/applicant/application/step1" class="inline-flex items-center px-3 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reapply
            </a>
            ` : ''}
        `;
        
        return `
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <!-- Header with ID and Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-r ${progressColors[app.status]} rounded-xl flex items-center justify-center text-white font-bold">
                                BP
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Building Permit Application</h3>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm text-gray-500 font-mono">${app.application_number}</p>
                                    <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">Application #</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 ${statusColors[app.status]} rounded-full text-xs font-medium">${statusText[app.status]}</span>
                            <span class="text-sm text-gray-400">Updated ${formattedDate}</span>
                        </div>
                    </div>
                    
                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-400">Project Name</p>
                            <p class="text-sm font-medium text-gray-800">${app.project_name || 'Not specified'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Location</p>
                            <p class="text-sm font-medium text-gray-800">${app.location || 'Not specified'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Project Type</p>
                            <p class="text-sm font-medium text-gray-800">${app.project_type || 'Not specified'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Documents</p>
                            <p class="text-sm font-medium text-gray-800">${app.google_drive_link ? '13/13' : '0/13'} in Google Drive</p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-600">Application Progress</span>
                            <span class="${app.status === 'verified' ? 'text-green-600' : (app.status === 'rejected' ? 'text-red-600' : 'text-[#155386]')} font-medium">${progress}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r ${progressColors[app.status]} h-2 rounded-full" style="width: ${progress}%"></div>
                        </div>
                    </div>
                    
                    <!-- Hard Copy Status -->
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-xs ${app.status === 'verified' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'} px-2 py-1 rounded-full">
                            Hard Copy: ${app.status === 'verified' ? 'Received' : (app.status === 'pending' ? 'Pending' : 'Not Submitted')}
                        </span>
                        <span class="text-xs text-gray-400">
                            ${app.status === 'verified' ? 'Verified by OBO' : (app.status === 'pending' ? 'Awaiting verification' : 'Submit originals to OBO')}
                        </span>
                    </div>
                    
                    <!-- Rejection Reason (if rejected) -->
                    ${app.status === 'rejected' && app.rejection_reason ? `
                    <div class="mb-4 p-3 bg-red-50 rounded-lg">
                        <p class="text-xs text-red-600 font-medium">Rejection Reason:</p>
                        <p class="text-sm text-gray-600">${app.rejection_reason}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Draft Info (if draft) -->
                    ${app.status === 'draft' ? `
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-600 font-medium">Application Number Generated:</p>
                        <p class="text-sm text-gray-600">Your application number is ${app.application_number}. Use this when submitting requirements.</p>
                    </div>
                    ` : ''}
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
    }

    // Update pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
        const pagination = document.getElementById('pagination');
        const paginationInfo = document.getElementById('pagination-info');
        const pageNumbers = document.getElementById('page-numbers');
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (filteredApplications.length <= itemsPerPage) {
            pagination.classList.add('hidden');
            return;
        }
        
        pagination.classList.remove('hidden');
        
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredApplications.length);
        paginationInfo.textContent = `Showing ${start} to ${end} of ${filteredApplications.length} applications`;
        
        // Generate page numbers
        let pagesHtml = '';
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                pagesHtml += `<button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">${i}</button>`;
            } else {
                pagesHtml += `<button onclick="goToPage(${i})" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">${i}</button>`;
            }
        }
        pageNumbers.innerHTML = pagesHtml;
        
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    // Go to specific page
    function goToPage(page) {
        currentPage = page;
        displayApplications();
    }

    // Previous page
    document.getElementById('prev-page')?.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            displayApplications();
        }
    });

    // Next page
    document.getElementById('next-page')?.addEventListener('click', function() {
        const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayApplications();
        }
    });

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
            const response = await fetch(`/applicant/applications/${deleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccessModal('Draft deleted successfully');
                closeDeleteModal();
                loadApplications();
                loadStats();
                checkApplicationLimit();
            } else {
                showErrorModal(data.message || 'Failed to delete draft');
            }
        } catch (error) {
            console.error('Error deleting draft:', error);
            showErrorModal('Failed to delete draft');
        } finally {
            btn.innerHTML = 'Delete';
            btn.disabled = false;
        }
    }

    // Setup event listeners
    function setupEventListeners() {
        // Search input debounce
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });
        
        // Filter changes
        document.getElementById('status-filter').addEventListener('change', applyFilters);
        document.getElementById('sort-filter').addEventListener('change', applyFilters);
    }

    // Setup modals
    function setupModals() {
        // Close modals when clicking outside
        const deleteModal = document.getElementById('delete-modal');
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) closeDeleteModal();
            });
        }
        
        if (errorModal) {
            errorModal.addEventListener('click', function(e) {
                if (e.target === errorModal) closeErrorModal();
            });
        }
        
        if (successModal) {
            successModal.addEventListener('click', function(e) {
                if (e.target === successModal) closeSuccessModal();
            });
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeErrorModal();
                closeSuccessModal();
            }
        });
    }

    // Error modal functions
    function showErrorModal(message) {
        document.getElementById('error-modal-message').textContent = message;
        document.getElementById('error-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Success modal functions
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
</script>

<style>
    /* Modal animations */
    #delete-modal, #error-modal, #success-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #delete-modal .bg-white, #error-modal .bg-white, #success-modal .bg-white {
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

    /* Disabled button styling */
    button:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Progress bar animation */
    #application-progress-bar {
        transition: width 0.5s ease-in-out;
    }
</style>
@endsection