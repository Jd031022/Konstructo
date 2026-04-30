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
            
            Log::info('GmailService initialization', [
                'has_refresh_token' => !empty($refreshToken),
                'has_client_id' => !empty($clientId),
                'has_client_secret' => !empty($clientSecret),
                'refresh_token_length' => strlen($refreshToken ?? 0),
                'refresh_token_prefix' => substr($refreshToken ?? '', 0, 15) . '...'
            ]);
            
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
            $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI', 'http://localhost'));
            $this->client->setScopes([Google_Service_Gmail::MAIL_GOOGLE_COM]);
            
            $this->client->refreshToken($refreshToken);
            
            $newToken = $this->client->fetchAccessTokenWithRefreshToken();
            
            if (isset($newToken['error'])) {
                Log::error('Token refresh failed', [
                    'error' => $newToken['error'],
                    'error_description' => $newToken['error_description'] ?? 'No description'
                ]);
                $this->isConfigured = false;
                return;
            }
            
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
     * Public method to send email (wrapper for the private method)
     */
    public function sendEmail($to, $subject, $htmlContent, $fromEmail = null, $fromName = 'Konstructo')
    {
        return $this->sendEmailInternal($to, $subject, $htmlContent, $fromEmail, $fromName);
    }

    /**
     * Send email using either Gmail API or Laravel mail fallback
     */
    private function sendEmailInternal($to, $subject, $htmlContent, $fromEmail = null, $fromName = 'Konstructo')
    {
        $fromEmail = $fromEmail ?? env('MAIL_FROM_ADDRESS', 'noreply@konstructo.com');
        
        Log::info('📧 Attempting to send email', [
            'to' => $to,
            'subject' => $subject,
            'method' => $this->isConfigured ? 'Gmail API' : 'Mail Fallback',
            'configured' => $this->isConfigured
        ]);
        
        if (!$this->isConfigured || $this->useMailFallback) {
            try {
                if (app()->environment('local')) {
                    Log::info('📧 LOCAL DEVELOPMENT - Email would be sent:', [
                        'to' => $to,
                        'subject' => $subject,
                        'from' => $fromEmail,
                        'content_preview' => substr(strip_tags($htmlContent), 0, 200) . '...'
                    ]);
                    
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
        
        try {
            if ($this->client->isAccessTokenExpired()) {
                Log::info('Access token expired, refreshing...');
                $newToken = $this->client->fetchAccessTokenWithRefreshToken();
                if (isset($newToken['error'])) {
                    Log::error('Failed to refresh token', $newToken);
                    return false;
                }
            }
            
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
     * Send OR Verification email to applicant
     */
    public function sendORVerificationEmail($to, $applicationNumber, $applicantName, $applicationId, $cpdoName)
    {
        $subject = 'Official Receipt Verified - Konstructo';
        $htmlContent = $this->getORVerificationEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName);
        
        Log::info('📧 Sending OR verification email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send OR Rejection email to applicant
     */
    public function sendORRejectionEmail($to, $applicationNumber, $applicantName, $applicationId, $cpdoName, $reason)
    {
        $subject = 'Official Receipt Needs Attention - Konstructo';
        $htmlContent = $this->getORRejectionEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName, $reason);
        
        Log::info('📧 Sending OR rejection email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName,
            'reason' => $reason
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send Zoning Certificate uploaded email to applicant
     */
    public function sendZoningCertificateUploadedEmail($to, $applicationNumber, $applicantName, $certificateLink, $applicationId, $cpdoName)
    {
        $subject = 'Zoning Certificate Uploaded for Your Application - Konstructo';
        $htmlContent = $this->getCertificateUploadedEmailContent($applicationNumber, $applicantName, $certificateLink, $applicationId, $cpdoName, 'Zoning Certificate');
        
        Log::info('📧 Sending Zoning Certificate upload email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send Locational Clearance uploaded email to applicant
     */
    public function sendLocationalClearanceUploadedEmail($to, $applicationNumber, $applicantName, $certificateLink, $applicationId, $cpdoName)
    {
        $subject = 'Locational Clearance Uploaded for Your Application - Konstructo';
        $htmlContent = $this->getCertificateUploadedEmailContent($applicationNumber, $applicantName, $certificateLink, $applicationId, $cpdoName, 'Locational Clearance');
        
        Log::info('📧 Sending Locational Clearance upload email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send CPDO Assessment email with printable receipt
     */
    public function sendCPDOAssessmentEmail($to, $applicantName, $applicationNumber, $assessmentData, $applicationId)
    {
        $subject = 'CPDO Fee Assessment for Building Permit Application - Konstructo';
        $htmlContent = $this->getCPDOAssessmentEmailContent($applicantName, $applicationNumber, $assessmentData, $applicationId);
        
        Log::info('📧 Sending CPDO assessment email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'total_amount' => $assessmentData['total_cpdo_amount'] ?? 0
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send CPDO approval email to applicant
     */
    public function sendCPDOApprovalEmail($to, $applicationNumber, $applicantName, $applicationId, $cpdoName)
    {
        $subject = 'CPDO Approval Received - Konstructo';
        $htmlContent = $this->getCPDOApprovalEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName);
        
        Log::info('📧 Sending CPDO approval email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send CPDO rejection email to applicant
     */
    public function sendCPDORejectionEmail($to, $applicationNumber, $applicantName, $applicationId, $cpdoName, $remarks = null)
    {
        $subject = 'CPDO Decision on Your Application - Konstructo';
        $htmlContent = $this->getCPDORejectionEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName, $remarks);
        
        Log::info('📧 Sending CPDO rejection email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId,
            'cpdo_name' => $cpdoName,
            'has_remarks' => !empty($remarks)
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send user credentials email (username and password) to newly created users
     */
    public function sendCredentialsEmail($to, $name, $username, $password, $isReset = false)
    {
        $subject = $isReset 
            ? 'Your Password Has Been Reset - Konstructo' 
            : 'Welcome to Konstructo - Your Account Credentials';
        
        $htmlContent = $this->getCredentialsEmailContent($name, $username, $password, $isReset);
        
        Log::info('📧 Sending credentials email', [
            'to' => $to,
            'is_reset' => $isReset,
            'username' => $username
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send application submitted email with application number
     */
    public function sendApplicationSubmittedEmail($to, $applicationNumber, $applicantName, $applicationId)
    {
        $subject = 'Application Submitted Successfully - Konstructo';
        $htmlContent = $this->getApplicationSubmittedEmailContent($applicationNumber, $applicantName, $applicationId);
        
        Log::info('📧 Sending application submission email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'application_id' => $applicationId
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail($to, $code, $firstName = null)
    {
        $subject = 'Verify Your Email Address - Konstructo';
        $formattedName = $firstName ? ucfirst(strtolower(trim($firstName))) : null;
        
        $htmlContent = $this->getVerificationEmailContent($code, $formattedName);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send account approval email to user
     */
    public function sendAccountApprovalEmail($to, $name)
    {
        $subject = 'Account Approved - Konstructo';
        $htmlContent = $this->getAccountApprovalEmailContent($name);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send account rejection email to user
     */
    public function sendAccountRejectionEmail($to, $name, $reason = null)
    {
        $subject = 'Account Application Status - Konstructo';
        $htmlContent = $this->getAccountRejectionEmailContent($name, $reason);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send admin notification about new user
     */
    public function sendAdminNotification($to, $subject, $message, $userName = null, $userEmail = null)
    {
        $htmlContent = $this->getAdminNotificationEmailContent($message, $userName, $userEmail);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send status update email
     */
    public function sendStatusEmail($to, $status, $applicationNumber, $applicantName, $applicationId, $additionalData = [])
    {
        $subject = $this->getEmailSubject($status);
        $htmlContent = $this->getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId, $additionalData);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send assessment completed email with fee breakdown
     */
    public function sendAssessmentEmail($to, $applicationNumber, $applicantName, $assessmentData, $applicationId)
    {
        $subject = 'Building Permit Assessment Completed - Konstructo';
        $htmlContent = $this->getAssessmentEmailContent($applicationNumber, $applicantName, $assessmentData, $applicationId);
        
        Log::info('📧 Sending assessment completion email', [
            'to' => $to,
            'application_number' => $applicationNumber,
            'total_amount' => $assessmentData['total_amount'] ?? 0,
            'has_additional_fees' => isset($assessmentData['additional_fees']) && count($assessmentData['additional_fees'] ?? []) > 0
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send missing documents email
     */
    public function sendMissingDocumentsEmail($to, $applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        $subject = 'Action Required: Missing Documents for Your Building Permit Application';
        $htmlContent = $this->getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Get OR Verification email content
     */
    private function getORVerificationEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Official Receipt Verified</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 40px 30px; background-color: #ffffff; }
                .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
                .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10B981; }
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
                    <h1>Official Receipt Verified</h1>
                    <p>Your payment has been confirmed</p>
                </div>
                <div class='content'>
                    <div class='greeting'>{$greeting}</div>
                    
                    <p>Your Official Receipt (OR) for application <strong>{$formattedNumber}</strong> has been <strong>verified</strong> by CPDO.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <span class='success-badge'>✓ Payment Verified</span>
                    </div>
                    
                    <div class='info-box'>
                        <p><strong>Application Number:</strong> {$formattedNumber}</p>
                        <p><strong>Verified by:</strong> {$cpdoName}</p>
                    </div>
                    
                    <div class='next-steps'>
                        <h4>📋 What Happens Next?</h4>
                        <ul>
                            <li>CPDO will now process and upload the required certificates</li>
                            <li>You will receive notifications when certificates are available</li>
                            <li>Once certificates are uploaded, the Engineering Department will review your application</li>
                            <li>You will be notified of the final decision</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$appUrl}' class='button'>View Application Status</a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                        Thank you for your payment. We will process your application as quickly as possible.
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
     * Get OR Rejection email content
     */
    private function getORRejectionEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName, $reason)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Official Receipt Needs Attention</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                .header { background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; background-color: #ffffff; }
                .greeting { font-size: 18px; color: #DC2626; font-weight: 500; margin-bottom: 20px; }
                .rejection-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                .reason-box { background-color: #FEE2E2; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                .reason-box p { margin: 8px 0 0 0; color: #991B1B; }
                .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                .button:hover { opacity: 0.9; transform: translateY(-2px); }
                .next-steps { background-color: #FEE2E2; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
                .next-steps h4 { margin: 0 0 10px 0; color: #991B1B; }
                .next-steps ul { margin: 0; padding-left: 20px; }
                .next-steps li { margin: 5px 0; color: #991B1B; font-size: 14px; }
                .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                .brand-name { font-weight: 600; color: #155386; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Official Receipt Needs Attention</h1>
                    <p>Please review and resubmit</p>
                </div>
                <div class='content'>
                    <div class='greeting'>{$greeting}</div>
                    
                    <p>Your Official Receipt (OR) for application <strong>{$formattedNumber}</strong> has been <strong>rejected</strong> by CPDO.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <span class='rejection-badge'>✗ Payment Not Verified</span>
                    </div>
                    
                    <div class='info-box'>
                        <p><strong>Application Number:</strong> {$formattedNumber}</p>
                        <p><strong>Reviewed by:</strong> {$cpdoName}</p>
                    </div>
                    
                    <div class='reason-box'>
                        <strong>Reason for rejection:</strong>
                        <p>" . nl2br(htmlspecialchars($reason)) . "</p>
                    </div>
                    
                    <div class='next-steps'>
                        <h4>📋 What You Need to Do:</h4>
                        <ul>
                            <li>Review the reason for rejection above</li>
                            <li>Prepare a valid Official Receipt with the correct amount</li>
                            <li>Upload the new OR in your application dashboard</li>
                            <li>Once uploaded, CPDO will review it again</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$appUrl}' class='button'>Upload New Official Receipt</a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                        If you have questions about this decision, please contact the CPDO office directly.
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
     * Get certificate uploaded email content
     */
    private function getCertificateUploadedEmailContent($applicationNumber, $applicantName, $certificateLink, $applicationId, $cpdoName, $certificateType)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
        $certificateIcon = $certificateType === 'Zoning Certificate' ? '📍' : '🏢';
        $certificateColor = $certificateType === 'Zoning Certificate' ? '#8B5CF6' : '#3B82F6';
        $certificateLight = $certificateType === 'Zoning Certificate' ? '#EDE9FE' : '#DBEAFE';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$certificateType} Uploaded</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                .header { background: linear-gradient(135deg, {$certificateColor} 0%, " . ($certificateType === 'Zoning Certificate' ? '#6D28D9' : '#2563EB') . " 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 40px 30px; background-color: #ffffff; }
                .greeting { font-size: 18px; color: {$certificateColor}; font-weight: 500; margin-bottom: 20px; }
                .success-badge { background-color: {$certificateLight}; color: {$certificateColor}; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid {$certificateColor}20; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid {$certificateColor}; }
                .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                .button:hover { opacity: 0.9; transform: translateY(-2px); }
                .next-steps { background-color: #f0fdf4; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #22c55e; }
                .next-steps h4 { margin: 0 0 10px 0; color: #166534; }
                .next-steps ul { margin: 0; padding-left: 20px; }
                .next-steps li { margin: 5px 0; color: #166534; font-size: 14px; }
                .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                .brand-name { font-weight: 600; color: #155386; }
                .view-link { word-break: break-all; font-family: monospace; font-size: 13px; background: #f1f3f5; padding: 8px 12px; border-radius: 6px; display: inline-block; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>{$certificateIcon} {$certificateType} Uploaded</h1>
                    <p>A new document has been added to your application</p>
                </div>
                <div class='content'>
                    <div class='greeting'>{$greeting}</div>
                    
                    <p>The City Planning and Development Office (CPDO) has uploaded the <strong>{$certificateType}</strong> for your building permit application.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <span class='success-badge'>✓ {$certificateType} Available</span>
                    </div>
                    
                    <div class='info-box'>
                        <p><strong>Application Number:</strong> {$formattedNumber}</p>
                        <p><strong>Certificate Type:</strong> {$certificateType}</p>
                        <p><strong>Uploaded by:</strong> {$cpdoName}</p>
                        <p><strong>Document Link:</strong></p>
                        <a href='{$certificateLink}' target='_blank' class='view-link' style='color: {$certificateColor}; text-decoration: underline;'>View {$certificateType}</a>
                    </div>
                    
                    <div class='next-steps'>
                        <h4>📋 What Happens Next?</h4>
                        <ul>
                            <li>The Engineering Department will now review your application</li>
                            <li>They will verify all submitted documents</li>
                            <li>You will receive notifications as the review progresses</li>
                            <li>Once approved, you will be notified to submit hard copies</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$appUrl}' class='button'>View Application Status</a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                        The Engineering Department has been notified and will begin their review shortly.
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
     * Get CPDO approval email content
     */
    private function getCPDOApprovalEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>CPDO Approval</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 30px; }
                .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
                .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
                .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10B981; }
                .next-steps { background-color: #e6f7e6; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10b981; }
                .next-steps h4 { margin: 0 0 12px 0; color: #065f46; font-size: 16px; }
                .next-steps ul { margin: 0; padding-left: 20px; }
                .next-steps li { margin: 8px 0; color: #065f46; font-size: 14px; }
                .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                .button:hover { opacity: 0.9; transform: translateY(-2px); }
                .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                .footer { padding: 20px; background-color: #f8f9fa; text-align: center; font-size: 12px; color: #666; }
                .brand-name { font-weight: 600; color: #155386; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>CPDO Approval Received</h1>
                    <p>Your application has been approved by the City Planning and Development Office</p>
                </div>
                <div class='content'>
                    <div class='greeting'>{$greeting}</div>
                    
                    <p>Great news! The City Planning and Development Office (CPDO) has reviewed and <strong>approved</strong> your application.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <span class='success-badge'>✓ CPDO Approved</span>
                    </div>
                    
                    <div class='info-box'>
                        <p><strong>Application Number:</strong> {$formattedNumber}</p>
                        <p><strong>Reviewed by:</strong> {$cpdoName}</p>
                    </div>
                    
                    <div class='next-steps'>
                        <h4>📋 What Happens Next?</h4>
                        <ul>
                            <li>Other departments can now proceed with document verification</li>
                            <li>You will receive notifications as each department reviews your application</li>
                            <li>Once all departments have verified, assessment will be completed</li>
                            <li>You will be notified when your permit is ready for release</li>
                        </ul>
                          <p><strong>💰 Assessment Fee Notice:</strong> Please wait for both the CPDO assessment fee (to be sent by CPDO) and the Building Permit Fee (to be sent by the Engineering Office) before making any payment. Once both are ready, you can pay in one go — isahan na lang po ang pagbabayad, either in person at the OBO or through Filipizen, depending on the applicable channel.</p>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$appUrl}' class='button'>View Application Status</a>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p style='font-size: 14px; color: #666; text-align: center;'>
                        Thank you for your patience throughout the review process.
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
     * Get CPDO rejection email content
     */
    private function getCPDORejectionEmailContent($applicationNumber, $applicantName, $applicationId, $cpdoName, $remarks = null)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $remarksHtml = '';
        if ($remarks) {
            $remarksHtml = '
                <div class="remarks-box" style="background-color: #FEE2E2; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626;">
                    <strong>Reason for rejection:</strong>
                    <p style="margin: 8px 0 0 0;">' . nl2br(htmlspecialchars($remarks)) . '</p>
                </div>';
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
                    .header { background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #DC2626; font-weight: 500; margin-bottom: 20px; }
                    .rejection-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
                    .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #DC2626; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .next-steps { background-color: #FEE2E2; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
                    .next-steps h4 { margin: 0 0 10px 0; color: #991B1B; }
                    .next-steps ul { margin: 0; padding-left: 20px; }
                    .next-steps li { margin: 5px 0; color: #991B1B; font-size: 14px; }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>CPDO Decision on Your Application</h1>
                        <p>The City Planning and Development Office has reviewed your application</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>After careful review, the City Planning and Development Office (CPDO) has <strong>rejected</strong> your application.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='rejection-badge'>✗ CPDO Rejected</span>
                        </div>
                        
                        <div class='info-box'>
                            <p><strong>Application Number:</strong> {$applicationNumber}</p>
                            <p><strong>Reviewed by:</strong> {$cpdoName}</p>
                        </div>
                        
                        {$remarksHtml}
                        
                        <div class='next-steps'>
                            <h4>📋 What You Can Do:</h4>
                            <ul>
                                <li>Review the reason for rejection provided above</li>
                                <li>Make the necessary corrections or provide additional documents</li>
                                <li>Submit a new application addressing the issues identified</li>
                                <li>Contact CPDO directly if you need clarification</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application Details</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            If you have questions about this decision, please contact the City Planning and Development Office directly.
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
     * Get CPDO Assessment email content with printable receipt
     */
    private function getCPDOAssessmentEmailContent($applicantName, $applicationNumber, $assessmentData, $applicationId)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $printUrl = env('APP_URL') . "/applicant/print-receipt/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formatAmount = function($amount) {
            if (!$amount || $amount == 0) return '₱0.00';
            return '₱' . number_format($amount, 2);
        };
        
        $totalAmount = $assessmentData['total_cpdo_amount'] ?? 0;
        $assessmentDate = $assessmentData['assessment_date'] ?? date('F d, Y');
        $assessedBy = $assessmentData['cpdo_assessed_by'] ?? 'CPDO Staff';
        $assessmentNotes = $assessmentData['cpdo_assessment_notes'] ?? null;
        
        // Escape values
        $escapedApplicantName = htmlspecialchars($applicantName, ENT_QUOTES, 'UTF-8');
        $escapedAssessmentDate = htmlspecialchars($assessmentDate, ENT_QUOTES, 'UTF-8');
        $escapedAssessedBy = htmlspecialchars($assessedBy, ENT_QUOTES, 'UTF-8');
        $escapedApplicationNumber = htmlspecialchars($applicationNumber, ENT_QUOTES, 'UTF-8');
        
        // Build fee rows
        $feeRows = '';
        
        if (($assessmentData['zonal_location_fee'] ?? 0) > 0) {
            $feeRows .= '<tr><td>Locational Clearance</td><td>' . $formatAmount($assessmentData['zonal_location_fee']) . '</td></tr>';
        }
        if (($assessmentData['palc_fee'] ?? 0) > 0) {
            $feeRows .= '<tr><td>PALC Fee</td><td>' . $formatAmount($assessmentData['palc_fee']) . '</td></tr>';
        }
        if (($assessmentData['development_permit_fee'] ?? 0) > 0) {
            $feeRows .= '<tr><td>Development Permit</td><td>' . $formatAmount($assessmentData['development_permit_fee']) . '</td></tr>';
        }
        if (($assessmentData['alteration_permit_fee'] ?? 0) > 0) {
            $feeRows .= '<tr><td>Alteration Permit</td><td>' . $formatAmount($assessmentData['alteration_permit_fee']) . '</td></tr>';
        }
        if (($assessmentData['site_zoning_certificate_fee'] ?? 0) > 0) {
            $feeRows .= '<tr><td>Site/Zoning Certificate</td><td>' . $formatAmount($assessmentData['site_zoning_certificate_fee']) . '</td></tr>';
        }
        
        // Additional fees
        $additionalFees = $assessmentData['cpdo_additional_fees'] ?? [];
        if (is_string($additionalFees)) {
            $additionalFees = json_decode($additionalFees, true);
        }
        
        if (is_array($additionalFees) && count($additionalFees) > 0) {
            foreach ($additionalFees as $fee) {
                $amount = is_array($fee) ? ($fee['amount'] ?? 0) : 0;
                $description = is_array($fee) ? ($fee['description'] ?? 'Additional Fee') : 'Additional Fee';
                if ($amount > 0) {
                    $feeRows .= '<tr><td>' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</td><td>' . $formatAmount($amount) . '</td></tr>';
                }
            }
        }
        
        $notesHtml = '';
        if ($assessmentNotes) {
            $notesHtml = '<div class="notes-section"><strong>Notes:</strong> ' . nl2br(htmlspecialchars($assessmentNotes, ENT_QUOTES, 'UTF-8')) . '</div>';
        }
        
        $currentYear = date('Y');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>CPDO Fee Assessment</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            line-height: 1.5; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .header { 
            background: linear-gradient(135deg, #155386 0%, #40798C 100%); 
            color: white; 
            padding: 25px 20px; 
            text-align: center; 
        }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .greeting { font-size: 16px; color: #155386; font-weight: 500; margin-bottom: 20px; }
        
        /* Receipt Styles */
        .receipt-container { 
            border: 2px solid #155386; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0;
        }
        .receipt-header { 
            text-align: center; 
            border-bottom: 2px solid #155386; 
            padding-bottom: 15px; 
            margin-bottom: 20px;
        }
        .receipt-header h2 { margin: 0; color: #155386; font-size: 18px; }
        .receipt-header p { margin: 5px 0; font-size: 12px; color: #666; }
        .receipt-title { text-align: center; margin: 15px 0; }
        .receipt-title h3 { margin: 0; text-transform: uppercase; font-size: 14px; }
        .receipt-details { margin: 20px 0; }
        .receipt-details table { width: 100%; border-collapse: collapse; }
        .receipt-details td { padding: 8px; border: none; }
        .receipt-details td:first-child { font-weight: 600; width: 40%; }
        .receipt-items { margin: 20px 0; }
        .receipt-items table { width: 100%; border-collapse: collapse; }
        .receipt-items th, .receipt-items td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        .receipt-items th { background: #f8f9fa; }
        .receipt-items td:last-child { text-align: right; }
        .receipt-total { margin: 20px 0; text-align: right; }
        .receipt-total table { width: 100%; }
        .receipt-total td { padding: 8px; }
        .receipt-total .total-label { font-weight: bold; font-size: 16px; }
        .receipt-total .total-amount { font-weight: bold; font-size: 18px; color: #155386; }
        .signature-section { margin-top: 40px; border-top: 1px solid #dee2e6; padding-top: 30px; }
        .signature-line { margin-top: 30px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; }
        .signature-box { text-align: center; flex: 1; min-width: 200px; }
        .signature-box .line { border-top: 1px solid #333; width: 100%; margin: 30px 0 5px; }
        .signature-box .name { font-weight: bold; margin-top: 10px; }
        .signature-box .title { font-size: 12px; color: #666; }
        .signature-box .wet-signature-note { font-size: 10px; color: #dc2626; font-style: italic; margin-top: 5px; }
        
        .button-container { text-align: center; margin: 20px 0; }
        .button { 
            display: inline-block; 
            background: #155386; 
            color: white; 
            padding: 12px 25px; 
            text-decoration: none; 
            border-radius: 6px; 
            margin: 0 5px; 
            font-weight: 600; 
            font-size: 14px; 
        }
        .print-button { background: #6c757d; }
        .button:hover { opacity: 0.9; }
        
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; margin: 0; max-width: 100%; }
            .no-print { display: none !important; }
            .receipt-container { border: 1px solid #000; }
            .signature-box .line { border-top: 1px solid #000; }
            .button-container, .header { display: none; }
        }
        
        @media (max-width: 600px) {
            .signature-line { flex-direction: column; gap: 20px; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Konstructo</h1>
            <p>Building Permit Application System</p>
        </div>
        <div class='content'>
            <div class='greeting'>{$greeting}</div>
            
            <p>The City Planning and Development Office (CPDO) has completed the fee assessment for your building permit application.</p>
            
            <!-- Printable Receipt -->
            <div class='receipt-container' id='printable-receipt'>
                <div class='receipt-header'>
                    <h2>CITY GOVERNMENT OF LIGAO</h2>
                    <p>CITY PLANNING AND DEVELOPMENT OFFICE</p>
                    <p>CPDO Fee Assessment Receipt</p>
                </div>
                
                <div class='receipt-title'>
                    <h3>OFFICIAL FEE ASSESSMENT</h3>
                </div>
                
                <div class='receipt-details'>
                    <table>
                        <tr><td>Application Number:</td><td><strong>{$escapedApplicationNumber}</strong></td></tr>
                        <tr><td>Applicant Name:</td><td><strong>{$escapedApplicantName}</strong></td></tr>
                        <tr><td>Assessment Date:</td><td><strong>{$escapedAssessmentDate}</strong></td></tr>
                    </table>
                </div>
                
                <div class='receipt-items'>
                    <h4>Fee Breakdown:</h4>
                    <table>
                        <thead><tr><th>Description</th><th>Amount</th></tr></thead>
                        <tbody>{$feeRows}</tbody>
                    </table>
                </div>
                
                <div class='receipt-total'>
                    <table>
                        <tr><td class='total-label'>TOTAL CPDO FEES:</td><td class='total-amount'>{$formatAmount($totalAmount)}</td></tr>
                    </table>
                </div>
                
                {$notesHtml}
                
                <div class='signature-section'>
                    <div class='signature-line'>
                        <div class='signature-box'>
                            <div class='line'></div>
                            <div class='name'>ASSESSED BY</div>
                        </div>
                        <div class='signature-box'>
                            <div class='line'></div>
                            <div class='name'>OSCAR D. AQUINO, EnP</div>
                            <div class='title'>ACDH I / Acting CPDC</div>
                        </div>
                    </div>
                    <div style='margin-top: 20px; text-align: center; font-size: 11px; color: #666;'>
                        <p>Assessment Date: {$escapedAssessmentDate} | Assessed by: {$escapedAssessedBy}</p>
                    </div>
                </div>
            </div>
            
            <div class='button-container no-print'>
                <a href='{$printUrl}' target='_blank' class='button print-button'>🖨️ Print Assessment Receipt</a>
            </div>
            
            <div class='no-print' style='background: #e6f7e6; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                <h4 style='margin: 0 0 10px 0; color: #065f46;'>📋 Next Step:</h4>
                <ul style='margin: 0; padding-left: 20px;'>
                    <p>To avoid multiple separate payments, kindly hold off on settling any fees until the Building Permit Fee has been generated. Once available, you may proceed with a one-time payment covering all required fees, as facilitated by Filipizen.</p>
                </ul>
            </div>
            
            <p style='font-size: 14px; color: #666; text-align: center; margin-top: 20px;'>
                If you have any questions, please contact the City Planning and Development Office.
            </p>
        </div>
        <div style='padding: 20px; background: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 12px; color: #666; text-align: center;'>
            <p>Konstructo — Smart Infrastructure Oversight</p>
            <p>&copy; {$currentYear} Konstructo. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get credentials email content
     */
    private function getCredentialsEmailContent($name, $username, $password, $isReset = false)
    {
        $greeting = $name ? "Dear " . $name . "," : "Dear Valued User,";
        $loginUrl = env('APP_URL') . '/login';
        
        $introText = $isReset 
            ? "Your password has been reset by an administrator. Please find your new login credentials below:"
            : "Welcome to Konstructo! Your account has been created successfully. Please find your login credentials below:";
        
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
                    .header p { margin: 10px 0 0 0; opacity: 0.9; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .credentials-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; border-radius: 12px; margin: 25px 0; border: 1px solid #dee2e6; }
                    .credential-item { display: flex; align-items: center; justify-content: space-between; padding: 12px; background: white; border-radius: 8px; margin-bottom: 10px; }
                    .credential-item:last-child { margin-bottom: 0; }
                    .credential-label { font-weight: 600; color: #155386; font-size: 14px; }
                    .credential-value { font-family: 'Courier New', monospace; background-color: #f1f3f5; padding: 6px 12px; border-radius: 6px; font-size: 14px; letter-spacing: 0.5px; }
                    .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0; }
                    .warning-box p { margin: 0; color: #856404; font-size: 14px; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .security-tip { background-color: #e8f4f8; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
                    .security-tip h4 { margin: 0 0 8px 0; color: #155386; }
                    @media (max-width: 600px) {
                        .credential-item { flex-direction: column; align-items: flex-start; gap: 8px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Konstructo</h1>
                        <p>Building Permits & Licensing System</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>{$introText}</p>
                        
                        <div class='credentials-box'>
                            <div class='credential-item'>
                                <span class='credential-label'>📧 Email:</span>
                                <span class='credential-value'>{$to}</span>
                            </div>
                            <div class='credential-item'>
                                <span class='credential-label'>👤 Username:</span>
                                <span class='credential-value'>{$username}</span>
                            </div>
                            <div class='credential-item'>
                                <span class='credential-label'>🔑 Password:</span>
                                <span class='credential-value'>{$password}</span>
                            </div>
                        </div>
                        
                        <div class='warning-box'>
                            <p><strong>⚠️ Important:</strong> For security reasons, please change your password immediately after your first login.</p>
                        </div>
                        
                        <div class='security-tip'>
                            <h4>🔒 Security Tips:</h4>
                            <ul style='margin: 8px 0 0 0; padding-left: 20px;'>
                                <li>Do not share your password with anyone</li>
                                <li>Use a strong, unique password</li>
                                <li>Enable two-factor authentication if available</li>
                                <li>Always log out after using shared devices</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$loginUrl}' class='button'>Login to Your Account</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            If you did not request this account, please contact our support team immediately.
                        </p>
                        <p style='font-size: 12px; color: #6c757d; text-align: center;'>
                            This is an automated message, please do not reply to this email.
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
     * Get application submitted email content
     */
    private function getApplicationSubmittedEmailContent($applicationNumber, $applicantName, $applicationId)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formattedNumber = $applicationNumber;
        if (strlen($applicationNumber) === 10) {
            $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                              substr($applicationNumber, 2, 4) . '-' . 
                              substr($applicationNumber, 6, 4);
        }
        
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
     * Get assessment email content with fee breakdown
     */
    private function getAssessmentEmailContent($applicationNumber, $applicantName, $assessmentData, $applicationId)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
        
        $formatAmount = function($amount) {
            if (!$amount || $amount == 0) return '₱0.00';
            return '₱' . number_format($amount, 2);
        };
        
        $standardFeesHtml = '';
        $standardFees = [
            ['label' => 'Line Grade Fee', 'value' => $assessmentData['line_grade'] ?? null],
            ['label' => 'Building Fee', 'value' => $assessmentData['building_fee'] ?? null],
            ['label' => 'Sanitary/Plumbing Fee', 'value' => $assessmentData['sanitary_fee'] ?? null],
            ['label' => 'Mechanical Fee', 'value' => $assessmentData['mechanical_fee'] ?? null],
            ['label' => 'Electrical Fee', 'value' => $assessmentData['electrical_fee'] ?? null],
        ];
        
        $hasStandardFees = false;
        foreach ($standardFees as $fee) {
            if ($fee['value'] && $fee['value'] > 0) {
                $hasStandardFees = true;
                $standardFeesHtml .= '
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #4b5563;">' . $fee['label'] . ':</span>
                        <span style="font-weight: 500; color: #1f2937;">' . $formatAmount($fee['value']) . '</span>
                    </div>';
            }
        }
        
        $additionalFeesHtml = '';
        $additionalFees = $assessmentData['additional_fees'] ?? [];
        if (is_string($additionalFees)) {
            $additionalFees = json_decode($additionalFees, true);
        }
        
        $hasAdditionalFees = false;
        if (is_array($additionalFees) && count($additionalFees) > 0) {
            foreach ($additionalFees as $fee) {
                $amount = is_array($fee) ? ($fee['amount'] ?? 0) : 0;
                $description = is_array($fee) ? ($fee['description'] ?? 'Additional Fee') : 'Additional Fee';
                if ($amount && $amount > 0) {
                    $hasAdditionalFees = true;
                    $additionalFeesHtml .= '
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                            <span style="color: #4b5563;">' . htmlspecialchars($description) . ':</span>
                            <span style="font-weight: 500; color: #1f2937;">' . $formatAmount($amount) . '</span>
                        </div>';
                }
            }
        }
        
        $penaltiesHtml = '';
        if (($assessmentData['penalties_fines'] ?? 0) > 0) {
            $penaltiesHtml .= '
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #dc2626;">Penalties/Fines:</span>
                    <span style="font-weight: 500; color: #dc2626;">' . $formatAmount($assessmentData['penalties_fines']) . '</span>
                </div>';
        }
        
        $totalAmount = $assessmentData['total_amount'] ?? 0;
        $assessmentNotes = $assessmentData['assessment_notes'] ?? null;
        
        $notesHtml = '';
        if ($assessmentNotes) {
            $notesHtml = '
                <div class="notes-section" style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b;">
                    <h3 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px; font-weight: 600;">📝 Assessment Notes</h3>
                    <p style="margin: 0; color: #78350f; font-size: 14px;">' . nl2br(htmlspecialchars($assessmentNotes)) . '</p>
                </div>';
        }
        
        $feeBreakdownHtml = '';
        if ($hasStandardFees || $hasAdditionalFees || $penaltiesHtml) {
            $feeBreakdownHtml = '<div class="fee-summary" style="background-color: #f8f9fa; border-radius: 12px; padding: 20px; margin: 25px 0; border: 1px solid #e5e7eb;">';
            $feeBreakdownHtml .= '<h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 16px;">💰 Building Permit Fee Breakdown</h3>';
            
            if ($hasStandardFees) {
                $feeBreakdownHtml .= $standardFeesHtml;
            }
            
            if ($hasAdditionalFees) {
                $feeBreakdownHtml .= '
                    <div style="margin-top: 10px;">
                        <div style="font-weight: 600; color: #4b5563; padding: 8px 0; border-bottom: 1px solid #d1d5db;">Additional Fees:</div>
                        ' . $additionalFeesHtml . '
                    </div>';
            }
            
            $feeBreakdownHtml .= $penaltiesHtml;
            
            $feeBreakdownHtml .= '
                <div class="total-row" style="display: flex; justify-content: space-between; padding: 12px 0; margin-top: 10px; border-top: 2px solid #d1d5db; font-weight: bold; font-size: 18px;">
                    <span style="color: #155386;">TOTAL BUILDING PERMIT FEE:</span>
                    <span style="color: #155386;">' . $formatAmount($totalAmount) . '</span>
                </div>
            </div>';
        } else {
            $feeBreakdownHtml = '
                <div class="fee-summary" style="background-color: #f8f9fa; border-radius: 12px; padding: 20px; margin: 25px 0; border: 1px solid #e5e7eb; text-align: center;">
                    <p style="color: #6c757d; margin: 0;">No fees have been assessed for this application yet.</p>
                </div>';
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
                    .header p { margin: 10px 0 0 0; opacity: 0.9; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .status-badge { background-color: #e0e7ff; color: #4338ca; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #c7d2fe; }
                    .app-number { background-color: #f3f4f6; padding: 10px 15px; border-radius: 8px; text-align: center; margin: 20px 0; }
                    .app-number span { font-family: monospace; font-size: 18px; font-weight: bold; color: #155386; }
                    .fee-summary { background-color: #f8f9fa; border-radius: 12px; padding: 20px; margin: 25px 0; border: 1px solid #e5e7eb; }
                    .fee-summary h3 { margin: 0 0 15px 0; color: #1f2937; font-size: 16px; }
                    .total-row { display: flex; justify-content: space-between; padding: 12px 0; margin-top: 10px; border-top: 2px solid #d1d5db; font-weight: bold; font-size: 18px; }
                    .total-row span:first-child { color: #155386; }
                    .total-row span:last-child { color: #155386; }
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
                        <h1>Building Permit Assessment Completed</h1>
                        <p>Your application has been reviewed and assessed</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Great news! The assessment for your building permit application has been completed.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='status-badge'>✓ Assessment Completed</span>
                        </div>
                        
                        <div class='app-number'>
                            <strong>Application Number:</strong><br>
                            <span>{$applicationNumber}</span>
                        </div>
                        
                        {$feeBreakdownHtml}
                        
                        {$notesHtml}
                        
                        <div class='next-steps'>
                            <h4>📋 Next Steps:</h4>
                            <ul>
                                <li>Prepare the total assessed fee of <strong>{$formatAmount($totalAmount)}</strong></li>
                                <li>Pay Online via Filipizen, you can access it on your applications page</li>
                                <li>Once payment and documents are received, your permit will be released</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application Details</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            If you have any questions about the assessment, please contact the One Building Office.
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
                        <p>Thank you for registering with Konstructo. To complete your account setup, please verify your email address using the verification code below:</p>
                        
                        <div class='code-box'>{$code}</div>
                        
                        <div class='expiry-note'>
                            <strong>Note:</strong> This verification code will expire in 10 minutes.
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d;'>
                            For security reasons, never share this code with anyone.
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
                        <h1>Welcome to Konstructo!</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Great news! Your account has been <strong>approved</strong> by the administrator.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='success-badge'>✓ Account Approved</span>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . env('APP_URL') . "/login' class='button'>Login to Your Account</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for choosing Konstructo.
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
                        <h1>Account Application Status</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Your account application has been <strong>rejected</strong>.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='rejection-badge'>✗ Account Rejected</span>
                        </div>
                        
                        {$reasonHtml}
                        
                        <div style='text-align: center;'>
                            <a href='" . env('APP_URL') . "/register' class='button'>Create New Account</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            If you believe this is a mistake, please contact our support team.
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
                            <a href='" . env('APP_URL') . "/admin/settings' class='button'>Go to Admin Panel</a>
                        </div>
                        
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
     * Get status email content
     */
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId, $additionalData = [])
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        $statusDisplay = ucfirst(str_replace('-', ' ', $status));
        
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
        
        $additionalContent = '';
        if ($status === 'approved' && !empty($additionalData)) {
            $submissionDate = $additionalData['hardcopy_submission_date'] ?? null;
            $instructions = $additionalData['hardcopy_instructions'] ?? null;
            
            if ($submissionDate || $instructions) {
                $additionalContent = '
                    <div class="hardcopy-info" style="background-color: #e0e7ff; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #4338ca;">
                        <h3 style="margin: 0 0 10px 0; color: #4338ca; font-size: 16px;">📄 Hard Copy Submission Required</h3>';
                
                if ($submissionDate) {
                    $additionalContent .= '<p style="margin: 5px 0;"><strong>Submission Date:</strong> ' . htmlspecialchars($submissionDate) . '</p>';
                }
                
                if ($instructions) {
                    $additionalContent .= '<p style="margin: 5px 0;"><strong>Instructions:</strong> ' . nl2br(htmlspecialchars($instructions)) . '</p>';
                }
                
                $additionalContent .= '
                        <p style="margin-top: 10px; font-size: 13px; color: #4338ca;">Please bring the required hard copies on the specified date.</p>
                    </div>';
            }
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
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .status-badge { background-color: {$color['light']}; color: {$color['bg']}; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid {$color['bg']}20; }
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
                        
                        <p>Your building permit application <strong>#{$applicationNumber}</strong> has received a status update.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='status-badge'>Current Status: {$statusDisplay}</span>
                        </div>
                        
                        {$additionalContent}
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Thank you for using Konstructo.
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
     * Get missing documents email content
     */
    private function getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
        
        $documentList = '<div style="margin: 20px 0;">';
        foreach ($missingDocuments as $doc) {
            $documentList .= '
                <div style="display: flex; align-items: center; padding: 10px; background-color: #fef2f2; border-radius: 4px; margin-bottom: 5px;">
                    <span style="color: #DC2626; margin-right: 10px;">✗</span>
                    <span style="font-size: 14px; color: #1F2937;">' . htmlspecialchars($doc) . '</span>
                </div>';
        }
        $documentList .= '</div>';
        
        $remarksHtml = '';
        if ($remarks) {
            $remarksHtml = '
                <div class="remarks-box" style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b;">
                    <strong>Remarks:</strong>
                    <p style="margin: 8px 0 0 0;">' . nl2br(htmlspecialchars($remarks)) . '</p>
                </div>';
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
                    .header { background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #DC2626; font-weight: 500; margin-bottom: 20px; }
                    .warning-badge { background-color: #FEE2E2; color: #DC2626; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #DC2626; }
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
                        <h1>Action Required: Missing Documents</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>Your building permit application <strong>#{$applicationNumber}</strong> requires additional documents.</p>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='warning-badge'>⚠️ Missing Documents Required</span>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #DC2626; font-size: 16px;'>The following documents are missing:</h3>
                            {$documentList}
                        </div>
                        
                        {$remarksHtml}
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            Please submit the required documents as soon as possible.
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
    /**
 * Send email to treasurer when both assessments are ready for payment order creation
 */
public function sendAssessmentsReadyForPaymentOrderEmail($to, $treasurerName, $applicationNumber, $applicantName, $applicationId, $buildingPermitFee, $cpdoFee, $totalAmount)
{
    $subject = 'Action Required: Payment Order Number Needed - Konstructo';
    $htmlContent = $this->getAssessmentsReadyEmailContent($treasurerName, $applicationNumber, $applicantName, $applicationId, $buildingPermitFee, $cpdoFee, $totalAmount);
    
    Log::info('📧 Sending assessments ready email to treasurer', [
        'to' => $to,
        'application_number' => $applicationNumber,
        'total_amount' => $totalAmount
    ]);
    
    return $this->sendEmailInternal($to, $subject, $htmlContent);
}

/**
 * Send email to treasurer when applicant uploads OR
 */
public function sendORUploadedToTreasurerEmail($to, $treasurerName, $applicationNumber, $applicantName, $applicationId, $orLink)
{
    $subject = 'Official Receipt Uploaded - Action Required - Konstructo';
    $htmlContent = $this->getORUploadedToTreasurerEmailContent($treasurerName, $applicationNumber, $applicantName, $applicationId, $orLink);
    
    Log::info('📧 Sending OR uploaded notification to treasurer', [
        'to' => $to,
        'application_number' => $applicationNumber,
        'or_link' => $orLink
    ]);
    
    return $this->sendEmailInternal($to, $subject, $htmlContent);
}

/**
 * Send email to applicant when payment order number is created
 */
public function sendPaymentOrderCreatedToApplicantEmail($to, $applicantName, $applicationNumber, $orderNumber, $applicationId, $totalAmount)
{
    $subject = 'Payment Order Number Ready - Proceed with Payment - Konstructo';
    $htmlContent = $this->getPaymentOrderCreatedEmailContent($applicantName, $applicationNumber, $orderNumber, $applicationId, $totalAmount);
    
    Log::info('📧 Sending payment order created email to applicant', [
        'to' => $to,
        'application_number' => $applicationNumber,
        'order_number' => $orderNumber
    ]);
    
    return $this->sendEmailInternal($to, $subject, $htmlContent);
}

/**
 * Get assessments ready email content for treasurer
 */
private function getAssessmentsReadyEmailContent($treasurerName, $applicationNumber, $applicantName, $applicationId, $buildingPermitFee, $cpdoFee, $totalAmount)
{
    $appUrl = env('APP_URL') . "/staff/payment-assessments";
    $greeting = $treasurerName ? "Dear Treasurer " . $treasurerName . "," : "Dear Treasurer,";
    
    $formattedNumber = $applicationNumber;
    if (strlen($applicationNumber) === 10) {
        $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                          substr($applicationNumber, 2, 4) . '-' . 
                          substr($applicationNumber, 6, 4);
    }
    
    $formatAmount = function($amount) {
        if (!$amount || $amount == 0) return '₱0.00';
        return '₱' . number_format($amount, 2);
    };
    
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
            .action-badge { background-color: #FEF3C7; color: #D97706; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #F59E0B; }
            .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #F59E0B; }
            .fee-summary { background-color: #f8f9fa; border-radius: 12px; padding: 20px; margin: 20px 0; border: 1px solid #e5e7eb; }
            .fee-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
            .total-row { display: flex; justify-content: space-between; padding: 12px 0; margin-top: 10px; border-top: 2px solid #d1d5db; font-weight: bold; font-size: 18px; }
            .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
            .button:hover { opacity: 0.9; transform: translateY(-2px); }
            .steps-box { background-color: #e6f7e6; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #10b981; }
            .steps-box h4 { margin: 0 0 10px 0; color: #065f46; }
            .steps-box ul { margin: 0; padding-left: 20px; }
            .steps-box li { margin: 5px 0; color: #065f46; font-size: 14px; }
            .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
            .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
            .brand-name { font-weight: 600; color: #155386; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Action Required: Payment Order Number</h1>
                <p>Both assessments are ready for payment processing</p>
            </div>
            <div class='content'>
                <div class='greeting'>{$greeting}</div>
                
                <p>Both the Building Permit Fee Assessment and CPDO Fee Assessment have been completed for the following application.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <span class='action-badge'>⚠️ Action Required: Create Payment Order Number</span>
                </div>
                
                <div class='info-box'>
                    <p><strong>📋 Application Number:</strong> {$formattedNumber}</p>
                    <p><strong>👤 Applicant Name:</strong> " . htmlspecialchars($applicantName) . "</p>
                </div>
                
                <div class='fee-summary'>
                    <h4 style=\"margin: 0 0 15px 0; color: #1f2937;\">💰 Assessment Summary</h4>
                    <div class='fee-row'>
                        <span>Building Permit Fee:</span>
                        <span><strong>{$formatAmount($buildingPermitFee)}</strong></span>
                    </div>
                    <div class='fee-row'>
                        <span>CPDO Fee:</span>
                        <span><strong>{$formatAmount($cpdoFee)}</strong></span>
                    </div>
                    <div class='total-row'>
                        <span>TOTAL AMOUNT:</span>
                        <span style=\"color: #D97706;\">{$formatAmount($totalAmount)}</span>
                    </div>
                </div>
                
                <div class='steps-box'>
                    <h4>📌 What You Need to Do:</h4>
                    <ul>
                        <li>Create a Payment Order Number for this application</li>
                        <li>Go to the Payment Assessments page</li>
                        <li>Click \"Add Order Number\" for this application</li>
                        <li>Enter the official order number and payment date</li>
                        <li>Once created, the applicant will be notified automatically</li>
                    </ul>
                </div>
                
                <div style='text-align: center;'>
                    <a href='{$appUrl}' class='button'>Go to Payment Assessments</a>
                </div>
                
                <div class='divider'></div>
                
                <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                    Please create the payment order number as soon as possible so the applicant can proceed with payment.
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
 * Get OR uploaded to treasurer email content
 */
private function getORUploadedToTreasurerEmailContent($treasurerName, $applicationNumber, $applicantName, $applicationId, $orLink)
{
    $appUrl = env('APP_URL') . "/staff/payment-assessments";
    $greeting = $treasurerName ? "Dear Treasurer " . $treasurerName . "," : "Dear Treasurer,";
    
    $formattedNumber = $applicationNumber;
    if (strlen($applicationNumber) === 10) {
        $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                          substr($applicationNumber, 2, 4) . '-' . 
                          substr($applicationNumber, 6, 4);
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
            .header { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 40px 30px; background-color: #ffffff; }
            .greeting { font-size: 18px; color: #10B981; font-weight: 500; margin-bottom: 20px; }
            .action-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
            .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10B981; }
            .or-link-box { background-color: #e6f7e6; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; word-break: break-all; }
            .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
            .button:hover { opacity: 0.9; transform: translateY(-2px); }
            .steps-box { background-color: #FEF3C7; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #F59E0B; }
            .steps-box h4 { margin: 0 0 10px 0; color: #92400E; }
            .steps-box ul { margin: 0; padding-left: 20px; }
            .steps-box li { margin: 5px 0; color: #92400E; font-size: 14px; }
            .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
            .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
            .brand-name { font-weight: 600; color: #155386; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Official Receipt Uploaded</h1>
                <p>Action Required: Verify the OR</p>
            </div>
            <div class='content'>
                <div class='greeting'>{$greeting}</div>
                
                <p>The applicant has uploaded their Official Receipt (OR) for the following application.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <span class='action-badge'>📄 OR Ready for Verification</span>
                </div>
                
                <div class='info-box'>
                    <p><strong>📋 Application Number:</strong> {$formattedNumber}</p>
                    <p><strong>👤 Applicant Name:</strong> " . htmlspecialchars($applicantName) . "</p>
                </div>
                
                <div class='or-link-box'>
                    <strong>🔗 Official Receipt Link:</strong><br>
                    <a href='{$orLink}' target='_blank' style='color: #059669;'>View Official Receipt</a>
                </div>
                
                <div class='steps-box'>
                    <h4>📌 What You Need to Do:</h4>
                    <ul>
                        <li>Review the uploaded Official Receipt</li>
                        <li>Verify if the amount matches the total assessment fee</li>
                        <li>Click \"Verify\" or \"Reject\" with reason</li>
                        <li>Once verified, the application will proceed to the next step</li>
                    </ul>
                </div>
                
                <div style='text-align: center;'>
                    <a href='{$appUrl}' class='button'>Go to Payment Assessments</a>
                </div>
                
                <div class='divider'></div>
                
                <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                    Please verify the OR as soon as possible to avoid delays in processing.
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
 * Get payment order created email content for applicant
 */
private function getPaymentOrderCreatedEmailContent($applicantName, $applicationNumber, $orderNumber, $applicationId, $totalAmount)
{
    $appUrl = env('APP_URL') . "/applicant/application-details/{$applicationId}";
    $greeting = $applicantName ? "Dear " . $applicantName . "," : "Dear Valued User,";
    
    $formattedNumber = $applicationNumber;
    if (strlen($applicationNumber) === 10) {
        $formattedNumber = substr($applicationNumber, 0, 2) . '-' . 
                          substr($applicationNumber, 2, 4) . '-' . 
                          substr($applicationNumber, 6, 4);
    }
    
    $formatAmount = function($amount) {
        if (!$amount || $amount == 0) return '₱0.00';
        return '₱' . number_format($amount, 2);
    };
    
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
            .header p { margin: 10px 0 0 0; opacity: 0.9; }
            .content { padding: 40px 30px; background-color: #ffffff; }
            .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
            .success-badge { background-color: #D1FAE5; color: #059669; padding: 8px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; border: 1px solid #10B981; }
            .order-number-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 25px; text-align: center; border-radius: 12px; margin: 25px 0; border: 1px solid #dee2e6; }
            .order-number-box .label { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
            .order-number-box .number { font-size: 32px; font-weight: bold; font-family: monospace; color: #155386; letter-spacing: 2px; }
            .info-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #155386; }
            .fee-box { background-color: #e6f7e6; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; text-align: center; }
            .fee-box .amount { font-size: 24px; font-weight: bold; color: #155386; }
            .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; transition: all 0.3s ease; }
            .button:hover { opacity: 0.9; transform: translateY(-2px); }
            .steps-box { background-color: #FEF3C7; padding: 15px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #F59E0B; }
            .steps-box h4 { margin: 0 0 10px 0; color: #92400E; }
            .steps-box ul { margin: 0; padding-left: 20px; }
            .steps-box li { margin: 5px 0; color: #92400E; font-size: 14px; }
            .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
            .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
            .brand-name { font-weight: 600; color: #155386; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Payment Order Number Ready</h1>
                <p>You can now proceed with your payment</p>
            </div>
            <div class='content'>
                <div class='greeting'>{$greeting}</div>
                
                <p>Great news! The treasurer has created a Payment Order Number for your building permit application.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <span class='success-badge'>✓ Payment Order Number Assigned</span>
                </div>
                
                <div class='order-number-box'>
                    <div class='label'>Your Payment Order Number</div>
                    <div class='number'>" . htmlspecialchars($orderNumber) . "</div>
                    <div class='label' style='margin-top: 10px;'>Application Number: {$formattedNumber}</div>
                </div>
                
                <div class='fee-box'>
                    <strong>💰 Total Amount to Pay:</strong><br>
                    <span class='amount'>{$formatAmount($totalAmount)}</span>
                </div>
                
                <div class='steps-box'>
                    <h4>📌 How to Proceed with Payment:</h4>
                    <ul>
                        <li>Go to your Application Details page</li>
                        <li>Click the \"Payment Portal\" button</li>
                        <li>Use your <strong>Payment Order Number: " . htmlspecialchars($orderNumber) . "</strong> when making the payment</li>
                        <li>Complete the payment through the Filipizen payment portal</li>
                        <li>After payment, upload your Official Receipt (OR) on your application page</li>
                    </ul>
                </div>
                
                <div class='info-box'>
                    <p><strong>⚠️ Important Notes:</strong></p>
                    <ul style='margin: 5px 0 0 20px;'>
                        <li>Keep your Payment Order Number for reference</li>
                        <li>You must use this order number when paying</li>
                        <li>Upload your OR immediately after payment</li>
                        <li>Processing will continue once your OR is verified</li>
                    </ul>
                </div>
                
                <div style='text-align: center;'>
                    <a href='{$appUrl}' class='button'>Go to Your Application</a>
                </div>
                
                <div class='divider'></div>
                
                <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                    If you have any questions about the payment process, please contact the Treasurer's Office.
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
}