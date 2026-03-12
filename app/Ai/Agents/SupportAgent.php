<?php

namespace App\Ai\Agents;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportAgent
{
    protected $user = null;
    protected $conversationId = null;
    protected $apiKey = null;

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
        Log::info('SupportAgent initialized', [
            'api_key_exists' => !empty($this->apiKey),
            'api_key_prefix' => $this->apiKey ? substr($this->apiKey, 0, 10) . '...' : 'none'
        ]);
    }

    public function setUser($user)
    {
        $this->user = $user;
        Log::info('User set', ['user_id' => $user?->id]);
        return $this;
    }

    public function setConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        Log::info('Conversation set', ['conversation_id' => $conversationId]);
        return $this;
    }

    public function getConversationId()
    {
        return $this->conversationId;
    }

    public function send($message)
    {
        try {
            Log::info('Send method called', [
                'message' => $message,
                'conversation_id' => $this->conversationId,
                'user_id' => $this->user?->id
            ]);
            
            // Save user message to database
            $this->saveMessage('user', $message);
            
            // Load knowledge base from your existing files
            $context = $this->loadAllContexts();
            
            // Get conversation history for context (last 5 messages)
            $history = $this->getConversationHistory(5);
            Log::info('Conversation history retrieved', ['history_count' => count($history)]);
            
            // Prepare messages for Groq
            $messages = $this->prepareMessages($message, $context, $history);
            
            // Call Groq API
            $response = $this->callGroqAPI($messages);
            
            // Save assistant response to database
            $this->saveMessage('assistant', $response);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('SupportAgent error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = "I'm sorry, but I'm having trouble processing your request. Please try again later or contact our support team at support@konstructo.com.";
            
            // Save error response to database
            $this->saveMessage('assistant', $errorMessage);
            
            return $errorMessage;
        }
    }

   protected function prepareMessages($currentMessage, $context, $history = [])
{
    $systemPrompt = "You are a helpful customer support agent for Konstructo, a system made for Ligao City Engineering Office. 

IMPORTANT INSTRUCTIONS:
- Never mention or refer to the 'knowledge base' in your responses
- Never mention filenames, file sections, or any technical structure (like 'WHERE TO SECURE section')
- Just answer the questions directly as if you naturally know the information
- If referencing information from different topics, just present it naturally without naming the source

FORMATTING INSTRUCTIONS:
- Use emojis where appropriate (📋 for documents, 📍 for locations, 💰 for fees, ⏱️ for time, ✅ for steps)
- Use bullet points for lists (start with - or •)
- Use numbered steps for processes (1., 2., 3.)
- Add line breaks between sections for readability
- Bold important terms using **text**
- Keep paragraphs short and scannable
- For multi-step processes, put each step on a new line

INFORMATION TO USE:
" . $context . "

Always answer based only on the information provided above. If information is not available, politely say you don't have that information and direct them to contact the Ligao City Engineering Office. 

Remember: 
- Never mention the words 'knowledge base' in your responses
- Never mention filenames, sections, or any technical structure
- Present information naturally as if you're an expert who just knows this information
- Instead of saying 'according to the WHERE TO SECURE section', just say 'You can get this from...' or provide the location directly";

    $messages = [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ]
    ];

    // Add conversation history for context
    foreach ($history as $msg) {
        $messages[] = [
            'role' => $msg['role'],
            'content' => $msg['content']
        ];
    }

    // Add current message
    $messages[] = [
        'role' => 'user',
        'content' => $currentMessage
    ];

    return $messages;
}
    protected function callGroqAPI($messages)
    {
        try {
            Log::info('Calling Groq API');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1024
            ]);

            Log::info('Groq API response status', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? 'No response generated';
                
                // Track token usage if available
                if (isset($data['usage'])) {
                    $this->saveUsageData($data['usage']);
                }
                
                return $content;
            } else {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return "I apologize, but I'm having trouble connecting to my knowledge base right now. Please try again in a moment.";
            }
            
        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage());
            return "I apologize, but I'm experiencing technical difficulties. Please try again later.";
        }
    }

    /**
     * Save a message to the database
     */
    protected function saveMessage($role, $content)
    {
        try {
            Log::info('Attempting to save message', [
                'role' => $role,
                'content_length' => strlen($content),
                'current_conversation_id' => $this->conversationId,
                'user_id' => $this->user?->id
            ]);
            
            // Check database connection
            try {
                DB::connection()->getPdo();
                Log::info('Database connection is working');
            } catch (\Exception $e) {
                Log::error('Database connection failed: ' . $e->getMessage());
                return;
            }
            
            // If no conversation exists, create one
            if (!$this->conversationId) {
                Log::info('No conversation ID, creating new conversation');
                $this->createConversation();
            }
            
            // Prepare message data
            $messageData = [
                'id' => (string) Str::uuid(),
                'conversation_id' => $this->conversationId,
                'user_id' => $this->user?->id,
                'agent' => 'support-agent',
                'role' => $role,
                'content' => $content,
                'attachments' => null,
                'tool_calls' => null,
                'tool_results' => null,
                'usage' => null,
                'meta' => json_encode([
                    'source' => 'groq',
                    'model' => 'llama-3.3-70b-versatile'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            Log::info('Inserting message with data', ['message_data' => $messageData]);
            
            // Save message to agent_conversation_messages table
            $inserted = DB::table('agent_conversation_messages')->insert($messageData);
            
            Log::info('Message insert result', ['success' => $inserted]);
            
            // Update conversation's updated_at
            $updated = DB::table('agent_conversations')
                ->where('id', $this->conversationId)
                ->update(['updated_at' => now()]);
                
            Log::info('Conversation updated', ['rows_affected' => $updated]);
            
        } catch (\Exception $e) {
            Log::error('Failed to save message to database: ' . $e->getMessage(), [
                'conversation_id' => $this->conversationId,
                'role' => $role,
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Create a new conversation
     */
    protected function createConversation()
    {
        try {
            $this->conversationId = (string) Str::uuid();
            
            // Generate title from first message or use default
            $title = 'Chat ' . now()->format('Y-m-d H:i');
            
            $conversationData = [
                'id' => $this->conversationId,
                'user_id' => $this->user?->id,
                'title' => $title,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            Log::info('Creating new conversation', ['data' => $conversationData]);
            
            DB::table('agent_conversations')->insert($conversationData);
            
            Log::info('Conversation created successfully', [
                'conversation_id' => $this->conversationId,
                'user_id' => $this->user?->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create conversation: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Generate a fallback ID
            $this->conversationId = 'conv_' . uniqid();
        }
    }

    /**
     * Get conversation history
     */
    protected function getConversationHistory($limit = 10)
    {
        if (!$this->conversationId) {
            return [];
        }
        
        try {
            $messages = DB::table('agent_conversation_messages')
                ->where('conversation_id', $this->conversationId)
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get(['role', 'content']);
                
            Log::info('Retrieved conversation history', [
                'conversation_id' => $this->conversationId,
                'count' => count($messages)
            ]);
                
            return $messages->toArray();
            
        } catch (\Exception $e) {
            Log::error('Failed to get conversation history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Save token usage data
     */
    protected function saveUsageData($usage)
    {
        if (!$this->conversationId) {
            return;
        }
        
        try {
            // Get the last message (assistant response) and update usage
            $updated = DB::table('agent_conversation_messages')
                ->where('conversation_id', $this->conversationId)
                ->where('role', 'assistant')
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update([
                    'usage' => json_encode($usage),
                    'updated_at' => now()
                ]);
                
            Log::info('Usage data saved', ['rows_updated' => $updated]);
                
        } catch (\Exception $e) {
            Log::error('Failed to save usage data: ' . $e->getMessage());
        }
    }

    /**
     * Get all conversations for a user
     */
    public function getUserConversations($userId = null)
    {
        $userId = $userId ?? $this->user?->id;
        
        if (!$userId) {
            return [];
        }
        
        try {
            return DB::table('agent_conversations')
                ->where('user_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Failed to get user conversations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get full conversation with messages
     */
    public function getFullConversation($conversationId = null)
    {
        $conversationId = $conversationId ?? $this->conversationId;
        
        if (!$conversationId) {
            return null;
        }
        
        try {
            $conversation = DB::table('agent_conversations')
                ->where('id', $conversationId)
                ->first();
                
            if (!$conversation) {
                return null;
            }
            
            $messages = DB::table('agent_conversation_messages')
                ->where('conversation_id', $conversationId)
                ->orderBy('created_at', 'asc')
                ->get();
                
            return [
                'conversation' => $conversation,
                'messages' => $messages
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get full conversation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Load all knowledge base context from your files
     */
    private function loadAllContexts(): string
    {
        $context = '';
        $knowledgePath = storage_path('app/ai-knowledge');
        
        Log::info('Loading knowledge from: ' . $knowledgePath);
        
        if (!is_dir($knowledgePath)) {
            Log::warning('Knowledge directory not found');
            return "No knowledge base found.";
        }
        
        $files = glob($knowledgePath . '/*.txt');
        Log::info('Found files', ['count' => count($files)]);
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $filename = basename($file, '.txt');
            $topic = ucwords(str_replace('-', ' ', $filename));
            
            $context .= "\n=== " . strtoupper($topic) . " ===\n";
            $context .= $content . "\n";
        }
        
        Log::info('Knowledge base loaded', ['total_length' => strlen($context)]);
        
        return $context;
    }
}