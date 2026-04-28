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

    <!-- TABS -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('surveys')" id="tab-surveys" 
                class="tab-button py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 border-[#155386] text-[#155386]">
                Satisfaction Surveys
            </button>
            @php
                $user = Auth::user();
                $user->load('profile');
                $position = $user->profile ? $user->profile->position : null;
                $showCPDOTab = ($position === 'cpdo' || $position === 'mayor');
            @endphp
            @if($showCPDOTab)
            <button onclick="switchTab('cpdo-ratings')" id="tab-cpdo-ratings" 
                class="tab-button py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                CPDO Experience Ratings
            </button>
            @endif
        </nav>
    </div>

    <!-- SATISFACTION SURVEYS TAB CONTENT -->
    <div id="surveys-tab" class="tab-content">
        
        <!-- Filters and Search - ONLY for non-Mayor users -->
        @php
            $isMayor = ($position === 'mayor');
        @endphp
        
        @if(!$isMayor)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search by application number or applicant name..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white">
                </div>
                <div><input type="date" id="dateFromInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white w-full sm:w-auto"></div>
                <div><input type="date" id="dateToInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white w-full sm:w-auto"></div>
                <select id="clientTypeInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[150px]">
                    <option value="">All Types</option>
                    <option value="citizen">Citizen</option>
                    <option value="business">Business</option>
                    <option value="government">Government</option>
                </select>
                <select id="sexInput" class="px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] bg-white min-w-[120px]">
                    <option value="">All Sex</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <button id="applyFiltersBtn" class="px-6 py-3 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition font-medium text-sm">Apply Filters</button>
                <button id="clearFiltersBtn" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">Reset</button>
            </div>
        </div>
        @endif

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="stats-container">
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
        </div>

        <!-- CHARTS - Satisfaction Trend & Rating Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Satisfaction Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div><h2 class="text-lg font-semibold text-gray-800">Satisfaction Trend</h2><p class="text-xs text-gray-500 mt-1">Average satisfaction rating over time</p></div>
                    <div class="relative"><select id="trend-period" class="appearance-none border border-gray-200 rounded-lg text-sm px-4 py-2.5 pr-8 bg-gray-50"><option value="this_month">This Month</option><option value="last_month">Last Month</option><option value="this_year">This Year</option></select>
                    <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></div>
                </div>
                <div class="relative h-80"><canvas id="satisfactionChart"></canvas><div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 hidden"><svg class="animate-spin h-8 w-8 text-[#155386]" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div></div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div class="text-center"><p class="text-xs text-gray-500">Average Rating</p><p class="text-lg font-bold text-gray-800" id="avg-rating">0.0</p></div>
                    <div class="text-center"><p class="text-xs text-gray-500">Highest Rating</p><p class="text-lg font-bold text-green-600" id="highest-rating">0.0</p></div>
                    <div class="text-center"><p class="text-xs text-gray-500">Lowest Rating</p><p class="text-lg font-bold text-red-600" id="lowest-rating">0.0</p></div>
                    <div class="text-center"><p class="text-xs text-gray-500">Response Rate</p><p class="text-lg font-bold text-blue-600" id="response-rate">0%</p></div>
                </div>
            </div>
            
            <!-- Rating Distribution Donut Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between w-full mb-6"><div><h2 class="text-lg font-semibold text-gray-800">Rating Distribution</h2><p class="text-xs text-gray-500 mt-1">Satisfaction rating breakdown</p></div><span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Overall</span></div>
                <div class="relative w-48 h-48 mx-auto mb-6"><canvas id="ratingDonutChart" width="192" height="192"></canvas></div>
                <div class="w-full space-y-4 mt-4" id="rating-legend"></div>
            </div>
        </div>

        <!-- SECOND ROW - Client Distribution & Service Quality Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Client Distribution -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between w-full mb-6"><div><h2 class="text-lg font-semibold text-gray-800">Client Distribution</h2><p class="text-xs text-gray-500 mt-1">By client type</p></div></div>
                <div class="h-64"><canvas id="clientTypeChart"></canvas></div>
            </div>
            
            <!-- Service Quality Radar Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between w-full mb-6"><div><h2 class="text-lg font-semibold text-gray-800">Service Quality Metrics</h2><p class="text-xs text-gray-500 mt-1">Average scores by dimension</p></div></div>
                <div class="h-64"><canvas id="sqdRadarChart"></canvas></div>
            </div>
        </div>

        <!-- Surveys Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-900">Survey Responses</h3>
                <div class="flex items-center space-x-2"><span class="text-sm text-gray-500">Show</span><select id="perPageSelect" class="border border-gray-300 rounded-lg text-sm px-2 py-1"><option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select><span class="text-sm text-gray-500">entries</span></div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Survey Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Overall Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="surveysTableBody" class="bg-white divide-y divide-gray-200">
                        <tr><td colspan="6" class="px-6 py-8 text-center"><div class="flex flex-col items-center"><svg class="animate-spin h-8 w-8 text-[#155386] mb-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p>Loading surveys...</p></div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="text-sm text-gray-700" id="paginationInfo">Showing 0 to 0 of 0 entries</div>
                    <div class="flex space-x-2" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CPDO RATINGS TAB CONTENT (Visible to CPDO and Mayor) -->
    <div id="cpdo-ratings-tab" class="tab-content hidden">
        <!-- CPDO Ratings Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="cpdo-stats-container">
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse"><div class="h-16 bg-gray-200 rounded"></div></div>
        </div>

        <!-- CPDO Ratings Chart Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">CPDO Star Rating Distribution</h2>
                <div class="relative w-64 h-64 mx-auto mb-6"><canvas id="cpdoRatingDonutChart" width="256" height="256"></canvas></div>
                <div class="w-full space-y-4 mt-4" id="cpdo-rating-legend"></div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">CPDO Service Metrics</h2>
                <div class="h-80"><canvas id="cpdoRadarChart"></canvas></div>
            </div>
        </div>

        <!-- CPDO Ratings Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <h3 class="text-lg font-semibold text-gray-900">CPDO Experience Ratings</h3>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Show</span>
            <select id="cpdoPerPageSelect" class="border border-gray-300 rounded-lg text-sm px-2 py-1">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-500">entries</span>
            <button onclick="exportCPDORatings()" class="ml-4 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition text-sm">
                Export CSV
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application #</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processing Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsiveness</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clarity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fairness</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="cpdoRatingsTableBody" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="10" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-[#155386] mb-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p>Loading CPDO ratings...</p>
                        </div>
                    </span
                </tr>
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="text-sm text-gray-700" id="cpdoPaginationInfo">Showing 0 to 0 of 0 entries</div>
            <div class="flex space-x-2" id="cpdoPaginationControls"></div>
        </div>
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
                    <div><h3 class="text-xl font-bold">Survey Details</h3><p class="text-sm opacity-90">Client Satisfaction Survey Response</p></div>
                    <button id="closeModalBtn" class="text-white hover:text-gray-200 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto" id="modalContent"></div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end"><button id="closeModalFooterBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button></div>
            </div>
        </div>
    </div>
</div>

<!-- CPDO Rating Details Modal -->
<div id="cpdoRatingModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4 py-8" style="backdrop-filter: blur(4px);">
    <div class="relative min-h-full flex items-center justify-center">
        <div class="mx-auto w-full max-w-4xl">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <div><h3 class="text-xl font-bold">CPDO Experience Rating Details</h3><p class="text-sm opacity-90">Client feedback on CPDO service</p></div>
                    <button id="closeCPDORatingModalBtn" class="text-white hover:text-gray-200 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto" id="cpdoRatingModalContent"></div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end"><button id="closeCPDORatingModalFooterBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let satisfactionChart = null, ratingDonutChart = null, clientTypeChart = null, sqdRadarChart = null;
    let cpdoRatingDonutChart = null, cpdoRadarChart = null;
    let currentPage = 1, currentFilters = {}, currentPerPage = 15, surveysData = [], statisticsData = null;
    let cpdoCurrentPage = 1, cpdoCurrentFilters = {}, cpdoCurrentPerPage = 15, cpdoRatingsData = [], cpdoStatsData = null;

    // Check if user is CPDO or Mayor
    let isCPDO = {{ Auth::user()->profile && Auth::user()->profile->position === 'cpdo' ? 'true' : 'false' }};
    let isMayor = {{ Auth::user()->profile && Auth::user()->profile->position === 'mayor' ? 'true' : 'false' }};
    let showFilters = !isMayor;

    // Tab switching
    function switchTab(tab) {
        const surveysTab = document.getElementById('surveys-tab');
        const cpdoRatingsTab = document.getElementById('cpdo-ratings-tab');
        const tabSurveys = document.getElementById('tab-surveys');
        const tabCpdo = document.getElementById('tab-cpdo-ratings');
        
        if (tab === 'surveys') {
            surveysTab.classList.remove('hidden');
            cpdoRatingsTab.classList.add('hidden');
            tabSurveys.classList.add('border-[#155386]', 'text-[#155386]');
            tabSurveys.classList.remove('border-transparent', 'text-gray-500');
            if (tabCpdo) {
                tabCpdo.classList.remove('border-[#155386]', 'text-[#155386]');
                tabCpdo.classList.add('border-transparent', 'text-gray-500');
            }
        } else {
            surveysTab.classList.add('hidden');
            cpdoRatingsTab.classList.remove('hidden');
            if (tabCpdo) {
                tabCpdo.classList.add('border-[#155386]', 'text-[#155386]');
                tabCpdo.classList.remove('border-transparent', 'text-gray-500');
            }
            tabSurveys.classList.remove('border-[#155386]', 'text-[#155386]');
            tabSurveys.classList.add('border-transparent', 'text-gray-500');
            if ((isCPDO || isMayor) && cpdoRatingsData.length === 0) {
                loadCPDORatings();
                loadCPDOStatistics();
            }
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadSurveys();
        loadStatistics();
        
        if (showFilters) {
            document.getElementById('applyFiltersBtn')?.addEventListener('click', applyFilters);
            document.getElementById('clearFiltersBtn')?.addEventListener('click', clearFilters);
        }
        document.getElementById('perPageSelect')?.addEventListener('change', changePerPage);
        document.getElementById('trend-period')?.addEventListener('change', loadStatistics);
        
        if (isCPDO || isMayor) {
            document.getElementById('cpdoPerPageSelect')?.addEventListener('change', changeCPDOPerPage);
        }
        
        document.getElementById('closeModalBtn')?.addEventListener('click', closeModal);
        document.getElementById('closeModalFooterBtn')?.addEventListener('click', closeModal);
        document.getElementById('closeCPDORatingModalBtn')?.addEventListener('click', closeCPDORatingModal);
        document.getElementById('closeCPDORatingModalFooterBtn')?.addEventListener('click', closeCPDORatingModal);
        
        document.getElementById('surveyModal')?.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
        document.getElementById('cpdoRatingModal')?.addEventListener('click', function(e) { if (e.target === this) closeCPDORatingModal(); });
        
        if (typeof html2pdf === 'undefined') console.error('html2pdf library not loaded');
        
        // Load CPDO ratings if user is CPDO or Mayor
        if (isCPDO || isMayor) {
            loadCPDORatings();
            loadCPDOStatistics();
        }
    });

    function toggleExportDropdown() {
        const dropdown = document.getElementById('export-dropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    }
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('export-dropdown');
        const button = event.target.closest('.relative');
        if (!button && dropdown && !dropdown.classList.contains('hidden')) dropdown.classList.add('hidden');
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { const dropdown = document.getElementById('export-dropdown'); if (dropdown && !dropdown.classList.contains('hidden')) dropdown.classList.add('hidden'); } });

    // ========== SATISFACTION SURVEYS FUNCTIONS ==========
    async function loadStatistics() {
        const period = document.getElementById('trend-period').value;
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
        } catch (error) { console.error('Error loading statistics:', error); }
    }

    function updateStatsCards(stats) {
        const total = stats.total || 0;
        const avgRating = stats.avg_rating ? stats.avg_rating.toFixed(1) : '0.0';
        const responseRate = stats.response_rate ? stats.response_rate.toFixed(1) : 0;
        document.getElementById('stats-container').innerHTML = `
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Total Surveys</p><p class="text-2xl font-bold mt-1">${total.toLocaleString()}</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Average Rating</p><p class="text-2xl font-bold mt-1">${avgRating} / 5.0</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">This Month</p><p class="text-2xl font-bold mt-1">${(stats.this_month || 0).toLocaleString()}</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Response Rate</p><p class="text-2xl font-bold mt-1">${responseRate}%</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div></div></div>`;
        document.getElementById('avg-rating').textContent = avgRating;
        document.getElementById('highest-rating').textContent = stats.highest_rating ? stats.highest_rating.toFixed(1) : '0.0';
        document.getElementById('lowest-rating').textContent = stats.lowest_rating ? stats.lowest_rating.toFixed(1) : '0.0';
        document.getElementById('response-rate').textContent = `${responseRate}%`;
    }

    function updateSatisfactionChart(stats) {
        const ctx = document.getElementById('satisfactionChart').getContext('2d');
        if (satisfactionChart) satisfactionChart.destroy();
        satisfactionChart = new Chart(ctx, { type: 'line', data: { labels: stats.trend_labels || ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ label: 'Rating', data: stats.trend_values || [0,0,0,0,0,0], borderColor: '#155386', backgroundColor: 'rgba(21,83,134,0.1)', tension: 0.4, fill: true, pointBackgroundColor: '#40798C', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { min: 1, max: 5, title: { display: true, text: 'Rating (1-5)' } } } } });
    }

    function updateRatingDonutChart(stats) {
        const ctx = document.getElementById('ratingDonutChart').getContext('2d');
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        if (ratingDonutChart) ratingDonutChart.destroy();
        ratingDonutChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['5 Stars','4 Stars','3 Stars','2 Stars','1 Star'], datasets: [{ data: [ratings[5], ratings[4], ratings[3], ratings[2], ratings[1]], backgroundColor: ['#22C55E','#3B82F6','#F59E0B','#F97316','#EF4444'], borderWidth: 0, cutout: '65%' }] }, options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } } });
    }

    function updateRatingLegend(stats) {
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        const total = ratings[1]+ratings[2]+ratings[3]+ratings[4]+ratings[5];
        document.getElementById('rating-legend').innerHTML = `
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-sm">5 Stars</span></div><span class="text-sm font-bold">${((ratings[5]/total)*100 || 0).toFixed(1)}% (${ratings[5]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width: ${(ratings[5]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-500"></div><span class="text-sm">4 Stars</span></div><span class="text-sm font-bold">${((ratings[4]/total)*100 || 0).toFixed(1)}% (${ratings[4]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-blue-500 h-1.5 rounded-full" style="width: ${(ratings[4]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-yellow-500"></div><span class="text-sm">3 Stars</span></div><span class="text-sm font-bold">${((ratings[3]/total)*100 || 0).toFixed(1)}% (${ratings[3]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-yellow-500 h-1.5 rounded-full" style="width: ${(ratings[3]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div><span class="text-sm">2 Stars</span></div><span class="text-sm font-bold">${((ratings[2]/total)*100 || 0).toFixed(1)}% (${ratings[2]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-orange-500 h-1.5 rounded-full" style="width: ${(ratings[2]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-500"></div><span class="text-sm">1 Star</span></div><span class="text-sm font-bold">${((ratings[1]/total)*100 || 0).toFixed(1)}% (${ratings[1]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-red-500 h-1.5 rounded-full" style="width: ${(ratings[1]/total)*100 || 0}%"></div></div></div>`;
    }

    function updateClientTypeChart(stats) {
        const ctx = document.getElementById('clientTypeChart').getContext('2d');
        const types = stats.client_types || { citizen: 0, business: 0, government: 0 };
        if (clientTypeChart) clientTypeChart.destroy();
        clientTypeChart = new Chart(ctx, { type: 'bar', data: { labels: ['Citizen','Business','Government'], datasets: [{ label: 'Surveys', data: [types.citizen, types.business, types.government], backgroundColor: ['#155386','#40798C','#70A9A1'], borderRadius: 8 }] }, options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Surveys' } } } } });
    }

    function updateSQDRadarChart(stats) {
        const ctx = document.getElementById('sqdRadarChart').getContext('2d');
        const scores = stats.sqd_scores || [0,0,0,0,0,0,0,0,0];
        if (sqdRadarChart) sqdRadarChart.destroy();
        sqdRadarChart = new Chart(ctx, { type: 'radar', data: { labels: ['SQD0','SQD1','SQD2','SQD3','SQD4','SQD5','SQD6','SQD7','SQD8'], datasets: [{ label: 'Score', data: scores, backgroundColor: 'rgba(21,83,134,0.2)', borderColor: '#155386', borderWidth: 2, pointBackgroundColor: '#40798C', pointBorderColor: '#fff', pointRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: true, scales: { r: { min: 1, max: 5, ticks: { stepSize: 1 } } } } });
    }

    async function loadSurveys(page = 1) {
        const params = new URLSearchParams({ page, per_page: currentPerPage, ...currentFilters });
        try {
            const response = await fetch(`/staff/surveys/data?${params}`);
            const data = await response.json();
            if (data.success) { surveysData = data.surveys; renderSurveys(surveysData); renderPagination(data.pagination); }
        } catch (error) { console.error(error); }
    }

    function renderSurveys(surveys) {
        const tbody = document.getElementById('surveysTableBody');
        if (!surveys || !surveys.length) { tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No surveys found</span></td></tr>'; return; }
        tbody.innerHTML = surveys.map(s => {
            const ratings = [
                parseInt(s.sqd0_satisfied) || 0,
                parseInt(s.sqd1_reasonable_time) || 0,
                parseInt(s.sqd2_requirements_followed) || 0,
                parseInt(s.sqd3_steps_easy) || 0,
                parseInt(s.sqd4_info_easy_find) || 0,
                parseInt(s.sqd5_reasonable_fees) || 0,
                parseInt(s.sqd6_fair_treatment) || 0,
                parseInt(s.sqd7_courteous_staff) || 0,
                parseInt(s.sqd8_got_what_needed) || 0
            ].filter(r => r > 0);
            const avg = ratings.length ? (ratings.reduce((a,b)=>a+b,0)/ratings.length).toFixed(1) : 'N/A';
            const color = avg >= 4.5 ? 'bg-green-100 text-green-800' : avg >= 3.5 ? 'bg-blue-100 text-blue-800' : avg >= 2.5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800';
            return `<tr class="hover:bg-gray-50"><td class="px-6 py-4"><div class="text-sm font-medium">${escapeHtml(s.applicant_name||'Unknown')}</div><div class="text-xs text-gray-500">${escapeHtml(s.email||'')}</div></td><td class="px-6 py-4"><span class="text-sm">${s.application_number||'N/A'}</span></td><td class="px-6 py-4"><div class="text-sm">${s.client_type||''} ${s.sex?`(${s.sex})`:''}</div><div class="text-xs text-gray-500">Age: ${s.age||'N/A'}</div></td><td class="px-6 py-4 text-sm">${new Date(s.created_at).toLocaleDateString()}</td><td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${color}">${avg === 'N/A' ? 'N/A' : `${avg} / 5`}</span></td><td class="px-6 py-4"><button onclick="viewSurveyDetails(${s.id})" class="text-[#155386] hover:text-[#1F363D] text-sm">View Details</button></td></td>`;
        }).join('');
    }

    function renderPagination(p) {
        document.getElementById('paginationInfo').textContent = `Showing ${p.from||0} to ${p.to||0} of ${p.total||0}`;
        const ctrl = document.getElementById('paginationControls');
        ctrl.innerHTML = '';
        if (p.last_page > 1) {
            if (p.current_page > 1) ctrl.appendChild(createPageBtn('←', () => loadSurveys(p.current_page-1)));
            for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++)
                ctrl.appendChild(createPageBtn(i, () => loadSurveys(i), i === p.current_page));
            if (p.current_page < p.last_page) ctrl.appendChild(createPageBtn('→', () => loadSurveys(p.current_page+1)));
        }
    }

    function createPageBtn(text, onClick, active = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = `px-3 py-1 text-sm rounded-lg ${active ? 'bg-[#155386] text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'}`;
        if (!active) btn.addEventListener('click', onClick);
        return btn;
    }

    function applyFilters() {
        currentFilters = { search: document.getElementById('searchInput').value, date_from: document.getElementById('dateFromInput').value, date_to: document.getElementById('dateToInput').value, client_type: document.getElementById('clientTypeInput').value, sex: document.getElementById('sexInput').value };
        loadSurveys(); loadStatistics();
    }
    function clearFilters() {
        document.getElementById('searchInput').value = ''; document.getElementById('dateFromInput').value = ''; document.getElementById('dateToInput').value = ''; document.getElementById('clientTypeInput').value = ''; document.getElementById('sexInput').value = '';
        currentFilters = {}; loadSurveys(); loadStatistics();
    }
    function changePerPage() { currentPerPage = parseInt(document.getElementById('perPageSelect').value); loadSurveys(); }
    function exportSurveys(format = 'csv') { window.location.href = `/staff/surveys/export?${new URLSearchParams(currentFilters).toString()}`; }
    
    async function exportToPDF() {
        const dropdown = document.getElementById('export-dropdown'); if (dropdown) dropdown.classList.add('hidden');
        showNotification('Generating PDF, please wait...', 'info');
        try {
            if (typeof html2pdf === 'undefined') throw new Error('PDF library not loaded');
            const element = document.getElementById('survey-report-content');
            if (!element) throw new Error('Content not found');
            await html2pdf().set({ margin: [10,10,10,10], filename: `client_satisfaction_surveys_${new Date().toISOString().split('T')[0]}.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2, useCORS: true, letterRendering: true, logging: false, backgroundColor: '#ffffff' }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' } }).from(element).save();
            showNotification('PDF generated successfully!', 'success');
        } catch (error) { console.error('PDF generation error:', error); showNotification(error.message || 'Failed to generate PDF', 'error'); }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-0 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
        notification.innerHTML = `<div class="flex items-center gap-2">${type === 'success' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : type === 'error' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' : '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>'}<span>${message}</span></div>`;
        document.body.appendChild(notification);
        setTimeout(() => { notification.style.opacity = '0'; setTimeout(() => notification.remove(), 300); }, 3000);
    }

    window.viewSurveyDetails = (id) => {
        const s = surveysData.find(s => s.id === id);
        if (!s) return;
        const ratings = [
            parseInt(s.sqd0_satisfied) || 0,
            parseInt(s.sqd1_reasonable_time) || 0,
            parseInt(s.sqd2_requirements_followed) || 0,
            parseInt(s.sqd3_steps_easy) || 0,
            parseInt(s.sqd4_info_easy_find) || 0,
            parseInt(s.sqd5_reasonable_fees) || 0,
            parseInt(s.sqd6_fair_treatment) || 0,
            parseInt(s.sqd7_courteous_staff) || 0,
            parseInt(s.sqd8_got_what_needed) || 0
        ].filter(r => r > 0);
        const avg = ratings.length ? (ratings.reduce((a,b)=>a+b,0)/ratings.length).toFixed(1) : 'N/A';
        document.getElementById('modalContent').innerHTML = `
            <div class="space-y-6"><div class="bg-gray-50 rounded-lg p-4"><h4 class="font-semibold mb-3">Applicant Information</h4><div class="grid grid-cols-2 gap-3 text-sm"><div><span class="font-medium">Name:</span> ${escapeHtml(s.applicant_name||'-')}</div><div><span class="font-medium">Email:</span> ${escapeHtml(s.email||'-')}</div><div><span class="font-medium">Application #:</span> ${s.application_number||'-'}</div><div><span class="font-medium">Survey Date:</span> ${new Date(s.created_at).toLocaleDateString()}</div><div><span class="font-medium">Client Type:</span> ${s.client_type||'-'}</div><div><span class="font-medium">Sex:</span> ${s.sex||'-'}</div><div><span class="font-medium">Age:</span> ${s.age||'-'}</div><div><span class="font-medium">Overall Rating:</span> <strong>${avg}/5.0</strong></div></div></div>
            <div class="bg-blue-50 rounded-lg p-4"><h4 class="font-semibold mb-3">Citizens' Charter</h4><div class="space-y-2 text-sm"><div><span class="font-medium">CC1:</span> ${getCC1Text(s.cc1_awareness)}</div><div><span class="font-medium">CC2:</span> ${getCC2Text(s.cc2_helpfulness)}</div><div><span class="font-medium">CC3:</span> ${getCC3Text(s.cc3_help_level)}</div></div></div>
            <div class="bg-green-50 rounded-lg p-4"><h4 class="font-semibold mb-3">Service Quality</h4><div class="grid grid-cols-2 gap-3 text-sm"><div><span class="font-medium">SQD0:</span> ${getRatingText(s.sqd0_satisfied)}</div><div><span class="font-medium">SQD1:</span> ${getRatingText(s.sqd1_reasonable_time)}</div><div><span class="font-medium">SQD2:</span> ${getRatingText(s.sqd2_requirements_followed)}</div><div><span class="font-medium">SQD3:</span> ${getRatingText(s.sqd3_steps_easy)}</div><div><span class="font-medium">SQD4:</span> ${getRatingText(s.sqd4_info_easy_find)}</div><div><span class="font-medium">SQD5:</span> ${getRatingText(s.sqd5_reasonable_fees)}</div><div><span class="font-medium">SQD6:</span> ${getRatingText(s.sqd6_fair_treatment)}</div><div><span class="font-medium">SQD7:</span> ${getRatingText(s.sqd7_courteous_staff)}</div><div><span class="font-medium">SQD8:</span> ${getRatingText(s.sqd8_got_what_needed)}</div></div></div>
            <div class="bg-yellow-50 rounded-lg p-4"><h4 class="font-semibold mb-2">Suggestions</h4><p class="text-sm">${s.suggestions || 'No suggestions provided'}</p></div></div>`;
        document.getElementById('surveyModal').classList.remove('hidden'); document.body.style.overflow = 'hidden';
    };
    function closeModal() { document.getElementById('surveyModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }
    function closeCPDORatingModal() { document.getElementById('cpdoRatingModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }
    function getCC1Text(v) { return {1:'I know what a CC is and I saw this office\'s CC.',2:'I know what a CC is but I did NOT see this office\'s CC.',3:'I learned of the CC only when I saw this office\'s CC.',4:'I do not know what a CC is and I did not see one in this office.'}[v] || 'Not answered'; }
    function getCC2Text(v) { return {1:'Easy to see',2:'Somewhat easy to see',3:'Difficult to see',4:'Not visible at all',5:'N/A'}[v] || 'Not answered'; }
    function getCC3Text(v) { return {1:'Helped very much',2:'Somewhat helped',3:'Did not help',4:'N/A'}[v] || 'Not answered'; }
    function getRatingText(v) { return {1:'Strongly Disagree',2:'Disagree',3:'Neither',4:'Agree',5:'Strongly Agree'}[v] || 'Not answered'; }
    function escapeHtml(str) { if (!str) return ''; const div = document.createElement('div'); div.textContent = str; return div.innerHTML; }

    // ========== CPDO RATINGS FUNCTIONS ==========
    async function loadCPDOStatistics() {
        try {
            const response = await fetch('/staff/cpdo-ratings/stats');
            const data = await response.json();
            if (data.success) {
                cpdoStatsData = data.data;
                updateCPDOStatsCards(cpdoStatsData);
                updateCPDORatingDonutChart(cpdoStatsData);
                updateCPDORadarChart(cpdoStatsData);
                updateCPDORatingLegend(cpdoStatsData);
            }
        } catch (error) { console.error('Error loading CPDO stats:', error); }
    }

    function updateCPDOStatsCards(stats) {
        const total = stats.total || 0;
        const avgRating = stats.avg_rating ? stats.avg_rating.toFixed(1) : '0.0';
        const fiveStarPercent = stats.five_star_percent || 0;
        document.getElementById('cpdo-stats-container').innerHTML = `
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Total CPDO Ratings</p><p class="text-2xl font-bold mt-1">${total}</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Average Rating</p><p class="text-2xl font-bold mt-1">${avgRating} / 5.0</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">5-Star Ratings</p><p class="text-2xl font-bold mt-1">${fiveStarPercent}%</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div></div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500"><div class="flex justify-between"><div><p class="text-gray-500 text-sm">Response Rate</p><p class="text-2xl font-bold mt-1">${stats.response_rate || 0}%</p></div><div class="w-12 h-12 rounded-full bg-[#155386] flex items-center justify-center text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div></div></div>`;
    }

    function updateCPDORatingDonutChart(stats) {
        const ctx = document.getElementById('cpdoRatingDonutChart').getContext('2d');
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        if (cpdoRatingDonutChart) cpdoRatingDonutChart.destroy();
        cpdoRatingDonutChart = new Chart(ctx, { type: 'doughnut', data: { labels: ['5 Stars','4 Stars','3 Stars','2 Stars','1 Star'], datasets: [{ data: [ratings[5], ratings[4], ratings[3], ratings[2], ratings[1]], backgroundColor: ['#22C55E','#3B82F6','#F59E0B','#F97316','#EF4444'], borderWidth: 0, cutout: '65%' }] }, options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } } });
    }

    function updateCPDORatingLegend(stats) {
        const ratings = stats.rating_distribution || { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
        const total = ratings[1]+ratings[2]+ratings[3]+ratings[4]+ratings[5];
        document.getElementById('cpdo-rating-legend').innerHTML = `
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-sm">5 Stars</span></div><span class="text-sm font-bold">${((ratings[5]/total)*100 || 0).toFixed(1)}% (${ratings[5]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-green-500 h-1.5 rounded-full" style="width: ${(ratings[5]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-500"></div><span class="text-sm">4 Stars</span></div><span class="text-sm font-bold">${((ratings[4]/total)*100 || 0).toFixed(1)}% (${ratings[4]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-blue-500 h-1.5 rounded-full" style="width: ${(ratings[4]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-yellow-500"></div><span class="text-sm">3 Stars</span></div><span class="text-sm font-bold">${((ratings[3]/total)*100 || 0).toFixed(1)}% (${ratings[3]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-yellow-500 h-1.5 rounded-full" style="width: ${(ratings[3]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div><span class="text-sm">2 Stars</span></div><span class="text-sm font-bold">${((ratings[2]/total)*100 || 0).toFixed(1)}% (${ratings[2]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-orange-500 h-1.5 rounded-full" style="width: ${(ratings[2]/total)*100 || 0}%"></div></div></div>
            <div><div class="flex justify-between mb-1"><div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-500"></div><span class="text-sm">1 Star</span></div><span class="text-sm font-bold">${((ratings[1]/total)*100 || 0).toFixed(1)}% (${ratings[1]})</span></div><div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-red-500 h-1.5 rounded-full" style="width: ${(ratings[1]/total)*100 || 0}%"></div></div></div>`;
    }

    function updateCPDORadarChart(stats) {
        const ctx = document.getElementById('cpdoRadarChart').getContext('2d');
        const metrics = stats.metrics_scores || { processing_time: 0, responsiveness: 0, clarity: 0, fairness: 0, overall: 0 };
        if (cpdoRadarChart) cpdoRadarChart.destroy();
        cpdoRadarChart = new Chart(ctx, { type: 'radar', data: { labels: ['Processing Time','Staff Responsiveness','Clarity of Instructions','Fairness of Assessment','Overall Satisfaction'], datasets: [{ label: 'Average Score', data: [metrics.processing_time, metrics.responsiveness, metrics.clarity, metrics.fairness, metrics.overall], backgroundColor: 'rgba(21,83,134,0.2)', borderColor: '#155386', borderWidth: 2, pointBackgroundColor: '#40798C', pointBorderColor: '#fff', pointRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: true, scales: { r: { min: 1, max: 5, ticks: { stepSize: 1 } } } } });
    }

    async function loadCPDORatings(page = 1) {
        const params = new URLSearchParams({ page, per_page: cpdoCurrentPerPage });
        try {
            const response = await fetch(`/staff/cpdo-ratings/data?${params}`);
            const data = await response.json();
            if (data.success) {
                cpdoRatingsData = data.ratings;
                renderCPDORatings(cpdoRatingsData);
                renderCPDOPagination(data.pagination);
            }
        } catch (error) { console.error('Error loading CPDO ratings:', error); }
    }

    function renderCPDORatings(ratings) {
    const tbody = document.getElementById('cpdoRatingsTableBody');
    if (!ratings || !ratings.length) { 
        tbody.innerHTML = '<tr><td colspan="10" class="px-6 py-12 text-center text-gray-500">No CPDO ratings found</td></tr>';
        return; 
    }
    
    tbody.innerHTML = ratings.map(r => {
        // Star rating display
        const ratingValue = r.rating || 0;
        const starHtml = '<div class="flex justify-center items-center gap-0.5">' + 
            Array(5).fill().map((_, i) => 
                `<svg class="w-4 h-4 ${i < ratingValue ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>`
            ).join('') + 
            `<span class="ml-1 text-xs text-gray-500">(${ratingValue}/5)</span></div>`;
        
        // Get rating class for badges
        const getBadgeClass = (value) => {
            if (!value) return 'bg-gray-100 text-gray-600';
            const map = { 
                'Excellent': 'bg-green-100 text-green-800', 
                'Good': 'bg-blue-100 text-blue-800', 
                'Average': 'bg-yellow-100 text-yellow-800', 
                'Poor': 'bg-orange-100 text-orange-800', 
                'Very Poor': 'bg-red-100 text-red-800',
                'Very Satisfied': 'bg-green-100 text-green-800', 
                'Satisfied': 'bg-blue-100 text-blue-800', 
                'Neutral': 'bg-yellow-100 text-yellow-800', 
                'Dissatisfied': 'bg-orange-100 text-orange-800', 
                'Very Dissatisfied': 'bg-red-100 text-red-800' 
            };
            return map[value] || 'bg-gray-100 text-gray-600';
        };
        
        const formatValue = (value) => {
            if (!value) return '<span class="text-gray-400">—</span>';
            return `<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium ${getBadgeClass(value)}">${value}</span>`;
        };
        
        return `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(r.applicant_name || 'Unknown')}</div>
                    <div class="text-xs text-gray-500">${escapeHtml(r.email || '')}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm font-mono text-gray-600">${r.application_number || 'N/A'}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    ${starHtml}
                </td>
                <td class="px-4 py-3">
                    ${formatValue(r.processing_time)}
                </td>
                <td class="px-4 py-3">
                    ${formatValue(r.responsiveness)}
                </td>
                <td class="px-4 py-3">
                    ${formatValue(r.clarity)}
                </td>
                <td class="px-4 py-3">
                    ${formatValue(r.fairness)}
                </td>
                <td class="px-4 py-3">
                    ${formatValue(r.overall_satisfaction)}
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">
                    ${new Date(r.created_at).toLocaleDateString()}
                </td>
                <td class="px-4 py-3 text-center">
                    <button onclick="viewCPDORatingDetails(${r.id})" class="text-[#155386] hover:text-[#1F363D] text-sm font-medium">
                        View Details
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}
    function getRatingClass(value) {
        if (!value) return 'bg-gray-100 text-gray-600';
        const map = { 'Excellent': 'bg-green-100 text-green-800', 'Good': 'bg-blue-100 text-blue-800', 'Average': 'bg-yellow-100 text-yellow-800', 'Poor': 'bg-orange-100 text-orange-800', 'Very Poor': 'bg-red-100 text-red-800', 'Very Satisfied': 'bg-green-100 text-green-800', 'Satisfied': 'bg-blue-100 text-blue-800', 'Neutral': 'bg-yellow-100 text-yellow-800', 'Dissatisfied': 'bg-orange-100 text-orange-800', 'Very Dissatisfied': 'bg-red-100 text-red-800' };
        return map[value] || 'bg-gray-100 text-gray-600';
    }

    function renderCPDOPagination(p) {
        document.getElementById('cpdoPaginationInfo').textContent = `Showing ${p.from||0} to ${p.to||0} of ${p.total||0}`;
        const ctrl = document.getElementById('cpdoPaginationControls');
        ctrl.innerHTML = '';
        if (p.last_page > 1) {
            if (p.current_page > 1) ctrl.appendChild(createCPDOPageBtn('←', () => loadCPDORatings(p.current_page-1)));
            for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++)
                ctrl.appendChild(createCPDOPageBtn(i, () => loadCPDORatings(i), i === p.current_page));
            if (p.current_page < p.last_page) ctrl.appendChild(createCPDOPageBtn('→', () => loadCPDORatings(p.current_page+1)));
        }
    }

    function createCPDOPageBtn(text, onClick, active = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = `px-3 py-1 text-sm rounded-lg ${active ? 'bg-[#155386] text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'}`;
        if (!active) btn.addEventListener('click', onClick);
        return btn;
    }

    function changeCPDOPerPage() { cpdoCurrentPerPage = parseInt(document.getElementById('cpdoPerPageSelect').value); loadCPDORatings(); }

    window.viewCPDORatingDetails = (id) => {
    const r = cpdoRatingsData.find(r => r.id === id);
    if (!r) return;
    
    const getBadgeClass = (value) => {
        if (!value) return 'bg-gray-100 text-gray-600';
        const map = { 
            'Excellent': 'bg-green-100 text-green-800', 
            'Good': 'bg-blue-100 text-blue-800', 
            'Average': 'bg-yellow-100 text-yellow-800', 
            'Poor': 'bg-orange-100 text-orange-800', 
            'Very Poor': 'bg-red-100 text-red-800',
            'Very Satisfied': 'bg-green-100 text-green-800', 
            'Satisfied': 'bg-blue-100 text-blue-800', 
            'Neutral': 'bg-yellow-100 text-yellow-800', 
            'Dissatisfied': 'bg-orange-100 text-orange-800', 
            'Very Dissatisfied': 'bg-red-100 text-red-800' 
        };
        return map[value] || 'bg-gray-100 text-gray-600';
    };
    
    // Check if comments exist
    const hasComments = r.comments && r.comments.trim() !== '';
    const commentsHtml = hasComments 
        ? `<p class="text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(r.comments)}</p>`
        : `<p class="text-sm text-gray-400 italic">No suggestions or comments provided</p>`;
    
    document.getElementById('cpdoRatingModalContent').innerHTML = `
        <div class="space-y-6">
            <!-- Applicant Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Applicant Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><span class="font-medium text-gray-600">Name:</span> <span class="text-gray-900">${escapeHtml(r.applicant_name || '-')}</span></div>
                    <div><span class="font-medium text-gray-600">Email:</span> <span class="text-gray-900">${escapeHtml(r.email || '-')}</span></div>
                    <div><span class="font-medium text-gray-600">Application #:</span> <span class="text-gray-900">${r.application_number || '-'}</span></div>
                    <div><span class="font-medium text-gray-600">Submitted:</span> <span class="text-gray-900">${new Date(r.created_at).toLocaleString()}</span></div>
                </div>
            </div>
            
            <!-- Star Rating Summary -->
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Overall Rating
                </h4>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1">
                        ${Array(5).fill().map((_, i) => 
                            `<svg class="w-6 h-6 ${i < r.rating ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>`
                        ).join('')}
                    </div>
                    <span class="text-lg font-bold text-gray-800">${r.rating}/5</span>
                    <span class="text-sm text-gray-500">- 
                        ${r.rating >= 4.5 ? 'Excellent' : r.rating >= 3.5 ? 'Good' : r.rating >= 2.5 ? 'Average' : r.rating >= 1.5 ? 'Poor' : 'Very Poor'}
                    </span>
                </div>
            </div>
            
            <!-- Detailed Ratings -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Service Quality Ratings
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Processing Time:</span>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium ${getBadgeClass(r.processing_time)}">${r.processing_time || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Staff Responsiveness:</span>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium ${getBadgeClass(r.responsiveness)}">${r.responsiveness || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Clarity of Instructions:</span>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium ${getBadgeClass(r.clarity)}">${r.clarity || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-700">Fairness of Assessment:</span>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium ${getBadgeClass(r.fairness)}">${r.fairness || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-medium text-gray-700">Overall Satisfaction:</span>
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium ${getBadgeClass(r.overall_satisfaction)}">${r.overall_satisfaction || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Comments / Suggestions Section -->
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Suggestions & Comments
                </h4>
                <div class="bg-white rounded-lg p-4">
                    ${commentsHtml}
                </div>
                ${!hasComments ? `
                <div class="mt-3 flex items-center justify-center text-gray-400">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs">No additional feedback provided</span>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    document.getElementById('cpdoRatingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

    function exportCPDORatings() { 
    window.location.href = '/staff/cpdo-ratings/export'; 
}
</script>
@endsection