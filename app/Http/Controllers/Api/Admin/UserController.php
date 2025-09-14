<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function allUsers(Request $request) {
        $user = User::with('patientInfo', 'doctorInfo')
        ->where('type', '!=', 'admin')
        ->orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'message' => 'Users list.',
            'data' => $user
        ], 200);
    }
    public function approve(Request $request) {
        $doctor_id = $request->input('doctor_id');

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

        $doctorInfo->approved = 1;
        $doctorInfo->save();

        return response()->json([
            'message' => 'Doctor approved.',
            'data' => $user
        ], 200);
    }
}
