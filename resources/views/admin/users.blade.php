@extends('layouts.dashboard')

@section('title', 'User Management - Konstructo')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage system users, roles, and permissions</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-4 md:mt-0 flex items-center gap-3">
            <a href="/admin/users/export" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </a>
            <button onclick="openUserModal()" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition shadow-md text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Add New User
            </button>
        </div>
    </div>

    <!-- User Roles Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8" id="stats-container">
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-orange-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-users">0</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-purple-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Admins</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-admins">0</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <span class="text-white font-bold text-lg">A</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Staff</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-staff">0</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <span class="text-white font-bold text-lg">S</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-gray-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Applicants</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-applicants">0</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <span class="text-white font-bold text-lg">A</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Active</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1" id="total-active">0</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M5.5 20a6.5 6.5 0 0 1 13 0"/>
                    </svg>
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
                       placeholder="Search users by name, email, or role..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
            </div>
            
            <select id="role-filter" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="applicant">Applicant</option>
            </select>
            
            <select id="status-filter" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            
            <button onclick="applyFilters()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                Apply Filters
            </button>
            
            <button onclick="resetFilters()" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                Reset
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Position</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Active</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <div id="loading-indicator" class="text-center py-8 hidden">
            <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500 mt-2">Loading users...</p>
        </div>

        <div id="empty-state" class="text-center py-12 hidden">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
            <p class="text-gray-500 mb-4">Get started by creating a new user.</p>
            <button onclick="openUserModal()" class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New User
            </button>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-3xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold" id="modal-title">Add New User</h3>
                    <button onclick="closeUserModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <div id="modal-error" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm"></div>
                    <div id="modal-success" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm"></div>
                    
                    <form id="user-form" onsubmit="saveUser(event)">
                        <input type="hidden" id="user-id">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                            
                            <!-- Last Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                            
                            <!-- Middle Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" id="middle_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                            
                            <!-- Suffix -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                                <select id="suffix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                    <option value="">None</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                            
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" id="email" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                            
                            <!-- Username -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                                <input type="text" id="username" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Letters, numbers, dashes and underscores only</p>
                            </div>
                            
                            <!-- Password Fields -->
                            <div id="password-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" id="password" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" id="password_confirmation" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                </div>
                            </div>
                            
                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">User Role <span class="text-red-500">*</span></label>
                                <select id="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="staff">Staff</option>
                                    <option value="applicant">Applicant</option>
                                </select>
                            </div>
                            
                            <!-- Position (Staff only) -->
                            <div id="position-field" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Position <span class="text-red-500">*</span></label>
                                <select id="position" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                    <option value="">Select Position</option>
                                    <option value="engineer">Engineer</option>
                                    <option value="architect">Architect</option>
                                    <option value="BFP">BFP</option>
                                    <option value="cpdo">CPDO</option>
                                    <option value="administrative_aide">Administrative Aide</option>
                                    <option value="treasurer">Treasurer</option>
                                    <option value="assessor">Assessor</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Select the staff member's position/department</p>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" onclick="closeUserModal()" 
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Cancel
                            </button>
                            <button type="submit" id="save-btn"
                                class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition flex items-center gap-2 text-sm">
                                <span id="save-btn-text">Save User</span>
                                <span id="save-btn-spinner" class="hidden">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="reset-password-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold">Reset Password</h3>
                    <button onclick="closeResetPasswordModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div id="reset-password-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div id="toggle-status-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold" id="toggle-modal-title">Toggle Status</h3>
                    <button onclick="closeToggleModal()" class="text-white hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <p id="toggle-modal-message" class="text-gray-700 mb-6"></p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeToggleModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">Cancel</button>
                        <button onclick="confirmToggleStatus()" id="confirm-toggle-btn" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition flex items-center gap-2 text-sm">
                            <span id="toggle-btn-text">Confirm</span>
                            <span id="toggle-btn-spinner" class="hidden">
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

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-red-600 text-white">
                    <h3 class="text-xl font-bold">Delete User</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">Cancel</button>
                        <button onclick="confirmDelete()" id="delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 text-sm">
                            <span id="delete-btn-text">Delete</span>
                            <span id="delete-btn-spinner" class="hidden">
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

