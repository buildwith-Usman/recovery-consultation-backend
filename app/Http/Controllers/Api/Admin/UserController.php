<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function allUsers(Request $request) {

        $limit = $request->input('limit') ?? 10;
        $type = $request->input('type');
        $specialization = $request->input('specialization');

        $user = User::with('patientInfo', 'doctorInfo')
        ->where(function ($q) use($type) {
            if($type) {
                $q->where('type', $type);
            }
        })
        ->when($type === 'doctor' && $specialization, function ($q) use($specialization) {
            $q->whereHas('doctorInfo', function ($query) use($specialization) {
                $query->where('specialization', $specialization);
            });
        })
        ->where('type', '!=', 'admin')
        ->orderBy('id', 'desc')->paginate($limit);

        return response()->json([
            "message" => "Users list.",
            "data" => $user->items(),
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
