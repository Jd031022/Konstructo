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

    public function __construct()
    {
        try {
            $refreshToken = env('GOOGLE_REFRESH_TOKEN');
            
            if (empty($refreshToken)) {
                throw new \Exception('Refresh token is empty. Check your .env file.');
            }
            
            // Log token info for debugging (remove in production)
            Log::info('Token prefix: ' . substr($refreshToken, 0, 10) . '...');
            
            $this->client = new Google_Client();
            $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
            $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt('force');
            
            // IMPORTANT: Set the refresh token correctly
            $this->client->refreshToken($refreshToken);
            
            // Fetch a new access token
            $newToken = $this->client->fetchAccessTokenWithRefreshToken();
            
            // Check for errors
            if (isset($newToken['error'])) {
                throw new \Exception('Token refresh failed: ' . json_encode($newToken));
            }
            
            $this->service = new Google_Service_Gmail($this->client);
            
        } catch (\Exception $e) {
            Log::error('GmailService constructor error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Gmail service: ' . $e->getMessage());
        }
    }

    /**
     * Send verification email with personalized name
     */
    public function sendVerificationEmail($to, $code, $firstName = null)
    {
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
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create email content with proper formatting
     */
    private function createEmailContent($to, $subject, $code, $firstName = null)
    {
        $fromName = "Konstructo";
        $fromEmail = env('EMAIL_USER');
        
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
        try {
            // Ensure token is fresh before sending
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            
            $subject = $this->getEmailSubject($status);
            $htmlContent = $this->getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId);
            
            $fromName = "Konstructo";
            $fromEmail = env('EMAIL_USER');
            
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

    private function getEmailSubject($status)
    {
        return match($status) {
            'approved' => 'Building Permit Application Approved - Konstructo',
            'for-release' => 'Building Permit Ready for Release - Konstructo',
            default => 'Building Permit Application Status Update - Konstructo'
        };
    }

    /**
     * Get email content based on status
     */
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/applications/{$applicationId}";
        
        $content = match($status) {
            'approved' => $this->getApprovedEmailContent($applicationNumber, $applicantName, $appUrl),
            'for-release' => $this->getForReleaseEmailContent($applicationNumber, $applicantName, $appUrl),
            default => $this->getDefaultEmailContent($applicationNumber, $applicantName, $status, $appUrl)
        };
        
        return $content;
    }

    /**
     * Approved email template - Corporate Design
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
                    }
                    .content { 
                        padding: 40px 30px; 
                        background-color: #ffffff; 
                    }
                    .status-badge { 
                        background-color: #28a745; 
                        color: white; 
                        padding: 6px 16px; 
                        border-radius: 30px; 
                        display: inline-block; 
                        font-weight: 600; 
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        margin-bottom: 25px; 
                    }
                    .greeting {
                        font-size: 18px;
                        color: #155386;
                        font-weight: 500;
                        margin-bottom: 20px;
                    }
                    .info-section { 
                        background-color: #f8f9fa; 
                        padding: 25px; 
                        border-radius: 8px; 
                        margin: 25px 0; 
                        border-left: 4px solid #155386;
                    }
                    .section-title {
                        font-size: 18px;
                        font-weight: 600;
                        color: #155386;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 1px solid #dee2e6;
                    }
                    .step-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    .step-item { 
                        margin: 15px 0; 
                        padding-left: 28px; 
                        position: relative; 
                    }
                    .step-item:before { 
                        content: ''; 
                        width: 6px;
                        height: 6px;
                        background-color: #155386;
                        border-radius: 50%;
                        position: absolute; 
                        left: 8px; 
                        top: 10px;
                    }
                    .fee-table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin: 20px 0; 
                        background-color: white;
                        border-radius: 6px;
                        overflow: hidden;
                    }
                    .fee-table td { 
                        padding: 12px; 
                        border-bottom: 1px solid #e9ecef; 
                    }
                    .fee-table tr:last-child td { 
                        border-bottom: none; 
                    }
                    .fee-table tr:last-child {
                        background-color: #f8f9fa;
                        font-weight: 600;
                    }
                    .fee-table td:last-child {
                        text-align: right;
                        font-weight: 500;
                    }
                    .office-info { 
                        background-color: #fff8e7; 
                        padding: 20px; 
                        border-radius: 6px; 
                        margin: 20px 0; 
                        border-left: 4px solid #40798C; 
                    }
                    .reminder-box {
                        background-color: #fff8e7;
                        padding: 20px;
                        border-radius: 6px;
                        border-left: 4px solid #ffc107;
                        margin: 25px 0;
                    }
                    .button { 
                        background: linear-gradient(135deg, #155386 0%, #40798C 100%); 
                        color: white; 
                        padding: 14px 30px; 
                        text-decoration: none; 
                        border-radius: 6px; 
                        display: inline-block; 
                        margin: 20px 0; 
                        font-weight: 600; 
                        font-size: 15px;
                        transition: all 0.3s ease;
                    }
                    .button:hover { 
                        opacity: 0.9;
                        transform: translateY(-2px);
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
                    .brand-name {
                        font-weight: 600;
                        color: #155386;
                    }
                    .application-number {
                        font-family: 'Courier New', monospace;
                        font-weight: 600;
                        color: #155386;
                        background-color: #f0f7fa;
                        padding: 2px 8px;
                        border-radius: 4px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Application Approved</h1>
                    </div>
                    <div class='content'>
                        <div class='status-badge'>Approved</div>
                        
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>We are pleased to inform you that your building permit application <span class='application-number'>#{$applicationNumber}</span> has been reviewed and <strong>approved</strong>.</p>
                        
                        <div class='info-section'>
                            <div class='section-title'>Next Steps for Permit Release</div>
                            
                            <h4 style='margin-bottom: 10px; color: #155386;'>Step 1: Prepare Required Documents</h4>
                            <ul class='step-list' style='margin-bottom: 25px;'>
                                <li class='step-item'>Original hard copies of all 13 submitted documents with signatures</li>
                                <li class='step-item'>Printed copy of the application form</li>
                                <li class='step-item'>Valid government ID (original and 2 photocopies)</li>
                                <li class='step-item'>Official receipt of payment (if applicable)</li>
                            </ul>
                            
                            <h4 style='margin-bottom: 10px; color: #155386;'>Step 2: Pay Required Fees</h4>
                            <table class='fee-table'>
                                <tr><td>Filing Fee - Office of the Building Official (OBO)</td><td>₱100.00</td></tr>
                                <tr><td>Fire Safety Inspection Fee - BFP</td><td>₱200.00</td></tr>
                                <tr><td><strong>Total Amount Due</strong></td><td><strong>₱300.00</strong></td></tr>
                            </table>
                            <p style='font-size: 13px; color: #6c757d; margin-top: 5px;'>*Additional fees may apply based on the current schedule of fees under the National Building Code and Fire Code of the Philippines.</p>
                            
                            <h4 style='margin-bottom: 10px; color: #155386;'>Step 3: Submit to OBO Office</h4>
                            <div class='office-info'>
                                <p style='margin: 5px 0;'><strong>Office of the Building Official (OBO)</strong></p>
                                <p style='margin: 5px 0;'>City Hall Compound, Legazpi City</p>
                                <p style='margin: 5px 0;'><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                                <p style='margin: 5px 0;'><strong>Contact Number:</strong> (052) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class='reminder-box'>
                            <strong style='color: #856404;'>Processing Time:</strong> Your permit will be available for release within 3-5 business days after submission of hard copy documents and payment confirmation.
                        </div>
                        
                        <p>Upon release, you will receive an email notification. You may track your application status through your Konstructo dashboard.</p>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button' style='color: white; text-decoration: none; display: inline-block;'>Track Application Status</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p style='font-size: 14px; color: #6c757d;'>Thank you for choosing Konstructo for your permitting needs.</p>
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
     * For Release email template - Corporate Design
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
                    }
                    .content { 
                        padding: 40px 30px; 
                        background-color: #ffffff; 
                    }
                    .status-badge { 
                        background-color: #155386; 
                        color: white; 
                        padding: 6px 16px; 
                        border-radius: 30px; 
                        display: inline-block; 
                        font-weight: 600; 
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        margin-bottom: 25px; 
                    }
                    .greeting {
                        font-size: 18px;
                        color: #155386;
                        font-weight: 500;
                        margin-bottom: 20px;
                    }
                    .info-section { 
                        background-color: #f8f9fa; 
                        padding: 25px; 
                        border-radius: 8px; 
                        margin: 25px 0; 
                        border-left: 4px solid #155386;
                    }
                    .section-title {
                        font-size: 18px;
                        font-weight: 600;
                        color: #155386;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 1px solid #dee2e6;
                    }
                    .checklist {
                        background-color: white;
                        padding: 20px;
                        border-radius: 6px;
                        margin: 20px 0;
                        border: 1px solid #e9ecef;
                    }
                    .checklist-item { 
                        margin: 12px 0; 
                        padding-left: 28px; 
                        position: relative; 
                    }
                    .checklist-item:before { 
                        content: ''; 
                        width: 6px;
                        height: 6px;
                        background-color: #28a745;
                        border-radius: 50%;
                        position: absolute; 
                        left: 8px; 
                        top: 10px;
                    }
                    .office-info { 
                        background-color: #fff8e7; 
                        padding: 20px; 
                        border-radius: 6px; 
                        margin: 20px 0; 
                        border-left: 4px solid #40798C; 
                    }
                    .reminder-box {
                        background-color: #fff8e7;
                        padding: 20px;
                        border-radius: 6px;
                        border-left: 4px solid #ffc107;
                        margin: 25px 0;
                    }
                    .doc-list {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 10px;
                        margin: 15px 0;
                    }
                    .doc-item {
                        background-color: #f8f9fa;
                        padding: 10px;
                        border-radius: 4px;
                        font-size: 13px;
                        border: 1px solid #e9ecef;
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
                    .brand-name {
                        font-weight: 600;
                        color: #155386;
                    }
                    .application-number {
                        font-family: 'Courier New', monospace;
                        font-weight: 600;
                        color: #155386;
                        background-color: #f0f7fa;
                        padding: 2px 8px;
                        border-radius: 4px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Permit Ready for Release</h1>
                    </div>
                    <div class='content'>
                        <div class='status-badge'>For Release</div>
                        
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>We are pleased to inform you that your building permit <span class='application-number'>#{$applicationNumber}</span> is now <strong>ready for release</strong>.</p>
                        
                        <div class='info-section'>
                            <div class='section-title'>Claim Your Permit</div>
                            
                            <div class='checklist'>
                                <h4 style='margin-top: 0; color: #155386;'>Documents to Present:</h4>
                                <div class='checklist-item'>Valid government ID (original and photocopy)</div>
                                <div class='checklist-item'>Official receipt of payment</div>
                                <div class='checklist-item'>Printed application form</div>
                                <div class='checklist-item'>Authorization letter (if claiming on behalf of another person)</div>
                            </div>
                            
                            <div class='office-info'>
                                <p style='margin: 5px 0;'><strong>Office of the Building Official (OBO)</strong></p>
                                <p style='margin: 5px 0;'>City Hall Compound, Legazpi City</p>
                                <p style='margin: 5px 0;'><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                                <p style='margin: 5px 0;'><strong>Contact Number:</strong> (052) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class='reminder-box'>
                            <strong style='color: #856404;'>Important Reminders:</strong>
                            <ul style='margin: 10px 0 0 20px; padding-left: 0;'>
                                <li style='margin: 5px 0;'>Permit is valid for one (1) year from date of release</li>
                                <li style='margin: 5px 0;'>Construction must commence within the validity period</li>
                                <li style='margin: 5px 0;'>Keep the permit posted at the construction site at all times</li>
                                <li style='margin: 5px 0;'>Regular inspections will be conducted during construction</li>
                            </ul>
                        </div>
                        
                        <h4 style='color: #155386; margin: 20px 0 10px;'>You will receive the following documents:</h4>
                        <div class='doc-list'>
                            <div class='doc-item'>Building Permit (Original)</div>
                            <div class='doc-item'>Approved Architectural Plans</div>
                            <div class='doc-item'>Approved Structural Plans</div>
                            <div class='doc-item'>Approved Sanitary/Plumbing Plans</div>
                            <div class='doc-item'>Approved Electrical Plans</div>
                            <div class='doc-item'>Certificate of Completion</div>
                            <div class='doc-item'>Inspection Schedule</div>
                            <div class='doc-item'>Official Receipt</div>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$appUrl}' class='button' style='background: linear-gradient(135deg, #155386 0%, #40798C 100%); color: white; padding: 14px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;'>View Application Details</a>
                        </div>
                        
                        <div class='divider'></div>
                        
                        <p>Congratulations on the approval of your building permit. Should you have any questions, please contact the OBO office during office hours.</p>
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
     * Default email template for other statuses - Corporate Design
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
                        background: linear-gradient(135deg, #6c757d 0%, #495057 100%); 
                        color: white; 
                        padding: 30px 20px; 
                        text-align: center; 
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 28px;
                        font-weight: 600;
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
                    .status-info {
                        background-color: #f8f9fa;
                        padding: 20px;
                        border-radius: 8px;
                        margin: 25px 0;
                        border-left: 4px solid #155386;
                    }
                    .button { 
                        background: linear-gradient(135deg, #155386 0%, #40798C 100%); 
                        color: white; 
                        padding: 14px 30px; 
                        text-decoration: none; 
                        border-radius: 6px; 
                        display: inline-block; 
                        margin: 20px 0; 
                        font-weight: 600; 
                        transition: all 0.3s ease;
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
                    .brand-name {
                        font-weight: 600;
                        color: #155386;
                    }
                    .application-number {
                        font-family: 'Courier New', monospace;
                        font-weight: 600;
                        color: #155386;
                        background-color: #f0f7fa;
                        padding: 2px 8px;
                        border-radius: 4px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Status Update</h1>
                    </div>
                    <div class='content'>
                        <div class='greeting'>Dear {$applicantName},</div>
                        
                        <p>This is to inform you that your building permit application <span class='application-number'>#{$applicationNumber}</span> status has been updated.</p>
                        
                        <div class='status-info'>
                            <p style='margin: 0;'><strong>Current Status:</strong> {$statusDisplay}</p>
                        </div>
                        
                        <p>Please log in to your Konstructo dashboard to view complete details regarding this update.</p>
                        
                        <div style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application Details</a>
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
 * Send missing documents request email to applicant
 */
public function sendMissingDocumentsEmail($to, $applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks = null)
{
    try {
        // Ensure token is fresh before sending
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken();
        }
        
        $subject = 'Action Required: Missing Documents for Your Building Permit Application - Konstructo';
        $htmlContent = $this->getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks);
        
        $fromName = "Konstructo";
        $fromEmail = env('EMAIL_USER');
        
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
    $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/applications/{$applicationId}";
    
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
                    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 600;
                }
                .header p {
                    margin: 10px 0 0;
                    font-size: 16px;
                    opacity: 0.9;
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
                .alert-box { 
                    background-color: #fff8e7; 
                    padding: 25px; 
                    border-radius: 8px; 
                    margin: 25px 0; 
                    border-left: 4px solid #dc3545;
                }
                .section-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #dc3545;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #dee2e6;
                }
                .document-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                    background-color: white;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                }
                .document-table th {
                    background-color: #f8f9fa;
                    padding: 12px 10px;
                    text-align: left;
                    font-weight: 600;
                    color: #495057;
                    border-bottom: 2px solid #dee2e6;
                }
                .instruction-box {
                    background-color: #f0f7fa;
                    padding: 25px;
                    border-radius: 8px;
                    margin: 25px 0;
                    border-left: 4px solid #155386;
                }
                .step-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                .step-item { 
                    margin: 15px 0; 
                    padding-left: 28px; 
                    position: relative; 
                }
                .step-item:before { 
                    content: ''; 
                    width: 6px;
                    height: 6px;
                    background-color: #155386;
                    border-radius: 50%;
                    position: absolute; 
                    left: 8px; 
                    top: 10px;
                }
                .deadline-box {
                    background-color: #fff3cd;
                    padding: 20px;
                    border-radius: 6px;
                    border-left: 4px solid #ffc107;
                    margin: 25px 0;
                }
                .remarks-box {
                    background-color: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    margin: 20px 0;
                    font-style: italic;
                    border: 1px dashed #6c757d;
                }
                .button { 
                    background: linear-gradient(135deg, #155386 0%, #40798C 100%); 
                    color: white; 
                    padding: 14px 30px; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    display: inline-block; 
                    margin: 20px 0; 
                    font-weight: 600; 
                    font-size: 15px;
                    transition: all 0.3s ease;
                }
                .button:hover { 
                    opacity: 0.9;
                    transform: translateY(-2px);
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
                .brand-name {
                    font-weight: 600;
                    color: #155386;
                }
                .application-number {
                    font-family: 'Courier New', monospace;
                    font-weight: 600;
                    color: #155386;
                    background-color: #f0f7fa;
                    padding: 2px 8px;
                    border-radius: 4px;
                }
                .drive-link {
                    background-color: #e8f5e9;
                    padding: 15px;
                    border-radius: 6px;
                    text-align: center;
                    margin: 20px 0;
                }
                .drive-link a {
                    color: #155386;
                    font-weight: 600;
                    text-decoration: none;
                }
                .drive-link a:hover {
                    text-decoration: underline;
                }
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
                            <li class='step-item'><strong>Step 1:</strong> Access your Google Drive folder using the link below</li>
                            <li class='step-item'><strong>Step 2:</strong> Upload the missing documents in the appropriate format (PDF, JPG, PNG)</li>
                            <li class='step-item'><strong>Step 3:</strong> Ensure files are clearly named (e.g., \"Proof_of_Ownership.pdf\")</li>
                            <li class='step-item'><strong>Step 4:</strong> Organize documents in the correct folder structure</li>
                            <li class='step-item'><strong>Step 5:</strong> No need to resubmit - we will automatically detect the new files</li>
                        </ul>
                        
                        <div class='drive-link'>
                            <svg width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='#155386' stroke-width='2' style='vertical-align: middle; margin-right: 8px;'>
                                <path d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/>
                                <path d='M14 2v6h6M16 13H8M16 17H8M10 9H8'/>
                            </svg>
                            <a href='#' id='drive-link-placeholder' onclick='event.preventDefault();'>Your Google Drive Folder</a>
                            <p style='margin: 5px 0 0; font-size: 13px; color: #6c757d;'>Access your dedicated folder through your Konstructo dashboard</p>
                        </div>
                    </div>
                    
                    <div class='deadline-box'>
                        <strong style='color: #856404;'>⏰ Important Deadline:</strong>
                        <p style='margin: 10px 0 0;'>Please upload the missing documents within <strong>5 business days</strong> to avoid delays in processing your application. If you need more time, please contact the OBO office.</p>
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
                        Once you've uploaded the documents, our staff will be notified and will continue reviewing your application. You can track the status in real-time through your dashboard.
                    </p>
                    
                    <p style='font-size: 14px; color: #6c757d;'>
                        If you have any questions or need assistance, please contact the Office of the Building Official (OBO) during office hours.
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