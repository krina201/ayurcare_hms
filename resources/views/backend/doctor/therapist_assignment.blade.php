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
        <div id="therapistTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('doctor.therapist-assignment') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor.therapist-assignment') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Therapist Assignment
                    </a>
                    <a href="{{ route('doctor.therapist-schedule') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor.therapist-schedule') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Therapist Schedule

                    </a>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Treatment Rooms
                    </button>
                </nav>
            </div>
        </div>

        <!-- ASSIGNMENT PANEL -->
        <div id="assignmentPanel" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- TREATMENT QUEUE -->
            <div id="treatmentQueue" class="bg-white rounded-lg shadow-md p-5 lg:col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Pending Treatments</h3>
                    <div class="flex items-center">
                        <span
                            class="bg-ayur-green-100 text-ayur-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Today</span>
                        <button class="text-ayur-brown-600 ml-2 hover:text-ayur-brown-800">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                        class="bg-gray-50 border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 block w-full pl-10 p-2.5"
                        placeholder="Search patients...">
                </div>

                <div class="mt-4 space-y-3 overflow-y-auto max-h-[520px]" id="pendingTreatmentsList">
                    @forelse($pendingTreatments as $treatment)
                        <div data-treatment-id="{{ $treatment->id }}"
                            class="pending-treatment-item p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:shadow-md transition-all hover:border-ayur-green-300">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full mr-3"
                                        src="{{ $treatment->patient->photo_path ? asset($treatment->patient->photo_path) : '' }}"
                                        alt="Patient">
                                    <div>
                                        <p class="font-medium text-ayur-brown-800">{{ $treatment->patient->full_name }}</p>
                                        <p class="text-xs text-ayur-brown-600">UHID:
                                            {{ $treatment->patient->uhid ?? 'N/A' }} •
                                            {{ $treatment->patient->age ?? 'N/A' }}/{{ substr($treatment->patient->gender ?? 'U', 0, 1) }}
                                        </p>
                                    </div>
                                </div>
                                @if ($treatment->priority ?? false)
                                    <span
                                        class="bg-ayur-yellow-100 text-ayur-yellow-800 text-xs px-2 py-1 rounded-full">Priority</span>
                                @endif
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-ayur-brown-700"><span class="font-medium">Treatment:</span>
                                    {{ $treatment->procedure_name }}</p>
                                <p class="text-sm text-ayur-brown-700"><span class="font-medium">Category:</span>
                                    {{ $treatment->treatmentCategory->name ?? 'N/A' }}</p>
                                <p class="text-sm text-ayur-brown-700"><span class="font-medium">Duration:</span>
                                    {{ $treatment->duration_text }}</p>
                                @if ($treatment->patient->prakriti)
                                    <p class="text-sm text-ayur-brown-700"><span class="font-medium">Dosha:</span>
                                        {{ $treatment->patient->prakriti }}</p>
                                @endif
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <p class="text-xs text-ayur-brown-600"><i class="fa-regular fa-clock mr-1"></i>
                                    Created: {{ $treatment->created_at->format('M d, H:i') }}</p>
                                <button class="assign-treatment-btn text-ayur-green-600 hover:text-ayur-green-700 text-sm"
                                    data-treatment-id="{{ $treatment->id }}">
                                    <i class="fa-solid fa-arrow-right-long"></i> Assign
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fa-solid fa-clipboard-list text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No pending treatments found</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ASSIGNMENT FORM -->
            <div id="assignmentForm" class="bg-white rounded-lg shadow-md p-5 lg:col-span-2">
                <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Assign Therapist</h3>

                <!-- PATIENT DETAILS SECTION -->
                <div id="selectedPatientDetails" class="bg-ayur-yellow-50 border border-ayur-yellow-200 rounded-lg p-4 mb-6"
                    style="display: none;">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-ayur-brown-800">Patient Details</h4>
                            <div class="flex items-center mt-2">
                                <img class="h-24 w-24 rounded-full object-cover border-4 border-ayur-green-200"
                                    src="{{ asset('backend-assets/media/uploads/download (3).png') }}"
                                    onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                    alt="Patient avatar" id="patientPhoto">
                                <div>
                                    <p class="font-medium text-ayur-brown-800" id="patientName">Anjali Mehta</p>
                                    <p class="text-sm text-ayur-brown-600" id="patientUhid">UHID: AYR-2025-0032 • 34/F</p>
                                    <p class="text-sm text-ayur-brown-600" id="patientDosha">Dosha: Vata-Pitta</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-ayur-brown-800">Treatment Details</h4>
                            <p class="text-sm text-ayur-brown-700 mt-2"><span class="font-medium">Treatment:</span>
                                <span id="treatmentProcedure">Abhyanga + Shirodhara</span>
                            </p>
                            <p class="text-sm text-ayur-brown-700"><span class="font-medium">Duration:</span> <span
                                    id="treatmentDuration">90</span>
                                mins</p>
                            <p class="text-sm text-ayur-brown-700"><span class="font-medium">Scheduled:</span>
                                <span id="treatmentScheduled">Today, 10:30 AM</span>
                            </p>
                        </div>
                        <div>
                            <h4 class="font-medium text-ayur-brown-800">Medical Notes</h4>
                            <p class="text-sm text-ayur-brown-700 mt-2" id="patientAllergies">Chronic headache,
                                stress-induced insomnia.
                                Avoid pressure on right shoulder.</p>
                            <p class="text-sm text-ayur-green-600 mt-2">
                                <i class="fa-solid fa-file-medical"></i> View Full Medical History
                            </p>
                        </div>
                    </div>
                </div>

                <div id="noPatientSelected" class="text-center py-8">
                    <i class="fa-solid fa-user-plus text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Select a patient from the pending treatments to assign a therapist</p>
                </div>

                <!-- ASSIGNMENT FORM FIELDS -->
                <form id="therapistAssignmentForm" style="display: none;">
                    <input type="hidden" id="selectedTreatmentId" name="treatment_plan_id">
                    <input type="hidden" name="assignment_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="treatment_details" id="treatmentDetails">
                    <input type="hidden" name="materials_required" id="materialsRequiredInput">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                Select Therapist
                                <span id="treatmentCategoryFilter" class="text-xs text-ayur-green-600 ml-2"></span>
                            </label>

                            <!-- Loading State -->
                            <div id="therapistsLoading" class="text-center py-4 hidden">
                                <i class="fa-solid fa-spinner fa-spin text-ayur-green-600 text-lg"></i>
                                <p class="text-sm text-ayur-brown-600 mt-2">Loading matching therapists...</p>
                            </div>

                            <!-- Therapists List -->
                            <div id="therapistsList" class="space-y-3">
                                @foreach ($therapists as $index => $therapist)
                                    @php
                                        // Determine availability status and styling
                                        $isFirst = $index === 0;
                                        $isSelected = $isFirst;
                                        $availabilityStatus =
                                            $index === 0
                                                ? 'Available'
                                                : ($index === 1
                                                    ? 'Available'
                                                    : ($index === 2
                                                        ? 'Busy until 11:30'
                                                        : 'Unavailable today'));
                                        $availabilityColor =
                                            $index === 0
                                                ? 'text-ayur-green-700'
                                                : ($index === 1
                                                    ? 'text-ayur-green-700'
                                                    : ($index === 2
                                                        ? 'text-amber-600'
                                                        : 'text-red-600'));
                                        $availabilityIcon = $index === 2 ? 'fa-clock' : 'fa-circle';
                                        $specialty =
                                            $index === 0
                                                ? 'Vata-Pitta Specialist'
                                                : ($index === 1
                                                    ? 'Shirodhara Expert'
                                                    : ($index === 2
                                                        ? 'Abhyanga Expert'
                                                        : 'Pitta Specialist'));
                                        $rating =
                                            $index === 0
                                                ? '4.9'
                                                : ($index === 1
                                                    ? '4.7'
                                                    : ($index === 2
                                                        ? '4.8'
                                                        : '4.6'));
                                        $experience =
                                            $index === 0 ? '8+' : ($index === 1 ? '5+' : ($index === 2 ? '7+' : '4+'));

                                        $cardClasses = $isSelected
                                            ? 'p-3 border border-ayur-green-300 bg-ayur-green-50 rounded-lg flex items-center relative cursor-pointer'
                                            : 'p-3 border border-gray-200 rounded-lg flex items-center relative cursor-pointer hover:border-ayur-green-300 hover:bg-ayur-green-50';
                                        $radioClasses = $isSelected
                                            ? 'w-5 h-5 inline-block mr-3 rounded-full border border-ayur-green-500 flex-shrink-0 bg-white flex items-center justify-center'
                                            : 'w-5 h-5 inline-block mr-3 rounded-full border border-gray-300 flex-shrink-0 bg-white flex items-center justify-center';
                                        $dotClasses = $isSelected
                                            ? 'w-2.5 h-2.5 rounded-full bg-ayur-green-500'
                                            : 'w-2.5 h-2.5 rounded-full bg-ayur-green-500 hidden';
                                    @endphp

                                    <div class="{{ $cardClasses }}" data-therapist-id="{{ $therapist->id }}">
                                        <input type="radio" name="therapist_id" value="{{ $therapist->id }}"
                                            class="absolute opacity-0" {{ $isSelected ? 'checked' : '' }}>
                                        <span class="{{ $radioClasses }}">
                                            <span class="{{ $dotClasses }}"></span>
                                        </span>
                                        <div class="flex items-center flex-1">
                                            <img class="h-10 w-10 rounded-full mr-3"
                                                src="{{ $therapist->photo ? asset('backend-assets/media/uploads/doctors/' . $therapist->photo) : asset('backend-assets/media/uploads/download (3).png') }}"
                                                onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                                alt="Therapist">
                                            <div>
                                                <p class="font-medium text-ayur-brown-800">{{ $therapist->full_name }}</p>
                                                <div class="flex items-center">
                                                    <span class="text-xs text-ayur-green-700 mr-2">
                                                        <i class="fa-solid fa-star"></i> {{ $rating }}
                                                    </span>
                                                    <span class="text-xs text-ayur-brown-600">
                                                        {{ $experience }}+ years exp.
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right text-xs">
                                            <p class="{{ $availabilityColor }}">
                                                <i class="fa-solid {{ $availabilityIcon }} text-xs"></i>
                                                {{ $availabilityStatus }}
                                            </p>
                                            <p class="text-ayur-brown-600 mt-1">{{ $specialty }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- No Therapists Found -->
                            <div id="noTherapistsFound" class="text-center py-8 hidden">
                                <i class="fa-solid fa-user-times text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500 mb-2">No therapists found for this treatment category</p>
                                <p class="text-xs text-gray-400">Try selecting a different treatment or contact the
                                    administrator</p>
                            </div>

                            <!-- Therapist Selection Error -->
                            <div id="therapist_id-error" class="js-error text-red-500 text-sm mt-1"
                                style="display: none;"></div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Select Treatment
                                    Room</label>
                                <select name="room_id" id="room_id"
                                    class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 block w-full p-2.5">
                                    <option value="">Select a room...</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->room_number }} -
                                            {{ $room->room_type }}</option>
                                    @endforeach
                                </select>
                                <!-- Room Selection Error -->
                                <div id="room_id-error" class="js-error text-red-500 text-sm mt-1"
                                    style="display: none;"></div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Select Treatment
                                    Time</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-ayur-brown-600 mb-1">Start Time</label>
                                        <input type="time" name="start_time" value="10:30"
                                            class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-ayur-brown-600 mb-1">End Time</label>
                                        <input type="time" name="end_time" value="12:00"
                                            class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 block w-full p-2.5">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Materials
                                    Required</label>
                                <div id="materialsRequired" class="p-3 bg-ayur-offwhite rounded-lg">
                                    <div class="text-center py-4 text-gray-500">
                                        <i class="fa-solid fa-box text-2xl mb-2"></i>
                                        <p class="text-sm">Select a treatment to view required materials</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Special
                                    Instructions</label>
                                <textarea name="special_instructions" rows="3"
                                    class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 block w-full p-2.5"
                                    placeholder="Add any special instructions for the therapist..."> </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelAssignmentBtn"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            Cancel
                        </button>
                        <button type="submit" id="confirmAssignmentBtn"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <span class="btn-text">Confirm Assignment</span>
                            <i class="fa-solid fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ACTIVE TREATMENTS -->
        <div id="activeTreatments" class="bg-white rounded-lg shadow-md p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Active Treatments</h3>
                {{-- <div class="flex space-x-2">
                    <button
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                    <button
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                        <i class="fa-solid fa-print mr-1"></i> Print Schedule
                    </button>
                </div> --}}
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="example">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Treatment</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Therapist</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Room</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Time</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activeAssignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full"
                                            src="{{ $assignment->patient->photo_path ? asset($assignment->patient->photo_path) : '' }}"
                                            alt="">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $assignment->patient->full_name }}</div>
                                            <div class="text-xs text-ayur-brown-600">
                                                {{ $assignment->patient->uhid ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $assignment->treatmentPlan->procedure_name ?? $assignment->treatment_details }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-7 w-7 rounded-full"
                                            src="{{ $assignment->therapist->photo ? asset('backend-assets/media/uploads/doctors/' . $assignment->therapist->photo) : asset('backend-assets/media/uploads/download (3).png') }}"
                                            onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                            alt="Therapist">
                                        <div class="ml-2 text-sm text-ayur-brown-700">
                                            {{ $assignment->therapist->full_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    Room {{ $assignment->room->room_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $assignment->formatted_time_slot }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->status_badge_class }}">
                                        {{ $assignment->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="View Details"><i class="fa-solid fa-eye"></i></button>
                                    @if ($assignment->canComplete())
                                        <button class="text-blue-600 hover:text-blue-900 mr-3 complete-assignment-btn"
                                            data-assignment-id="{{ $assignment->id }}" title="Complete"><i
                                                class="fa-solid fa-check"></i></button>
                                    @endif
                                    @if ($assignment->canCancel())
                                        <button class="text-red-600 hover:text-red-900 cancel-assignment-btn"
                                            data-assignment-id="{{ $assignment->id }}" title="Cancel"><i
                                                class="fa-solid fa-times"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No active assignments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- THERAPIST AVAILABILITY -->
        <div id="therapistAvailability" class="bg-white rounded-lg shadow-md p-5">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Therapist Availability</h3>
                <div>
                    <select
                        class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 p-2">
                        <option>Today</option>
                        <option>Tomorrow</option>
                        <option>This Week</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <img class="h-10 w-10 rounded-full mr-3" src="" alt="Therapist">
                        <div>
                            <p class="font-medium text-ayur-brown-800">Meena Kumari</p>
                            <p class="text-xs text-ayur-brown-600">Vata-Pitta Specialist</p>
                        </div>
                        <span
                            class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">09:00 - 10:30</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">10:30 - 12:00</span>
                            <span class="text-amber-600">Reserved</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">12:00 - 13:00</span>
                            <span class="text-ayur-brown-600">Lunch</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">13:00 - 14:30</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">14:30 - 16:00</span>
                            <span class="text-green-600">Free</span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <img class="h-10 w-10 rounded-full mr-3" src="" alt="Therapist">
                        <div>
                            <p class="font-medium text-ayur-brown-800">Priya Sharma</p>
                            <p class="text-xs text-ayur-brown-600">Shirodhara Expert</p>
                        </div>
                        <span
                            class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">09:00 - 10:45</span>
                            <span class="text-red-600">Busy</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">10:45 - 12:15</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">12:15 - 13:15</span>
                            <span class="text-ayur-brown-600">Lunch</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">13:15 - 14:45</span>
                            <span class="text-amber-600">Reserved</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">14:45 - 16:15</span>
                            <span class="text-green-600">Free</span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <img class="h-10 w-10 rounded-full mr-3" src="" alt="Therapist">
                        <div>
                            <p class="font-medium text-ayur-brown-800">Rajesh Kumar</p>
                            <p class="text-xs text-ayur-brown-600">Abhyanga Expert</p>
                        </div>
                        <span
                            class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Busy
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">09:00 - 10:30</span>
                            <span class="text-red-600">Busy</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">10:30 - 12:00</span>
                            <span class="text-amber-600">Reserved</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">12:00 - 13:00</span>
                            <span class="text-ayur-brown-600">Lunch</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">13:00 - 14:30</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">14:30 - 16:00</span>
                            <span class="text-amber-600">Reserved</span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <img class="h-10 w-10 rounded-full mr-3" src="" alt="Therapist">
                        <div>
                            <p class="font-medium text-ayur-brown-800">Deepak Singh</p>
                            <p class="text-xs text-ayur-brown-600">Udvartana Expert</p>
                        </div>
                        <span
                            class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            Reserved
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">09:00 - 10:30</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">10:30 - 12:00</span>
                            <span class="text-red-600">Busy</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">12:00 - 13:00</span>
                            <span class="text-ayur-brown-600">Lunch</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">13:00 - 14:30</span>
                            <span class="text-amber-600">Reserved</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-ayur-brown-600">14:30 - 16:00</span>
                            <span class="text-green-600">Free</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedTreatmentData = null;

            // Handle treatment selection
            document.querySelectorAll('.assign-treatment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const treatmentId = this.getAttribute('data-treatment-id');
                    const treatmentItem = document.querySelector(
                        `[data-treatment-id="${treatmentId}"]`);

                    // Remove previous selection
                    document.querySelectorAll('.pending-treatment-item').forEach(item => {
                        item.classList.remove('bg-ayur-yellow-50',
                            'border-ayur-yellow-200');
                        item.classList.add('bg-white', 'border-gray-200');
                    });

                    // Highlight selected treatment
                    treatmentItem.classList.remove('bg-white', 'border-gray-200');
                    treatmentItem.classList.add('bg-ayur-yellow-50', 'border-ayur-yellow-200');

                    // Fetch detailed patient and treatment information
                    fetchDetailedAssignmentData(treatmentId);
                });
            });

            // Function to fetch detailed assignment data
            function fetchDetailedAssignmentData(treatmentId) {
                fetch(`{{ route('therapist-assignment.details') }}?treatment_id=${treatmentId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.treatment && data.patient) {
                            updatePatientProfile(data.patient, data.treatment);
                            selectedTreatmentData = {
                                id: treatmentId,
                                patient: data.patient,
                                treatment: data.treatment
                            };

                            // Set hidden form field
                            document.getElementById('selectedTreatmentId').value = treatmentId;
                            document.getElementById('treatmentDetails').value = data.treatment.procedure_name;

                            // Show assignment form and hide no selection message
                            document.getElementById('selectedPatientDetails').style.display = 'block';
                            document.getElementById('therapistAssignmentForm').style.display = 'block';
                            document.getElementById('noPatientSelected').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching assignment details:', error);
                        // Fallback to basic data extraction
                        extractBasicTreatmentData(treatmentId);
                    });
            }

            // Function to update patient profile with detailed information
            function updatePatientProfile(patient, treatment) {
                // Update patient photo
                const patientPhoto = document.getElementById('patientPhoto');

                if (patient.photo_path) {
                    const photoPath = `{{ asset('') }}${patient.photo_path}`;
                    patientPhoto.src = photoPath;
                    // Add error handling for image loading
                    patientPhoto.onerror = function() {
                        this.src = '{{ asset('backend-assets/media/uploads/download (3).png') }}';
                    };
                } else {
                    // Set default photo if no photo
                    patientPhoto.src = '{{ asset('backend-assets/media/uploads/download (3).png') }}';
                }

                // Update patient basic information for the new design
                document.getElementById('patientName').textContent = patient.full_name;
                document.getElementById('patientUhid').textContent =
                    `UHID: ${patient.uhid || 'N/A'} • ${patient.age || 'N/A'}/${patient.gender || 'N/A'}`;
                document.getElementById('patientDosha').textContent = `Dosha: ${patient.doshas || 'N/A'}`;

                // Update treatment information
                document.getElementById('treatmentProcedure').textContent = treatment.procedure_name;
                document.getElementById('treatmentDuration').textContent = treatment.duration_text;
                document.getElementById('treatmentScheduled').textContent =
                    `Today, ${new Date(treatment.created_at).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}`;

                // Update medical notes
                document.getElementById('patientAllergies').textContent = patient.allergies || 'No known allergies';

                // Update materials required from treatment plan
                updateMaterialsRequired(treatment);

                // Show the patient details and assignment form
                document.getElementById('selectedPatientDetails').style.display = 'block';
                document.getElementById('therapistAssignmentForm').style.display = 'block';
                document.getElementById('noPatientSelected').style.display = 'none';

                // Set hidden form field
                document.getElementById('selectedTreatmentId').value = treatment.id;

                // Filter therapists based on treatment category
                if (treatment.treatment_category && treatment.treatment_category.id) {
                    filterTherapistsByCategory(treatment.treatment_category.id, treatment.treatment_category.name);
                }
            }

            // Function to update materials required from treatment plan
            function updateMaterialsRequired(treatment) {
                const materialsContainer = document.getElementById('materialsRequired');

                if (treatment.oils && treatment.oils.length > 0) {
                    let materialsHtml = '';
                    let materialsList = [];

                    treatment.oils.forEach((oil, index) => {
                        const oilName = oil.name || oil;
                        materialsList.push(oilName);
                        materialsHtml += `
                            <div class="flex items-center justify-between ${index < treatment.oils.length - 1 ? 'mb-2' : ''}">
                                <span class="text-sm text-ayur-brown-700">${oilName}</span>
                                <span class="text-xs text-ayur-green-700">In Stock</span>
                            </div>
                        `;
                    });

                    // Add common equipment
                    materialsList.push('Treatment Equipment', 'Cotton Towels (6)');
                    materialsHtml += `
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-ayur-brown-700">Treatment Equipment</span>
                            <span class="text-xs text-ayur-green-700">Available</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-ayur-brown-700">Cotton Towels (6)</span>
                            <span class="text-xs text-ayur-green-700">Available</span>
                        </div>
                    `;

                    materialsContainer.innerHTML = materialsHtml;
                    document.getElementById('materialsRequiredInput').value = materialsList.join(', ');
                } else {
                    // Fallback to default materials if no oils specified
                    const defaultMaterials = ['Sesame Oil (Til Taila)', 'Treatment Equipment', 'Cotton Towels (6)'];
                    materialsContainer.innerHTML = `
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-ayur-brown-700">Sesame Oil (Til Taila)</span>
                            <span class="text-xs text-ayur-green-700">In Stock</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-ayur-brown-700">Treatment Equipment</span>
                            <span class="text-xs text-ayur-green-700">Available</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-ayur-brown-700">Cotton Towels (6)</span>
                            <span class="text-xs text-ayur-green-700">Available</span>
                        </div>
                    `;
                    document.getElementById('materialsRequiredInput').value = defaultMaterials.join(', ');
                }
            }

            // Function to filter therapists by treatment category
            function filterTherapistsByCategory(categoryId, categoryName) {
                // Show loading state
                document.getElementById('therapistsLoading').classList.remove('hidden');
                document.getElementById('therapistsList').classList.add('hidden');
                document.getElementById('noTherapistsFound').classList.add('hidden');

                // Update filter label
                document.getElementById('treatmentCategoryFilter').textContent = `(Filtered by: ${categoryName})`;

                // Fetch filtered therapists
                fetch(`{{ route('therapist-assignment.therapists-by-category') }}?treatment_category_id=${categoryId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading state
                        document.getElementById('therapistsLoading').classList.add('hidden');

                        if (data.success && data.therapists.length > 0) {
                            // Update therapists list
                            updateTherapistsList(data.therapists);
                            document.getElementById('therapistsList').classList.remove('hidden');
                            document.getElementById('noTherapistsFound').classList.add('hidden');
                        } else {
                            // Show no therapists found
                            document.getElementById('therapistsList').classList.add('hidden');
                            document.getElementById('noTherapistsFound').classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error filtering therapists:', error);
                        // Hide loading state
                        document.getElementById('therapistsLoading').classList.add('hidden');
                        // Show original therapists list as fallback
                        document.getElementById('therapistsList').classList.remove('hidden');
                        document.getElementById('noTherapistsFound').classList.add('hidden');
                    });
            }

            // Function to update therapists list with filtered data
            function updateTherapistsList(therapists) {
                const therapistsList = document.getElementById('therapistsList');

                // Clear existing therapists
                therapistsList.innerHTML = '';

                // Add filtered therapists with simplified HTML
                therapists.forEach(therapist => {
                    const therapistCard = document.createElement('div');
                    therapistCard.className =
                        'therapist-option p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-ayur-green-300 hover:bg-ayur-green-50 transition-all';
                    therapistCard.setAttribute('data-therapist-id', therapist.id);

                    therapistCard.innerHTML = `
                        <input type="radio" name="therapist_id" value="${therapist.id}" class="absolute opacity-0">
                        
                        <div class="flex items-center mb-3">
                            <span class="w-5 h-5 inline-block mr-3 rounded-full border border-gray-300 flex-shrink-0 bg-white flex items-center justify-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-ayur-green-500 hidden"></span>
                            </span>
                            <img class="h-12 w-12 rounded-full mr-3" 
                                 src="${therapist.photo ? '{{ asset('backend-assets/media/uploads/doctors/') }}' + therapist.photo : '{{ asset('backend-assets/media/uploads/download (3).png') }}'}" 
                                 onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                 alt="Therapist">
                            <div class="flex-1">
                                <p class="font-semibold text-ayur-brown-800">${therapist.full_name}</p>
                                <p class="text-sm text-ayur-brown-600">${therapist.qualification || 'Therapist'}</p>
                                <div class="flex items-center mt-1">
                                    <span class="text-xs text-ayur-brown-600 mr-3">
                                        <i class="fa-solid fa-clock mr-1"></i>${therapist.experience}+ years exp.
                                    </span>
                                    <span class="text-xs text-ayur-green-700">
                                        <i class="fa-solid fa-circle text-xs mr-1"></i>Available
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                       
                    `;

                    therapistsList.appendChild(therapistCard);
                });

                // Re-attach event listeners
                attachTherapistSelectionListeners();
            }

            // Function to attach therapist selection listeners
            function attachTherapistSelectionListeners() {
                document.querySelectorAll('[data-therapist-id]').forEach(option => {
                    option.addEventListener('click', function() {
                        // Remove previous selection
                        document.querySelectorAll('[data-therapist-id]').forEach(opt => {
                            opt.classList.remove('border-ayur-green-300',
                                'bg-ayur-green-50');
                            opt.classList.add('border-gray-200');
                            opt.querySelector('input[type="radio"]').checked = false;

                            // Update radio button styling
                            const radioSpan = opt.querySelector('span');
                            radioSpan.classList.remove('border-ayur-green-500');
                            radioSpan.classList.add('border-gray-300');

                            // Hide the dot
                            const dot = radioSpan.querySelector('span');
                            if (dot) {
                                dot.classList.add('hidden');
                            }
                        });

                        // Select this therapist
                        this.classList.remove('border-gray-200');
                        this.classList.add('border-ayur-green-300', 'bg-ayur-green-50');
                        this.querySelector('input[type="radio"]').checked = true;

                        // Update radio button styling
                        const radioSpan = this.querySelector('span');
                        radioSpan.classList.remove('border-gray-300');
                        radioSpan.classList.add('border-ayur-green-500');

                        // Show the dot
                        const dot = radioSpan.querySelector('span');
                        if (dot) {
                            dot.classList.remove('hidden');
                        }

                        // Clear validation error when therapist is selected
                        clearError('therapist_id');
                    });
                });
            }

            // Fallback function for basic data extraction
            function extractBasicTreatmentData(treatmentId) {
                const treatmentItem = document.querySelector(`[data-treatment-id="${treatmentId}"]`);

                selectedTreatmentData = {
                    id: treatmentId,
                    patientName: treatmentItem.querySelector('.font-medium.text-ayur-brown-800').textContent,
                    patientUhid: treatmentItem.querySelector('.text-xs.text-ayur-brown-600').textContent,
                    treatment: treatmentItem.querySelector('.text-sm.text-ayur-brown-700 span:nth-child(2)')
                        .textContent,
                    duration: treatmentItem.querySelectorAll('.text-sm.text-ayur-brown-700')[1]?.querySelector(
                        'span:nth-child(2)')?.textContent || 'N/A',
                    patientPhoto: treatmentItem.querySelector('img').src
                };

                // Update basic patient details for new design
                document.getElementById('patientName').textContent = selectedTreatmentData.patientName;
                document.getElementById('patientUhid').textContent = selectedTreatmentData.patientUhid;
                document.getElementById('patientPhoto').src = selectedTreatmentData.patientPhoto;
                document.getElementById('treatmentProcedure').textContent = selectedTreatmentData.treatment;
                document.getElementById('treatmentDuration').textContent = selectedTreatmentData.duration;
                document.getElementById('treatmentScheduled').textContent = 'Today, 10:30 AM';
                document.getElementById('patientDosha').textContent = 'Dosha: Vata-Pitta';
                document.getElementById('patientAllergies').textContent =
                    'Chronic headache, stress-induced insomnia. Avoid pressure on right shoulder.';

                // Update materials with sample data
                const materialsContainer = document.getElementById('materialsRequired');
                const fallbackMaterials = ['Sesame Oil (Til Taila)', 'Brahmi Oil', 'Treatment Equipment',
                    'Cotton Towels (6)'
                ];
                materialsContainer.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-ayur-brown-700">Sesame Oil (Til Taila)</span>
                        <span class="text-xs text-ayur-green-700">In Stock</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-ayur-brown-700">Brahmi Oil</span>
                        <span class="text-xs text-ayur-green-700">In Stock</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-ayur-brown-700">Treatment Equipment</span>
                        <span class="text-xs text-ayur-green-700">Available</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ayur-brown-700">Cotton Towels (6)</span>
                        <span class="text-xs text-ayur-green-700">Available</span>
                    </div>
                `;
                document.getElementById('materialsRequiredInput').value = fallbackMaterials.join(', ');

                // Set hidden form field
                document.getElementById('selectedTreatmentId').value = treatmentId;
                document.getElementById('treatmentDetails').value = selectedTreatmentData.treatment;

                // Show assignment form and hide no selection message
                document.getElementById('selectedPatientDetails').style.display = 'block';
                document.getElementById('therapistAssignmentForm').style.display = 'block';
                document.getElementById('noPatientSelected').style.display = 'none';
            }

            // Client-side validation for therapist assignment form
            const form = document.getElementById('therapistAssignmentForm');
            if (!form) return;

            const fields = {
                therapist_id: () => document.querySelector('input[name="therapist_id"]:checked'),
                room_id: document.getElementById('room_id')
            };

            function showError(fieldName, message) {
                const el = document.getElementById(fieldName + '-error');
                if (el) {
                    el.textContent = message;
                    el.style.display = 'block';
                }

                // Highlight the field or container
                if (fieldName === 'therapist_id') {
                    const therapistSection = document.querySelector('#therapistsList');
                    if (therapistSection) {
                        therapistSection.style.border = '2px solid #ef4444';
                        therapistSection.style.borderRadius = '8px';
                    }
                } else if (fieldName === 'room_id') {
                    const input = fields[fieldName];
                    if (input) input.classList.add('border-red-500');
                }
            }

            function clearError(fieldName) {
                const el = document.getElementById(fieldName + '-error');
                if (el) {
                    el.textContent = '';
                    el.style.display = 'none';
                }

                // Remove highlighting
                if (fieldName === 'therapist_id') {
                    const therapistSection = document.querySelector('#therapistsList');
                    if (therapistSection) {
                        therapistSection.style.border = '';
                    }
                } else if (fieldName === 'room_id') {
                    const input = fields[fieldName];
                    if (input) input.classList.remove('border-red-500');
                }
            }

            // Initialize therapist selection listeners
            attachTherapistSelectionListeners();

            // Handle form submission
            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate therapist selection
                clearError('therapist_id');
                const therapistSelected = fields.therapist_id();
                if (!therapistSelected) {
                    showError('therapist_id', 'Please select a therapist before submitting.');
                    valid = false;
                }

                // Validate room selection
                clearError('room_id');
                const roomSelected = fields.room_id && fields.room_id.value;
                if (!roomSelected) {
                    showError('room_id', 'Please select a treatment room before submitting.');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    const firstErr = document.querySelector('.js-error[style*="display: block"]');
                    if (firstErr) firstErr.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = document.getElementById('confirmAssignmentBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const spinner = submitBtn.querySelector('.fa-spinner');

                // Show loading state
                btnText.textContent = 'Assigning...';
                spinner.classList.remove('hidden');
                submitBtn.disabled = true;

                fetch('{{ route('therapist-assignment.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Something went wrong',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to assign therapist. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    })
                    .finally(() => {
                        // Reset loading state
                        btnText.textContent = 'Confirm Assignment';
                        spinner.classList.add('hidden');
                        submitBtn.disabled = false;
                    });
            });

            // Clear errors when user interacts with fields
            // Clear therapist error when therapist is selected
            document.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'therapist_id') {
                    clearError('therapist_id');
                }
            });

            // Clear room error when room is selected
            if (fields.room_id) {
                fields.room_id.addEventListener('change', () => clearError('room_id'));
            }

            // Handle close patient details button
            document.getElementById('closePatientDetails').addEventListener('click', function() {
                document.getElementById('selectedPatientDetails').style.display = 'none';
                document.getElementById('therapistAssignmentForm').style.display = 'none';
                document.getElementById('noPatientSelected').style.display = 'block';

                // Remove treatment selection
                document.querySelectorAll('.pending-treatment-item').forEach(item => {
                    item.classList.remove('bg-ayur-yellow-50', 'border-ayur-yellow-200');
                    item.classList.add('bg-white', 'border-gray-200');
                });

                // Remove therapist selection
                document.querySelectorAll('[data-therapist-id]').forEach(opt => {
                    opt.classList.remove('border-ayur-green-300', 'bg-ayur-green-50');
                    opt.classList.add('border-gray-200');
                    opt.querySelector('input[type="radio"]').checked = false;

                    // Update radio button styling
                    const radioSpan = opt.querySelector('span');
                    radioSpan.classList.remove('border-ayur-green-500');
                    radioSpan.classList.add('border-gray-300');

                    // Hide the dot
                    const dot = radioSpan.querySelector('span');
                    if (dot) {
                        dot.classList.add('hidden');
                    }
                });

                // Reset therapist filter
                document.getElementById('treatmentCategoryFilter').textContent = '';
                document.getElementById('therapistsLoading').classList.add('hidden');
                document.getElementById('noTherapistsFound').classList.add('hidden');

                // Clear any validation errors
                clearError('therapist_id');
                clearError('room_id');

                selectedTreatmentData = null;
            });

            // Handle cancel button
            document.getElementById('cancelAssignmentBtn').addEventListener('click', function() {
                // Reset form and hide
                document.getElementById('therapistAssignmentForm').reset();
                document.getElementById('selectedPatientDetails').style.display = 'none';
                document.getElementById('therapistAssignmentForm').style.display = 'none';
                document.getElementById('noPatientSelected').style.display = 'block';

                // Remove treatment selection
                document.querySelectorAll('.pending-treatment-item').forEach(item => {
                    item.classList.remove('bg-ayur-yellow-50', 'border-ayur-yellow-200');
                    item.classList.add('bg-white', 'border-gray-200');
                });

                // Remove therapist selection
                document.querySelectorAll('[data-therapist-id]').forEach(opt => {
                    opt.classList.remove('border-ayur-green-300', 'bg-ayur-green-50');
                    opt.classList.add('border-gray-200');
                    opt.querySelector('input[type="radio"]').checked = false;

                    // Update radio button styling
                    const radioSpan = opt.querySelector('span');
                    radioSpan.classList.remove('border-ayur-green-500');
                    radioSpan.classList.add('border-gray-300');

                    // Hide the dot
                    const dot = radioSpan.querySelector('span');
                    if (dot) {
                        dot.classList.add('hidden');
                    }
                });

                // Reset therapist filter
                document.getElementById('treatmentCategoryFilter').textContent = '';
                document.getElementById('therapistsLoading').classList.add('hidden');
                document.getElementById('noTherapistsFound').classList.add('hidden');

                // Clear any validation errors
                clearError('therapist_id');
                clearError('room_id');

                selectedTreatmentData = null;
            });

            // Handle assignment status updates
            document.querySelectorAll('.complete-assignment-btn, .cancel-assignment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const assignmentId = this.getAttribute('data-assignment-id');
                    const isComplete = this.classList.contains('complete-assignment-btn');
                    const status = isComplete ? 2 : 3; // 2=Completed, 3=Cancelled
                    const title = isComplete ? 'Complete Assignment' : 'Cancel Assignment';
                    const text = isComplete ? 'Mark this assignment as completed?' :
                        'Cancel this assignment?';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/therapist-assignment/${assignmentId}/status`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        status: status
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message,
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to update assignment status',
                                        icon: 'error'
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
@endsection
