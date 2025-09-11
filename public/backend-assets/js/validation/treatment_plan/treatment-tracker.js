$(document).ready(function () {
    // Initialize form handling
    initializeTreatmentTrackerForm();

    // Initialize material management
    initializeMaterialManagement();

    // Initialize image upload
    initializeImageUpload();
});

function initializeTreatmentTrackerForm() {
    const form = $('#treatmentSessionForm');
    if (!form.length) return;

    // Handle form submission for different actions
    $('#startSessionBtn').on('click', function () {
        submitForm('start');
    });

    $('#saveDraftBtn').on('click', function () {
        submitForm('save_draft');
    });

    $('#completeSessionBtn').on('click', function () {
        submitForm('complete');
    });

    // Real-time validation
    initializeRealTimeValidation(form);

    // Form validation
    form.on('submit', function (e) {
        e.preventDefault();
        // Validation will be handled by individual button clicks
    });
}

function initializeRealTimeValidation(form) {
    // Validate required fields on blur
    const requiredFields = ['session_date', 'session_time', 'room_id', 'therapist_notes'];

    requiredFields.forEach(fieldName => {
        const input = form.find(`[name="${fieldName}"]`);

        input.on('blur', function () {
            validateSingleField($(this), fieldName);
        });

        input.on('input', function () {
            // Clear error when user starts typing
            if ($(this).hasClass('border-red-500')) {
                $(this).closest('.space-y-6').find('.field-error').remove();
                $(this).removeClass('border-red-500').addClass('border-gray-300');
            }
        });
    });

    // Special validation for therapist notes length
    form.find('[name="therapist_notes"]').on('input', function () {
        const input = $(this);
        const value = input.val();

        // Clear previous error
        input.closest('.space-y-6').find('.field-error').remove();
        input.removeClass('border-red-500').addClass('border-gray-300');

        // Validate minimum length if there's content
        if (value && value.trim().length > 0 && value.trim().length < 10) {
            showFieldError(input, 'Therapist notes must be at least 10 characters long');
        }
    });

    // Validate room selection on change
    form.find('[name="room_id"]').on('change', function () {
        validateSingleField($(this), 'room_id');
    });

    // Validate vital signs inputs
    form.find('[name^="vital_signs"]').on('input', function () {
        validateVitalSigns();
    });

    // Validate materials inputs
    form.find('[name^="materials_used"]').on('input', function () {
        validateMaterials();
    });
}

function validateVitalSigns() {
    const form = $('#treatmentSessionForm');
    const vitalSigns = form.find('[name^="vital_signs"]');
    let hasVitalSigns = false;

    vitalSigns.each(function () {
        if ($(this).val() && $(this).val().trim() !== '') {
            hasVitalSigns = true;
        }
    });

    // Clear previous section error
    form.find('.vital-signs-section .section-error').remove();

    // Add helpful message if no vital signs are entered
    if (!hasVitalSigns) {
        const vitalSignsSection = form.find('[name^="vital_signs"]').closest('.space-y-6');
        if (vitalSignsSection.find('.section-error').length === 0) {
            const infoHtml = `<div class="section-error text-blue-600 text-sm mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
            Vital signs are optional but recommended for treatment completion
        </div>`;
            vitalSignsSection.append(infoHtml);
        }
    }
}

function validateMaterials() {
    const form = $('#treatmentSessionForm');
    const materialsUsed = form.find('[name^="materials_used"]');
    let hasMaterials = false;

    materialsUsed.each(function () {
        if ($(this).val() && parseFloat($(this).val()) > 0) {
            hasMaterials = true;
        }
    });

    // Clear previous section error
    form.find('.materials-section .section-error').remove();

    // Add helpful message if no materials are used
    if (!hasMaterials) {
        const materialsSection = $('#materialsContainer');
        if (materialsSection.find('.section-error').length === 0) {
            const infoHtml = `<div class="section-error text-blue-600 text-sm mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
                Record materials used for better treatment tracking
            </div>`;
            materialsSection.append(infoHtml);
        }
    }
}

