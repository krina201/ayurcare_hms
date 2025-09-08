@extends('backend.layouts.master')

@section('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
        }

        /* Preserve Font Awesome icons */
        .fa,
        .fas,
        .far,
        .fal,
        .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
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

        <!-- DATE NAVIGATION & FILTERS -->
        <div id="dateNavigation" class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <button class="text-ayur-brown-600 hover:text-ayur-brown-800" onclick="changeDate(-1)">
                        <i class="fa-solid fa-chevron-left text-lg"></i>
                    </button>
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-ayur-brown-800">{{ $carbonDate->format('F j, Y') }}
                        </h3>
                        <p class="text-sm text-ayur-brown-600">{{ $carbonDate->format('l') }}</p>
                    </div>
                    <button class="text-ayur-brown-600 hover:text-ayur-brown-800" onclick="changeDate(1)">
                        <i class="fa-solid fa-chevron-right text-lg"></i>
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <button id="filterBtn"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                    <div class="relative">
                        <select
                            class="bg-white border border-gray-300 text-ayur-brown-800 text-sm rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 px-3 py-2">
                            <option>Day View</option>
                            <option>Week View</option>
                            <option>Month View</option>
                        </select>
                    </div>
                    <button
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Add Appointment
                    </button>
                </div>
            </div>
        </div>

        <!-- SCHEDULE CALENDAR -->
        <div id="scheduleCalendar" class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Daily Schedule Overview</h3>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Time Grid Header -->
                    <div class="grid grid-cols-6 border-b border-gray-200">
                        <div class="p-3 bg-ayur-offwhite font-medium text-ayur-brown-700 text-sm">Time</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-3 bg-ayur-offwhite font-medium text-ayur-brown-700 text-sm text-center">
                                {{ $therapist->full_name }}
                            </div>
                        @endforeach
                    </div>

                    <!-- 9:00 AM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">9:00 AM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['09:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-ayur-green-100 border-l-4 border-ayur-green-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-2.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-ayur-green-700">
                                                {{ $assignment->duration_minutes }} mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 10:00 AM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">10:00 AM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['10:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-ayur-yellow-100 border-l-4 border-ayur-yellow-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-6.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-ayur-yellow-700">
                                                {{ $assignment->duration_minutes }} mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 11:00 AM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">11:00 AM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['11:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-orange-100 border-l-4 border-orange-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-1.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-orange-700">{{ $assignment->duration_minutes }}
                                                mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 12:00 PM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">12:00 PM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2 bg-ayur-brown-50">
                                <div class="text-center text-sm text-ayur-brown-600 py-2">
                                    <i class="fa-solid fa-utensils mr-1"></i>
                                    Lunch Break
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- 1:00 PM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">1:00 PM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['13:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-pink-100 border-l-4 border-pink-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-6.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-pink-700">{{ $assignment->duration_minutes }}
                                                mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 2:00 PM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">2:00 PM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['14:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-indigo-100 border-l-4 border-indigo-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-8.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-indigo-700">{{ $assignment->duration_minutes }}
                                                mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 3:00 PM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">3:00 PM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['15:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-teal-100 border-l-4 border-teal-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-7.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-teal-700">{{ $assignment->duration_minutes }}
                                                mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 4:00 PM -->
                    <div class="grid grid-cols-6 border-b border-gray-100 hover:bg-gray-50">
                        <div class="p-4 text-sm text-ayur-brown-600 font-medium">4:00 PM</div>
                        @foreach ($therapists as $therapist)
                            <div class="p-2">
                                @php
                                    $assignments =
                                        $scheduleData[$therapist->id]['timeSlots']['16:00']['assignments'] ?? collect();
                                @endphp
                                @if ($assignments->isNotEmpty())
                                    @foreach ($assignments as $assignment)
                                        <div class="bg-purple-100 border-l-4 border-purple-500 rounded-lg p-3">
                                            <div class="flex items-center mb-1">
                                                <img class="h-6 w-6 rounded-full mr-2"
                                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-9.jpg' }}"
                                                    alt="">
                                                <span
                                                    class="text-sm font-medium text-ayur-brown-800">{{ $assignment->patient->full_name }}</span>
                                            </div>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $assignment->treatment_details }} •
                                                {{ $assignment->room->room_name ?? 'Room TBD' }}</p>
                                            <p class="text-xs text-purple-700">{{ $assignment->duration_minutes }}
                                                mins</p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="border-2 border-dashed border-ayur-green-300 rounded-lg p-3 text-center">
                                        <i class="fa-solid fa-plus text-ayur-green-500 mb-1"></i>
                                        <p class="text-xs text-ayur-green-600">Available</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- APPOINTMENT LIST & THERAPIST SUMMARY -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- APPOINTMENT LIST -->
            <div id="appointmentList" class="lg:col-span-2 bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-ayur-brown-800">Today's Appointments</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-ayur-brown-600">{{ $stats['total'] }} Total</span>
                            <div class="w-2 h-2 bg-ayur-green-500 rounded-full"></div>
                            <span class="text-sm text-ayur-green-700">{{ $stats['active'] }} Active</span>
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            <span class="text-sm text-gray-600">{{ $stats['pending'] }} Pending</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                    @forelse($assignments as $assignment)
                        <div
                            class="flex items-center justify-between p-3 border border-ayur-green-200 bg-ayur-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-ayur-green-500 rounded-full mr-3"></div>
                                <img class="h-10 w-10 rounded-full mr-3"
                                    src="{{ $assignment->patient->photo_path ? asset('storage/' . $assignment->patient->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-2.jpg' }}"
                                    alt="">
                                <div>
                                    <p class="font-medium text-ayur-brown-800">
                                        {{ $assignment->patient->full_name }}</p>
                                    <p class="text-sm text-ayur-brown-600">{{ $assignment->treatment_details }}
                                        with {{ $assignment->therapist->full_name }}</p>
                                    <p class="text-xs text-ayur-brown-500">
                                        {{ $assignment->room->room_name ?? 'Room TBD' }} •
                                        {{ \Carbon\Carbon::parse($assignment->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($assignment->end_time)->format('g:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->status_badge_class }}">
                                    {{ $assignment->status_text }}
                                </span>
                                <div class="mt-1 flex space-x-1">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-800">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-800">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-ayur-brown-600">
                            <i class="fa-solid fa-calendar-xmark text-4xl mb-4"></i>
                            <p>No appointments scheduled for today</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- THERAPIST SUMMARY -->
            <div id="therapistSummary" class="bg-white rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Therapist Summary</h3>
                </div>

                <div class="p-4 space-y-4">
                    @foreach ($therapists as $therapist)
                        @php
                            $therapistAssignments = $assignments->where('therapist_id', $therapist->id);
                            $status = $therapistAssignments->where('status', 1)->isNotEmpty() ? 'busy' : 'available';
                            $statusText = $status === 'busy' ? 'Busy' : 'Available';
                            $statusClass =
                                $status === 'busy' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full mr-2"
                                        src="{{ $therapist->photo ? asset('storage/' . $therapist->photo) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg' }}"
                                        alt="">
                                    <span class="font-medium text-ayur-brown-800">{{ $therapist->full_name }}</span>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    <i class="fa-solid fa-circle text-xs mr-1"></i>
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="text-sm text-ayur-brown-600">
                                <p><span class="font-medium">Today:</span> {{ $therapistAssignments->count() }}
                                    appointments</p>
                                <p><span class="font-medium">Specialization:</span>
                                    {{ $therapist->specialty ?? 'General' }}</p>
                                <p><span class="font-medium">Next available:</span>
                                    {{ $status === 'available' ? 'Now' : 'Check schedule' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (window.innerWidth < 768 &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target) &&
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Filter drawer functionality
        document.getElementById('filterBtn').addEventListener('click', function() {
            const drawer = document.getElementById('filterDrawer');
            const drawerContent = drawer.querySelector('.fixed.right-0');
            drawer.classList.remove('hidden');
            setTimeout(() => {
                drawerContent.classList.remove('translate-x-full');
            }, 10);
        });

        function closeFilterDrawer() {
            const drawer = document.getElementById('filterDrawer');
            const drawerContent = drawer.querySelector('.fixed.right-0');
            drawerContent.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }

        function changeDate(direction) {
            const currentDate = new Date('{{ $selectedDate }}');
            currentDate.setDate(currentDate.getDate() + direction);
            const newDate = currentDate.toISOString().split('T')[0];
            window.location.href = '{{ route('doctor.therapist-schedule') }}?date=' + newDate;
        }

        // Update status functionality
        function updateAssignmentStatus(assignmentId, status) {
            fetch(`/admin/therapist-schedule/${assignmentId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                            icon: 'success',
                            timer: 2000
                        });
                        // Reload page to show updated data
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Something went wrong',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong',
                        icon: 'error'
                    });
                });
        }
    </script>
@endsection
