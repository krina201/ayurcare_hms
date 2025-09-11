<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\TreatmentPlan;
use App\Models\MasterPaymentMode;
use App\Services\PatientSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Display a listing of bills
     */
    public function index(Request $request)
    {
        $pagename = 'Bills & Payments';
        $breadcrumb = 'Billing Management';

        // Handle success message from payment processing
        if ($request->has('success')) {
            session()->flash('success', $request->success);
        }

        $query = Bill::with([
            'patient',
            'appointment.doctor',
            'prescription.doctor',
            'treatmentPlan.doctor',
            'dispense.dispensedBy',
            'paymentMode'
        ])
            ->orderBy('invoice_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('uhid', 'like', "%{$search}%");
                    });
            });
        }

        // Patient-specific bills
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $bills = $query->paginate(15)->withQueryString();

        // Calculate billing summary stats
        $totalBills = Bill::sum('total_amount');
        $paidAmount = Bill::paid()->sum('total_amount');
        $outstandingAmount = Bill::whereIn('status', [1, 3])->get()->sum(function ($bill) {
            return $bill->total_amount - $bill->paid_amount;
        });
        $lastPayment = Bill::paid()->latest('updated_at')->first();

        $stats = [
            'total_bills' => $totalBills,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'last_payment' => $lastPayment,
            'total_bill_count' => Bill::count(),
            'paid_count' => Bill::paid()->count(),
            'pending_count' => Bill::pending()->count(),
            'overdue_count' => Bill::overdue()->count(),
            'formatted_total_bills' => '₹' . number_format($totalBills, 2),
            'formatted_paid_amount' => '₹' . number_format($paidAmount, 2),
            'formatted_outstanding_amount' => '₹' . number_format($outstandingAmount, 2),
        ];

        // Get patient data if patient_id is provided
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        // Get payment modes for payment processing
        $paymentModes = MasterPaymentMode::active()->get();

        return view('backend.bill.index', compact('bills', 'stats', 'patient', 'paymentModes', 'pagename', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new bill
     */
    public function create(Request $request)
    {
        $pagename = 'Create New Bill';
        $breadcrumb = 'Billing Management';

        $patients = Patient::select('id', 'full_name', 'uhid')->get();
        $appointments = collect();
        $prescriptions = collect();
        $treatmentPlans = collect();

        // If patient is selected, get related data
        if ($request->filled('patient_id')) {
            $appointments = Appointment::where('patient_id', $request->patient_id)
                ->where('status', 'completed')
                ->with(['doctor', 'department'])
                ->get();

            $prescriptions = Prescription::where('patient_id', $request->patient_id)
                ->with(['doctor', 'items.medicine'])
                ->get();

            $treatmentPlans = TreatmentPlan::where('patient_id', $request->patient_id)
                ->with('doctor')
                ->get();
        }

        return view('backend.bill.create', compact(
            'patients',
            'appointments',
            'prescriptions',
            'treatmentPlans',
            'pagename',
            'breadcrumb'
        ));
    }

    /**
     * Store a newly created bill
     */
    public function store(Request $request)
    {
        $validatedData = [
            'patient_id' => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'prescription_id' => $request->prescription_id,
            'treatment_plan_id' => $request->treatment_plan_id,
            'invoice_date' => $request->invoice_date ?? Carbon::today(),
            'due_date' => $request->due_date ?? Carbon::today()->addDays(15),
            'subtotal' => $request->subtotal,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'total_amount' => $request->total_amount,
            'notes' => $request->notes,
            'bill_items' => $request->bill_items,
        ];

        // Generate invoice number
        $invoiceNumber = $this->generateInvoiceNumber();

        // Create new bill
        $bill = new Bill();
        $bill->invoice_number = $invoiceNumber;
        $bill->patient_id = $validatedData['patient_id'];
        $bill->appointment_id = $validatedData['appointment_id'];
        $bill->prescription_id = $validatedData['prescription_id'];
        $bill->treatment_plan_id = $validatedData['treatment_plan_id'];
        $bill->invoice_date = $validatedData['invoice_date'];
        $bill->due_date = $validatedData['due_date'];
        $bill->subtotal = $validatedData['subtotal'];
        $bill->tax_amount = $validatedData['tax_amount'];
        $bill->discount_amount = $validatedData['discount_amount'];
        $bill->total_amount = $validatedData['total_amount'];
        $bill->paid_amount = 0;
        $bill->status = 1; // 1 = pending
        $bill->payment_method = 'pending';
        $bill->notes = $validatedData['notes'];
        $bill->bill_items = $validatedData['bill_items'];

        if ($bill->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Bill created successfully!', 'bill_id' => $bill->id]);
            }
            return redirect()->route('bill.index')->with('success', 'Bill created successfully!');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while creating the bill']);
            }
            return redirect()->back()->with('error', 'An error occurred while creating the bill');
        }
    }

    /**
     * Display the specified bill
     */
    public function show(Bill $bill)
    {
        $bill->load(['patient', 'appointment.doctor', 'prescription.doctor', 'treatmentPlan.doctor']);

        return view('backend.bill.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified bill
     */
    public function edit(Bill $bill)
    {
        $pagename = 'Edit Bill';
        $breadcrumb = 'Billing Management';

        $patients = Patient::select('id', 'full_name', 'uhid')->get();

        return view('backend.bill.edit', compact('bill', 'patients', 'pagename', 'breadcrumb'));
    }

    /**
     * Update the specified bill
     */
    public function update(Request $request, Bill $bill)
    {
        $validatedData = [
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $request->subtotal,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'total_amount' => $request->total_amount,
            'notes' => $request->notes,
            'bill_items' => $request->bill_items,
        ];

        $bill->invoice_date = $validatedData['invoice_date'];
        $bill->due_date = $validatedData['due_date'];
        $bill->subtotal = $validatedData['subtotal'];
        $bill->tax_amount = $validatedData['tax_amount'];
        $bill->discount_amount = $validatedData['discount_amount'];
        $bill->total_amount = $validatedData['total_amount'];
        $bill->notes = $validatedData['notes'];
        $bill->bill_items = $validatedData['bill_items'];

        // Update bill status based on payment
        $this->updateBillStatus($bill);

        if ($bill->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Bill updated successfully!']);
            }
            return redirect()->route('bill.index')->with('success', 'Bill updated successfully!');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while updating the bill']);
            }
            return redirect()->back()->with('error', 'An error occurred while updating the bill');
        }
    }

    /**
     * Remove the specified bill
     */
    public function destroy(Bill $bill)
    {
        if ($bill->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Cannot delete paid bills']);
        }

        if ($bill->delete()) {
            return response()->json(['success' => true, 'message' => 'Bill deleted successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the bill']);
        }
    }

    /**
     * Process payment for a bill
     */
    public function processPayment(Request $request, Bill $bill)
    {
        try {
            // Validate the request data
            $request->validate([
                'payment_amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|integer|exists:master_payment_mode,id',
                'payment_reference' => 'nullable|string|max:255',
            ]);

            $paymentAmount = $request->payment_amount;
            $paymentMethod = $request->payment_method;
            $paymentReference = $request->payment_reference;

            $outstandingAmount = $bill->total_amount - $bill->paid_amount;

            if ($paymentAmount > $outstandingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed outstanding amount of ₹' . number_format($outstandingAmount, 2)
                ]);
            }

            if ($paymentAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount must be greater than zero'
                ]);
            }

            // Log the payment details before processing
            Log::info('Processing payment', [
                'bill_id' => $bill->id,
                'payment_amount' => $paymentAmount,
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'current_paid_amount' => $bill->paid_amount,
                'total_amount' => $bill->total_amount
            ]);

            $bill->addPayment($paymentAmount, $paymentMethod, $paymentReference);

            // Log after processing
            Log::info('Payment processed successfully', [
                'bill_id' => $bill->id,
                'new_paid_amount' => $bill->fresh()->paid_amount,
                'new_status' => $bill->fresh()->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!',
                'bill_status' => $bill->fresh()->status,
                'paid_amount' => $bill->fresh()->paid_amount,
                'outstanding_amount' => $bill->fresh()->outstanding_amount
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'bill_id' => $bill->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payment. Please try again.'
            ]);
        }
    }

    /**
     * Search patients for bill creation
     */
    public function searchPatient(Request $request)
    {
        $patientSearchService = new PatientSearchService();
        return $patientSearchService->searchPatients($request, 'billing');
    }

    /**
     * Get patient billing history
     */
    public function getPatientBills(Request $request)
    {
        $patientId = $request->patient_id;

        $bills = Bill::where('patient_id', $patientId)
            ->with(['appointment.doctor', 'prescription.doctor', 'treatmentPlan.doctor'])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $patient = Patient::find($patientId);

        return response()->json([
            'bills' => $bills,
            'patient' => $patient,
        ]);
    }

    /**
     * Get appointment data for bill creation
     */
    public function getAppointmentData(Request $request)
    {
        $appointmentId = $request->appointment_id;

        $appointment = Appointment::with(['patient', 'doctor', 'department'])
            ->find($appointmentId);

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found']);
        }

        // Create bill items from appointment
        $billItems = [
            [
                'description' => $appointment->appointment_type . ' - ' . ($appointment->doctor->full_name ?? 'Doctor'),
                'quantity' => 1,
                'amount' => $appointment->fee ?? 0,
                'type' => 'appointment'
            ]
        ];

        return response()->json([
            'success' => true,
            'appointment' => $appointment,
            'bill_items' => $billItems,
            'subtotal' => $appointment->fee ?? 0,
            'total_amount' => $appointment->fee ?? 0
        ]);
    }

    /**
     * Get prescription data for bill creation
     */
    public function getPrescriptionData(Request $request)
    {
        $prescriptionId = $request->prescription_id;

        $prescription = Prescription::with(['patient', 'doctor', 'items.medicine'])
            ->find($prescriptionId);

        if (!$prescription) {
            return response()->json(['success' => false, 'message' => 'Prescription not found']);
        }

        // Create bill items from prescription
        $billItems = [];
        $subtotal = 0;

        foreach ($prescription->items as $item) {
            $amount = $item->medicine->price ?? 0;
            $billItems[] = [
                'description' => $item->medicine->name ?? 'Medicine',
                'quantity' => $item->quantity ?? 1,
                'amount' => $amount,
                'type' => 'medicine'
            ];
            $subtotal += ($item->quantity ?? 1) * $amount;
        }

        return response()->json([
            'success' => true,
            'prescription' => $prescription,
            'bill_items' => $billItems,
            'subtotal' => $subtotal,
            'total_amount' => $subtotal
        ]);
    }

    /**
     * Export bills
     */
    public function export(Request $request)
    {
        // Implementation for exporting bills to PDF/Excel
        // This would typically use a package like Laravel Excel or DomPDF
        return response()->json(['success' => true, 'message' => 'Export functionality will be implemented']);
    }

    /**
     * Update bill status based on payment
     */
    private function updateBillStatus($bill)
    {
        $outstandingAmount = $bill->total_amount - $bill->paid_amount;

        if ($outstandingAmount <= 0) {
            $bill->status = 2; // 2 = paid
        } elseif ($bill->due_date < now() && $bill->status !== 2) {
            $bill->status = 3; // 3 = overdue
        } else {
            $bill->status = 1; // 1 = pending
        }
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
