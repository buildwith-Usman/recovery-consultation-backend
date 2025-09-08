<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset - Recovery App</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #e0e0e0; padding: 32px; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { color: #667eea; }
        .code { font-size: 2.5em; color: #f5576c; letter-spacing: 8px; margin: 24px 0; text-align: center; font-family: 'Courier New', monospace; }
        .footer { margin-top: 32px; text-align: center; color: #888; font-size: 0.95em; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Password Reset</h1>
        <p>Recovery App</p>
    </div>
    <p>Hello {{ $user->name }},</p>
    <p>We received a request to reset your password. Use the code below to reset it. This code will expire in 15 minutes.</p>
    <div class="code">{{ $resetCode }}</div>
    <p>If you did not request a password reset, you can safely ignore this email.</p>
    <div class="footer">
        &copy; {{ date('Y') }} Recovery App. All rights reserved.
    </div>
</div>
</body>
</html>
