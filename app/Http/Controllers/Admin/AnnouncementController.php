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
use Illuminate\Database\Eloquent\Collection;

class AnnouncementController extends Controller
{
    /**
     * Get recent announcements
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
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

            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $limit = (int) $request->get('limit', 3);
            
            // Check if table exists
            try {
                /** @var Collection $announcements */
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
                /** @var \App\Models\Announcement $announcement */
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => (string) $announcement->content,
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
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

            /** @var \App\Models\User $creator */
            $creator = Auth::user();
            
            Log::info('Creating announcement', ['creator_id' => $creator->id, 'creator_email' => $creator->email]);
            
            // Create announcement
            /** @var Announcement $announcement */
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'color' => $request->color,
                'created_by' => $creator->id,
                'published_at' => now(),
                'is_active' => true,
            ]);

            Log::info('Announcement created', ['announcement_id' => $announcement->id]);

            // Send database notifications to all staff and applicants
            $this->sendAnnouncementNotifications($announcement, $creator);

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully and notifications sent!',
                'announcement' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => (string) $announcement->content,
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
 * Send database notifications to all staff and applicants
 */
private function sendAnnouncementNotifications($announcement, $creator): void
{
    try {
        Log::info('========== START SEND ANNOUNCEMENT NOTIFICATIONS ==========');
        Log::info('Announcement ID: ' . $announcement->id);
        Log::info('Creator ID: ' . $creator->id);
        
        // Get all staff and applicants (excluding the creator)
        $users = User::whereIn('role', ['staff', 'applicant'])
            ->where('id', '!=', $creator->id)
            ->get();
        
        Log::info('Found ' . $users->count() . ' users to notify');
        
        // Log each user found
        foreach ($users as $user) {
            Log::info('User to notify:', [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $user->role
            ]);
        }
        
        if ($users->isEmpty()) {
            Log::warning('No users found to notify!');
            Log::info('========== END SEND ANNOUNCEMENT NOTIFICATIONS (NO USERS) ==========');
            return;
        }
        
        $notification = new NewAnnouncementNotification($announcement, $creator);
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($users as $user) {
            try {
                Log::info('Attempting to notify user: ' . $user->email);
                
                // Check if notifications table exists
                $tableExists = \Illuminate\Support\Facades\Schema::hasTable('notifications');
                Log::info('Notifications table exists: ' . ($tableExists ? 'YES' : 'NO'));
                
                if (!$tableExists) {
                    Log::error('Notifications table does not exist!');
                    Log::info('========== END SEND ANNOUNCEMENT NOTIFICATIONS (NO TABLE) ==========');
                    return;
                }
                
                // Count before
                $beforeCount = $user->notifications()->count();
                Log::info('User notifications before: ' . $beforeCount);
                
                // Send notification
                $user->notify($notification);
                
                // Count after
                $afterCount = $user->notifications()->count();
                Log::info('User notifications after: ' . $afterCount);
                
                if ($afterCount > $beforeCount) {
                    Log::info('✅ Notification created successfully for user: ' . $user->email);
                    
                    // Get the latest notification
                    $latest = $user->notifications()->latest()->first();
                    Log::info('Latest notification ID: ' . $latest->id);
                    Log::info('Latest notification data: ' . json_encode($latest->data));
                    
                    $successCount++;
                } else {
                    Log::error('❌ No notification was created for user: ' . $user->email);
                    $failCount++;
                }
                
            } catch (\Exception $e) {
                Log::error("❌ Failed to notify user {$user->id}: " . $e->getMessage());
                Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
                $failCount++;
            }
        }
        
        Log::info("Announcement database notifications sent - Success: {$successCount}, Failed: {$failCount}");
        Log::info('========== END SEND ANNOUNCEMENT NOTIFICATIONS ==========');
        
    } catch (\Exception $e) {
        Log::error('Error in sendAnnouncementNotifications: ' . $e->getMessage());
        Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
    }
}

    /**
     * Get color class based on color name
     */
    private function getColorClass(string $color): string
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
    private function getIconForColor(string $color): string
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
    public function all(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            /** @var \Illuminate\Pagination\LengthAwarePaginator $announcements */
            $announcements = Announcement::with('creator')
                ->orderBy('created_at', 'desc')
                ->paginate((int) $request->get('per_page', 15));
            
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
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
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

            /** @var Announcement $announcement */
            $announcement = Announcement::findOrFail($id);
            
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'color' => $request->color,
                'is_active' => $request->boolean('is_active', $announcement->is_active),
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
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            /** @var Announcement $announcement */
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