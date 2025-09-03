@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="max-w-4xl mx-auto">
            <!-- PAGE HEADER -->
            <div id="pageHeader" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-ayur-brown-800 mb-2">Add New Medicine</h1>
                        <p class="text-ayur-brown-600">Add a new Ayurvedic medicine to your inventory with detailed
                            information for optimal patient care.</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('pharmacy') }}"
                            class=" btn inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Back to Inventory
                        </a>

                    </div>
                </div>
            </div>

            <!-- ADD MEDICINE FORM -->
            <div id="addMedicineForm" class="bg-white rounded-xl shadow-lg p-6">
                <form id="medicine-form" method="POST" action="{{ route('pharmacy.store') }}" novalidate>
                    @csrf
                    <input type="hidden" name="save_type" id="save_type" value="1">

                    <!-- BASIC INFORMATION -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-ayur-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-info text-ayur-green-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Basic Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Medicine Name
                                    *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Enter medicine name" required="">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="name-error" style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Medicine Code
                                    *</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('code') border-red-500 @enderror"
                                    placeholder="AYM-001" required="">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="code-error" style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Medicine Type
                                    *</label>
                                <select name="medicine_type_id" id="medicine_type_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('medicine_type_id') border-red-500 @enderror"
                                    required="">
                                    <option value="">Select medicine type</option>
                                    @foreach ($medicineTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('medicine_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('medicine_type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="medicine_type_id-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Category *</label>
                                <select name="medicine_category_id" id="medicine_category_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('medicine_category_id') border-red-500 @enderror"
                                    required="">
                                    <option value="">Select category</option>
                                    @foreach ($medicineCategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('medicine_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('medicine_category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="medicine_category_id-error"
                                    style="display:none"></p>
                            </div>
                        </div>
                    </div>

                    <!-- MANUFACTURER & SUPPLIER INFO -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-ayur-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-building text-ayur-yellow-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Manufacturer &amp; Supplier</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Manufacturer
                                    *</label>
                                <select name="manufacturer_id" id="manufacturer_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('manufacturer_id') border-red-500 @enderror"
                                    required="">
                                    <option value="">Select manufacturer</option>
                                    @foreach ($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}"
                                            {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                                            {{ $manufacturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manufacturer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="manufacturer_id-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Supplier *</label>
                                <input type="text" name="supplier_name" id="supplier_name"
                                    value="{{ old('supplier_name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('supplier_name') border-red-500 @enderror"
                                    placeholder="Enter supplier name" required="">
                                @error('supplier_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="supplier_name-error"
                                    style="display:none">
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Supplier
                                    Contact *</label>
                                <input type="tel" name="supplier_contact" id="supplier_contact"
                                    value="{{ old('supplier_contact') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('supplier_contact') border-red-500 @enderror"
                                    placeholder="+91 98765 43210" required="">
                                @error('supplier_contact')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="supplier_contact-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Supplier
                                    Email *</label>
                                <input type="email" name="supplier_email" id="supplier_email"
                                    value="{{ old('supplier_email') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('supplier_email') border-red-500 @enderror"
                                    placeholder="supplier@example.com" required="">
                                @error('supplier_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="supplier_email-error"
                                    style="display:none"></p>
                            </div>
                        </div>
                    </div>

                    <!-- DOSAGE & COMPOSITION -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-ayur-brown-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-prescription-bottle-medical text-ayur-brown-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Dosage &amp; Composition</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Strength/Dosage *</label>
                                <input type="text" name="strength_dosage" id="strength_dosage"
                                    value="{{ old('strength_dosage') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('strength_dosage') border-red-500 @enderror"
                                    placeholder="250mg, 500mg, 10ml, etc." required="">
                                @error('strength_dosage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="strength_dosage-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Unit of
                                    Measurement *</label>
                                <select name="measurement_id" id="measurement_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('measurement_id') border-red-500 @enderror"
                                    required="">
                                    <option value="">Select unit</option>
                                    @foreach ($measurements as $measurement)
                                        <option value="{{ $measurement->id }}"
                                            {{ old('measurement_id') == $measurement->id ? 'selected' : '' }}>
                                            {{ $measurement->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('measurement_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="measurement_id-error"
                                    style="display:none"></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Main
                                    Ingredients *</label>
                                <textarea rows="3" name="main_ingredients" id="main_ingredients"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('main_ingredients') border-red-500 @enderror"
                                    placeholder="List main Ayurvedic herbs and ingredients" required="">{{ old('main_ingredients') }}</textarea>
                                @error('main_ingredients')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="main_ingredients-error"
                                    style="display:none"></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Indications &amp;
                                    Usage *</label>
                                <textarea rows="3" name="indications_usage" id="indications_usage"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('indications_usage') border-red-500 @enderror"
                                    placeholder="Describe what conditions this medicine treats and how to use it" required="">{{ old('indications_usage') }}</textarea>
                                @error('indications_usage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="indications_usage-error"
                                    style="display:none"></p>
                            </div>
                        </div>
                    </div>

                    <!-- INVENTORY DETAILS -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Inventory Details</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Batch Number
                                    *</label>
                                <input type="text" name="batch_number" id="batch_number"
                                    value="{{ old('batch_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('batch_number') border-red-500 @enderror"
                                    placeholder="BATCH-2025-001" required="">
                                @error('batch_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="batch_number-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Manufacturing Date
                                    *</label>
                                <input type="date" name="manufacturing_date" id="manufacturing_date"
                                    value="{{ old('manufacturing_date') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('manufacturing_date') border-red-500 @enderror"
                                    required="">
                                @error('manufacturing_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="manufacturing_date-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Expiry Date
                                    *</label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                    value="{{ old('expiry_date') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('expiry_date') border-red-500 @enderror"
                                    required="">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="expiry_date-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Initial Stock
                                    Quantity *</label>
                                <input type="number" name="initial_stock_quantity" id="initial_stock_quantity"
                                    value="{{ old('initial_stock_quantity') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('initial_stock_quantity') border-red-500 @enderror"
                                    placeholder="100" required="">
                                @error('initial_stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="initial_stock_quantity-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Minimum Stock
                                    Level *</label>
                                <input type="number" name="minimum_stock_level" id="minimum_stock_level"
                                    value="{{ old('minimum_stock_level') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('minimum_stock_level') border-red-500 @enderror"
                                    placeholder="20" required="">
                                @error('minimum_stock_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="minimum_stock_level-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Storage
                                    Location *</label>
                                <input type="text" name="storage_location" id="storage_location"
                                    value="{{ old('storage_location') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('storage_location') border-red-500 @enderror"
                                    placeholder="Shelf A-12" required="">
                                @error('storage_location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="storage_location-error"
                                    style="display:none"></p>
                            </div>
                        </div>
                    </div>

                    <!-- PRICING INFORMATION -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-indian-rupee-sign text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Pricing Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Purchase Price
                                    *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                        <span class="text-gray-500">₹</span>
                                    </div>
                                    <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                                        value="{{ old('purchase_price') }}"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('purchase_price') border-red-500 @enderror"
                                        placeholder="0.00" required="">
                                </div>
                                @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="purchase_price-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Selling Price
                                    *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                        <span class="text-gray-500">₹</span>
                                    </div>
                                    <input type="number" step="0.01" name="selling_price" id="selling_price"
                                        value="{{ old('selling_price') }}"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('selling_price') border-red-500 @enderror"
                                        placeholder="0.00" required="">
                                </div>
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="selling_price-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">MRP *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                        <span class="text-gray-500">₹</span>
                                    </div>
                                    <input type="number" step="0.01" name="mrp" id="mrp"
                                        value="{{ old('mrp') }}"
                                        class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('mrp') border-red-500 @enderror"
                                        placeholder="0.00" required="">
                                </div>
                                @error('mrp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="mrp-error" style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Tax Rate
                                    (%)</label>
                                <select name="tax_rate" id="tax_rate"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('tax_rate') border-red-500 @enderror">
                                    <option value="0" {{ old('tax_rate') == '0' ? 'selected' : '' }}>0% (Exempt)
                                    </option>
                                    <option value="5" {{ old('tax_rate') == '5' ? 'selected' : '' }}>5% GST</option>
                                    <option value="12" {{ old('tax_rate') == '12' ? 'selected' : '' }}>12% GST
                                    </option>
                                    <option value="18" {{ old('tax_rate') == '18' ? 'selected' : '' }}>18% GST
                                    </option>
                                </select>
                                @error('tax_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="tax_rate-error" style="display:none">
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ADDITIONAL INFORMATION -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-clipboard-list text-purple-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Additional Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Storage
                                    Instructions *</label>
                                <textarea rows="3" name="storage_instructions" id="storage_instructions"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('storage_instructions') border-red-500 @enderror"
                                    placeholder="Store in a cool, dry place away from direct sunlight" required="">{{ old('storage_instructions') }}</textarea>
                                @error('storage_instructions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="storage_instructions-error"
                                    style="display:none"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Side Effects &amp;
                                    Precautions *</label>
                                <textarea rows="3" name="side_effects_precautions" id="side_effects_precautions"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('side_effects_precautions') border-red-500 @enderror"
                                    placeholder="List any known side effects and precautions" required="">{{ old('side_effects_precautions') }}</textarea>
                                @error('side_effects_precautions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="side_effects_precautions-error"
                                    style="display:none"></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Notes</label>
                                <textarea rows="2" name="notes" id="notes"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                    placeholder="Any additional notes about this medicine">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-red-600 js-error" id="notes-error" style="display:none"></p>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS TOGGLES -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-toggle-on text-indigo-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-ayur-brown-800">Status &amp; Preferences</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">Active Status</h4>
                                    <p class="text-xs text-ayur-brown-600">Medicine is available for dispensing</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="status" id="status" value="1"
                                        class="sr-only peer" {{ old('status', true) ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-ayur-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ayur-green-600">
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">Track Expiry</h4>
                                    <p class="text-xs text-ayur-brown-600">Send alerts before expiry date</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="track_expiry" id="track_expiry" value="1"
                                        class="sr-only peer" {{ old('track_expiry', true) ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-ayur-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ayur-green-600">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div id="actionButtons" class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" id="submitBtn"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Add Medicine to Inventory
                        </button>

                        <button type="button" id="draftBtn"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                            Save as Draft
                        </button>

                        <button type="button" id="cancelBtn"
                            class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-xmark mr-2"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Client-side validation for medicine form
            const form = document.getElementById('medicine-form');
            if (!form) return;

            const fields = {
                name: document.getElementById('name'),
                code: document.getElementById('code'),
                medicine_type_id: document.getElementById('medicine_type_id'),
                medicine_category_id: document.getElementById('medicine_category_id'),
                manufacturer_id: document.getElementById('manufacturer_id'),
                supplier_name: document.getElementById('supplier_name'),
                supplier_contact: document.getElementById('supplier_contact'),
                supplier_email: document.getElementById('supplier_email'),
                strength_dosage: document.getElementById('strength_dosage'),
                measurement_id: document.getElementById('measurement_id'),
                main_ingredients: document.getElementById('main_ingredients'),
                indications_usage: document.getElementById('indications_usage'),
                side_effects_precautions: document.getElementById('side_effects_precautions'),
                batch_number: document.getElementById('batch_number'),
                manufacturing_date: document.getElementById('manufacturing_date'),
                expiry_date: document.getElementById('expiry_date'),
                initial_stock_quantity: document.getElementById('initial_stock_quantity'),
                minimum_stock_level: document.getElementById('minimum_stock_level'),
                storage_location: document.getElementById('storage_location'),
                purchase_price: document.getElementById('purchase_price'),
                selling_price: document.getElementById('selling_price'),
                mrp: document.getElementById('mrp'),
                storage_instructions: document.getElementById('storage_instructions')
            };

            function showError(id, msg) {
                const el = document.getElementById(id + '-error');
                if (el) {
                    el.textContent = msg;
                    el.style.display = 'block';
                }
                const input = document.getElementById(id);
                if (input) input.classList.add('border-red-500');
            }

            function clearError(id) {
                const el = document.getElementById(id + '-error');
                if (el) {
                    el.textContent = '';
                    el.style.display = 'none';
                }
                const input = document.getElementById(id);
                if (input) input.classList.remove('border-red-500');
            }

            function validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function validatePhone(phone) {
                return /^[\+]?[0-9\s\-\(\)]{10,}$/.test(phone);
            }

            function validateCode(code) {
                return /^[A-Z0-9\-]+$/.test(code);
            }

            form.addEventListener('submit', function(e) {
                let valid = true;

                // Medicine name validation
                const name = fields.name && fields.name.value.trim();
                clearError('name');
                if (!name) {
                    showError('name', 'Medicine name is required.');
                    valid = false;
                } else if (name.length < 3) {
                    showError('name', 'Medicine name must be at least 3 characters.');
                    valid = false;
                } else if (name.length > 255) {
                    showError('name', 'Medicine name may not be greater than 255 characters.');
                    valid = false;
                }

                // Medicine code validation
                const code = fields.code && fields.code.value.trim();
                clearError('code');
                if (!code) {
                    showError('code', 'Medicine code is required.');
                    valid = false;
                } else if (!validateCode(code)) {
                    showError('code',
                        'Medicine code should contain only uppercase letters, numbers, and hyphens.');
                    valid = false;
                } else if (code.length > 50) {
                    showError('code', 'Medicine code may not be greater than 50 characters.');
                    valid = false;
                }

                // Medicine type validation
                const medicineType = fields.medicine_type_id && fields.medicine_type_id.value;
                clearError('medicine_type_id');
                if (!medicineType) {
                    showError('medicine_type_id', 'Please select a medicine type.');
                    valid = false;
                }

                // Medicine category validation
                const medicineCategory = fields.medicine_category_id && fields.medicine_category_id.value;
                clearError('medicine_category_id');
                if (!medicineCategory) {
                    showError('medicine_category_id', 'Please select a medicine category.');
                    valid = false;
                }

                // Manufacturer validation
                const manufacturer = fields.manufacturer_id && fields.manufacturer_id.value;
                clearError('manufacturer_id');
                if (!manufacturer) {
                    showError('manufacturer_id', 'Please select a manufacturer.');
                    valid = false;
                }

                // Supplier name validation
                const supplierName = fields.supplier_name && fields.supplier_name.value.trim();
                clearError('supplier_name');
                if (!supplierName) {
                    showError('supplier_name', 'Supplier name is required.');
                    valid = false;
                } else if (supplierName.length > 255) {
                    showError('supplier_name', 'Supplier name may not be greater than 255 characters.');
                    valid = false;
                }

                // Supplier contact validation
                const supplierContact = fields.supplier_contact && fields.supplier_contact.value.trim();
                clearError('supplier_contact');
                if (!supplierContact) {
                    showError('supplier_contact', 'Supplier contact is required.');
                    valid = false;
                } else if (!validatePhone(supplierContact)) {
                    showError('supplier_contact', 'Please provide a valid phone number.');
                    valid = false;
                }

                // Supplier email validation
                const supplierEmail = fields.supplier_email && fields.supplier_email.value.trim();
                clearError('supplier_email');
                if (!supplierEmail) {
                    showError('supplier_email', 'Supplier email is required.');
                    valid = false;
                } else if (!validateEmail(supplierEmail)) {
                    showError('supplier_email', 'Please provide a valid email address.');
                    valid = false;
                }

                // Strength/Dosage validation
                const strengthDosage = fields.strength_dosage && fields.strength_dosage.value.trim();
                clearError('strength_dosage');
                if (!strengthDosage) {
                    showError('strength_dosage', 'Strength/Dosage is required.');
                    valid = false;
                } else if (strengthDosage.length > 100) {
                    showError('strength_dosage', 'Strength/Dosage may not be greater than 100 characters.');
                    valid = false;
                }

                // Measurement validation
                const measurement = fields.measurement_id && fields.measurement_id.value;
                clearError('measurement_id');
                if (!measurement) {
                    showError('measurement_id', 'Please select a unit of measurement.');
                    valid = false;
                }

                // Main ingredients validation
                const mainIngredients = fields.main_ingredients && fields.main_ingredients.value.trim();
                clearError('main_ingredients');
                if (!mainIngredients) {
                    showError('main_ingredients', 'Main ingredients are required.');
                    valid = false;
                }

                // Indications & Usage validation
                const indicationsUsage = fields.indications_usage && fields.indications_usage.value.trim();
                clearError('indications_usage');
                if (!indicationsUsage) {
                    showError('indications_usage', 'Indications & Usage are required.');
                    valid = false;
                }

                // Side Effects & Precautions validation
                const sideEffectsPrecautions = fields.side_effects_precautions && fields
                    .side_effects_precautions.value.trim();
                clearError('side_effects_precautions');
                if (!sideEffectsPrecautions) {
                    showError('side_effects_precautions', 'Side Effects & Precautions are required.');
                    valid = false;
                }

                // Batch number validation
                const batchNumber = fields.batch_number && fields.batch_number.value.trim();
                clearError('batch_number');
                if (!batchNumber) {
                    showError('batch_number', 'Batch number is required.');
                    valid = false;
                } else if (batchNumber.length > 100) {
                    showError('batch_number', 'Batch number may not be greater than 100 characters.');
                    valid = false;
                }

                // Manufacturing date validation
                const manufacturingDate = fields.manufacturing_date && fields.manufacturing_date.value;
                clearError('manufacturing_date');
                if (!manufacturingDate) {
                    showError('manufacturing_date', 'Manufacturing date is required.');
                    valid = false;
                }

                // Expiry date validation
                const expiryDate = fields.expiry_date && fields.expiry_date.value;
                clearError('expiry_date');
                if (!expiryDate) {
                    showError('expiry_date', 'Expiry date is required.');
                    valid = false;
                } else if (manufacturingDate && expiryDate <= manufacturingDate) {
                    showError('expiry_date', 'Expiry date must be after manufacturing date.');
                    valid = false;
                }

                // Initial stock quantity validation
                const initialStock = fields.initial_stock_quantity && fields.initial_stock_quantity.value;
                clearError('initial_stock_quantity');
                if (!initialStock) {
                    showError('initial_stock_quantity', 'Initial stock quantity is required.');
                    valid = false;
                } else if (parseInt(initialStock) < 0) {
                    showError('initial_stock_quantity', 'Initial stock quantity must be non-negative.');
                    valid = false;
                }

                // Minimum stock level validation
                const minimumStock = fields.minimum_stock_level && fields.minimum_stock_level.value;
                clearError('minimum_stock_level');
                if (!minimumStock) {
                    showError('minimum_stock_level', 'Minimum stock level is required.');
                    valid = false;
                } else if (parseInt(minimumStock) < 0) {
                    showError('minimum_stock_level', 'Minimum stock level must be non-negative.');
                    valid = false;
                }

                // Storage location validation
                const storageLocation = fields.storage_location && fields.storage_location.value.trim();
                clearError('storage_location');
                if (!storageLocation) {
                    showError('storage_location', 'Storage location is required.');
                    valid = false;
                } else if (storageLocation.length > 255) {
                    showError('storage_location',
                        'Storage location may not be greater than 255 characters.');
                    valid = false;
                }

                // Purchase price validation
                const purchasePrice = fields.purchase_price && fields.purchase_price.value;
                clearError('purchase_price');
                if (!purchasePrice) {
                    showError('purchase_price', 'Purchase price is required.');
                    valid = false;
                } else if (parseFloat(purchasePrice) < 0) {
                    showError('purchase_price', 'Purchase price must be non-negative.');
                    valid = false;
                }

                // Selling price validation
                const sellingPrice = fields.selling_price && fields.selling_price.value;
                clearError('selling_price');
                if (!sellingPrice) {
                    showError('selling_price', 'Selling price is required.');
                    valid = false;
                } else if (parseFloat(sellingPrice) < 0) {
                    showError('selling_price', 'Selling price must be non-negative.');
                    valid = false;
                }

                // MRP validation
                const mrp = fields.mrp && fields.mrp.value;
                clearError('mrp');
                if (!mrp) {
                    showError('mrp', 'MRP is required.');
                    valid = false;
                } else if (parseFloat(mrp) < 0) {
                    showError('mrp', 'MRP must be non-negative.');
                    valid = false;
                }

                // Storage instructions validation
                const storageInstructions = fields.storage_instructions && fields.storage_instructions.value
                    .trim();
                clearError('storage_instructions');
                if (!storageInstructions) {
                    showError('storage_instructions', 'Storage instructions are required.');
                    valid = false;
                }



                if (!valid) {
                    e.preventDefault();
                    const firstErr = document.querySelector('.js-error[style*="display: block"]');
                    if (firstErr) firstErr.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });

            // Real-time validation clearing
            Object.keys(fields).forEach(fieldName => {
                const field = fields[fieldName];
                if (field) {
                    field.addEventListener('input', () => clearError(fieldName));
                    field.addEventListener('change', () => clearError(fieldName));
                }
            });

            // Auto-generate medicine code if empty
            const nameField = fields.name;
            const codeField = fields.code;

            if (nameField && codeField) {
                nameField.addEventListener('input', function() {
                    if (!codeField.value.trim()) {
                        const name = this.value.trim();
                        if (name.length >= 3) {
                            const code = 'AYM-' + name.substring(0, 3).toUpperCase() + '-' +
                                Math.random().toString(36).substring(2, 5).toUpperCase();
                            codeField.value = code;
                        }
                    }
                });
            }

            // Set minimum manufacturing date to today
            const manufacturingDateField = fields.manufacturing_date;
            if (manufacturingDateField) {
                const today = new Date().toISOString().split('T')[0];
                manufacturingDateField.setAttribute('max', today);
            }

            // Set minimum expiry date to manufacturing date
            const expiryDateField = fields.expiry_date;
            if (manufacturingDateField && expiryDateField) {
                manufacturingDateField.addEventListener('change', function() {
                    expiryDateField.setAttribute('min', this.value);
                });
            }

            // Handle Save as Draft button click
            const draftBtn = document.getElementById('draftBtn');
            if (draftBtn) {
                draftBtn.addEventListener('click', function() {
                    // Set save_type to 0 (draft)
                    document.getElementById('save_type').value = '0';

                    // Submit the form
                    form.submit();
                });
            }

            // Handle Submit button click
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    // Set save_type to 1 (submitted)
                    document.getElementById('save_type').value = '1';

                    // Let the form validation run normally
                });
            }

            // Handle Cancel button click
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
                        window.location.href = '{{ route('pharmacy') }}';
                    }
                });
            }
        });
    </script>
@endsection
