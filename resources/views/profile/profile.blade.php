@extends('layouts.dashboard')

@section('title', 'My Profile - Konstructo')

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
            <span class="font-medium">Last login:</span> May 10, 2025 • 8:30 AM
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
                        JD
                    </div>
                    <button class="absolute bottom-0 right-0 w-8 h-8 bg-[#155386] text-white rounded-full flex items-center justify-center hover:bg-[#40798C] transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                
                <h2 class="text-xl font-bold text-gray-800 mt-4">Juan Dela Cruz</h2>
                <p class="text-gray-500 text-sm">Applicant</p>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2 text-sm">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-gray-600">Active Account</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Member since May 2025</p>
                </div>
            </div>

            <!-- Account Stats Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Account Statistics</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Applications</span>
                        <span class="text-sm font-bold text-gray-800">8</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pending Review</span>
                        <span class="text-sm font-bold text-yellow-600">3</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Approved</span>
                        <span class="text-sm font-bold text-green-600">4</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Rejected</span>
                        <span class="text-sm font-bold text-red-600">1</span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="/user/applications" class="text-sm text-[#155386] hover:underline flex items-center justify-between">
                        <span>View All Applications</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
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
                            <p class="text-sm font-medium text-gray-800">Juan</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Middle Name</label>
                            <p class="text-sm font-medium text-gray-800">Santos</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Last Name</label>
                            <p class="text-sm font-medium text-gray-800">Dela Cruz</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Suffix</label>
                            <p class="text-sm font-medium text-gray-800">—</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Date of Birth</label>
                            <p class="text-sm font-medium text-gray-800">January 15, 1990</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Place of Birth</label>
                            <p class="text-sm font-medium text-gray-800">Legazpi City, Albay</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Gender</label>
                            <p class="text-sm font-medium text-gray-800">Male</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Civil Status</label>
                            <p class="text-sm font-medium text-gray-800">Married</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Citizenship</label>
                            <p class="text-sm font-medium text-gray-800">Filipino</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">TIN</label>
                            <p class="text-sm font-medium text-gray-800">123-456-789-000</p>
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
                                <p class="text-sm font-medium text-gray-800">juan.delacruz@email.com</p>
                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full">Verified</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Mobile Number</label>
                            <p class="text-sm font-medium text-gray-800">+63 917 123 4567</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Telephone Number</label>
                            <p class="text-sm font-medium text-gray-800">(052) 123-4567</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Alternative Email</label>
                            <p class="text-sm font-medium text-gray-800">juan.work@email.com</p>
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
                            <p class="text-sm font-medium text-gray-800">123</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Street</label>
                            <p class="text-sm font-medium text-gray-800">Rizal Street</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Barangay</label>
                            <p class="text-sm font-medium text-gray-800">San Jose</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">City/Municipality</label>
                            <p class="text-sm font-medium text-gray-800">Legazpi City</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Province</label>
                            <p class="text-sm font-medium text-gray-800">Albay</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Zip Code</label>
                            <p class="text-sm font-medium text-gray-800">4500</p>
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
                                    <p class="text-xs text-gray-500">Last changed 30 days ago</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
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
                                    <p class="text-xs text-gray-500">Protect your account with 2FA</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#40798C] transition text-sm">
                                Enable
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
                                    <p class="text-xs text-gray-500">You're logged in on 2 devices</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                                Manage
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Card -->
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
                            <button class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition text-sm">
                                Deactivate
                            </button>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Delete Account</p>
                                    <p class="text-xs text-gray-500">Permanently delete your account and all data</p>
                                </div>
                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                <form id="edit-form">
                    <!-- Dynamic fields will be loaded here -->
                    <div id="modal-fields">
                        <!-- Personal Information Fields -->
                        <div id="personal-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" value="Juan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                    <input type="text" value="Santos" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" value="Dela Cruz" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="">None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input type="date" value="1990-01-15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                                    <input type="text" value="Legazpi City, Albay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="male" selected>Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                        <option value="single">Single</option>
                                        <option value="married" selected>Married</option>
                                        <option value="widowed">Widowed</option>
                                        <option value="separated">Separated</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Citizenship</label>
                                    <input type="text" value="Filipino" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">TIN</label>
                                    <input type="text" value="123-456-789-000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Fields -->
                        <div id="contact-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" value="juan.delacruz@email.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">+63</span>
                                        <input type="tel" value="9171234567" class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-[#155386]">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Telephone Number</label>
                                    <input type="tel" value="0521234567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alternative Email</label>
                                    <input type="email" value="juan.work@email.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                            </div>
                        </div>

                        <!-- Address Fields -->
                        <div id="address-fields" class="space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">House/Unit No.</label>
                                    <input type="text" value="123" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Street</label>
                                    <input type="text" value="Rizal Street" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                    <input type="text" value="San Jose" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                                    <input type="text" value="Legazpi City" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                    <input type="text" value="Albay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <input type="text" value="4500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386]">
                                </div>
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
        document.getElementById('personal-fields').classList.add('hidden');
        document.getElementById('contact-fields').classList.add('hidden');
        document.getElementById('address-fields').classList.add('hidden');
        
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
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEditModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeEditModal();
            }
        });
    });
</script>
@endsection