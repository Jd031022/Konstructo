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
        
        <!-- Selected Date Display -->
        <div class="relative">
            <button onclick="toggleCalendarDropdown()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span id="selected-date-display" class="font-medium">April 2026</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <!-- Calendar Dropdown -->
            <div id="calendar-dropdown" class="hidden absolute right-0 mt-2 w-[340px] bg-white rounded-2xl border border-gray-200 shadow-xl z-50">
                <div class="p-5">
                    <!-- Calendar Header - Only visible in Daily mode -->
                    <div id="calendar-header" class="flex items-center justify-between mb-6">
                        <button onclick="changeCalendarMonth(-1)" class="w-9 h-9 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <h2 id="calendar-month-year" class="text-xl font-bold text-gray-800">April 2026</h2>
                        <button onclick="changeCalendarMonth(1)" class="w-9 h-9 rounded-lg hover:bg-gray-100 flex items-center justify-center transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Weekdays - Only visible in Daily mode -->
                    <div id="weekdays-header" class="grid grid-cols-7 text-center text-gray-400 text-xs font-medium mb-3">
                        <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                    </div>
                    
                    <!-- Calendar Days - Only visible in Daily mode -->
                    <div id="calendar-days-grid" class="grid grid-cols-7 gap-1 text-center text-sm">
                        <!-- Populated by JS -->
                    </div>
                    
                    <!-- Month Selection for Monthly View -->
                    <div id="month-selector" class="hidden">
                        <div class="grid grid-cols-3 gap-2 mt-4">
                            <button onclick="selectMonth(0)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">January</button>
                            <button onclick="selectMonth(1)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">February</button>
                            <button onclick="selectMonth(2)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">March</button>
                            <button onclick="selectMonth(3)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">April</button>
                            <button onclick="selectMonth(4)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">May</button>
                            <button onclick="selectMonth(5)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">June</button>
                            <button onclick="selectMonth(6)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">July</button>
                            <button onclick="selectMonth(7)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">August</button>
                            <button onclick="selectMonth(8)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">September</button>
                            <button onclick="selectMonth(9)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">October</button>
                            <button onclick="selectMonth(10)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">November</button>
                            <button onclick="selectMonth(11)" class="month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">December</button>
                        </div>
                    </div>
                    
                    <!-- Year Selection for Yearly View -->
                    <div id="year-selector" class="hidden">
                        <div class="grid grid-cols-3 gap-2 mt-4">
                            <button onclick="selectYear(2023)" class="year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">2023</button>
                            <button onclick="selectYear(2024)" class="year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">2024</button>
                            <button onclick="selectYear(2025)" class="year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">2025</button>
                            <button onclick="selectYear(2026)" class="year-option px-3 py-2 text-sm rounded-lg bg-[#155386] text-white">2026</button>
                            <button onclick="selectYear(2027)" class="year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">2027</button>
                            <button onclick="selectYear(2028)" class="year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition">2028</button>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="grid grid-cols-3 gap-2 mt-6">
                        <button onclick="applyDailyFilter()" id="daily-apply-btn" class="bg-white border border-gray-200 py-2.5 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition">
                            Daily
                        </button>
                        <button onclick="applyMonthlyFilter()" id="monthly-apply-btn" class="bg-[#155386] text-white py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition">
                            Monthly
                        </button>
                        <button onclick="applyYearlyFilter()" id="yearly-apply-btn" class="bg-white border border-gray-200 py-2.5 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition">
                            Yearly
                        </button>
                    </div>
                </div>
            </div>
        </div>
        

        
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

                <!-- Show user's position -->
        @if(Auth::user()->profile && Auth::user()->profile->position)
        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#155386] rounded-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', Auth::user()->profile->position)) }}</span>
        </span>
        @endif
    </div>
</div>

