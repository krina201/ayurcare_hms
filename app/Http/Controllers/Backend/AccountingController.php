<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\MasterTransactionCategory;
use App\Models\MasterPaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;


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
        // Basic validation only for data types and existence
        $validatedData = [
            'transaction_type' => (int) $request->transaction_type,
            'transaction_date' => $request->transaction_date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'reference_number' => $request->reference_number,
            'patient_vendor' => $request->patient_vendor,
            'notes' => $request->notes,
        ];

        // Generate transaction ID
        $transactionId = $this->generateTransactionId();

        // Create new accounting instance
        $accounting = new Accounting();
        $accounting->transaction_id = $transactionId;
        $accounting->transaction_type = $validatedData['transaction_type'];
        $accounting->transaction_date = $validatedData['transaction_date'];
        $accounting->category_id = $validatedData['category_id'];
        $accounting->amount = $validatedData['amount'];
        $accounting->payment_mode = $validatedData['payment_mode'];
        $accounting->reference_number = $validatedData['reference_number'];
        $accounting->patient_vendor = $validatedData['patient_vendor'];
        $accounting->notes = $validatedData['notes'];

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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pagename = 'Accounting Management';
        $breadcrumb = 'Financial Transactions';

        // Find the transaction to edit
        $editTransaction = Accounting::with(['category', 'paymentMode'])->findOrFail($id);

        // Get all transactions for the table
        $query = Accounting::with(['category', 'paymentMode'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

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
            'editTransaction',
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the transaction to update
        $accounting = Accounting::findOrFail($id);

        // Basic validation only for data types and existence
        $validatedData = [
            'transaction_type' => (int) $request->transaction_type,
            'transaction_date' => $request->transaction_date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'reference_number' => $request->reference_number,
            'patient_vendor' => $request->patient_vendor,
            'notes' => $request->notes,
        ];

        // Update accounting instance
        $accounting->transaction_type = $validatedData['transaction_type'];
        $accounting->transaction_date = $validatedData['transaction_date'];
        $accounting->category_id = $validatedData['category_id'];
        $accounting->amount = $validatedData['amount'];
        $accounting->payment_mode = $validatedData['payment_mode'];
        $accounting->reference_number = $validatedData['reference_number'];
        $accounting->patient_vendor = $validatedData['patient_vendor'];
        $accounting->notes = $validatedData['notes'];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($accounting->attachment && file_exists(public_path($accounting->attachment))) {
                try {
                    unlink(public_path($accounting->attachment));
                } catch (Exception $e) {
                    // Log the error but don't fail the update
                    Log::warning('Failed to delete old attachment: ' . $accounting->attachment . ' - ' . $e->getMessage());
                }
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

        // Save the accounting record
        if ($accounting->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaction updated successfully!']);
            }

            return redirect()->route('accounting')->with('success', 'Transaction updated successfully!');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while updating the transaction']);
            }

            return redirect()->route('accounting')->with('error', 'An error occurred while updating the transaction');
        }
    }

    // Transaction for delete
    public function destroy($id)
    {
        // Find the accounting record
        $accounting = Accounting::findOrFail($id);

        // Delete associated attachment file if exists
        if ($accounting->attachment && file_exists(public_path($accounting->attachment))) {
            try {
                unlink(public_path($accounting->attachment));
            } catch (Exception $e) {
                // Log the error but don't fail the deletion
                Log::warning('Failed to delete attachment file: ' . $accounting->attachment . ' - ' . $e->getMessage());
            }
        }

        if ($accounting->delete()) {
            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to delete Transaction.'], 500);
    }
}
