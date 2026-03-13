<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code - Konstructo</title>
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
        .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
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
            text-align: center;
        }
        .code-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .code-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .code {
            font-size: 48px;
            font-weight: 700;
            color: #155386;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            line-height: 1.2;
        }
        .expiry-note {
            background-color: #fff8e7;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #40798C;
            margin: 25px 0;
            font-size: 14px;
            text-align: left;
        }
        .security-notice {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
            margin: 25px 0;
            font-size: 14px;
            text-align: left;
        }
        .security-notice strong {
            color: #dc3545;
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
        .footer p {
            margin: 5px 0;
        }
        .brand-name {
            font-weight: 600;
            color: #155386;
        }
        .contact-info {
            font-size: 12px;
            color: #adb5bd;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Konstructo</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Password Reset Request
            </div>
            
            <p style="text-align: center; margin-bottom: 25px;">We received a request to reset the password associated with your Konstructo account. To proceed with the password reset process, please use the verification code below:</p>
            
            <div class="code-container">
                <div class="code-label">Verification Code</div>
                <div class="code">{{ $code }}</div>
            </div>
            
            <div class="expiry-note">
                <strong>Note:</strong> This verification code will expire in <strong>15 minutes</strong> for security purposes. If you do not complete the password reset within this timeframe, you will need to request a new code.
            </div>
            
            <div class="security-notice">
                <strong>Security Notice:</strong> For your protection, never share this verification code with anyone. Our support team will never ask for your password or verification codes.
            </div>
            
            <p style="text-align: center; margin: 25px 0 15px;">If you did not initiate this password reset request, please take the following steps:</p>
            
            <ul style="color: #6c757d; font-size: 14px; margin-bottom: 25px;">
                <li>Ignore this email - no changes have been made to your account</li>
                <li>Ensure your account security by logging in and reviewing recent activity</li>
                <li>Contact our support team immediately if you notice any unauthorized access</li>
            </ul>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/contact" class="button" style="color: white; text-decoration: none;">Contact Support</a>
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #6c757d; text-align: center;">
                For additional assistance, please visit our support center or email us at support@konstructo.com
            </p>
        </div>
        
        <div class="footer">
            <p class="brand-name">Konstructo — Smart Infrastructure Oversight</p>
            <p>&copy; {{ date('Y') }} Konstructo. All rights reserved.</p>
            <p>Office of the Building Official | Legazpi City, Philippines</p>
            <div class="contact-info">
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>