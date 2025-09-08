@extends('backend.layouts.master')

@section('content')
    <main class="p-4">

        <div class="mb-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">
                    {{ session('error') }}
                </div>
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

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div id="patientInfoSection" class="xl:col-span-1">
                <div id="patientInfoCard" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-ayur-brown-800">Patient Information</h3>
                        <span class="bg-ayur-green-100 text-ayur-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Active
                        </span>
                    </div>

                    <div class="flex items-center mb-4">
                        <div class="relative mr-4">
                            <img class="h-16 w-16 rounded-full object-cover border-4 border-ayur-green-200"
                                src="{{ $patient->photo_path ? asset($patient->photo_path) : asset('backend-assets/media/uploads/download (3).png') }}"
                                onerror="this.onerror=null; this.src='{{ asset('backend-assets/media/uploads/download (3).png') }}';"
                                alt="Patient avatar">
                            <span
                                class="absolute bottom-0 right-0 h-5 w-5 rounded-full bg-ayur-green-500 border-2 border-white flex items-center justify-center">
                                <i class="fa-solid fa-check text-white text-xs"></i>
                            </span>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-ayur-brown-800">{{ $patient->full_name }}</h4>
                            <p class="text-sm text-ayur-brown-600">UHID: {{ $patient->uhid }}</p>
                            <p class="text-sm text-ayur-brown-600">{{ $patient->age }} Years /
                                {{ ucfirst($patient->gender) }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                <i class="fa-solid fa-yin-yang text-ayur-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-ayur-brown-600">Prakriti</p>
                                <p class="text-sm font-medium text-ayur-brown-800">
                                    {{ $patient->prakriti ?? 'Not Assessed' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                <i class="fa-solid fa-phone text-ayur-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-ayur-brown-600">Mobile</p>
                                <p class="text-sm font-medium text-ayur-brown-800">{{ $patient->mobile }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-ayur-offwhite p-2 rounded-full mr-3">
                                <i class="fa-solid fa-calendar text-ayur-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-ayur-brown-600">Registration Date</p>
                                <p class="text-sm font-medium text-ayur-brown-800">
                                    {{ optional($patient->registration_date)->format('d M, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Medications Card -->
                <div id="currentMedicationsCard" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Current Medications</h3>
                    @php
                        $activePrescriptions = $patient->activePrescriptions()->with('items')->get();
                        $currentMedications = $activePrescriptions->flatMap(function ($prescription) {
                            return $prescription->items;
                        });
                    @endphp

                    @if ($currentMedications->count() > 0)
                        <div class="space-y-3">
                            @foreach ($currentMedications->take(3) as $medication)
                                <div class="flex items-center justify-between p-3 bg-ayur-offwhite rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-capsules text-ayur-green-600 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-ayur-brown-800">{{ $medication->name }}</p>
                                            <p class="text-xs text-ayur-brown-600">{{ $medication->dosage }}
                                                {{ $medication->frequency }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="bg-ayur-green-100 text-ayur-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-ayur-brown-600 text-center py-4">No current medications</p>
                    @endif
                </div>

                <!-- Recent Prescriptions Card -->
                <div id="prescriptionHistoryCard" class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-ayur-brown-800">Recent Prescriptions</h3>
                        <a href="{{ route('patients.show', $patient) }}"
                            class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                            View All
                        </a>
                    </div>
                    @php
                        $recentPrescriptions = $patient->recentPrescriptions(3)->get();
                    @endphp

                    @if ($recentPrescriptions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($recentPrescriptions as $prescription)
                                <div
                                    class="border-l-4 {{ $prescription->status === 'active' ? 'border-ayur-green-500' : 'border-gray-300' }} pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-ayur-brown-800">PR-{{ $prescription->id }}
                                            </p>
                                            <p class="text-xs text-ayur-brown-600">
                                                {{ $prescription->prescription_date->format('d M, Y') }} -
                                                Dr. {{ $prescription->doctor->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-ayur-brown-600">{{ $prescription->chief_complaint }}</p>
                                        </div>
                                        <span
                                            class="bg-{{ $prescription->status === 'active' ? 'ayur-green' : 'gray' }}-100 text-{{ $prescription->status === 'active' ? 'ayur-green' : 'gray' }}-800 text-xs px-2 py-1 rounded-full">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-ayur-brown-600 text-center py-4">No previous prescriptions</p>
                    @endif
                </div>
            </div>

            <!-- Prescription Form Section -->
            <div id="prescriptionFormSection" class="xl:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-ayur-brown-800">New Prescription</h3>
                            <p class="text-sm text-ayur-brown-600 mt-1">Add medications and treatment recommendations</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="bg-ayur-yellow-100 text-ayur-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                Draft
                            </span>
                        </div>
                    </div>

                    <form id="prescriptionForm" action="{{ route('patients.prescriptions.store', $patient) }}"
                        method="POST" class="space-y-6" novalidate>
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                    <i class="fa-solid fa-calendar-days mr-2 text-ayur-green-600"></i>
                                    Prescription Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="prescription_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                    value="{{ old('prescription_date', now()->format('Y-m-d')) }}">
                                @error('prescription_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                    <i class="fa-solid fa-stethoscope mr-2 text-ayur-green-600"></i>
                                    Chief Complaint <span class="text-red-500">*</span>
                                </label>
                                <input type="text" placeholder="Enter primary concern..." name="chief_complaint"
                                    value="{{ old('chief_complaint') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">
                                <p class="mt-1 text-sm text-red-600 js-error" id="chief_complaint-error"
                                    style="display:none"></p>
                                @error('chief_complaint')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                <i class="fa-solid fa-notes-medical mr-2 text-ayur-green-600"></i>
                                Diagnosis & Assessment <span class="text-red-500">*</span>
                            </label>
                            <textarea rows="3" name="diagnosis" placeholder="Enter diagnosis and ayurvedic assessment..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">{{ old('diagnosis') }}</textarea>
                            <p class="mt-1 text-sm text-red-600 js-error" id="diagnosis-error" style="display:none"></p>
                            @error('diagnosis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Medications Section -->
                        <div id="medicationsSection">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-ayur-brown-800">
                                    <i class="fa-solid fa-pills mr-2 text-ayur-green-600"></i>
                                    Medications <span class="text-red-500">*</span>
                                </h4>
                                <button type="button" id="addMedicationBtn"
                                    class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-4 py-2 flex items-center transition-colors">
                                    <i class="fa-solid fa-plus mr-2"></i>
                                    Add Medication
                                </button>
                            </div>

                            <div id="medicationsList" class="space-y-4">
                                <!-- Medication items will be added here by JavaScript -->
                                <!-- Debug: Show medicines count -->
                                <div id="debug-info" class="text-sm text-gray-500 mb-2">
                                    Medicines available: {{ count($medicines ?? []) }}
                                    @if (isset($medicines) && count($medicines) > 0)
                                        ({{ $medicines->first()->name ?? 'N/A' }})
                                    @endif
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-red-600 js-error" id="medications-error" style="display:none">
                            </p>
                            @error('medications')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                <i class="fa-solid fa-spa mr-2 text-ayur-green-600"></i>
                                Treatment Recommendations
                            </label>
                            <textarea rows="3" name="notes"
                                placeholder="Enter dietary advice, lifestyle modifications, panchkarma treatments..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                    <i class="fa-solid fa-calendar-plus mr-2 text-ayur-green-600"></i>
                                    Follow-up Date
                                </label>
                                <input type="date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">
                                @error('follow_up_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                    <i class="fa-solid fa-exclamation-triangle mr-2 text-ayur-green-600"></i>
                                    Priority Level <span class="text-red-500">*</span>
                                </label>
                                <select name="priority"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">
                                    <option value="Normal" {{ old('priority') == 'Normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent
                                    </option>
                                    <option value="High Priority"
                                        {{ old('priority') == 'High Priority' ? 'selected' : '' }}>High Priority</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-2">
                                <i class="fa-solid fa-clipboard mr-2 text-ayur-green-600"></i>
                                Special Notes & Precautions
                            </label>
                            <textarea rows="2" name="special_notes" placeholder="Any special instructions or precautions..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500">{{ old('special_notes') }}</textarea>
                            @error('special_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div
                            class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('patients.show', $patient) }}"
                                class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-ayur-brown-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-ayur-green-500 transition-colors text-center">
                                <i class="fa-solid fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" name="save_as_draft" value="1"
                                class="w-full sm:w-auto px-6 py-3 bg-ayur-yellow-100 text-ayur-yellow-800 font-medium rounded-lg hover:bg-ayur-yellow-200 focus:outline-none focus:ring-2 focus:ring-ayur-yellow-500 transition-colors">
                                <i class="fa-solid fa-save mr-2"></i>
                                Save as Draft
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto px-6 py-3 bg-ayur-green-600 text-white font-medium rounded-lg hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-ayur-green-500 transition-colors">
                                <i class="fa-solid fa-prescription mr-2"></i>
                                Save Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let medicationIndex = 0;
            const medicationsList = document.getElementById('medicationsList');
            const form = document.getElementById('prescriptionForm');

            // Initialize with existing data if editing
            const oldMedications = @json(old('medications', []));

            // Medicine options from server
            const medicines = @json($medicines ?? []);
            console.log('Medicines loaded:', medicines.length, medicines);

            function updateMedicationIndices() {
                const items = medicationsList.querySelectorAll('.medication-item');
                items.forEach((item, index) => {
                    item.dataset.index = index;
                    item.querySelector('h5').textContent = `Medication ${index + 1}`;

                    // Update all input names and error IDs
                    item.querySelectorAll('input, select').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name && name.includes('medications[')) {
                            const fieldName = name.match(/\[(\w+)\]$/)[1];
                            input.setAttribute('name', `medications[${index}][${fieldName}]`);
                        }
                    });

                    item.querySelectorAll('.js-error').forEach(p => {
                        const id = p.getAttribute('id');
                        if (id && id.includes('medications.')) {
                            const fieldName = id.split('.').pop().replace('-error', '');
                            p.setAttribute('id', `medications.${index}.${fieldName}-error`);
                        }
                    });
                });
                medicationIndex = items.length;
            }

            function createMedicationItem(index, data = {}) {
                const item = document.createElement('div');
                item.classList.add('medication-item', 'border', 'border-gray-200', 'rounded-lg', 'p-4',
                    'bg-ayur-offwhite');
                item.dataset.index = index;

                // Generate medicine options
                let medicineOptions = '<option value="">Select Medication</option>';

                if (medicines && medicines.length > 0) {
                    medicines.forEach(medicine => {
                        const selected = data.name == medicine.id ? 'selected' : '';
                        const strengthText = medicine.strength_dosage ? ` - ${medicine.strength_dosage}` :
                            '';
                        medicineOptions += `<option value="${medicine.id}" data-strength="${medicine.strength_dosage || ''}" ${selected}>
                            ${medicine.name}${strengthText}
                        </option>`;
                    });
                } else {
                    medicineOptions += '<option value="" disabled>No medicines available</option>';
                }

                item.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="font-medium text-ayur-brown-800">Medication ${index + 1}</h5>
                        <button type="button" class="text-red-500 hover:text-red-700 remove-medication transition-colors">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                Medication Name <span class="text-red-500">*</span>
                            </label>
                            <select name="medications[${index}][name]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm medicine-select">
                                ${medicineOptions}
                            </select>
                            <p class="mt-1 text-sm text-red-600 js-error" id="medications.${index}.name-error" style="display:none"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                Dosage <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="medications[${index}][dosage]" 
                                   placeholder="e.g., 2g"
                                   value="${data.dosage || ''}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                            <p class="mt-1 text-sm text-red-600 js-error" id="medications.${index}.dosage-error" style="display:none"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                Frequency <span class="text-red-500">*</span>
                            </label>
                            <select name="medications[${index}][frequency]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                                <option value="Once daily" ${data.frequency === 'Once daily' ? 'selected' : ''}>Once daily</option>
                                <option value="Twice daily" ${data.frequency === 'Twice daily' ? 'selected' : ''}>Twice daily</option>
                                <option value="Three times daily" ${data.frequency === 'Three times daily' ? 'selected' : ''}>Three times daily</option>
                                <option value="As needed" ${data.frequency === 'As needed' ? 'selected' : ''}>As needed</option>
                            </select>
                            <p class="mt-1 text-sm text-red-600 js-error" id="medications.${index}.frequency-error" style="display:none"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                Duration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="medications[${index}][duration]" 
                                   placeholder="e.g., 15 days"
                                   value="${data.duration || ''}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                            <p class="mt-1 text-sm text-red-600 js-error" id="medications.${index}.duration-error" style="display:none"></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Instructions</label>
                        <input type="text" name="medications[${index}][instructions]" 
                               placeholder="e.g., Take after meals with warm water"
                               value="${data.instructions || ''}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-ayur-green-500 focus:border-ayur-green-500 text-sm">
                    </div>
                `;
                return item;
            }

            // Add medication button event
            document.getElementById('addMedicationBtn').addEventListener('click', function() {
                medicationsList.appendChild(createMedicationItem(medicationIndex));
                medicationIndex++;
            });

            // Remove medication event
            medicationsList.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-medication');
                if (removeBtn) {
                    const item = removeBtn.closest('.medication-item');

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Remove Medication?',
                        text: 'Are you sure you want to remove this medication?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            item.remove();
                            updateMedicationIndices();

                            // Clear any general medications error if items exist
                            if (medicationsList.querySelectorAll('.medication-item').length > 0) {
                                clearError('medications');
                            }
                        }
                    });
                }
            });

            // Initialize with old data or add first medication
            if (oldMedications && oldMedications.length > 0) {
                oldMedications.forEach((medication, index) => {
                    medicationsList.appendChild(createMedicationItem(index, medication));
                    medicationIndex++;
                });
            } else {
                // Add first medication by default
                document.getElementById('addMedicationBtn').click();
            }

            // Form fields for validation
            const fields = {
                chief_complaint: document.querySelector('[name="chief_complaint"]'),
                diagnosis: document.querySelector('[name="diagnosis"]'),
            };

            // Utility functions for error handling
            function showError(fieldName, message) {
                const errorEl = document.getElementById(fieldName + '-error');
                const inputEl = document.querySelector(`[name="${fieldName}"]`);

                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.style.display = 'block';
                }
                if (inputEl) {
                    inputEl.classList.add('border-red-500', 'focus:border-red-500');
                    inputEl.classList.remove('border-gray-300');
                }
            }

            function clearError(fieldName) {
                const errorEl = document.getElementById(fieldName + '-error');
                const inputEl = document.querySelector(`[name="${fieldName}"]`);

                if (errorEl) {
                    errorEl.textContent = '';
                    errorEl.style.display = 'none';
                }
                if (inputEl) {
                    inputEl.classList.remove('border-red-500', 'focus:border-red-500');
                    inputEl.classList.add('border-gray-300');
                }
            }

            function showDynamicError(fieldName, index, message) {
                const errorEl = document.getElementById(`medications.${index}.${fieldName}-error`);
                const inputEl = document.querySelector(`[name="medications[${index}][${fieldName}]"]`);

                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.style.display = 'block';
                }
                if (inputEl) {
                    inputEl.classList.add('border-red-500', 'focus:border-red-500');
                    inputEl.classList.remove('border-gray-300');
                }
            }

            function clearDynamicError(fieldName, index) {
                const errorEl = document.getElementById(`medications.${index}.${fieldName}-error`);
                const inputEl = document.querySelector(`[name="medications[${index}][${fieldName}]"]`);

                if (errorEl) {
                    errorEl.textContent = '';
                    errorEl.style.display = 'none';
                }
                if (inputEl) {
                    inputEl.classList.remove('border-red-500', 'focus:border-red-500');
                    inputEl.classList.add('border-gray-300');
                }
            }

            // Client-side validation function
            function validateForm() {
                let isValid = true;

                // Clear all errors first
                Object.keys(fields).forEach(key => clearError(key));
                clearError('medications');

                document.querySelectorAll('.medication-item').forEach(item => {
                    const index = item.dataset.index;
                    ['name', 'dosage', 'frequency', 'duration'].forEach(field => {
                        clearDynamicError(field, index);
                    });
                });

                // Validate required fields
                if (!fields.chief_complaint.value.trim()) {
                    showError('chief_complaint', 'Chief complaint is required.');
                    isValid = false;
                }

                if (!fields.diagnosis.value.trim()) {
                    showError('diagnosis', 'Diagnosis and assessment are required.');
                    isValid = false;
                }

                // Validate medications
                const medicationItems = document.querySelectorAll('.medication-item');
                if (medicationItems.length === 0) {
                    showError('medications', 'At least one medication is required.');
                    isValid = false;
                } else {
                    medicationItems.forEach(item => {
                        const index = item.dataset.index;
                        const name = item.querySelector(`[name="medications[${index}][name]"]`).value
                            .trim();
                        const dosage = item.querySelector(`[name="medications[${index}][dosage]"]`).value
                            .trim();
                        const frequency = item.querySelector(`[name="medications[${index}][frequency]"]`)
                            .value.trim();
                        const duration = item.querySelector(`[name="medications[${index}][duration]"]`)
                            .value.trim();

                        if (!name) {
                            showDynamicError('name', index, 'Medication name is required.');
                            isValid = false;
                        }
                        if (!dosage) {
                            showDynamicError('dosage', index, 'Dosage is required.');
                            isValid = false;
                        }
                        if (!frequency) {
                            showDynamicError('frequency', index, 'Frequency is required.');
                            isValid = false;
                        }
                        if (!duration) {
                            showDynamicError('duration', index, 'Duration is required.');
                            isValid = false;
                        }
                    });
                }

                return isValid;
            }

            // Add real-time validation
            Object.keys(fields).forEach(function(key) {
                const element = fields[key];
                if (element) {
                    element.addEventListener('input', () => clearError(key));
                    element.addEventListener('change', () => clearError(key));
                }
            });

            // Add real-time validation for medication fields
            medicationsList.addEventListener('input', function(e) {
                if (e.target.closest('.medication-item')) {
                    const item = e.target.closest('.medication-item');
                    const index = item.dataset.index;
                    const nameMatch = e.target.name.match(/\[(\w+)\]$/);

                    if (nameMatch) {
                        const fieldName = nameMatch[1];
                        clearDynamicError(fieldName, index);
                    }

                    // Clear general medications error if items exist
                    if (medicationsList.querySelectorAll('.medication-item').length > 0) {
                        clearError('medications');
                    }
                }
            });

            // Handle medicine selection change
            medicationsList.addEventListener('change', function(e) {
                if (e.target.classList.contains('medicine-select')) {
                    const selectedOption = e.target.selectedOptions[0];
                    const item = e.target.closest('.medication-item');
                    const index = item.dataset.index;

                    // Auto-populate dosage if strength is available
                    if (selectedOption && selectedOption.dataset.strength) {
                        const dosageInput = item.querySelector(`[name="medications[${index}][dosage]"]`);
                        if (dosageInput && !dosageInput.value) {
                            dosageInput.value = selectedOption.dataset.strength;
                        }
                    }

                    // Clear name error when medicine is selected
                    clearDynamicError('name', index);
                }
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();

                    // Scroll to first error
                    const firstError = document.querySelector('.js-error[style*="display: block"]');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }


                } else {
                    // Show loading state
                    const submitBtn = e.target.querySelector('button[type="submit"]:focus') ||
                        e.target.querySelector('button[type="submit"]:not([name="save_as_draft"])');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...';

                    // Re-enable after 5 seconds as fallback
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                }
            });

            // Auto-save draft functionality (optional)
            let autoSaveTimer;
            form.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // You can implement auto-save draft functionality here
                    console.log('Auto-save draft...');
                }, 30000); // 30 seconds delay
            });
        });
    </script>
@endsection
