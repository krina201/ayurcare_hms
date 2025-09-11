@extends('backend.layouts.master')

@section('content')
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

        <!-- Patient Info Section (if viewing specific patient) -->
        @if (isset($patient))
            <div id="patientInfo" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row">
                    <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                        <div class="relative">
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-ayur-green-200"
                                src="{{ $patient->photo ? asset('backend-assets/media/uploads/patients/' . $patient->photo) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg' }}"
                                alt="Patient avatar">
                            <span
                                class="absolute bottom-0 right-0 h-5 w-5 rounded-full bg-ayur-green-500 border-2 border-white flex items-center justify-center">
                                <i class="fa-solid fa-check text-white text-xs"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-3">
                            <div>
                                <h3 class="text-xl font-bold text-ayur-brown-800">{{ $patient->full_name }}</h3>
                                <div class="flex items-center mt-1 text-ayur-brown-600">
                                    <span
                                        class="bg-ayur-green-100 text-ayur-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">
                                        UHID: {{ $patient->uhid }}
                                    </span>
                                    <span class="text-sm">{{ $patient->age }} Years / {{ ucfirst($patient->gender) }}</span>
                                    @if ($patient->prakriti)
                                        <span class="mx-2">•</span>
                                        <span class="text-sm">{{ $patient->prakriti }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <div class="flex items-center">
                                <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                    <i class="fa-solid fa-phone text-ayur-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-ayur-brown-600">Mobile</p>
                                    <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->mobile }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                    <i class="fa-solid fa-envelope text-ayur-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-ayur-brown-600">Email</p>
                                    <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                    <i class="fa-solid fa-location-dot text-ayur-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-ayur-brown-600">Address</p>
                                    <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Billing Summary Panel -->
        <div id="billingSummaryPanel" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-ayur-brown-600 mb-1">Total Bills</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">{{ $stats['formatted_total_bills'] }}</p>
                        <p class="text-xs text-ayur-green-600 mt-1">
                            <i class="fa-solid fa-arrow-up mr-1"></i> {{ $stats['total_bill_count'] }} invoices
                        </p>
                    </div>
                    <div class="bg-ayur-green-100 w-12 h-12 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-ayur-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-ayur-brown-600 mb-1">Paid Amount</p>
                        <p class="text-2xl font-bold text-ayur-green-700">{{ $stats['formatted_paid_amount'] }}</p>
                        <p class="text-xs text-ayur-green-600 mt-1">
                            <i class="fa-solid fa-check-circle mr-1"></i> {{ $stats['paid_count'] }} paid
                        </p>
                    </div>
                    <div class="bg-ayur-green-100 w-12 h-12 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-credit-card text-ayur-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-ayur-brown-600 mb-1">Outstanding</p>
                        <p class="text-2xl font-bold text-ayur-yellow-700">{{ $stats['formatted_outstanding_amount'] }}</p>
                        <p class="text-xs text-ayur-yellow-600 mt-1">
                            <i class="fa-solid fa-clock mr-1"></i> {{ $stats['pending_count'] + $stats['overdue_count'] }}
                            pending
                        </p>
                    </div>
                    <div class="bg-ayur-yellow-100 w-12 h-12 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-ayur-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-ayur-brown-600 mb-1">Last Payment</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">
                            @if ($stats['last_payment'])
                                {{ $stats['last_payment']->formatted_total_amount }}
                            @else
                                ₹0
                            @endif
                        </p>
                        <p class="text-xs text-ayur-brown-600 mt-1">
                            <i class="fa-solid fa-calendar mr-1"></i>
                            @if ($stats['last_payment'])
                                {{ $stats['last_payment']->updated_at->format('d M, Y') }}
                            @else
                                No payments
                            @endif
                        </p>
                    </div>
                    <div class="bg-ayur-brown-100 w-12 h-12 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-indian-rupee-sign text-ayur-brown-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bills Section -->
        <div id="billsSection" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h3 class="text-2xl font-semibold text-ayur-brown-800 mb-2">Billing History</h3>
                    <p class="text-sm text-ayur-brown-600">Complete record of all bills and payment transactions</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 mt-4 md:mt-0">
                    <a href="{{ route('bill.export') }}"
                        class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                        <i class="fa-solid fa-download mr-2"></i> Export Bills
                    </a>
                    <a href="{{ route('bill.create') }}"
                        class="bg-ayur-yellow-600 text-white hover:bg-ayur-yellow-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i> New Bill
                    </a>
                </div>
            </div>

            <!-- Patient Search -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="patientSearch" placeholder="Search patients by name or UHID..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-gray-400"></i>
                            </div>
                        </div>
                        <div id="patientSearchResults"
                            class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                    @if (isset($patient))
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Showing bills for:</span>
                            <span class="font-medium text-ayur-brown-800">{{ $patient->full_name }}
                                ({{ $patient->uhid }})</span>
                            <a href="{{ route('bill') }}" class="text-red-600 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-times mr-1"></i> Clear
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Search and Filter -->
            <form method="GET" class="flex flex-col md:flex-row gap-4 mb-6">
                @if (isset($patient))
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                @endif
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search bills, invoice numbers, or services..."
                            value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <select name="status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-ayur-green-500 focus:border-ayur-green-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-ayur-green-500 focus:border-ayur-green-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-ayur-green-500 focus:border-ayur-green-500">
                    <button type="submit"
                        class="bg-ayur-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-ayur-green-700">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Bills List -->
            <div class="space-y-4">
                @forelse($bills as $bill)
                    <div id="bill{{ $bill->id }}"
                        class="border rounded-lg p-5 hover:shadow-md transition-shadow 
                               {{ $bill->status == 1 ? 'border-yellow-200 bg-ayur-yellow-50' : 'border-gray-200' }}">
                        <div class="flex flex-col lg:flex-row justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="text-lg font-semibold text-ayur-brown-800 mr-3">{{ $bill->invoice_number }}
                                    </h4>
                                    <span class="px-3 py-1 {{ $bill->status_class }} text-xs font-medium rounded-full">
                                        @if ($bill->status == 2)
                                            Paid
                                        @elseif($bill->status == 1)
                                            Pending
                                        @elseif($bill->status == 3)
                                            Overdue
                                        @else
                                            {{ ucfirst($bill->status) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center mb-3">
                                    <span
                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full mr-2">
                                        @if ($bill->appointment_id)
                                            <i class="fa-solid fa-calendar-check mr-1"></i>Appointment
                                        @elseif($bill->dispense_id)
                                            <i class="fa-solid fa-pills mr-1"></i>Medication Dispense
                                        @elseif($bill->prescription_id)
                                            <i class="fa-solid fa-prescription mr-1"></i>Prescription
                                        @elseif($bill->treatment_plan_id)
                                            <i class="fa-solid fa-spa mr-1"></i>Treatment Plan
                                        @else
                                            <i class="fa-solid fa-file-invoice mr-1"></i>General Bill
                                        @endif
                                    </span>
                                    @if ($bill->patient)
                                        <span class="text-sm text-ayur-brown-600">
                                            <i class="fa-solid fa-user mr-1"></i>{{ $bill->patient->full_name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-ayur-brown-600 mb-1">Invoice Date</p>
                                        <p class="font-medium text-ayur-brown-800">
                                            {{ $bill->invoice_date->format('d M, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-ayur-brown-600 mb-1">Due Date</p>
                                        <p
                                            class="font-medium {{ $bill->is_overdue ? 'text-red-600' : 'text-ayur-brown-800' }}">
                                            {{ $bill->due_date->format('d M, Y') }}
                                            @if ($bill->is_overdue)
                                                (Overdue)
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-ayur-brown-600 mb-1">Amount</p>
                                        <p class="font-medium text-ayur-brown-800">{{ $bill->formatted_total_amount }}</p>
                                    </div>
                                    <div>
                                        <p class="text-ayur-brown-600 mb-1">Payment Method</p>
                                        <p class="font-medium text-ayur-brown-800">
                                            @if ($bill->status == 2)
                                                {{ $bill->paymentMode->name ?? 'Paid' }}
                                            @else
                                                Pending
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($bill->appointment || $bill->dispense || $bill->prescription || $bill->treatmentPlan)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                            @if ($bill->appointment)
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-calendar text-blue-600 mr-2"></i>
                                                    <span class="text-gray-600">Doctor:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->appointment->doctor->full_name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-clock text-blue-600 mr-2"></i>
                                                    <span class="text-gray-600">Time:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->appointment->appointment_time ? \Carbon\Carbon::parse($bill->appointment->appointment_time)->format('g:i A') : 'N/A' }}</span>
                                                </div>
                                            @endif

                                            @if ($bill->dispense)
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-pills text-green-600 mr-2"></i>
                                                    <span class="text-gray-600">Receipt:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->dispense->receipt_number ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-user-md text-green-600 mr-2"></i>
                                                    <span class="text-gray-600">Dispensed by:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->dispense->dispensedBy->name ?? 'N/A' }}</span>
                                                </div>
                                            @endif

                                            @if ($bill->prescription)
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-prescription text-purple-600 mr-2"></i>
                                                    <span class="text-gray-600">Prescribed by:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->prescription->doctor->full_name ?? 'N/A' }}</span>
                                                </div>
                                            @endif

                                            @if ($bill->treatmentPlan)
                                                <div class="flex items-center">
                                                    <i class="fa-solid fa-spa text-orange-600 mr-2"></i>
                                                    <span class="text-gray-600">Treatment:</span>
                                                    <span
                                                        class="font-medium ml-1">{{ $bill->treatmentPlan->treatment_category ?? 'N/A' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($bill->bill_items && count($bill->bill_items) > 0)
                            <div class="border-t {{ $bill->status == 1 ? 'border-yellow-200' : 'border-gray-100' }} pt-4">
                                <h5 class="font-medium text-ayur-brown-800 mb-3">
                                    @if ($bill->appointment_id)
                                        <i class="fa-solid fa-calendar-check mr-2"></i>Appointment Details
                                    @elseif($bill->dispense_id)
                                        <i class="fa-solid fa-pills mr-2"></i>Medication Details
                                    @elseif($bill->prescription_id)
                                        <i class="fa-solid fa-prescription mr-2"></i>Prescription Details
                                    @elseif($bill->treatment_plan_id)
                                        <i class="fa-solid fa-spa mr-2"></i>Treatment Details
                                    @else
                                        <i class="fa-solid fa-list mr-2"></i>Bill Items
                                    @endif
                                </h5>
                                <div class="space-y-2">
                                    @foreach ($bill->bill_items as $item)
                                        <div class="flex justify-between items-center text-sm">
                                            <div class="flex-1">
                                                <span
                                                    class="text-ayur-brown-700">{{ $item['description'] ?? 'Service/Item' }}</span>
                                                @if (isset($item['quantity']) && $item['quantity'] > 1)
                                                    <span class="text-xs text-ayur-brown-500 ml-2">(Qty:
                                                        {{ $item['quantity'] }})</span>
                                                @endif
                                            </div>
                                            <span
                                                class="font-medium text-ayur-brown-800">₹{{ number_format($item['amount'] ?? ($item['total'] ?? 0), 2) }}</span>
                                        </div>
                                    @endforeach
                                    @if ($bill->discount_amount > 0)
                                        <div
                                            class="flex justify-between items-center text-sm {{ $bill->status == 1 ? 'border-t border-yellow-200 pt-2' : 'border-t border-gray-100 pt-2' }}">
                                            <span class="text-ayur-brown-700">Discount</span>
                                            <span
                                                class="font-medium text-red-600">-₹{{ number_format($bill->discount_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    @if ($bill->tax_amount > 0)
                                        <div
                                            class="flex justify-between items-center text-sm {{ $bill->status == 1 ? 'border-t border-yellow-200 pt-2' : 'border-t border-gray-100 pt-2' }}">
                                            <span class="text-ayur-brown-700">Tax (GST
                                                {{ $bill->tax_amount > 0 ? '18%' : '12%' }})</span>
                                            <span
                                                class="font-medium text-ayur-brown-800">₹{{ number_format($bill->tax_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div
                                        class="flex justify-between items-center text-sm font-semibold {{ $bill->status == 1 ? 'border-t border-yellow-300 pt-2' : 'border-t border-gray-200 pt-2' }}">
                                        <span class="text-ayur-brown-800">Total Amount</span>
                                        <span class="text-ayur-brown-800">{{ $bill->formatted_total_amount }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div
                            class="flex {{ $bill->status == 2 ? 'justify-end' : 'justify-between items-center' }} mt-4 pt-4 {{ $bill->status == 1 ? 'border-t border-yellow-200' : 'border-t border-gray-100' }}">
                            <div class="flex space-x-3">
                                <a href="{{ route('bill.show', $bill) }}"
                                    class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                                    <i class="fa-solid fa-eye mr-1"></i> View
                                </a>
                                <button onclick="printBill({{ $bill->id }})"
                                    class="text-ayur-brown-600 hover:text-ayur-brown-700 text-sm font-medium">
                                    <i class="fa-solid fa-print mr-1"></i> Print
                                </button>
                                <button onclick="downloadPDF({{ $bill->id }})"
                                    class="text-ayur-yellow-600 hover:text-ayur-yellow-700 text-sm font-medium">
                                    <i class="fa-solid fa-download mr-1"></i> PDF
                                </button>
                            </div>
                            @if ($bill->status != 2)
                                <div class="flex space-x-2">
                                    <button onclick="processPayment({{ $bill->id }})"
                                        class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                                        <i class="fa-solid fa-credit-card mr-1"></i> Pay Online
                                    </button>
                                    <button onclick="processCashPayment({{ $bill->id }})"
                                        class="bg-ayur-brown-600 text-white hover:bg-ayur-brown-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                                        <i class="fa-solid fa-money-bill mr-1"></i> Cash Payment
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fa-solid fa-file-invoice text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No bills found</p>
                        <a href="{{ route('bill.create') }}"
                            class="text-ayur-green-600 hover:text-ayur-green-700 font-medium">
                            Create your first bill
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($bills->hasPages())
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-ayur-brown-600">
                        Showing <span class="font-medium">{{ $bills->firstItem() }}</span> to <span
                            class="font-medium">{{ $bills->lastItem() }}</span> of <span
                            class="font-medium">{{ $bills->total() }}</span> bills
                    </div>
                    <div class="flex space-x-2">
                        {{ $bills->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Payment Options Section -->
        <div id="paymentOptionsSection" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-ayur-brown-800 mb-4">Payment Options</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div
                            class="bg-ayur-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-mobile-screen-button text-ayur-green-600 text-2xl"></i>
                        </div>
                        <h4 class="font-medium text-ayur-brown-800 mb-2">UPI Payment</h4>
                        <p class="text-sm text-ayur-brown-600 mb-3">Pay instantly using UPI apps like PhonePe, Google Pay,
                            Paytm</p>
                        <button
                            class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2 w-full">
                            Pay via UPI
                        </button>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div
                            class="bg-ayur-brown-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-credit-card text-ayur-brown-600 text-2xl"></i>
                        </div>
                        <h4 class="font-medium text-ayur-brown-800 mb-2">Card Payment</h4>
                        <p class="text-sm text-ayur-brown-600 mb-3">Pay securely using your debit or credit card</p>
                        <button
                            class="bg-ayur-brown-600 text-white hover:bg-ayur-brown-700 font-medium rounded-lg text-sm px-4 py-2 w-full">
                            Pay by Card
                        </button>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div
                            class="bg-ayur-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-money-bill text-ayur-yellow-600 text-2xl"></i>
                        </div>
                        <h4 class="font-medium text-ayur-brown-800 mb-2">Cash Payment</h4>
                        <p class="text-sm text-ayur-brown-600 mb-3">Pay in cash at the reception desk</p>
                        <button
                            class="bg-ayur-yellow-600 text-white hover:bg-ayur-yellow-700 font-medium rounded-lg text-sm px-4 py-2 w-full">
                            Record Cash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Process Payment</h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <form id="paymentForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Amount</label>
                            <input type="number" id="paymentAmount" step="0.01" min="0"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select id="paymentMethod"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                required>
                                <option value="">Select Payment Method</option>
                                @foreach ($paymentModes as $paymentMode)
                                    <option value="{{ $paymentMode->id }}">{{ $paymentMode->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                            <input type="text" id="paymentReference"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closePaymentModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-ayur-green-600 text-white rounded-lg hover:bg-ayur-green-700">
                            Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentBillId = null;
        let patientSearchTimeout = null;

        // Patient search functionality
        document.getElementById('patientSearch').addEventListener('input', function(e) {
            const query = e.target.value.trim();

            clearTimeout(patientSearchTimeout);

            if (query.length < 2) {
                document.getElementById('patientSearchResults').classList.add('hidden');
                return;
            }

            patientSearchTimeout = setTimeout(() => {
                searchPatients(query);
            }, 300);
        });

        function searchPatients(query) {
            console.log('Searching patients with query:', query);

            // Show loading state
            const resultsContainer = document.getElementById('patientSearchResults');
            resultsContainer.innerHTML = `
                <div class="p-3 text-gray-500 text-center">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Searching patients...
                </div>
            `;
            resultsContainer.classList.remove('hidden');

            const url = "{{ route('bill.search-patient') }}?query=" + encodeURIComponent(query);
            console.log('Fetching from URL:', url);

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search response:', data);

                    // Check if the response contains an error
                    if (data.error) {
                        throw new Error(data.message || data.error);
                    }
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Error searching patients:', error);
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack
                    });

                    resultsContainer.innerHTML = `
                        <div class="p-3 text-red-500 text-center">
                            <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                            ${error.message || 'Error searching patients. Please try again.'}
                            <br>
                            <small class="text-gray-500">Check console for more details</small>
                        </div>
                    `;
                    resultsContainer.classList.remove('hidden');
                });
        }

        // Display search results with enhanced formatting
        function displaySearchResults(patients) {
            const resultsContainer = document.getElementById('patientSearchResults');

            if (!Array.isArray(patients) || patients.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="p-3 text-gray-500 text-center">
                        <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                        No patients found
                    </div>
                `;
                resultsContainer.classList.remove('hidden');
                return;
            }

            const resultsHtml = patients.map(patient => {
                // Escape special characters to prevent XSS
                const safeName = patient.full_name ? patient.full_name.replace(/[<>]/g, '') : 'N/A';
                const safeUhid = patient.uhid ? patient.uhid.replace(/[<>]/g, '') : 'N/A';
                const safeGender = patient.gender ? patient.gender.replace(/[<>]/g, '') : 'N/A';
                const safeMobile = patient.mobile ? patient.mobile.replace(/[<>]/g, '') : 'N/A';
                const safePrakriti = patient.prakriti ? patient.prakriti.replace(/[<>]/g, '') : 'N/A';
                const safeAllergies = patient.allergies ? patient.allergies.replace(/[<>]/g, '') : 'None';
                const safePhotoPath = patient.photo_path ? patient.photo_path.replace(/[<>]/g, '') : '';

                const age = patient.age || 'N/A';
                const genderInitial = safeGender.charAt(0).toUpperCase();

                return `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                         onclick="selectPatient(${patient.id}, '${safeUhid}', '${safeName}', '${safeGender}', '${age}', '${safeMobile}', '${safePrakriti}', '${safeAllergies}', '${safePhotoPath}')">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 mr-3">
                                <img class="h-8 w-8 rounded-full object-cover" 
                                     src="${safePhotoPath ? '{{ asset('') }}' + safePhotoPath : '{{ asset('backend-assets/media/uploads/download (3).png') }}'}" 
                                     alt="Patient avatar"
                                     onerror="this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">${safeName}</div>
                                <div class="text-xs text-gray-500">${safeUhid} • ${age}/${genderInitial}</div>
                                <div class="text-xs text-gray-400">${safeMobile}</div>
                                ${safePrakriti !== 'N/A' ? `<div class="text-xs text-ayur-brown-600">Prakriti: ${safePrakriti}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            resultsContainer.innerHTML = resultsHtml;
            resultsContainer.classList.remove('hidden');
        }

        function selectPatient(patientId, uhid, name, gender, age, mobile, prakriti, allergies, photoPath) {
            try {
                // Validate required parameters
                if (!patientId || !name) {
                    console.error('Invalid patient data:', {
                        id: patientId,
                        name: name
                    });
                    return;
                }

                // Hide search results
                const searchResults = document.getElementById('patientSearchResults');
                if (searchResults) {
                    searchResults.classList.add('hidden');
                }

                // Update search input with patient name
                const patientSearchInput = document.getElementById('patientSearch');
                if (patientSearchInput) {
                    patientSearchInput.value = name;
                }

                // Redirect to bills page with patient filter
                const redirectUrl = "{{ route('bill.index') }}?patient_id=" + patientId;
                window.location.href = redirectUrl;

            } catch (error) {
                console.error('Error selecting patient:', error);
                // Show error message to user
                const searchResults = document.getElementById('patientSearchResults');
                if (searchResults) {
                    searchResults.innerHTML = `
                        <div class="p-3 text-red-500 text-center">
                            <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                            Error selecting patient. Please try again.
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                }
            }
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById('patientSearch').closest('.relative');
            if (!searchContainer.contains(e.target)) {
                document.getElementById('patientSearchResults').classList.add('hidden');
            }
        });


        function showErrorMessage(message) {
            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className =
                'fixed top-4 right-4 bg-red-50 text-red-700 px-6 py-4 rounded-lg border border-red-200 shadow-lg z-50 flex items-center max-w-md';
            errorDiv.innerHTML = `
                <i class="fa-solid fa-exclamation-triangle mr-3 text-red-600"></i>
                <div class="flex-1">
                    <div class="font-medium">Payment Error</div>
                    <div class="text-sm mt-1">${message}</div>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-4 text-red-600 hover:text-red-800">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;

            // Add to page
            document.body.appendChild(errorDiv);

            // Auto remove after 8 seconds
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.remove();
                }
            }, 8000);
        }

        function processPayment(billId) {
            currentBillId = billId;
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function processCashPayment(billId) {
            currentBillId = billId;
            // Set payment method to cash (assuming cash has ID 1, adjust as needed)
            document.getElementById('paymentMethod').value = 1;
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            currentBillId = null;
            document.getElementById('paymentForm').reset();
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('payment_amount', document.getElementById('paymentAmount').value);
            formData.append('payment_method', document.getElementById('paymentMethod').value);
            formData.append('payment_reference', document.getElementById('paymentReference').value);

            console.log('Payment form data:', {
                bill_id: currentBillId,
                payment_amount: document.getElementById('paymentAmount').value,
                payment_method: document.getElementById('paymentMethod').value,
                payment_reference: document.getElementById('paymentReference').value
            });

            const paymentUrl = "{{ route('bill.payment', ':id') }}".replace(':id', currentBillId);
            fetch(paymentUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to show success message via session
                        const redirectUrl = "{{ route('bill.index') }}?success=" + encodeURIComponent(data
                            .message);
                        window.location.href = redirectUrl;
                    } else {
                        let errorMessage = data.message;
                        if (data.errors) {
                            errorMessage += '\n\nValidation Errors:\n';
                            Object.keys(data.errors).forEach(field => {
                                errorMessage += field + ': ' + data.errors[field].join(', ') + '\n';
                            });
                        }
                        showErrorMessage(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('An error occurred while processing payment. Please try again.');
                });
        });

        function printBill(billId) {
            const url = "{{ route('bill.show', ':id') }}".replace(':id', billId) + '?print=1';
            window.open(url, '_blank');
        }

        function downloadPDF(billId) {
            const url = "{{ route('bill.show', ':id') }}".replace(':id', billId) + '?pdf=1';
            window.open(url, '_blank');
        }
    </script>
@endsection
