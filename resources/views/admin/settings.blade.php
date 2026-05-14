@extends('layouts.dashboard')

@section('title', 'Settings')
@section('content')
@php
    // Set default value for currentTab if not set
    $currentTab = request()->query('tab', 'general');
@endphp
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage system configuration and view activity logs</p>
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
        <a href="{{ route('admin.logs.export', array_merge(request()->query(), ['format' => 'csv', 'tab' => $currentTab])) }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
            <svg class="inline w-4 h-4 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export as Excel
        </a>
        <a href="{{ route('admin.logs.export', array_merge(request()->query(), ['format' => 'html', 'tab' => $currentTab])) }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
            <svg class="inline w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Export as PDF
        </a>
    </div>
</div>

<script>
function toggleExportDropdown() {
    const dropdown = document.getElementById('export-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('export-dropdown');
    const button = event.target.closest('.relative');
    if (!button && dropdown) {
        dropdown.classList.add('hidden');
    }
});
</script>
    </div>

    <!-- Settings Tabs (Hidden General and Security) -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="flex space-x-8 overflow-x-auto pb-1">
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'general']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'general' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                General
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'system-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'system-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                System Logs
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'application-logs']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'application-logs' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                Application Review Logs
            </button>
            <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'roles']) }}'" 
                class="py-3 px-1 border-b-2 {{ $currentTab == 'roles' ? 'border-[#155386] text-[#155386]' : 'border-transparent text-gray-500 hover:text-gray-700' }} font-medium text-sm whitespace-nowrap transition">
                User Approval
            </button>
        </nav>
    </div>

    <!-- General Settings Tab -->
