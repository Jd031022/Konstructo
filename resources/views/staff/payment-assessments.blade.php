@extends('layouts.dashboard')

@section('title', 'Payment Assessments - Treasurer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Payment Assessments</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and review all building permit fee assessments</p>
        </div>
        
        <!-- Export Button - Simple without dropdown -->
        <div class="mt-4 md:mt-0">
            <button id="exportBtn" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </button>
        </div>
    </div>

    <!-- Stats Cards with Dashboard-style loading animation -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="statsContainer">
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search - FIXED: properly handles search input -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       id="searchInput"
                       placeholder="Search by application number or applicant name..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
            </div>
        
            <!-- Date Range Filter -->
            <select id="dateFilter" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="">All Dates</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="year">This Year</option>
            </select>
            
            <!-- Payment Status Filter -->
            <select id="paymentStatusFilter" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="">All Payment Status</option>
                <option value="paid">Paid (OR Uploaded)</option>
                <option value="unpaid">Unpaid</option>
                <option value="no_assessment">No Assessment</option>
            </select>
            
            <!-- Filter Button -->
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Apply Filters
            </button>
            
            <!-- Reset Button -->
            <button onclick="resetFilters()" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                Reset
            </button>
        </div>
        
        <!-- Status Legend -->
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-4">
            <span class="text-xs text-gray-500">Payment Status Legend:</span>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                <span class="text-xs text-gray-600">OR uploaded</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unpaid</span>
                <span class="text-xs text-gray-600">No OR uploaded yet</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Assessment</span>
                <span class="text-xs text-gray-600">Assessment not completed</span>
            </div>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Building Permit Fee</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">CPDO Fee</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Assessment</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">OR Link</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="applicationsTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#155386]"></div>
                            </div>
                            <p class="mt-2">Loading assessments...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="px-6 py-4 border-t border-gray-200 bg-gray-50 hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div id="paginationInfo" class="text-sm text-gray-600"></div>
                <div id="paginationButtons" class="flex gap-2"></div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Summary Card -->
        <div class="bg-white rounded-xl shadow-sm p-6" id="paymentSummaryCard">
            <div class="animate-pulse">
                <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                <div class="space-y-3">
                    <div class="h-8 bg-gray-200 rounded"></div>
                    <div class="h-8 bg-gray-200 rounded"></div>
                    <div class="h-8 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Collection Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Monthly Collections
            </h3>
            <div id="monthlyChart" class="space-y-3">
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-2 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
        </div>

        <!-- Recently Added Order Numbers -->
        <div class="bg-white rounded-xl shadow-sm p-6" id="recentOrdersContainer">
            <div class="animate-pulse">
                <div class="h-5 bg-gray-200 rounded w-40 mb-4"></div>
                <div class="space-y-3">
                    <div class="h-12 bg-gray-200 rounded"></div>
                    <div class="h-12 bg-gray-200 rounded"></div>
                    <div class="h-12 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Assessment Breakdown Modal -->
<div id="assessmentModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Fee Breakdown</h3>
                    <p id="modalAppNumber" class="text-sm opacity-90 mt-1">Application #: -</p>
                </div>
                <button onclick="closeAssessmentModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div id="buildingPermitBreakdown" class="border rounded-lg p-4">
                        <div class="text-center animate-pulse">Loading...</div>
                    </div>
                    <div id="cpdoBreakdown" class="border rounded-lg p-4">
                        <div class="text-center animate-pulse">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Order Modal - UPDATED: only accessible if assessment is complete -->
<div id="paymentOrderModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Add Payment Order Number</h3>
                    <p class="text-sm opacity-90 mt-1">Record payment transaction reference</p>
                </div>
                <button onclick="closePaymentOrderModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form id="paymentOrderForm" onsubmit="submitPaymentOrder(event)">
                    <input type="hidden" id="modal_application_id" value="">
                    
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Application Number:</span>
                            <span id="modalAppNumberDisplay" class="text-sm font-mono font-semibold text-[#155386]">-</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Applicant:</span>
                            <span id="modalApplicantName" class="text-sm font-medium text-gray-800">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Total Assessment:</span>
                            <span id="modalTotalAssessment" class="text-sm font-bold text-green-600">-</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Order Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="orderNumber" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="e.g., PO-2024-001234" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="paymentDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="paymentNotes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" placeholder="Additional notes..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closePaymentOrderModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">Cancel</button>
                        <button type="submit" id="submitOrderBtn" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm font-medium">Save Order Number</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Orders Modal -->
<div id="viewOrdersModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Payment Orders</h3>
                    <p id="ordersAppNumber" class="text-sm opacity-90 mt-1">Application #: -</p>
                </div>
                <button onclick="closeViewOrdersModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div id="ordersList" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center text-gray-500">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
            <p id="successMessage" class="text-sm text-gray-600 mb-6">Order number has been added successfully.</p>
            <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
            <p id="errorMessage" class="text-sm text-gray-600 mb-6">An error occurred. Please try again.</p>
            <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let perPage = 15;
let searchTerm = '';
let dateFilter = '';
let paymentStatusFilter = '';

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadData();
    loadMonthlyChart();
    
    // FIXED: Search filter event listeners with proper debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            searchTerm = e.target.value;
            currentPage = 1;
            loadData();
        }, 500));
    }
    
    const dateFilterEl = document.getElementById('dateFilter');
    if (dateFilterEl) {
        dateFilterEl.addEventListener('change', function(e) {
            dateFilter = e.target.value;
            currentPage = 1;
            loadData();
        });
    }

    const paymentStatusFilterEl = document.getElementById('paymentStatusFilter');
    if (paymentStatusFilterEl) {
        paymentStatusFilterEl.addEventListener('change', function(e) {
            paymentStatusFilter = e.target.value;
            currentPage = 1;
            loadData();
        });
    }
    
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportToCSV);
    }
});

function showLoadingTable() {
    const tbody = document.getElementById('applicationsTableBody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#155386]"></div>
            </div>
            <p class="mt-2">Loading assessments...</p>
        </td></tr>`;
    }
}

function showSuccess(message) {
    const successMsgEl = document.getElementById('successMessage');
    if (successMsgEl) successMsgEl.textContent = message;
    const modal = document.getElementById('successModal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function showError(message) {
    const errorMsgEl = document.getElementById('errorMessage');
    if (errorMsgEl) errorMsgEl.textContent = message;
    const modal = document.getElementById('errorModal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function loadData() {
    showLoadingTable();
    
    // Build URL with all filter parameters
    let url = `/staff/payment-assessments/data?page=${currentPage}&per_page=${perPage}`;
    if (searchTerm) url += `&search=${encodeURIComponent(searchTerm)}`;
    if (dateFilter) url += `&date_filter=${dateFilter}`;
    if (paymentStatusFilter) url += `&payment_status=${paymentStatusFilter}`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderStats(data.stats);
            renderTable(data.applications);
            renderPagination(data.pagination);
            renderPaymentSummary(data.stats);
            renderRecentOrders(data.applications);
        } else {
            showError(data.message || 'Failed to load data');
        }
    })
    .catch(error => {
        console.error('Error loading data:', error);
        showError('Error loading data. Please try again.');
        const tbody = document.getElementById('applicationsTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-8 text-center text-red-500">Error loading data. Please try again.</td></tr>`;
        }
    });
}

function renderStats(stats) {
    const container = document.getElementById('statsContainer');
    if (!container) return;
    
    const totalFormatted = stats.total_assessments ? stats.total_assessments.toLocaleString() : '0';
    const pendingOrCount = stats.pending_or_count || 0;
    const totalAmountFormatted = formatNumber(stats.total_assessment_amount);
    const avgFormatted = formatNumber(stats.average_assessment);
    
    container.innerHTML = `
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Assessments</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">${totalFormatted}</p>
                    <p class="text-xs text-gray-500 mt-2">Total apps with assessment</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-yellow-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending OR Upload</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">${pendingOrCount}</p>
                    <p class="text-xs text-yellow-600 mt-2">Awaiting OR upload</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Collections</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">₱${totalAmountFormatted}</p>
                    <p class="text-xs text-green-600 mt-2">Total assessed amount</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-purple-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Avg Assessment</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">₱${avgFormatted}</p>
                    <p class="text-xs text-gray-500 mt-2">Per application</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    `;
}

function renderTable(applications) {
    const tbody = document.getElementById('applicationsTableBody');
    
    if (!tbody) return;
    
    if (!applications || applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No applications found.</td></tr>';
        return;
    }
    
    tbody.innerHTML = applications.map(app => {
        const statusBadge = getStatusBadge(app.payment_status);
        const buildingFee = formatNumber(app.building_permit_fee);
        const cpdoFee = formatNumber(app.cpdo_fee);
        const total = formatNumber(app.total_assessment);
        
        // Check if assessment is complete (both building permit fee and cpdo fee are not null and total > 0)
        const isAssessmentComplete = app.building_permit_fee !== null && app.cpdo_fee !== null && app.total_assessment > 0;
        
        let orLinkHtml = '<span class="text-xs text-gray-400">No OR uploaded</span>';
        if (app.or_link) {
            orLinkHtml = `<a href="${escapeHtml(app.or_link)}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View OR
            </a>`;
        }
        
        // Conditionally enable/disable the Add Order Number button based on assessment completion
        const addOrderButton = isAssessmentComplete 
            ? `<button onclick="openPaymentOrderModal(${app.id}, '${escapeHtml(app.application_number)}', '${escapeHtml(app.applicant_name)}', ${app.total_assessment})" class="text-[#155386] hover:text-[#1F363D]" title="Add Order Number">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
               </button>`
            : `<button disabled class="text-gray-300 cursor-not-allowed" title="Assessment must be completed before adding payment">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
               </button>`;
        
        return `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-mono text-sm font-medium text-[#155386]">${escapeHtml(app.application_number)}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(app.applicant_name)}</div>
                    <div class="text-xs text-gray-500">${escapeHtml(app.applicant_email || '')}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-medium text-gray-900">₱${buildingFee}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-medium text-gray-900">₱${cpdoFee}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-bold text-gray-900">₱${total}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    ${orLinkHtml}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    ${statusBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewAssessment(${app.id}, '${escapeHtml(app.application_number)}')" class="text-blue-600 hover:text-blue-800" title="View Fees">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </button>
                        <button onclick="viewPaymentOrders(${app.id}, '${escapeHtml(app.application_number)}', ${JSON.stringify(app.payment_orders).replace(/"/g, '&quot;')})" class="text-green-600 hover:text-green-800" title="View Orders">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </button>
                        ${addOrderButton}
                    </div>
                </td>
             </tr>
        `;
    }).join('');
}

