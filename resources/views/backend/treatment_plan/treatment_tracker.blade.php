@extends('backend.layouts.master')

@section('styles')
    <style>
        .field-error {
            animation: slideDown 0.3s ease-out;
        }

        .section-error {
            animation: slideDown 0.3s ease-out;
        }

        .upload-error {
            animation: slideDown 0.3s ease-out;
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

        /* Ensure error messages have proper spacing */
        .field-error+* {
            margin-top: 0.5rem;
        }

        /* Style for required field indicators */
        .required-field::after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }


        /* Enhanced styling for pending status */
        .status-pending {
            background-color: #9ca3af !important;
            color: #ffffff !important;
            border: 2px solid #6b7280 !important;
        }

        .status-pending-body {
            background-color: #f9fafb !important;
            border-color: #d1d5db !important;
            border-width: 2px !important;
        }

        .status-pending-icon {
            background-color: #e5e7eb !important;
            color: #6b7280 !important;
            border: 1px solid #9ca3af !important;
        }

        /* Add subtle animation for pending days */
        .treatment-pending {
            opacity: 0.8;
            filter: grayscale(0.2);
            transition: all 0.3s ease;
        }

        .treatment-pending:hover {
            opacity: 1;
            filter: grayscale(0);
            transform: scale(1.05);
        }
    </style>
@endsection

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


        <!-- PATIENT INFO CARD -->
        <div id="patientInfoCard" class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <img class="h-16 w-16 rounded-full mr-4"
                        src="{{ $treatmentPlan->patient->photo_path ? asset($treatmentPlan->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-2.jpg' }}"
                        alt="Patient avatar">
                    <div>
                        <h3 class="text-xl font-semibold text-ayur-brown-800">{{ $treatmentPlan->patient->full_name }}</h3>
                        <div class="flex flex-wrap items-center text-sm text-ayur-brown-600 mt-1">
                            <span class="mr-3">UHID: {{ $treatmentPlan->patient->uhid }}</span>
                            <span
                                class="mr-3">{{ $treatmentPlan->patient->age }}/{{ $treatmentPlan->patient->gender }}</span>
                            <span class="mr-3">Prakriti: {{ $treatmentPlan->patient->prakriti ?? 'N/A' }}</span>
                            <span
                                class="px-2 py-0.5 bg-ayur-yellow-100 text-ayur-yellow-800 rounded-full text-xs font-medium">IPD
                                Patient</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('patients.show', $treatmentPlan->patient) }}"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-file-medical mr-2"></i> View Medical Record
                    </a>
                    <a href="{{ route('treatment-plan') }}"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- TREATMENT TABS -->
        <div id="treatmentTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('treatment-plan') }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Treatment Plan Creation
                    </a>
                    <a href="{{ route('treatment-plan.tracker', $treatmentPlan->id) }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Day-wise Tracker
                    </a>
                    <a href="{{ route('treatment-plan.feedback') }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Outcome &amp; Feedback
                    </a>

                </nav>
            </div>
        </div>

        <!-- TREATMENT PROGRESS SUMMARY -->
        <div id="treatmentProgressSummary" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Treatment Progress Summary</h3>
                <span class="px-3 py-1 bg-ayur-green-100 text-ayur-green-800 rounded-full text-sm font-medium">
                    Day {{ $completedDays + 1 }} of {{ $totalDays }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-ayur-brown-600">Treatment Type</p>
                            <p class="text-lg font-semibold text-ayur-brown-800">{{ $treatmentPlan->procedure_name }}</p>
                        </div>
                        <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                            <i class="fa-solid fa-spa"></i>
                        </div>
                    </div>
                    <p class="text-xs text-ayur-brown-600 mt-2">
                        <i class="fa-solid fa-clock mr-1"></i> {{ $treatmentPlan->duration_minutes ?? 'N/A' }} minutes per
                        session
                    </p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-ayur-brown-600">Primary Dosha Focus</p>
                            <p class="text-lg font-semibold text-ayur-brown-800">
                                {{ $treatmentPlan->patient->prakriti ?? 'N/A' }} Balancing</p>
                        </div>
                        <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                            <i class="fa-solid fa-wind"></i>
                        </div>
                    </div>
                    <p class="text-xs text-ayur-brown-600 mt-2">
                        <i class="fa-solid fa-leaf mr-1"></i>
                        {{ $treatmentPlan->dosha_report ?? 'Targeting overall wellness' }}
                    </p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-ayur-brown-600">Assigned Therapist</p>
                            <p class="text-lg font-semibold text-ayur-brown-800">
                                {{ $assignments->first()?->therapist?->full_name ?? 'Not Assigned' }}
                            </p>
                        </div>
                        <div class="rounded-full bg-ayur-brown-100 p-2 text-ayur-brown-600">
                            <i class="fa-solid fa-user-nurse"></i>
                        </div>
                    </div>
                    <p class="text-xs text-ayur-brown-600 mt-2">
                        <i class="fa-solid fa-certificate mr-1"></i>
                        {{ $assignments->first()?->therapist?->specialty ?? 'Panchkarma Specialist' }}
                    </p>
                </div>
            </div>

            <!-- ROOM ASSIGNMENT INFO -->
            {{-- @if ($assignments->first()?->room)
                <div class="mt-4 p-4 bg-ayur-offwhite rounded-lg">
                    <div class="flex items-center">
                        <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600 mr-3">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-ayur-brown-600">Assigned Room</p>
                            <p class="text-lg font-semibold text-ayur-brown-800">
                                {{ $assignments->first()->room->room_number }} -
                                {{ $assignments->first()->room->room_type }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-ayur-brown-600 mt-2">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        Room assigned for treatment sessions
                    </p>
                </div>
            @endif --}}

            <!-- PROGRESS BAR -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-ayur-brown-600">Treatment Progress</span>
                    <span class="text-sm font-medium text-ayur-green-600">{{ $progressPercentage }}% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-ayur-green-600 h-2.5 rounded-full transition-all duration-500"
                        style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>



            <!-- TREATMENT CALENDAR -->
            <div class="overflow-x-auto">
                <div class="flex space-x-2 py-2">
                    @foreach ($assignments as $index => $assignment)
                        @php
                            $dayNumber = $index + 1;
                            $isCompleted = $assignment->status == 2;
                            $isInProgress = $assignment->status == 1;
                            $isPending = $assignment->status == 0;
                            $isCancelled = $assignment->status == 3;
                            $isToday = \Carbon\Carbon::parse($assignment->assignment_date)->isToday();

                            $headerClass = $isCompleted
                                ? 'bg-ayur-green-600'
                                : ($isInProgress
                                    ? 'bg-ayur-yellow-600'
                                    : ($isCancelled
                                        ? 'bg-red-600'
                                        : ($isToday
                                            ? 'bg-blue-600'
                                            : ($isPending
                                                ? 'bg-gray-500'
                                                : 'bg-gray-400'))));
                            $bodyClass = $isCompleted
                                ? 'bg-white border border-ayur-green-200'
                                : ($isInProgress
                                    ? 'bg-ayur-yellow-50 border border-ayur-yellow-200'
                                    : ($isCancelled
                                        ? 'bg-red-50 border border-red-200'
                                        : ($isToday
                                            ? 'bg-blue-50 border border-blue-200'
                                            : ($isPending
                                                ? 'bg-gray-100 border border-gray-300'
                                                : 'bg-white border border-gray-200'))));
                            $iconClass = $isCompleted
                                ? 'bg-ayur-green-100 text-ayur-green-800'
                                : ($isInProgress
                                    ? 'bg-ayur-yellow-100 text-ayur-yellow-800'
                                    : ($isCancelled
                                        ? 'bg-red-100 text-red-800'
                                        : ($isToday
                                            ? 'bg-blue-100 text-blue-800'
                                            : ($isPending
                                                ? 'bg-gray-200 text-gray-600'
                                                : 'bg-gray-100 text-gray-500'))));
                            $icon = $isCompleted
                                ? 'fa-check'
                                : ($isInProgress
                                    ? 'fa-spinner'
                                    : ($isCancelled
                                        ? 'fa-times'
                                        : ($isToday
                                            ? 'fa-calendar-day'
                                            : ($isPending
                                                ? 'fa-clock'
                                                : 'fa-ellipsis'))));
                        @endphp
                        <div
                            class="flex-shrink-0 w-16 text-center cursor-pointer hover:scale-105 transition-transform duration-200 {{ $isPending ? 'treatment-pending' : '' }}">
                            <div
                                class="rounded-t-lg {{ $headerClass }} {{ $isPending ? 'status-pending' : '' }} text-white text-xs font-medium py-1 shadow-sm">
                                Day
                                {{ $dayNumber }}</div>
                            <div
                                class="rounded-b-lg {{ $bodyClass }} {{ $isPending ? 'status-pending-body' : '' }} py-2 shadow-sm">
                                <div class="text-xs text-ayur-brown-600 font-medium">
                                    {{ \Carbon\Carbon::parse($assignment->assignment_date)->format('M j') }}</div>
                                <div class="flex justify-center mt-1">
                                    <span
                                        class="h-6 w-6 flex items-center justify-center rounded-full {{ $iconClass }} {{ $isPending ? 'status-pending-icon' : '' }} shadow-sm">
                                        <i class="fa-solid {{ $icon }} text-xs"></i>
                                    </span>
                                </div>
                                @if ($isToday)
                                    <div class="text-xs text-blue-600 font-medium mt-1">Today</div>
                                @endif
                                @if ($assignment->room)
                                    <div class="text-xs {{ $isPending ? 'text-gray-400' : 'text-gray-500' }} mt-1 truncate"
                                        title="{{ $assignment->room->room_number }}">
                                        {{ $assignment->room->room_number }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- TODAY'S TREATMENT FORM -->
        <div id="todaysTreatmentForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">
                    @if ($todaysAssignment)
                        @if ($todaysAssignment->assignment_date->isToday())
                            Today's Treatment Details (Day {{ $completedDays + 1 }})
                        @else
                            Current Treatment Session (Day {{ $completedDays + 1 }})
                        @endif
                    @else
                        No Treatment Scheduled
                    @endif
                </h3>
                <div class="flex space-x-2">
                    @if ($todaysAssignment)
                        <span
                            class="px-3 py-1 {{ $todaysAssignment->status_badge_class }} rounded-full text-sm font-medium">
                            <i
                                class="fa-solid {{ $todaysAssignment->status == 0 ? 'fa-clock' : ($todaysAssignment->status == 1 ? 'fa-spinner' : ($todaysAssignment->status == 2 ? 'fa-check' : 'fa-times')) }} mr-1"></i>
                            {{ $todaysAssignment->status_text }}
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                            <i class="fa-solid fa-calendar-xmark mr-1"></i> No Session
                        </span>
                    @endif
                </div>
            </div>

            @if ($todaysAssignment)
                <form id="treatmentSessionForm" data-assignment-id="{{ $todaysAssignment->id ?? 'virtual' }}"
                    action="{{ route('treatment-tracker.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($todaysAssignment->id)
                        <input type="hidden" name="therapist_assignment_id" value="{{ $todaysAssignment->id }}">
                    @else
                        <input type="hidden" name="virtual_assignment" value="true">
                        <input type="hidden" name="treatment_plan_id" value="{{ $treatmentPlan->id }}">
                        <input type="hidden" name="day_number" value="1">
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT COLUMN -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 required-field">Session Date
                                    &amp;
                                    Time</label>
                                <div class="flex space-x-2 mt-1">
                                    <input type="date" name="session_date" required
                                        value="{{ $todaysAssignment->assignment_date->format('Y-m-d') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    <input type="time" name="session_time" required
                                        value="{{ $todaysAssignment->start_time->format('H:i') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 required-field">Room
                                    Assigned</label>
                                <select name="room_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    <option value="">Select Room</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ $todaysAssignment && $todaysAssignment->room_id == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} - {{ $room->room_type }}
                                            {{-- @if ($room->capacity > 1)
                                                (Capacity: {{ $room->capacity }})
                                            @endif --}}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($todaysAssignment && $todaysAssignment->room)
                                    <p class="text-xs text-ayur-brown-600 mt-1">
                                        <i class="fa-solid fa-door-open mr-1"></i>
                                        Currently assigned: {{ $todaysAssignment->room->room_number }} -
                                        {{ $todaysAssignment->room->room_type }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 required-field">Therapist
                                    Notes</label>
                                <textarea name="therapist_notes" rows="5" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="Enter therapist's observations and notes"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700">Patient Feedback</label>
                                <textarea name="patient_feedback" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="Enter patient's feedback about the treatment">{{ $todaysAssignment->special_instructions }}</textarea>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Materials Used</label>
                                <div id="materialsContainer" class="space-y-3">
                                    <!-- Materials from treatment plan requirements -->
                                    @if ($requiredMedicines->isNotEmpty())
                                        @foreach ($requiredMedicines as $medicine)
                                            <div
                                                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg material-item">
                                                <div class="flex items-center">
                                                    <i
                                                        class="fa-solid {{ $medicine->medicine_type_id == 3 ? 'fa-oil-can' : 'fa-leaf' }} text-ayur-brown-600 mr-2"></i>
                                                    <div>
                                                        <p class="text-sm font-medium text-ayur-brown-800">
                                                            {{ $medicine->name }}</p>
                                                        <p class="text-xs text-ayur-brown-600">
                                                            {{ $medicine->medicineType->name ?? 'Medicine' }}
                                                            @if ($medicine->strength_dosage)
                                                                - {{ $medicine->strength_dosage }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="number"
                                                        name="materials_used[{{ $medicine->id }}][quantity]"
                                                        value="0" min="0" step="0.1"
                                                        class="w-16 rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 text-sm border">
                                                    <span
                                                        class="ml-1 text-sm text-ayur-brown-600">{{ $medicine->measurement->name ?? 'unit' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4 text-gray-500">
                                            <i class="fa-solid fa-info-circle text-2xl mb-2"></i>
                                            <p class="text-sm">No materials specified in treatment plan</p>
                                        </div>
                                    @endif

                                    <button type="button" id="addMaterialBtn"
                                        class="flex items-center text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                                        <i class="fa-solid fa-plus mr-1"></i> Add More Materials
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Vital Signs (Post
                                    Treatment)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-ayur-brown-600">Blood Pressure</label>
                                        <input type="text" name="vital_signs[bp]" value=""
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-ayur-brown-600">Pulse Rate</label>
                                        <input type="text" name="vital_signs[pulse]" value=""
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-ayur-brown-600">Temperature</label>
                                        <input type="text" name="vital_signs[temperature]" value=""
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-ayur-brown-600">Weight</label>
                                        <input type="text" name="vital_signs[weight]" value=""
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700">Upload Images
                                    (Optional)</label>
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <i class="fa-solid fa-cloud-arrow-up mx-auto text-ayur-brown-400 text-2xl"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-ayur-green-500">
                                                <span>Upload files</span>
                                                <input id="tracker-images" name="tracker_images[]" type="file"
                                                    multiple accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        @if ($todaysAssignment->status == 0)
                            <button type="button" id="startSessionBtn"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fa-solid fa-play mr-2"></i> Start Session
                            </button>
                        @endif

                        <button type="button" id="saveDraftBtn"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-save mr-2"></i> Save as Draft
                        </button>

                        @if ($todaysAssignment->status == 1)
                            <button type="button" id="completeSessionBtn"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                <i class="fa-solid fa-check mr-2"></i> Complete Session
                            </button>
                        @endif
                    </div>
                </form>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-calendar-xmark text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-600">No treatment session is scheduled for today.</p>
                    <p class="text-sm text-gray-500 mt-2">Please check the treatment calendar or contact the administrator.
                    </p>
                </div>
            @endif
        </div>

        <!-- PREVIOUS SESSIONS -->
        <div id="previousSessions" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Previous Sessions</h3>
                <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                    View All Sessions <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>

            <div class="space-y-4">
                @forelse($previousSessions as $index => $session)
                    <div
                        class="border border-gray-200 rounded-lg p-4 hover:bg-ayur-offwhite transition-colors cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start">
                                <div class="rounded-full {{ $session->status_badge_class }} p-2 mr-3">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h4 class="text-md font-medium text-ayur-brown-800">
                                        Day {{ $index + 1 }} - {{ $treatmentPlan->procedure_name }}
                                    </h4>
                                    <p class="text-sm text-ayur-brown-600 mt-1">
                                        {{ $session->assignment_date->format('M j, Y') }} |
                                        {{ $session->formatted_time_slot }}
                                    </p>
                                    <div class="mt-2 text-sm space-y-1">
                                        <div>
                                            <span class="text-ayur-brown-600">Therapist:</span>
                                            <span
                                                class="text-ayur-brown-800">{{ $session->therapist->full_name ?? 'N/A' }}</span>
                                        </div>
                                        @if ($session->room)
                                            <div>
                                                <span class="text-ayur-brown-600">Room:</span>
                                                <span class="text-ayur-brown-800">{{ $session->room->room_number }} -
                                                    {{ $session->room->room_type }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button class="text-ayur-green-600 hover:text-ayur-green-700"
                                onclick="viewSessionDetails({{ $session->id }})">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="mt-3 pl-10">
                            <p class="text-sm text-ayur-brown-700">
                                <span class="font-medium">Materials:</span> {{ $session->materials_used_text }}
                            </p>
                            <p class="text-sm text-ayur-brown-700 mt-1">
                                <span class="font-medium">Notes:</span> {{ $session->treatment_details_text }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fa-solid fa-history text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No previous sessions found.</p>
                        <p class="text-sm text-gray-500 mt-2">Completed sessions will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <!-- Define routes for JavaScript -->
    <script>
        window.treatmentTrackerRoutes = {
            store: '{{ route('treatment-tracker.store') }}',
            details: '{{ url('/admin/treatment-tracker') }}',
            deleteImage: '{{ url('/admin/treatment-tracker') }}'
        };
    </script>

    <script src="{{ asset('backend-assets/js/validation/treatment_plan/treatment-tracker.js') }}"></script>
@endsection
