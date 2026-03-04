@extends('layouts.app')

@section('title', 'Konstructo')

@section('content')

<!-- HERO SECTION -->
<section class="relative min-h-screen bg-gray-100 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img 
            src="{{ asset('images/cover.jpg') }}" 
            alt="City Hall Background"
            class="w-full h-full object-cover opacity-30"
        >
    </div>

   <!-- NAVBAR -->
<nav class="relative z-10 flex items-center justify-between px-10 py-4 bg-white/70 backdrop-blur-md shadow-sm">
    <!-- Left Spacer (empty div with same width as right buttons to balance) -->
    <div class="w-[180px]"></div>

    <!-- Center Menu - perfectly centered -->
    <ul class="flex gap-8 text-sm font-medium text-gray-700">
        <li>
            <a href="#home" class="px-4 py-1 rounded-full border border-blue-500 text-blue-600">
                Home
            </a>
        </li>
        <li>
            <a href="#courses" class="hover:text-blue-600 transition">Courses</a>
        </li>
        <li>
            <a href="#community" class="hover:text-blue-600 transition">Community</a>
        </li>
        <li>
            <a href="#about" class="hover:text-blue-600 transition">About</a>
        </li>
    </ul>

    <!-- Right Buttons - fixed width to match left spacer -->
    <div class="flex items-center gap-3 w-[180px] justify-end">
        <a href="{{ route('login') }}" 
           class="px-4 py-1.5 text-sm rounded-full bg-gray-200 hover:bg-gray-300 transition">
            Log in
        </a>
        <a href="{{ route('register') }}" 
           class="px-5 py-1.5 text-sm rounded-full bg-blue-600 text-white hover:bg-blue-700 shadow">
            Sign up
        </a>
    </div>
</nav>

    <!-- HERO CONTENT -->
    <div id="home" class="relative z-10 flex items-center justify-center min-h-[40vh]">
    <div class="flex flex-col items-center text-center">
        <img src="{{ asset('images/ligao-seal.png') }}" alt="City Seal" class="w-20 md:w-28 drop-shadow-lg">
    </div>
</div>
</section>

<!-- Features Section -->
<section class="py-20 px-4 bg-white text-gray-800">
    <div class="max-w-6xl mx-auto text-center">
        <h1 class="text-2xl font-bold mb-4">Smart Infrastructure Oversight</h1>
        <p class="mb-12 max-w-2xl mx-auto text-sm text-gray-600">
            Discover how <b>Konsctructo</b> transforms city project monitoring through transparency, real-time updates, and a platform designed for efficient governance.
        </p>

        <!-- Icon Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-12 justify-items-center text-center">
            <!-- Feature 1 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/conference.png" alt="Community Driven" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">Collaborative Project Oversight</h3>
                <p class="text-sm text-gray-600">Konsctructo ensures that city engineers, project teams, and LGU leaders collaborate seamlessly to track project progress, budget utilization, and timelines.</p>
            </div>

            <!-- Feature 2 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/door-opened.png" alt="Accessible" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">Accessible</h3>
                <p class="text-sm text-gray-600">Open to city officials, engineers, planners, and authorized stakeholders — everyone involved in infrastructure management has the information they need.</p>
            </div>

            <!-- Feature 3 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/gift.png" alt="Free for All" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">Free for All</h3>
                <p class="text-sm text-gray-600">The platform is built to support LGU operations without licensing costs, helping cities manage resources efficiently.</p>
            </div>

            <!-- Feature 4 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/smartphone.png" alt="User-Friendly" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">User-Friendly</h3>
                <p class="text-sm text-gray-600">Intuitive dashboard design with easy-to-read metrics, clear status indicators, and interactive project timelines.</p>
            </div>

            <!-- Feature 5 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/synchronize.png" alt="Adaptive" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">Adaptive</h3>
                <p class="text-sm text-gray-600">Accessible on desktop, tablet, or mobile devices — so users can monitor projects from the office or on-site.</p>
            </div>

            <!-- Feature 6 -->
            <div class="flex flex-col items-center text-center max-w-xs">
                <img src="https://img.icons8.com/ios-filled/50/ff7f50/expand.png" alt="Scalable" class="w-12 h-12 mb-4">
                <h3 class="font-semibold text-lg mb-1">Scalable</h3>
                <p class="text-sm text-gray-600">Built to grow with your city’s infrastructure portfolio, from local barangay projects to large-scale city initiatives.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Learning Path with Image Overlay -->