function getStatusBadge(status) {
    switch(status) {
        case 'paid':
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid (OR Uploaded)</span>';
        case 'unpaid':
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Unpaid</span>';
        default:
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Assessment</span>';
    }
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    const infoDiv = document.getElementById('paginationInfo');
    const buttonsDiv = document.getElementById('paginationButtons');
    
    if (!container || !infoDiv || !buttonsDiv) return;
    
    if (!pagination || pagination.total <= pagination.per_page) {
        container.classList.add('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    
    infoDiv.innerHTML = `Showing <span class="font-medium">${pagination.from || 0}</span> to <span class="font-medium">${pagination.to || 0}</span> of <span class="font-medium">${pagination.total}</span> results`;
    
    let buttonsHtml = '';
    
    if (pagination.current_page > 1) {
        buttonsHtml += `<button onclick="goToPage(${pagination.current_page - 1})" class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">Previous</button>`;
    }
    
    const maxButtons = 5;
    let startPage = Math.max(1, pagination.current_page - Math.floor(maxButtons / 2));
    let endPage = Math.min(pagination.last_page, startPage + maxButtons - 1);
    
    if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === pagination.current_page ? 'bg-[#155386] text-white' : 'bg-white text-gray-600 hover:bg-gray-50';
        buttonsHtml += `<button onclick="goToPage(${i})" class="px-3 py-1 border border-gray-300 rounded-lg text-sm ${activeClass} transition">${i}</button>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        buttonsHtml += `<button onclick="goToPage(${pagination.current_page + 1})" class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">Next</button>`;
    }
    
    buttonsDiv.innerHTML = buttonsHtml;
}

function goToPage(page) {
    currentPage = page;
    loadData();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function renderPaymentSummary(stats) {
    const container = document.getElementById('paymentSummaryCard');
    if (!container) return;
    
    const totalAmount = formatNumber(stats.total_assessment_amount);
    const paidAmount = formatNumber(stats.paid_amount || 0);
    const pendingAmount = formatNumber(stats.pending_amount);
    
    container.innerHTML = `
        <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Assessment Summary
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Total Assessment Amount</span>
                <span class="text-sm font-semibold text-gray-800">₱${totalAmount}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Paid (OR Uploaded)</span>
                <span class="text-sm font-semibold text-green-600">₱${paidAmount}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Pending Amount</span>
                <span class="text-sm font-semibold text-yellow-600">₱${pendingAmount}</span>
            </div>
            <div class="flex justify-between items-center pt-2">
                <span class="text-base font-bold text-gray-900">GRAND TOTAL</span>
                <span class="text-xl font-bold text-[#155386]">₱${totalAmount}</span>
            </div>
        </div>
    `;
}

function renderRecentOrders(applications) {
    const container = document.getElementById('recentOrdersContainer');
    if (!container) return;
    
    const recentOrders = [];
    
    applications.forEach(app => {
        if (app.payment_orders && app.payment_orders.length > 0) {
            app.payment_orders.forEach(order => {
                recentOrders.push({
                    application_number: app.application_number,
                    order_number: order.order_number,
                    created_at: order.created_at
                });
            });
        }
    });
    
    recentOrders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    const latestOrders = recentOrders.slice(0, 5);
    
    if (latestOrders.length === 0) {
        container.innerHTML = `
            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Recently Added Order Numbers
            </h3>
            <p class="text-sm text-gray-500 text-center py-4">No payment orders yet.</p>
        `;
        return;
    }
    
    container.innerHTML = `
        <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Recently Added Order Numbers
        </h3>
        <div class="space-y-3 max-h-48 overflow-y-auto">
            ${latestOrders.map(order => `
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${escapeHtml(order.application_number)}</p>
                        <p class="text-xs text-gray-400">Order #: ${escapeHtml(order.order_number)}</p>
                    </div>
                    <span class="text-xs text-gray-500">${formatDate(order.created_at)}</span>
                </div>
            `).join('')}
        </div>
    `;
}

function loadMonthlyChart() {
    fetch('/staff/payment-assessments/monthly-collection', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            const maxAmount = Math.max(...data.data.map(d => d.amount), 1);
            const container = document.getElementById('monthlyChart');
            if (container) {
                container.innerHTML = data.data.map(item => {
                    const percentage = (item.amount / maxAmount) * 100;
                    return `
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>${item.month}</span>
                                <span>₱${formatNumber(item.amount)}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                }).join('');
                container.innerHTML += `<button onclick="window.location.href='/staff/payment-assessments/export'" class="mt-4 w-full text-center text-sm text-[#155386] hover:text-[#1F363D] font-medium">View Detailed Report →</button>`;
            }
        }
    })
    .catch(error => console.error('Error loading chart:', error));
}

function viewAssessment(appId, appNumber) {
    const modalAppNumber = document.getElementById('modalAppNumber');
    if (modalAppNumber) modalAppNumber.textContent = `Application #: ${appNumber}`;
    const modal = document.getElementById('assessmentModal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    const url = `/staff/payment-assessments/data?page=1&per_page=1000&search=&date_filter=`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.applications) {
            const application = data.applications.find(app => app.id == appId);
            if (application) {
                renderBuildingPermitBreakdown(application.assessment_details);
                renderCPDOBreakdown(application.cpdo_assessment_details);
            } else {
                const buildingDiv = document.getElementById('buildingPermitBreakdown');
                if (buildingDiv) buildingDiv.innerHTML = '<div class="text-center text-gray-500 py-4">No building permit assessment found</div>';
                const cpdoDiv = document.getElementById('cpdoBreakdown');
                if (cpdoDiv) cpdoDiv.innerHTML = '<div class="text-center text-gray-500 py-4">No CPDO assessment found</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading assessment:', error);
        const buildingDiv = document.getElementById('buildingPermitBreakdown');
        if (buildingDiv) buildingDiv.innerHTML = '<div class="text-center text-red-500 py-4">Error loading assessment data</div>';
        const cpdoDiv = document.getElementById('cpdoBreakdown');
        if (cpdoDiv) cpdoDiv.innerHTML = '<div class="text-center text-red-500 py-4">Error loading CPDO assessment data</div>';
    });
}

function renderBuildingPermitBreakdown(data) {
    const container = document.getElementById('buildingPermitBreakdown');
    if (!container) return;
    
    if (!data) {
        container.innerHTML = '<div class="text-center text-gray-500 py-4">No building permit assessment found</div>';
        return;
    }
    
    const total = (data.line_grade || 0) + (data.building_fee || 0) + (data.sanitary_fee || 0) + 
                  (data.mechanical_fee || 0) + (data.electrical_fee || 0) + (data.penalties_fines || 0);
    
    let additionalHtml = '';
    if (data.additional_fees && data.additional_fees.length > 0) {
        additionalHtml = '<div class="mt-2 pt-2 border-t border-gray-200"><p class="text-xs font-semibold text-gray-700 mb-1">Additional Fees:</p>';
        data.additional_fees.forEach(fee => {
            additionalHtml += `<div class="flex justify-between text-xs"><span>${escapeHtml(fee.name || 'Additional Fee')}</span><span>₱${formatNumber(fee.amount || 0)}</span></div>`;
        });
        additionalHtml += '</div>';
    }
    
    const assessedByName = data.assessed_by_name ? escapeHtml(data.assessed_by_name) : 'N/A';
    const assessedAt = data.assessed_at ? data.assessed_at : 'N/A';
    
    container.innerHTML = `
        <h4 class="font-semibold text-gray-800 mb-3">Building Permit Assessment</h4>
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Line & Grade Fee:</span>
                <span>₱${formatNumber(data.line_grade || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Building Fee:</span>
                <span>₱${formatNumber(data.building_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Sanitary Fee:</span>
                <span>₱${formatNumber(data.sanitary_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Mechanical Fee:</span>
                <span>₱${formatNumber(data.mechanical_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Electrical Fee:</span>
                <span>₱${formatNumber(data.electrical_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Penalties/Fines:</span>
                <span>₱${formatNumber(data.penalties_fines || 0)}</span>
            </div>
            ${additionalHtml}
            <div class="flex justify-between text-sm font-bold pt-2 border-t border-gray-200">
                <span>Total Building Permit Fee:</span>
                <span class="text-green-600">₱${formatNumber(total)}</span>
            </div>
            ${data.assessment_notes ? `<div class="mt-3 p-2 bg-gray-50 rounded text-xs text-gray-600"><strong>Notes:</strong> ${escapeHtml(data.assessment_notes)}</div>` : ''}
            <div class="mt-2 text-xs text-gray-400">
                <strong>Assessed by:</strong> ${assessedByName}<br>
                <strong>Assessed at:</strong> ${assessedAt}
            </div>
        </div>
    `;
}

function renderCPDOBreakdown(data) {
    const container = document.getElementById('cpdoBreakdown');
    if (!container) return;
    
    if (!data) {
        container.innerHTML = '<div class="text-center text-gray-500 py-4">No CPDO assessment found</div>';
        return;
    }
    
    const total = (data.zonal_location_fee || 0) + (data.palc_fee || 0) + (data.development_permit_fee || 0) + 
                  (data.alteration_permit_fee || 0) + (data.site_zoning_certificate_fee || 0);
    
    let additionalHtml = '';
    if (data.additional_fees && data.additional_fees.length > 0) {
        additionalHtml = '<div class="mt-2 pt-2 border-t border-gray-200"><p class="text-xs font-semibold text-gray-700 mb-1">Additional Fees:</p>';
        data.additional_fees.forEach(fee => {
            additionalHtml += `<div class="flex justify-between text-xs"><span>${escapeHtml(fee.name || 'Additional Fee')}</span><span>₱${formatNumber(fee.amount || 0)}</span></div>`;
        });
        additionalHtml += '</div>';
    }
    
    const assessedByName = data.assessed_by_name ? escapeHtml(data.assessed_by_name) : 'N/A';
    const assessedAt = data.assessed_at ? data.assessed_at : 'N/A';
    
    container.innerHTML = `
        <h4 class="font-semibold text-gray-800 mb-3">CPDO Assessment</h4>
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Zonal Location Fee:</span>
                <span>₱${formatNumber(data.zonal_location_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">PALC Fee:</span>
                <span>₱${formatNumber(data.palc_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Development Permit Fee:</span>
                <span>₱${formatNumber(data.development_permit_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Alteration Permit Fee:</span>
                <span>₱${formatNumber(data.alteration_permit_fee || 0)}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Site Zoning Certificate Fee:</span>
                <span>₱${formatNumber(data.site_zoning_certificate_fee || 0)}</span>
            </div>
            ${additionalHtml}
            <div class="flex justify-between text-sm font-bold pt-2 border-t border-gray-200">
                <span>Total CPDO Fee:</span>
                <span class="text-green-600">₱${formatNumber(total)}</span>
            </div>
            ${data.assessment_notes ? `<div class="mt-3 p-2 bg-gray-50 rounded text-xs text-gray-600"><strong>Notes:</strong> ${escapeHtml(data.assessment_notes)}</div>` : ''}
            <div class="mt-2 text-xs text-gray-400">
                <strong>Assessed by:</strong> ${assessedByName}<br>
                <strong>Assessed at:</strong> ${assessedAt}
            </div>
        </div>
    `;
}

function viewPaymentOrders(appId, appNumber, orders) {
    const ordersAppNumber = document.getElementById('ordersAppNumber');
    if (ordersAppNumber) ordersAppNumber.textContent = `Application #: ${appNumber}`;
    const ordersList = document.getElementById('ordersList');
    
    if (!ordersList) return;
    
    if (!orders || orders.length === 0) {
        ordersList.innerHTML = '<div class="text-center text-gray-500 py-4">No payment orders recorded yet.</div>';
    } else {
        ordersList.innerHTML = orders.map(order => `
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-sm font-semibold text-[#155386]">${escapeHtml(order.order_number)}</p>
                        <p class="text-xs text-gray-500">Date: ${order.payment_date || order.created_at}</p>
                        <p class="text-xs text-gray-500">Recorded by: ${escapeHtml(order.created_by)}</p>
                        ${order.notes ? `<p class="text-xs text-gray-600 mt-1">Notes: ${escapeHtml(order.notes)}</p>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    const modal = document.getElementById('viewOrdersModal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openPaymentOrderModal(appId, appNumber, applicantName, totalAssessment) {
    const appIdInput = document.getElementById('modal_application_id');
    if (appIdInput) appIdInput.value = appId;
    const modalAppNumberDisplay = document.getElementById('modalAppNumberDisplay');
    if (modalAppNumberDisplay) modalAppNumberDisplay.textContent = appNumber;
    const modalApplicantName = document.getElementById('modalApplicantName');
    if (modalApplicantName) modalApplicantName.textContent = applicantName;
    const modalTotalAssessment = document.getElementById('modalTotalAssessment');
    if (modalTotalAssessment) modalTotalAssessment.textContent = `₱${formatNumber(totalAssessment)}`;
    
    const orderNumberInput = document.getElementById('orderNumber');
    if (orderNumberInput) orderNumberInput.value = '';
    const paymentNotes = document.getElementById('paymentNotes');
    if (paymentNotes) paymentNotes.value = '';
    const paymentDate = document.getElementById('paymentDate');
    if (paymentDate) paymentDate.value = new Date().toISOString().split('T')[0];
    
    const modal = document.getElementById('paymentOrderModal');
    if (modal) modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAssessmentModal() {
    const modal = document.getElementById('assessmentModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function closePaymentOrderModal() {
    const modal = document.getElementById('paymentOrderModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function closeViewOrdersModal() {
    const modal = document.getElementById('viewOrdersModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function submitPaymentOrder(event) {
    event.preventDefault();
    
    const appId = document.getElementById('modal_application_id').value;
    const orderNumber = document.getElementById('orderNumber').value;
    const paymentDate = document.getElementById('paymentDate').value;
    const notes = document.getElementById('paymentNotes').value;
    const submitBtn = document.getElementById('submitOrderBtn');
    
    if (!submitBtn) return;
    
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div> Saving...';
    submitBtn.disabled = true;
    
    fetch(`/staff/payment-assessments/${appId}/payment-order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            order_number: orderNumber,
            payment_date: paymentDate,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            closePaymentOrderModal();
            showSuccess(`Order number "${orderNumber}" has been added successfully.`);
            loadData();
            loadMonthlyChart();
        } else {
            showError(data.message || 'Failed to add order number');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        showError('Error adding order number. Please try again.');
    });
}

function formatNumber(value) {
    if (value === undefined || value === null) return '0.00';
    return parseFloat(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const dateFilterEl = document.getElementById('dateFilter');
    const paymentStatusFilterEl = document.getElementById('paymentStatusFilter');
    
    if (searchInput) searchTerm = searchInput.value;
    if (dateFilterEl) dateFilter = dateFilterEl.value;
    if (paymentStatusFilterEl) paymentStatusFilter = paymentStatusFilterEl.value;
    currentPage = 1;
    loadData();
}

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    const dateFilterEl = document.getElementById('dateFilter');
    const paymentStatusFilterEl = document.getElementById('paymentStatusFilter');
    
    if (searchInput) searchInput.value = '';
    if (dateFilterEl) dateFilterEl.value = '';
    if (paymentStatusFilterEl) paymentStatusFilterEl.value = '';
    searchTerm = '';
    dateFilter = '';
    paymentStatusFilter = '';
    currentPage = 1;
    loadData();
}

function exportToCSV() {
    let url = `/staff/payment-assessments/export`;
    const params = [];
    if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
    if (dateFilter) params.push(`date_filter=${dateFilter}`);
    if (paymentStatusFilter) params.push(`payment_status=${paymentStatusFilter}`);
    if (params.length) url += `?${params.join('&')}`;
    window.location.href = url;
}

// Auto-refresh every 60 seconds
setInterval(() => {
    loadData();
    loadMonthlyChart();
}, 60000);
</script>

<style>
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection