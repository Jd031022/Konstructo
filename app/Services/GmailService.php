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
        
        // Personalize greeting
        $greeting = $firstName ? "Dear " . $firstName . "," : "Dear Valued User,";
        
        $htmlContent = "
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
                    .footer { padding: 25px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; font-size: 13px; color: #6c757d; text-align: center; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'><h1>Konstructo</h1></div>
                    <div class='content'>
                        <div class='greeting'>{$greeting}</div>
                        <p>Thank you for registering with Konstructo. Please verify your email address using the code below:</p>
                        <div class='code-box'>{$code}</div>
                        <p>This code will expire in 10 minutes.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

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
        $subject = 'Action Required: Missing Documents for Your Building Permit Application - Konstructo';
        $htmlContent = $this->getMissingDocumentsEmailContent($applicationNumber, $applicantName, $missingDocuments, $applicationId, $remarks);
        
        return $this->sendEmail($to, $subject, $htmlContent);
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
            'document-verification' => '📋 Document Verification in Progress - Konstructo',
            default => 'Building Permit Application Status Update - Konstructo'
        };
    }

    /**
     * Get email content based on status
     */
    private function getStatusEmailContent($status, $applicationNumber, $applicantName, $applicationId)
    {
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        
        $statusDisplay = ucfirst(str_replace('-', ' ', $status));
        
        // Simplified template for brevity - you can use your full templates here
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                    .header { background: #155386; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { padding: 20px; }
                    .status { color: #155386; font-weight: bold; }
                    .button { background: #155386; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Application Status Update</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$applicantName},</p>
                        <p>Your application <strong>#{$applicationNumber}</strong> status has been updated to: <span class='status'>{$statusDisplay}</span></p>
                        <p>Click the button below to view your application details:</p>
                        <p style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </p>
                        <p>Thank you for using Konstructo.</p>
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
        $appUrl = env('APP_URL', 'http://localhost:8000') . "/applicant/application-details/{$applicationId}";
        
        $documentList = '<ul>';
        foreach ($missingDocuments as $doc) {
            $documentList .= "<li>" . htmlspecialchars($doc) . "</li>";
        }
        $documentList .= '</ul>';
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                    .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .content { padding: 20px; }
                    .button { background: #155386; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Missing Documents Required</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$applicantName},</p>
                        <p>Your application <strong>#{$applicationNumber}</strong> requires the following documents:</p>
                        {$documentList}
                        " . ($remarks ? "<p><strong>Remarks:</strong> {$remarks}</p>" : "") . "
                        <p>Please upload these documents to your Google Drive folder.</p>
                        <p style='text-align: center;'>
                            <a href='{$appUrl}' class='button'>View Application</a>
                        </p>
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