@extends('layouts.dashboard')

@section('title', 'Verified Ownership Documents')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="/staff/dashboard" class="text-gray-400 hover:text-[#155386] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Verified Ownership Documents</h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                @php
                    $position = Auth::user()->profile ? Auth::user()->profile->position : null;
                @endphp
                @if($position === 'cpdo')
                    Documents you have verified: <span class="font-medium text-green-600">TCT/Deed of Sale</span>
                @elseif($position === 'assessor')
                    Documents you have verified: <span class="font-medium text-purple-600">Tax Declaration, TCT/Deed of Sale</span>
                @elseif($position === 'treasurer')
                    Documents you have verified: <span class="font-medium text-orange-600">Current Tax Receipt, SPA</span>
                @else
                    No position assigned
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3 mt-2 md:mt-0">
            <!-- Show user's position -->
            @if(Auth::user()->profile && Auth::user()->profile->position)
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#155386] rounded-lg text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', Auth::user()->profile->position)) }}</span>
            </span>
            @endif
            
            <!-- EXPORT BUTTON -->
            <button onclick="exportToCSV()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export to CSV
            </button>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" id="search-input" placeholder="Search by applicant name or application #" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent">
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Document Type</label>
                <select id="document-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <option value="all">All Types</option>
                    <option value="TCT / Deed of Sale">TCT / Deed of Sale</option>
                    <option value="Tax Declaration">Tax Declaration</option>
                    <option value="Current Tax Receipt">Current Tax Receipt</option>
                    <option value="Special Power of Attorney (SPA)">Special Power of Attorney (SPA)</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Date Range</label>
                <select id="date-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <button onclick="resetFilters()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                Reset Filters
            </button>
        </div>
    </div>

    <!-- VERIFIED DOCUMENTS TABLE -->
    <div id="table-container" class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified At</th>
                    </tr>
                </thead>
                <tbody id="verified-table-body" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="animate-spin h-8 w-8 mx-auto text-[#155386] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p>Loading verified documents...</p>
                        </span>
                    </span>
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing <span id="showing-start">0</span> to <span id="showing-end">0</span> of <span id="total-items">0</span> results
            </div>
            <div class="flex gap-2">
                <button onclick="previousPage()" id="prev-btn" disabled class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Previous
                </button>
                <button onclick="nextPage()" id="next-btn" disabled class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- EMPTY STATE -->
    <div id="empty-state" class="hidden text-center py-12 bg-white rounded-xl shadow-sm mt-6">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No verified documents yet</h3>
        <p class="text-gray-500">Documents you verify will appear here.</p>
        <a href="/staff/dashboard" class="inline-flex items-center px-4 py-2 mt-4 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>

<script>
    let allDocuments = [];
    let filteredDocuments = [];
    let currentPage = 1;
    const itemsPerPage = 10;
    let currentUserPosition = null;

    document.addEventListener('DOMContentLoaded', function() {
        fetchUserPosition();
        loadVerifiedDocuments();
        
        // Add event listeners for filters
        document.getElementById('search-input').addEventListener('input', applyFilters);
        document.getElementById('document-type-filter').addEventListener('change', applyFilters);
        document.getElementById('date-filter').addEventListener('change', applyFilters);
    });

    async function fetchUserPosition() {
        try {
            const response = await fetch('/staff/position/check');
            if (response.ok) {
                const data = await response.json();
                currentUserPosition = data.position;
                console.log('User position:', currentUserPosition);
            }
        } catch (error) {
            console.error('Error fetching user position:', error);
        }
    }

    async function loadVerifiedDocuments() {
        try {
            const response = await fetch('/staff/ownership-verifications/verified-data');
            
            if (!response.ok) {
                console.error('Response status:', response.status);
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            console.log('Data received:', data);
            
            if (data.success && data.verifications && data.verifications.length > 0) {
                allDocuments = data.verifications;
                applyFilters();
            } else {
                console.log('No verified documents found');
                showEmptyState();
            }
        } catch (error) {
            console.error('Error loading verified documents:', error);
            showEmptyState();
        }
    }

    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const documentType = document.getElementById('document-type-filter').value;
        const dateRange = document.getElementById('date-filter').value;
        
        filteredDocuments = allDocuments.filter(doc => {
            // Search filter
            if (searchTerm) {
                const applicantName = `${doc.first_name} ${doc.last_name}`.toLowerCase();
                const appNumber = (doc.application_number || '').toLowerCase();
                if (!applicantName.includes(searchTerm) && !appNumber.includes(searchTerm)) {
                    return false;
                }
            }
            
            // Document type filter
            if (documentType !== 'all' && doc.document_type !== documentType) {
                return false;
            }
            
            // Date range filter
            if (dateRange !== 'all') {
                const verifiedDate = new Date(doc.verified_at);
                const today = new Date();
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                startOfWeek.setHours(0, 0, 0, 0);
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const startOfYear = new Date(today.getFullYear(), 0, 1);
                
                switch(dateRange) {
                    case 'today':
                        if (verifiedDate.toDateString() !== today.toDateString()) return false;
                        break;
                    case 'week':
                        if (verifiedDate < startOfWeek) return false;
                        break;
                    case 'month':
                        if (verifiedDate < startOfMonth) return false;
                        break;
                    case 'year':
                        if (verifiedDate < startOfYear) return false;
                        break;
                }
            }
            
            return true;
        });
        
        currentPage = 1;
        renderTable();
        updatePaginationInfo();
    }

    function resetFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('document-type-filter').value = 'all';
        document.getElementById('date-filter').value = 'all';
        applyFilters();
    }

    function renderTable() {
        const tbody = document.getElementById('verified-table-body');
        const tableContainer = document.getElementById('table-container');
        const emptyState = document.getElementById('empty-state');
        
        if (filteredDocuments.length === 0) {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }
        
        tableContainer.classList.remove('hidden');
        emptyState.classList.add('hidden');
        
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageDocuments = filteredDocuments.slice(start, end);
        
        let html = '';
        pageDocuments.forEach(doc => {
            const verifiedDate = new Date(doc.verified_at);
            const formattedDate = verifiedDate.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            let badgeColor = 'bg-green-100 text-green-700';
            let iconColor = 'text-green-600';
            
            if (doc.document_type === 'Tax Declaration') {
                badgeColor = 'bg-purple-100 text-purple-700';
                iconColor = 'text-purple-600';
            } else if (doc.document_type === 'Current Tax Receipt') {
                badgeColor = 'bg-orange-100 text-orange-700';
                iconColor = 'text-orange-600';
            } else if (doc.document_type === 'Special Power of Attorney (SPA)') {
                badgeColor = 'bg-red-100 text-red-700';
                iconColor = 'text-red-600';
            }
            
            html += `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600">${getInitials(doc.first_name, doc.last_name)}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(doc.first_name)} ${escapeHtml(doc.last_name)}</p>
                                <p class="text-xs text-gray-400">${escapeHtml(doc.email || 'No email')}</p>
                            </div>
                        </div>
                    </span>
                    <td class="px-6 py-4">
                        <span class="text-sm font-mono text-gray-600">${escapeHtml(doc.application_number || 'N/A')}</span>
                    </span>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full ${badgeColor}">
                            <svg class="w-3 h-3 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            ${escapeHtml(doc.document_type)}
                        </span>
                    </span>
                    <td class="px-6 py-4">
                        ${doc.document_link ? `
                            <a href="${escapeHtml(doc.document_link)}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                View Document
                            </a>
                        ` : '<span class="text-sm text-gray-400">No link</span>'}
                    </span>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">${escapeHtml(doc.verified_by_name || 'You')}</span>
                        </div>
                    </span>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-500">${formattedDate}</span>
                        </div>
                    </span>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function updatePaginationInfo() {
        const total = filteredDocuments.length;
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, total);
        
        document.getElementById('showing-start').textContent = total > 0 ? start : 0;
        document.getElementById('showing-end').textContent = end;
        document.getElementById('total-items').textContent = total;
        
        document.getElementById('prev-btn').disabled = currentPage === 1;
        document.getElementById('next-btn').disabled = currentPage * itemsPerPage >= total;
    }

    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
            updatePaginationInfo();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextPage() {
        if (currentPage * itemsPerPage < filteredDocuments.length) {
            currentPage++;
            renderTable();
            updatePaginationInfo();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function showEmptyState() {
        const tableContainer = document.getElementById('table-container');
        const emptyState = document.getElementById('empty-state');
        
        tableContainer.classList.add('hidden');
        emptyState.classList.remove('hidden');
    }

    function exportToCSV() {
        if (filteredDocuments.length === 0) {
            alert('No data to export');
            return;
        }
        
        const headers = ['Applicant Name', 'Email', 'Application Number', 'Document Type', 'Document Link', 'Verified By', 'Verified At'];
        const rows = filteredDocuments.map(doc => [
            `${doc.first_name} ${doc.last_name}`,
            doc.email || '',
            doc.application_number || 'N/A',
            doc.document_type,
            doc.document_link || '',
            doc.verified_by_name || 'You',
            new Date(doc.verified_at).toLocaleString()
        ]);
        
        const csvContent = [headers, ...rows].map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
        const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute('download', `verified_documents_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function getInitials(firstName, lastName) {
        const first = firstName ? firstName.charAt(0).toUpperCase() : '';
        const last = lastName ? lastName.charAt(0).toUpperCase() : '';
        return first + last;
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
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
</style>
@endsection