function validateSingleField(input, fieldName) {
    const fieldLabels = {
        'session_date': 'Session Date',
        'session_time': 'Session Time',
        'room_id': 'Room',
        'therapist_notes': 'Therapist Notes'
    };

    // Clear previous error
    input.closest('.space-y-6').find('.field-error').remove();
    input.removeClass('border-red-500').addClass('border-gray-300');

    // Validate based on field type
    if (fieldName === 'therapist_notes') {
        const value = input.val();
        if (value && value.trim().length > 0 && value.trim().length < 10) {
            showFieldError(input, 'Therapist notes must be at least 10 characters long');
        }
    } else {
        // Required field validation
        if (!input.val() || input.val().trim() === '') {
            showFieldError(input, `${fieldLabels[fieldName]} is required`);
        }
    }
}

function submitForm(action) {
    const form = $('#treatmentSessionForm');

    // Add action to form data
    const actionInput = $('<input>').attr({
        type: 'hidden',
        name: 'action',
        value: action
    });
    form.append(actionInput);

    // Add therapist assignment ID
    const assignmentId = form.data('assignment-id');
    const assignmentInput = $('<input>').attr({
        type: 'hidden',
        name: 'therapist_assignment_id',
        value: assignmentId
    });
    form.append(assignmentInput);

    // Validate required fields based on action
    if (!validateForm(action)) {
        return;
    }

    // Show loading state
    showLoadingState(action);

    // Submit form normally (will redirect)
    form[0].submit();
}

function validateForm(action) {
    const form = $('#treatmentSessionForm');
    let isValid = true;

    // Clear previous error states
    clearFieldErrors(form);

    // Skip validation for "start" action - allow starting session without required fields
    if (action === 'start') {
        return true;
    }

    // Required fields validation (only for save_draft and complete actions)
    const requiredFields = [
        { name: 'session_date', label: 'Session Date' },
        { name: 'session_time', label: 'Session Time' },
        { name: 'room_id', label: 'Room' },
        { name: 'therapist_notes', label: 'Therapist Notes' }
    ];

    requiredFields.forEach(field => {
        const input = form.find(`[name="${field.name}"]`);
        if (!input.val() || input.val().trim() === '') {
            showFieldError(input, `${field.label} is required`);
            isValid = false;
        }
    });

    // Therapist notes minimum length (only for save_draft and complete actions)
    const therapistNotesInput = form.find('[name="therapist_notes"]');
    const therapistNotes = therapistNotesInput.val();
    if (therapistNotes && therapistNotes.trim().length < 10) {
        showFieldError(therapistNotesInput, 'Therapist notes must be at least 10 characters long');
        isValid = false;
    }

    // Validate materials used (if any)
    const materialsUsed = form.find('[name^="materials_used"]');
    let hasMaterials = false;
    materialsUsed.each(function () {
        if ($(this).val() && parseFloat($(this).val()) > 0) {
            hasMaterials = true;
        }
    });

    // For complete action, require at least some materials or vital signs
    if (action === 'complete') {
        const vitalSigns = form.find('[name^="vital_signs"]');
        let hasVitalSigns = false;
        vitalSigns.each(function () {
            if ($(this).val() && $(this).val().trim() !== '') {
                hasVitalSigns = true;
            }
        });

        if (!hasMaterials && !hasVitalSigns) {
            // Show error in materials section
            const materialsContainer = $('#materialsContainer');
            showSectionError(materialsContainer, 'Please record at least some materials used or vital signs for completion');
            isValid = false;
        }
    }

    return isValid;
}

function clearFieldErrors(form) {
    // Remove error styling from all inputs
    form.find('.border-red-500').removeClass('border-red-500').addClass('border-gray-300');

    // Remove all error messages
    form.find('.field-error').remove();
    form.find('.section-error').remove();
}

function showFieldError(input, message) {
    // Add error styling to input
    input.addClass('border-red-500').removeClass('border-gray-300');

    // Remove existing error message for this field
    input.closest('.space-y-6').find('.field-error').remove();

    // Add error message below the field
    const errorHtml = `<div class="field-error text-red-600 text-sm mt-1">
        ${message}
    </div>`;

    // Insert error message after the input's parent container
    input.closest('div').after(errorHtml);
}

function showSectionError(container, message) {
    // Remove existing section error
    container.find('.section-error').remove();

    // Add section error message
    const errorHtml = `<div class="section-error text-red-600 text-sm mt-2 p-2 bg-red-50 border border-red-200 rounded">
        ${message}
    </div>`;

    container.append(errorHtml);
}

