<?php

namespace App\Services;

use App\Models\User;
use App\Models\ApplicationDocument;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\AdminNoteNotification;
use App\Notifications\NewApplicationNotification;
use App\Notifications\HardCopyReceivedNotification;
use App\Notifications\StaffStatusChangeNotification;
use App\Notifications\FSECUploadedNotification;
use App\Notifications\BFPCommentsAddedNotification;
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
}