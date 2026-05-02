@extends('layouts.dashboard')

@section('title', 'Staff Settings')
@section('content')
@php
    $currentTab = $currentTab ?? 'system-logs';
@endphp

<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
            <p class="text-sm text-gray-500 mt-1">View system logs and application review activity.</p>
        </div>

        <div class="relative inline-block">
            <button onclick="toggleExportDropdown()" 
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Logs
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                <a href="{{ route('staff.logs.export', array_merge(request()->query(), ['format' => 'csv', 'type' => $currentTab === 'application-logs' ? 'application' : 'system', 'tab' => $currentTab])) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                    <svg class="inline w-4 h-4 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export as Excel
                </a>
                <a href="{{ route('staff.logs.export', array_merge(request()->query(), ['format' => 'html', 'type' => $currentTab === 'application-logs' ? 'application' : 'system', 'tab' => $currentTab])) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                    <svg class="inline w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export as PDF
                </a>
            </div>
        </div>
    </div>

    <div class="border-b border-gray-200 mb-8">
        <nav class="flex space-x-8 overflow-x-auto pb-1">
            <button onclick="window.location.href='{{ route('staff.settings', ['tab' => 'system-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'system-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                System Logs
            </button>
            <button onclick="window.location.href='{{ route('staff.settings', ['tab' => 'application-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'application-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                Application Review Logs
            </button>
        </nav>
    </div>

    @if($currentTab == 'system-logs')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('staff.settings') }}" id="filter-form">
                <input type="hidden" name="tab" value="system-logs">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search logs by user, action, or IP address..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    </div>

                    <select name="action" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">All Actions</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                        <option value="export" {{ request('action') == 'export' ? 'selected' : '' }}>Export</option>
                        <option value="settings" {{ request('action') == 'settings' ? 'selected' : '' }}>Settings Change</option>
                    </select>
                    
                    <select name="date_range" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">Date Range</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                    
                    <button type="submit" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Apply Filters
                    </button>
                    
                    <a href="{{ route('staff.settings', ['tab' => 'system-logs']) }}" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($systemLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ $log->user ? strtoupper(substr($log->user->first_name, 0, 1) . substr($log->user->last_name, 0, 1)) : 'UN' }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">
                                        {{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->user ? $log->user->username : 'N/A' }}</td>
                            <td class="py-4 px-6">
                                @php
                                    $actionColors = [
                                        'login' => 'bg-green-100 text-green-600',
                                        'logout' => 'bg-gray-100 text-gray-600',
                                        'create' => 'bg-blue-100 text-blue-600',
                                        'update' => 'bg-yellow-100 text-yellow-600',
                                        'delete' => 'bg-red-100 text-red-600',
                                        'export' => 'bg-purple-100 text-purple-600',
                                        'settings' => 'bg-orange-100 text-orange-600',
                                    ];
                                    $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="px-3 py-1 {{ $colorClass }} rounded-full text-xs font-medium capitalize whitespace-nowrap">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-600">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-700" title="{{ $log->created_at->format('F j, Y g:i:s A') }}">
                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 {{ $log->status == 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full text-xs font-medium capitalize whitespace-nowrap">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 mb-2">No system logs found</p>
                                <p class="text-sm text-gray-500">Try adjusting your filters or check back later</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $systemLogs->withQueryString()->links() }}
            </div>
        </div>
    @endif

    @if($currentTab == 'application-logs')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('staff.settings') }}" id="filter-form">
                <input type="hidden" name="tab" value="application-logs">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search logs by reviewer, application, or remarks..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    </div>

                    <select name="action" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">All Actions</option>
                        <option value="document_verified" {{ request('action') == 'document_verified' ? 'selected' : '' }}>Document Verified</option>
                        <option value="document_rejected" {{ request('action') == 'document_rejected' ? 'selected' : '' }}>Document Rejected</option>
                        <option value="status_updated" {{ request('action') == 'status_updated' ? 'selected' : '' }}>Status Updated</option>
                    </select>
                    
                    <select name="date_range" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">Date Range</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                    
                    <button type="submit" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Apply Filters
                    </button>
                    
                    <a href="{{ route('staff.settings', ['tab' => 'application-logs']) }}" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Reviewer</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Application</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Old Status</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">New Status</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Remarks</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($applicationLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->reviewer?->first_name ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->application?->application_number ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $log->action)) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->old_status ?? '—' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->new_status ?? '—' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-600">{{ $log->remarks ?? '—' }}</td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-700" title="{{ $log->created_at->format('F j, Y g:i:s A') }}">
                                        {{ $log->created_at->format('M d, Y h:i A') }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm text-gray-600">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 mb-2">No application review logs found</p>
                                <p class="text-sm text-gray-500">Try adjusting your filters or check back later.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $applicationLogs->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>

<script>
function toggleExportDropdown() {
    const dropdown = document.getElementById('export-dropdown');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('export-dropdown');
    const button = event.target.closest('.relative');
    if (!button && dropdown) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endsection
