@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your personal information and account settings</p>
        </div>
        
        <!-- Last Login Info -->
        <div class="text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-lg">
            <span class="font-medium">Last login:</span> {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y • h:i A') : 'First login' }}
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Sidebar - Profile Summary -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <!-- Profile Avatar -->
                <div class="relative inline-block">
                    <div class="w-24 h-24 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto">
                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                    </div>
                    <button onclick="openEditModal('avatar')" class="absolute bottom-0 right-0 w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center hover:bg-[#40798C] transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                
                <h2 class="text-xl font-bold text-gray-800 mt-4">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>
                <p class="text-gray-500 text-sm capitalize">{{ auth()->user()->role }}</p>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2 text-sm">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-gray-600">Active Account</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                </div>
            </div>

            <!-- Account Stats Card - Role-based stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Account Statistics</h3>
                
                @if(auth()->user()->role === 'admin')
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Users</span>
                        <span class="text-sm font-bold text-gray-800">{{ \App\Models\User::count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Applications</span>
                        <span class="text-sm font-bold text-gray-800">{{ \App\Models\Application::count() ?? 0 }}</span>
                    </div>
                </div>
                @elseif(auth()->user()->role === 'staff')
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Assigned Applications</span>
                        <span class="text-sm font-bold text-gray-800">{{ auth()->user()->assignedApplications()->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pending Review</span>
                        <span class="text-sm font-bold text-yellow-600">{{ auth()->user()->pendingReviews()->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-sm font-bold text-green-600">{{ auth()->user()->completedReviews()->count() ?? 0 }}</span>
                    </div>
                </div>
                @elseif(auth()->user()->role === 'applicant')
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Applications</span>
                        <span class="text-sm font-bold text-gray-800">{{ auth()->user()->applications()->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pending Review</span>
                        <span class="text-sm font-bold text-yellow-600">{{ auth()->user()->pendingApplications()->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Approved</span>
                        <span class="text-sm font-bold text-green-600">{{ auth()->user()->approvedApplications()->count() ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Rejected</span>
                        <span class="text-sm font-bold text-red-600">{{ auth()->user()->rejectedApplications()->count() ?? 0 }}</span>
                    </div>
                </div>
                @endif
                
                @if(auth()->user()->role !== 'admin')
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="/{{ auth()->user()->role }}/applications" class="text-sm text-[#155386] hover:underline flex items-center justify-between">
                        <span>View All Applications</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Profile Details -->
        <div class="lg:col-span-3 space-y-8">

            <!-- Personal Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                    <h2 class="text-xl font-bold">Personal Information</h2>
                    <button onclick="openEditModal('personal')" class="text-white hover:text-gray-200 transition flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">First Name</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Middle Name</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->middle_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Last Name</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Suffix</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->suffix ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Date of Birth</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('F d, Y') : '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Place of Birth</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->place_of_birth ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Gender</label>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst(auth()->user()->gender ?? '—') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Civil Status</label>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst(auth()->user()->civil_status ?? '—') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Citizenship</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->citizenship ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">TIN</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->tin ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#40798C] to-[#1F363D] text-white flex justify-between items-center">
                    <h2 class="text-xl font-bold">Contact Information</h2>
                    <button onclick="openEditModal('contact')" class="text-white hover:text-gray-200 transition flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Email Address</label>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->email }}</p>
                                @if(auth()->user()->email_verified_at)
                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full">Verified</span>
                                @else
                                <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Unverified</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Mobile Number</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->phone_number ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Telephone Number</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->telephone ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Alternative Email</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->alternative_email ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#1F363D] to-[#155386] text-white flex justify-between items-center">
                    <h2 class="text-xl font-bold">Address Information</h2>
                    <button onclick="openEditModal('address')" class="text-white hover:text-gray-200 transition flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">House/Unit No.</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->house_number ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Street</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->street ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Barangay</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->barangay ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">City/Municipality</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->city ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Province</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->province ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Zip Code</label>
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->zip_code ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Security Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white">
                    <h2 class="text-xl font-bold">Account Security</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Password</p>
                                    <p class="text-xs text-gray-500">Last changed {{ auth()->user()->password_changed_at ? auth()->user()->password_changed_at->diffForHumans() : 'Never' }}</p>
                                </div>
                            </div>
                            <button onclick="openEditModal('password')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Change Password
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Two-Factor Authentication</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Protect your account with 2FA' }}</p>
                                </div>
                            </div>
                            <button onclick="openEditModal('2fa')" class="px-4 py-2 {{ auth()->user()->two_factor_enabled ? 'border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-[#155386] text-white hover:bg-[#40798C]' }} rounded-lg transition text-sm">
                                {{ auth()->user()->two_factor_enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Active Sessions</p>
                                    <p class="text-xs text-gray-500">You're logged in on {{ auth()->user()->sessions()->count() ?? 1 }} device(s)</p>
                                </div>
                            </div>
                            <button onclick="openEditModal('sessions')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Manage
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Card - Admin only or optional -->
            @if(auth()->user()->role === 'admin')
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-6 py-4 bg-red-500 text-white">
                    <h2 class="text-xl font-bold">Danger Zone</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Deactivate Account</p>
                                <p class="text-xs text-gray-500">Temporarily disable your account</p>
                            </div>
                            <button onclick="openEditModal('deactivate')" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition text-sm">
                                Deactivate
                            </button>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Delete Account</p>
                                    <p class="text-xs text-gray-500">Permanently delete your account and all data</p>
                                </div>
                                <button onclick="openEditModal('delete')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<!-- Edit Profile Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#40798C] text-white flex justify-between items-center">
                <h3 class="text-xl font-bold" id="modal-title">Edit Information</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <form id="edit-form" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Dynamic fields will be loaded here -->
                    <div id="modal-fields">
                        <!-- Personal Information Fields -->
                        <div id="personal-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" name="first_name" value="{{ auth()->user()->first_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                    <input type="text" name="middle_name" value="{{ auth()->user()->middle_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="last_name" value="{{ auth()->user()->last_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                                    <select name="suffix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">None</option>
                                        <option value="Jr." {{ auth()->user()->suffix == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                        <option value="Sr." {{ auth()->user()->suffix == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                        <option value="II" {{ auth()->user()->suffix == 'II' ? 'selected' : '' }}>II</option>
                                        <option value="III" {{ auth()->user()->suffix == 'III' ? 'selected' : '' }}>III</option>
                                        <option value="IV" {{ auth()->user()->suffix == 'IV' ? 'selected' : '' }}>IV</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y-m-d') : '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                                    <input type="text" name="place_of_birth" value="{{ auth()->user()->place_of_birth }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                    <select name="civil_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">Select Status</option>
                                        <option value="single" {{ auth()->user()->civil_status == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ auth()->user()->civil_status == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ auth()->user()->civil_status == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ auth()->user()->civil_status == 'separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="divorced" {{ auth()->user()->civil_status == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Citizenship</label>
                                    <input type="text" name="citizenship" value="{{ auth()->user()->citizenship }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">TIN</label>
                                    <input type="text" name="tin" value="{{ auth()->user()->tin }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Fields -->
                        <div id="contact-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">+63</span>
                                        <input type="tel" name="phone_number" value="{{ str_replace('+63', '', auth()->user()->phone_number) }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-[#155386]">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telephone Number</label>
                                    <input type="tel" name="telephone" value="{{ auth()->user()->telephone }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alternative Email</label>
                                    <input type="email" name="alternative_email" value="{{ auth()->user()->alternative_email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Address Fields -->
                        <div id="address-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">House/Unit No.</label>
                                    <input type="text" name="house_number" value="{{ auth()->user()->house_number }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Street</label>
                                    <input type="text" name="street" value="{{ auth()->user()->street }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                    <input type="text" name="barangay" value="{{ auth()->user()->barangay }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                                    <input type="text" name="city" value="{{ auth()->user()->city }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                    <input type="text" name="province" value="{{ auth()->user()->province }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <input type="text" name="zip_code" value="{{ auth()->user()->zip_code }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Fields -->
                        <div id="password-fields" class="space-y-4 hidden">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <p class="text-xs text-gray-500">Password must be at least 8 characters long</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" 
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function openEditModal(type) {
        const modal = document.getElementById('edit-modal');
        const modalTitle = document.getElementById('modal-title');
        
        // Hide all field sections
        document.getElementById('personal-fields')?.classList.add('hidden');
        document.getElementById('contact-fields')?.classList.add('hidden');
        document.getElementById('address-fields')?.classList.add('hidden');
        document.getElementById('password-fields')?.classList.add('hidden');
        
        // Show the selected section
        if (type === 'personal') {
            document.getElementById('personal-fields').classList.remove('hidden');
            modalTitle.textContent = 'Edit Personal Information';
        } else if (type === 'contact') {
            document.getElementById('contact-fields').classList.remove('hidden');
            modalTitle.textContent = 'Edit Contact Information';
        } else if (type === 'address') {
            document.getElementById('address-fields').classList.remove('hidden');
            modalTitle.textContent = 'Edit Address Information';
        } else if (type === 'password') {
            document.getElementById('password-fields').classList.remove('hidden');
            modalTitle.textContent = 'Change Password';
        } else {
            modalTitle.textContent = 'Edit Information';
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('edit-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeEditModal();
                }
            });
        }

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeEditModal();
            }
        });
    });
</script>
@endsection