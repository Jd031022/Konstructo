@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back! Here's your applications overview.</p>
        </div>
    </div>

      <!-- TOP STATS - 4 cards in one row with blue icons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Total Applications -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-orange-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">5,423</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        +12.5% from last month
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

        <!-- Pending Review -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-green-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">103</p>
                    <p class="text-xs text-red-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        +8 new today
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

        <!-- Completed -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-blue-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Completed</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">190</p>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        95% on-time rate
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

        <!-- For Releasing -->
        <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-all duration-200 border-l-4 border-red-500 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">For Releasing</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">189</p>
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
                    <select class="appearance-none border border-gray-200 rounded-lg text-sm px-4 py-2.5 pr-8 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- BAR GRAPH with Y-axis -->
            <div class="relative h-72">
                <!-- Y-axis lines and labels -->
                <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-400 py-2">
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
                
                <!-- Bars container -->
                <div class="ml-12 h-full flex items-end justify-around relative z-10">
                    
                    <!-- Week 1 - Blue -->
                    <div class="flex flex-col items-center w-16 group">
                        <div class="relative">
                            <div class="w-10 bg-gradient-to-t from-[#155386] to-[#40798C] rounded-t-lg group-hover:brightness-110 group-hover:scale-105 transition-all" style="height: 112px;"></div>
                            <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">45 applications</span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-600">Week 1</p>
                        <span class="text-sm font-bold text-[#155386]">45</span>
                    </div>
                    
                    <!-- Week 2 - Teal -->
                    <div class="flex flex-col items-center w-16 group">
                        <div class="relative">
                            <div class="w-10 bg-gradient-to-t from-[#40798C] to-[#70A9A1] rounded-t-lg group-hover:brightness-110 group-hover:scale-105 transition-all" style="height: 155px;"></div>
                            <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">62 applications</span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-600">Week 2</p>
                        <span class="text-sm font-bold text-[#40798C]">62</span>
                    </div>
                    
                    <!-- Week 3 - Light Blue -->
                    <div class="flex flex-col items-center w-16 group">
                        <div class="relative">
                            <div class="w-10 bg-gradient-to-t from-[#70A9A1] to-[#9EC5CB] rounded-t-lg group-hover:brightness-110 group-hover:scale-105 transition-all" style="height: 98px;"></div>
                            <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">58 applications</span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-600">Week 3</p>
                        <span class="text-sm font-bold text-[#40798C]">58</span>
                    </div>
                    
                    <!-- Week 4 - Dark Blue -->
                    <div class="flex flex-col items-center w-16 group">
                        <div class="relative">
                            <div class="w-10 bg-gradient-to-t from-[#0F3B5A] to-[#155386] rounded-t-lg group-hover:brightness-110 group-hover:scale-105 transition-all" style="height: 145px;"></div>
                            <span class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">71 applications</span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-600">Week 4</p>
                        <span class="text-sm font-bold text-[#155386]">71</span>
                    </div>
                    
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-4 border-t border-gray-100">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-bold text-gray-800">236</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Average</p>
                    <p class="text-lg font-bold text-gray-800">59</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Peak</p>
                    <p class="text-lg font-bold text-gray-800">71</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Growth</p>
                    <p class="text-lg font-bold text-green-600">+57.7%</p>
                </div>
            </div>

        </div>

        <!-- DONUT CHART - Overall -->
        <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center">

            <div class="flex items-center justify-between w-full mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Overall</h2>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Live</span>
            </div>

            <!-- Donut Chart Container -->
            <div class="relative w-48 h-48 mb-6">
                <!-- Simple donut chart using conic-gradient -->
                <div class="w-full h-full rounded-full shadow-inner"
                     style="background: conic-gradient(#F59E0B 0deg 252deg, #10B981 252deg 360deg);">
                </div>
                
                <!-- Center hole for donut effect -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-white rounded-full shadow-sm flex flex-col items-center justify-center">
                    <span class="text-xl font-bold text-gray-700">70%</span>
                    <span class="text-[10px] text-gray-500">complete</span>
                </div>
            </div>

            <!-- Legend with progress bars -->
            <div class="w-full space-y-4 mt-2">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Pending Review</span>
                        </div>
                        <span class="text-sm font-bold text-gray-700">70%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: 70%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Completed</span>
                        </div>
                        <span class="text-sm font-bold text-gray-700">30%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 30%"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-2 gap-4 w-full mt-6 pt-4 border-t border-gray-100">
                <div class="text-center bg-orange-50 rounded-lg p-3">
                    <p class="text-xs text-orange-600 font-medium">Total</p>
                    <p class="text-lg font-bold text-gray-800">5,423</p>
                </div>
                <div class="text-center bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">This Month</p>
                    <p class="text-lg font-bold text-gray-800">+236</p>
                </div>
            </div>

        </div>

    </div>

    <!-- EXTRA SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">

        <!-- RECENT ACTIVITY - Enhanced -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Recent Activity</h3>
                    <p class="text-xs text-gray-500 mt-1">Latest updates from your team</p>
                </div>
                <button class="text-sm text-[#155386] hover:text-[#40798C] font-medium">View all</button>
            </div>

            <ul class="space-y-4 text-sm">
                <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Application #234 submitted</span>
                    </div>
                    <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full">5 mins ago</span>
                </li>

                <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Application #221 approved</span>
                    </div>
                    <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full">20 mins ago</span>
                </li>

                <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Document released</span>
                    </div>
                    <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full">1 hour ago</span>
                </li>

                <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">New staff registered</span>
                    </div>
                    <span class="text-gray-400 text-xs bg-gray-100 px-2 py-1 rounded-full">Today</span>
                </li>
            </ul>
        </div>

       <!-- UPCOMING DEADLINES - Replacement for Quick Actions -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Upcoming Deadlines</h3>
            <p class="text-xs text-gray-500 mt-1">Applications needing attention</p>
        </div>
        <span class="text-xs bg-red-100 text-red-600 px-3 py-1 rounded-full font-medium">5 due soon</span>
    </div>

    <div class="space-y-4">
        <!-- Deadline Item 1 -->
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Building Permit #234</p>
                    <p class="text-xs text-gray-500">Juan Dela Cruz</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-red-600">2 days left</p>
                <p class="text-xs text-gray-400">Mar 15, 2024</p>
            </div>
        </div>

        <!-- Deadline Item 2 -->
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Occupancy Permit #221</p>
                    <p class="text-xs text-gray-500">Maria Santos</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-orange-600">5 days left</p>
                <p class="text-xs text-gray-400">Mar 18, 2024</p>
            </div>
        </div>

        <!-- Deadline Item 3 -->
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Certificate of Completion #118</p>
                    <p class="text-xs text-gray-500">Pedro Reyes</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-yellow-600">1 week left</p>
                <p class="text-xs text-gray-400">Mar 22, 2024</p>
            </div>
        </div>
    </div>

    <!-- View All Link -->
    <div class="mt-6 pt-4 border-t border-gray-100">
        <button class="text-sm text-[#155386] hover:text-[#40798C] font-medium w-full text-center">
            View all deadlines →
        </button>
    </div>
</div>

    </div>

</div>
@endsection