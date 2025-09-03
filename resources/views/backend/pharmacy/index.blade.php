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
                <nav class="flex flex-wrap -mb-px">
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Medicine Inventory
                    </button>
                    <a href="{{ route('pharmacy.dispense') }}"
                        class="btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Dispense Medication
                    </a>

                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Restock Purchase
                    </button>
                </nav>
            </div>
        </div>

        <!-- FILTERS AND ACTIONS -->
        <div id="medicineFilters" class="bg-white rounded-lg shadow-md p-5 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <h3 class="text-xl font-semibold text-ayur-brown-800 mb-3 md:mb-0">Medicine Inventory</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pharmacy.create') }}"
                        class="btn inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-plus mr-2"></i> Add New Medicine
                    </a>

                    <button
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-file-export mr-2"></i> Export
                    </button>
                    <button
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Medicine Type</label>
                    <select
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                        <option value="">All Types</option>
                        <option value="tablet">Tablet</option>
                        <option value="oil">Oil</option>
                        <option value="decoction">Decoction</option>
                        <option value="powder">Powder</option>
                        <option value="churna">Churna</option>
                        <option value="bhasma">Bhasma</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Stock Status</label>
                    <select
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                        <option value="">All Status</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Manufacturer</label>
                    <select
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                        <option value="">All Manufacturers</option>
                        <option value="patanjali">Patanjali</option>
                        <option value="dabur">Dabur</option>
                        <option value="himalaya">Himalaya</option>
                        <option value="baidyanath">Baidyanath</option>
                        <option value="in-house">In-House</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border pr-10"
                            placeholder="Search by name, code...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INVENTORY STATS -->
        <div id="inventoryStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Total Medicines</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">{{ $totalMedicines }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-green-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> {{ $submittedMedicines }} submitted, {{ $draftMedicines }} drafts
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Low Stock Items</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">{{ $lowStock }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-yellow-600 mt-2">
                    <i class="fa-solid fa-exclamation-triangle"></i> Requires attention
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Expiring Soon</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">{{ $expiringSoon }}</p>
                    </div>
                    <div class="rounded-full bg-red-100 p-2 text-red-600">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                </div>
                <p class="text-xs text-red-600 mt-2">
                    <i class="fa-solid fa-clock"></i> Within 30 days
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Total Value</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹{{ number_format($totalValue / 1000, 1) }}K</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fa-solid fa-chart-line"></i> Inventory value
                </p>
            </div>
        </div>

        <!-- MEDICINE INVENTORY TABLE -->
        <div id="medicineInventory" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-stone-800">Medicine Inventory</h3>
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
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
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
                                            $medicine->expiry_date->diffInDays(now()) <= 30 &&
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('pharmacy.show', $medicine->id) }}"
                                        class="text-ayur-green-600 hover:text-ayur-green-900 mr-3">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pharmacy.edit', $medicine->id) }}"
                                        class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- delete button --}}
                                    <button type="button" class="delete-medicine-btn text-red-600 hover:text-red-900"
                                        data-id="{{ $medicine->id }}" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <form id="delete-medicine-form-{{ $medicine->id }}"
                                        action="{{ route('pharmacy.delete', $medicine->id) }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        @method('delete')
                                    </form>

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

        <!-- INVENTORY ANALYTICS -->
        <div id="inventoryAnalytics" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- MEDICINE CATEGORY DISTRIBUTION -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Medicine Type Distribution</h3>
                <div id="medicineTypeChart" class="h-96 w-full">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-ayur-green-100 rounded-full mb-4">
                                <i class="fa-solid fa-chart-pie text-2xl text-ayur-green-600"></i>
                            </div>
                            <h4 class="text-lg font-medium text-ayur-brown-800 mb-2">Medicine Distribution</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-w-md mx-auto">
                                <div class="bg-ayur-green-50 rounded-lg p-3 border border-ayur-green-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-ayur-green-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-tablets text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Tablets</div>
                                    <div class="text-lg font-bold text-ayur-green-600">35.8%</div>
                                    <div class="text-xs text-ayur-brown-500">174 items</div>
                                </div>
                                <div class="bg-ayur-yellow-50 rounded-lg p-3 border border-ayur-yellow-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-ayur-yellow-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-bottle-droplet text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Oils</div>
                                    <div class="text-lg font-bold text-ayur-yellow-600">18.5%</div>
                                    <div class="text-xs text-ayur-brown-500">90 items</div>
                                </div>
                                <div class="bg-ayur-brown-50 rounded-lg p-3 border border-ayur-brown-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-ayur-brown-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-mortar-pestle text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Powders</div>
                                    <div class="text-lg font-bold text-ayur-brown-600">14.2%</div>
                                    <div class="text-xs text-ayur-brown-500">69 items</div>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-flask text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Liquids</div>
                                    <div class="text-lg font-bold text-blue-600">12.6%</div>
                                    <div class="text-xs text-ayur-brown-500">61 items</div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-purple-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-seedling text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Churnas</div>
                                    <div class="text-lg font-bold text-purple-600">10.4%</div>
                                    <div class="text-xs text-ayur-brown-500">51 items</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-gray-500 rounded-full mx-auto mb-2">
                                        <i class="fa-solid fa-capsules text-white text-sm"></i>
                                    </div>
                                    <div class="text-xs font-medium text-ayur-brown-700">Others</div>
                                    <div class="text-lg font-bold text-gray-600">8.5%</div>
                                    <div class="text-xs text-ayur-brown-500">41 items</div>
                                </div>
                            </div>
                            <div class="mt-4 text-xs text-ayur-brown-500">
                                Total Medicine Categories: 6 | Total Items: 486
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STOCK VALUE TRENDS -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Inventory Value Trend</h3>
                <div id="inventoryValueChart" class="h-80 w-full">
                    <div class="h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-ayur-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-ayur-brown-700">Inventory Value</span>
                                <div class="w-3 h-3 bg-ayur-yellow-500 rounded-full ml-4"></div>
                                <span class="text-sm font-medium text-ayur-brown-700">Restock Value</span>
                            </div>
                            <div class="text-sm text-ayur-brown-600">Last 12 months</div>
                        </div>

                        <div class="flex-1 relative">
                            <!-- Chart Grid Background -->
                            <div class="absolute inset-0 grid grid-rows-5 gap-0 opacity-20">
                                <div class="border-b border-gray-200"></div>
                                <div class="border-b border-gray-200"></div>
                                <div class="border-b border-gray-200"></div>
                                <div class="border-b border-gray-200"></div>
                                <div class="border-b border-gray-200"></div>
                            </div>

                            <!-- Y-Axis Labels -->
                            <div
                                class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-ayur-brown-500 -ml-12">
                                <span>₹3L</span>
                                <span>₹2.5L</span>
                                <span>₹2L</span>
                                <span>₹1.5L</span>
                                <span>₹1L</span>
                                <span>₹0.5L</span>
                            </div>

                            <!-- Chart Area -->
                            <div class="h-full flex items-end justify-between px-4">
                                <!-- Jan -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 72px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 14px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Jan</span>
                                </div>

                                <!-- Feb -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 78px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 17px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Feb</span>
                                </div>

                                <!-- Mar -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 84px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 12px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Mar</span>
                                </div>

                                <!-- Apr -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 82px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 10px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Apr</span>
                                </div>

                                <!-- May -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 88px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 16px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">May</span>
                                </div>

                                <!-- Jun -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 90px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 13px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Jun</span>
                                </div>

                                <!-- Jul -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 94px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 15px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Jul</span>
                                </div>

                                <!-- Aug -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 92px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 11px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Aug</span>
                                </div>

                                <!-- Sep -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 90px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 12px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Sep</span>
                                </div>

                                <!-- Oct -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 94px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 17px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Oct</span>
                                </div>

                                <!-- Nov -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 96px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 14px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Nov</span>
                                </div>

                                <!-- Dec -->
                                <div class="flex flex-col items-center space-y-1 flex-1">
                                    <div class="relative w-full max-w-8 flex flex-col items-center">
                                        <div class="w-3 bg-ayur-green-500 rounded-t-sm" style="height: 98px;"></div>
                                        <div class="w-2 bg-ayur-yellow-500 rounded-t-sm -mt-1" style="height: 13px;">
                                        </div>
                                    </div>
                                    <span class="text-xs text-ayur-brown-500 mt-2">Dec</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Summary -->
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="bg-ayur-green-50 rounded-lg p-3 border border-ayur-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-ayur-brown-600">Current Inventory</p>
                                        <p class="text-lg font-bold text-ayur-green-600">₹2.45L</p>
                                    </div>
                                    <div class="text-ayur-green-600">
                                        <i class="fa-solid fa-trending-up"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-ayur-green-600 mt-1">+2.1% from last month</p>
                            </div>
                            <div class="bg-ayur-yellow-50 rounded-lg p-3 border border-ayur-yellow-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-ayur-brown-600">Monthly Restock</p>
                                        <p class="text-lg font-bold text-ayur-yellow-600">₹32K</p>
                                    </div>
                                    <div class="text-ayur-yellow-600">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-ayur-yellow-600 mt-1">-8.5% from last month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LOW STOCK & EXPIRING ITEMS -->
        <div id="alertItems" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LOW STOCK ITEMS -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Low Stock Items</h3>
                    <a href="{{ route('pharmacy.inventory') }}"
                        class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                        View All <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($lowStockItems as $item)
                        <div
                            class="flex items-center justify-between p-3 bg-ayur-yellow-50 rounded-lg border border-ayur-yellow-200">
                            <div class="flex items-center">
                                <div
                                    class="h-10 w-10 bg-ayur-yellow-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fa-solid fa-pills text-ayur-yellow-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">{{ $item->name }}</h4>
                                    <p class="text-xs text-ayur-brown-600">{{ $item->initial_stock_quantity }} remaining
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('pharmacy.edit', $item->id) }}"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                                Restock
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p class="text-sm">No low stock items</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- EXPIRING ITEMS -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Expiring Soon</h3>
                    <a href="{{ route('pharmacy.inventory') }}"
                        class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                        View All <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($expiringItems as $item)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fa-solid fa-pills text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">{{ $item->name }}</h4>
                                    <p class="text-xs text-red-600">Expires in {{ $item->expiry_date->diffInDays(now()) }}
                                        days</p>
                                </div>
                            </div>
                            <a href="{{ route('pharmacy.edit', $item->id) }}"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Mark for Sale
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p class="text-sm">No expiring items</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-medicine-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-medicine-form-' + id)
                        .action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this Medicine?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            }).then(res => {
                                if (res.ok) {
                                    Swal.fire({
                                        text: 'Medicine deleted successfully!',
                                        icon: 'success'
                                    }).then(() => location.reload());
                                } else {
                                    throw new Error('Failed');
                                }
                            }).catch(() => {
                                Swal.fire({
                                    text: 'Something went wrong!',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });

            const tabs = document.querySelectorAll('#pharmacyTabs button');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-ayur-green-500',
                            'text-ayur-green-600');
                        t.classList.add('border-transparent',
                            'text-ayur-brown-600');
                    });

                    // Add active class to clicked tab
                    this.classList.remove('border-transparent', 'text-ayur-brown-600');
                    this.classList.add('border-ayur-green-500', 'text-ayur-green-600');
                });
            });
        });
    </script>
@endsection