<!-- Success Message Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-sm">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Success</h3>
                    <p id="success-modal-message" class="text-sm text-gray-600 mb-6"></p>
                    <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Message Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-sm">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                    <p id="error-modal-message" class="text-sm text-gray-600 mb-6"></p>
                    <button onclick="closeErrorModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let users = [];
let filteredUsers = [];
let resetUserId = null;
let toggleUserId = null;
let toggleAction = null;
let deleteUserId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    
    const roleSelect = document.getElementById('role');
    roleSelect.addEventListener('change', function() {
        const positionField = document.getElementById('position-field');
        const positionSelect = document.getElementById('position');
        if (this.value === 'staff') {
            positionField.classList.remove('hidden');
            positionSelect.required = true;
        } else {
            positionField.classList.add('hidden');
            positionSelect.required = false;
            positionSelect.value = '';
        }
    });
    
    const modals = ['user-modal', 'reset-password-modal', 'toggle-status-modal', 'delete-modal', 'success-modal', 'error-modal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modalId === 'user-modal') closeUserModal();
                    if (modalId === 'reset-password-modal') closeResetPasswordModal();
                    if (modalId === 'toggle-status-modal') closeToggleModal();
                    if (modalId === 'delete-modal') closeDeleteModal();
                    if (modalId === 'success-modal') closeSuccessModal();
                    if (modalId === 'error-modal') closeErrorModal();
                }
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('user-modal').classList.contains('hidden')) closeUserModal();
            if (!document.getElementById('reset-password-modal').classList.contains('hidden')) closeResetPasswordModal();
            if (!document.getElementById('toggle-status-modal').classList.contains('hidden')) closeToggleModal();
            if (!document.getElementById('delete-modal').classList.contains('hidden')) closeDeleteModal();
            if (!document.getElementById('success-modal').classList.contains('hidden')) closeSuccessModal();
            if (!document.getElementById('error-modal').classList.contains('hidden')) closeErrorModal();
        }
    });

    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });

    document.getElementById('role-filter').addEventListener('change', applyFilters);
    document.getElementById('status-filter').addEventListener('change', applyFilters);
});

