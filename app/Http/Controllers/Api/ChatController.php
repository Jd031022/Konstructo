<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Ai\Agents\SupportAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        try {
            Log::info('Chat send request received', [
                'message' => $request->message,
                'conversation_id' => $request->conversation_id
            ]);

            $request->validate([
                'message' => 'required|string',
                'conversation_id' => 'nullable|string'
            ]);

            $agent = new SupportAgent();
            
            if ($request->conversation_id) {
                $agent->setConversation($request->conversation_id);
            }
            
            $response = $agent->send($request->message);
            
            // Generate a conversation ID if not set
            $conversationId = $agent->getConversationId() ?? uniqid('conv_', true);
            
            Log::info('Chat response generated', [
                'conversation_id' => $conversationId
            ]);
            
            return response()->json([
                'success' => true,
                'response' => $response,
                'conversation_id' => $conversationId
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Chat controller error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to process message',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}