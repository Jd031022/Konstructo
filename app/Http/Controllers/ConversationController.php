<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        // For testing, get the first user if not authenticated
        if (!Auth::check()) {
            // For development, you can use a specific user
            // Remove this in production
            $user = User::first();
            Auth::login($user);
        }
        
        $conversations = Auth::user()->conversations()
            ->with(['participants', 'lastMessage'])
            ->get()
            ->map(function ($conversation) {
                $otherParticipant = $conversation->participants->firstWhere('id', '!=', Auth::id());
                $conversation->name = $conversation->name ?? ($otherParticipant ? $otherParticipant->full_name : 'Unknown');
                $conversation->avatar = $otherParticipant ? $otherParticipant->initials : '?';
                $conversation->unread_count = $conversation->unreadCount(Auth::id());
                $conversation->last_message = $conversation->lastMessage;
                $conversation->avatar_url = $otherParticipant ? $otherParticipant->avatar_url : null;
                return $conversation;
            });

        return response()->json($conversations);
    }

    public function show(Conversation $conversation)
    {
        // Check if user is participant
        if (!$conversation->participants->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->load(['messages.user', 'participants']);
        
        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return response()->json($conversation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        // Check if conversation exists between these users
        $conversation = Conversation::whereHas('participants', function ($query) use ($request) {
            $query->where('user_id', Auth::id());
        })->whereHas('participants', function ($query) use ($request) {
            $query->where('user_id', $request->user_id);
        })->where('type', 'private')->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::create([
                'type' => 'private'
            ]);

            $conversation->participants()->attach([Auth::id(), $request->user_id]);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->message,
            'type' => 'text'
        ]);

        $message->load('user');

        return response()->json([
            'message' => $message,
            'conversation_id' => $conversation->id
        ]);
    }
    public function createOnly(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id'
    ]);

    // Check if conversation exists between these users
    $conversation = Conversation::whereHas('participants', function ($query) use ($request) {
        $query->where('user_id', Auth::id());
    })->whereHas('participants', function ($query) use ($request) {
        $query->where('user_id', $request->user_id);
    })->where('type', 'private')->first();

    if (!$conversation) {
        // Create new conversation
        $conversation = Conversation::create([
            'type' => 'private'
        ]);

        $conversation->participants()->attach([Auth::id(), $request->user_id]);
    }

    return response()->json($conversation);
}

    
}