function initializeMaterialManagement() {
    // Add more materials functionality
    $('#addMaterialBtn').on('click', function () {
        addCustomMaterial();
    });

    // Remove material functionality
    $(document).on('click', '.remove-material', function () {
        $(this).closest('.material-item').remove();
    });
}

function addCustomMaterial() {
    const container = $('#materialsContainer');
    const materialHtml = `
        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg material-item">
            <div class="flex items-center flex-1">
                <input type="text" name="custom_materials[][name]" placeholder="Material name" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border mr-2">
                <input type="text" name="custom_materials[][unit]" placeholder="Unit" 
                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2 border mr-2">
            </div>
            <div class="flex items-center">
                <input type="number" name="custom_materials[][quantity]" value="0" min="0" step="0.1"
                    class="w-16 rounded-md border-gray-300 shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-1 text-sm border">
                <button type="button" class="remove-material ml-2 text-red-600 hover:text-red-700">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.append(materialHtml);
}

function initializeImageUpload() {
    const fileInput = $('#tracker-images');
    const uploadArea = fileInput.closest('.border-dashed');

    // Handle file selection
    fileInput.on('change', function () {
        const files = this.files;
        if (files.length > 0) {
            validateAndDisplayFiles(files);
        }
    });

    // Handle drag and drop
    uploadArea.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('border-ayur-green-500 bg-ayur-green-50');
    });

    uploadArea.on('dragleave', function (e) {
        e.preventDefault();
        $(this).removeClass('border-ayur-green-500 bg-ayur-green-50');
    });

    uploadArea.on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('border-ayur-green-500 bg-ayur-green-50');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            validateAndDisplayFiles(files);
        }
    });
}

function validateAndDisplayFiles(files) {
    const container = $('#tracker-images').closest('.border-dashed');
    let fileListHtml = '<div class="mt-2"><p class="text-sm text-gray-600">Selected files:</p><ul class="text-xs text-gray-500">';
    let hasErrors = false;
    let errorMessages = [];

    Array.from(files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) { // 5MB limit
            fileListHtml += `<li class="text-red-600">${file.name} (File too large - max 5MB)</li>`;
            hasErrors = true;
            errorMessages.push(`${file.name} exceeds 5MB limit`);
        } else if (!file.type.startsWith('image/')) {
            fileListHtml += `<li class="text-red-600">${file.name} (Only image files allowed)</li>`;
            hasErrors = true;
            errorMessages.push(`${file.name} is not an image file`);
        } else {
            fileListHtml += `<li class="text-green-600">${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</li>`;
        }
    });

    fileListHtml += '</ul></div>';

    // Remove existing file list and errors
    container.find('.mt-2').remove();
    container.find('.upload-error').remove();

    // Add file list
    container.append(fileListHtml);

    // Show validation errors if any
    if (hasErrors) {
        const errorHtml = `<div class="upload-error text-red-600 text-sm mt-2 p-2 bg-red-50 border border-red-200 rounded">
            ${errorMessages.join(', ')}
        </div>`;
        container.append(errorHtml);
    }
}


function showLoadingState(action) {
    const buttonText = {
        'start': 'Starting Session...',
        'save_draft': 'Saving Draft...',
        'complete': 'Completing Session...'
    };

    // Disable all action buttons
    $('#startSessionBtn, #saveDraftBtn, #completeSessionBtn').prop('disabled', true);

    // Update button text
    const activeButton = $(`#${action === 'start' ? 'startSessionBtn' : action === 'save_draft' ? 'saveDraftBtn' : 'completeSessionBtn'}`);
    const originalText = activeButton.html();
    activeButton.data('original-text', originalText);
    activeButton.html(`<i class="fa-solid fa-spinner fa-spin mr-2"></i>${buttonText[action]}`);
}

function hideLoadingState() {
    // Re-enable all action buttons
    $('#startSessionBtn, #saveDraftBtn, #completeSessionBtn').prop('disabled', false);

    // Restore original button text
    $('#startSessionBtn, #saveDraftBtn, #completeSessionBtn').each(function () {
        const originalText = $(this).data('original-text');
        if (originalText) {
            $(this).html(originalText);
        }
    });
}

// Success and error message functions for better user feedback
function showSuccessMessage(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-blue-50 text-blue-700 border-blue-200';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';

    const alertHtml = `
        <div class="fixed top-4 right-4 z-50 ${alertClass} px-4 py-3 rounded-lg border shadow-lg max-w-sm">
            <div class="flex items-center">
                <span>${message}</span>
            </div>
        </div>
    `;

    $('body').append(alertHtml);

    // Auto remove after 3 seconds
    setTimeout(() => {
        $('.fixed.top-4.right-4').fadeOut(300, function () {
            $(this).remove();
        });
    }, 3000);
}

function showErrorMessage(message) {
    const alertHtml = `
        <div class="fixed top-4 right-4 z-50 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg shadow-lg max-w-sm">
            <div class="flex items-center">
                <span>${message}</span>
            </div>
        </div>
    `;

    $('body').append(alertHtml);

    // Auto remove after 5 seconds
    setTimeout(() => {
        $('.fixed.top-4.right-4').fadeOut(300, function () {
            $(this).remove();
        });
    }, 5000);
}

// View session details function (called from blade template)
function viewSessionDetails(sessionId) {
    $.ajax({
        url: `${window.treatmentTrackerRoutes.details}/${sessionId}/details`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                showSessionDetailsModal(response.data);
            } else {
                showErrorMessage('Failed to load session details');
            }
        },
        error: function () {
            showErrorMessage('Failed to load session details');
        }
    });
}

