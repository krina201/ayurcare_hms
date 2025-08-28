@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">
        <div class="mb-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">
                    {{ session('success') }}</div>
            @endif
            {{-- @if ($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
        </div>

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
                    {{-- <a href="{{ route('therapist.assignment') }}"
                        class="py-2 px-4 border-b-2 {{ request()->routeIs('therapist.assignment') ? 'border-ayur-green-500 text-ayur-green-600' : 'border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300' }} font-medium">
                        Therapist Assignment
                    </a> --}}
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Therapist Assignment
                    </button>
                </nav>
            </div>
        </div>



        <!-- DOCTOR PROFILE SETUP FORM -->
        <div id="doctorProfileForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Doctor Profile Setup</h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                        <i class="fa-solid fa-circle mr-1 text-xs"></i> Active
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('doctor.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- COLUMN 1: Basic Information -->
                    <div class="space-y-4">
                        <div class="flex justify-center mb-6">
                            <div class="relative" id="photo-wrapper">
                                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-ayur-green-200"
                                    id="photo-border">
                                    <img id="photo-preview" src="#" alt="   "
                                        class="w-full h-full object-cover">
                                </div>
                                <button type="button" onclick="document.getElementById('photo').click()"
                                    class="absolute bottom-0 right-0 bg-ayur-green-600 text-white rounded-full p-2 shadow-md hover:bg-ayur-green-700">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                                <input type="file" id="photo" name="photo" class="hidden" accept="image/*" required
                                    onchange="previewPhoto(this)">
                            </div>
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Full Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Dr. Full Name" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Gender <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="male"
                                        class="form-radio text-ayur-green-600"
                                        {{ old('gender') == 'male' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-ayur-brown-700">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="female"
                                        class="form-radio text-ayur-green-600"
                                        {{ old('gender') == 'female' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-ayur-brown-700">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="other"
                                        class="form-radio text-ayur-green-600"
                                        {{ old('gender') == 'other' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-ayur-brown-700">Other</span>
                                </label>
                            </div>
                            @error('gender')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="doctor@example.com" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Mobile <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" name="mobile" value="{{ old('mobile') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter mobile number" pattern="[0-9]{10}" maxlength="10" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Date of Birth <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="dob" value="{{ old('dob') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Address <span
                                    class="text-red-500">*</span></label>
                            <textarea name="address"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="3" placeholder="Enter full address" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- COLUMN 2: Professional Details -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Doctor ID</label>
                            <div class="mt-1 flex">
                                <input type="text" disabled
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border"
                                    placeholder="Auto generated on save">
                                <button type="button"
                                    class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                    <i class="fa-solid fa-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Specialty <span
                                    class="text-red-500">*</span></label>
                            <select name="specialty"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                required>
                                <option value="">Select Specialty</option>
                                <option value="Kayachikitsa (Internal Medicine)"
                                    {{ old('specialty') == 'Kayachikitsa (Internal Medicine)' ? 'selected' : '' }}>
                                    Kayachikitsa (Internal Medicine)</option>
                                <option value="Shalya Tantra (Surgery)"
                                    {{ old('specialty') == 'Shalya Tantra (Surgery)' ? 'selected' : '' }}>Shalya Tantra
                                    (Surgery)</option>
                                <option value="Shalakya Tantra (ENT & Ophthalmology)"
                                    {{ old('specialty') == 'Shalakya Tantra (ENT & Ophthalmology)' ? 'selected' : '' }}>
                                    Shalakya Tantra (ENT & Ophthalmology)</option>
                                <option value="Kaumarbhritya (Pediatrics)"
                                    {{ old('specialty') == 'Kaumarbhritya (Pediatrics)' ? 'selected' : '' }}>Kaumarbhritya
                                    (Pediatrics)</option>
                                <option value="Prasuti Tantra (Obstetrics & Gynecology)"
                                    {{ old('specialty') == 'Prasuti Tantra (Obstetrics & Gynecology)' ? 'selected' : '' }}>
                                    Prasuti Tantra (Obstetrics & Gynecology)</option>
                                <option value="Panchakarma" {{ old('specialty') == 'Panchakarma' ? 'selected' : '' }}>
                                    Panchakarma</option>
                                <option value="Rasayana & Vajeekarana (Rejuvenation)"
                                    {{ old('specialty') == 'Rasayana & Vajeekarana (Rejuvenation)' ? 'selected' : '' }}>
                                    Rasayana & Vajeekarana (Rejuvenation)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Qualification <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="qualification" value="{{ old('qualification') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="BAMS, MD, PhD, etc." required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Experience (Years) <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="experience" value="{{ old('experience') }}" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Years of experience" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Registration Number <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="registration_number" value="{{ old('registration_number') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Medical council registration" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Consultation Fee (₹) <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                                <input type="number" name="consultation_fee" value="{{ old('consultation_fee') }}"
                                    min="0" step="0.01"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="0.00" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Follow-up Fee (₹) <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                                <input type="number" name="followup_fee" value="{{ old('followup_fee') }}"
                                    min="0" step="0.01"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="0.00" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Commission Model <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="commission_type" value="fixed"
                                        class="form-radio text-ayur-green-600"
                                        {{ old('commission_type') == 'fixed' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-ayur-brown-700">Fixed</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="commission_type" value="percentage"
                                        class="form-radio text-ayur-green-600"
                                        {{ old('commission_type') == 'percentage' ? 'checked' : '' }} required>
                                    <span class="ml-2 text-ayur-brown-700">Percentage</span>
                                </label>
                            </div>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" name="commission_value" value="{{ old('commission_value') }}"
                                    min="0" step="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                    placeholder="Commission value" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="commission-symbol">%</span>
                                </div>
                            </div>
                        </div>

                        <div id="available-days-container">
                            <label class="block text-sm font-medium text-ayur-brown-700">
                                Available Days <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 grid grid-cols-4 gap-2" id="available-days-wrapper">
                                @php
                                    $days = [
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday',
                                    ];
                                    $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                @endphp
                                @foreach ($days as $index => $day)
                                    <label class="inline-flex items-center bg-ayur-offwhite px-3 py-2 rounded-md">
                                        <input type="checkbox" name="available_days[]" value="{{ $day }}"
                                            class="form-checkbox text-ayur-green-600"
                                            {{ is_array(old('available_days')) && in_array($day, old('available_days')) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-ayur-brown-700">{{ $dayLabels[$index] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <!-- error will be inserted here -->
                        </div>


                    </div>
                </div>

                <!-- Schedule Section -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-ayur-brown-800 mb-4">Consultation Schedule</h4>
                    <div class="bg-ayur-offwhite rounded-lg p-4">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700">Morning Shift</label>
                                <div class="mt-2 flex space-x-4">
                                    <div class="w-1/2">
                                        <label class="block text-xs text-ayur-brown-600 mb-1">From <span
                                                class="text-red-500">*</span></label>
                                        <input type="time" name="morning_from" value="{{ old('morning_from') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                            required>
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-xs text-ayur-brown-600 mb-1">To <span
                                                class="text-red-500">*</span></label>
                                        <input type="time" name="morning_to" value="{{ old('morning_to') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700">Evening Shift</label>
                                <div class="mt-2 flex space-x-4">
                                    <div class="w-1/2">
                                        <label class="block text-xs text-ayur-brown-600 mb-1">From <span
                                                class="text-red-500">*</span></label>
                                        <input type="time" name="evening_from" value="{{ old('evening_from') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                            required>
                                    </div>
                                    <div class="w-1/2">
                                        <label class="block text-xs text-ayur-brown-600 mb-1">To <span
                                                class="text-red-500">*</span></label>
                                        <input type="time" name="evening_to" value="{{ old('evening_to') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-ayur-brown-700">Time per Consultation (minutes)
                                <span class="text-red-500">*</span></label>
                            <select name="time_per_consultation"
                                class="mt-1 block w-full md:w-1/4 rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                required>
                                <option value="">Select Duration</option>
                                <option value="15" {{ old('time_per_consultation') == '15' ? 'selected' : '' }}>15
                                </option>
                                <option value="20" {{ old('time_per_consultation') == '20' ? 'selected' : '' }}>20
                                </option>
                                <option value="30" {{ old('time_per_consultation') == '30' ? 'selected' : '' }}>30
                                </option>
                                <option value="45" {{ old('time_per_consultation') == '45' ? 'selected' : '' }}>45
                                </option>
                                <option value="60" {{ old('time_per_consultation') == '60' ? 'selected' : '' }}>60
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Specialization & Skills -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-ayur-brown-800 mb-4">Specialization & Skills</h4>
                    <div class="bg-ayur-offwhite rounded-lg p-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-ayur-brown-700">Expertise Areas <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2 grid md:grid-cols-3 gap-2">
                                @php
                                    $expertiseAreas = [
                                        'Diabetes',
                                        'Joint Pain',
                                        'Digestive Disorders',
                                        'Skin Conditions',
                                        'Stress Management',
                                        'Hypertension',
                                        'Respiratory Issues',
                                        'Weight Management',
                                        'Hormonal Imbalances',
                                        'Chronic Fatigue',
                                        'Mental Health',
                                        'Women Health',
                                    ];
                                @endphp
                                @foreach ($expertiseAreas as $area)
                                    <label class="inline-flex items-center bg-white px-3 py-2 rounded-md">
                                        <input type="checkbox" name="expertise_areas[]" value="{{ $area }}"
                                            class="form-checkbox text-ayur-green-600"
                                            {{ is_array(old('expertise_areas')) && in_array($area, old('expertise_areas')) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-ayur-brown-700">{{ $area }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Panchkarma Treatments <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2 grid md:grid-cols-3 gap-2">
                                @php
                                    $treatments = [
                                        'Vamana',
                                        'Virechana',
                                        'Basti',
                                        'Nasya',
                                        'Raktamokshana',
                                        'Shirodhara',
                                        'Abhyanga',
                                        'Pizhichil',
                                    ];
                                @endphp
                                @foreach ($treatments as $treatment)
                                    <label class="inline-flex items-center bg-white px-3 py-2 rounded-md">
                                        <input type="checkbox" name="panchkarma_treatments[]"
                                            value="{{ $treatment }}" class="form-checkbox text-ayur-green-600"
                                            {{ is_array(old('panchkarma_treatments')) && in_array($treatment, old('panchkarma_treatments')) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-ayur-brown-700">{{ $treatment }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Upload -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-ayur-brown-800 mb-4">Documents</h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div id="degree-certificate-container">
                            <label class="block text-sm font-medium text-ayur-brown-700">
                                Upload Degree Certificate <span class="text-red-500">*</span>
                            </label>
                            <div id="degree-certificate-box"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-file-pdf mx-auto text-ayur-brown-400 text-2xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500">
                                            <span>Upload a file</span>
                                            <input name="degree_certificate" type="file" class="sr-only"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG up to 5MB</p>
                                    <span id="degree-preview" class="text-sm text-gray-700"></span>
                                </div>
                            </div>
                        </div>

                        <div id="registration-certificate-container">
                            <label class="block text-sm font-medium text-ayur-brown-700">
                                Upload Registration Certificate <span class="text-red-500">*</span>
                            </label>
                            <div id="registration-certificate-box"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-file-pdf mx-auto text-ayur-brown-400 text-2xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500">
                                            <span>Upload a file</span>
                                            <input name="registration_certificate" type="file" class="sr-only"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG up to 5MB</p>
                                    <span id="registration-preview" class="text-sm text-gray-700"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("#doctorProfileForm form");

            // Image preview helper
            const previewImage = (input, previewElId) => {
                const previewEl = document.getElementById(previewElId);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewEl.src = e.target.result;
                        previewEl.style.display = "block";
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            };

            // Document preview helper (shows filename)
            const previewDocument = (input, containerId) => {
                const container = document.getElementById(containerId);
                if (input.files && input.files[0]) {
                    container.textContent = input.files[0].name;
                }
            };

            // File input change events for previews
            const photoInput = form.querySelector('#photo');
            photoInput.addEventListener('change', function() {
                const photoBorder = document.getElementById('photo-border');
                photoBorder.classList.remove('border-red-500');
                const error = document.querySelector('#photo-wrapper .error-text');
                if (error) error.remove();
                previewImage(photoInput, 'photo-preview'); // make sure you have <img id="photo-preview">
            });

            const degreeInput = form.querySelector('[name="degree_certificate"]');
            degreeInput.addEventListener('change', function() {
                const degreeBox = document.getElementById("degree-certificate-box");
                degreeBox.classList.remove("border-red-500");
                const error = document.querySelector('#degree-certificate-container .error-text');
                if (error) error.remove();
                previewDocument(degreeInput, "degree-preview"); // <span id="degree-preview"></span>
            });

            const regInput = form.querySelector('[name="registration_certificate"]');
            regInput.addEventListener('change', function() {
                const regBox = document.getElementById("registration-certificate-box");
                regBox.classList.remove("border-red-500");
                const error = document.querySelector('#registration-certificate-container .error-text');
                if (error) error.remove();
                previewDocument(regInput,
                    "registration-preview"); // <span id="registration-preview"></span>
            });

            form.addEventListener("submit", function(e) {
                let isValid = true;

                // clear old errors
                form.querySelectorAll(".error-text").forEach(el => el.remove());
                form.querySelectorAll(".border-red-500").forEach(el => {
                    el.classList.remove("border-red-500");
                });

                // loop through all required fields
                form.querySelectorAll("[required]").forEach(field => {

                    if (!field.value || (field.type === "radio" && !form.querySelector(
                            `input[name="${field.name}"]:checked`))) {
                        isValid = false;

                        // highlight invalid field
                        field.classList.add("border-red-500");

                        // show error message (skip if already exists)
                        if (!field.closest("div").querySelector(".error-text")) {
                            const error = document.createElement("p");
                            error.className = "error-text text-red-500 text-sm mt-1";
                            error.innerText = "This field is required.";
                            field.closest("div").appendChild(error);
                        }
                    }

                    // Special validation for mobile number
                    if (field.name === "mobile" && field.value) {
                        if (!/^\d{10}$/.test(field.value)) {
                            isValid = false;
                            field.classList.add("border-red-500");
                            const error = document.createElement("p");
                            error.className = "error-text text-red-500 text-sm mt-1";
                            error.innerText = "Mobile must be exactly 10 digits.";
                            field.closest("div").appendChild(error);
                        }
                    }

                    // Special validation for email
                    if (field.type === "email" && field.value) {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(field.value)) {
                            isValid = false;
                            field.classList.add("border-red-500");
                            const error = document.createElement("p");
                            error.className = "error-text text-red-500 text-sm mt-1";
                            error.innerText = "Enter a valid email address.";
                            field.closest("div").appendChild(error);
                        }
                    }
                });

                // stop submit if invalid
                if (!isValid) {
                    e.preventDefault();
                }

                const photoInput = form.querySelector('#photo');
                const photoWrapper = document.getElementById('photo-wrapper');
                const photoBorder = document.getElementById('photo-border');

                if (!photoInput.files || photoInput.files.length === 0) {
                    isValid = false;

                    // highlight border red
                    photoBorder.classList.add("border-red-500");

                    // show error below photo (only once)
                    if (!photoWrapper.querySelector(".error-text")) {
                        const error = document.createElement("p");
                        error.className = "error-text text-red-500 text-sm mt-2 text-center";
                        error.innerText = "Doctor photo is required.";
                        photoWrapper.appendChild(error);
                    }
                }

                // ---- Available Days Validation ----
                const availableDays = form.querySelectorAll('input[name="available_days[]"]');
                const daysContainer = document.getElementById('available-days-container');
                const daysWrapper = document.getElementById('available-days-wrapper');

                if (![...availableDays].some(cb => cb.checked)) {
                    isValid = false;

                    // highlight wrapper border
                    daysWrapper.classList.add("border", "border-red-500", "rounded-md", "p-2");

                    // show error outside (below container, not inside wrapper)
                    if (!daysContainer.querySelector(".error-text")) {
                        const error = document.createElement("p");
                        error.className = "error-text text-red-500 text-sm mt-1";
                        error.innerText = "Please select at least one available day.";
                        daysContainer.appendChild(error);
                    }
                } else {
                    daysWrapper.classList.remove("border", "border-red-500", "p-2");
                    const error = daysContainer.querySelector(".error-text");
                    if (error) error.remove();
                }


                // ---- Degree Certificate Validation ----
                const degreeInput = form.querySelector('[name="degree_certificate"]');
                const degreeBox = document.getElementById("degree-certificate-box");
                const degreeContainer = document.getElementById("degree-certificate-container");

                if (!degreeInput.files || degreeInput.files.length === 0) {
                    isValid = false;
                    degreeBox.classList.add("border-red-500");
                    if (!degreeContainer.querySelector(".error-text")) {
                        const error = document.createElement("p");
                        error.className = "error-text text-red-500 text-sm mt-1";
                        error.innerText = "Degree certificate is required.";
                        degreeContainer.appendChild(error);
                    }
                } else {
                    degreeBox.classList.remove("border-red-500");
                    const error = degreeContainer.querySelector(".error-text");
                    if (error) error.remove();
                }

                // ---- Registration Certificate Validation ----
                const regInput = form.querySelector('[name="registration_certificate"]');
                const regBox = document.getElementById("registration-certificate-box");
                const regContainer = document.getElementById("registration-certificate-container");

                if (!regInput.files || regInput.files.length === 0) {
                    isValid = false;
                    regBox.classList.add("border-red-500");
                    if (!regContainer.querySelector(".error-text")) {
                        const error = document.createElement("p");
                        error.className = "error-text text-red-500 text-sm mt-1";
                        error.innerText = "Registration certificate is required.";
                        regContainer.appendChild(error);
                    }
                } else {
                    regBox.classList.remove("border-red-500");
                    const error = regContainer.querySelector(".error-text");
                    if (error) error.remove();
                }

            });

        });
    </script>
@endsection
