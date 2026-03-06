
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #0f766e;
            letter-spacing: 8px;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 10px;
            margin: 20px 0;
            font-family: monospace;
        }
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #f0f0f0;
            font-size: 12px;
            color: #666;
        }
        .warning {
            color: #dc2626;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Use full URL to your logo -->
         <img src="{{ asset('images/logo.png') }}" alt="Konstructo Logo" class="logo">
        <h1 style="color: #0f766e; margin: 0;">Konstructo</h1>
    </div>
    
    <div class="content">
        <h2>Password Reset Request</h2>
        <p>We received a request to reset your password. Use the 6-digit code below to proceed:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>This code will expire in <strong>15 minutes</strong>.</p>
        
        <p>If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
        
        <div class="warning">
            <strong>⚠️ Security Notice:</strong> Never share this code with anyone.
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Konstructo. All rights reserved.</p>
        <p>This is an automated message, please do not reply.</p>
    </div>
</body>
</html>