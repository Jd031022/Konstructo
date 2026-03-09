@extends('layouts.dashboard')

@section('title', 'Application Details')

@section('content')

<div class="p-8 bg-gray-50 min-h-screen">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Application Details</h1>
            <p class="text-gray-500 text-sm">Application #APP-2025-001</p>
        </div>

        <span class="px-4 py-2 text-sm font-semibold bg-yellow-100 text-yellow-700 rounded-full">
            Pending
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-6">

            <!-- APPLICANT INFO -->
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h2 class="font-semibold text-lg text-gray-800 mb-4">Applicant Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                    <div>
                        <p class="text-gray-500">Name</p>
                        <p class="font-medium text-gray-800">Jane Doe</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium text-gray-800">example@email.com</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Contact Number</p>
                        <p class="font-medium text-gray-800">09997989999</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Location</p>
                        <p class="font-medium text-gray-800">Ligao City</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Project Name</p>
                        <p class="font-medium text-gray-800">Project Name</p>
                    </div>

                </div>
            </div>


            <!-- UPLOADS -->
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h2 class="font-semibold text-lg text-gray-800 mb-4">Uploads</h2>

                <div class="space-y-3">

                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <div>
                            <p class="font-medium text-gray-700">Application_Form.pdf</p>
                            <p class="text-xs text-gray-500">(2.4 MB)</p>
                        </div>

                        <button class="text-blue-600 text-sm font-medium hover:underline">
                            Download
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-3 border rounded-lg">
                        <p class="font-medium text-gray-700">Google Drive Link</p>

                        <a href="#" class="text-blue-600 text-sm hover:underline">
                            Open Link
                        </a>
                    </div>

                </div>

                <div class="mt-4 text-sm text-gray-500">
                    Posted on: <span class="text-gray-700">February 29, 2025, 2:15 PM</span>
                    <span class="ml-2 text-gray-400">(5 days ago)</span>
                </div>

            </div>


            <!-- DOCUMENT CHECKING -->
            <div class="bg-white shadow-sm rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-lg text-gray-800">
                        Document Checking
                    </h2>

                    <span class="text-sm text-gray-500">2 / 4 Verified</span>
                </div>

                <!-- Progress bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                    <div class="bg-green-500 h-2 rounded-full w-1/2"></div>
                </div>

                <div class="space-y-4">

                    <!-- verified -->
                    <div class="flex justify-between items-center">
                        <p class="text-gray-700">Application Letter</p>
                        <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                            Verified
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <p class="text-gray-700">Building Permit Forms</p>
                        <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                            Verified
                        </span>
                    </div>

                    <!-- pending -->
                    <div class="flex justify-between items-center">
                        <p class="text-gray-700">
                            Architectural Plans and Specifications (5 sets)
                        </p>
                        <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">
                            Pending
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <p class="text-gray-700">
                            Civil/Structural Plans and Specifications (5 sets)
                        </p>
                        <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">
                            Pending
                        </span>
                    </div>

                </div>

            </div>


            <!-- ACTION BUTTONS -->
            <div class="flex justify-end gap-3">

                <button class="px-5 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                    Cancel
                </button>

                <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>

            </div>

        </div>



        <!-- RIGHT SIDEBAR -->
        <div class="space-y-6">

            <!-- STATUS UPDATE -->
            <div class="bg-white shadow-sm rounded-xl p-6">

                <h2 class="font-semibold text-lg text-gray-800 mb-4">
                    Status Update
                </h2>

                <p class="text-sm text-gray-500 mb-3">
                    Current: <span class="font-medium text-yellow-600">Pending</span>
                </p>

                <select class="w-full border rounded-lg p-2 text-sm mb-4">
                    <option>Pending Review</option>
                    <option>Delayed</option>
                    <option>For Releasing</option>
                    <option>Archived</option>
                    <option>Completed</option>
                    <option>On Track</option>
                </select>

                <button class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                    Update Status
                </button>

            </div>


            <!-- HISTORY -->
            <div class="bg-white shadow-sm rounded-xl p-6">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-lg text-gray-800">
                        History
                    </h2>

                    <button class="text-blue-600 text-sm hover:underline">
                        View History
                    </button>
                </div>

                <div class="text-sm text-gray-500 space-y-2">

                    <p>
                        <span class="text-gray-600">Last updated:</span>
                        February 29, 2025
                    </p>

                    <p>
                        <span class="text-gray-600">Updated by:</span>
                        John Santos (Engineer)
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection