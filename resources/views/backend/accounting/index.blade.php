@extends('backend.layouts.master')

@section('css')
    <style>
        .payment-mode-option {
            transition: all 0.2s ease-in-out;
        }

        .payment-mode-option:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .payment-mode-option.active {
            transform: scale(0.98);
        }

        .transaction-type-btn {
            transition: all 0.2s ease-in-out;
            font-weight: 600;
        }

        .transaction-type-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .transaction-type-btn.active {
            transform: scale(0.98);
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">

        <div class="mb-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- TABS -->
        <div id="financeTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium"
                        data-tab="transactions">
                        Cash In/Out
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300"
                        data-tab="ledger">
                        Ledger Dashboard
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300"
                        data-tab="reports">
                        GST Reports
                    </button>
                </nav>
            </div>
        </div>

        <!-- TRANSACTION FORM -->
        <div id="transactionForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800" id="formTitle">Record Financial Transaction</h3>
                <div class="flex space-x-2">
                    <span id="receiptBtn"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800 cursor-pointer">
                        <i class="fa-solid fa-arrow-down mr-1"></i> Receipt
                    </span>
                    <span id="paymentBtn"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-yellow-100 text-ayur-yellow-800 cursor-pointer">
                        <i class="fa-solid fa-arrow-up mr-1"></i> Payment
                    </span>
                </div>
            </div>

            <form id="cashInOutForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="transactionId" name="transaction_id">
                <input type="hidden" id="isEdit" name="is_edit" value="0">

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- COLUMN 1 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Transaction Type</label>
                            <div class="mt-1">
                                <div class="flex space-x-2">
                                    <button type="button" id="receiptTypeBtn"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn active">
                                        <i class="fa-solid fa-arrow-down mr-2"></i> Receipt
                                    </button>
                                    <button type="button" id="paymentTypeBtn"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn">
                                        <i class="fa-solid fa-arrow-up mr-2"></i> Payment
                                    </button>
                                </div>
                                <input type="hidden" id="transactionType" name="transaction_type" value="0">
                            </div>
                            @error('transaction_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Transaction Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" id="transactionDate"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                value="{{ date('Y-m-d') }}">
                            @error('transaction_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Transaction ID</label>
                            <div class="mt-1 flex">
                                <input type="text" id="displayTransactionId" disabled
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border"
                                    placeholder="Auto generated">
                                <button type="button" id="refreshTransactionId"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Transaction Category <span
                                    class="text-red-500">*</span></label>
                            <select name="category_id" id="categoryId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Amount <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                    class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-ayur-green-500 focus:border-ayur-green-500 p-2 border"
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">INR</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 2 -->
                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Payment Mode <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 grid grid-cols-3 gap-2">
                                @foreach ($paymentModes as $mode)
                                    <label
                                        class="flex items-center justify-center bg-ayur-offwhite px-3 py-2 rounded-md border border-gray-300 cursor-pointer hover:bg-ayur-green-50 payment-mode-option text-ayur-brown-700"
                                        data-value="{{ $mode->id }}">
                                        <input type="radio" name="payment_mode" value="{{ $mode->id }}"
                                            class="hidden">
                                        <div class="flex flex-col items-center">
                                            @if ($mode->code === 'CASH')
                                                <i
                                                    class="fa-solid fa-money-bill-wave text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @elseif($mode->code === 'CARD')
                                                <i
                                                    class="fa-solid fa-credit-card text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @elseif($mode->code === 'BANK')
                                                <i
                                                    class="fa-solid fa-building-columns text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @elseif($mode->code === 'UPI')
                                                <i
                                                    class="fa-solid fa-mobile-screen text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @elseif($mode->code === 'WALLET')
                                                <i
                                                    class="fa-solid fa-wallet text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @elseif($mode->code === 'CHEQUE')
                                                <i
                                                    class="fa-solid fa-money-check text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @else
                                                <i
                                                    class="fa-solid fa-ellipsis text-ayur-green-600 mb-1 payment-mode-icon"></i>
                                            @endif
                                            <span class="text-sm payment-mode-text">{{ $mode->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_mode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Reference Number</label>
                            <input type="text" name="reference_number" id="referenceNumber"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Cheque/UPI/Transaction ID">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Patient/Vendor <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="patient_vendor" id="patientVendor"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Search patient or vendor">
                            @error('patient_vendor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Notes/Description <span
                                    class="text-red-500">*</span></label>
                            <textarea name="notes" id="notes"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="3" placeholder="Add any additional information about this transaction"></textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Attach Receipt/Invoice </label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-3 pb-3 border-2 border-gray-300 border-dashed rounded-md file-upload-container">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-file-invoice text-ayur-brown-400 text-xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-ayur-green-500">
                                            <span>Upload a file</span>
                                            <input id="attachment" name="attachment" type="file" class="sr-only"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>

                            <div id="fileInfo" class="mt-2 hidden">
                                <div class="flex items-center p-2 bg-green-50 border border-green-200 rounded-md">
                                    <i class="fa-solid fa-check-circle text-green-600 mr-2"></i>
                                    <span id="fileName" class="text-sm text-green-700"></span>
                                    <button type="button" id="removeFile"
                                        class="ml-auto text-red-600 hover:text-red-800">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" id="clearFormBtn"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Clear Form
                    </button>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>

        <!-- QUICK STATS -->
        <div id="quickStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Today's Receipts</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹{{ number_format($todayReceipts, 2) }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                        <i class="fa-solid fa-arrow-down"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Today's Payments</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹{{ number_format($todayPayments, 2) }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                        <i class="fa-solid fa-arrow-up"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-brown-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Cash Balance</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹{{ number_format($cashBalance, 2) }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-brown-100 p-2 text-ayur-brown-600">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Bank Balance</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹{{ number_format($bankBalance, 2) }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT TRANSACTIONS -->
        <div id="recentTransactions" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-ayur-brown-800"></h3>
            </div>

            {{-- <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" id="searchInput"
                            class="pl-8 pr-4 py-2 border border-gray-300 rounded-md  text-sm"
                            placeholder="Search transactions...">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fa-solid fa-search"></i>
                        </div>
                    </div>
                    <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                        View All <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </div> --}}

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="example">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Category</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient/Vendor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Mode</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Amount</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    {{ $transaction->transaction_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $transaction->transaction_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->transaction_type_class }}">
                                        <i
                                            class="fa-solid fa-arrow-{{ $transaction->transaction_type == 0 ? 'down' : 'up' }} mr-1"></i>
                                        {{ $transaction->transaction_type_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $transaction->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $transaction->patient_vendor ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    <span class="inline-flex items-center text-sm">
                                        <i
                                            class="fa-solid fa-{{ $transaction->paymentMode->code == 'CASH' ? 'money-bill-wave' : ($transaction->paymentMode->code == 'CARD' ? 'credit-card' : ($transaction->paymentMode->code == 'BANK' ? 'building-columns' : ($transaction->paymentMode->code == 'UPI' ? 'mobile-screen' : 'ellipsis'))) }} mr-1 text-ayur-green-600"></i>
                                        {{ $transaction->paymentMode->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->transaction_type == 0 ? 'text-ayur-green-600' : 'text-ayur-yellow-600' }}">
                                    {{ $transaction->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        onclick="viewTransaction({{ $transaction->id }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        onclick="editTransaction({{ $transaction->id }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900"
                                        onclick="deleteTransaction({{ $transaction->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
    </main>

    <!-- Edit Transaction Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ayur-brown-800">Edit Transaction</h3>
                        <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="editFormContent">
                        <!-- Edit form will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Transaction</h3>
                        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this transaction? This action
                            cannot be undone.</p>
                        <div class="flex justify-center space-x-3">
                            <button onclick="closeDeleteModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button id="confirmDeleteBtn"
                                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentTransactionId = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            initializeEventListeners();
            generateTransactionId();

            // Check for existing Laravel validation errors and display them
            checkForLaravelErrors();
        });

        function initializeForm() {
            // Set default transaction type
            document.getElementById('transactionType').value = '0';
            updateTransactionTypeButtons(0);
        }

        function initializeEventListeners() {
            // Transaction type buttons
            document.getElementById('receiptTypeBtn').addEventListener('click', () => setTransactionType(0));
            document.getElementById('paymentTypeBtn').addEventListener('click', () => setTransactionType(1));

            // Form submission
            document.getElementById('cashInOutForm').addEventListener('submit', handleFormSubmit);

            // Clear form button
            document.getElementById('clearFormBtn').addEventListener('click', clearForm);

            // Refresh transaction ID button
            document.getElementById('refreshTransactionId').addEventListener('click', generateTransactionId);

            // Search functionality - only if searchInput exists
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', handleSearch);
            }

            // Payment mode selection
            initializePaymentModeSelection();

            // Add real-time validation listeners
            addValidationListeners();
        }

        function setTransactionType(type) {
            document.getElementById('transactionType').value = type;
            updateTransactionTypeButtons(type);

            // Add visual feedback animation
            const clickedBtn = type === 0 ? document.getElementById('receiptTypeBtn') : document.getElementById(
                'paymentTypeBtn');
            clickedBtn.style.transform = 'scale(0.98)';
            setTimeout(() => {
                clickedBtn.style.transform = 'scale(1)';
            }, 150);
        }

        function updateTransactionTypeButtons(type) {
            const receiptBtn = document.getElementById('receiptTypeBtn');
            const paymentBtn = document.getElementById('paymentTypeBtn');

            if (type === 0) {
                receiptBtn.className =
                    'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn active';
                paymentBtn.className =
                    'inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn';
            } else {
                receiptBtn.className =
                    'inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn';
                paymentBtn.className =
                    'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center transaction-type-btn active';
            }
        }

        function generateTransactionId() {
            // This would typically call an API endpoint to generate the ID
            // For now, we'll create a simple timestamp-based ID
            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 1000);
            const transactionId = `TRN-${new Date().getFullYear()}-${String(random).padStart(4, '0')}`;

            document.getElementById('displayTransactionId').value = transactionId;
            document.getElementById('transactionId').value = transactionId;
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            // Validate form before submission
            if (!validateForm()) {
                return false;
            }

            const formData = new FormData(e.target);
            const isEdit = document.getElementById('isEdit').value === '1';

            if (isEdit) {
                // Update existing transaction
                updateTransaction(formData);
            } else {
                // Create new transaction
                createTransaction(formData);
            }
        }

        function createTransaction(formData) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            fetch('{{ route('accounting.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If response is not JSON, reload the page (success case)
                        window.location.reload();
                        return null;
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the transaction');
                });
        }

        function updateTransaction(formData) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            fetch(`{{ route('accounting.index') }}/${currentTransactionId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If response is not JSON, reload the page (success case)
                        window.location.reload();
                        return null;
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the transaction');
                });
        }

        function editTransaction(id) {
            currentTransactionId = id;

            fetch(`{{ route('accounting.index') }}/${id}/edit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Response is not JSON');
                    }
                })
                .then(data => {
                    if (data && data.transaction) {
                        populateEditForm(data.transaction);
                        document.getElementById('editModal').classList.remove('hidden');
                    } else {
                        alert('Invalid response format');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading the transaction');
                });
        }

        function populateEditForm(transaction) {
            // Populate the main form with transaction data
            document.getElementById('transactionType').value = transaction.transaction_type;
            document.getElementById('transactionDate').value = transaction.transaction_date;
            document.getElementById('categoryId').value = transaction.category_id;
            document.getElementById('amount').value = transaction.amount;
            document.getElementById('referenceNumber').value = transaction.reference_number || '';
            document.getElementById('patientVendor').value = transaction.patient_vendor || '';
            document.getElementById('notes').value = transaction.notes || '';

            // Update payment mode selection
            updatePaymentModeSelection(transaction.payment_mode);

            // Update form for edit mode
            document.getElementById('isEdit').value = '1';
            document.getElementById('formTitle').textContent = 'Edit Financial Transaction';
            document.getElementById('submitBtn').textContent = 'Update Transaction';

            // Update transaction type buttons
            updateTransactionTypeButtons(transaction.transaction_type);

            // Trigger validation for all fields
            setTimeout(() => {
                validateField('transactionDate');
                validateField('categoryId');
                validateField('amount');
                validateField('referenceNumber');
                validateField('patientVendor');
                validateField('notes');
                validateField('attachment');
                validatePaymentMode();
            }, 100);

            // Scroll to form
            document.getElementById('transactionForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deleteTransaction(id) {
            currentTransactionId = id;
            document.getElementById('deleteModal').classList.remove('hidden');

            document.getElementById('confirmDeleteBtn').onclick = function() {
                performDelete(id);
            };
        }

        function performDelete(id) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            fetch(`{{ route('accounting.index') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If response is not JSON, reload the page (success case)
                        window.location.reload();
                        return null;
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.reload();
                    } else if (data && data.message) {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the transaction');
                });
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function initializePaymentModeSelection() {
            // Add click event listeners to all payment mode options
            document.querySelectorAll('.payment-mode-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active state from all options
                    document.querySelectorAll('.payment-mode-option').forEach(opt => {
                        opt.classList.remove('bg-ayur-green-600', 'border-ayur-green-500',
                            'text-white', 'active');
                        opt.classList.add('bg-ayur-offwhite', 'border-gray-300',
                            'text-ayur-brown-700');

                        // Reset icon and text colors
                        const icon = opt.querySelector('.payment-mode-icon');
                        const text = opt.querySelector('.payment-mode-text');
                        if (icon) icon.classList.remove('text-white');
                        if (icon) icon.classList.add('text-ayur-green-600');
                        if (text) text.classList.remove('text-white');
                        if (text) text.classList.add('text-ayur-brown-700');
                    });

                    // Add active state to selected option
                    this.classList.remove('bg-ayur-offwhite', 'border-gray-300', 'text-ayur-brown-700');
                    this.classList.add('bg-ayur-green-600', 'border-ayur-green-500', 'text-white',
                        'active');

                    // Update icon and text colors
                    const icon = this.querySelector('.payment-mode-icon');
                    const text = this.querySelector('.payment-mode-text');
                    if (icon) {
                        icon.classList.remove('text-ayur-green-600');
                        icon.classList.add('text-white');
                    }
                    if (text) {
                        text.classList.remove('text-ayur-brown-700');
                        text.classList.add('text-white');
                    }

                    // Add visual feedback animation
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Validate payment mode after selection
                    validatePaymentMode();
                });
            });
        }

        function resetPaymentModeSelection() {
            // Reset all payment mode options to default state
            document.querySelectorAll('.payment-mode-option').forEach(option => {
                option.classList.remove('bg-ayur-green-600', 'border-ayur-green-500', 'text-white', 'active');
                option.classList.add('bg-ayur-offwhite', 'border-gray-300', 'text-ayur-brown-700');

                // Reset icon and text colors
                const icon = option.querySelector('.payment-mode-icon');
                const text = option.querySelector('.payment-mode-text');
                if (icon) icon.classList.remove('text-white');
                if (icon) icon.classList.add('text-ayur-green-600');
                if (text) text.classList.remove('text-white');
                if (text) text.classList.add('text-ayur-brown-700');
            });

            // Uncheck all radio buttons
            document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
                radio.checked = false;
            });
        }

        function updatePaymentModeSelection(paymentModeId) {
            // Reset all payment mode options first
            resetPaymentModeSelection();

            // Find the option with the matching payment mode ID
            const targetOption = document.querySelector(`.payment-mode-option[data-value="${paymentModeId}"]`);
            if (targetOption) {
                // Add active state to the target option
                targetOption.classList.remove('bg-ayur-offwhite', 'border-gray-300', 'text-ayur-brown-700');
                targetOption.classList.add('bg-ayur-green-600', 'border-ayur-green-500', 'text-white', 'active');

                // Update icon and text colors
                const icon = targetOption.querySelector('.payment-mode-icon');
                const text = targetOption.querySelector('.payment-mode-text');
                if (icon) {
                    icon.classList.remove('text-ayur-green-600');
                    icon.classList.add('text-white');
                }
                if (text) {
                    text.classList.remove('text-ayur-brown-700');
                    text.classList.add('text-white');
                }

                // Check the radio button
                const radio = targetOption.querySelector('input[type="radio"]');
                radio.checked = true;
            }
        }

        function clearForm() {
            document.getElementById('cashInOutForm').reset();
            document.getElementById('isEdit').value = '0';
            document.getElementById('formTitle').textContent = 'Record Financial Transaction';
            document.getElementById('submitBtn').textContent = 'Save Transaction';
            document.getElementById('transactionType').value = '0';
            updateTransactionTypeButtons(0);
            resetPaymentModeSelection();
            generateTransactionId();

            // Clear validation states
            clearValidationStates();
        }

        function handleSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Tab functionality
        document.querySelectorAll('[data-tab]').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Update active tab
                document.querySelectorAll('[data-tab]').forEach(btn => {
                    btn.classList.remove('border-ayur-green-500', 'text-ayur-green-600');
                    btn.classList.add('border-transparent', 'text-ayur-brown-600');
                });

                this.classList.remove('border-transparent', 'text-ayur-brown-600');
                this.classList.add('border-ayur-green-500', 'text-ayur-green-600');

                // Handle tab content (you can implement this based on your needs)
                console.log('Switched to tab:', tabName);
            });
        });

        // Validation Functions
        function addValidationListeners() {
            // Add listeners to all required fields
            const requiredFields = [
                'transactionDate',
                'categoryId',
                'amount',
                'referenceNumber',
                'patientVendor',
                'notes',
                'attachment'
            ];

            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('blur', () => validateField(fieldId));
                    field.addEventListener('input', () => validateField(fieldId));
                    field.addEventListener('change', () => validateField(fieldId));
                }
            });

            // Special listener for payment mode
            document.querySelectorAll('.payment-mode-option').forEach(option => {
                option.addEventListener('click', () => validatePaymentMode());
            });

            // File upload handling
            const attachmentInput = document.getElementById('attachment');
            if (attachmentInput) {
                attachmentInput.addEventListener('change', handleFileUpload);
            }

            // Remove file button
            const removeFileBtn = document.getElementById('removeFile');
            if (removeFileBtn) {
                removeFileBtn.addEventListener('click', removeFile);
            }
        }

        function validateForm() {
            let isValid = true;
            const requiredFields = [
                'transactionDate',
                'categoryId',
                'amount',
                'referenceNumber',
                'patientVendor',
                'notes',
                'attachment'
            ];

            // Validate all required fields
            requiredFields.forEach(fieldId => {
                if (!validateField(fieldId)) {
                    isValid = false;
                }
            });

            // Validate payment mode
            if (!validatePaymentMode()) {
                isValid = false;
            }

            // Show error message if form is invalid
            if (!isValid) {
                // showValidationMessage('Please fill in all required fields correctly.', 'error');
                return false;
            }

            return true;
        }

        function validateField(fieldId) {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';

            // Remove existing error states
            removeFieldError(field);

            // Field-specific validation
            switch (fieldId) {
                case 'transactionDate':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Transaction date is required';
                    } else {
                        const selectedDate = new Date(value);
                        const today = new Date();
                        if (selectedDate > today) {
                            isValid = false;
                            errorMessage = 'Transaction date cannot be in the future';
                        }
                    }
                    break;

                case 'categoryId':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Please select a transaction category';
                    }
                    break;

                case 'amount':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Amount is required';
                    } else if (isNaN(value) || parseFloat(value) <= 0) {
                        isValid = false;
                        errorMessage = 'Please enter a valid amount greater than 0';
                    }
                    break;

                case 'referenceNumber':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Reference number is required';
                    } else if (value.length < 3) {
                        isValid = false;
                        errorMessage = 'Reference number must be at least 3 characters';
                    }
                    break;

                case 'patientVendor':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Patient/Vendor name is required';
                    } else if (value.length < 2) {
                        isValid = false;
                        errorMessage = 'Patient/Vendor name must be at least 2 characters';
                    }
                    break;

                case 'notes':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Notes/Description is required';
                    } else if (value.length < 10) {
                        isValid = false;
                        errorMessage = 'Notes must be at least 10 characters';
                    }
                    break;

                case 'attachment':
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Please attach a receipt or invoice';
                    } else {
                        // Validate file type and size
                        const file = field.files[0];
                        if (file) {
                            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                            const maxSize = 5 * 1024 * 1024; // 5MB

                            if (!allowedTypes.includes(file.type)) {
                                isValid = false;
                                errorMessage = 'Please upload only PDF, JPG, or PNG files';
                            } else if (file.size > maxSize) {
                                isValid = false;
                                errorMessage = 'File size must be less than 5MB';
                            }
                        }
                    }
                    break;
            }

            if (isValid) {
                // Add success state
                addFieldSuccess(field);
            } else {
                // Add error state
                addFieldError(field, errorMessage);
            }

            return isValid;
        }

        function validatePaymentMode() {
            const selectedPaymentMode = document.querySelector('input[name="payment_mode"]:checked');

            // Remove existing error states
            removePaymentModeError();

            if (!selectedPaymentMode) {
                addPaymentModeError('Please select a payment mode');
                return false;
            }

            // Add success state
            addPaymentModeSuccess();
            return true;
        }

        function addFieldError(field, message) {
            field.classList.remove('border-gray-300', 'border-ayur-green-500', 'focus:border-ayur-green-500');
            field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mt-1 text-sm text-red-600 validation-error';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        function addFieldSuccess(field) {
            field.classList.remove('border-gray-300', 'border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            field.classList.add('border-ayur-green-500', 'focus:border-ayur-green-500', 'focus:ring-ayur-green-200');
        }

        function removeFieldError(field) {
            field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            field.classList.add('border-gray-300', 'focus:border-ayur-green-500', 'focus:ring-ayur-green-200');

            // Remove error message
            const errorDiv = field.parentNode.querySelector('.validation-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }

        function addPaymentModeError(message) {
            // Remove any existing Laravel error message first
            removePaymentModeError();

            // Find the payment mode container - look for the div containing payment mode options
            const paymentModeContainer = document.querySelector('.payment-mode-option').closest('.mt-1');
            if (paymentModeContainer) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mt-1 text-sm text-red-600 payment-mode-error';
                errorDiv.textContent = message;
                paymentModeContainer.appendChild(errorDiv);
            }
        }

        function addPaymentModeSuccess() {
            // Remove any existing error messages
            removePaymentModeError();
        }

        function removePaymentModeError() {
            // Remove JavaScript created error
            const errorDiv = document.querySelector('.payment-mode-error');
            if (errorDiv) {
                errorDiv.remove();
            }

            // Also remove any Laravel error message that might be present
            const laravelError = document.querySelector('.payment-mode-option').closest('.mt-1').querySelector(
                '.text-red-600');
            if (laravelError && laravelError.textContent.includes('payment_mode')) {
                laravelError.remove();
            }
        }

        function clearValidationStates() {
            // Clear all field validation states
            const fields = ['transactionDate', 'categoryId', 'amount', 'referenceNumber', 'patientVendor', 'notes',
                'attachment'
            ];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    removeFieldError(field);
                }
            });

            // Clear payment mode validation
            removePaymentModeError();
        }

        function showValidationMessage(message, type) {
            // Create or update validation message
            let messageDiv = document.getElementById('validationMessage');
            if (!messageDiv) {
                messageDiv = document.createElement('div');
                messageDiv.id = 'validationMessage';
                messageDiv.className = 'mb-4 p-4 rounded-md';
                const form = document.getElementById('transactionForm');
                form.insertBefore(messageDiv, form.firstChild);
            }

            messageDiv.className =
                `mb-4 p-4 rounded-md ${type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'}`;
            messageDiv.textContent = message;

            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (messageDiv) {
                    messageDiv.remove();
                }
            }, 5000);
        }

        function checkForLaravelErrors() {
            // Check if there are any Laravel validation errors present
            const laravelErrors = document.querySelectorAll('.text-red-600');
            if (laravelErrors.length > 0) {
                // If Laravel errors exist, disable JavaScript validation temporarily
                // and let Laravel errors show
                console.log('Laravel validation errors detected');
            }
        }

        function handleFileUpload(event) {
            const file = event.target.files[0];
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const uploadContainer = document.querySelector('.file-upload-container');

            if (file) {
                // Show file info
                fileName.textContent = file.name;
                fileInfo.classList.remove('hidden');
                uploadContainer.classList.add('border-green-500', 'bg-green-50');
                uploadContainer.classList.remove('border-gray-300');

                // Validate the file
                validateField('attachment');
            } else {
                removeFile();
            }
        }

        function removeFile() {
            const attachmentInput = document.getElementById('attachment');
            const fileInfo = document.getElementById('fileInfo');
            const uploadContainer = document.querySelector('.file-upload-container');

            // Clear the file input
            attachmentInput.value = '';

            // Hide file info
            fileInfo.classList.add('hidden');

            // Reset upload container styling
            uploadContainer.classList.remove('border-green-500', 'bg-green-50');
            uploadContainer.classList.add('border-gray-300');

            // Validate the field
            validateField('attachment');
        }
    </script>
@endsection
