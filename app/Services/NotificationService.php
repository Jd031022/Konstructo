<?php

namespace App\Services;

use App\Models\User;
use App\Models\ApplicationDocument;
use App\Models\PaymentProof;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\AdminNoteNotification;
use App\Notifications\NewApplicationNotification;
use App\Notifications\HardCopyReceivedNotification;
use App\Notifications\StaffStatusChangeNotification;
use App\Notifications\FSECUploadedNotification;
use App\Notifications\BFPCommentsAddedNotification;
use App\Notifications\PaymentProofUploadedNotification;
use App\Notifications\PaymentProofVerifiedNotification;
use App\Notifications\PaymentProofRejectedNotification;
use App\Notifications\CertificateUploadedNotification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationService
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Get payment proof staff email content
     */
    private function getPaymentProofStaffEmailContent(ApplicationDocument $application, PaymentProof $paymentProof, User $staff, $appUrl)
    {
        $applicant = $application->user;
        $applicantName = $applicant ? $applicant->first_name . ' ' . $applicant->last_name : 'N/A';
        $applicantEmail = $applicant ? $applicant->email : 'N/A';
        
        $formattedNumber = $application->application_number;
        if (strlen($formattedNumber) === 10) {
            $formattedNumber = substr($formattedNumber, 0, 2) . '-' . 
                              substr($formattedNumber, 2, 4) . '-' . 
                              substr($formattedNumber, 6, 4);
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                .header { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 40px 30px; background-color: #ffffff; }
                .greeting { font-size: 18px; color: #D97706; font-weight: 500; margin-bottom: 20px; }
                .badge { background-color: #FEF3C7; color: #D97706; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #F59E0B; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #F59E0B; }
                .info-box p { margin: 10px 0; }
                .info-box p:first-child { margin-top: 0; }
                .info-box p:last-child { margin-bottom: 0; }
                .or-link { 
                    display: inline-block; 
                    background-color: #e8f4f8; 
                    padding: 10px 15px; 
                    border-radius: 8px; 
                    word-break: break-all;
                    font-family: monospace;
                    font-size: 13px;
                    margin-top: 10px;
                }
                .or-link a { color: #155386; text-decoration: none; }
                .or-link a:hover { text-decoration: underline; }
                .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                .button:hover { opacity: 0.9; transform: translateY(-2px); }
                .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                .brand-name { font-weight: 600; color: #155386; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Payment Proof Uploaded</h1>
                    <p>Action Required: Verify Official Receipt</p>
                </div>
                <div class='content'>
                    <div class='greeting'>Dear {$staff->first_name},</div>
                    
                    <p>A new payment proof (Official Receipt) has been uploaded for the following application:</p>
                    
                    <div class='info-box'>
                        <p><strong>📋 Application Number:</strong> {$formattedNumber}</p>
                        <p><strong>👤 Applicant Name:</strong> {$applicantName}</p>
                        <p><strong>📧 Applicant Email:</strong> {$applicantEmail}</p>
                        <p><strong>🔗 Official Receipt Link:</strong></p>
                        <div class='or-link'>
                            <a href='{$paymentProof->or_link}' target='_blank'>{$paymentProof->or_link}</a>
                        </div>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$appUrl}' class='button'>Review Application</a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                        Please review and verify the Official Receipt to proceed with the application process.
                    </p>
                    <p style='font-size: 12px; color: #6c757d; text-align: center;'>
                        This is an automated notification from Konstructo.
                    </p>
                </div>
                <div class='footer'>
                    <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                    <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Send notification to staff when applicant uploads payment proof (OR)
     */
    public function notifyStaffPaymentProofUploaded(ApplicationDocument $application, PaymentProof $paymentProof)
    {
        Log::info('========== NOTIFY STAFF PAYMENT PROOF UPLOADED ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'payment_proof_id' => $paymentProof->id
        ]);
        
        // Get staff users with relevant roles (CPDO, Treasurer, Engineer, Architect)
        $staffUsers = User::where('role', 'staff')
            ->whereHas('profile', function($query) {
                $query->whereIn('position', ['cpdo', 'treasurer', 'engineer', 'architect']);
            })
            ->get();
        
        $applicant = $application->user;
        $staffNotifiedCount = 0;
        
        foreach ($staffUsers as $staff) {
            try {
                // Send database notification to staff
                $staff->notify(new PaymentProofUploadedNotification($application, $paymentProof, $applicant));
                $staffNotifiedCount++;
                Log::info('✅ Payment proof notification sent to staff: ' . $staff->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send payment proof notification to staff ' . $staff->email . ': ' . $e->getMessage());
            }
        }
        
        Log::info("Payment proof notifications sent to {$staffNotifiedCount} staff members");
        
        // Also send email to staff as backup
        try {
            $subject = 'New Payment Proof Uploaded - Action Required';
            $appUrl = env('APP_URL') . "/staff/application-details/{$application->id}";
            
            foreach ($staffUsers as $staff) {
                $htmlContent = $this->getPaymentProofStaffEmailContent($application, $paymentProof, $staff, $appUrl);
                $this->gmailService->sendEmail($staff->email, $subject, $htmlContent);
                Log::info('✅ Payment proof email sent to staff: ' . $staff->email);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to send payment proof emails to staff: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY STAFF PAYMENT PROOF UPLOADED END ==========');
    }

    /**
     * Notify applicant that payment proof has been verified
     */
    public function notifyPaymentProofVerified(ApplicationDocument $application, User $staff, PaymentProof $paymentProof)
    {
        Log::info('========== NOTIFY PAYMENT PROOF VERIFIED ==========');
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found!');
            return;
        }
        
        // Send database notification to applicant
        try {
            $applicant->notify(new PaymentProofVerifiedNotification($application, $staff, $paymentProof));
            Log::info('✅ Database notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send database notification: ' . $e->getMessage());
        }
        
        // Send email notification via GmailService
        try {
            $this->gmailService->sendORVerificationEmail(
                $applicant->email,
                $application->application_number,
                $applicant->first_name,
                $application->id,
                $staff->first_name . ' ' . $staff->last_name
            );
            Log::info('✅ Email notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send email notification: ' . $e->getMessage());
        }
        
        // Log activity
        try {
            if (Schema::hasTable('application_review_activities')) {
                $application->reviewActivities()->create([
                    'reviewer_id' => $staff->id,
                    'action' => 'payment_proof_verified',
                    'new_status' => $application->status,
                    'remarks' => "Payment proof (OR) verified by {$staff->first_name} {$staff->last_name}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log activity: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY PAYMENT PROOF VERIFIED END ==========');
    }

    /**
     * Notify applicant that payment proof has been rejected
     */
    public function notifyPaymentProofRejected(ApplicationDocument $application, User $staff, $reason, PaymentProof $paymentProof)
    {
        Log::info('========== NOTIFY PAYMENT PROOF REJECTED ==========');
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found!');
            return;
        }
        
        // Send database notification to applicant
        try {
            $applicant->notify(new PaymentProofRejectedNotification($application, $staff, $reason, $paymentProof));
            Log::info('✅ Database notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send database notification: ' . $e->getMessage());
        }
        
        // Send email notification via GmailService
        try {
            $this->gmailService->sendORRejectionEmail(
                $applicant->email,
                $application->application_number,
                $applicant->first_name,
                $application->id,
                $staff->first_name . ' ' . $staff->last_name,
                $reason
            );
            Log::info('✅ Email notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send email notification: ' . $e->getMessage());
        }
        
        // Log activity
        try {
            if (Schema::hasTable('application_review_activities')) {
                $application->reviewActivities()->create([
                    'reviewer_id' => $staff->id,
                    'action' => 'payment_proof_rejected',
                    'new_status' => $application->status,
                    'remarks' => "Payment proof (OR) rejected. Reason: {$reason}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log activity: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY PAYMENT PROOF REJECTED END ==========');
    }

    /**
     * Notify applicant when certificate is uploaded by CPDO
     */
    public function notifyCertificateUploaded(ApplicationDocument $application, User $cpdoUser, PaymentProof $paymentProof, $certificateType, $certificateLink)
    {
        Log::info('========== NOTIFY CERTIFICATE UPLOADED ==========');
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found!');
            return;
        }
        
        $certificateName = $certificateType === 'zoning_cert' ? 'Zoning Certificate' : 'Locational Clearance';
        
        // Send database notification to applicant
        try {
            $applicant->notify(new CertificateUploadedNotification($application, $cpdoUser, $certificateType, $certificateName, $certificateLink));
            Log::info('✅ Database notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send database notification: ' . $e->getMessage());
        }
        
        // Send email notification via GmailService
        try {
            if ($certificateType === 'zoning_cert') {
                $this->gmailService->sendZoningCertificateUploadedEmail(
                    $applicant->email,
                    $application->application_number,
                    $applicant->first_name,
                    $certificateLink,
                    $application->id,
                    $cpdoUser->first_name . ' ' . $cpdoUser->last_name
                );
            } else {
                $this->gmailService->sendLocationalClearanceUploadedEmail(
                    $applicant->email,
                    $application->application_number,
                    $applicant->first_name,
                    $certificateLink,
                    $application->id,
                    $cpdoUser->first_name . ' ' . $cpdoUser->last_name
                );
            }
            Log::info('✅ Email notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send email notification: ' . $e->getMessage());
        }
        
        // Log activity
        try {
            if (Schema::hasTable('application_review_activities')) {
                $application->reviewActivities()->create([
                    'reviewer_id' => $cpdoUser->id,
                    'action' => 'certificate_uploaded',
                    'new_status' => $application->status,
                    'remarks' => "{$certificateName} uploaded by CPDO: {$cpdoUser->first_name} {$cpdoUser->last_name}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log activity: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY CERTIFICATE UPLOADED END ==========');
    }

    /**
     * Send notification to applicant when CPDO approves application
     */
    public function notifyCPDOApproved(ApplicationDocument $application, User $cpdoUser)
    {
        Log::info('========== NOTIFY CPDO APPROVED START ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'cpdo_user_id' => $cpdoUser->id,
            'cpdo_user_name' => $cpdoUser->first_name . ' ' . $cpdoUser->last_name
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found for CPDO approval notification!');
            return;
        }
        
        $message = "Your application has been approved by CPDO. Other departments can now proceed with verification.";
        $details = "The City Planning and Development Office (CPDO) has reviewed and approved your application documents. You will be notified as other departments complete their verification.";
        
        try {
            $notification = new ApplicationStatusNotification(
                $application,
                $application->status,
                $application->status,
                $message,
                $details
            );
            $applicant->notify($notification);
            Log::info('✅ CPDO approval database notification sent to applicant: ' . $applicant->email);
        } catch (\Exception $e) {
            Log::error('❌ Failed to send CPDO approval database notification: ' . $e->getMessage());
        }

        try {
            $this->gmailService->sendCPDOApprovalEmail(
                $applicant->email,
                $application->application_number,
                $applicant->first_name,
                $application->id,
                $cpdoUser->first_name . ' ' . $cpdoUser->last_name
            );
            Log::info('✅ CPDO approval email sent via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send CPDO approval email: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $cpdoUser->id,
                    'action' => 'cpdo_approved',
                    'old_status' => $application->cpdo_status ?? 'pending',
                    'new_status' => 'approved',
                    'remarks' => "Application approved by CPDO: {$cpdoUser->first_name} {$cpdoUser->last_name}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ CPDO approval activity logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log CPDO approval activity: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY CPDO APPROVED END ==========');
    }

    /**
     * Send notification to applicant when CPDO rejects application
     */
    public function notifyCPDORejected(ApplicationDocument $application, User $cpdoUser, $remarks = null)
    {
        Log::info('========== NOTIFY CPDO REJECTED START ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'cpdo_user_id' => $cpdoUser->id,
            'cpdo_user_name' => $cpdoUser->first_name . ' ' . $cpdoUser->last_name,
            'remarks' => $remarks
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found for CPDO rejection notification!');
            return;
        }
        
        $message = "Your application has been rejected by CPDO. Please review the remarks and resubmit.";
        $details = $remarks ?? "The City Planning and Development Office (CPDO) has reviewed your application and found issues that need to be addressed.";
        
        try {
            $notification = new ApplicationStatusNotification(
                $application,
                $application->status,
                'rejected',
                $message,
                $details
            );
            $applicant->notify($notification);
            Log::info('✅ CPDO rejection database notification sent to applicant: ' . $applicant->email);
        } catch (\Exception $e) {
            Log::error('❌ Failed to send CPDO rejection database notification: ' . $e->getMessage());
        }

        try {
            $this->gmailService->sendCPDORejectionEmail(
                $applicant->email,
                $application->application_number,
                $applicant->first_name,
                $application->id,
                $cpdoUser->first_name . ' ' . $cpdoUser->last_name,
                $remarks
            );
            Log::info('✅ CPDO rejection email sent via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send CPDO rejection email: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $cpdoUser->id,
                    'action' => 'cpdo_rejected',
                    'old_status' => $application->cpdo_status ?? 'pending',
                    'new_status' => 'rejected',
                    'remarks' => $remarks ?? "Application rejected by CPDO: {$cpdoUser->first_name} {$cpdoUser->last_name}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ CPDO rejection activity logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log CPDO rejection activity: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY CPDO REJECTED END ==========');
    }
/**
 * Send notification when application is submitted (with application number)
 * 
 * @param ApplicationDocument $application
 * @return void
 */
public function applicationSubmitted(ApplicationDocument $application)
{
    Log::info('========== APPLICATION SUBMITTED NOTIFICATION ==========');
    Log::info('Parameters:', [
        'application_id' => $application->id,
        'application_number' => $application->application_number,
        'applicant_id' => $application->user_id
    ]);
    
    $applicant = $application->user;
    
    if (!$applicant) {
        Log::error('❌ No applicant found for application submission notification!');
        return;
    }
    
    // Send email notification to applicant
    try {
        $this->sendApplicationSubmittedEmail($application, $applicant);
        Log::info('✅ Application submission email sent to applicant: ' . $applicant->email);
    } catch (\Exception $e) {
        Log::error('❌ Failed to send application submission email: ' . $e->getMessage());
    }
    
    // Notify staff about new application
    try {
        $this->notifyStaffNewApplication($application);
        Log::info('✅ Staff notified about new application');
    } catch (\Exception $e) {
        Log::error('❌ Failed to notify staff about new application: ' . $e->getMessage());
    }
    
    Log::info('========== APPLICATION SUBMITTED NOTIFICATION END ==========');
}
    /**
     * Send email notification for application submission (with application number)
     */
    public function sendApplicationSubmittedEmail(ApplicationDocument $application, User $applicant)
    {
        Log::info('========== SEND APPLICATION SUBMISSION EMAIL ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'applicant_email' => $applicant->email,
            'applicant_name' => $applicant->first_name . ' ' . $applicant->last_name
        ]);
        
        $subject = 'Application Submitted Successfully - Konstructo';
        $htmlContent = $this->getApplicationSubmittedEmailContent($application, $applicant);
        
        try {
            $this->gmailService->sendEmail($applicant->email, $subject, $htmlContent);
            Log::info('✅ Application submission email sent successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to send submission email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification for approved application
     */
    public function sendApprovedEmail(ApplicationDocument $application, User $reviewer)
    {
        $applicant = $application->user;
        
        Log::info('Sending approved email via GmailService', [
            'applicant_id' => $applicant->id,
            'applicant_email' => $applicant->email,
            'application_id' => $application->id
        ]);
        
        try {
            $this->gmailService->sendStatusEmail(
                $applicant->email,
                'approved',
                $application->application_number,
                $applicant->first_name,
                $application->id
            );
            
            Log::info('✅ Approved email sent successfully via GmailService');
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to send approved email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification for for-release application
     */
    public function sendForReleaseEmail(ApplicationDocument $application, User $reviewer)
    {
        $applicant = $application->user;
        
        Log::info('Sending for-release email via GmailService', [
            'applicant_id' => $applicant->id,
            'applicant_email' => $applicant->email,
            'application_id' => $application->id
        ]);
        
        try {
            $this->gmailService->sendStatusEmail(
                $applicant->email,
                'for-release',
                $application->application_number,
                $applicant->first_name,
                $application->id
            );
            
            Log::info('✅ For-release email sent successfully via GmailService');
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Failed to send for-release email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification when BFP uploads FSEC document
     */
    public function notifyFSECUploaded(ApplicationDocument $application, User $bfpUser, $fsecLink, $filename)
    {
        Log::info('========== NOTIFY FSEC UPLOADED ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'bfp_user_id' => $bfpUser->id,
            'bfp_user_name' => $bfpUser->first_name . ' ' . $bfpUser->last_name,
            'filename' => $filename
        ]);
        
        $applicant = $application->user;
        
        // 1. Notify the applicant
        if ($applicant) {
            try {
                $applicant->notify(new FSECUploadedNotification($application, $bfpUser, $fsecLink, $filename, 'applicant'));
                Log::info('✅ FSEC notification sent to applicant: ' . $applicant->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send FSEC notification to applicant: ' . $e->getMessage());
            }
        }
        
        // 2. Notify all staff (including non-BFP)
        $staff = User::whereIn('role', ['admin', 'staff'])->get();
        $staffNotifiedCount = 0;
        
        foreach ($staff as $staffMember) {
            // Skip the BFP user who uploaded (they already know)
            if ($staffMember->id === $bfpUser->id) {
                continue;
            }
            
            try {
                $staffMember->notify(new FSECUploadedNotification($application, $bfpUser, $fsecLink, $filename, 'staff'));
                $staffNotifiedCount++;
                Log::info('✅ FSEC notification sent to staff: ' . $staffMember->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send FSEC notification to staff ' . $staffMember->email . ': ' . $e->getMessage());
            }
        }
        
        Log::info("FSEC notifications sent to {$staffNotifiedCount} staff members");
        
        // 3. Send email to applicant (via GmailService)
        try {
            $subject = 'Fire Safety Evaluation Clearance (FSEC) Uploaded - Konstructo';
            $htmlContent = $this->getFSECEmailContent($application, $bfpUser, $fsecLink, $filename);
            $this->gmailService->sendEmail($applicant->email, $subject, $htmlContent);
            Log::info('✅ FSEC email sent to applicant via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send FSEC email: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY FSEC UPLOADED END ==========');
    }

    /**
     * Send notification when BFP adds comments
     */
    public function notifyBFPCommentsAdded(ApplicationDocument $application, User $bfpUser, $comments)
    {
        Log::info('========== NOTIFY BFP COMMENTS ADDED ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'bfp_user_id' => $bfpUser->id,
            'bfp_user_name' => $bfpUser->first_name . ' ' . $bfpUser->last_name,
            'comments_length' => strlen($comments)
        ]);
        
        $applicant = $application->user;
        
        // 1. Notify the applicant
        if ($applicant) {
            try {
                $applicant->notify(new BFPCommentsAddedNotification($application, $bfpUser, $comments, 'applicant'));
                Log::info('✅ BFP comments notification sent to applicant: ' . $applicant->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send BFP comments notification to applicant: ' . $e->getMessage());
            }
        }
        
        // 2. Notify all staff (including non-BFP)
        $staff = User::whereIn('role', ['admin', 'staff'])->get();
        $staffNotifiedCount = 0;
        
        foreach ($staff as $staffMember) {
            // Skip the BFP user who added comments (they already know)
            if ($staffMember->id === $bfpUser->id) {
                continue;
            }
            
            try {
                $staffMember->notify(new BFPCommentsAddedNotification($application, $bfpUser, $comments, 'staff'));
                $staffNotifiedCount++;
                Log::info('✅ BFP comments notification sent to staff: ' . $staffMember->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send BFP comments notification to staff ' . $staffMember->email . ': ' . $e->getMessage());
            }
        }
        
        Log::info("BFP comments notifications sent to {$staffNotifiedCount} staff members");
        
        // 3. Send email to applicant (via GmailService)
        try {
            $subject = 'BFP Comments Added to Your Application - Konstructo';
            $htmlContent = $this->getBFPCommentsEmailContent($application, $bfpUser, $comments);
            $this->gmailService->sendEmail($applicant->email, $subject, $htmlContent);
            Log::info('✅ BFP comments email sent to applicant via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send BFP comments email: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY BFP COMMENTS ADDED END ==========');
    }

    /**
     * Send assessment completed notification to applicant with fee breakdown
     */
    public function notifyAssessmentCompleted(ApplicationDocument $application, $oldStatus, $newStatus, User $reviewer, $assessmentData)
    {
        Log::info('========== NOTIFY ASSESSMENT COMPLETED START ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reviewer_id' => $reviewer->id,
            'total_amount' => $assessmentData['total_amount'] ?? 0
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found for assessment notification!');
            return;
        }
        
        Log::info('Applicant found:', [
            'applicant_id' => $applicant->id,
            'applicant_email' => $applicant->email,
            'applicant_name' => $applicant->first_name . ' ' . $applicant->last_name
        ]);

        $message = "Your application assessment has been completed. Total fee: ₱" . number_format($assessmentData['total_amount'] ?? 0, 2);
        
        try {
            $this->gmailService->sendAssessmentEmail(
                $applicant->email,
                $application->application_number,
                $applicant->first_name,
                $assessmentData,
                $application->id
            );
            Log::info('✅ Assessment email sent successfully via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send assessment email: ' . $e->getMessage());
        }
        
        try {
            $notification = new ApplicationStatusNotification(
                $application,
                $oldStatus,
                $newStatus,
                $message,
                "Total Building Permit Fee: ₱" . number_format($assessmentData['total_amount'] ?? 0, 2)
            );
            $applicant->notify($notification);
            Log::info('✅ Assessment database notification sent');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send assessment database notification: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $reviewer->id,
                    'action' => 'assessment_completed',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => "Assessment completed. Total fee: ₱" . number_format($assessmentData['total_amount'] ?? 0, 2),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                Log::info('✅ Assessment activity logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log assessment activity: ' . $e->getMessage());
        }

        $application->update(['last_updated_by' => $reviewer->id]);
        
        Log::info('========== NOTIFY ASSESSMENT COMPLETED END ==========');
    }

    /**
     * Send notification to applicant when status changes
     */
    public function notifyApplicantStatusChange(ApplicationDocument $application, $oldStatus, $newStatus, User $reviewer)
    {
        Log::info('========== NOTIFY APPLICANT STATUS CHANGE START ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'application_number' => $application->application_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reviewer_id' => $reviewer->id,
            'reviewer_email' => $reviewer->email,
            'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found for application!');
            Log::info('========== NOTIFY APPLICANT STATUS CHANGE END (ERROR) ==========');
            return;
        }
        
        Log::info('Applicant found:', [
            'applicant_id' => $applicant->id,
            'applicant_email' => $applicant->email,
            'applicant_name' => $applicant->first_name . ' ' . $applicant->last_name,
            'applicant_role' => $applicant->role
        ]);

        $messages = [
            'pending' => 'Your application is now pending review.',
            'under-review' => 'Your application is now under review.',
            'approved' => 'Your application has been approved! Please prepare your hard copies for submission.',
            'rejected' => 'Your application has been rejected. Please check the reason and resubmit.',
            'for-release' => 'Your application is ready for release.',
            'verified' => 'Your application has been verified.'
        ];

        $message = $messages[$newStatus] ?? "Your application status has been updated to {$newStatus}.";
        
        $details = $newStatus === 'approved' 
            ? 'Please prepare the original hard copies of your documents for submission.'
            : null;

        Log::info('Notification details:', [
            'message' => $message,
            'details' => $details
        ]);

        try {
            Log::info('Creating notification instance...');
            
            $notification = new ApplicationStatusNotification(
                $application,
                $oldStatus,
                $newStatus,
                $message,
                $details
            );
            
            Log::info('Notification instance created', [
                'notification_class' => get_class($notification),
                'via' => json_encode($notification->via($applicant))
            ]);
            
            Log::info('Calling $applicant->notify()...');
            
            $tableExists = Schema::hasTable('notifications');
            Log::info('Notifications table exists: ' . ($tableExists ? 'YES' : 'NO'));
            
            if (!$tableExists) {
                Log::error('❌ Notifications table does not exist!');
                return;
            }
            
            $beforeCount = $applicant->notifications()->count();
            Log::info('Notifications before: ' . $beforeCount);
            
            $applicant->notify($notification);
            
            $afterCount = $applicant->notifications()->count();
            Log::info('Notifications after: ' . $afterCount);
            
            if ($afterCount > $beforeCount) {
                Log::info('✅ Notification created successfully in database');
                $latest = $applicant->notifications()->latest()->first();
                Log::info('Latest notification ID: ' . ($latest ? $latest->id : 'null'));
            } else {
                Log::error('❌ No notification was created in the database!');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to send notification: ' . $e->getMessage());
            Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            Log::error($e->getTraceAsString());
        }

        try {
            $this->gmailService->sendStatusEmail(
                $applicant->email,
                $newStatus,
                $application->application_number,
                $applicant->first_name,
                $application->id
            );
            Log::info('✅ Status email sent via GmailService');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send status email: ' . $e->getMessage());
        }

        if (in_array($newStatus, ['verified', 'approved', 'rejected'])) {
            Log::info('Also notifying staff about status change');
            $this->notifyStaffOfStatusChange($application, $oldStatus, $newStatus, $reviewer);
        }

        try {
            Log::info('Creating review activity record...');
            
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $reviewer->id,
                    'action' => 'status_updated',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => "Status changed from {$oldStatus} to {$newStatus} by {$reviewer->first_name} {$reviewer->last_name}",
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ Review activity created with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log review activity: ' . $e->getMessage());
        }

        try {
            $application->update([
                'last_updated_by' => $reviewer->id
            ]);
            Log::info('✅ Updated last_updated_by to: ' . $reviewer->id);
        } catch (\Exception $e) {
            Log::error('❌ Failed to update last_updated_by: ' . $e->getMessage());
        }

        Log::info('========== NOTIFY APPLICANT STATUS CHANGE END ==========');
    }

    /**
     * Send notification when admin adds notes
     */
    public function notifyApplicantOfNote(ApplicationDocument $application, $note, User $reviewer)
    {
        Log::info('========== NOTIFY APPLICANT OF NOTE ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name,
            'note_length' => strlen($note)
        ]);
        
        $applicant = $application->user;
        
        if (!$applicant) {
            Log::error('❌ No applicant found!');
            return;
        }
        
        try {
            $applicant->notify(new AdminNoteNotification(
                $application,
                $note,
                $reviewer
            ));
            Log::info('✅ Note notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send note notification: ' . $e->getMessage());
        }

        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $reviewer->id,
                    'action' => 'note_added',
                    'remarks' => $note,
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ Note activity logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log note activity: ' . $e->getMessage());
        }

        $application->update(['last_updated_by' => $reviewer->id]);
        
        Log::info('========== NOTIFY APPLICANT OF NOTE END ==========');
    }

    /**
     * Send notification to staff when application is submitted
     */
    public function notifyStaffNewApplication(ApplicationDocument $application)
    {
        Log::info('========== NOTIFY STAFF NEW APPLICATION ==========');
        Log::info('Application:', [
            'id' => $application->id,
            'number' => $application->application_number
        ]);
        
        $staff = User::whereIn('role', ['admin', 'staff'])->get();
        $applicant = $application->user;
        
        Log::info('Staff to notify count: ' . $staff->count());
        
        foreach ($staff as $user) {
            try {
                $user->notify(new NewApplicationNotification($application, $applicant));
                Log::info('✅ Notified staff: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to notify staff ' . $user->email . ': ' . $e->getMessage());
            }
        }

        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $applicant->id,
                    'action' => 'application_created',
                    'old_status' => 'draft',
                    'new_status' => 'pending',
                    'remarks' => 'Application submitted by applicant',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ Submission logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log submission: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY STAFF NEW APPLICATION END ==========');
    }

    /**
     * Send notification when hard copy is received
     */
    public function notifyHardCopyReceived(ApplicationDocument $application, User $reviewer)
    {
        Log::info('========== NOTIFY HARD COPY RECEIVED ==========');
        Log::info('Parameters:', [
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'reviewer_name' => $reviewer->first_name . ' ' . $reviewer->last_name
        ]);
        
        $applicant = $application->user;
        
        try {
            $applicant->notify(new HardCopyReceivedNotification($application));
            Log::info('✅ Hard copy notification sent to applicant');
        } catch (\Exception $e) {
            Log::error('❌ Failed to send hard copy notification: ' . $e->getMessage());
        }
        
        try {
            if (Schema::hasTable('application_review_activities')) {
                $activity = $application->reviewActivities()->create([
                    'reviewer_id' => $reviewer->id,
                    'action' => 'hard_copy_received',
                    'remarks' => 'Hard copies received and verified',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent()
                ]);
                Log::info('✅ Hard copy activity logged with ID: ' . ($activity ? $activity->id : 'null'));
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log hard copy activity: ' . $e->getMessage());
        }

        $application->update([
            'hard_copy_received' => true,
            'hard_copy_received_at' => now(),
            'last_updated_by' => $reviewer->id
        ]);
        
        Log::info('========== NOTIFY HARD COPY RECEIVED END ==========');
    }

    /**
     * Send notification to staff about status change (for monitoring)
     */
    protected function notifyStaffOfStatusChange(ApplicationDocument $application, $oldStatus, $newStatus, User $reviewer)
    {
        Log::info('========== NOTIFY STAFF OF STATUS CHANGE ==========');
        
        $staff = User::whereIn('role', ['admin', 'staff'])
            ->where('id', '!=', $reviewer->id)
            ->get();
        
        $applicant = $application->user;
        
        Log::info('Notifying ' . $staff->count() . ' staff members about status change');
        
        foreach ($staff as $user) {
            try {
                $user->notify(new StaffStatusChangeNotification(
                    $application,
                    $applicant,
                    $oldStatus,
                    $newStatus,
                    $reviewer
                ));
                Log::info('✅ Notified staff: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to notify staff ' . $user->email . ': ' . $e->getMessage());
            }
        }

        try {
            if ($staff->count() > 0) {
                Log::info('Staff notified of status change', [
                    'application_id' => $application->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'reviewer' => $reviewer->id,
                    'notified_staff_count' => $staff->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to log staff notification: ' . $e->getMessage());
        }
        
        Log::info('========== NOTIFY STAFF OF STATUS CHANGE END ==========');
    }

    /**
     * Get application submitted email content with application number
     */
    private function getApplicationSubmittedEmailContent(ApplicationDocument $application, User $applicant)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$application->id}";
        $greeting = "Dear " . ($applicant->first_name ?? 'Valued User') . ",";
        $applicationNumber = $application->application_number;
        
        // Format the application number for display with dashes for readability
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
        // Parse the application number to show meaning
        $year = substr($applicationNumber, 0, 2);
        $zipcode = substr($applicationNumber, 2, 4);
        $sequence = substr($applicationNumber, 6, 4);
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .header p { margin: 10px 0 0 0; opacity: 0.9; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
                    .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
                    .app-number-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; text-align: center; border-radius: 12px; margin: 25px 0; border: 1px solid #dee2e6; }
                    .app-number-box .label { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
                    .app-number-box .number { font-size: 32px; font-weight: bold; font-family: monospace; color: #155386; letter-spacing: 2px; }
                    .app-number-box .breakdown { font-size: 11px; color: #6c757d; margin-top: 10px; }
                    .info-section { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10B981; }
                    .info-section h3 { margin: 0 0 10px 0; color: #155386; font-size: 16px; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .next-steps { background-color: #e6f7e6; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10b981; }
                    .next-steps h4 { margin: 0 0 10px 0; color: #065f46; }
                    .next-steps ul { margin: 0; padding-left: 20px; }
                    .next-steps li { margin: 5px 0; color: #065f46; font-size: 14px; }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Application Submitted Successfully!</h1>
                        <p>Your building permit application has been received</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Thank you for submitting your building permit application. We have successfully received your application and it is now in queue for review.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='success-badge'>✓ Application Submitted</span>
                        </div>
                        
                        <div class='app-number-box'>
                            <div class='label'>Your Application Number</div>
                            <div class='number'>{$formattedNumber}</div>
                            <div class='breakdown'>
                                Year: 20{$year} | Location (ZIP): {$zipcode} | Sequence: {$sequence}
                            </div>
                        </div>
                        
                        <div class='info-section'>
                            <h3>📋 What Happens Next?</h3>
                            <p>Your application will be reviewed by our staff. The process typically takes <strong>5-7 working days</strong>. You will receive email notifications for status updates.</p>
                        </div>
                        
                        <div class='next-steps'>
                            <h4>🔑 Keep This Number Safe</h4>
                            <ul>
                                <li>Use this number when checking your application status</li>
                                <li>Reference this number when submitting hard copies to OBO</li>
                                <li>Include this number in all correspondence regarding this application</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>Track Your Application</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Please allow 5-7 working days for initial review. You will be notified once your application status changes.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get FSEC email content
     */
    private function getFSECEmailContent(ApplicationDocument $application, User $bfpUser, $fsecLink, $filename)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$application->id}";
        $applicant = $application->user;
        $greeting = "Dear " . ($applicant->first_name ?? 'Valued User') . ",";
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #DC2626; font-weight: 500; margin-bottom: 20px; }
                    .badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                    .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Fire Safety Evaluation Clearance (FSEC)</h1>
                        <p>Document Uploaded for Your Application</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>The Bureau of Fire Protection (BFP) has uploaded the Fire Safety Evaluation Clearance (FSEC) for your building permit application.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='badge'>🔥 FSEC Document Uploaded</span>
                        </div>
                        
                        <div class='info-box'>
                            <p><strong>Uploaded by:</strong> {$bfpUser->first_name} {$bfpUser->last_name}</p>
                            <p><strong>Document:</strong> {$filename}</p>
                            <p><strong>Application Number:</strong> {$application->application_number}</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$fsecLink}' class='button' target='_blank'>View FSEC Document</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='text-align: center;'>
                            <a href='{$appUrl}' style='color: #155386;'>View Application Details →</a>
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    

    /**
     * Get BFP comments email content
     */
    private function getBFPCommentsEmailContent(ApplicationDocument $application, User $bfpUser, $comments)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$application->id}";
        $applicant = $application->user;
        $greeting = "Dear " . ($applicant->first_name ?? 'Valued User') . ",";
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #D97706; font-weight: 500; margin-bottom: 20px; }
                    .badge { background-color: #FEF3C7; color: #D97706; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #F59E0B; }
                    .comments-box { background-color: #FFFBEB; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #F59E0B; }
                    .comments-box p { margin: 0; color: #78350F; }
                    .info-box { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>BFP Comments Added</h1>
                        <p>New comments from the Bureau of Fire Protection</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>The Bureau of Fire Protection (BFP) has added comments to your building permit application.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='badge'>📝 New Comments Added</span>
                        </div>
                        
                        <div class='comments-box'>
                            <strong>Comments from {$bfpUser->first_name} {$bfpUser->last_name}:</strong>
                            <p style='margin-top: 10px;'>" . nl2br(htmlspecialchars($comments)) . "</p>
                        </div>
                        
                        <div class='info-box'>
                            <p><strong>Application Number:</strong> {$application->application_number}</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application Details</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Please review the comments and take appropriate action.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
    /**
 * Notify applicant about ownership document remark (database notification only)
 * 
 * @param ApplicationDocument $application
 * @param User $staff
 * @param string $documentKey
 * @param string $documentName
 * @param string $remark
 * @return void
 */
public function notifyOwnershipDocumentRemarkDatabase(ApplicationDocument $application, User $staff, $documentKey, $documentName, $remark)
{
    // Ensure document name is not null
    $safeDocumentName = $documentName;
    if (empty($safeDocumentName) || $safeDocumentName === 'null') {
        $documentNames = [
            'tct_link' => 'TCT / Deed of Sale',
            'tax_declaration_link' => 'Tax Declaration',
            'current_tax_receipt_link' => 'Current Tax Receipt',
            'spa_link' => 'Special Power of Attorney (SPA)'
        ];
        $safeDocumentName = $documentNames[$documentKey] ?? 'Ownership Document';
    }
    
    try {
        if (class_exists('App\Notifications\OwnershipDocumentRemarkNotification')) {
            $application->user->notify(new \App\Notifications\OwnershipDocumentRemarkNotification(
                $application,
                $staff,
                $documentKey,
                $safeDocumentName,  // Important: Use safeDocumentName here
                $remark
            ));
            Log::info('Database notification sent with document name: ' . $safeDocumentName);
        }
    } catch (\Exception $e) {
        Log::error('Failed to send database notification: ' . $e->getMessage());
    }
}
/**
 * Notify applicant about ownership document remark with both database and email
 * 
 * @param ApplicationDocument $application
 * @param User $staff
 * @param string $documentKey
 * @param string $documentName
 * @param string $remark
 * @return void
 */
public function notifyOwnershipDocumentRemark(ApplicationDocument $application, User $staff, $documentKey, $documentName, $remark)
{
    Log::info('========== NOTIFY OWNERSHIP DOCUMENT REMARK ==========');
    Log::info('Received - document_key: ' . $documentKey . ', document_name: ' . $documentName);
    
    // Ensure we have a valid document name
    $safeDocumentName = $documentName;
    
    // If document name is null or 'null', use the mapping
    if (empty($safeDocumentName) || $safeDocumentName === 'null') {
        $documentNames = [
            'tct_link' => 'TCT / Deed of Sale',
            'tax_declaration_link' => 'Tax Declaration',
            'current_tax_receipt_link' => 'Current Tax Receipt',
            'spa_link' => 'Special Power of Attorney (SPA)'
        ];
        $safeDocumentName = $documentNames[$documentKey] ?? 'Ownership Document';
        Log::info('Using fallback document name: ' . $safeDocumentName);
    }
    
    $applicant = $application->user;
    
    if (!$applicant) {
        Log::error('No applicant found');
        return;
    }
    
    // Send database notification with the correct document name
    try {
        if (class_exists('App\Notifications\OwnershipDocumentRemarkNotification')) {
            $applicant->notify(new \App\Notifications\OwnershipDocumentRemarkNotification(
                $application,
                $staff,
                $documentKey,
                $safeDocumentName,  // Use the safe name here
                $remark
            ));
            Log::info('Database notification sent with document name: ' . $safeDocumentName);
        }
    } catch (\Exception $e) {
        Log::error('Failed to send database notification: ' . $e->getMessage());
    }
    
    // Send email with the correct document name
    try {
        $subject = "Clarification Needed: {$safeDocumentName} - Application #{$application->application_number}";
        $htmlContent = $this->getOwnershipDocumentRemarkEmailContent($application, $staff, $documentKey, $safeDocumentName, $remark);
        $this->gmailService->sendEmail($applicant->email, $subject, $htmlContent);
        Log::info('Email sent with document name: ' . $safeDocumentName);
    } catch (\Exception $e) {
        Log::error('Failed to send email: ' . $e->getMessage());
    }
}
/**
 * Get ownership document remark email content
 */
private function getOwnershipDocumentRemarkEmailContent(ApplicationDocument $application, User $staff, $documentKey, $documentName, $remark)
{
    $appUrl = env('APP_URL') . "/applicant/application-details/{$application->id}";
    $applicant = $application->user;
    $greeting = "Dear " . ($applicant->first_name ?? 'Valued User') . ",";
    $formattedNumber = $application->application_number;
    
    // Ensure document name is not null - provide fallback based on document key if needed
    $safeDocumentName = $documentName;
    if (empty($safeDocumentName) || $safeDocumentName === 'null') {
        // Fallback document names based on key
        $documentNames = [
            'tct_link' => 'TCT / Deed of Sale',
            'tax_declaration_link' => 'Tax Declaration',
            'current_tax_receipt_link' => 'Current Tax Receipt',
            'spa_link' => 'Special Power of Attorney (SPA)'
        ];
        $safeDocumentName = $documentNames[$documentKey] ?? 'Ownership Document';
    }
    
    // Format the application number for display
    if (strlen($formattedNumber) === 10) {
        $formattedNumber = substr($formattedNumber, 0, 2) . '-' . 
                          substr($formattedNumber, 2, 4) . '-' . 
                          substr($formattedNumber, 6, 4);
    }
    
    $staffName = $staff->first_name . ' ' . $staff->last_name;
    $staffPosition = $staff->profile ? ucfirst($staff->profile->position) : 'Staff';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
            .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
            .header { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 40px 30px; background-color: #ffffff; }
            .greeting { font-size: 18px; color: #D97706; font-weight: 500; margin-bottom: 20px; }
            .badge { background-color: #FEF3C7; color: #D97706; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #F59E0B; }
            .remarks-box { background-color: #FFFBEB; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #F59E0B; }
            .remarks-box p { margin: 0; color: #78350F; line-height: 1.5; }
            .document-box { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #D97706; }
            .info-box { background-color: #e6f7f5; padding: 15px; border-radius: 8px; margin: 20px 0; }
            .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
            .button:hover { opacity: 0.9; transform: translateY(-2px); }
            .next-steps { background-color: #e6f7e6; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10b981; }
            .next-steps h4 { margin: 0 0 10px 0; color: #065f46; }
            .next-steps ul { margin: 0; padding-left: 20px; }
            .next-steps li { margin: 5px 0; color: #065f46; font-size: 14px; }
            .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
            .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
            .brand-name { font-weight: 600; color: #155386; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Action Required: Document Clarification</h1>
                <p>Remarks added to your ownership document</p>
            </div>
            <div class='content'>
                <div class='greeting'>{$greeting}</div>
                
                <p>Our staff has reviewed your submitted documents and requires clarification on your <strong>" . htmlspecialchars($safeDocumentName) . "</strong>.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <span class='badge'>📝 Clarification Needed</span>
                </div>
                
                <div class='document-box'>
                    <p><strong>📄 Document Requiring Clarification:</strong> " . htmlspecialchars($safeDocumentName) . "</p>
                    <p><strong>🏢 Application Number:</strong> {$formattedNumber}</p>
                    <p><strong>👤 Reviewed By:</strong> {$staffName} ({$staffPosition})</p>
                    <p><strong>📅 Date:</strong> " . date('F d, Y g:i A') . "</p>
                </div>
                
                <div class='remarks-box'>
                    <strong>💬 Remarks / Clarification Request:</strong>
                    <p style='margin-top: 10px;'>" . nl2br(htmlspecialchars($remark)) . "</p>
                </div>
                
                <div class='next-steps'>
                    <h4>📌 What you need to do:</h4>
                    <ul>
                        <li>Review the remarks above carefully</li>
                        <li>Prepare the corrected or additional document</li>
                        <li>Contact the reviewer via our chat box</li>
                        <li>Paste there your new link</li>
                    </ul>
                </div>
                
                
                <div class='divider'></div>
                
                <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                    Please take action on this request to continue with your building permit application.
                </p>
                <p style='font-size: 12px; color: #9ca3af; text-align: center; margin-top: 20px;'>
                    This is an automated notification from Konstructo.
                </p>
            </div>
            <div class='footer'>
                <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}
/**
 * Notify treasurer that both assessments are ready and payment order is needed
 */
public function notifyTreasurerAssessmentsReady(ApplicationDocument $application, $buildingPermitFee, $cpdoFee, $totalAmount)
{
    Log::info('========== NOTIFY TREASURER ASSESSMENTS READY ==========');
    
    // Get treasurer users
    $treasurers = User::where('role', 'staff')
        ->whereHas('profile', function($query) {
            $query->where('position', 'treasurer');
        })
        ->get();
    
    $notifiedCount = 0;
    
    foreach ($treasurers as $treasurer) {
        try {
            // Send email via GmailService
            $this->gmailService->sendAssessmentsReadyForPaymentOrderEmail(
                $treasurer->email,
                $treasurer->first_name,
                $application->application_number,
                $application->user ? $application->user->first_name . ' ' . $application->user->last_name : 'N/A',
                $application->id,
                $buildingPermitFee,
                $cpdoFee,
                $totalAmount
            );
            $notifiedCount++;
            Log::info("✅ Assessments ready email sent to treasurer: {$treasurer->email}");
        } catch (\Exception $e) {
            Log::error("❌ Failed to send assessments ready email to treasurer {$treasurer->email}: " . $e->getMessage());
        }
    }
    
    Log::info("Assessments ready notifications sent to {$notifiedCount} treasurer(s)");
    Log::info('========== NOTIFY TREASURER ASSESSMENTS READY END ==========');
}

/**
 * Send payment order created notification to applicant
 */
public function notifyPaymentOrderCreated(ApplicationDocument $application, $paymentOrder, User $treasurer)
{
    Log::info('========== NOTIFY PAYMENT ORDER CREATED ==========');
    Log::info('Parameters:', [
        'application_id' => $application->id,
        'application_number' => $application->application_number,
        'order_number' => $paymentOrder->order_number,
        'treasurer_id' => $treasurer->id
    ]);
    
    $applicant = $application->user;
    
    if (!$applicant) {
        Log::error('❌ No applicant found for payment order notification!');
        return;
    }
    
    // Calculate total amount (Building Permit Fee + CPDO Fee)
    $buildingFee = 0;
    if ($application->assessmentFee) {
        $buildingFee = ($application->assessmentFee->building_fee ?? 0) + 
                       ($application->assessmentFee->line_grade ?? 0) + 
                       ($application->assessmentFee->sanitary_fee ?? 0) + 
                       ($application->assessmentFee->mechanical_fee ?? 0) + 
                       ($application->assessmentFee->electrical_fee ?? 0) + 
                       ($application->assessmentFee->penalties_fines ?? 0);
    }
    
    $cpdoFee = ($application->cpdo_zonal_location_fee ?? 0) + 
               ($application->cpdo_palc_fee ?? 0) + 
               ($application->cpdo_development_permit_fee ?? 0) + 
               ($application->cpdo_alteration_permit_fee ?? 0) + 
               ($application->cpdo_site_zoning_certificate_fee ?? 0);
    
    $totalAmount = $buildingFee + $cpdoFee;
    
    // Send email to applicant
    try {
        $this->gmailService->sendPaymentOrderCreatedToApplicantEmail(
            $applicant->email,
            $applicant->first_name,
            $application->application_number,
            $paymentOrder->order_number,
            $application->id,
            $totalAmount
        );
        Log::info('✅ Payment order created email sent to applicant: ' . $applicant->email);
    } catch (\Exception $e) {
        Log::error('❌ Failed to send payment order email to applicant: ' . $e->getMessage());
    }
    
    // Create in-app notification
    try {
        $applicant->notify(new \App\Notifications\PaymentOrderCreatedNotification(
            $application,
            $paymentOrder,
            $treasurer
        ));
        Log::info('✅ Payment order database notification sent to applicant');
    } catch (\Exception $e) {
        Log::error('❌ Failed to send payment order database notification: ' . $e->getMessage());
    }
    
    // Log activity
    try {
        if (Schema::hasTable('application_review_activities')) {
            $application->reviewActivities()->create([
                'reviewer_id' => $treasurer->id,
                'action' => 'payment_order_created',
                'new_status' => $application->status,
                'remarks' => "Payment Order Number created: {$paymentOrder->order_number}",
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
            Log::info('✅ Payment order activity logged');
        }
    } catch (\Exception $e) {
        Log::error('❌ Failed to log payment order activity: ' . $e->getMessage());
    }
    
    Log::info('========== NOTIFY PAYMENT ORDER CREATED END ==========');
}

/**
 * Notify treasurer when applicant uploads OR
 */
public function notifyTreasurerORUploaded(ApplicationDocument $application, PaymentProof $paymentProof, User $applicant)
{
    Log::info('========== NOTIFY TREASURER OR UPLOADED ==========');
    Log::info('Parameters:', [
        'application_id' => $application->id,
        'application_number' => $application->application_number,
        'payment_proof_id' => $paymentProof->id,
        'applicant_id' => $applicant->id
    ]);
    
    // Get treasurer users
    $treasurers = User::where('role', 'staff')
        ->whereHas('profile', function($query) {
            $query->where('position', 'treasurer');
        })
        ->get();
    
    $notifiedCount = 0;
    
    foreach ($treasurers as $treasurer) {
        try {
            // Send email
            $this->gmailService->sendORUploadedToTreasurerEmail(
                $treasurer->email,
                $treasurer->first_name,
                $application->application_number,
                $applicant->first_name . ' ' . $applicant->last_name,
                $application->id,
                $paymentProof->or_link
            );
            $notifiedCount++;
            Log::info("✅ OR uploaded email sent to treasurer: {$treasurer->email}");
        } catch (\Exception $e) {
            Log::error("❌ Failed to send OR uploaded email to treasurer {$treasurer->email}: " . $e->getMessage());
        }
        
        // Send in-app notification to treasurer
        try {
            if (class_exists('\App\Notifications\ORUploadedToTreasurerNotification')) {
                $treasurer->notify(new \App\Notifications\ORUploadedToTreasurerNotification(
                    $application,
                    $paymentProof,
                    $applicant
                ));
                Log::info("✅ OR uploaded database notification sent to treasurer: {$treasurer->email}");
            }
        } catch (\Exception $e) {
            Log::error("❌ Failed to send OR uploaded database notification to treasurer: " . $e->getMessage());
        }
    }
    
    Log::info("OR uploaded notifications sent to {$notifiedCount} treasurer(s)");
    Log::info('========== NOTIFY TREASURER OR UPLOADED END ==========');
}
}