<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\MedicineDispenseItem;
use App\Models\MasterMedicineType;
use App\Models\MasterMedicineCategory;
use App\Models\MasterManufacturer;
use App\Models\MasterMeasurement;
use App\Models\MasterPaymentMode;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PharmacyController extends Controller
{
    /**
     * Display the pharmacy dashboard
     */
    public function index()
    {
        $pagename = 'Pharmacy Management';
        $breadcrumb = 'Pharmacy Management';

        // Get statistics
        $totalMedicines = Medicine::count();
        $activeMedicines = Medicine::where('status', 1)->count();
        $expiringSoon = Medicine::where('expiry_date', '<=', now()->addDays(30))->count();
        $lowStock = Medicine::whereRaw('initial_stock_quantity <= minimum_stock_level')->count();
        $draftMedicines = Medicine::draft()->count();
        $submittedMedicines = Medicine::submitted()->count();

        // Get medicines for the table
        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer'])
            ->orderBy('created_at', 'desc')->get();

        // Calculate total inventory value
        $totalValue = Medicine::sum(DB::raw('initial_stock_quantity * selling_price'));

        // Get medicine type distribution
        $medicineTypeDistribution = Medicine::join('master_medicine_type', 'medicines.medicine_type_id', '=', 'master_medicine_type.id')
            ->select('master_medicine_type.name', DB::raw('count(*) as count'))
            ->groupBy('master_medicine_type.id', 'master_medicine_type.name')
            ->get();

        // Get low stock items
        $lowStockItems = Medicine::whereRaw('initial_stock_quantity <= minimum_stock_level')
            ->with(['medicineType'])
            ->limit(5)
            ->get();

        // Get expiring items
        $expiringItems = Medicine::where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date')
            ->limit(5)
            ->get();

        return view('backend.pharmacy.index', compact(
            'pagename',
            'breadcrumb',
            'totalMedicines',
            'activeMedicines',
            'expiringSoon',
            'lowStock',
            'draftMedicines',
            'submittedMedicines',
            'medicines',
            'totalValue',
            'medicineTypeDistribution',
            'lowStockItems',
            'expiringItems'
        ));
    }
    // view Role form
    public function create()
    {
        $pagename = 'Add New Medicine';
        $breadcrumb = 'Add New Medicine';

        // Get master data for dropdowns
        $medicineTypes = MasterMedicineType::orderBy('name')->get();
        $medicineCategories = MasterMedicineCategory::orderBy('name')->get();
        $manufacturers = MasterManufacturer::orderBy('name')->get();
        $measurements = MasterMeasurement::orderBy('name')->get();

        return view('backend.pharmacy.create', compact('pagename', 'breadcrumb', 'medicineTypes', 'medicineCategories', 'manufacturers', 'measurements'));
    }

    // store data in data base
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:medicines,code',
            'medicine_type_id' => 'required',
            'medicine_category_id' => 'required',
            'manufacturer_id' => 'required',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'required|string|max:20',
            'supplier_email' => 'required|email|max:255',
            'strength_dosage' => 'required|string|max:100',
            'measurement_id' => 'required',
            'main_ingredients' => 'required|string',
            'indications_usage' => 'required|string',
            'batch_number' => 'required|string|max:100',
            'manufacturing_date' => 'required|date',
            'expiry_date' => 'required|date|after:manufacturing_date',
            'initial_stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'storage_location' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'storage_instructions' => 'required|string',
            'side_effects_precautions' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'boolean',
            'track_expiry' => 'boolean',
            'save_type' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // DB::beginTransaction();

        $medicine = Medicine::create([
            'name' => $request->name,
            'code' => $request->code,
            'medicine_type_id' => $request->medicine_type_id,
            'medicine_category_id' => $request->medicine_category_id,
            'manufacturer_id' => $request->manufacturer_id,
            'supplier_name' => $request->supplier_name,
            'supplier_contact' => $request->supplier_contact,
            'supplier_email' => $request->supplier_email,
            'strength_dosage' => $request->strength_dosage,
            'measurement_id' => $request->measurement_id,
            'main_ingredients' => $request->main_ingredients,
            'indications_usage' => $request->indications_usage,
            'batch_number' => $request->batch_number,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date' => $request->expiry_date,
            'initial_stock_quantity' => $request->initial_stock_quantity,
            'minimum_stock_level' => $request->minimum_stock_level,
            'storage_location' => $request->storage_location,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'mrp' => $request->mrp,
            'tax_rate' => $request->tax_rate ?? 0,
            'storage_instructions' => $request->storage_instructions,
            'side_effects_precautions' => $request->side_effects_precautions,
            'notes' => $request->notes,
            'status' => $request->has('status'),
            'track_expiry' => $request->has('track_expiry'),
            'save_type' => $request->save_type
        ]);

        // DB::commit();

        // Save the user
        if ($medicine->save()) {

            $message = $request->save_type == 0
                ? 'Medicine saved as draft successfully!'
                : 'Medicine added successfully!';

            return redirect()->route('pharmacy')->with('success', $message);
        } else {
            return redirect()->route('pharmacy')->with('error', 'Something went wrong while saving the User');
        }
    }


    /**
     * Display medicine inventory
     */
    public function inventory()
    {
        $pagename = 'Medicine Inventory';
        $breadcrumb = 'Medicine Inventory';

        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pharmacy.inventory', compact('pagename', 'breadcrumb', 'medicines'));
    }

    /**
     * Show medicine details
     */
    public function show($id)
    {
        $medicine = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])->findOrFail($id);
        $pagename = 'Medicine Details';
        $breadcrumb = 'Medicine Details';

        return view('backend.pharmacy.show', compact('pagename', 'breadcrumb', 'medicine'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        $pagename = 'Edit Medicine';
        $breadcrumb = 'Edit Medicine';

        // Get master data for dropdowns
        $medicineTypes = MasterMedicineType::orderBy('name')->get();
        $medicineCategories = MasterMedicineCategory::orderBy('name')->get();
        $manufacturers = MasterManufacturer::orderBy('name')->get();
        $measurements = MasterMeasurement::orderBy('name')->get();

        return view('backend.pharmacy.edit', compact('pagename', 'breadcrumb', 'medicine', 'medicineTypes', 'medicineCategories', 'manufacturers', 'measurements'));
    }

    /**
     * Update medicine
     */
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:medicines,code,' . $id,
            'medicine_type_id' => 'required',
            'medicine_category_id' => 'required',
            'manufacturer_id' => 'required',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'required|string|max:20',
            'supplier_email' => 'required|email|max:255',
            'strength_dosage' => 'required|string|max:100',
            'measurement_id' => 'required',
            'main_ingredients' => 'required|string',
            'indications_usage' => 'required|string',
            'batch_number' => 'required|string|max:100',
            'manufacturing_date' => 'required|date',
            'expiry_date' => 'required|date|after:manufacturing_date',
            'initial_stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'storage_location' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'storage_instructions' => 'required|string',
            'side_effects_precautions' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'boolean',
            'track_expiry' => 'boolean',
            'save_type' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // DB::beginTransaction();

        $medicine->update([
            'name' => $request->name,
            'code' => $request->code,
            'medicine_type_id' => $request->medicine_type_id,
            'medicine_category_id' => $request->medicine_category_id,
            'manufacturer_id' => $request->manufacturer_id,
            'supplier_name' => $request->supplier_name,
            'supplier_contact' => $request->supplier_contact,
            'supplier_email' => $request->supplier_email,
            'strength_dosage' => $request->strength_dosage,
            'measurement_id' => $request->measurement_id,
            'main_ingredients' => $request->main_ingredients,
            'indications_usage' => $request->indications_usage,
            'batch_number' => $request->batch_number,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date' => $request->expiry_date,
            'initial_stock_quantity' => $request->initial_stock_quantity,
            'minimum_stock_level' => $request->minimum_stock_level,
            'storage_location' => $request->storage_location,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'mrp' => $request->mrp,
            'tax_rate' => $request->tax_rate ?? 0,
            'storage_instructions' => $request->storage_instructions,
            'side_effects_precautions' => $request->side_effects_precautions,
            'notes' => $request->notes,
            'status' => $request->has('status'),
            'track_expiry' => $request->has('track_expiry'),
            'save_type' => $request->save_type
        ]);

        // DB::commit();

        // Save the user
        if ($medicine->save()) {

            $message = $request->save_type == 0
                ? 'Medicine updated and saved as draft successfully!'
                : 'Medicine updated successfully!';

            return redirect()->route('pharmacy')->with('success', $message);
        } else {
            return redirect()->route('pharmacy')->with('error', 'Something went wrong while saving the User');
        }
    }


    //  medicine for delete
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        if ($medicine->delete()) {
            return response()->json(['success' => true, 'message' => 'Medicine deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to delete Medicine.'], 500);
    }

    /**
     * Display dispense form
     */
    public function dispense()
    {
        $pagename = 'Dispense Medicine';
        $breadcrumb = 'Dispense Medicine';

        // Get all active medicines for inventory status
        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // Get payment modes
        $paymentModes = MasterPaymentMode::orderBy('name')->get();

        // Get users for dispensed by dropdown
        $users = User::with('role')->orderBy('name')->get();

        // Get recent dispensations
        $recentDispensations = collect(); // Default empty collection

        // Check if table exists before querying
        if (Schema::hasTable('medicine_dispenses')) {
            $recentDispensations = DB::table('medicine_dispenses')
                ->join('patients', 'medicine_dispenses.patient_id', '=', 'patients.id')
                ->join('users', 'medicine_dispenses.dispensed_by', '=', 'users.id')
                ->select(
                    'medicine_dispenses.receipt_number',
                    'medicine_dispenses.dispense_date',
                    'medicine_dispenses.total_amount',
                    'patients.full_name',
                    'patients.uhid',
                    'patients.photo_path',
                    'users.name as dispensed_by_name'
                )
                ->orderBy('medicine_dispenses.created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            Log::warning('medicine_dispenses table does not exist');
        }

        return view('backend.pharmacy.dispense', compact(
            'pagename',
            'breadcrumb',
            'medicines',
            'paymentModes',
            'users',
            'recentDispensations'
        ));
    }

    /**
     * Search medicines by name or code
     */
    public function searchMedicines(Request $request)
    {
        try {
            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            // Sanitize the query to prevent SQL injection
            $query = strip_tags($query);

            // Search for medicines
            $medicines = Medicine::with(['medicineType', 'manufacturer'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('code', 'LIKE', "%{$query}%");
                })
                ->where('status', 1) // Only active medicines
                ->select([
                    'id',
                    'name',
                    'code',
                    'medicine_type_id',
                    'manufacturer_id',
                    'selling_price',
                    'initial_stock_quantity',
                    'strength_dosage'
                ])
                ->limit(10)
                ->get();

            // Format the results
            $formattedMedicines = $medicines->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name ?? 'N/A',
                    'code' => $medicine->code ?? 'N/A',
                    'type' => $medicine->medicineType->name ?? 'N/A',
                    'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                    'selling_price' => $medicine->selling_price ?? 0,
                    'stock_quantity' => $medicine->initial_stock_quantity ?? 0,
                    'strength_dosage' => $medicine->strength_dosage ?? 'N/A'
                ];
            });

            Log::info('Pharmacy medicine search results', [
                'query' => $query,
                'results_count' => $formattedMedicines->count()
            ]);

            return response()->json($formattedMedicines);
        } catch (\Exception $e) {
            Log::error('Pharmacy medicine search error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while searching medicines',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search patient by UHID, name, or mobile (using Appointment module pattern)
     */
    public function searchPatient(Request $request)
    {
        try {
            // Log the incoming request
            Log::info("Pharmacy patient search request received", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            $query = trim($request->get('query', ''));

            if (empty($query) || strlen($query) < 2) {
                Log::info("Pharmacy patient search: Query too short or empty", ['query' => $query]);
                return response()->json([]);
            }

            // Sanitize the query to prevent SQL injection
            $query = strip_tags($query);

            // Log the search attempt for debugging
            Log::info("Pharmacy patient search attempt", [
                'query' => $query,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            // Check if Patient model exists and is accessible
            if (!class_exists(Patient::class)) {
                Log::error("Patient model not found");
                throw new \Exception("Patient model not accessible");
            }

            // Check database connection
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                Log::error("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }

            // Search for patients with better error handling
            $patients = Patient::where(function ($q) use ($query) {
                $q->where('uhid', 'LIKE', "%{$query}%")
                    ->orWhere('full_name', 'LIKE', "%{$query}%")
                    ->orWhere('mobile', 'LIKE', "%{$query}%");
            })
                ->select([
                    'id',
                    'uhid',
                    'full_name',
                    'gender',
                    'age',
                    'mobile',
                    'prakriti',
                    'allergies',
                    'photo_path'
                ])
                ->limit(10)
                ->get();

            // Log the search results for debugging
            Log::info("Pharmacy patient search results", [
                'query' => $query,
                'count' => $patients->count(),
                'results' => $patients->toArray()
            ]);

            // If no results found, return empty array
            if ($patients->isEmpty()) {
                Log::info("Pharmacy patient search: No results found", ['query' => $query]);
                return response()->json([]);
            }

            // Format the results
            $formattedPatients = $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'uhid' => $patient->uhid ?? 'N/A',
                    'full_name' => $patient->full_name ?? 'N/A',
                    'gender' => $patient->gender ?? 'N/A',
                    'age' => $patient->age ?? 'N/A',
                    'mobile' => $patient->mobile ?? 'N/A',
                    'prakriti' => $patient->prakriti ?? 'N/A',
                    'allergies' => $patient->allergies ?? 'None',
                    'photo_path' => $patient->photo_path ?? null
                ];
            });

            Log::info("Pharmacy patient search: Returning formatted results", [
                'query' => $query,
                'count' => $formattedPatients->count()
            ]);

            return response()->json($formattedPatients);
        } catch (\Exception $e) {
            Log::error('Pharmacy patient search error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while searching patients',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient prescriptions
     */
    public function getPatientPrescriptions(Request $request)
    {
        $patientId = $request->get('patient_id');

        if (!$patientId) {
            return response()->json(['success' => false, 'message' => 'Patient ID is required']);
        }

        $prescriptions = Prescription::with(['doctor', 'items'])
            ->where('patient_id', $patientId)
            ->orderBy('prescription_date', 'desc')
            ->get();

        if ($prescriptions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No prescriptions found for this patient']);
        }

        return response()->json([
            'success' => true,
            'prescriptions' => $prescriptions
        ]);
    }

    /**
     * Get medicine inventory status
     */
    public function getMedicineInventory()
    {
        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer'])
            ->where('status', 1)
            ->select(
                'id',
                'name',
                'code',
                'medicine_type_id',
                'medicine_category_id',
                'manufacturer_id',
                'batch_number',
                'expiry_date',
                'initial_stock_quantity',
                'minimum_stock_level',
                'selling_price'
            )
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'medicines' => $medicines
        ]);
    }

    /**
     * Process dispense medication
     */
    public function processDispense(Request $request)
    {
        Log::info('Process dispense request received:', $request->all());

        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|exists:patients,id',
            'prescription_id' => 'required|exists:prescriptions,id',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required',
            'medicines.*.quantity' => 'required|integer|min:1',
            'medicines.*.unit_price' => 'required|numeric|min:0',
            'medicines.*.total_price' => 'required|numeric|min:0',
            'medicines.*.is_additional' => 'boolean',
            'payment_mode' => 'required|in:cash,card,upi',
            'dispense_status' => 'required|in:ready,partial,out_of_stock',
            'dispensed_by' => 'required|exists:users,id',
            'special_instructions' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'gst_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ], [
            'patient_id.exists' => 'Selected patient does not exist in the database.',
            'prescription_id.required' => 'Prescription ID is required. Please select a prescription.',
            'prescription_id.exists' => 'Selected prescription does not exist in the database.',
            'medicines.required' => 'At least one medicine must be selected.',
            'medicines.array' => 'Medicines must be provided as an array.',
            'medicines.min' => 'At least one medicine must be selected.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'request_data' => $request->all() // Include request data for debugging
            ]);
        }

        DB::beginTransaction();
        try {
            // Generate receipt number
            $lastDispense = MedicineDispense::orderBy('id', 'desc')->first();
            $receiptNumber = 'RX-' . date('Y') . '-' . str_pad(($lastDispense ? $lastDispense->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create dispense record
            $dispense = MedicineDispense::create([
                'patient_id' => $request->patient_id,
                'prescription_id' => $request->prescription_id,
                'dispensed_by' => $request->dispensed_by,
                'dispense_date' => now(),
                'payment_mode' => $this->getPaymentModeId($request->payment_mode),
                'dispense_status' => $request->dispense_status,
                'special_instructions' => $request->special_instructions,
                'subtotal' => $request->subtotal,
                'gst_amount' => $request->gst_amount,
                'total_amount' => $request->total_amount,
                'receipt_number' => $receiptNumber,
            ]);

            Log::info('Dispense record created:', ['id' => $dispense->id, 'receipt_number' => $receiptNumber]);

            // Process each medicine
            foreach ($request->medicines as $medicineData) {
                // Find the medicine
                $medicine = Medicine::find($medicineData['medicine_id']);

                if (!$medicine) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Medicine not found: ' . $medicineData['medicine_id']
                    ]);
                }

                // Check stock availability
                if ($medicine->initial_stock_quantity < $medicineData['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$medicine->name}. Available: {$medicine->initial_stock_quantity}, Requested: {$medicineData['quantity']}. Please reduce the quantity or select a different medicine.",
                        'medicine_name' => $medicine->name,
                        'available_stock' => $medicine->initial_stock_quantity,
                        'requested_quantity' => $medicineData['quantity']
                    ]);
                }

                // Create dispense item record
                MedicineDispenseItem::create([
                    'dispense_id' => $dispense->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $medicineData['quantity'],
                    'unit_price' => $medicineData['unit_price'],
                    'total_price' => $medicineData['total_price'],
                ]);

                // Update stock quantity
                $medicine->decrement('initial_stock_quantity', $medicineData['quantity']);

                Log::info("Medicine dispensed:", [
                    'medicine_id' => $medicine->id,
                    'name' => $medicine->name,
                    'quantity' => $medicineData['quantity'],
                    'is_additional' => $medicineData['is_additional'] ?? false
                ]);
            }

            DB::commit();

            Log::info('Dispense completed successfully', [
                'dispense_id' => $dispense->id,
                'receipt_number' => $receiptNumber,
                'total_amount' => $request->total_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medication dispensed successfully',
                'dispense_id' => $dispense->id,
                'receipt_number' => $receiptNumber,
                'total_amount' => $request->total_amount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dispense failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to dispense medication: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment mode ID from payment method string
     */
    private function getPaymentModeId($paymentMethod)
    {
        $paymentModes = [
            'cash' => 1,
            'card' => 2,
            'upi' => 3,
        ];

        return $paymentModes[$paymentMethod] ?? 1; // Default to cash
    }



    /**
     * Store dispense data from form submission
     */
    public function storeDispenseForm(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Dispense form request received:', $request->all());

        // Validate the request
        $validator = Validator::make($request->all(), [
            'patient_id' => 'nullable|exists:patients,id',
            'prescription_id' => 'required|exists:prescriptions,id',
            'dispensed_by' => 'required|exists:users,id',
            'dispense_status' => 'required|in:ready,partial,out_of_stock',
            'payment' => 'required|in:cash,card,upi',
            'special_instructions' => 'nullable|string|max:500',
            'medicines' => 'nullable|string',
            'subtotal' => 'nullable|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate receipt number
            $lastDispense = MedicineDispense::orderBy('id', 'desc')->first();
            $receiptNumber = 'RX-' . date('Y') . '-' . str_pad(($lastDispense ? $lastDispense->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Calculate totals
            $subtotal = $request->subtotal ?? 0;
            $gstAmount = $request->gst_amount ?? 0;
            $totalAmount = $request->total_amount ?? 0;

            // Create dispense record
            $dispense = MedicineDispense::create([
                'patient_id' => $request->patient_id,
                'prescription_id' => $request->prescription_id,
                'dispensed_by' => $request->dispensed_by,
                'dispense_date' => now(),
                'payment_mode' => $this->getPaymentModeId($request->payment),
                'dispense_status' => $request->dispense_status,
                'special_instructions' => $request->special_instructions,
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'receipt_number' => $receiptNumber,
            ]);

            Log::info('Dispense record created with ID:', $dispense->id);

            // Process selected medicines from the form
            $selectedMedicines = json_decode($request->medicines, true) ?? [];
            Log::info('Selected medicines:', $selectedMedicines);

            if (!empty($selectedMedicines)) {
                foreach ($selectedMedicines as $medicineData) {
                    // Find medicine by ID or name
                    $medicine = null;
                    if (isset($medicineData['id']) && $medicineData['id']) {
                        $medicine = Medicine::find($medicineData['id']);
                    } elseif (isset($medicineData['name'])) {
                        $medicine = Medicine::where('name', 'LIKE', '%' . $medicineData['name'] . '%')
                            ->where('status', 1)
                            ->first();
                    }

                    if ($medicine) {
                        $quantity = $medicineData['quantity'] ?? 1;
                        $unitPrice = $medicineData['unit_price'] ?? $medicine->selling_price;
                        $totalPrice = $medicineData['total_price'] ?? ($unitPrice * $quantity);

                        // Check stock availability
                        if ($medicine->initial_stock_quantity < $quantity) {
                            DB::rollBack();
                            return redirect()->back()
                                ->with('error', "Insufficient stock for {$medicine->name}. Available: {$medicine->initial_stock_quantity}, Requested: {$quantity}")
                                ->withInput();
                        }

                        // Create dispense item record
                        MedicineDispenseItem::create([
                            'dispense_id' => $dispense->id,
                            'medicine_id' => $medicine->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                        ]);

                        // Update stock quantity
                        $medicine->decrement('initial_stock_quantity', $quantity);

                        Log::info("Medicine dispensed: {$medicine->name}, Quantity: {$quantity}");
                    } else {
                        Log::warning("Medicine not found for data:", $medicineData);
                    }
                }
            }

            DB::commit();
            Log::info('Dispense completed successfully');

            return redirect()->route('pharmacy.dispense')
                ->with('success', 'Medication dispensed successfully! Receipt #: ' . $receiptNumber);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dispense failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to dispense medication: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get available medicines for dispense
     */
    public function getAvailableMedicines()
    {
        $medicines = Medicine::where('status', 1)
            ->where('initial_stock_quantity', '>', 0)
            ->select('id', 'name', 'code', 'initial_stock_quantity', 'selling_price')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'medicines' => $medicines
        ]);
    }

    /**
     * Display restock form
     */
    public function restock()
    {
        $pagename = 'Restock Medicine';
        $breadcrumb = 'Restock Medicine';

        $medicines = Medicine::orderBy('name')->get();

        return view('backend.pharmacy.restock', compact('pagename', 'breadcrumb', 'medicines'));
    }
}
