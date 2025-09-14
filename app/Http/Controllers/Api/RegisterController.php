<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorInfo;
use App\Models\UserLanguage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'type' => 'required|string|in:patient,doctor',
                'phone' => 'nullable|string|max:15'
            ]);

            // Generate 5-digit verification code
            $verificationCode = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

            $user = User::where([
                'email' => $validatedData['email'],
            ])->first();


            // Create the user
            if($user) {

                if($user->is_verified === true) {
                    return response()->json([
                        'message' => 'Email is already taken.',
                        'errors' => ['Email is already taken.'],
                    ], 422);
                }

                $user->update([
                    'name' => $validatedData['name'],
                    'password' => Hash::make($validatedData['password']),
                    'type' => $validatedData['type'],
                    'phone' => $validatedData['phone'] ?? null,
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires_at' => Carbon::now()->addMinutes(15), // 15 minutes expiry
                    'is_verified' => false,
                ]);
                $user = User::find($user->id);

                if($user->type === "doctor") {
                    $this->createOrUpdateDoctorInfo($user, $request->all());
                }

            } else {
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                    'type' => $validatedData['type'],
                    'phone' => $validatedData['phone'] ?? null,
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires_at' => Carbon::now()->addMinutes(15), // 15 minutes expiry
                    'is_verified' => false,
                ]);
                if($user->type === "doctor") {
                    $this->createOrUpdateDoctorInfo($user, $request->all());
                }
            }

            // Send verification email
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationCode));

            // Don't log the user in automatically, they need to verify first
            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification code.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => $user->type,
                        'phone' => $user->phone,
                        'is_verified' => $user->is_verified,
                    ],
                    'verification_required' => true,
                ]
            ], 201);

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

    // This function is a placeholder for patient registration logic
    // You can implement the logic to handle patient-specific registration here
    // For now, it does nothing but can be extended later
    public function createOrUpdateDoctorInfo($user, $validatedData) {
        $doctorInfo = DoctorInfo::where('user_id', $user->id)->first();

        if ($doctorInfo) {
            $doctorInfo->update([
                'specialization' => $validatedData['specialization'] ?? null,
                'experience' => $validatedData['experience'] ?? null,
                'dob' => $validatedData['dob'] ?? null,
                'age' => $validatedData['age'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'degree' => $validatedData['degree'] ?? null,
                'license_no' => $validatedData['license_no'] ?? null,
            ]);

            // Update languages
            if(isset($validatedData['languages'])) {
                foreach ($validatedData['languages'] as $language) {
                    UserLanguage::updateOrCreate(
                        ['user_id' => $user->id, 'language' => $language],
                        ['language' => $language]
                    );
                }
            }
        } else {
            $doctorInfo = DoctorInfo::create([
                'user_id' => $user->id,
                'specialization' => $validatedData['specialization'] ?? null,
                'experience' => $validatedData['experience'] ?? null,
                'dob' => $validatedData['dob'] ?? null,
                'age' => $validatedData['age'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'degree' => $validatedData['degree'] ?? null,
                'license_no' => $validatedData['license_no'] ?? null,
            ]);
            
            if(isset($validatedData['languages'])) {
                foreach ($validatedData['languages'] as $language) {
                    UserLanguage::create([
                        'user_id' => $user->id,
                        'language' => $language,
                    ]);
                }
            }
        }
        return $doctorInfo;
    }
}
