<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function doctors_list(Request $request)
    {
        $specialization = $request->input('specialization');
        try {
            // Example query to get doctors based on specialization
            $doctors = User::with(['doctorInfo', 'questionnaires'])
            ->whereHas('doctorInfo', function ($query) use ($specialization) {
                $query->where('specialization', $specialization);
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
            $doctor = User::with(['doctorInfo', 'questionnaires'])->where([
                'id' => $id,
                'type' => 'doctor'
            ])->first();
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
}