@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">
        <div class="mb-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">{{ session('success') }}
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
        <div id="pharmacyTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('pharmacy') }}"
                        class="btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Medicine Inventory
                    </a>
                    <a href="{{ route('pharmacy.dispense') }}"
                        class="btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Dispense Medication
                    </a>

                    <a href="{{ route('pharmacy.restock') }}"
                        class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Restock Purchase
                    </a>
                </nav>
            </div>
        </div>

        <!-- RESTOCK PURCHASE FORM -->
        <div id="restockPurchaseForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Medicine Restock Purchase Entry</h3>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                    <i class="fa-solid fa-cart-plus mr-1"></i> New Purchase
                </span>
            </div>

            <form action="{{ route('pharmacy.restock.store') }}" method="POST" enctype="multipart/form-data"
                id="restockForm">
                @csrf
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- COLUMN 1: Purchase Details -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Purchase ID <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 flex">
                                <input type="text" disabled=""
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border"
                                    value="PUR-2025-0087" placeholder="Auto generated">
                                <button type="button"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Purchase Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('purchase_date') border-red-500 @enderror">
                            @error('purchase_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Invoice Number <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('invoice_number') border-red-500 @enderror"
                                placeholder="Enter invoice number">
                            @error('invoice_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Invoice Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('invoice_date') border-red-500 @enderror">
                            @error('invoice_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Payment Method <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_mode_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('payment_mode_id') border-red-500 @enderror">
                                <option value="">Select Payment Method</option>
                                @foreach ($paymentModes as $paymentMode)
                                    <option value="{{ $paymentMode->id }}"
                                        {{ old('payment_mode_id') == $paymentMode->id ? 'selected' : '' }}>
                                        {{ $paymentMode->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_mode_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- COLUMN 2: Vendor Details -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Vendor <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 flex">
                                <select name="vendor_id" id="vendorSelect"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('vendor_id') border-red-500 @enderror">
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}"
                                            {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" id="addVendorBtn"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            @error('vendor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Vendor Contact <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" name="vendor_contact" id="vendorContact"
                                value="{{ old('vendor_contact') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('vendor_contact') border-red-500 @enderror"
                                placeholder="Enter 10 digit number" maxlength="10" pattern="[0-9]{10}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('vendor_contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">GSTIN</label>
                            <input type="text" name="gstin" id="vendorGstin" value="{{ old('gstin') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('gstin') border-red-500 @enderror"
                                placeholder="Vendor GSTIN">
                            @error('gstin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Notes</label>
                            <textarea name="notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border @error('notes') border-red-500 @enderror"
                                placeholder="Additional notes about this purchase">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- COLUMN 3: Purchase Summary -->
                    <div class="space-y-4">
                        <div class="bg-ayur-offwhite rounded-lg p-4 border border-gray-200">
                            <h4 class="font-medium text-ayur-brown-800 mb-3">Purchase Summary</h4>

                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-ayur-brown-700">Total Items:</span>
                                <span class="text-sm font-medium text-ayur-brown-800" id="totalItems">0</span>
                            </div>

                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-ayur-brown-700">Sub Total:</span>
                                <span class="text-sm font-medium text-ayur-brown-800" id="subtotalDisplay">₹0.00</span>
                                <input type="hidden" name="subtotal" id="subtotal" value="0">
                            </div>

                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-ayur-brown-700">Discount:</span>
                                <span class="text-sm font-medium text-ayur-brown-800" id="discountDisplay">₹0.00</span>
                                <input type="hidden" name="discount" id="discount" value="0">
                            </div>

                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-ayur-brown-700">GST (18%):</span>
                                <span class="text-sm font-medium text-ayur-brown-800" id="gstDisplay">₹0.00</span>
                                <input type="hidden" name="gst_amount" id="gst_amount" value="0">
                            </div>

                            <div class="border-t border-gray-200 my-2 pt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-medium text-ayur-brown-800">Grand Total:</span>
                                    <span class="text-base font-bold text-ayur-green-700"
                                        id="grandTotalDisplay">₹0.00</span>
                                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Upload Invoice</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-file-invoice mx-auto text-ayur-brown-400 text-2xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-ayur-green-500">
                                            <span>Upload invoice</span>
                                            <input id="invoice_file" name="invoice_file" type="file" class="sr-only"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                            @error('invoice_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- MEDICINE ITEMS TABLE -->
                <div id="medicineItemsSection" class="mt-8">
                    <h4 class="text-lg font-medium text-ayur-brown-800 mb-4">Medicine Items</h4>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-ayur-offwhite">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Medicine Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Batch No.</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Mfg. Date <span class="text-red-500">*</span></th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Exp. Date <span class="text-red-500">*</span></th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Quantity <span class="text-red-500">*</span></th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Unit</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Price/Unit</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Total</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="medicineItemsBody">
                                <tr class="medicine-item-row">
                                    <td class="px-6 py-4">
                                        <select name="items[0][medicine_id]"
                                            class="medicine-select block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.medicine_id') border-red-500 @enderror">
                                            <option value="">Select Medicine</option>
                                            @foreach ($medicines as $medicine)
                                                <option value="{{ $medicine->id }}"
                                                    data-purchase-price="{{ $medicine->purchase_price }}"
                                                    data-measurement="{{ $medicine->measurement->name ?? '' }}">
                                                    {{ $medicine->name }} ({{ $medicine->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('items.0.medicine_id')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="items[0][batch_number]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.batch_number') border-red-500 @enderror"
                                            placeholder="Batch No.">
                                        @error('items.0.batch_number')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="date" name="items[0][manufacturing_date]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.manufacturing_date') border-red-500 @enderror">
                                        @error('items.0.manufacturing_date')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="date" name="items[0][expiry_date]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.expiry_date') border-red-500 @enderror">
                                        @error('items.0.expiry_date')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="items[0][quantity]" min="1"
                                            class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.quantity') border-red-500 @enderror"
                                            placeholder="Qty">
                                        @error('items.0.quantity')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="items[0][measurement_id]"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm @error('items.0.measurement_id') border-red-500 @enderror">
                                            <option value="">Select Unit</option>
                                            @foreach ($measurements as $measurement)
                                                <option value="{{ $measurement->id }}">{{ $measurement->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('items.0.measurement_id')
                                            <div class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="items[0][unit_price]" step="0.01" min="0"
                                            class="unit-price-input block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm"
                                            placeholder="Price">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" step="0.01" disabled=""
                                            class="total-price-input block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-1 border text-sm"
                                            value="0.00">
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="remove-item-btn text-red-600 hover:text-red-900">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-center">
                        <button type="button" id="addItemBtn"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-plus mr-2"></i> Add More Items
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" id="cancelBtn"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-times mr-2"></i> Cancel
                    </button>
                    <button type="button" id="saveDraftBtn"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-save mr-2"></i> Save as Draft
                    </button>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-check mr-2"></i> Submit Purchase
                    </button>
                </div>
            </form>
        </div>

        <!-- RECENT PURCHASES -->
        <div id="recentPurchases" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Recent Purchases</h3>
                <a href="{{ route('pharmacy.restocks') }}"
                    class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                    View All <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Purchase ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Vendor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Invoice No.</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Items</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Total Amount</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentRestocks as $restock)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    {{ $restock->purchase_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $restock->formatted_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $restock->vendor_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $restock->invoice_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $restock->items->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $restock->formatted_total }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($restock->status == 1)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    @elseif($restock->status == 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('pharmacy.restock.view', $restock->id) }}"
                                        class="text-ayur-green-600 hover:text-ayur-green-900 mr-3">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No recent purchases
                                    found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 0;
            const medicines = @json($medicines);
            const measurements = @json($measurements);

            // Initialize form
            initializeForm();

            // Add item button
            document.getElementById('addItemBtn').addEventListener('click', addNewItem);

            // Save draft button
            document.getElementById('saveDraftBtn').addEventListener('click', function() {
                document.getElementById('restockForm').insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="save_type" value="0">');
                document.getElementById('restockForm').submit();
            });

            // Cancel button
            document.getElementById('cancelBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel? All data will be lost.')) {
                    window.location.href = '{{ route('pharmacy') }}';
                }
            });

            // Vendor selection change
            document.getElementById('vendorSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('vendorName').value = selectedOption.text;
                }
            });

            function initializeForm() {
                // Set today's date as default
                const today = new Date().toISOString().split('T')[0];
                document.querySelector('input[name="purchase_date"]').value = today;
                document.querySelector('input[name="invoice_date"]').value = today;

                // Add event listeners to first row
                addRowEventListeners(0);
            }

            function addNewItem() {
                itemIndex++;
                const tbody = document.getElementById('medicineItemsBody');
                const newRow = createItemRow(itemIndex);
                tbody.insertAdjacentHTML('beforeend', newRow);
                addRowEventListeners(itemIndex);
            }

            function createItemRow(index) {
                let medicineOptions = '<option value="">Select Medicine</option>';
                medicines.forEach(medicine => {
                    medicineOptions +=
                        `<option value="${medicine.id}" data-purchase-price="${medicine.purchase_price}" data-measurement="${medicine.measurement?.name || ''}">${medicine.name} (${medicine.code})</option>`;
                });

                let measurementOptions = '<option value="">Select Unit</option>';
                measurements.forEach(measurement => {
                    measurementOptions += `<option value="${measurement.id}">${measurement.name}</option>`;
                });

                return `
                    <tr class="medicine-item-row">
                        <td class="px-6 py-4">
                            <select name="items[${index}][medicine_id]" class="medicine-select block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm">
                                ${medicineOptions}
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" name="items[${index}][batch_number]" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm"
                                placeholder="Batch No.">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" name="items[${index}][manufacturing_date]" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" name="items[${index}][expiry_date]" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" name="items[${index}][quantity]" min="1" 
                                class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm"
                                placeholder="Qty">
                        </td>
                        <td class="px-6 py-4">
                            <select name="items[${index}][measurement_id]" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm">
                                ${measurementOptions}
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" name="items[${index}][unit_price]" step="0.01" min="0" 
                                class="unit-price-input block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 border text-sm"
                                placeholder="Price">
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" step="0.01" disabled="" 
                                class="total-price-input block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-1 border text-sm"
                                value="0.00">
                        </td>
                        <td class="px-6 py-4">
                            <button type="button" class="remove-item-btn text-red-600 hover:text-red-900">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            function addRowEventListeners(index) {
                const row = document.querySelectorAll('.medicine-item-row')[index];
                if (!row) return;

                // Medicine selection change
                const medicineSelect = row.querySelector('.medicine-select');
                medicineSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const purchasePrice = selectedOption.getAttribute('data-purchase-price');
                        const measurement = selectedOption.getAttribute('data-measurement');

                        // Set default unit price
                        const unitPriceInput = row.querySelector('.unit-price-input');
                        if (purchasePrice && !unitPriceInput.value) {
                            unitPriceInput.value = parseFloat(purchasePrice).toFixed(2);
                        }

                        // Set default measurement
                        const measurementSelect = row.querySelector('select[name*="measurement_id"]');
                        if (measurement) {
                            Array.from(measurementSelect.options).forEach(option => {
                                if (option.text === measurement) {
                                    option.selected = true;
                                }
                            });
                        }

                        calculateRowTotal(row);
                    }
                });

                // Quantity and unit price change
                const quantityInput = row.querySelector('.quantity-input');
                const unitPriceInput = row.querySelector('.unit-price-input');

                [quantityInput, unitPriceInput].forEach(input => {
                    input.addEventListener('input', function() {
                        calculateRowTotal(row);
                    });
                });

                // Remove item button
                const removeBtn = row.querySelector('.remove-item-btn');
                removeBtn.addEventListener('click', function() {
                    if (document.querySelectorAll('.medicine-item-row').length > 1) {
                        row.remove();
                        updateSummary();
                    } else {
                        alert('At least one item is required');
                    }
                });
            }

            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const total = quantity * unitPrice;

                row.querySelector('.total-price-input').value = total.toFixed(2);
                updateSummary();
            }

            function updateSummary() {
                let totalItems = 0;
                let subtotal = 0;

                document.querySelectorAll('.medicine-item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const totalPrice = parseFloat(row.querySelector('.total-price-input').value) || 0;

                    if (quantity > 0) {
                        totalItems++;
                        subtotal += totalPrice;
                    }
                });

                const gstRate = 0.18; // 18% GST
                const gstAmount = subtotal * gstRate;
                const grandTotal = subtotal + gstAmount;

                // Update display
                document.getElementById('totalItems').textContent = totalItems;
                document.getElementById('subtotalDisplay').textContent = '₹' + subtotal.toFixed(2);
                document.getElementById('gstDisplay').textContent = '₹' + gstAmount.toFixed(2);
                document.getElementById('grandTotalDisplay').textContent = '₹' + grandTotal.toFixed(2);

                // Update hidden inputs
                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('gst_amount').value = gstAmount.toFixed(2);
                document.getElementById('total_amount').value = grandTotal.toFixed(2);
            }

            // Form validation following role module pattern
            const form = document.getElementById('restockForm');
            if (!form) return;

            const fields = {
                purchase_date: document.querySelector('input[name="purchase_date"]'),
                invoice_number: document.querySelector('input[name="invoice_number"]'),
                invoice_date: document.querySelector('input[name="invoice_date"]'),
                payment_mode_id: document.querySelector('select[name="payment_mode_id"]'),
                vendor_id: document.querySelector('select[name="vendor_id"]'),
                vendor_contact: document.querySelector('input[name="vendor_contact"]')
            };

            function showError(fieldName, message) {
                const input = fields[fieldName];
                if (input) {
                    input.classList.add('border-red-500');

                    // For vendor_id, look in the parent div that contains the select and button
                    let container;
                    if (fieldName === 'vendor_id') {
                        container = input.parentNode.parentNode;
                    } else {
                        container = input.parentNode;
                    }

                    // Remove any existing error messages first
                    const existingErrors = container.querySelectorAll('.js-error');
                    existingErrors.forEach(error => error.remove());

                    // Create new error message
                    const errorEl = document.createElement('div');
                    errorEl.className = 'mt-1 text-sm text-red-600 js-error';
                    errorEl.textContent = message;
                    container.appendChild(errorEl);
                }
            }

            function clearError(fieldName) {
                const input = fields[fieldName];
                if (input) {
                    input.classList.remove('border-red-500');

                    // For vendor_id, look in the parent div that contains the select and button
                    let container;
                    if (fieldName === 'vendor_id') {
                        container = input.parentNode.parentNode;
                    } else {
                        container = input.parentNode;
                    }

                    // Remove all error messages
                    const existingErrors = container.querySelectorAll('.js-error');
                    existingErrors.forEach(error => error.remove());
                }
            }

            function validateItems() {
                const rows = document.querySelectorAll('.medicine-item-row');
                let hasValidItems = false;

                rows.forEach((row, index) => {
                    const medicineId = row.querySelector('.medicine-select').value;
                    const batchNumber = row.querySelector('input[name*="batch_number"]').value.trim();
                    const manufacturingDate = row.querySelector('input[name*="manufacturing_date"]').value;
                    const expiryDate = row.querySelector('input[name*="expiry_date"]').value;
                    const quantity = row.querySelector('.quantity-input').value;
                    const measurementId = row.querySelector('select[name*="measurement_id"]').value;
                    const unitPrice = row.querySelector('.unit-price-input').value;

                    // Clear previous errors
                    clearItemErrors(row);

                    if (!medicineId) {
                        showItemError(row, 'Medicine selection is required');
                        return;
                    }
                    if (!batchNumber) {
                        showItemError(row, 'Batch number is required');
                        return;
                    }
                    if (!manufacturingDate) {
                        showItemError(row, 'Manufacturing date is required');
                        return;
                    }
                    if (!expiryDate) {
                        showItemError(row, 'Expiry date is required');
                        return;
                    }
                    if (!quantity || quantity <= 0) {
                        showItemError(row, 'Quantity must be greater than 0');
                        return;
                    }
                    if (!measurementId) {
                        showItemError(row, 'Unit selection is required');
                        return;
                    }
                    if (!unitPrice || unitPrice <= 0) {
                        showItemError(row, 'Unit price must be greater than 0');
                        return;
                    }

                    hasValidItems = true;
                });

                return hasValidItems;
            }

            function showItemError(row, message) {
                const errorEl = row.querySelector('.js-item-error');
                if (!errorEl) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'js-item-error text-red-600 text-sm mt-1';
                    row.insertAdjacentElement('afterend', errorDiv);
                }
                const errorDiv = row.parentNode.querySelector('.js-item-error');
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                }
            }

            function clearItemErrors(row) {
                const errorDiv = row.parentNode.querySelector('.js-item-error');
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            }

            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate basic fields
                Object.keys(fields).forEach(fieldName => {
                    clearError(fieldName);
                    const field = fields[fieldName];
                    let isValid = true;
                    let message = '';

                    if (field) {
                        const value = field.value.trim();

                        // Check if field is empty
                        if (value === '') {
                            isValid = false;
                            // Custom messages for specific fields
                            switch (fieldName) {
                                case 'purchase_date':
                                    message = 'Purchase Date field is required.';
                                    break;
                                case 'invoice_number':
                                    message = 'Invoice Number field is required.';
                                    break;
                                case 'invoice_date':
                                    message = 'Invoice Date field is required.';
                                    break;
                                case 'vendor_id':
                                    message = 'Vendor selection is required.';
                                    break;
                                case 'vendor_contact':
                                    message = 'Vendor Contact field is required.';
                                    break;
                                case 'payment_mode_id':
                                    message = 'Payment Method selection is required.';
                                    break;
                                default:
                                    const displayName = fieldName.replace('_', ' ').replace(/\b\w/g,
                                        l => l.toUpperCase());
                                    message = `${displayName} is required`;
                            }
                        }

                        // Additional validation for vendor_contact (exactly 10 digits)
                        if (fieldName === 'vendor_contact' && value !== '') {
                            // Check if it's exactly 10 digits
                            const phoneRegex = /^[0-9]{10}$/;
                            if (!phoneRegex.test(value)) {
                                isValid = false;
                                message = 'Vendor Contact must be exactly 10 digits.';
                            }
                        }

                        if (!isValid) {
                            showError(fieldName, message);
                            valid = false;
                        }
                    }
                });

                // Validate items
                if (!validateItems()) {
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    return false;
                }

                // Add completed status for submit
                this.insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="save_type" value="1">');
            });

            // File upload handling
            const fileInput = document.getElementById('invoice_file');
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        alert('File size must be less than 5MB');
                        this.value = '';
                        return;
                    }
                }
            });
        });
    </script>
@endsection
