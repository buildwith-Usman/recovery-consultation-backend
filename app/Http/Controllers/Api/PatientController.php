<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserAvailableTime;
use App\Models\UserReview;
use App\Models\UserTimeSlot;
use Illuminate\Http\Request;

class PatientController extends Controller
{

  public function match_doctors_list(Request $request)
  {

    $limit = $request->input('limit') ?? 10;
    $user = User::with(['patientInfo', 'file', 'questionnaires'])->where('id', auth()->user()->id)->first();

    if ($user->type !== "patient") {
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

    if (str_contains($age_prefer, '-')) {
      $age_prefer_arr = explode('-', $age_prefer);
      $min_age_prefer = (int) $age_prefer_arr[0];
      $max_age_prefer = (int) $age_prefer_arr[1];
    } else if (str_contains($age_prefer, '+')) {
      $min_age_prefer = (int) str_replace('+', '', $age_prefer);
    } else if (str_contains($age_prefer, '<')) {
      $max_age_prefer = (int) str_replace('<', '', $age_prefer);
    }

    $lang_prefer = $user->questionnaires
      ->where('key', 'lang_prefer')
      ->pluck('answer')->first();

    $help_support = $user->questionnaires
      ->where('key', 'help_support')
      ->pluck('answer')->first();

    $doctors = User::with(['doctorInfo', 'file', 'questionnaires'])
      ->whereHas('doctorInfo', function ($q) use ($looking_for, $gender_prefer_data, $min_age_prefer, $max_age_prefer) {
        $q->where('specialization', $looking_for);
        if ($gender_prefer_data) {
          $q->where('gender', strtolower($gender_prefer_data));
        }
        if ($min_age_prefer && $max_age_prefer) {
          $q->whereBetween('age', [(int) $min_age_prefer, (int) $max_age_prefer]);
        } else if ($min_age_prefer && empty($max_age_prefer)) {
          $q->where('age', '>=', $min_age_prefer);
        } else if (empty($min_age_prefer) && $max_age_prefer) {
          $q->where('age', '<=', $max_age_prefer);
        }
        $q->where('status', 'approved');
      })
      ->whereHas('questionnaires', function ($q) use ($age_group_prefer, $help_support) {
        $q->where(function ($query) use ($age_group_prefer, $help_support) {
          if ($age_group_prefer) {
            $query->orWhere('key', 'age_group_prefer')
              ->whereRaw('FIND_IN_SET(?, answer)', [$age_group_prefer]);
          }
          if ($help_support) {
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
      ->whereHas('userLanguages', function ($q) use ($lang_prefer) {
        $languageArr = explode(',', $lang_prefer);
        $q->whereIn('language', $languageArr);
      })
      ->where('type', 'doctor')
      ->where('is_verified', true)
      ->paginate($limit);

    return response()->json([
      'message' => 'Doctors (' . $looking_for . ') list.',
      "data" => $doctors->items(),
      "errors" => null,
      "pagination" => [
        "total" => $doctors->total(),
        "current_page" => $doctors->currentPage(),
        "per_page" => $doctors->perPage(),
        "last_page" => $doctors->lastPage(),
        "from" => $doctors->firstItem(),
        "to" => $doctors->lastItem()
      ]
    ]);
  }

  public function doctors_list(Request $request)
  {
    $specialization = $request->input('specialization');
    $language = $request->input('language');
    $limit = $request->input('limit') ?? 10;
    try {
      // Example query to get doctors based on specialization
      $doctors = User::with([
          'doctorInfo',
          'file',
          'questionnaires',
          'userLanguages',
          'reviews'
        ])
        ->where('type', 'doctor')
        
        ->whereHas('doctorInfo', function ($query) use ($specialization) {
          $query->where('specialization', $specialization);
        })
        ->when($language, function ($query) use ($language) {
          $query->whereHas('userLanguages', function ($q) use ($language) {
            $q->where('language', $language);
          });
        })
        ->paginate($limit);

      return response()->json([
        "data" => $doctors->items(),
        "errors" => null,
        "pagination" => [
          "total" => $doctors->total(),
          "current_page" => $doctors->currentPage(),
          "per_page" => $doctors->perPage(),
          "last_page" => $doctors->lastPage(),
          "from" => $doctors->firstItem(),
          "to" => $doctors->lastItem()
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to retrieve doctors',
        'errors' => [$e->getMessage()]
      ], 500);
    } catch (\Throwable $th) {
      // Log the error or handle it as needed
      return response()->json([
        "message" => "Failed to retrieve doctors",
        "errors" => [$th->getMessage()]
      ], 500);
    }
  }

  public function appointment_booking(Request $request)
  {
    try {
      $request->validate([
        'doc_user_id' => 'required|exists:users,id',
        'date' => 'required|date',
        'time_slot_id' => 'required|exists:user_time_slots,id',
        'agora_video_url' => 'nullable|string|max:500'
      ]);

      // Get authenticated patient
      $patientId = auth()->user()->id;

      // Verify the doctor exists and is a doctor
      $doctor = User::with('doctorInfo')->find($request->doc_user_id);
      if (!$doctor || $doctor->type !== 'doctor') {
        return response()->json([
          'message' => 'Invalid doctor',
          'errors' => ['The selected doctor is not valid']
        ], 422);
      }

      // Get the time slot
      $timeSlot = UserTimeSlot::where('id', $request->time_slot_id)
        ->where('user_id', $request->doc_user_id)
        ->first();

      if (!$timeSlot) {
        return response()->json([
          'message' => 'Time slot not found',
          'errors' => ['The selected time slot does not belong to this doctor']
        ], 422);
      }

      // Check if time slot is already booked
      if ($timeSlot->is_booked) {
        return response()->json([
          'message' => 'Time slot already booked',
          'errors' => ['This time slot is already booked']
        ], 422);
      }

      // Check if the requested date matches the weekday of the time slot
      $requestedWeekday = date('D', strtotime($request->date));
      if ($timeSlot->weekday !== $requestedWeekday) {
        return response()->json([
          'message' => 'Invalid date',
          'errors' => ['The selected date does not match the time slot weekday']
        ], 422);
      }

      // Check for conflicting appointments on the same date and time slot
      $conflictingAppointment = Appointment::where('doc_user_id', $request->doc_user_id)
        ->where('date', $request->date)
        ->where('time_slot_id', $request->time_slot_id)
        ->exists();

      if ($conflictingAppointment) {
        return response()->json([
          'message' => 'Time slot already booked',
          'errors' => ['This time slot is already booked for the selected date']
        ], 422);
      }

      // Get price from doctor info
      $price = $doctor->doctorInfo->price ?? 0;

      // Create appointment
      $appointment = Appointment::create([
        'pat_user_id' => $patientId,
        'doc_user_id' => $request->doc_user_id,
        'date' => $request->date,
        'start_time' => $timeSlot->slot_start_time,
        'end_time' => $timeSlot->slot_end_time,
        'start_time_in_secconds' => strtotime($request->date . ' ' . $timeSlot->slot_start_time),
        'end_time_in_secconds' => strtotime($request->date . ' ' . $timeSlot->slot_end_time),
        'price' => $price,
        'time_slot_id' => $request->time_slot_id,
        'agora_video_url' => $request->input('agora_video_url')
      ]);

      // Mark time slot as booked
      $timeSlot->is_booked = true;
      $timeSlot->save();

      // Load relationships for response
      $appointment->load(['patient', 'doctor', 'timeSlot']);

      return response()->json([
        'message' => 'Appointment booked successfully',
        'data' => $appointment
      ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
      $errorsList = [];
      foreach ($e->errors() as $err) {
        $errorsList = array_merge($errorsList, $err);
      }
      return response()->json([
        'message' => 'Validation failed',
        'errors' => $errorsList
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Appointment booking failed',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }

  public function appointment_update(Request $request)
  {
    try {
      $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
      ]);

      $user = auth()->user();

      $appointment = Appointment::where('id', $request->appointment_id)
        ->where(function ($q) use ($user) {
          $q->where('pat_user_id', $user->id);
          $q->orWhere('doc_user_id', $user->id);
        })
        ->first();

      if (!$appointment) {
        return response()->json([
          'message' => 'Appointment not found',
          'errors' => ['The specified appointment does not exist for this patient']
        ], 404);
      }

      $appointment->status = $request->status;
      $appointment->save();

      return response()->json([
        'message' => 'Appointment updated successfully',
        'data' => $appointment
      ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
      $errorsList = [];
      foreach ($e->errors() as $err) {
        $errorsList = array_merge($errorsList, $err);
      }
      return response()->json([
        'message' => 'Validation failed',
        'errors' => $errorsList
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Appointment update failed',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }

  public function add_reviews(Request $request)
  {
    try {
      $request->validate([
        'receiver_id' => 'required',
        'rating' => 'required',
        'appointment_id' => 'required'
      ]);

      $user = auth()->user();

      $check = Appointment::where(function ($q) use ($user) {
        $q->orWhere('pat_user_id', $user->id);
        $q->orWhere('doc_user_id', $user->id);
      })->first();

      if (!$check) {
        return response()->json([
          'message' => 'Validation failed',
          'errors' => ['Appointment Id isn\'t valid!']
        ], 422);
      }

      $review = UserReview::create([
        'sender_id' => $user->id,
        'receiver_id' => $request->input('receiver_id'),
        'appointment_id' => $request->input('appointment_id'),
        'rating' => $request->input('rating'),
        'message' => $request->input('message')
      ]);

      return response()->json([
        'message' => 'Review added',
        'data' => $review
      ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
      $errorsList = [];
      foreach ($e->errors() as $err) {
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