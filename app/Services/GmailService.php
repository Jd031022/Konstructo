<?php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GmailService
{
    protected $client;
    protected $service;
    protected $isConfigured = false;
    protected $useMailFallback = true;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize the Gmail client
     */
    private function initialize()
    {
        try {
            $refreshToken = env('GOOGLE_REFRESH_TOKEN');
            $clientId = env('GOOGLE_CLIENT_ID');
            $clientSecret = env('GOOGLE_CLIENT_SECRET');
            
            // Log what we have (without exposing full tokens)
            Log::info('GmailService initialization', [
                'has_refresh_token' => !empty($refreshToken),
                'has_client_id' => !empty($clientId),
                'has_client_secret' => !empty($clientSecret),
                'refresh_token_length' => strlen($refreshToken ?? 0),
                'refresh_token_prefix' => substr($refreshToken ?? '', 0, 15) . '...'
            ]);
            
            // Check if all required credentials are present
            if (empty($refreshToken) || empty($clientId) || empty($clientSecret)) {
                Log::warning('GmailService: Missing Google API credentials. Using mail fallback.');
                $this->isConfigured = false;
                return;
            }
            
            $this->client = new Google_Client();
            $this->client->setClientId($clientId);
            $this->client->setClientSecret($clientSecret);
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt('force');
            
            // IMPORTANT: Set the refresh token correctly
            $this->client->refreshToken($refreshToken);
            
            // Fetch a new access token
            $newToken = $this->client->fetchAccessTokenWithRefreshToken();
            
            // Check for errors
            if (isset($newToken['error'])) {
                Log::error('Token refresh failed', [
                    'error' => $newToken['error'],
                    'error_description' => $newToken['error_description'] ?? 'No description'
                ]);
                $this->isConfigured = false;
                return;
            }
            
            // Verify we got an access token
            if (!isset($newToken['access_token'])) {
                Log::error('No access token in response', ['token' => $newToken]);
                $this->isConfigured = false;
                return;
            }
            
            $this->service = new Google_Service_Gmail($this->client);
            $this->isConfigured = true;
            
            Log::info('GmailService initialized successfully');
            
        } catch (\Exception $e) {
            Log::error('GmailService constructor error: ' . $e->getMessage());
            $this->isConfigured = false;
        }
    }

    /**
     * Check if the service is properly configured
     */
    public function isConfigured()
    {
        return $this->isConfigured;
    }

    /**
     * Send email using either Gmail API or Laravel mail fallback
     */
    private function sendEmail($to, $subject, $htmlContent, $fromEmail = null, $fromName = 'Konstructo')
    {
        $fromEmail = $fromEmail ?? env('MAIL_FROM_ADDRESS', 'noreply@konstructo.com');
        
        // Log the attempt
        Log::info('📧 Attempting to send email', [
            'to' => $to,
            'subject' => $subject,
            'method' => $this->isConfigured ? 'Gmail API' : 'Mail Fallback',
            'configured' => $this->isConfigured
        ]);
        
        // If using mail fallback (for testing/development)
        if (!$this->isConfigured || $this->useMailFallback) {
            try {
                // For local development, just log the email
                if (app()->environment('local')) {
                    Log::info('📧 LOCAL DEVELOPMENT - Email would be sent:', [
                        'to' => $to,
                        'subject' => $subject,
                        'from' => $fromEmail,
                        'content_preview' => substr(strip_tags($htmlContent), 0, 200) . '...'
                    ]);
                    
                    // Also try to send via Laravel mail if configured
                    try {
                        Mail::html($htmlContent, function ($message) use ($to, $subject, $fromEmail, $fromName) {
                            $message->to($to)
                                    ->subject($subject)
                                    ->from($fromEmail, $fromName);
                        });
                        Log::info('✓✓✓ Email sent via Laravel mail');
                    } catch (\Exception $mailEx) {
                        Log::warning('Laravel mail not configured: ' . $mailEx->getMessage());
                    }
                    
                    return true;
                }
                
                // For production with fallback
                Mail::html($htmlContent, function ($message) use ($to, $subject, $fromEmail, $fromName) {
                    $message->to($to)
                            ->subject($subject)
                            ->from($fromEmail, $fromName);
                });
                
                Log::info('✓✓✓ Email sent via Laravel mail');
                return true;
                
            } catch (\Exception $e) {
                Log::error('Failed to send via mail fallback: ' . $e->getMessage());
                return false;
            }
        }
        
        // If using Gmail API
        try {
            // Ensure token is fresh
            if ($this->client->isAccessTokenExpired()) {
                Log::info('Access token expired, refreshing...');
                $newToken = $this->client->fetchAccessTokenWithRefreshToken();
                if (isset($newToken['error'])) {
                    Log::error('Failed to refresh token', $newToken);
                    return false;
                }
            }
            
            // Build email with proper headers
            $email = "";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "From: {$fromName} <{$fromEmail}>\r\n";
            $email .= "To: {$to}\r\n";
            $email .= "Subject: {$subject}\r\n";
            $email .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $email .= "\r\n";
            $email .= quoted_printable_encode($htmlContent);

            $rawMessage = rtrim(strtr(base64_encode($email), '+/', '-_'), '=');
            
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($rawMessage);
            
            $this->service->users_messages->send('me', $message);
            
            Log::info('✓✓✓ Email sent via Gmail API');
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send via Gmail API: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail($to, $code, $firstName = null)
    {
        $subject = 'Verify Your Email Address - Konstructo';
        $formattedName = $firstName ? ucfirst(strtolower(trim($firstName))) : null;
        
        $htmlContent = $this->getVerificationEmailContent($code, $formattedName);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send account approval email to user
     */
    public function sendAccountApprovalEmail($to, $name)
    {
        $subject = 'Account Approved - Konstructo';
        $htmlContent = $this->getAccountApprovalEmailContent($name);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send account rejection email to user
     */
    public function sendAccountRejectionEmail($to, $name, $reason = null)
    {
        $subject = 'Account Application Status - Konstructo';
        $htmlContent = $this->getAccountRejectionEmailContent($name, $reason);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send admin notification about new user
     */
    public function sendAdminNotification($to, $subject, $message, $userName = null, $userEmail = null)
    {
        $htmlContent = $this->getAdminNotificationEmailContent($message, $userName, $userEmail);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send status update email
     */
    public function sendStatusEmail($to, $status, $applicationNumber, $applicantName, $applicationId)
    {
        $subject = $this->getEmailSubject($status);
        $htmlContent = $this->getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send missing documents email
     */
    public function sendMissingDocumentsEmail($to, $applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        $subject = 'Action Required: Missing Documents for Your Building Permit Application';
        $htmlContent = $this->getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * ========== BASIC REQUIREMENTS EMAIL METHODS ==========
     */

    /**
     * Send basic requirements approval email
     */
    public function sendBasicRequirementsApprovedEmail($to, $firstName, $requirementId, $approverName = null)
    {
        $subject = 'Basic Requirements Approved - Konstructo';
        $htmlContent = $this->getBasicRequirementsApprovedEmailContent($firstName, $requirementId, $approverName);
        
        Log::info('📧 Sending basic requirements approval email', [
            'to' => $to,
            'requirement_id' => $requirementId
        ]);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send basic requirements rejection email
     */
    public function sendBasicRequirementsRejectedEmail($to, $firstName, $reason, $requirementId, $rejectorName = null)
    {
        $subject = 'Basic Requirements Update - Konstructo';
        $htmlContent = $this->getBasicRequirementsRejectedEmailContent($firstName, $reason, $requirementId, $rejectorName);
        
        Log::info('📧 Sending basic requirements rejection email', [
            'to' => $to,
            'requirement_id' => $requirementId,
            'reason_length' => strlen($reason)
        ]);
        
        return $this->sendEmail($to, $subject, $htmlContent);
    }

    /**
     * Send notification to staff about new basic requirements submission
     */
    public function sendNewBasicRequirementsEmail($staffEmail, $staffName, $applicant, $requirement)
    {
        $subject = 'New Basic Requirements Submitted - Konstructo';
        $htmlContent = $this->getNewBasicRequirementsEmailContent($staffName, $applicant, $requirement);
        
        Log::info('📧 Sending new basic requirements notification to staff', [
            'to' => $staffEmail,
            'applicant_id' => $applicant->id,
            'requirement_id' => $requirement->id
        ]);
        
        return $this->sendEmail($staffEmail, $subject, $htmlContent);
    }

    /**
     * Get basic requirements approved email content
     */
    private function getBasicRequirementsApprovedEmailContent($firstName, $requirementId, $approverName = null)
    {
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        $approverText = $approverName ? " by " . $approverName : "";
        
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
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
                    .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10B981; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .next-steps { margin: 20px 0; padding-left: 20px; }
                    .next-steps li { margin: 10px 0; color: #4B5563; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Basic Requirements Approved! ✅</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Great news! Your basic requirements have been <strong>approved</strong>{$approverText}.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='success-badge'>✓ Requirements Approved</span>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #059669; font-size: 16px;'>What's Next?</h3>
                            <ul class='next-steps'>
                                <li>You can now proceed to Step 1 of your building permit application</li>
                                <li>Download the required forms from the application portal</li>
                                <li>Fill out the forms completely and accurately</li>
                                <li>Upload your completed documents in Step 2</li>
                                <li>Submit your application for review</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . route('applicant.application.step1') . "' class='button'>Start Your Application</a>
                        </div>
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            If you have any questions or need assistance, please don't hesitate to contact our support team.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for choosing Konstructo for your permitting needs.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get basic requirements rejected email content
     */
    private function getBasicRequirementsRejectedEmailContent($firstName, $reason, $requirementId, $rejectorName = null)
    {
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        $rejectorText = $rejectorName ? " by " . $rejectorName : "";
        
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
                    .rejection-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                    .reason-box { background-color: #FEE2E2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                    .reason-box p { margin: 8px 0 0 0; color: #991B1B; line-height: 1.5; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .next-steps { margin: 20px 0; padding-left: 20px; }
                    .next-steps li { margin: 10px 0; color: #4B5563; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Basic Requirements Update</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>We regret to inform you that your basic requirements have been <strong>rejected</strong>{$rejectorText}.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='rejection-badge'>✗ Requirements Rejected</span>
                        </div>
                        
                        <div class='reason-box'>
                            <strong style='color: #DC2626;'>Reason for rejection:</strong>
                            <p>" . nl2br(htmlspecialchars($reason)) . "</p>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #DC2626; font-size: 16px;'>What you can do:</h3>
                            <ul class='next-steps'>
                                <li>Review the reason for rejection provided above</li>
                                <li>Prepare corrected or missing documents</li>
                                <li>Ensure all documents are clear and complete</li>
                                <li>Resubmit your basic requirements with the necessary corrections</li>
                                <li>Contact our support team if you need clarification</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . route('applicant.basic-requirements.index') . "' class='button'>Resubmit Requirements</a>
                        </div>
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            If you believe this is a mistake or need assistance, please contact our support team.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for your patience and cooperation.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get new basic requirements notification email content for staff
     */
    private function getNewBasicRequirementsEmailContent($staffName, $applicant, $requirement)
    {
        $greeting = $staffName ? "Dear " . $staffName . "," : "Dear Staff,";
        $requirementsUrl = route('staff.basic-requirements.show', $requirement->id);
        
        // Build document list
        $documentList = "<ul style='margin: 15px 0; padding-left: 20px;'>";
        $documentList .= "<li><strong>Transfer Certificate of Title (TCT):</strong> <a href='{$requirement->tct_link}' style='color: #155386;'>View Document</a></li>";
        $documentList .= "<li><strong>Tax Declaration:</strong> <a href='{$requirement->tax_declaration_link}' style='color: #155386;'>View Document</a></li>";
        $documentList .= "<li><strong>Current Tax Receipt:</strong> <a href='{$requirement->current_tax_receipt_link}' style='color: #155386;'>View Document</a></li>";
        
        if (!$requirement->is_owner) {
            if ($requirement->deed_of_sale_link) {
                $documentList .= "<li><strong>Notarized Deed of Sale:</strong> <a href='{$requirement->deed_of_sale_link}' style='color: #155386;'>View Document</a></li>";
            }
            if ($requirement->spa_link) {
                $documentList .= "<li><strong>Special Power of Attorney (SPA):</strong> <a href='{$requirement->spa_link}' style='color: #155386;'>View Document</a></li>";
            }
        }
        $documentList .= "</ul>";
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .badge { background-color: #FEF3C7; color: #F59E0B; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #F59E0B; }
                    .applicant-info { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #155386; }
                    .document-list { background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 20px 0; }
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
                        <h1>New Basic Requirements Submission</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>A new basic requirements submission requires your review.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='badge'>⏳ Pending Review</span>
                        </div>
                        
                        <div class='applicant-info'>
                            <h3 style='margin-top: 0; color: #155386; font-size: 16px;'>Applicant Information:</h3>
                            <p><strong>Name:</strong> {$applicant->first_name} {$applicant->last_name}</p>
                            <p><strong>Email:</strong> {$applicant->email}</p>
                            <p><strong>Phone:</strong> {$applicant->phone_number}</p>
                            <p><strong>Property Owner:</strong> " . ($requirement->is_owner ? 'Yes' : 'No (Authorized Representative)') . "</p>
                            <p><strong>Submitted:</strong> " . ($requirement->submitted_at ? $requirement->submitted_at->format('F d, Y h:i A') : 'N/A') . "</p>
                        </div>
                        
                        <div class='document-list'>
                            <h3 style='margin-top: 0; color: #155386; font-size: 16px;'>Submitted Documents:</h3>
                            {$documentList}
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$requirementsUrl}' class='button'>Review Requirements</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Please review and take appropriate action on this submission.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get account approval email content
     */
    private function getAccountApprovalEmailContent($name)
    {
        $greeting = $name ? "Dear " . $name . "," : "Dear Valued User,";
        
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
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
                    .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10B981; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .steps-list { margin: 20px 0; padding-left: 20px; }
                    .steps-list li { margin: 10px 0; color: #4B5563; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to Konstructo!</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Great news! Your account has been <strong>approved</strong> by the administrator.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='success-badge'>✓ Account Approved</span>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #059669; font-size: 16px;'>What's Next?</h3>
                            <ul class='steps-list'>
                                <li>Log in to your account using your registered email/username and password</li>
                                <li>Complete your profile information</li>
                                <li>Start submitting building permit applications</li>
                                <li>Track the status of your applications in real-time</li>
                            </ul>
                        </div>
                    
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            If you have any questions or need assistance, please don't hesitate to contact our support team.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for choosing Konstructo for your permitting needs.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get account rejection email content
     */
    private function getAccountRejectionEmailContent($name, $reason = null)
    {
        $greeting = $name ? "Dear " . $name . "," : "Dear Valued User,";
        $reasonHtml = $reason ? "
            <div class='reason-box'>
                <strong>Reason for rejection:</strong>
                <p style='margin: 8px 0 0 0;'>" . htmlspecialchars($reason) . "</p>
            </div>
        " : "";
        
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
                    .rejection-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                    .reason-box { background-color: #FEE2E2; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .support-list { margin: 20px 0; padding-left: 20px; }
                    .support-list li { margin: 8px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Account Application Status</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>We regret to inform you that your account application has been <strong>rejected</strong>.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='rejection-badge'>✗ Account Rejected</span>
                        </div>
                        
                        {$reasonHtml}
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #DC2626; font-size: 16px;'>What can you do?</h3>
                            <ul class='support-list'>
                                <li>Review the reason for rejection provided above</li>
                                <li>Ensure all required information is accurate and complete</li>
                                <li>Try registering again with a new account</li>
                                <li>Contact our support team for assistance</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . url('/register') . "' class='button'>Create New Account</a>
                        </div>
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            If you believe this is a mistake or if you have any questions, please contact our support team.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for your interest in Konstructo.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get admin notification email content
     */
    private function getAdminNotificationEmailContent($message, $userName = null, $userEmail = null)
    {
        $userInfo = "";
        if ($userName || $userEmail) {
            $userInfo = "
                <div class='user-info'>
                    <h3>User Details:</h3>
                    " . ($userName ? "<p><strong>Name:</strong> " . htmlspecialchars($userName) . "</p>" : "") . "
                    " . ($userEmail ? "<p><strong>Email:</strong> " . htmlspecialchars($userEmail) . "</p>" : "") . "
                </div>
            ";
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
                    .header { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .user-info { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #155386; }
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
                        <h1>Admin Notification</h1>
                    </div>
                    <div class='content'>
                        <p>" . nl2br(htmlspecialchars($message)) . "</p>
                        
                        {$userInfo}
                        
                        <div style='text-align: center;'>
                            <a href='" . url('/admin/settings?tab=roles') . "' class='button'>Go to Admin Panel</a>
                        </div>
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            Please log in to review and take action.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
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
     * Get email subject based on status
     */
    private function getEmailSubject($status)
    {
        return match($status) {
            'approved' => 'Building Permit Application Approved - Konstructo',
            'for-release' => 'Building Permit Ready for Release - Konstructo',
            'rejected' => 'Building Permit Application Update - Konstructo',
            'under-review' => 'Your Application is Under Review - Konstructo',
            'verified' => 'Documents Verified - Konstructo',
            'pending' => 'Application Received - Konstructo',
            'document-verification' => 'Document Verification in Progress - Konstructo',
            default => 'Building Permit Application Status Update - Konstructo'
        };
    }

    /**
     * Get verification email content
     */
    private function getVerificationEmailContent($code, $firstName = null)
    {
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .code-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; text-align: center; font-size: 42px; letter-spacing: 8px; font-weight: 600; border-radius: 8px; margin: 30px 0; font-family: 'Courier New', monospace; color: #155386; border: 1px solid #dee2e6; }
                    .expiry-note { background-color: #fff8e7; padding: 15px; border-radius: 6px; border-left: 4px solid #40798C; margin: 25px 0; font-size: 14px; }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Konstructo</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        <p>Thank you for registering with Konstructo. To complete your account setup and ensure the security of your account, please verify your email address using the verification code below:</p>
                        
                        <div class='code-box'>{$code}</div>
                        
                        <div class='expiry-note'>
                            <strong>Note:</strong> This verification code will expire in 10 minutes for security purposes.
                        </div>
                        
                        <p>If you did not initiate this registration request, please disregard this email. No further action is required.</p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d;'>
                            For security reasons, never share this code with anyone. Our team will never ask for your verification code.
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get status email content
     */
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        $statusDisplay = ucfirst(str_replace('-', ' ', $status));
        
        // Define colors based on status
        $statusColors = [
            'approved' => ['bg' => '#10B981', 'light' => '#D1FAE5'],
            'rejected' => ['bg' => '#EF4444', 'light' => '#FEE2E2'],
            'pending' => ['bg' => '#F59E0B', 'light' => '#FEF3C7'],
            'under-review' => ['bg' => '#8B5CF6', 'light' => '#EDE9FE'],
            'for-release' => ['bg' => '#3B82F6', 'light' => '#DBEAFE'],
            'verified' => ['bg' => '#10B981', 'light' => '#D1FAE5'],
            'document-verification' => ['bg' => '#8B5CF6', 'light' => '#EDE9FE']
        ];
        
        $color = $statusColors[$status] ?? ['bg' => '#6B7280', 'light' => '#F3F4F6'];
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .status-badge { background-color: {$color['light']}; color: {$color['bg']}; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid {$color['bg']}20; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #155386; }
                    .application-number { font-family: 'Courier New', monospace; font-weight: 600; color: #155386; background-color: #f0f7fa; padding: 2px 8px; border-radius: 4px; }
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
                        <h1>Application Status Update</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>Your building permit application <span class='application-number'>#{$applicationNumber}</span> has received a status update.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='status-badge'>Current Status: {$statusDisplay}</span>
                        </div>
                        
                        <div class='info-section'>
                            <p style='margin: 0;'>Please log in to your Konstructo dashboard to view the complete details regarding this update and any next steps required.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d;'>Thank you for using Konstructo for your permitting needs.</p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get missing documents email content with professional design
     */
    private function getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        
        // Build document list HTML
        $documentList = '<div style="margin: 20px 0;">';
        foreach ($missingDocuments as $index => $doc) {
            $documentList .= '
                <div style="display: flex; align-items: center; padding: 10px; ' . ($index % 2 === 0 ? 'background-color: #f8f9fa;' : '') . ' border-radius: 4px; margin-bottom: 5px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="margin-right: 12px; flex-shrink: 0;">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" fill="#EF4444" fill-opacity="0.2" stroke="#EF4444" stroke-width="2"/>
                    </svg>
                    <span style="font-size: 14px; color: #1F2937;">' . htmlspecialchars($doc) . '</span>
                </div>';
        }
        $documentList .= '</div>';
        
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
                    .warning-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
                    .document-list { background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin: 20px 0; }
                    .application-number { font-family: 'Courier New', monospace; font-weight: 600; color: #DC2626; background-color: #FEE2E2; padding: 2px 8px; border-radius: 4px; }
                    .remarks-box { background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; border-radius: 6px; margin: 20px 0; font-size: 14px; color: #92400E; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .deadline-note { background-color: #FEF2F2; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #FEE2E2; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Action Required: Missing Documents</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>Your building permit application <span class='application-number'>#{$applicationNumber}</span> requires additional documents to proceed with the review process.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='warning-badge'>⚠️ Missing Documents Required</span>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #DC2626; font-size: 16px;'>The following documents are missing:</h3>
                            {$documentList}
                            
                            " . ($remarks ? "
                            <div class='remarks-box'>
                                <strong>Additional Remarks:</strong>
                                <p style='margin: 8px 0 0 0;'>" . htmlspecialchars($remarks) . "</p>
                            </div>
                            " : "") . "
                            
                            <div class='deadline-note'>
                                <strong>⏰ Please submit these documents within 7 days</strong>
                                <p style='margin: 8px 0 0 0; font-size: 13px; color: #6B7280;'>Failure to provide the required documents may result in delays or rejection of your application.</p>
                            </div>
                        </div>
                        
                        <p><strong>Next Steps:</strong></p>
                        <ol style='margin-bottom: 25px; color: #4B5563;'>
                            <li>Upload the missing documents to your Google Drive folder</li>
                            <li>Ensure all documents are clear and readable</li>
                            <li>Submit hard copies to the OBO office if required</li>
                            <li>Track your application status through your dashboard</li>
                        </ol>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                        
                        <p style='margin-top: 25px; font-size: 14px; color: #6B7280; text-align: center;'>
                            If you have already uploaded these documents, please disregard this message.
                        </p>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            For questions or assistance, please contact the Office of the Building Official (OBO).
                        </p>
                    </div>
                    <div class='footer'>
                        <p class='brand-name'>Konstructo — Smart Infrastructure Oversight</p>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                        <p style='margin-top: 15px; font-size: 12px;'>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Test connection to Gmail API
     */
    public function testConnection()
    {
        if (!$this->isConfigured) {
            return "Gmail service is not configured. Using mail fallback.";
        }
        
        try {
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            $profile = $this->service->users->getProfile('me');
            return "Connected as: " . $profile->getEmailAddress();
        } catch (\Exception $e) {
            return "Connection failed: " . $e->getMessage();
        }
    }
}