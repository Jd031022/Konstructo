@extends('layouts.dashboard')

@section('title', 'Payment Assessments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Payment Assessments</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and review all building permit fee assessments</p>
        </div>
        
        <!-- Export Button -->
        <div class="mt-4 md:mt-0">
            <button class="inline-flex items-center px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Report
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Assessments -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Assessments</p>
                    <p class="text-2xl font-bold mt-1">147</p>
                    <p class="text-xs text-green-600 mt-1">+12 this month</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Pending Payments</p>
                    <p class="text-2xl font-bold mt-1">23</p>
                    <p class="text-xs text-yellow-600 mt-1">Awaiting settlement</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Collected -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Collected</p>
                    <p class="text-2xl font-bold mt-1">₱1,284,500</p>
                    <p class="text-xs text-green-600 mt-1">+₱128,450 this month</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Assessment -->
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Average Assessment</p>
                    <p class="text-2xl font-bold mt-1">₱8,738</p>
                    <p class="text-xs text-gray-500 mt-1">Per application</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <!-- Date Range Filter -->
                <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#155386] focus:border-transparent bg-white">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                </select>

                <!-- Search Input -->
                <div class="relative">
                    <input type="text" placeholder="Search application # or owner..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- View Toggle -->
            <div class="flex items-center gap-2 border border-gray-200 rounded-lg p-1 bg-gray-50">
                <button class="px-3 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-[#155386]">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    Table View
                </button>
                <button class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Card View
                </button>
            </div>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Location</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment Date</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Building Permit Fee</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">CPDO Fee</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Assessment Row 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0001</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Juan Dela Cruz</div>
                            <div class="text-xs text-gray-500">juan.delacruz@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">123 Rizal St., Barangay San Juan, Ligao City</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Jan 15, 2024</div>
                            <div class="text-xs text-gray-400">3 months ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱5,250.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱2,500.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-green-700">₱7,750.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0001', 'Juan Dela Cruz', '₱7,750.00')" class="inline-flex items-center px-3 py-1.5 bg-[#155386] text-white text-sm rounded-lg hover:bg-[#1F363D] transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>

                    <!-- Assessment Row 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0002</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                            <div class="text-xs text-gray-500">maria.santos@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">45 Mabini Ave., Barangay Poblacion, Ligao City</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Feb 02, 2024</div>
                            <div class="text-xs text-gray-400">2 months ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱12,500.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱3,800.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-green-700">₱16,300.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0002', 'Maria Santos', '₱16,300.00')" class="inline-flex items-center px-3 py-1.5 bg-[#155386] text-white text-sm rounded-lg hover:bg-[#1F363D] transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>

                    <!-- Assessment Row 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0003</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Pedro Reyes</div>
                            <div class="text-xs text-gray-500">pedro.reyes@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">88 Luna St., Barangay Paulba, Ligao City</div>
                        <td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Mar 20, 2024</div>
                            <div class="text-xs text-gray-400">1 month ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱8,250.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱1,750.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-yellow-700">₱10,000.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0003', 'Pedro Reyes', '₱10,000.00')" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>

                    <!-- Assessment Row 4 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0004</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Ana Garcia</div>
                            <div class="text-xs text-gray-500">ana.garcia@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">15 Bonifacio St., Barangay Tinago, Ligao City</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Apr 05, 2024</div>
                            <div class="text-xs text-gray-400">25 days ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱6,750.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱2,200.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-orange-700">₱8,950.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0004', 'Ana Garcia', '₱8,950.00')" class="inline-flex items-center px-3 py-1.5 bg-[#155386] text-white text-sm rounded-lg hover:bg-[#1F363D] transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>

                    <!-- Assessment Row 5 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0005</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Ramon Fernandez</div>
                            <div class="text-xs text-gray-500">ramon.fernandez@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">222 Quezon Ave., Barangay Sta. Elena, Ligao City</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Jan 30, 2024</div>
                            <div class="text-xs text-gray-400">3 months ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱15,000.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱4,500.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-red-700">₱19,500.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0005', 'Ramon Fernandez', '₱19,500.00')" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>

                    <!-- Assessment Row 6 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-[#155386]">BP-2024-0006</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Carmen Villanueva</div>
                            <div class="text-xs text-gray-500">carmen.v@email.com</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">7 Rizal Park, Barangay Bagong Barrio, Ligao City</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">Apr 18, 2024</div>
                            <div class="text-xs text-gray-400">12 days ago</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱4,500.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900">₱1,200.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-green-700">₱5,700.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openPaymentOrderModal('BP-2024-0006', 'Carmen Villanueva', '₱5,700.00')" class="inline-flex items-center px-3 py-1.5 bg-[#155386] text-white text-sm rounded-lg hover:bg-[#1F363D] transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Order #
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">147</span> results
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">
                        Previous
                    </button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-[#155386] text-white transition">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">4</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">5</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white text-gray-600 hover:bg-gray-50 transition">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Summary Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Payment Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Total Collected (Building Permit)</span>
                    <span class="text-sm font-semibold text-gray-800">₱985,750.00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Total Collected (CPDO)</span>
                    <span class="text-sm font-semibold text-gray-800">₱298,750.00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Pending Amount</span>
                    <span class="text-sm font-semibold text-yellow-600">₱156,200.00</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-base font-bold text-gray-900">GRAND TOTAL</span>
                    <span class="text-xl font-bold text-[#155386]">₱1,284,500.00</span>
                </div>
            </div>
        </div>

        <!-- Monthly Collection Chart Preview -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Monthly Collections
            </h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>January</span>
                        <span>₱245,000</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>February</span>
                        <span>₱312,500</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 82%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>March</span>
                        <span>₱298,000</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 78%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>April</span>
                        <span>₱429,000</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <button class="mt-4 w-full text-center text-sm text-[#155386] hover:text-[#1F363D] font-medium">
                View Detailed Report →
            </button>
        </div>

        <!-- Recently Added Order Numbers -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Recently Added Order Numbers
            </h3>
            <div class="space-y-3 max-h-48 overflow-y-auto">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-800">BP-2024-0006</p>
                        <p class="text-xs text-gray-400">Order #: PO-2024-0012</p>
                    </div>
                    <span class="text-xs text-green-600">Added today</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-800">BP-2024-0002</p>
                        <p class="text-xs text-gray-400">Order #: PO-2024-0008</p>
                    </div>
                    <span class="text-xs text-green-600">Added yesterday</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-800">BP-2024-0004</p>
                        <p class="text-xs text-gray-400">Order #: PO-2024-0005</p>
                    </div>
                    <span class="text-xs text-gray-500">2 days ago</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800">BP-2024-0001</p>
                        <p class="text-xs text-gray-400">Order #: PO-2024-0001</p>
                    </div>
                    <span class="text-xs text-gray-500">3 days ago</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Order Number Modal -->
<div id="paymentOrderModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Add Payment Order Number</h3>
                    <p class="text-sm opacity-90 mt-1">Record payment transaction reference</p>
                </div>
                <button onclick="closePaymentOrderModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="paymentOrderForm" onsubmit="submitPaymentOrder(event)">
                    <input type="hidden" id="modal_application_number" value="">
                    
                    <!-- Application Info Display -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Application Number:</span>
                            <span id="modal_app_number_display" class="text-sm font-mono font-semibold text-[#155386]">-</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Applicant:</span>
                            <span id="modal_applicant_name" class="text-sm font-medium text-gray-800">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Total Amount:</span>
                            <span id="modal_total_amount" class="text-sm font-bold text-green-600">-</span>
                        </div>
                    </div>

                    <!-- Order Number Input -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Order Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="order_number" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" 
                               placeholder="e.g., PO-2024-001234"
                               required>
                        <p class="text-xs text-gray-400 mt-1">Enter the official payment order number or OR number from the payment portal</p>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="payment_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <!-- Notes (Optional) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="payment_notes" 
                                  rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent text-sm" 
                                  placeholder="Additional notes about this payment..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closePaymentOrderModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm font-medium">
                            Save Order Number
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Modal (Hidden by default) -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4" style="backdrop-filter: blur(4px);">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
            <p id="successMessage" class="text-sm text-gray-600 mb-6">Payment order number has been added successfully.</p>
            <button onclick="closeSuccessModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                OK
            </button>
        </div>
    </div>
</div>

<script>
    // Payment Order Modal Functions
    function openPaymentOrderModal(appNumber, applicantName, totalAmount) {
        document.getElementById('modal_application_number').value = appNumber;
        document.getElementById('modal_app_number_display').textContent = appNumber;
        document.getElementById('modal_applicant_name').textContent = applicantName;
        document.getElementById('modal_total_amount').textContent = totalAmount;
        document.getElementById('order_number').value = '';
        document.getElementById('payment_notes').value = '';
        document.getElementById('paymentOrderModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePaymentOrderModal() {
        document.getElementById('paymentOrderModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function submitPaymentOrder(event) {
        event.preventDefault();
        
        const applicationNumber = document.getElementById('modal_application_number').value;
        const orderNumber = document.getElementById('order_number').value;
        const paymentDate = document.getElementById('payment_date').value;
        const notes = document.getElementById('payment_notes').value;
        
        // Here you would typically send this data to your backend
        console.log({
            application_number: applicationNumber,
            order_number: orderNumber,
            payment_date: paymentDate,
            notes: notes
        });
        
        // Close the modal and show success message
        closePaymentOrderModal();
        
        // Show success modal
        document.getElementById('successMessage').textContent = `Order number "${orderNumber}" has been added for application ${applicationNumber}.`;
        document.getElementById('successModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Refresh or update the table row if needed
        setTimeout(() => {
            closeSuccessModal();
        }, 3000);
    }

    function closeSuccessModal() {
        document.getElementById('successModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.getElementById('paymentOrderModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePaymentOrderModal();
    });
    
    document.getElementById('successModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSuccessModal();
    });
</script>
@endsection