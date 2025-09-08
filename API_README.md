# Recovery App API Documentation

## Overview
This API provides authentication and user management functionality for the Recovery App using Laravel Passport for OAuth2 authentication and email verification with 5-digit codes.

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses Laravel Passport (OAuth2) for authentication. Users must verify their email before they can log in and receive access tokens.

### Authorization Header
```
Authorization: Bearer {access_token}
```

## Registration & Verification Flow

1. **Register** → User provides details, receives verification email
2. **Email Verification** → User enters 5-digit code from email
3. **Login** → User can now log in and receive access token
4. **Protected Routes** → Use access token for authenticated requests

## Endpoints

### 1. User Registration
**POST** `/api/register`

Register a new user and send email verification code.

#### Request Body
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "type": "patient",
  "phone": "1234567890"
}
```

#### Parameters
- `name` (string, required): User's full name
- `email` (string, required): Valid email address, must be unique
- `password` (string, required): Minimum 8 characters
- `password_confirmation` (string, required): Must match password
- `type` (string, required): User type - `admin`, `patient`, or `doctor`
- `phone` (string, optional): Phone number, max 15 characters

#### Response (201 Created)
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

### 2. Email Verification
**POST** `/api/email/verify`

Verify user email with 5-digit code and receive access token.

#### Request Body
```json
{
  "email": "john@example.com",
  "verification_code": "12345"
}
```

#### Parameters
- `email` (string, required): User's email address
- `verification_code` (string, required): 5-digit code from email

#### Response (200 OK)
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

### 3. Resend Verification Code
**POST** `/api/email/resend`

Request a new verification code if the previous one expired.

#### Request Body
```json
{
  "email": "john@example.com"
}
```

#### Parameters
- `email` (string, required): User's email address

#### Response (200 OK)
```json
{
  "message": "Verification code resent successfully. Please check your email."
}
```

### 4. User Login
**POST** `/api/login`

Authenticate a verified user and receive an access token.

#### Request Body
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Parameters
- `email` (string, required): User's email address
- `password` (string, required): User's password

#### Response (200 OK) - Verified User
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

#### Response (403 Forbidden) - Unverified User
```json
{
  "message": "Please verify your email address before logging in.",
  "verification_required": true,
  "email": "john@example.com"
}
```

### 5. Get Authenticated User
**GET** `/api/user`

Get information about the currently authenticated user.

#### Headers
```
Authorization: Bearer {access_token}
```

#### Response (200 OK)
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "type": "patient",
    "phone": "1234567890",
    "is_verified": true,
    "created_at": "2025-08-03T13:20:00.000000Z",
    "updated_at": "2025-08-03T13:20:00.000000Z"
  }
}
```

### 6. User Logout
**POST** `/api/logout`

Revoke the current user's access tokens.

#### Headers
```
Authorization: Bearer {access_token}
```

#### Response (200 OK)
```json
{
  "message": "Logout successful"
}
```

## Email Verification Details

### Verification Code
- **Format**: 5-digit numeric code (e.g., "12345")
- **Expiry**: 15 minutes from generation
- **Security**: Each code is unique and single-use

### Email Design
The verification email includes:
- **Professional design** with Recovery App branding
- **Responsive layout** for mobile and desktop
- **Clear instructions** for verification process
- **Security notice** and expiry information
- **Gradient styling** and modern UI elements

### SMTP Configuration
Configure your `.env` file with SMTP settings:

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

## Error Responses

### Validation Error (422 Unprocessable Entity)
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "verification_code": ["The verification code must be exactly 5 characters."]
  }
}
```

### Authentication Error (401 Unauthorized)
```json
{
  "message": "Invalid credentials"
}
```

### Verification Errors

#### Invalid Code (400 Bad Request)
```json
{
  "message": "Invalid verification code"
}
```

#### Expired Code (400 Bad Request)
```json
{
  "message": "Verification code has expired. Please request a new one."
}
```

#### Already Verified (400 Bad Request)
```json
{
  "message": "Email is already verified"
}
```

### Server Error (500 Internal Server Error)
```json
{
  "message": "Registration failed",
  "error": "Error details here"
}
```

## User Types
- `admin`: Administrator with full system access
- `patient`: Patients seeking healthcare services
- `doctor`: Healthcare providers (therapists, psychiatrists)

## Token Expiration
- Access tokens expire in 15 days
- Refresh tokens expire in 30 days
- Personal access tokens expire in 6 months
- Verification codes expire in 15 minutes

## Testing

### Postman Collection
Import the provided Postman collection: `Recovery_App_API.postman_collection.json`

### PHP Test Script
Run the PHP test script: `php test_api.php`

### Example curl commands:

#### Register a new user:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "type": "patient",
    "phone": "1234567890"
  }'
```

#### Verify email:
```bash
curl -X POST http://localhost:8000/api/email/verify \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "verification_code": "12345"
  }'
```

#### Resend verification code:
```bash
curl -X POST http://localhost:8000/api/email/resend \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com"
  }'
```

#### Login:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

## Setup Instructions

### 1. Configure Email
1. Update `.env` file with SMTP credentials
2. See `EMAIL_SETUP_GUIDE.md` for detailed configuration

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Install Passport and Register Client
```bash
php artisan passport:install
php artisan passport:client --personal
```

### 4. Start Server
```bash
php artisan serve
```

## Security Features

1. **Email Verification**: Required before login
2. **Code Expiry**: 15-minute expiration for security
3. **Password Hashing**: Bcrypt encryption
4. **OAuth2 Tokens**: Secure JWT tokens
5. **Rate Limiting**: Prevents spam and abuse
6. **Input Validation**: Comprehensive request validation

## Notes
- All requests should include `Content-Type: application/json` header
- Protected endpoints require valid Bearer token in Authorization header
- Verification codes are case-sensitive and must be exactly 5 digits
- Users cannot log in until their email is verified
- Email addresses must be unique across the system
