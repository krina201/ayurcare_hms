@extends('backend.layouts.master')

@section('styles')
    <style>
        /* Validation Error Styling */
        .validation-error {
            animation: slideInError 0.3s ease-out;
        }

        @keyframes slideInError {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Field Error States */
        .border-red-500 {
            border-color: #EF4444 !important;
        }

        .border-green-500 {
            border-color: #10B981 !important;
        }

        .focus\:border-red-500:focus {
            border-color: #EF4444 !important;
        }

        .focus\:ring-red-200:focus {
            --tw-ring-color: rgba(239, 68, 68, 0.2);
        }

        /* Enhanced focus styles for validation */
        input.border-red-500:focus,
        select.border-red-500:focus,
        textarea.border-red-500:focus {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        input.border-green-500:focus,
        select.border-green-500:focus,
        textarea.border-green-500:focus {
            border-color: #10B981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Checkbox group validation styling */
        .grid.border-red-500 {
            background-color: rgba(239, 68, 68, 0.05);
        }

        .grid.border-green-500 {
            background-color: rgba(16, 185, 129, 0.05);
        }

        /* File upload validation styling */
        .border-dashed.border-red-500 {
            background-color: rgba(239, 68, 68, 0.05);
        }

        .border-dashed.border-green-500 {
            background-color: rgba(16, 185, 129, 0.05);
        }

        /* Ensure validation errors are visible */
        .validation-error {
            z-index: 10;
            position: relative;
        }
    </style>
@endsection

@section('content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        <div id="panchkarmaTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="{{ route('treatment-plan') }}"
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Treatment Plan Creation
                    </a>
                    <a href="{{ route('treatment-plan .tracker') }}"
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

        <!-- TREATMENT PLAN CREATION FORM -->
        @php $isEdit = isset($editTreatmentPlan); @endphp
        <div id="treatmentPlanForm" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-ayur-brown-800">
                    {{ $isEdit ? 'Edit Treatment Plan' : 'Create Treatment Plan' }}
                </h3>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-ayur-green-100 text-ayur-green-800">
                        <i class="fa-solid fa-spa mr-1"></i> Panchkarma
                    </span>
                </div>
            </div>

            <form id="treatmentPlanForm" method="POST"
                action="{{ $isEdit ? route('treatment-plan.update', $editTreatmentPlan->id) : route('treatment-plan.store') }}"
                enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif
                <input type="hidden" id="patientId" name="patient_id"
                    value="{{ $isEdit ? $editTreatmentPlan->patient_id : '' }}">
                <input type="hidden" id="dayWiseScheduleInput" name="day_wise_schedule" value="">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- COLUMN 1 -->
                    <div class="space-y-4">
                        <div id="patientSearchContainer" class="relative">
                            <label class="block text-sm font-medium text-ayur-brown-700">Patient <span
                                    class="text-red-500">*</span></label>

                            <!-- Patient Search Input -->
                            <div id="patientSearchInputContainer" class="mt-1">
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
                                    <option value="{{ $category->id }}"
                                        {{ $isEdit && $editTreatmentPlan->treatment_category == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Procedure Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="procedureName" name="procedure_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                placeholder="Enter procedure name"
                                value="{{ $isEdit ? $editTreatmentPlan->procedure_name : '' }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Treatment Duration <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-ayur-brown-600 mb-1">Start Date</label>
                                    <input type="date" id="startDate" name="start_date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                        value="{{ $isEdit && $editTreatmentPlan->start_date ? $editTreatmentPlan->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs text-ayur-brown-600 mb-1">End Date</label>
                                    <input type="date" id="endDate" name="end_date"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                        value="{{ $isEdit && $editTreatmentPlan->end_date ? $editTreatmentPlan->end_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            @if ($isEdit && $editTreatmentPlan->start_date && $editTreatmentPlan->end_date)
                                <div class="mt-2 p-2 bg-ayur-offwhite rounded-md">
                                    <p class="text-xs text-ayur-brown-600">
                                        <i class="fa-solid fa-calendar-days mr-1"></i>
                                        Current Duration: {{ $editTreatmentPlan->start_date->format('M d, Y') }} -
                                        {{ $editTreatmentPlan->end_date->format('M d, Y') }}
                                        ({{ $editTreatmentPlan->start_date->diffInDays($editTreatmentPlan->end_date) + 1 }}
                                        days)
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Recommended
                                Therapist <span class="text-red-500">*</span></label>
                            <select id="recommendedTherapist" name="recommended_therapist"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Therapist</option>
                                @foreach ($therapists as $therapist)
                                    <option value="{{ $therapist->id }}"
                                        {{ $isEdit && $editTreatmentPlan->recommended_therapist == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->full_name }} ({{ $therapist->specialty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Room Allocation <span
                                    class="text-red-500">*</span></label>
                            <select id="roomAllocation" name="room_allocation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border">
                                <option value="">Select Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ $isEdit && $editTreatmentPlan->room_allocation == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }} - {{ $room->room_type }}
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
                                rows="3" placeholder="Enter dosha imbalance details and assessment">{{ $isEdit ? $editTreatmentPlan->dosha_report : '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Oils Required <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2">
                                    @foreach ($oils as $oil)
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" name="oils_required[]" value="{{ $oil->id }}"
                                                class="form-checkbox text-ayur-green-600 rounded"
                                                {{ $isEdit && in_array($oil->id, $editTreatmentPlan->oils_required ?? []) ? 'checked' : '' }}>
                                            <span class="text-sm text-ayur-brown-700">{{ $oil->name }}</span>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Herbs Required <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2">
                                    @foreach ($herbs as $herb)
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" name="herbs_required[]" value="{{ $herb->id }}"
                                                class="form-checkbox text-ayur-green-600 rounded"
                                                {{ $isEdit && in_array($herb->id, $editTreatmentPlan->herbs_required ?? []) ? 'checked' : '' }}>
                                            <span class="text-sm text-ayur-brown-700">{{ $herb->name }}</span>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700">Special
                                Instructions <span class="text-red-500">*</span></label>
                            <textarea id="specialInstructions" name="special_instructions"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border"
                                rows="2" placeholder="Enter any special instructions for the therapist">{{ $isEdit ? $editTreatmentPlan->special_instructions : '' }}</textarea>
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
                            @if ($isEdit && $editTreatmentPlan->consent_file_path)
                                <div class="bg-ayur-offwhite rounded-lg p-4 mt-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-file-pdf text-red-500 text-2xl mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-ayur-brown-800">Current Consent File</p>
                                                <p class="text-xs text-ayur-brown-600">
                                                    {{ basename($editTreatmentPlan->consent_file_path) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ asset($editTreatmentPlan->consent_file_path) }}" target="_blank"
                                                class="text-ayur-green-600 hover:text-ayur-green-700" title="View File">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="text-xs text-ayur-brown-500 mt-2">Upload a new file to replace this one</p>
                                </div>
                            @endif
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

                <div class="flex justify-end gap-3 mt-8">

                    @if ($isEdit)
                        <a href="{{ route('treatment-plan') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fa-solid fa-times mr-2"></i>
                            Cancel Edit
                        </a>
                    @else
                        <button type="button"
                            class="save-draft-btn inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            Save as Draft
                        </button>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                        {{ $isEdit ? 'Update Treatment Plan' : 'Create Treatment Plan' }}
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
                                                src="{{ $plan->patient && $plan->patient->photo_path ? asset($plan->patient->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                                                onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                                alt="Patient Photo">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-ayur-brown-800">
                                                {{ $plan->patient ? $plan->patient->full_name : 'Patient Not Found' }}
                                            </div>
                                            <div class="text-xs text-ayur-brown-600">
                                                {{ $plan->patient ? $plan->patient->uhid : 'N/A' }}</div>
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
                                    <a href="{{ route('treatment-plan.tracker', $plan->id) }}"
                                        class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"
                                        title="View Treatment Tracker">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- edit button --}}
                                    <a href="{{ route('treatment-plan.edit', $plan->id) }}"
                                        class="text-stone-600 hover:text-stone-900 mr-2" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>


                                    {{-- delete button --}}
                                    <button type="button" class="delete-treatment-btn text-red-600 hover:text-red-900"
                                        data-id="{{ $plan->id }}" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <form id="delete-treatment-form-{{ $plan->id }}"
                                        action="{{ route('treatment-plan.delete', $plan->id) }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        @method('delete')
                                    </form>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle treatment category change to filter therapists
            const treatmentCategorySelect = document.getElementById('treatmentCategory');
            const recommendedTherapistSelect = document.getElementById('recommendedTherapist');

            if (treatmentCategorySelect && recommendedTherapistSelect) {
                // Store the current therapist ID for edit mode
                const currentTherapistId = recommendedTherapistSelect.value;

                treatmentCategorySelect.addEventListener('change', function() {
                    const categoryId = this.value;

                    // Clear current therapist options except the first one
                    recommendedTherapistSelect.innerHTML = '<option value="">Select Therapist</option>';

                    if (categoryId) {
                        // Fetch therapists for the selected category
                        fetch(
                                `{{ route('treatment-plan.therapists-by-category') }}?category_id=${categoryId}`
                            )
                            .then(response => response.json())
                            .then(therapists => {
                                therapists.forEach(therapist => {
                                    const option = document.createElement('option');
                                    option.value = therapist.id;
                                    option.textContent =
                                        `${therapist.full_name} (${therapist.specialty || 'N/A'})`;

                                    // Restore selected therapist in edit mode
                                    if (therapist.id == currentTherapistId) {
                                        option.selected = true;
                                    }

                                    recommendedTherapistSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching therapists:', error);
                            });
                    }
                });

                // Trigger change event on page load if category is pre-selected (for edit mode)
                if (treatmentCategorySelect.value) {
                    treatmentCategorySelect.dispatchEvent(new Event('change'));
                }
            }

            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-treatment-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-treatment-form-' + id).action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this Treatment Plan?',
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
                                        text: 'Treatment Plan deleted successfully!',
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

        // Initialize day-wise schedule data for edit mode
        @if ($isEdit)
            // Load existing day-wise schedule data if available
            if (typeof dayWiseSchedule !== 'undefined') {
                @if ($editTreatmentPlan->day_wise_schedule && count($editTreatmentPlan->day_wise_schedule) > 0)
                    dayWiseSchedule = @json($editTreatmentPlan->day_wise_schedule);
                    console.log('Loaded existing day-wise schedule:', dayWiseSchedule);
                @else
                    // If no existing schedule, create one from dates (like create mode)
                    const startDate = $('#startDate').val();
                    const endDate = $('#endDate').val();
                    if (startDate && endDate) {
                        console.log('Creating new day-wise schedule for edit mode');
                        generateDayWiseSchedule();
                    }
                @endif

                // Update the display and hidden input
                if (dayWiseSchedule.length > 0) {
                    updateDayWiseDisplay();
                    updateDayWiseScheduleInput();
                } else {
                    // Fallback: if still no schedule, try to generate from dates
                    const startDate = $('#startDate').val();
                    const endDate = $('#endDate').val();
                    if (startDate && endDate) {
                        generateDayWiseSchedule();
                        updateDayWiseDisplay();
                        updateDayWiseScheduleInput();
                    }
                }
            }
        @endif
    </script>
@endsection
