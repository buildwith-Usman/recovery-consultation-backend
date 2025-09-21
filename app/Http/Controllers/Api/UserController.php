<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorInfo;
use App\Models\PatientInfo;
use App\Models\User;
use App\Models\UserLanguage;
use App\Models\UserQuestionnaire;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $user = auth()->user();
        $user = User::with('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'reviews')->where('id', $user->id)->first();
        return response()->json(['data' => ['user' => $user]]);
    }
    public function update_profile(Request $request) {
        try {
            $user = auth()->user();

            // Update the patient's profile
            if($user->type === "patient") {
                $this->update_patient($request);
            }

            // Update the doctor's profile
            if($user->type === "doctor") {
                $this->update_doctor($request);
            }

            // Update Questionnaires
            $questionnaires = @$request->input('questionnaires');
            if($questionnaires) {
                $this->add_questionnaires($request);
            }

            // Update Languages
            $this->change_user_language($request);

            // Get user with relations
            $user = User::with('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'reviews')->where('id', $user->id)->first();
            
            $user->update([
                'name' => $request->name ?? $user->name,
                'phone' => $request->phone ?? $user->phone,
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

    public function update_patient($data) {
        $user = auth()->user();
        $patient = PatientInfo::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = PatientInfo::create([
                'user_id' => $user->id,
                'looking_for' => $data['looking_for'] ?? null,
                'completed' => (int)$data['completed'] ?? 0,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'age' => $data['age'] ?? 0,
                'blood_group' => $data['blood_group'] ?? null,
                'reason' => $data['reason']
            ]);
        } else {
            $patient->update([
                'looking_for' => $data['looking_for'] ?? $patient->looking_for,
                'completed' => (int)$data['completed'] ?? (int)$patient->completed,
                'dob' => $data['dob'] ?? $patient->dob,
                'gender' => $data['gender'] ?? $patient->gender,
                'age' => $data['age'] ?? $patient->age,
                'blood_group' => $data['blood_group'] ?? $patient->blood_group,
                'reason' => $data['reason'] ?? $patient->reason
            ]);
        }

        return $patient;
    }

    public function update_doctor($data) {
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
                'completed' => (int)$data['completed'] ?? 0
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
                'completed' => (int)$data['completed'] ?? (int)$doctor->completed
            ]);
        }

        return $doctor;
    }

    public function change_user_language(Request $request) {
        $languages = $request->input('languages');
        $user = auth()->user();
        UserLanguage::where('user_id', $user->id)->delete();
        
        if(isset($languages)) {
            foreach($languages as $language) {
                UserLanguage::create([
                    'user_id' => $user->id,
                    'language' => $language
                ]);
            }
        }
    }

    public function match_doctors_list(Request $request) {
        $user = User::with(['patientInfo', 'questionnaires'])->where('id', auth()->user()->id)->first();

        if($user->type !== "patient") {
            return response()->json([
                'message' => 'User is not valid!',
                'data' => [
                    'doctors' => []
                ]
            ], 404);
        }

        $looking_for = $user->patientInfo->looking_for ?? null;

        $gender_prefer_data = $user->questionnaires
            ->where('key', 'gender_prefer')
            ->pluck('answer')->first();

        $age_group_prefer = $user->questionnaires
            ->where('key', 'age_group_prefer')
            ->pluck('answer')->first();

        $age_prefer = $user->questionnaires
            ->where('key', 'age_prefer')
            ->pluck('answer')->first();
        $min_age_prefer = null;
        $max_age_prefer = null;

        if(str_contains($age_prefer, '-')) {
            $age_prefer_arr = explode('-', $age_prefer);
            $min_age_prefer = (int)$age_prefer_arr[0];
            $max_age_prefer = (int)$age_prefer_arr[1];
        } else if(str_contains($age_prefer, '+')) {
            $min_age_prefer = (int)str_replace('+', '', $age_prefer);
        } else if(str_contains($age_prefer, '<')) {
            $max_age_prefer = (int)str_replace('<', '', $age_prefer);
        }

        $lang_prefer = $user->questionnaires
            ->where('key', 'lang_prefer')
            ->pluck('answer')->first();

        $help_support = $user->questionnaires
            ->where('key', 'help_support')
            ->pluck('answer')->first();

        $doctors = User::with(['doctorInfo', 'questionnaires'])
        ->whereHas('doctorInfo', function ($q) use($looking_for, $gender_prefer_data, $min_age_prefer, $max_age_prefer) {
            $q->where('specialization', $looking_for);
            if($gender_prefer_data) {
                $q->where('gender', strtolower($gender_prefer_data));
            }
            if($min_age_prefer && $max_age_prefer) {
                $q->whereBetween('age', [(int)$min_age_prefer, (int)$max_age_prefer]);
            } else if($min_age_prefer && empty($max_age_prefer)) {
                $q->where('age', '>=', $min_age_prefer);
            } else if(empty($min_age_prefer) && $max_age_prefer) {
                $q->where('age', '<=', $max_age_prefer);
            }
            $q->where('approved', 1);
        })
        ->whereHas('questionnaires', function ($q) use($age_group_prefer, $help_support) {
            $q->where(function ($query) use ($age_group_prefer, $help_support) {
                if($age_group_prefer) {
                    $query->orWhere('key', 'age_group_prefer')
                        ->whereRaw('FIND_IN_SET(?, answer)', [$age_group_prefer]);
                }
                if($help_support) {
                    $helpSupportArr = explode(',', $help_support);
                    foreach ($helpSupportArr as $support) {
                        $query->orWhere(function ($sub) use ($support) {
                            $sub->where('key', 'help_support')
                                ->whereRaw('FIND_IN_SET(?, answer)', [$support]);
                        });
                    }
                }
            });
        })
        ->whereHas('userLanguages', function($q) use($lang_prefer) {
            $languageArr = explode(',', $lang_prefer);
            $q->whereIn('language', $languageArr);
        })
        ->where('type', 'doctor')
        ->where('is_verified', true)
        ->get();

        return response()->json([
            'message' => 'Doctors ('.$looking_for.') list.',
            'data' => [
                'doctors' => $doctors
            ]
        ]);
    }

    public function add_questionnaires(Request $request) {
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
                    'options' => implode(',',$questionnaire['options']),
                    'answer' => is_array($questionnaire['answer']) ? implode(',', $questionnaire['answer']) : $questionnaire['answer'],
                    'key' => $questionnaire['key']
                ]);
            }

            $user = User::find($user->id);
            if($user->type === "patient") {
                $patientInfo = $user->patientInfo;
                $patientInfo->completed = 1;
                $patientInfo->save();
            }

            if($user->type === "doctor") {
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
}