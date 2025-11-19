<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Product;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Get list of prescriptions created by the doctor
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit') ?? 10;
            $status = $request->input('status');
            $patientId = $request->input('patient_id');

            $doctor = auth()->user();

            $prescriptions = Prescription::with(['patient', 'appointment', 'prescriptionImage', 'items.product'])
                ->byDoctor($doctor->id)
                ->when($status, function ($q) use ($status) {
                    $q->byStatus($status);
                })
                ->when($patientId, function ($q) use ($patientId) {
                    $q->where('patient_user_id', $patientId);
                })
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

    /**
     * Create a new prescription
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'appointment_id' => 'required|exists:appointments,id',
                'prescription_image_id' => 'nullable|exists:files,id',
                'prescription_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'nullable|array',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.dosage_instructions' => 'nullable|string',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.duration_days' => 'nullable|integer|min:1'
            ]);

            $doctor = auth()->user();

            // Verify appointment belongs to this doctor
            $appointment = Appointment::find($validatedData['appointment_id']);
            if ($appointment->doc_user_id !== $doctor->id) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'errors' => ['This appointment does not belong to you']
                ], 403);
            }

            // Check if prescription already exists for this appointment
            if (Prescription::where('appointment_id', $validatedData['appointment_id'])->exists()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['A prescription already exists for this appointment']
                ], 422);
            }

            // Validate that at least image or items are provided
            if (empty($validatedData['prescription_image_id']) && empty($validatedData['items'])) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['Either prescription image or items must be provided']
                ], 422);
            }

            // Create prescription
            $prescription = Prescription::create([
                'appointment_id' => $validatedData['appointment_id'],
                'prescribed_by_doctor_id' => $doctor->id,
                'patient_user_id' => $appointment->pat_user_id,
                'prescription_image_id' => $validatedData['prescription_image_id'] ?? null,
                'prescription_date' => $validatedData['prescription_date'],
                'notes' => $validatedData['notes'] ?? null,
                'status' => 'issued',
                'created_by' => $doctor->id
            ]);

            // Add prescription items if provided
            if (!empty($validatedData['items'])) {
                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['product_id']);

                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $product->medicine_name,
                        'dosage_instructions' => $item['dosage_instructions'] ?? null,
                        'quantity' => $item['quantity'],
                        'duration_days' => $item['duration_days'] ?? null
                    ]);
                }
            }

            // Load relationships
            $prescription->load(['patient', 'appointment', 'prescriptionImage', 'items.product']);

            return response()->json([
                'message' => 'Prescription created successfully',
                'data' => $prescription
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
                'message' => 'Prescription creation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get a single prescription
     */
    public function show($id)
    {
        try {
            $doctor = auth()->user();

            $prescription = Prescription::with(['patient', 'appointment', 'prescriptionImage', 'items.product'])
                ->byDoctor($doctor->id)
                ->find($id);

            if (!$prescription) {
                return response()->json([
                    'message' => 'Prescription not found',
                    'errors' => ['Prescription does not exist or you do not have access']
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

    /**
     * Update an existing prescription
     */
    public function update(Request $request, $id)
    {
        try {
            $doctor = auth()->user();

            $prescription = Prescription::byDoctor($doctor->id)->find($id);

            if (!$prescription) {
                return response()->json([
                    'message' => 'Prescription not found',
                    'errors' => ['Prescription does not exist or you do not have access']
                ], 404);
            }

            // Only allow updating draft or issued prescriptions
            if ($prescription->status === 'dispensed') {
                return response()->json([
                    'message' => 'Cannot update dispensed prescription',
                    'errors' => ['This prescription has already been dispensed']
                ], 422);
            }

            $validatedData = $request->validate([
                'prescription_image_id' => 'nullable|exists:files,id',
                'notes' => 'nullable|string',
                'items' => 'nullable|array',
                'items.*.product_id' => 'required_with:items|exists:products,id',
                'items.*.dosage_instructions' => 'nullable|string',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.duration_days' => 'nullable|integer|min:1'
            ]);

            // Update prescription
            $prescription->update([
                'prescription_image_id' => $validatedData['prescription_image_id'] ?? $prescription->prescription_image_id,
                'notes' => $validatedData['notes'] ?? $prescription->notes,
            ]);

            // Update items if provided
            if (isset($validatedData['items'])) {
                // Delete existing items
                PrescriptionItem::where('prescription_id', $prescription->id)->delete();

                // Add new items
                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['product_id']);

                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $product->medicine_name,
                        'dosage_instructions' => $item['dosage_instructions'] ?? null,
                        'quantity' => $item['quantity'],
                        'duration_days' => $item['duration_days'] ?? null
                    ]);
                }
            }

            // Load relationships
            $prescription->load(['patient', 'appointment', 'prescriptionImage', 'items.product']);

            return response()->json([
                'message' => 'Prescription updated successfully',
                'data' => $prescription
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
                'message' => 'Prescription update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Delete a prescription
     */
    public function destroy($id)
    {
        try {
            $doctor = auth()->user();

            $prescription = Prescription::byDoctor($doctor->id)->find($id);

            if (!$prescription) {
                return response()->json([
                    'message' => 'Prescription not found',
                    'errors' => ['Prescription does not exist or you do not have access']
                ], 404);
            }

            // Only allow deleting draft prescriptions
            if ($prescription->status !== 'draft') {
                return response()->json([
                    'message' => 'Cannot delete prescription',
                    'errors' => ['Only draft prescriptions can be deleted']
                ], 422);
            }

            $prescription->delete();

            return response()->json([
                'message' => 'Prescription deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Prescription deletion failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
