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
     * Send user credentials email (username and password) to newly created users
     * 
     * @param string $to Recipient email address
     * @param string $name Recipient's full name
     * @param string $username User's username
     * @param string $password Plain text password
     * @param bool $isReset Whether this is a password reset email
     * @return bool
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
    public function sendStatusEmail($to, $status, $applicationNumber, $applicantName, $applicationId)
    {
        $subject = $this->getEmailSubject($status);
        $htmlContent = $this->getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId);
        
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
            'total_amount' => $assessmentData['total_amount'] ?? 0
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
        
        return $this->sendEmailInternal($staffEmail, $subject, $htmlContent);
    }

    /**
     * Send basic requirements approval email
     */
    public function sendBasicRequirementsApprovedEmail($to, $firstName, $requirementId, $approverName = null, $applicationNumber = null)
    {
        $subject = 'Basic Requirements Approved - Konstructo';
        $htmlContent = $this->getBasicRequirementsApprovedEmailContent($firstName, $requirementId, $approverName, $applicationNumber);
        
        Log::info('📧 Sending basic requirements approval email', [
            'to' => $to,
            'requirement_id' => $requirementId,
            'application_number' => $applicationNumber
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
    }

    /**
     * Send basic requirements rejection email
     */
    public function sendBasicRequirementsRejectedEmail($to, $firstName, $requirementId, $reason, $applicationNumber = null)
    {
        $subject = 'Basic Requirements Update - Konstructo';
        $htmlContent = $this->getBasicRequirementsRejectedEmailContent($firstName, $requirementId, $reason, $applicationNumber);
        
        Log::info('📧 Sending basic requirements rejection email', [
            'to' => $to,
            'requirement_id' => $requirementId,
            'reason_length' => strlen($reason),
            'application_number' => $applicationNumber
        ]);
        
        return $this->sendEmailInternal($to, $subject, $htmlContent);
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
        
        $feeItems = '';
        $feeItemsArray = [
            ['label' => 'Line Grade Fee', 'value' => $assessmentData['line_grade'] ?? null],
            ['label' => 'Building Fee', 'value' => $assessmentData['building_fee'] ?? null],
            ['label' => 'Sanitary/Plumbing Fee', 'value' => $assessmentData['sanitary_fee'] ?? null],
            ['label' => 'Mechanical Fee', 'value' => $assessmentData['mechanical_fee'] ?? null],
            ['label' => 'Electrical Fee', 'value' => $assessmentData['electrical_fee'] ?? null],
        ];
        
        foreach ($feeItemsArray as $item) {
            if ($item['value'] && $item['value'] > 0) {
                $feeItems .= '
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                        <span style="color: #4b5563;">' . $item['label'] . ':</span>
                        <span style="font-weight: 500; color: #1f2937;">' . $formatAmount($item['value']) . '</span>
                    </div>';
            }
        }
        
        if (($assessmentData['others_amount'] ?? 0) > 0) {
            $othersLabel = !empty($assessmentData['others_description']) 
                ? 'Others (' . htmlspecialchars($assessmentData['others_description']) . ')' 
                : 'Others';
            $feeItems .= '
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: #4b5563;">' . $othersLabel . ':</span>
                    <span style="font-weight: 500; color: #1f2937;">' . $formatAmount($assessmentData['others_amount']) . '</span>
                </div>';
        }
        
        if (($assessmentData['penalties_fines'] ?? 0) > 0) {
            $feeItems .= '
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
                        
                        <div class='fee-summary'>
                            <h3>💰 Building Permit Fee Breakdown</h3>
                            {$feeItems}
                            <div class='total-row'>
                                <span>TOTAL BUILDING PERMIT FEE:</span>
                                <span>{$formatAmount($totalAmount)}</span>
                            </div>
                        </div>
                        
                        {$notesHtml}
                        
                        <div class='next-steps'>
                            <h4>📋 Next Steps:</h4>
                            <ul>
                                <li>Prepare the total assessed fee of <strong>{$formatAmount($totalAmount)}</strong></li>
                                <li>Submit the payment and original hard copies of your documents to the One Building Office (OBO)</li>
                                <li>Bring a printed copy of this email for reference</li>
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
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId)
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
     * Get new basic requirements notification email content for staff
     */
    private function getNewBasicRequirementsEmailContent($staffName, $applicant, $requirement)
    {
        $greeting = $staffName ? "Dear " . $staffName . "," : "Dear Staff,";
        $requirementsUrl = env('APP_URL') . "/staff/basic-requirements/{$requirement->id}";
        
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
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get basic requirements approved email content
     */
    private function getBasicRequirementsApprovedEmailContent($firstName, $requirementId, $approverName = null, $applicationNumber = null)
    {
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        $approverText = $approverName ? " by " . $approverName : "";
        $appNumberText = $applicationNumber ? "<p><strong>Application Number:</strong> {$applicationNumber}</p>" : "";
        
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
                        
                        {$appNumberText}
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='success-badge'>✓ Requirements Approved</span>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #059669; font-size: 16px;'>What's Next?</h3>
                            <p>You can now proceed with your building permit application.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . env('APP_URL') . "/applicant/application/step1?id={$requirementId}' class='button'>Start Your Application</a>
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
     * Get basic requirements rejected email content
     */
    private function getBasicRequirementsRejectedEmailContent($firstName, $requirementId, $reason, $applicationNumber = null)
    {
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        $appNumberText = $applicationNumber ? "<p><strong>Application Number:</strong> {$applicationNumber}</p>" : "";
        
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
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #DC2626; }
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
                        <h1>Basic Requirements Update</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        
                        <p>Your basic requirements have been <strong>rejected</strong>.</p>
                        
                        {$appNumberText}
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <span class='rejection-badge'>✗ Requirements Rejected</span>
                        </div>
                        
                        <div class='reason-box'>
                            <strong>Reason for rejection:</strong>
                            <p style='margin: 8px 0 0 0;'>" . nl2br(htmlspecialchars($reason)) . "</p>
                        </div>
                        
                        <div class='info-section'>
                            <h3 style='margin-top: 0; color: #DC2626; font-size: 16px;'>What you can do:</h3>
                            <p>Please review the reason above, make the necessary corrections, and resubmit your requirements.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='" . env('APP_URL') . "/applicant/basic-requirements?application_id={$requirementId}' class='button'>Resubmit Requirements</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d; text-align: center;'>
                            If you need assistance, please contact our support team.
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
}