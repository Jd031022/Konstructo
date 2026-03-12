<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                $type = $data['type'] ?? 'info';
                
                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'details' => $data['details'] ?? null,
                    'link' => $data['link'] ?? null,
                    'read_at' => $notification->read_at,
                    'time' => $this->formatTime($notification->created_at),
                ];
            });

        $unreadCount = $user->unreadNotifications->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function unreadCount()
    {
        /** @var User $user */
        $user = Auth::user();
        $count = $user->unreadNotifications->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    public function markAsRead($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }

    private function formatTime($date)
    {
        $now = Carbon::now();
        $diff = $date->diffInMinutes($now);

        if ($diff < 1) {
            return 'Just now';
        } elseif ($diff < 60) {
            $minutes = floor($diff);
            return $minutes . ' ' . ($minutes === 1 ? 'min' : 'mins') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours') . ' ago';
        } elseif ($diff < 10080) {
            $days = floor($diff / 1440);
            return $days . ' ' . ($days === 1 ? 'day' : 'days') . ' ago';
        } else {
            return $date->format('M d, Y');
        }
    }
}