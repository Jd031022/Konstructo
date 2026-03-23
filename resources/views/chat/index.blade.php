@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Desktop Layout: Two Columns (Hidden on mobile) -->
    <div class="hidden md:flex h-screen overflow-hidden">
        <!-- Left Column - Conversation List (Desktop) -->
        <div class="w-96 bg-white border-r border-gray-200 flex flex-col h-screen">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] p-4">
                <h2 class="text-white font-bold text-lg">Messages</h2>
                <p class="text-white/70 text-xs">Connect with support and staff</p>
            </div>
            
            <!-- Search Bar -->
            <div class="p-4 border-b border-gray-100">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="search-conversations" placeholder="Search conversations..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] text-sm">
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversations-list" class="flex-1 overflow-y-auto">
                <!-- Support Team -->
                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" data-conversation-id="1" data-name="Support Team" data-avatar="S">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                S
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Support Team</h3>
                                <span class="text-xs text-gray-400">2 min ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">How can we help you today?</p>
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                </div>

                <!-- Juan Dela Cruz -->
                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" data-conversation-id="2" data-name="Juan Dela Cruz" data-avatar="J">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            J
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Juan Dela Cruz</h3>
                                <span class="text-xs text-gray-400">1 hour ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">I have a question about my application...</p>
                        </div>
                    </div>
                </div>

                <!-- Maria Santos -->
                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" data-conversation-id="3" data-name="Maria Santos" data-avatar="M">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            M
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Maria Santos</h3>
                                <span class="text-xs text-gray-400">3 hours ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Thank you for the update!</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Team -->
                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" data-conversation-id="4" data-name="Admin Team" data-avatar="A">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-[#155386] font-bold text-lg shadow-md">
                            A
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Admin Team</h3>
                                <span class="text-xs text-gray-400">Yesterday</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Your application is being processed</p>
                        </div>
                    </div>
                </div>

                <!-- Engineer Department -->
                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" data-conversation-id="5" data-name="Engineer Department" data-avatar="E">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            E
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Engineer Department</h3>
                                <span class="text-xs text-gray-400">2 days ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Technical review in progress</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Chat View (Desktop) -->
        <div id="chat-view" class="flex-1 flex flex-col bg-gray-50">
            <!-- Chat Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md" id="chat-avatar">
                        S
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-gray-800 text-lg" id="chat-name">Support Team</h2>
                    <p class="text-xs text-green-600" id="chat-status">Online</p>
                </div>
                <button onclick="showConversationInfo()" class="text-gray-500 hover:text-[#155386] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Messages will be dynamically added here -->
            </div>

            <!-- Message Input -->
            <div class="bg-white border-t border-gray-200 p-4">
                <div class="flex gap-3 items-end">
                    <button class="flex-shrink-0 p-2 text-gray-400 hover:text-[#155386] transition rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <textarea id="message-input" rows="1" 
                              placeholder="Type your message..."
                              class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] resize-none text-sm max-h-32"></textarea>
                    <button onclick="sendMessage()" 
                            class="flex-shrink-0 p-2 bg-[#155386] text-white rounded-full hover:bg-[#1F363D] transition shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">Press Enter to send message</p>
            </div>
        </div>
    </div>

    <!-- Mobile Layout (Hidden on desktop) -->
    <div class="md:hidden flex flex-col h-screen">
        <!-- Mobile Header -->
        <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] sticky top-0 z-10 shadow-md">
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="javascript:history.back()" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-white font-bold text-lg">Messages</h1>
                        <p class="text-white/70 text-xs">Connect with support and staff</p>
                    </div>
                </div>
                <button onclick="startNewConversation()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Conversation List -->
        <div id="mobile-conversation-list" class="flex-1 overflow-y-auto bg-white">
            <!-- Search Bar -->
            <div class="sticky top-0 bg-white p-3 border-b border-gray-100">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="mobile-search-conversations" placeholder="Search conversations..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386] text-sm bg-gray-50">
                </div>
            </div>

            <!-- Conversations List for Mobile -->
            <div id="mobile-conversations-list" class="divide-y divide-gray-100">
                <!-- Support Team -->
                <div class="conversation-item p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition" data-conversation-id="1" data-name="Support Team" data-avatar="S">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                S
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Support Team</h3>
                                <span class="text-xs text-gray-400">2 min ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">How can we help you today?</p>
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                </div>

                <!-- Juan Dela Cruz -->
                <div class="conversation-item p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition" data-conversation-id="2" data-name="Juan Dela Cruz" data-avatar="J">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#40798C] to-[#70A9A1] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            J
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Juan Dela Cruz</h3>
                                <span class="text-xs text-gray-400">1 hour ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">I have a question about my application...</p>
                        </div>
                    </div>
                </div>

                <!-- Maria Santos -->
                <div class="conversation-item p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition" data-conversation-id="3" data-name="Maria Santos" data-avatar="M">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#70A9A1] to-[#9EC5CB] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            M
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Maria Santos</h3>
                                <span class="text-xs text-gray-400">3 hours ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Thank you for the update!</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Team -->
                <div class="conversation-item p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition" data-conversation-id="4" data-name="Admin Team" data-avatar="A">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#9EC5CB] to-[#B8D8E3] rounded-full flex items-center justify-center text-[#155386] font-bold text-lg shadow-md">
                            A
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Admin Team</h3>
                                <span class="text-xs text-gray-400">Yesterday</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Your application is being processed</p>
                        </div>
                    </div>
                </div>

                <!-- Engineer Department -->
                <div class="conversation-item p-4 hover:bg-gray-50 active:bg-gray-100 cursor-pointer transition" data-conversation-id="5" data-name="Engineer Department" data-avatar="E">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            E
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-semibold text-gray-800">Engineer Department</h3>
                                <span class="text-xs text-gray-400">2 days ago</span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">Technical review in progress</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Chat View (Hidden by default) -->
        <div id="mobile-chat-view" class="hidden flex-1 flex flex-col bg-gray-50 h-full">
            <!-- Chat Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3">
                <button onclick="backToConversations()" class="text-gray-600 hover:text-[#155386] transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <div class="relative">
                    <div class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold shadow-md" id="mobile-chat-avatar">
                        S
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-gray-800" id="mobile-chat-name">Support Team</h2>
                    <p class="text-xs text-green-600" id="mobile-chat-status">Online</p>
                </div>
                <button onclick="showConversationInfo()" class="text-gray-500 hover:text-[#155386] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <!-- Messages Container -->
            <div id="mobile-messages-container" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Messages will be dynamically added here -->
            </div>

            <!-- Message Input -->
            <div class="bg-white border-t border-gray-200 p-3">
                <div class="flex gap-2 items-end">
                    <button class="flex-shrink-0 p-2 text-gray-400 hover:text-[#155386] transition rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <textarea id="mobile-message-input" rows="1" 
                              placeholder="Type your message..."
                              class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#155386] resize-none text-sm max-h-32"></textarea>
                    <button onclick="sendMobileMessage()" 
                            class="flex-shrink-0 p-2 bg-[#155386] text-white rounded-full hover:bg-[#1F363D] transition shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1 text-center">Press Enter to send message</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentConversation = null;
    
    // Sample messages for each conversation
    const conversationMessages = {
        1: [
            { type: 'received', text: 'Hello! How can we help you today?', time: '10:30 AM', sender: 'Support Team' },
            { type: 'sent', text: 'Hi, I have a question about my building permit application status.', time: '10:32 AM', sender: 'You' },
            { type: 'received', text: 'Sure! Let me check that for you. Could you provide your application number?', time: '10:33 AM', sender: 'Support Team' },
            { type: 'sent', text: 'It\'s APP-2025-001234', time: '10:34 AM', sender: 'You' },
            { type: 'received', text: 'Thank you! Let me look that up for you. One moment please.', time: '10:35 AM', sender: 'Support Team' }
        ],
        2: [
            { type: 'received', text: 'Hello Juan! How can I assist you?', time: '2:30 PM', sender: 'Juan Dela Cruz' },
            { type: 'sent', text: 'I have a question about my application requirements.', time: '2:32 PM', sender: 'You' },
            { type: 'received', text: 'Sure, what specific requirements are you asking about?', time: '2:33 PM', sender: 'Juan Dela Cruz' }
        ],
        3: [
            { type: 'received', text: 'Hi there! Thank you for reaching out.', time: '11:15 AM', sender: 'Maria Santos' },
            { type: 'sent', text: 'Thank you for the update on my application!', time: '11:16 AM', sender: 'You' },
            { type: 'received', text: 'You\'re welcome! Let me know if you need anything else.', time: '11:17 AM', sender: 'Maria Santos' }
        ],
        4: [
            { type: 'received', text: 'Good morning! Your application is currently being processed.', time: '9:00 AM', sender: 'Admin Team' },
            { type: 'sent', text: 'Thank you for the update. How long will the processing take?', time: '9:05 AM', sender: 'You' },
            { type: 'received', text: 'Typically 5-7 business days. We\'ll notify you once completed.', time: '9:10 AM', sender: 'Admin Team' }
        ],
        5: [
            { type: 'received', text: 'Technical review in progress for your application.', time: '1:00 PM', sender: 'Engineer Department' },
            { type: 'sent', text: 'Thanks for letting me know. Is there anything I need to provide?', time: '1:05 PM', sender: 'You' },
            { type: 'received', text: 'Not at the moment. We\'ll reach out if we need additional documents.', time: '1:10 PM', sender: 'Engineer Department' }
        ]
    };

    // Load conversation messages
    function loadConversation(conversationId, name, avatar) {
        currentConversation = conversationId;
        
        // Update desktop chat header
        document.getElementById('chat-name').textContent = name;
        document.getElementById('chat-avatar').textContent = avatar;
        
        // Update mobile chat header
        document.getElementById('mobile-chat-name').textContent = name;
        document.getElementById('mobile-chat-avatar').textContent = avatar;
        
        // Load messages for desktop
        const messages = conversationMessages[conversationId] || [];
        const container = document.getElementById('messages-container');
        container.innerHTML = '';
        
        messages.forEach(msg => {
            addMessageToChat(msg.text, msg.type, msg.time, 'desktop');
        });
        
        // Load messages for mobile
        const mobileContainer = document.getElementById('mobile-messages-container');
        mobileContainer.innerHTML = '';
        
        messages.forEach(msg => {
            addMessageToChat(msg.text, msg.type, msg.time, 'mobile');
        });
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
        mobileContainer.scrollTop = mobileContainer.scrollHeight;
        
        // On mobile, show chat view and hide conversation list
        if (window.innerWidth < 768) {
            document.getElementById('mobile-conversation-list').classList.add('hidden');
            document.getElementById('mobile-chat-view').classList.remove('hidden');
        }
        
        // Highlight active conversation on desktop
        document.querySelectorAll('#conversations-list .conversation-item').forEach(item => {
            item.classList.remove('bg-gray-100');
            if (item.dataset.conversationId == conversationId) {
                item.classList.add('bg-gray-100');
            }
        });
        
        // Highlight active conversation on mobile
        document.querySelectorAll('#mobile-conversations-list .conversation-item').forEach(item => {
            item.classList.remove('bg-gray-100');
            if (item.dataset.conversationId == conversationId) {
                item.classList.add('bg-gray-100');
            }
        });
    }
    
    function addMessageToChat(message, type, time, platform = 'desktop') {
        const containerId = platform === 'desktop' ? 'messages-container' : 'mobile-messages-container';
        const container = document.getElementById(containerId);
        
        const messageHtml = `
            <div class="flex ${type === 'sent' ? 'justify-end' : 'justify-start'}">
                <div class="max-w-[80%] ${type === 'sent' ? 'items-end' : 'items-start'}">
                    <div class="${type === 'sent' ? 'bg-[#155386] text-white rounded-2xl rounded-tr-none' : 'bg-white text-gray-800 rounded-2xl rounded-tl-none'} px-4 py-2 shadow-sm">
                        <p class="text-sm">${escapeHtml(message)}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-1 block ${type === 'sent' ? 'text-right' : 'text-left'}">${time}</span>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', messageHtml);
        container.scrollTop = container.scrollHeight;
    }
    
    function sendMessage() {
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if (message === '') return;
        
        sendMessageToPlatform(message, input, 'desktop');
    }
    
    function sendMobileMessage() {
        const input = document.getElementById('mobile-message-input');
        const message = input.value.trim();
        
        if (message === '') return;
        
        sendMessageToPlatform(message, input, 'mobile');
    }
    
    function sendMessageToPlatform(message, inputElement, platform) {
        const now = new Date();
        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        // Add user message to both platforms
        addMessageToChat(message, 'sent', timeString, 'desktop');
        addMessageToChat(message, 'sent', timeString, 'mobile');
        
        // Save to conversation messages
        if (!conversationMessages[currentConversation]) {
            conversationMessages[currentConversation] = [];
        }
        conversationMessages[currentConversation].push({
            type: 'sent',
            text: message,
            time: timeString,
            sender: 'You'
        });
        
        // Clear input and reset height
        inputElement.value = '';
        inputElement.style.height = 'auto';
        
        // Simulate typing indicator
        showTypingIndicator();
        
        // Simulate response after delay
        setTimeout(() => {
            removeTypingIndicator();
            
            const responseTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const responseMessage = getAutoResponse(message);
            
            addMessageToChat(responseMessage, 'received', responseTime, 'desktop');
            addMessageToChat(responseMessage, 'received', responseTime, 'mobile');
            
            // Save response
            conversationMessages[currentConversation].push({
                type: 'received',
                text: responseMessage,
                time: responseTime,
                sender: document.getElementById('chat-name').textContent
            });
        }, 1500);
    }
    
    function getAutoResponse(message) {
        const lowerMsg = message.toLowerCase();
        
        if (lowerMsg.includes('status') || lowerMsg.includes('update')) {
            return "Your application is currently under review. We'll notify you once there's an update.";
        } else if (lowerMsg.includes('document') || lowerMsg.includes('requirement')) {
            return "Please ensure all required documents are uploaded. Check the requirements list in your application.";
        } else if (lowerMsg.includes('time') || lowerMsg.includes('how long')) {
            return "Processing usually takes 5-7 business days after submission. We'll keep you posted!";
        } else if (lowerMsg.includes('thank')) {
            return "You're welcome! Is there anything else I can help you with?";
        } else {
            return "Thank you for your message. Our team will get back to you shortly.";
        }
    }
    
    function showTypingIndicator() {
        const container = document.getElementById('messages-container');
        const mobileContainer = document.getElementById('mobile-messages-container');
        
        const typingHtml = `
            <div id="typing-indicator" class="flex justify-start">
                <div class="bg-white rounded-2xl rounded-tl-none px-4 py-2 shadow-sm">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', typingHtml);
        mobileContainer.insertAdjacentHTML('beforeend', typingHtml);
        
        container.scrollTop = container.scrollHeight;
        mobileContainer.scrollTop = mobileContainer.scrollHeight;
    }
    
    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
        
        const mobileIndicator = document.querySelector('#mobile-messages-container #typing-indicator');
        if (mobileIndicator) mobileIndicator.remove();
    }
    
    function backToConversations() {
        document.getElementById('mobile-conversation-list').classList.remove('hidden');
        document.getElementById('mobile-chat-view').classList.add('hidden');
    }
    
    function startNewConversation() {
        alert('New conversation feature coming soon!');
    }
    
    function showConversationInfo() {
        alert('Conversation info coming soon!');
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Auto-resize textarea
    const textarea = document.getElementById('message-input');
    const mobileTextarea = document.getElementById('mobile-message-input');
    
    function setupTextarea(textareaElement, sendFunction) {
        if (textareaElement) {
            textareaElement.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 128) + 'px';
            });
            
            textareaElement.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendFunction();
                }
            });
        }
    }
    
    setupTextarea(textarea, sendMessage);
    setupTextarea(mobileTextarea, sendMobileMessage);
    
    // Search conversations for desktop
    const searchInput = document.getElementById('search-conversations');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const conversations = document.querySelectorAll('#conversations-list .conversation-item');
            
            conversations.forEach(conv => {
                const name = conv.querySelector('h3').textContent.toLowerCase();
                if (name.includes(searchTerm)) {
                    conv.style.display = '';
                } else {
                    conv.style.display = 'none';
                }
            });
        });
    }
    
    // Search conversations for mobile
    const mobileSearchInput = document.getElementById('mobile-search-conversations');
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const conversations = document.querySelectorAll('#mobile-conversations-list .conversation-item');
            
            conversations.forEach(conv => {
                const name = conv.querySelector('h3').textContent.toLowerCase();
                if (name.includes(searchTerm)) {
                    conv.style.display = '';
                } else {
                    conv.style.display = 'none';
                }
            });
        });
    }
    
    // Conversation click handlers for desktop
    document.querySelectorAll('#conversations-list .conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.conversationId;
            const name = this.dataset.name;
            const avatar = this.dataset.avatar;
            loadConversation(id, name, avatar);
        });
    });
    
    // Conversation click handlers for mobile
    document.querySelectorAll('#mobile-conversations-list .conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.conversationId;
            const name = this.dataset.name;
            const avatar = this.dataset.avatar;
            loadConversation(id, name, avatar);
        });
    });
    
    // Load default conversation on desktop
    loadConversation('1', 'Support Team', 'S');
