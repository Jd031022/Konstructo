<?php
// app/Http/Middleware/CheckBasicRequirements.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBasicRequirements
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'applicant') {
            // Check if user has approved basic requirements
            $hasApprovedRequirements = \App\Models\BasicRequirement::where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();
                
            if (!$hasApprovedRequirements) {
                // Check if they're trying to access the basic requirements page itself
                if ($request->route()->getName() !== 'applicant.basic-requirements.index') {
                    return redirect()->route('applicant.basic-requirements.index')
                        ->with('error', 'Please submit and get approval for basic requirements before proceeding.');
                }
            }
        }
        
        return $next($request);
    }
}