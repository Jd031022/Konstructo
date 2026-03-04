<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Model;
use Illuminate\Support\Facades\Log;

#[Provider('groq')]
#[Model('llama-3.3-70b-versatile')]
#[Temperature(0.7)]
class SupportAgent implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        $context = $this->loadAllContexts();
        
        return 'You are a helpful customer support agent for Konstructo. You have access to multiple knowledge bases about our services.

AVAILABLE TOPICS (use the relevant one based on the user question):
' . $this->listAvailableTopics() . '

INSTRUCTIONS:
1. Read the user question carefully
2. Identify which topic they are asking about
3. Use ONLY the information from the relevant section to answer
4. If the question spans multiple topics, combine information appropriately
5. If asked about something not in any knowledge base, say: "I don\'t have information about that yet. Please contact our support team."

HERE IS ALL THE KNOWLEDGE BASE INFORMATION:
' . $context . '

Remember: Stay strictly within the provided information. Do not invent features or processes.';
    }

    private function loadAllContexts(): string
    {
        $context = '';
        $knowledgePath = storage_path('app/ai-knowledge');
        
        $files = glob($knowledgePath . '/*.txt');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $filename = basename($file, '.txt');
            $topic = $this->formatTopicName($filename);
            
            $context .= "\n=== " . strtoupper($topic) . " ===\n";
            $context .= $content . "\n";
        }
        
        return $context;
    }

    private function listAvailableTopics(): string
    {
        $knowledgePath = storage_path('app/ai-knowledge');
        $files = glob($knowledgePath . '/*.txt');
        $topics = [];
        
        foreach ($files as $file) {
            $filename = basename($file, '.txt');
            $topics[] = '- ' . $this->formatTopicName($filename);
        }
        
        return implode("\n", $topics);
    }

    private function formatTopicName(string $filename): string
    {
        return ucwords(str_replace('-', ' ', $filename));
    }
}