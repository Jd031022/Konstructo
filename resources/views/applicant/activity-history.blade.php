@extends('layouts.dashboard')

@section('title', 'Activity History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center text-gray-500 hover:text-[#155386] transition group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Application
        </a>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-r from-[#155386] to-[#40798C] rounded-2xl p-8 mb-8 text-white shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold mb-1">Activity History</h1>
                <p class="text-white/80">Complete timeline of all activities for application <span id="header-application-number" class="font-mono font-semibold"></span></p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1">
                <select id="activity-filter" class="w-full md:w-auto px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#155386]">
                    <option value="all">All Activities</option>
                    <option value="status_updated">Status Updates</option>
                    <option value="note_added">Notes Added</option>
                    <option value="hard_copy_received">Hard Copy Received</option>
                    <option value="application_created">Application Created</option>
                </select>
            </div>
            <div class="text-sm text-gray-500">
                <span id="total-count">0</span> activities found
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div id="activity-timeline" class="space-y-4">
            <!-- Activities will be loaded here -->
            <div class="text-center py-12">
                <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 mt-2">Loading activity history...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
            <button id="prev-page" class="px-4 py-2 text-sm text-gray-500 hover:text-[#155386] disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Previous
            </button>
            <span id="page-info" class="text-sm text-gray-600">Page 1 of 1</span>
            <button id="next-page" class="px-4 py-2 text-sm text-gray-500 hover:text-[#155386] disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Next
            </button>
        </div>
    </div>
</div>

<script>
    const applicationId = {{ $applicationId ?? 'null' }};
    let allActivities = [];
    let filteredActivities = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    document.addEventListener('DOMContentLoaded', function() {
        if (!applicationId) {
            window.location.href = '/applicant/applications';
            return;
        }
        loadFullActivityHistory();
        
        document.getElementById('activity-filter').addEventListener('change', filterActivities);
    });

    async function loadFullActivityHistory() {
        try {
            const response = await fetch(`/applicant/applications/${applicationId}/review-activities`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.activities) {
                    allActivities = data.activities;
                    document.getElementById('header-application-number').textContent = 
                        data.activities[0]?.application_number || '';
                    filterActivities();
                } else {
                    showEmptyState();
                }
            } else {
                showEmptyState();
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            showEmptyState();
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
        const timeline = document.getElementById('activity-timeline');
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageActivities = filteredActivities.slice(start, end);
        
        if (pageActivities.length === 0) {
            showEmptyState();
            return;
        }
        
        let html = '';
        pageActivities.forEach(activity => {
            const date = new Date(activity.created_at);
            const exactDateTime = formatExactDateTime(date);
            const timeAgo = getTimeAgo(date);
            
            // Determine icon based on action
            let iconColor = 'bg-blue-100';
            let iconTextColor = 'text-blue-600';
            let iconSvg = getActionIcon(activity.action, activity.new_status);
            
            const reviewerName = activity.reviewer ? activity.reviewer.name : 'System';
            const reviewerRole = activity.reviewer ? activity.reviewer.role : '';
            
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
            
            html += `
                <div class="flex gap-4 p-4 hover:bg-gray-50 rounded-xl transition">
                    <div class="w-10 h-10 ${iconColor} rounded-full flex items-center justify-center flex-shrink-0">
                        ${iconSvg}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-gray-800">${actionDisplay}</p>
                            <span class="text-xs text-gray-400" title="${exactDateTime}">${timeAgo}</span>
                        </div>
                        ${activity.remarks ? `<p class="text-sm text-gray-600 mt-1">${activity.remarks}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-2">
                            <span class="font-medium">${reviewerName}</span>
                            ${reviewerRole ? `<span class="text-gray-400"> • ${reviewerRole}</span>` : ''}
                        </p>
                    </div>
                </div>
            `;
        });
        
        timeline.innerHTML = html;
        updatePagination();
    }

    function getActionIcon(action, status) {
        if (action === 'status_updated') {
            if (status === 'approved') {
                return `<svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>`;
            } else if (status === 'rejected') {
                return `<svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
            }
            return `<svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>`;
        }
        
        if (action === 'note_added') {
            return `<svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>`;
        }
        
        if (action === 'hard_copy_received') {
            return `<svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>`;
        }
        
        if (action === 'application_created') {
            return `<svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>`;
        }
        
        return `<svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>`;
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredActivities.length / itemsPerPage);
        document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
        
        document.getElementById('prev-page').disabled = currentPage === 1;
        document.getElementById('next-page').disabled = currentPage === totalPages || totalPages === 0;
    }

    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            displayActivities();
        }
    });

    document.getElementById('next-page').addEventListener('click', () => {
        const totalPages = Math.ceil(filteredActivities.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            displayActivities();
        }
    });

    function showEmptyState() {
        document.getElementById('activity-timeline').innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Activities Found</h3>
                <p class="text-gray-500">There are no activities recorded for this application yet.</p>
            </div>
        `;
        document.getElementById('pagination').classList.add('hidden');
    }

    // Reuse helper functions from your main page
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
</script>
@endsection