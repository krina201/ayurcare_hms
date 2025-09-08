@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">

        <!-- TABS -->
        <div id="doctorTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('doctor') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Doctor Profile Setup
                    </a>
                    <a href="{{ route('doctor.dashboard') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor.dashboard') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Doctor Dashboard
                    </a>
                    <a href="{{ route('doctor.therapist-assignment') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('doctor.therapist-assignment') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Therapist Assignment
                    </a>

                </nav>
            </div>
        </div>
        <!-- DOCTOR PROFILE HEADER -->
        <div id="doctorProfileHeader" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    {{-- Use the doctor's photo from the database --}}
                    <img class="h-16 w-16 rounded-full border-2 border-ayur-green-500 object-cover"
                        src="{{ asset($doctor->photo) }}" alt="Photo of {{ $doctor->full_name }}">
                    <div class="ml-4">
                        {{-- Display doctor's full name --}}
                        <h3 class="text-xl font-semibold text-ayur-brown-800">{{ $doctor->full_name }}</h3>

                        {{-- Display specialty and qualification --}}
                        <p class="text-ayur-brown-600">{{ $doctor->specialty }}, {{ $doctor->qualification }}</p>

                        <div class="flex items-center mt-1">
                            {{-- Status can be dynamic later if you add it to the DB --}}
                            @if ($doctor->status == 1)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800 mr-2">
                                    <i class="fa-solid fa-circle-check mr-1 text-ayur-green-600"></i> Verified
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                    <i class="fa-solid fa-circle-xmark mr-1 text-red-600"></i> Not Verified
                                </span>
                            @endif

                            {{-- Display the unique doctor ID --}}
                            <span class="text-sm text-ayur-brown-600">ID: {{ $doctor->doctor_id }}</span>
                        </div>
                    </div>
                </div>

                <div class="md:ml-auto flex flex-wrap gap-3">
                    <button
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-calendar-plus mr-2"></i> Set Schedule
                    </button>
                    <button
                        class="inline-flex items-center px-4 py-2 border border-ayur-green-600 text-sm font-medium rounded-md text-ayur-green-700 bg-white hover:bg-ayur-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- PERFORMANCE METRICS -->
        <div id="performanceMetrics" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Patients Seen (This Month)</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">142</p>
                    </div>
                    <div class="rounded-full bg-ayur-green-100 p-2 text-ayur-green-600">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-green-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> 12% from last month
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-yellow-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Average Consultation Time</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">24 min</p>
                    </div>
                    <div class="rounded-full bg-ayur-yellow-100 p-2 text-ayur-yellow-600">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-yellow-600 mt-2">
                    <i class="fa-solid fa-arrow-down"></i> 3 min less than last month
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-ayur-brown-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Medicine Cost Prescribed</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹86,450</p>
                    </div>
                    <div class="rounded-full bg-ayur-brown-100 p-2 text-ayur-brown-600">
                        <i class="fa-solid fa-mortar-pestle"></i>
                    </div>
                </div>
                <p class="text-xs text-ayur-brown-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> 8% from last month
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-ayur-brown-600">Net Revenue Generated</p>
                        <p class="text-2xl font-bold text-ayur-brown-800">₹2,18,600</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fa-solid fa-arrow-up"></i> 15% from last month
                </p>
            </div>
        </div>

        <!-- CHARTS SECTION -->
        <div id="chartsSection" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- PATIENTS TREND CHART -->
            <div id="patientsTrendChart"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-ayur-brown-800 mb-1">Patients Trend</h3>
                        <p class="text-sm text-ayur-brown-600">Last 6 months performance</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="bg-ayur-green-100 p-2 rounded-full">
                            <i class="fa-solid fa-chart-line text-ayur-green-600"></i>
                        </div>
                        <select
                            class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:border-ayur-green-500 focus:ring-1 focus:ring-ayur-green-200">
                            <option>6 Months</option>
                            <option>3 Months</option>
                            <option>1 Year</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-ayur-green-500 rounded-full mr-2"></div>
                        <span class="text-xs text-ayur-brown-600">OPD Patients</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-ayur-yellow-500 rounded-full mr-2"></div>
                        <span class="text-xs text-ayur-brown-600">IPD Patients</span>
                    </div>
                </div>

                <div id="patientsChart" class="h-[280px]"></div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-ayur-green-600">548</p>
                            <p class="text-xs text-ayur-brown-600">Total OPD</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-ayur-yellow-600">147</p>
                            <p class="text-xs text-ayur-brown-600">Total IPD</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REVENUE CHART -->
            <div id="revenueChart" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-ayur-brown-800 mb-1">Revenue Breakdown</h3>
                        <p class="text-sm text-ayur-brown-600">Current month distribution</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="bg-blue-100 p-2 rounded-full">
                            <i class="fa-solid fa-chart-pie text-blue-600"></i>
                        </div>
                        <select
                            class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:border-ayur-green-500 focus:ring-1 focus:ring-ayur-green-200">
                            <option>This Month</option>
                            <option>Last Month</option>
                            <option>Quarter</option>
                        </select>
                    </div>
                </div>

                <div id="revenuePieChart" class="h-[280px]"></div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-ayur-green-500 rounded-full mr-2"></div>
                                <span class="text-ayur-brown-600">Consultations</span>
                            </div>
                            <span class="font-semibold text-ayur-brown-800">₹85K</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-ayur-yellow-500 rounded-full mr-2"></div>
                                <span class="text-ayur-brown-600">Panchkarma</span>
                            </div>
                            <span class="font-semibold text-ayur-brown-800">₹68K</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-ayur-brown-500 rounded-full mr-2"></div>
                                <span class="text-ayur-brown-600">Medicine</span>
                            </div>
                            <span class="font-semibold text-ayur-brown-800">₹43K</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-ayur-brown-600">Tests</span>
                            </div>
                            <span class="font-semibold text-ayur-brown-800">₹23K</span>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="text-lg font-bold text-ayur-brown-800">₹2,18,600</p>
                        <p class="text-xs text-ayur-brown-600">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- UPCOMING APPOINTMENTS -->
        <div id="upcomingAppointments" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Today's Appointments</h3>
                <div class="flex items-center">
                    <span class="text-sm text-ayur-brown-600 mr-3">July 16, 2025</span>
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
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Reason</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">09:30 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Priya Sharma</div>
                                        <div class="text-xs text-ayur-brown-500">28/F • AYR-2025-0041</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">OPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Joint Pain &amp;
                                Stiffness</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">In
                                    Progress</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-notes-medical"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-prescription"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">10:15 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-2.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Rajesh Kumar</div>
                                        <div class="text-xs text-ayur-brown-500">45/M • AYR-2025-0040</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">IPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Chronic Digestive
                                Issues</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Waiting</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-notes-medical"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-prescription"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">11:30 AM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-7.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Meera Patel</div>
                                        <div class="text-xs text-ayur-brown-500">32/F • AYR-2025-0039</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">OPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Skin Allergy &amp;
                                Rashes</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Waiting</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-notes-medical"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-prescription"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">02:00 PM</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-8.jpg"
                                            alt="">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-ayur-brown-800">Arjun Desai</div>
                                        <div class="text-xs text-ayur-brown-500">52/M • AYR-2025-0038</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">IPD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Diabetes Management
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Scheduled</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-notes-medical"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-prescription"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TREATMENT EFFECTIVENESS -->
        <div id="treatmentEffectiveness" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Treatment Effectiveness</h3>
                <div class="flex items-center">
                    <select
                        class="text-sm text-ayur-brown-700 border border-gray-300 rounded-md shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1">
                        <option>Last 30 Days</option>
                        <option>Last 3 Months</option>
                        <option>Last 6 Months</option>
                        <option>Last Year</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-ayur-brown-700 font-medium">Digestive Disorders</span>
                        <div class="bg-ayur-green-500 h-8 w-8 rounded-full flex items-center justify-center text-white">
                            <i class="fa-solid fa-stomach"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-bold mb-2 text-ayur-brown-800">87%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-ayur-green-500 h-2 rounded-full" style="width: 87%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-ayur-brown-600">
                        <span>38 patients</span>
                        <span>33 improved</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-ayur-brown-700 font-medium">Skin Conditions</span>
                        <div class="bg-ayur-yellow-500 h-8 w-8 rounded-full flex items-center justify-center text-white">
                            <i class="fa-solid fa-allergies"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-bold mb-2 text-ayur-brown-800">76%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-ayur-yellow-500 h-2 rounded-full" style="width: 76%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-ayur-brown-600">
                        <span>25 patients</span>
                        <span>19 improved</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-ayur-brown-700 font-medium">Joint &amp; Muscular Pain</span>
                        <div class="bg-ayur-brown-500 h-8 w-8 rounded-full flex items-center justify-center text-white">
                            <i class="fa-solid fa-bone"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-bold mb-2 text-ayur-brown-800">92%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-ayur-brown-500 h-2 rounded-full" style="width: 92%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-ayur-brown-600">
                        <span>48 patients</span>
                        <span>44 improved</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('scripts')
@endsection
