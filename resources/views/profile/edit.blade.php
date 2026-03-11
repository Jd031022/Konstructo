@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-900">Edit Profile</h2>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Add your form fields here -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Copy fields from show view but make them inputs -->
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="submit" class="bg-[#155386] hover:bg-[#40798C] text-white px-4 py-2 rounded-lg">
                        Save Changes
                    </button>
                    <a href="{{ route('profile.show') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection