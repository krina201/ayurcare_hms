@extends('backend.layouts.master')

@section('css')
    <style>
        .error-field {
            position: relative;
        }

        .error-field::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 1px solid #ef4444;
            border-radius: 0.375rem;
            pointer-events: none;
            animation: errorPulse 0.5s ease-in-out;
        }

        @keyframes errorPulse {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .field-success {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .field-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .validation-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .validation-icon.success {
            color: #10b981;
        }

        .validation-icon.error {
            color: #ef4444;
        }

        /* Enhanced error styling */
        .js-error {
            transition: all 0.3s ease;
        }

        .js-error:not([style*="display: none"]) {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input focus states */
        input:focus,
        select:focus,
        textarea:focus {
            transition: all 0.3s ease;
        }

        /* Error state styling */
        .border-red-500:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Success state styling */
        .border-green-500:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Consultation Type styling */
        .appointment-type-option {
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .appointment-type-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .appointment-type-option.active {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
        }

        .appointment-type-option input[type="radio"]:checked+* {
            color: #059669;
        }

        /* Consultation type icons */
        .appointment-type-option i {
            transition: all 0.2s ease;
        }

        .appointment-type-option:hover i {
            transform: scale(1.1);
        }

        /* Time slot styling */
        .time-slot-option {
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .time-slot-option:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .time-slot-option input[type="radio"]:checked+* {
            font-weight: 600;
        }

        /* Selected time slot styling */
        .time-slot-option.selected {
            background-color: #dbeafe !important;
            border-color: #3b82f6 !important;
            color: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        /* Mode button styling improvements */
        .mode-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .mode-option:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .mode-option.active {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        /* Loading animation */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Fade in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Search results styling */
        #searchResults {
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        #searchResults::-webkit-scrollbar {
            width: 6px;
        }

        #searchResults::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #searchResults::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #searchResults::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Patient info card styling */
        #patientInfo {
            transition: all 0.3s ease;
        }

        #patientInfo:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        <div id="appointmentTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Book Appointment
                    </button>
                    <a href="{{ route('appointment.calendar') }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Appointment Calendar
                    </a>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Queue Manager
                    </button>
                </nav>
            </div>
        </div>

        <!-- BOOKING FORM -->
        <div id="bookingForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Validation Messages Container - Above the form -->
            <div id="validationMessages" class="mb-4"></div>

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">New Appointment</h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                        <i class="fa-solid fa-hospital-user mr-1"></i> OPD
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-yellow-100 text-ayur-yellow-800">
                        <i class="fa-solid fa-spa mr-1"></i> Panchkarma
                    </span>
                </div>
            </div>

            <form action="{{ route('appointment.store') }}" method="POST" id="appointment-form">
                @csrf
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- COLUMN 1: PATIENT SELECTION -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-ayur-brown-800 border-b border-gray-200 pb-2">1. Select Patient
                        </h4>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Patient Type</label>
                            <div class="flex space-x-2">
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center">
                                    <i class="fa-solid fa-user mr-2"></i> Existing
                                </button>
                                <a href="{{ route('patients') }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center">
                                    <i class="fa-solid fa-user-plus mr-2"></i> New
                                </a>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Search Patient</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="patientSearch"
                                    class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="UHID, Name or Mobile">
                                <input type="hidden" name="patient_id" id="selectedPatientId" required>

                                <!-- Search Results Dropdown -->
                                <div id="searchResults"
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                </div>
                            </div>
                            <!-- Debug button for testing -->
                            <div class="mt-2 flex space-x-2">
                                <button type="button" id="testPatientSearch"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded border">
                                    <i class="fa-solid fa-bug mr-1"></i> Test Search
                                </button>
                                <button type="button" id="testPatientRoute"
                                    class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded border">
                                    <i class="fa-solid fa-route mr-1"></i> Test Route
                                </button>
                                <button type="button" id="clearPatientSearch"
                                    class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded border">
                                    <i class="fa-solid fa-times mr-1"></i> Clear
                                </button>
                            </div>
                            <!-- Patient validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="patient_id-error" style="display:none"></p>
                            @error('patient_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="patientInfo" class="border rounded-md p-4 bg-ayur-offwhite hidden">
                            <div class="flex items-center mb-2">
                                <img id="patientPhoto" class="h-10 w-10 rounded-full mr-3"
                                    src="{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}"
                                    alt="Patient avatar">
                                <div>
                                    <h5 id="patientName" class="font-medium text-ayur-brown-800"></h5>
                                    <p id="patientDetails" class="text-xs text-ayur-brown-600"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                <div>
                                    <span class="text-ayur-brown-600">Mobile:</span>
                                    <span id="patientMobile" class="text-ayur-brown-800 ml-1"></span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Prakriti:</span>
                                    <span id="patientPrakriti" class="text-ayur-brown-800 ml-1"></span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Last Visit:</span>
                                    <span id="patientLastVisit" class="text-ayur-brown-800 ml-1">N/A</span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Allergies:</span>
                                    <span id="patientAllergies" class="text-ayur-brown-800 ml-1">None</span>
                                </div>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="button" class="text-xs text-ayur-green-600 hover:text-ayur-green-700">
                                    View Profile <i class="fa-solid fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>

                        <div id="noPatientSelected" class="border rounded-md p-4 bg-gray-50 text-center text-gray-500">
                            <i class="fa-solid fa-user-plus text-2xl mb-2"></i>
                            <p>Search and select a patient to continue</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Appointment
                                Mode</label>
                            <div class="flex space-x-2">
                                <label
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center cursor-pointer mode-option"
                                    data-value="OPD">
                                    <input type="radio" name="mode" value="OPD" class="hidden" required checked>
                                    <i class="fa-solid fa-hospital-user mr-2"></i> OPD
                                </label>
                                <label
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center cursor-pointer mode-option"
                                    data-value="Panchkarma">
                                    <input type="radio" name="mode" value="Panchkarma" class="hidden" required>
                                    <i class="fa-solid fa-spa mr-2"></i> Panchkarma
                                </label>
                            </div>
                            <!-- Mode validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="mode-error" style="display:none"></p>
                            @error('mode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- COLUMN 2: DOCTOR SELECTION -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-ayur-brown-800 border-b border-gray-200 pb-2">2. Select
                            Department &amp; Doctor</h4>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Department</label>
                            <select name="department_id" id="department_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Department</option>
                                @foreach ($departments ?? [] as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <!-- Department validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="department_id-error" style="display:none">
                            </p>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Doctor</label>
                            <select name="doctor_id" id="doctor_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                required>
                                <option value="">Select Doctor</option>
                                @foreach ($doctors ?? [] as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->full_name }}</option>
                                @endforeach
                            </select>
                            <!-- Doctor validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="doctor_id-error" style="display:none"></p>
                            @error('doctor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Consultation
                                Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="border rounded-md p-3 bg-amber-50 border-amber-200 flex items-center justify-center cursor-pointer appointment-type-option active"
                                    data-value="First Visit">
                                    <input type="radio" name="appointment_type" value="First Visit" class="hidden"
                                        required checked>
                                    <i class="fa-solid fa-user-doctor text-amber-600 mr-2"></i>
                                    <span class="text-sm font-medium text-ayur-brown-800">First Visit</span>
                                </label>
                                <label
                                    class="border rounded-md p-3 hover:bg-amber-50 hover:border-amber-200 flex items-center justify-center cursor-pointer appointment-type-option"
                                    data-value="Follow Up">
                                    <input type="radio" name="appointment_type" value="Follow Up" class="hidden"
                                        required>
                                    <i class="fa-solid fa-rotate text-ayur-brown-600 mr-2"></i>
                                    <span class="text-sm font-medium text-ayur-brown-800">Follow Up</span>
                                </label>

                            </div>
                            <!-- Appointment type validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="appointment_type-error"
                                style="display:none"></p>
                            @error('appointment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- COLUMN 3: TIME SLOT SELECTION -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-ayur-brown-800 border-b border-gray-200 pb-2">3. Select Date
                            &amp; Time Slot</h4>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                            <!-- Date validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="appointment_date-error"
                                style="display:none"></p>
                            @error('appointment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-ayur-brown-700">Available Time
                                    Slots</label>
                                <span class="text-xs text-ayur-green-600" id="doctorScheduleInfo">Select Doctor and
                                    Date</span>
                            </div>



                            <div class="border rounded-md p-3 bg-white" id="timeSlotsContainer">
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fa-solid fa-clock text-2xl mb-2"></i>
                                    <p>Please select a doctor and date to view available time slots</p>
                                </div>
                            </div>
                            <!-- Time validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="appointment_time-error"
                                style="display:none"></p>
                            @error('appointment_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-ayur-offwhite rounded-md p-4 border border-gray-200">
                            <h5 class="font-medium text-ayur-brown-800 mb-2">Appointment Summary</h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Patient:</span>
                                    <span class="text-ayur-brown-800">Select Patient</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Doctor:</span>
                                    <span class="text-ayur-brown-800">Select Doctor</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Date &amp; Time:</span>
                                    <span class="text-ayur-brown-800">Select Date & Time</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Type:</span>
                                    <span class="text-ayur-brown-800">Select Type</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Fee:</span>
                                    <span class="font-medium text-ayur-brown-800">₹0</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Chief Complaint</label>
                            <textarea name="chief_complaint" id="chief_complaint"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="2" placeholder="Enter patient's chief complaint"></textarea>
                            <!-- Chief complaint validation error message -->
                            <p class="mt-1 text-sm text-red-600 js-error" id="chief_complaint-error"
                                style="display:none"></p>
                            @error('chief_complaint')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Clear Form
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>

        <!-- QUICK STATS -->
        <div id="quickStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-green-500 hover:shadow-lg transition-shadow duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Today's Appointments</p>
                        <p class="text-2xl font-bold text-ayur-brown-800" id="todayCount">
                            {{ $todayAppointments->count() ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-green-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span id="todayPercentage">{{ $todayPercentage ?? 0 }}%</span> from yesterday
                </p>
            </div>

            <div
                class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-yellow-500 hover:shadow-lg transition-shadow duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Available Slots</p>
                        <p class="text-2xl font-bold text-ayur-brown-800" id="availableSlots">{{ $availableSlots ?? 0 }}
                        </p>
                    </div>
                    <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-yellow-600 mt-2">
                    <i class="fa-solid fa-arrows-left-right"></i> All doctors combined
                </p>
            </div>

            <div
                class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-brown-500 hover:shadow-lg transition-shadow duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Waiting Patients</p>
                        <p class="text-2xl font-bold text-ayur-brown-800" id="waitingCount">
                            {{ $waitingAppointments->count() ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-ayur-brown-100 p-2 text-ayur-brown-600">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-brown-600 mt-2">
                    <i class="fa-solid fa-arrow-down"></i>
                    <span id="waitingDifference">{{ $waitingDifference ?? 0 }}</span> less than usual
                </p>
            </div>

            <div
                class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Completed Today</p>
                        <p class="text-2xl font-bold text-ayur-brown-800" id="completedCount">
                            {{ $completedAppointments->count() ?? 0 }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span id="completionPercentage">{{ $completionPercentage ?? 0 }}%</span> of scheduled
                </p>
            </div>
        </div>

        <!-- TODAY'S APPOINTMENTS -->
        <div id="todaysAppointments" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Today's Appointments</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="example">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider cursor-pointer hover:bg-ayur-green-50">
                                Time
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider cursor-pointer hover:bg-ayur-green-50">
                                Patient
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider cursor-pointer hover:bg-ayur-green-50">
                                Doctor
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider cursor-pointer hover:bg-ayur-green-50">
                                Type
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider cursor-pointer hover:bg-ayur-green-50">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="appointmentsTableBody">
                        @forelse($todayAppointments ?? [] as $appointment)
                            <tr class="hover:bg-gray-50 transition-colors duration-150"
                                data-appointment-id="{{ $appointment->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                src="{{ $appointment->patient->photo_path ? asset($appointment->patient->photo_path) : asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}"
                                                alt="{{ $appointment->patient->name ?? 'Patient' }}"
                                                onerror="this.src='{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $appointment->patient->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-ayur-brown-600">
                                                {{ $appointment->patient->patient_id ?? 'N/A' }} •
                                                {{ $appointment->patient->age ?? 'N/A' }}/{{ $appointment->patient->gender ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-ayur-brown-800">
                                        {{ $appointment->doctor->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-ayur-brown-600">{{ $appointment->department->name ?? 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $appointment->appointment_type === 'First Visit'
                                        ? 'bg-ayur-green-100 text-ayur-green-800'
                                        : ($appointment->appointment_type === 'Follow Up'
                                            ? 'bg-ayur-yellow-100 text-ayur-yellow-800'
                                            : 'bg-gray-100 text-gray-800') }}">
                                        {{ $appointment->appointment_type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $appointment->status === 'Waiting'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : ($appointment->status === 'In Progress'
                                            ? 'bg-blue-100 text-blue-800'
                                            : ($appointment->status === 'Completed'
                                                ? 'bg-green-100 text-green-800'
                                                : ($appointment->status === 'Cancelled'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ $appointment->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button
                                        class="text-ayur-green-600 hover:text-ayur-green-900 mr-3 transition-colors duration-150"
                                        onclick="viewAppointment({{ $appointment->id }})" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button
                                        class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3 transition-colors duration-150"
                                        onclick="editAppointment({{ $appointment->id }})" title="Edit Appointment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                        onclick="cancelAppointment({{ $appointment->id }})" title="Cancel Appointment">
                                        <i class="fa-solid fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-calendar-times text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-lg font-medium">No appointments for today</p>
                                        <p class="text-sm">All clear! No scheduled appointments.</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            const patientSearch = document.getElementById('patientSearch');
            const searchResults = document.getElementById('searchResults');
            const selectedPatientId = document.getElementById('selectedPatientId');
            const patientInfo = document.getElementById('patientInfo');
            const noPatientSelected = document.getElementById('noPatientSelected');
            const validationMessages = document.getElementById('validationMessages'); // New variable

            let searchTimeout;

            // Client-side validation for appointment form
            const form = document.getElementById('appointment-form');
            if (!form) return;

            const fields = {
                patient_id: document.getElementById('selectedPatientId'),
                department_id: document.getElementById('department_id'),
                doctor_id: document.getElementById('doctor_id'),
                appointment_date: document.getElementById('appointment_date'),
                appointment_time: () => document.querySelector('input[name="appointment_time"]:checked'),
                appointment_type: () => document.querySelector('input[name="appointment_type"]:checked'),
                mode: () => document.querySelector('input[name="mode"]:checked'),
                chief_complaint: document.getElementById('chief_complaint')
            };

            function showError(fieldName, message) {
                const el = document.getElementById(fieldName + '-error');
                const input = fields[fieldName];
                if (el) {
                    el.textContent = message;
                    el.style.display = 'block';
                }
                if (input && input.classList) {
                    input.classList.add('border-red-500');
                    input.classList.remove('border-gray-300');
                }

                // Add error styling to the field container
                const fieldContainer = getFieldContainer(fieldName);
                if (fieldContainer) {
                    fieldContainer.classList.add('error-field');
                }
            }

            function clearError(fieldName) {
                const el = document.getElementById(fieldName + '-error');
                const input = fields[fieldName];
                if (el) {
                    el.textContent = '';
                    el.style.display = 'none';
                }
                if (input && input.classList) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-300');
                }

                // Remove error styling from the field container
                const fieldContainer = getFieldContainer(fieldName);
                if (fieldContainer) {
                    fieldContainer.classList.remove('error-field');
                }
            }

            // Helper function to get field container for styling
            function getFieldContainer(fieldName) {
                const input = fields[fieldName];
                if (!input) return null;

                try {
                    // For radio buttons, get the parent container
                    if (typeof input === 'function') {
                        const radioInput = input();
                        if (radioInput && radioInput.closest) {
                            return radioInput.closest('.space-y-4');
                        }
                        return null;
                    }

                    // For regular inputs, get the parent div
                    if (input && input.closest) {
                        return input.closest('.space-y-4') || input.parentElement;
                    }

                    return null;
                } catch (error) {
                    console.warn('Error in getFieldContainer for field:', fieldName, error);
                    return null;
                }
            }

            // Function to show success state for valid fields
            function showSuccess(fieldName) {
                const input = fields[fieldName];
                if (input && input.classList) {
                    input.classList.add('field-success');
                }
                // Optionally, add a success icon
                const validationIcon = document.getElementById(fieldName + '-validation-icon');
                if (validationIcon) {
                    validationIcon.classList.add('success');
                }
            }

            // Function to reset field styling to normal
            function resetFieldStyling(fieldName) {
                const input = fields[fieldName];
                if (input && input.classList) {
                    input.classList.remove('border-red-500', 'field-success');
                    input.classList.add('border-gray-300');
                }
                // Optionally, remove success icon
                const validationIcon = document.getElementById(fieldName + '-validation-icon');
                if (validationIcon) {
                    validationIcon.classList.remove('success');
                }
            }

            function validate() {
                let valid = true;

                // Patient validation
                const patientId = fields.patient_id && fields.patient_id.value;
                clearError('patient_id');
                if (!patientId) {
                    showError('patient_id', 'Please select a patient');
                    valid = false;
                }

                // Department validation
                const departmentId = fields.department_id && fields.department_id.value;
                clearError('department_id');
                if (!departmentId) {
                    showError('department_id', 'Please select a department');
                    valid = false;
                }

                // Doctor validation
                const doctorId = fields.doctor_id && fields.doctor_id.value;
                clearError('doctor_id');
                if (!doctorId) {
                    showError('doctor_id', 'Please select a doctor');
                    valid = false;
                }

                // Date validation
                const appointmentDate = fields.appointment_date && fields.appointment_date.value;
                clearError('appointment_date');
                if (!appointmentDate) {
                    showError('appointment_date', 'Please select an appointment date');
                    valid = false;
                } else {
                    const selectedDate = new Date(appointmentDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        showError('appointment_date', 'Appointment date cannot be in the past');
                        valid = false;
                    }
                }

                // Time validation
                const appointmentTime = fields.appointment_time();
                clearError('appointment_time');
                if (!appointmentTime) {
                    showError('appointment_time', 'Please select an appointment time slot');
                    valid = false;
                }

                // Mode validation
                const mode = fields.mode();
                clearError('mode');
                if (!mode) {
                    showError('mode', 'Please select appointment mode');
                    valid = false;
                }

                // Type validation
                const appointmentType = fields.appointment_type();
                clearError('appointment_type');
                if (!appointmentType) {
                    showError('appointment_type', 'Please select appointment type');
                    valid = false;
                }

                // Chief complaint validation (optional but recommended)
                const chiefComplaint = fields.chief_complaint && fields.chief_complaint.value.trim();
                clearError('chief_complaint');
                if (!chiefComplaint) {
                    showError('chief_complaint', 'Chief complaint is recommended');
                    // Don't set valid = false for this as it's optional
                }

                // Update submit button state based on validation
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    if (!valid) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }

                return valid;
            }

            // Real-time validation function
            function validateField(fieldName) {
                const field = fields[fieldName];
                if (!field) return;

                let isValid = true;
                let errorMessage = '';

                switch (fieldName) {
                    case 'patient_id':
                        if (!field.value) {
                            isValid = false;
                            errorMessage = 'Please select a patient';
                        }
                        break;

                    case 'department_id':
                        if (!field.value) {
                            isValid = false;
                            errorMessage = 'Please select a department';
                        }
                        break;

                    case 'doctor_id':
                        if (!field.value) {
                            isValid = false;
                            errorMessage = 'Please select a doctor';
                        }
                        break;

                    case 'appointment_date':
                        if (!field.value) {
                            isValid = false;
                            errorMessage = 'Please select an appointment date';
                        } else {
                            const selectedDate = new Date(field.value);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            if (selectedDate < today) {
                                isValid = false;
                                errorMessage = 'Appointment date cannot be in the past';
                            }
                        }
                        break;

                    case 'appointment_time':
                        const timeInput = field();
                        if (!timeInput) {
                            isValid = false;
                            errorMessage = 'Please select an appointment time slot';
                        }
                        break;

                    case 'mode':
                        const modeInput = field();
                        if (!modeInput) {
                            isValid = false;
                            errorMessage = 'Please select appointment mode';
                        }
                        break;

                    case 'appointment_type':
                        const typeInput = field();
                        if (!typeInput) {
                            isValid = false;
                            errorMessage = 'Please select appointment type';
                        }
                        break;

                    case 'chief_complaint':
                        // Chief complaint is optional, so no validation needed
                        break;
                }

                if (!isValid) {
                    showError(fieldName, errorMessage);
                } else {
                    clearError(fieldName);
                    // Show success state for valid fields (optional)
                    if (fieldName !== 'chief_complaint') { // Don't show success for optional field
                        showSuccess(fieldName);
                        // Reset to normal styling after a short delay
                        setTimeout(() => {
                            resetFieldStyling(fieldName);
                        }, 1000);
                    }
                }

                // Update overall form validation
                validate();
            }

            // Patient search functionality
            patientSearch.addEventListener('input', function() {
                const query = this.value.trim();

                // Clear previous timeout
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                // Set timeout to avoid too many requests
                searchTimeout = setTimeout(() => {
                    searchPatients(query);
                }, 300);
            });

            // Search patients via AJAX
            function searchPatients(query) {
                console.log('Searching patients with query:', query);

                // Show loading state
                searchResults.innerHTML = `
                    <div class="p-3 text-gray-500 text-center">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                        Searching patients...
                    </div>
                `;
                searchResults.classList.remove('hidden');

                const url = `{{ route('appointment.search-patient') }}?query=${encodeURIComponent(query)}`;
                console.log('Fetching from URL:', url);

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
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

                        searchResults.innerHTML = `
                        <div class="p-3 text-red-500 text-center">
                            <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                            ${error.message || 'Error searching patients. Please try again.'}
                            <br>
                            <small class="text-gray-500">Check console for more details</small>
                        </div>
                    `;
                        searchResults.classList.remove('hidden');
                    });
            }

            // Display search results
            function displaySearchResults(patients) {
                if (!Array.isArray(patients) || patients.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-3 text-gray-500 text-center">
                            <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                            No patients found
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                    return;
                }

                const resultsHtml = patients.map(patient => {
                    // Escape special characters to prevent XSS
                    const safeName = patient.full_name ? patient.full_name.replace(/[<>]/g, '') : 'N/A';
                    const safeUhid = patient.uhid ? patient.uhid.replace(/[<>]/g, '') : 'N/A';
                    const safeGender = patient.gender ? patient.gender.replace(/[<>]/g, '') : 'N/A';
                    const safeMobile = patient.mobile ? patient.mobile.replace(/[<>]/g, '') : 'N/A';
                    const safePrakriti = patient.prakriti ? patient.prakriti.replace(/[<>]/g, '') : 'N/A';
                    const safeAllergies = patient.allergies ? patient.allergies.replace(/[<>]/g, '') :
                        'None';
                    const safePhotoPath = patient.photo_path ? patient.photo_path.replace(/[<>]/g, '') : '';

                    const age = patient.age || 'N/A';
                    const genderInitial = safeGender.charAt(0).toUpperCase();

                    return `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                             onclick="selectPatient(${patient.id}, '${safeUhid}', '${safeName}', '${safeGender}', '${age}', '${safeMobile}', '${safePrakriti}', '${safeAllergies}', '${safePhotoPath}')">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                         src="${safePhotoPath ? '{{ asset('') }}' + safePhotoPath : '{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}'}" 
                                         alt="Patient avatar"
                                         onerror="this.src='{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}'">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">${safeName}</div>
                                    <div class="text-xs text-gray-500">${safeUhid} • ${age}/${genderInitial}</div>
                                    <div class="text-xs text-gray-400">${safeMobile}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                searchResults.innerHTML = resultsHtml;
                searchResults.classList.remove('hidden');
            }

            // Select patient function
            window.selectPatient = function(id, uhid, name, gender, age, mobile, prakriti, allergies, photoPath) {
                try {
                    // Validate required parameters
                    if (!id || !name) {
                        console.error('Invalid patient data:', {
                            id,
                            name
                        });
                        return;
                    }

                    // Set hidden input value
                    selectedPatientId.value = id;

                    // Update patient info display
                    const patientNameEl = document.getElementById('patientName');
                    const patientDetailsEl = document.getElementById('patientDetails');
                    const patientMobileEl = document.getElementById('patientMobile');
                    const patientPrakritiEl = document.getElementById('patientPrakriti');
                    const patientAllergiesEl = document.getElementById('patientAllergies');
                    const patientPhotoEl = document.getElementById('patientPhoto');

                    if (patientNameEl) patientNameEl.textContent = name || 'N/A';
                    if (patientDetailsEl) patientDetailsEl.textContent =
                        `${uhid || 'N/A'} • ${age || 'N/A'}/${(gender || 'N/A').charAt(0).toUpperCase()}`;
                    if (patientMobileEl) patientMobileEl.textContent = mobile || 'N/A';
                    if (patientPrakritiEl) patientPrakritiEl.textContent = prakriti || 'N/A';
                    if (patientAllergiesEl) patientAllergiesEl.textContent = allergies || 'None';

                    // Update patient photo
                    if (patientPhotoEl) {
                        if (photoPath && photoPath.trim() !== '') {
                            patientPhotoEl.src = '{{ asset('') }}' + photoPath;
                            // Add error handling for image
                            patientPhotoEl.onerror = function() {
                                this.src =
                                    '{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}';
                            };
                        } else {
                            patientPhotoEl.src =
                                '{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}';
                        }
                    }

                    // Show patient info, hide placeholder
                    if (patientInfo) patientInfo.classList.remove('hidden');
                    if (noPatientSelected) noPatientSelected.classList.add('hidden');

                    // Clear search and hide results
                    if (patientSearch) patientSearch.value = name;
                    if (searchResults) searchResults.classList.add('hidden');

                    // Update appointment summary
                    if (typeof updateAppointmentSummary === 'function') {
                        updateAppointmentSummary();
                    }

                    // Clear patient error
                    clearError('patient_id');

                    // Add visual feedback
                    if (patientInfo) {
                        patientInfo.style.animation = 'fadeIn 0.3s ease-in-out';
                        setTimeout(() => {
                            patientInfo.style.animation = '';
                        }, 300);
                    }

                } catch (error) {
                    console.error('Error selecting patient:', error);
                    // Show error message to user
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
            };

            // Update appointment summary
            function updateAppointmentSummary() {
                const patientName = document.getElementById('patientName').textContent;
                const doctorSelect = document.querySelector('select[name="doctor_id"]');
                const dateInput = document.querySelector('input[name="appointment_date"]');
                const timeInput = document.querySelector('input[name="appointment_time"]:checked');
                const modeInput = document.querySelector('input[name="mode"]:checked');
                const typeInput = document.querySelector('input[name="appointment_type"]:checked');

                if (patientName && doctorSelect.value) {
                    const selectedDoctor = doctorSelect.options[doctorSelect.selectedIndex].text;

                    // Get fee information
                    if (doctorSelect.value && typeInput) {
                        fetch(
                                `{{ route('appointment.doctor-fees') }}?doctor_id=${doctorSelect.value}&appointment_type=${typeInput.value}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                if (data.calculated_fee) {
                                    updateSummaryWithFee(patientName, selectedDoctor, dateInput, timeInput,
                                        modeInput, typeInput, data.calculated_fee);
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching fees:', error);
                                updateSummaryWithFee(patientName, selected, dateInput, timeInput, modeInput,
                                    typeInput, 0);
                            });
                    } else {
                        updateSummaryWithFee(patientName, selectedDoctor, dateInput, timeInput, modeInput,
                            typeInput, 0);
                    }
                }
            }

            function updateSummaryWithFee(patientName, selectedDoctor, dateInput, timeInput, modeInput, typeInput,
                fee) {
                const summaryHtml = `
                    <div class="flex justify-between">
                        <span class="text-ayur-brown-600">Patient:</span>
                        <span class="text-ayur-brown-800">${patientName}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ayur-brown-600">Doctor:</span>
                        <span class="text-ayur-brown-800">${selectedDoctor}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ayur-brown-600">Date & Time:</span>
                        <span class="text-ayur-brown-800">${dateInput.value ? new Date(dateInput.value).toLocaleDateString() : 'Select Date'}, ${timeInput ? timeInput.value : 'Select Time'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ayur-brown-600">Type:</span>
                        <span class="text-ayur-brown-800">${modeInput ? modeInput.value : 'Select Mode'} - ${typeInput ? typeInput.value : 'Select Type'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ayur-brown-600">Fee:</span>
                        <span class="font-medium text-ayur-brown-800">₹${fee}</span>
                    </div>
                `;

                const summaryContainer = document.querySelector(
                    '.bg-ayur-offwhite.rounded-md.p-4.border.border-gray-200');
                if (summaryContainer) {
                    const existingSummary = summaryContainer.querySelector('.space-y-2.text-sm');
                    if (existingSummary) {
                        existingSummary.innerHTML = summaryHtml;
                    }
                }
            }

            // Load doctor time slots
            function loadDoctorTimeSlots() {
                const doctorId = document.querySelector('select[name="doctor_id"]').value;
                const date = document.querySelector('input[name="appointment_date"]').value;

                console.log('=== LOADING TIME SLOTS ===');
                console.log('Doctor ID:', doctorId);
                console.log('Date:', date);

                if (!doctorId || !date) {
                    console.log('Missing doctor ID or date, returning');
                    return;
                }

                // Show loading state
                const timeSlotsContainer = document.getElementById('timeSlotsContainer');
                if (timeSlotsContainer) {
                    timeSlotsContainer.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading available time slots...</p>
                        </div>
                    `;
                }

                const url = `{{ route('appointment.doctor-time-slots') }}?doctor_id=${doctorId}&date=${date}`;
                console.log('Fetching from URL:', url);

                fetch(url)
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('=== TIME SLOTS RESPONSE ===');
                        console.log('Full response:', data);
                        console.log('Time slots:', data.time_slots);
                        console.log('Doctor schedule:', data.doctor_schedule);

                        if (data.error) {
                            console.log('Error in response:', data.error);
                            displayTimeSlotsError(data.error, data);
                        } else if (!data.time_slots || Object.keys(data.time_slots).length === 0) {
                            console.log('No time slots returned');
                            displayTimeSlotsError('No time slots available for the selected date', data);
                        } else {
                            displayTimeSlots(data.time_slots, data.doctor_schedule);
                        }
                    })
                    .catch(error => {
                        console.error('=== ERROR LOADING TIME SLOTS ===');
                        console.error('Error:', error);
                        console.error('Error message:', error.message);
                        displayTimeSlotsError('Failed to load time slots: ' + error.message);
                    });
            }

            // Test function for debugging
            function testTimeSlots() {
                console.log('=== TESTING TIME SLOTS ===');
                const doctorId = document.querySelector('select[name="doctor_id"]').value;
                const date = document.querySelector('input[name="appointment_date"]').value;

                if (!doctorId) {
                    alert('Please select a doctor first');
                    return;
                }

                if (!date) {
                    alert('Please select a date first');
                    return;
                }

                console.log('Testing with doctor ID:', doctorId, 'and date:', date);
                loadDoctorTimeSlots();
            }


            // Display time slots
            function displayTimeSlots(timeSlots, doctorSchedule) {
                const container = document.getElementById('timeSlotsContainer');
                const scheduleInfo = document.getElementById('doctorScheduleInfo');

                console.log('Displaying time slots:', timeSlots);
                console.log('Doctor schedule:', doctorSchedule);

                if (!timeSlots || Object.keys(timeSlots).length === 0) {
                    container.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <i class="fa-solid fa-calendar-times text-2xl mb-2"></i>
                            <p>No time slots available for selected date</p>
                            <p class="text-xs mt-1">Please check doctor's schedule or try a different date</p>
                        </div>
                    `;
                    if (scheduleInfo) {
                        scheduleInfo.textContent = 'No slots available';
                    }
                    return;
                }

                let slotsHtml = '';
                let hasAvailableSlots = false;

                // Morning slots
                if (timeSlots.morning && timeSlots.morning.length > 0) {
                    const availableMorningSlots = timeSlots.morning.filter(slot => slot.is_available);
                    if (availableMorningSlots.length > 0) {
                        hasAvailableSlots = true;
                        slotsHtml += `
                            <div class="mb-4">
                                <h6 class="text-sm font-medium text-ayur-brown-800 mb-2">Morning (${doctorSchedule.morning_from} - ${doctorSchedule.morning_to})</h6>
                                <div class="grid grid-cols-4 gap-2">
                                    ${timeSlots.morning.map(slot => `
                                                                                    <label class="time-slot-option ${slot.is_available ? 'bg-ayur-green-100 text-ayur-green-800 hover:bg-ayur-green-200 cursor-pointer' : 'bg-gray-200 text-gray-500 cursor-not-allowed'} rounded p-2 text-center text-xs border ${slot.is_available ? 'border-ayur-green-300' : 'border-gray-300'}">
                                                                                        <input type="radio" name="appointment_time" value="${slot.time}" class="hidden" ${slot.is_available ? '' : 'disabled'} required>
                                                                                        ${slot.formatted_time || slot.time}
                                                                                    </label>
                                                                                `).join('')}
                                </div>
                            </div>
                        `;
                    }
                }

                // Evening slots
                if (timeSlots.evening && timeSlots.evening.length > 0) {
                    const availableEveningSlots = timeSlots.evening.filter(slot => slot.is_available);
                    if (availableEveningSlots.length > 0) {
                        hasAvailableSlots = true;
                        slotsHtml += `
                            <div>
                                <h6 class="text-sm font-medium text-ayur-brown-800 mb-2">Evening (${doctorSchedule.evening_from} - ${doctorSchedule.evening_to})</h6>
                                <div class="grid grid-cols-4 gap-2">
                                    ${timeSlots.evening.map(slot => `
                                                                                    <label class="time-slot-option ${slot.is_available ? 'bg-ayur-green-100 text-ayur-green-800 hover:bg-ayur-green-200 cursor-pointer' : 'bg-gray-200 text-gray-500 cursor-not-allowed'} rounded p-2 text-center text-xs border ${slot.is_available ? 'border-ayur-green-300' : 'border-gray-300'}">
                                                                                        <input type="radio" name="appointment_time" value="${slot.time}" class="hidden" ${slot.is_available ? '' : 'disabled'} required>
                                                                                        ${slot.formatted_time || slot.time}
                                                                                    </label>
                                                                                `).join('')}
                                </div>
                            </div>
                        `;
                    }
                }

                if (!hasAvailableSlots) {
                    container.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <i class="fa-solid fa-calendar-times text-2xl mb-2"></i>
                            <p>All time slots are booked for this date</p>
                            <p class="text-xs mt-1">Please try a different date or time</p>
                        </div>
                    `;
                    if (scheduleInfo) {
                        scheduleInfo.textContent = 'All slots booked';
                    }
                    return;
                }

                container.innerHTML = slotsHtml;

                // Update schedule info
                const selectedDoctor = document.querySelector('select[name="doctor_id"] option:checked').text;
                const selectedDate = document.querySelector('input[name="appointment_date"]').value;
                if (scheduleInfo) {
                    scheduleInfo.textContent = `${selectedDoctor} • ${new Date(selectedDate).toLocaleDateString()}`;
                }

                // Add event listeners to new time slot radio buttons
                container.querySelectorAll('input[name="appointment_time"]').forEach(input => {
                    input.addEventListener('change', function() {
                        updateAppointmentSummary();
                        clearError('appointment_time');
                    });
                });
            }

            // Display time slots error
            function displayTimeSlotsError(message, data) {
                const container = document.getElementById('timeSlotsContainer');
                const scheduleInfo = document.getElementById('doctorScheduleInfo');

                let errorHtml = `
                    <div class="text-center text-red-500 py-8">
                        <i class="fa-solid fa-exclamation-triangle text-2xl mb-2"></i>
                        <p class="font-medium">${message}</p>
                `;

                // Add additional error details if available
                if (data) {
                    if (data.available_days) {
                        errorHtml += `
                            <div class="mt-3 text-sm">
                                <p class="text-gray-600">Available days: ${data.available_days.join(', ')}</p>
                                <p class="text-gray-600">Selected day: ${data.selected_day}</p>
                            </div>
                        `;
                    }

                    if (data.doctor_schedule) {
                        errorHtml += `
                            <div class="mt-2 text-xs text-gray-500">
                                <p>Morning: ${data.doctor_schedule.morning_from} - ${data.doctor_schedule.morning_to}</p>
                                <p>Evening: ${data.doctor_schedule.evening_from} - ${data.doctor_schedule.evening_to}</p>
                            </div>
                        `;
                    }
                }

                errorHtml += '</div>';

                container.innerHTML = errorHtml;

                if (scheduleInfo) {
                    scheduleInfo.textContent = 'Error loading slots';
                }

                if (data && data.error) {
                    console.error('Additional error details:', data.error);
                }
            }

            // Add event listeners for form changes
            document.querySelector('select[name="department_id"]').addEventListener('change', function() {
                clearError('department_id');
                // Remove server-side error styling
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
                // Real-time validation
                validateField('department_id');
            });

            document.querySelector('select[name="doctor_id"]').addEventListener('change', function() {
                clearError('doctor_id');
                // Remove server-side error styling
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
                // Real-time validation
                validateField('doctor_id');
                updateAppointmentSummary();
                console.log('Doctor changed, loading time slots...');
                loadDoctorTimeSlots();
            });

            document.querySelector('input[name="appointment_date"]').addEventListener('change', function() {
                clearError('appointment_date');
                // Remove server-side error styling
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
                // Real-time validation
                validateField('appointment_date');
                updateAppointmentSummary();
                console.log('Date changed, loading time slots...');
                loadDoctorTimeSlots();
            });

            document.querySelectorAll('input[name="mode"]').forEach(input => {
                input.addEventListener('change', function() {
                    clearError('mode');
                    // Real-time validation
                    validateField('mode');
                    updateAppointmentSummary();
                });
            });

            document.querySelectorAll('input[name="appointment_type"]').forEach(input => {
                input.addEventListener('change', function() {
                    clearError('appointment_type');
                    // Real-time validation
                    validateField('appointment_type');
                    updateAppointmentSummary();
                });
            });

            document.querySelector('textarea[name="chief_complaint"]').addEventListener('input', function() {
                clearError('chief_complaint');
                // Remove server-side error styling
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
                // Real-time validation
                validateField('chief_complaint');
            });

            // Handle patient search input for real-time validation
            patientSearch.addEventListener('input', function() {
                // Clear patient error when user starts typing
                clearError('patient_id');
                // Remove server-side error styling
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');

                const query = this.value.trim();

                // Clear previous timeout
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                // Set timeout to avoid too many requests
                searchTimeout = setTimeout(() => {
                    searchPatients(query);
                }, 300);
            });

            // Add real-time validation for time slot selection
            document.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'appointment_time') {
                    clearError('appointment_time');
                    validateField('appointment_time');
                    updateAppointmentSummary();

                    // Add visual feedback for selected time slot
                    document.querySelectorAll('.time-slot-option').forEach(option => {
                        option.classList.remove('selected');
                    });

                    if (e.target.checked) {
                        const selectedOption = e.target.closest('.time-slot-option');
                        if (selectedOption) {
                            selectedOption.classList.add('selected');
                        }
                    }
                }
            });

            // Handle appointment type radio button styling
            document.querySelectorAll('.appointment-type-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active styling from all options
                    document.querySelectorAll('.appointment-type-option').forEach(opt => {
                        opt.classList.remove('bg-amber-50', 'border-amber-200',
                            'active');
                        opt.classList.add('hover:bg-amber-50',
                            'hover:border-amber-200');
                    });

                    // Add active styling to clicked option
                    this.classList.add('bg-amber-50', 'border-amber-200', 'active');
                    this.classList.remove('hover:bg-amber-50', 'hover:border-amber-200');

                    // Trigger the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));

                    // Add visual feedback
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Handle mode radio button styling
            document.querySelectorAll('input[name="mode"]').forEach(input => {
                input.addEventListener('change', function() {
                    const labels = document.querySelectorAll('input[name="mode"]');
                    labels.forEach(label => {
                        const parent = label.parentElement;
                        if (label.checked) {
                            parent.classList.remove('border-gray-300',
                                'text-ayur-brown-700', 'bg-white');
                            parent.classList.add('border-transparent', 'text-white',
                                'bg-ayur-green-600', 'active');
                        } else {
                            parent.classList.remove('border-transparent', 'text-white',
                                'bg-ayur-green-600', 'active');
                            parent.classList.add('border-gray-300', 'text-ayur-brown-700',
                                'bg-white');
                        }
                    });
                });
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!patientSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Enhanced form validation on submit
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();

                if (validate()) {
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Appointment',
                        text: 'Are you sure you want to book this appointment?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, Book Appointment',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            this.submit();
                        }
                    });
                } else {
                    // Scroll to first error
                    const firstError = document.querySelector('.js-error[style*="display: block"]');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });

            // Clear form functionality
            document.querySelector('button[type="button"]').addEventListener('click', function() {
                // Reset form
                document.querySelector('form').reset();

                // Clear patient selection
                selectedPatientId.value = '';
                patientInfo.classList.add('hidden');
                noPatientSelected.classList.remove('hidden');
                patientSearch.value = '';

                // Reset time slots
                const timeSlotsContainer = document.getElementById('timeSlotsContainer');
                if (timeSlotsContainer) {
                    timeSlotsContainer.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <i class="fa-solid fa-clock text-2xl mb-2"></i>
                            <p>Please select a doctor and date to view available time slots</p>
                        </div>
                    `;
                }

                // Clear selected time slot styling
                document.querySelectorAll('.time-slot-option').forEach(option => {
                    option.classList.remove('selected');
                });

                // Reset schedule info
                const scheduleInfo = document.getElementById('doctorScheduleInfo');
                if (scheduleInfo) {
                    scheduleInfo.textContent = 'Select Doctor and Date';
                }

                // Reset appointment summary
                const summaryContainer = document.querySelector(
                    '.bg-ayur-offwhite.rounded-md.p-4.border.border-gray-200');
                if (summaryContainer) {
                    const existingSummary = summaryContainer.querySelector('.space-y-2.text-sm');
                    if (existingSummary) {
                        existingSummary.innerHTML = `
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Patient:</span>
                                <span class="text-ayur-brown-800">Select Patient</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Doctor:</span>
                                <span class="text-ayur-brown-800">Select Doctor</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Date & Time:</span>
                                <span class="text-ayur-brown-800">Select Date & Time</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Type:</span>
                                <span class="text-ayur-brown-800">Select Type</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ayur-brown-600">Fee:</span>
                                <span class="text-ayur-brown-800">₹0</span>
                            </div>
                        `;
                    }
                }

                // Clear all validation errors
                clearError('patient_id');
                clearError('department_id');
                clearError('doctor_id');
                clearError('appointment_date');
                clearError('appointment_time');
                clearError('mode');
                clearError('appointment_type');
                clearError('chief_complaint');

                // Reset submit button
                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Reset mode button styling
                document.querySelectorAll('input[name="mode"]').forEach(input => {
                    const parent = input.parentElement;
                    if (input.checked) {
                        parent.classList.remove('border-gray-300', 'text-ayur-brown-700',
                            'bg-white');
                        parent.classList.add('border-transparent', 'text-white',
                            'bg-ayur-green-600');
                    } else {
                        parent.classList.remove('border-transparent', 'text-white',
                            'bg-ayur-green-600');
                        parent.classList.add('border-gray-300', 'text-ayur-brown-700', 'bg-white');
                    }
                });

                // Reset appointment type styling
                document.querySelectorAll('.appointment-type-option').forEach(option => {
                    option.classList.remove('bg-amber-50', 'border-amber-200', 'active');
                    option.classList.add('hover:bg-amber-50', 'hover:border-amber-200');
                });

                // Set first option as active
                const firstTypeOption = document.querySelector('.appointment-type-option');
                if (firstTypeOption) {
                    firstTypeOption.classList.add('bg-amber-50', 'border-amber-200', 'active');
                    firstTypeOption.classList.remove('hover:bg-amber-50',
                        'hover:border-amber-200');
                }
            });

            // Function to handle server-side validation errors
            function handleServerSideErrors() {
                // Check if there are any server-side errors
                const serverErrors = document.querySelectorAll('.js-error + .text-red-600');

                serverErrors.forEach(errorElement => {
                    if (errorElement.textContent.trim()) {
                        // Get the field name from the error element's previous sibling
                        const errorId = errorElement.previousElementSibling.id;
                        const fieldName = errorId.replace('-error', '');

                        // Apply error styling to the corresponding input
                        const input = document.getElementById(fieldName);
                        if (input) {
                            input.classList.add('border-red-500');
                            input.classList.remove('border-gray-300');
                        }

                        // For patient search, also style the search input
                        if (fieldName === 'patient_id') {
                            const patientSearchInput = document.getElementById('patientSearch');
                            if (patientSearchInput) {
                                patientSearchInput.classList.add('border-red-500');
                                patientSearchInput.classList.remove('border-gray-300');
                            }
                        }
                    }
                });
            }

            // Initialize server-side error handling on page load
            handleServerSideErrors();

            // Test button for patient search
            const testPatientSearchBtn = document.getElementById('testPatientSearch');
            if (testPatientSearchBtn) {
                testPatientSearchBtn.addEventListener('click', function() {
                    console.log('Testing patient search...');
                    const testQueries = ['AYR', 'test', 'john', '123'];
                    const randomQuery = testQueries[Math.floor(Math.random() * testQueries.length)];

                    if (patientSearch) {
                        patientSearch.value = randomQuery;
                        patientSearch.dispatchEvent(new Event('input'));
                        console.log('Test query set:', randomQuery);
                    }
                });
            }

            // Test button for patient search route
            const testPatientRouteBtn = document.getElementById('testPatientRoute');
            if (testPatientRouteBtn) {
                testPatientRouteBtn.addEventListener('click', function() {
                    console.log('Testing patient search route...');

                    fetch('{{ route('appointment.test-search') }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => {
                            console.log('Test route response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Test route response:', data);
                            alert(
                                `Route test result: ${data.status}\nMessage: ${data.message}\nPatient count: ${data.patient_count || 'N/A'}`
                            );
                        })
                        .catch(error => {
                            console.error('Test route error:', error);
                            alert(`Route test failed: ${error.message}`);
                        });
                });
            }

            // Clear patient search button
            const clearPatientSearchBtn = document.getElementById('clearPatientSearch');
            if (clearPatientSearchBtn) {
                clearPatientSearchBtn.addEventListener('click', function() {
                    console.log('Clearing patient search...');
                    if (patientSearch) {
                        patientSearch.value = '';
                    }
                    if (selectedPatientId) {
                        selectedPatientId.value = '';
                    }
                    if (patientInfo) {
                        patientInfo.classList.add('hidden');
                    }
                    if (noPatientSelected) {
                        noPatientSelected.classList.remove('hidden');
                    }
                    if (searchResults) {
                        searchResults.classList.add('hidden');
                    }
                    clearError('patient_id');
                });
            }

            // Test button for time slots
            const testTimeSlotsBtn = document.getElementById('testTimeSlots');
            if (testTimeSlotsBtn) {
                testTimeSlotsBtn.addEventListener('click', function() {
                    console.log('Test button clicked');
                    // Set default values for testing
                    const doctorSelect = document.querySelector('select[name="doctor_id"]');
                    const dateInput = document.querySelector('input[name="appointment_date"]');

                    if (doctorSelect && dateInput) {
                        if (!doctorSelect.value) {
                            doctorSelect.value = doctorSelect.options[1]?.value || '';
                        }
                        if (!dateInput.value) {
                            const today = new Date();
                            const tomorrow = new Date(today);
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            dateInput.value = tomorrow.toISOString().split('T')[0];
                        }

                        console.log('Test values set:', {
                            doctor: doctorSelect.value,
                            date: dateInput.value
                        });
                        loadDoctorTimeSlots();
                    }
                });
            }

            // Safety check: ensure all required fields exist
            const requiredFields = ['patient_id', 'department_id', 'doctor_id', 'appointment_date',
                'chief_complaint'
            ];
            requiredFields.forEach(fieldName => {
                if (!fields[fieldName]) {
                    console.warn(`Required field not found: ${fieldName}`);
                }
            });

            // Check if we have all the required elements before proceeding
            const requiredElements = [
                'patientSearch',
                'searchResults',
                'selectedPatientId',
                'patientInfo',
                'noPatientSelected',
                'validationMessages'
            ];

            let missingElements = [];
            requiredElements.forEach(elementId => {
                if (!document.getElementById(elementId)) {
                    missingElements.push(elementId);
                }
            });

            if (missingElements.length > 0) {
                console.warn('Missing required elements:', missingElements);
                return; // Don't proceed if essential elements are missing
            }

            // Initialize patient data if patient_id is provided in URL
            @if ($selectedPatient)
                document.addEventListener('DOMContentLoaded', function() {
                    // Pre-fill patient data
                    const patient = @json($selectedPatient);

                    // Set hidden input value
                    document.getElementById('selectedPatientId').value = patient.id;

                    // Update patient info display
                    document.getElementById('patientName').textContent = patient.full_name;
                    document.getElementById('patientDetails').textContent =
                        `${patient.uhid} • ${patient.age}/${patient.gender.charAt(0).toUpperCase()}`;
                    document.getElementById('patientMobile').textContent = patient.mobile;
                    document.getElementById('patientPrakriti').textContent = patient.prakriti || 'N/A';
                    document.getElementById('patientAllergies').textContent = patient.allergies || 'None';

                    if (patient.photo_path) {
                        document.getElementById('patientPhoto').src = '{{ asset('') }}' + patient
                            .photo_path;
                    } else {
                        document.getElementById('patientPhoto').src =
                            '{{ asset('backend-assets/media/uploads/products/patient_20250825104721_ggatR2.jpg') }}';
                    }

                    // Show patient info, hide placeholder
                    document.getElementById('patientInfo').classList.remove('hidden');
                    document.getElementById('noPatientSelected').classList.add('hidden');

                    // Set search input value
                    document.getElementById('patientSearch').value = patient.full_name;

                    // Update appointment summary
                    if (typeof updateAppointmentSummary === 'function') {
                        updateAppointmentSummary();
                    }
                });
            @endif
        });
    </script>
@endsection
