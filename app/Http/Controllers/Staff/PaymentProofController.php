<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
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

    public function verify(Request $request, $id)
    {
        Log::info('PaymentProofController@verify called', ['id' => $id]);

        try {
            $paymentProof = PaymentProof::with('application.user')->find($id);

            if (!$paymentProof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof not found'
                ], 404);
            }

            $staff = Auth::user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;

            // Only CPDO can verify payment proofs
            if ($position !== 'cpdo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only CPDO staff can verify payment proofs'
                ], 403);
            }

            $paymentProof->markAsVerified($staff->id);

            // Send notification to applicant (database + email)
            try {
                $this->notificationService->notifyPaymentProofVerified($paymentProof->application, $staff, $paymentProof);
                Log::info('Applicant notified about payment proof verification');
            } catch (\Exception $e) {
                Log::error('Failed to send verification notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment proof verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying payment proof: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment proof'
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        Log::info('PaymentProofController@reject called', ['id' => $id]);

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a reason for rejection',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paymentProof = PaymentProof::with('application.user')->find($id);

            if (!$paymentProof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof not found'
                ], 404);
            }

            $staff = Auth::user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;

            // Only CPDO can reject payment proofs
            if ($position !== 'cpdo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only CPDO staff can reject payment proofs'
                ], 403);
            }

            $paymentProof->markAsRejected($staff->id, $request->reason);

            // Send notification to applicant (database + email)
            try {
                $this->notificationService->notifyPaymentProofRejected($paymentProof->application, $staff, $request->reason, $paymentProof);
                Log::info('Applicant notified about payment proof rejection');
            } catch (\Exception $e) {
                Log::error('Failed to send rejection notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment proof rejected'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting payment proof: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment proof'
            ], 500);
        }
    }

    public function uploadCertificate(Request $request, $id)
    {
        Log::info('PaymentProofController@uploadCertificate called', ['id' => $id]);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:zoning_cert,locational_clearance',
            'link' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid Google Drive link',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paymentProof = PaymentProof::with('application.user')->find($id);

            if (!$paymentProof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof not found'
                ], 404);
            }

            $staff = Auth::user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;

            // Only CPDO can upload certificates
            if ($position !== 'cpdo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only CPDO staff can upload certificates'
                ], 403);
            }

            // Check if OR is verified first
            if ($paymentProof->status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify the Official Receipt first before uploading certificates'
                ], 422);
            }

            $certificateType = $request->type;
            $certificateLink = $request->link;
            $certificateName = $certificateType === 'zoning_cert' ? 'Zoning Certificate' : 'Locational Clearance';

            if ($certificateType === 'zoning_cert') {
                $paymentProof->update([
                    'zoning_cert_link' => $request->link,
                    'zoning_cert_uploaded_at' => now(),
                    'zoning_cert_uploaded_by' => $staff->id
                ]);
                $message = 'Zoning Certificate uploaded successfully';
            } else {
                $paymentProof->update([
                    'locational_clearance_link' => $request->link,
                    'locational_clearance_uploaded_at' => now(),
                    'locational_clearance_uploaded_by' => $staff->id
                ]);
                $message = 'Locational Clearance uploaded successfully';
            }

            // Send notification to applicant about certificate upload (database + email)
            try {
                $this->notificationService->notifyCertificateUploaded(
                    $paymentProof->application, 
                    $staff, 
                    $paymentProof, 
                    $certificateType, 
                    $certificateLink
                );
                Log::info('Applicant notified about certificate upload');
            } catch (\Exception $e) {
                Log::error('Failed to send certificate upload notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $paymentProof
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading certificate: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload certificate: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeCertificate(Request $request, $id)
    {
        Log::info('PaymentProofController@removeCertificate called', ['id' => $id]);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:zoning_cert,locational_clearance'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid certificate type'
            ], 422);
        }

        try {
            $paymentProof = PaymentProof::find($id);

            if (!$paymentProof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment proof not found'
                ], 404);
            }

            $staff = Auth::user();
            $staff->load('profile');
            $position = $staff->profile ? $staff->profile->position : null;

            // Only CPDO can remove certificates
            if ($position !== 'cpdo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only CPDO staff can remove certificates'
                ], 403);
            }

            if ($request->type === 'zoning_cert') {
                $paymentProof->update([
                    'zoning_cert_link' => null,
                    'zoning_cert_uploaded_at' => null,
                    'zoning_cert_uploaded_by' => null
                ]);
                $message = 'Zoning Certificate removed successfully';
            } else {
                $paymentProof->update([
                    'locational_clearance_link' => null,
                    'locational_clearance_uploaded_at' => null,
                    'locational_clearance_uploaded_by' => null
                ]);
                $message = 'Locational Clearance removed successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing certificate: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove certificate'
            ], 500);
        }
    }

    public function getPaymentProof($applicationId)
    {
        try {
            $paymentProof = PaymentProof::where('application_id', $applicationId)
                ->with(['verifier', 'zoningCertUploader', 'locationalClearanceUploader'])
                ->first();

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