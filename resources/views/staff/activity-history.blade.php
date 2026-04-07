@extends('layouts.dashboard')

@section('title', 'Activity History')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Application
                </a>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#155386] to-[#1F363D] flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Activity History</h1>
                    <p class="text-sm text-gray-500 mt-1">Complete timeline of all activities for this application</p>
                </div>
            </div>
        </div>
        
        <!-- Application Info Badge -->
        <div class="mt-4 md:mt-0">
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">Application Number</p>
                <p class="text-sm font-mono font-bold text-[#155386]" id="application-number">Loading...</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1">
                <select id="activity-filter" class="w-full md:w-auto px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#155386]">
                    <option value="all">All Activities</option>
                    <option value="status_updated">Status Updates</option>
                    <option value="note_added">Notes Added</option>
                    <option value="hard_copy_received">Hard Copy Received</option>
                    <option value="application_created">Application Created</option>
                    <option value="missing_documents_requested">Missing Documents Requested</option>
                </select>
            </div>
            <div class="text-sm text-gray-500">
                <span id="total-count">0</span> activities found
            </div>
            <button onclick="refreshActivities()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="text-center py-12">
        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600 mt-2">Loading activity history...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mt-4">Failed to Load Activity History</h3>
        <p class="text-gray-500 mt-2">Unable to load activity history at this time.</p>
        <button onclick="location.reload()" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
            Try Again
        </button>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Activity History</h3>
        <p class="text-gray-500 mb-4">No activities have been recorded for this application yet.</p>
        <a href="/staff/applications" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
            Back to Applications
        </a>
    </div>

    <!-- Timeline -->
    <div id="activity-content" class="hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div id="activity-timeline" class="divide-y divide-gray-100">
                <!-- Activities will be loaded here -->
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
</div>