</script>

<style>
    /* Animations */
    @keyframes bounce {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-5px);
        }
    }
    
    .animate-bounce {
        animation: bounce 1s infinite;
    }
    
    /* Custom scrollbar */
    #messages-container::-webkit-scrollbar,
    #conversations-list::-webkit-scrollbar,
    #mobile-messages-container::-webkit-scrollbar,
    #mobile-conversations-list::-webkit-scrollbar {
        width: 4px;
    }
    
    #messages-container::-webkit-scrollbar-track,
    #conversations-list::-webkit-scrollbar-track,
    #mobile-messages-container::-webkit-scrollbar-track,
    #mobile-conversations-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    #messages-container::-webkit-scrollbar-thumb,
    #conversations-list::-webkit-scrollbar-thumb,
    #mobile-messages-container::-webkit-scrollbar-thumb,
    #mobile-conversations-list::-webkit-scrollbar-thumb {
        background: #155386;
        border-radius: 4px;
    }
    
    /* Textarea auto-resize */
    textarea {
        resize: none;
        overflow-y: auto;
        line-height: 1.5;
    }
    
    textarea:focus {
        outline: none;
    }
    
    /* Active conversation highlight */
    .conversation-item.active {
        background-color: #f0f9ff;
        border-left: 3px solid #155386;
    }
</style>
@endsection