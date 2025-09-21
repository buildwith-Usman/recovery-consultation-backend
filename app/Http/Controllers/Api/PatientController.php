<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserReview;
use Illuminate\Http\Request;

class PatientController extends Controller
{
  public function doctors_list(Request $request)
  {
    $specialization = $request->input('specialization');
    $language = $request->input('language');
    try {
      // Example query to get doctors based on specialization
      $doctors = User::with(['doctorInfo', 'questionnaires', 'userLanguages', 'reviews'])
        ->whereHas('doctorInfo', function ($query) use ($specialization) {
          $query->where('specialization', $specialization);
        })
        ->whereHas('userLanguages', function ($q) use ($language) {
          if ($language) {
            $q->where('language', $language);
          }
        })
        ->where('type', 'doctor')->get();

      return response()->json([
        "message" => "Doctors retrieved successfully",
        "data" => $doctors
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

  public function doctor_details(Request $request)
  {
    $id = $request->input('id');
    try {
      $doctor = User::with(['doctorInfo', 'questionnaires', 'userLanguages', 'reviews'])->where([
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

  public function appointment_booking(Request $request)
  {
    try {
      $request->validate([
        'pat_user_id' => 'required',
        'doc_user_id' => 'required',
        'date' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'price' => 'required'
      ]);

      $data = $request->all();

      $data['start_time_in_secconds'] = strtotime($request->date . ' ' . $request->start_time);
      $data['end_time_in_secconds'] = strtotime($request->date . ' ' . $request->start_time);

      $appointment = Appointment::create($data);

      return response()->json([
        'message' => 'Appointment created.',
        'data' => $appointment
      ]);

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

  public function appointments(Request $request)
  {

    $user = auth()->user();

    try {
      $appointments = Appointment::where('pat_user_id', $user->id)->orderBy('id', 'desc')->get();

      return response()->json([
        'message' => 'Appointment list',
        'data' => $appointments
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Registration failed',
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

      $check = Appointment::where(function ($q) use($user) {
        $q->orWhere('pat_user_id', $user->id);
        $q->orWhere('doc_user_id', $user->id);
      })->first();

      if(!$check) {
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