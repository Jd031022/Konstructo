<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">

    <!-- Floating Button -->
    <button @click="open = !open"
        class="w-14 h-14 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition">

        <!-- Blue Chat Icon -->
        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-7 h-7 text-blue-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">

            <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.9 9.9 0 01-4-.8L3 20l1.2-3.2A7.7 7.7 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

    </button>


    <!-- Chat Window -->
    <div x-show="open"
        x-transition
        class="w-80 bg-white rounded-2xl shadow-xl mt-3 border overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b">

            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>

                <h3 class="font-semibold text-gray-700">
                    Konstructo Chatbot
                </h3>
            </div>

            <!-- Close Button -->
            <button @click="open = false"
                class="text-gray-400 hover:text-gray-700 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>

            </button>

        </div>


        <!-- Chat Body -->
        <div class="p-4 space-y-3">

            <div class="bg-gray-100 p-3 rounded-xl text-sm text-gray-700">
                Hi there! 👋 Thanks for reaching out.  
                How can I help you today?
            </div>

            <!-- Suggested Questions -->
            <div class="space-y-2">

                <button
                    class="w-full text-left border rounded-lg px-3 py-2 text-sm hover:bg-gray-50 flex justify-between">

                    How to apply to building permit?

                    <span class="text-blue-500">↑</span>

                </button>

                <button
                    class="w-full text-left border rounded-lg px-3 py-2 text-sm hover:bg-gray-50 flex justify-between">

                    Where to secure the building permit?

                    <span class="text-blue-500">↑</span>

                </button>

                <button
                    class="w-full text-left border rounded-lg px-3 py-2 text-sm hover:bg-gray-50 flex justify-between">

                    What are the requirements for building permit?

                    <span class="text-blue-500">↑</span>

                </button>

            </div>

        </div>


        <!-- Chat Input -->
        <div class="border-t p-3 flex gap-2">

            <input
                type="text"
                placeholder="Send message..."
                class="flex-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button
                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">

                ➤

            </button>

        </div>

    </div>

</div>