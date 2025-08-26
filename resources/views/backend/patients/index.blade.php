@extends('backend.layouts.master')

@section('content')
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

        <div class="mb-4">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" role="tablist">
                    <button type="button" data-tab-target="registration"
                        class="tab-btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300 text-sm">
                        New Registration
                    </button>
                    <button type="button" data-tab-target="search"
                        class="tab-btn py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300 text-sm">
                        Patient Search
                    </button>
                </nav>
            </div>
        </div>

        <div id="tab-search" class="hidden">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-ayur-brown-800">Patient Search</h3>
                    <div class="flex space-x-2">
                        <button
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-download mr-1"></i> Export
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-filter mr-1"></i> More Filters
                        </button>
                    </div>
                </div>

                <form method="GET" action="{{ route('patients') }}" id="patient-search-form" class="mb-6">
                    <input type="hidden" name="tab" value="search">
                    <div class="grid md:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">UHID</label>
                            <input type="text" name="uhid" value="{{ request('uhid') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter UHID">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Patient Name</label>
                            <input type="text" name="full_name" value="{{ request('full_name') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Patient Email</label>
                            <input type="text" name="email" value="{{ request('email') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter Email">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Phone Number</label>
                            <input type="tel" name="mobile" value="{{ request('mobile') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter phone">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Visit Date</label>
                            <input type="date" name="visit_date" value="{{ request('visit_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between items-center">
                        <div class="flex flex-wrap gap-2 mb-4 md:mb-0">
                            <label class="inline-flex items-center bg-ayur-offwhite px-3 py-1.5 rounded-full">
                                <input type="checkbox" name="types[]" value="OPD"
                                    class="form-checkbox text-ayur-green-600"
                                    {{ in_array('OPD', (array) request('types', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-ayur-brown-700">OPD</span>
                            </label>
                            <label class="inline-flex items-center bg-ayur-offwhite px-3 py-1.5 rounded-full">
                                <input type="checkbox" name="types[]" value="IPD"
                                    class="form-checkbox text-ayur-green-600"
                                    {{ in_array('IPD', (array) request('types', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-ayur-brown-700">IPD</span>
                            </label>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('patients', ['tab' => 'search']) }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">

                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-stone-800">Search Results </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 example">
                        <thead class="bg-ayur-offwhite">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        UHID
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Patient Name
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Email
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Age / Gender
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Type
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Mobile
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Last Visit
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Prakriti
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse(($searchResults ?? collect()) as $p)
                                <tr class="hover:bg-ayur-offwhite">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-800">
                                        {{ $p->uhid }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <img class="h-8 w-8 rounded-full"
                                                    src="{{ $p->photo_path ? asset($p->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                                    alt="Patient Photo">

                                                {{-- <img class="h-8 w-8 rounded-full"
                                                    src="{{ $p->photo_path ? asset($p->photo_path) : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg' }}"
                                                    alt="Patient Photo"> --}}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-ayur-brown-800">
                                                    {{ $p->full_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                        {{ $p->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                        {{ $p->age }}/{{ strtoupper(substr($p->gender, 0, 1)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $p->registration_type === 'OPD' ? 'bg-ayur-green-100 text-ayur-green-800' : 'bg-ayur-yellow-100 text-ayur-yellow-800' }}">
                                            {{ $p->registration_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                        {{ $p->mobile }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                        {{ optional($p->registration_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                        {{ $p->prakriti }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('patients.edit', ['patient' => $p->getRouteKey()]) }}"
                                            class="text-stone-600 hover:text-stone-900 mr-2" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        {{-- you can add delete/view here if needed --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-ayur-brown-600">
                                        No patients found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                {{-- @if (isset($searchResults))
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-ayur-brown-600">
                            Showing <span class="font-medium">{{ $searchResults->firstItem() }}</span> to <span
                                class="font-medium">{{ $searchResults->lastItem() }}</span> of <span
                                class="font-medium">{{ $searchResults->total() }}</span> results
                        </div>
                        <div class="flex items-center space-x-2">
                            <div>{{ $searchResults->appends(request()->query())->links() }}</div>
                        </div>
                    </div>
                @endif --}}
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-ayur-brown-800">Saved Searches</h3>
                    <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                        <i class="fa-solid fa-plus mr-1"></i> Save Current Search
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium text-ayur-brown-800">Recent OPD Patients</h4>
                            <div class="flex space-x-1">
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900"><i
                                        class="fa-solid fa-pen-to-square text-xs"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-trash text-xs"></i></button>
                            </div>
                        </div>
                        <p class="text-sm text-ayur-brown-600 mt-1">Type: OPD, Last visit: Last 7 days</p>
                        <div class="mt-3">
                            <button
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Run Search
                            </button>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium text-ayur-brown-800">Active Panchkarma Patients</h4>
                            <div class="flex space-x-1">
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900"><i
                                        class="fa-solid fa-pen-to-square text-xs"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-trash text-xs"></i></button>
                            </div>
                        </div>
                        <p class="text-sm text-ayur-brown-600 mt-1">Type: Panchkarma, Status: Active</p>
                        <div class="mt-3">
                            <button
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Run Search
                            </button>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h4 class="font-medium text-ayur-brown-800">Vata Dosha Patients</h4>
                            <div class="flex space-x-1">
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900"><i
                                        class="fa-solid fa-pen-to-square text-xs"></i></button>
                                <button class="text-red-600 hover:text-red-900"><i
                                        class="fa-solid fa-trash text-xs"></i></button>
                            </div>
                        </div>
                        <p class="text-sm text-ayur-brown-600 mt-1">Prakriti: Vata or Vata-dominant</p>
                        <div class="mt-3">
                            <button
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Run Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-registration" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-stone-800">
                    {{ isset($patient) ? 'Edit Patient' : 'New Patient Registration' }} @if (isset($patient))
                        <span class="text-sm text-stone-500">({{ $patient->uhid }})</span>
                    @endif
                </h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fa-solid fa-hospital-user mr-1"></i> OPD
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <i class="fa-solid fa-bed-pulse mr-1"></i> IPD
                    </span>
                </div>
            </div>

            <form id="patient-form" method="POST"
                action="{{ isset($patient) ? route('patients.update', ['patient' => $patient->getRouteKey()]) : route('patients.store') }}"
                enctype="multipart/form-data" novalidate>
                @csrf
                @if (isset($patient))
                    @method('PUT')
                @endif
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700">Full Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="full_name" id="full_name" required
                                value="{{ old('full_name', $patient->full_name ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('full_name') border-red-500 @enderror"
                                placeholder="Enter patient name">
                            @error('full_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="email" id="email" required
                                value="{{ old('email', $patient->email ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('email') border-red-500 @enderror"
                                placeholder="Enter patient Email">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Gender <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 flex space-x-4 @error('gender') ring-1 ring-red-500 rounded-md @enderror">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="male"
                                        class="form-radio text-green-700" required @checked(old('gender', $patient->gender ?? '') === 'male')>
                                    <span class="ml-2 text-stone-700">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="female"
                                        class="form-radio text-green-700" required @checked(old('gender', $patient->gender ?? '') === 'female')>
                                    <span class="ml-2 text-stone-700">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="other"
                                        class="form-radio text-green-700" required @checked(old('gender', $patient->gender ?? '') === 'other')>
                                    <span class="ml-2 text-stone-700">Other</span>
                                </label>
                            </div>
                        </div>
                        @error('gender')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Age <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="age" id="age" required min="0" max="150"
                                value="{{ old('age', $patient->age ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('age') border-red-500 @enderror"
                                placeholder="Enter age">
                            @error('age')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Mobile <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" name="mobile" id="mobile" required pattern="^[0-9]{10}$"
                                value="{{ old('mobile', $patient->mobile ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('mobile') border-red-500 @enderror"
                                placeholder="Enter 10-digit mobile number">
                            @error('mobile')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Emergency Contact</label>
                            <input type="tel" name="emergency_contact" id="emergency_contact" required
                                pattern="^[0-9]{10}$"
                                value="{{ old('emergency_contact', $patient->emergency_contact ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('emergency_contact') border-red-500 @enderror"
                                placeholder="Emergency contact number">
                            @error('emergency_contact')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700">UHID</label>
                            <div class="mt-1 flex">
                                <input type="text" disabled
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border"
                                    value="{{ isset($patient) ? $patient->uhid : 'Auto generated' }}"
                                    placeholder="Auto generated">
                                <button type="button"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" id="aadhaar_number" required
                                pattern="^[0-9]{12}$" value="{{ old('aadhaar_number', $patient->aadhaar_number ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('aadhaar_number') border-red-500 @enderror"
                                placeholder="Enter 12-digit Aadhaar number">
                            @error('aadhaar_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Address</label>
                            <textarea name="address" id="address" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('address') border-red-500 @enderror"
                                placeholder="Enter full address">{{ old('address', $patient->address ?? '') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Allergies</label>
                            <input type="text" name="allergies" id="allergies" required
                                value="{{ old('allergies', $patient->allergies ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('allergies') border-red-500 @enderror"
                                placeholder="Known allergies, if any">
                            @error('allergies')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700">Prakriti</label>
                            <select name="prakriti" id="prakriti" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('prakriti') border-red-500 @enderror">
                                <option value="">Select Prakriti</option>
                                @foreach (['Vata', 'Pitta', 'Kapha', 'Vata-Pitta', 'Pitta-Kapha', 'Vata-Kapha', 'Vata-Pitta-Kapha'] as $p)
                                    <option value="{{ $p }}" @selected(old('prakriti', $patient->prakriti ?? '') === $p)>
                                        {{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('prakriti')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Dosha Type</label>
                            @php $selectedDoshas = collect(old('doshas', $patient->doshas ?? [])); @endphp
                            <div
                                class="mt-1 flex flex-wrap gap-2 @error('doshas') ring-1 ring-red-500 rounded-md @enderror">
                                @foreach (['Vata', 'Pitta', 'Kapha'] as $d)
                                    <label class="inline-flex items-center bg-[#f8f5f0] px-3 py-1 rounded-full">
                                        <input type="checkbox" name="doshas[]" value="{{ $d }}"
                                            @checked($selectedDoshas->contains($d)) class="form-checkbox text-green-700">
                                        <span class="ml-2 text-sm text-stone-700">{{ $d }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('doshas')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Registration Date</label>
                            <input type="date" name="registration_date" id="registration_date" required
                                value="{{ old('registration_date', isset($patient) ? optional($patient->registration_date)->format('Y-m-d') : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring focus:ring-green-200 focus:ring-opacity-50 p-2 border @error('registration_date') border-red-500 @enderror">
                            @error('registration_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700">Registration Type</label>
                            @php $selectedType = old('registration_type', $patient->registration_type ?? 'OPD'); @endphp
                            <div class="mt-1 flex space-x-2 @error('registration_type') ring-1 ring-red-500 rounded-md @enderror"
                                data-regtype>
                                <label class="flex-1">
                                    <input type="radio" name="registration_type" value="OPD" class="hidden"
                                        @checked($selectedType === 'OPD') required>
                                    <span
                                        class="inline-flex items-center px-4 py-2 w-full justify-center border text-sm font-medium rounded-md cursor-pointer {{ $selectedType === 'OPD' ? 'text-white bg-green-700' : 'text-stone-700 bg-white hover:bg-gray-50' }}">OPD</span>
                                </label>
                                <label class="flex-1">
                                    <input type="radio" name="registration_type" value="IPD" class="hidden"
                                        @checked($selectedType === 'IPD') required>
                                    <span
                                        class="inline-flex items-center px-4 py-2 w-full justify-center border text-sm font-medium rounded-md cursor-pointer {{ $selectedType === 'IPD' ? 'text-white bg-green-700' : 'text-stone-700 bg-white hover:bg-gray-50' }}">IPD</span>
                                </label>
                            </div>
                        </div>
                        @error('registration_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="pt-4">
                            <label class="block text-sm font-medium text-stone-700">Upload Photo</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('photo') ring-1 ring-red-500 @enderror"
                                id="photo-drop">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up mx-auto text-stone-400 text-2xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-green-700 hover:text-green-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-600">
                                            <span>Upload a file</span>
                                            <input id="photo" name="photo" type="file" class="sr-only"
                                                @if (!isset($patient)) required @endif accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    <div id="photo-preview-wrapper"
                                        class="mt-2 flex items-center justify-center gap-2 hidden">
                                        <img id="photo-preview" class="h-12 w-12 rounded-full object-cover"
                                            alt="">
                                        <span class="text-xs text-stone-600">Preview</span>
                                    </div>
                                    @if (isset($patient) && $patient->photo_path)
                                        <div class="mt-2 flex items-center justify-center gap-2">
                                            <img src="{{ asset($patient->photo_path) }}"
                                                class="h-12 w-12 rounded-full object-cover" alt="">
                                            <span class="text-xs text-stone-600">Current photo</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('patients') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">{{ isset($patient) ? 'Cancel' : 'Clear Form' }}</a>
                    @if (isset($patient))
                        {{-- @permission('patients.edit') --}}
                        <button id="submitBtn" type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">Update
                            Patient</button>
                        {{-- @endpermission --}}
                    @else
                        {{-- @permission('patients.create') --}}
                        <button id="submitBtn" type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">Register
                            Patient</button>
                        {{-- @endpermission --}}
                    @endif
                </div>
            </form>
        </div>

        <div id="tab-overview">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-stone-600">Today's Registrations</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $stats['todayRegistrations'] }}</p>
                            @if ($stats['todayRegistrationsTrend'])
                                <p
                                    class="text-xs mt-2 {{ $stats['todayRegistrationsTrend']['type'] === 'up' ? 'text-green-700' : ($stats['todayRegistrationsTrend']['type'] === 'down' ? 'text-red-700' : 'text-stone-600') }}">
                                    <i
                                        class="fa-solid {{ $stats['todayRegistrationsTrend']['type'] === 'up' ? 'fa-arrow-up' : ($stats['todayRegistrationsTrend']['type'] === 'down' ? 'fa-arrow-down' : 'fa-arrows-left-right') }}"></i>
                                    {{ $stats['todayRegistrationsTrend']['text'] }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-full bg-green-100 p-2 text-green-700"><i class="fa-solid fa-user-plus"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-amber-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-stone-600">Active OPD Patients</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $stats['activeOpdCount'] ?? '' }}</p>
                            @if ($stats['activeOpdTrend'] ?? '')
                                <p
                                    class="text-xs mt-2 {{ $stats['activeOpdTrend']['type'] === 'up' ? 'text-green-700' : ($stats['activeOpdTrend']['type'] === 'down' ? 'text-red-700' : 'text-amber-700') }}">
                                    <i
                                        class="fa-solid {{ $stats['activeOpdTrend']['type'] === 'up' ? 'fa-arrow-up' : ($stats['activeOpdTrend']['type'] === 'down' ? 'fa-arrow-down' : 'fa-arrows-left-right') }}"></i>
                                    {{ $stats['activeOpdTrend']['text'] }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-full bg-amber-100 p-2 text-amber-600"><i
                                class="fa-solid fa-hospital-user"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-stone-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-stone-600">Active IPD Patients</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $stats['activeIpdCount'] ?? '' }}</p>
                            @if ($stats['activeIpdTrend'] ?? '')
                                <p
                                    class="text-xs mt-2 {{ $stats['activeIpdTrend']['type'] === 'up' ? 'text-green-700' : ($stats['activeIpdTrend']['type'] === 'down' ? 'text-red-700' : 'text-stone-700') }}">
                                    <i
                                        class="fa-solid {{ $stats['activeIpdTrend']['type'] === 'up' ? 'fa-arrow-up' : ($stats['activeIpdTrend']['type'] === 'down' ? 'fa-arrow-down' : 'fa-arrows-left-right') }}"></i>
                                    {{ $stats['activeIpdTrend']['text'] }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-full bg-stone-100 p-2 text-stone-600"><i class="fa-solid fa-bed-pulse"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-stone-600">Panchkarma Treatments</p>
                            <p class="text-2xl font-bold text-stone-800">{{ $stats['panchkarmaTreatments'] ?? '—' }}
                            </p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-2 text-blue-600"><i class="fa-solid fa-spa"></i></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-stone-800">Recent Registrations</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 example">
                        <thead class="bg-ayur-offwhite">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    UHID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Patient Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Age / Gender
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Registration
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Mobile
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Prakriti
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($patients as $patient)
                                <tr class="hover:bg-ayur-offwhite">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-800">
                                        {{ $patient->uhid }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <img class="h-8 w-8 rounded-full"
                                                    src="{{ $patient->photo_path ? asset($patient->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                                    alt="Patient Photo">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-stone-800">
                                                    {{ $patient->full_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">
                                        {{ $patient->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">
                                        {{ $patient->age }}/{{ strtoupper(substr($patient->gender, 0, 1)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $type = $patient->registration_type; @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $type === 'OPD' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">
                                        {{ $patient->mobile }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">
                                        {{ $patient->prakriti }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- view button --}}
                                        <a href="{{ route('patients.show', ['patient' => $patient->getRouteKey()]) }}"
                                            class="text-ayur-green-600 hover:text-ayur-green-900 mr-2"
                                            title="View Patient Profile">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- edit button --}}
                                        <a href="{{ route('patients.edit', ['patient' => $patient->getRouteKey()]) }}"
                                            class="text-stone-600 hover:text-stone-900 mr-2" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- delete button --}}
                                        <button type="button" class="delete-patient-btn text-red-600 hover:text-red-900"
                                            data-id="{{ $patient->id }}" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-patient-form-{{ $patient->id }}"
                                            action="{{ route('patients.delete', $patient->id) }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-stone-600">
                                        No patients found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-patient-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-patient-form-' + id).action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this Patient?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            }).then(res => {
                                if (res.ok) {
                                    Swal.fire({
                                        text: 'Patient deleted successfully!',
                                        icon: 'success'
                                    }).then(() => location.reload());
                                } else {
                                    throw new Error('Failed');
                                }
                            }).catch(() => {
                                Swal.fire({
                                    text: 'Something went wrong!',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>
    <script>
        function showError(input, message) {
            const parent = input.closest('div');
            let hint = parent.querySelector('.input-error');
            if (!hint) {
                hint = document.createElement('p');
                hint.className = 'input-error mt-1 text-xs text-red-600';
                parent.appendChild(hint);
            }
            hint.textContent = message;
            input.classList.add('border-red-500');
        }

        function clearError(input) {
            const parent = input.closest('div');
            const hint = parent.querySelector('.input-error');
            if (hint) hint.remove();
            input.classList.remove('border-red-500');
        }

        function showGroupError(container, message) {
            container.classList.add('ring-1', 'ring-red-500', 'rounded-md');
            let hint = container.nextElementSibling;
            if (!hint || !hint.classList || !hint.classList.contains('group-error')) {
                hint = document.createElement('p');
                hint.className = 'group-error input-error mt-1 text-xs text-red-600';
                container.insertAdjacentElement('afterend', hint);
            }
            hint.textContent = message;
        }

        function clearGroupError(container) {
            container.classList.remove('ring-1', 'ring-red-500', 'rounded-md');
            const next = container.nextElementSibling;
            if (next && next.classList && next.classList.contains('group-error')) {
                next.remove();
            }
        }

        function validateForm() {
            const form = document.getElementById('patient-form');
            let valid = true;

            const fullName = document.getElementById('full_name');
            if (!fullName.value.trim()) {
                showError(fullName, 'Full name is required');
                valid = false;
            } else {
                clearError(fullName);
            }

            // email
            const email = fields.email && fields.email.value.trim();
            clearError('email');
            if (!email) {
                showError('email', 'Email field is required.');
                valid = false;
            } else if (!validateEmail(email)) {
                showError('email', 'Please provide a valid Email address.');
                valid = false;
            } else if (email.length > 255) {
                showError('email', 'Email may not be greater than 255 characters.');
                valid = false;
            }

            const genderChecked = !!form.querySelector('input[name="gender"]:checked');
            const genderContainer = form.querySelector('input[name="gender"]').closest('div');
            if (!genderChecked) {
                showGroupError(genderContainer, 'Gender is required');
                valid = false;
            } else {
                clearGroupError(genderContainer);
            }

            const age = document.getElementById('age');
            const ageVal = parseInt(age.value, 10);
            if (!age.value || isNaN(ageVal) || ageVal < 0 || ageVal > 150) {
                showError(age, 'Age must be between 0 and 150');
                valid = false;
            } else {
                clearError(age);
            }

            const mobile = document.getElementById('mobile');
            if (!/^[0-9]{10}$/.test(mobile.value)) {
                showError(mobile, 'Enter a valid 10-digit mobile number');
                valid = false;
            } else {
                clearError(mobile);
            }

            const emergency = document.getElementById('emergency_contact');
            if (!/^[0-9]{10}$/.test(emergency.value)) {
                showError(emergency, 'Emergency contact must be 10 digits');
                valid = false;
            } else {
                clearError(emergency);
            }

            const aadhaar = document.getElementById('aadhaar_number');
            if (!/^[0-9]{12}$/.test(aadhaar.value)) {
                showError(aadhaar, 'Aadhaar must be 12 digits');
                valid = false;
            } else {
                clearError(aadhaar);
            }

            const address = document.getElementById('address');
            if (!address.value.trim()) {
                showError(address, 'Address is required');
                valid = false;
            } else {
                clearError(address);
            }

            const allergies = document.getElementById('allergies');
            if (!allergies.value.trim()) {
                showError(allergies, 'Allergies are required');
                valid = false;
            } else {
                clearError(allergies);
            }

            const prakriti = document.getElementById('prakriti');
            if (!prakriti.value) {
                showError(prakriti, 'Prakriti is required');
                valid = false;
            } else {
                clearError(prakriti);
            }

            const regDate = document.getElementById('registration_date');
            if (!regDate.value) {
                showError(regDate, 'Registration date is required');
                valid = false;
            } else {
                clearError(regDate);
            }

            const regTypeChecked = !!form.querySelector('input[name="registration_type"]:checked');
            const regTypeContainer = form.querySelector('input[name="registration_type"]').closest('div');
            if (!regTypeChecked) {
                showGroupError(regTypeContainer, 'Registration type is required');
                valid = false;
            } else {
                clearGroupError(regTypeContainer);
            }

            // At least one dosha must be selected
            const doshaChecked = !!form.querySelector('input[name="doshas[]"]:checked');
            const doshaContainer = form.querySelector('input[name="doshas[]"]').closest('div');
            if (!doshaChecked) {
                showGroupError(doshaContainer, 'Select at least one Dosha');
                valid = false;
            } else {
                clearGroupError(doshaContainer);
            }

            // Photo required only when creating or when no existing photo
            const photo = document.getElementById('photo');
            const photoContainer = document.getElementById('photo-drop') || photo.closest('.border-dashed') || photo
                .closest('div');
            const hasExistingPhoto = @json(isset($patient) && $patient->photo_path);
            if (!hasExistingPhoto && (!photo.files || photo.files.length === 0)) {
                showGroupError(photoContainer, 'Photo is required');
                valid = false;
            } else {
                clearGroupError(photoContainer);
            }

            return valid;
        }

        document.getElementById('patient-form').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // Real-time clearing
        document.getElementById('full_name').addEventListener('input', e => clearError(e.target));
        document.getElementById('email').addEventListener('input', e => clearError(e.target));
        document.getElementById('age').addEventListener('input', e => clearError(e.target));
        document.getElementById('mobile').addEventListener('input', e => clearError(e.target));
        document.getElementById('emergency_contact').addEventListener('input', e => clearError(e.target));
        document.getElementById('aadhaar_number').addEventListener('input', e => clearError(e.target));
        document.getElementById('address').addEventListener('input', e => clearError(e.target));
        document.getElementById('allergies').addEventListener('input', e => clearError(e.target));
        document.getElementById('prakriti').addEventListener('change', e => clearError(e.target));
        document.getElementById('registration_date').addEventListener('change', e => clearError(e.target));
        document.querySelectorAll('input[name="gender"]').forEach(r => r.addEventListener('change', () => {
            const c = r.closest('div');
            clearGroupError(c);
        }));
        document.querySelectorAll('input[name="registration_type"]').forEach(r => r.addEventListener('change', () => {
            const c = r.closest('div');
            clearGroupError(c);
        }));
        document.querySelectorAll('input[name="doshas[]"]').forEach(cbx => cbx.addEventListener('change', () => {
            const c = cbx.closest('div');
            clearGroupError(c);
        }));
        document.getElementById('photo').addEventListener('change', e => {
            const c = e.target.closest('.border-dashed') || e.target.closest('div');
            clearGroupError(c);
            const file = e.target.files && e.target.files[0];
            const previewImg = document.getElementById('photo-preview');
            const previewWrap = document.getElementById('photo-preview-wrapper');
            if (file && previewImg && previewWrap) {
                const url = URL.createObjectURL(file);
                previewImg.src = url;
                previewWrap.classList.remove('hidden');
                previewImg.onload = () => URL.revokeObjectURL(url);
            }
        });

        // Tabs logic
        (function() {
            const buttons = Array.from(document.querySelectorAll('.tab-btn'));
            const reg = document.getElementById('tab-registration');
            const search = document.getElementById('tab-search');
            const urlParams = new URLSearchParams(window.location.search);
            const initial = urlParams.get('tab') === 'search' ? 'search' : 'registration';

            function activate(which) {
                if (which === 'search') {
                    reg.classList.add('hidden');
                    search.classList.remove('hidden');
                    const ov = document.getElementById('tab-overview');
                    if (ov) ov.classList.add('hidden');
                } else {
                    search.classList.add('hidden');
                    reg.classList.remove('hidden');
                    const ov = document.getElementById('tab-overview');
                    if (ov) ov.classList.remove('hidden');
                }
                buttons.forEach(b => {
                    const isActive = b.getAttribute('data-tab-target') === which;
                    b.classList.toggle('border-ayur-green-500', isActive);
                    b.classList.toggle('text-ayur-green-600', isActive);
                    b.classList.toggle('font-medium', isActive);
                    b.classList.toggle('border-transparent', !isActive);
                    b.classList.toggle('text-ayur-brown-600', !isActive);
                });
            }
            activate(initial);
            buttons.forEach(b => b.addEventListener('click', () => activate(b.getAttribute('data-tab-target'))));
        })();

        // Toggle active styles for OPD/IPD buttons on click
        document.querySelectorAll('[data-regtype] input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const container = radio.closest('[data-regtype]');
                const labels = container.querySelectorAll('label');
                labels.forEach(label => {
                    const input = label.querySelector('input[type="radio"]');
                    const span = label.querySelector('span');
                    if (input.checked) {
                        span.classList.remove('text-stone-700', 'bg-white', 'hover:bg-gray-50');
                        span.classList.add('text-white', 'bg-green-700');
                    } else {
                        span.classList.remove('text-white', 'bg-green-700');
                        span.classList.add('text-stone-700', 'bg-white', 'hover:bg-gray-50');
                    }
                });
            });
        });
    </script>
@endpush