<section class="relative">
    <img src="{{ asset('images/cover2.jpg') }}"
        class="w-full h-[420px] object-cover" 
        alt="Learning Group">
    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center px-6 md:px-20 text-white">
        <h2 class="text-2xl md:text-3xl font-semibold mb-2">Our Community</h2>
        <p class="max-w-2xl text-sm md:text-base leading-relaxed">
            Konsctructo thrives on collaboration between engineers, city planners, and decision-makers. The platform encourages shared knowledge and accountability: 
        </p>
        <p class="max-w-2xl text-sm md:text-base leading-relaxed">
           Connect with project engineers and departments. Share updates, progress photos, and reports. Collaborate on approvals and validations
    </div>
</section>

<!-- Courses Section -->
<section id="courses" class="bg-gray-50 py-16 px-6 md:px-20">
    <div class="max-w-7xl mx-auto text-center mb-12">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">Services Offered</h2>
        <p class="text-gray-600 text-sm md:text-base max-w-2xl mx-auto">
            Explore Konsctructo services. The platform provides key services for city infrastructure management and public engagement:
        </p>
    </div>

    <!-- Course Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        <!-- Course 1 -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
            <img src="{{ asset('images/course1.jpg') }}"
                alt="Child Protection & Rights" 
                class="w-full h-40 object-cover">
            <div class="p-5 text-left">
                <h3 class="font-semibold text-lg mb-2 text-orange-500">Building Permit</h3>
                <p class="text-sm text-gray-600">Apply for building permit. Manage and track your application in one place.</p>
            </div>
        </div>

        <!-- Course 2 -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
            <img src="{{ asset('images/course22.jpg') }}"
                alt="Disaster Preparedness" 
                class="w-full h-40 object-cover">
            <div class="p-5 text-left">
                <h3 class="font-semibold text-lg mb-2 text-orange-500">Coming soon.</h3>
                <p class="text-sm text-gray-600">Exciting new services coming soon. Stay tuned for updates!</p>
            </div>
        </div>

       <!-- Course 2 -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden">
            <img src="{{ asset('images/course22.jpg') }}"
                alt="Disaster Preparedness" 
                class="w-full h-40 object-cover">
            <div class="p-5 text-left">
                <h3 class="font-semibold text-lg mb-2 text-orange-500">Coming soon.</h3>
                <p class="text-sm text-gray-600">Exciting new services coming soon. Stay tuned for updates!</p>
            </div>
        </div>

</section>

<!-- Community Section -->
<section id="community" class="bg-white py-16 px-6 md:px-20"> 
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <!-- Image -->
        <img src="{{ asset('images/cover6.jpg') }}"
            alt="Community Collaboration" 
            class="rounded-2xl shadow-md w-full h-[350px] object-cover">
        
        <!-- Text -->
        <div class="flex flex-col justify-center items-center text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4 text-gray-800">Our Community</h2>
            <p class="text-gray-600 text-base md:text-lg mb-4 max-w-xl">
                Turo Moko thrives through the collective efforts of learners, teachers, and advocates. 
                Our community fosters collaboration, shared knowledge, and support to drive meaningful change. 
            </p>
            <ul class="list-disc list-inside text-gray-600 space-y-2 text-left">
                <li>Connect with like-minded advocates</li>
                <li>Share resources and experiences</li>
                <li>Collaborate on local and national initiatives</li>
            </ul>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="bg-gray-50 py-16 px-6 md:px-20">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-4 text-gray-800">About Turo Moko</h2>
        <p class="text-gray-600 text-base md:text-lg max-w-3xl mx-auto mb-8">
            Turo Moko is a capstone project developed as a prototype e-learning platform to support 
            civil society organizations and communities in Albay. The platform is designed to make 
            learning more accessible, collaborative, and inclusive by offering free online courses, 
            community spaces, and resources for local advocacies.  
        </p>

        <!-- Stats or Values -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-10">
            <div class="bg-white shadow-md rounded-xl p-6">
                <h3 class="text-2xl font-bold text-orange-500 mb-2">0</h3>
                <p class="text-gray-600 text-sm">Prototype Users</p>
            </div>
            <div class="bg-white shadow-md rounded-xl p-6">
                <h3 class="text-2xl font-bold text-orange-500 mb-2">0</h3>
                <p class="text-gray-600 text-sm">Test Communities</p>
            </div>
            <div class="bg-white shadow-md rounded-xl p-6">
                <h3 class="text-2xl font-bold text-orange-500 mb-2">0</h3>
                <p class="text-gray-600 text-sm">Pilot Courses</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-black text-gray-300 pt-12">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10 px-6 md:px-20">
        <!-- Contact -->
        <div>
            <h3 class="text-white font-semibold mb-4">Contact Us</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center gap-2">
                    <span class="text-orange-500">📞</span>
                    <span>(+63) 912-345-6789</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-orange-500">📍</span>
                    <span>123 Albay Road, Legazpi City, Philippines</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-orange-500">✉️</span>
                    <span>support@turomoko.org</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-orange-500">💬</span>
                    <span>Chat with Us</span>
                </li>
            </ul>
        </div>

        <!-- Follow Us -->
        <div>
            <h3 class="text-white font-semibold mb-4">Follow Us</h3>
            <div class="flex gap-4 mb-6">
                <!-- Facebook -->
                <a href="#" class="w-9 h-9 flex items-center justify-center bg-orange-500 rounded-full text-white hover:bg-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 5 3.66 9.13 8.44 9.88v-6.99h-2.54v-2.89h2.54V9.41c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.45h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.89h-2.34v6.99C18.34 21.13 22 17 22 12z"/>
                    </svg>
                </a>

                <!-- Twitter (X) -->
                <a href="#" class="w-9 h-9 flex items-center justify-center bg-orange-500 rounded-full text-white hover:bg-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.31l-5.214-6.82-5.97 6.82H1.816l7.73-8.836L1.308 2.25h6.972l4.713 6.231 5.251-6.231z"/>
                    </svg>
                </a>

                <!-- LinkedIn -->
                <a href="#" class="w-9 h-9 flex items-center justify-center bg-orange-500 rounded-full text-white hover:bg-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3A2 2 0 0 1 21 5V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V5A2 2 0 0 1 5 3H19M8.34 17V10.67H6V17H8.34M7.17 9.67A1.17 1.17 0 1 0 7.17 7.33 1.17 1.17 0 0 0 7.17 9.67M18 17V13.22C18 11.09 16.66 10.5 15.27 10.5C14.25 10.5 13.65 11 13.41 11.46H13.36V10.67H11V17H13.34V13.47C13.34 12.79 13.73 12.33 14.39 12.33C15.05 12.33 15.34 12.79 15.34 13.47V17H18Z"/>
                    </svg>
                </a>

                <!-- Instagram -->
                <a href="#" class="w-9 h-9 flex items-center justify-center bg-orange-500 rounded-full text-white hover:bg-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm0 2h10c1.654 0 3 1.346 3 3v10c0 1.654-1.346 3-3 3H7c-1.654 0-3-1.346-3-3V7c0-1.654 1.346-3 3-3zm5 2.5A5.507 5.507 0 0 0 6.5 12 5.507 5.507 0 0 0 12 17.5 5.507 5.507 0 0 0 17.5 12 5.507 5.507 0 0 0 12 6.5zm0 2A3.505 3.505 0 0 1 15.5 12 3.505 3.505 0 0 1 12 15.5 3.505 3.505 0 0 1 8.5 12 3.505 3.505 0 0 1 12 8.5zm4.75-2.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5z"/>
                    </svg>
                </a>
            </div>
            <p class="text-sm text-gray-400">Stay connected with our advocacy stories and community impact.</p>
        </div>

        <!-- Newsletter -->
        <div>
            <h3 class="text-white font-semibold mb-4">Newsletter Signup</h3>
            <form class="space-y-3">
                <label class="flex items-center text-sm gap-2">
                    <input type="checkbox" class="accent-orange-500">
                    <span>Send me updates about Turo Moko via email.</span>
                </label>
                <div class="flex">
                    <input type="email" placeholder="Email Address" class="w-full px-3 py-2 rounded-l-md text-black text-sm focus:outline-none">
                    <button class="bg-orange-500 hover:bg-orange-600 px-4 rounded-r-md text-white">Subscribe</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-800 mt-10"></div>

    <!-- Bottom Bar -->
    <div class="max-w-7xl mx-auto px-6 md:px-20 py-6 flex flex-col md:flex-row justify-between text-sm text-gray-500">
        <p>© 2025 Turo Moko. All Rights Reserved.</p>
        <div class="space-x-4">
            <a href="#" class="hover:underline">Privacy Policy</a>
            <a href="#" class="hover:underline">Terms of Use</a>
        </div>
    </div>
</footer>

@endsection