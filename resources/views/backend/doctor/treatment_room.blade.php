@extends('backend.layouts.master')

@section('styles')
    <style>
        ::-webkit-scrollbar {
            display: none;
        }

        .highlighted-section {
            outline: 2px solid #3F20FB;
            background-color: rgba(63, 32, 251, 0.1);
        }

        .edit-button {
            position: absolute;
            z-index: 1000;
        }

        ::-webkit-scrollbar {
            display: none;
        }

        html,
        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .room-card {
            transition: all 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-available {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .status-occupied {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .status-maintenance {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }
    </style>
@endsection

@section('content')
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
                    <a href="{{ route('doctor.treatment-room') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor.treatment-room') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Treatment Rooms
                    </a>
                </nav>
            </div>
        </div>

        <!-- ROOM OVERVIEW HEADER -->
        <div id="roomOverviewHeader" class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-ayur-brown-800">Treatment Room Overview</h3>
                        <p class="text-sm text-ayur-brown-600">{{ $selectedDate->format('F d, Y - l') }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-ayur-brown-600">Available ({{ $roomCounts['available'] }})</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                            <span class="text-sm text-ayur-brown-600">Occupied ({{ $roomCounts['occupied'] }})</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-sm text-ayur-brown-600">Maintenance ({{ $roomCounts['maintenance'] }})</span>
                        </div>
                    </div>
                    <div class="relative">
                        <button id="roomFilterBtn"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                    <button id="bookRoomBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Book Room
                    </button>
                </div>
            </div>
        </div>

        <!-- ROOM AVAILABILITY GRID -->
        <div id="roomAvailabilityGrid" class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Room Availability Grid</h3>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($roomData as $roomInfo)
                        @php
                            $room = $roomInfo['room'];
                            $status = $roomInfo['status'];
                            $currentAssignment = $roomInfo['currentAssignment'];
                            $nextAssignment = $roomInfo['nextAssignment'];
                        @endphp

                        <div id="room{{ $room->id }}"
                            class="border-2 rounded-lg p-4 relative {{ $status === 'occupied' ? 'border-red-300 bg-red-50' : ($status === 'maintenance' ? 'border-yellow-300 bg-yellow-50' : 'border-green-300 bg-green-50 cursor-pointer hover:shadow-md transition-all') }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-ayur-brown-800">Room {{ $room->room_number }}</h4>
                                    <p class="text-sm text-ayur-brown-600">{{ $room->room_type }}</p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $status === 'occupied' ? 'bg-red-100 text-red-800' : ($status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    <i
                                        class="fa-solid {{ $status === 'occupied' ? 'fa-circle' : ($status === 'maintenance' ? 'fa-wrench' : 'fa-circle') }} text-xs mr-1"></i>
                                    {{ ucfirst($status) }}
                                </span>
                            </div>

                            @if ($status === 'occupied' && $currentAssignment)
                                <div class="mb-3">
                                    <div class="flex items-center mb-2">
                                        <div class="h-6 w-6 rounded-full mr-2 bg-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-xs"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-ayur-brown-800">{{ $currentAssignment->patient->first_name }}
                                            {{ $currentAssignment->patient->last_name }}</span>
                                    </div>
                                    <p class="text-xs text-ayur-brown-600">{{ $currentAssignment->treatment_details }}</p>
                                    <p class="text-xs text-ayur-brown-600">
                                        {{ \Carbon\Carbon::parse($currentAssignment->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($currentAssignment->end_time)->format('H:i') }}</p>
                                    <p class="text-xs text-ayur-brown-600">Therapist:
                                        {{ $currentAssignment->therapist->full_name }}</p>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-ayur-brown-500">
                                        @if ($nextAssignment)
                                            Next: {{ \Carbon\Carbon::parse($nextAssignment->start_time)->format('H:i') }}
                                        @else
                                            No upcoming bookings
                                        @endif
                                    </span>
                                    <div class="flex space-x-1">
                                        <button onclick="viewAssignment({{ $currentAssignment->id }})"
                                            class="text-ayur-green-600 hover:text-ayur-green-800">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button onclick="editAssignment({{ $currentAssignment->id }})"
                                            class="text-ayur-brown-600 hover:text-ayur-brown-800">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </div>
                            @elseif($status === 'available')
                                <div class="mb-3 text-center py-4">
                                    <i class="fa-solid fa-plus text-2xl text-ayur-green-500 mb-2"></i>
                                    <p class="text-sm text-ayur-green-600 font-medium">Ready for booking</p>
                                    <p class="text-xs text-ayur-brown-500">Charges: ₹{{ $room->charges }}</p>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-ayur-brown-500">Available now</span>
                                    <button onclick="bookRoom({{ $room->id }})"
                                        class="text-ayur-green-600 hover:text-ayur-green-800 font-medium">
                                        <i class="fa-solid fa-calendar-plus mr-1"></i>
                                        Book
                                    </button>
                                </div>
                            @elseif($status === 'maintenance')
                                <div class="mb-3 text-center py-4">
                                    <i class="fa-solid fa-tools text-2xl text-yellow-500 mb-2"></i>
                                    <p class="text-sm text-yellow-600 font-medium">Under maintenance</p>
                                    <p class="text-xs text-ayur-brown-500">Equipment servicing</p>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-ayur-brown-500">Available: TBD</span>
                                    <button class="text-ayur-brown-400 cursor-not-allowed">
                                        <i class="fa-solid fa-ban mr-1"></i>
                                        Unavailable
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ROOM BOOKING DETAILS & ACCORDION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- BOOKING DETAILS -->
            <div id="bookingDetails" class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Current Bookings</h3>
                </div>

                <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                    @forelse($currentBookings as $booking)
                        @php
                            $statusColors = [
                                0 => [
                                    'border' => 'border-yellow-200',
                                    'bg' => 'bg-yellow-50',
                                    'text' => 'text-yellow-600',
                                    'badge' => 'bg-yellow-100',
                                ],
                                1 => [
                                    'border' => 'border-red-200',
                                    'bg' => 'bg-red-50',
                                    'text' => 'text-red-600',
                                    'badge' => 'bg-red-100',
                                ],
                                2 => [
                                    'border' => 'border-blue-200',
                                    'bg' => 'bg-blue-50',
                                    'text' => 'text-blue-600',
                                    'badge' => 'bg-blue-100',
                                ],
                                4 => [
                                    'border' => 'border-purple-200',
                                    'bg' => 'bg-purple-50',
                                    'text' => 'text-purple-600',
                                    'badge' => 'bg-purple-100',
                                ],
                            ];
                            $colors = $statusColors[$booking->status] ?? $statusColors[0];
                        @endphp

                        <div class="border {{ $colors['border'] }} {{ $colors['bg'] }} rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 {{ $colors['badge'] }} rounded-lg flex items-center justify-center mr-3">
                                        <span
                                            class="text-sm font-bold {{ $colors['text'] }}">{{ $booking->room->room_number }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-ayur-brown-800">{{ $booking->patient->first_name }}
                                            {{ $booking->patient->last_name }}</h4>
                                        <p class="text-sm text-ayur-brown-600">{{ $booking->treatment_details }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-xs {{ $colors['text'] }} font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                    - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-ayur-brown-600">Therapist: {{ $booking->therapist->full_name }}</span>
                                <div class="flex space-x-2">
                                    <button onclick="viewAssignment({{ $booking->id }})"
                                        class="text-ayur-green-600 hover:text-ayur-green-800">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button onclick="editAssignment({{ $booking->id }})"
                                        class="text-ayur-brown-600 hover:text-ayur-brown-800">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @if ($booking->canCancel())
                                        <button onclick="cancelAssignment({{ $booking->id }})"
                                            class="text-red-600 hover:text-red-800">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No bookings for today</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ROOM FEATURES ACCORDION -->
            <div id="roomFeaturesAccordion" class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Room Features &amp; Equipment</h3>
                </div>

                <div class="p-4">
                    @foreach ($allRooms as $index => $room)
                        @php
                            $roomId = 'room_' . $room->id;
                            $badgeColors = [
                                'Abhyanga Suite' => ['bg' => 'bg-ayur-green-100', 'text' => 'text-ayur-green-600'],
                                'Shirodhara Room' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                'Basti Suite' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                'General Treatment' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
                                'Pinda Sweda Room' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
                                'Nasya Room' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
                                'Udvartana Room' => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
                                'Multi-purpose' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600'],
                            ];
                            $colors = $badgeColors[$room->room_type] ?? [
                                'bg' => 'bg-gray-100',
                                'text' => 'text-gray-600',
                            ];

                            $features = [
                                'Abhyanga Suite' => [
                                    'Adjustable massage table',
                                    'Oil warming system',
                                    'Steam shower facility',
                                    'Temperature control',
                                    'Sound system for meditation music',
                                    'Private changing area',
                                ],
                                'Shirodhara Room' => [
                                    'Specialized Shirodhara table',
                                    'Oil dripping apparatus',
                                    'Oil collection system',
                                    'Dimmed lighting controls',
                                    'Aromatherapy diffuser',
                                    'Heated towel storage',
                                ],
                                'Basti Suite' => [
                                    'Specialized Basti equipment',
                                    'Herbal preparation area',
                                    'Sterilization unit',
                                    'Privacy screens',
                                    'Emergency equipment',
                                    'Comfortable seating for relatives',
                                ],
                                'General Treatment' => [
                                    'Multi-purpose treatment table',
                                    'Basic oil storage',
                                    'Towel warmer',
                                    'Wash basin',
                                    'Storage cabinets',
                                    'Air purification system',
                                ],
                                'Pinda Sweda Room' => [
                                    'Specialized Sweda equipment',
                                    'Herbal preparation area',
                                    'Temperature control',
                                    'Steam generation system',
                                    'Patient comfort features',
                                    'Safety equipment',
                                ],
                                'Nasya Room' => [
                                    'Nasya administration equipment',
                                    'Patient positioning system',
                                    'Medication preparation area',
                                    'Sterilization facilities',
                                    'Comfort features',
                                    'Safety protocols',
                                ],
                                'Udvartana Room' => [
                                    'Udvartana powder preparation area',
                                    'Specialized massage equipment',
                                    'Temperature control',
                                    'Patient positioning aids',
                                    'Cleaning facilities',
                                    'Storage for herbal powders',
                                ],
                                'Multi-purpose' => [
                                    'Flexible treatment table',
                                    'Basic equipment storage',
                                    'Cleaning facilities',
                                    'Patient comfort features',
                                    'Safety equipment',
                                    'Adaptable setup',
                                ],
                            ];
                            $roomFeatures = $features[$room->room_type] ?? [
                                'Standard treatment table',
                                'Basic equipment',
                                'Cleaning facilities',
                                'Patient comfort features',
                            ];
                        @endphp

                        <div class="border border-gray-200 rounded-lg {{ $index < count($allRooms) - 1 ? 'mb-3' : '' }}">
                            <button
                                class="w-full px-4 py-3 text-left flex justify-between items-center hover:bg-gray-50 rounded-lg"
                                onclick="toggleAccordion('{{ $roomId }}')">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 {{ $colors['bg'] }} rounded-lg flex items-center justify-center mr-3">
                                        <span
                                            class="text-sm font-bold {{ $colors['text'] }}">{{ $room->room_number }}</span>
                                    </div>
                                    <span class="font-medium text-ayur-brown-800">{{ $room->room_type }}</span>
                                </div>
                                <i id="{{ $roomId }}-icon"
                                    class="fa-solid fa-chevron-down text-ayur-brown-600 transition-transform"></i>
                            </button>
                            <div id="{{ $roomId }}-content" class="hidden px-4 pb-4">
                                <div class="pl-11 space-y-2 text-sm text-ayur-brown-600">
                                    @foreach ($roomFeatures as $feature)
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-check text-ayur-green-600 mr-2"></i>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                    <div class="mt-3 pt-2 border-t border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Capacity: {{ $room->capacity }}
                                                patients</span>
                                            <span class="text-xs text-gray-500">Charges: ₹{{ $room->charges }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ROOM UTILIZATION STATS -->
        <div id="roomUtilizationStats" class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Room Utilization Statistics</h3>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-ayur-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-ayur-green-600 mb-2">
                            {{ $utilizationStats['overallUtilization'] }}%</div>
                        <div class="text-sm text-ayur-brown-600">Overall Utilization</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 mb-2">{{ $utilizationStats['treatmentsToday'] }}
                        </div>
                        <div class="text-sm text-ayur-brown-600">Treatments Today</div>
                    </div>
                    <div class="text-center p-4 bg-ayur-yellow-50 rounded-lg">
                        <div class="text-2xl font-bold text-ayur-yellow-600 mb-2">
                            {{ $utilizationStats['avgTreatmentTime'] }}</div>
                        <div class="text-sm text-ayur-brown-600">Avg Treatment Time</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 mb-2">{{ $utilizationStats['revenueToday'] }}</div>
                        <div class="text-sm text-ayur-brown-600">Revenue Today</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- BOOKING MODAL -->
    <div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-ayur-brown-800">Book Treatment Room</h3>
                    <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <form id="bookingForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="room_id" name="room_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Patient</label>
                            <input type="text" id="patient_search" name="patient_search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                placeholder="Search patient by name or phone">
                            <input type="hidden" id="patient_id" name="patient_id">
                            <div id="patient_results"
                                class="hidden mt-1 border border-gray-300 rounded-md bg-white shadow-lg max-h-40 overflow-y-auto">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Therapist</label>
                            <select id="therapist_id" name="therapist_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                                <option value="">Select Therapist</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Date</label>
                            <input type="date" id="assignment_date" name="assignment_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                value="{{ $selectedDate->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Start Time</label>
                            <select id="start_time" name="start_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                                <option value="">Select Start Time</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">End Time</label>
                            <select id="end_time" name="end_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500">
                                <option value="">Select End Time</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Treatment Details</label>
                        <textarea id="treatment_details" name="treatment_details" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                            placeholder="Describe the treatment to be performed"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Materials Required</label>
                            <textarea id="materials_required" name="materials_required" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                placeholder="List any special materials needed"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Special Instructions</label>
                            <textarea id="special_instructions" name="special_instructions" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500"
                                placeholder="Any special instructions for the therapist"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeBookingModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                            Book Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FILTER DRAWER -->
    <div id="filterDrawer" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-ayur-brown-800">Filter Rooms</h3>
                    <button onclick="closeFilterDrawer()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <form id="filterForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Room Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="available" class="mr-2">
                                <span class="text-sm text-ayur-brown-600">Available</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="occupied" class="mr-2">
                                <span class="text-sm text-ayur-brown-600">Occupied</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="maintenance" class="mr-2">
                                <span class="text-sm text-ayur-brown-600">Maintenance</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Room Type</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach ($allRooms->pluck('room_type')->unique() as $roomType)
                                <label class="flex items-center">
                                    <input type="checkbox" name="room_type" value="{{ $roomType }}" class="mr-2">
                                    <span class="text-sm text-ayur-brown-600">{{ $roomType }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeFilterDrawer()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Patient search functionality
            $('#patient_search').on('input', function() {
                const query = $(this).val();
                if (query.length >= 2) {
                    $.get('{{ route('treatment-room.search-patients') }}', {
                            q: query
                        })
                        .done(function(patients) {
                            const results = $('#patient_results');
                            results.empty();

                            if (patients.length > 0) {
                                patients.forEach(function(patient) {
                                    results.append(`
                                        <button type="button" class="w-full px-3 py-2 text-left hover:bg-gray-100 border-b border-gray-200 last:border-b-0" 
                                                onclick="selectPatient(${patient.id}, '${patient.first_name} ${patient.last_name}', '${patient.phone}')">
                                            <div class="font-medium text-ayur-brown-800">${patient.first_name} ${patient.last_name}</div>
                                            <small class="text-gray-500">${patient.phone}</small>
                                        </button>
                                    `);
                                });
                                results.removeClass('hidden');
                            } else {
                                results.append(
                                    '<div class="px-3 py-2 text-gray-500">No patients found</div>'
                                );
                                results.removeClass('hidden');
                            }
                        });
                } else {
                    $('#patient_results').addClass('hidden');
                }
            });

            // Room selection change
            $('#room_id').on('change', function() {
                const roomId = $(this).val();
                const date = $('#assignment_date').val();

                if (roomId && date) {
                    loadAvailableSlots(roomId, date);
                }
            });

            // Date change
            $('#assignment_date').on('change', function() {
                const roomId = $('#room_id').val();
                const date = $(this).val();

                if (roomId && date) {
                    loadAvailableSlots(roomId, date);
                }
            });

            // Start time change - auto-populate end time
            $('#start_time').on('change', function() {
                const startTime = $(this).val();
                const endTimeSelect = $('#end_time');

                if (startTime) {
                    endTimeSelect.empty().append('<option value="">Select End Time</option>');

                    // Generate end time options (1-4 hours after start time)
                    for (let i = 1; i <= 4; i++) {
                        const start = new Date('2000-01-01 ' + startTime);
                        const end = new Date(start.getTime() + (i * 60 * 60 * 1000));
                        const endTime = end.toTimeString().substr(0, 5);

                        endTimeSelect.append(`<option value="${endTime}">${endTime}</option>`);
                    }
                } else {
                    endTimeSelect.empty().append('<option value="">Select End Time</option>');
                }
            });

            // Booking form submission
            $('#bookingForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('treatment-room.store-booking') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Something went wrong'
                        });
                    }
                });
            });

            // Filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();

                const status = [];
                $('input[name="status"]:checked').each(function() {
                    status.push($(this).val());
                });

                const roomType = [];
                $('input[name="room_type"]:checked').each(function() {
                    roomType.push($(this).val());
                });

                // Apply filters
                applyRoomFilters(status, roomType);
                closeFilterDrawer();
            });

            // Book room button click
            $('#bookRoomBtn').on('click', function() {
                openBookingModal();
            });

            // Filter button click
            $('#roomFilterBtn').on('click', function() {
                openFilterDrawer();
            });
        });

        function selectPatient(id, name, phone) {
            $('#patient_id').val(id);
            $('#patient_search').val(name + ' (' + phone + ')');
            $('#patient_results').addClass('hidden');
        }

        function loadAvailableSlots(roomId, date) {
            $.get('{{ route('treatment-room.booking-form-data') }}', {
                room_id: roomId,
                date: date
            }).done(function(data) {
                // Populate therapists
                const therapistSelect = $('#therapist_id');
                therapistSelect.empty().append('<option value="">Select Therapist</option>');

                data.therapists.forEach(function(therapist) {
                    therapistSelect.append(`
                        <option value="${therapist.id}">${therapist.full_name}</option>
                    `);
                });

                // Populate time slots
                const startTimeSelect = $('#start_time');
                startTimeSelect.empty().append('<option value="">Select Start Time</option>');

                data.availableSlots.forEach(function(slot) {
                    startTimeSelect.append(`<option value="${slot.start}">${slot.display}</option>`);
                });
            });
        }

        function bookRoom(roomId) {
            $('#room_id').val(roomId);
            openBookingModal();

            // Load available slots for this room
            const date = $('#assignment_date').val();
            if (date) {
                loadAvailableSlots(roomId, date);
            }
        }

        function openBookingModal() {
            $('#bookingModal').removeClass('hidden');
        }

        function closeBookingModal() {
            $('#bookingModal').addClass('hidden');
            $('#bookingForm')[0].reset();
            $('#patient_results').addClass('hidden');
        }

        function openFilterDrawer() {
            $('#filterDrawer').removeClass('hidden');
        }

        function closeFilterDrawer() {
            $('#filterDrawer').addClass('hidden');
        }

        function applyRoomFilters(status, roomType) {
            // Hide all rooms first
            $('[id^="room"]').hide();

            // Show rooms based on filters
            $('[id^="room"]').each(function() {
                const roomElement = $(this);
                let showRoom = true;

                // Check status filter
                if (status.length > 0) {
                    const roomStatus = roomElement.find('.inline-flex').text().trim().toLowerCase();
                    if (!status.includes(roomStatus)) {
                        showRoom = false;
                    }
                }

                // Check room type filter
                if (roomType.length > 0) {
                    const roomTypeText = roomElement.find('p.text-sm').text().trim();
                    if (!roomType.includes(roomTypeText)) {
                        showRoom = false;
                    }
                }

                if (showRoom) {
                    roomElement.show();
                }
            });
        }

        function toggleAccordion(roomId) {
            const content = $(`#${roomId}-content`);
            const icon = $(`#${roomId}-icon`);

            if (content.hasClass('hidden')) {
                content.removeClass('hidden');
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                content.addClass('hidden');
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        }

        function viewAssignment(assignmentId) {
            // Implement view assignment functionality
            Swal.fire({
                title: 'Assignment Details',
                text: 'View assignment functionality will be implemented here',
                icon: 'info'
            });
        }

        function editAssignment(assignmentId) {
            // Implement edit assignment functionality
            Swal.fire({
                title: 'Edit Assignment',
                text: 'Edit assignment functionality will be implemented here',
                icon: 'info'
            });
        }

        function cancelAssignment(assignmentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will cancel the assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/treatment-room') }}/${assignmentId}/status`,
                        method: 'PATCH',
                        data: {
                            status: 3
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Cancelled!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            const error = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.message || 'Something went wrong'
                            });
                        }
                    });
                }
            });
        }

        // Close modals when clicking outside
        $(document).on('click', function(e) {
            if ($(e.target).hasClass('fixed') && $(e.target).attr('id') === 'bookingModal') {
                closeBookingModal();
            }
            if ($(e.target).hasClass('fixed') && $(e.target).attr('id') === 'filterDrawer') {
                closeFilterDrawer();
            }
        });
    </script>
@endsection
