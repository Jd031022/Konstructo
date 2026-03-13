@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Manage users, applications, and system settings</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex items-center gap-3">
            <a href="/staff/applications/export" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </a>
            <button onclick="openAnnouncementModal()" class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Announcement
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Applications</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2" id="total-applications">0</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1" id="applications-trend">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>Loading...</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Review</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2" id="pending-applications">0</p>
                    <p class="text-xs text-yellow-600 mt-2 flex items-center gap-1" id="pending-aging">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Loading...</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Users</p>
                    <p class="text-3xl font-bold text-green-600 mt-2" id="active-users">0</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1" id="users-trend">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>Loading...</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completion Rate</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2" id="completion-rate">0%</p>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1" id="completion-trend">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Loading...</span>
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Applications Trend Chart -->
       <!-- Applications Trend Chart -->
<div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Applications Trend</h2>
        <select id="trend-period" onchange="loadTrendData()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#155386]">
            <option value="7">Last 7 Days</option>
            <option value="30" selected>Last 30 Days</option>
            <option value="90">Last 3 Months</option>
            <option value="365">This Year</option>
        </select>
    </div>
    
    <!-- Chart Container -->
    <div id="trend-chart-container" class="relative h-64 w-full overflow-x-auto pb-6">
        <div id="trend-chart" class="flex items-end justify-start gap-2 min-w-max h-full">
            <!-- Chart will be dynamically loaded -->
            <div class="flex-1 flex flex-col items-center justify-end min-w-[40px]">
                <div class="w-full bg-gray-200 animate-pulse rounded-t-lg" style="height: 200px;"></div>
            </div>
        </div>
    </div>