async function loadUsers() {
    showLoading();
    
    try {
        const response = await fetch('{{ route("admin.users.list") }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('API Response:', data);
        
        if (response.ok && data.users) {
            users = data.users;
            const stats = data.stats || calculateStats(users);
            updateStats(stats);
            filteredUsers = [...users];
            renderUsers();
        } else {
            console.error('Failed to load users:', data);
            showErrorModal(data.error || 'Failed to load users');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred while loading users');
    } finally {
        hideLoading();
    }
}

function calculateStats(users) {
    return {
        total: users.length,
        admins: users.filter(u => u.role === 'admin').length,
        staff: users.filter(u => u.role === 'staff').length,
        applicants: users.filter(u => u.role === 'applicant').length,
        active: users.filter(u => u.status === 'active').length
    };
}

function updateStats(stats) {
    document.getElementById('total-users').textContent = stats.total || 0;
    document.getElementById('total-admins').textContent = stats.admins || 0;
    document.getElementById('total-staff').textContent = stats.staff || 0;
    document.getElementById('total-applicants').textContent = stats.applicants || 0;
    document.getElementById('total-active').textContent = stats.active || 0;
}

function applyFilters() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const roleFilter = document.getElementById('role-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    
    filteredUsers = users.filter(user => {
        const matchesSearch = searchTerm === '' || 
            user.name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            user.role.toLowerCase().includes(searchTerm);
        const matchesRole = roleFilter === '' || user.role === roleFilter;
        const matchesStatus = statusFilter === '' || user.status === statusFilter;
        return matchesSearch && matchesRole && matchesStatus;
    });
    
    renderUsers();
}

function resetFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('role-filter').value = '';
    document.getElementById('status-filter').value = '';
    filteredUsers = [...users];
    renderUsers();
}

function getRoleColor(role) {
    const colors = { 'admin': 'purple', 'staff': 'blue', 'applicant': 'gray' };
    return colors[role] || 'gray';
}

function getStatusColor(status) {
    return status === 'active' ? 'green' : 'red';
}

function getPositionDisplay(position) {
    if (!position) return '—';
    const positionMap = {
        'engineer': 'Engineer',
        'architect': 'Architect',
        'BFP': 'BFP',
        'bfp': 'BFP',
        'cpdo': 'CPDO',
        'administrative_aide': 'Admin Aide',
        'treasurer': 'Treasurer',
        'assessor': 'Assessor'
    };
    return positionMap[position] || position;
}

function getUserAvatarUrl(user) {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=40&background=155386&color=fff&bold=true`;
}

function renderUsers() {
    const tbody = document.getElementById('users-table-body');
    const emptyState = document.getElementById('empty-state');
    const table = document.querySelector('table');
    
    if (!filteredUsers || filteredUsers.length === 0) {
        if (table) table.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    if (table) table.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    tbody.innerHTML = filteredUsers.map(user => {
        const roleColor = getRoleColor(user.role);
        const statusColor = getStatusColor(user.status);
        const avatarUrl = getUserAvatarUrl(user);
        const positionDisplay = getPositionDisplay(user.position);
        
        return `
        <tr class="hover:bg-gray-50 transition">
            <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border-2 border-gray-200">
                        <img src="${avatarUrl}" 
                             alt="${user.name}" 
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('${user.name}') + '&size=40&background=155386&color=fff&bold=true';">
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">${user.name}</p>
                        <p class="text-xs text-gray-400">ID: USR-${String(user.id).padStart(4, '0')}</p>
                    </div>
                </div>
            </td>
            <td class="py-4 px-6 text-sm text-gray-600">${user.email}</td>
            <td class="py-4 px-6">
                <span class="px-3 py-1 bg-${roleColor}-100 text-${roleColor}-600 rounded-full text-xs font-medium capitalize">
                    ${user.role}
                </span>
            </td>
            <td class="py-4 px-6">
                ${user.role === 'staff' ? `<span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">${positionDisplay}</span>` : '<span class="text-gray-400 text-xs">—</span>'}
            </td>
            <td class="py-4 px-6">
                <span class="px-3 py-1 bg-${statusColor}-100 text-${statusColor}-600 rounded-full text-xs font-medium">
                    ${user.status}
                </span>
            </td>
            <td class="py-4 px-6 text-sm text-gray-500">${user.last_active || 'Never'}</td>
            <td class="py-4 px-6">
                <div class="flex items-center gap-2">
                    <button onclick="editUser(${user.id})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="resetPassword(${user.id}, '${user.name}')" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Reset Password">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </button>
                    <button onclick="toggleUserStatus(${user.id}, '${user.name}', '${user.status}')" class="p-2 ${user.status === 'active' ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50'} rounded-lg transition" title="${user.status === 'active' ? 'Deactivate' : 'Activate'}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${user.status === 'active' 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                            }
                        </svg>
                    </button>
                    <button onclick="confirmDeleteUser(${user.id}, '${user.name}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `}).join('');
}

function showLoading() {
    document.getElementById('loading-indicator').classList.remove('hidden');
    const table = document.querySelector('table');
    const emptyState = document.getElementById('empty-state');
    if (table) table.classList.add('hidden');
    if (emptyState) emptyState.classList.add('hidden');
}

function hideLoading() {
    document.getElementById('loading-indicator').classList.add('hidden');
}

function openUserModal() {
    document.getElementById('modal-title').textContent = 'Add New User';
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-fields').classList.remove('hidden');
    document.getElementById('password').required = true;
    document.getElementById('password_confirmation').required = true;
    document.getElementById('position-field').classList.add('hidden');
    document.getElementById('position').required = false;
    document.getElementById('user-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    clearModalMessages();
}

function closeUserModal() {
    document.getElementById('user-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    clearModalMessages();
}

function clearModalMessages() {
    const errorDiv = document.getElementById('modal-error');
    const successDiv = document.getElementById('modal-success');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
    if (successDiv) {
        successDiv.classList.add('hidden');
        successDiv.textContent = '';
    }
}

function showModalError(message) {
    const errorDiv = document.getElementById('modal-error');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

function showModalSuccess(message) {
    const successDiv = document.getElementById('modal-success');
    if (successDiv) {
        successDiv.textContent = message;
        successDiv.classList.remove('hidden');
    }
}

async function saveUser(event) {
    event.preventDefault();
    
    const userId = document.getElementById('user-id').value;
    const isEditing = !!userId;
    const role = document.getElementById('role').value;
    
    // Basic validation
    const required = ['first_name', 'last_name', 'email', 'username', 'role'];
    for (let field of required) {
        const input = document.getElementById(field);
        if (!input.value) {
            showModalError(`${field.replace('_', ' ')} is required`);
            input.focus();
            return;
        }
    }
    
    // Position validation for staff
    if (role === 'staff') {
        const position = document.getElementById('position').value;
        if (!position) {
            showModalError('Position is required for staff users');
            document.getElementById('position').focus();
            return;
        }
    }
    
    // Email validation
    const email = document.getElementById('email').value;
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showModalError('Please enter a valid email address');
        return;
    }
    
    // Username validation
    const username = document.getElementById('username').value;
    if (!username.match(/^[a-zA-Z0-9_-]+$/)) {
        showModalError('Username may only contain letters, numbers, dashes and underscores');
        return;
    }
    
    // Password validation for new users
    if (!isEditing) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        
        if (!password) {
            showModalError('Password is required');
            return;
        }
        
        if (password.length < 8) {
            showModalError('Password must be at least 8 characters');
            return;
        }
        
        if (password !== confirm) {
            showModalError('Passwords do not match');
            return;
        }
    }
    
    // Show loading on button
    const saveBtnText = document.getElementById('save-btn-text');
    const saveBtnSpinner = document.getElementById('save-btn-spinner');
    const saveBtn = document.getElementById('save-btn');
    
    saveBtnText.classList.add('hidden');
    saveBtnSpinner.classList.remove('hidden');
    saveBtn.disabled = true;
    
    const formData = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        middle_name: document.getElementById('middle_name').value || null,
        suffix: document.getElementById('suffix').value || null,
        email: document.getElementById('email').value,
        username: document.getElementById('username').value,
        role: role,
        position: role === 'staff' ? document.getElementById('position').value : null
    };
    
    if (!isEditing) {
        formData.password = document.getElementById('password').value;
        formData.password_confirmation = document.getElementById('password_confirmation').value;
    }
    
    try {
        const url = isEditing 
            ? `/admin/users/${userId}`
            : '{{ route("admin.users.store") }}';
        
        console.log('Sending request to:', url);
        console.log('Form data:', formData);
        
        const response = await fetch(url, {
            method: isEditing ? 'PUT' : 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        console.log('Response:', data);
        
        if (response.ok) {
            showModalSuccess(data.message || 'User saved successfully');
            setTimeout(() => {
                closeUserModal();
                loadUsers();
            }, 1500);
        } else {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showModalError(errorMessages);
            } else {
                showModalError(data.message || data.error || 'Failed to save user');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showModalError('An error occurred: ' + error.message);
    } finally {
        saveBtnText.classList.remove('hidden');
        saveBtnSpinner.classList.add('hidden');
        saveBtn.disabled = false;
    }
}

async function editUser(userId) {
    try {
        const response = await fetch(`/admin/users/${userId}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const user = await response.json();
        
        if (response.ok) {
            document.getElementById('modal-title').textContent = 'Edit User';
            document.getElementById('user-id').value = user.id;
            document.getElementById('first_name').value = user.first_name;
            document.getElementById('last_name').value = user.last_name;
            document.getElementById('middle_name').value = user.middle_name || '';
            document.getElementById('suffix').value = user.suffix || '';
            document.getElementById('email').value = user.email;
            document.getElementById('username').value = user.username;
            document.getElementById('role').value = user.role;
            
            if (user.role === 'staff') {
                document.getElementById('position-field').classList.remove('hidden');
                document.getElementById('position').required = true;
                document.getElementById('position').value = user.position || '';
            } else {
                document.getElementById('position-field').classList.add('hidden');
                document.getElementById('position').required = false;
            }
            
            document.getElementById('password-fields').classList.add('hidden');
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            
            document.getElementById('user-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            clearModalMessages();
        } else {
            showErrorModal(user.error || 'Failed to load user data');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred');
    }
}

function resetPassword(userId, userName) {
    resetUserId = userId;
    
    const modalContent = document.getElementById('reset-password-content');
    modalContent.innerHTML = `
        <p class="text-gray-700 mb-6">Are you sure you want to reset the password for <strong>${userName}</strong>? A new random password will be generated.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeResetPasswordModal()" 
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                Cancel
            </button>
            <button onclick="confirmResetPassword()" id="confirm-reset-btn"
                class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition flex items-center gap-2 text-sm">
                <span id="reset-btn-text">Reset Password</span>
                <span id="reset-btn-spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
    `;
    
    document.getElementById('reset-password-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

async function confirmResetPassword() {
    if (!resetUserId) return;
    
    const btn = document.getElementById('confirm-reset-btn');
    const btnText = document.getElementById('reset-btn-text');
    const spinner = document.getElementById('reset-btn-spinner');
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        const response = await fetch(`/admin/users/${resetUserId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const modalContent = document.getElementById('reset-password-content');
            modalContent.innerHTML = `
                <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-gray-700 mb-2">New Password:</p>
                    <p class="text-lg font-mono bg-white p-2 rounded border text-center select-all">${data.new_password}</p>
                    <p class="text-xs text-gray-500 mt-2">Please share this password with the user. They can change it after logging in.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button onclick="closeResetPasswordModal()" 
                        class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                        Close
                    </button>
                </div>
            `;
        } else {
            showErrorModal(data.error || 'Failed to reset password');
            closeResetPasswordModal();
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred');
        closeResetPasswordModal();
    }
}

function closeResetPasswordModal() {
    document.getElementById('reset-password-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetUserId = null;
}

function toggleUserStatus(userId, userName, currentStatus) {
    toggleUserId = userId;
    toggleAction = currentStatus === 'active' ? 'deactivate' : 'activate';
    
    document.getElementById('toggle-modal-title').textContent = toggleAction === 'deactivate' ? 'Deactivate User' : 'Activate User';
    document.getElementById('toggle-modal-message').innerHTML = `Are you sure you want to <strong>${toggleAction}</strong> <strong>${userName}</strong>?`;
    
    document.getElementById('toggle-status-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeToggleModal() {
    document.getElementById('toggle-status-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    toggleUserId = null;
    toggleAction = null;
}

async function confirmToggleStatus() {
    if (!toggleUserId) return;
    
    const btn = document.getElementById('confirm-toggle-btn');
    const btnText = document.getElementById('toggle-btn-text');
    const spinner = document.getElementById('toggle-btn-spinner');
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        const response = await fetch(`/admin/users/${toggleUserId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            closeToggleModal();
            showSuccessModal(data.message || `User ${toggleAction}d successfully`);
            loadUsers();
        } else {
            showErrorModal(data.error || `Failed to ${toggleAction} user`);
            closeToggleModal();
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred');
        closeToggleModal();
    }
}

function confirmDeleteUser(userId, userName) {
    deleteUserId = userId;
    document.getElementById('delete-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    deleteUserId = null;
}

async function confirmDelete() {
    if (!deleteUserId) return;
    
    const btn = document.getElementById('delete-btn');
    const btnText = document.getElementById('delete-btn-text');
    const spinner = document.getElementById('delete-btn-spinner');
    
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    btn.disabled = true;
    
    try {
        const response = await fetch(`/admin/users/${deleteUserId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            closeDeleteModal();
            showSuccessModal(data.message || 'User deleted successfully');
            loadUsers();
        } else {
            showErrorModal(data.error || 'Failed to delete user');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorModal('An error occurred');
    } finally {
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
        btn.disabled = false;
    }
}

function showSuccessModal(message) {
    const successMsg = document.getElementById('success-modal-message');
    if (successMsg) successMsg.textContent = message;
    document.getElementById('success-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        closeSuccessModal();
    }, 3000);
}

function closeSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showErrorModal(message) {
    const errorMsg = document.getElementById('error-modal-message');
    if (errorMsg) errorMsg.textContent = message;
    document.getElementById('error-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeErrorModal() {
    document.getElementById('error-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>

<style>
    #user-modal, #reset-password-modal, #toggle-status-modal, #delete-modal, #success-modal, #error-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #user-modal .bg-white, #reset-password-modal .bg-white, #toggle-status-modal .bg-white, 
    #delete-modal .bg-white, #success-modal .bg-white, #error-modal .bg-white {
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
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .select-all {
        user-select: all;
    }
</style>
@endsection