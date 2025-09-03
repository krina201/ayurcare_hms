$(document).ready(function () {
    // Global variables
    let selectedPatient = null;
    let consentFile = null;
    let dayWiseSchedule = [];
    let searchTimeout;

    // Initialize the form
    initializeForm();

    // Patient selection tab functionality
    $('#selectTab').on('click', function () {
        $(this).removeClass('bg-gray-100 text-gray-700 border-gray-300')
            .addClass('bg-ayur-green-100 text-ayur-green-700 border-ayur-green-300');
        $('#searchTab').removeClass('bg-ayur-green-100 text-ayur-green-700 border-ayur-green-300')
            .addClass('bg-gray-100 text-gray-700 border-gray-300');

        $('#patientSelectContainer').removeClass('hidden');
        $('#patientSearchInputContainer').addClass('hidden');
        hidePatientResults();
    });

    $('#searchTab').on('click', function () {
        $(this).removeClass('bg-gray-100 text-gray-700 border-gray-300')
            .addClass('bg-ayur-green-100 text-ayur-green-700 border-ayur-green-300');
        $('#selectTab').removeClass('bg-ayur-green-100 text-ayur-green-700 border-ayur-green-300')
            .addClass('bg-gray-100 text-gray-700 border-gray-300');

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
        e.preventDefault();
        submitTreatmentPlan();
    });

    // Save as draft button
    $('.save-draft-btn').on('click', function () {
        submitTreatmentPlan(true);
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

    // Remove procedure
    $(document).on('click', '.remove-procedure-btn', function () {
        $(this).closest('.procedure-card').remove();
    });

    // Remove day
    $(document).on('click', '.remove-day-btn', function () {
        $(this).closest('[id^="day"]').remove();
    });

    // Initialize form
    function initializeForm() {
        // Set default dates
        const today = new Date();
        const endDate = new Date(today.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 days from today

        $('#startDate').val(today.toISOString().split('T')[0]);
        $('#endDate').val(endDate.toISOString().split('T')[0]);

        // Initialize day-wise schedule
        generateDayWiseSchedule();
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
                    <img class="h-16 w-16 rounded-full mr-4"
                         src="${patient.photo_path ? '/storage/' + patient.photo_path : '/backend-assets/media/default-avatar.png'}"
                         alt="Patient avatar"
                         onerror="this.src='/backend-assets/media/default-avatar.png'">
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
            // Validate file type and size
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedTypes.includes(file.type)) {
                showAlert('error', 'Please select a valid file type (PDF, JPG, JPEG, PNG)');
                input.value = '';
                return;
            }

            if (file.size > maxSize) {
                showAlert('error', 'File size must not exceed 5MB');
                input.value = '';
                return;
            }

            consentFile = file;
            displayConsentPreview(file);
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
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());

        if (startDate && endDate) {
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
        }
    }

    // Update day-wise display
    function updateDayWiseDisplay() {
        const container = $('#dayWiseSchedule');
        container.empty();

        dayWiseSchedule.forEach((day, index) => {
            const dayHtml = `
                <div id="day${day.day}" class="bg-ayur-offwhite rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="font-medium text-ayur-brown-800">Day ${day.day} - ${formatDate(day.date)}</h5>
                        <button type="button" class="add-procedure-btn text-ayur-green-600 hover:text-ayur-green-700">
                            <i class="fa-solid fa-plus"></i> Add Procedure
                        </button>
                    </div>
                    <div class="procedures-container grid md:grid-cols-3 gap-4">
                        ${day.procedures.map(proc => `
                            <div class="procedure-card bg-white rounded-md p-3 border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-ayur-brown-800">${proc.name}</p>
                                        <p class="text-xs text-ayur-brown-600">Duration: ${proc.duration} mins</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button type="button" class="text-ayur-brown-600 hover:text-ayur-brown-700">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </button>
                                        <button type="button" class="remove-procedure-btn text-red-600 hover:text-red-700">
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
    }

    // Add procedure to day
    function addProcedureToDay(dayId) {
        const dayNumber = parseInt(dayId.replace('day', ''));
        const day = dayWiseSchedule.find(d => d.day === dayNumber);

        if (day) {
            // You can add a modal or form to collect procedure details
            const procedure = {
                name: 'New Procedure',
                duration: 30
            };

            day.procedures.push(procedure);
            updateDayWiseDisplay();
        }
    }

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
        // Validate required fields
        if (!selectedPatient || !selectedPatient.id) {
            showAlert('error', 'Please select a patient');
            return;
        }

        if (!$('#treatmentCategory').val()) {
            showAlert('error', 'Please select treatment category');
            return;
        }

        if (!$('#procedureName').val()) {
            showAlert('error', 'Please enter procedure name');
            return;
        }

        if (!$('#startDate').val() || !$('#endDate').val()) {
            showAlert('error', 'Please select start and end dates');
            return;
        }

        if (!$('#doshaReport').val()) {
            showAlert('error', 'Please enter dosha report');
            return;
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
        showLoading();

        // Submit form
        $.ajax({
            url: '/admin/treatment-plan',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                hideLoading();
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function (xhr) {
                hideLoading();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `- ${errors[key][0]}\n`;
                    });
                    showAlert('error', errorMessage);
                } else {
                    showAlert('error', 'Failed to create treatment plan. Please try again.');
                }
            }
        });
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
    function showLoading() {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we create your treatment plan',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Hide loading
    function hideLoading() {
        Swal.close();
    }

    // Date change handlers
    $('#startDate, #endDate').on('change', function () {
        generateDayWiseSchedule();
    });

    // Click outside to hide patient results
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#patientSearchContainer').length) {
            hidePatientResults();
        }
    });

    // Action functions for treatment plan management
    window.viewTreatmentPlan = function (planId) {
        window.location.href = `/treatment-plan/${planId}`;
    };

    window.editTreatmentPlan = function (planId) {
        window.location.href = `/treatment-plan/${planId}/edit`;
    };

    window.deleteTreatmentPlan = function (planId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/treatment-plan/${planId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            'Failed to delete treatment plan.',
                            'error'
                        );
                    }
                });
            }
        });
    };
});
