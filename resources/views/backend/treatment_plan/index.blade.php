@extends('backend.layouts.master')

@section('content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- MAIN CONTENT -->
    <main class="p-4">
        <!-- TABS -->
        <div id="panchkarmaTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Treatment Plan Creation
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Day-wise Tracker
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Outcome &amp; Feedback
                    </button>
                </nav>
            </div>
        </div>

        <!-- TREATMENT PLAN CREATION FORM -->
        <div id="treatmentPlanForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Create Treatment Plan</h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                        <i class="fa-solid fa-spa mr-1"></i> Panchkarma
                    </span>
                </div>
            </div>

            <form id="treatmentPlanForm">
                <input type="hidden" id="patientId" name="patient_id">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- COLUMN 1 -->
                    <div class="space-y-4">
                        <div id="patientSearchContainer" class="relative">
                            <label class="block text-sm font-medium text-ayur-brown-700">Patient <span
                                    class="text-red-500">*</span></label>

                            <!-- Patient Selection Tabs -->
                            <div class="mt-1 mb-2">
                                <div class="flex space-x-1">
                                    <button type="button" id="selectTab"
                                        class="px-3 py-1 text-xs font-medium rounded-md bg-ayur-green-100 text-ayur-green-700 border border-ayur-green-300">
                                        Select
                                    </button>
                                    <button type="button" id="searchTab"
                                        class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-700 border border-gray-300">
                                        Search
                                    </button>
                                </div>
                            </div>

                            <!-- Patient Select Dropdown -->
                            <div id="patientSelectContainer" class="mt-1">
                                <select id="patientSelect" name="patient_select"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}" data-uhid="{{ $patient->uhid }}"
                                            data-name="{{ $patient->full_name }}" data-gender="{{ $patient->gender }}"
                                            data-age="{{ $patient->age }}" data-mobile="{{ $patient->mobile }}"
                                            data-prakriti="{{ $patient->prakriti }}"
                                            data-allergies="{{ $patient->allergies }}"
                                            data-photo="{{ $patient->photo_path }}">
                                            {{ $patient->full_name }} ({{ $patient->uhid }}) - {{ $patient->mobile }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Patient Search Input -->
                            <div id="patientSearchInputContainer" class="mt-1 hidden">
                                <div class="flex">
                                    <input type="text" id="patientSearch"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                        placeholder="UHID, Name or Mobile">
                                    <button type="button"
                                        class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                                <div id="patientSearchResults"
                                    class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                </div>
                            </div>
                        </div>

                        <div id="patientDisplay">
                            <!-- Patient display will be populated by JavaScript -->
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Treatment Category <span
                                    class="text-red-500">*</span></label>
                            <select id="treatmentCategory" name="treatment_category"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Category</option>
                                @foreach ($treatmentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Procedure Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="procedureName" name="procedure_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter procedure name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Treatment Duration <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-ayur-brown-600 mb-1">Start Date</label>
                                    <input type="date" id="startDate" name="start_date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                </div>
                                <div>
                                    <label class="block text-xs text-ayur-brown-600 mb-1">End Date</label>
                                    <input type="date" id="endDate" name="end_date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Recommended
                                Therapist</label>
                            <select id="recommendedTherapist" name="recommended_therapist"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Therapist</option>
                                @foreach ($therapists as $therapist)
                                    <option value="{{ $therapist->id }}">{{ $therapist->full_name }}
                                        ({{ $therapist->specialty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Room Allocation</label>
                            <select id="roomAllocation" name="room_allocation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->room_number }} -
                                        {{ $room->room_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- COLUMN 2 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Dosha Report <span
                                    class="text-red-500">*</span></label>
                            <textarea id="doshaReport" name="dosha_report"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="3" placeholder="Enter dosha imbalance details and assessment"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Oils Required <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 space-y-2">
                                @foreach ($oils as $oil)
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="oils_required[]" value="{{ $oil->id }}"
                                            class="form-checkbox text-ayur-green-600 rounded">
                                        <span class="text-sm text-ayur-brown-700">{{ $oil->name }}</span>
                                    </div>
                                @endforeach
                                <div class="flex items-center space-x-2 mt-2">
                                    <input type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                        placeholder="Add other oils">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Herbs Required</label>
                            <div class="mt-1 space-y-2">
                                @foreach ($herbs as $herb)
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="herbs_required[]" value="{{ $herb->id }}"
                                            class="form-checkbox text-ayur-green-600 rounded">
                                        <span class="text-sm text-ayur-brown-700">{{ $herb->name }}</span>
                                    </div>
                                @endforeach
                                <div class="flex items-center space-x-2 mt-2">
                                    <input type="text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                        placeholder="Add other herbs">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Special
                                Instructions</label>
                            <textarea id="specialInstructions" name="special_instructions"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="2" placeholder="Enter any special instructions for the therapist"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Consent Upload <span
                                    class="text-red-500">*</span></label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-file-signature mx-auto text-ayur-brown-400 text-2xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-ayur-green-600 hover:text-ayur-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-ayur-green-500">
                                            <span>Upload consent form</span>
                                            <input id="consentFile" name="consent_file" type="file" class="sr-only"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                        </div>

                        <div id="consentPreview">
                            <!-- Consent file preview will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-medium text-ayur-brown-800 mb-4">Day-wise Treatment Schedule</h4>

                    <div id="dayWiseSchedule" class="space-y-4">
                        <!-- Day-wise schedule will be populated by JavaScript -->
                    </div>

                    <button type="button"
                        class="add-more-days-btn flex items-center justify-center w-full py-2 border border-dashed border-ayur-green-500 rounded-md text-ayur-green-600 hover:bg-ayur-green-50">
                        <i class="fa-solid fa-plus mr-2"></i> Add More Days
                    </button>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button"
                        class="save-draft-btn inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Save as Draft
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        Create Treatment Plan
                    </button>
                </div>
            </form>
        </div>

        <!-- RECENT TREATMENT PLANS -->
        <div id="recentTreatmentPlans" class="bg-white rounded-lg shadow-md p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-ayur-brown-800">Recent Treatment Plans</h3>
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
                                Duration</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Therapist</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentTreatmentPlans as $plan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full"
                                                src="{{ $plan->patient->photo_path ? '/storage/' . $plan->patient->photo_path : '/backend-assets/media/default-avatar.png' }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $plan->patient->full_name }}</div>
                                            <div class="text-xs text-ayur-brown-600">{{ $plan->patient->uhid }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-ayur-brown-800">{{ $plan->procedure_name }}</div>
                                    <div class="text-xs text-ayur-brown-600">{{ $plan->treatmentCategory->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-ayur-brown-800">{{ $plan->start_date->format('d M') }} -
                                        {{ $plan->end_date->format('d M Y') }}</div>
                                    <div class="text-xs text-ayur-brown-600">{{ $plan->duration_text }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                    {{ $plan->recommended_therapist ? \App\Models\Doctor::find($plan->recommended_therapist)->full_name ?? 'N/A' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plan->status_class }}">
                                        {{ $plan->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        onclick="viewTreatmentPlan({{ $plan->id }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"
                                        onclick="editTreatmentPlan({{ $plan->id }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900"
                                        onclick="deleteTreatmentPlan({{ $plan->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No treatment plans found
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
    <script src="{{ asset('backend-assets/js/validation/treatment_plan/treatmentPlanList.js') }}"></script>
@endsection
