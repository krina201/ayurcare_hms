<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\MasterTransactionCategory;
use App\Models\MasterPaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AccountingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pagename = 'Accounting Management';
        $breadcrumb = 'Financial Transactions';

        $query = Accounting::with(['category', 'paymentMode'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('patient_vendor', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('transaction_type') && $request->transaction_type !== '') {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Calculate stats
        $todayReceipts = Accounting::receipts()->today()->sum('amount');
        $todayPayments = Accounting::payments()->today()->sum('amount');
        $cashBalance = Accounting::receipts()->whereHas('paymentMode', function ($q) {
            $q->where('code', 'CASH');
        })->sum('amount') - Accounting::payments()->whereHas('paymentMode', function ($q) {
            $q->where('code', 'CASH');
        })->sum('amount');
        $bankBalance = Accounting::receipts()->whereHas('paymentMode', function ($q) {
            $q->where('code', 'BANK');
        })->sum('amount') - Accounting::payments()->whereHas('paymentMode', function ($q) {
            $q->where('code', 'BANK');
        })->sum('amount');

        // Get master data for forms
        $categories = MasterTransactionCategory::active()->get();
        $paymentModes = MasterPaymentMode::active()->get();

        return view('backend.accounting.index', compact(
            'transactions',
            'todayReceipts',
            'todayPayments',
            'cashBalance',
            'bankBalance',
            'categories',
            'paymentModes',
            'pagename',
            'breadcrumb'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'transaction_type' => 'required|in:0,1',
                'transaction_date' => 'required|date',
                'category_id' => 'required|exists:master_transaction_categories,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_mode' => 'required|exists:master_payment_mode,id',
                'reference_number' => 'nullable|string|max:255',
                'patient_vendor' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ],
            [

                'transaction_date.required' => 'Transaction Date field is required.',
                'transaction_date.date' => 'Transaction Date must be a valid date.',

                'category_id.required' => 'Transaction Category field is required.',
                'category_id.exists' => 'Selected Transaction Category is invalid.',

                'amount.required' => 'Amount field is required.',
                'amount.numeric' => 'Amount must be a valid number.',
                'amount.min' => 'Amount must be at least 0.01.',

                'payment_mode.required' => 'Payment Mode field is required.',
                'payment_mode.exists' => 'Selected Payment Mode is invalid.',

                'reference_number.max' => 'Reference Number may not be greater than 255 characters.',
                'patient_vendor.max' => 'Patient/Vendor field may not be greater than 255 characters.',
                'attachment.file' => 'Attachment must be a valid file.',
                'attachment.mimes' => 'Attachment must be a PDF, JPG, JPEG, or PNG file.',
                'attachment.max' => 'Attachment size must not exceed 5MB.',
                'transaction_type.required' => 'Transaction Type field is required.',
                'transaction_type.in' => 'Transaction Type must be either Receipt or Payment.',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Generate transaction ID
        $transactionId = $this->generateTransactionId();

        // Create new accounting instance
        $accounting = new Accounting();
        $accounting->transaction_id = $transactionId;
        $accounting->transaction_type = (int) $validatedData['transaction_type'];
        $accounting->transaction_date = $validatedData['transaction_date'];
        $accounting->category_id = $validatedData['category_id'];
        $accounting->amount = $validatedData['amount'];
        $accounting->payment_mode = $validatedData['payment_mode'];
        $accounting->reference_number = $validatedData['reference_number'] ?? null;
        $accounting->patient_vendor = $validatedData['patient_vendor'] ?? null;
        $accounting->notes = $validatedData['notes'] ?? null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('backend-assets/media/accounting/attachments');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $accounting->attachment = 'backend-assets/media/accounting/attachments/' . $fileName;
        }

        // Save the accounting record
        if ($accounting->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaction recorded successfully!']);
            }

            return redirect()->route('accounting')->with('success', 'Transaction recorded successfully!');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while saving the transaction']);
            }

            return redirect()->route('accounting')->with('error', 'An error occurred while saving the transaction');
        }
    }



    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId()
    {
        $prefix = 'TRN';
        $year = date('Y');
        $lastTransaction = Accounting::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get transaction statistics
     */
    public function getStats()
    {
        $todayReceipts = Accounting::receipts()->today()->sum('amount');
        $todayPayments = Accounting::payments()->today()->sum('amount');
        $monthReceipts = Accounting::receipts()->thisMonth()->sum('amount');
        $monthPayments = Accounting::payments()->thisMonth()->sum('amount');

        return response()->json([
            'today_receipts' => $todayReceipts,
            'today_payments' => $todayPayments,
            'month_receipts' => $monthReceipts,
            'month_payments' => $monthPayments,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the accounting record
        $accounting = Accounting::findOrFail($id);

        // Delete the attached file if it exists
        if ($accounting->attachment && file_exists(public_path($accounting->attachment))) {
            unlink(public_path($accounting->attachment));
        }

        // Delete the record
        $accounting->delete();

        // Return success response
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully!'
            ]);
        }

        return redirect()->route('accounting')->with('success', 'Transaction deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $accounting = Accounting::with(['category', 'paymentMode'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'transaction' => $accounting
            ]);
        }

        // For non-AJAX requests, redirect to the main page with edit data
        return redirect()->route('accounting')->with('edit_transaction', $accounting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $accounting = Accounting::findOrFail($id);

        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'transaction_type' => 'required|in:0,1',
                'transaction_date' => 'required|date',
                'category_id' => 'required|exists:master_transaction_categories,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_mode' => 'required|exists:master_payment_mode,id',
                'reference_number' => 'nullable|string|max:255',
                'patient_vendor' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ],
            [
                'transaction_date.required' => 'Transaction Date field is required.',
                'transaction_date.date' => 'Transaction Date must be a valid date.',
                'category_id.required' => 'Transaction Category field is required.',
                'category_id.exists' => 'Selected Transaction Category is invalid.',
                'amount.required' => 'Amount field is required.',
                'amount.numeric' => 'Amount must be a valid number.',
                'amount.min' => 'Amount must be at least 0.01.',
                'payment_mode.required' => 'Payment Mode field is required.',
                'payment_mode.exists' => 'Selected Payment Mode is invalid.',
                'reference_number.max' => 'Reference Number may not be greater than 255 characters.',
                'patient_vendor.max' => 'Patient/Vendor field may not be greater than 255 characters.',
                'attachment.file' => 'Attachment must be a valid file.',
                'attachment.mimes' => 'Attachment must be a PDF, JPG, JPEG, or PNG file.',
                'attachment.max' => 'Attachment size must not exceed 5MB.',
                'transaction_type.required' => 'Transaction Type field is required.',
                'transaction_type.in' => 'Transaction Type must be either Receipt or Payment.',
            ]
        );

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Update the accounting record
        $accounting->transaction_type = (int) $validatedData['transaction_type'];
        $accounting->transaction_date = $validatedData['transaction_date'];
        $accounting->category_id = $validatedData['category_id'];
        $accounting->amount = $validatedData['amount'];
        $accounting->payment_mode = $validatedData['payment_mode'];
        $accounting->reference_number = $validatedData['reference_number'] ?? null;
        $accounting->patient_vendor = $validatedData['patient_vendor'] ?? null;
        $accounting->notes = $validatedData['notes'] ?? null;

        // Handle file upload if new file is provided
        if ($request->hasFile('attachment')) {
            // Delete old file if it exists
            if ($accounting->attachment && file_exists(public_path($accounting->attachment))) {
                unlink(public_path($accounting->attachment));
            }

            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('backend-assets/media/accounting/attachments');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $accounting->attachment = 'backend-assets/media/accounting/attachments/' . $fileName;
        }

        // Save the updated accounting record
        $accounting->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaction updated successfully!']);
        }

        return redirect()->route('accounting')->with('success', 'Transaction updated successfully!');
    }
}
