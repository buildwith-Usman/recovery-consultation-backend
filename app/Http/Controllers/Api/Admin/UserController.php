<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserTimeSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    public function allUsers(Request $request) {

        $limit = $request->input('limit') ?? 10;
        $type = $request->input('type');
        $specialization = $request->input('specialization');
        $status = $request->input('doctor.status');

        $user = User::with('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'file', 'available_times', 'timeSlots')
        ->where(function ($q) use($type) {
            if($type) {
                $q->where('type', $type);
            }
        })
        ->when($type === 'doctor', function ($q) use($specialization, $status) {
            $q->whereHas('doctorInfo', function ($query) use($specialization, $status) {
                $query->where('completed', 1);
                if($specialization) {
                    $query->where('specialization', $specialization);
                }
                if($status) {
                    $query->where('status', $status);
                }
            });
        })
        ->where('type', '!=', 'admin')
        ->orderBy('id', 'desc')->paginate($limit);

        return response()->json([
            "message" => "Users list.",
            "data" => array_map(function($userItem) {
                return ['user' => $userItem];
            }, $user->items()),
            "errors" => null,
            "pagination" => [
            "total" => $user->total(),
            "current_page" => $user->currentPage(),
            "per_page" => $user->perPage(),
            "last_page" => $user->lastPage(),
            "from" => $user->firstItem(),
            "to" => $user->lastItem()
            ]
        ], 200);
    }
    public function approve(Request $request) {
        $doctor_id = $request->input('doctor_id');
        $status = $request->input('status');

        $user = User::where([
            'id' => $doctor_id,
            'type' => 'doctor'
        ])->first();

        if(!$user) {
            return response()->json([
                'message' => 'Doctor not found!',
                'data' => null
            ], 404);
        }

        $doctorInfo = $user->doctorInfo;
        if (!$doctorInfo) {
            return response()->json([
                'message' => 'Doctor info not found!',
                'data' => null
            ], 404);
        }

        $doctorInfo->status = $status;
        $doctorInfo->save();

        return response()->json([
            'message' => 'Doctor '.$status.'.',
            'data' => $user
        ], 200);
    }

    public function update_user(Request $request) {
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found!',
                'data' => null
            ], 404);
        }

        $userData = $request->only(['name', 'email', 'phone', 'bio']);
        if ($request->has('file_id')) {
            $userData['profile_image_id'] = $request->input('file_id');
        }

        // Update user data
        $user->fill($userData);
        $user->save();

        if ($request->has('languages')) {
            $languages = $request->input('languages');
            
            $user->userLanguages()->delete();
            foreach ($languages as $language) {
                $user->userLanguages()->create([
                    'language' => $language
                ]);
            }
        }

        if ($user->type === 'doctor') {
            // Update or create doctor info
            $doctorData = $request->only([
                'specialization', 'experience', 'dob', 'degree',
                'license_no', 'country_id', 'gender', 'age',
                'commision_type', 'commision_value', 'completed', 'status'
            ]);

            $user->doctorInfo()->updateOrCreate(
                ['user_id' => $user->id],
                $doctorData
            );

            // Handle available times
            if ($request->has('available_times')) {
                $availableTimes = $request->input('available_times');

                foreach ($availableTimes as $timeData) {
                    // Update or create based on weekday
                    $user->available_times()->updateOrCreate(
                        ['user_id' => $user->id, 'weekday' => $timeData['weekday']],
                        $timeData
                    );
                }

                // Generate time slots for all available times
                $this->generateTimeSlots($user);
            }
        } elseif ($user->type === 'patient') {
            // Update or create patient info
            $patientData = $request->only([
                'looking_for', 'completed', 'dob',
                'gender', 'age', 'blood_group'
            ]);

            $user->patientInfo()->updateOrCreate(
                ['user_id' => $user->id],
                $patientData
            );
        }

        // Reload user with relationships
        $user->load('patientInfo', 'doctorInfo', 'questionnaires', 'userLanguages', 'file', 'available_times', 'timeSlots');

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => $user
        ], 200);
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

    /**
     * Generate time slots based on available times
     */
    private function generateTimeSlots(User $user)
    {
        // Delete all existing time slots for this user
        UserTimeSlot::where('user_id', $user->id)->delete();

        // Get all available times for the user
        $availableTimes = $user->available_times()->where('status', 'available')->get();

        foreach ($availableTimes as $availableTime) {
            // Skip if required fields are missing
            if (!$availableTime->start_time || !$availableTime->end_time || !$availableTime->session_duration) {
                continue;
            }

            $sessionDuration = (int) $availableTime->session_duration; // in minutes
            $startTime = Carbon::parse($availableTime->start_time);
            $endTime = Carbon::parse($availableTime->end_time);

            // Generate slots
            $currentSlotStart = $startTime->copy();

            while ($currentSlotStart->lt($endTime)) {
                $currentSlotEnd = $currentSlotStart->copy()->addMinutes($sessionDuration);

                // Don't create slot if it exceeds end time
                if ($currentSlotEnd->gt($endTime)) {
                    break;
                }

                // Create time slot
                UserTimeSlot::create([
                    'user_id' => $user->id,
                    'available_time_id' => $availableTime->id,
                    'weekday' => $availableTime->weekday,
                    'slot_start_time' => $currentSlotStart->format('H:i:s'),
                    'slot_end_time' => $currentSlotEnd->format('H:i:s'),
                    'is_booked' => false
                ]);

                // Move to next slot
                $currentSlotStart = $currentSlotEnd->copy();
            }
        }
    }
}
