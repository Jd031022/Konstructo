<?php
// app/Http/Controllers/Applicant/BasicRequirementController.php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\BasicRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BasicRequirementController extends Controller
{
    /**
     * Show the basic requirements submission form
     */
    public function index()
    {
        $user = Auth::user();
        $basicRequirement = BasicRequirement::where('user_id', $user->id)->first();
        
        // Check if already approved
        if ($basicRequirement && $basicRequirement->isApproved()) {
            return redirect()->route('applicant.application.step1')
                ->with('success', 'Your basic requirements have been approved. You may now proceed with your application.');
        }
        
        return view('applicant.basic-requirements.index', compact('basicRequirement'));
    }

    /**
     * Store or update basic requirements
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_owner' => 'required|boolean',
            'tct_link' => 'required|url',
            'tax_declaration_link' => 'required|url',
            'current_tax_receipt_link' => 'required|url',
            'deed_of_sale_link' => 'required_if:is_owner,0|nullable|url',
            'spa_link' => 'required_if:is_owner,0|nullable|url',
        ], [
            'is_owner.required' => 'Please indicate if you are the property owner.',
            'tct_link.required' => 'Transfer Certificate of Title is required.',
            'tct_link.url' => 'Please provide a valid Google Drive link.',
            'tax_declaration_link.required' => 'Tax Declaration is required.',
            'tax_declaration_link.url' => 'Please provide a valid Google Drive link.',
            'current_tax_receipt_link.required' => 'Current Tax Receipt is required.',
            'current_tax_receipt_link.url' => 'Please provide a valid Google Drive link.',
            'deed_of_sale_link.required_if' => 'Deed of Sale is required since you are not the owner.',
            'deed_of_sale_link.url' => 'Please provide a valid Google Drive link for Deed of Sale.',
            'spa_link.required_if' => 'Special Power of Attorney is required since you are not the owner.',
            'spa_link.url' => 'Please provide a valid Google Drive link for Special Power of Attorney.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Check if already has approved requirements
            $existing = BasicRequirement::where('user_id', $user->id)
                ->where('status', 'approved')
                ->first();
                
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your basic requirements have already been approved. You cannot submit again.'
                ], 403);
            }

            $data = [
                'user_id' => $user->id,
                'is_owner' => $request->is_owner,
                'tct_link' => $request->tct_link,
                'tax_declaration_link' => $request->tax_declaration_link,
                'current_tax_receipt_link' => $request->current_tax_receipt_link,
                'status' => 'pending',
                'submitted_at' => now(),
            ];

            // Add authorization documents if not owner
            if (!$request->is_owner) {
                $data['deed_of_sale_link'] = $request->deed_of_sale_link;
                $data['spa_link'] = $request->spa_link;
            } else {
                $data['deed_of_sale_link'] = null;
                $data['spa_link'] = null;
            }

            $basicRequirement = BasicRequirement::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            Log::info('Basic requirements submitted', [
                'user_id' => $user->id,
                'requirement_id' => $basicRequirement->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Basic requirements submitted successfully. Please wait for staff approval.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting basic requirements: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check status of basic requirements
     */
    public function checkStatus()
    {
        $user = Auth::user();
        $requirement = BasicRequirement::where('user_id', $user->id)->first();
        
        if (!$requirement) {
            return response()->json([
                'has_submitted' => false,
                'status' => 'not_submitted',
                'message' => 'You have not submitted any requirements yet.'
            ]);
        }
        
        $statusMessages = [
            'pending' => 'Your requirements are pending review by staff.',
            'approved' => 'Your requirements have been approved! You may now proceed to Step 1.',
            'rejected' => 'Your requirements were rejected. Please check the reason and resubmit.'
        ];
        
        return response()->json([
            'has_submitted' => true,
            'status' => $requirement->status,
            'message' => $statusMessages[$requirement->status] ?? 'Status unknown',
            'rejection_reason' => $requirement->rejection_reason,
            'submitted_at' => $requirement->submitted_at?->format('Y-m-d H:i:s'),
            'approved_at' => $requirement->approved_at?->format('Y-m-d H:i:s'),
            'is_owner' => $requirement->is_owner
        ]);
    }

    /**
     * Check if user can proceed to step 1
     */
    public function canProceed()
    {
        $user = Auth::user();
        $requirement = BasicRequirement::where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();
            
        return response()->json([
            'can_proceed' => !is_null($requirement),
            'status' => $requirement ? 'approved' : 'not_approved'
        ]);
    }
}