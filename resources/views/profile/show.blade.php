{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg" role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg" role="alert">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="font-bold">Please fix the following errors:</p>
        </div>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your personal information and account settings</p>
        </div>
        
        <!-- Last Login Info -->
        <div class="text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-lg">
            <span class="font-medium">Last login:</span> 
            {{ optional($user->profile)->last_login_at ? optional($user->profile)->last_login_at->format('M d, Y • h:i A') : 'First login' }}
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
                    @php
                        $user = auth()->user();
                        $avatarPath = $user->avatar;
                        
                        if (!empty($avatarPath)) {
                            $avatarUrl = asset('storage/' . $avatarPath) . '?v=' . time();
                        } else {
                            $fullName = urlencode($user->first_name . ' ' . $user->last_name);
                            $avatarUrl = "https://ui-avatars.com/api/?name={$fullName}&size=96&background=155386&color=fff&bold=true";
                        }
                    @endphp
                    
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg mx-auto">
                        <img src="{{ $avatarUrl }}" 
                             alt="{{ $user->full_name }}" 
                             class="w-full h-full object-cover"
                             id="avatar-image"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $user->first_name }} {{ $user->last_name }}') + '&size=96&background=155386&color=fff&bold=true';">
                    </div>
                    
                    <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden" onchange="previewAndUploadAvatar(this)">
                    </form>
                    
                    <button type="button" onclick="document.getElementById('avatar-input').click()" 
                            class="absolute bottom-0 right-0 w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center hover:bg-[#40798C] transition shadow-lg hover:scale-110 transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                
                <h2 class="text-xl font-bold text-gray-800 mt-4">{{ $user->full_name }}</h2>
                <p class="text-gray-500 text-sm capitalize">{{ $user->role }}</p>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2 text-sm">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-gray-600">Active Account</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

           <!-- Account Statistics Card -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-800 mb-4">Account Statistics</h3>
    
    @if($user->role === 'admin')
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Total Users</span>
            <span class="text-sm font-bold text-gray-800">{{ \App\Models\User::count() }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Total Applications</span>
            <span class="text-sm font-bold text-gray-800">{{ \App\Models\ApplicationDocument::count() ?? 0 }}</span>
        </div>
    </div>
    @else
    <!-- For all non-admin users (applicant, staff, engineer) - show their own application statistics -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Total Applications</span>
            <span class="text-sm font-bold text-gray-800">{{ $user->total_applications ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Draft</span>
            <span class="text-sm font-bold text-gray-500">{{ $user->draft_count ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Pending Review</span>
            <span class="text-sm font-bold text-yellow-600">{{ $user->pending_count ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Verified</span>
            <span class="text-sm font-bold text-blue-600">{{ $user->verified_count ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Approved</span>
            <span class="text-sm font-bold text-green-600">{{ $user->approved_count ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Rejected</span>
            <span class="text-sm font-bold text-red-600">{{ $user->rejected_count ?? 0 }}</span>
        </div>
    </div>
    @endif
    
    @if($user->role !== 'admin')
    <div class="mt-4 pt-4 border-t border-gray-100">
        <a href="/{{ $user->role }}/applications" class="text-sm text-[#155386] hover:underline flex items-center justify-between">
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
                            <p class="text-sm font-medium text-gray-800">{{ $user->first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Middle Name</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->middle_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Last Name</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Suffix</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->suffix ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Date of Birth</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->date_of_birth ? optional($user->profile)->date_of_birth->format('F d, Y') : '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Place of Birth</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->place_of_birth ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Gender</label>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst(optional($user->profile)->gender ?? '—') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Civil Status</label>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst(optional($user->profile)->civil_status ?? '—') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Citizenship</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->citizenship ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">TIN</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->tin ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#155386] to-[#1F363D] text-white flex justify-between items-center">
                    <h2 class="text-xl font-bold">Account Information</h2>
                    <button onclick="openEditModal('account')" class="text-white hover:text-gray-200 transition flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Username</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->username }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Role</label>
                            <p class="text-sm font-medium text-gray-800 capitalize">{{ $user->role }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Account Created</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Last Updated</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->updated_at->format('F d, Y') }}</p>
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
                                <p class="text-sm font-medium text-gray-800">{{ $user->email }}</p>
                                @if($user->email_verified_at)
                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full">Verified</span>
                                @else
                                <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">Unverified</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Mobile Number</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->phone_number ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Telephone Number</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->telephone ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Alternative Email</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->alternative_email ?? '—' }}</p>
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
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->house_number ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Street</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->street ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Barangay</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->barangay ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">City/Municipality</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->city ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Province</label>
                            <p class="text-sm font-medium text-gray-800">{{ optional($user->profile)->province ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Zip Code</label>
                            <p class="text-sm font-medium text-gray-800">{{ $user->zip_code ?? '—' }}</p>
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
                                    <p class="text-xs text-gray-500">Last changed {{ optional($user->profile)->password_changed_at ? optional($user->profile)->password_changed_at->diffForHumans() : 'Never' }}</p>
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
                                    <p class="text-xs text-gray-500">{{ optional($user->profile)->two_factor_enabled ? 'Enabled' : 'Protect your account with 2FA' }}</p>
                                </div>
                            </div>
                            <button onclick="openEditModal('2fa')" class="px-4 py-2 {{ optional($user->profile)->two_factor_enabled ? 'border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-[#155386] text-white hover:bg-[#40798C]' }} rounded-lg transition text-sm">
                                {{ optional($user->profile)->two_factor_enabled ? 'Disable' : 'Enable' }}
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
                                    <p class="text-xs text-gray-500">You're logged in on {{ $user->sessions()->count() ?? 1 }} device(s)</p>
                                </div>
                            </div>
                            <button onclick="openEditModal('sessions')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Manage
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Card - Admin only -->
            @if($user->role === 'admin')
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
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                                    <select name="suffix" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">None</option>
                                        <option value="Jr." {{ old('suffix', $user->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                        <option value="Sr." {{ old('suffix', $user->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                        <option value="II" {{ old('suffix', $user->suffix) == 'II' ? 'selected' : '' }}>II</option>
                                        <option value="III" {{ old('suffix', $user->suffix) == 'III' ? 'selected' : '' }}>III</option>
                                        <option value="IV" {{ old('suffix', $user->suffix) == 'IV' ? 'selected' : '' }}>IV</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->profile)->date_of_birth ? optional($user->profile)->date_of_birth->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                                    <input type="text" name="place_of_birth" value="{{ old('place_of_birth', optional($user->profile)->place_of_birth) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', optional($user->profile)->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', optional($user->profile)->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', optional($user->profile)->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                    <select name="civil_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">Select Status</option>
                                        <option value="single" {{ old('civil_status', optional($user->profile)->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('civil_status', optional($user->profile)->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('civil_status', optional($user->profile)->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ old('civil_status', optional($user->profile)->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="divorced" {{ old('civil_status', optional($user->profile)->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Citizenship</label>
                                    <input type="text" name="citizenship" value="{{ old('citizenship', optional($user->profile)->citizenship) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">TIN</label>
                                    <input type="text" name="tin" value="{{ old('tin', optional($user->profile)->tin) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Fields -->
                        <div id="account-fields" class="space-y-4 hidden">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                    <p class="text-xs text-gray-500 mt-1">Your unique username for login</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-blue-700">
                                        <span class="font-medium">Note:</span> Changing your username will affect how you log in.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Fields -->
                        <div id="contact-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">+63</span>
                                        <input type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number ? str_replace('+63', '', $user->phone_number) : '') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-[#155386]">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telephone Number</label>
                                    <input type="tel" name="telephone" value="{{ old('telephone', optional($user->profile)->telephone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alternative Email</label>
                                    <input type="email" name="alternative_email" value="{{ old('alternative_email', optional($user->profile)->alternative_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Address Fields -->
                        <div id="address-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">House/Unit No.</label>
                                    <input type="text" name="house_number" value="{{ old('house_number', optional($user->profile)->house_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Street</label>
                                    <input type="text" name="street" value="{{ old('street', optional($user->profile)->street) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                    <input type="text" name="barangay" value="{{ old('barangay', optional($user->profile)->barangay) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                                    <input type="text" name="city" value="{{ old('city', optional($user->profile)->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                    <input type="text" name="province" value="{{ old('province', optional($user->profile)->province) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <input type="text" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
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
    let currentEditType = null;

    function openEditModal(type) {
        const modal = document.getElementById('edit-modal');
        const modalTitle = document.getElementById('modal-title');
        currentEditType = type;
        
        // Hide all field sections
        document.getElementById('personal-fields')?.classList.add('hidden');
        document.getElementById('account-fields')?.classList.add('hidden');
        document.getElementById('contact-fields')?.classList.add('hidden');
        document.getElementById('address-fields')?.classList.add('hidden');
        document.getElementById('password-fields')?.classList.add('hidden');
        
        // Show the selected section
        if (type === 'personal') {
            document.getElementById('personal-fields').classList.remove('hidden');
            modalTitle.textContent = 'Edit Personal Information';
        } else if (type === 'account') {
            document.getElementById('account-fields').classList.remove('hidden');
            modalTitle.textContent = 'Edit Account Information';
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
        
        // Clear any error states if needed
        const errorMessages = document.querySelectorAll('.text-red-500');
        errorMessages.forEach(msg => msg.remove());
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

        // Auto-hide success message after 5 seconds
        const successMessage = document.querySelector('.bg-green-100');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => {
                    successMessage.remove();
                }, 500);
            }, 5000);
        }
    });
    
    function previewAndUploadAvatar(input) {
        if (input.files && input.files[0]) {
            // Check file size (max 2MB)
            if (input.files[0].size > 2 * 1024 * 1024) {
                alert('File is too large. Maximum size is 2MB.');
                input.value = '';
                return;
            }
            
            // Check file type
            if (!input.files[0].type.match('image.*')) {
                alert('Please select an image file (JPEG, PNG, JPG, GIF)');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Update the image preview
                const avatarImage = document.getElementById('avatar-image');
                if (avatarImage) {
                    avatarImage.src = e.target.result;
                }
                
                // Show loading state
                const button = document.querySelector('button[onclick*="avatar-input"]');
                const originalHtml = button.innerHTML;
                button.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                button.disabled = true;
                
                // Submit the form
                input.form.submit();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Clear cache on page load to show new image
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };

    // Add cache busting parameter to image
    document.addEventListener('DOMContentLoaded', function() {
        const avatarImage = document.getElementById('avatar-image');
        if (avatarImage && !avatarImage.src.includes('ui-avatars')) {
            // Add timestamp to force reload
            let src = avatarImage.src.split('?')[0];
            avatarImage.src = src + '?v=' + new Date().getTime();
        }
    });
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection