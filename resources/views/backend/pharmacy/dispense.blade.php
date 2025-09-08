@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">
        <!-- TABS -->
        <div id="pharmacyTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('pharmacy') }}"
                        class="btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Medicine Inventory
                    </a>
                    <a class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Dispense Medication
                    </a>

                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Restock Purchase
                    </button>
                </nav>
            </div>
        </div>

        <!-- DISPENSE MEDICATION FORM -->
        <form id="dispenseMedicationForm" method="POST" action="{{ route('pharmacy.store-dispense-form') }}"
            class="bg-white rounded-lg shadow-md p-6 mb-6">
            @csrf

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Dispense Medication</h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                        <i class="fa-solid fa-prescription-bottle-medical mr-1"></i> OPD
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-yellow-100 text-ayur-yellow-800">
                        <i class="fa-solid fa-bed-pulse mr-1"></i> IPD
                    </span>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- COLUMN 1: Patient & Prescription Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Patient Search <span
                                class="text-red-500">*</span></label>
                        <div class="mt-1 relative">
                            <div class="flex">
                                <input type="text" id="patientSearchInput"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="UHID / Name / Mobile">
                                <button type="button" id="patientSearchBtn"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <button type="button" id="refreshPatientBtn" onclick="refreshPatientData()"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-sync-alt"></i>
                                </button>
                            </div>
                            <!-- Search Results Dropdown -->
                            <div id="searchResults"
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                            </div>
                            <input type="hidden" name="patient_id" id="selectedPatientId">
                        </div>
                    </div>

                    <!-- Patient Details Card -->
                    <div id="patientDetailsCard" class="bg-ayur-offwhite rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-ayur-brown-800">Patient Details</h4>
                            <span class="text-xs text-gray-500">Select a patient to view details</span>
                        </div>
                        <div id="patientCards" class="space-y-3 patientCard">
                            <div class="flex items-start">

                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-ayur-brown-800">Select a patient</h4>
                                    <p class="text-xs text-ayur-brown-600">Search by UHID, name, or mobile</p>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Information Details -->
                        <div id="patientInfoDetails" class="mt-4 space-y-2 hidden">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-ayur-brown-600">UHID:</span>
                                    <span id="patientUhid" class="text-ayur-brown-800 ml-1 font-medium"></span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Age/Gender:</span>
                                    <span id="patientAgeGender" class="text-ayur-brown-800 ml-1 font-medium"></span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Mobile:</span>
                                    <span id="patientMobile" class="text-ayur-brown-800 ml-1 font-medium"></span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Prakriti:</span>
                                    <span id="patientPrakriti" class="text-ayur-brown-800 ml-1 font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Prescription <span
                                class="text-red-500">*</span></label>
                        <select name="prescription_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                            <option value="">Select Prescription</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Dosha Type</label>
                        <div class="mt-1 flex space-x-2">
                            <span class="text-xs text-gray-500">Select a patient to view dosha type</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Allergies</label>
                        <div class="mt-1">
                            <span class="text-xs text-gray-500">Select a patient to view allergies</span>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: Prescribed Medications -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Prescribed Medications</label>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-ayur-green-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-ayur-brown-800">Prescribed by: Dr. Sharma on 16/07/2025
                                </h4>
                            </div>
                            <div class="p-4 space-y-3">
                                <p class="text-sm text-gray-500 text-center py-4">Select a prescription to view medications
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Add Additional Medicine</label>
                        <div class="mt-1 relative">
                            <div class="flex">
                                <input type="text" id="medicineSearchInput"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="Search medicine by name or code">
                                <button type="button" id="medicineSearchBtn"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </div>
                            <!-- Medicine Search Results Dropdown -->
                            <div id="medicineSearchResults"
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Additional Medicines</label>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-ayur-blue-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-ayur-brown-800">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Additional Medicines Added
                                </h4>
                            </div>
                            <div id="additionalMedicinesContainer" class="p-4 space-y-3">
                                <div class="text-sm text-gray-500 text-center py-4">
                                    No additional medicines added
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Special Instructions</label>
                        <textarea name="special_instructions"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                            rows="2" placeholder="Any special instructions for the patient"></textarea>
                    </div>
                </div>

                <!-- COLUMN 3: Billing & Submission -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Bill Summary</label>
                        <div class="bg-ayur-offwhite rounded-lg p-4 border border-gray-200">
                            <div class="space-y-3 billsummary" id="billSummaryContainer">
                                <h4 class="text-lg font-semibold text-ayur-brown-800 border-b border-gray-200 pb-2">Bill
                                    Summary</h4>
                                <div class="text-sm text-gray-500 text-center py-8" id="billSummaryContent">
                                    <i class="fa-solid fa-receipt text-2xl text-gray-400 mb-2"></i>
                                    <p>Select medications to view bill summary</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Payment Method</label>
                        <div class="mt-1 grid grid-cols-3 gap-2">
                            <label
                                class="flex items-center justify-center p-2 border border-gray-300 rounded-md cursor-pointer bg-white">
                                <input type="radio" name="payment" value="cash"
                                    class="form-radio text-ayur-green-600 mr-1" checked="">
                                <span class="text-sm text-ayur-brown-700">Cash</span>
                            </label>
                            <label
                                class="flex items-center justify-center p-2 border border-gray-300 rounded-md cursor-pointer bg-white">
                                <input type="radio" name="payment" value="card"
                                    class="form-radio text-ayur-green-600 mr-1">
                                <span class="text-sm text-ayur-brown-700">Card</span>
                            </label>
                            <label
                                class="flex items-center justify-center p-2 border border-gray-300 rounded-md cursor-pointer bg-white">
                                <input type="radio" name="payment" value="upi"
                                    class="form-radio text-ayur-green-600 mr-1">
                                <span class="text-sm text-ayur-brown-700">UPI</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Dispense Status</label>
                        <div class="mt-1">
                            <select name="dispense_status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Status</option>
                                <option value="ready">Ready to Dispense</option>
                                <option value="partial">Partially Available</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700">Dispensed By</label>
                        <div class="mt-1">
                            <select name="dispensed_by"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Staff</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}
                                        ({{ $user->role->name ?? 'Staff' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-2">
                        <!-- Hidden fields for form data -->
                        <input type="hidden" name="patient_id" id="selectedPatientId">
                        <input type="hidden" name="prescription_id" id="selectedPrescriptionId">
                        <input type="hidden" name="medicines" id="selectedMedicines">
                        <input type="hidden" name="subtotal" id="formSubtotal" value="0">
                        <input type="hidden" name="gst_amount" id="formGstAmount" value="0">
                        <input type="hidden" name="total_amount" id="formTotalAmount" value="0">

                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-prescription-bottle-medical mr-2"></i> Dispense Medication
                        </button>

                        <button type="button"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-print mr-2"></i> Print Receipt
                        </button>
                        <button type="button"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-envelope mr-2"></i> Email Receipt
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- INVENTORY STATUS -->
        <div id="medicineInventory" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Medication Inventory Status</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="example">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Code</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Medicine Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Batch</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Mfg Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Exp Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Stock Qty</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Unit Price</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($medicines as $medicine)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">{{ $medicine->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-8 w-8 bg-ayur-green-100 rounded-full flex items-center justify-center">
                                            <i class="fa-solid fa-pills text-ayur-green-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">{{ $medicine->name }}
                                            </div>
                                            <div class="text-xs text-ayur-brown-500">
                                                {{ $medicine->manufacturer->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $medicine->medicineType->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $medicine->batch_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $medicine->manufacturing_date ? $medicine->manufacturing_date->format('M Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $medicine->expiry_date ? $medicine->expiry_date->format('M Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $medicine->initial_stock_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    ₹{{ number_format($medicine->selling_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = 'In Stock';
                                        $statusClass = 'bg-ayur-green-100 text-ayur-green-800';

                                        // Check if out of stock first
                                        if ($medicine->initial_stock_quantity <= 0) {
                                            $status = 'Out of Stock';
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                        }
                                        // Check if expiring soon (within 30 days)
                                        elseif (
                                            $medicine->expiry_date &&
                                            $medicine->expiry_date->isFuture() &&
                                            now()->diffInDays($medicine->expiry_date) <= 30 &&
                                            $medicine->track_expiry
                                        ) {
                                            $status = 'Expiring Soon';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        }
                                        // Check if low stock
                                        elseif (
                                            $medicine->minimum_stock_level &&
                                            $medicine->initial_stock_quantity <= $medicine->minimum_stock_level
                                        ) {
                                            $status = 'Low Stock';
                                            $statusClass = 'bg-ayur-yellow-100 text-ayur-yellow-800';
                                        }
                                        // Check if expired
                                        elseif (
                                            $medicine->expiry_date &&
                                            $medicine->expiry_date->isPast() &&
                                            $medicine->track_expiry
                                        ) {
                                            $status = 'Expired';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        }
                                    @endphp

                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                    No medicines found. <a href="{{ route('pharmacy.create') }}"
                                        class="text-ayur-green-600 hover:text-ayur-green-900">Add your first medicine</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        <!-- RECENT DISPENSATIONS -->
        <div id="recentDispensations" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Recent Dispensations</h3>
                <a href="{{ route('pharmacy.dispensations') }}"
                    class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium transition-colors duration-200">
                    View All <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="example2">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Receipt No.</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Prescribed By</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Dispensed On</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Items</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Amount</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentDispensations as $dispense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    {{ $dispense->receipt_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ $dispense->patient && $dispense->patient->photo_path ? asset($dispense->patient->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                                                onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                                alt="Patient avatar">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $dispense->patient ? $dispense->patient->full_name : 'N/A' }}
                                            </div>
                                            <div class="text-xs text-ayur-brown-600">
                                                {{ $dispense->patient ? $dispense->patient->uhid : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $dispense->prescription && $dispense->prescription->doctor ? $dispense->prescription->doctor->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $dispense->dispense_date ? $dispense->dispense_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $dispense->items ? $dispense->items->count() : 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-ayur-brown-800">
                                    ₹{{ number_format($dispense->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        title="Print Receipt">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-prescription-bottle-medical text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-sm">No dispensations found</p>
                                        <p class="text-xs text-gray-400">Dispensations will appear here once medicines are
                                            dispensed</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Pass route URLs to JavaScript with error handling
        window.pharmacyRoutes = {
            searchPatient: '{{ route('pharmacy.search-patient') }}',
            searchMedicines: '{{ route('pharmacy.search-medicines') }}',
            getPrescriptions: '{{ route('pharmacy.get-prescriptions') }}',
            inventory: '{{ route('pharmacy.inventory') }}',
            processDispense: '{{ route('pharmacy.process-dispense') }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Debug route information
        console.log('Pharmacy routes loaded:', window.pharmacyRoutes);

        // Form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('dispenseMedicationForm');
            const submitBtn = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const patientId = document.getElementById('selectedPatientId').value;
                const prescriptionSelect = document.querySelector('select[name="prescription_id"]');
                const prescriptionId = prescriptionSelect ? prescriptionSelect.value : '';
                const dispensedBy = document.querySelector('select[name="dispensed_by"]').value;
                const dispenseStatus = document.querySelector('select[name="dispense_status"]').value;
                const payment = document.querySelector('input[name="payment"]:checked').value;
                const specialInstructions = document.querySelector('textarea[name="special_instructions"]')
                    .value;

                // Ensure patient_id and prescription_id are properly set in hidden fields
                const patientIdField = document.getElementById('selectedPatientId');
                const prescriptionIdField = document.getElementById('selectedPrescriptionId');

                if (patientIdField) {
                    patientIdField.value = patientId;
                }

                if (prescriptionIdField) {
                    prescriptionIdField.value = prescriptionId;
                }

                // Debug: Log the values being set
                console.log('Setting form values:', {
                    patient_id: patientId,
                    prescription_id: prescriptionId,
                    patientIdField: patientIdField?.value,
                    prescriptionIdField: prescriptionIdField?.value
                });

                // Validate required fields (patient_id is now optional)
                // Patient ID validation removed - it's now optional

                if (!prescriptionId || prescriptionId.trim() === '') {
                    Swal.fire('Error', 'Please select a prescription', 'error');
                    return false;
                }

                if (!dispensedBy) {
                    Swal.fire('Error', 'Please select who is dispensing', 'error');
                    return false;
                }

                if (!dispenseStatus) {
                    Swal.fire('Error', 'Please select dispense status', 'error');
                    return false;
                }

                // Check if any medicines are selected
                const hasPrescriptionMedicines = window.selectedMedicines && window.selectedMedicines
                    .length > 0 && window.selectedMedicines.some(m => m.selected !== false);
                const hasAdditionalMedicines = window.additionalMedicines && window.additionalMedicines
                    .length > 0;

                if (!hasPrescriptionMedicines && !hasAdditionalMedicines) {
                    Swal.fire('Error',
                        'No medications selected. Please select at least one medicine from prescription or add additional medicines.',
                        'error');
                    return false;
                }

                // Set default values for amounts (these would be calculated from selected medicines)
                document.getElementById('formSubtotal').value = '1000.00';
                document.getElementById('formGstAmount').value = '120.00';
                document.getElementById('formTotalAmount').value = '1120.00';

                // Ensure medicines data is properly formatted
                const medicinesData = [];

                // Add prescription medicines
                if (window.selectedMedicines && window.selectedMedicines.length > 0) {
                    window.selectedMedicines.forEach(medicine => {
                        if (medicine.selected !== false) {
                            medicinesData.push({
                                medicine_id: medicine.id,
                                quantity: medicine.quantity || 1,
                                unit_price: medicine.unit_price || 100,
                                total_price: (medicine.quantity || 1) * (medicine
                                    .unit_price || 100),
                                is_additional: false
                            });
                        }
                    });
                }

                // Add additional medicines
                if (window.additionalMedicines && window.additionalMedicines.length > 0) {
                    window.additionalMedicines.forEach(medicine => {
                        medicinesData.push({
                            medicine_id: medicine.id,
                            quantity: medicine.quantity || 1,
                            unit_price: medicine.price || 100,
                            total_price: medicine.totalPrice || 100,
                            is_additional: true
                        });
                    });
                }

                // Set medicines data in hidden field
                const medicinesField = document.getElementById('selectedMedicines');
                if (medicinesField) {
                    medicinesField.value = JSON.stringify(medicinesData);
                }

                console.log('Form data prepared for submission:', {
                    patient_id: patientId,
                    prescription_id: prescriptionId,
                    medicines: medicinesData,
                    subtotal: document.getElementById('formSubtotal').value,
                    gst_amount: document.getElementById('formGstAmount').value,
                    total_amount: document.getElementById('formTotalAmount').value
                });

                // Show confirmation dialog
                Swal.fire({
                    title: 'Confirm Dispense',
                    text: 'Are you sure you want to dispense this medication?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Dispense!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Debug: Log form data before submission
                        console.log('Form data before submission:', {
                            patient_id: document.getElementById('selectedPatientId').value,
                            prescription_id: document.getElementById(
                                'selectedPrescriptionId').value,
                            medicines: document.getElementById('selectedMedicines').value,
                            subtotal: document.getElementById('formSubtotal').value,
                            gst_amount: document.getElementById('formGstAmount').value,
                            total_amount: document.getElementById('formTotalAmount').value
                        });

                        // Disable submit button to prevent double submission
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Dispensing...';

                        // Submit the form
                        form.submit();
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('backend-assets/js/validation/dispense/dispense.js') }}"></script>
@endsection
