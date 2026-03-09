@extends('layouts.dashboard')

@section('title', 'Settings - Konstructo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Settings</h1>
            <p class="text-gray-500 text-sm mt-1">Manage system configuration and view activity logs</p>
        </div>
        
        <!-- Export Logs Button -->
        <button class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export Logs
        </button>
    </div>

    <!-- Settings Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
            <button class="py-4 px-1 border-b-2 border-[#155386] text-[#155386] font-medium text-sm">
                System Logs
            </button>
            <button class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                General Settings
            </button>
            <button class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                User Roles
            </button>
            <button class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Security
            </button>
        </nav>
    </div>

    <!-- Filters and Search - Using your design -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       placeholder="Search logs by user, action, or IP address..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
        
            <!-- Action Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">All Actions</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="create">Create</option>
                <option value="update">Update</option>
                <option value="delete">Delete</option>
                <option value="export">Export</option>
                <option value="settings">Settings Change</option>
            </select>
            
            <!-- Date Range Filter -->
            <select class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                <option value="">Date Range</option>
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="week">Last 7 Days</option>
                <option value="month">This Month</option>
                <option value="custom">Custom Range</option>
            </select>
            
            <!-- Filter Button -->
            <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                Apply Filters
            </button>
            
            <!-- Clear Filters (optional) -->
            <button class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition font-medium">
                Clear
            </button>
        </div>
    </div>

    <!-- System Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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
                    <!-- Log Entry 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    JD
                                </div>
                                <span class="font-medium text-gray-800">John Doe</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">johndoe</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Login</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.105</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 08:30:45</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Success</span>
                        </td>
                    </tr>

                    <!-- Log Entry 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    JS
                                </div>
                                <span class="font-medium text-gray-800">Jane Smith</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">janesmith</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">Create</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.110</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 09:15:22</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Success</span>
                        </td>
                    </tr>

                    <!-- Log Entry 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    MR
                                </div>
                                <span class="font-medium text-gray-800">Mike Reyes</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">mreyes</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-medium">Update</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.125</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 10:05:33</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Success</span>
                        </td>
                    </tr>

                    <!-- Log Entry 4 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-gray-700 text-xs font-bold">
                                    AL
                                </div>
                                <span class="font-medium text-gray-800">Anna Lopez</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">alopez</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">Delete</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.98</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 11:20:17</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">Failed</span>
                        </td>
                    </tr>

                    <!-- Log Entry 5 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    CG
                                </div>
                                <span class="font-medium text-gray-800">Carlos Gomez</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">cgomez</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-medium">Export</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.145</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 12:45:09</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Success</span>
                        </td>
                    </tr>

                    <!-- Log Entry 6 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    ST
                                </div>
                                <span class="font-medium text-gray-800">Sarah Tan</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600">stan</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-medium">Settings</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-600">192.168.1.156</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">2025-03-09 13:30:28</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Success</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-gray-500">Showing 1 to 6 of 1,247 log entries</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition disabled:opacity-50" disabled>
                    Previous
                </button>
                <button class="px-3 py-1 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">1</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">2</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">3</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">4</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">5</button>
                <span class="text-gray-400">...</span>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">25</button>
                <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>
    </div>



</div>

<!-- JavaScript for Settings -->
<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.border-b button');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-[#155386]', 'text-[#155386]');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Add active class to clicked tab
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-[#155386]', 'text-[#155386]');
                
                // Here you would load different content based on tab
                console.log('Switched to tab:', this.textContent.trim());
            });
        });
    });
</script>

<!-- Add to existing styles -->
<style>
    /* Custom scrollbar for modal */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #40798C;
    }
</style>
@endsection