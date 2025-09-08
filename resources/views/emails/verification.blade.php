<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Recovery App</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .content {
            padding: 50px 40px;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .user-name {
            font-size: 24px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 30px;
        }
        
        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .verification-code-container {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
        }
        
        .verification-code-label {
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 500;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .verification-code {
            font-size: 48px;
            font-weight: bold;
            color: white;
            letter-spacing: 8px;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            font-family: 'Courier New', monospace;
        }
        
        .expiry-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            color: #856404;
            font-size: 14px;
        }
        
        .expiry-notice strong {
            color: #d63031;
        }
        
        .instructions {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .instructions h3 {
            color: #333;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #666;
        }
        
        .instructions li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .app-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .app-info h4 {
            color: #667eea;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .security-notice {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #0c5460;
            font-size: 13px;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 15px;
            }
            
            .content {
                padding: 30px 25px;
            }
            
            .header {
                padding: 30px 25px;
            }
            
            .verification-code {
                font-size: 36px;
                letter-spacing: 4px;
            }
            
            .footer {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">R</div>
            <h1>Recovery App</h1>
        </div>
        
        <div class="content">
            <div class="welcome-text">Welcome to Recovery App!</div>
            <div class="user-name">{{ $user->name }}</div>
            
            <p class="message">
                Thank you for registering with Recovery App. To complete your account setup and start your journey towards better mental health, please verify your email address using the verification code below.
            </p>
            
            <div class="verification-code-container">
                <div class="verification-code-label">Your Verification Code</div>
                <div class="verification-code">{{ $verificationCode }}</div>
            </div>
            
            <div class="expiry-notice">
                ⏰ <strong>Important:</strong> This verification code will expire in 15 minutes for your security.
            </div>
            
            <div class="instructions">
                <h3>How to verify your account:</h3>
                <ol>
                    <li>Open the Recovery App on your device</li>
                    <li>Navigate to the email verification screen</li>
                    <li>Enter the 5-digit code shown above</li>
                    <li>Click "Verify Email" to complete the process</li>
                </ol>
            </div>
            
            <div class="security-notice">
                🔒 <strong>Security Note:</strong> If you didn't create an account with Recovery App, please ignore this email. Your security is our top priority.
            </div>
        </div>
        
        <div class="footer">
            <div class="app-info">
                <h4>Recovery App</h4>
                <p>Your trusted partner in mental health and wellness recovery.</p>
                <p>Connecting patients with qualified therapists and psychiatrists.</p>
            </div>
            
            <p style="margin-top: 20px;">
                This is an automated email. Please do not reply to this message.<br>
                If you need assistance, please contact our support team.
            </p>
            
            <p style="margin-top: 15px; font-size: 12px; color: #adb5bd;">
                © {{ date('Y') }} Recovery App. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
