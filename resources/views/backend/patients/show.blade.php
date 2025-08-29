@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">

        <div class="mb-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">
                    {{ session('status') }}</div>
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

        <!-- PATIENT INFO -->
        <div id="patientInfo" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row">
                <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                    <div class="relative">
                        <img class="h-24 w-24 rounded-full object-cover border-4 border-ayur-green-200"
                            src="{{ $patient->photo_path ? asset($patient->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                            onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                            alt="Patient avatar">
                        <span
                            class="absolute bottom-0 right-0 h-6 w-6 rounded-full bg-ayur-green-500 border-2 border-white flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-xs"></i>
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-ayur-brown-800">{{ $patient->full_name }}</h3>
                            <div class="flex items-center mt-1 text-ayur-brown-600">
                                <span
                                    class="bg-ayur-green-100 text-ayur-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">
                                    UHID: {{ $patient->uhid }}
                                </span>
                                <span class="text-sm">{{ $patient->age }} Years / {{ ucfirst($patient->gender) }}</span>
                                <span class="mx-2">•</span>
                                <span class="text-sm">{{ $patient->prakriti }}</span>
                            </div>
                        </div>
                        <div class="flex mt-3 md:mt-0">
                            {{-- <button
                                class="bg-ayur-green-100 text-ayur-green-700 hover:bg-ayur-green-200 font-medium rounded-lg text-sm px-4 py-2 flex items-center mr-2">
                                <i class="fa-solid fa-edit mr-2"></i> Edit
                            </button> --}}
                            {{-- edit button --}}
                            {{-- @permission('patients.edit') --}}
                            <a href="{{ route('patients.edit', ['patient' => $patient->getRouteKey()]) }}"
                                class="bg-ayur-green-100 text-ayur-green-700 hover:bg-ayur-green-200 font-medium rounded-lg text-sm px-4 py-2 flex items-center mr-2"
                                title="Edit">
                                <i class="fa-solid fa-edit mr-2"></i> Edit</a>
                            {{-- @endpermission --}}
                            <button
                                class="bg-ayur-yellow-100 text-ayur-yellow-700 hover:bg-ayur-yellow-200 font-medium rounded-lg text-sm px-4 py-2 flex items-center">
                                <i class="fa-solid fa-calendar-plus mr-2"></i> New Appointment
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
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
                                <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                <i class="fa-solid fa-location-dot text-ayur-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-ayur-brown-600">Address</p>
                                <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PATIENT TABS -->
        <div id="patientProfileTabs" class="mb-6">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex overflow-x-auto scrollbar-hide">
                    <button id="visitsTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-green-600 border-b-2 border-ayur-green-500 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-calendar-check mr-2"></i> Past Visits
                    </button>
                    <button id="prescriptionsTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-brown-600 hover:text-ayur-brown-800 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-prescription mr-2"></i> Prescriptions
                    </button>
                    <button id="panchkarmaTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-brown-600 hover:text-ayur-brown-800 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-spa mr-2"></i> Panchkarma History
                    </button>
                    <button id="billsTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-brown-600 hover:text-ayur-brown-800 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Bills
                    </button>
                    <button id="feedbackTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-brown-600 hover:text-ayur-brown-800 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-comment-dots mr-2"></i> Feedback
                    </button>
                    <button id="reportsTab"
                        class="flex-shrink-0 px-6 py-4 text-ayur-brown-600 hover:text-ayur-brown-800 font-medium text-sm focus:outline-none">
                        <i class="fa-solid fa-file-medical mr-2"></i> Reports
                    </button>
                </div>
            </div>
        </div>

        <!-- VISITS TAB CONTENT -->
        <div id="visitsTabContent" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Past Visits</h3>
                <div class="flex items-center">
                    <div class="relative mr-2">
                        <input type="text" placeholder="Search visits..."
                            class="pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button
                        class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fa-solid fa-plus mr-1"></i> New Visit
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Visit Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Visit Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Chief Complaint</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Treatment</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">15 Jul, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">OPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-3.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Dr. Sharma</div>
                                        <div class="text-xs text-ayur-brown-600">Ayurvedic Physician</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Chronic Digestive
                                Issues</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dietary changes,
                                Herbal formulation</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-print"></i></button>
                                <button class="text-ayur-yellow-600 hover:text-ayur-yellow-900"><i
                                        class="fa-solid fa-share"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">28 Jun, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">IPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-4.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Dr. Patel</div>
                                        <div class="text-xs text-ayur-brown-600">Panchkarma Specialist</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Chronic Lower Back
                                Pain</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Kati Basti,
                                Abhyanga, Herbal oils</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-print"></i></button>
                                <button class="text-ayur-yellow-600 hover:text-ayur-yellow-900"><i
                                        class="fa-solid fa-share"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PRESCRIPTIONS TAB CONTENT -->
        <div id="prescriptionsTabContent" class="bg-white rounded-lg shadow-md p-6 mb-6 hidden">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Prescriptions</h3>
                <div class="flex items-center">
                    <div class="relative mr-2">
                        <input type="text" placeholder="Search prescriptions..."
                            class="pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <a href="{{ route('patients.prescriptions.create', $patient) }}"
                        class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2"
                        role="button">
                        <i class="fa-solid fa-plus mr-1"></i> Add Prescription
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="prescription1" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-medium text-ayur-brown-800">Prescription #PR-2025-153</h4>
                            <p class="text-sm text-ayur-brown-600">15 Jul, 2025</p>
                        </div>
                        <span class="px-2 py-1 bg-ayur-green-100 text-ayur-green-800 text-xs rounded-full">Active</span>
                    </div>
                    <div class="mb-3 pb-3 border-b border-gray-100">
                        <p class="text-sm text-ayur-brown-700 mb-1"><span class="font-medium">Doctor:</span> Dr.
                            Sharma</p>
                        <p class="text-sm text-ayur-brown-700"><span class="font-medium">Diagnosis:</span> Chronic
                            Digestive Issues (Pitta imbalance)</p>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="flex items-start">
                            <i class="fa-solid fa-capsules text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Avipattikar Churna</p>
                                <p class="text-xs text-ayur-brown-600">2g twice daily after meals with warm water
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fa-solid fa-prescription-bottle text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Triphala Tablet</p>
                                <p class="text-xs text-ayur-brown-600">2 tablets at bedtime with warm water</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fa-solid fa-flask text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Amalaki Rasayana</p>
                                <p class="text-xs text-ayur-brown-600">1 teaspoon with honey in the morning</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                            <i class="fa-solid fa-eye mr-1"></i> View
                        </button>
                        <button class="text-ayur-brown-600 hover:text-ayur-brown-700 text-sm font-medium">
                            <i class="fa-solid fa-print mr-1"></i> Print
                        </button>
                    </div>
                </div>

                <div id="prescription2" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-medium text-ayur-brown-800">Prescription #PR-2025-112</h4>
                            <p class="text-sm text-ayur-brown-600">28 Jun, 2025</p>
                        </div>
                        <span class="px-2 py-1 bg-ayur-green-100 text-ayur-green-800 text-xs rounded-full">Active</span>
                    </div>
                    <div class="mb-3 pb-3 border-b border-gray-100">
                        <p class="text-sm text-ayur-brown-700 mb-1"><span class="font-medium">Doctor:</span> Dr.
                            Patel</p>
                        <p class="text-sm text-ayur-brown-700"><span class="font-medium">Diagnosis:</span> Chronic
                            Lower Back Pain (Vata imbalance)</p>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="flex items-start">
                            <i class="fa-solid fa-oil-can text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Mahanarayana Taila</p>
                                <p class="text-xs text-ayur-brown-600">For external application on affected area</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fa-solid fa-pills text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Yogaraja Guggulu</p>
                                <p class="text-xs text-ayur-brown-600">2 tablets twice daily after meals</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fa-solid fa-mortar-pestle text-ayur-green-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-ayur-brown-800">Ashwagandha Churna</p>
                                <p class="text-xs text-ayur-brown-600">1 teaspoon with warm milk at bedtime</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                            <i class="fa-solid fa-eye mr-1"></i> View
                        </button>
                        <button class="text-ayur-brown-600 hover:text-ayur-brown-700 text-sm font-medium">
                            <i class="fa-solid fa-print mr-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <div class="text-sm text-ayur-brown-600">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span
                        class="font-medium">8</span> prescriptions
                </div>
                <div class="flex space-x-2">
                    <button
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-ayur-brown-600 bg-white hover:bg-ayur-offwhite">
                        Previous
                    </button>
                    <button
                        class="px-3 py-1 border border-ayur-green-500 rounded-md text-sm font-medium text-white bg-ayur-green-500 hover:bg-ayur-green-600">
                        1
                    </button>
                    <button
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-ayur-brown-600 bg-white hover:bg-ayur-offwhite">
                        2
                    </button>
                    <button
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-ayur-brown-600 bg-white hover:bg-ayur-offwhite">
                        Next
                    </button>
                </div>
            </div>
        </div>


    </main>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('backend-assets/js/validation/patients/patientShow.js') }}"></script>
@endpush
