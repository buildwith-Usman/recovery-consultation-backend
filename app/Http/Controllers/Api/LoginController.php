<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // Handle user login
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:8',
            ]);

            // Attempt to authenticate the user
            if (!auth()->attempt($validatedData)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = auth()->user();
            
            // Check if user's email is verified
            if (!$user->is_verified) {
                return response()->json([
                    'message' => 'Please verify your email address before logging in.',
                    'verification_required' => true,
                    'email' => $user->email
                ], 403);
            }
            
            // Generate API token for authenticated user using Passport
            $token = $user->createToken('auth_token')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'data' => array(
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                )
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    // Handle user logout
    public function logout(Request $request)
    {
        try {
            // Revoke all tokens for the authenticated user
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
