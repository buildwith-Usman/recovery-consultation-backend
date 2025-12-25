<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorInfo;
use App\Models\PatientInfo;
use App\Models\User;
use App\Models\UserLanguage;
use App\Models\UserQuestionnaire;
use App\Models\UserReview;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $user_relational_array = [
        'patientInfo',
        'doctorInfo',
        'questionnaires',
        'userLanguages',
        'file',
        'available_times',
        'timeSlots'
    ];
    public function index(Request $request)
    {
        $user = auth()->user();
        $user = User::with(array_merge($this->user_relational_array, ['reviews' => function ($query) {
            $query->latest()->limit(5);
        }]))
        ->when($user->type === 'doctor', function ($q) {
            $q->withCount('distinctPatients');
        })
        ->where('id', $user->id)->first();
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
            $user = User::where('id', $user->id)->first();

            $user->update([
                'name' => $request->name ?? $user->name,
                'phone' => $request->phone ?? $user->phone,
                'profile_image_id' => $request->file_id ? $request->file_id : $user->profile_image_id,
                'bio' => $request->bio ?? $user->bio,
            ]);

            $user = User::with($this->user_relational_array)->where('id', $user->id)->first();
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
                'status' => $data['status'] ?? false,
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
                'status' => $data['status'] ?? $doctor->status,
                'completed' => (int) $data['completed'] ?? (int) $doctor->completed
            ]);
        }

        return $doctor;
    }

    public function change_user_language(Request $request)
    {
        $languages = $request->input('languages');
        $user = auth()->user();
        if (isset($languages)) {
            UserLanguage::where('user_id', $user->id)->delete();
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

    public function user_details(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::with(array_merge(
                $this->user_relational_array, 
                [
                    'reviews' => function ($query) {
                        $query->latest()->limit(5);
                    }
                ]
                ))
                ->withCount('doc_appointments')
                ->where([
                    'id' => $id,
                ])
                ->first();
            $user->total_rating = $user->reviews->avg('rating') ?? 0;
            return response()->json([
                "message" => "Doctor retrieved successfully",
                "data" => $user
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

    public function reviews(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $user_id = $request->input('user_id') ?? 10;
        $user = auth()->user();

        try {
            $reviews = UserReview::with([
                'sender'=>  function ($q) {
                    $q->with($this->user_relational_array);
                },
                'receiver' => function ($q) {
                    $q->with($this->user_relational_array);
                }
            ])
            ->where('receiver_id', !empty($user_id) ? $user_id : $user->id)
            ->latest()->paginate($limit);

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

    public function appointments(Request $request) {
        $limit = $request->input('limit') ?? 10;
        $docUserId = $request->input('doc_user_id');
        $patUserId = $request->input('pat_user_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $type = $request->input('type'); // upcoming, ongoing, all
        $status = $request->input('status');

        $appointments = Appointment::with([
            'patient' => function ($q) {
                $q->with('patientInfo', 'doctorInfo', 'file');
            },
            'doctor' => function ($q) {
                $q->with('patientInfo', 'doctorInfo', 'file');
            }
        ])
        ->when($type === 'upcoming', function ($q) {
            $q->where('start_time_in_secconds', '>=', time());
        })
        ->when($type === 'ongoing', function ($q) {
            $q->where('start_time_in_secconds', '<=', time())
              ->where('end_time_in_secconds', '>=', time());
        })
        ->when($docUserId, function ($q) use ($docUserId) {
            $q->where('doc_user_id', $docUserId);
        })
        ->when($patUserId, function ($q) use ($patUserId) {
            $q->where('pat_user_id', $patUserId);
        })
        ->when($dateFrom, function ($q) use ($dateFrom) {
            $q->where('date', '>=', $dateFrom);
        })
        ->when($dateTo, function ($q) use ($dateTo) {
            $q->where('date', '<=', $dateTo);
        })
        ->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->orderBy('start_time_in_secconds', 'asc')
        ->paginate($limit);

        return response()->json([
            "message" => "Appointments list.",
            "data" => $appointments->items(),
            "errors" => null,
            "pagination" => [
                "total" => $appointments->total(),
                "current_page" => $appointments->currentPage(),
                "per_page" => $appointments->perPage(),
                "last_page" => $appointments->lastPage(),
                "from" => $appointments->firstItem(),
                "to" => $appointments->lastItem()
            ]
        ], 200);
    }

    public function appointment_detail(Request $request) {
        $appointmentId = $request->input('appointment_id');

        if (!$appointmentId) {
            return response()->json([
                'message' => 'Appointment ID is required',
                'errors' => ['appointment_id is required']
            ], 422);
        }

        $appointment = Appointment::with([
            'patient' => function ($q) {
                $q->with('patientInfo', 'doctorInfo', 'file');
            },
            'doctor' => function ($q) {
                $q->with('patientInfo', 'doctorInfo', 'file');
            },
            'timeSlot'
        ])->find($appointmentId);

        if (!$appointment) {
            return response()->json([
                'message' => 'Appointment not found',
                'errors' => ['Appointment does not exist']
            ], 404);
        }

        // Check if the authenticated user has access to this appointment
        $user = auth()->user();
        if ($appointment->pat_user_id !== $user->id && $appointment->doc_user_id !== $user->id && $user->type !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => ['You do not have access to this appointment']
            ], 403);
        }

        return response()->json([
            'message' => 'Appointment details retrieved successfully',
            'data' => $appointment,
            'errors' => null
        ], 200);
    }

    public function prescriptions(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $user = auth()->user();

            $prescriptions = \App\Models\Prescription::with(['doctor.doctorInfo', 'doctor.file', 'appointment', 'prescriptionImage', 'items.product'])
                ->forPatient($user->id)
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'message' => 'Prescriptions list',
                'data' => $prescriptions->items(),
                'errors' => null,
                'pagination' => [
                    'total' => $prescriptions->total(),
                    'current_page' => $prescriptions->currentPage(),
                    'per_page' => $prescriptions->perPage(),
                    'last_page' => $prescriptions->lastPage(),
                    'from' => $prescriptions->firstItem(),
                    'to' => $prescriptions->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve prescriptions',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function prescription_detail(Request $request)
    {
        try {
            $prescriptionId = $request->input('prescription_id');
            $user = auth()->user();

            if (!$prescriptionId) {
                return response()->json([
                    'message' => 'Prescription ID is required',
                    'errors' => ['prescription_id is required']
                ], 422);
            }

            $prescription = \App\Models\Prescription::with(['doctor.doctorInfo', 'doctor.file', 'appointment', 'prescriptionImage', 'items.product.image'])
                ->forPatient($user->id)
                ->find($prescriptionId);

            if (!$prescription) {
                return response()->json([
                    'message' => 'Prescription not found',
                    'errors' => ['Prescription does not exist']
                ], 404);
            }

            return response()->json([
                'message' => 'Prescription details retrieved successfully',
                'data' => $prescription
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve prescription',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}