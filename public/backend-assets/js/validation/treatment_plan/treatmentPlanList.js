$(document).ready(function () {
    // Global variables
    let selectedPatient = null;
    let consentFile = null;
    let dayWiseSchedule = [];
    let searchTimeout;
    let validationRules = {};
    let validationErrors = {};

    // Initialize the form
    initializeForm();
    initializeValidation();
    initializeEditMode();



    $('#searchTab').on('click', function () {
        $(this).removeClass('bg-gray-100 text-gray-700 border-gray-300')
            .addClass('bg-ayur-green-100 text-ayur-green-700 border-ayur-green-300');

        $('#patientSelectContainer').addClass('hidden');
        $('#patientSearchInputContainer').removeClass('hidden');

        // Clear selected patient from dropdown
        $('#patientSelect').val('');
    });

    // Patient select dropdown functionality
    $('#patientSelect').on('change', function () {
        const selectedOption = $(this).find(':selected');
        const patientId = selectedOption.val();

        if (patientId) {
            const patientData = {
                id: patientId,
                uhid: selectedOption.data('uhid'),
                full_name: selectedOption.data('name'),
                gender: selectedOption.data('gender'),
                age: selectedOption.data('age'),
                mobile: selectedOption.data('mobile'),
                prakriti: selectedOption.data('prakriti'),
                allergies: selectedOption.data('allergies'),
                photo_path: selectedOption.data('photo')
            };

            selectPatientFromDropdown(patientData);
        } else {
            // Clear patient display if no selection
            selectedPatient = null;
            $('#patientDisplay').empty();
            $('#patientId').val('');

            // Clear validation for patient selection
            updateFieldValidation('patient_id', false, 'Patient selection is required');
        }
    });

    // Patient search functionality
    $('#patientSearch').on('input', function () {
        const query = $(this).val().trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            hidePatientResults();
            return;
        }

        // Set timeout to avoid too many requests
        searchTimeout = setTimeout(() => {
            searchPatient(query);
        }, 300);
    });

    // Treatment category change
    $('#treatmentCategory').on('change', function () {
        updateProcedureOptions();
    });

    // Consent file upload
    $('#consentFile').on('change', function () {
        handleConsentFileUpload(this);
    });

    // Form submission
    $('#treatmentPlanForm').on('submit', function (e) {
        // Check if this is edit mode by looking for PATCH method input
        const isEditMode = $('input[name="_method"][value="PATCH"]').length > 0;

        if (isEditMode) {
            // For edit mode, use regular form submission
            // Just validate and let the form submit normally
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            // Update hidden input with current day-wise schedule before submission
            updateDayWiseScheduleInput();

            // Let the form submit normally (don't prevent default)
            return true;
        } else {
            // For create mode, use custom AJAX submission
            e.preventDefault();

            // Clear any existing validation errors first
            clearValidationErrors();

            // Add a small delay to ensure UI is ready
            setTimeout(() => {
                submitTreatmentPlan();
            }, 100);
        }
    });

    // Save as draft button
    $('.save-draft-btn').on('click', function () {
        // Check if this is edit mode
        const isEditMode = $('input[name="_method"][value="PATCH"]').length > 0;

        if (isEditMode) {
            // For edit mode, add draft flag and submit form normally
            $('<input>').attr({
                type: 'hidden',
                name: 'save_as_draft',
                value: '1'
            }).appendTo('#treatmentPlanForm');

            // Validate and submit
            if (validateForm()) {
                $('#treatmentPlanForm').submit();
            } else {
                // Remove the draft flag if validation fails
                $('input[name="save_as_draft"]').remove();
            }
        } else {
            // For create mode, use existing logic
            clearValidationErrors();
            setTimeout(() => {
                submitTreatmentPlan(true);
            }, 100);
        }
    });

    // Add more days button
    $('.add-more-days-btn').on('click', function () {
        addNewDay();
    });

    // Add procedure to day
    $(document).on('click', '.add-procedure-btn', function () {
        const dayId = $(this).closest('[id^="day"]').attr('id');
        addProcedureToDay(dayId);
    });

    // Edit procedure
    $(document).on('click', '.edit-procedure-btn', function () {
        const dayNumber = parseInt($(this).data('day'));
        const procedureIndex = parseInt($(this).data('procedure-index'));
        showProcedureModal(dayNumber, procedureIndex);
    });

    // Remove procedure
    $(document).on('click', '.remove-procedure-btn', function () {
        const $card = $(this).closest('.procedure-card');
        const dayNumber = parseInt($(this).closest('[id^="day"]').attr('id').replace('day', ''));
        const procedureIndex = parseInt($(this).closest('.procedure-card').index());

        // Remove from data structure
        const day = dayWiseSchedule.find(d => d.day === dayNumber);
        if (day && day.procedures[procedureIndex]) {
            day.procedures.splice(procedureIndex, 1);
        }

        // Remove from display
        $card.remove();

        // Update hidden input
        updateDayWiseScheduleInput();

        // Trigger validation after removing procedure
        setTimeout(() => {
            validateDayWiseSchedule();
        }, 100);
    });

    // Edit day date
    $(document).on('change', '.day-date-input', function () {
        const dayNumber = parseInt($(this).data('day'));
        const newDate = $(this).val();

        // Find the day in the schedule and update its date
        const day = dayWiseSchedule.find(d => d.day === dayNumber);
        if (day && newDate) {
            // Validate date is not too far in the past or future
            const selectedDate = new Date(newDate);
            const today = new Date();
            const oneYearFromNow = new Date();
            oneYearFromNow.setFullYear(today.getFullYear() + 1);

            if (selectedDate < today.setHours(0, 0, 0, 0)) {
                showAlert('error', 'Date cannot be in the past');
                $(this).val(day.date); // Revert to original date
                return;
            }

            if (selectedDate > oneYearFromNow) {
                showAlert('error', 'Date cannot be more than 1 year in the future');
                $(this).val(day.date); // Revert to original date
                return;
            }

            day.date = newDate;
            console.log(`Updated Day ${dayNumber} date to: ${newDate}`);
        }
    });

    // Remove day
    $(document).on('click', '.remove-day-btn', function () {
        const dayId = $(this).closest('[id^="day"]').attr('id');
        const dayNumber = parseInt(dayId.replace('day', ''));
        const day = dayWiseSchedule.find(d => d.day === dayNumber);

        // Show confirmation dialog
        Swal.fire({
            title: 'Remove Day?',
            text: `Are you sure you want to remove Day ${dayNumber}${day && day.procedures.length > 0 ? ` and its ${day.procedures.length} procedure(s)` : ''}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove from data structure
                const dayIndex = dayWiseSchedule.findIndex(d => d.day === dayNumber);
                if (dayIndex !== -1) {
                    dayWiseSchedule.splice(dayIndex, 1);
                }

                // Remove from display
                $(this).closest('[id^="day"]').remove();

                console.log(`Removed Day ${dayNumber} from schedule`);

                // Update hidden input
                updateDayWiseScheduleInput();

                // Trigger validation after removing day
                setTimeout(() => {
                    validateDayWiseSchedule();
                }, 100);

                Swal.fire({
                    title: 'Removed!',
                    text: `Day ${dayNumber} has been removed from the schedule.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    // Initialize form
    function initializeForm() {
        // Check if in edit mode
        const isEditMode = $('input[name="_method"][value="PATCH"]').length > 0;

        if (!isEditMode) {
            // Set only start date to today for new forms, leave end date blank
            const today = new Date();
            $('#startDate').val(today.toISOString().split('T')[0]);
            $('#endDate').val('');
        }

        // Don't initialize day-wise schedule until both dates are selected
        // generateDayWiseSchedule();
    }

    // Initialize edit mode
    function initializeEditMode() {
        const isEditMode = $('input[name="_method"][value="PATCH"]').length > 0;

        if (isEditMode) {
            // Get the patient ID from the hidden input
            const patientId = $('#patientId').val();

            if (patientId) {
                // Find the patient in the dropdown and select it
                const $patientOption = $('#patientSelect option[value="' + patientId + '"]');
                if ($patientOption.length) {
                    $('#patientSelect').val(patientId);

                    // Trigger the patient selection to populate the display
                    const patientData = {
                        id: patientId,
                        uhid: $patientOption.data('uhid'),
                        full_name: $patientOption.data('name'),
                        gender: $patientOption.data('gender'),
                        age: $patientOption.data('age'),
                        mobile: $patientOption.data('mobile'),
                        prakriti: $patientOption.data('prakriti'),
                        allergies: $patientOption.data('allergies'),
                        photo_path: $patientOption.data('photo')
                    };

                    selectPatientFromDropdown(patientData);
                }
            }

            // Generate day-wise schedule if dates are available (always create like in create mode)
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            if (startDate && endDate) {
                // Always generate schedule in edit mode, just like create mode
                generateDayWiseSchedule();
            }
        }
    }

    // Search patient
    function searchPatient(query) {
        console.log('Searching patients with query:', query);

        // Show loading state
        const resultsContainer = $('#patientSearchResults');
        resultsContainer.html(`
            <div class="p-3 text-gray-500 text-center">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Searching patients...
            </div>
        `);
        resultsContainer.show();

        $.ajax({
            url: '/admin/treatment-plan/search-patient',
            method: 'GET',
            data: { query: query },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            success: function (response) {
                console.log('Search response:', response);

                // Check if the response contains an error
                if (response.error) {
                    throw new Error(response.message || response.error);
                }
                displayPatientResults(response);
            },
            error: function (xhr, status, error) {
                console.error('Error searching patients:', error);
                console.error('Error details:', {
                    status: status,
                    responseText: xhr.responseText
                });

                resultsContainer.html(`
                    <div class="p-3 text-red-500 text-center">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        ${error || 'Error searching patients. Please try again.'}
                        <br>
                        <small class="text-gray-500">Check console for more details</small>
                    </div>
                `);
                resultsContainer.show();
            }
        });
    }

    // Display patient search results
    function displayPatientResults(patients) {
        const resultsContainer = $('#patientSearchResults');
        resultsContainer.empty();

        if (!Array.isArray(patients) || patients.length === 0) {
            resultsContainer.html(`
                <div class="p-3 text-gray-500 text-center">
                    <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                    No patients found
                </div>
            `);
            resultsContainer.show();
            return;
        }

        const resultsHtml = patients.map(patient => {
            // Sanitize data to prevent XSS
            const safeUhid = patient.uhid ? patient.uhid.replace(/[<>]/g, '') : 'N/A';
            const safeName = patient.full_name ? patient.full_name.replace(/[<>]/g, '') : 'N/A';
            const safeGender = patient.gender ? patient.gender.replace(/[<>]/g, '') : 'N/A';
            const safeMobile = patient.mobile ? patient.mobile.replace(/[<>]/g, '') : 'N/A';
            const safePrakriti = patient.prakriti ? patient.prakriti.replace(/[<>]/g, '') : 'N/A';
            const safeAllergies = patient.allergies ? patient.allergies.replace(/[<>]/g, '') : 'None';
            const safePhotoPath = patient.photo_path ? patient.photo_path.replace(/[<>]/g, '') : '';

            const age = patient.age || 'N/A';
            const genderInitial = safeGender.charAt(0).toUpperCase();

            return `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                     onclick="selectPatient(${patient.id}, '${safeUhid}', '${safeName}', '${safeGender}', '${age}', '${safeMobile}', '${safePrakriti}', '${safeAllergies}', '${safePhotoPath}')">
                    <div class="flex items-center">
                     
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">${safeName}</div>
                            <div class="text-xs text-gray-500">${safeUhid} • ${age}/${genderInitial}</div>
                            <div class="text-xs text-gray-400">${safeMobile}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        resultsContainer.html(resultsHtml);
        resultsContainer.show();
    }

    // Hide patient search results
    function hidePatientResults() {
        $('#patientSearchResults').hide();
    }

    // Select patient from dropdown
    function selectPatientFromDropdown(patientData) {
        try {
            // Validate required parameters
            if (!patientData.id || !patientData.full_name) {
                console.error('Invalid patient data:', patientData);
                return;
            }

            selectedPatient = {
                id: patientData.id,
                uhid: patientData.uhid || 'N/A',
                full_name: patientData.full_name,
                gender: patientData.gender || 'N/A',
                age: patientData.age || 'N/A',
                mobile: patientData.mobile || 'N/A',
                prakriti: patientData.prakriti || 'N/A',
                allergies: patientData.allergies || 'None',
                photo_path: patientData.photo_path
            };

            displaySelectedPatient(selectedPatient);

        } catch (error) {
            console.error('Error selecting patient from dropdown:', error);
            showAlert('error', 'Failed to select patient. Please try again.');
        }
    }

    // Select patient function (for search results)
    window.selectPatient = function (id, uhid, name, gender, age, mobile, prakriti, allergies, photoPath) {
        try {
            // Validate required parameters
            if (!id || !name) {
                console.error('Invalid patient data:', { id, name });
                return;
            }

            selectedPatient = {
                id: id,
                uhid: uhid,
                full_name: name,
                gender: gender,
                age: age,
                mobile: mobile,
                prakriti: prakriti,
                allergies: allergies,
                photo_path: photoPath
            };

            displaySelectedPatient(selectedPatient);

            // Hide search results
            hidePatientResults();
            $('#patientSearch').val(name);

        } catch (error) {
            console.error('Error selecting patient:', error);
            showAlert('error', 'Failed to select patient. Please try again.');
        }
    };

    // Display selected patient (common function)
    function displaySelectedPatient(patient) {
        const patientDisplay = `
            <div class="bg-ayur-offwhite rounded-lg p-4">
                <div class="flex items-center">
                
                                         <img class="h-16 w-16 rounded-full mr-4 object-cover"
                          src="${patient.photo_path ? '/' + patient.photo_path : '/backend-assets/media/uploads/download (3).png'}"
                          alt="Patient avatar"
                          onerror="this.src='/backend-assets/media/uploads/download (3).png'">
                    <div>
                        <h4 class="text-ayur-brown-800 font-medium">${patient.full_name}</h4>
                        <p class="text-sm text-ayur-brown-600">UHID: ${patient.uhid} | ${patient.age}/${patient.gender}</p>
                        <p class="text-sm text-ayur-brown-600">Prakriti: ${patient.prakriti}</p>
                        <p class="text-sm text-ayur-brown-600">Mobile: ${patient.mobile}</p>
                        <p class="text-sm text-ayur-brown-600">Allergies: ${patient.allergies}</p>
                    </div>
                </div>
            </div>
        `;

        $('#patientDisplay').html(patientDisplay);
        $('#patientId').val(patient.id);

        // Trigger validation for patient selection
        validateField('patient_id', patient.id);
    }

    // Update procedure options based on treatment category
    function updateProcedureOptions() {
        const category = $('#treatmentCategory').val();
        const procedureInput = $('#procedureName');

        if (category) {
            // You can add predefined procedures based on category
            const procedures = {
                'snehana': ['Abhyanga', 'Pizhichil', 'Shiroabhyanga', 'Padabhyanga'],
                'swedana': ['Nadi Swedana', 'Pinda Swedana', 'Upanaha Swedana'],
                'vamana': ['Vamana Karma'],
                'virechana': ['Virechana Karma'],
                'basti': ['Niruha Basti', 'Anuvasana Basti', 'Uttara Basti'],
                'nasya': ['Shirovirechana', 'Pratimarsha Nasya'],
                'raktamokshana': ['Siravedha', 'Pracchana'],
                'shirodhara': ['Shirodhara']
            };

            if (procedures[category.toLowerCase()]) {
                procedureInput.attr('placeholder', 'Select or enter procedure name');
                // You can add autocomplete here
            }
        }
    }

    // Handle consent file upload
    function handleConsentFileUpload(input) {
        const file = input.files[0];
        if (file) {
            // Use the validation system
            if (validateConsentFile(input)) {
                consentFile = file;
                displayConsentPreview(file);
            }
        } else {
            // Clear file if no file selected
            consentFile = null;
            $('#consentPreview').empty();
            updateFieldValidation('consent_file', false, 'Consent file is required');
        }
    }

    // Display consent file preview
    function displayConsentPreview(file) {
        const previewHtml = `
            <div class="bg-ayur-offwhite rounded-lg p-4 mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fa-solid fa-file-pdf text-red-500 text-2xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-ayur-brown-800">${file.name}</p>
                            <p class="text-xs text-ayur-brown-600">${(file.size / 1024 / 1024).toFixed(2)} MB • Just uploaded</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" class="text-red-600 hover:text-red-700" onclick="removeConsentFile()">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#consentPreview').html(previewHtml);
    }

    // Remove consent file
    window.removeConsentFile = function () {
        consentFile = null;
        $('#consentFile').val('');
        $('#consentPreview').empty();
    };

    // Generate day-wise schedule
    function generateDayWiseSchedule() {
        const startDateVal = $('#startDate').val();
        const endDateVal = $('#endDate').val();

        // Clear schedule if either date is missing
        if (!startDateVal || !endDateVal) {
            dayWiseSchedule = [];
            $('#dayWiseSchedule').empty();
            return;
        }

        const startDate = new Date(startDateVal);
        const endDate = new Date(endDateVal);

        // Validate dates are valid
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            dayWiseSchedule = [];
            $('#dayWiseSchedule').empty();
            return;
        }

        if (startDate && endDate && endDate >= startDate) {
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

            dayWiseSchedule = [];

            for (let i = 1; i <= days; i++) {
                const currentDate = new Date(startDate);
                currentDate.setDate(startDate.getDate() + i - 1);

                dayWiseSchedule.push({
                    day: i,
                    date: currentDate.toISOString().split('T')[0],
                    procedures: []
                });
            }

            updateDayWiseDisplay();

            // Trigger validation after generating schedule
            setTimeout(() => {
                validateDayWiseSchedule();
            }, 100);
        } else {
            // Clear schedule if dates are invalid
            dayWiseSchedule = [];
            $('#dayWiseSchedule').empty();

            // Trigger validation after clearing schedule
            setTimeout(() => {
                validateDayWiseSchedule();
            }, 100);
        }
    }

    // Update day-wise display
    function updateDayWiseDisplay() {
        const container = $('#dayWiseSchedule');
        container.empty();

        if (dayWiseSchedule.length === 0) {
            return;
        }

        // Update hidden input field for form submission
        updateDayWiseScheduleInput();

        dayWiseSchedule.forEach((day, index) => {
            const dayHtml = `
                <div id="day${day.day}" class="bg-ayur-offwhite rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-2">
                            <h5 class="font-medium text-ayur-brown-800">Day ${day.day} - </h5>
                            <input type="date" 
                                   class="day-date-input px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                   value="${day.date}"
                                   data-day="${day.day}"
                                   title="Click to edit date">
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" class="add-procedure-btn text-ayur-green-600 hover:text-ayur-green-700">
                                <i class="fa-solid fa-plus"></i> Add Procedure
                            </button>
                            <button type="button" class="remove-day-btn text-red-600 hover:text-red-700" title="Remove this day">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="procedures-container grid md:grid-cols-3 gap-4">
                        ${day.procedures.map((proc, procIndex) => `
                            <div class="procedure-card bg-white rounded-md p-3 border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-ayur-brown-800">${proc.name}</p>
                                        <p class="text-xs text-ayur-brown-600">Duration: ${proc.duration} mins</p>
                                        ${proc.notes ? `<p class="text-xs text-gray-500 mt-1">${proc.notes}</p>` : ''}
                                    </div>
                                    <div class="flex space-x-1 ml-2">
                                        <button type="button" 
                                                class="edit-procedure-btn text-ayur-brown-600 hover:text-ayur-brown-700"
                                                data-day="${day.day}" 
                                                data-procedure-index="${procIndex}"
                                                title="Edit procedure">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </button>
                                        <button type="button" 
                                                class="remove-procedure-btn text-red-600 hover:text-red-700"
                                                title="Remove procedure">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            container.append(dayHtml);
        });
    }

    // Update hidden input field with day-wise schedule data
    function updateDayWiseScheduleInput() {
        const hiddenInput = $('#dayWiseScheduleInput');
        if (hiddenInput.length) {
            hiddenInput.val(JSON.stringify(dayWiseSchedule));
            console.log('Updated hidden input with day-wise schedule:', dayWiseSchedule);
        }
    }

    // Add new day
    function addNewDay() {
        const newDayNumber = dayWiseSchedule.length + 1;
        const newDate = new Date();
        newDate.setDate(newDate.getDate() + newDayNumber - 1);

        dayWiseSchedule.push({
            day: newDayNumber,
            date: newDate.toISOString().split('T')[0],
            procedures: []
        });

        updateDayWiseDisplay();

        // Update hidden input
        updateDayWiseScheduleInput();

        // Trigger validation after adding new day
        setTimeout(() => {
            validateDayWiseSchedule();
        }, 100);
    }

    // Add procedure to day
    function addProcedureToDay(dayId) {
        const dayNumber = parseInt(dayId.replace('day', ''));
        const day = dayWiseSchedule.find(d => d.day === dayNumber);

        if (day) {
            // Show modal to collect procedure details
            showProcedureModal(dayNumber, null);
        }
    }

    // Show procedure modal for adding/editing procedures
    function showProcedureModal(dayNumber, procedureIndex = null) {
        const isEdit = procedureIndex !== null;
        const procedure = isEdit ? dayWiseSchedule.find(d => d.day === dayNumber).procedures[procedureIndex] : null;

        const modalHtml = `
            <div id="procedureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ayur-brown-800">
                            ${isEdit ? 'Edit Procedure' : 'Add New Procedure'}
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeProcedureModal()">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="procedureForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                    Procedure Name *
                                </label>
                                <input type="text" 
                                       id="procedureNameInput" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                       placeholder="Enter procedure name"
                                       value="${procedure ? procedure.name : ''}"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                    Duration (minutes) *
                                </label>
                                <input type="number" 
                                       id="procedureDurationInput" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                       placeholder="Enter duration in minutes"
                                       min="1"
                                       max="480"
                                       value="${procedure ? procedure.duration : ''}"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-1">
                                    Notes (Optional)
                                </label>
                                <textarea id="procedureNotesInput" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                          rows="3"
                                          placeholder="Add any additional notes...">${procedure ? (procedure.notes || '') : ''}</textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" 
                                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                                    onclick="closeProcedureModal()">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-ayur-green-600 text-white rounded-md hover:bg-ayur-green-700">
                                ${isEdit ? 'Update Procedure' : 'Add Procedure'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#procedureModal').remove();

        // Add modal to body
        $('body').append(modalHtml);

        // Focus on first input
        $('#procedureNameInput').focus();

        // Handle form submission
        $('#procedureForm').on('submit', function (e) {
            e.preventDefault();
            saveProcedure(dayNumber, procedureIndex);
        });
    }

    // Save procedure
    function saveProcedure(dayNumber, procedureIndex = null) {
        const name = $('#procedureNameInput').val().trim();
        const duration = parseInt($('#procedureDurationInput').val());
        const notes = $('#procedureNotesInput').val().trim();

        // Validate inputs
        if (!name) {
            showAlert('error', 'Please enter a procedure name');
            return;
        }

        if (!duration || duration < 1 || duration > 480) {
            showAlert('error', 'Please enter a valid duration (1-480 minutes)');
            return;
        }

        const day = dayWiseSchedule.find(d => d.day === dayNumber);
        if (!day) {
            showAlert('error', 'Day not found');
            return;
        }

        const procedure = {
            name: name,
            duration: duration,
            notes: notes || ''
        };

        if (procedureIndex !== null) {
            // Edit existing procedure
            day.procedures[procedureIndex] = procedure;
        } else {
            // Add new procedure
            day.procedures.push(procedure);
        }

        // Close modal and update display
        closeProcedureModal();
        updateDayWiseDisplay();

        // Update hidden input
        updateDayWiseScheduleInput();

        // Trigger validation after adding/editing procedure
        setTimeout(() => {
            validateDayWiseSchedule();
        }, 100);

        showAlert('success', `Procedure ${procedureIndex !== null ? 'updated' : 'added'} successfully!`);
    }

    // Close procedure modal
    window.closeProcedureModal = function () {
        $('#procedureModal').remove();
    };

    // Close modal when clicking outside
    $(document).on('click', '#procedureModal', function (e) {
        if (e.target === this) {
            closeProcedureModal();
        }
    });

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    // Submit treatment plan
    function submitTreatmentPlan(isDraft = false) {
        // For drafts, we can be more lenient with validation
        if (!isDraft) {
            // Full validation for final submission
            if (!validateForm()) {
                return;
            }
        } else {
            // Basic validation for drafts - just check if we have a patient
            if (!selectedPatient || !selectedPatient.id) {
                updateFieldValidation('patient_id', false, 'Patient selection is required even for drafts');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please select a patient before saving as draft',
                    confirmButtonColor: '#EF4444'
                });
                return;
            }
        }

        // Collect form data
        const formData = new FormData();
        formData.append('patient_id', selectedPatient.id);
        formData.append('treatment_category', $('#treatmentCategory').val());
        formData.append('procedure_name', $('#procedureName').val());
        formData.append('start_date', $('#startDate').val());
        formData.append('end_date', $('#endDate').val());
        formData.append('dosha_report', $('#doshaReport').val());
        formData.append('special_instructions', $('#specialInstructions').val());
        formData.append('recommended_therapist', $('#recommendedTherapist').val());
        formData.append('room_allocation', $('#roomAllocation').val());
        formData.append('day_wise_schedule', JSON.stringify(dayWiseSchedule));

        // Add oils as array elements
        $('input[name="oils_required[]"]:checked').each(function () {
            formData.append('oils_required[]', $(this).val());
        });

        // Add herbs as array elements
        $('input[name="herbs_required[]"]:checked').each(function () {
            formData.append('herbs_required[]', $(this).val());
        });

        // Add consent file
        if (consentFile) {
            formData.append('consent_file', consentFile);
        }

        // Add draft flag
        if (isDraft) {
            formData.append('save_as_draft', '1');
        }

        // Debug: Log form data
        console.log('Submitting treatment plan with data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }

        // Show loading
        showLoading(isDraft);

        // Create a temporary form for submission
        const tempForm = $('<form>', {
            method: 'POST',
            action: '/admin/treatment-plan',
            enctype: 'multipart/form-data'
        });

        // Add CSRF token
        tempForm.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        // Add all form data to the temporary form
        for (let [key, value] of formData.entries()) {
            if (key === 'consent_file' && value instanceof File) {
                // Handle file input specially
                const fileInput = $('<input>', {
                    type: 'file',
                    name: key,
                    style: 'display: none;'
                });

                // Create a new FileList with our file
                const dt = new DataTransfer();
                dt.items.add(value);
                fileInput[0].files = dt.files;

                tempForm.append(fileInput);
            } else {
                tempForm.append($('<input>', {
                    type: 'hidden',
                    name: key,
                    value: value
                }));
            }
        }

        // Append form to body and submit
        $('body').append(tempForm);
        tempForm.submit();
    }

    // Show alert
    function showAlert(type, message) {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            confirmButtonColor: type === 'success' ? '#10B981' : '#EF4444'
        });
    }

    // Show loading
    function showLoading(isDraft = false) {
        const message = isDraft
            ? 'Please wait while we save your draft...'
            : 'Please wait while we create your treatment plan...';

        Swal.fire({
            title: 'Processing...',
            text: message,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Hide loading
    function hideLoading() {
        Swal.close();
    }

    // Date change handlers are now handled in the validation system

    // Click outside to hide patient results
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#patientSearchContainer').length) {
            hidePatientResults();
        }
    });

    // ==================== VALIDATION SYSTEM ====================

    // Initialize validation
    function initializeValidation() {
        // Define validation rules
        validationRules = {
            patient_id: {
                required: true,
                message: 'Patient selection is required'
            },
            treatment_category: {
                required: true,
                message: 'Treatment category is required'
            },
            procedure_name: {
                required: true,
                minLength: 3,
                maxLength: 255,
                message: 'Procedure name is required and must be between 3-255 characters'
            },
            start_date: {
                required: true,
                type: 'date',
                message: 'Start date is required'
            },
            end_date: {
                required: true,
                type: 'date',
                message: 'End date is required'
            },
            dosha_report: {
                required: true,
                message: 'Dosha report is required'
            },
            // oils_required: {
            //     required: true,
            //     type: 'checkbox',
            //     message: 'Please select at least one oil'
            // },
            // herbs_required: {
            //     required: true,
            //     type: 'checkbox',
            //     message: 'Please select at least one herb'
            // },
            // special_instructions: {
            //     required: true,
            //     // minLength: 5,
            //     // maxLength: 1000,
            //     message: 'Special instructions are required'
            // },
            recommended_therapist: {
                required: true,
                message: 'Please select a recommended therapist'
            },
            room_allocation: {
                required: true,
                message: 'Please select a room for allocation'
            },
            consent_file: {
                required: true,
                type: 'file',
                allowedTypes: ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                maxSize: 5242880, // 5MB in bytes
                message: 'Consent file is required (PDF, JPG, JPEG, PNG, max 5MB)'
            },
            day_wise_schedule: {
                required: true,
                type: 'array',
                minLength: 1,
                message: 'Day-wise treatment schedule is required'
            }
        };

        // Add real-time validation listeners
        addValidationListeners();
    }

    // Add validation event listeners
    function addValidationListeners() {
        // Patient selection validation
        $('#patientSelect').on('change', function () {
            validateField('patient_id', selectedPatient ? selectedPatient.id : '');
        });

        // Treatment category validation
        $('#treatmentCategory').on('change blur', function () {
            validateField('treatment_category', $(this).val());
        });

        // Procedure name validation
        $('#procedureName').on('input blur', function () {
            validateField('procedure_name', $(this).val());
        });

        // Date validation (blur events only - change events handled separately)
        $('#startDate').on('blur', function () {
            const startDate = $(this).val();
            validateField('start_date', startDate);
        });

        $('#endDate').on('blur', function () {
            const endDate = $(this).val();
            validateField('end_date', endDate);
        });

        // Dosha report validation
        $('#doshaReport').on('input blur', function () {
            validateField('dosha_report', $(this).val());
        });

        // Special instructions validation
        $('#specialInstructions').on('input blur', function () {
            validateField('special_instructions', $(this).val());
        });

        // Therapist validation
        $('#recommendedTherapist').on('change blur', function () {
            validateField('recommended_therapist', $(this).val());
        });

        // Room allocation validation
        $('#roomAllocation').on('change blur', function () {
            validateField('room_allocation', $(this).val());
        });

        // Consent file validation
        $('#consentFile').on('change', function () {
            validateConsentFile(this);
        });

        // Oils validation
        $('input[name="oils_required[]"]').on('change', function () {
            validateCheckboxGroup('oils_required', 'input[name="oils_required[]"]:checked');
        });

        // Herbs validation
        $('input[name="herbs_required[]"]').on('change', function () {
            validateCheckboxGroup('herbs_required', 'input[name="herbs_required[]"]:checked');
        });

        // Day-wise schedule validation (triggered when schedule changes)
        $(document).on('click', '.add-procedure-btn, .remove-procedure-btn, .remove-day-btn', function () {
            // Small delay to allow the schedule to update
            setTimeout(() => {
                validateDayWiseSchedule();
            }, 100);
        });
    }

    // Validate individual field
    function validateField(fieldName, value) {
        const rule = validationRules[fieldName];
        if (!rule) return true;

        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (rule.required && (!value || value.toString().trim() === '')) {
            isValid = false;
            errorMessage = rule.message || `${fieldName} is required`;
        }

        // Length validations
        if (isValid && value && rule.minLength && value.toString().length < rule.minLength) {
            isValid = false;
            errorMessage = rule.message || `${fieldName} must be at least ${rule.minLength} characters`;
        }

        if (isValid && value && rule.maxLength && value.toString().length > rule.maxLength) {
            isValid = false;
            errorMessage = rule.message || `${fieldName} must not exceed ${rule.maxLength} characters`;
        }

        // Date validation
        if (isValid && rule.type === 'date' && value) {
            const date = new Date(value);
            if (isNaN(date.getTime())) {
                isValid = false;
                errorMessage = 'Please enter a valid date';
            }
        }

        // Update validation state
        updateFieldValidation(fieldName, isValid, errorMessage);
        return isValid;
    }

    // Validate date range
    function validateDateRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);

        let isValid = true;
        let errorMessage = '';

        if (end < start) {
            isValid = false;
            errorMessage = 'End date must be after or equal to start date';
        }

        updateFieldValidation('end_date', isValid, errorMessage);
        return isValid;
    }

    // Validate checkbox group
    function validateCheckboxGroup(fieldName, selector) {
        const rule = validationRules[fieldName];
        if (!rule) return true;

        const checkedItems = $(selector);
        let isValid = true;
        let errorMessage = '';

        if (rule.required && checkedItems.length === 0) {
            isValid = false;
            errorMessage = rule.message || `Please select at least one ${fieldName}`;
        }

        // Special handling for checkbox groups
        updateCheckboxGroupValidation(fieldName, selector, isValid, errorMessage);
        return isValid;
    }

    // Validate day-wise schedule
    function validateDayWiseSchedule() {
        const rule = validationRules['day_wise_schedule'];
        if (!rule) return true;

        let isValid = true;
        let errorMessage = '';

        // Check if schedule exists and has at least one day
        if (rule.required && (!dayWiseSchedule || dayWiseSchedule.length === 0)) {
            isValid = false;
            errorMessage = rule.message || 'Day-wise treatment schedule is required';
        }

        // Check if schedule has at least one procedure across all days
        if (isValid && dayWiseSchedule.length > 0) {
            const hasProcedures = dayWiseSchedule.some(day => day.procedures && day.procedures.length > 0);
            if (!hasProcedures) {
                isValid = false;
                errorMessage = 'At least one procedure must be added to the day-wise schedule';
            }
        }

        updateDayWiseScheduleValidation(isValid, errorMessage);
        return isValid;
    }

    // Update checkbox group validation display
    function updateCheckboxGroupValidation(fieldName, selector, isValid, errorMessage) {
        const $allCheckboxes = $('input[name="' + fieldName + '[]"]');
        const $container = $allCheckboxes.first().closest('.space-y-4 > div');
        const $checkboxContainer = $container.find('.grid');

        // Remove existing errors and styling
        $container.find('.validation-error').remove();
        $checkboxContainer.removeClass('border-red-500 border-green-500');

        if (!isValid) {
            // Add red border to checkbox container
            $checkboxContainer.addClass('border border-red-500 rounded p-2');

            // Add error message after the checkbox container
            const errorHtml = `
                <div class="validation-error mt-2 text-sm text-red-600">
                    ${errorMessage}
                </div>
            `;
            $container.append(errorHtml);

            validationErrors[fieldName] = errorMessage;
        } else {
            // Add green border for valid selection
            $checkboxContainer.addClass('border border-green-500 rounded p-2');
            validationErrors[fieldName] = null;
        }
    }

    // Validate consent file
    function validateConsentFile(input) {
        const rule = validationRules.consent_file;
        const file = input.files[0];

        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (rule.required && !file) {
            isValid = false;
            errorMessage = 'Consent file is required';
        }

        if (file) {
            // File type validation
            if (rule.allowedTypes && !rule.allowedTypes.includes(file.type)) {
                isValid = false;
                errorMessage = 'Please select a valid file type (PDF, JPG, JPEG, PNG)';
            }

            // File size validation
            if (isValid && rule.maxSize && file.size > rule.maxSize) {
                isValid = false;
                errorMessage = 'File size must not exceed 5MB';
            }
        }

        if (!isValid) {
            input.value = '';
            consentFile = null;
            $('#consentPreview').empty();
        } else if (file) {
            // File is valid
            consentFile = file;
        }

        updateFieldValidation('consent_file', isValid, errorMessage);
        return isValid;
    }

    // Update field validation display
    function updateFieldValidation(fieldName, isValid, errorMessage) {
        // Map field names to their input elements
        const fieldMap = {
            'patient_id': '#patientSearchContainer',
            'treatment_category': '#treatmentCategory',
            'procedure_name': '#procedureName',
            'start_date': '#startDate',
            'end_date': '#endDate',
            'dosha_report': '#doshaReport',
            'special_instructions': '#specialInstructions',
            'recommended_therapist': '#recommendedTherapist',
            'room_allocation': '#roomAllocation',
            'consent_file': '#consentFile',
            'oils_required': 'input[name="oils_required[]"]',
            'herbs_required': 'input[name="herbs_required[]"]'
        };

        const fieldSelector = fieldMap[fieldName];
        if (!fieldSelector) return;

        // Special handling for patient_id
        if (fieldName === 'patient_id') {
            const $container = $(fieldSelector);
            const $patientSelect = $container.find('#patientSelect');
            const $patientSearch = $container.find('#patientSearch');

            // Remove existing error and styling
            $container.find('.validation-error').remove();
            $patientSelect.removeClass('border-red-500 border-green-500');
            $patientSearch.removeClass('border-red-500 border-green-500');

            if (!isValid) {
                // Add red border to active patient selection field
                if ($('#patientSelectContainer').is(':visible')) {
                    $patientSelect.addClass('border-red-500');
                } else {
                    $patientSearch.addClass('border-red-500');
                }

                // Add error message for patient selection (without icon)
                const errorHtml = `
                    <div class="validation-error mt-1 text-sm text-red-600">
                        ${errorMessage}
                    </div>
                `;
                $container.append(errorHtml);
                validationErrors[fieldName] = errorMessage;
            } else {
                // Add green border to active patient selection field
                if ($('#patientSelectContainer').is(':visible')) {
                    $patientSelect.addClass('border-green-500');
                } else {
                    $patientSearch.addClass('border-green-500');
                }
                validationErrors[fieldName] = null;
            }
            return;
        }

        // Special handling for consent_file
        if (fieldName === 'consent_file') {
            const $field = $(fieldSelector);
            const $container = $field.closest('.space-y-4 > div');
            const $dropZone = $container.find('.border-dashed');

            // Remove existing error and styling
            $container.find('.validation-error').remove();
            $dropZone.removeClass('border-red-500 border-green-500');

            if (!isValid) {
                // Add red border to drop zone
                $dropZone.addClass('border-red-500');

                // Add error message
                const errorHtml = `
                    <div class="validation-error mt-2 text-sm text-red-600">
                        ${errorMessage}
                    </div>
                `;
                $container.append(errorHtml);
                validationErrors[fieldName] = errorMessage;
            } else if (consentFile) {
                // Add green border for valid file
                $dropZone.addClass('border-green-500');
                validationErrors[fieldName] = null;
            }
            return;
        }

        const $field = $(fieldSelector);
        const $container = $field.closest('.space-y-4 > div, .grid > div, .relative');

        // Remove existing error and styling
        $container.find('.validation-error').remove();
        $field.removeClass('border-red-500 focus:border-red-500 focus:ring-red-200 border-green-500');

        if (isValid) {
            // Add success styling - green border
            $field.addClass('border-green-500');
            validationErrors[fieldName] = null;
        } else if (!isValid) {
            // Add error styling - red border
            $field.addClass('border-red-500 focus:border-red-500 focus:ring-red-200');

            // Add error message
            const errorHtml = `
                <div class="validation-error mt-1 text-sm text-red-600">
                    ${errorMessage}
                </div>
            `;
            $container.append(errorHtml);

            validationErrors[fieldName] = errorMessage;
        }
    }

    // Validate entire form
    function validateForm() {
        let isFormValid = true;
        const errors = [];

        // Validate patient selection
        if (!validateField('patient_id', selectedPatient ? selectedPatient.id : '')) {
            isFormValid = false;
            errors.push('Patient selection is required');
        }

        // Validate treatment category
        if (!validateField('treatment_category', $('#treatmentCategory').val())) {
            isFormValid = false;
            errors.push('Treatment category is required');
        }

        // Validate procedure name
        if (!validateField('procedure_name', $('#procedureName').val())) {
            isFormValid = false;
            errors.push('Procedure name is required');
        }

        // Validate dates
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        if (!validateField('start_date', startDate)) {
            isFormValid = false;
            errors.push('Start date is required');
        }

        if (!validateField('end_date', endDate)) {
            isFormValid = false;
            errors.push('End date is required');
        }

        if (startDate && endDate) {
            if (!validateDateRange(startDate, endDate)) {
                isFormValid = false;
                errors.push('End date must be after or equal to start date');
            }

            if (!validateSpecialCases()) {
                isFormValid = false;
                errors.push('Please check treatment duration');
            }
        }

        // Validate dosha report
        if (!validateField('dosha_report', $('#doshaReport').val())) {
            isFormValid = false;
            errors.push('Dosha report is required');
        }

        // Validate oils required
        if (!validateCheckboxGroup('oils_required', 'input[name="oils_required[]"]:checked')) {
            isFormValid = false;
            errors.push('Please select at least one oil');
        }

        // Validate herbs required
        if (!validateCheckboxGroup('herbs_required', 'input[name="herbs_required[]"]:checked')) {
            isFormValid = false;
            errors.push('Please select at least one herb');
        }

        // Validate special instructions (now required)
        if (!validateField('special_instructions', $('#specialInstructions').val())) {
            isFormValid = false;
            errors.push('Special instructions are required');
        }

        // Validate recommended therapist
        if (!validateField('recommended_therapist', $('#recommendedTherapist').val())) {
            isFormValid = false;
            errors.push('Please select a recommended therapist');
        }

        // Validate room allocation
        if (!validateField('room_allocation', $('#roomAllocation').val())) {
            isFormValid = false;
            errors.push('Please select a room for allocation');
        }

        // Validate consent file (only required for new forms, optional for edit)
        const isEditMode = $('input[name="_method"][value="PATCH"]').length > 0;
        const hasExistingFile = $('.bg-ayur-offwhite').length > 0; // Check if existing file display is present

        if (!isEditMode || !hasExistingFile) {
            // Required for new forms or edit forms without existing file
            if (!consentFile) {
                updateFieldValidation('consent_file', false, 'Consent file is required');
                isFormValid = false;
                errors.push('Consent file is required');
            }
        }

        // Validate day-wise schedule (required)
        if (dayWiseSchedule.length === 0) {
            // Auto-generate if dates are provided
            if (startDate && endDate) {
                generateDayWiseSchedule();
            }

            // Check again after auto-generation
            if (dayWiseSchedule.length === 0) {
                isFormValid = false;
                errors.push('Day-wise treatment schedule is required');
                // Show error for day-wise schedule section
                updateDayWiseScheduleValidation(false, 'Day-wise treatment schedule is required');
            }
        } else {
            // Clear any existing error for day-wise schedule
            updateDayWiseScheduleValidation(true, '');
        }

        // If form is invalid, scroll to first error (no popup)
        if (!isFormValid) {
            scrollToFirstError();
        }

        return isFormValid;
    }

    // Scroll to first error
    function scrollToFirstError() {
        const firstError = $('.validation-error').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }

    // Validation summary removed - only show field-level errors

    // Update day-wise schedule validation display
    function updateDayWiseScheduleValidation(isValid, errorMessage) {
        const $container = $('#dayWiseSchedule').closest('.space-y-4 > div');

        // Remove existing error and styling
        $container.find('.validation-error').remove();
        $('#dayWiseSchedule').removeClass('border border-red-500 border-green-500 rounded p-2');

        if (!isValid) {
            // Add red border to day-wise schedule container
            $('#dayWiseSchedule').addClass('border border-red-500 rounded p-2');

            // Add error message
            const errorHtml = `
                <div class="validation-error mt-2 text-sm text-red-600">
                    ${errorMessage}
                </div>
            `;
            $container.append(errorHtml);
            validationErrors['day_wise_schedule'] = errorMessage;
        } else {
            // Add green border for valid schedule
            $('#dayWiseSchedule').addClass('border border-green-500 rounded p-2');
            validationErrors['day_wise_schedule'] = null;
        }
    }

    // Clear all validation errors
    function clearValidationErrors() {
        $('.validation-error').remove();
        $('input, select, textarea').removeClass('border-red-500 border-green-500 focus:border-red-500 focus:ring-red-200');

        // Clear patient selection styling
        $('#patientSelect, #patientSearch').removeClass('border-red-500 border-green-500');

        // Clear checkbox group styling
        $('.grid').removeClass('border border-red-500 border-green-500 rounded p-2');

        // Clear file upload styling
        $('.border-dashed').removeClass('border-red-500 border-green-500');

        // Clear day-wise schedule styling
        $('#dayWiseSchedule').removeClass('border border-red-500 border-green-500 rounded p-2');

        validationErrors = {};
    }


    // Enhanced validation for special cases
    function validateSpecialCases() {
        // Check if end date is too far in future (more than 1 year)
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());

        if (startDate && endDate) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 365) {
                updateFieldValidation('end_date', false, 'Treatment duration cannot exceed 1 year');
                return false;
            }

            if (diffDays < 1) {
                updateFieldValidation('end_date', false, 'Treatment must be at least 1 day');
                return false;
            }
        }

        return true;
    }


    // Action functions for treatment plan management
    window.viewTreatmentPlan = function (planId) {
        window.location.href = `/treatment-plan/${planId}`;
    };

    window.editTreatmentPlan = function (planId) {
        window.location.href = `/treatment-plan/${planId}/edit`;
    };

    // Confirm delete helper using fetch to send DELETE
    // document.querySelectorAll('.delete-treatment-btn').forEach(function(btn) {
    //     btn.addEventListener('click', function() {
    //         const id = this.getAttribute('data-id');
    //         const url = document.getElementById('delete-treatment-form-' + id).action;
    //         Swal.fire({
    //             text: 'Are you sure you want to delete this Treatment Plan?',
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonText: 'Yes, delete!',
    //             cancelButtonText: 'No, cancel'
    //         }).then(function(result) {
    //             if (result.isConfirmed) {
    //                 fetch(url, {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/json',
    //                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //                     },
    //                     body: JSON.stringify({
    //                         _method: 'DELETE'
    //                     })
    //                 }).then(res => {
    //                     if (res.ok) {
    //                         Swal.fire({
    //                             text: 'Treatment Plan deleted successfully!',
    //                             icon: 'success'
    //                         }).then(() => location.reload());
    //                     } else {
    //                         throw new Error('Failed');
    //                     }
    //                 }).catch(() => {
    //                     Swal.fire({
    //                         text: 'Something went wrong!',
    //                         icon: 'error'
    //                     });
    //                 });
    //             }
    //         });
    //     });
    // });
});