function showSessionDetailsModal(data) {
    const tracker = data.tracker;
    const assignment = tracker.therapist_assignment;

    let materialsHtml = '';
    if (tracker.materials_used && tracker.materials_used.length > 0) {
        materialsHtml = '<ul class="list-disc list-inside">';
        tracker.materials_used.forEach(material => {
            materialsHtml += `<li>${material.name} - ${material.quantity} ${material.unit}</li>`;
        });
        materialsHtml += '</ul>';
    } else {
        materialsHtml = '<p class="text-gray-500">No materials recorded</p>';
    }

    let vitalSignsHtml = '';
    if (tracker.vital_signs && Object.keys(tracker.vital_signs).length > 0) {
        vitalSignsHtml = '<ul class="list-disc list-inside">';
        Object.entries(tracker.vital_signs).forEach(([key, value]) => {
            vitalSignsHtml += `<li><strong>${key.toUpperCase()}:</strong> ${value}</li>`;
        });
        vitalSignsHtml += '</ul>';
    } else {
        vitalSignsHtml = '<p class="text-gray-500">No vital signs recorded</p>';
    }

    let imagesHtml = '';
    if (tracker.tracker_images && tracker.tracker_images.length > 0) {
        imagesHtml = '<div class="grid grid-cols-2 gap-2 mt-2">';
        tracker.tracker_images.forEach(image => {
            imagesHtml += `
                <div class="relative">
                    <img src="/storage/${image}" alt="Session image" class="w-full h-20 object-cover rounded">
                    <button onclick="deleteImage(${tracker.id}, '${image}')" 
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        });
        imagesHtml += '</div>';
    } else {
        imagesHtml = '<p class="text-gray-500">No images uploaded</p>';
    }

    // Simple alert with session details
    const detailsText = `
Session Details - Day ${assignment.assignment_date}

Session Information:
- Date: ${tracker.session_date}
- Time: ${tracker.formatted_session_time}
- Room: ${tracker.room ? tracker.room.room_number + ' - ' + tracker.room.room_type : 'N/A'}
- Status: ${tracker.status_text}
- Duration: ${data.duration_text}

Therapist Notes: ${tracker.therapist_notes || 'No notes recorded'}

Patient Feedback: ${tracker.patient_feedback || 'No feedback recorded'}

Materials Used: ${data.materials_used_text}

Vital Signs: ${data.vital_signs_text}
    `;

    alert(detailsText);
}

function deleteImage(trackerId, imagePath) {
    if (confirm('Are you sure you want to delete this image?')) {
        $.ajax({
            url: `${window.treatmentTrackerRoutes.deleteImage}/${trackerId}/image`,
            type: 'DELETE',
            data: {
                image_path: imagePath
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    showSuccessMessage('Image deleted successfully', 'info');
                    // Reload the modal or refresh the page
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showErrorMessage('Failed to delete image');
                }
            },
            error: function () {
                showErrorMessage('Failed to delete image');
            }
        });
    }
}
