<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\MedicineDispenseItem;
use App\Models\MedicineRestock;
use App\Models\MedicineRestockItem;
use App\Models\MasterMedicineType;
use App\Models\MasterMedicineCategory;
use App\Models\MasterManufacturer;
use App\Models\MasterMeasurement;
use App\Models\MasterPaymentMode;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\VendorMaster;
use App\Models\Bill;
use App\Services\PatientSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PharmacyController extends Controller
{
    protected $patientSearchService;

    public function __construct(PatientSearchService $patientSearchService)
    {
        $this->patientSearchService = $patientSearchService;
    }

    //   Display the pharmacy dashboard
    public function index()
    {
        $pagename = 'Pharmacy Management';
        $breadcrumb = 'Pharmacy Management';

        // Get statistics
        $totalMedicines = Medicine::count();
        $activeMedicines = Medicine::where('status', 1)->count();
        $expiringSoon = Medicine::where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->where('status', 1)
            ->where('track_expiry', true)
            ->count();
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


    // Edit form view
    public function edit($id)
    {
        $pagename = 'Edit Medicine';
        $breadcrumb = 'Edit Medicine';

        // Find the medicine
        $medicine = Medicine::findOrFail($id);

        // Get master data for dropdowns
        $medicineTypes = MasterMedicineType::orderBy('name')->get();
        $medicineCategories = MasterMedicineCategory::orderBy('name')->get();
        $manufacturers = MasterManufacturer::orderBy('name')->get();
        $measurements = MasterMeasurement::orderBy('name')->get();

        return view('backend.pharmacy.edit', compact(
            'pagename',
            'breadcrumb',
            'medicine',
            'medicineTypes',
            'medicineCategories',
            'manufacturers',
            'measurements'
        ));
    }

    // Update medicine data in database
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
            'current_stock_quantity' => 'nullable|integer|min:0',
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
            'current_stock_quantity' => $request->current_stock_quantity,
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
            'save_type' => $request->save_type,
            'updated_at' => now()
        ]);

        // DB::commit();
        if ($medicine->save()) {

            $message = $request->save_type == 0
                ? 'Medicine saved as draft successfully!'
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


    //  Display medicine inventory

    public function inventory()
    {
        $pagename = 'Medicine Inventory';
        $breadcrumb = 'Medicine Inventory';

        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pharmacy.inventory', compact('pagename', 'breadcrumb', 'medicines'));
    }


    //  Show medicine details
    public function show($id)
    {
        $medicine = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])->findOrFail($id);
        $pagename = 'Medicine Details';
        $breadcrumb = 'Medicine Details';

        return view('backend.pharmacy.show', compact('pagename', 'breadcrumb', 'medicine'));
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

        // Get recent dispensations with relationships
        $recentDispensations = collect(); // Default empty collection

        // Check if table exists before querying
        if (Schema::hasTable('medicine_dispenses')) {
            $recentDispensations = MedicineDispense::with([
                'patient:id,uhid,full_name,photo_path',
                'prescription.doctor:id,name',
                'dispensedBy:id,name',
                'items'
            ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
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

    //  Search patient by UHID, name, or mobile (using Appointment module pattern)
    public function searchPatient(Request $request)
    {
        return $this->patientSearchService->searchPatients($request, 'pharmacy');
    }


    //   Get patient prescriptions
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


    //  Get medicine inventory status
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

    // Process dispense medication
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

            // Create bill record for the dispense
            $this->createBillForDispense($dispense, $request->total_amount);

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

            // Create bill record for the dispense
            $this->createBillForDispense($dispense, $totalAmount);

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
     * Display all dispensations
     */
    public function allDispensations()
    {
        $pagename = 'All Dispensations';
        $breadcrumb = 'All Dispensations';

        // Get all dispensations with relationships and pagination
        $dispensations = collect(); // Default empty collection

        // Check if table exists before querying
        if (Schema::hasTable('medicine_dispenses')) {
            $dispensations = MedicineDispense::with([
                'patient:id,uhid,full_name,photo_path',
                'prescription.doctor:id,name',
                'dispensedBy:id,name',
                'items.medicine:id,name',
                'paymentMode:id,name'
            ])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            Log::warning('medicine_dispenses table does not exist');
        }

        return view('backend.pharmacy.dispensations_view', compact(
            'pagename',
            'breadcrumb',
            'dispensations'
        ));
    }

    /**
     * Display restock form
     */
    public function restock()
    {
        $pagename = 'Restock Medicine';
        $breadcrumb = 'Restock Medicine';

        // Get master data for dropdowns
        $medicines = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $vendors = VendorMaster::orderBy('name')->get();
        $paymentModes = MasterPaymentMode::orderBy('name')->get();
        $measurements = MasterMeasurement::orderBy('name')->get();

        // Get recent restocks
        $recentRestocks = MedicineRestock::with(['vendor', 'paymentMode', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('backend.pharmacy.restock', compact(
            'pagename',
            'breadcrumb',
            'medicines',
            'vendors',
            'paymentModes',
            'measurements',
            'recentRestocks'
        ));
    }

    /**
     * Store restock purchase
     */
    public function storeRestock(Request $request)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'purchase_date' => 'required|date',
                'invoice_number' => 'required|string|max:255',
                'invoice_date' => 'required|date',
                'vendor_id' => 'required|exists:vendor_master,id',
                'vendor_contact' => 'required|string|size:10|regex:/^[0-9]{10}$/',
                'gstin' => 'nullable|string|max:20',
                'payment_mode_id' => 'required|exists:master_payment_mode,id',
                'notes' => 'nullable|string',
                'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'items' => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.batch_number' => 'required|string|max:100',
                'items.*.manufacturing_date' => 'required|date',
                'items.*.expiry_date' => 'required|date|after:items.*.manufacturing_date',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.measurement_id' => 'required|exists:master_measurement,id',
                'items.*.unit_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'gst_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'save_type' => 'required|in:0,1'
            ],

        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Generate purchase ID
        $lastRestock = MedicineRestock::orderBy('id', 'desc')->first();
        $purchaseId = 'PUR-' . date('Y') . '-' . str_pad(($lastRestock ? $lastRestock->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        // Handle file upload
        $invoiceFilePath = null;
        if ($request->hasFile('invoice_file')) {
            $file = $request->file('invoice_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('backend-assets/media/uploads/invoices'), $fileName);
            $invoiceFilePath = 'backend-assets/media/uploads/invoices/' . $fileName;
        }

        // Create new restock instance
        $restock = new MedicineRestock();
        $restock->purchase_id = $purchaseId;
        $restock->purchase_date = $validatedData['purchase_date'];
        $restock->invoice_number = $validatedData['invoice_number'];
        $restock->invoice_date = $validatedData['invoice_date'];
        $restock->vendor_id = $validatedData['vendor_id'];
        $restock->vendor_contact = $validatedData['vendor_contact'];
        $restock->gstin = $validatedData['gstin'] ?? null;
        $restock->payment_mode_id = $validatedData['payment_mode_id'];
        $restock->notes = $validatedData['notes'] ?? null;
        $restock->invoice_file_path = $invoiceFilePath;
        $restock->subtotal = $validatedData['subtotal'];
        $restock->discount = $validatedData['discount'] ?? 0;
        $restock->gst_amount = $validatedData['gst_amount'];
        $restock->total_amount = $validatedData['total_amount'];
        $restock->status = $validatedData['save_type'] == '1' ? 1 : 0;
        $restock->created_by = Auth::id();

        // Save the restock
        if ($restock->save()) {
            // Process each medicine item
            foreach ($validatedData['items'] as $itemData) {
                $totalPrice = $itemData['quantity'] * $itemData['unit_price'];

                // Create new restock item instance
                $restockItem = new MedicineRestockItem();
                $restockItem->restock_id = $restock->id;
                $restockItem->medicine_id = $itemData['medicine_id'];
                $restockItem->batch_number = $itemData['batch_number'];
                $restockItem->manufacturing_date = $itemData['manufacturing_date'];
                $restockItem->expiry_date = $itemData['expiry_date'];
                $restockItem->quantity = $itemData['quantity'];
                $restockItem->measurement_id = $itemData['measurement_id'];
                $restockItem->unit_price = $itemData['unit_price'];
                $restockItem->total_price = $totalPrice;
                $restockItem->save();

                // Update medicine stock if status is completed
                if ($validatedData['save_type'] == '1') {
                    $medicine = Medicine::find($itemData['medicine_id']);
                    $medicine->increment('initial_stock_quantity', $itemData['quantity']);

                    // Update medicine details with latest batch info
                    $medicine->update([
                        'batch_number' => $itemData['batch_number'],
                        'manufacturing_date' => $itemData['manufacturing_date'],
                        'expiry_date' => $itemData['expiry_date'],
                        'purchase_price' => $itemData['unit_price']
                    ]);
                }
            }

            $message = $validatedData['save_type'] == '1'
                ? 'Restock purchase completed successfully!'
                : 'Restock purchase saved as draft successfully!';

            return redirect()->route('pharmacy.restock')->with('success', $message . ' Purchase ID: ' . $purchaseId);
        } else {
            return redirect()->route('pharmacy.restock')->with('error', 'Something went wrong while saving the Restock Purchase');
        }
    }

    /**
     * Get medicine details for restock
     */
    public function getMedicineDetails(Request $request)
    {
        $medicineId = $request->get('medicine_id');

        if (!$medicineId) {
            return response()->json(['success' => false, 'message' => 'Medicine ID is required']);
        }

        $medicine = Medicine::with(['medicineType', 'medicineCategory', 'manufacturer', 'measurement'])
            ->find($medicineId);

        if (!$medicine) {
            return response()->json(['success' => false, 'message' => 'Medicine not found']);
        }

        return response()->json([
            'success' => true,
            'medicine' => [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'code' => $medicine->code,
                'type' => $medicine->medicineType->name ?? 'N/A',
                'category' => $medicine->medicineCategory->name ?? 'N/A',
                'manufacturer' => $medicine->manufacturer->name ?? 'N/A',
                'measurement' => $medicine->measurement->name ?? 'N/A',
                'current_stock' => $medicine->initial_stock_quantity,
                'purchase_price' => $medicine->purchase_price,
                'selling_price' => $medicine->selling_price
            ]
        ]);
    }

    /**
     * Display all restock purchases
     */
    public function allRestocks()
    {
        $pagename = 'All Restock Purchases';
        $breadcrumb = 'All Restock Purchases';

        $restocks = MedicineRestock::with(['vendor', 'paymentMode', 'createdBy', 'items.medicine'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.pharmacy.all_restocks', compact(
            'pagename',
            'breadcrumb',
            'restocks'
        ));
    }

    /**
     * View restock details
     */
    public function viewRestock($id)
    {
        $restock = MedicineRestock::with([
            'vendor',
            'paymentMode',
            'createdBy',
            'items.medicine.medicineType',
            'items.medicine.medicineCategory',
            'items.medicine.manufacturer',
            'items.measurement'
        ])->findOrFail($id);

        $pagename = 'Restock Details';
        $breadcrumb = 'Restock Details';

        return view('backend.pharmacy.view_restock', compact(
            'pagename',
            'breadcrumb',
            'restock'
        ));
    }

    /**
     * Update restock status
     */
    public function updateRestockStatus(Request $request, $id)
    {
        $restock = MedicineRestock::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ]);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $restock->status;
            $restock->update(['status' => $request->status]);

            // If changing from draft to completed, update medicine stocks
            if ($oldStatus === 'draft' && $request->status === 'completed') {
                foreach ($restock->items as $item) {
                    $medicine = Medicine::find($item->medicine_id);
                    $medicine->increment('initial_stock_quantity', $item->quantity);

                    // Update medicine details with latest batch info
                    $medicine->update([
                        'batch_number' => $item->batch_number,
                        'manufacturing_date' => $item->manufacturing_date,
                        'expiry_date' => $item->expiry_date,
                        'purchase_price' => $item->unit_price
                    ]);
                }
            }

            // If changing from completed to draft, reverse stock updates
            if ($oldStatus === 'completed' && $request->status === 'draft') {
                foreach ($restock->items as $item) {
                    $medicine = Medicine::find($item->medicine_id);
                    $medicine->decrement('initial_stock_quantity', $item->quantity);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Restock status updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update restock status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restock status'
            ]);
        }
    }

    /**
     * Create bill record for dispense
     */
    private function createBillForDispense($dispense, $totalAmount)
    {
        $invoiceNumber = $this->generateInvoiceNumber();

        // Get dispense items for bill items
        $billItems = [];
        foreach ($dispense->items as $item) {
            $billItems[] = [
                'description' => $item->medicine->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total_price
            ];
        }

        $bill = Bill::create([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $dispense->patient_id,
            'dispense_id' => $dispense->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $dispense->subtotal,
            'tax_amount' => $dispense->gst_amount,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'status' => 1, // 1 = pending (as per migration comment)
            'bill_items' => json_encode($billItems)
        ]);

        return $bill;
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastBill = Bill::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