</div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Application Status</h2>
            
            <!-- Donut Chart -->
            <div class="relative w-40 h-40 mx-auto mb-6">
                <canvas id="status-chart" width="160" height="160"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span id="total-apps-display" class="text-2xl font-bold text-gray-800">0</span>
                </div>
            </div>
            
            <!-- Legend -->
            <div id="status-legend" class="space-y-2">
                <!-- Will be populated by JavaScript -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Pending Review</span>
                    </div>
                    <span id="pending-count" class="text-sm font-medium">0 (0%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Under Review</span>
                    </div>
                    <span id="under-review-count" class="text-sm font-medium">0 (0%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Approved</span>
                    </div>
                    <span id="approved-count" class="text-sm font-medium">0 (0%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Rejected</span>
                    </div>
                    <span id="rejected-count" class="text-sm font-medium">0 (0%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">For Release</span>
                    </div>
                    <span id="for-release-count" class="text-sm font-medium">0 (0%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Verified</span>
                    </div>
                    <span id="verified-count" class="text-sm font-medium">0 (0%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row - User Management & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Roles Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">User Roles</h2>
                <a href="/admin/users" class="text-sm text-[#155386] hover:underline">Manage →</a>
            </div>
            
            <div id="user-roles-container" class="space-y-4">
                <!-- Will be populated by JavaScript -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-lg">A</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Admins</p>
                            <p class="text-xs text-gray-500">System administrators</p>
                        </div>
                    </div>
                    <span id="admin-count" class="text-lg font-bold text-gray-800">0</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-green-600 font-bold text-lg">S</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Staff</p>
                            <p class="text-xs text-gray-500">Processing documents</p>
                        </div>
                    </div>
                    <span id="staff-count" class="text-lg font-bold text-gray-800">0</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-gray-600 font-bold text-lg">A</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Applicants</p>
                            <p class="text-xs text-gray-500">Permit applicants</p>
                        </div>
                    </div>
                    <span id="applicant-count" class="text-lg font-bold text-gray-800">0</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total Users</span>
                    <span id="total-users" class="font-bold text-gray-800">0</span>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Applications</h2>
                <a href="/staff/applications" class="text-sm text-[#155386] hover:underline">View All →</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Application #</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Applicant</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Submitted</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recent-applications-body" class="divide-y divide-gray-100">
                        <!-- Will be populated by JavaScript -->
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <svg class="animate-spin h-5 w-5 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-2">Loading applications...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Third Row - Document Verification Queue & Staff Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Document Verification Queue -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Document Verification Queue</h2>
                <a href="/admin/verification-queue" class="text-sm text-[#155386] hover:underline">View All →</a>
            </div>
            
            <div id="verification-queue" class="space-y-4">
                <!-- Queue Item 1 -->
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <span id="pending-count-large" class="text-yellow-600 font-bold text-sm">0</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Pending Document Verification</p>
                            <p class="text-xs text-gray-500">Applications awaiting document check</p>
                        </div>
                    </div>
                    <span id="pending-count-value" class="text-2xl font-bold text-yellow-600">0</span>
                </div>
                
                <!-- Queue Item 2 -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Under Review</p>
                            <p class="text-xs text-gray-500">Being reviewed by engineers</p>
                        </div>
                    </div>
                    <span id="under-review-count-value" class="text-2xl font-bold text-blue-600">0</span>
                </div>
                
                <!-- Queue Item 3 -->
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Ready for Release</p>
                            <p class="text-xs text-gray-500">Approved permits for releasing</p>
                        </div>
                    </div>
                    <span id="for-release-count-value" class="text-2xl font-bold text-green-600">0</span>
                </div>
                
                <!-- Queue Item 4 -->
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Rejected</p>
                            <p class="text-xs text-gray-500">Applications that were rejected</p>
                        </div>
                    </div>
                    <span id="rejected-count-value" class="text-2xl font-bold text-red-600">0</span>
                </div>
            </div>
            
            <!-- Average Processing Time -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-600">Average Processing Time</span>
                    </div>
                    <span id="avg-processing-time" class="text-lg font-semibold text-gray-800">0 days</span>
                </div>
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Staff Performance</h2>
                <a href="/admin/staff" class="text-sm text-[#155386] hover:underline">View All →</a>
            </div>
            
            <!-- Top Performers -->
            <div id="staff-performance" class="space-y-3">
                <!-- Will be populated by JavaScript -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                            LD
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Loading...</p>
                            <p class="text-xs text-gray-500">Loading stats...</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-600">-</p>
                    </div>
                </div>
            </div>
            
            <!-- Performance Summary -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <p class="text-xs text-gray-500">Total Processed</p>
                    <p id="total-processed" class="text-xl font-bold text-gray-800">0</p>
                    <p class="text-xs text-green-600 mt-1" id="processed-trend">↑ 0%</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg text-center">
                    <p class="text-xs text-gray-500">Avg. per Staff</p>
                    <p id="avg-per-staff" class="text-xl font-bold text-gray-800">0</p>
                    <p class="text-xs text-green-600 mt-1" id="avg-trend">↑ 0%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements & Updates -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">System Announcements</h2>
        <button onclick="openAnnouncementModal()" class="text-sm text-[#155386] hover:underline font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New
        </button>
    </div>
    
    <div id="announcements-container" class="space-y-4 min-h-[200px]">
        <!-- Announcements will be populated here -->
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-6 w-6 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
    
    <button onclick="window.location.href='/admin/announcements'" class="mt-4 w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
        View All Announcements
    </button>
</div>
</div>

<!-- New Announcement Modal -->
<div id="announcement-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold">Create Announcement</h3>
                    <button onclick="closeAnnouncementModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6">
                    <form id="announcement-form" onsubmit="createAnnouncement(event)">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="announcement-title" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                placeholder="e.g., System Maintenance">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                            <textarea id="announcement-content" rows="4" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent"
                                placeholder="Enter announcement details..."></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <select id="announcement-color" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                                <option value="blue">Blue (Info)</option>
                                <option value="green">Green (Success)</option>
                                <option value="yellow">Yellow (Warning)</option>
                                <option value="red">Red (Important)</option>
                            </select>
                        </div>
                        
                        <!-- Notification Option -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Send notifications to all users</span>
                                </div>
                                <input type="checkbox" id="notify-users" checked class="h-5 w-5 text-[#155386] border-gray-300 rounded focus:ring-[#155386]">
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Staff and applicants will receive in-app notifications and emails about this announcement.</p>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" onclick="closeAnnouncementModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
                                Post Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let statusChart = null;

    // Load all dashboard data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();
        loadTrendData();
        loadRecentApplications();
        loadUserStats();
        loadStaffPerformance();
        loadAnnouncements();
        setupModals();
    });

    // Load main dashboard statistics
    async function loadDashboardData() {
        try {
            const response = await fetch('/admin/dashboard/stats');
            const data = await response.json();
            
            if (data.success) {
                // Update statistics cards
                document.getElementById('total-applications').textContent = data.total_applications || 0;
                document.getElementById('pending-applications').textContent = data.pending_applications || 0;
                document.getElementById('active-users').textContent = data.active_users || 0;
                document.getElementById('completion-rate').textContent = (data.completion_rate || 0) + '%';
                
                // Update trends
                document.getElementById('applications-trend').innerHTML = `
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>${data.applications_trend || '+0%'} from last month</span>
                `;
                
                document.getElementById('pending-aging').innerHTML = `
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>${data.pending_aging || '0'} awaiting > 7 days</span>
                `;
                
                document.getElementById('users-trend').innerHTML = `
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>+${data.new_users_week || '0'} this week</span>
                `;
                
                document.getElementById('completion-trend').innerHTML = `
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>${data.completion_trend || '+0%'} vs last month</span>
                `;
                
                // Update status distribution
                updateStatusChart(data.status_counts);
                
                // Update verification queue
                updateVerificationQueue(data.status_counts);
                
                // Update processing time
                document.getElementById('avg-processing-time').textContent = data.avg_processing_time || '0';
            }
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    // Load trend data based on selected period
async function loadTrendData() {
    const period = document.getElementById('trend-period').value;
    
    try {
        const response = await fetch(`/admin/dashboard/trend?days=${period}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.trend_data) {
            renderTrendChart(data.trend_data);
        } else {
            console.error('No trend data received');
            document.getElementById('trend-chart').innerHTML = 
                '<div class="w-full text-center text-gray-500 py-12">No trend data available</div>';
        }
    } catch (error) {
        console.error('Error loading trend data:', error);
        document.getElementById('trend-chart').innerHTML = 
            '<div class="w-full text-center text-red-500 py-12">Failed to load trend data</div>';
    }
}

// Render trend chart
function renderTrendChart(trendData) {
    const chartContainer = document.getElementById('trend-chart');
    
    if (!trendData || trendData.length === 0) {
        chartContainer.innerHTML = '<div class="w-full text-center text-gray-500 py-12">No data available</div>';
        return;
    }
    
    let html = '';
    const maxValue = Math.max(...trendData.map(d => d.count), 1);
    
    // Limit the number of bars shown to prevent overflow
    const displayData = trendData.length > 30 ? trendData.slice(-30) : trendData;
    
    displayData.forEach(item => {
        const height = maxValue > 0 ? (item.count / maxValue) * 180 : 0; // Max height 180px
        
        html += `
            <div class="flex flex-col items-center justify-end min-w-[50px]">
                <div class="w-8 bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg transition-all duration-300 hover:opacity-80" 
                     style="height: ${height}px; min-height: 4px;">
                </div>
                <span class="text-xs text-gray-500 mt-2 font-medium">${item.label}</span>
                <span class="text-xs font-semibold text-[#155386]">${item.count}</span>
            </div>
        `;
    });
    
    chartContainer.innerHTML = html;
    
    // Add smooth scroll to the right to show latest data
    const container = document.getElementById('trend-chart-container');
    if (container) {
        container.scrollLeft = container.scrollWidth;
    }
}

    // Update status chart
    function updateStatusChart(counts) {
        const ctx = document.getElementById('status-chart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (statusChart) {
            statusChart.destroy();
        }
        
        const data = {
            labels: ['Pending', 'Under Review', 'Approved', 'Rejected', 'For Release', 'Verified'],
            datasets: [{
                data: [
                    counts.pending || 0,
                    counts.under_review || 0,
                    counts.approved || 0,
                    counts.rejected || 0,
                    counts.for_release || 0,
                    counts.verified || 0
                ],
                backgroundColor: [
                    '#F59E0B', // yellow
                    '#3B82F6', // blue
                    '#10B981', // green
                    '#EF4444', // red
                    '#8B5CF6', // purple
                    '#059669'  // emerald
                ],
                borderWidth: 0
            }]
        };
        
        statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Update legend counts
        const total = Object.values(counts).reduce((a, b) => a + b, 0);
        document.getElementById('total-apps-display').textContent = total;
        document.getElementById('pending-count').textContent = `${counts.pending || 0} (${total > 0 ? Math.round((counts.pending || 0) / total * 100) : 0}%)`;
        document.getElementById('under-review-count').textContent = `${counts.under_review || 0} (${total > 0 ? Math.round((counts.under_review || 0) / total * 100) : 0}%)`;
        document.getElementById('approved-count').textContent = `${counts.approved || 0} (${total > 0 ? Math.round((counts.approved || 0) / total * 100) : 0}%)`;
        document.getElementById('rejected-count').textContent = `${counts.rejected || 0} (${total > 0 ? Math.round((counts.rejected || 0) / total * 100) : 0}%)`;
        document.getElementById('for-release-count').textContent = `${counts.for_release || 0} (${total > 0 ? Math.round((counts.for_release || 0) / total * 100) : 0}%)`;
        document.getElementById('verified-count').textContent = `${counts.verified || 0} (${total > 0 ? Math.round((counts.verified || 0) / total * 100) : 0}%)`;
    }

    // Update verification queue counts
    function updateVerificationQueue(counts) {
        document.getElementById('pending-count-large').textContent = counts.pending || 0;
        document.getElementById('pending-count-value').textContent = counts.pending || 0;
        document.getElementById('under-review-count-value').textContent = counts.under_review || 0;
        document.getElementById('for-release-count-value').textContent = counts.for_release || 0;
        document.getElementById('rejected-count-value').textContent = counts.rejected || 0;
    }

    // Load recent applications
    async function loadRecentApplications() {
        try {
            const response = await fetch('/staff/applications/data?limit=5');
            const data = await response.json();
            
            const tbody = document.getElementById('recent-applications-body');
            
            if (data.success && data.applications && data.applications.length > 0) {
                let html = '';
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-600',
                    'under-review': 'bg-blue-100 text-blue-600',
                    'approved': 'bg-green-100 text-green-600',
                    'rejected': 'bg-red-100 text-red-600',
                    'for-release': 'bg-purple-100 text-purple-600',
                    'verified': 'bg-emerald-100 text-emerald-600'
                };
                
                const statusText = {
                    'pending': 'Pending',
                    'under-review': 'Under Review',
                    'approved': 'Approved',
                    'rejected': 'Rejected',
                    'for-release': 'For Release',
                    'verified': 'Completed'
                };
                
                data.applications.slice(0, 5).forEach(app => {
                    const date = app.created_at ? new Date(app.created_at) : new Date();
                    const timeAgo = getTimeAgo(date);
                    
                    const gradientColors = [
                        'from-[#155386] to-[#40798C]',
                        'from-[#40798C] to-[#70A9A1]',
                        'from-[#70A9A1] to-[#9EC5CB]',
                        'from-[#9EC5CB] to-[#B8D8E3]'
                    ];
                    
                    const randomGradient = gradientColors[app.id % gradientColors.length];
                    
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-[#155386]">${app.application_number}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-r ${randomGradient} rounded-full"></div>
                                    <span class="text-sm">${app.applicant_name || 'Unknown'}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm">${timeAgo}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 ${statusColors[app.status] || 'bg-gray-100 text-gray-600'} rounded-full text-xs">${statusText[app.status] || app.status}</span>
                            </td>
                            <td class="py-3 px-4">
                                <a href="/staff/application-details/${app.id}" class="text-[#155386] hover:text-[#40798C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2">No recent applications</p>
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error loading recent applications:', error);
        }
    }

    // Load user statistics
    async function loadUserStats() {
        try {
            const response = await fetch('/admin/users/stats');
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('admin-count').textContent = data.admin_count || 0;
                document.getElementById('staff-count').textContent = data.staff_count || 0;
                document.getElementById('applicant-count').textContent = data.applicant_count || 0;
                document.getElementById('total-users').textContent = data.total_users || 0;
            }
        } catch (error) {
            console.error('Error loading user stats:', error);
        }
    }

    // Load staff performance data
    async function loadStaffPerformance() {
        try {
            const response = await fetch('/admin/staff/performance');
            const data = await response.json();
            
            if (data.success) {
                const container = document.getElementById('staff-performance');
                
                if (data.staff && data.staff.length > 0) {
                    let html = '';
                    const gradients = [
                        'from-[#155386] to-[#40798C]',
                        'from-[#40798C] to-[#70A9A1]',
                        'from-[#70A9A1] to-[#9EC5CB]',
                        'from-[#9EC5CB] to-[#B8D8E3]'
                    ];
                    
                    data.staff.forEach((staff, index) => {
                        const gradient = gradients[index % gradients.length];
                        const initials = (staff.first_name?.[0] || '') + (staff.last_name?.[0] || '');
                        
                        html += `
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r ${gradient} rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        ${initials || 'ST'}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">${staff.first_name || ''} ${staff.last_name || ''}</p>
                                        <p class="text-xs text-gray-500">${staff.role || 'Staff'}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold ${staff.processed >= 15 ? 'text-green-600' : staff.processed >= 10 ? 'text-yellow-600' : 'text-gray-600'}">${staff.processed || 0}</p>
                                    <p class="text-xs text-gray-400">this week</p>
                                </div>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = html;
                    
                    // Update summary stats
                    document.getElementById('total-processed').textContent = data.total_processed || 0;
                    document.getElementById('avg-per-staff').textContent = data.avg_per_staff || 0;
                    document.getElementById('processed-trend').innerHTML = `↑ ${data.processed_trend || 0}%`;
                    document.getElementById('avg-trend').innerHTML = `↑ ${data.avg_trend || 0}%`;
                }
            }
        } catch (error) {
            console.error('Error loading staff performance:', error);
        }
    }

  // Load announcements
async function loadAnnouncements() {
    try {
        const response = await fetch('/admin/announcements?limit=3');
        
        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
        }
        
        const data = await response.json();
        
        const container = document.getElementById('announcements-container');
        
        if (data.success && data.announcements && data.announcements.length > 0) {
            let html = '';
            
            data.announcements.forEach(ann => {
                html += `
                    <div class="p-4 ${ann.color_class.split(' ')[0]} rounded-lg border ${ann.color_class.split(' ')[1]} hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 ${ann.color_class.split(' ')[0]} rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 ${ann.color_class.split(' ')[2]}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${ann.icon}" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-800">${ann.title}</p>
                                    <span class="text-xs text-gray-400">${ann.time_ago}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">${ann.content}</p>
                                <p class="text-xs text-gray-400 mt-2">Posted by ${ann.created_by}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="p-8 bg-gray-50 rounded-lg text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <p class="mt-2">No announcements yet</p>
                    <button onclick="openAnnouncementModal()" class="mt-3 text-sm text-[#155386] hover:underline">
                        Create your first announcement
                    </button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading announcements:', error);
        document.getElementById('announcements-container').innerHTML = `
            <div class="p-4 bg-red-50 rounded-lg text-center text-red-600">
                <p>Failed to load announcements</p>
                <button onclick="loadAnnouncements()" class="mt-2 text-sm underline">Try again</button>
            </div>0
        `;
    }
}

    // Create new announcement
async function createAnnouncement(event) {
    event.preventDefault();
    
    const title = document.getElementById('announcement-title').value;
    const content = document.getElementById('announcement-content').value;
    const color = document.getElementById('announcement-color').value;
    const notifyUsers = document.getElementById('notify-users').checked;
    
    // Show loading state on button
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Posting...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/admin/announcements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                title, 
                content, 
                color,
                notify_users: notifyUsers
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeAnnouncementModal();
            loadAnnouncements();
            document.getElementById('announcement-form').reset();
            
            // Show success message
            showSuccessModal('Announcement posted successfully' + 
                (notifyUsers ? ' and notifications sent to all users!' : '!'));
        } else {
            alert('Failed to create announcement: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error creating announcement:', error);
        alert('Failed to create announcement');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}
// Success modal function
function showSuccessModal(message) {
    // Create a temporary success notification
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
    // Helper function to get time ago string
    function getTimeAgo(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.round(diffMs / 60000);
        const diffHours = Math.round(diffMs / 3600000);
        const diffDays = Math.round(diffMs / 86400000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return diffMins + ' minutes ago';
        if (diffHours < 24) return diffHours + ' hours ago';
        if (diffDays < 7) return diffDays + ' days ago';
        return date.toLocaleDateString();
    }

    // Modal functions
    function openAnnouncementModal() {
        document.getElementById('announcement-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAnnouncementModal() {
        document.getElementById('announcement-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Setup modals
    function setupModals() {
        const modal = document.getElementById('announcement-modal');
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAnnouncementModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAnnouncementModal();
            }
        });
    }
</script>

<style>
    /* Trend chart scrollbar styling */
#trend-chart-container {
    scrollbar-width: thin;
    scrollbar-color: #155386 #e5e7eb;
}

#trend-chart-container::-webkit-scrollbar {
    height: 6px;
}

#trend-chart-container::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 3px;
}

#trend-chart-container::-webkit-scrollbar-thumb {
    background: #155386;
    border-radius: 3px;
}

#trend-chart-container::-webkit-scrollbar-thumb:hover {
    background: #40798C;
}

/* Chart bar hover effect */
#trend-chart > div:hover .bg-gradient-to-t {
    filter: brightness(1.1);
    transform: scaleY(1.02);
    transition: all 0.2s ease;
}
    /* Modal animations */
    #announcement-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #announcement-modal .bg-white {
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
    @keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.3s ease-out;
}
</style>
@endsection