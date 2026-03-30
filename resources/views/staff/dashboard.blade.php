@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')

@section('content')
<!-- POSITION MODAL - Overlay on top of dashboard -->
<div id="positionModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none; background-color: transparent; pointer-events: none;">
    <!-- Modal Content - Only this is clickable, the rest of the overlay allows clicking through to dashboard -->
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 z-50" id="modalContent" style="pointer-events: auto; position: relative;">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#155386] bg-opacity-10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#155386]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">What is your position?</h2>
                </div>
                <button onclick="hidePositionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">Please select your position to continue. You can close this and set it later.</p>
                
                <!-- Position Selection Form -->
                <form id="positionForm">
                    @csrf
                    <div class="space-y-3">
                        <!-- Engineer -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#155386] hover:bg-blue-50 transition-all group">
                            <input type="radio" name="position" value="engineer" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" required>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800 group-hover:text-[#155386]">Engineer</span>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Technical</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Civil, Electrical, Mechanical Engineering</p>
                            </div>
                        </label>
                        
                        <!-- Architect -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#155386] hover:bg-blue-50 transition-all group">
                            <input type="radio" name="position" value="architect" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" required>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800 group-hover:text-[#155386]">Architect</span>
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Design</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Building Design and Planning</p>
                            </div>
                        </label>
                        
                        <!-- BFP -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#155386] hover:bg-blue-50 transition-all group">
                            <input type="radio" name="position" value="BFP" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" required>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800 group-hover:text-[#155386]">BFP</span>
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Fire Safety</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Bureau of Fire Protection</p>
                            </div>
                        </label>
                        
                        <!-- CPDO (City Planning and Development Office) -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#155386] hover:bg-blue-50 transition-all group">
                            <input type="radio" name="position" value="cpdo" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" required>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800 group-hover:text-[#155386]">CPDO</span>
                                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">Planning</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">City Planning and Development Office</p>
                            </div>
                        </label>
                        
                        <!-- Administrative Aide -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#155386] hover:bg-blue-50 transition-all group">
                            <input type="radio" name="position" value="administrative_aide" class="w-4 h-4 text-[#155386] focus:ring-[#155386]" required>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800 group-hover:text-[#155386]">Administrative Aide</span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Support</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Administrative and Support Services</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="positionError" class="mt-4 text-sm text-red-600 hidden"></div>
                    
                    <!-- Submit Button -->
                    <button type="submit" id="submitPositionBtn" class="w-full mt-6 bg-[#155386] text-white py-3 px-4 rounded-xl font-medium hover:bg-[#0f3b5a] transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span>Save Position</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="text-center text-xs text-gray-400">
                <p>You can close this and set your position later</p>
            </div>
        </div>
    </div>
</div>

<!-- Original Dashboard Content -->
<div class="p-4 md:p-6 bg-gray-50 min-h-screen max-w-7xl mx-auto" id="dashboardContent">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back! Here's your applications overview.</p>
        </div>
        <!-- Show user's position if set -->
        @if(Auth::user()->profile && Auth::user()->profile->position)
        <div class="mt-2 md:mt-0">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#155386] rounded-lg text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', Auth::user()->profile->position)) }}</span>
            </span>
        </div>
        @endif
    </div>

    <!-- TOP STATS - 4 cards in one row with blue icons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="stats-container">
        <!-- Stats will be loaded dynamically -->
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
                    <p class="text-xs text-gray-500 mt-1">Application volume over the last 4 weeks</p>
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

            <!-- BAR GRAPH with Y-axis -->
            <div class="relative h-72">
                <!-- Y-axis lines and labels (dynamic) -->
                <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-400 py-2" id="y-axis-labels">
                    <span>80</span>
                    <span>60</span>
                    <span>40</span>
                    <span>20</span>
                    <span>0</span>
                </div>
                
                <!-- Grid lines -->
                <div class="absolute left-8 right-0 top-0 h-full">
                    <div class="border-b border-dashed border-gray-200 h-1/4"></div>
                    <div class="border-b border-dashed border-gray-200 h-1/4"></div>
                    <div class="border-b border-dashed border-gray-200 h-1/4"></div>
                    <div class="border-b border-dashed border-gray-200 h-1/4"></div>
                </div>
                
                <!-- Bars container with loading state -->
                <div id="weekly-bars" class="ml-12 h-full flex items-end justify-around relative z-10">
                    <!-- Initial loading state -->
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

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-4 border-t border-gray-100" id="summary-stats">
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
        <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center">
            <div class="flex items-center justify-between w-full mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Application Status</h2>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Live</span>
            </div>

            <!-- Donut Chart Container -->
            <div class="relative w-48 h-48 mb-6">
                <div id="donut-chart" class="w-full h-full rounded-full shadow-inner"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-white rounded-full shadow-sm flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-gray-700" id="completion-percentage">0%</span>
                    <span class="text-[10px] text-gray-500">complete</span>
                </div>
            </div>

            <!-- Legend with progress bars -->
            <div class="w-full space-y-4 mt-2" id="status-legend"></div>

            <!-- Stats Summary -->
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
                <!-- Recent activities will be loaded dynamically -->
                <div class="flex items-center justify-center p-4">
                    <svg class="animate-spin h-5 w-5 text-[#155386]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- UPCOMING DEADLINES -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Upcoming Deadlines</h3>
            <p class="text-xs text-gray-500 mt-1">Applications needing attention</p>
        </div>
        <div class="flex items-center gap-3">
                        <span class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full font-medium" id="deadline-count">0 due soon</span>
            <a href="/staff/applications" class="text-sm text-[#155386] hover:text-[#40798C] font-medium">
                View all
            </a>
        </div>
    </div>

    <div id="deadline-list" class="space-y-4">
        <!-- Deadlines will be loaded dynamically -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking position...');
        
        // Check if user needs to set position
        setTimeout(() => {
            checkPositionNeeded();
        }, 500);
        
        // Load dashboard data
        loadDashboardData();
        
        // Add event listener for period change
        document.getElementById('trend-period')?.addEventListener('change', function() {
            loadDashboardData(this.value);
        });
        
        // Setup position form submission
        setupPositionForm();
        
        // Close modal when clicking outside
        document.getElementById('positionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePositionModal();
            }
        });
    });

    // POSITION MODAL FUNCTIONS (keep existing)
    async function checkPositionNeeded() {
        try {
            console.log('Checking if position needed...');
            const response = await fetch('/staff/position/check');
            const data = await response.json();
            console.log('Position check response:', data);
            
            if (data.needs_position) {
                console.log('Position needed, showing modal');
                showPositionModal();
            } else {
                console.log('Position already set');
            }
        } catch (error) {
            console.error('Error checking position:', error);
        }
    }

    function showPositionModal() {
        const modal = document.getElementById('positionModal');
        const modalContent = document.getElementById('modalContent');
        modal.style.display = 'flex';
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 50);
    }

    function hidePositionModal() {
        const modal = document.getElementById('positionModal');
        const modalContent = document.getElementById('modalContent');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function setupPositionForm() {
        const form = document.getElementById('positionForm');
        const errorDiv = document.getElementById('positionError');
        const submitBtn = document.getElementById('submitPositionBtn');
        
        if (!form) {
            console.error('Position form not found');
            return;
        }
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const selectedPosition = document.querySelector('input[name="position"]:checked')?.value;
            
            if (!selectedPosition) {
                errorDiv.textContent = 'Please select a position';
                errorDiv.classList.remove('hidden');
                form.classList.add('animate-shake');
                setTimeout(() => form.classList.remove('animate-shake'), 500);
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Saving...</span>
            `;
            
            errorDiv.classList.add('hidden');
            
            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) throw new Error('CSRF token not found');
                
                const response = await fetch('/staff/position/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ position: selectedPosition })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.errors?.position?.[0] || data.error || 'Failed to save position');
                }
                
                showToast('Position saved successfully!', 'success');
                hidePositionModal();
                updateHeaderWithPosition(selectedPosition);
                await loadDashboardData();
                
            } catch (error) {
                console.error('Error saving position:', error);
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <span>Save Position</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;
            }
        });
    }

    function updateHeaderWithPosition(position) {
        const headerRight = document.querySelector('.flex.flex-col.md\\:flex-row.md\\:items-center.md\\:justify-between.mb-6 .mt-2.md\\:mt-0');
        if (headerRight) {
            const positionDisplay = position.replace('_', ' ').replace('bfp', 'BFP');
            headerRight.innerHTML = `
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-[#155386] rounded-lg text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">${positionDisplay.charAt(0).toUpperCase() + positionDisplay.slice(1)}</span>
                </span>
            `;
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[60] px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 transform transition-all duration-300 translate-x-0 ${
            type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'
        }`;
        toast.innerHTML = `
            <svg class="w-5 h-5 ${type === 'success' ? 'text-green-500' : 'text-red-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                }
            </svg>
            <span class="text-sm font-medium">${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // DASHBOARD FUNCTIONS
    async function loadDashboardData(period = 'this_month') {
        try {
            await loadStats();
            await loadWeeklyTrend(period);
            await loadRecentActivities();
            await loadDeadlines();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    async function loadStats() {
        try {
            const response = await fetch('/staff/applications/stats');
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
                            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                +${growthPercent}% from last month
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
                            <p class="text-xs text-red-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                +${stats.new_today || 0} new today
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
                                ${stats.completion_rate || 0}% on-time rate
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
                <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-red-500 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">For Release</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">${stats.for_release || 0}</p>
                            <p class="text-xs text-orange-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
        }
    }

    async function loadWeeklyTrend(period) {
        try {
            const response = await fetch(`/staff/applications/weekly-trend?period=${period}`);
            const data = await response.json();
            const barsContainer = document.getElementById('weekly-bars');
            const weeks = data.weeks || ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            const values = data.values || [45, 62, 58, 71];
            const colors = ['from-[#155386] to-[#40798C]', 'from-[#40798C] to-[#70A9A1]', 'from-[#70A9A1] to-[#9EC5CB]', 'from-[#0F3B5A] to-[#155386]'];
            const maxValue = Math.max(...values);
            const scaleFactor = maxValue > 0 ? 160 / maxValue : 1;
            let barsHtml = '';
            let total = 0;
            
            weeks.forEach((week, index) => {
                const height = Math.max(20, values[index] * scaleFactor);
                total += values[index];
                barsHtml += `
                    <div class="flex flex-col items-center w-16 group">
                        <div class="relative">
                            <div class="w-10 bg-gradient-to-t ${colors[index % colors.length]} rounded-t-lg group-hover:brightness-110 group-hover:scale-105 transition-all" style="height: ${height}px;"></div>
                            <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">${values[index]} applications</span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-600">${week}</p>
                        <span class="text-sm font-bold text-[#155386]">${values[index]}</span>
                    </div>
                `;
            });
            
            barsContainer.innerHTML = barsHtml;
            const avg = Math.round(total / weeks.length);
            const peak = Math.max(...values);
            
            document.getElementById('total-apps').textContent = total;
            document.getElementById('avg-apps').textContent = avg;
            document.getElementById('peak-apps').textContent = peak;
            
            if (values.length >= 2) {
                const growth = ((values[values.length-1] - values[0]) / values[0] * 100).toFixed(1);
                document.getElementById('growth-rate').textContent = (growth > 0 ? '+' : '') + growth + '%';
                document.getElementById('growth-rate').className = growth >= 0 ? 'text-lg font-bold text-green-600' : 'text-lg font-bold text-red-600';
            }
        } catch (error) {
            console.error('Error loading weekly trend:', error);
        }
    }

    function updateDonutChart(stats) {
        const total = stats.total || 0;
        const pending = stats.pending || 0;
        const underReview = stats.under_review || 0;
        const approved = stats.approved || 0;
        const forRelease = stats.for_release || 0;
        const verified = stats.verified || 0;
        const rejected = stats.rejected || 0;
        
        const pendingPercent = total > 0 ? (pending / total * 100).toFixed(1) : 0;
        const underReviewPercent = total > 0 ? (underReview / total * 100).toFixed(1) : 0;
        const approvedPercent = total > 0 ? (approved / total * 100).toFixed(1) : 0;
        const forReleasePercent = total > 0 ? (forRelease / total * 100).toFixed(1) : 0;
        const verifiedPercent = total > 0 ? (verified / total * 100).toFixed(1) : 0;
        const rejectedPercent = total > 0 ? (rejected / total * 100).toFixed(1) : 0;
        
        document.getElementById('completion-percentage').textContent = verifiedPercent + '%';
        
        const completedPercent = verifiedPercent;
        const pendingTotal = (parseFloat(pendingPercent) + parseFloat(underReviewPercent)).toFixed(1);
        const otherTotal = (parseFloat(approvedPercent) + parseFloat(forReleasePercent) + parseFloat(rejectedPercent)).toFixed(1);
        const pendingAngle = pendingTotal * 3.6;
        const completedAngle = completedPercent * 3.6;
        
        const donutChart = document.getElementById('donut-chart');
        donutChart.style.background = `conic-gradient(
            #F59E0B 0deg ${pendingAngle}deg, 
            #10B981 ${pendingAngle}deg ${pendingAngle + completedAngle}deg,
            #94A3B8 ${pendingAngle + completedAngle}deg 360deg
        )`;
        
        const legend = document.getElementById('status-legend');
        legend.innerHTML = `
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Pending/Under Review</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${pendingTotal}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: ${pendingTotal}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Completed</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${completedPercent}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: ${completedPercent}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        <span class="text-sm text-gray-600">Other</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">${otherTotal}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-gray-400 h-1.5 rounded-full" style="width: ${otherTotal}%"></div>
                </div>
            </div>
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
            
            if (!Array.isArray(activities)) {
                console.error('Activities is not an array:', activities);
                listContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">Unable to load activities</p>
                    </div>
                `;
                return;
            }
            
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
            
            // Limit to 5 most recent activities
            const recentActivities = activities.slice(0, 5);
            let activitiesHtml = '';
            
            recentActivities.forEach(activity => {
                const timeAgo = getTimeAgo(activity.created_at);
                
                let iconColor = 'bg-blue-100 text-blue-600';
                let icon = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                `;
                
                if (activity.action === 'approved') {
                    iconColor = 'bg-green-100 text-green-600';
                    icon = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                } else if (activity.action === 'released') {
                    iconColor = 'bg-purple-100 text-purple-600';
                    icon = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                    `;
                } else if (activity.action === 'registered') {
                    iconColor = 'bg-amber-100 text-amber-600';
                    icon = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    `;
                }
                
                activitiesHtml += `
                    <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full ${iconColor} flex items-center justify-center">
                                ${icon}
                            </div>
                            <span class="font-medium text-gray-700">${activity.description || 'Unknown activity'}</span>
                        </div>
                        <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full">${timeAgo}</span>
                    </li>
                `;
            });
            
            listContainer.innerHTML = activitiesHtml;
            
        } catch (error) {
            console.error('Error loading recent activities:', error);
            document.getElementById('recent-activity-list').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p class="text-sm">Failed to load activities</p>
                </div>
            `;
        }
    }

    async function loadDeadlines() {
        try {
            const response = await fetch('/staff/applications/upcoming-deadlines');
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const deadlines = await response.json();
            const listContainer = document.getElementById('deadline-list');
            const deadlineCount = document.getElementById('deadline-count');
            
            if (!Array.isArray(deadlines)) {
                console.error('Deadlines is not an array:', deadlines);
                listContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm">Unable to load deadlines</p>
                    </div>
                `;
                deadlineCount.textContent = '0 due soon';
                return;
            }
            
            deadlineCount.textContent = `${deadlines.length || 0} due soon`;
            
            if (!deadlines || deadlines.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm">No upcoming deadlines</p>
                    </div>
                `;
                return;
            }
            
            // Limit to 5 deadlines (they already are from the API, but just to be safe)
            const recentDeadlines = deadlines.slice(0, 5);
            let deadlinesHtml = '';
            
            recentDeadlines.forEach(deadline => {
                const daysLeft = deadline.days_left;
                let colorClass = 'text-red-600';
                let bgColor = 'bg-red-100';
                
                if (daysLeft > 5) {
                    colorClass = 'text-yellow-600';
                    bgColor = 'bg-yellow-100';
                } else if (daysLeft > 2) {
                    colorClass = 'text-orange-600';
                    bgColor = 'bg-orange-100';
                }
                
                deadlinesHtml += `
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full ${bgColor} flex items-center justify-center ${colorClass}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${deadline.application_name || 'Unknown'}</p>
                                <p class="text-xs text-gray-500">${deadline.applicant_name || 'Unknown'}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold ${colorClass}">${daysLeft} days left</p>
                            <p class="text-xs text-gray-400">${deadline.due_date || 'N/A'}</p>
                        </div>
                    </div>
                `;
            });
            
            listContainer.innerHTML = deadlinesHtml;
            
        } catch (error) {
            console.error('Error loading deadlines:', error);
            document.getElementById('deadline-list').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p class="text-sm">Failed to load deadlines</p>
                </div>
            `;
            document.getElementById('deadline-count').textContent = '0 due soon';
        }
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' mins ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        return Math.floor(seconds / 86400) + ' days ago';
    }
    
    // Load trend data functions
    document.addEventListener('DOMContentLoaded', function() {
        loadTrendData();
        document.getElementById('trend-period').addEventListener('change', function() {
            loadTrendData();
        });
    });

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
            const response = await fetch(`/staff/applications/trend?period=${period}`);
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            const data = await response.json();
            if (data.success) {
                renderTrendChart(data);
            } else {
                showErrorState('No data available');
            }
        } catch (error) {
            console.error('Error loading trend data:', error);
            showErrorState('Failed to load data');
        }
    }

    function renderTrendChart(data) {
        const barsContainer = document.getElementById('weekly-bars');
        const values = data.values || [];
        const labels = data.labels || [];
        
        if (values.length === 0) {
            showErrorState('No data available');
            return;
        }
        
        const maxValue = Math.max(...values, 1);
        updateYAxisLabels(maxValue);
        
        let barsHtml = '';
        const gradientColors = ['from-[#155386] to-[#40798C]', 'from-[#40798C] to-[#70A9A1]', 'from-[#70A9A1] to-[#9EC5CB]', 'from-[#9EC5CB] to-[#B8D8E3]'];
        
        values.forEach((value, index) => {
            const percentage = (value / maxValue) * 100;
            const colorIndex = index % gradientColors.length;
            const formattedValue = value.toLocaleString();
            
            barsHtml += `
                <div class="group relative flex flex-col items-center justify-end h-full w-16">
                    <div class="relative w-10 bg-gradient-to-t ${gradientColors[colorIndex]} rounded-t-lg transition-all duration-300 hover:brightness-110 hover:scale-105 cursor-pointer"
                         style="height: ${percentage}%; min-height: 4px;">
                        <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none z-20">
                            ${formattedValue} applications
                        </div>
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-full text-xs font-medium text-[#155386] opacity-0 group-hover:opacity-100 transition">
                            ${value}
                        </div>
                    </div>
                    <span class="mt-2 text-xs font-medium text-gray-600 truncate w-full text-center" title="${labels[index]}">
                        ${labels[index]}
                    </span>
                </div>
            `;
        });
        
        barsContainer.innerHTML = barsHtml;
        updateSummaryStats(values);
    }

    function updateYAxisLabels(maxValue) {
        const yAxisLabels = document.getElementById('y-axis-labels');
        const labels = [Math.ceil(maxValue), Math.ceil(maxValue * 0.75), Math.ceil(maxValue * 0.5), Math.ceil(maxValue * 0.25), 0];
        yAxisLabels.innerHTML = labels.map(label => `<span>${label.toLocaleString()}</span>`).join('');
    }

    function updateSummaryStats(values) {
        const total = values.reduce((a, b) => a + b, 0);
        const avg = Math.round(total / values.length);
        const peak = Math.max(...values);
        
        const half = Math.floor(values.length / 2);
        const firstHalf = values.slice(0, half);
        const secondHalf = values.slice(half);
        const firstHalfAvg = firstHalf.length > 0 ? firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length : 0;
        const secondHalfAvg = secondHalf.length > 0 ? secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length : 0;
        
        let growth = 0;
        let growthClass = 'text-gray-800';
        
        if (firstHalfAvg > 0) {
            growth = ((secondHalfAvg - firstHalfAvg) / firstHalfAvg * 100).toFixed(1);
            growthClass = growth >= 0 ? 'text-green-600' : 'text-red-600';
        }
        
        document.getElementById('total-apps').textContent = total.toLocaleString();
        document.getElementById('avg-apps').textContent = avg.toLocaleString();
        document.getElementById('peak-apps').textContent = peak.toLocaleString();
        
        const growthElement = document.getElementById('growth-rate');
        growthElement.textContent = growth > 0 ? `+${growth}%` : `${growth}%`;
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

    #positionModal {
        transition: opacity 0.3s ease;
        background-color: transparent;
        pointer-events: none;
    }
    
    #positionModal.hidden {
        opacity: 0;
        pointer-events: none;
    }

    #modalContent {
        pointer-events: auto !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }

    #positionForm label,
    #positionForm button,
    #positionForm input {
        cursor: pointer;
    }

    #modalContent button {
        cursor: pointer;
    }

    #positionModal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        backdrop-filter: blur(2px);
        background-color: rgba(0,0,0,0.1);
        pointer-events: none;
    }
</style>
@endsection