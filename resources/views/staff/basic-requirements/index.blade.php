{{-- resources/views/staff/basic-requirements/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Basic Requirements Review')

@section('content')
@php
    // Get the authenticated user and their position
    $authUser = Auth::user();
    $userPosition = null;
    $canViewDocuments = false;
    $canModify = false;
    
    // Get position from user profile
    if ($authUser && $authUser->userProfile) {
        $userPosition = $authUser->userProfile->position;
    }
    
    // Fallback to direct DB query
    if (!$userPosition && $authUser) {
        $userPosition = DB::table('user_profiles')
            ->where('user_id', $authUser->id)
            ->value('position');
    }
    
    // Define permissions based on position
    $allowedPositions = ['assessor', 'treasurer', 'engineer', 'architect', 'BFP', 'cpdo', 'administrative_aide'];
    $canViewDocuments = in_array($userPosition, $allowedPositions);
    $canModify = in_array($userPosition, ['assessor', 'treasurer']);
    
    // Determine which documents this user can verify
    $canVerifyTCT = ($userPosition === 'assessor');
    $canVerifyTaxDocs = ($userPosition === 'treasurer');
    $canViewSPA = in_array($userPosition, ['assessor', 'treasurer']);
@endphp

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
                    @if($userPosition)
                        <p class="text-xs text-blue-600 mt-1">Logged in as: {{ ucfirst($userPosition) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
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
            
            <select id="status-filter" name="status" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
            </select>
            
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Apply Filters
            </button>
            
            <a href="{{ route('staff.basic-requirements.index') }}" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </a>
            
            @if($canModify)
            <button onclick="exportBasicRequirements()" class="px-6 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#0d3a5c] transition font-medium text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </button>
            @endif
        </div>
    </div>

    <!-- Requirements Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Submitted</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Applicant</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Application #</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Documents</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
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
                        <td class="py-4 px-6">
                            @if($canViewDocuments)
                                <button onclick="viewDocuments({{ $req->id }})" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View & Review Documents
                                </button>
                            @else
                                <span class="text-gray-400 text-sm italic">Access Restricted</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            @if($req->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>
                            @elseif($req->status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
                        <h3 class="text-xl font-bold text-white">Review Documents</h3>
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
                            </div>
                        </div>
                        
                        <!-- Document Review Section -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Document Verification
                            </h4>
                            <div class="space-y-3" id="property-docs">
                                <!-- Dynamic content with checkboxes -->
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
                        
                        <!-- Auto-approve info & Reject Button (only for users who can modify) -->
                        @if($canModify)
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="bg-blue-50 rounded-lg p-3 mb-4">
                                <div class="flex items-center gap-2 text-sm text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Once all documents are checked, the requirements will be automatically approved.</span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button onclick="showRejectModalFromDocs()" 
                                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject Requirements
                                </button>
                            </div>
                        </div>
                        @endif
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
// Get permissions from PHP
let currentUserPosition = '{{ $userPosition }}';
let canModify = {{ $canModify ? 'true' : 'false' }};
let canVerifyTCT = {{ $canVerifyTCT ? 'true' : 'false' }};
let canVerifyTaxDocs = {{ $canVerifyTaxDocs ? 'true' : 'false' }};
let canViewSPA = {{ $canViewSPA ? 'true' : 'false' }};

let documentCheckStatus = {
    tct_checked: false,
    tax_declaration_checked: false,
    tax_receipt_checked: false
};

console.log('User Position:', currentUserPosition);
console.log('Can Modify:', canModify);
console.log('Can Verify TCT:', canVerifyTCT);
console.log('Can Verify Tax Docs:', canVerifyTaxDocs);
console.log('Can View SPA:', canViewSPA);

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

document.getElementById('search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

async function viewDocuments(id) {
    currentRequirementId = id;
    documentCheckStatus = {
        tct_checked: false,
        tax_declaration_checked: false,
        tax_receipt_checked: false
    };
    
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
    document.getElementById('doc-applicant-name').textContent = `${data.user.first_name} ${data.user.last_name}`;
    document.getElementById('doc-applicant-email').textContent = data.user.email;
    document.getElementById('doc-application-number').textContent = data.application_number || 'Pending';
    document.getElementById('doc-submitted-date').textContent = new Date(data.submitted_at).toLocaleString();
    
    documentCheckStatus = {
        tct_checked: data.tct_checked || false,
        tax_declaration_checked: data.tax_declaration_checked || false,
        tax_receipt_checked: data.tax_receipt_checked || false
    };
    
    // Define document permissions based on user position
    const propertyDocs = [
        { 
            id: 'tct', 
            name: 'TCT / Deed of Sale', 
            link: data.tct_link, 
            canCheck: canVerifyTCT,
            canView: true,
            isChecked: documentCheckStatus.tct_checked,
            requiredPosition: 'Assessor',
            icon: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102'
        },
        { 
            id: 'tax_declaration', 
            name: 'Tax Declaration', 
            link: data.tax_declaration_link, 
            canCheck: canVerifyTaxDocs,
            canView: true,
            isChecked: documentCheckStatus.tax_declaration_checked,
            requiredPosition: 'Treasurer',
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        },
        { 
            id: 'tax_receipt', 
            name: 'Current Tax Receipt', 
            link: data.current_tax_receipt_link, 
            canCheck: canVerifyTaxDocs,
            canView: true,
            isChecked: documentCheckStatus.tax_receipt_checked,
            requiredPosition: 'Treasurer',
            icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
        },
        { 
            id: 'spa', 
            name: 'Special Power of Attorney (SPA)', 
            link: data.spa_link, 
            canCheck: false,
            canView: canViewSPA,
            isChecked: false,
            requiredPosition: 'Assessor or Treasurer',
            icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
        }
    ];
    
    const propertyContainer = document.getElementById('property-docs');
    propertyContainer.innerHTML = propertyDocs.map(doc => {
        // If user can't view this document at all, don't show it
        if (!doc.canView) {
            return '';
        }
        
        // Determine if checkbox is disabled
        const isDisabled = !doc.canCheck || !doc.link || !canModify;
        
        // Show checked status even if user can't modify (disabled checkbox or checkmark)
        let statusHtml = '';
        if (doc.isChecked && !doc.canCheck) {
            // Show that it's already checked by someone else
            statusHtml = `<div class="w-5 h-5 text-green-500 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                         </div>`;
        } else if (doc.canCheck && canModify) {
            statusHtml = `<input type="checkbox" id="check-${doc.id}" class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500 cursor-pointer" 
                            onchange="updateDocumentCheck('${doc.id}', this.checked, ${currentRequirementId})"
                            ${doc.isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}>`;
        } else {
            statusHtml = `<div class="w-5 h-5 ${doc.isChecked ? 'text-green-500' : 'text-gray-300'} flex items-center justify-center">
                            ${doc.isChecked ? 
                                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>' : 
                                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                            }
                         </div>`;
        }
        
        const disabledClass = isDisabled ? 'opacity-50' : '';
        const tooltip = !doc.canCheck && doc.requiredPosition && doc.link ? 
            `title="Only ${doc.requiredPosition} can verify this document. Current status: ${doc.isChecked ? 'Verified' : 'Pending verification'}"` : '';
        
        // Only show view link if user can view the document
        const viewLink = doc.link && doc.canView ? 
            `<a href="${doc.link}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                View Document
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>` : 
            '<span class="text-sm text-gray-400 italic">Not provided</span>';
        
        return `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition ${disabledClass}" ${tooltip}>
                <div class="flex items-center gap-3">
                    ${statusHtml}
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${doc.icon}" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">${doc.name}</span>
                    ${!doc.canCheck && doc.requiredPosition && doc.link ? 
                        `<span class="text-xs text-gray-400 ml-2">(Verified by ${doc.requiredPosition})</span>` : 
                        (!doc.canCheck && doc.requiredPosition ? 
                            `<span class="text-xs text-gray-400 ml-2">(Requires ${doc.requiredPosition})</span>` : '')}
                </div>
                ${viewLink}
            </div>
        `;
    }).join('');
    
    const rejectionSection = document.getElementById('rejection-reason-section');
    if (data.status === 'rejected' && data.rejection_reason) {
        document.getElementById('rejection-reason-text').textContent = data.rejection_reason;
        rejectionSection.classList.remove('hidden');
    } else {
        rejectionSection.classList.add('hidden');
    }
}

async function updateDocumentCheck(documentType, isChecked, requirementId) {
    if (!canModify) {
        showError('You do not have permission to modify document verification status.');
        return;
    }
    
    if (documentType === 'tct') documentCheckStatus.tct_checked = isChecked;
    else if (documentType === 'tax_declaration') documentCheckStatus.tax_declaration_checked = isChecked;
    else if (documentType === 'tax_receipt') documentCheckStatus.tax_receipt_checked = isChecked;
    
    try {
        const response = await fetch(`/staff/basic-requirements/${requirementId}/update-check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                document_type: documentType,
                checked: isChecked
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            
            if (data.auto_approved) {
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else if (data.all_verified) {
                showSuccess('All documents verified! Auto-approving...');
            }
        } else {
            showError(data.message || 'Failed to update check status');
            const checkbox = document.getElementById(`check-${documentType}`);
            if (checkbox) checkbox.checked = !isChecked;
            if (documentType === 'tct') documentCheckStatus.tct_checked = !isChecked;
            else if (documentType === 'tax_declaration') documentCheckStatus.tax_declaration_checked = !isChecked;
            else if (documentType === 'tax_receipt') documentCheckStatus.tax_receipt_checked = !isChecked;
        }
    } catch (error) {
        console.error('Error updating check status:', error);
        showError('An error occurred. Please try again.');
        const checkbox = document.getElementById(`check-${documentType}`);
        if (checkbox) checkbox.checked = !isChecked;
        if (documentType === 'tct') documentCheckStatus.tct_checked = !isChecked;
        else if (documentType === 'tax_declaration') documentCheckStatus.tax_declaration_checked = !isChecked;
        else if (documentType === 'tax_receipt') documentCheckStatus.tax_receipt_checked = !isChecked;
    }
}

function showRejectModalFromDocs() {
    if (!canModify) {
        showError('You do not have permission to reject requirements.');
        return;
    }
    closeDocumentsModal();
    document.getElementById('reject-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentsModal() {
    const modal = document.getElementById('documents-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
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

document.addEventListener('click', function(e) {
    const rejectModal = document.getElementById('reject-modal');
    const docsModal = document.getElementById('documents-modal');
    
    if (e.target === rejectModal) {
        closeRejectModal();
    }
    if (e.target === docsModal) {
        closeDocumentsModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeDocumentsModal();
    }
});

function exportBasicRequirements() {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    
    let url = '{{ route("staff.basic-requirements.export") }}';
    const params = [];
    
    if (search) params.push(`search=${encodeURIComponent(search)}`);
    if (status && status !== 'pending') params.push(`status=${status}`);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
    
    #reject-modal, #documents-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #reject-modal .bg-white, #documents-modal .bg-white {
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
    
    input[type="checkbox"] {
        cursor: pointer;
    }
    
    input[type="checkbox"]:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
</style>
@endsection