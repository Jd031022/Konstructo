@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Konstructo')

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
            <button class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </button>
            <button class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md">
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">1,247</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>12% from last month</span>
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
                    <p class="text-3xl font-bold text-yellow-600 mt-2">342</p>
                    <p class="text-xs text-yellow-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>24 awaiting &gt; 7 days</span>
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
                    <p class="text-3xl font-bold text-green-600 mt-2">1,234</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>+48 this week</span>
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
                    <p class="text-3xl font-bold text-purple-600 mt-2">78%</p>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>+5% vs last month</span>
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
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Applications Trend</h2>
                <select class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#155386]">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Last 3 Months</option>
                    <option>This Year</option>
                </select>
            </div>
            
            <!-- Chart Placeholder -->
            <div class="h-64 flex items-end justify-between gap-2">
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 45px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Mon</span>
                    <span class="text-xs font-medium">45</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 62px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Tue</span>
                    <span class="text-xs font-medium">62</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 58px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Wed</span>
                    <span class="text-xs font-medium">58</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 71px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Thu</span>
                    <span class="text-xs font-medium">71</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 55px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Fri</span>
                    <span class="text-xs font-medium">55</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 48px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Sat</span>
                    <span class="text-xs font-medium">48</span>
                </div>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg" style="height: 52px;"></div>
                    <span class="text-xs text-gray-500 mt-2">Sun</span>
                    <span class="text-xs font-medium">52</span>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Application Status</h2>
            
            <!-- Donut Chart Placeholder -->
            <div class="relative w-40 h-40 mx-auto mb-6">
                <svg viewBox="0 0 36 36" class="w-full h-full">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="3" stroke-dasharray="75, 100" />
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#F59E0B" stroke-width="3" stroke-dasharray="27, 100" stroke-dashoffset="-25" />
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" stroke-width="3" stroke-dasharray="15, 100" stroke-dashoffset="-52" />
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#EF4444" stroke-width="3" stroke-dasharray="10, 100" stroke-dashoffset="-67" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-800">1,247</span>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Pending Review</span>
                    </div>
                    <span class="text-sm font-medium">342 (27%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Approved</span>
                    </div>
                    <span class="text-sm font-medium">589 (47%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Rejected</span>
                    </div>
                    <span class="text-sm font-medium">160 (13%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                        <span class="text-sm text-gray-600">Others</span>
                    </div>
                    <span class="text-sm font-medium">156 (13%)</span>
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
            
            <div class="space-y-4">
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
                    <span class="text-lg font-bold text-gray-800">24</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-lg">E</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Engineers</p>
                            <p class="text-xs text-gray-500">Reviewing applications</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-800">156</span>
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
                    <span class="text-lg font-bold text-gray-800">89</span>
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
                    <span class="text-lg font-bold text-gray-800">965</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total Users</span>
                    <span class="font-bold text-gray-800">1,234</span>
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
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">ID</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Applicant</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Project</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Submitted</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-[#155386]">APP-2025-001</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full"></div>
                                    <span class="text-sm">Juan Dela Cruz</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm">Residential</td>
                            <td class="py-3 px-4 text-sm">2 hours ago</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs">Pending</span>
                            </td>
                            <td class="py-3 px-4">
                                <button class="text-[#155386] hover:text-[#40798C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-[#155386]">APP-2025-002</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full"></div>
                                    <span class="text-sm">Maria Santos</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm">Commercial</td>
                            <td class="py-3 px-4 text-sm">5 hours ago</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-full text-xs">Under Review</span>
                            </td>
                            <td class="py-3 px-4">
                                <button class="text-[#155386] hover:text-[#40798C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-[#155386]">APP-2025-003</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full"></div>
                                    <span class="text-sm">Pedro Reyes</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm">Industrial</td>
                            <td class="py-3 px-4 text-sm">1 day ago</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Approved</span>
                            </td>
                            <td class="py-3 px-4">
                                <button class="text-[#155386] hover:text-[#40798C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-[#155386]">APP-2025-004</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full"></div>
                                    <span class="text-sm">Anna Lopez</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm">Residential</td>
                            <td class="py-3 px-4 text-sm">2 days ago</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-purple-100 text-purple-600 rounded-full text-xs">For Release</span>
                            </td>
                            <td class="py-3 px-4">
                                <button class="text-[#155386] hover:text-[#40798C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
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
        
        <div class="space-y-4">
            <!-- Queue Item 1 -->
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 font-bold text-sm">3</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Pending Document Verification</p>
                        <p class="text-xs text-gray-500">Applications awaiting document check</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-yellow-600">24</span>
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
                <span class="text-2xl font-bold text-blue-600">18</span>
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
                <span class="text-2xl font-bold text-green-600">31</span>
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
                        <p class="text-sm font-medium text-gray-800">Returned for Revision</p>
                        <p class="text-xs text-gray-500">Applications needing changes</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-red-600">7</span>
            </div>
        </div>
        
        <!-- Average Wait Time -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-gray-600">Average Wait Time</span>
                </div>
                <span class="text-lg font-semibold text-gray-800">4.5 days</span>
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
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                        MS
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Maria Santos</p>
                        <p class="text-xs text-gray-500">Engineer</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">24</p>
                    <p class="text-xs text-gray-400">this week</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold">
                        JR
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Juan Reyes</p>
                        <p class="text-xs text-gray-500">Evaluator</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">18</p>
                    <p class="text-xs text-gray-400">this week</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white text-xs font-bold">
                        AL
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Anna Lopez</p>
                        <p class="text-xs text-gray-500">Reviewer</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600">15</p>
                    <p class="text-xs text-gray-400">this week</p>
                </div>
            </div>
            
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-gray-700 text-xs font-bold">
                        PT
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Pedro Tan</p>
                        <p class="text-xs text-gray-500">Processor</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-orange-500">9</p>
                    <p class="text-xs text-gray-400">this week</p>
                </div>
            </div>
        </div>
        
        <!-- Performance Summary -->
        <div class="mt-6 grid grid-cols-2 gap-4">
            <div class="p-3 bg-gray-50 rounded-lg text-center">
                <p class="text-xs text-gray-500">Total Processed</p>
                <p class="text-xl font-bold text-gray-800">156</p>
                <p class="text-xs text-green-600 mt-1">↑ 12%</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg text-center">
                <p class="text-xs text-gray-500">Avg. per Staff</p>
                <p class="text-xl font-bold text-gray-800">16.5</p>
                <p class="text-xs text-green-600 mt-1">↑ 5%</p>
            </div>
        </div>
    </div>
</div>

        <!-- Announcements & Updates -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Announcements</h2>
                <button class="text-sm text-[#155386] hover:underline">Create New</button>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">System Maintenance</p>
                            <p class="text-xs text-gray-600">Scheduled maintenance on Sunday, 2:00 AM - 4:00 AM.</p>
                            <p class="text-xs text-gray-400 mt-1">Posted 2 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">New Feature: Google Drive Upload</p>
                            <p class="text-xs text-gray-600">Applicants can now upload documents via Google Drive.</p>
                            <p class="text-xs text-gray-400 mt-1">Posted 1 day ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Application Deadline Extension</p>
                            <p class="text-xs text-gray-600">Building permit applications extended to June 30, 2025.</p>
                            <p class="text-xs text-gray-400 mt-1">Posted 3 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="mt-4 w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                View All Announcements
            </button>
        </div>
    </div>

    <!-- Quick Stats Footer -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Avg. Processing Time</p>
                    <p class="text-lg font-semibold text-gray-800">4.5 days</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Completion Rate</p>
                    <p class="text-lg font-semibold text-gray-800">78%</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Active Users</p>
                    <p class="text-lg font-semibold text-gray-800">1,234</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection