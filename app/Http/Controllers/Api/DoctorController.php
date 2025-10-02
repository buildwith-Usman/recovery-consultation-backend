<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
  public function patients(Request $request)
  {
    $limit = $request->input('limit') ?? 10;
    $user = auth()->user();

    try {
      $patients = Appointment::where('doc_user_id', $user->id)
        ->where('status', 'completed')
        ->distinct()
        ->paginate($limit);

      return response()->json([
        "message" => "Patients list",
        "data" => $patients->items(),
        "pagination" => [
          "total" => $patients->total(),
          "current_page" => $patients->currentPage(),
          "per_page" => $patients->perPage(),
          "last_page" => $patients->lastPage(),
          "from" => $patients->firstItem(),
          "to" => $patients->lastItem()
        ]
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to retrieve patients',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }

  public function patient_history(Request $request) {
    
    $patient_id = $request->input('patient_id');
    $limit = $request->input('limit') ?? 10;
    $user = auth()->user();
    
    try {
      $patients = Appointment::with('patient')->where('doc_user_id', $user->id)
      ->where('pat_user_id', $patient_id)
      ->paginate($limit);

      return response()->json([
        "message" => "Patient history list",
        "data" => $patients->items(),
        "pagination" => [
          "total" => $patients->total(),
          "current_page" => $patients->currentPage(),
          "per_page" => $patients->perPage(),
          "last_page" => $patients->lastPage(),
          "from" => $patients->firstItem(),
          "to" => $patients->lastItem()
        ]
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Failed to retrieve patients',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }
}
