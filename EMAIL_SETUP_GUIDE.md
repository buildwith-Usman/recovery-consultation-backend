# Email Configuration Guide for Recovery App

## Overview
This guide will help you configure SMTP settings for sending email verification codes in the Recovery App.

## Supported Email Providers

### 1. Gmail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

**Setup Steps for Gmail:**
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → App passwords
   - Generate a new app password for "Mail"
   - Use this password in `MAIL_PASSWORD`

### 2. Outlook/Hotmail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

### 3. Yahoo Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yahoo.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

### 4. SendGrid Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

### 5. Mailgun Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

### 6. Amazon SES Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-username
MAIL_PASSWORD=your-ses-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@recoveryapp.com"
MAIL_FROM_NAME="Recovery App"
```

## Configuration Steps

### 1. Update Environment File
Copy the appropriate configuration from above to your `.env` file:

```bash
cp .env.example .env
# Edit .env file with your SMTP credentials
```

### 2. Test Email Configuration
Use Laravel's tinker to test email sending:

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

### 3. Verify Email Template
The email verification template is located at:
`resources/views/emails/verification.blade.php`

## Email Verification Flow

### 1. User Registration
- User registers with email and password
- System generates 5-digit verification code
- Verification email is sent automatically
- User account remains unverified until code is entered

### 2. Email Verification
- User receives email with 5-digit code
- Code expires after 15 minutes
- User submits code via API
- Upon successful verification, user gets access token

### 3. Resend Verification
- If code expires, user can request new code
- New 5-digit code is generated and sent
- Previous code becomes invalid

## API Endpoints

### Register User
```
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "type": "patient",
  "phone": "1234567890"
}
```

**Response:**
```json
{
  "message": "User registered successfully. Please check your email for verification code.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "type": "patient",
    "phone": "1234567890",
    "is_verified": false
  },
  "verification_required": true
}
```

### Verify Email
```
POST /api/email/verify
Content-Type: application/json

{
  "email": "john@example.com",
  "verification_code": "12345"
}
```

**Response:**
```json
{
  "message": "Email verified successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "type": "patient",
    "phone": "1234567890",
    "is_verified": true,
    "email_verified_at": "2025-08-03T14:30:00.000000Z"
  },
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "token_type": "Bearer"
}
```

### Resend Verification Code
```
POST /api/email/resend
Content-Type: application/json

{
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "message": "Verification code resent successfully. Please check your email."
}
```

### Login (After Verification)
```
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (if verified):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "type": "patient",
    "phone": "1234567890"
  },
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "token_type": "Bearer"
}
```

**Response (if not verified):**
```json
{
  "message": "Please verify your email address before logging in.",
  "verification_required": true,
  "email": "john@example.com"
}
```

## Troubleshooting

### Common Issues

#### 1. "Connection refused" Error
- Check SMTP host and port settings
- Verify firewall settings
- Ensure TLS/SSL settings are correct

#### 2. "Authentication failed" Error
- Verify username/password credentials
- For Gmail, ensure you're using App Password, not regular password
- Check if 2FA is enabled and configured properly

#### 3. "SSL Certificate verify failed" Error
- Try changing `MAIL_ENCRYPTION` from `ssl` to `tls`
- Or disable SSL verification (not recommended for production)

#### 4. Emails going to spam
- Configure SPF, DKIM, and DMARC records for your domain
- Use a reputable email service provider
- Ensure proper "From" email address

### Testing Commands

#### Test Mail Configuration
```bash
php artisan tinker
```

```php
use App\Mail\EmailVerificationMail;
use App\Models\User;

$user = User::first();
$code = '12345';
Mail::to('test@example.com')->send(new EmailVerificationMail($user, $code));
```

#### Clear Config Cache
```bash
php artisan config:clear
php artisan config:cache
```

#### View Mail Configuration
```bash
php artisan tinker
```

```php
config('mail');
```

## Security Best Practices

1. **Use App Passwords**: Never use your main email password
2. **Environment Variables**: Keep credentials in `.env` file, never commit to version control
3. **Rate Limiting**: Implement rate limiting for verification code requests
4. **Code Expiry**: Verification codes expire after 15 minutes
5. **HTTPS**: Always use HTTPS in production
6. **Email Validation**: Validate email format before sending

## Production Considerations

1. **Use Professional Email Service**: SendGrid, Mailgun, or Amazon SES
2. **Domain Authentication**: Set up SPF, DKIM, and DMARC
3. **Monitoring**: Monitor email delivery rates and bounces
4. **Queue Processing**: Use Laravel queues for email sending in production
5. **Error Logging**: Log email failures for debugging

## Queue Configuration (Recommended for Production)

Update your `.env` for queue processing:
```env
QUEUE_CONNECTION=database
```

Then run:
```bash
php artisan queue:work
```

Make your email implement `ShouldQueue`:
```php
class EmailVerificationMail extends Mailable implements ShouldQueue
{
    // ... existing code
}
```
