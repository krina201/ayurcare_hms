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

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Create New Bill</h3>
                <a href="{{ route('bill.index') }}"
                    class="bg-gray-500 text-white hover:bg-gray-600 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Bills
                </a>
            </div>

            <form id="bill-form" method="POST" action="{{ route('bill.store') }}" class="space-y-6">
                @csrf

                <!-- Patient Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-ayur-brown-700 mb-2">Select
                            Patient</label>
                        <select name="patient_id" id="patient_id" required
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                            <option value="">Select a patient...</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}"
                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }} ({{ $patient->uhid }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="appointment_id" class="block text-sm font-medium text-ayur-brown-700 mb-2">Related
                            Appointment (Optional)</label>
                        <select name="appointment_id" id="appointment_id"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                            <option value="">Select appointment...</option>
                            @foreach ($appointments as $appointment)
                                <option value="{{ $appointment->id }}"
                                    {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                    {{ $appointment->appointment_date->format('d M Y') }} -
                                    {{ $appointment->doctor->full_name ?? 'Doctor' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Prescription Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="prescription_id" class="block text-sm font-medium text-ayur-brown-700 mb-2">Related
                            Prescription (Optional)</label>
                        <select name="prescription_id" id="prescription_id"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                            <option value="">Select prescription...</option>
                            @foreach ($prescriptions as $prescription)
                                <option value="{{ $prescription->id }}"
                                    {{ old('prescription_id') == $prescription->id ? 'selected' : '' }}>
                                    {{ $prescription->prescription_date->format('d M Y') }} -
                                    {{ $prescription->doctor->full_name ?? 'Doctor' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Quick Actions</label>
                        <div class="flex gap-2">
                            <button type="button" id="loadAppointmentData"
                                class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2">
                                <i class="fa-solid fa-calendar-check mr-1"></i> Load Appointment Data
                            </button>
                            <button type="button" id="loadPrescriptionData"
                                class="bg-ayur-yellow-600 text-white hover:bg-ayur-yellow-700 font-medium rounded-lg text-sm px-4 py-2">
                                <i class="fa-solid fa-prescription mr-1"></i> Load Prescription Data
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bill Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-ayur-brown-700 mb-2">Invoice
                            Date</label>
                        <input type="date" name="invoice_date" id="invoice_date"
                            value="{{ old('invoice_date', date('Y-m-d')) }}" required
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                        @error('invoice_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-ayur-brown-700 mb-2">Due Date</label>
                        <input type="date" name="due_date" id="due_date"
                            value="{{ old('due_date', date('Y-m-d', strtotime('+15 days'))) }}" required
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-ayur-brown-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                            placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Bill Items -->
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Bill Items</label>
                    <div id="bill-items-container" class="space-y-4">
                        <div class="bill-item border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <input type="text" name="bill_items[0][description]"
                                        class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                        placeholder="Service or item description" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                    <input type="number" name="bill_items[0][quantity]" value="1" min="1"
                                        class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                                    <input type="number" name="bill_items[0][amount]" step="0.01" min="0"
                                        class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                        required>
                                </div>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="button" onclick="removeBillItem(this)"
                                    class="text-red-600 hover:text-red-700 text-sm">
                                    <i class="fa-solid fa-trash mr-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addBillItem()"
                        class="mt-2 bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fa-solid fa-plus mr-2"></i> Add Item
                    </button>
                </div>

                <!-- Tax and Discount -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-ayur-brown-700 mb-2">Tax Amount
                            (₹)</label>
                        <input type="number" name="tax_amount" id="tax_amount" step="0.01" min="0"
                            value="{{ old('tax_amount', 0) }}"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                    </div>

                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-ayur-brown-700 mb-2">Discount
                            Amount (₹)</label>
                        <input type="number" name="discount_amount" id="discount_amount" step="0.01" min="0"
                            value="{{ old('discount_amount', 0) }}"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                    </div>
                </div>

                <!-- Total Amount Display -->
                <div class="bg-ayur-offwhite rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-ayur-brown-800">Total Amount:</span>
                        <span id="total-amount-display" class="text-2xl font-bold text-ayur-green-600">₹0.00</span>
                    </div>
                    <input type="hidden" name="subtotal" id="subtotal" value="0">
                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('bill.index') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-ayur-green-600 text-white rounded-lg hover:bg-ayur-green-700">
                        Create Bill
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        let itemIndex = 1;

        function addBillItem() {
            const container = document.getElementById('bill-items-container');
            const newItem = document.createElement('div');
            newItem.className = 'bill-item border border-gray-200 rounded-lg p-4';
            newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="bill_items[${itemIndex}][description]" 
                       class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                       placeholder="Service or item description" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input type="number" name="bill_items[${itemIndex}][quantity]" value="1" min="1"
                       class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                <input type="number" name="bill_items[${itemIndex}][amount]" step="0.01" min="0"
                       class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500" required>
            </div>
        </div>
        <div class="flex justify-end mt-2">
            <button type="button" onclick="removeBillItem(this)" class="text-red-600 hover:text-red-700 text-sm">
                <i class="fa-solid fa-trash mr-1"></i> Remove
            </button>
        </div>
    `;
            container.appendChild(newItem);
            itemIndex++;

            // Add event listeners to new inputs
            const amountInput = newItem.querySelector('input[name*="[amount]"]');
            amountInput.addEventListener('input', calculateTotal);
        }

        function removeBillItem(button) {
            const container = document.getElementById('bill-items-container');
            if (container.children.length > 1) {
                button.closest('.bill-item').remove();
                calculateTotal();
            }
        }

        function calculateTotal() {
            let subtotal = 0;
            const billItems = document.querySelectorAll('.bill-item');

            billItems.forEach(item => {
                const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]').value) || 0;
                const amount = parseFloat(item.querySelector('input[name*="[amount]"]').value) || 0;
                subtotal += quantity * amount;
            });

            const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const totalAmount = subtotal + taxAmount - discountAmount;

            document.getElementById('subtotal').value = subtotal;
            document.getElementById('total_amount').value = totalAmount;
            document.getElementById('total-amount-display').textContent = '₹' + totalAmount.toFixed(2);
        }

        // Load appointment data
        function loadAppointmentData() {
            const appointmentId = document.getElementById('appointment_id').value;
            if (!appointmentId) {
                alert('Please select an appointment first');
                return;
            }

            fetch(`/admin/bill/appointment-data?appointment_id=${appointmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear existing bill items
                        const container = document.getElementById('bill-items-container');
                        container.innerHTML = '';
                        itemIndex = 0;

                        // Add appointment items
                        data.bill_items.forEach(item => {
                            addBillItemFromData(item);
                        });

                        // Update totals
                        document.getElementById('subtotal').value = data.subtotal;
                        document.getElementById('total_amount').value = data.total_amount;
                        calculateTotal();

                        alert('Appointment data loaded successfully!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading appointment data');
                });
        }

        // Load prescription data
        function loadPrescriptionData() {
            const prescriptionId = document.getElementById('prescription_id').value;
            if (!prescriptionId) {
                alert('Please select a prescription first');
                return;
            }

            fetch(`/admin/bill/prescription-data?prescription_id=${prescriptionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear existing bill items
                        const container = document.getElementById('bill-items-container');
                        container.innerHTML = '';
                        itemIndex = 0;

                        // Add prescription items
                        data.bill_items.forEach(item => {
                            addBillItemFromData(item);
                        });

                        // Update totals
                        document.getElementById('subtotal').value = data.subtotal;
                        document.getElementById('total_amount').value = data.total_amount;
                        calculateTotal();

                        alert('Prescription data loaded successfully!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading prescription data');
                });
        }

        // Add bill item from data
        function addBillItemFromData(itemData) {
            const container = document.getElementById('bill-items-container');
            const newItem = document.createElement('div');
            newItem.className = 'bill-item border border-gray-200 rounded-lg p-4';
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="bill_items[${itemIndex}][description]" 
                               value="${itemData.description || ''}"
                               class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                               placeholder="Service or item description" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="bill_items[${itemIndex}][quantity]" value="${itemData.quantity || 1}" min="1"
                               class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
                        <input type="number" name="bill_items[${itemIndex}][amount]" step="0.01" min="0" value="${itemData.amount || 0}"
                               class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ayur-green-500" required>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" onclick="removeBillItem(this)" class="text-red-600 hover:text-red-700 text-sm">
                        <i class="fa-solid fa-trash mr-1"></i> Remove
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            itemIndex++;

            // Add event listeners to new inputs
            const amountInput = newItem.querySelector('input[name*="[amount]"]');
            const quantityInput = newItem.querySelector('input[name*="[quantity]"]');
            amountInput.addEventListener('input', calculateTotal);
            quantityInput.addEventListener('input', calculateTotal);
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to existing inputs
            document.querySelectorAll(
                'input[name*="[amount]"], input[name*="[quantity]"], #tax_amount, #discount_amount').forEach(
                input => {
                    input.addEventListener('input', calculateTotal);
                });

            // Add event listeners to quick action buttons
            document.getElementById('loadAppointmentData').addEventListener('click', loadAppointmentData);
            document.getElementById('loadPrescriptionData').addEventListener('click', loadPrescriptionData);

            // Calculate initial total
            calculateTotal();
        });
    </script>
@endsection
