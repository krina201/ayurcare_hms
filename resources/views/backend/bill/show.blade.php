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
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Bill Details - {{ $bill->invoice_number }}</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('bill.index') }}"
                        class="bg-gray-500 text-white hover:bg-gray-600 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Bills
                    </a>
                    <a href="{{ route('bill.edit', $bill) }}"
                        class="bg-ayur-yellow-600 text-white hover:bg-ayur-yellow-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                        <i class="fa-solid fa-edit mr-2"></i> Edit
                    </a>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-ayur-offwhite rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Patient Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-ayur-brown-600">Patient Name</p>
                        <p class="font-medium text-ayur-brown-800">{{ $bill->patient->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-ayur-brown-600">UHID</p>
                        <p class="font-medium text-ayur-brown-800">{{ $bill->patient->uhid }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-ayur-brown-600">Mobile</p>
                        <p class="font-medium text-ayur-brown-800">{{ $bill->patient->mobile }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-ayur-brown-600">Email</p>
                        <p class="font-medium text-ayur-brown-800">{{ $bill->patient->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Bill Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Bill Information</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Invoice Number:</span>
                            <span class="font-medium text-ayur-brown-800">{{ $bill->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Invoice Date:</span>
                            <span
                                class="font-medium text-ayur-brown-800">{{ $bill->invoice_date->format('d M, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Due Date:</span>
                            <span class="font-medium {{ $bill->is_overdue ? 'text-red-600' : 'text-ayur-brown-800' }}">
                                {{ $bill->due_date->format('d M, Y') }}
                                @if ($bill->is_overdue)
                                    (Overdue)
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Status:</span>
                            <span class="px-2 py-1 {{ $bill->status_class }} text-xs font-medium rounded-full">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Payment Information</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Payment Method:</span>
                            <span class="font-medium text-ayur-brown-800">{{ $bill->payment_method_text }}</span>
                        </div>
                        @if ($bill->payment_reference)
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Reference:</span>
                                <span class="font-medium text-ayur-brown-800">{{ $bill->payment_reference }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Total Amount:</span>
                            <span class="font-medium text-ayur-brown-800">{{ $bill->formatted_total_amount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Paid Amount:</span>
                            <span class="font-medium text-ayur-green-600">{{ $bill->formatted_paid_amount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ayur-brown-600">Outstanding:</span>
                            <span class="font-medium text-ayur-yellow-600">{{ $bill->formatted_outstanding_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Items -->
            @if ($bill->bill_items && count($bill->bill_items) > 0)
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Bill Items</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-ayur-brown-700">Description</th>
                                    <th class="text-center py-3 px-4 font-medium text-ayur-brown-700">Quantity</th>
                                    <th class="text-right py-3 px-4 font-medium text-ayur-brown-700">Amount</th>
                                    <th class="text-right py-3 px-4 font-medium text-ayur-brown-700">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bill->bill_items as $item)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3 px-4 text-ayur-brown-800">
                                            {{ $item['description'] ?? 'Service/Item' }}</td>
                                        <td class="py-3 px-4 text-center text-ayur-brown-800">{{ $item['quantity'] ?? 1 }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-ayur-brown-800">
                                            ₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                        <td class="py-3 px-4 text-right text-ayur-brown-800">
                                            ₹{{ number_format(($item['quantity'] ?? 1) * ($item['amount'] ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-200">
                                    <td colspan="3" class="py-3 px-4 text-right font-medium text-ayur-brown-700">
                                        Subtotal:</td>
                                    <td class="py-3 px-4 text-right font-medium text-ayur-brown-800">
                                        ₹{{ number_format($bill->subtotal, 2) }}</td>
                                </tr>
                                @if ($bill->tax_amount > 0)
                                    <tr>
                                        <td colspan="3" class="py-3 px-4 text-right font-medium text-ayur-brown-700">Tax:
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-ayur-brown-800">
                                            ₹{{ number_format($bill->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if ($bill->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="py-3 px-4 text-right font-medium text-ayur-brown-700">
                                            Discount:</td>
                                        <td class="py-3 px-4 text-right font-medium text-red-600">
                                            -₹{{ number_format($bill->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="border-t-2 border-gray-300">
                                    <td colspan="3" class="py-3 px-4 text-right font-bold text-ayur-brown-800">Total
                                        Amount:</td>
                                    <td class="py-3 px-4 text-right font-bold text-ayur-brown-800 text-lg">
                                        {{ $bill->formatted_total_amount }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if ($bill->notes)
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Notes</h4>
                    <p class="text-ayur-brown-700">{{ $bill->notes }}</p>
                </div>
            @endif

            <!-- Related Information -->
            @if ($bill->appointment || $bill->prescription || $bill->treatmentPlan)
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 mb-4">Related Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if ($bill->appointment)
                            <div>
                                <p class="text-sm text-ayur-brown-600">Appointment</p>
                                <p class="font-medium text-ayur-brown-800">
                                    {{ $bill->appointment->appointment_date->format('d M, Y') }}
                                    @if ($bill->appointment->doctor)
                                        - {{ $bill->appointment->doctor->full_name }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if ($bill->prescription)
                            <div>
                                <p class="text-sm text-ayur-brown-600">Prescription</p>
                                <p class="font-medium text-ayur-brown-800">
                                    {{ $bill->prescription->created_at->format('d M, Y') }}
                                    @if ($bill->prescription->doctor)
                                        - {{ $bill->prescription->doctor->full_name }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if ($bill->treatmentPlan)
                            <div>
                                <p class="text-sm text-ayur-brown-600">Treatment Plan</p>
                                <p class="font-medium text-ayur-brown-800">
                                    {{ $bill->treatmentPlan->created_at->format('d M, Y') }}
                                    @if ($bill->treatmentPlan->doctor)
                                        - {{ $bill->treatmentPlan->doctor->full_name }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
