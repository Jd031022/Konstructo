{{-- resources/views/staff/basic-requirements/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Basic Requirements Review')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-[#155386] to-[#1F363D] flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Basic Requirements Review</h1>
                    <p class="text-sm text-gray-500 mt-1">Review and approve applicant basic requirements</p>
                </div>
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
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name or email..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
            </div>
            
            <!-- Status Filter -->
            <select id="status-filter" name="status" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
            </select>
            
            <!-- Filter Button -->
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Apply Filters
            </button>
            
            <!-- Reset Button -->
            <a href="{{ route('staff.basic-requirements.index') }}" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </a>
        </div>
    </div>

    <!-- Requirements Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    办法
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Submitted</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Applicant</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Application #</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Owner Status</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Documents</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($requirements as $req)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $req->submitted_at->format('M d, Y h:i A') }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($req->user->first_name, 0, 1) . substr($req->user->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-800">{{ $req->user->first_name }} {{ $req->user->last_name }}</span>
                                    <p class="text-xs text-gray-500">{{ $req->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @if($req->application && $req->application->application_number)
                                <span class="font-mono text-sm text-gray-600">{{ $req->application->application_number }}</span>
                            @else
                                <span class="text-sm text-gray-400 italic">Pending</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            @if($req->is_owner)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Owner</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Authorized Rep</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <button onclick="viewDocuments({{ $req->id }})" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Documents
                            </button>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            @if($req->status === 'pending')
                            <div class="flex gap-2">
                                <button onclick="showApproveModal({{ $req->id }})" 
                                        class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                    Approve
                                </button>
                                <button onclick="showRejectModal({{ $req->id }})" 
                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    Reject
                                </button>
                            </div>
                            @elseif($req->status === 'approved')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>
                            @elseif($req->status === 'rejected')
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2">No requirements found</p>
                            <p class="text-sm text-gray-400">Try adjusting your filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requirements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-gray-500">
                Showing {{ $requirements->firstItem() ?? 0 }} to {{ $requirements->lastItem() ?? 0 }} of {{ $requirements->total() }} results
            </p>
            <div class="flex items-center gap-2">
                {{ $requirements->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- View Documents Modal -->
<div id="documents-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-xl font-bold text-white">Submitted Documents</h3>
                    </div>
                    <button onclick="closeDocumentsModal()" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <!-- Loading State -->
                    <div id="docs-loading" class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-600 mt-2">Loading documents...</p>
                    </div>
                    
                    <!-- Documents Content -->
                    <div id="docs-content" class="hidden">
                        <!-- Applicant Info -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-gray-800 mb-2">Applicant Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium text-gray-800" id="doc-applicant-name"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium text-gray-800" id="doc-applicant-email"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Application Number</p>
                                    <p class="font-medium text-gray-800 font-mono" id="doc-application-number"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Submitted Date</p>
                                    <p class="font-medium text-gray-800" id="doc-submitted-date"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Owner Status</p>
                                    <p class="font-medium text-gray-800" id="doc-owner-status"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Proof of Ownership Documents -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Proof of Ownership
                            </h4>
                            <div class="space-y-3" id="ownership-docs">
                                <!-- Dynamic content -->
                            </div>
                        </div>
                        
                        <!-- Authorization Documents (Conditional) -->
                        <div id="auth-docs-section" class="mb-6 hidden">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Authorization Documents
                            </h4>
                            <div class="space-y-3" id="auth-docs">
                                <!-- Dynamic content -->
                            </div>
                        </div>
                        
                        <!-- Rejection Reason (if rejected) -->
                        <div id="rejection-reason-section" class="mb-6 hidden">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Rejection Reason
                            </h4>
                            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                <p id="rejection-reason-text" class="text-sm text-gray-700"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeDocumentsModal()" 
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold">Approve Requirements</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Are you sure you want to approve these basic requirements? The applicant will be able to proceed to Step 1.</p>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Optional Notes</label>
                        <textarea id="approve-notes" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Add any notes or comments..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeApproveModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                            Cancel
                        </button>
                        <button onclick="confirmApprove()" id="confirm-approve-btn"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                            <span id="approve-btn-text">Approve</span>
                            <span id="approve-btn-spinner" class="hidden">
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

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold">Reject Requirements</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-2">Please provide a reason for rejection:</p>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                        <textarea id="rejection-reason" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Explain why the requirements are being rejected..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                        <textarea id="reject-notes" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Add any additional notes..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeRejectModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                            Cancel
                        </button>
                        <button onclick="confirmReject()" id="confirm-reject-btn"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 text-sm">
                            <span id="reject-btn-text">Reject</span>
                            <span id="reject-btn-spinner" class="hidden">
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

<!-- Success Message Toast -->
<div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden animate-slide-in">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span id="success-message"></span>
    </div>
</div>

<!-- Error Message Toast -->
<div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden animate-slide-in">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span id="error-message"></span>
    </div>
</div>

<script>
let currentRequirementId = null;
let currentDocuments = null;

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    
    let url = '{{ route("staff.basic-requirements.index") }}';
    const params = [];
    
    if (search) params.push(`search=${encodeURIComponent(search)}`);
    if (status && status !== 'pending') params.push(`status=${status}`);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

// Allow Enter key to trigger search
document.getElementById('search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

async function viewDocuments(id) {
    currentRequirementId = id;
    
    // Show modal with loading state
    const modal = document.getElementById('documents-modal');
    const loadingDiv = document.getElementById('docs-loading');
    const contentDiv = document.getElementById('docs-content');
    
    modal.classList.remove('hidden');
    loadingDiv.classList.remove('hidden');
    contentDiv.classList.add('hidden');
    document.body.style.overflow = 'hidden';
    
    try {
        const response = await fetch(`/staff/basic-requirements/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentDocuments = data.data;
            renderDocuments(data.data);
            loadingDiv.classList.add('hidden');
            contentDiv.classList.remove('hidden');
        } else {
            showError(data.message || 'Failed to load documents');
            closeDocumentsModal();
        }
    } catch (error) {
        console.error('Error loading documents:', error);
        showError('Failed to load documents');
        closeDocumentsModal();
    }
}

function renderDocuments(data) {
    // Applicant Info
    document.getElementById('doc-applicant-name').textContent = `${data.user.first_name} ${data.user.last_name}`;
    document.getElementById('doc-applicant-email').textContent = data.user.email;
    document.getElementById('doc-application-number').textContent = data.application_number || 'Pending';
    document.getElementById('doc-submitted-date').textContent = new Date(data.submitted_at).toLocaleString();
    document.getElementById('doc-owner-status').innerHTML = data.is_owner 
        ? '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Owner</span>'
        : '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Authorized Representative</span>';
    
    // Proof of Ownership Documents
    const ownershipDocs = [
        { name: 'Transfer Certificate of Title (TCT)', link: data.tct_link, icon: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102' },
        { name: 'Tax Declaration', link: data.tax_declaration_link, icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { name: 'Current Tax Receipt', link: data.current_tax_receipt_link, icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' }
    ];
    
    const ownershipContainer = document.getElementById('ownership-docs');
    ownershipContainer.innerHTML = ownershipDocs.map(doc => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${doc.icon}" />
                </svg>
                <span class="text-sm font-medium text-gray-700">${doc.name}</span>
            </div>
            ${doc.link ? 
                `<a href="${doc.link}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                    View Document
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>` : 
                '<span class="text-sm text-gray-400 italic">Not provided</span>'
            }
        </div>
    `).join('');
    
    // Authorization Documents (if not owner)
    const authSection = document.getElementById('auth-docs-section');
    const authContainer = document.getElementById('auth-docs');
    
    if (!data.is_owner) {
        const authDocs = [
            { name: 'Notarized Deed of Sale', link: data.deed_of_sale_link, icon: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2' },
            { name: 'Special Power of Attorney (SPA)', link: data.spa_link, icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' }
        ];
        
        authContainer.innerHTML = authDocs.map(doc => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${doc.icon}" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">${doc.name}</span>
                </div>
                ${doc.link ? 
                    `<a href="${doc.link}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                        View Document
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>` : 
                    '<span class="text-sm text-gray-400 italic">Not provided</span>'
                }
            </div>
        `).join('');
        
        authSection.classList.remove('hidden');
    } else {
        authSection.classList.add('hidden');
    }
    
    // Rejection Reason (if rejected)
    const rejectionSection = document.getElementById('rejection-reason-section');
    if (data.status === 'rejected' && data.rejection_reason) {
        document.getElementById('rejection-reason-text').textContent = data.rejection_reason;
        rejectionSection.classList.remove('hidden');
    } else {
        rejectionSection.classList.add('hidden');
    }
}

function closeDocumentsModal() {
    const modal = document.getElementById('documents-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showApproveModal(id) {
    currentRequirementId = id;
    document.getElementById('approve-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
    document.getElementById('approve-notes').value = '';
    document.body.style.overflow = 'auto';
}

async function confirmApprove() {
    const btn = document.getElementById('confirm-approve-btn');
    const btnText = document.getElementById('approve-btn-text');
    const spinner = document.getElementById('approve-btn-spinner');
    const notes = document.getElementById('approve-notes').value;
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        const response = await fetch(`/staff/basic-requirements/${currentRequirementId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes: notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showError(data.message || 'Failed to approve');
            btnText.classList.remove('hidden');
            spinner.classList.add('hidden');
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
        btn.disabled = false;
    }
}

function showRejectModal(id) {
    currentRequirementId = id;
    document.getElementById('reject-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    document.getElementById('rejection-reason').value = '';
    document.getElementById('reject-notes').value = '';
    document.body.style.overflow = 'auto';
}

async function confirmReject() {
    const reason = document.getElementById('rejection-reason').value.trim();
    if (!reason) {
        showError('Please provide a rejection reason');
        return;
    }
    
    const btn = document.getElementById('confirm-reject-btn');
    const btnText = document.getElementById('reject-btn-text');
    const spinner = document.getElementById('reject-btn-spinner');
    const notes = document.getElementById('reject-notes').value;
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        const response = await fetch(`/staff/basic-requirements/${currentRequirementId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                rejection_reason: reason,
                notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showError(data.message || 'Failed to reject');
            btnText.classList.remove('hidden');
            spinner.classList.add('hidden');
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        showError('An error occurred. Please try again.');
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
        btn.disabled = false;
    }
}

function showSuccess(message) {
    const toast = document.getElementById('success-toast');
    const messageSpan = document.getElementById('success-message');
    messageSpan.textContent = message;
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

function showError(message) {
    const toast = document.getElementById('error-toast');
    const messageSpan = document.getElementById('error-message');
    messageSpan.textContent = message;
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const approveModal = document.getElementById('approve-modal');
    const rejectModal = document.getElementById('reject-modal');
    const docsModal = document.getElementById('documents-modal');
    
    if (e.target === approveModal) {
        closeApproveModal();
    }
    if (e.target === rejectModal) {
        closeRejectModal();
    }
    if (e.target === docsModal) {
        closeDocumentsModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
        closeDocumentsModal();
    }
});
</script>

<style>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Modal animations */
    #approve-modal, #reject-modal, #documents-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #approve-modal .bg-white, #reject-modal .bg-white, #documents-modal .bg-white {
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
    
    /* Custom scrollbar */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 10px;
    }
    
    /* Pagination styling */
    .pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .pagination .page-item {
        list-style: none;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        color: #6b7280;
        background: white;
        transition: all 0.2s;
    }
    
    .pagination .page-link:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    
    .pagination .active .page-link {
        background: #155386;
        color: white;
        border-color: #155386;
    }
    
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection