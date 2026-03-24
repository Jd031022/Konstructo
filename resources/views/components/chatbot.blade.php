<div x-data="chatbot()" x-init="init()" class="fixed bottom-6 right-6 z-50">

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
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute bottom-16 right-0 w-96 bg-white rounded-2xl shadow-xl border overflow-hidden">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b bg-gradient-to-r from-blue-600 to-blue-700">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                <h3 class="font-semibold text-white">
                    Konstructo Support
                </h3>
            </div>
            <button @click="open = false"
                class="text-white hover:text-gray-200 transition">
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

        <!-- Chat Messages Area -->
        <div class="h-96 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer">
            <template x-for="(message, index) in messages" :key="index">
                <div class="flex" :class="{'justify-end': message.role === 'user', 'justify-start': message.role === 'assistant'}">
                    <div 
                        class="max-w-xs px-4 py-2 rounded-lg shadow"
                        :class="{
                            'bg-blue-600 text-white rounded-br-none': message.role === 'user',
                            'bg-gray-100 text-gray-800 rounded-bl-none': message.role === 'assistant'
                        }"
                    >
                        <div class="text-sm whitespace-pre-wrap break-words" x-html="formatMessage(message.content)"></div>
                        <span class="text-xs opacity-75 mt-1 block" x-text="formatTime(message.timestamp)"></span>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-gray-100 text-gray-800 px-4 py-3 rounded-lg rounded-bl-none">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggested Questions (shown when no messages) -->
        <div x-show="messages.length === 0" class="px-4 pb-4">
            <p class="text-xs text-gray-500 mb-2">Suggested questions:</p>
            <div class="space-y-2">
                <template x-for="question in suggestedQuestions" :key="question">
                    <button
                        @click="useSuggestedQuestion(question)"
                        class="w-full text-left border rounded-lg px-3 py-2 text-sm hover:bg-gray-50 hover:border-blue-300 transition flex justify-between items-center group">
                        <span x-text="question"></span>
                        <span class="text-blue-500 opacity-0 group-hover:opacity-100 transition">↑</span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="border-t p-3">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input
                    type="text"
                    x-model="newMessage"
                    placeholder="Type your message..."
                    class="flex-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :disabled="isTyping"
                >
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!newMessage.trim() || isTyping"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatbot() {
    return {
        open: false,
        messages: [],
        newMessage: '',
        isTyping: false,
        conversationId: null,
        suggestedQuestions: [
            'What are the requirements for building permit?',
            'Where to secure building permit?',
            'How much are the fees?',
            'What is the registration process?',
            'What documents do I need to submit?'
        ],

        init() {
            // Clear chat on every page load/refresh
            this.clearChat();
            
            // Also check if user just logged out (page reload after logout)
            window.addEventListener('load', () => {
                this.clearChat();
            });
        },

        clearChat() {
            // Clear all messages
            this.messages = [];
            this.conversationId = null;
            
            // Remove from localStorage
            localStorage.removeItem('chat_conversation');
            
            // Add welcome message
            this.addWelcomeMessage();
            
            console.log('Chat cleared');
        },

        addWelcomeMessage() {
            this.messages.push({
                role: 'assistant',
                content: 'Hi there! 👋 Thanks for reaching out to Konstructo. How can I help you today?',
                timestamp: new Date().toISOString()
            });
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        formatMessage(content) {
            if (!content) return '';
            
            // First, escape HTML to prevent XSS
            let formatted = this.escapeHtml(content);
            
            // Convert line breaks to <br> tags
            formatted = formatted.replace(/\n/g, '<br>');
            
            // Convert numbered lists (1., 2., 3., etc.)
            formatted = formatted.replace(/(\d+\.\s+)([^<]+)/g, '<span class="font-bold text-blue-600">$1</span>$2');
            
            // Convert bullet points (lines starting with - or •)
            formatted = formatted.replace(/^[-•]\s+(.*?)(?=<br>|$)/gm, '• $1');
            
            // Bold text between ** **
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Add some styling for sections (all caps followed by colon)
            formatted = formatted.replace(/([A-Z\s]+:)/g, '<span class="font-bold text-blue-700">$1</span>');
            
            // Add emojis if missing for common sections
            if (formatted.includes('REQUIREMENTS') && !formatted.includes('📋')) {
                formatted = '📋 ' + formatted;
            }
            if (formatted.includes('FEES') && !formatted.includes('💰')) {
                formatted = '💰 ' + formatted;
            }
            if (formatted.includes('WHERE') && !formatted.includes('📍')) {
                formatted = '📍 ' + formatted;
            }
            if (formatted.includes('PROCESS') && !formatted.includes('📝')) {
                formatted = '📝 ' + formatted;
            }
            
            return formatted;
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        useSuggestedQuestion(question) {
            this.newMessage = question;
            this.sendMessage();
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.isTyping) return;

            // Add user message
            const userMessage = {
                role: 'user',
                content: this.newMessage,
                timestamp: new Date().toISOString()
            };
            
            this.messages.push(userMessage);
            const messageText = this.newMessage;
            this.newMessage = '';
            this.isTyping = true;

            // Scroll to bottom
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });

            try {
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                if (!token) {
                    console.error('CSRF token not found');
                    throw new Error('CSRF token not found');
                }

                // Send to server
                const response = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: messageText,
                        conversation_id: this.conversationId
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    console.error('Server error:', {
                        status: response.status,
                        statusText: response.statusText,
                        data: errorData
                    });
                    throw new Error(`Server error: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Save conversation ID
                    if (data.conversation_id) {
                        this.conversationId = data.conversation_id;
                    }

                    // Add assistant message
                    this.messages.push({
                        role: 'assistant',
                        content: data.response || 'I received your message but could not generate a response.',
                        timestamp: new Date().toISOString()
                    });

                    // Don't save to localStorage anymore since we clear on refresh
                } else {
                    throw new Error(data.error || 'Unknown error occurred');
                }

            } catch (error) {
                console.error('Fetch error:', error);
                
                // Add error message
                this.messages.push({
                    role: 'assistant',
                    content: 'I apologize, but I\'m having trouble connecting right now. Please try again later or contact our support team at konstructo@gmail.com.',
                    timestamp: new Date().toISOString()
                });
            } finally {
                this.isTyping = false;
                
                // Scroll to bottom
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    container.scrollTop = container.scrollHeight;
                });
            }
        }
    }
}
</script>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush