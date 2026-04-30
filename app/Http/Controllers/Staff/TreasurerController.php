<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\AssessmentFee;
use App\Models\PaymentOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TreasurerController extends Controller
{
    public function getPaymentAssessments(Request $request)
    {
        try {
            Log::info('TreasurerController@getPaymentAssessments START');

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user->load('profile');
            $position = $user->profile ? $user->profile->position : null;

            if ($position !== 'treasurer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Treasurer access only.'
                ], 403);
            }

            $applications = ApplicationDocument::with(['user', 'assessmentFee', 'paymentOrders', 'paymentProof'])
                ->whereIn('status', ['for-assessment', 'approved', 'for-release', 'verified'])
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Found applications count: ' . $applications->count());

            $formattedApplications = [];
            $totalPaidAmount = 0;
            $paidApplicationsCount = 0;
            $totalAssessmentAmount = 0;

            foreach ($applications as $app) {
                // Calculate building permit fee
                $buildingFee = 0;
                if ($app->assessmentFee) {
                    $buildingFee = ($app->assessmentFee->building_fee ?? 0) + 
                                   ($app->assessmentFee->line_grade ?? 0) + 
                                   ($app->assessmentFee->sanitary_fee ?? 0) + 
                                   ($app->assessmentFee->mechanical_fee ?? 0) + 
                                   ($app->assessmentFee->electrical_fee ?? 0) + 
                                   ($app->assessmentFee->penalties_fines ?? 0);
                    
                    if ($app->assessmentFee->additional_fees) {
                        $additionalFees = is_string($app->assessmentFee->additional_fees) 
                            ? json_decode($app->assessmentFee->additional_fees, true) 
                            : $app->assessmentFee->additional_fees;
                        if (is_array($additionalFees)) {
                            foreach ($additionalFees as $fee) {
                                $buildingFee += $fee['amount'] ?? 0;
                            }
                        }
                    }
                }

                // Calculate CPDO fee
                $cpdoFee = ($app->cpdo_zonal_location_fee ?? 0) + 
                           ($app->cpdo_palc_fee ?? 0) + 
                           ($app->cpdo_development_permit_fee ?? 0) + 
                           ($app->cpdo_alteration_permit_fee ?? 0) + 
                           ($app->cpdo_site_zoning_certificate_fee ?? 0);
                
                if ($app->cpdo_additional_fees) {
                    $cpdoAdditionalFees = is_string($app->cpdo_additional_fees) 
                        ? json_decode($app->cpdo_additional_fees, true) 
                        : $app->cpdo_additional_fees;
                    if (is_array($cpdoAdditionalFees)) {
                        foreach ($cpdoAdditionalFees as $fee) {
                            $cpdoFee += $fee['amount'] ?? 0;
                        }
                    }
                }

                $totalAssessment = $buildingFee + $cpdoFee;
                $totalAssessmentAmount += $totalAssessment;
                
                // Check if OR is uploaded
                $hasOrUploaded = $app->paymentProof && !empty($app->paymentProof->or_link);
                $orLink = $app->paymentProof ? $app->paymentProof->or_link : null;
                
                $paymentStatus = $hasOrUploaded ? 'paid' : 'unpaid';
                
                if ($hasOrUploaded) {
                    $totalPaidAmount += $totalAssessment;
                    $paidApplicationsCount++;
                }

                // Get building permit assessor name
                $buildingAssessorName = null;
                if ($app->assessmentFee && $app->assessmentFee->assessed_by) {
                    $assessor = User::find($app->assessmentFee->assessed_by);
                    if ($assessor) {
                        $buildingAssessorName = $assessor->first_name . ' ' . $assessor->last_name;
                    }
                }

                // Get CPDO assessor name
                $cpdoAssessorName = null;
                if ($app->cpdo_assessed_by) {
                    $cpdoAssessor = User::find($app->cpdo_assessed_by);
                    if ($cpdoAssessor) {
                        $cpdoAssessorName = $cpdoAssessor->first_name . ' ' . $cpdoAssessor->last_name;
                    }
                }

                $formattedApplications[] = [
                    'id' => $app->id,
                    'application_number' => $app->application_number ?? 'N/A',
                    'applicant_name' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                    'applicant_email' => $app->user ? $app->user->email : null,
                    'building_permit_fee' => $buildingFee,
                    'cpdo_fee' => $cpdoFee,
                    'total_assessment' => $totalAssessment,
                    'payment_status' => $paymentStatus,
                    'or_link' => $orLink,
                    'payment_orders' => $app->paymentOrders ? $app->paymentOrders->map(function($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'payment_date' => $order->payment_date ? date('Y-m-d', strtotime($order->payment_date)) : null,
                            'notes' => $order->notes,
                            'created_by' => $order->creator ? $order->creator->first_name . ' ' . $order->creator->last_name : 'System',
                            'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null
                        ];
                    }) : [],
                    'assessment_details' => $app->assessmentFee ? [
                        'line_grade' => floatval($app->assessmentFee->line_grade ?? 0),
                        'building_fee' => floatval($app->assessmentFee->building_fee ?? 0),
                        'sanitary_fee' => floatval($app->assessmentFee->sanitary_fee ?? 0),
                        'mechanical_fee' => floatval($app->assessmentFee->mechanical_fee ?? 0),
                        'electrical_fee' => floatval($app->assessmentFee->electrical_fee ?? 0),
                        'penalties_fines' => floatval($app->assessmentFee->penalties_fines ?? 0),
                        'total_amount' => floatval($app->assessmentFee->total_amount ?? 0),
                        'assessment_notes' => $app->assessmentFee->assessment_notes,
                        'additional_fees' => $app->assessmentFee->additional_fees ? 
                            (is_string($app->assessmentFee->additional_fees) ? json_decode($app->assessmentFee->additional_fees, true) : $app->assessmentFee->additional_fees) : [],
                        'assessed_by_name' => $buildingAssessorName,
                        'assessed_at' => $app->assessmentFee->assessed_at ? 
                            date('F d, Y g:i A', strtotime($app->assessmentFee->assessed_at)) : null
                    ] : null,
                    'cpdo_assessment_details' => [
                        'assessment_date' => $app->cpdo_assessment_date ? date('F d, Y', strtotime($app->cpdo_assessment_date)) : null,
                        'zonal_location_fee' => floatval($app->cpdo_zonal_location_fee ?? 0),
                        'palc_fee' => floatval($app->cpdo_palc_fee ?? 0),
                        'development_permit_fee' => floatval($app->cpdo_development_permit_fee ?? 0),
                        'alteration_permit_fee' => floatval($app->cpdo_alteration_permit_fee ?? 0),
                        'site_zoning_certificate_fee' => floatval($app->cpdo_site_zoning_certificate_fee ?? 0),
                        'total_amount' => floatval($app->cpdo_total_amount ?? 0),
                        'assessment_notes' => $app->cpdo_assessment_notes,
                        'additional_fees' => $app->cpdo_additional_fees ? 
                            (is_string($app->cpdo_additional_fees) ? json_decode($app->cpdo_additional_fees, true) : $app->cpdo_additional_fees) : [],
                        'assessed_by_name' => $cpdoAssessorName,
                        'assessed_at' => $app->cpdo_assessed_at ? 
                            date('F d, Y g:i A', strtotime($app->cpdo_assessed_at)) : null
                    ],
                    'created_at' => $app->created_at ? $app->created_at->format('Y-m-d') : null
                ];
            }

            $unpaidCount = $applications->count() - $paidApplicationsCount;
            $pendingAmount = $totalAssessmentAmount - $totalPaidAmount;

            $stats = [
                'total_assessments' => $applications->count(),
                'total_assessment_amount' => $totalAssessmentAmount,
                'total_collected' => $totalPaidAmount,
                'paid_amount' => $totalPaidAmount,
                'pending_or_count' => $unpaidCount,
                'pending_amount' => $pendingAmount,
                'average_assessment' => $applications->count() > 0 ? $totalAssessmentAmount / $applications->count() : 0,
                'trend_change' => 0
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'applications' => $formattedApplications,
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => count($formattedApplications),
                    'total' => count($formattedApplications),
                    'from' => 1,
                    'to' => count($formattedApplications)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment assessments: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading assessments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addPaymentOrder(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_number' => 'required|string|unique:payment_orders,order_number',
                'payment_date' => 'required|date',
                'notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $application = ApplicationDocument::find($id);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $user = Auth::user();

            $paymentOrder = PaymentOrder::create([
                'application_id' => $id,
                'order_number' => $request->order_number,
                'payment_date' => $request->payment_date,
                'amount_paid' => 0,
                'notes' => $request->notes,
                'created_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order number added successfully',
                'data' => [
                    'id' => $paymentOrder->id,
                    'order_number' => $paymentOrder->order_number,
                    'payment_date' => date('Y-m-d', strtotime($paymentOrder->payment_date)),
                    'notes' => $paymentOrder->notes,
                    'created_by' => $user->first_name . ' ' . $user->last_name,
                    'created_at' => $paymentOrder->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding payment order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add order number: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMonthlyCollection(Request $request)
    {
        try {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $totalAssessed = 0;
                $paymentProofs = \App\Models\PaymentProof::whereMonth('created_at', $month)
                    ->whereYear('created_at', now()->year)
                    ->whereNotNull('or_link')
                    ->get();
                    
                foreach ($paymentProofs as $proof) {
                    $app = $proof->application;
                    if ($app) {
                        $buildingFee = 0;
                        if ($app->assessmentFee) {
                            $buildingFee = ($app->assessmentFee->building_fee ?? 0) + 
                                           ($app->assessmentFee->line_grade ?? 0) + 
                                           ($app->assessmentFee->sanitary_fee ?? 0) + 
                                           ($app->assessmentFee->mechanical_fee ?? 0) + 
                                           ($app->assessmentFee->electrical_fee ?? 0) + 
                                           ($app->assessmentFee->penalties_fines ?? 0);
                        }
                        $cpdoFee = ($app->cpdo_zonal_location_fee ?? 0) + 
                                   ($app->cpdo_palc_fee ?? 0) + 
                                   ($app->cpdo_development_permit_fee ?? 0) + 
                                   ($app->cpdo_alteration_permit_fee ?? 0) + 
                                   ($app->cpdo_site_zoning_certificate_fee ?? 0);
                        $totalAssessed += ($buildingFee + $cpdoFee);
                    }
                }

                $monthlyData[] = [
                    'month' => date('F', mktime(0, 0, 0, $month, 1)),
                    'amount' => $totalAssessed
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $monthlyData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monthly collection: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching collection data'
            ], 500);
        }
    }

    public function exportPaymentAssessments()
    {
        try {
            $applications = ApplicationDocument::with(['user', 'assessmentFee', 'paymentProof'])
                ->whereIn('status', ['for-assessment', 'approved', 'for-release', 'verified'])
                ->get();

            $filename = 'payment_assessments_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($applications) {
                $handle = fopen('php://output', 'w');
                
                fputcsv($handle, [
                    'Application Number',
                    'Applicant Name',
                    'Building Permit Fee',
                    'CPDO Fee',
                    'Total Assessment',
                    'OR Uploaded',
                    'Status',
                    'Created At'
                ]);

                foreach ($applications as $app) {
                    $buildingFee = 0;
                    if ($app->assessmentFee) {
                        $buildingFee = ($app->assessmentFee->building_fee ?? 0) + 
                                       ($app->assessmentFee->line_grade ?? 0) + 
                                       ($app->assessmentFee->sanitary_fee ?? 0) + 
                                       ($app->assessmentFee->mechanical_fee ?? 0) + 
                                       ($app->assessmentFee->electrical_fee ?? 0) + 
                                       ($app->assessmentFee->penalties_fines ?? 0);
                    }

                    $cpdoFee = ($app->cpdo_zonal_location_fee ?? 0) + 
                               ($app->cpdo_palc_fee ?? 0) + 
                               ($app->cpdo_development_permit_fee ?? 0) + 
                               ($app->cpdo_alteration_permit_fee ?? 0) + 
                               ($app->cpdo_site_zoning_certificate_fee ?? 0);

                    $totalAssessment = $buildingFee + $cpdoFee;
                    $hasOrUploaded = $app->paymentProof && !empty($app->paymentProof->or_link);
                    $status = $hasOrUploaded ? 'Paid' : 'Unpaid';

                    fputcsv($handle, [
                        $app->application_number ?? 'N/A',
                        $app->user ? $app->user->first_name . ' ' . $app->user->last_name : 'Unknown',
                        number_format($buildingFee, 2),
                        number_format($cpdoFee, 2),
                        number_format($totalAssessment, 2),
                        $hasOrUploaded ? 'Yes' : 'No',
                        $status,
                        $app->created_at ? $app->created_at->format('Y-m-d') : ''
                    ]);
                }

                fclose($handle);
            };

            return response()->streamDownload($callback, $filename, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting payment assessments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data'
            ], 500);
        }
    }
    public function addPaymentOrder(Request $request, $id)
{
    try {
        Log::info('addPaymentOrder called', ['application_id' => $id]);

        $validator = Validator::make($request->all(), [
            'order_number' => 'required|string|unique:payment_orders,order_number',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $application = ApplicationDocument::find($id);
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $user = Auth::user();
        $user->load('profile');
        $position = $user->profile ? $user->profile->position : null;

        if ($position !== 'treasurer') {
            return response()->json([
                'success' => false,
                'message' => 'Only treasurer can add payment orders'
            ], 403);
        }

        $paymentOrder = PaymentOrder::create([
            'application_id' => $id,
            'order_number' => $request->order_number,
            'payment_date' => $request->payment_date,
            'amount_paid' => 0,
            'notes' => $request->notes,
            'created_by' => $user->id
        ]);

        // Send notification to applicant about the payment order
        try {
            $this->notificationService->notifyPaymentOrderCreated($application, $paymentOrder, $user);
            Log::info('✅ Payment order notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send payment order notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Order number added successfully',
            'data' => [
                'id' => $paymentOrder->id,
                'order_number' => $paymentOrder->order_number,
                'payment_date' => date('Y-m-d', strtotime($paymentOrder->payment_date)),
                'notes' => $paymentOrder->notes,
                'created_by' => $user->first_name . ' ' . $user->last_name,
                'created_at' => $paymentOrder->created_at->format('Y-m-d H:i:s')
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error adding payment order: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to add order number: ' . $e->getMessage()
        ], 500);
    }
}
}