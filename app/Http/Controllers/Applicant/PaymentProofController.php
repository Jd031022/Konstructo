<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentProofController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function upload(Request $request)
    {
        Log::info('PaymentProofController@upload called', $request->all());

        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application_documents,id',
            'or_link' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid Google Drive link',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $request->application_id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Check if payment proof already exists
            $existing = PaymentProof::where('application_id', $request->application_id)->first();
            
            if ($existing) {
                $existing->update([
                    'or_link' => $request->or_link,
                    'status' => 'pending',
                    'verified_by' => null,
                    'verified_at' => null,
                    'rejection_reason' => null
                ]);
                $paymentProof = $existing;
                $message = 'Payment proof updated successfully';
            } else {
                $paymentProof = PaymentProof::create([
                    'application_id' => $request->application_id,
                    'user_id' => $user->id,
                    'or_link' => $request->or_link,
                    'status' => 'pending'
                ]);
                $message = 'Payment proof uploaded successfully';
            }

            // Notify staff about the new payment proof
            try {
                $this->notificationService->notifyStaffPaymentProofUploaded($application, $paymentProof);
                Log::info('Staff notified about payment proof upload');
            } catch (\Exception $e) {
                Log::error('Failed to notify staff about payment proof: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $paymentProof
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading payment proof: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment proof: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentProof($applicationId)
    {
        try {
            $user = Auth::user();
            
            $application = ApplicationDocument::where('user_id', $user->id)
                ->where('id', $applicationId)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $paymentProof = PaymentProof::where('application_id', $applicationId)->first();

            return response()->json([
                'success' => true,
                'data' => $paymentProof
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment proof: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment proof'
            ], 500);
        }
    }
}