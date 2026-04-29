@extends('layouts.dashboard')

@section('title', 'My Applications')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Applications</h1>
            <p class="text-sm text-gray-500 mt-1">Track and manage your building permit applications</p>
        </div>
        
        <!-- New Application Button -->
        <a href="#" 
           id="new-application-btn"
           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition shadow-md text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Application
        </a>
    </div>

    <!-- Application Limit Status -->
    <div id="application-limit-container" class="mb-8 hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Application Summary</h4>
                        <p class="text-sm text-gray-500">You have <span id="application-count">0</span> out of 3 applications</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                        <div id="application-progress-bar" class="bg-[#155386] h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <span id="remaining-slots" class="text-sm font-medium text-gray-600">3 slots left</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Limit Warning -->
    <div id="limit-warning" class="hidden mb-8">
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg" role="alert">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Application Limit Reached</p>
                    <p class="text-sm">You have reached the maximum limit of 3 applications. Please complete or delete existing applications before creating a new one.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Citizens Charter Aging Legend -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border border-blue-200">
        <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold text-gray-800">Processing Time: 20 Working Days</span>
        </div>
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-gray-600">0-15 days (On Track)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <span class="text-gray-600">16-20 days (Due Soon)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                <span class="text-gray-600">21-25 days (Warning)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                <span class="text-gray-600">26+ days (Overdue)</span>
            </div>
            <div class="border-l border-gray-300 pl-3 ml-1">
                <span class="text-gray-500">Expected completion: <span class="font-semibold text-[#155386]">20 working days</span> from submission</span>
            </div>
        </div>
    </div>

    <!-- Search, Filter, View Toggle and Sort -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           id="search-input"
                           placeholder="Search by application number or project title..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                </div>
                
                <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-lg">
                    <button onclick="setViewMode('card')" id="view-card-btn" class="flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Card</span>
                    </button>
                    <button onclick="setViewMode('list')" id="view-list-btn" class="flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>List</span>
                    </button>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <select id="status-filter" class="flex-1 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending Review</option>
                    <option value="under-review">Under Review</option>
                    <option value="document-verification">Document Verification</option>
                    <option value="approved">Approved</option>
                    <option value="for-release">For Release</option>
                    <option value="verified">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
                
                <select id="aging-filter" class="flex-1 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    <option value="">All Aging Status</option>
                    <option value="ontrack">On Track (0-15 days)</option>
                    <option value="due">Due Soon (16-20 days)</option>
                    <option value="warning">Warning (21-25 days)</option>
                    <option value="overdue">Overdue (26+ days)</option>
                </select>
                
                <select id="sort-filter" class="flex-1 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="aging-asc">Aging (Oldest First)</option>
                    <option value="aging-desc">Aging (Newest First)</option>
                    <option value="status-asc">Status (A-Z)</option>
                    <option value="status-desc">Status (Z-A)</option>
                    <option value="progress-asc">Progress (Low to High)</option>
                    <option value="progress-desc">Progress (High to Low)</option>
                </select>
            </div>
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
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No applications yet</h3>
        <p class="text-gray-500 mb-4">Start your first building permit application today.</p>
        <a href="#" id="empty-state-new-app-btn" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Start New Application
        </a>
    </div>

    <!-- Applications Container -->
    <div id="applications-container" class="hidden"></div>

    <!-- Pagination -->
    <div id="pagination" class="hidden mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p id="pagination-info" class="text-sm text-gray-500"></p>
        <div class="flex items-center gap-2">
            <button id="prev-page" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50 text-sm" disabled>Previous</button>
            <span id="page-numbers" class="flex items-center gap-2"></span>
            <button id="next-page" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50 text-sm" disabled>Next</button>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Application Tips</h4>
                <p class="text-sm text-gray-600">Your application number is generated when you submit your application in Step 4. You will receive it via email.</p>
                <p class="text-sm text-gray-600 mt-1">Processing time is 20 working days from submission based on the Citizens Charter.</p>
            </div>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-sm">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-red-600 text-white"><h3 class="text-xl font-bold">Delete Draft</h3></div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6">Are you sure you want to delete this draft application? This action cannot be undone.</p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">Cancel</button>
                        <button onclick="confirmDelete()" id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">Delete</button>
                    </div>
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
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                    <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
                    <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
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
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
                    <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
                    <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let applications = [];
    let filteredApplications = [];
    let currentPage = 1;
    let currentView = 'card';
    let itemsPerPage = 5;
    let deleteId = null;
    const MAX_APPLICATIONS_PER_DAY = 3;
    const PROCESSING_DAYS = 20;

    // CSRF token helper
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        return token || '{{ csrf_token() }}';
    }

    // Calculate aging days since submission (using working days approximation)
    function calculateAgingDays(submittedAt) {
        const submittedDate = new Date(submittedAt);
        const currentDate = new Date();
        const daysDiff = Math.floor((currentDate - submittedDate) / (1000 * 60 * 60 * 24));
        const workingDaysDiff = Math.floor(daysDiff * 5 / 7);
        return Math.max(0, workingDaysDiff);
    }
    
    // Get aging status based on Citizens Charter (20 working days)
    function getAgingStatus(days) {
        if (days <= 15) return { status: 'ontrack', text: 'On Track', color: 'green', days: days, description: 'Within processing timeframe' };
        if (days <= 20) return { status: 'due', text: 'Due Soon', color: 'yellow', days: days, description: 'Expected completion within 20 working days' };
        if (days <= 25) return { status: 'warning', text: 'Warning', color: 'orange', days: days, description: 'Exceeding expected timeframe' };
        return { status: 'overdue', text: 'Overdue', color: 'red', days: days, description: 'Significantly delayed' };
    }
    
    function getAgingBadge(days) {
        const aging = getAgingStatus(days);
        let badgeClass = 'aging-badge-';
        
        switch(aging.status) {
            case 'ontrack': badgeClass += 'ontrack'; break;
            case 'due': badgeClass += 'due'; break;
            case 'warning': badgeClass += 'warning'; break;
            case 'overdue': badgeClass += 'overdue'; break;
        }
        
        return `<div class="aging-badge ${badgeClass}"><span>${aging.text} (${days} day${days !== 1 ? 's' : ''})</span></div>`;
    }
    
    function getRowClass(days) {
        const aging = getAgingStatus(days);
        return `aging-${aging.status}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupNewApplicationButton();
        loadApplications();
        loadStats();
        checkApplicationLimit();
        setupEventListeners();
        setupModals();
        initViewToggle();
    });

    async function setupNewApplicationButton() {
    const newAppBtn = document.getElementById('new-application-btn');
    const emptyStateBtn = document.getElementById('empty-state-new-app-btn');
    
    const handleClick = async (e) => {
        e.preventDefault();
        
        // First check if user can submit today
        try {
            const limitCheck = await fetch('/applicant/application/limit-info', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            const limitData = await limitCheck.json();
            
            if (!limitData.success || !limitData.data.can_submit_today) {
                const remaining = limitData.data?.remaining || 0;
                showErrorModal(`You have reached the daily limit of ${MAX_APPLICATIONS_PER_DAY} applications. You can submit ${remaining} more today.`);
                return;
            }
        } catch (error) {
            console.error('Error checking limit:', error);
        }
        
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
            } else {
                showErrorModal(data.message || 'Failed to create new application');
            }
        } catch (error) {
            console.error('Error creating draft:', error);
            showErrorModal('An error occurred. Please try again.');
        }
    };
    
    if (newAppBtn) newAppBtn.addEventListener('click', handleClick);
    if (emptyStateBtn) emptyStateBtn.addEventListener('click', handleClick);
}

    function initViewToggle() {
        const savedView = localStorage.getItem('applications_view_mode');
        if (savedView) currentView = savedView;
        updateViewToggleUI();
    }

    function setViewMode(mode) {
        currentView = mode;
        localStorage.setItem('applications_view_mode', mode);
        updateViewToggleUI();
        displayApplications();
    }

    function updateViewToggleUI() {
        const cardBtn = document.getElementById('view-card-btn');
        const listBtn = document.getElementById('view-list-btn');
        if (currentView === 'card') {
            cardBtn.classList.add('bg-white', 'shadow-sm', 'text-[#155386]');
            cardBtn.classList.remove('text-gray-600');
            listBtn.classList.remove('bg-white', 'shadow-sm', 'text-[#155386]');
            listBtn.classList.add('text-gray-600');
        } else {
            listBtn.classList.add('bg-white', 'shadow-sm', 'text-[#155386]');
            listBtn.classList.remove('text-gray-600');
            cardBtn.classList.remove('bg-white', 'shadow-sm', 'text-[#155386]');
            cardBtn.classList.add('text-gray-600');
        }
    }

    async function checkApplicationLimit() {
    try {
        const response = await fetch('/applicant/application/limit-info', {
            headers: { 
                'Accept': 'application/json', 
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            const todaySubmitted = data.data.today_submitted || 0;
            const remaining = data.data.remaining || MAX_APPLICATIONS_PER_DAY;
            const canSubmitToday = data.data.can_submit_today;
            
            // Update progress bar for daily usage
            updateProgressBar(todaySubmitted);
            document.getElementById('application-count').textContent = todaySubmitted;
            document.getElementById('remaining-slots').textContent = `${remaining} slot${remaining !== 1 ? 's' : ''} left today`;
            
            if (todaySubmitted > 0) {
                document.getElementById('application-limit-container').classList.remove('hidden');
            }
            
            // Update container title
            const summaryTitle = document.querySelector('#application-limit-container h4');
            if (summaryTitle) {
                summaryTitle.textContent = 'Today\'s Application Summary';
            }
            
            const containerText = document.querySelector('#application-limit-container .text-sm');
            if (containerText) {
                containerText.innerHTML = `You have submitted <span id="application-count">${todaySubmitted}</span> out of ${MAX_APPLICATIONS_PER_DAY} applications today`;
            }
            
            const limitWarning = document.getElementById('limit-warning');
            const newAppBtn = document.getElementById('new-application-btn');
            
            if (!canSubmitToday) {
                limitWarning.classList.remove('hidden');
                if (newAppBtn) { 
                    newAppBtn.classList.add('opacity-50', 'pointer-events-none'); 
                    newAppBtn.style.pointerEvents = 'none';
                }
                
                // Update warning message with reset info
                const warningText = document.querySelector('#limit-warning .text-sm');
                if (warningText) {
                    warningText.innerHTML = `You have reached the maximum limit of ${MAX_APPLICATIONS_PER_DAY} applications per day. Your limit will reset at midnight (12:00 AM). Please try again tomorrow.`;
                }
            } else {
                limitWarning.classList.add('hidden');
                if (newAppBtn) { 
                    newAppBtn.classList.remove('opacity-50', 'pointer-events-none'); 
                    newAppBtn.style.pointerEvents = 'auto';
                }
            }
        }
    } catch (error) { 
        console.error('Error checking application limit:', error); 
    }
}

// Update progress bar to show daily usage
function updateProgressBar(todaySubmitted) {
    const percentage = (todaySubmitted / MAX_APPLICATIONS_PER_DAY) * 100;
    const progressBar = document.getElementById('application-progress-bar');
    if (progressBar) {
        progressBar.style.width = `${Math.min(percentage, 100)}%`;
        if (percentage >= 100) {
            progressBar.classList.remove('bg-[#155386]');
            progressBar.classList.add('bg-red-500');
        } else if (percentage >= 66) {
            progressBar.classList.remove('bg-[#155386]');
            progressBar.classList.add('bg-yellow-500');
        } else {
            progressBar.classList.remove('bg-red-500', 'bg-yellow-500');
            progressBar.classList.add('bg-[#155386]');
        }
    }
}

    async function loadApplications() {
        try {
            const response = await fetch('/applicant/applications/data', {
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            const data = await response.json();
            if (data.success) {
                applications = data.applications;
                applications = applications.map(app => {
                    if (app.submitted_at && app.status !== 'draft') {
                        const days = calculateAgingDays(app.submitted_at);
                        app.aging_days = days;
                        app.aging_status = getAgingStatus(days);
                    } else {
                        app.aging_days = null;
                        app.aging_status = null;
                    }
                    return app;
                });
                applyFilters();
            } else { showErrorModal(data.message || 'Failed to load applications'); }
        } catch (error) { console.error('Error loading applications:', error); showErrorModal('Failed to load applications'); }
        finally { document.getElementById('loading-state').classList.add('hidden'); }
    }

    async function loadStats() {
        try { 
            await fetch('/applicant/applications/stats', {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            }); 
        } catch (error) { console.error('Error loading stats:', error); }
    }

    function getStatusProgress(status) {
        const progressMap = { 
            'draft': 25, 
            'pending': 40, 
            'under-review': 55, 
            'document-verification': 70, 
            'approved': 85, 
            'for-release': 95, 
            'verified': 100, 
            'rejected': 100 
        };
        return progressMap[status] || 0;
    }

    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const agingFilter = document.getElementById('aging-filter').value;
        const sortFilter = document.getElementById('sort-filter').value;
        
        filteredApplications = [...applications];
        filteredApplications = filteredApplications.filter(app => {
            const matchesSearch = (app.application_number && app.application_number.toLowerCase().includes(searchTerm)) ||
                                  (app.project_title && app.project_title.toLowerCase().includes(searchTerm));
            const matchesStatus = !statusFilter || app.status === statusFilter;
            
            let matchesAging = true;
            if (agingFilter && app.aging_days !== null && app.aging_days !== undefined) {
                const days = app.aging_days;
                switch(agingFilter) {
                    case 'ontrack': matchesAging = days <= 15; break;
                    case 'due': matchesAging = days >= 16 && days <= 20; break;
                    case 'warning': matchesAging = days >= 21 && days <= 25; break;
                    case 'overdue': matchesAging = days >= 26; break;
                }
            } else if (agingFilter && (app.aging_days === null || app.aging_days === undefined)) {
                matchesAging = false;
            }
            
            return matchesSearch && matchesStatus && matchesAging;
        });
        
        filteredApplications.sort((a, b) => {
            const dateA = new Date(a.created_at);
            const dateB = new Date(b.created_at);
            const agingA = a.aging_days || 0;
            const agingB = b.aging_days || 0;
            
            switch(sortFilter) {
                case 'newest': return dateB - dateA;
                case 'oldest': return dateA - dateB;
                case 'aging-asc': return agingB - agingA;
                case 'aging-desc': return agingA - agingB;
                case 'status-asc': return (a.status || '').localeCompare(b.status || '');
                case 'status-desc': return (b.status || '').localeCompare(a.status || '');
                case 'progress-asc': return getStatusProgress(a.status) - getStatusProgress(b.status);
                case 'progress-desc': return getStatusProgress(b.status) - getStatusProgress(a.status);
                default: return dateB - dateA;
            }
        });
        
        currentPage = 1;
        displayApplications();
    }

    function displayApplications() {
        const container = document.getElementById('applications-container');
        const emptyState = document.getElementById('empty-state');
        const pagination = document.getElementById('pagination');
        
        if (filteredApplications.length === 0) {
            container.classList.add('hidden');
            emptyState.classList.remove('hidden');
            pagination.classList.add('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        container.classList.remove('hidden');
        
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedApps = filteredApplications.slice(start, end);
        
        container.innerHTML = currentView === 'card' ? createCardView(paginatedApps) : createListView(paginatedApps);
        updatePagination();
    }

    function createCardView(apps) {
        return `<div class="grid grid-cols-1 gap-4">${apps.map(app => createApplicationCard(app)).join('')}</div>`;
    }

    function createListView(apps) {
        return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Application</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Project Title</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aging</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Progress</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Hard Copy</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">${apps.map(app => createListRow(app)).join('')}</tbody>
                    </table>
                </div>
            </div>
        `;
    }

    function getStatusConfig(status) {
        const config = {
            'draft': { color: 'bg-gray-100 text-gray-600', text: 'Draft', progress: 25 },
            'pending': { color: 'bg-yellow-100 text-yellow-600', text: 'Submitted', progress: 40 },
            'under-review': { color: 'bg-purple-100 text-purple-600', text: 'Under Review', progress: 55 },
            'document-verification': { color: 'bg-indigo-100 text-indigo-600', text: 'Document Verification', progress: 70 },
            'approved': { color: 'bg-green-100 text-green-600', text: 'Approved', progress: 85 },
            'for-release': { color: 'bg-blue-100 text-blue-600', text: 'For Release', progress: 95 },
            'verified': { color: 'bg-emerald-100 text-emerald-600', text: 'Completed', progress: 100 },
            'rejected': { color: 'bg-red-100 text-red-600', text: 'Rejected', progress: 100 }
        };
        return config[status] || { color: 'bg-gray-100 text-gray-600', text: status, progress: 0 };
    }

    function getProgressColor(status) {
        const colors = {
            'draft': 'from-gray-400 to-gray-500',
            'pending': 'from-[#155386] to-[#40798C]',
            'under-review': 'from-purple-500 to-purple-600',
            'document-verification': 'from-indigo-500 to-indigo-600',
            'approved': 'from-green-500 to-green-600',
            'for-release': 'from-blue-500 to-blue-600',
            'verified': 'from-emerald-500 to-emerald-600',
            'rejected': 'from-red-500 to-red-600'
        };
        return colors[status] || 'from-gray-400 to-gray-500';
    }

    function createApplicationCard(app) {
        const statusConfig = getStatusConfig(app.status);
        const progressColor = getProgressColor(app.status);
        
        const date = new Date(app.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        
        const showAging = app.status !== 'draft' && app.aging_days !== null;
        const agingDays = showAging ? (app.aging_days || 0) : 0;
        const rowClass = showAging ? getRowClass(agingDays) : '';
        
        let hardCopyStatus = 'Not Submitted';
        let hardCopyColor = 'bg-gray-100 text-gray-600';
        if (app.hard_copy_received) { hardCopyStatus = 'Received'; hardCopyColor = 'bg-green-100 text-green-600'; }
        else if (app.status !== 'draft' && app.status !== 'rejected') { hardCopyStatus = 'Pending'; hardCopyColor = 'bg-yellow-100 text-yellow-600'; }
        
        const displayNumber = app.application_number || 'Pending';
        const hasNumber = app.application_number !== null;
        const projectTitle = app.project_title || 'Untitled Project';
        
        let actionButton = '';
        if (app.status === 'draft') {
            actionButton = `
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <button onclick="openDeleteModal(${app.id})" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Draft
                    </button>
                    <a href="/applicant/application/step1?id=${app.id}" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Continue Application
                    </a>
                </div>
            `;
        } else {
            actionButton = `<a href="/applicant/application-details/${app.id}" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium">View Details</a>`;
        }
        
        return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition ${rowClass}">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-gradient-to-r ${progressColor} rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                ${hasNumber ? displayNumber.substring(0, 2) : 'BP'}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">${escapeHtml(projectTitle)}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-sm text-gray-500 font-mono">${displayNumber}</p>
                                    ${hasNumber ? '<span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">Application #</span>' : '<span class="text-xs bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded-full">Pending Number</span>'}
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Created: ${formattedDate}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${showAging ? getAgingBadge(agingDays) : ''}
                            <span class="px-3 py-1 ${statusConfig.color} rounded-full text-xs font-medium whitespace-nowrap">${statusConfig.text}</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-600">Application Progress</span>
                            <span class="${app.status === 'verified' ? 'text-green-600' : (app.status === 'rejected' ? 'text-red-600' : 'text-[#155386]')} font-medium">${statusConfig.progress}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r ${progressColor} h-2 rounded-full" style="width: ${statusConfig.progress}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-xs ${hardCopyColor} px-2 py-1 rounded-full">Hard Copy: ${hardCopyStatus}</span>
                    </div>
                    
                    ${app.status === 'rejected' && app.rejection_reason ? `
                    <div class="mb-4 p-3 bg-red-50 rounded-lg">
                        <p class="text-xs text-red-600 font-medium mb-1">Rejection Reason</p>
                        <p class="text-sm text-gray-600">${escapeHtml(app.rejection_reason)}</p>
                    </div>
                    ` : ''}
                    
                    <div class="pt-4 border-t border-gray-100">${actionButton}</div>
                </div>
            </div>
        `;
    }

    function createListRow(app) {
        const statusConfig = getStatusConfig(app.status);
        const progressColor = getProgressColor(app.status);
        const date = new Date(app.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        const showAging = app.status !== 'draft' && app.aging_days !== null;
        const agingDays = showAging ? (app.aging_days || 0) : 0;
        const rowClass = showAging ? getRowClass(agingDays) : '';
        
        let hardCopyStatus = 'Not Submitted';
        let hardCopyColor = 'bg-gray-100 text-gray-600';
        if (app.hard_copy_received) { hardCopyStatus = 'Received'; hardCopyColor = 'bg-green-100 text-green-600'; }
        else if (app.status !== 'draft' && app.status !== 'rejected') { hardCopyStatus = 'Pending'; hardCopyColor = 'bg-yellow-100 text-yellow-600'; }
        
        const displayNumber = app.application_number || 'Pending';
        const hasNumber = app.application_number !== null;
        const projectTitle = app.project_title || 'Untitled Project';
        
        let actionButton = '';
        if (app.status === 'draft') {
            actionButton = `
                <div class="flex gap-2">
                    <button onclick="openDeleteModal(${app.id})" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-medium">Delete</button>
                    <a href="/applicant/application/step1?id=${app.id}" class="px-3 py-2 bg-[#155386] text-white rounded-lg text-xs font-medium">Continue</a>
                </div>
            `;
        } else {
            actionButton = `<a href="/applicant/application-details/${app.id}" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium">View Details</a>`;
        }
        
        return `
            <tr class="hover:bg-gray-50 transition ${rowClass}">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r ${progressColor} rounded-full flex items-center justify-center text-white text-sm">${hasNumber ? displayNumber.substring(0, 2) : 'BP'}</div>
                        <div><p class="font-medium text-gray-800">${displayNumber}</p><p class="text-xs text-gray-500">${formattedDate}</p></div>
                    </div>
                </td>
                <td class="py-4 px-6"><p class="text-sm font-medium text-gray-800 max-w-xs truncate" title="${escapeHtml(projectTitle)}">${escapeHtml(projectTitle.substring(0, 40))}${projectTitle.length > 40 ? '...' : ''}</p></td>
                <td class="py-4 px-6">${showAging ? getAgingBadge(agingDays) : '<span class="text-xs text-gray-400">N/A</span>'}</td>
                <td class="py-4 px-6"><span class="px-3 py-1 ${statusConfig.color} rounded-full text-xs">${statusConfig.text}</span></td>
                <td class="py-4 px-6"><div class="w-20 bg-gray-200 rounded-full h-2"><div class="bg-gradient-to-r ${progressColor} h-2 rounded-full" style="width: ${statusConfig.progress}%"></div></div></td>
                <td class="py-4 px-6"><span class="px-3 py-1 ${hardCopyColor} rounded-full text-xs">${hardCopyStatus}</span></td>
                <td class="py-4 px-6">${actionButton}</td>
            </tr>
        `;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function openDeleteModal(id) { deleteId = id; document.getElementById('delete-modal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeDeleteModal() { document.getElementById('delete-modal').classList.add('hidden'); document.body.style.overflow = 'auto'; deleteId = null; }

    async function confirmDelete() {
        if (!deleteId) return;
        const btn = document.getElementById('confirm-delete-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Deleting...'; btn.disabled = true;
        try {
            const response = await fetch(`/applicant/applications/${deleteId}`, { 
                method: 'DELETE', 
                headers: { 
                    'X-CSRF-TOKEN': getCsrfToken(), 
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                } 
            });
            const data = await response.json();
            if (data.success) {
                showSuccessModal('Draft deleted successfully');
                closeDeleteModal();
                await loadApplications();
                await checkApplicationLimit();
            } else { showErrorModal(data.message || 'Failed to delete draft'); }
        } catch (error) { 
            console.error('Delete error:', error);
            showErrorModal('Failed to delete draft'); 
        }
        finally { btn.innerHTML = originalText; btn.disabled = false; }
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
        const pagination = document.getElementById('pagination');
        if (filteredApplications.length <= itemsPerPage) { pagination.classList.add('hidden'); return; }
        pagination.classList.remove('hidden');
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredApplications.length);
        document.getElementById('pagination-info').textContent = `Showing ${start} to ${end} of ${filteredApplications.length} applications`;
        let pagesHtml = '';
        for (let i = 1; i <= totalPages; i++) {
            pagesHtml += i === currentPage ? `<button class="w-8 h-8 bg-[#155386] text-white rounded-lg text-sm">${i}</button>` : `<button onclick="goToPage(${i})" class="w-8 h-8 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 text-sm">${i}</button>`;
        }
        document.getElementById('page-numbers').innerHTML = pagesHtml;
        document.getElementById('prev-page').disabled = currentPage === 1;
        document.getElementById('next-page').disabled = currentPage === totalPages;
    }

    function goToPage(page) { currentPage = page; displayApplications(); }
    
    function setupEventListeners() {
        let searchTimeout;
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() { 
                clearTimeout(searchTimeout); 
                searchTimeout = setTimeout(applyFilters, 300); 
            });
        }
        
        const statusFilter = document.getElementById('status-filter');
        const agingFilter = document.getElementById('aging-filter');
        const sortFilter = document.getElementById('sort-filter');
        
        if (statusFilter) statusFilter.addEventListener('change', applyFilters);
        if (agingFilter) agingFilter.addEventListener('change', applyFilters);
        if (sortFilter) sortFilter.addEventListener('change', applyFilters);
        
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => { 
                if (currentPage > 1) { 
                    currentPage--; 
                    displayApplications(); 
                } 
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => { 
                const totalPages = Math.ceil(filteredApplications.length / itemsPerPage); 
                if (currentPage < totalPages) { 
                    currentPage++; 
                    displayApplications(); 
                } 
            });
        }
    }

    function setupModals() {
        const modals = ['delete-modal', 'error-modal', 'success-modal'];
        modals.forEach(id => { 
            const modal = document.getElementById(id); 
            if (modal) { 
                modal.addEventListener('click', e => { 
                    if (e.target === modal) closeModal(id); 
                }); 
            } 
        });
        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') { 
                modals.forEach(id => closeModal(id)); 
            } 
        });
    }
    
    function closeModal(id) { 
        const modal = document.getElementById(id); 
        if (modal) { 
            modal.classList.add('hidden'); 
            document.body.style.overflow = 'auto'; 
        } 
    }

    function showErrorModal(message) { 
        const messageEl = document.getElementById('error-modal-message');
        if (messageEl) messageEl.textContent = message;
        document.getElementById('error-modal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    
    function closeErrorModal() { closeModal('error-modal'); }
    
    function showSuccessModal(message) { 
        const messageEl = document.getElementById('success-modal-message');
        if (messageEl) messageEl.textContent = message;
        document.getElementById('success-modal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
        setTimeout(() => closeSuccessModal(), 3000); 
    }
    
    function closeSuccessModal() { closeModal('success-modal'); }
</script>

<style>
    #delete-modal, #error-modal, #success-modal { transition: opacity 0.2s ease-in-out; }
    #delete-modal .bg-white, #error-modal .bg-white, #success-modal .bg-white { animation: modalSlideIn 0.3s ease-out; }
    @keyframes modalSlideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    button:disabled { cursor: not-allowed; opacity: 0.5; }
    #application-progress-bar { transition: width 0.5s ease-in-out; }
    .pointer-events-none { pointer-events: none; }
    
    /* Aging Styles */
    .aging-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .aging-badge-ontrack { background-color: #dcfce7; color: #166534; }
    .aging-badge-due { background-color: #fef9c3; color: #854d0e; }
    .aging-badge-warning { background-color: #ffedd5; color: #9a3412; }
    .aging-badge-overdue { background-color: #fee2e2; color: #991b1b; animation: pulseRed 1.5s ease-in-out infinite; }
    
    .aging-ontrack { background-color: rgba(34, 197, 94, 0.02); border-left: 3px solid #22c55e; }
    .aging-due { background-color: rgba(234, 179, 8, 0.03); border-left: 3px solid #eab308; }
    .aging-warning { background-color: rgba(249, 115, 22, 0.03); border-left: 3px solid #f97316; }
    .aging-overdue { background-color: rgba(239, 68, 68, 0.03); border-left: 3px solid #ef4444; }
    
    @keyframes pulseRed {
        0%, 100% { background-color: #fee2e2; }
        50% { background-color: #fecaca; }
    }
    
    .max-w-xs { max-width: 200px; }
    .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endsection