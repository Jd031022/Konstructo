{{-- resources/views/staff/basic-requirements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Basic Requirements Review')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Basic Requirements Review</h1>
        <p class="text-gray-600 mt-2">Review and approve applicant basic requirements</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Review</p>
                    <p class="text-2xl font-bold text-yellow-600" id="pending-count">{{ $requirements->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Approved Today</p>
                    <p class="text-2xl font-bold text-green-600" id="approved-today">0</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Rejected Today</p>
                    <p class="text-2xl font-bold text-red-600" id="rejected-today">0</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Submitted</p>
                    <p class="text-2xl font-bold text-blue-600" id="total-submitted">0</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Pending Requirements</h2>
                <div class="flex gap-2">
                    <input type="text" id="search-input" placeholder="Search by name or email..." 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#155386] focus:border-[#155386]">
                    <button onclick="refreshList()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requirements as $req)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $req->submitted_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $req->user->first_name }} {{ $req->user->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $req->user->username }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $req->user->email }}</div>
                            <div>{{ $req->user->phone_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($req->is_owner)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Owner</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Authorized Rep</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <button onclick="viewDocuments({{ $req->id }})" class="text-blue-600 hover:text-blue-800">
                                    View All (5)
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2">No pending requirements to review</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requirements->links() }}
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center px-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Approve Requirements</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to approve these basic requirements? The applicant will be able to proceed to Step 1.</p>
        <textarea id="approve-notes" placeholder="Optional notes..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4" rows="3"></textarea>
        <div class="flex gap-3 justify-end">
            <button onclick="closeApproveModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button onclick="confirmApprove()" id="confirm-approve-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Approve</button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center px-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Requirements</h3>
        <p class="text-gray-600 mb-2">Please provide a reason for rejection:</p>
        <textarea id="rejection-reason" placeholder="Explain why the requirements are being rejected..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4" rows="4" required></textarea>
        <textarea id="reject-notes" placeholder="Additional notes (optional)..." class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4" rows="3"></textarea>
        <div class="flex gap-3 justify-end">
            <button onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            <button onclick="confirmReject()" id="confirm-reject-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Reject</button>
        </div>
    </div>
</div>

<script>
let currentRequirementId = null;

function loadStats() {
    fetch('{{ route("staff.basic-requirements.stats") }}')
        .then(res => res.json())
        .then(data => {
            document.getElementById('pending-count').textContent = data.pending;
            document.getElementById('approved-today').textContent = data.approved_today;
            document.getElementById('rejected-today').textContent = data.rejected_today;
            document.getElementById('total-submitted').textContent = data.total_submitted;
        })
        .catch(err => console.error('Error loading stats:', err));
}

function showApproveModal(id) {
    currentRequirementId = id;
    document.getElementById('approve-modal').classList.remove('hidden');
    document.getElementById('approve-modal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
    document.getElementById('approve-modal').classList.remove('flex');
    document.getElementById('approve-notes').value = '';
    document.body.style.overflow = 'auto';
}

function confirmApprove() {
    const btn = document.getElementById('confirm-approve-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    btn.disabled = true;
    
    const notes = document.getElementById('approve-notes').value;
    
    fetch(`/staff/basic-requirements/${currentRequirementId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showError(data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function showRejectModal(id) {
    currentRequirementId = id;
    document.getElementById('reject-modal').classList.remove('hidden');
    document.getElementById('reject-modal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    document.getElementById('reject-modal').classList.remove('flex');
    document.getElementById('rejection-reason').value = '';
    document.getElementById('reject-notes').value = '';
    document.body.style.overflow = 'auto';
}

function confirmReject() {
    const reason = document.getElementById('rejection-reason').value.trim();
    if (!reason) {
        showError('Please provide a rejection reason');
        return;
    }
    
    const btn = document.getElementById('confirm-reject-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    btn.disabled = true;
    
    const notes = document.getElementById('reject-notes').value;
    
    fetch(`/staff/basic-requirements/${currentRequirementId}/reject`, {
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
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showError(data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function viewDocuments(id) {
    window.open(`/staff/basic-requirements/${id}`, '_blank');
}

function refreshList() {
    window.location.reload();
}

function showSuccess(message) {
    const div = document.createElement('div');
    div.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
    div.innerHTML = `<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg><span>${message}</span></div>`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

function showError(message) {
    const div = document.createElement('div');
    div.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
    div.innerHTML = `<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span>${message}</span></div>`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
}

// Load stats on page load
loadStats();
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
</style>
@endsection