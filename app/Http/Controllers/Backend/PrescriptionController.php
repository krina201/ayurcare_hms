<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class PrescriptionController extends Controller
{

    //   Show the form for creating a new prescription.
    public function create(Patient $patient)
    {
        $pagename = 'New Prescription';
        $breadcrumb = 'New Prescription';

        // Get active medicines for dropdown
        $medicines = Medicine::all();

        return view('backend.patients.prescription', compact('patient', 'pagename', 'breadcrumb', 'medicines'));
    }

    //  Store a newly created prescription in storage.
    public function store(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'prescription_date' => 'required|date',
            'chief_complaint' => 'required|string|max:255',
            'diagnosis' => 'required|string',
            'follow_up_date' => 'nullable|date|after_or_equal:prescription_date',
            'priority' => 'required|string|in:Normal,Urgent,High Priority',
            'notes' => 'nullable|string',
            'special_notes' => 'nullable|string',
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|integer|exists:medicines,id',
            'medications.*.dosage' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'required|string|max:255',
            'medications.*.instructions' => 'nullable|string|max:500',
        ], [
            'chief_complaint.required' => 'Chief complaint is required.',
            'diagnosis.required' => 'Diagnosis and assessment are required.',
            'medications.required' => 'At least one medication is required.',
            'medications.min' => 'At least one medication is required.',
            'medications.*.name.required' => 'Medication selection is required.',
            'medications.*.name.exists' => 'Selected medication is invalid.',
            'medications.*.dosage.required' => 'Dosage is required.',
            'medications.*.frequency.required' => 'Frequency is required.',
            'medications.*.duration.required' => 'Duration is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $prescription = $patient->prescriptions()->create([
                'doctor_id' => Auth::id(),
                'prescription_date' => $validatedData['prescription_date'],
                'chief_complaint' => $validatedData['chief_complaint'],
                'diagnosis' => $validatedData['diagnosis'],
                'follow_up_date' => $validatedData['follow_up_date'] ?? null,
                'priority' => $validatedData['priority'],
                'notes' => $validatedData['notes'] ?? null,
                'special_notes' => $validatedData['special_notes'] ?? null,
                'status' => $request->has('save_as_draft') ? 0 : 1,
            ]);

            // More efficient version with better error handling
            foreach ($validatedData['medications'] as $medicationData) {
                $medicine = Medicine::where('id', $medicationData['name'])
                    ->active()
                    ->first();

                if (!$medicine) {
                    throw new \Exception("Medicine with ID '{$medicationData['name']}' not found or is inactive in database.");
                }

                $prescription->items()->create([
                    'medicine_id' => $medicine->id,
                    'name' => $medicine->name, // Use the exact name from database
                    'dosage' => $medicationData['dosage'],
                    'frequency' => $medicationData['frequency'],
                    'duration' => $medicationData['duration'],
                    'instructions' => $medicationData['instructions'] ?? null,
                ]);
            }

            DB::commit();

            $message = $request->has('save_as_draft')
                ? 'Prescription saved as draft successfully.'
                : 'Prescription created successfully.';

            return redirect()->route('patients.show', $patient)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Prescription creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'patient_id' => $patient->id,
                'doctor_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create prescription: ' . $e->getMessage())
                ->withInput();
        }
    }
}
