<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
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
        return view('backend.patients.prescription', compact('patient', 'pagename', 'breadcrumb'));
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
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'required|string|max:255',
            'medications.*.instructions' => 'nullable|string|max:500',
        ], [
            'chief_complaint.required' => 'Chief complaint is required.',
            'diagnosis.required' => 'Diagnosis and assessment are required.',
            'medications.required' => 'At least one medication is required.',
            'medications.min' => 'At least one medication is required.',
            'medications.*.name.required' => 'Medication name is required.',
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
                'status' => $request->has('save_as_draft') ? 'draft' : 'active',
            ]);

            foreach ($validatedData['medications'] as $medicationData) {
                $prescription->items()->create([
                    'name' => $medicationData['name'],
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
            Log::error('Prescription creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create prescription. Please try again.')
                ->withInput();
        }
    }

    // edit form
    public function edit(Patient $patient, Prescription $prescription)
    {
        $prescription->load('items');
        $pagename = 'Edit Prescription';
        $breadcrumb = 'Edit Prescription';

        return view('backend.patients.prescription-edit', compact('patient', 'prescription', 'pagename', 'breadcrumb'));
    }

    /**
     * Update the specified prescription in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @param  \App\Models\Prescription  $prescription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Patient $patient, Prescription $prescription)
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
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'required|string|max:255',
            'medications.*.instructions' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $prescription->update([
                'prescription_date' => $validatedData['prescription_date'],
                'chief_complaint' => $validatedData['chief_complaint'],
                'diagnosis' => $validatedData['diagnosis'],
                'follow_up_date' => $validatedData['follow_up_date'] ?? null,
                'priority' => $validatedData['priority'],
                'notes' => $validatedData['notes'] ?? null,
                'special_notes' => $validatedData['special_notes'] ?? null,
                'status' => $request->has('save_as_draft') ? 'draft' : 'active',
            ]);

            // Delete existing items
            $prescription->items()->delete();

            // Create new items
            foreach ($validatedData['medications'] as $medicationData) {
                $prescription->items()->create([
                    'name' => $medicationData['name'],
                    'dosage' => $medicationData['dosage'],
                    'frequency' => $medicationData['frequency'],
                    'duration' => $medicationData['duration'],
                    'instructions' => $medicationData['instructions'] ?? null,
                ]);
            }

            DB::commit();

            $message = $request->has('save_as_draft')
                ? 'Prescription updated and saved as draft successfully.'
                : 'Prescription updated successfully.';

            return redirect()->route('patients.prescriptions.show', [$patient, $prescription])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Prescription update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update prescription. Please try again.')
                ->withInput();
        }
    }
}
