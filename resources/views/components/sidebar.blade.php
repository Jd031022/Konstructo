<aside id="sidebar" class="w-20 bg-white min-h-screen h-full shadow-md fixed top-0 left-0 flex flex-col items-start py-6 overflow-y-auto transition-all duration-300 rounded-tr-2xl rounded-br-2xl scrollbar-hide">

    <!-- Burger Menu Logo with Konstructo Name -->
<div id="burger-menu" class="mb-10 cursor-pointer px-6 w-full flex items-center gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#155386] hover:text-[#40798C] transition min-w-8" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <span class="text-lg font-bold whitespace-nowrap opacity-0 transition-opacity duration-300">
        <span class="text-[#155386]">Konstr</span><span class="text-[rgb(64,121,140)]">ucto</span>
    </span>
</div>

    <!-- Navigation -->
    <nav class="flex flex-col items-start gap-6 flex-1 w-full px-4">

        <!-- Home (Active) -->
        <a href="#" class="w-full flex items-center gap-4 p-2 rounded-xl bg-[#155386] text-white shadow hover:bg-[#40798C] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Home</span>
        </a>

        <!-- Projects -->
        <a href="#" class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Projects</span>
        </a>
        
        <!-- Services 
        <a href="#" class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4-3-9s1.34-9 3-9"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Services</span>
        </a>

        Documents
        <a href="#" class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Documents</span>
        </a>
        -->
        <!-- Settings -->
        <a href="#" class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Settings</span>
        </a>

    </nav>

    <!-- Logout -->
    <div class="mt-auto pt-6 w-full px-4">
        <button onclick="showLogoutModal()" 
           class="w-full flex items-center gap-4 p-2 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 min-w-6" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="text-sm font-medium opacity-0 transition-opacity duration-300 whitespace-nowrap">Logout</span>
        </button>
    </div>

</aside>

<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 px-4">
    <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-4 w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Logout Confirmation</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to log out of your account?
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="logout()" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                        Yes, Logout
                    </button>
                    <button onclick="closeLogoutModal()" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<!-- JavaScript for toggle functionality and logout modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const burgerMenu = document.getElementById('burger-menu');
        let isExpanded = false;

        burgerMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (!isExpanded) {
                // Expand sidebar
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-48');
                
                // Show all text labels with animation
                const labels = sidebar.querySelectorAll('span');
                labels.forEach(label => {
                    label.classList.remove('opacity-0');
                    label.classList.add('opacity-100');
                });
                
                isExpanded = true;
            } else {
                // Collapse sidebar
                sidebar.classList.remove('w-48');
                sidebar.classList.add('w-20');
                
                // Hide all text labels
                const labels = sidebar.querySelectorAll('span');
                labels.forEach(label => {
                    label.classList.remove('opacity-100');
                    label.classList.add('opacity-0');
                });
                
                isExpanded = false;
            }
        });

        // Click outside to collapse
        document.addEventListener('click', function(e) {
            if (isExpanded && !sidebar.contains(e.target)) {
                // Collapse sidebar
                sidebar.classList.remove('w-48');
                sidebar.classList.add('w-20');
                
                const labels = sidebar.querySelectorAll('span');
                labels.forEach(label => {
                    label.classList.remove('opacity-100');
                    label.classList.add('opacity-0');
                });
                
                isExpanded = false;
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });

        // Close modal when clicking outside
        const modal = document.getElementById('logout-modal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLogoutModal();
            }
        });
    });

    // Logout modal functions
    function showLogoutModal() {
        document.getElementById('logout-modal').classList.remove('hidden');
        // Prevent scrolling on body when modal is open
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        document.getElementById('logout-modal').classList.add('hidden');
        // Re-enable scrolling
        document.body.style.overflow = 'auto';
    }

    function logout() {
        // Show loading state on button (optional)
        const modal = document.getElementById('logout-modal');
        const modalContent = modal.querySelector('.bg-white');
        modalContent.innerHTML = `
            <div class="text-center p-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Logging out...</h3>
                <p class="text-sm text-gray-600">Please wait while we securely log you out.</p>
            </div>
        `;
        
        // Submit the logout form after a brief delay (for UX)
        setTimeout(() => {
            document.getElementById('logout-form').submit();
        }, 500);
    }
</script>

<style>
    /* Smooth transitions for width change */
    #sidebar {
        transition: width 0.3s ease-in-out;
    }
    
    #sidebar span {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Ensure icons don't shrink */
    .min-w-6 {
        min-width: 1.5rem;
    }
    
    .min-w-8 {
        min-width: 2rem;
    }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    /* Optional: Add a subtle shadow on the rounded edges for depth */
    #sidebar {
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Modal animations */
    #logout-modal {
        transition: opacity 0.2s ease-in-out;
    }
    
    #logout-modal .bg-white {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>