@extends('layouts.dashboard')

@section('content')

<!-- Main Container - Add this to center and constrain width -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Services Title -->
    <div class="text-center mb-10">
        <h1 class="text-2xl font-semibold">Services offered</h1>
        <p class="text-gray-500 max-w-2xl mx-auto mt-2  ">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
        </p>
    </div>

    <!-- Services Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">

        <!-- Building Permit -->
        <div class="bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition">

            <img src="{{ asset('images/bp.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Building Permit
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Apply for building permit. Manage and track your application.
            </p>

            <button class="mt-4 w-full bg-[#155386] text-white py-2 rounded-full hover:bg-[#1F363D]">
                Apply
            </button>

        </div>

        <!-- Coming Soon -->
        <div class="bg-white rounded-xl shadow-md p-4">

            <img src="{{ asset('images/cm.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Coming soon
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Exciting new services coming soon. Stay tuned for updates!
            </p>

        </div>

        <!-- Coming Soon -->
        <div class="bg-white rounded-xl shadow-md p-4">

            <img src="{{ asset('images/cm.jpg') }}"
                 class="rounded-lg mb-4 w-full h-40 object-cover shadow-lg">

            <h3 class="text-[#155386] font-semibold text-lg">
                Coming soon
            </h3>

            <p class="text-gray-500 text-sm mt-2">
                Exciting new services coming soon. Stay tuned for updates!
            </p>

        </div>

    </div>
    
</div> <!-- End of container -->

@endsection