<script>
    const applicationId = {{ $applicationId ?? 'null' }};
    let allActivities = [];
    let filteredActivities = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let currentApplication = null;

    document.addEventListener('DOMContentLoaded', function() {
        if (!applicationId) {
            window.location.href = '/staff/applications';
            return;
        }
        loadFullActivityHistory();
        loadApplicationDetails();
        
        document.getElementById('activity-filter').addEventListener('change', filterActivities);
    });

    async function loadApplicationDetails() {
        try {
            const response = await fetch(`/staff/applications/${applicationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentApplication = data.data;
                    document.getElementById('application-number').textContent = currentApplication.application_number || 'N/A';
                }
            }
        } catch (error) {
            console.error('Error loading application details:', error);
        }
    }

    async function loadFullActivityHistory() {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('activity-content').classList.add('hidden');
        document.getElementById('error-state').classList.add('hidden');
        document.getElementById('empty-state').classList.add('hidden');
        
        try {
            const response = await fetch(`/staff/applications/${applicationId}/review-activities`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities && data.activities.length > 0) {
                    allActivities = data.activities;
                    filterActivities();
                    document.getElementById('loading-state').classList.add('hidden');
                    document.getElementById('activity-content').classList.remove('hidden');
                } else {
                    showEmptyState();
                }
            } else {
                console.error('Response not OK:', response.status);
                showErrorState();
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            showErrorState();
        }
    }

    function filterActivities() {
        const filter = document.getElementById('activity-filter').value;
        
        if (filter === 'all') {
            filteredActivities = [...allActivities];
        } else {
            filteredActivities = allActivities.filter(a => a.action === filter);
        }
        
        document.getElementById('total-count').textContent = filteredActivities.length;
        currentPage = 1;
        displayActivities();
    }

    function displayActivities() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageActivities = filteredActivities.slice(start, end);
        const totalPages = Math.ceil(filteredActivities.length / itemsPerPage);
        
        if (pageActivities.length === 0) {
            document.getElementById('activity-timeline').innerHTML = `
                <div class="text-center py-12">
                    <p class="text-gray-500">No activities found</p>
                </div>
            `;
            document.getElementById('pagination').classList.add('hidden');
            return;
        }
        
        document.getElementById('pagination').classList.remove('hidden');
        
        let html = '';
        pageActivities.forEach(activity => {
            const date = new Date(activity.created_at);
            const timeAgo = getTimeAgo(date);
            
            // Determine icon and color based on action (matching application details page)
            let iconColor = 'bg-blue-100';
            let iconTextColor = 'text-blue-600';
            let iconSvg = '';
            
            if (activity.action === 'status_updated') {
                if (activity.new_status === 'approved') {
                    iconColor = 'bg-green-100';
                    iconTextColor = 'text-green-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                } else if (activity.new_status === 'rejected') {
                    iconColor = 'bg-red-100';
                    iconTextColor = 'text-red-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                } else if (activity.new_status === 'document-verification') {
                    iconColor = 'bg-purple-100';
                    iconTextColor = 'text-purple-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />';
                } else if (activity.new_status === 'under-review') {
                    iconColor = 'bg-yellow-100';
                    iconTextColor = 'text-yellow-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                } else if (activity.new_status === 'for-release') {
                    iconColor = 'bg-blue-100';
                    iconTextColor = 'text-blue-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />';
                } else {
                    iconColor = 'bg-purple-100';
                    iconTextColor = 'text-purple-600';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                }
            } else if (activity.action === 'note_added') {
                iconColor = 'bg-yellow-100';
                iconTextColor = 'text-yellow-600';
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />';
            } else if (activity.action === 'hard_copy_received') {
                iconColor = 'bg-indigo-100';
                iconTextColor = 'text-indigo-600';
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />';
            } else if (activity.action === 'application_created') {
                iconColor = 'bg-emerald-100';
                iconTextColor = 'text-emerald-600';
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />';
            } else if (activity.action === 'missing_documents_requested') {
                iconColor = 'bg-yellow-100';
                iconTextColor = 'text-yellow-600';
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            } else {
                iconColor = 'bg-gray-100';
                iconTextColor = 'text-gray-600';
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            }
            
            // Format action display
            let actionDisplay = activity.action_display || activity.action;
            if (activity.action === 'status_updated') {
                if (activity.old_status && activity.new_status) {
                    actionDisplay = `Status changed from ${formatStatus(activity.old_status)} to ${formatStatus(activity.new_status)}`;
                } else {
                    actionDisplay = 'Status updated';
                }
            } else {
                actionDisplay = actionDisplay.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            }
            
            const reviewerName = activity.reviewer_name || activity.reviewer?.name || 'System';
            const reviewerRole = activity.reviewer_role || activity.reviewer?.role || '';
            
            // Format remarks/details
            let detailsHtml = '';
            if (activity.remarks && activity.action !== 'missing_documents_requested') {
                detailsHtml = `
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Details:</p>
                        <p class="text-sm text-gray-700">${escapeHtml(activity.remarks)}</p>
                    </div>
                `;
            }
            
            // Format missing documents list
            if (activity.action === 'missing_documents_requested' && activity.missing_documents) {
                const docs = Array.isArray(activity.missing_documents) ? activity.missing_documents : [];
                if (docs.length > 0) {
                    detailsHtml = `
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-2">Missing Documents Requested:</p>
                            <div class="flex flex-wrap gap-2">
                                ${docs.map(doc => `<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">${escapeHtml(doc)}</span>`).join('')}
                            </div>
                        </div>
                    `;
                }
            }
            
            html += `
                <div class="flex gap-3 p-4 hover:bg-gray-50 transition animate-fade-in">
                    <div class="w-8 h-8 ${iconColor} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 ${iconTextColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-medium text-gray-800">${escapeHtml(actionDisplay)}</p>
                            <span class="text-xs text-gray-400" title="${formatExactDateTime(date)}">${timeAgo}</span>
                        </div>
                        ${detailsHtml}
                        <p class="text-xs text-gray-500 mt-1">
                            by <span class="font-medium">${escapeHtml(reviewerName)}</span>
                            ${reviewerRole ? `<span class="text-gray-400">• ${escapeHtml(reviewerRole)}</span>` : ''}
                        </p>
                    </div>
                </div>
            `;
        });
        
        document.getElementById('activity-timeline').innerHTML = html;
        updatePagination(totalPages);
    }

    function updatePagination(totalPages) {
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredActivities.length);
        
        document.getElementById('pagination-info').textContent = 
            `Showing ${start} to ${end} of ${filteredActivities.length} activities`;
        
        const paginationControls = document.getElementById('pagination-controls');
        let controlsHtml = '';
        
        controlsHtml += `
            <button onclick="changePage(${currentPage - 1})" 
                class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === 1 ? 'disabled' : ''}>
                Previous
            </button>
        `;
        
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
        const totalPages = Math.ceil(filteredActivities.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayActivities();
    }

    function refreshActivities() {
        loadFullActivityHistory();
    }

    function showEmptyState() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('empty-state').classList.remove('hidden');
        document.getElementById('activity-content').classList.add('hidden');
        document.getElementById('error-state').classList.add('hidden');
    }

    function showErrorState() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
        document.getElementById('activity-content').classList.add('hidden');
        document.getElementById('empty-state').classList.add('hidden');
    }

    function formatExactDateTime(date) {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
        });
    }

    function getTimeAgo(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return diffMins + ' minute' + (diffMins > 1 ? 's' : '') + ' ago';
        if (diffHours < 24) return diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
        if (diffDays < 7) return diffDays + ' day' + (diffDays > 1 ? 's' : '') + ' ago';
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    function formatStatus(status) {
        if (!status) return '';
        return status.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

    #pagination-controls button[disabled] {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .group:hover {
        background-color: #f9fafb;
    }
</style>
@endsection