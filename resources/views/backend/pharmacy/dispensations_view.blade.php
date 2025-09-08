@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-ayur-brown-800">{{ $pagename }}</h2>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-ayur-brown-700 hover:text-ayur-green-600">
                                <i class="fa-solid fa-home mr-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('pharmacy.dispense') }}"
                                    class="ml-1 text-sm font-medium text-ayur-brown-700 hover:text-ayur-green-600 md:ml-2">
                                    Pharmacy
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $breadcrumb }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('pharmacy.dispense') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                    <i class="fa-solid fa-prescription-bottle-medical mr-2"></i>
                    New Dispense
                </a>
            </div>
        </div>

        <!-- DISPENSATIONS TABLE -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">All Dispensations</h3>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search by receipt, patient, or doctor..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-ayur-green-500 focus:border-ayur-green-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <select id="statusFilter"
                        class="border border-gray-300 rounded-md px-3 py-2 focus:ring-ayur-green-500 focus:border-ayur-green-500">
                        <option value="">All Status</option>
                        <option value="ready">Ready</option>
                        <option value="partial">Partial</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="dispensationsTable">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Receipt No.
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Prescribed By
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Dispensed By
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Dispensed On
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Items
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dispensations as $dispense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    <span class="font-medium">{{ $dispense->receipt_number }}</span>
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
                                    {{ $dispense->dispensedBy ? $dispense->dispensedBy->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $dispense->dispense_date ? $dispense->dispense_date->format('d/m/Y') : 'N/A' }}
                                    <div class="text-xs text-gray-500">
                                        {{ $dispense->created_at ? $dispense->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $dispense->items ? $dispense->items->count() : 0 }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dispense->dispense_status_class }}">
                                        {{ $dispense->dispense_status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-ayur-brown-800">
                                    ₹{{ number_format($dispense->total_amount, 2) }}
                                    @if ($dispense->paymentMode)
                                        <div class="text-xs text-gray-500">{{ $dispense->paymentMode->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-ayur-green-600 hover:text-ayur-green-900" title="View Details"
                                            onclick="viewDispenseDetails({{ $dispense->id }})">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="text-ayur-brown-600 hover:text-ayur-brown-900" title="Print Receipt"
                                            onclick="printReceipt('{{ $dispense->receipt_number }}')">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" title="Email Receipt"
                                            onclick="emailReceipt({{ $dispense->id }})">
                                            <i class="fa-solid fa-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-prescription-bottle-medical text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No dispensations found</h3>
                                        <p class="text-sm text-gray-500 mb-4">Dispensations will appear here once medicines
                                            are dispensed to patients.</p>
                                        <a href="{{ route('pharmacy.dispense') }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                            <i class="fa-solid fa-plus mr-2"></i>
                                            Dispense First Medicine
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($dispensations->hasPages())
                <div class="mt-6">
                    {{ $dispensations->links() }}
                </div>
            @endif
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#dispensationsTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function(e) {
            const status = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#dispensationsTable tbody tr');

            rows.forEach(row => {
                if (!status) {
                    row.style.display = '';
                    return;
                }

                const statusCell = row.querySelector('td:nth-child(7) span');
                if (statusCell) {
                    const rowStatus = statusCell.textContent.toLowerCase();
                    row.style.display = rowStatus.includes(status) ? '' : 'none';
                }
            });
        });

        // View dispense details
        function viewDispenseDetails(dispenseId) {
            // TODO: Implement view details modal or redirect to details page
            console.log('View details for dispense:', dispenseId);
            alert('View details functionality will be implemented soon.');
        }

        // Print receipt
        function printReceipt(receiptNumber) {
            // TODO: Implement print functionality
            console.log('Print receipt:', receiptNumber);
            alert('Print functionality will be implemented soon.');
        }

        // Email receipt
        function emailReceipt(dispenseId) {
            // TODO: Implement email functionality
            console.log('Email receipt for dispense:', dispenseId);
            alert('Email functionality will be implemented soon.');
        }
    </script>
@endsection