<!-- DATE RANGE INDICATOR - RIGHT ALIGNED -->
<div class="mb-4 flex items-center justify-end gap-2" id="date-range-indicator">
    <span class="text-xs text-gray-500">Showing data for:</span>
    <span class="text-sm font-semibold text-[#155386] bg-blue-50 px-3 py-1 rounded-full" id="current-date-range">April 2026</span>
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
    <!-- CHART AREA -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800" id="chart-title">Monthly Application Trend</h2>
                <p class="text-xs text-gray-500 mt-1" id="chart-subtitle">Application volume by day</p>
            </div>
            <div class="relative">
                <select id="trend-period" class="appearance-none border border-gray-200 rounded-lg text-sm px-4 py-2.5 pr-8 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month" selected>This Month</option>
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

    <!-- CITIZEN SATISFACTION SECTION -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Citizen Satisfaction</h3>
            <p class="text-xs text-gray-500 mt-1">Client feedback and service quality metrics</p>
        </div>
        <a href="/staff/surveys" class="text-sm text-[#155386] hover:text-[#40798C] font-medium inline-flex items-center gap-1">
            View all surveys
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <!-- Main Satisfaction Score -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-[#155386] to-[#40798C] text-white mb-3">
            <span class="text-3xl font-bold" id="sat-avg-rating">0.0</span>
            <span class="text-sm ml-0.5">/5</span>
        </div>
        <div class="flex items-center justify-center gap-1 mb-1" id="sat-stars">
            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <p class="text-xs text-gray-500"><span id="sat-total-surveys">0</span> survey responses</p>
    </div>

    <!-- Two Column Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-green-700" id="sat-response-rate">0%</p>
            <p class="text-xs text-gray-600">Response Rate</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-blue-700" id="cpdo-avg-rating">0.0</p>
            <p class="text-xs text-gray-600">CPDO Service Rating</p>
            <div class="flex items-center justify-center gap-0.5 mt-1" id="cpdo-stars">
                <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
        </div>
    </div>

    <!-- Service Quality Metrics -->
    <div>
        <p class="text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wide">Service Quality</p>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-600">Processing Time</span>
                    <span class="font-medium text-gray-800" id="sqd-proc-time">0</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" id="proc-time-bar" style="width: 0%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-600">Staff Responsiveness</span>
                    <span class="font-medium text-gray-800" id="sqd-responsiveness">0</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" id="responsiveness-bar" style="width: 0%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-600">Clarity & Fairness</span>
                    <span class="font-medium text-gray-800" id="sqd-clarity">0</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" id="clarity-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-100 my-4"></div>

    <!-- Rating Distribution -->
    <div>
        <p class="text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wide">Rating Breakdown</p>
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-12 text-sm font-medium text-green-600">5 ★</div>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" id="dist-5" style="width: 0%"></div>
                </div>
                <div class="w-10 text-xs text-right text-gray-600" id="dist-5-val">0%</div>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-12 text-sm font-medium text-blue-600">4 ★</div>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" id="dist-4" style="width: 0%"></div>
                </div>
                <div class="w-10 text-xs text-right text-gray-600" id="dist-4-val">0%</div>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-12 text-sm font-medium text-yellow-600">3 ★</div>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" id="dist-3" style="width: 0%"></div>
                </div>
                <div class="w-10 text-xs text-right text-gray-600" id="dist-3-val">0%</div>
            </div>
            <div class="flex items-center gap-2 opacity-60">
                <div class="w-12 text-sm font-medium text-orange-600">2 ★</div>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full transition-all duration-300" id="dist-2" style="width: 0%"></div>
                </div>
                <div class="w-10 text-xs text-right text-gray-600" id="dist-2-val">0%</div>
            </div>
            <div class="flex items-center gap-2 opacity-60">
                <div class="w-12 text-sm font-medium text-red-600">1 ★</div>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full transition-all duration-300" id="dist-1" style="width: 0%"></div>
                </div>
                <div class="w-10 text-xs text-right text-gray-600" id="dist-1-val">0%</div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <div class="bg-white rounded-xl shadow-sm p-6 h-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Document Verification Queue</h2>
            <a href="/staff/applications" class="text-sm text-[#155386] hover:underline font-medium">View All →</a>
        </div>

        <div class="space-y-4" id="verification-queue">
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span id="pending-queue-count" class="text-yellow-600 font-bold text-sm">0</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Pending Document Verification</p>
                        <p class="text-xs text-gray-500">Applications awaiting review</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-yellow-600" id="pending-queue-count-value">0</span>
            </div>

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
                        <p class="text-xs text-gray-500">Being checked by staff</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-blue-600" id="under-review-queue-count">0</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Ready for Release</p>
                        <p class="text-xs text-gray-500">Approved documents ready next</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-green-600" id="for-release-queue-count">0</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Rejected</p>
                        <p class="text-xs text-gray-500">Applications not accepted</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-red-600" id="rejected-queue-count">0</span>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-gray-600">Average Processing Time</span>
                </div>
                <span id="doc-avg-processing-time" class="text-lg font-semibold text-gray-800">0 days</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 h-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Staff Performance</h2>
            <a href="/staff/applications" class="text-sm text-[#155386] hover:underline font-medium">View All →</a>
        </div>

        <div id="staff-performance" class="space-y-3">
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-xs font-bold">ST</div>
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
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let donutChart = null;
    let currentUserPosition = null;
    
    // Filter state - initialize with monthly (default)
    let currentFilterType = 'monthly';
    let selectedDate = new Date();
    let selectedMonth = new Date().getMonth();
    let selectedYear = new Date().getFullYear();
    
    // Calendar state
    let calendarCurrentYear = new Date().getFullYear();
    let calendarCurrentMonth = new Date().getMonth();

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, loading dashboard data...');
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const currentMonthName = monthNames[selectedMonth];
        const currentYear = selectedYear;
        
        // Set default display to monthly
        document.getElementById('selected-date-display').textContent = `${currentMonthName} ${currentYear}`;
        document.getElementById('current-date-range').textContent = `${currentMonthName} ${currentYear}`;
        document.getElementById('chart-title').textContent = 'Monthly Application Trend';
        document.getElementById('chart-subtitle').textContent = `Applications for ${currentMonthName} ${currentYear}`;
        
        fetchUserPosition();
        loadDashboardData();
        
        document.getElementById('trend-period')?.addEventListener('change', function() {
            loadTrendData();
        });
        
        updateCalendarDisplay();
    });
    
    function toggleCalendarDropdown() {
        const dropdown = document.getElementById('calendar-dropdown');
        dropdown.classList.toggle('hidden');
        
        if (!dropdown.classList.contains('hidden')) {
            updateCalendarForFilterType();
        }
    }
    
    function updateCalendarForFilterType() {
        const calendarHeader = document.getElementById('calendar-header');
        const weekdaysHeader = document.getElementById('weekdays-header');
        const calendarGrid = document.getElementById('calendar-days-grid');
        const monthSelector = document.getElementById('month-selector');
        const yearSelector = document.getElementById('year-selector');
        const dailyApplyBtn = document.getElementById('daily-apply-btn');
        const monthlyApplyBtn = document.getElementById('monthly-apply-btn');
        const yearlyApplyBtn = document.getElementById('yearly-apply-btn');
        
        // Reset button styles
        dailyApplyBtn.className = 'bg-white border border-gray-200 py-2.5 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition';
        monthlyApplyBtn.className = 'bg-white border border-gray-200 py-2.5 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition';
        yearlyApplyBtn.className = 'bg-white border border-gray-200 py-2.5 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition';
        
        if (currentFilterType === 'daily') {
            dailyApplyBtn.className = 'bg-[#155386] text-white py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition';
            calendarHeader.classList.remove('hidden');
            weekdaysHeader.classList.remove('hidden');
            calendarGrid.classList.remove('hidden');
            monthSelector.classList.add('hidden');
            yearSelector.classList.add('hidden');
            updateCalendarDisplay();
        } else if (currentFilterType === 'monthly') {
            monthlyApplyBtn.className = 'bg-[#155386] text-white py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition';
            calendarHeader.classList.add('hidden');
            weekdaysHeader.classList.add('hidden');
            calendarGrid.classList.add('hidden');
            monthSelector.classList.remove('hidden');
            yearSelector.classList.add('hidden');
            highlightSelectedMonth();
        } else if (currentFilterType === 'yearly') {
            yearlyApplyBtn.className = 'bg-[#155386] text-white py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition';
            calendarHeader.classList.add('hidden');
            weekdaysHeader.classList.add('hidden');
            calendarGrid.classList.add('hidden');
            monthSelector.classList.add('hidden');
            yearSelector.classList.remove('hidden');
            highlightSelectedYear();
        }
    }
    
    function updateCalendarDisplay() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('calendar-month-year').textContent = `${monthNames[calendarCurrentMonth]} ${calendarCurrentYear}`;
        
        const firstDayOfMonth = new Date(calendarCurrentYear, calendarCurrentMonth, 1);
        const startingDayOfWeek = firstDayOfMonth.getDay();
        const daysInMonth = new Date(calendarCurrentYear, calendarCurrentMonth + 1, 0).getDate();
        
        let daysHtml = '';
        
        for (let i = 0; i < startingDayOfWeek; i++) {
            daysHtml += `<div class="h-10"></div>`;
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const isSelected = (currentFilterType === 'daily' && 
                                selectedDate.getDate() === day && 
                                selectedDate.getMonth() === calendarCurrentMonth && 
                                selectedDate.getFullYear() === calendarCurrentYear);
            
            const selectedClass = isSelected ? 'bg-teal-700 text-white font-semibold shadow' : 'hover:bg-gray-100';
            
            daysHtml += `
                <div onclick="selectCalendarDay(${day})" 
                     class="h-10 w-10 mx-auto flex items-center justify-center rounded-xl cursor-pointer transition ${selectedClass}">
                    ${day}
                </div>
            `;
        }
        
        document.getElementById('calendar-days-grid').innerHTML = daysHtml;
    }
    
    function selectCalendarDay(day) {
        selectedDate = new Date(calendarCurrentYear, calendarCurrentMonth, day);
        updateCalendarDisplay();
    }
    
    function changeCalendarMonth(delta) {
        calendarCurrentMonth += delta;
        
        if (calendarCurrentMonth < 0) {
            calendarCurrentMonth = 11;
            calendarCurrentYear--;
        } else if (calendarCurrentMonth > 11) {
            calendarCurrentMonth = 0;
            calendarCurrentYear++;
        }
        
        updateCalendarDisplay();
    }
    
    function selectMonth(monthIndex) {
        selectedMonth = monthIndex;
        highlightSelectedMonth();
    }
    
    function highlightSelectedMonth() {
        document.querySelectorAll('.month-option').forEach((btn, index) => {
            if (index === selectedMonth) {
                btn.className = 'month-option px-3 py-2 text-sm rounded-lg bg-[#155386] text-white transition';
            } else {
                btn.className = 'month-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition';
            }
        });
    }
    
    function selectYear(year) {
        selectedYear = year;
        highlightSelectedYear();
    }
    
    function highlightSelectedYear() {
        document.querySelectorAll('.year-option').forEach(btn => {
            if (parseInt(btn.textContent) === selectedYear) {
                btn.className = 'year-option px-3 py-2 text-sm rounded-lg bg-[#155386] text-white transition';
            } else {
                btn.className = 'year-option px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition';
            }
        });
    }
    
    function applyDailyFilter() {
        currentFilterType = 'daily';
        updateCalendarForFilterType();
        document.getElementById('calendar-dropdown').classList.add('hidden');
        
        const formattedDate = selectedDate.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
        document.getElementById('selected-date-display').textContent = formattedDate;
        document.getElementById('current-date-range').textContent = formattedDate;
        document.getElementById('chart-title').textContent = 'Daily Application Trend';
        document.getElementById('chart-subtitle').textContent = `Applications for ${formattedDate}`;
        
        loadStats();
        loadTrendData();
    }
    
    function applyMonthlyFilter() {
        currentFilterType = 'monthly';
        updateCalendarForFilterType();
        document.getElementById('calendar-dropdown').classList.add('hidden');
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const monthName = monthNames[selectedMonth];
        
        document.getElementById('selected-date-display').textContent = `${monthName} ${selectedYear}`;
        document.getElementById('current-date-range').textContent = `${monthName} ${selectedYear}`;
        document.getElementById('chart-title').textContent = 'Monthly Application Trend';
        document.getElementById('chart-subtitle').textContent = `Applications for ${monthName} ${selectedYear}`;
        
        loadStats();
        loadTrendData();
    }
    
    function applyYearlyFilter() {
        currentFilterType = 'yearly';
        updateCalendarForFilterType();
        document.getElementById('calendar-dropdown').classList.add('hidden');
        
        document.getElementById('selected-date-display').textContent = selectedYear.toString();
        document.getElementById('current-date-range').textContent = selectedYear.toString();
        document.getElementById('chart-title').textContent = 'Yearly Application Trend';
        document.getElementById('chart-subtitle').textContent = `Applications for ${selectedYear}`;
        
        loadStats();
        loadTrendData();
    }

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
            await loadCitizenSatisfaction();
            await loadStaffPerformance();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    async function loadStats() {
        try {
            let url = `/staff/applications/stats?filter=${currentFilterType}`;
            
            if (currentFilterType === 'daily') {
                const year = selectedDate.getFullYear();
                const month = selectedDate.getMonth() + 1;
                const day = selectedDate.getDate();
                url += `&year=${year}&month=${month}&day=${day}`;
            } else if (currentFilterType === 'monthly') {
                url += `&year=${selectedYear}&month=${selectedMonth + 1}`;
            } else if (currentFilterType === 'yearly') {
                url += `&year=${selectedYear}`;
            }
            
            const response = await fetch(url);
            if (!response.ok) throw new Error('Failed to load stats');
            
            const stats = await response.json();
            
            const statsContainer = document.getElementById('stats-container');
            const lastPeriodTotal = stats.last_period_total || 0;
            const thisPeriodTotal = stats.this_period_total || 0;
            const growthPercent = lastPeriodTotal > 0 
                ? ((thisPeriodTotal - lastPeriodTotal) / lastPeriodTotal * 100).toFixed(1)
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
                                ${Math.abs(growthPercent)}% from last period
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
                                ${stats.new_today || 0} new
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
            updateVerificationQueue(stats);
        } catch (error) {
            console.error('Error loading stats:', error);
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
            let url = `/staff/applications/trend-data?period=${period}&filter=${currentFilterType}`;
            
            if (currentFilterType === 'daily') {
                const year = selectedDate.getFullYear();
                const month = selectedDate.getMonth() + 1;
                const day = selectedDate.getDate();
                url += `&year=${year}&month=${month}&day=${day}`;
            } else if (currentFilterType === 'monthly') {
                url += `&year=${selectedYear}&month=${selectedMonth + 1}`;
            } else if (currentFilterType === 'yearly') {
                url += `&year=${selectedYear}`;
            }
            
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            
            const data = await response.json();
            
            let values = [];
            let labels = [];
            
            if (data.values && data.labels) {
                values = data.values;
                labels = data.labels;
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
            const label = labels[index] || `Item ${index + 1}`;
            
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
                    legend: { display: false },
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
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #F59E0B"></div><span class="text-sm text-gray-600">Pending</span></div><span class="text-sm font-bold text-gray-700">${pendingPercent}% (${pending})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-amber-500 h-1.5 rounded-full" style="width: ${pendingPercent}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #8B5CF6"></div><span class="text-sm text-gray-600">Under Review</span></div><span class="text-sm font-bold text-gray-700">${underReviewPercent}% (${underReview})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-purple-500 h-1.5 rounded-full" style="width: ${underReviewPercent}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #10B981"></div><span class="text-sm text-gray-600">Approved</span></div><span class="text-sm font-bold text-gray-700">${approvedPercent}% (${approved})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: ${approvedPercent}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #3B82F6"></div><span class="text-sm text-gray-600">For Release</span></div><span class="text-sm font-bold text-gray-700">${forReleasePercent}% (${forRelease})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-blue-500 h-1.5 rounded-full" style="width: ${forReleasePercent}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #22C55E"></div><span class="text-sm text-gray-600">Completed</span></div><span class="text-sm font-bold text-gray-700">${verifiedPercent}% (${verified})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width: ${verifiedPercent}%"></div></div></div>
            ${rejected > 0 ? `<div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background-color: #EF4444"></div><span class="text-sm text-gray-600">Rejected</span></div><span class="text-sm font-bold text-gray-700">${rejectedPercent}% (${rejected})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-red-500 h-1.5 rounded-full" style="width: ${rejectedPercent}%"></div></div></div>` : ''}
        `;
    }

    async function loadRecentActivities() {
        try {
            const response = await fetch('/staff/applications/recent-activities');
            if (!response.ok) throw new Error('Network response was not ok');
            
            const activities = await response.json();
            const listContainer = document.getElementById('recent-activity-list');
            
            if (!activities || activities.length === 0) {
                listContainer.innerHTML = `<div class="text-center py-8 text-gray-500"><svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><p class="text-sm">No recent activities</p></div>`;
                return;
            }
            
            const recentActivities = activities.slice(0, 6);
            let activitiesHtml = '';
            
            recentActivities.forEach(activity => {
                const timeAgo = getTimeAgo(activity.created_at);
                const actionDisplay = activity.action_display || activity.action || 'Activity';
                const reviewerName = activity.reviewer_name || 'System';
                const remarks = activity.remarks;
                
                activitiesHtml += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
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
            document.getElementById('recent-activity-list').innerHTML = `<div class="text-center py-8 text-red-500"><p class="text-sm">Failed to load activities</p><button onclick="loadRecentActivities()" class="mt-2 text-xs text-[#155386] hover:underline">Try again</button></div>`;
        }
    }
    
    async function loadVerifiedOwnershipDocuments() {
        try {
            const response = await fetch('/staff/ownership-verifications/verified-data');
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const listContainer = document.getElementById('verified-ownership-list');
            
            if (!data.verifications || data.verifications.length === 0) {
                listContainer.innerHTML = `<div class="text-center py-8 text-gray-500"><svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg><p class="text-sm">No verified documents yet</p></div>`;
                return;
            }
            
            const recentVerifications = data.verifications.slice(0, 5);
            let verificationsHtml = '';
            
            recentVerifications.forEach(item => {
                const timeAgo = getTimeAgo(item.verified_at);
                const applicantName = `${item.first_name} ${item.last_name}`;
                const documentType = item.document_type;
                const verifiedByName = item.verified_by_name || 'You';
                
                verificationsHtml += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(applicantName)}</p>
                                <p class="text-xs text-gray-500">${escapeHtml(documentType)}</p>
                                <p class="text-xs text-green-600 mt-1">Verified by ${escapeHtml(verifiedByName)} • ${timeAgo}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-600 rounded-full">Verified</span>
                    </div>
                `;
            });
            
            listContainer.innerHTML = verificationsHtml;
        } catch (error) {
            console.error('Error loading verified documents:', error);
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

    function toggleExportDropdown() {
        const dropdown = document.getElementById('export-dropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('export-dropdown');
        const calendarDropdown = document.getElementById('calendar-dropdown');
        
        if (!event.target.closest('.relative') && dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
        
        if (!event.target.closest('#calendar-dropdown') && !event.target.closest('[onclick="toggleCalendarDropdown()"]')) {
            if (calendarDropdown && !calendarDropdown.classList.contains('hidden')) {
                calendarDropdown.classList.add('hidden');
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('export-dropdown');
            const calendarDropdown = document.getElementById('calendar-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) dropdown.classList.add('hidden');
            if (calendarDropdown && !calendarDropdown.classList.contains('hidden')) calendarDropdown.classList.add('hidden');
        }
    });

   async function loadCitizenSatisfaction() {
    try {
        // Load satisfaction survey stats
        const surveyResponse = await fetch('/staff/surveys/data?period=this_year&page=1&per_page=1');
        const surveyData = await surveyResponse.json();
        
        if (surveyData.success && surveyData.stats) {
            const stats = surveyData.stats;
            const avgRating = stats.avg_rating || 0;
            const responseRate = stats.response_rate || 0;
            const totalSurveys = stats.total || 0;
            const distribution = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
            const total = distribution[1] + distribution[2] + distribution[3] + distribution[4] + distribution[5];
            
            // Update UI
            document.getElementById('sat-avg-rating').textContent = avgRating.toFixed(1);
            document.getElementById('sat-response-rate').textContent = Math.round(responseRate);
            document.getElementById('sat-total-surveys').textContent = totalSurveys;
            
            // Update stars
            const fullStars = Math.floor(avgRating);
            const starsContainer = document.getElementById('sat-stars');
            starsContainer.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                star.setAttribute('class', `w-5 h-5 ${i <= fullStars ? 'text-yellow-400' : 'text-gray-300'}`);
                star.setAttribute('fill', 'currentColor');
                star.setAttribute('viewBox', '0 0 20 20');
                star.innerHTML = '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>';
                starsContainer.appendChild(star);
            }
            
            // Update distribution percentages
            const getPercent = (val) => total > 0 ? ((val / total) * 100).toFixed(0) : 0;
            document.getElementById('dist-5').style.width = getPercent(distribution[5]) + '%';
            document.getElementById('dist-5-val').textContent = getPercent(distribution[5]) + '%';
            document.getElementById('dist-4').style.width = getPercent(distribution[4]) + '%';
            document.getElementById('dist-4-val').textContent = getPercent(distribution[4]) + '%';
            document.getElementById('dist-3').style.width = getPercent(distribution[3]) + '%';
            document.getElementById('dist-3-val').textContent = getPercent(distribution[3]) + '%';
            document.getElementById('dist-2').style.width = getPercent(distribution[2]) + '%';
            document.getElementById('dist-2-val').textContent = getPercent(distribution[2]) + '%';
            document.getElementById('dist-1').style.width = getPercent(distribution[1]) + '%';
            document.getElementById('dist-1-val').textContent = getPercent(distribution[1]) + '%';
        }
        
        // Load CPDO ratings stats
        const cpdoResponse = await fetch('/staff/cpdo-ratings/stats');
        const cpdoData = await cpdoResponse.json();
        
        if (cpdoData.success && cpdoData.data) {
            const cpdoStats = cpdoData.data;
            const avgCpdo = cpdoStats.avg_rating || 0;
            const metrics = cpdoStats.metrics_scores || { processing_time: 0, responsiveness: 0, clarity: 0, fairness: 0, overall: 0 };
            
            document.getElementById('cpdo-avg-rating').textContent = avgCpdo.toFixed(1);
            
            // Update stars for CPDO
            const cpdoFullStars = Math.floor(avgCpdo);
            const cpdoStarsContainer = document.getElementById('cpdo-stars');
            cpdoStarsContainer.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                star.setAttribute('class', `w-3 h-3 ${i <= cpdoFullStars ? 'text-yellow-400' : 'text-gray-300'}`);
                star.setAttribute('fill', 'currentColor');
                star.setAttribute('viewBox', '0 0 20 20');
                star.innerHTML = '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>';
                cpdoStarsContainer.appendChild(star);
            }
            
            // Update SQD metrics (1-5 scale)
            const procTime = metrics.processing_time || 0;
            const responsiveness = metrics.responsiveness || 0;
            const clarityFairness = ((metrics.clarity || 0) + (metrics.fairness || 0)) / 2;
            
            document.getElementById('sqd-proc-time').textContent = procTime.toFixed(1);
            document.getElementById('sqd-responsiveness').textContent = responsiveness.toFixed(1);
            document.getElementById('sqd-clarity').textContent = clarityFairness.toFixed(1);
            
            document.getElementById('proc-time-bar').style.width = (procTime / 5 * 100) + '%';
            document.getElementById('responsiveness-bar').style.width = (responsiveness / 5 * 100) + '%';
            document.getElementById('clarity-bar').style.width = (clarityFairness / 5 * 100) + '%';
        }
    } catch (error) {
        console.error('Error loading citizen satisfaction:', error);
    }
}

    function updateVerificationQueue(stats) {
        const pending = stats.pending || 0;
        const underReview = stats.under_review || 0;
        const forRelease = stats.for_release || 0;
        const rejected = stats.rejected || 0;
        const avgProcessing = stats.avg_processing_time || '0 days';

        document.getElementById('pending-queue-count').textContent = pending;
        document.getElementById('pending-queue-count-value').textContent = pending;
        document.getElementById('under-review-queue-count').textContent = underReview;
        document.getElementById('for-release-queue-count').textContent = forRelease;
        document.getElementById('rejected-queue-count').textContent = rejected;
        document.getElementById('doc-avg-processing-time').textContent = avgProcessing;
    }

    async function loadStaffPerformance() {
        try {
            const response = await fetch('/staff/dashboard/staff-performance');
            if (!response.ok) throw new Error('Failed to load staff performance');
            const data = await response.json();

            const container = document.getElementById('staff-performance');
            if (!data.success || !Array.isArray(data.staff) || data.staff.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-gray-500">No staff performance data available</div>';
                document.getElementById('total-processed').textContent = '0';
                document.getElementById('avg-per-staff').textContent = '0';
                document.getElementById('processed-trend').textContent = '0%';
                document.getElementById('avg-trend').textContent = '0%';
                return;
            }

            let html = '';
            const gradients = ['from-[#155386] to-[#40798C]', 'from-[#40798C] to-[#70A9A1]', 'from-[#70A9A1] to-[#9EC5CB]', 'from-[#9EC5CB] to-[#B8D8E3]'];
            data.staff.slice(0, 5).forEach((staff, idx) => {
                const initials = `${staff.first_name?.[0] || ''}${staff.last_name?.[0] || ''}`.toUpperCase() || 'ST';
                const gradient = gradients[idx % gradients.length];
                html += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r ${gradient} rounded-full flex items-center justify-center text-white text-xs font-bold">${initials}</div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${staff.first_name || ''} ${staff.last_name || ''}</p>
                                <p class="text-xs text-gray-500">${staff.position || staff.role || 'Staff'}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-800">${staff.processed || 0}</p>
                            <p class="text-xs text-gray-400">this week</p>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            document.getElementById('total-processed').textContent = data.total_processed || 0;
            document.getElementById('avg-per-staff').textContent = data.avg_per_staff || 0;
            document.getElementById('processed-trend').textContent = data.processed_trend >= 0 ? `↑ ${data.processed_trend}%` : `↓ ${Math.abs(data.processed_trend)}%`;
            document.getElementById('avg-trend').textContent = data.avg_trend >= 0 ? `↑ ${data.avg_trend}%` : `↓ ${Math.abs(data.avg_trend)}%`;
        } catch (error) {
            console.error('Error loading staff performance:', error);
            document.getElementById('staff-performance').innerHTML = '<div class="text-center py-8 text-red-500">Unable to load staff performance</div>';
        }
    }


</script>
<style>
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .group:hover .group-hover\:opacity-100 { opacity: 1; }
</style>
@endsection