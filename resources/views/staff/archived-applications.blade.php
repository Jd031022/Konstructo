@extends('layouts.dashboard')

@section('title', 'Archived Applications')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Archived Applications</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage archived building permit applications</p>
        </div>
        
        <!-- Stats Summary -->
        <div class="mt-4 md:mt-0 flex gap-3">
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500">Total Archived</p>
                <p class="text-xl font-bold text-gray-800" id="total-archived">0</p>
            </div>
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500">This Month</p>
                <p class="text-xl font-bold text-gray-800" id="archived-this-month">0</p>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       id="search-input"
                       placeholder="Search by application number, applicant name, or address..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
            </div>
            
            <!-- Date Filter -->
            <select id="filter-date" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">Last 7 Days</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
            
            <!-- Application Type Filter -->
            <select id="filter-type" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="all">All Types</option>
                <option value="building_permit">Building Permit</option>
                <option value="occupancy_permit">Occupancy Permit</option>
                <option value="zoning_permit">Zoning Permit</option>
            </select>
            
            <!-- Filter Button -->
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Apply Filters
            </button>
            
            <!-- Reset Button -->
            <button onclick="resetFilters()" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                Reset
            </button>
            
            <!-- Restore Selected Button -->
            <button id="restore-selected" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Restore Selected
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading archived applications...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No archived applications found</h3>
        <p class="text-gray-500 mb-4">There are no archived applications matching your criteria.</p>
        <button onclick="resetFilters()" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
            Clear Filters
        </button>
    </div>

    <!-- Applications Table -->
    <div id="applications-table-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-[#155386] focus:ring-[#155386]">
                        </th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Application #</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Applicant</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Archived Date</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Archived By</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
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

<!-- Restore Confirmation Modal -->
<div id="restore-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <h3 class="text-xl font-bold">Restore Application</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6" id="restore-modal-message">Are you sure you want to restore this application? It will be moved back to active applications.</p>
                    
                    <div class="flex justify-end gap-3">
                        <button onclick="closeRestoreModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                            Cancel
                        </button>
                        <button onclick="confirmRestore()" id="confirm-restore-btn"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                            <span id="restore-btn-text">Restore</span>
                            <span id="restore-btn-spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-sm">
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
</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-sm">
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
</div>

<script>
// Application state
let currentPage = 1;
let totalPages = 1;
let totalItems = 0;
let selectedApplications = new Set();
let restoreId = null;
let isRestoringMultiple = false;

// Status configuration
const APPLICATION_TYPES = {
    'building_permit': { label: 'Building Permit', color: 'bg-blue-100 text-blue-800' },
    'occupancy_permit': { label: 'Occupancy Permit', color: 'bg-green-100 text-green-800' },
    'zoning_permit': { label: 'Zoning Permit', color: 'bg-purple-100 text-purple-800' }
};

// Load applications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadArchivedApplications();
    setupEventListeners();
    setupModals();
});

function setupEventListeners() {
    // Search with debounce
    const searchInput = document.getElementById('search-input');
    let debounceTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentPage = 1;
            loadArchivedApplications();
        }, 500);
    });
    
    // Filter changes
    document.getElementById('filter-date').addEventListener('change', () => {
        currentPage = 1;
        loadArchivedApplications();
    });
    
    document.getElementById('filter-type').addEventListener('change', () => {
        currentPage = 1;
        loadArchivedApplications();
    });
    
    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function(e) {
        if (e.target.checked) {
            selectAllApplications();
        } else {
            clearSelection();
        }
    });
    
    // Restore selected button
    document.getElementById('restore-selected').addEventListener('click', () => {
        if (selectedApplications.size === 0) {
            showErrorModal('Please select at least one application to restore');
            return;
        }
        openRestoreModal(null, true);
    });
}

// Apply filters
function applyFilters() {
    currentPage = 1;
    loadArchivedApplications();
}

// Reset filters
function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('filter-date').value = 'all';
    document.getElementById('filter-type').value = 'all';
    currentPage = 1;
    loadArchivedApplications();
}

