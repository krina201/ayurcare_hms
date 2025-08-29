@extends('backend.layouts.master')

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
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Appointment Calendar
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Queue Manager
                    </button>
                </nav>
            </div>
        </div>

        <!-- BOOKING FORM -->
        <div id="bookingForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
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

            <form>
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
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center">
                                    <i class="fa-solid fa-user-plus mr-2"></i> New
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Search Patient</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400"></i>
                                </div>
                                <input type="text"
                                    class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="UHID, Name or Mobile">
                            </div>
                        </div>

                        <div class="border rounded-md p-4 bg-ayur-offwhite">
                            <div class="flex items-center mb-2">
                                <img class="h-10 w-10 rounded-full mr-3"
                                    src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg"
                                    alt="Patient avatar">
                                <div>
                                    <h5 class="font-medium text-ayur-brown-800">Priya Sharma</h5>
                                    <p class="text-xs text-ayur-brown-600">AYR-2025-0041 • 28/F</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                <div>
                                    <span class="text-ayur-brown-600">Mobile:</span>
                                    <span class="text-ayur-brown-800 ml-1">9876543210</span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Prakriti:</span>
                                    <span class="text-ayur-brown-800 ml-1">Pitta-Kapha</span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Last Visit:</span>
                                    <span class="text-ayur-brown-800 ml-1">12-Jul-2025</span>
                                </div>
                                <div>
                                    <span class="text-ayur-brown-600">Allergies:</span>
                                    <span class="text-red-600 ml-1">Dairy</span>
                                </div>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="button" class="text-xs text-ayur-green-600 hover:text-ayur-green-700">
                                    View Profile <i class="fa-solid fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Appointment
                                Mode</label>
                            <div class="flex space-x-2">
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center">
                                    <i class="fa-solid fa-hospital-user mr-2"></i> OPD
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 flex-1 justify-center">
                                    <i class="fa-solid fa-spa mr-2"></i> Panchkarma
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 2: DOCTOR SELECTION -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-ayur-brown-800 border-b border-gray-200 pb-2">2. Select
                            Department &amp; Doctor</h4>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Department</label>
                            <select
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option>Select Department</option>
                                <option>General Ayurveda</option>
                                <option>Panchakarma</option>
                                <option>Kayachikitsa</option>
                                <option>Shalya Tantra</option>
                                <option>Rasayana</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Doctor</label>
                            <div class="grid grid-cols-1 gap-2">
                                <div class="border rounded-md p-3 bg-ayur-green-50 border-ayur-green-200 cursor-pointer">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full mr-3"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-3.jpg"
                                            alt="Doctor avatar">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-ayur-brown-800">Dr. Rahul Sharma</h5>
                                            <p class="text-xs text-ayur-brown-600">MD Ayurveda • General Ayurveda
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center justify-center h-5 w-5 rounded-full bg-ayur-green-500 text-white">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs flex justify-between">
                                        <span class="text-ayur-brown-600">Consultation Fee: <span
                                                class="font-medium text-ayur-brown-800">₹800</span></span>
                                        <span class="text-ayur-green-600">Available Today</span>
                                    </div>
                                </div>

                                <div
                                    class="border rounded-md p-3 hover:bg-ayur-green-50 hover:border-ayur-green-200 cursor-pointer">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full mr-3"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-9.jpg"
                                            alt="Doctor avatar">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-ayur-brown-800">Dr. Ananya Patel</h5>
                                            <p class="text-xs text-ayur-brown-600">MD Ayurveda • Kayachikitsa</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs flex justify-between">
                                        <span class="text-ayur-brown-600">Consultation Fee: <span
                                                class="font-medium text-ayur-brown-800">₹700</span></span>
                                        <span class="text-ayur-green-600">Available Today</span>
                                    </div>
                                </div>

                                <div
                                    class="border rounded-md p-3 hover:bg-ayur-green-50 hover:border-ayur-green-200 cursor-pointer">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full mr-3"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-4.jpg"
                                            alt="Doctor avatar">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-ayur-brown-800">Dr. Vikram Singh</h5>
                                            <p class="text-xs text-ayur-brown-600">MD Ayurveda • Panchakarma</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs flex justify-between">
                                        <span class="text-ayur-brown-600">Consultation Fee: <span
                                                class="font-medium text-ayur-brown-800">₹900</span></span>
                                        <span class="text-ayur-yellow-600">Available Tomorrow</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Consultation
                                Type</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div
                                    class="border rounded-md p-2 bg-ayur-green-50 border-ayur-green-200 flex items-center justify-center cursor-pointer">
                                    <i class="fa-solid fa-user-doctor text-ayur-green-600 mr-2"></i>
                                    <span class="text-sm text-ayur-brown-800">First Visit</span>
                                </div>
                                <div
                                    class="border rounded-md p-2 hover:bg-ayur-green-50 hover:border-ayur-green-200 flex items-center justify-center cursor-pointer">
                                    <i class="fa-solid fa-rotate text-ayur-brown-600 mr-2"></i>
                                    <span class="text-sm text-ayur-brown-800">Follow Up</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 3: TIME SLOT SELECTION -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-ayur-brown-800 border-b border-gray-200 pb-2">3. Select Date
                            &amp; Time Slot</h4>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Date</label>
                            <input type="date"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-ayur-brown-700">Available Time
                                    Slots</label>
                                <span class="text-xs text-ayur-green-600">Dr. Rahul Sharma • 16 July 2025</span>
                            </div>

                            <div class="border rounded-md p-3 bg-white">
                                <div class="mb-2">
                                    <h6 class="text-sm font-medium text-ayur-brown-800">Morning</h6>
                                    <div class="grid grid-cols-4 gap-2 mt-1">
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer border border-ayur-green-300">
                                            09:00 AM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            09:30 AM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            10:00 AM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            10:30 AM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            11:00 AM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            11:30 AM
                                        </div>
                                        <div class="bg-gray-200 text-gray-500 rounded p-1 text-center text-xs">
                                            12:00 PM
                                        </div>
                                        <div class="bg-gray-200 text-gray-500 rounded p-1 text-center text-xs">
                                            12:30 PM
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h6 class="text-sm font-medium text-ayur-brown-800">Evening</h6>
                                    <div class="grid grid-cols-4 gap-2 mt-1">
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            04:00 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            04:30 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            05:00 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            05:30 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            06:00 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            06:30 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            07:00 PM
                                        </div>
                                        <div
                                            class="bg-ayur-green-100 text-ayur-green-800 rounded p-1 text-center text-xs cursor-pointer">
                                            07:30 PM
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-ayur-offwhite rounded-md p-4 border border-gray-200">
                            <h5 class="font-medium text-ayur-brown-800 mb-2">Appointment Summary</h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Patient:</span>
                                    <span class="text-ayur-brown-800">Priya Sharma</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Doctor:</span>
                                    <span class="text-ayur-brown-800">Dr. Rahul Sharma</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Date &amp; Time:</span>
                                    <span class="text-ayur-brown-800">16 Jul 2025, 09:00 AM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Type:</span>
                                    <span class="text-ayur-brown-800">OPD - First Visit</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-ayur-brown-600">Fee:</span>
                                    <span class="font-medium text-ayur-brown-800">₹800</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Chief
                                Complaint</label>
                            <textarea
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="2" placeholder="Enter patient's chief complaint"></textarea>
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
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Today's Appointments</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">32</p>
                    </div>
                    <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-green-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> 12% from yesterday
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Available Slots</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">56</p>
                    </div>
                    <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-yellow-600 mt-2">
                    <i class="fa-solid fa-arrows-left-right"></i> All doctors combined
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-brown-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Waiting Patients</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">8</p>
                    </div>
                    <div class="rounded-full bg-ayur-brown-100 p-2 text-ayur-brown-600">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-brown-600 mt-2">
                    <i class="fa-solid fa-arrow-down"></i> 2 less than usual
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Completed Today</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">14</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> 44% of scheduled
                </p>
            </div>
        </div>

        <!-- TODAY'S APPOINTMENTS -->
        <div id="todaysAppointments" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Today's Appointments</h3>
                <div class="flex items-center">
                    <div class="relative mr-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text"
                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1.5 border text-sm"
                            placeholder="Search appointments">
                    </div>
                    <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                        View All <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>
                </div>
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
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">09:00 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Priya Sharma</div>
                                        <div class="text-xs text-ayur-brown-600">AYR-2025-0041 • 28/F</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ayur-brown-800">Dr. Rahul Sharma</div>
                                <div class="text-xs text-ayur-brown-600">General Ayurveda</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">OPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Waiting</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-pen-to-square"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-times-circle"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">09:30 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-2.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Rajesh Kumar</div>
                                        <div class="text-xs text-ayur-brown-600">AYR-2025-0040 • 45/M</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ayur-brown-800">Dr. Ananya Patel</div>
                                <div class="text-xs text-ayur-brown-600">Kayachikitsa</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">Panchkarma</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">In
                                    Progress</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-pen-to-square"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-times-circle"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">10:00 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-7.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Meera Patel</div>
                                        <div class="text-xs text-ayur-brown-600">AYR-2025-0039 • 32/F</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ayur-brown-800">Dr. Rahul Sharma</div>
                                <div class="text-xs text-ayur-brown-600">General Ayurveda</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">OPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-pen-to-square"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-times-circle"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">10:30 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-8.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Arjun Desai</div>
                                        <div class="text-xs text-ayur-brown-600">AYR-2025-0038 • 52/M</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-ayur-brown-800">Dr. Vikram Singh</div>
                                <div class="text-xs text-ayur-brown-600">Panchakarma</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">Panchkarma</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-pen-to-square"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-times-circle"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
