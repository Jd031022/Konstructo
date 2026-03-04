<?php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;

class GmailService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        try {
            $refreshToken = env('GOOGLE_REFRESH_TOKEN');
            
            if (empty($refreshToken)) {
                throw new \Exception('Refresh token is empty');
            }
            
            $this->client = new Google_Client();
            $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
            $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            
            $this->client->refreshToken($refreshToken);
            
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken();
            }
            
            $this->service = new Google_Service_Gmail($this->client);
            
        } catch (\Exception $e) {
            Log::error('GmailService error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Gmail service: ' . $e->getMessage());
        }
    }

    /**
     * Send verification email with personalized name
     * 
     * @param string $to Recipient email
     * @param string $code Verification code
     * @param string $firstName Recipient's first name (optional)
     * @return bool
     */
    public function sendVerificationEmail($to, $code, $firstName = null)
    {
        try {
            // Ensure token is fresh
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

    private function createEmailContent($to, $subject, $code, $firstName = null)
{
    $fromName = "Konstructo";
    $fromEmail = env('EMAIL_USER');
    
    $greeting = $firstName ? "Hi " . ucfirst($firstName) . "," : "Hello,";
    
    $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background-color: #4CAF50; color: white; padding: 20px; text-align: center;'>
                    <h1>Welcome to Konstructo!</h1>
                </div>
                <div style='padding: 20px; background-color: #f9f9f9;'>
                    <h2>Email Verification</h2>
                    <p>{$greeting}</p>
                    <p>Thank you for signing up! Please verify your email address using the code below:</p>
                    <div style='background-color: #e9e9e9; padding: 20px; text-align: center; font-size: 36px; letter-spacing: 8px; font-weight: bold; border-radius: 5px; margin: 20px 0;'>
                        {$code}
                    </div>
                    <p><strong>This code will expire in 10 minutes.</strong></p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <p>Thanks,</p>
                    <p>The Konstructo Team</p>
                </div>
                <div style='font-size: 12px; color: #666; text-align: center; margin-top: 20px;'>
                    <p>&copy; " . date('Y') . " Konstructo. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";

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
}