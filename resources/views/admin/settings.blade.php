@extends('layouts.dashboard')

@section('title', 'Settings')
@section('content')
@php
    // Set default value for currentTab if not set
    $currentTab = $currentTab ?? 'system-logs';
@endphp
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage system configuration and view activity logs</p>
        </div>
        
        <!-- Export Logs Button -->
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.logs.export') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Logs
            </a>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="flex space-x-8 overflow-x-auto pb-1">
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'system-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'system-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                System Logs
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'application-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'application-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                Application Review Logs
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'general']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'general' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                General Settings
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'roles']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'roles' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                User Roles
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'security']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'security' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                Security
            </button>
        </nav>
    </div>

    <!-- System Logs Tab -->
    @if($currentTab == 'system-logs')
        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('admin.settings') }}" id="filter-form">
                <input type="hidden" name="tab" value="system-logs">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
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
                
                    <!-- Action Filter -->
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
                    
                    <!-- Date Range Filter -->
                    <select name="date_range" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">Date Range</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                    
                    <!-- Filter Button -->
                    <button type="submit" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Apply Filters
                    </button>
                    
                    <!-- Clear Filters -->
                    <a href="{{ route('admin.settings', ['tab' => 'system-logs']) }}" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- System Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Name
                                    @if(request('sort') == 'name')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'username', 'direction' => request('sort') == 'username' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Username
                                    @if(request('sort') == 'username')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'action', 'direction' => request('sort') == 'action' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Action
                                    @if(request('sort') == 'action')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'ip_address', 'direction' => request('sort') == 'ip_address' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    IP Address
                                    @if(request('sort') == 'ip_address')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Date & Time
                                    @if(request('sort') == 'created_at')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'system-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Status
                                    @if(request('sort') == 'status')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
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

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Showing {{ $systemLogs->firstItem() ?? 0 }} to {{ $systemLogs->lastItem() ?? 0 }} of {{ $systemLogs->total() }} log entries
                </p>
                <div class="flex items-center gap-2">
                    @if($systemLogs->onFirstPage())
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm" disabled>Previous</button>
                    @else
                        <a href="{{ $systemLogs->previousPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">Previous</a>
                    @endif
                    
                    @php
                        $start = max(1, $systemLogs->currentPage() - 2);
                        $end = min($systemLogs->lastPage(), $systemLogs->currentPage() + 2);
                    @endphp
                    
                    @if($start > 1)
                        <a href="{{ $systemLogs->url(1) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">1</a>
                        @if($start > 2)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                    @endif
                    
                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $systemLogs->currentPage())
                            <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">{{ $page }}</button>
                        @else
                            <a href="{{ $systemLogs->url($page) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">{{ $page }}</a>
                        @endif
                    @endfor
                    
                    @if($end < $systemLogs->lastPage())
                        @if($end < $systemLogs->lastPage() - 1)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                        <a href="{{ $systemLogs->url($systemLogs->lastPage()) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">{{ $systemLogs->lastPage() }}</a>
                    @endif
                    
                    @if($systemLogs->hasMorePages())
                        <a href="{{ $systemLogs->nextPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">Next</a>
                    @else
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Application Review Logs Tab -->
    @if($currentTab == 'application-logs')
        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('admin.settings') }}" id="filter-form">
                <input type="hidden" name="tab" value="application-logs">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by application number, reviewer, or remarks..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                    </div>
                
                    <!-- Action Filter -->
                    <select name="action" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[180px]">
                        <option value="">All Actions</option>
                        <option value="document_verified" {{ request('action') == 'document_verified' ? 'selected' : '' }}>Document Verified</option>
                        <option value="document_rejected" {{ request('action') == 'document_rejected' ? 'selected' : '' }}>Document Rejected</option>
                        <option value="status_updated" {{ request('action') == 'status_updated' ? 'selected' : '' }}>Status Updated</option>
                    </select>
                    
                    <!-- Date Range Filter -->
                    <select name="date_range" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                        <option value="">Date Range</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                    
                    <!-- Filter Button -->
                    <button type="submit" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Apply Filters
                    </button>
                    
                    <!-- Clear Filters -->
                    <a href="{{ route('admin.settings', ['tab' => 'application-logs']) }}" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Application Review Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'reviewer', 'direction' => request('sort') == 'reviewer' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Reviewer
                                    @if(request('sort') == 'reviewer')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'application', 'direction' => request('sort') == 'application' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Application #
                                    @if(request('sort') == 'application')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'action', 'direction' => request('sort') == 'action' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Action
                                    @if(request('sort') == 'action')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Change</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Remarks</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1">
                                    Date & Time
                                    @if(request('sort') == 'created_at')
                                        <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($applicationLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ $log->reviewer ? strtoupper(substr($log->reviewer->first_name, 0, 1) . substr($log->reviewer->last_name, 0, 1)) : 'UN' }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer' }}
                                        </span>
                                        <p class="text-xs text-gray-500">
                                            {{ $log->reviewer ? $log->reviewer->email : '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <a href="/admin/applications/{{ $log->application_id }}" class="text-[#155386] hover:underline font-mono text-sm">
                                    {{ $log->application->application_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $actionColors = [
                                        'document_verified' => 'bg-green-100 text-green-600',
                                        'document_rejected' => 'bg-red-100 text-red-600',
                                        'status_updated' => 'bg-blue-100 text-blue-600',
                                    ];
                                    $actionLabels = [
                                        'document_verified' => 'Verified',
                                        'document_rejected' => 'Rejected',
                                        'status_updated' => 'Status Updated',
                                    ];
                                    $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                                    $actionLabel = $actionLabels[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
                                @endphp
                                <span class="px-3 py-1 {{ $colorClass }} rounded-full text-xs font-medium whitespace-nowrap">
                                    {{ $actionLabel }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($log->old_status)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs whitespace-nowrap">
                                            {{ str_replace('_', ' ', $log->old_status) }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @endif
                                    @if($log->new_status)
                                        <span class="px-2 py-1 
                                            @if($log->new_status == 'verified') bg-green-100 text-green-600
                                            @elseif($log->new_status == 'rejected') bg-red-100 text-red-600
                                            @elseif($log->new_status == 'approved') bg-green-100 text-green-600
                                            @elseif($log->new_status == 'under-review') bg-purple-100 text-purple-600
                                            @elseif($log->new_status == 'for-release') bg-blue-100 text-blue-600
                                            @else bg-gray-100 text-gray-600
                                            @endif rounded text-xs whitespace-nowrap">
                                            {{ str_replace('_', ' ', $log->new_status) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="max-w-xs">
                                    <p class="text-sm text-gray-600 truncate" title="{{ $log->remarks }}">
                                        {{ $log->remarks ?? 'No remarks' }}
                                    </p>
                                </div>
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
                                <span class="font-mono text-xs text-gray-600">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 mb-2">No application review logs found</p>
                                <p class="text-sm text-gray-500">Try adjusting your filters or check back later</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Showing {{ $applicationLogs->firstItem() ?? 0 }} to {{ $applicationLogs->lastItem() ?? 0 }} of {{ $applicationLogs->total() }} log entries
                </p>
                <div class="flex items-center gap-2">
                    @if($applicationLogs->onFirstPage())
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm" disabled>Previous</button>
                    @else
                        <a href="{{ $applicationLogs->previousPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">Previous</a>
                    @endif
                    
                    @php
                        $start = max(1, $applicationLogs->currentPage() - 2);
                        $end = min($applicationLogs->lastPage(), $applicationLogs->currentPage() + 2);
                    @endphp
                    
                    @if($start > 1)
                        <a href="{{ $applicationLogs->url(1) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">1</a>
                        @if($start > 2)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                    @endif
                    
                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $applicationLogs->currentPage())
                            <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">{{ $page }}</button>
                        @else
                            <a href="{{ $applicationLogs->url($page) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">{{ $page }}</a>
                        @endif
                    @endfor
                    
                    @if($end < $applicationLogs->lastPage())
                        @if($end < $applicationLogs->lastPage() - 1)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                        <a href="{{ $applicationLogs->url($applicationLogs->lastPage()) }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">{{ $applicationLogs->lastPage() }}</a>
                    @endif
                    
                    @if($applicationLogs->hasMorePages())
                        <a href="{{ $applicationLogs->nextPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition text-sm">Next</a>
                    @else
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- General Settings Tab -->
    @if($currentTab == 'general')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-[#155386]/10 to-[#40798C]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">General Settings Coming Soon</h3>
                <p class="text-gray-500 max-w-md mx-auto">This section is under construction. Check back later for configuration options.</p>
            </div>
        </div>
    @endif

    <!-- User Roles Tab -->
    @if($currentTab == 'roles')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-[#155386]/10 to-[#40798C]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">User Roles Coming Soon</h3>
                <p class="text-gray-500 max-w-md mx-auto">This section is under construction. Check back later for role management.</p>
            </div>
        </div>
    @endif

    <!-- Security Tab -->
    @if($currentTab == 'security')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-[#155386]/10 to-[#40798C]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Security Settings Coming Soon</h3>
                <p class="text-gray-500 max-w-md mx-auto">This section is under construction. Check back later for security configurations.</p>
            </div>
        </div>
    @endif
</div>

<style>
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
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #40798C;
    }
    
    /* Table hover effects */
    tbody tr {
        transition: all 0.2s ease;
    }
    
    /* Status badge hover */
    .rounded-full {
        transition: all 0.2s ease;
    }
    
    .rounded-full:hover {
        transform: scale(1.05);
    }
    
    /* Tab hover effect */
    .border-b-2 {
        transition: all 0.2s ease;
    }
    
    /* Gradient text */
    .bg-gradient-to-r {
        background-size: 100% 100%;
    }
</style>
@endsection