<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationReviewController extends Controller
{
    public function index()
    {
        $applications = ApplicationDocument::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = ApplicationDocument::with(['user', 'verifier'])->findOrFail($id);
        return view('admin.applications.show', compact('application'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $application = ApplicationDocument::findOrFail($id);
        $application->markAsVerified(Auth::id(), $request->notes);

        // Notify user (if you have notification system)
        // $application->user->notify(new DocumentsVerified($application));

        return redirect()->back()->with('success', 'Application documents verified successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        $application = ApplicationDocument::findOrFail($id);
        $application->markAsRejected(
            $request->rejection_reason,
            Auth::id(),
            $request->notes
        );

        // Notify user (if you have notification system)
        // $application->user->notify(new DocumentsRejected($application, $request->rejection_reason));

        return redirect()->back()->with('success', 'Application documents rejected.');
    }
}