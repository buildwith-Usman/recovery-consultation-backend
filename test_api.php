<?php

// Simple test script to verify API endpoints with email verification
echo "Testing Recovery App API with Passport Authentication & Email Verification\n";
echo "=========================================================================\n\n";

// Test data
$testUser = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'type' => 'patient',
    'phone' => '1234567890'
];

$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$verificationData = [
    'email' => 'test@example.com',
    'verification_code' => '12345' // You'll need to get this from the email
];

$resendData = [
    'email' => 'test@example.com'
];

// Base URL (adjust as needed)
$baseUrl = 'http://localhost:8000/api';

function makeRequest($url, $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "1. Testing Registration (with email verification)...\n";
$registerResponse = makeRequest($baseUrl . '/register', $testUser);
echo "Status: " . $registerResponse['status'] . "\n";
echo "Response: " . json_encode($registerResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

echo "2. Testing Login (should fail - email not verified)...\n";
$loginResponse = makeRequest($baseUrl . '/login', $loginData);
echo "Status: " . $loginResponse['status'] . "\n";
echo "Response: " . json_encode($loginResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

echo "3. Testing Resend Verification Code...\n";
$resendResponse = makeRequest($baseUrl . '/email/resend', $resendData);
echo "Status: " . $resendResponse['status'] . "\n";
echo "Response: " . json_encode($resendResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

echo "4. Testing Email Verification (you need to manually set the code)...\n";
echo "NOTE: Check your email for the 5-digit verification code and update \$verificationData above\n";
$verifyResponse = makeRequest($baseUrl . '/email/verify', $verificationData);
echo "Status: " . $verifyResponse['status'] . "\n";
echo "Response: " . json_encode($verifyResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

if (isset($verifyResponse['data']['access_token'])) {
    $token = $verifyResponse['data']['access_token'];
    
    echo "5. Testing Login (should work after verification)...\n";
    $loginResponse2 = makeRequest($baseUrl . '/login', $loginData);
    echo "Status: " . $loginResponse2['status'] . "\n";
    echo "Response: " . json_encode($loginResponse2['data'], JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($loginResponse2['data']['access_token'])) {
        $token = $loginResponse2['data']['access_token'];
    }
    
    echo "6. Testing Protected Route (Get User)...\n";
    $userResponse = makeRequest($baseUrl . '/user', null, $token);
    echo "Status: " . $userResponse['status'] . "\n";
    echo "Response: " . json_encode($userResponse['data'], JSON_PRETTY_PRINT) . "\n\n";
    
    echo "7. Testing Logout...\n";
    $logoutResponse = makeRequest($baseUrl . '/logout', [], $token);
    echo "Status: " . $logoutResponse['status'] . "\n";
    echo "Response: " . json_encode($logoutResponse['data'], JSON_PRETTY_PRINT) . "\n\n";
}

echo "=== Email Verification Flow Test Complete! ===\n";
echo "NOTES:\n";
echo "- Check your email for verification codes\n";
echo "- Update the verification code in this script to test email verification\n";
echo "- Configure SMTP settings in .env file for actual email sending\n";
