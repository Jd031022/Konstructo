@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto" id="dashboardContent">
<!-- PAGE HEADER -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Welcome back! Here's your applications overview.</p>
    </div>
    <div class="flex items-center gap-3 mt-2 md:mt-0">
        <!-- Show user's position if set -->
        @if(Auth::user()->profile && Auth::user()->profile->position)
        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#155386] rounded-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', Auth::user()->profile->position)) }}</span>
        </span>
        @endif
        
        <!-- EXPORT DROPDOWN -->
        <div class="relative inline-block">
            <button onclick="toggleExportDropdown()" 
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                <a href="{{ route('staff.dashboard.export', ['format' => 'excel']) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                    <svg class="inline w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export as Excel
                </a>
                <a href="{{ route('staff.dashboard.export', ['format' => 'pdf']) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                    <svg class="inline w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export as PDF
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- TOP STATS - 4 cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="stats-container">
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
            <div class="h-16 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- CHART AREA - Monthly Application Trend -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Monthly Application Trend</h2>
                    <p class="text-xs text-gray-500 mt-1">Application volume over time</p>
                </div>
                <div class="relative">
                    <select id="trend-period" class="appearance-none border border-gray-200 rounded-lg text-sm px-4 py-2.5 pr-8 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- BAR GRAPH -->
            <div class="relative h-80">
                <div id="y-axis-labels" class="absolute left-0 top-0 bottom-8 w-12 flex flex-col justify-between text-right pr-2 text-xs text-gray-400">
                    <span>0</span>
                    <span>0</span>
                    <span>0</span>
                    <span>0</span>
                    <span>0</span>
                </div>
                
                <div class="absolute left-12 right-0 top-0 bottom-8">
                    <div class="absolute w-full border-t border-dashed border-gray-200" style="top: 0%"></div>
                    <div class="absolute w-full border-t border-dashed border-gray-200" style="top: 25%"></div>
                    <div class="absolute w-full border-t border-dashed border-gray-200" style="top: 50%"></div>
                    <div class="absolute w-full border-t border-dashed border-gray-200" style="top: 75%"></div>
                    <div class="absolute w-full border-t border-gray-100" style="bottom: 0%"></div>
                </div>
                
                <div id="weekly-bars" class="absolute left-12 right-0 top-0 bottom-8 flex items-end justify-around gap-2 overflow-x-auto pb-2">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">Loading chart data...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100" id="summary-stats">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-bold text-gray-800" id="total-apps">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Average</p>
                    <p class="text-lg font-bold text-gray-800" id="avg-apps">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Peak</p>
                    <p class="text-lg font-bold text-gray-800" id="peak-apps">0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Growth</p>
                    <p class="text-lg font-bold text-green-600" id="growth-rate">0%</p>
                </div>
            </div>
        </div>

        <!-- DONUT CHART - Overall -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between w-full mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Application Status</h2>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Live</span>
            </div>

            <div class="relative w-48 h-48 mx-auto mb-6">
                <canvas id="donut-chart" width="192" height="192"></canvas>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-white rounded-full shadow-sm flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-gray-700" id="completion-percentage">0%</span>
                    <span class="text-[10px] text-gray-500">complete</span>
                </div>
            </div>

            <div class="w-full space-y-4 mt-2" id="status-legend"></div>

            <div class="grid grid-cols-2 gap-4 w-full mt-6 pt-4 border-t border-gray-100">
                <div class="text-center bg-orange-50 rounded-lg p-3">
                    <p class="text-xs text-orange-600 font-medium">Total</p>
                    <p class="text-lg font-bold text-gray-800" id="total-all-apps">0</p>
                </div>
                <div class="text-center bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">This Month</p>
                    <p class="text-lg font-bold text-gray-800" id="monthly-apps">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- EXTRA SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- RECENT ACTIVITY -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Recent Activity</h3>
                    <p class="text-xs text-gray-500 mt-1">Latest application updates</p>
                </div>
                <a href="/staff/applications" class="text-sm text-[#155386] hover:text-[#40798C] font-medium">View all</a>
            </div>

            <div id="recent-activity-list" class="space-y-4 text-sm">
                <div class="flex items-center justify-center p-4">
                    <svg class="animate-spin h-5 w-5 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- RECENTLY VERIFIED OWNERSHIP DOCUMENTS SECTION -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Recently Verified Documents</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        @php
                            $position = Auth::user()->profile ? Auth::user()->profile->position : null;
                        @endphp
                        @if($position === 'cpdo')
                            Documents you have verified: <span class="font-medium text-green-600">TCT/Deed of Sale</span>
                        @elseif($position === 'assessor')
                            Documents you have verified: <span class="font-medium text-purple-600">Tax Declaration, TCT/Deed of Sale</span>
                        @elseif($position === 'treasurer')
                            Documents you have verified: <span class="font-medium text-orange-600">Current Tax Receipt, SPA</span>
                        @else
                            No position assigned
                        @endif
                    </p>
                </div>
                <a href="/staff/ownership-verifications/verified" class="text-sm text-[#155386] hover:text-[#40798C] font-medium inline-flex items-center gap-1">
                    View all verified
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div id="verified-ownership-list" class="space-y-4">
                <div class="flex items-center justify-center p-4">
                    <svg class="animate-spin h-5 w-5 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let donutChart = null;
    let currentUserPosition = null;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, loading dashboard data...');
        
        // Get user position
        fetchUserPosition();
        
        // Load dashboard data
        loadDashboardData();
        
        // Add event listener for period change
        document.getElementById('trend-period')?.addEventListener('change', function() {
            loadTrendData();
        });
    });

    async function fetchUserPosition() {
        try {
            const response = await fetch('/staff/position/check');
            if (response.ok) {
                const data = await response.json();
                currentUserPosition = data.position;
                console.log('User position:', currentUserPosition);
            }
        } catch (error) {
            console.error('Error fetching user position:', error);
        }
    }

    async function loadDashboardData() {
        try {
            await loadStats();
            await loadTrendData();
            await loadRecentActivities();
            await loadVerifiedOwnershipDocuments();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    async function loadStats() {
        try {
            const response = await fetch('/staff/applications/stats');
            if (!response.ok) throw new Error('Failed to load stats');
            
            const stats = await response.json();
            
            const statsContainer = document.getElementById('stats-container');
            const lastMonthTotal = stats.last_month_total || 0;
            const thisMonthTotal = stats.this_month_total || 0;
            const growthPercent = lastMonthTotal > 0 
                ? ((thisMonthTotal - lastMonthTotal) / lastMonthTotal * 100).toFixed(1)
                : 0;
            
            statsContainer.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-orange-500 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Applications</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${stats.total || 0}</p>
                            <p class="text-xs ${growthPercent >= 0 ? 'text-green-600' : 'text-red-600'} mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${growthPercent >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'}" />
                                </svg>
                                ${Math.abs(growthPercent)}% from last month
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Pending Review</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${stats.pending || 0}</p>
                            <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ${stats.new_today || 0} new today
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="12" rx="2"/>
                                <path d="M2 20h20"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Completed</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${stats.verified || 0}</p>
                            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ${stats.completion_rate || 0}% completion rate
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="7" r="4"/>
                                <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-purple-500 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">For Release</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${stats.for_release || 0}</p>
                            <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Ready for pickup
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('total-all-apps').textContent = stats.total || 0;
            document.getElementById('monthly-apps').textContent = stats.this_month_total || 0;
            updateDonutChart(stats);
        } catch (error) {
            console.error('Error loading stats:', error);
            document.getElementById('stats-container').innerHTML = `
                <div class="col-span-4 text-center py-8 text-red-500 bg-white rounded-xl">
                    <p>Failed to load statistics</p>
                    <button onclick="loadStats()" class="mt-2 text-sm text-[#155386] hover:underline">Try again</button>
                </div>
            `;
        }
    }

    async function loadTrendData() {
        const period = document.getElementById('trend-period').value;
        const barsContainer = document.getElementById('weekly-bars');
        
        barsContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 mt-2">Loading chart data...</p>
                </div>
            </div>
        `;
        
        try {
            const response = await fetch(`/staff/applications/weekly-trend?period=${period}`);
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            
            const data = await response.json();
            
            let values = [];
            let labels = [];
            
            if (data.values && data.labels) {
                values = data.values;
                labels = data.labels;
            } else if (data.weeks && data.values) {
                labels = data.weeks;
                values = data.values;
            } else if (Array.isArray(data)) {
                values = data.map(d => d.count || 0);
                labels = data.map(d => d.label || d.week || 'Week');
            } else {
                values = [0, 0, 0, 0];
                labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            }
            
            if (values.length === 0 || values.every(v => v === 0)) {
                showErrorState('No data available for this period');
                return;
            }
            
            renderTrendChart(values, labels);
        } catch (error) {
            console.error('Error loading trend data:', error);
            showErrorState('Failed to load chart data');
        }
    }

    function renderTrendChart(values, labels) {
        const barsContainer = document.getElementById('weekly-bars');
        const maxValue = Math.max(...values, 1);
        const gradientColors = ['from-[#155386] to-[#40798C]', 'from-[#40798C] to-[#70A9A1]', 'from-[#70A9A1] to-[#9EC5CB]', 'from-[#9EC5CB] to-[#B8D8E3]'];
        
        let barsHtml = '';
        
        values.forEach((value, index) => {
            const percentage = Math.max(4, (value / maxValue) * 100);
            const colorIndex = index % gradientColors.length;
            const formattedValue = value.toLocaleString();
            const label = labels[index] || `Week ${index + 1}`;
            
            barsHtml += `
                <div class="group relative flex flex-col items-center justify-end h-full flex-1 min-w-[60px]">
                    <div class="relative w-full max-w-[60px] mx-auto">
                        <div class="w-full bg-gradient-to-t ${gradientColors[colorIndex]} rounded-t-lg transition-all duration-300 hover:brightness-110 hover:scale-105 cursor-pointer"
                             style="height: ${percentage}%; min-height: 30px;">
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none z-20">
                                ${formattedValue} applications
                            </div>
                        </div>
                    </div>
                    <span class="mt-2 text-xs font-medium text-gray-600 truncate w-full text-center px-1" title="${label}">
                        ${label.length > 10 ? label.substring(0, 8) + '...' : label}
                    </span>
                    <span class="text-[10px] font-bold text-[#155386]">${value}</span>
                </div>
            `;
        });
        
        barsContainer.innerHTML = barsHtml;
        updateYAxisLabels(maxValue);
        updateSummaryStats(values);
    }

    function updateYAxisLabels(maxValue) {
        const yAxisLabels = document.getElementById('y-axis-labels');
        const labels = [maxValue, Math.ceil(maxValue * 0.75), Math.ceil(maxValue * 0.5), Math.ceil(maxValue * 0.25), 0];
        
        if (yAxisLabels) {
            yAxisLabels.innerHTML = labels.map(label => `<span>${label.toLocaleString()}</span>`).join('');
        }
    }

    function updateSummaryStats(values) {
        const total = values.reduce((a, b) => a + b, 0);
        const avg = values.length > 0 ? Math.round(total / values.length) : 0;
        const peak = Math.max(...values);
        
        const half = Math.floor(values.length / 2);
        const firstHalf = values.slice(0, half);
        const secondHalf = values.slice(half);
        const firstHalfAvg = firstHalf.length > 0 ? firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length : 0;
        const secondHalfAvg = secondHalf.length > 0 ? secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length : 0;
        
        let growth = 0;
        let growthClass = 'text-gray-800';
        
        if (firstHalfAvg > 0) {
            growth = ((secondHalfAvg - firstHalfAvg) / firstHalfAvg * 100);
            growthClass = growth >= 0 ? 'text-green-600' : 'text-red-600';
        }
        
        document.getElementById('total-apps').textContent = total.toLocaleString();
        document.getElementById('avg-apps').textContent = avg.toLocaleString();
        document.getElementById('peak-apps').textContent = peak.toLocaleString();
        
        const growthElement = document.getElementById('growth-rate');
        const growthDisplay = Math.abs(growth).toFixed(1);
        growthElement.textContent = growth > 0 ? `+${growthDisplay}%` : growth < 0 ? `-${growthDisplay}%` : '0%';
        growthElement.className = `text-lg font-bold ${growthClass}`;
    }

    function showErrorState(message) {
        const barsContainer = document.getElementById('weekly-bars');
        barsContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-500">${message}</p>
                    <button onclick="loadTrendData()" class="mt-3 text-xs text-[#155386] hover:underline">Try again</button>
                </div>
            </div>
        `;
        
        document.getElementById('total-apps').textContent = '-';
        document.getElementById('avg-apps').textContent = '-';
        document.getElementById('peak-apps').textContent = '-';
        document.getElementById('growth-rate').textContent = '-';
    }

    function updateDonutChart(stats) {
        const total = stats.total || 0;
        const pending = stats.pending || 0;
        const underReview = stats.under_review || 0;
        const approved = stats.approved || 0;
        const forRelease = stats.for_release || 0;
        const verified = stats.verified || 0;
        const rejected = stats.rejected || 0;
        
        const completedPercent = total > 0 ? (verified / total * 100).toFixed(1) : 0;
        document.getElementById('completion-percentage').textContent = completedPercent + '%';
        
        const ctx = document.getElementById('donut-chart').getContext('2d');
        
        if (donutChart) {
            donutChart.destroy();
        }
        
        donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Under Review', 'Approved', 'For Release', 'Completed', 'Rejected'],
                datasets: [{
                    data: [pending, underReview, approved, forRelease, verified, rejected],
                    backgroundColor: ['#F59E0B', '#8B5CF6', '#10B981', '#3B82F6', '#22C55E', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 10,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        const pendingPercent = total > 0 ? (pending / total * 100).toFixed(1) : 0;
        const underReviewPercent = total > 0 ? (underReview / total * 100).toFixed(1) : 0;
        const approvedPercent = total > 0 ? (approved / total * 100).toFixed(1) : 0;
        const forReleasePercent = total > 0 ? (forRelease / total * 100).toFixed(1) : 0;
        const verifiedPercent = total > 0 ? (verified / total * 100).toFixed(1) : 0;
        const rejectedPercent = total > 0 ? (rejected / total * 100).toFixed(1) : 0;
        
        const legend = document.getElementById('status-legend');
        legend.innerHTML = `
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #F59E0B"></div>
                        <span class="text-sm text-gray-600">Pending</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${pendingPercent}% (${pending})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: ${pendingPercent}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #8B5CF6"></div>
                        <span class="text-sm text-gray-600">Under Review</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${underReviewPercent}% (${underReview})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: ${underReviewPercent}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #10B981"></div>
                        <span class="text-sm text-gray-600">Approved</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${approvedPercent}% (${approved})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: ${approvedPercent}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #3B82F6"></div>
                        <span class="text-sm text-gray-600">For Release</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${forReleasePercent}% (${forRelease})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: ${forReleasePercent}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #22C55E"></div>
                        <span class="text-sm text-gray-600">Completed</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${verifiedPercent}% (${verified})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: ${verifiedPercent}%"></div>
                </div>
            </div>
            ${rejected > 0 ? `
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: #EF4444"></div>
                        <span class="text-sm text-gray-600">Rejected</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${rejectedPercent}% (${rejected})</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-red-500 h-1.5 rounded-full" style="width: ${rejectedPercent}%"></div>
                </div>
            </div>
            ` : ''}
        `;
    }

    async function loadRecentActivities() {
        try {
            const response = await fetch('/staff/applications/recent-activities');
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const activities = await response.json();
            const listContainer = document.getElementById('recent-activity-list');
            
            if (!activities || activities.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">No recent activities</p>
                    </div>
                `;
                return;
            }
            
            const recentActivities = activities.slice(0, 5);
            let activitiesHtml = '';
            
            recentActivities.forEach(activity => {
                const timeAgo = getTimeAgo(activity.created_at);
                const actionDisplay = activity.action_display || activity.action || 'Activity';
                const reviewerName = activity.reviewer_name || 'System';
                const remarks = activity.remarks;
                
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                `;
                
                if (activity.action === 'status_updated') {
                    if (activity.new_status === 'approved') {
                        iconColor = 'bg-green-100 text-green-600';
                        icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
                    } else if (activity.new_status === 'rejected') {
                        iconColor = 'bg-red-100 text-red-600';
                        icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;
                    } else if (activity.new_status === 'for-assessment') {
                        iconColor = 'bg-indigo-100 text-indigo-600';
                        icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>`;
                    }
                } else if (activity.action === 'hard_copy_received') {
                    iconColor = 'bg-indigo-100 text-indigo-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`;
                } else if (activity.action === 'application_submitted') {
                    iconColor = 'bg-emerald-100 text-emerald-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>`;
                } else if (activity.action === 'document_verified') {
                    iconColor = 'bg-green-100 text-green-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                } else if (activity.action === 'note_added') {
                    iconColor = 'bg-yellow-100 text-yellow-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>`;
                } else if (activity.action === 'cpdo_approved') {
                    iconColor = 'bg-cyan-100 text-cyan-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>`;
                } else if (activity.action === 'fsec_uploaded') {
                    iconColor = 'bg-red-100 text-red-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>`;
                }
                
                activitiesHtml += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                                ${icon}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(actionDisplay)}</p>
                                <p class="text-xs text-gray-400">by ${escapeHtml(reviewerName)}</p>
                                ${remarks ? `<p class="text-xs text-gray-500 mt-1 italic">"${escapeHtml(remarks.substring(0, 100))}"</p>` : ''}
                            </div>
                        </div>
                        <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full flex-shrink-0 ml-2">${timeAgo}</span>
                    </div>
                `;
            });
            
            listContainer.innerHTML = activitiesHtml;
            
        } catch (error) {
            console.error('Error loading recent activities:', error);
            document.getElementById('recent-activity-list').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p class="text-sm">Failed to load activities</p>
                    <button onclick="loadRecentActivities()" class="mt-2 text-xs text-[#155386] hover:underline">Try again</button>
                </div>
            `;
        }
    }
    // Load verified ownership documents based on user role
async function loadVerifiedOwnershipDocuments() {
    try {
        const response = await fetch('/staff/ownership-verifications/verified-data');
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        const listContainer = document.getElementById('verified-ownership-list');
        
        if (!data.verifications || data.verifications.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm">No verified documents yet</p>
                    <p class="text-xs text-gray-400 mt-1">Documents you verify will appear here</p>
                </div>
            `;
            return;
        }
        
        // Get only the 5 most recent verifications for dashboard
        const recentVerifications = data.verifications.slice(0, 5);
        let verificationsHtml = '';
        
        recentVerifications.forEach(item => {
            const timeAgo = getTimeAgo(item.verified_at);
            const applicantName = `${item.first_name} ${item.last_name}`;
            const documentType = item.document_type;
            const documentLink = item.document_link;
            const applicationNumber = item.application_number || 'N/A';
            const verifiedByName = item.verified_by_name || 'You';
            
            // Determine document icon and color based on type
            let iconColor = 'bg-green-100 text-green-600';
            let icon = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `;
            
            if (documentType === 'TCT / Deed of Sale') {
                iconColor = 'bg-green-100 text-green-600';
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`;
            } else if (documentType === 'Tax Declaration') {
                iconColor = 'bg-purple-100 text-purple-600';
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>`;
            } else if (documentType === 'Current Tax Receipt') {
                iconColor = 'bg-orange-100 text-orange-600';
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            } else if (documentType === 'Special Power of Attorney (SPA)') {
                iconColor = 'bg-red-100 text-red-600';
                icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h6m-6 3h6m-6 3h6M3 9h6m-6 3h6m-6 3h6m-6 0V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2z" /></svg>`;
            }
            
            verificationsHtml += `
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition group">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                            ${icon}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(applicantName)}</p>
                                <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded-full">${escapeHtml(applicationNumber)}</span>
                            </div>
                            <p class="text-xs text-gray-500">Document: <span class="font-medium">${escapeHtml(documentType)}</span></p>
                            <p class="text-xs text-green-600 mt-1">Verified by ${escapeHtml(verifiedByName)} • ${timeAgo}</p>
                            ${documentLink ? `
                            <div class="mt-1">
                                <a href="${escapeHtml(documentLink)}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    View Document
                                </a>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                    </div>
                </div>
            `;
        });
        
        listContainer.innerHTML = verificationsHtml;
        
    } catch (error) {
        console.error('Error loading verified ownership documents:', error);
        document.getElementById('verified-ownership-list').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <p class="text-sm">Failed to load verified documents</p>
                <button onclick="loadVerifiedOwnershipDocuments()" class="mt-2 text-xs text-[#155386] hover:underline">Try again</button>
            </div>
        `;
    }
}

    // Load verified ownership documents based on user role
    async function loadVerifiedOwnershipDocuments() {
        try {
            const response = await fetch('/staff/ownership-verifications/verified-data');
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            const listContainer = document.getElementById('verified-ownership-list');
            
            if (!data.verifications || data.verifications.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-sm">No verified documents yet</p>
                        <p class="text-xs text-gray-400 mt-1">Documents you verify will appear here</p>
                    </div>
                `;
                return;
            }
            
            const recentVerifications = data.verifications.slice(0, 5);
            let verificationsHtml = '';
            
            recentVerifications.forEach(item => {
                const timeAgo = getTimeAgo(item.verified_at);
                const applicantName = `${item.first_name} ${item.last_name}`;
                const documentType = item.document_type;
                const documentLink = item.document_link;
                const applicationNumber = item.application_number || 'N/A';
                const verifiedByName = item.verified_by_name || 'You';
                
                // Determine document icon and color based on type
                let iconColor = 'bg-green-100 text-green-600';
                let icon = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                `;
                
                if (documentType === 'TCT / Deed of Sale') {
                    iconColor = 'bg-green-100 text-green-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>`;
                } else if (documentType === 'Tax Declaration') {
                    iconColor = 'bg-purple-100 text-purple-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m2 5H7m11-9H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2z" /></svg>`;
                } else if (documentType === 'Current Tax Receipt') {
                    iconColor = 'bg-orange-100 text-orange-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                } else if (documentType === 'Special Power of Attorney (SPA)') {
                    iconColor = 'bg-red-100 text-red-600';
                    icon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h6m-6 3h6m-6 3h6M3 9h6m-6 3h6m-6 3h6m-6 0V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2z" /></svg>`;
                }
                
                verificationsHtml += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                                ${icon}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-medium text-gray-800">${escapeHtml(applicantName)}</p>
                                    <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded-full">${escapeHtml(applicationNumber)}</span>
                                </div>
                                <p class="text-xs text-gray-500">Document: <span class="font-medium">${escapeHtml(documentType)}</span></p>
                                <p class="text-xs text-green-600 mt-1">Verified by ${escapeHtml(verifiedByName)} • ${timeAgo}</p>
                                ${documentLink ? `
                                <div class="mt-1">
                                    <a href="${escapeHtml(documentLink)}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        View Document
                                    </a>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                        </div>
                    </div>
                `;
            });
            
            listContainer.innerHTML = verificationsHtml;
            
        } catch (error) {
            console.error('Error loading verified ownership documents:', error);
            document.getElementById('verified-ownership-list').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p class="text-sm">Failed to load verified documents</p>
                    <button onclick="loadVerifiedOwnershipDocuments()" class="mt-2 text-xs text-[#155386] hover:underline">Try again</button>
                </div>
            `;
        }
    }

    function getTimeAgo(dateString) {
        if (!dateString) return 'unknown';
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' mins ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
        return date.toLocaleDateString();
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Export dropdown toggle
    function toggleExportDropdown() {
        const dropdown = document.getElementById('export-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('export-dropdown');
        const button = event.target.closest('.relative');
        if (!button && dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('export-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>

<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
    
    .group-hover\:scale-105:hover {
        transform: scale(1.05);
    }
    
    .group-hover\:scale-110:hover {
        transform: scale(1.1);
    }
</style>
@endsection