@extends('layouts.dashboard')

@section('title', 'Client Satisfaction Surveys')

@section('content')
<div id="survey-report-content" class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Client Satisfaction Surveys</h1>
            <p class="text-sm text-gray-500 mt-1">View and analyze client feedback and satisfaction metrics</p>
        </div>
        
        <!-- EXPORT DROPDOWN -->
        <div class="relative inline-block mt-4 md:mt-0">
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
                <button onclick="exportSurveys('csv')" 
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                    <svg class="inline w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export as CSV
                </button>
                <button onclick="exportToPDF()" 
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg">
                    <svg class="inline w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       id="searchInput"
                       placeholder="Search by application number or applicant name..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
            </div>
            
            <!-- Date From -->
            <div>
                <input type="date" id="dateFromInput" placeholder="Date From" 
                       class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white w-full sm:w-auto">
            </div>
            
            <!-- Date To -->
            <div>
                <input type="date" id="dateToInput" placeholder="Date To" 
                       class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white w-full sm:w-auto">
            </div>
            
            <!-- Client Type Filter -->
            <select id="clientTypeInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                <option value="">All Types</option>
                <option value="citizen">Citizen</option>
                <option value="business">Business</option>
                <option value="government">Government</option>
            </select>
            
            <!-- Sex Filter -->
            <select id="sexInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[120px]">
                <option value="">All Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            
            <!-- Apply Filters Button -->
            <button id="applyFiltersBtn" class="px-6 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition font-medium text-sm">
                Apply Filters
            </button>
            
            <!-- Reset Button -->
            <button id="clearFiltersBtn" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                Reset
            </button>
        </div>
    </div>

    <!-- STATS CARDS -->
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

    <!-- MAIN GRID - Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Satisfaction Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Satisfaction Trend</h2>
                    <p class="text-xs text-gray-500 mt-1">Average satisfaction rating over time</p>
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

            <!-- Line Chart Container -->
            <div class="relative h-80">
                <canvas id="satisfactionChart"></canvas>
                <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 hidden">
                    <svg class="animate-spin h-8 w-8 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Average Rating</p>
                    <p class="text-lg font-bold text-gray-800" id="avg-rating">0.0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Highest Rating</p>
                    <p class="text-lg font-bold text-green-600" id="highest-rating">0.0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Lowest Rating</p>
                    <p class="text-lg font-bold text-red-600" id="lowest-rating">0.0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Response Rate</p>
                    <p class="text-lg font-bold text-blue-600" id="response-rate">0%</p>
                </div>
            </div>
        </div>

        <!-- Rating Distribution Donut Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between w-full mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Rating Distribution</h2>
                    <p class="text-xs text-gray-500 mt-1">Satisfaction rating breakdown</p>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Overall</span>
            </div>

            <!-- Donut Chart Container -->
            <div class="relative w-48 h-48 mx-auto mb-6">
                <canvas id="ratingDonutChart" width="192" height="192"></canvas>
            </div>

            <!-- Legend with progress bars -->
            <div class="w-full space-y-4 mt-4" id="rating-legend"></div>
        </div>
    </div>

    <!-- SECOND ROW - Demographics & Service Quality -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Client Type Distribution -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between w-full mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Client Distribution</h2>
                    <p class="text-xs text-gray-500 mt-1">By client type</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="clientTypeChart"></canvas>
            </div>
        </div>

        <!-- Service Quality Radar Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between w-full mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Service Quality Metrics</h2>
                    <p class="text-xs text-gray-500 mt-1">Average scores by dimension</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="sqdRadarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Surveys Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-900">Survey Responses</h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Show</span>
                <select id="perPageSelect" class="border border-gray-300 rounded-lg text-sm px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#155386]">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-500">entries</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Survey Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="surveysTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin h-8 w-8 text-[#155386] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p>Loading surveys...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="text-sm text-gray-700" id="paginationInfo">
                    Showing 0 to 0 of 0 entries
                </div>
                <div class="flex space-x-2" id="paginationControls"></div>
            </div>
        </div>
    </div>
</div>

<!-- Survey Details Modal -->
<div id="surveyModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Survey Details</h3>
                        <p class="text-sm opacity-90">Client Satisfaction Survey Response</p>
                    </div>
                    <button id="closeModalBtn" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 max-h-[70vh] overflow-y-auto" id="modalContent">
                    <!-- Content loaded dynamically -->
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button id="closeModalFooterBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let satisfactionChart = null;
    let ratingDonutChart = null;
    let clientTypeChart = null;
    let sqdRadarChart = null;
    
    let currentPage = 1;
    let currentFilters = {};
    let currentPerPage = 15;
    let surveysData = [];
    let statisticsData = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadSurveys();
        loadStatistics();
        
        document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
        document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);
        document.getElementById('perPageSelect').addEventListener('change', changePerPage);
        document.getElementById('trend-period').addEventListener('change', loadStatistics);
        
        // Close modal buttons
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeModalFooterBtn = document.getElementById('closeModalFooterBtn');
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (closeModalFooterBtn) closeModalFooterBtn.addEventListener('click', closeModal);
        
        document.getElementById('surveyModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        
        // Check if html2pdf is loaded
        if (typeof html2pdf === 'undefined') {
            console.error('html2pdf library not loaded');
        }
    });

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

    async function loadStatistics() {
        const period = document.getElementById('trend-period').value;
        showChartLoading(true);
        
        try {
            const response = await fetch(`/staff/surveys/data?period=${period}&page=1&per_page=1`);
            const data = await response.json();
            
            if (data.success && data.stats) {
                statisticsData = data.stats;
                updateStatsCards(statisticsData);
                updateSatisfactionChart(statisticsData);
                updateRatingDonutChart(statisticsData);
                updateClientTypeChart(statisticsData);
                updateSQDRadarChart(statisticsData);
                updateRatingLegend(statisticsData);
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        } finally {
            showChartLoading(false);
        }
    }

    function showChartLoading(show) {
        const loadingEl = document.getElementById('chart-loading');
        if (loadingEl) {
            if (show) loadingEl.classList.remove('hidden');
            else loadingEl.classList.add('hidden');
        }
    }

    function updateStatsCards(stats) {
        const statsContainer = document.getElementById('stats-container');
        const total = stats.total || 0;
        const avgRating = stats.avg_rating ? stats.avg_rating.toFixed(1) : '0.0';
        const responseRate = stats.response_rate ? stats.response_rate.toFixed(1) : 0;
        
        statsContainer.innerHTML = `
            <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Surveys</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${total.toLocaleString()}</p>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Overall submissions
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Average Rating</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${avgRating} / 5.0</p>
                        <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            Customer satisfaction
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-purple-500 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">This Month</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${(stats.this_month || 0).toLocaleString()}</p>
                        <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            New surveys this month
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-orange-500 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Response Rate</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${responseRate}%</p>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Of total applicants
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('avg-rating').textContent = avgRating;
        document.getElementById('highest-rating').textContent = stats.highest_rating ? stats.highest_rating.toFixed(1) : '0.0';
        document.getElementById('lowest-rating').textContent = stats.lowest_rating ? stats.lowest_rating.toFixed(1) : '0.0';
        document.getElementById('response-rate').textContent = `${responseRate}%`;
    }

    function updateSatisfactionChart(stats) {
        const ctx = document.getElementById('satisfactionChart').getContext('2d');
        const labels = stats.trend_labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const values = stats.trend_values || [4.2, 4.3, 4.1, 4.4, 4.5, 4.6];
        
        if (satisfactionChart) satisfactionChart.destroy();
        
        satisfactionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Average Satisfaction Rating',
                    data: values,
                    borderColor: '#155386',
                    backgroundColor: 'rgba(21, 83, 134, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#40798C',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                                return `Rating: ${context.raw.toFixed(1)} / 5.0`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 1,
                        max: 5,
                        title: { display: true, text: 'Rating (1-5)' },
                        grid: { color: '#e5e7eb' }
                    },
                    x: { title: { display: true, text: 'Period' }, grid: { display: false } }
                }
            }
        });
    }

    function updateRatingDonutChart(stats) {
        const ctx = document.getElementById('ratingDonutChart').getContext('2d');
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        
        if (ratingDonutChart) ratingDonutChart.destroy();
        
        ratingDonutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
                datasets: [{
                    data: [ratings[5] || 0, ratings[4] || 0, ratings[3] || 0, ratings[2] || 0, ratings[1] || 0],
                    backgroundColor: ['#22C55E', '#3B82F6', '#F59E0B', '#F97316', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 10,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw} (${((ctx.raw / (ratings[1]+ratings[2]+ratings[3]+ratings[4]+ratings[5])) * 100).toFixed(1)}%)` } } }
            }
        });
    }

    function updateRatingLegend(stats) {
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        const total = ratings[1] + ratings[2] + ratings[3] + ratings[4] + ratings[5];
        const legend = document.getElementById('rating-legend');
        
        legend.innerHTML = `
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-sm text-gray-600">5 Stars</span></div><span class="text-sm font-bold text-gray-700">${((ratings[5]/total)*100 || 0).toFixed(1)}% (${ratings[5]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width: ${(ratings[5]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-500"></div><span class="text-sm text-gray-600">4 Stars</span></div><span class="text-sm font-bold text-gray-700">${((ratings[4]/total)*100 || 0).toFixed(1)}% (${ratings[4]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-blue-500 h-1.5 rounded-full" style="width: ${(ratings[4]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-yellow-500"></div><span class="text-sm text-gray-600">3 Stars</span></div><span class="text-sm font-bold text-gray-700">${((ratings[3]/total)*100 || 0).toFixed(1)}% (${ratings[3]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-yellow-500 h-1.5 rounded-full" style="width: ${(ratings[3]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div><span class="text-sm text-gray-600">2 Stars</span></div><span class="text-sm font-bold text-gray-700">${((ratings[2]/total)*100 || 0).toFixed(1)}% (${ratings[2]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-orange-500 h-1.5 rounded-full" style="width: ${(ratings[2]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex items-center justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-500"></div><span class="text-sm text-gray-600">1 Star</span></div><span class="text-sm font-bold text-gray-700">${((ratings[1]/total)*100 || 0).toFixed(1)}% (${ratings[1]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-red-500 h-1.5 rounded-full" style="width: ${(ratings[1]/total)*100 || 0}%"></div></div></div>
        `;
    }

    function updateClientTypeChart(stats) {
        const ctx = document.getElementById('clientTypeChart').getContext('2d');
        const clientTypes = stats.client_types || { citizen: 0, business: 0, government: 0 };
        
        if (clientTypeChart) clientTypeChart.destroy();
        
        clientTypeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Citizen', 'Business', 'Government'],
                datasets: [{
                    label: 'Number of Surveys',
                    data: [clientTypes.citizen || 0, clientTypes.business || 0, clientTypes.government || 0],
                    backgroundColor: ['#155386', '#40798C', '#70A9A1'],
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `${ctx.raw} surveys` } } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Surveys' }, grid: { color: '#e5e7eb' } }, x: { title: { display: true, text: 'Client Type' }, grid: { display: false } } }
            }
        });
    }

    function updateSQDRadarChart(stats) {
        const ctx = document.getElementById('sqdRadarChart').getContext('2d');
        const sqdScores = stats.sqd_scores || [4.2, 4.1, 4.3, 4.0, 4.2, 4.4, 4.5, 4.3, 4.2];
        
        if (sqdRadarChart) sqdRadarChart.destroy();
        
        sqdRadarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['SQD0', 'SQD1', 'SQD2', 'SQD3', 'SQD4', 'SQD5', 'SQD6', 'SQD7', 'SQD8'],
                datasets: [{
                    label: 'Average Score',
                    data: sqdScores,
                    backgroundColor: 'rgba(21, 83, 134, 0.2)',
                    borderColor: '#155386',
                    borderWidth: 2,
                    pointBackgroundColor: '#40798C',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { r: { min: 1, max: 5, ticks: { stepSize: 1 } } },
                plugins: { tooltip: { callbacks: { label: (ctx) => `Score: ${ctx.raw.toFixed(1)} / 5.0` } } }
            }
        });
    }

    async function loadSurveys(page = 1) {
        const params = new URLSearchParams({ page, per_page: currentPerPage, ...currentFilters });
        try {
            const response = await fetch(`/staff/surveys/data?${params}`);
            const data = await response.json();
            if (data.success) {
                surveysData = data.surveys;
                renderSurveys(surveysData);
                renderPagination(data.pagination);
            }
        } catch (error) { console.error('Error loading surveys:', error); }
    }

    function renderSurveys(surveys) {
        const tbody = document.getElementById('surveysTableBody');
        if (!surveys || surveys.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500"><div class="flex flex-col items-center"><svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p>No surveys found</p></div></td></tr>`;
            return;
        }
        
        tbody.innerHTML = surveys.map(survey => {
            const ratings = [survey.sqd0_satisfied, survey.sqd1_reasonable_time, survey.sqd2_requirements_followed, survey.sqd3_steps_easy, survey.sqd4_info_easy_find, survey.sqd5_reasonable_fees, survey.sqd6_fair_treatment, survey.sqd7_courteous_staff, survey.sqd8_got_what_needed].filter(r => r);
            const avgRating = ratings.length > 0 ? (ratings.reduce((a, b) => a + b, 0) / ratings.length).toFixed(1) : 'N/A';
            const ratingColor = avgRating >= 4.5 ? 'bg-green-100 text-green-800' : avgRating >= 3.5 ? 'bg-blue-100 text-blue-800' : avgRating >= 2.5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800';
            
            return `<tr class="hover:bg-gray-50 transition"><td class="px-6 py-4"><div class="text-sm font-medium text-gray-900">${escapeHtml(survey.applicant_name || 'Unknown')}</div><div class="text-xs text-gray-500">${escapeHtml(survey.email || '')}</div></td><td class="px-6 py-4"><span class="text-sm text-gray-900">${survey.application_number || 'N/A'}</span></td><td class="px-6 py-4"><div class="text-sm text-gray-900">${survey.client_type || ''} ${survey.sex ? `(${survey.sex})` : ''}</div><div class="text-xs text-gray-500">Age: ${survey.age || 'N/A'}</div></td><td class="px-6 py-4 text-sm text-gray-900">${new Date(survey.created_at).toLocaleDateString()}</td><td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${ratingColor}">${avgRating === 'N/A' ? 'N/A' : `${avgRating} / 5`}</span></td><td class="px-6 py-4"><button onclick="viewSurveyDetails(${survey.id})" class="text-[#155386] hover:text-[#1F363D] font-medium text-sm">View Details</button></td></table>`;
        }).join('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderPagination(pagination) {
        document.getElementById('paginationInfo').textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total || 0} entries`;
        const controls = document.getElementById('paginationControls');
        controls.innerHTML = '';
        
        if (pagination.last_page > 1) {
            const prevBtn = createPageButton('← Previous', pagination.current_page > 1, () => loadSurveys(pagination.current_page - 1));
            controls.appendChild(prevBtn);
            
            for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
                controls.appendChild(createPageButton(i.toString(), true, () => loadSurveys(i), i === pagination.current_page));
            }
            
            controls.appendChild(createPageButton('Next →', pagination.current_page < pagination.last_page, () => loadSurveys(pagination.current_page + 1)));
        }
    }

    function createPageButton(text, enabled, onClick, active = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = `px-3 py-1 text-sm rounded-lg transition-colors ${active ? 'bg-[#155386] text-white' : enabled ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}`;
        if (enabled && !active) btn.addEventListener('click', onClick);
        return btn;
    }

    function applyFilters() {
        currentFilters = {
            search: document.getElementById('searchInput').value,
            date_from: document.getElementById('dateFromInput').value,
            date_to: document.getElementById('dateToInput').value,
            client_type: document.getElementById('clientTypeInput').value,
            sex: document.getElementById('sexInput').value
        };
        loadSurveys();
        loadStatistics();
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('dateFromInput').value = '';
        document.getElementById('dateToInput').value = '';
        document.getElementById('clientTypeInput').value = '';
        document.getElementById('sexInput').value = '';
        currentFilters = {};
        loadSurveys();
        loadStatistics();
    }

    function changePerPage() {
        currentPerPage = parseInt(document.getElementById('perPageSelect').value);
        loadSurveys();
    }

    function exportSurveys(format = 'csv') { 
        const params = new URLSearchParams(currentFilters);
        window.location.href = `/staff/surveys/export?${params.toString()}`;
    }

    async function exportToPDF() {
        // Close dropdown
        const dropdown = document.getElementById('export-dropdown');
        if (dropdown) dropdown.classList.add('hidden');
        
        // Show loading notification
        showNotification('Generating PDF, please wait...', 'info');
        
        try {
            // Check if html2pdf is available
            if (typeof html2pdf === 'undefined') {
                throw new Error('PDF library not loaded. Please refresh the page and try again.');
            }
            
            const element = document.getElementById('survey-report-content');
            if (!element) {
                throw new Error('Content not found');
            }
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: `client_satisfaction_surveys_${new Date().toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    letterRendering: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };
            
            await html2pdf().set(opt).from(element).save();
            showNotification('PDF generated successfully!', 'success');
        } catch (error) {
            console.error('PDF generation error:', error);
            showNotification(error.message || 'Failed to generate PDF. Please try again.', 'error');
        }
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-0 ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                ${type === 'success' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : 
                  type === 'error' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :
                  '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>'}
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    window.viewSurveyDetails = (id) => {
        const survey = surveysData.find(s => s.id === id);
        if (!survey) return;
        
        const modalContent = document.getElementById('modalContent');
        const avgRating = ((survey.sqd0_satisfied + survey.sqd1_reasonable_time + survey.sqd2_requirements_followed + survey.sqd3_steps_easy + survey.sqd4_info_easy_find + survey.sqd5_reasonable_fees + survey.sqd6_fair_treatment + survey.sqd7_courteous_staff + survey.sqd8_got_what_needed) / 9).toFixed(1);
        
        modalContent.innerHTML = `
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center"><svg class="w-5 h-5 mr-2 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Applicant Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm"><div><span class="font-medium text-gray-600">Name:</span> <span class="text-gray-900">${escapeHtml(survey.applicant_name || '-')}</span></div><div><span class="font-medium text-gray-600">Email:</span> <span class="text-gray-900">${escapeHtml(survey.email || '-')}</span></div><div><span class="font-medium text-gray-600">Application #:</span> <span class="text-gray-900">${survey.application_number || '-'}</span></div><div><span class="font-medium text-gray-600">Survey Date:</span> <span class="text-gray-900">${new Date(survey.created_at).toLocaleDateString()}</span></div><div><span class="font-medium text-gray-600">Client Type:</span> <span class="text-gray-900">${survey.client_type || '-'}</span></div><div><span class="font-medium text-gray-600">Sex:</span> <span class="text-gray-900">${survey.sex || '-'}</span></div><div><span class="font-medium text-gray-600">Age:</span> <span class="text-gray-900">${survey.age || '-'}</span></div><div><span class="font-medium text-gray-600">Overall Rating:</span> <span class="text-gray-900 font-bold">${avgRating} / 5.0</span></div></div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100"><h4 class="font-semibold text-gray-900 mb-3 flex items-center"><svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Citizens' Charter Questions</h4><div class="space-y-2 text-sm"><div><span class="font-medium text-gray-700">CC1 - Awareness:</span> <span class="text-gray-900 ml-2">${getCC1Text(survey.cc1_awareness)}</span></div><div><span class="font-medium text-gray-700">CC2 - Helpfulness:</span> <span class="text-gray-900 ml-2">${getCC2Text(survey.cc2_helpfulness)}</span></div><div><span class="font-medium text-gray-700">CC3 - Help Level:</span> <span class="text-gray-900 ml-2">${getCC3Text(survey.cc3_help_level)}</span></div></div></div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-100"><h4 class="font-semibold text-gray-900 mb-3 flex items-center"><svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Service Quality Dimensions</h4><div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm"><div><span class="font-medium text-gray-700">SQD0 - Satisfied:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd0_satisfied)}</span></div><div><span class="font-medium text-gray-700">SQD1 - Reasonable Time:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd1_reasonable_time)}</span></div><div><span class="font-medium text-gray-700">SQD2 - Requirements Followed:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd2_requirements_followed)}</span></div><div><span class="font-medium text-gray-700">SQD3 - Steps Easy:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd3_steps_easy)}</span></div><div><span class="font-medium text-gray-700">SQD4 - Info Easy Find:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd4_info_easy_find)}</span></div><div><span class="font-medium text-gray-700">SQD5 - Reasonable Fees:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd5_reasonable_fees)}</span></div><div><span class="font-medium text-gray-700">SQD6 - Fair Treatment:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd6_fair_treatment)}</span></div><div><span class="font-medium text-gray-700">SQD7 - Courteous Staff:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd7_courteous_staff)}</span></div><div><span class="font-medium text-gray-700">SQD8 - Got What Needed:</span> <span class="text-gray-900 ml-2">${getRatingText(survey.sqd8_got_what_needed)}</span></div></div></div>
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100"><h4 class="font-semibold text-gray-900 mb-2 flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>Suggestions & Comments</h4><p class="text-sm text-gray-700">${survey.suggestions || 'No suggestions provided'}</p></div>
            </div>
        `;
        
        document.getElementById('surveyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    function closeModal() {
        document.getElementById('surveyModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function getCC1Text(v) { return { '1': 'I know what a CC is and I saw this office\'s CC.', '2': 'I know what a CC is but I did NOT see this office\'s CC.', '3': 'I learned of the CC only when I saw this office\'s CC.', '4': 'I do not know what a CC is and I did not see one in this office.' }[v] || v || 'Not answered'; }
    function getCC2Text(v) { return { '1': 'Easy to see', '2': 'Somewhat easy to see', '3': 'Difficult to see', '4': 'Not visible at all', '5': 'N/A' }[v] || v || 'Not answered'; }
    function getCC3Text(v) { return { '1': 'Helped very much', '2': 'Somewhat helped', '3': 'Did not help', '4': 'N/A' }[v] || v || 'Not answered'; }
    function getRatingText(v) { return { '1': 'Strongly Disagree', '2': 'Disagree', '3': 'Neither Agree nor Disagree', '4': 'Agree', '5': 'Strongly Agree' }[v] || v || 'Not answered'; }
</script>
@endsection