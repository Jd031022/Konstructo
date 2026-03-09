@extends('layouts.dashboard')

@section('title', 'User Management - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
            <p class="text-gray-500 text-sm mt-1">Manage system users, roles, and permissions</p>
        </div>
        
        <!-- Add User Button -->
        <button onclick="openUserModal()" 
            class="inline-flex items-center px-4 py-2.5 bg-[#155386] text-white rounded-xl hover:bg-[#40798C] transition shadow-md hover:shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add New User
        </button>
    </div>

    <!-- User Roles Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-2">
        
        <!-- Engineers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-lg">E</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Engineers</p>
                    <p class="text-2xl font-bold text-gray-800">156</p>
                </div>
            </div>
        </div>
        
        <!-- Staff Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="text-green-600 font-bold text-lg">S</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Staff</p>
                    <p class="text-2xl font-bold text-gray-800">89</p>
                </div>
            </div>
        </div>
        
        <!-- Applicants Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="text-gray-600 font-bold text-lg">A</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applicants</p>
                    <p class="text-2xl font-bold text-gray-800">965</p>
                </div>
            </div>
        </div>
        
        <!-- Pending Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <span class="text-yellow-600 font-bold text-lg">P</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-800">24</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       placeholder="Search users by name, email, or role..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
            
            <!-- Role Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="engineer">Engineer</option>
                <option value="staff">Staff</option>
                <option value="applicant">Applicant</option>
            </select>
            
            <!-- Status Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
            </select>
            
            <!-- Filter Button -->
            <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">User</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Email</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Role</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Last Active</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Admin User -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold">
                                    JD
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">John Doe</p>
                                    <p class="text-xs text-gray-400">ID: USR-001</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">john.doe@example.com</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-medium">Admin</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Active</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2 mins ago</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <button onclick="editUser(1)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="resetPassword(1)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Reset Password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                <button onclick="toggleUserStatus(1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Deactivate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Engineer User -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white font-bold">
                                    JS
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Jane Santos</p>
                                    <p class="text-xs text-gray-400">ID: USR-002</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">jane.santos@example.com</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">Engineer</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Active</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">15 mins ago</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <button onclick="editUser(2)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="resetPassword(2)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Reset Password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                <button onclick="toggleUserStatus(2)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Deactivate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Staff User -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white font-bold">
                                    MR
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Mike Reyes</p>
                                    <p class="text-xs text-gray-400">ID: USR-003</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">mike.reyes@example.com</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Staff</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Pending</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2 days ago</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <button onclick="editUser(3)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="resetPassword(3)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Reset Password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                <button onclick="approveUser(3)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Approve">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Applicant User -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-gray-700 font-bold">
                                    AL
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Anna Lopez</p>
                                    <p class="text-xs text-gray-400">ID: USR-004</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-600">anna.lopez@example.com</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Applicant</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Active</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">1 hour ago</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <button onclick="editUser(4)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="resetPassword(4)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Reset Password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                <button onclick="toggleUserStatus(4)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Deactivate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">Showing 1 to 4 of 1,234 users</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                    Previous
                </button>
                <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">1</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">2</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">3</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">4</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">5</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Add/Edit User Modal -->
<div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <h3 class="text-xl font-bold" id="modal-title">Add New User</h3>
                <button onclick="closeUserModal()" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <form id="user-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                <option value="admin">Admin</option>
                                <option value="engineer">Engineer</option>
                                <option value="staff">Staff</option>
                                <option value="applicant">Applicant</option>
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                        
                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeUserModal()" 
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
                            Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for User Management -->
<script>
    function openUserModal() {
        document.getElementById('user-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('modal-title').textContent = 'Add New User';
    }

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editUser(userId) {
        document.getElementById('user-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('modal-title').textContent = 'Edit User';
        // Here you would load user data and populate the form
        console.log('Editing user:', userId);
    }

    function resetPassword(userId) {
        if (confirm('Are you sure you want to reset this user\'s password? A reset link will be sent to their email.')) {
            console.log('Reset password for user:', userId);
            // API call to reset password
            alert('Password reset link sent to user\'s email.');
        }
    }

    function toggleUserStatus(userId) {
        if (confirm('Are you sure you want to deactivate this user?')) {
            console.log('Deactivate user:', userId);
            // API call to deactivate user
            alert('User has been deactivated.');
        }
    }

    function approveUser(userId) {
        if (confirm('Approve this user?')) {
            console.log('Approve user:', userId);
            // API call to approve user
            alert('User has been approved.');
        }
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('user-modal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeUserModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeUserModal();
            }
        });
    });
</script>

<!-- Add to existing styles -->
<style>
    /* Modal animations */
    #user-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #user-modal .bg-white {
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
</style>
@endsection