// Load archived applications from API
async function loadArchivedApplications() {
    const search = document.getElementById('search-input').value;
    const dateFilter = document.getElementById('filter-date').value;
    const typeFilter = document.getElementById('filter-type').value;
    
    // Show loading state
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('applications-table-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    
    try {
        const response = await fetch(`/staff/archived-applications/data?page=${currentPage}&search=${encodeURIComponent(search)}&date=${dateFilter}&type=${typeFilter}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            renderApplicationsTable(data.applications);
            updatePagination(data.pagination);
            updateStats(data.stats);
        } else {
            showErrorModal(data.message || 'Failed to load archived applications');
        }
    } catch (error) {
        console.error('Error loading archived applications:', error);
        showErrorModal('Failed to load archived applications: ' + error.message);
        
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('empty-state').classList.remove('hidden');
        document.getElementById('empty-state').innerHTML = `
            <svg class="w-16 h-16 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Failed to Load Applications</h3>
            <p class="text-gray-500 mt-2">${error.message}</p>
            <button onclick="loadArchivedApplications()" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                Try Again
            </button>
        `;
    } finally {
        document.getElementById('loading-state').classList.add('hidden');
    }
}

function renderApplicationsTable(applications) {
    const tableContainer = document.getElementById('applications-table-container');
    const emptyState = document.getElementById('empty-state');
    const tableBody = document.getElementById('applications-table-body');
    
    if (!applications || applications.length === 0) {
        tableContainer.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }
    
    tableContainer.classList.remove('hidden');
    emptyState.classList.add('hidden');
    
    // Clear selection when rendering new data
    selectedApplications.clear();
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    
    const gradientColors = [
        'from-[#155386] to-[#40798C]',
        'from-[#40798C] to-[#70A9A1]',
        'from-[#70A9A1] to-[#9EC5CB]',
        'from-[#9EC5CB] to-[#B8D8E3]'
    ];
    
    tableBody.innerHTML = applications.map((app, index) => {
        const initials = app.applicant_name ? 
            app.applicant_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 
            'NA';
        
        const randomGradient = gradientColors[index % gradientColors.length];
        const typeConfig = APPLICATION_TYPES[app.application_type] || { label: app.application_type || 'N/A', color: 'bg-gray-100 text-gray-800' };
        
        return `
            <tr class="hover:bg-gray-50 transition">
                <td class="py-4 px-6">
                    <input type="checkbox" class="application-checkbox rounded border-gray-300 text-[#155386] focus:ring-[#155386]" data-id="${app.id}">
                </td>
                <td class="py-4 px-6">
                    <span class="font-mono text-sm font-medium text-[#155386]">${app.application_number || 'N/A'}</span>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-r ${randomGradient} rounded-full flex items-center justify-center text-white text-xs font-bold">
                            ${initials}
                        </div>
                        <div>
                            <span class="font-medium text-gray-800">${app.applicant_name || 'N/A'}</span>
                            <p class="text-xs text-gray-500">${app.applicant_email || ''}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <span class="px-3 py-1 ${typeConfig.color} rounded-full text-xs font-medium whitespace-nowrap">
                        ${typeConfig.label}
                    </span>
                </td>
                <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap">
                    ${formatDate(app.archived_at)}
                </td>
                <td class="py-4 px-6 text-sm text-gray-600">
                    ${app.archived_by_name || 'System'}
                </td>
                <td class="py-4 px-6">
                    <span class="text-sm text-gray-600 line-clamp-2" title="${escapeHtml(app.archive_reason || 'No reason provided')}">
                        ${escapeHtml(app.archive_reason || 'No reason provided')}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <button onclick="openRestoreModal(${app.id})" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" 
                                title="Restore Application">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <a href="/staff/application-details/${app.id}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                           title="View Details">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Attach checkbox event listeners
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const id = parseInt(this.dataset.id);
            if (this.checked) {
                selectedApplications.add(id);
            } else {
                selectedApplications.delete(id);
            }
            updateSelectAllCheckbox();
            updateRestoreButtonState();
        });
    });
    
    updateRestoreButtonState();
}

function updatePagination(pagination) {
    currentPage = pagination.current_page;
    totalPages = pagination.last_page;
    totalItems = pagination.total;
    
    const start = pagination.from || 0;
    const end = pagination.to || 0;
    
    document.getElementById('pagination-info').textContent = 
        `Showing ${start} to ${end} of ${totalItems} archived applications`;
    
    const paginationControls = document.getElementById('pagination-controls');
    let controlsHtml = '';
    
    controlsHtml += `
        <button onclick="changePage(${currentPage - 1})" 
            class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
            ${currentPage === 1 ? 'disabled' : ''}>
            Previous
        </button>
    `;
    
    // Show page numbers
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage + 1 < maxVisible) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    if (startPage > 1) {
        controlsHtml += `<button onclick="changePage(1)" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">1</button>`;
        if (startPage > 2) {
            controlsHtml += `<span class="px-2 text-gray-400">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            controlsHtml += `<button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">${i}</button>`;
        } else {
            controlsHtml += `<button onclick="changePage(${i})" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">${i}</button>`;
        }
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            controlsHtml += `<span class="px-2 text-gray-400">...</span>`;
        }
        controlsHtml += `<button onclick="changePage(${totalPages})" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">${totalPages}</button>`;
    }
    
    controlsHtml += `
        <button onclick="changePage(${currentPage + 1})" 
            class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
            ${currentPage === totalPages ? 'disabled' : ''}>
            Next
        </button>
    `;
    
    paginationControls.innerHTML = controlsHtml;
}

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadArchivedApplications();
}

function updateStats(stats) {
    document.getElementById('total-archived').textContent = stats.total || 0;
    document.getElementById('archived-this-month').textContent = stats.this_month || 0;
}

function selectAllApplications() {
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        const id = parseInt(checkbox.dataset.id);
        selectedApplications.add(id);
    });
    updateRestoreButtonState();
}

function clearSelection() {
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedApplications.clear();
    updateRestoreButtonState();
}

function updateSelectAllCheckbox() {
    const allCheckboxes = document.querySelectorAll('.application-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (allCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        return;
    }
    
    const checkedCount = document.querySelectorAll('.application-checkbox:checked').length;
    
    if (checkedCount === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCount === allCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

function updateRestoreButtonState() {
    const restoreBtn = document.getElementById('restore-selected');
    restoreBtn.disabled = selectedApplications.size === 0;
}

// Restore modal functions
function openRestoreModal(id = null, isMultiple = false) {
    restoreId = id;
    isRestoringMultiple = isMultiple;
    
    const modal = document.getElementById('restore-modal');
    const messageEl = document.getElementById('restore-modal-message');
    
    if (isMultiple) {
        messageEl.textContent = `Are you sure you want to restore ${selectedApplications.size} application(s)? They will be moved back to active applications.`;
    } else {
        messageEl.textContent = 'Are you sure you want to restore this application? It will be moved back to active applications.';
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRestoreModal() {
    document.getElementById('restore-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    restoreId = null;
    isRestoringMultiple = false;
}

async function confirmRestore() {
    const btn = document.getElementById('confirm-restore-btn');
    const btnText = document.getElementById('restore-btn-text');
    const spinner = document.getElementById('restore-btn-spinner');
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        let response;
        
        if (isRestoringMultiple) {
            response = await fetch('/staff/applications/restore-multiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ids: Array.from(selectedApplications)
                })
            });
        } else {
            response = await fetch(`/staff/applications/${restoreId}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
        }
        
        const data = await response.json();
        
        if (data.success) {
            const message = isRestoringMultiple 
                ? `${data.restored_count} application(s) restored successfully!`
                : 'Application restored successfully!';
            showSuccessModal(message);
            closeRestoreModal();
            clearSelection();
            loadArchivedApplications();
        } else {
            showErrorModal(data.message || 'Failed to restore application(s)');
        }
    } catch (error) {
        console.error('Error restoring application(s):', error);
        showErrorModal('An error occurred while restoring the application(s)');
    } finally {
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
        btn.disabled = false;
    }
}

// Modal utility functions
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

// Helper functions
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Setup modal click-outside and escape key handlers
function setupModals() {
    const modals = ['restore-modal', 'error-modal', 'success-modal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modalId === 'restore-modal') closeRestoreModal();
                    if (modalId === 'error-modal') closeErrorModal();
                    if (modalId === 'success-modal') closeSuccessModal();
                }
            });
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRestoreModal();
            closeErrorModal();
            closeSuccessModal();
        }
    });
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Modal animations */
#restore-modal,
#error-modal,
#success-modal {
    transition: opacity 0.2s ease-in-out;
}

#restore-modal .bg-white,
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

/* Custom scrollbar */
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

/* Pagination button styles */
#pagination-controls button[disabled] {
    cursor: not-allowed;
    opacity: 0.5;
}

/* Checkbox styling */
input[type="checkbox"] {
    cursor: pointer;
}
</style>
@endsection