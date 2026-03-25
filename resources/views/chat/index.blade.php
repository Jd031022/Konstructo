@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Desktop Layout: Two Columns -->
    <div class="hidden md:flex h-screen overflow-hidden">
        <!-- Left Column - Conversation List -->
        <div class="w-96 bg-white border-r border-gray-200 flex flex-col h-screen">
            <!-- Header with Plus Icon -->
            <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] p-4 flex items-center justify-between">
                <div>
                    <h2 class="text-white font-bold text-lg">Messages</h2>
                    <p class="text-white/70 text-xs">Connect with support and staff</p>
                </div>
                <button onclick="showNewConversationModal()" class="text-white hover:text-gray-200 transition p-1 rounded-full hover:bg-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
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

            <!-- Conversations List - Dynamically Loaded -->
            <div id="conversations-list" class="flex-1 overflow-y-auto">
                <div class="flex justify-center items-center h-64 text-gray-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Loading conversations...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Chat View -->
        <div id="chat-view" class="flex-1 flex flex-col bg-gray-50">
            <!-- Empty State -->
            <div id="empty-chat-state" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Select a conversation</h3>
                    <p class="text-sm text-gray-500">Choose a conversation from the list or start a new one</p>
                    <button onclick="showNewConversationModal()" class="mt-4 px-4 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition">
                        + New Conversation
                    </button>
                </div>
            </div>

            <!-- Chat Header (Hidden initially) -->
            <div id="chat-header" class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-3 hidden">
                <div class="relative">
                    <img id="chat-avatar-img" class="w-12 h-12 rounded-full object-cover shadow-md hidden" alt="Profile picture">
                    <div id="chat-avatar-placeholder" class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                        ?
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white" id="online-status"></div>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-gray-800 text-lg" id="chat-name">Select a conversation</h2>
                    <p class="text-xs" id="chat-status">Online</p>
                </div>
                <button onclick="showConversationInfo()" class="text-gray-500 hover:text-[#155386] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>

            <!-- Messages Container (Hidden initially) -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 hidden">
                <!-- Messages will be dynamically added here -->
            </div>

            <!-- Message Input (Hidden initially) -->
            <div id="message-input-container" class="bg-white border-t border-gray-200 p-4 hidden">
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

    <!-- Mobile Layout -->
    <div class="md:hidden flex flex-col h-screen">
        <!-- Mobile Header with Plus Icon -->
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
                <button onclick="showNewConversationModal()" class="text-white hover:text-gray-200 transition">
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

            <!-- Conversations List for Mobile - Dynamically Loaded -->
            <div id="mobile-conversations-list" class="divide-y divide-gray-100">
                <div class="flex justify-center items-center h-64 text-gray-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Loading conversations...</p>
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
                    <img id="mobile-chat-avatar-img" class="w-10 h-10 rounded-full object-cover shadow-md hidden" alt="Profile picture">
                    <div id="mobile-chat-avatar-placeholder" class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold shadow-md">
                        ?
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white" id="mobile-online-status"></div>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-gray-800" id="mobile-chat-name">Select a conversation</h2>
                    <p class="text-xs" id="mobile-chat-status">Online</p>
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

