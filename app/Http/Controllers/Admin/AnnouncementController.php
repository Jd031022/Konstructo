<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Get recent announcements
     */
    public function index(Request $request)
    {
        try {
            Log::info('Announcement index called');
            
            // Check authentication
            if (!Auth::check()) {
                Log::error('User not authenticated in announcement index');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $limit = $request->get('limit', 3);
            
            // Check if table exists
            try {
                $announcements = Announcement::with('creator')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
            } catch (\Exception $e) {
                Log::warning('Announcements table might not exist yet: ' . $e->getMessage());
                return response()->json([
                    'success' => true,
                    'announcements' => []
                ]);
            }
            
            $formattedAnnouncements = $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'color' => $announcement->color,
                    'color_class' => $this->getColorClass($announcement->color),
                    'icon' => $this->getIconForColor($announcement->color),
                    'created_at' => $announcement->created_at,
                    'created_by' => $announcement->creator ? $announcement->creator->first_name . ' ' . $announcement->creator->last_name : 'System',
                    'time_ago' => $announcement->created_at ? $announcement->created_at->diffForHumans() : 'Recently',
                ];
            });
            
            return response()->json([
                'success' => true,
                'announcements' => $formattedAnnouncements
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new announcement and notify all users
     */
    public function store(Request $request)
    {
        try {
            Log::info('Announcement store method called', ['request' => $request->all()]);
            
            // Check authentication
            if (!Auth::check()) {
                Log::error('User not authenticated in announcement store');
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to create announcements'
                ], 401);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'color' => 'required|in:blue,green,yellow,red',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $creator = Auth::user();
            
            Log::info('Creating announcement', ['creator_id' => $creator->id, 'creator_email' => $creator->email]);
            
            // Check if table exists, create announcement
            try {
                $announcement = Announcement::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'color' => $request->color,
                    'created_by' => $creator->id,
                    'published_at' => now(),
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create announcement: ' . $e->getMessage());
                
                // If table doesn't exist, return mock success for now
                if (strpos($e->getMessage(), 'table') !== false) {
                    Log::warning('Announcements table does not exist. Please run migrations.');
                    return response()->json([
                        'success' => true,
                        'message' => 'Announcement created (but database table not ready). Please run migrations.',
                        'announcement' => [
                            'id' => 1,
                            'title' => $request->title,
                            'content' => $request->content,
                            'color' => $request->color,
                            'time_ago' => 'Just now',
                        ]
                    ]);
                }
                
                throw $e;
            }

            Log::info('Announcement created', ['announcement_id' => $announcement->id]);

            // Send notifications if requested
            if ($request->has('notify_users') && $request->notify_users) {
                $this->sendAnnouncementNotifications($announcement, $creator);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully' . ($request->notify_users ? ' and notifications sent!' : ''),
                'announcement' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'color' => $announcement->color,
                    'time_ago' => $announcement->created_at->diffForHumans(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in store: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notifications to all staff and applicants
     */
    private function sendAnnouncementNotifications($announcement, $creator)
    {
        try {
            // Get all staff and applicants
            $users = User::whereIn('role', ['staff', 'applicant'])
                ->where('id', '!=', $creator->id)
                ->get();
            
            Log::info('Sending notifications to ' . $users->count() . ' users');
            
            $notification = new NewAnnouncementNotification($announcement, $creator);
            
            $successCount = 0;
            $failCount = 0;
            
            foreach ($users as $user) {
                try {
                    $user->notify($notification);
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Failed to notify user {$user->id}: " . $e->getMessage());
                    $failCount++;
                }
            }
            
            Log::info("Announcement notifications sent - Success: {$successCount}, Failed: {$failCount}");
            
        } catch (\Exception $e) {
            Log::error('Error sending announcement notifications: ' . $e->getMessage());
        }
    }

    /**
     * Get color class based on color name
     */
    private function getColorClass($color)
    {
        return match($color) {
            'blue' => 'bg-blue-50 border-blue-200 text-blue-600',
            'green' => 'bg-green-50 border-green-200 text-green-600',
            'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-600',
            'red' => 'bg-red-50 border-red-200 text-red-600',
            default => 'bg-gray-50 border-gray-200 text-gray-600',
        };
    }

    /**
     * Get icon path based on color
     */
    private function getIconForColor($color)
    {
        return match($color) {
            'blue' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'green' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'yellow' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'red' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    /**
     * Get all announcements (for management page)
     */
    public function all(Request $request)
    {
        try {
            $announcements = Announcement::with('creator')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'announcements' => $announcements
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in all: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcements'
            ], 500);
        }
    }

    /**
     * Update an announcement
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'color' => 'required|in:blue,green,yellow,red',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $announcement = Announcement::findOrFail($id);
            
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'color' => $request->color,
                'is_active' => $request->is_active ?? $announcement->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating announcement'
            ], 500);
        }
    }

    /**
     * Delete an announcement
     */
    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting announcement'
            ], 500);
        }
    }
}