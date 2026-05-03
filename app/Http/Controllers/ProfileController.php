<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ApplicationDocument; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;

/**
 * @method void middleware(string $middleware, array $options = [])
 */
class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Load profile relationship
        $user->load('profile');
        
        // Load application document counts for the user (their own applications for applicants, processed applications for staff)
        if ($user->role === 'applicant') {
            // For applicants: show their own application statistics
            $user->loadCount([
                'applicationDocuments as total_applications',
                'applicationDocuments as draft_count' => function ($query) {
                    $query->where('status', 'draft');
                },
                'applicationDocuments as pending_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'applicationDocuments as verified_count' => function ($query) {
                    $query->where('status', 'verified');
                },
                'applicationDocuments as approved_count' => function ($query) {
                    $query->where('status', 'approved');
                },
                'applicationDocuments as rejected_count' => function ($query) {
                    $query->where('status', 'rejected');
                },
            ]);
        } else {
            // For staff/admin: show applications they've processed/reviewed
            $processedCounts = \App\Models\ApplicationReviewActivity::where('reviewer_id', $user->id)
                ->selectRaw('COUNT(DISTINCT application_id) as total_processed')
                ->selectRaw('COUNT(DISTINCT CASE WHEN new_status = \'draft\' THEN application_id END) as draft_count')
                ->selectRaw('COUNT(DISTINCT CASE WHEN new_status = \'pending\' THEN application_id END) as pending_count')
                ->selectRaw('COUNT(DISTINCT CASE WHEN new_status = \'verified\' THEN application_id END) as verified_count')
                ->selectRaw('COUNT(DISTINCT CASE WHEN new_status = \'approved\' THEN application_id END) as approved_count')
                ->selectRaw('COUNT(DISTINCT CASE WHEN new_status = \'rejected\' THEN application_id END) as rejected_count')
                ->first();
            
            $user->total_applications = $processedCounts->total_processed ?? 0;
            $user->draft_count = $processedCounts->draft_count ?? 0;
            $user->pending_count = $processedCounts->pending_count ?? 0;
            $user->verified_count = $processedCounts->verified_count ?? 0;
            $user->approved_count = $processedCounts->approved_count ?? 0;
            $user->rejected_count = $processedCounts->rejected_count ?? 0;
        }
        
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('profile');
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Ensure profile exists
        if (!$user->profile) {
            $user->profile()->create();
        }

        // Separate validation rules for different sections
        $rules = [
            // User table fields
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            
            // Profile table fields
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'civil_status' => ['nullable', 'string', 'in:single,married,widowed,separated,divorced'],
            'citizenship' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'alternative_email' => ['nullable', 'email', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            
            // Password fields
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ];

        $validated = $request->validate($rules);

        // Handle password update
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
            
            /** @var \App\Models\UserProfile $profile */
            $profile = $user->profile;
            $profile->password_changed_at = Carbon::now();
            $profile->save();
        }

        // Separate user and profile data
        $userFields = [
            'first_name', 'last_name', 'middle_name', 'suffix',
            'phone_number', 'email', 'username', 'zip_code', 'address'
        ];
        
        $profileFields = [
            'date_of_birth', 'place_of_birth', 'gender', 'civil_status',
            'citizenship', 'tin', 'telephone', 'alternative_email',
            'house_number', 'street', 'barangay', 'city', 'province'
        ];

        // Update user table
        $userData = array_intersect_key($validated, array_flip($userFields));
        $user->update($userData);

        // Update profile table
        $profileData = array_intersect_key($validated, array_flip($profileFields));
        
        // Filter out null values if you don't want to overwrite with null
        $profileData = array_filter($profileData, function($value) {
            return !is_null($value);
        });
        
        if (!empty($profileData)) {
            /** @var \App\Models\UserProfile $profile */
            $profile = $user->profile;
            $profile->update($profileData);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update([
            'avatar' => $path
        ]);

        // Debug logging
        if (Storage::disk('public')->exists($path)) {
            Log::info('Avatar uploaded successfully: ' . $path);
            Log::info('Full URL: ' . asset('storage/' . $path));
        } else {
            Log::error('Avatar upload failed: ' . $path);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile picture updated successfully!');
    }
    public function getAvatarInfo()
{
    $user = Auth::user();
    
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }
    
    $avatarPath = $user->avatar;
    
    if (!empty($avatarPath)) {
        $avatarUrl = asset('storage/' . $avatarPath);
    } else {
        $fullName = urlencode($user->first_name . ' ' . $user->last_name);
        $avatarUrl = "https://ui-avatars.com/api/?name={$fullName}&size=40&background=ffffff&color=155386&bold=true";
    }
    
    return response()->json([
        'success' => true,
        'avatar_url' => $avatarUrl,
        'avatar_path' => $avatarPath
    ]);
}
}