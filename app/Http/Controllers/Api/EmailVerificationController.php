<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'type' => 'required|string|in:signup,forgot',
                'email' => 'required|string|email',
                'verification_code' => 'required|string|size:5',
            ]);

            // Find user by email
            $user = User::where('email', $validatedData['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if($validatedData['type'] === 'forgot') {
                if (!$user || !$user->password_reset_code || $user->password_reset_code !== $validatedData['verification_code']) {
                    return response()->json(['message' => 'Invalid reset code.'], 400);
                }
                if (Carbon::now()->isAfter($user->password_reset_code_expires_at)) {
                    return response()->json(['message' => 'Reset code has expired. Please request a new one.'], 400);
                }

                return response()->json([
                    'message' => 'Reset password code is validated.',
                    'data' => array (
                        'user' => [
                            'id' => null,
                            'name' => null,
                            'email' => $user->email,
                            'type' => null,
                            'phone' => null,
                            'is_verified' => null,
                            'email_verified_at' => null,
                        ],
                        'access_token' => null,
                        'token_type' => null
                    )
                ], 200);
            }

            // Check if user is already verified
            if ($user->is_verified) {
                return response()->json([
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Check if verification code matches
            if ($user->email_verification_code !== $validatedData['verification_code']) {
                return response()->json([
                    'message' => 'Invalid verification code'
                ], 400);
            }

            // Check if verification code has expired
            if (Carbon::now()->isAfter($user->email_verification_code_expires_at)) {
                return response()->json([
                    'message' => 'Verification code has expired. Please request a new one.'
                ], 400);
            }

            // Verify the user
            $user->update([
                'is_verified' => true,
                'email_verified_at' => Carbon::now(),
                'email_verification_code' => null,
                'email_verification_code_expires_at' => null,
            ]);

            // Generate access token for the verified user
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'message' => 'Email verified successfully',
                'data' => array (
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'phone' => $user->phone,
                        'is_verified' => $user->is_verified,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorsList = [];
            foreach($e->errors() as $err) {
                $errorsList = array_merge($errorsList, $err);
            }
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorsList
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Email verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resend(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'type' => 'required|string|in:signup,forgot',
                'email' => 'required|string|email',
            ]);

            // Find user by email
            $user = User::where('email', $validatedData['email'])->first();

            // Generate new 5-digit verification code
            $verificationCode = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if($validatedData['type'] === 'forgot') {
                $user->password_reset_code = $verificationCode;
                $user->password_reset_code_expires_at = Carbon::now()->addMinutes(15);
                $user->save();

                Mail::to($user->email)->send(new PasswordResetMail($user, $verificationCode));

                return response()->json([
                    'message' => 'Kindly check your email, you will receive a password reset code.'
                ], 200);
            }

            // Check if user is already verified
            if ($user->is_verified) {
                return response()->json([
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Update user with new verification code
            $user->email_verification_code = $verificationCode;
            $user->email_verification_code_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            // Send verification email
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationCode));

            return response()->json([
                'message' => 'Verification code resent successfully. Please check your email.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorsList = [];
            foreach($e->errors() as $err) {
                $errorsList = array_merge($errorsList, $err);
            }
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errorsList
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to resend verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
