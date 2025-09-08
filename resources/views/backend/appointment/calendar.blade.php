@extends('backend.layouts.master')

@section('content')
    <main class="p-4">
        <!-- TABS -->
        <div id="appointmentTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('appointment') }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Appointment Booking
                    </a>
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Calendar View
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Queue Manager
                    </button>
                </nav>
            </div>
        </div>

        <!-- CALENDAR FILTERS -->
        <div id="calendarFilters" class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Date</label>
                        <div class="relative">
                            <input type="date" id="calendarDate"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                value="{{ $today }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Department</label>
                        <select id="departmentFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">View</label>
                        <select id="viewType"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                            <option value="day">Day</option>
                            <option value="week" selected>Week</option>
                            <option value="month">Month</option>
                        </select>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('appointment') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-plus mr-2"></i> New Appointment
                    </a>
                    <button id="printCalendar"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- CALENDAR LEGEND -->
        <div id="calendarLegend" class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h3 class="text-sm font-medium text-ayur-brown-700 mb-3">Calendar Legend</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-ayur-green-500 mr-2"></div>
                    <span class="text-sm text-ayur-brown-700">Available</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-ayur-yellow-500 mr-2"></div>
                    <span class="text-sm text-ayur-brown-700">Booked</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-red-500 mr-2"></div>
                    <span class="text-sm text-ayur-brown-700">Overlap Alert</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-ayur-brown-300 mr-2"></div>
                    <span class="text-sm text-ayur-brown-700">Break/Unavailable</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-blue-500 mr-2"></div>
                    <span class="text-sm text-ayur-brown-700">Panchkarma Session</span>
                </div>
            </div>
        </div>

        <!-- CALENDAR VIEW -->
        <div id="calendarView" class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <!-- Calendar Header -->
            <div class="border-b border-gray-200 p-4 flex justify-between items-center bg-ayur-offwhite">
                <div class="flex space-x-2">
                    <button id="prevWeek" class="text-ayur-brown-700 hover:text-ayur-brown-900">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <h3 id="calendarTitle" class="text-lg font-semibold text-ayur-brown-800">
                        {{ \Carbon\Carbon::parse($today)->startOfWeek()->format('M d') }} -
                        {{ \Carbon\Carbon::parse($today)->endOfWeek()->format('M d, Y') }}
                    </h3>
                    <button id="nextWeek" class="text-ayur-brown-700 hover:text-ayur-brown-900">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <button id="todayBtn" class="text-sm text-ayur-green-600 hover:text-ayur-green-700 font-medium">
                    Today
                </button>
            </div>

            <!-- Time Indicators -->
            <div class="flex">
                <!-- Doctor/Room Column Headers -->
                <div class="w-24 bg-ayur-offwhite border-r border-gray-200">
                    <div class="h-12 border-b border-gray-200 flex items-center justify-center">
                        <span class="text-xs font-medium text-ayur-brown-600">Time</span>
                    </div>
                </div>

                <!-- Doctor Column Headers -->
                <div class="flex-1 grid" id="doctorHeaders"
                    style="grid-template-columns: repeat({{ $doctors->count() }}, 1fr);">
                    @foreach ($doctors as $doctor)
                        <div class="h-12 border-b border-r border-gray-200 flex flex-col items-center justify-center p-1">
                            <div class="flex items-center">
                                <img class="h-6 w-6 rounded-full mr-1"
                                    src="{{ $doctor->photo_path ? asset($doctor->photo_path) : asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                    alt="Doctor avatar">
                                <span class="text-xs font-medium text-ayur-brown-800">{{ $doctor->full_name }}</span>
                            </div>
                            <span class="text-xs text-ayur-brown-600">Room {{ $loop->iteration + 100 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="flex" style="height: 600px; overflow-y: auto;">
                <!-- Time Column -->
                <div class="w-24 flex flex-col border-r border-gray-200">
                    @for ($hour = 9; $hour <= 18; $hour++)
                        @for ($minute = 0; $minute < 60; $minute += 30)
                            <div class="h-16 border-b border-gray-200 flex items-center justify-center">
                                <span class="text-xs text-ayur-brown-600">
                                    {{ $hour > 12 ? $hour - 12 : $hour }}:{{ $minute == 0 ? '00' : $minute }}
                                    {{ $hour >= 12 ? 'PM' : 'AM' }}
                                </span>
                            </div>
                        @endfor
                    @endfor
                </div>

                <!-- Calendar Slots -->
                <div class="flex-1 grid" id="calendarSlots"
                    style="grid-template-columns: repeat({{ $doctors->count() }}, 1fr);">
                    @foreach ($doctors as $doctorIndex => $doctor)
                        <div class="border-r border-gray-200 relative" data-doctor-id="{{ $doctor->id }}">
                            <!-- Current time indicator -->
                            <div class="absolute w-full border-t-2 border-red-400 top-[120px] z-10 flex items-center"
                                id="currentTimeIndicator-{{ $doctor->id }}" style="display: none;">
                                <div class="w-3 h-3 rounded-full bg-red-500 -ml-1.5 -mt-1.5"></div>
                                <span class="text-xs text-red-500 ml-1 bg-white px-1"
                                    id="currentTimeText-{{ $doctor->id }}"></span>
                            </div>

                            @for ($hour = 9; $hour <= 18; $hour++)
                                @for ($minute = 0; $minute < 60; $minute += 30)
                                    @php
                                        $timeSlot = sprintf('%02d:%02d', $hour, $minute);
                                        $appointment = $weekAppointments
                                            ->where('doctor_id', $doctor->id)
                                            ->where('appointment_time', $timeSlot)
                                            ->first();
                                    @endphp

                                    <div class="h-16 border-b border-gray-200 relative" data-time="{{ $timeSlot }}">
                                        @if ($appointment)
                                            <div
                                                class="absolute inset-0.5 rounded p-1 flex flex-col 
                                                {{ $appointment->mode === 'Panchkarma' ? 'bg-blue-100' : 'bg-ayur-yellow-100' }}">
                                                <div class="flex justify-between items-start">
                                                    <span class="text-xs font-medium text-ayur-brown-800">
                                                        {{ $appointment->patient->full_name ?? 'N/A' }}
                                                    </span>
                                                    <span
                                                        class="text-xs {{ $appointment->mode === 'Panchkarma' ? 'bg-blue-500' : 'bg-ayur-yellow-500' }} text-white px-1 rounded">
                                                        {{ $timeSlot }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-ayur-brown-600">
                                                    {{ $appointment->appointment_type ?? 'Consultation' }}
                                                </span>
                                            </div>
                                        @else
                                            <!-- Add sample appointments for demonstration -->
                                            @if ($doctorIndex === 0 && $hour === 9 && $minute === 30)
                                                <div
                                                    class="absolute inset-0.5 bg-ayur-yellow-100 rounded p-1 flex flex-col">
                                                    <div class="flex justify-between items-start">
                                                        <span class="text-xs font-medium text-ayur-brown-800">Priya
                                                            Sharma</span>
                                                        <span
                                                            class="text-xs bg-ayur-yellow-500 text-white px-1 rounded">9:30-10:00</span>
                                                    </div>
                                                    <span class="text-xs text-ayur-brown-600">Consultation</span>
                                                </div>
                                            @elseif($doctorIndex === 0 && $hour === 10 && $minute === 30)
                                                <div class="absolute inset-0.5 bg-blue-100 rounded p-1 flex flex-col">
                                                    <div class="flex justify-between items-start">
                                                        <span class="text-xs font-medium text-ayur-brown-800">Rajesh
                                                            Kumar</span>
                                                        <span
                                                            class="text-xs bg-blue-500 text-white px-1 rounded">10:30-11:30</span>
                                                    </div>
                                                    <span class="text-xs text-ayur-brown-600">Abhyanga</span>
                                                </div>
                                            @elseif($doctorIndex === 0 && $hour === 12 && $minute === 0)
                                                <div
                                                    class="absolute inset-0.5 bg-ayur-brown-200 rounded p-1 flex flex-col">
                                                    <span class="text-xs font-medium text-ayur-brown-800 text-center">Lunch
                                                        Break</span>
                                                </div>
                                            @elseif($doctorIndex === 1 && $hour === 9 && $minute === 0)
                                                <div
                                                    class="absolute inset-0.5 bg-ayur-yellow-100 rounded p-1 flex flex-col">
                                                    <div class="flex justify-between items-start">
                                                        <span class="text-xs font-medium text-ayur-brown-800">Arjun
                                                            Desai</span>
                                                        <span
                                                            class="text-xs bg-ayur-yellow-500 text-white px-1 rounded">9:00-9:30</span>
                                                    </div>
                                                    <span class="text-xs text-ayur-brown-600">Consultation</span>
                                                </div>
                                            @elseif($doctorIndex === 1 && $hour === 10 && $minute === 0)
                                                <div class="absolute inset-0.5 bg-blue-100 rounded p-1 flex flex-col">
                                                    <div class="flex justify-between items-start">
                                                        <span class="text-xs font-medium text-ayur-brown-800">Anita
                                                            Gupta</span>
                                                        <span
                                                            class="text-xs bg-blue-500 text-white px-1 rounded">10:00-11:00</span>
                                                    </div>
                                                    <span class="text-xs text-ayur-brown-600">Shirodhara</span>
                                                </div>
                                            @elseif($doctorIndex === 2 && $hour === 9 && $minute === 30)
                                                <div
                                                    class="absolute inset-0.5 bg-ayur-yellow-100 rounded p-1 flex flex-col">
                                                    <div class="flex justify-between items-start">
                                                        <span class="text-xs font-medium text-ayur-brown-800">Kavita
                                                            Shah</span>
                                                        <span
                                                            class="text-xs bg-ayur-yellow-500 text-white px-1 rounded">9:30-10:00</span>
                                                    </div>
                                                    <span class="text-xs text-ayur-brown-600">Consultation</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endfor
                            @endfor
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- OVERLAP ALERTS SECTION -->
        <div id="overlapAlerts" class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i> Appointment Overlap Alerts
                </h3>
                <span id="overlapCount" class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    1 Alert
                </span>
            </div>
            <div id="overlapTable" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Time
                                Slot</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">
                                Patients</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="{{ asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Dr. Patel</div>
                                        <div class="text-xs text-ayur-brown-600">Room 102</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                11:00 AM - 11:30 AM
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-ayur-brown-800">
                                    1. Rani Verma (Consultation)
                                </div>
                                <div class="text-sm text-ayur-brown-800">
                                    2. Anita Gupta (Shirodhara - continuing)
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3">Reschedule</button>
                                <button class="text-red-600 hover:text-red-900">Resolve</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- UPCOMING APPOINTMENTS -->
        <div id="upcomingAppointments" class="bg-white rounded-lg shadow-md p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Today's Upcoming Appointments</h3>
                <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                    View All <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Time</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Service</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="todayAppointmentsTable">
                        @forelse($weekAppointments->where('appointment_date', $today) as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ $appointment->patient->photo_path ? asset($appointment->patient->photo_path) : asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $appointment->patient->full_name ?? 'N/A' }}</div>
                                            <div class="text-xs text-ayur-brown-600">UHID:
                                                {{ $appointment->patient->uhid ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $appointment->doctor->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $appointment->appointment_type ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $appointment->status === 'Waiting'
                                            ? 'bg-ayur-yellow-100 text-ayur-yellow-800'
                                            : ($appointment->status === 'In Progress'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($appointment->status === 'Completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $appointment->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="Start Appointment">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        title="Edit Appointment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Cancel Appointment">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <!-- Sample appointments for demonstration -->
                            <tr class="bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">10:30 AM</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">Rajesh Kumar</div>
                                            <div class="text-xs text-ayur-brown-600">UHID: AYR-2025-0040</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dr. Sharma</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Abhyanga</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">Checked
                                        In</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="Start Appointment">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        title="Edit Appointment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Cancel Appointment">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">11:00 AM</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">Rani Verma</div>
                                            <div class="text-xs text-ayur-brown-600">UHID: AYR-2025-0035</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dr. Patel</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Consultation</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Overlap
                                        Alert</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="Start Appointment">
                                        <i class="fa-solid fa-clock"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        title="Edit Appointment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Cancel Appointment">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">11:00 AM</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">Sanjay Mehta</div>
                                            <div class="text-xs text-ayur-brown-600">UHID: AYR-2025-0033</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dr. Reddy</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Udvartana</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">Confirmed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="Start Appointment">
                                        <i class="fa-solid fa-check-in"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        title="Edit Appointment">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900" title="Cancel Appointment">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- APPOINTMENT OVERLAP ALERT MODAL -->
    <div id="overlapModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-xl font-semibold text-red-600">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Appointment Overlap Alert
                </h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeOverlapModal()">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="mb-4 text-ayur-brown-800">There is a scheduling conflict for:</p>

                <div class="bg-red-50 p-3 rounded-lg mb-4" id="overlapDetails">
                    <!-- Overlap details will be populated by JavaScript -->
                </div>

                <p class="text-sm text-ayur-brown-700 mb-4">
                    Please choose an action to resolve this conflict:
                </p>

                <div class="space-y-3">
                    <button
                        class="w-full py-2 px-4 bg-ayur-yellow-500 text-white rounded-md hover:bg-ayur-yellow-600 transition">
                        Reschedule appointment
                    </button>
                    <button
                        class="w-full py-2 px-4 bg-ayur-green-600 text-white rounded-md hover:bg-ayur-green-700 transition">
                        Assign to different doctor
                    </button>
                    <button
                        class="w-full py-2 px-4 bg-ayur-brown-500 text-white rounded-md hover:bg-ayur-brown-600 transition">
                        Modify treatment duration
                    </button>
                </div>
            </div>
            <div class="p-4 bg-gray-50 rounded-b-lg flex justify-end">
                <button
                    class="py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 mr-2"
                    onclick="closeOverlapModal()">
                    Cancel
                </button>
                <button class="py-2 px-4 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    Resolve Manually
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentDate = new Date('{{ $today }}');
            let currentView = 'week';

            // Initialize calendar
            initializeCalendar();

            // Event listeners
            document.getElementById('prevWeek').addEventListener('click', () => navigateWeek(-1));
            document.getElementById('nextWeek').addEventListener('click', () => navigateWeek(1));
            document.getElementById('todayBtn').addEventListener('click', goToToday);
            document.getElementById('calendarDate').addEventListener('change', onDateChange);
            document.getElementById('departmentFilter').addEventListener('change', onDepartmentChange);
            document.getElementById('viewType').addEventListener('change', onViewChange);
            document.getElementById('printCalendar').addEventListener('click', printCalendar);

            function initializeCalendar() {
                updateCalendarDisplay();
                loadAppointments();
                checkForOverlaps();
                updateCurrentTimeIndicator();
                // Update current time indicator every minute
                setInterval(updateCurrentTimeIndicator, 60000);
            }

            function navigateWeek(direction) {
                currentDate.setDate(currentDate.getDate() + (direction * 7));
                updateCalendarDisplay();
                loadAppointments();
            }

            function goToToday() {
                currentDate = new Date();
                document.getElementById('calendarDate').value = formatDateForInput(currentDate);
                updateCalendarDisplay();
                loadAppointments();
            }

            function onDateChange() {
                currentDate = new Date(document.getElementById('calendarDate').value);
                updateCalendarDisplay();
                loadAppointments();
            }

            function onDepartmentChange() {
                loadAppointments();
            }

            function onViewChange() {
                currentView = document.getElementById('viewType').value;
                updateCalendarDisplay();
                loadAppointments();
            }

            function updateCalendarDisplay() {
                const title = document.getElementById('calendarTitle');
                const dateInput = document.getElementById('calendarDate');

                if (currentView === 'week') {
                    const startOfWeek = new Date(currentDate);
                    startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(startOfWeek.getDate() + 6);

                    title.textContent = `${formatDate(startOfWeek)} - ${formatDate(endOfWeek)}`;
                } else if (currentView === 'day') {
                    title.textContent = formatDate(currentDate);
                } else if (currentView === 'month') {
                    title.textContent = currentDate.toLocaleDateString('en-US', {
                        month: 'long',
                        year: 'numeric'
                    });
                }

                dateInput.value = formatDateForInput(currentDate);
            }

            function updateCurrentTimeIndicator() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();

                // Calculate position based on current time
                const startHour = 9; // Calendar starts at 9 AM
                const totalMinutes = (currentHour - startHour) * 60 + currentMinute;
                const position = (totalMinutes / 30) * 64; // 64px per 30-minute slot

                if (currentHour >= 9 && currentHour <= 18) {
                    document.querySelectorAll('[data-doctor-id]').forEach(doctorColumn => {
                        const indicator = doctorColumn.querySelector('[id^="currentTimeIndicator-"]');
                        const timeText = doctorColumn.querySelector('[id^="currentTimeText-"]');

                        if (indicator && timeText) {
                            indicator.style.top = `${position}px`;
                            indicator.style.display = 'flex';
                            timeText.textContent = now.toLocaleTimeString('en-US', {
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });
                        }
                    });
                }
            }

            function loadAppointments() {
                const departmentId = document.getElementById('departmentFilter').value;
                const date = formatDateForInput(currentDate);

                fetch(
                        `{{ route('appointment.calendar') }}?date=${date}&department=${departmentId}&view=${currentView}`)
                    .then(response => response.json())
                    .then(data => {
                        updateCalendarSlots(data.appointments);
                        updateTodayAppointments(data.todayAppointments);
                        checkForOverlaps();
                    })
                    .catch(error => {
                        console.error('Error loading appointments:', error);
                    });
            }

            function updateCalendarSlots(appointments) {
                // Clear existing appointments
                document.querySelectorAll('[data-time]').forEach(slot => {
                    const existingAppointment = slot.querySelector('.absolute');
                    if (existingAppointment) {
                        existingAppointment.remove();
                    }
                });

                // Add new appointments
                appointments.forEach(appointment => {
                    const slot = document.querySelector(
                        `[data-doctor-id="${appointment.doctor_id}"][data-time="${appointment.appointment_time}"]`
                        );
                    if (slot) {
                        const appointmentDiv = document.createElement('div');
                        appointmentDiv.className = `absolute inset-0.5 rounded p-1 flex flex-col ${
                            appointment.mode === 'Panchkarma' ? 'bg-blue-100' : 'bg-ayur-yellow-100'
                        }`;

                        appointmentDiv.innerHTML = `
                            <div class="flex justify-between items-start">
                                <span class="text-xs font-medium text-ayur-brown-800">${appointment.patient_name}</span>
                                <span class="text-xs ${appointment.mode === 'Panchkarma' ? 'bg-blue-500' : 'bg-ayur-yellow-500'} text-white px-1 rounded">
                                    ${appointment.appointment_time}
                                </span>
                            </div>
                            <span class="text-xs text-ayur-brown-600">${appointment.appointment_type}</span>
                        `;

                        slot.appendChild(appointmentDiv);
                    }
                });
            }

            function updateTodayAppointments(appointments) {
                const tbody = document.getElementById('todayAppointmentsTable');

                if (appointments.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-calendar-times text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">No appointments for today</p>
                                    <p class="text-sm">All clear! No scheduled appointments.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = appointments.map(appointment => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                            ${formatTime(appointment.appointment_time)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <img class="h-8 w-8 rounded-full"
                                        src="${appointment.patient_photo || '{{ asset('backend-assets/media/uploads/patient/patient_20250825104721_ggatR2.jpg') }}'}"
                                        alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-ayur-brown-800">${appointment.patient_name}</div>
                                    <div class="text-xs text-ayur-brown-600">UHID: ${appointment.patient_uhid}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">${appointment.doctor_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">${appointment.appointment_type}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(appointment.status)}">
                                ${appointment.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3" title="Start Appointment">
                                <i class="fa-solid fa-play"></i>
                            </button>
                            <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3" title="Edit Appointment">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900" title="Cancel Appointment">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            function checkForOverlaps() {
                // Overlap alerts are now displayed statically in the HTML
                // This function can be used for dynamic overlap detection in the future
                console.log('Checking for appointment overlaps...');
            }

            function printCalendar() {
                window.print();
            }

            // Utility functions
            function formatDate(date) {
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric'
                });
            }

            function formatDateForInput(date) {
                return date.toISOString().split('T')[0];
            }

            function formatTime(time) {
                const [hours, minutes] = time.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour > 12 ? hour - 12 : hour;
                return `${displayHour}:${minutes} ${ampm}`;
            }

            function getStatusClass(status) {
                switch (status) {
                    case 'Waiting':
                        return 'bg-ayur-yellow-100 text-ayur-yellow-800';
                    case 'In Progress':
                        return 'bg-blue-100 text-blue-800';
                    case 'Completed':
                        return 'bg-green-100 text-green-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }
        });

        // Global functions for modal
        function closeOverlapModal() {
            document.getElementById('overlapModal').classList.add('hidden');
        }

        function showOverlapModal(overlapData) {
            const modal = document.getElementById('overlapModal');
            const details = document.getElementById('overlapDetails');

            details.innerHTML = `
                <div class="font-medium">${overlapData.doctor}</div>
                <div class="text-sm">Time: ${overlapData.time}</div>
                <div class="text-sm mt-2">Conflicting appointments:</div>
                <ul class="list-disc pl-5 mt-1 text-sm">
                    ${overlapData.conflicts.map(conflict => `<li>${conflict}</li>`).join('')}
                </ul>
            `;

            modal.classList.remove('hidden');
        }
    </script>
@endsection
