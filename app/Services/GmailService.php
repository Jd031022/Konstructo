<?php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GmailService
{
    protected $client;
    protected $service;
    protected $isConfigured = false;

    public function __construct()
    {
        try {
            $refreshToken = env('GOOGLE_REFRESH_TOKEN');
            $clientId = env('GOOGLE_CLIENT_ID');
            $clientSecret = env('GOOGLE_CLIENT_SECRET');
            
            // Check if all required credentials are present
            if (empty($refreshToken) || empty($clientId) || empty($clientSecret)) {
                Log::warning('GmailService: Missing Google API credentials. Email functionality will be disabled.');
                $this->isConfigured = false;
                return;
            }
            
            // Log token info for debugging (remove in production)
            Log::info('Token prefix: ' . substr($refreshToken, 0, 10) . '...');
            
            $this->client = new Google_Client();
            $this->client->setClientId($clientId);
            $this->client->setClientSecret($clientSecret);
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt('force');
            
            // Set the refresh token
            $this->client->refreshToken($refreshToken);
            
            // Fetch a new access token
            $newToken = $this->client->fetchAccessTokenWithRefreshToken();
            
            // Check for errors
            if (isset($newToken['error'])) {
                Log::error('Token refresh failed: ' . json_encode($newToken));
                $this->isConfigured = false;
                return;
            }
            
            $this->service = new Google_Service_Gmail($this->client);
            $this->isConfigured = true;
            
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
     * Send verification email with personalized name
     */
    public function sendVerificationEmail($to, $code, $firstName = null)
    {
        if (!$this->isConfigured) {
            Log::info('Gmail not configured. Would send verification email to: ' . $to . ' with code: ' . $code);
            return true; // Return true to not break the flow
        }
        
        try {
            // Ensure token is fresh before sending
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            
            $subject = 'Verify Your Email Address - Konstructo';
            $formattedName = $firstName ? ucfirst(strtolower(trim($firstName))) : null;
            $emailContent = $this->createEmailContent($to, $subject, $code, $formattedName);
            
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($emailContent);
            
            $this->service->users_messages->send('me', $message);
            
            Log::info('Verification email sent successfully', ['to' => $to]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create email content with proper formatting
     */
    private function createEmailContent($to, $subject, $code, $firstName = null)
    {
        $fromName = "Konstructo";
        $fromEmail = env('EMAIL_USER', 'noreply@konstructo.com');
        
        // Personalize greeting
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        
        $htmlContent = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        line-height: 1.6; 
                        color: #333333; 
                        margin: 0;
                        padding: 0;
                        background-color: #f5f5f5;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 20px auto; 
                        background-color: #ffffff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    }
                    .header { 
                        background: linear-gradient(135deg, #155386 0%, #40798C 100%); 
                        color: white; 
                        padding: 30px 20px; 
                        text-align: center; 
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 28px;
                        font-weight: 600;
                        letter-spacing: 0.5px;
                    }
                    .content { 
                        padding: 40px 30px; 
                        background-color: #ffffff; 
                    }
                    .greeting {
                        font-size: 18px;
                        color: #155386;
                        font-weight: 500;
                        margin-bottom: 20px;
                    }
                    .code-box { 
                        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                        padding: 25px; 
                        text-align: center; 
                        font-size: 42px; 
                        letter-spacing: 8px; 
                        font-weight: 600;
                        border-radius: 8px;
                        margin: 30px 0;
                        font-family: 'Courier New', monospace;
                        color: #155386;
                        border: 1px solid #dee2e6;
                    }
                    .expiry-note {
                        background-color: #fff8e7;
                        padding: 15px;
                        border-radius: 6px;
                        border-left: 4px solid #40798C;
                        margin: 25px 0;
                        font-size: 14px;
                    }
                    .divider {
                        height: 1px;
                        background: linear-gradient(90deg, transparent, #dee2e6, transparent);
                        margin: 30px 0;
                    }
                    .footer { 
                        padding: 25px 30px;
                        background-color: #f8f9fa;
                        border-top: 1px solid #e9ecef;
                        font-size: 13px; 
                        color: #6c757d; 
                        text-align: center; 
                    }
                    .footer p {
                        margin: 5px 0;
                    }
                    .brand-name {
                        font-weight: 600;
                        color: #155386;
                    }
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
                        
                        <div class='code-box'>
                            {$code}
                        </div>
                        
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

        return rtrim(strtr(base64_encode($email), '+/', '-_'), '=');
    }

    /**
     * Test connection to Gmail API
     */
    public function testConnection()
    {
        if (!$this->isConfigured) {
            return "Gmail service is not configured. Please check your .env file.";
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
     * Send status update email to applicant
     */
    public function sendStatusEmail($to, $status, $applicationNumber, $applicantName, $applicationId)
    {
        if (!$this->isConfigured) {
            Log::info('Gmail not configured. Would send status email to: ' . $to . ' for application: ' . $applicationNumber);
            return true;
        }
        
        try {
            // Ensure token is fresh before sending
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            
            $subject = $this->getEmailSubject($status);
            $htmlContent = $this->getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId);
            
            $fromName = "Konstructo";
            $fromEmail = env('EMAIL_USER', 'noreply@konstructo.com');
            
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
            
            $message = new \Google_Service_Gmail_Message();
            $message->setRaw($rawMessage);
            
            $this->service->users_messages->send('me', $message);
            
            Log::info('Status email sent successfully', [
                'to' => $to,
                'status' => $status,
                'application' => $applicationNumber
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send status email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get email subject based on status
     */
    private function getEmailSubject($status)
    {
        return match($status) {
            'approved' => '✓ Building Permit Application Approved - Konstructo',
            'for-release' => '📄 Building Permit Ready for Release - Konstructo',
            'rejected' => '⚠️ Building Permit Application Update - Konstructo',
            'under-review' => '🔍 Your Application is Under Review - Konstructo',
            'verified' => '✅ Documents Verified - Konstructo',
            'pending' => '⏳ Application Received - Konstructo',
            default => 'Building Permit Application Status Update - Konstructo'
        };
    }

    /**
     * Get email content based on status
     */
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        
        return match($status) {
            'approved' => $this->getApprovedEmailContent($applicationNumber, $applicantName, $appUrl),
            'for-release' => $this->getForReleaseEmailContent($applicationNumber, $applicantName, $appUrl),
            'rejected' => $this->getRejectedEmailContent($applicationNumber, $applicantName, $appUrl),
            'under-review' => $this->getUnderReviewEmailContent($applicationNumber, $applicantName, $appUrl),
            'verified' => $this->getVerifiedEmailContent($applicationNumber, $applicantName, $appUrl),
            'pending' => $this->getPendingEmailContent($applicationNumber, $applicantName, $appUrl),
            default => $this->getDefaultEmailContent($applicationNumber, $applicantName, $status, $appUrl)
        };
    }

    /**
     * Approved email template
     */
    private function getApprovedEmailContent($applicationNumber, $applicantName, $appUrl)
    {
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
                    .status-badge { background-color: #28a745; color: white; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #155386; }
                    .section-title { font-size: 18px; font-weight: 600; color: #155386; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; }
                    .step-list { list-style: none; padding: 0; margin: 0; }
                    .step-item { margin: 15px 0; padding-left: 28px; position: relative; }
                    .step-item:before { content: ''; width: 6px; height: 6px; background-color: #155386; border-radius: 50%; position: absolute; left: 8px; top: 10px; }
                    .office-info { background-color: #fff8e7; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #40798C; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; font-size: 15px; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .application-number { font-family: 'Courier New', monospace; font-weight: 600; color: #155386; background-color: #f0f7fa; padding: 2px 8px; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Application Approved</h1></div>
                    <div class='content'>
                        <div class='status-badge'>Approved</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>We are pleased to inform you that your building permit application <span class='application-number'>#{$applicationNumber}</span> has been reviewed and <strong>approved</strong>.</p>
                        
                        <div class='info-section'>
                            <div class='section-title'>Next Steps for Permit Release</div>
                            <h4 style='margin-bottom: 10px; color: #155386;'>Submit to OBO Office:</h4>
                            <div class='office-info'>
                                <p style='margin: 5px 0;'><strong>Office of the Building Official (OBO)</strong></p>
                                <p style='margin: 5px 0;'>City Hall Compound, Legazpi City</p>
                                <p style='margin: 5px 0;'><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                                <p style='margin: 5px 0;'><strong>Contact:</strong> (052) 123-4567</p>
                            </div>
                        </div>
                        
                        <p>Your permit will be available for release within 3-5 business days after submission of hard copy documents.</p>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>Track Application</a>
                        </div>
                        
                        <div class='divider'></div>
                        <p style='font-size: 14px; color: #6c757d;'>Thank you for choosing Konstructo.</p>
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
     * For Release email template
     */
    private function getForReleaseEmailContent($applicationNumber, $applicantName, $appUrl)
    {
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
                    .status-badge { background-color: #155386; color: white; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #155386; }
                    .office-info { background-color: #fff8e7; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #40798C; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Permit Ready for Release</h1></div>
                    <div class='content'>
                        <div class='status-badge'>For Release</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Your building permit <span style='font-weight: 600; color: #155386;'>#{$applicationNumber}</span> is now <strong>ready for release</strong>.</p>
                        
                        <div class='info-section'>
                            <h4 style='margin-top: 0; color: #155386;'>To claim your permit, please bring:</h4>
                            <ul style='margin-bottom: 20px;'>
                                <li>Valid government ID (original and photocopy)</li>
                                <li>Official receipt of payment</li>
                                <li>Authorization letter (if claiming for someone else)</li>
                            </ul>
                            
                            <div class='office-info'>
                                <p style='margin: 5px 0;'><strong>Office of the Building Official (OBO)</strong></p>
                                <p style='margin: 5px 0;'>City Hall Compound, Legazpi City</p>
                                <p style='margin: 5px 0;'><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Details</a>
                        </div>
                        
                        <div class='divider'></div>
                        <p>Congratulations on your approved permit!</p>
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
     * Rejected email template
     */
    private function getRejectedEmailContent($applicationNumber, $applicantName, $appUrl)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .status-badge { background-color: #dc3545; color: white; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #fff8e7; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #dc3545; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Application Update</h1></div>
                    <div class='content'>
                        <div class='status-badge'>Status Update</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Your application <span style='font-weight: 600;'>#{$applicationNumber}</span> requires your attention.</p>
                        
                        <div class='info-section'>
                            <p>Please log in to your dashboard to view the complete details and take necessary action.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Konstructo — Smart Infrastructure Oversight</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Under Review email template
     */
    private function getUnderReviewEmailContent($applicationNumber, $applicantName, $appUrl)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #6f42c1 0%, #6610f2 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .status-badge { background-color: #6f42c1; color: white; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #6f42c1; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Under Review</h1></div>
                    <div class='content'>
                        <div class='status-badge'>Under Review</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Your application <span style='font-weight: 600;'>#{$applicationNumber}</span> is now under review by our team.</p>
                        
                        <div class='info-section'>
                            <p>We will notify you once the review is complete or if additional information is needed.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>Track Progress</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Konstructo — Smart Infrastructure Oversight</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Verified email template
     */
    private function getVerifiedEmailContent($applicationNumber, $applicantName, $appUrl)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .status-badge { background-color: #28a745; color: white; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #f8f9fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #28a745; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Documents Verified</h1></div>
                    <div class='content'>
                        <div class='status-badge'>Verified</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Your documents for application <span style='font-weight: 600;'>#{$applicationNumber}</span> have been verified successfully.</p>
                        
                        <div class='info-section'>
                            <p>Your application will now proceed to the next stage of processing.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Konstructo — Smart Infrastructure Oversight</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Pending email template
     */
    private function getPendingEmailContent($applicationNumber, $applicantName, $appUrl)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .status-badge { background-color: #ffc107; color: #856404; padding: 6px 16px; border-radius: 30px; display: inline-block; font-weight: 600; font-size: 14px; margin-bottom: 25px; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .info-section { background-color: #fff8e7; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #ffc107; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Application Received</h1></div>
                    <div class='content'>
                        <div class='status-badge'>Pending Review</div>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Thank you for submitting your building permit application <span style='font-weight: 600;'>#{$applicationNumber}</span>.</p>
                        
                        <div class='info-section'>
                            <p>Your application has been received and is queued for review. We will notify you once the review begins.</p>
                        </div>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>Track Application</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Konstructo — Smart Infrastructure Oversight</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Default email template for other statuses
     */
    private function getDefaultEmailContent($applicationNumber, $applicantName, $status, $appUrl)
    {
        $statusDisplay = ucfirst(str_replace('-', ' ', $status));
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f5f5f5; }
                    .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
                    .header { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .status-info { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #155386; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Status Update</h1></div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        <p>Your application <span style='font-weight: 600;'>#{$applicationNumber}</span> status has been updated.</p>
                        
                        <div class='status-info'>
                            <p style='margin: 0;'><strong>Current Status:</strong> {$statusDisplay}</p>
                        </div>
                        
                        <p>Please log in to your dashboard to view complete details.</p>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Details</a>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Konstructo — Smart Infrastructure Oversight</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Send missing documents request email to applicant
     */
    public function sendMissingDocumentsEmail($to, $applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        if (!$this->isConfigured) {
            Log::info('Gmail not configured. Would send missing documents email to: ' . $to . ' for application: ' . $applicationNumber);
            return true;
        }
        
        try {
            // Ensure token is fresh before sending
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            
            $subject = 'Action Required: Missing Documents for Your Building Permit Application - Konstructo';
            $htmlContent = $this->getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks);
            
            $fromName = "Konstructo";
            $fromEmail = env('EMAIL_USER', 'noreply@konstructo.com');
            
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
            
            $message = new \Google_Service_Gmail_Message();
            $message->setRaw($rawMessage);
            
            $this->service->users_messages->send('me', $message);
            
            Log::info('Missing documents email sent successfully', [
                'to' => $to,
                'application' => $applicationNumber,
                'document_count' => count($missingDocuments)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send missing documents email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get missing documents email content
     */
    private function getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        
        // Build document list HTML
        $documentListHtml = '';
        foreach ($missingDocuments as $index => $document) {
            $documentListHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #e9ecef; width: 30px; vertical-align: top;'>
                        <span style='display: inline-block; width: 20px; height: 20px; background-color: #dc3545; color: white; border-radius: 50%; text-align: center; line-height: 20px; font-size: 12px; font-weight: bold;'>!</span>
                    </td>
                    <td style='padding: 10px; border-bottom: 1px solid #e9ecef; color: #495057;'>" . htmlspecialchars($document) . "</td>
                </tr>
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
                    .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px 20px; text-align: center; }
                    .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                    .header p { margin: 10px 0 0; font-size: 16px; opacity: 0.9; }
                    .content { padding: 40px 30px; background-color: #ffffff; }
                    .greeting { font-size: 18px; color: #155386; font-weight: 500; margin-bottom: 20px; }
                    .alert-box { background-color: #fff8e7; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #dc3545; }
                    .section-title { font-size: 18px; font-weight: 600; color: #dc3545; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; }
                    .document-table { width: 100%; border-collapse: collapse; margin: 20px 0; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
                    .document-table th { background-color: #f8f9fa; padding: 12px 10px; text-align: left; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; }
                    .instruction-box { background-color: #f0f7fa; padding: 25px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #155386; }
                    .step-list { list-style: none; padding: 0; margin: 0; }
                    .step-item { margin: 15px 0; padding-left: 28px; position: relative; }
                    .step-item:before { content: ''; width: 6px; height: 6px; background-color: #155386; border-radius: 50%; position: absolute; left: 8px; top: 10px; }
                    .deadline-box { background-color: #fff3cd; padding: 20px; border-radius: 6px; border-left: 4px solid #ffc107; margin: 25px 0; }
                    .remarks-box { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; font-style: italic; border: 1px dashed #6c757d; }
                    .button { background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0; font-weight: 600; font-size: 15px; transition: all 0.3s ease; }
                    .button:hover { opacity: 0.9; transform: translateY(-2px); }
                    .divider { height: 1px; background: linear-gradient(90deg, transparent, #dee2e6, transparent); margin: 30px 0; }
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                    .brand-name { font-weight: 600; color: #155386; }
                    .application-number { font-family: 'Courier New', monospace; font-weight: 600; color: #155386; background-color: #f0f7fa; padding: 2px 8px; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Missing Documents Required</h1>
                        <p>Action Needed for Application #{$applicationNumber}</p>
                    </div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>We have reviewed your building permit application and identified that some required documents are missing or incomplete. Please upload the following documents to your Google Drive folder at your earliest convenience.</p>
                        
                        <div class='alert-box'>
                            <div class='section-title'>📋 Missing Documents Checklist</div>
                            
                            <table class='document-table'>
                                <thead>
                                    <tr>
                                        <th style='width: 40px;'></th>
                                        <th>Document Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$documentListHtml}
                                </tbody>
                            </table>
                            
                            <p style='margin-top: 15px; color: #6c757d; font-size: 14px;'>
                                <strong>Total:</strong> " . count($missingDocuments) . " document(s) required
                            </p>
                        </div>
                        
                        " . ($remarks ? "
                        <div class='remarks-box'>
                            <strong style='color: #495057;'>Additional Remarks from Staff:</strong>
                            <p style='margin: 10px 0 0; color: #6c757d;'>" . nl2br(htmlspecialchars($remarks)) . "</p>
                        </div>
                        " : "") . "
                        
                        <div class='instruction-box'>
                            <h4 style='margin-top: 0; color: #155386;'>📤 How to Submit Missing Documents</h4>
                            
                            <ul class='step-list'>
                                <li class='step-item'><strong>Step 1:</strong> Log in to your Konstructo dashboard</li>
                                <li class='step-item'><strong>Step 2:</strong> Go to your application details page</li>
                                <li class='step-item'><strong>Step 3:</strong> Upload the missing documents to your Google Drive folder</li>
                                <li class='step-item'><strong>Step 4:</strong> Ensure files are clearly named (e.g., \"Proof_of_Ownership.pdf\")</li>
                                <li class='step-item'><strong>Step 5:</strong> No need to resubmit - we will automatically detect the new files</li>
                            </ul>
                        </div>
                        
                        <div class='deadline-box'>
                            <strong style='color: #856404;'>⏰ Important Deadline:</strong>
                            <p style='margin: 10px 0 0;'>Please upload the missing documents within <strong>5 business days</strong> to avoid delays in processing your application.</p>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$appUrl}' class='button'>Go to My Application</a>
                        </div>
                        
                        <div style='background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                            <p style='margin: 0; font-size: 14px; color: #495057;'>
                                <strong>Application Reference:</strong> <span class='application-number'>#{$applicationNumber}</span><br>
                                <strong>Status:</strong> Pending Additional Documents
                            </p>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d;'>
                            Once you've uploaded the documents, our staff will be notified and will continue reviewing your application.
                        </p>
                        
                        <p style='font-size: 14px; color: #6c757d;'>
                            If you have any questions, please contact the Office of the Building Official (OBO) during office hours.
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
}