<!-- New Conversation Modal -->
<div id="newConversationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Start New Conversation</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search for a user</label>
                <input type="text" id="user-search" placeholder="Type name or email..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#155386]">
            </div>
            <div id="user-search-results" class="max-h-64 overflow-y-auto mb-4">
                <!-- Search results will appear here -->
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="closeNewConversationModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
// Isolate chat functionality to prevent conflicts with other pages
(function() {
    'use strict';
    
    let currentConversation = null;
    let echo = null;
    let currentUserId = {{ auth()->id() }};
    let allUsers = [];
    let pusherChannel = null;
    let pollingInterval = null;
    let lastMessageCount = 0;
    
    // Initialize Echo and Pusher for real-time
    function initializeEcho() {
        if (typeof window.Echo !== 'undefined') {
            echo = window.Echo;
            console.log('Echo initialized for real-time updates');
        } else {
            console.warn('Echo not available. Real-time features disabled.');
            setupPolling();
        }
    }
    
    // Fallback polling for real-time if Pusher not configured
    function setupPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
            if (currentConversation) {
                checkForNewMessages(currentConversation);
            }
        }, 3000);
    }
    
    async function checkForNewMessages(conversationId) {
        try {
            const response = await fetch(`/conversations/${conversationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                const conversation = await response.json();
                const messages = conversation.messages || [];
                if (messages.length > lastMessageCount) {
                    const newMessages = messages.slice(lastMessageCount);
                    newMessages.forEach(msg => {
                        if (msg.user_id !== currentUserId) {
                            const timeString = formatTime(msg.created_at);
                            addMessageToChat(msg.content, 'received', timeString, 'desktop');
                            addMessageToChat(msg.content, 'received', timeString, 'mobile');
                            playNotificationSound();
                        }
                    });
                    lastMessageCount = messages.length;
                }
            }
        } catch (error) {
            console.error('Error checking for new messages:', error);
        }
    }
    
    function playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log('Sound not available'));
        } catch(e) {}
    }
    
    // Fetch all users for new conversation with profile pictures
    async function fetchAllUsers() {
        try {
            const response = await fetch('/users/list', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                allUsers = await response.json();
            }
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    }
    
    // Show new conversation modal
    function showNewConversationModal() {
        const modal = document.getElementById('newConversationModal');
        if (modal) {
            modal.classList.remove('hidden');
            const searchInput = document.getElementById('user-search');
            if (searchInput) searchInput.focus();
            searchUsers();
        }
    }
    
    function closeNewConversationModal() {
        const modal = document.getElementById('newConversationModal');
        if (modal) {
            modal.classList.add('hidden');
            const searchInput = document.getElementById('user-search');
            if (searchInput) searchInput.value = '';
            const results = document.getElementById('user-search-results');
            if (results) results.innerHTML = '';
        }
    }
    
    // Search users with profile pictures
    function searchUsers() {
        const searchInput = document.getElementById('user-search');
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        const results = allUsers.filter(user => 
            user.id !== currentUserId && 
            (user.full_name.toLowerCase().includes(searchTerm) || 
             user.email.toLowerCase().includes(searchTerm))
        );
        
        const resultsContainer = document.getElementById('user-search-results');
        if (!resultsContainer) return;
        
        const resultsHtml = results.map(user => `
            <div onclick="window.startConversationWithUser(${user.id}, '${escapeHtml(user.full_name)}', '${escapeHtml(user.avatar_url || '')}', '${escapeHtml(user.initials || user.full_name.charAt(0))}')" 
                 class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 flex items-center gap-3">
                <div class="relative">
                    ${user.avatar_url ? 
                        `<img src="${escapeHtml(user.avatar_url)}" class="w-10 h-10 rounded-full object-cover" alt="${escapeHtml(user.full_name)}">` :
                        `<div class="w-10 h-10 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold">
                            ${escapeHtml(user.initials || user.full_name.charAt(0))}
                        </div>`
                    }
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">${escapeHtml(user.full_name)}</p>
                    <p class="text-sm text-gray-500">${escapeHtml(user.email)}</p>
                </div>
            </div>
        `).join('');
        
        resultsContainer.innerHTML = resultsHtml || '<div class="text-center text-gray-500 py-4">No users found</div>';
    }
    
    // Update avatar display
    function updateAvatar(avatarUrl, initials, elementPrefix = 'chat') {
        const imgElement = document.getElementById(`${elementPrefix}-avatar-img`);
        const placeholderElement = document.getElementById(`${elementPrefix}-avatar-placeholder`);
        
        if (imgElement && placeholderElement) {
            if (avatarUrl && avatarUrl !== '') {
                imgElement.src = avatarUrl;
                imgElement.classList.remove('hidden');
                placeholderElement.classList.add('hidden');
            } else {
                placeholderElement.innerHTML = initials;
                placeholderElement.classList.remove('hidden');
                imgElement.classList.add('hidden');
            }
        }
    }
    
    // Start conversation with selected user
    async function startConversationWithUser(userId, userName, userAvatar, userInitials) {
        closeNewConversationModal();
        
        try {
            const response = await fetch('/conversations/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ user_id: userId })
            });
            
            if (response.ok) {
                const conversation = await response.json();
                await fetchConversations();
                setTimeout(() => {
                    loadConversation(conversation.id, userName, userAvatar, userInitials);
                }, 500);
            } else {
                alert('Failed to start conversation. Please try again.');
            }
        } catch (error) {
            console.error('Error starting conversation:', error);
            alert('Failed to start conversation. Please try again.');
        }
    }
    
    // Fetch conversations from API
    async function fetchConversations() {
        try {
            const response = await fetch('/conversations', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            if (response.ok) {
                const conversations = await response.json();
                renderConversations(conversations);
            } else {
                console.error('Failed to fetch conversations:', response.status);
                showError('Failed to load conversations');
            }
        } catch (error) {
            console.error('Error fetching conversations:', error);
            showError('Network error. Please check your connection.');
        }
    }
    
    function showError(message) {
        const container = document.getElementById('conversations-list');
        if (container) {
            container.innerHTML = `
                <div class="flex justify-center items-center h-64 text-red-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>${message}</p>
                        <button onclick="window.fetchConversations()" class="mt-3 px-4 py-2 bg-[#155386] text-white rounded-lg text-sm">Retry</button>
                    </div>
                </div>
            `;
        }
    }
    
    function renderConversations(conversations) {
        const container = document.getElementById('conversations-list');
        const mobileContainer = document.getElementById('mobile-conversations-list');
        
        if (!container) return;
        
        if (!conversations || conversations.length === 0) {
            const emptyHtml = `
                <div class="flex justify-center items-center h-64 text-gray-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>No conversations yet</p>
                        <button onclick="window.showNewConversationModal()" class="mt-3 px-4 py-2 bg-[#155386] text-white rounded-lg text-sm">Start a new conversation</button>
                    </div>
                </div>
            `;
            container.innerHTML = emptyHtml;
            if (mobileContainer) mobileContainer.innerHTML = emptyHtml;
            return;
        }
        
        const conversationsHtml = conversations.map(conv => `
            <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition border-b border-gray-100" 
                 data-conversation-id="${conv.id}" 
                 data-name="${escapeHtml(conv.name)}" 
                 data-avatar-url="${escapeHtml(conv.avatar_url || '')}"
                 data-avatar-initials="${escapeHtml(conv.avatar)}">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        ${conv.avatar_url ? 
                            `<img src="${escapeHtml(conv.avatar_url)}" class="w-12 h-12 rounded-full object-cover shadow-md" alt="${escapeHtml(conv.name)}">` :
                            `<div class="w-12 h-12 bg-gradient-to-r from-[#155386] to-[#40798C] rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                ${escapeHtml(conv.avatar)}
                            </div>`
                        }
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <h3 class="font-semibold text-gray-800">${escapeHtml(conv.name)}</h3>
                            <span class="text-xs text-gray-400">${conv.last_message ? formatTime(conv.last_message.created_at) : ''}</span>
                        </div>
                        <p class="text-sm text-gray-500 truncate">${conv.last_message ? escapeHtml(conv.last_message.content) : 'No messages yet'}</p>
                    </div>
                    ${conv.unread_count > 0 ? `<div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs">${conv.unread_count}</div>` : ''}
                </div>
            </div>
        `).join('');
        
        container.innerHTML = conversationsHtml;
        if (mobileContainer) mobileContainer.innerHTML = conversationsHtml;
        
        attachConversationClickHandlers();
    }
    
    async function fetchMessages(conversationId) {
        try {
            const response = await fetch(`/conversations/${conversationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const conversation = await response.json();
                lastMessageCount = (conversation.messages || []).length;
                return conversation.messages || [];
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
            return [];
        }
    }
    
    async function loadConversation(conversationId, name, avatarUrl, avatarInitials) {
        currentConversation = conversationId;
        
        const emptyState = document.getElementById('empty-chat-state');
        const chatHeader = document.getElementById('chat-header');
        const messagesContainer = document.getElementById('messages-container');
        const messageInputContainer = document.getElementById('message-input-container');
        
        if (emptyState) emptyState.classList.add('hidden');
        if (chatHeader) chatHeader.classList.remove('hidden');
        if (messagesContainer) messagesContainer.classList.remove('hidden');
        if (messageInputContainer) messageInputContainer.classList.remove('hidden');
        
        const chatName = document.getElementById('chat-name');
        const mobileChatName = document.getElementById('mobile-chat-name');
        if (chatName) chatName.textContent = name;
        if (mobileChatName) mobileChatName.textContent = name;
        
        // Update avatars
        updateAvatar(avatarUrl, avatarInitials, 'chat');
        updateAvatar(avatarUrl, avatarInitials, 'mobile-chat');
        
        const container = document.getElementById('messages-container');
        const mobileContainer = document.getElementById('mobile-messages-container');
        if (container) container.innerHTML = '<div class="text-center text-gray-500">Loading messages...</div>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="text-center text-gray-500">Loading messages...</div>';
        
        const messages = await fetchMessages(conversationId);
        
        if (container) container.innerHTML = '';
        if (mobileContainer) mobileContainer.innerHTML = '';
        
        if (messages && messages.length > 0) {
            messages.forEach(msg => {
                addMessageToChat(msg.content, msg.user_id === currentUserId ? 'sent' : 'received', 
                               formatTime(msg.created_at), 'desktop');
                addMessageToChat(msg.content, msg.user_id === currentUserId ? 'sent' : 'received', 
                               formatTime(msg.created_at), 'mobile');
            });
        } else {
            if (container) container.innerHTML = '<div class="text-center text-gray-500">No messages yet. Send a message to start the conversation!</div>';
            if (mobileContainer) mobileContainer.innerHTML = '<div class="text-center text-gray-500">No messages yet. Send a message to start the conversation!</div>';
        }
        
        scrollToBottom();
        subscribeToConversation(conversationId);
        
        if (window.innerWidth < 768) {
            const mobileList = document.getElementById('mobile-conversation-list');
            const mobileView = document.getElementById('mobile-chat-view');
            if (mobileList) mobileList.classList.add('hidden');
            if (mobileView) mobileView.classList.remove('hidden');
        }
    }
    
    async function sendMessageToAPI(content, conversationId) {
        try {
            const response = await fetch(`/conversations/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ content })
            });
            
            if (response.ok) {
                const message = await response.json();
                lastMessageCount++;
                return message;
            } else {
                console.error('Failed to send message:', response.status);
                return null;
            }
        } catch (error) {
            console.error('Error sending message:', error);
            return null;
        }
    }
    
    async function sendMessage() {
        const input = document.getElementById('message-input');
        if (!input) return;
        
        const content = input.value.trim();
        
        if (content === '' || !currentConversation) return;
        
        const timeString = formatTime(new Date());
        addMessageToChat(content, 'sent', timeString, 'desktop');
        addMessageToChat(content, 'sent', timeString, 'mobile');
        
        input.value = '';
        input.style.height = 'auto';
        
        const message = await sendMessageToAPI(content, currentConversation);
        
        if (!message) {
            addMessageToChat('Failed to send message. Please try again.', 'error', timeString, 'desktop');
            addMessageToChat('Failed to send message. Please try again.', 'error', timeString, 'mobile');
        } else {
            fetchConversations();
        }
    }
    
    async function sendMobileMessage() {
        const input = document.getElementById('mobile-message-input');
        if (!input) return;
        
        const content = input.value.trim();
        
        if (content === '' || !currentConversation) return;
        
        const timeString = formatTime(new Date());
        addMessageToChat(content, 'sent', timeString, 'desktop');
        addMessageToChat(content, 'sent', timeString, 'mobile');
        
        input.value = '';
        input.style.height = 'auto';
        
        const message = await sendMessageToAPI(content, currentConversation);
        
        if (!message) {
            addMessageToChat('Failed to send message. Please try again.', 'error', timeString, 'desktop');
            addMessageToChat('Failed to send message. Please try again.', 'error', timeString, 'mobile');
        } else {
            fetchConversations();
        }
    }
    
    function addMessageToChat(message, type, time, platform = 'desktop') {
        const containerId = platform === 'desktop' ? 'messages-container' : 'mobile-messages-container';
        const container = document.getElementById(containerId);
        
        if (!container) return;
        
        const isSent = type === 'sent';
        const isError = type === 'error';
        
        const messageHtml = `
            <div class="flex ${isSent ? 'justify-end' : 'justify-start'} animate-fade-in">
                <div class="max-w-[80%] ${isSent ? 'items-end' : 'items-start'}">
                    <div class="${isSent ? (isError ? 'bg-red-500 text-white' : 'bg-[#155386] text-white') : 'bg-white text-gray-800'} rounded-2xl ${isSent ? 'rounded-tr-none' : 'rounded-tl-none'} px-4 py-2 shadow-sm">
                        <p class="text-sm">${escapeHtml(message)}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-1 block ${isSent ? 'text-right' : 'text-left'}">${time}</span>
                </div>
            </div>
        `;
        
        if (container.innerHTML.includes('No messages yet')) {
            container.innerHTML = '';
        }
        
        container.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();
    }
    
    function subscribeToConversation(conversationId) {
        if (echo) {
            if (pusherChannel) {
                echo.leave(`conversation.${currentConversation}`);
            }
            pusherChannel = echo.private(`conversation.${conversationId}`)
                .listen('NewMessage', (e) => {
                    if (e.user_id !== currentUserId) {
                        const timeString = formatTime(e.created_at);
                        addMessageToChat(e.content, 'received', timeString, 'desktop');
                        addMessageToChat(e.content, 'received', timeString, 'mobile');
                        fetchConversations();
                        playNotificationSound();
                    }
                });
            console.log(`Subscribed to conversation ${conversationId} for real-time updates`);
        }
    }
    
    function formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Just now';
        if (diff < 3600000) return `${Math.floor(diff / 60000)} min ago`;
        if (diff < 86400000) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        return date.toLocaleDateString();
    }
    
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        const mobileContainer = document.getElementById('mobile-messages-container');
        
        if (container) container.scrollTop = container.scrollHeight;
        if (mobileContainer) mobileContainer.scrollTop = mobileContainer.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function attachConversationClickHandlers() {
        document.querySelectorAll('#conversations-list .conversation-item').forEach(item => {
            item.removeEventListener('click', handleConversationClick);
            item.addEventListener('click', handleConversationClick);
        });
        
        document.querySelectorAll('#mobile-conversations-list .conversation-item').forEach(item => {
            item.removeEventListener('click', handleConversationClick);
            item.addEventListener('click', handleConversationClick);
        });
    }
    
    function handleConversationClick(e) {
        const item = e.currentTarget;
        const id = item.dataset.conversationId;
        const name = item.dataset.name;
        const avatarUrl = item.dataset.avatarUrl;
        const avatarInitials = item.dataset.avatarInitials;
        loadConversation(id, name, avatarUrl, avatarInitials);
    }
    
    function setupSearch() {
        const searchInput = document.getElementById('search-conversations');
        const mobileSearchInput = document.getElementById('mobile-search-conversations');
        
        const handleSearch = (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const conversations = document.querySelectorAll('#conversations-list .conversation-item');
            
            conversations.forEach(conv => {
                const name = conv.querySelector('h3').textContent.toLowerCase();
                conv.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        };
        
        if (searchInput) searchInput.addEventListener('input', handleSearch);
        if (mobileSearchInput) mobileSearchInput.addEventListener('input', handleSearch);
        
        const userSearch = document.getElementById('user-search');
        if (userSearch) {
            userSearch.addEventListener('input', searchUsers);
        }
    }
    
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
    
    function backToConversations() {
        const mobileList = document.getElementById('mobile-conversation-list');
        const mobileView = document.getElementById('mobile-chat-view');
        if (mobileList) mobileList.classList.remove('hidden');
        if (mobileView) mobileView.classList.add('hidden');
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', async () => {
        initializeEcho();
        await fetchAllUsers();
        await fetchConversations();
        setupSearch();
        
        setupTextarea(document.getElementById('message-input'), sendMessage);
        setupTextarea(document.getElementById('mobile-message-input'), sendMobileMessage);
    });
    
    // Attach functions to window with unique names to avoid conflicts
    window.chat = {
        sendMessage: sendMessage,
        sendMobileMessage: sendMobileMessage,
        backToConversations: backToConversations,
        showNewConversationModal: showNewConversationModal,
        closeNewConversationModal: closeNewConversationModal,
        startConversationWithUser: startConversationWithUser,
        fetchConversations: fetchConversations,
        showConversationInfo: function() {
            alert('Conversation details coming soon');
        }
    };
    
    // Also attach individual functions for onclick handlers (keeping compatibility)
    window.sendMessage = sendMessage;
    window.sendMobileMessage = sendMobileMessage;
    window.backToConversations = backToConversations;
    window.showNewConversationModal = showNewConversationModal;
    window.closeNewConversationModal = closeNewConversationModal;
    window.startConversationWithUser = startConversationWithUser;
    window.fetchConversations = fetchConversations;
    window.showConversationInfo = function() {
        alert('Conversation details coming soon');
    };
})();
</script>

<style>
    @keyframes bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
    }
    
    .animate-bounce {
        animation: bounce 1s infinite;
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
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
    
    textarea {
        resize: none;
        overflow-y: auto;
        line-height: 1.5;
    }
    
    textarea:focus {
        outline: none;
    }
    
    .conversation-item.active {
        background-color: #f0f9ff;
        border-left: 3px solid #155386;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }
</style>
@endsection