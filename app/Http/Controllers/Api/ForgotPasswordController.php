<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'If your email exists in our system, you will receive a password reset code.'
            ], 200);
        }

        $resetCode = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        $user->password_reset_code = $resetCode;
        $user->password_reset_code_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        Mail::to($user->email)->send(new PasswordResetMail($user, $resetCode));
        return response()->json([
            'message' => 'Kindly check your email, you will receive a password reset code.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'reset_code' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->password_reset_code || $user->password_reset_code !== $request->input('reset_code')) {
                return response()->json(['message' => 'Invalid reset code.'], 400);
            }
            if (Carbon::now()->isAfter($user->password_reset_code_expires_at)) {
                return response()->json(['message' => 'Reset code has expired. Please request a new one.'], 400);
            }

            $user->password = Hash::make($request->password);
            $user->password_reset_code = null;
            $user->password_reset_code_expires_at = null;
            $user->save();

            return response()->json(['message' => 'Password has been reset successfully.'], 200);
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
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
