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
            
            $subject = 'Verify Your Email Address';
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
        $greeting = $firstName ? "Hi " . $firstName . "," : "Hello,";
        
        $htmlContent = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { padding: 30px; background-color: #f9f9f9; border-radius: 0 0 10px 10px; }
                    .code-box { 
                        background-color: #e9e9e9; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 36px; 
                        letter-spacing: 8px; 
                        font-weight: bold;
                        border-radius: 5px;
                        margin: 20px 0;
                        font-family: monospace;
                    }
                    .footer { font-size: 12px; color: #666; text-align: center; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to Konstructo!</h1>
                    </div>
                    <div class='content'>
                        <h2>Email Verification</h2>
                        <p>{$greeting}</p>
                        <p>Thank you for signing up! Please verify your email address using the code below:</p>
                        <div class='code-box'>
                            {$code}
                        </div>
                        <p><strong>This code will expire in 10 minutes.</strong></p>
                        <p>If you didn't request this, please ignore this email.</p>
                        <p>Thanks,<br>The Konstructo Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
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
            return "✅ Connected as: " . $profile->getEmailAddress();
        } catch (\Exception $e) {
            return "❌ Connection failed: " . $e->getMessage();
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
        'approved' => 'Your Building Permit Application has been APPROVED',
        'for-release' => 'Your Building Permit is READY FOR RELEASE',
        default => 'Building Permit Application Status Update'
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
 * Approved email template
 */
private function getApprovedEmailContent($applicationNumber, $applicantName, $appUrl)
{
    return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background-color: #f9f9f9; border-radius: 0 0 10px 10px; }
                .success-badge { background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 50px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                .next-steps { background-color: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0; }
                .step-item { margin: 10px 0; padding-left: 25px; position: relative; }
                .step-item:before { content: '✓'; color: #4CAF50; font-weight: bold; position: absolute; left: 0; }
                .fee-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .fee-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .fee-table tr:last-child td { border-bottom: none; }
                .button { background-color: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; font-weight: bold; }
                .button:hover { background-color: #45a049; }
                .footer { font-size: 12px; color: #666; text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
                .office-info { background-color: #fff3e0; padding: 15px; border-radius: 8px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Application Approved!</h1>
                </div>
                <div class='content'>
                    <div class='success-badge'>APPROVED</div>
                    
                    <h2>Hello {$applicantName},</h2>
                    
                    <p>Great news! Your building permit application <strong>#{$applicationNumber}</strong> has been <strong style='color: #4CAF50;'>APPROVED</strong>.</p>
                    
                    <div class='next-steps'>
                        <h3 style='margin-top: 0; color: #1976d2;'>📋 Next Steps:</h3>
                        
                        <div class='step-item'><strong>Step 1:</strong> Prepare the following hard copy documents:</div>
                        <ul style='margin-left: 35px;'>
                            <li>All 13 required documents (original copies with signatures)</li>
                            <li>Printed copy of your application form</li>
                            <li>Valid government ID (2 copies)</li>
                            <li>Proof of payment (if already paid)</li>
                        </ul>
                        
                        <div class='step-item'><strong>Step 2:</strong> Prepare payment for the following fees:</div>
                        
                        <table class='fee-table'>
                            <tr><td>Filing Fee OBO</td><td style='text-align: right; font-weight: bold;'>₱100</td></tr>
                            <tr><td>BFP</td><td style='text-align: right; font-weight: bold;'>₱200</td></tr>
                            <tr style='border-top: 2px solid #4CAF50; font-weight: bold;'>
                                <td>TOTAL</td>
                                <td style='text-align: right;'>₱300</td>
                            </tr>
                        </table>
                        
                        <p style='font-size: 0.9em; color: #666;'>*Additional Fess will be based on new schedule of fees and other charges of the National Building Code of the Philippines and Fire Code of the Philippines.</p>
                        
                        <div class='step-item'><strong>Step 3:</strong> Submit to OBO Office:</div>
                        
                        <div class='office-info'>
                            <p style='margin: 5px 0;'><strong>📍 Office of the Building Official (OBO)</strong></p>
                            <p style='margin: 5px 0;'>City Hall Compound, Legazpi City</p>
                            <p style='margin: 5px 0;'><strong>Office Hours:</strong> Monday-Friday, 8:00 AM - 5:00 PM</p>
                            <p style='margin: 5px 0;'><strong>Contact:</strong> (052) 123-4567</p>
                        </div>
                    </div>
                    
                    <p style='background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>
                        <strong>⏱️ Processing Time:</strong> Your permit will be ready for release within 3-5 business days after hard copy submission and payment.
                    </p>
                    
        
                    <p>Thank you for choosing Konstructo!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
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
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2196F3 0%, #1976d2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background-color: #f9f9f9; border-radius: 0 0 10px 10px; }
                .success-badge { background-color: #2196F3; color: white; padding: 10px 20px; border-radius: 50px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                .claim-details { background-color: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0; }
                .checklist { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .checklist-item { margin: 10px 0; padding-left: 30px; position: relative; }
                .checklist-item:before { content: '✓'; color: #4CAF50; font-weight: bold; position: absolute; left: 0; font-size: 18px; }
                .button { background-color: #2196F3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; font-weight: bold; }
                .button:hover { background-color: #1976d2; }
                .footer { font-size: 12px; color: #666; text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
                .reminder { background-color: #fff3e0; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ff9800; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Ready for Release!</h1>
                </div>
                <div class='content'>
                    <div class='success-badge'>FOR RELEASE</div>
                    
                    <h2>Hello {$applicantName},</h2>
                    
                    <p>Good news! Your building permit <strong>#{$applicationNumber}</strong> is now <strong style='color: #2196F3;'>READY FOR RELEASE</strong>.</p>
                    
                    <div class='claim-details'>
                        <h3 style='margin-top: 0; color: #1976d2;'>📦 Claim Your Permit</h3>
                        
                        <div class='checklist'>
                            <h4 style='margin-top: 0;'>Please bring the following:</h4>
                            <div class='checklist-item'>Valid government ID (original and photocopy)</div>
                            <div class='checklist-item'>Official receipt of payment</div>
                            <div class='checklist-item'>Application form (printed copy)</div>
                            <div class='checklist-item'>Authorization letter (if claiming for someone else)</div>
                        </div>
                        
                        <p style='margin-top: 20px;'><strong>📍 Office of the Building Official (OBO)</strong><br>
                        City Hall Compound, Legazpi City<br>
                        <strong>Office Hours:</strong> Monday-Friday, 8:00 AM - 5:00 PM</p>
                    </div>
                    
                    <div class='reminder'>
                        <strong>⚠️ Important Reminders:</strong>
                        <ul style='margin: 10px 0 0 20px;'>
                            <li>Your permit is valid for one (1) year from date of release</li>
                            <li>Construction must commence within one year</li>
                            <li>Keep the permit posted at the construction site at all times</li>
                            <li>Regular inspections will be conducted during construction</li>
                        </ul>
                    </div>
                    
                    <p><strong>Upon claiming, you will receive:</strong></p>
                    <ul>
                        <li>Official Building Permit document</li>
                        <li>Approved plans (stamped and signed)</li>
                        <li>Certificate of Completion (if applicable)</li>
                        <li>Inspection schedule</li>
                    </ul>
                    
                    <p>Congratulations on your approved permit!</p>
                    <p>Best regards,<br>The Konstructo Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
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
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #757575 0%, #616161 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background-color: #f9f9f9; border-radius: 0 0 10px 10px; }
                .button { background-color: #155386; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; font-weight: bold; }
                .footer { font-size: 12px; color: #666; text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Status Update</h1>
                </div>
                <div class='content'>
                    <h2>Hello {$applicantName},</h2>
                    
                    <p>Your building permit application <strong>#{$applicationNumber}</strong> status has been updated to: <strong>{$statusDisplay}</strong>.</p>
                    
                    <p>Please log in to your dashboard to view more details about this update.</p>
                    
                    <p>Thank you for using Konstructo!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";
}
}