@if($currentTab == 'general')
    <div class="space-y-6">
        <!-- System Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold">System Information</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Application Name</label>
                        <p class="text-sm font-medium text-gray-800">Konstructo Building Permit System</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Version</label>
                        <p class="text-sm font-medium text-gray-800">v1.0.0</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Last Updated</label>
                        <p class="text-sm font-medium text-gray-800">{{ now()->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Status</label>
                        <p class="text-sm font-medium text-green-600 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Operational
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Configuration Card (Purple Theme) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-700 to-purple-500 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold">Email Settings</h2>
                </div>
            </div>
            <div class="p-6">
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Email Address</label>
                            <input type="email" value="noreply@konstructo.gov.ph" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                            <input type="text" value="Konstructo" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" disabled>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 rounded border-gray-300 text-purple-600">
                            <div>
                                <span class="font-medium text-gray-800">Send Email Notifications</span>
                                <p class="text-xs text-gray-500 mt-0.5">Enable email notifications for important system events</p>
                            </div>
                        </label>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-sm">Save Changes</button>
                        <button type="button" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Application Settings Card (Green Theme) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-700 to-green-500 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold">Application Settings</h2>
                </div>
            </div>
            <div class="p-6">
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Title</label>
                        <input type="text" value="Building Permit Application System" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Description</label>
                        <textarea rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">Building Permit Application System - Streamlined processing of building permits and related documentation.</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-3 p-3 bg-green-50 rounded-lg cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 rounded border-gray-300 text-green-600">
                                <span class="font-medium text-gray-800">Enable Applications</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center gap-3 p-3 bg-green-50 rounded-lg cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 rounded border-gray-300 text-green-600">
                                <span class="font-medium text-gray-800">Allow New Applicants</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">Save Changes</button>
                        <button type="button" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Maintenance Mode Card (Orange Theme) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-700 to-orange-500 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.34 9l1.42-1.42m2.83 2.83l1.42-1.42m2.83 2.83l1.42-1.42M18.66 9l1.42 1.42M9.76 18.66l1.42 1.42m2.83-2.83l1.42 1.42m2.83-2.83l1.42 1.42" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold">Maintenance Mode</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <p class="text-sm text-orange-800">
                            <strong>Maintenance mode is currently disabled.</strong> When enabled, only administrators can access the system.
                        </p>
                    </div>
                    <div>
                        <label class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-orange-600">
                            <div>
                                <span class="font-medium text-gray-800">Enable Maintenance Mode</span>
                                <p class="text-xs text-gray-500 mt-0.5">Restrict access to maintenance personnel only</p>
                            </div>
                        </label>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium text-sm">Update</button>
                        <button type="button" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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
                        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
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
                        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
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
                <table class="w-full min-w-[1200px]">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[15%]">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'reviewer', 'direction' => request('sort') == 'reviewer' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1 group">
                                    <span>Reviewer</span>
                                    @if(request('sort') == 'reviewer')
                                        <span class="ml-1 text-[#155386]">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="ml-1 opacity-0 group-hover:opacity-50">↕️</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[12%]">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'application', 'direction' => request('sort') == 'application' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1 group">
                                    <span>Application #</span>
                                    @if(request('sort') == 'application')
                                        <span class="ml-1 text-[#155386]">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="ml-1 opacity-0 group-hover:opacity-50">↕️</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[10%]">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'action', 'direction' => request('sort') == 'action' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1 group">
                                    <span>Action</span>
                                    @if(request('sort') == 'action')
                                        <span class="ml-1 text-[#155386]">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="ml-1 opacity-0 group-hover:opacity-50">↕️</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[15%]">
                                Status Change
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[20%]">
                                Remarks
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[15%]">
                                <a href="{{ route('admin.settings', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc', 'tab' => 'application-logs'])) }}" class="hover:text-[#155386] flex items-center gap-1 group">
                                    <span>Date & Time</span>
                                    @if(request('sort') == 'created_at')
                                        <span class="ml-1 text-[#155386]">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="ml-1 opacity-0 group-hover:opacity-50">↕️</span>
                                    @endif
                                </a>
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider w-[8%]">
                                IP Address
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($applicationLogs as $log)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm group-hover:shadow transition">
                                        {{ $log->reviewer ? strtoupper(substr($log->reviewer->first_name, 0, 1) . substr($log->reviewer->last_name, 0, 1)) : 'UN' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer' }}">
                                            {{ $log->reviewer ? $log->reviewer->first_name . ' ' . $log->reviewer->last_name : 'Unknown Reviewer' }}
                                        </p>
                                        @if($log->reviewer)
                                            <p class="text-xs text-gray-500 truncate">{{ $log->reviewer->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <a href="/admin/applications/{{ $log->application_id }}" class="text-[#155386] hover:text-[#40798C] font-mono text-sm font-medium underline decoration-transparent hover:decoration-[#155386] transition-all">
                                    {{ $log->application->application_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="py-3 px-6">
                                @php
                                    $actionColors = [
                                        'document_verified' => 'bg-green-100 text-green-700 border border-green-200',
                                        'document_rejected' => 'bg-red-100 text-red-700 border border-red-200',
                                        'status_updated' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    ];
                                    $actionIcons = [
                                        'document_verified' => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
                                        'document_rejected' => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
                                        'status_updated' => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>',
                                    ];
                                    $actionLabels = [
                                        'document_verified' => 'Document Verified',
                                        'document_rejected' => 'Document Rejected',
                                        'status_updated' => 'Status Updated',
                                    ];
                                    $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                                    $actionIcon = $actionIcons[$log->action] ?? '';
                                    $actionLabel = $actionLabels[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action));
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 {{ $colorClass }} rounded-full text-xs font-medium">
                                    {!! $actionIcon !!}
                                    {{ $actionLabel }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                @if($log->old_status || $log->new_status)
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($log->old_status)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs border border-gray-200">
                                                {{ str_replace('_', ' ', Str::title($log->old_status)) }}
                                            </span>
                                        @endif
                                        
                                        @if($log->old_status && $log->new_status)
                                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                        
                                        @if($log->new_status)
                                            @php
                                                $statusColors = [
                                                    'verified' => 'bg-green-100 text-green-700 border-green-200',
                                                    'approved' => 'bg-green-100 text-green-700 border-green-200',
                                                    'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                                    'under-review' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                    'for-release' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                ];
                                                $statusClass = $statusColors[$log->new_status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                            @endphp
                                            <span class="px-2 py-1 {{ $statusClass }} rounded-md text-xs font-medium border">
                                                {{ str_replace('_', ' ', Str::title($log->new_status)) }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">No status change</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                @if($log->remarks)
                                    <div class="group relative">
                                        <p class="text-sm text-gray-600 line-clamp-2 hover:line-clamp-none transition-all cursor-help" title="{{ $log->remarks }}">
                                            {{ $log->remarks }}
                                        </p>
                                        @if(strlen($log->remarks) > 100)
                                            <span class="text-xs text-[#155386] mt-1 hidden group-hover:inline-block">(click to expand)</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-700 whitespace-nowrap" title="{{ $log->created_at->format('F j, Y g:i:s A') }}">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ $log->created_at->format('h:i A') }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        ({{ $log->created_at->diffForHumans() }})
                                    </span>
                                </div>
                              </td>
                            <td class="py-3 px-6">
                                @if($log->ip_address)
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded-md text-gray-600 border border-gray-200" title="IP Address">
                                        {{ $log->ip_address }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">—</span>
                                @endif
                              </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No application review logs found</p>
                                    <p class="text-sm text-gray-500 max-w-md">Try adjusting your filters or check back later when staff have reviewed applications.</p>
                                    <button onclick="window.location.href='{{ route('admin.settings', ['tab' => 'application-logs']) }}'" class="mt-4 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                                        Clear All Filters
                                    </button>
                                </div>
                              </td>
                           </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-gray-500">
                    @if($applicationLogs->total() > 0)
                        Showing <span class="font-medium">{{ $applicationLogs->firstItem() ?? 0 }}</span> to 
                        <span class="font-medium">{{ $applicationLogs->lastItem() ?? 0 }}</span> of 
                        <span class="font-medium">{{ $applicationLogs->total() }}</span> log entries
                    @else
                        No entries found
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    @if($applicationLogs->onFirstPage())
                        <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm bg-gray-50" disabled>Previous</button>
                    @else
                        <a href="{{ $applicationLogs->previousPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition text-sm">Previous</a>
                    @endif
                    
                    <div class="flex items-center gap-1">
                        @php
                            $start = max(1, $applicationLogs->currentPage() - 2);
                            $end = min($applicationLogs->lastPage(), $applicationLogs->currentPage() + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $applicationLogs->url(1) }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition text-sm">1</a>
                            @if($start > 2)
                                <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
                            @endif
                        @endif
                        
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $applicationLogs->currentPage())
                                <span class="w-8 h-8 flex items-center justify-center bg-[#155386] text-white rounded-lg text-sm font-medium shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $applicationLogs->url($page) }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition text-sm">{{ $page }}</a>
                            @endif
                        @endfor
                        
                        @if($end < $applicationLogs->lastPage())
                            @if($end < $applicationLogs->lastPage() - 1)
                                <span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>
                            @endif
                            <a href="{{ $applicationLogs->url($applicationLogs->lastPage()) }}" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition text-sm">{{ $applicationLogs->lastPage() }}</a>
                        @endif
                    </div>
                    
                    @if($applicationLogs->hasMorePages())
                        <a href="{{ $applicationLogs->nextPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition text-sm">Next</a>
                    @else
                        <button class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-400 cursor-not-allowed text-sm bg-gray-50" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- User Approval Management -->
    @if($currentTab == 'roles')
    <!-- User Approval Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Applicants Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Applicants</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-applicants-count">0</p>
                    <p class="text-xs text-gray-500 mt-2">All applicants</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Approval Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-yellow-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="pending-applicants-count">0</p>
                    <p class="text-xs text-yellow-600 mt-2">Awaiting review</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Approved</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="approved-applicants-count">0</p>
                    <p class="text-xs text-green-600 mt-2">Successfully approved</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Rejected Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-red-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Rejected</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="rejected-applicants-count">0</p>
                    <p class="text-xs text-red-600 mt-2">Not approved</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           id="search-user"
                           placeholder="Search by name, email, or username..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                </div>
                
                <select id="filter-status" class="px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                
                <button onclick="resetUserFilters()" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                    Reset Filters
                </button>
                
                <button onclick="refreshUsers()" class="px-6 py-2.5 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Applicant</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Registered</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email Verified</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="animate-spin h-8 w-8 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">Loading applicants...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
        <div class="relative min-h-full flex items-center justify-center">
            <div class="mx-auto w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-4 bg-green-600 text-white">
                        <h3 class="text-xl font-bold">Approve Applicant</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">Are you sure you want to approve this applicant? They will be able to log in to the system.</p>
                        <p class="text-sm text-gray-500 mb-6" id="approve-user-info"></p>
                        
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
                    <div class="px-6 py-4 bg-red-600 text-white">
                        <h3 class="text-xl font-bold">Reject Applicant</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">Are you sure you want to reject this applicant? They will not be able to log in to the system.</p>
                        <p class="text-sm text-gray-500 mb-3" id="reject-user-info"></p>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection (Optional)</label>
                            <textarea id="reject-reason" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                placeholder="Enter reason for rejection..."></textarea>
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

    <script>
        let applicants = [];
        let currentUserId = null;
        
        // Load applicants from API on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadApplicants();
            setupEventListeners();
        });
        
        async function loadApplicants() {
            try {
                const response = await fetch('/admin/users/list?role=applicant', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.users) {
                    // Filter only applicants
                    applicants = data.users.filter(user => user.role === 'applicant');
                    
                    // Update summary cards
                    updateSummaryCards();
                    filterUsers();
                } else {
                    console.error('Failed to load applicants:', data);
                    showToast('Failed to load applicants', 'error');
                }
            } catch (error) {
                console.error('Error loading applicants:', error);
                showToast('Error loading applicants', 'error');
            }
        }
        
        function updateSummaryCards() {
            const totalCount = applicants.length;
            const pendingCount = applicants.filter(u => u.approval_status === 'pending').length;
            const approvedCount = applicants.filter(u => u.approval_status === 'approved').length;
            const rejectedCount = applicants.filter(u => u.approval_status === 'rejected').length;
            
            document.getElementById('total-applicants-count').textContent = totalCount;
            document.getElementById('pending-applicants-count').textContent = pendingCount;
            document.getElementById('approved-applicants-count').textContent = approvedCount;
            document.getElementById('rejected-applicants-count').textContent = rejectedCount;
        }
        
        function filterUsers() {
            const searchTerm = document.getElementById('search-user').value.toLowerCase();
            const statusFilter = document.getElementById('filter-status').value;
            
            let filteredUsers = applicants.filter(user => {
                const matchesSearch = (user.name?.toLowerCase().includes(searchTerm) || false) || 
                                     (user.email?.toLowerCase().includes(searchTerm) || false) ||
                                     (user.username?.toLowerCase().includes(searchTerm) || false);
                const matchesStatus = statusFilter === 'all' || user.approval_status === statusFilter;
                
                return matchesSearch && matchesStatus;
            });
            
            renderUsersTable(filteredUsers);
        }
        
        function renderUsersTable(filteredUsers) {
            const tbody = document.getElementById('users-table-body');
            
            if (filteredUsers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-gray-500">No applicants found</p>
                                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = filteredUsers.map(user => {
                // Get the username - handle both possible formats
                const username = user.username || user.user_name || 'N/A';
                
                // Check email verification status
                const emailVerified = user.email_verified_at !== null && user.email_verified_at !== undefined;
                
                // Get full name
                const fullName = user.name || (user.first_name ? `${user.first_name} ${user.last_name || ''}` : 'Unknown User');
                
                return `
                <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold">
                                ${getInitials(fullName)}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">${escapeHtml(fullName)}</p>
                            </div>
                        </div>
                      </td>
                    <td class="py-4 px-6 text-sm text-gray-600">${escapeHtml(user.email || 'N/A')}</td>
                    <td class="py-4 px-6 text-sm text-gray-600">${escapeHtml(username)}</td>
                    <td class="py-4 px-6 text-sm text-gray-500">
                        ${formatDate(user.created_at)}
                      </td>
                    <td class="py-4 px-6">
                        ${emailVerified ? 
                            '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Verified</span>' : 
                            '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Unverified</span>'}
                      </td>
                    <td class="py-4 px-6">
                        ${getStatusBadge(user.approval_status)}
                      </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2">
                            ${user.approval_status === 'pending' ? `
                                <button onclick="openApproveModal(${user.id}, '${escapeHtml(fullName).replace(/'/g, "\\'")}', '${escapeHtml(user.email).replace(/'/g, "\\'")}')" 
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Approve">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button onclick="openRejectModal(${user.id}, '${escapeHtml(fullName).replace(/'/g, "\\'")}', '${escapeHtml(user.email).replace(/'/g, "\\'")}')" 
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            ` : `
                                <span class="text-xs text-gray-400 italic">${user.approval_status === 'approved' ? 'Approved' : 'Rejected'}</span>
                            `}
                        </div>
                      </td>
                  </tr>
                `;
            }).join('');
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function getInitials(name) {
            if (!name || name === 'Unknown User') return 'U';
            const parts = name.split(' ');
            if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
            return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
        }
        
        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>',
                'approved': '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>',
                'rejected': '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Rejected</span>'
            };
            return badges[status] || badges.pending;
        }
        
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        function setupEventListeners() {
            document.getElementById('search-user').addEventListener('input', () => filterUsers());
            document.getElementById('filter-status').addEventListener('change', () => filterUsers());
        }
        
        function resetUserFilters() {
            document.getElementById('search-user').value = '';
            document.getElementById('filter-status').value = 'all';
            filterUsers();
        }
        
        function refreshUsers() {
            loadApplicants();
        }
        
        function openApproveModal(userId, userName, userEmail) {
            currentUserId = userId;
            document.getElementById('approve-user-info').innerHTML = `
                <strong>${escapeHtml(userName)}</strong><br>
                Email: ${escapeHtml(userEmail)}
            `;
            document.getElementById('approve-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentUserId = null;
        }
        
        async function confirmApprove() {
            if (!currentUserId) return;
            
            const btn = document.getElementById('confirm-approve-btn');
            const btnText = document.getElementById('approve-btn-text');
            const spinner = document.getElementById('approve-btn-spinner');
            
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
            btn.disabled = true;
            
            try {
                const response = await fetch(`/admin/users/${currentUserId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showToast('Applicant approved successfully!', 'success');
                    loadApplicants(); // Refresh the list
                } else {
                    showToast(data.error || 'Failed to approve applicant', 'error');
                }
            } catch (error) {
                console.error('Error approving applicant:', error);
                showToast('An error occurred', 'error');
            } finally {
                btnText.classList.remove('hidden');
                spinner.classList.add('hidden');
                btn.disabled = false;
                closeApproveModal();
            }
        }
        
        function openRejectModal(userId, userName, userEmail) {
            currentUserId = userId;
            document.getElementById('reject-user-info').innerHTML = `
                <strong>${escapeHtml(userName)}</strong><br>
                Email: ${escapeHtml(userEmail)}
            `;
            document.getElementById('reject-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('reject-reason').value = '';
            currentUserId = null;
        }
        
        async function confirmReject() {
            if (!currentUserId) return;
            
            const btn = document.getElementById('confirm-reject-btn');
            const btnText = document.getElementById('reject-btn-text');
            const spinner = document.getElementById('reject-btn-spinner');
            const reason = document.getElementById('reject-reason').value;
            
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
            btn.disabled = true;
            
            try {
                const response = await fetch(`/admin/users/${currentUserId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showToast(`Applicant rejected${reason ? ': ' + reason : ''}`, 'warning');
                    loadApplicants(); // Refresh the list
                } else {
                    showToast(data.error || 'Failed to reject applicant', 'error');
                }
            } catch (error) {
                console.error('Error rejecting applicant:', error);
                showToast('An error occurred', 'error');
            } finally {
                btnText.classList.remove('hidden');
                spinner.classList.add('hidden');
                btn.disabled = false;
                closeRejectModal();
            }
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' :
                type === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' :
                'bg-red-50 text-red-800 border border-red-200'
            }`;
            
            toast.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    }
                </svg>
                <span class="text-sm font-medium">${escapeHtml(message)}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
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
    
    /* Line clamp for remarks */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2:hover {
        -webkit-line-clamp: unset;
    }
    
    /* Pagination button styles */
    .pagination-btn {
        transition: all 0.2s ease;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }
    
    /* Summary cards hover */
    .grid > div {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .grid > div:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection