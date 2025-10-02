<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorInfo;
use App\Models\PatientInfo;
use App\Models\User;
use App\Models\UserLanguage;
use App\Models\UserQuestionnaire;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $user = User::with('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'reviews', 'file')->where('id', $user->id)->first();
        return response()->json(['data' => ['user' => $user]]);
    }
    public function update_profile(Request $request)
    {
        try {
            $user = auth()->user();

            // Update the patient's profile
            if ($user->type === "patient") {
                $this->update_patient($request);
            }

            // Update the doctor's profile
            if ($user->type === "doctor") {
                $this->update_doctor($request);
            }

            // Update Questionnaires
            $questionnaires = @$request->input('questionnaires');
            if ($questionnaires) {
                $this->add_questionnaires($request);
            }

            // Update Languages
            $this->change_user_language($request);

            // Get user with relations
            $user = User::with('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'reviews')->where('id', $user->id)->first();

            $user->update([
                'name' => $request->name ?? $user->name,
                'phone' => $request->phone ?? $user->phone,
                'profile_image_id' => $request->file_id ?? $user->profile_image_id,
            ]);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => ['user' => $user]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function update_patient($data)
    {
        $user = auth()->user();
        $patient = PatientInfo::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = PatientInfo::create([
                'user_id' => $user->id,
                'looking_for' => $data['looking_for'] ?? null,
                'completed' => (int) $data['completed'] ?? 0,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'age' => $data['age'] ?? 0,
                'blood_group' => $data['blood_group'] ?? null,
                'reason' => $data['reason']
            ]);
        } else {
            $patient->update([
                'looking_for' => $data['looking_for'] ?? $patient->looking_for,
                'completed' => (int) $data['completed'] ?? (int) $patient->completed,
                'dob' => $data['dob'] ?? $patient->dob,
                'gender' => $data['gender'] ?? $patient->gender,
                'age' => $data['age'] ?? $patient->age,
                'blood_group' => $data['blood_group'] ?? $patient->blood_group,
                'reason' => $data['reason'] ?? $patient->reason
            ]);
        }

        return $patient;
    }

    public function update_doctor($data)
    {
        $user = auth()->user();
        $doctor = DoctorInfo::where('user_id', $user->id)->first();

        if (!$doctor) {
            $doctor = DoctorInfo::create([
                'user_id' => $user->id,
                'specialization' => $data['specialization'],
                'experience' => $data['experience'],
                'dob' => $data['dob'],
                'degree' => $data['degree'],
                'license_no' => $data['license_no'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'gender' => $data['gender'] ?? null,
                'age' => $data['age'],
                'approved' => $data['approved'] ?? false,
                'completed' => (int) $data['completed'] ?? 0
            ]);
        } else {
            $doctor->update([
                'specialization' => $data['specialization'] ?? $doctor->specialization,
                'experience' => $data['experience'] ?? $doctor->experience,
                'dob' => $data['dob'] ?? $doctor->dob,
                'degree' => $data['degree'] ?? $doctor->degree,
                'license_no' => $data['license_no'] ?? $doctor->license_no,
                'country_id' => $data['country_id'] ?? $doctor->country_id,
                'gender' => $data['gender'] ?? $doctor->gender,
                'age' => $data['age'] ?? $doctor->age,
                'approved' => $data['approved'] ?? $doctor->approved,
                'completed' => (int) $data['completed'] ?? (int) $doctor->completed
            ]);
        }

        return $doctor;
    }

    public function change_user_language(Request $request)
    {
        $languages = $request->input('languages');
        $user = auth()->user();
        UserLanguage::where('user_id', $user->id)->delete();

        if (isset($languages)) {
            foreach ($languages as $language) {
                UserLanguage::create([
                    'user_id' => $user->id,
                    'language' => $language
                ]);
            }
        }
    }

    public function add_questionnaires(Request $request)
    {
        $user = auth()->user();
        $questionnaires = $request->input('questionnaires');

        try {

            if (!$questionnaires || !is_array($questionnaires)) {
                return response()->json([
                    'message' => 'No questionnaires provided',
                    'errors' => ['No questionnaires provided']
                ], 400);
            }

            UserQuestionnaire::where('user_id', $user->id)->delete();

            // Validate and add new questionnaires
            foreach ($questionnaires as $questionnaire) {
                UserQuestionnaire::create([
                    'user_id' => $user->id,
                    'question' => $questionnaire['question'],
                    'options' => implode(',', $questionnaire['options']),
                    'answer' => is_array($questionnaire['answer']) ? implode(',', $questionnaire['answer']) : $questionnaire['answer'],
                    'key' => $questionnaire['key']
                ]);
            }

            $user = User::find($user->id);
            if ($user->type === "patient") {
                $patientInfo = $user->patientInfo;
                $patientInfo->completed = 1;
                $patientInfo->save();
            }

            if ($user->type === "doctor") {
                $doctorInfo = $user->doctorInfo;
                $doctorInfo->completed = 1;
                $doctorInfo->save();
            }

            // Get the users all questionnaires
            $questionnaires = UserQuestionnaire::where('user_id', $user->id)->get();

            return response()->json([
                'message' => 'Questionnaires added successfully',
                "data" => [
                    'questionnaires' => $questionnaires
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function doctor_details(Request $request)
    {
        $id = $request->input('id');
        try {
            $doctor = User::with([
                'doctorInfo',
                'questionnaires',
                'userLanguages',
                'reviews' => function ($query) {
                    $query->latest()->limit(5);
                }
            ])
                ->withCount('doc_appointments')
                ->where([
                    'id' => $id,
                    'type' => 'doctor'
                ])
                ->first();
            $doctor->total_rating = $doctor->reviews->avg('rating') ?? 0;
            return response()->json([
                "message" => "Doctor retrieved successfully",
                "data" => $doctor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve doctor',
                'errors' => [$e->getMessage()]
            ], 500);
        } catch (\Throwable $th) {
            // Log the error or handle it as needed
            return response()->json([
                "message" => "Failed to retrieve doctor",
                "errors" => [$th->getMessage()]
            ], 500);
        }
    }

    public function appointments(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $purpose = $request->input('purpose');
        $user = auth()->user();

        try {
            $appointments = Appointment::with(['patient', 'doctor'])
                ->where(function ($q) use ($user, $purpose) {

                    if ($user->type === "doctor") {
                        $q->where('doc_user_id', $user->id);
                    } else if ($user->type === "patient") {
                        $q->where('pat_user_id', $user->id);
                    }

                    if ($purpose) {
                        $q->where('status', $purpose);
                    }
                })
                ->orderBy('id', 'desc')->paginate($limit);

            return response()->json([
                "message" => "Appointment list",
                "data" => $appointments->items(),
                "pagination" => [
                    "total" => $appointments->total(),
                    "current_page" => $appointments->currentPage(),
                    "per_page" => $appointments->perPage(),
                    "last_page" => $appointments->lastPage(),
                    "from" => $appointments->firstItem(),
                    "to" => $appointments->lastItem()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function reviews(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $user = auth()->user();

        try {
            $reviews = $user->reviews()->with(['sender', 'receiver'])->latest()->paginate($limit);

            return response()->json([
                "message" => "Reviews list",
                "data" => $reviews->items(),
                "pagination" => [
                    "total" => $reviews->total(),
                    "current_page" => $reviews->currentPage(),
                    "per_page" => $reviews->perPage(),
                    "last_page" => $reviews->lastPage(),
                    "from" => $reviews->firstItem(),
                    "to" => $reviews->lastItem()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve reviews',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}