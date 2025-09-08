document.addEventListener('DOMContentLoaded', function () {
    console.log('Dispense page loaded, initializing patient search...');

    // Check if routes are available
    if (!window.pharmacyRoutes) {
        console.error('Pharmacy routes not found!');
        return;
    }

    // Patient search elements
    const patientSearchInput = document.getElementById('patientSearchInput');
    const searchResultsContainer = document.getElementById('searchResults');
    const patientCards = document.getElementById('patientCards');
    const patientInfoDetails = document.getElementById('patientInfoDetails');
    const prescriptionSelect = document.querySelector('select[name="prescription_id"]');

    console.log('Patient search elements:', {
        patientSearchInput: !!patientSearchInput,
        searchResultsContainer: !!searchResultsContainer,
        patientCards: !!patientCards,
        patientInfoDetails: !!patientInfoDetails,
        prescriptionSelect: !!prescriptionSelect
    });

    let searchTimeout;
    let medicineSearchTimeout;
    let selectedPatientId = null;
    let selectedPrescriptionId = null;
    let selectedMedicines = [];
    let additionalMedicines = [];
    let billSummary = {
        subtotal: 0,
        gstAmount: 0,
        totalAmount: 0
    };

    // Make variables globally accessible for form validation
    window.selectedMedicines = selectedMedicines;
    window.additionalMedicines = additionalMedicines;

    // Initialize medicine search functionality
    const medicineSearchInput = document.getElementById('medicineSearchInput');
    const medicineSearchResults = document.getElementById('medicineSearchResults');
    const additionalMedicinesContainer = document.getElementById('additionalMedicinesContainer');

    if (medicineSearchInput) {
        // Medicine search input event listener
        medicineSearchInput.addEventListener('input', function () {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(medicineSearchTimeout);

            // Hide results if query is too short
            if (query.length < 2) {
                hideMedicineSearchResults();
                return;
            }

            // Set timeout to avoid too many requests
            medicineSearchTimeout = setTimeout(() => {
                searchMedicines(query);
            }, 300);
        });

        // Medicine search button click event
        const medicineSearchBtn = document.getElementById('medicineSearchBtn');
        if (medicineSearchBtn) {
            medicineSearchBtn.addEventListener('click', function () {
                const query = medicineSearchInput.value.trim();
                if (query.length >= 2) {
                    searchMedicines(query);
                }
            });
        }

        // Hide medicine search results when clicking outside
        document.addEventListener('click', function (e) {
            if (!medicineSearchInput.contains(e.target) && !medicineSearchResults.contains(e.target)) {
                hideMedicineSearchResults();
            }
        });
    }

    // Search medicines via AJAX
    function searchMedicines(query) {
        console.log('Searching medicines with query:', query);

        // Validate query
        if (!query || query.trim().length < 2) {
            console.log('Query too short, skipping search');
            return;
        }

        // Check if routes are available
        if (!window.pharmacyRoutes || !window.pharmacyRoutes.searchMedicines) {
            console.error('Pharmacy routes not available');
            showMedicineSearchResults(`
                <div class="p-3 text-red-500 text-center">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                    Pharmacy routes not configured. Please refresh the page.
                </div>
            `);
            return;
        }

        // Show loading state
        showMedicineSearchResults(`
            <div class="p-3 text-gray-500 text-center">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Searching medicines...
            </div>
        `);

        const url = `${window.pharmacyRoutes.searchMedicines}?query=${encodeURIComponent(query)}`;
        console.log('Fetching medicines from URL:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Medicine response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Medicine search response:', data);

                if (data.error) {
                    throw new Error(data.message || data.error);
                }

                // Log the first medicine to see its structure
                if (data.length > 0) {
                    console.log('First medicine data:', data[0]);
                    console.log('Price type:', typeof data[0].selling_price);
                    console.log('Stock type:', typeof data[0].stock_quantity);
                }

                displayMedicineSearchResults(data);
            })
            .catch(error => {
                console.error('Error searching medicines:', error);
                showMedicineSearchResults(`
                <div class="p-3 text-red-500 text-center">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                    ${error.message || 'Error searching medicines. Please try again.'}
                </div>
            `);
            });
    }

    // Display medicine search results
    function displayMedicineSearchResults(medicines) {
        if (!Array.isArray(medicines) || medicines.length === 0) {
            showMedicineSearchResults(`
                <div class="p-3 text-gray-500 text-center">
                    <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                    No medicines found
                </div>
            `);
            return;
        }

        console.log('Displaying medicine search results:', medicines);

        const resultsHtml = medicines.map(medicine => {
            const safeName = medicine.name || 'N/A';
            const safeCode = medicine.code || 'N/A';
            const safeType = medicine.type || 'N/A';
            const safeManufacturer = medicine.manufacturer || 'N/A';
            const safePrice = parseFloat(medicine.selling_price) || 0;
            const safeStock = parseInt(medicine.stock_quantity) || 0;
            const safeStrength = medicine.strength_dosage || 'N/A';

            console.log('Processing medicine:', {
                name: safeName,
                price: medicine.selling_price,
                parsedPrice: safePrice,
                stock: medicine.stock_quantity,
                parsedStock: safeStock
            });

            return `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                     onclick="selectAdditionalMedicine(${medicine.id}, '${safeName}', '${safeCode}', '${safeType}', '${safeManufacturer}', ${safePrice}, ${safeStock}, '${safeStrength}')">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">${safeName}</div>
                            <div class="text-xs text-gray-500">${safeCode} • ${safeType}</div>
                            <div class="text-xs text-gray-400">${safeManufacturer} • ${safeStrength}</div>
                            <div class="text-xs text-gray-400">Stock: ${safeStock} • Price: ₹${safePrice.toFixed(2)}</div>
                        </div>
                        <div class="flex-shrink-0 ml-2">
                            <i class="fa-solid fa-plus text-green-600"></i>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        showMedicineSearchResults(resultsHtml);
    }

    // Show medicine search results
    function showMedicineSearchResults(content) {
        if (medicineSearchResults) {
            medicineSearchResults.innerHTML = content;
            medicineSearchResults.classList.remove('hidden');
        }
    }

    // Hide medicine search results
    function hideMedicineSearchResults() {
        if (medicineSearchResults) {
            medicineSearchResults.classList.add('hidden');
        }
    }

    // Global function to select additional medicine
    window.selectAdditionalMedicine = function (id, name, code, type, manufacturer, price, stock, strength) {
        try {
            // Validate required parameters
            if (!id || !name) {
                console.error('Invalid medicine data:', { id, name });
                return;
            }

            // Convert price and stock to numbers
            const numericPrice = parseFloat(price) || 0;
            const numericStock = parseInt(stock) || 0;

            // Check if medicine is already added
            const existingMedicine = additionalMedicines.find(m => m.id === id);
            if (existingMedicine) {
                showErrorMessage('This medicine is already added to the bill');
                return;
            }

            // Check stock availability
            if (numericStock <= 0) {
                showErrorMessage('This medicine is out of stock');
                return;
            }

            // Hide search results
            hideMedicineSearchResults();

            // Clear search input
            if (medicineSearchInput) {
                medicineSearchInput.value = '';
            }

            // Add medicine to additional medicines list
            const medicine = {
                id: id,
                name: name,
                code: code,
                type: type,
                manufacturer: manufacturer,
                price: numericPrice,
                stock: numericStock,
                strength: strength,
                quantity: 1,
                totalPrice: numericPrice
            };

            additionalMedicines.push(medicine);

            // Update additional medicines display
            updateAdditionalMedicinesDisplay();

            // Update bill summary
            updateBillSummary();

            console.log('Additional medicine selected:', { id, name, price: numericPrice });
        } catch (error) {
            console.error('Error selecting additional medicine:', error);
        }
    };

    // Update additional medicines display
    function updateAdditionalMedicinesDisplay() {
        if (!additionalMedicinesContainer) return;

        if (additionalMedicines.length === 0) {
            additionalMedicinesContainer.innerHTML = `
                <div class="text-sm text-gray-500 text-center py-4">
                    No additional medicines added
                </div>
            `;
            return;
        }

        let medicinesHtml = '';
        additionalMedicines.forEach((medicine, index) => {
            medicinesHtml += `
                <div class="flex justify-between items-center p-3 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <h5 class="text-sm font-medium text-ayur-brown-800">${medicine.name}</h5>
                        <p class="text-xs text-ayur-brown-600">${medicine.code} • ${medicine.type}</p>
                        <p class="text-xs text-ayur-brown-500">${medicine.manufacturer} • ${medicine.strength}</p>
                        <p class="text-xs text-ayur-brown-500">Stock: ${medicine.stock} • Price: ₹${medicine.price.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            <label class="text-xs text-ayur-brown-700 mr-2">Qty:</label>
                            <input type="number" 
                                   class="w-16 px-2 py-1 border border-gray-300 rounded text-xs" 
                                   value="${medicine.quantity}" 
                                   min="1" 
                                   max="${medicine.stock}"
                                   onchange="updateAdditionalMedicineQuantity(${index}, this.value)">
                        </div>
                        <span class="text-xs text-ayur-brown-700">₹${medicine.totalPrice.toFixed(2)}</span>
                        <button type="button" 
                                class="text-red-500 hover:text-red-700 transition-colors"
                                onclick="removeAdditionalMedicine(${index})">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        additionalMedicinesContainer.innerHTML = medicinesHtml;
    }

    // Global function to update additional medicine quantity
    window.updateAdditionalMedicineQuantity = function (index, quantity) {
        const medicine = additionalMedicines[index];
        if (medicine) {
            const newQuantity = parseInt(quantity) || 1;

            // Validate quantity
            if (newQuantity > medicine.stock) {
                showErrorMessage(`Maximum available stock is ${medicine.stock}`);
                return;
            }

            if (newQuantity < 1) {
                showErrorMessage('Quantity must be at least 1');
                return;
            }

            medicine.quantity = newQuantity;
            medicine.totalPrice = medicine.price * newQuantity;

            updateAdditionalMedicinesDisplay();
            updateBillSummary();
        }
    };

    // Global function to remove additional medicine
    window.removeAdditionalMedicine = function (index) {
        additionalMedicines.splice(index, 1);
        updateAdditionalMedicinesDisplay();
        updateBillSummary();
    };
    // Initialize patient search functionality
    if (patientSearchInput) {
        // Patient search input event listener
        patientSearchInput.addEventListener('input', function () {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            // Hide results if query is too short
            if (query.length < 2) {
                hideSearchResults();
                return;
            }

            // Set timeout to avoid too many requests
            searchTimeout = setTimeout(() => {
                searchPatients(query);
            }, 300);
        });

        // Search button click event
        const searchBtn = document.getElementById('patientSearchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', function () {
                const query = patientSearchInput.value.trim();
                if (query.length >= 2) {
                    searchPatients(query);
                }
            });
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function (e) {
            if (!patientSearchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
                hideSearchResults();
            }
        });
    } else {
        console.error('Patient search input not found!');
    }

    // Search patients via AJAX (using Appointment module pattern)
    function searchPatients(query) {
        console.log('Searching patients with query:', query);

        // Validate query
        if (!query || query.trim().length < 2) {
            console.log('Query too short, skipping search');
            return;
        }

        // Show loading state
        showSearchResults(`
            <div class="p-3 text-gray-500 text-center">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Searching patients...
            </div>
        `);

        const url = `${window.pharmacyRoutes.searchPatient}?query=${encodeURIComponent(query)}`;
        console.log('Fetching from URL:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Search response:', data);

                if (data.error) {
                    throw new Error(data.message || data.error);
                }

                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Error searching patients:', error);
                showSearchResults(`
                <div class="p-3 text-red-500 text-center">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                    ${error.message || 'Error searching patients. Please try again.'}
                </div>
            `);
            });
    }

    // Display search results (using Appointment module pattern)
    function displaySearchResults(patients) {
        if (!Array.isArray(patients) || patients.length === 0) {
            showSearchResults(`
                <div class="p-3 text-gray-500 text-center">
                    <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                    No patients found
                </div>
            `);
            return;
        }

        const resultsHtml = patients.map(patient => {
            const safeUhid = patient.uhid || 'N/A';
            const safeName = patient.full_name || 'N/A';
            const safeGender = patient.gender || 'N/A';
            const safeMobile = patient.mobile || 'N/A';
            const safePrakriti = patient.prakriti || 'N/A';
            const safeAllergies = patient.allergies || 'None';
            const safePhotoPath = patient.photo_path || null;
            const age = patient.age || 'N/A';
            const genderInitial = safeGender.charAt(0).toUpperCase();

            return `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                     onclick="selectPatient(${patient.id}, '${safeUhid}', '${safeName}', '${safeGender}', '${age}', '${safeMobile}', '${safePrakriti}', '${safeAllergies}', '${safePhotoPath}')">
                    <div class="flex items-center">
                        <img class="h-16 w-16 rounded-full mr-4 object-cover"
                             src="${patient.photo_path ? '/' + patient.photo_path : '/backend-assets/media/uploads/download (3).png'}"
                             alt="Patient avatar"
                             onerror="this.src='/backend-assets/media/uploads/download (3).png'">
                    <div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">${safeName}</div>
                            <div class="text-xs text-gray-500">${safeUhid} • ${age}/${genderInitial}</div>
                            <div class="text-xs text-gray-400">${safeMobile}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        showSearchResults(resultsHtml);
    }

    // Show search results
    function showSearchResults(content) {
        if (searchResultsContainer) {
            searchResultsContainer.innerHTML = content;
            searchResultsContainer.classList.remove('hidden');
        }
    }

    // Hide search results
    function hideSearchResults() {
        if (searchResultsContainer) {
            searchResultsContainer.classList.add('hidden');
        }
    }

    // Global function to select patient (using Appointment module pattern)
    window.selectPatient = function (id, uhid, name, gender, age, mobile, prakriti, allergies, photoPath) {
        try {
            // Validate required parameters
            if (!id || !name) {
                console.error('Invalid patient data:', { id, name });
                return;
            }

            // Hide search results
            hideSearchResults();

            // Set selected patient ID
            selectedPatientId = id;
            const selectedPatientIdInput = document.getElementById('selectedPatientId');
            if (selectedPatientIdInput) {
                selectedPatientIdInput.value = id;
                console.log('Patient ID set in hidden field:', id);
            }

            // Clear search input
            if (patientSearchInput) {
                patientSearchInput.value = name;
            }

            // Update patient card display
            updatePatientCard(uhid, name, gender, age, mobile, prakriti, allergies, photoPath);

            // Load patient prescriptions
            loadPatientPrescriptions(id);

            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Patient Selected!',
                    text: `Patient ${name} has been selected successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            console.log('Patient selected:', { id, name, uhid });
        } catch (error) {
            console.error('Error selecting patient:', error);
            // Show error message to user
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error selecting patient. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        }
    };

    // Update patient card display

    // Enhanced Patient Card Display Function
    function updatePatientCard(uhid, name, gender, age, mobile, prakriti, allergies, photoPath) {
        const patientCards = document.getElementById('patientCards');
        const patientInfoDetails = document.getElementById('patientInfoDetails');

        if (!patientCards) return;

        const patientPhotoPath = photoPath ? '/' + photoPath : '/backend-assets/media/uploads/download (3).png';
        const genderInitial = gender.charAt(0).toUpperCase();
        const ageDisplay = age !== 'N/A' ? `${age} years` : 'Age not specified';

        // Enhanced patient card with better styling and more information
        patientCards.innerHTML = `
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 relative">
                    <img class="h-16 w-16 rounded-full object-cover border-2 border-ayur-green-200" 
                         src="${patientPhotoPath}" 
                         alt="Patient avatar" 
                         onerror="this.src='/backend-assets/media/uploads/download (3).png'">
                    <div class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-lg font-semibold text-ayur-brown-800 truncate">${name}</h4>
                    <div class="mt-1 space-y-1">
                        <p class="text-sm text-ayur-brown-600 flex items-center">
                            <i class="fa-solid fa-id-card mr-2 text-ayur-brown-500"></i>
                            UHID: ${uhid}
                        </p>
                        <p class="text-sm text-ayur-brown-600 flex items-center">
                            <i class="fa-solid fa-user mr-2 text-ayur-brown-500"></i>
                            ${ageDisplay}, ${gender}
                        </p>
                        <p class="text-sm text-ayur-brown-600 flex items-center">
                            <i class="fa-solid fa-phone mr-2 text-ayur-brown-500"></i>
                            ${mobile}
                        </p>
                    </div>
                    
                    <!-- Quick action buttons -->
                    <div class="mt-3 flex space-x-2">
                        <button type="button" class="text-xs bg-ayur-green-100 text-ayur-green-700 px-2 py-1 rounded-full hover:bg-ayur-green-200 transition-colors">
                            <i class="fa-solid fa-history mr-1"></i>
                            History
                        </button>
                        <button type="button" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full hover:bg-blue-200 transition-colors">
                            <i class="fa-solid fa-prescription mr-1"></i>
                            Prescriptions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Update detailed patient information if element exists
        if (patientInfoDetails) {
            const uhidElement = document.getElementById('patientUhid');
            const ageGenderElement = document.getElementById('patientAgeGender');
            const mobileElement = document.getElementById('patientMobile');
            const prakritiElement = document.getElementById('patientPrakriti');

            if (uhidElement) uhidElement.textContent = uhid;
            if (ageGenderElement) ageGenderElement.textContent = `${age}/${genderInitial}`;
            if (mobileElement) mobileElement.textContent = mobile;
            if (prakritiElement) prakritiElement.textContent = prakriti || 'N/A';

            patientInfoDetails.classList.remove('hidden');
        }

        // Update dosha and allergies display
        updateDoshaDisplay(prakriti);
        updateAllergiesDisplay(allergies);
    }

    // Update dosha type display
    function updateDoshaDisplay(prakriti) {
        const doshaLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.includes('Dosha Type'));
        if (doshaLabel) {
            const doshaContainer = doshaLabel.nextElementSibling;
            if (doshaContainer) {
                if (prakriti && prakriti.trim() !== '') {
                    const doshas = prakriti.split('-').map(d => d.trim());
                    const doshaHtml = doshas.map(dosha => {
                        const colorClass = getDoshaColorClass(dosha);
                        return `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${colorClass}">${dosha}</span>`;
                    }).join('');
                    doshaContainer.innerHTML = doshaHtml;
                } else {
                    doshaContainer.innerHTML = '<span class="text-xs text-gray-500">Select a patient to view dosha type</span>';
                }
            }
        }
    }

    // Update allergies display
    function updateAllergiesDisplay(allergies) {
        const allergiesLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.includes('Allergies'));
        if (allergiesLabel) {
            const allergiesContainer = allergiesLabel.nextElementSibling;
            if (allergiesContainer) {
                if (allergies && allergies !== 'None' && allergies.trim() !== '') {
                    const allergyList = allergies.split(',').map(a => a.trim());
                    const allergiesHtml = allergyList.map(allergy =>
                        `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">${allergy}</span>`
                    ).join('');
                    allergiesContainer.innerHTML = allergiesHtml;
                } else {
                    allergiesContainer.innerHTML = '<span class="text-xs text-gray-500">Select a patient to view allergies</span>';
                }
            }
        }
    }

    // Get dosha color class
    function getDoshaColorClass(dosha) {
        const doshaColors = {
            'Vata': 'bg-ayur-yellow-100 text-ayur-yellow-800',
            'Pitta': 'bg-ayur-red-100 text-ayur-red-800',
            'Kapha': 'bg-ayur-brown-100 text-ayur-brown-800'
        };
        return doshaColors[dosha] || 'bg-gray-100 text-gray-800';
    }

    // Load patient prescriptions
    function loadPatientPrescriptions(patientId) {
        if (!patientId) return;

        const url = `${window.pharmacyRoutes.getPrescriptions}?patient_id=${patientId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.prescriptions) {
                    updatePrescriptionDropdown(data.prescriptions);
                } else {
                    showErrorMessage(data.message || 'No prescriptions found for this patient');
                }
            })
            .catch(error => {
                console.error('Error loading prescriptions:', error);
                showErrorMessage('Error loading prescriptions');
            });
    }

    // Update prescription dropdown
    function updatePrescriptionDropdown(prescriptions) {
        if (!prescriptionSelect) return;

        // Clear existing options except the first one
        prescriptionSelect.innerHTML = '<option value="">Select Prescription</option>';

        prescriptions.forEach(prescription => {
            const option = document.createElement('option');
            option.value = prescription.id;
            option.textContent = `Dr. ${prescription.doctor?.name || 'Unknown'} - ${prescription.prescription_date}`;
            prescriptionSelect.appendChild(option);
        });
    }

    // Initialize prescription selection
    if (prescriptionSelect) {
        prescriptionSelect.addEventListener('change', function () {
            console.log('Prescription selection changed:', this.value);
            selectedPrescriptionId = this.value;

            // Update the hidden input for form submission
            const selectedPrescriptionIdInput = document.getElementById('selectedPrescriptionId');
            if (selectedPrescriptionIdInput) {
                selectedPrescriptionIdInput.value = this.value;
                console.log('Prescription ID set in hidden field:', this.value);
            }

            if (this.value && this.value !== '') {
                loadPrescriptionMedicines(this.value);
            } else {
                clearPrescriptionMedicines();
                selectedPrescriptionId = null;
                if (selectedPrescriptionIdInput) {
                    selectedPrescriptionIdInput.value = '';
                }
            }
        });
    }

    // Load prescription medicines
    function loadPrescriptionMedicines(prescriptionId) {
        if (!prescriptionId) return;

        const url = `${window.pharmacyRoutes.getPrescriptions}?patient_id=${selectedPatientId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.prescriptions) {
                    const prescription = data.prescriptions.find(p => p.id == prescriptionId);
                    if (prescription && prescription.items) {
                        updatePrescriptionHeader(prescription);
                        updatePrescriptionMedicines(prescription.items);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading prescription medicines:', error);
            });
    }

    // Update prescription header
    function updatePrescriptionHeader(prescription) {
        const headerElement = document.querySelector('.bg-ayur-green-50 h4');
        if (headerElement) {
            const doctorName = prescription.doctor?.name || 'Unknown Doctor';
            const prescriptionDate = prescription.prescription_date;
            headerElement.textContent = `Prescribed by: Dr. ${doctorName} on ${prescriptionDate}`;
        }
    }

    // Update prescription medicines display
    function updatePrescriptionMedicines(items) {
        const medicationsContainer = document.querySelector('.p-4.space-y-3');
        if (!medicationsContainer) return;

        selectedMedicines = [];
        let medicationsHtml = '';

        items.forEach((item, index) => {
            const medicineId = item.medicine_id || `medication_${index + 1}`;

            // Calculate quantity based on duration and frequency
            let calculatedQuantity = 1; // Default quantity
            if (item.duration && item.frequency) {
                // Extract numeric values from duration and frequency
                const durationMatch = item.duration.match(/(\d+)/);
                const frequencyMatch = item.frequency.match(/(\d+)/);

                if (durationMatch && frequencyMatch) {
                    const days = parseInt(durationMatch[1]);
                    const timesPerDay = parseInt(frequencyMatch[1]);
                    calculatedQuantity = days * timesPerDay;
                } else if (durationMatch) {
                    // If only duration is available, assume once daily
                    calculatedQuantity = parseInt(durationMatch[1]);
                }
            }

            // Ensure minimum quantity of 1
            calculatedQuantity = Math.max(1, calculatedQuantity);

            selectedMedicines.push({
                id: medicineId,
                medicine_id: item.medicine_id,
                name: item.name,
                dosage: item.dosage,
                frequency: item.frequency,
                duration: item.duration,
                instructions: item.instructions,
                quantity: calculatedQuantity,
                unit_price: 100, // Default price - should be fetched from medicine data
                total_price: calculatedQuantity * 100,
                selected: true // Default to selected
            });

            medicationsHtml += `
                <div id="${medicineId}" class="flex justify-between items-center p-3 border-b border-gray-100 last:border-b-0 bg-white hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <h5 class="text-sm font-medium text-ayur-brown-800">${item.name}</h5>
                        <p class="text-xs text-ayur-brown-600">${item.dosage || 'N/A'}, ${item.frequency || 'N/A'}</p>
                        <p class="text-xs text-ayur-brown-500">${item.duration || 'N/A'} • ${item.instructions || 'No special instructions'}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                            <label class="text-xs text-ayur-brown-700 mr-2">Qty:</label>
                            <input type="number" 
                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center" 
                                   value="${calculatedQuantity}" 
                                   min="1" 
                                   max="999"
                                   onchange="updateMedicineQuantity('${medicineId}', this.value)"
                                   onfocus="this.select()">
                        </div>
                        <div class="text-xs text-ayur-brown-600">
                            ₹<span id="price_${medicineId}">100.00</span>
                        </div>
                        <input type="checkbox" 
                               class="form-checkbox text-ayur-green-600 h-4 w-4" 
                               checked 
                               onchange="toggleMedicineSelection('${medicineId}', this.checked)"
                               title="Include in dispensation">
                    </div>
                </div>
            `;
        });

        medicationsContainer.innerHTML = medicationsHtml;

        // Fetch actual medicine prices for prescription items
        fetchMedicinePricesForPrescription();

        updateBillSummary();
    }

    // Fetch actual medicine prices for prescription items
    function fetchMedicinePricesForPrescription() {
        if (!selectedMedicines.length) return;

        selectedMedicines.forEach(async (medicine, index) => {
            if (medicine.medicine_id) {
                try {
                    // Search for the medicine to get its current price
                    const response = await fetch(`${window.pharmacyRoutes.searchMedicines}?query=${encodeURIComponent(medicine.name)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const medicines = await response.json();
                        const foundMedicine = medicines.find(m => m.id == medicine.medicine_id || m.name === medicine.name);

                        if (foundMedicine && foundMedicine.selling_price) {
                            const actualPrice = parseFloat(foundMedicine.selling_price) || 100;

                            // Update the medicine price in selectedMedicines array
                            selectedMedicines[index].unit_price = actualPrice;
                            selectedMedicines[index].total_price = selectedMedicines[index].quantity * actualPrice;

                            // Update the price display in the UI
                            const priceElement = document.getElementById(`price_${medicine.id}`);
                            if (priceElement) {
                                priceElement.textContent = actualPrice.toFixed(2);
                            }

                            // Update bill summary
                            updateBillSummary();
                        }
                    }
                } catch (error) {
                    console.warn(`Could not fetch price for medicine: ${medicine.name}`, error);
                }
            }
        });
    }

    // Clear prescription medicines
    function clearPrescriptionMedicines() {
        const medicationsContainer = document.querySelector('.p-4.space-y-3');
        if (medicationsContainer) {
            medicationsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Select a prescription to view medications</p>';
        }

        // Clear prescription header
        const headerElement = document.querySelector('.bg-ayur-green-50 h4');
        if (headerElement) {
            headerElement.textContent = 'Select a prescription to view medications';
        }

        selectedMedicines = [];
        updateBillSummary();
    }

    // Global function to update medicine quantity
    window.updateMedicineQuantity = function (medicineId, quantity) {
        const medicine = selectedMedicines.find(m => m.id === medicineId);
        if (medicine) {
            const newQuantity = parseInt(quantity) || 1;

            // Validate quantity
            if (newQuantity < 1) {
                showErrorMessage('Quantity must be at least 1');
                // Reset the input field to the current quantity
                const input = document.querySelector(`input[onchange="updateMedicineQuantity('${medicineId}', this.value)"]`);
                if (input) {
                    input.value = medicine.quantity;
                }
                return;
            }

            if (newQuantity > 999) {
                showErrorMessage('Quantity cannot exceed 999');
                // Reset the input field to the current quantity
                const input = document.querySelector(`input[onchange="updateMedicineQuantity('${medicineId}', this.value)"]`);
                if (input) {
                    input.value = medicine.quantity;
                }
                return;
            }

            // Update the medicine quantity and total price
            medicine.quantity = newQuantity;
            medicine.total_price = medicine.quantity * (medicine.unit_price || 100);

            // Update the price display
            const priceElement = document.getElementById(`price_${medicineId}`);
            if (priceElement) {
                const totalPrice = medicine.quantity * (medicine.unit_price || 100);
                priceElement.textContent = (medicine.unit_price || 100).toFixed(2);
            }

            // Update bill summary
            updateBillSummary();

            console.log(`Updated medicine ${medicine.name}: Qty=${newQuantity}, Unit Price=${medicine.unit_price}, Total=${medicine.total_price}`);
        }
    };

    // Global function to toggle medicine selection
    window.toggleMedicineSelection = function (medicineId, isSelected) {
        const medicine = selectedMedicines.find(m => m.id === medicineId);
        if (medicine) {
            medicine.selected = isSelected;
            updateBillSummary();
        }
    };


    // Update bill summary with comprehensive calculation
    function updateBillSummary() {
        const billContainer = document.getElementById('billSummaryContent');
        if (!billContainer) return;

        let subtotal = 0;
        let totalItems = 0;
        let billItemsHtml = '';

        // Process prescription medicines
        const prescriptionMedicines = selectedMedicines.filter(m => m.selected !== false);
        prescriptionMedicines.forEach(medicine => {
            const unitPrice = parseFloat(medicine.unit_price) || 100;
            const quantity = parseInt(medicine.quantity) || 0;
            const totalPrice = quantity * unitPrice;

            if (quantity > 0) {
                subtotal += totalPrice;
                totalItems += quantity;

                billItemsHtml += `
                    <div class="flex justify-between items-start border-b border-gray-200 pb-3 mb-3">
                        <div class="flex-1 pr-4">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-ayur-brown-800">${medicine.name}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Prescription
                                </span>
                            </div>
                            <div class="text-xs text-ayur-brown-600 space-y-0.5">
                                <div>${medicine.dosage || 'N/A'}, ${medicine.frequency || 'N/A'}</div>
                                <div class="flex items-center space-x-4">
                                    <span>Qty: ${quantity}</span>
                                    <span>Rate: ₹${unitPrice.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-ayur-brown-800">₹${totalPrice.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            }
        });

        // Process additional medicines
        additionalMedicines.forEach(medicine => {
            const totalPrice = parseFloat(medicine.totalPrice) || 0;
            const quantity = parseInt(medicine.quantity) || 0;

            if (quantity > 0) {
                subtotal += totalPrice;
                totalItems += quantity;

                billItemsHtml += `
                    <div class="flex justify-between items-start border-b border-gray-200 pb-3 mb-3">
                        <div class="flex-1 pr-4">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-ayur-brown-800">${medicine.name}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Additional
                                </span>
                            </div>
                            <div class="text-xs text-ayur-brown-600 space-y-0.5">
                                <div>${medicine.code} • ${medicine.type}</div>
                                <div>${medicine.manufacturer} • ${medicine.strength}</div>
                                <div class="flex items-center space-x-4">
                                    <span>Qty: ${quantity}</span>
                                    <span>Rate: ₹${medicine.price.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-ayur-brown-800">₹${totalPrice.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            }
        });

        // Calculate taxes and totals
        const gstRate = 0.12; // 12% GST
        const gstAmount = subtotal * gstRate;
        const totalAmount = subtotal + gstAmount;

        // Update global bill summary
        billSummary = {
            subtotal: subtotal,
            gstAmount: gstAmount,
            totalAmount: totalAmount,
            itemCount: totalItems
        };

        // Display enhanced bill summary
        if (subtotal > 0) {
            billContainer.innerHTML = `
                <div class="space-y-4">
                    <!-- Header with item count -->
                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <h4 class="text-lg font-semibold text-ayur-brown-800">Bill Summary</h4>
                        <span class="text-sm text-ayur-brown-600 bg-ayur-brown-100 px-2 py-1 rounded-full">
                            ${totalItems} item${totalItems !== 1 ? 's' : ''}
                        </span>
                    </div>
                    
                    <!-- Bill items -->
                    <div class="max-h-60 overflow-y-auto">
                        ${billItemsHtml}
                    </div>
                    
                    <!-- Totals section -->
                    <div class="border-t border-gray-200 pt-3 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-ayur-brown-700">Subtotal</span>
                            <span class="font-medium text-ayur-brown-800">₹${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-ayur-brown-700">GST (12%)</span>
                            <span class="font-medium text-ayur-brown-800">₹${gstAmount.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                            <span class="text-lg font-bold text-ayur-brown-800">Total Amount</span>
                            <span class="text-lg font-bold text-ayur-brown-800">₹${totalAmount.toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <!-- Summary info -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-3 mt-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-check-circle text-green-600"></i>
                                <span class="text-sm font-medium text-green-700">Ready for dispensing</span>
                            </div>
                            <div class="text-xs text-gray-600">
                                ${prescriptionMedicines.length} prescription + ${additionalMedicines.length} additional
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Enhanced empty state
            billContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fa-solid fa-receipt text-2xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500">Select medications to view bill summary</p>
                </div>
            `;
        }

        // Update form hidden inputs for submission
        updateFormInputs();
    }

    // Helper function to update form inputs
    function updateFormInputs() {
        const formElements = {
            subtotal: document.getElementById('formSubtotal'),
            gstAmount: document.getElementById('formGstAmount'),
            totalAmount: document.getElementById('formTotalAmount'),
            selectedMedicines: document.getElementById('selectedMedicines')
        };

        if (formElements.subtotal) formElements.subtotal.value = billSummary.subtotal.toFixed(2);
        if (formElements.gstAmount) formElements.gstAmount.value = billSummary.gstAmount.toFixed(2);
        if (formElements.totalAmount) formElements.totalAmount.value = billSummary.totalAmount.toFixed(2);

        if (formElements.selectedMedicines) {
            const allMedicines = [
                ...selectedMedicines.filter(m => m.selected !== false),
                ...additionalMedicines
            ];
            formElements.selectedMedicines.value = JSON.stringify(allMedicines);
        }

        // Update global variables for form validation
        window.selectedMedicines = selectedMedicines;
        window.additionalMedicines = additionalMedicines;
    }

    // Initialize payment method selection
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            console.log('Payment method selected:', this.value);
        });
    });

    // Initialize dispense status selection
    const dispenseStatusSelect = document.querySelector('select[name="dispense_status"]');
    if (dispenseStatusSelect) {
        dispenseStatusSelect.addEventListener('change', function () {
            console.log('Dispense status:', this.value);
        });
    }

    // Initialize dispensed by selection
    const dispensedBySelect = document.querySelector('select[name="dispensed_by"]');
    if (dispensedBySelect) {
        dispensedBySelect.addEventListener('change', function () {
            console.log('Dispensed by:', this.value);
        });
    }

    // Initialize dispense button
    const dispenseButton = Array.from(document.querySelectorAll('button')).find(button =>
        button.textContent.includes('Dispense Medication')
    );
    if (dispenseButton) {
        dispenseButton.addEventListener('click', function (e) {
            e.preventDefault();
            processDispense();
        });
    }

    // Process dispense
    function processDispense() {
        console.log('Processing dispense...');

        // Debug current state
        console.log('Current state before validation:', {
            selectedPatientId,
            selectedPrescriptionId,
            prescriptionSelectValue: prescriptionSelect ? prescriptionSelect.value : 'N/A',
            selectedMedicines: selectedMedicines.length,
            additionalMedicines: additionalMedicines.length
        });

        // Validate form
        if (!validateDispenseForm()) {
            return;
        }

        // Collect form data
        const formData = collectDispenseFormData();

        // Show loading state
        const dispenseButton = Array.from(document.querySelectorAll('button')).find(button =>
            button.textContent.includes('Dispense Medication')
        );
        const originalText = dispenseButton.innerHTML;
        dispenseButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...';
        dispenseButton.disabled = true;

        // Send to server
        fetch(window.pharmacyRoutes.processDispense, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Medication dispensed successfully! Receipt #: ' + data.receipt_number);
                    // Reset form
                    resetDispenseForm();
                } else {
                    showErrorMessage(data.message || 'Failed to dispense medication');
                }
            })
            .catch(error => {
                console.error('Dispense error:', error);
                showErrorMessage('An error occurred while processing the dispense');
            })
            .finally(() => {
                // Restore button state
                dispenseButton.innerHTML = originalText;
                dispenseButton.disabled = false;
            });
    }

    // Validate dispense form
    function validateDispenseForm() {
        const errors = [];

        // Debug logging
        console.log('Validating form with:', {
            selectedPatientId,
            selectedPrescriptionId,
            selectedMedicines: selectedMedicines.length,
            additionalMedicines: additionalMedicines.length
        });

        // Patient ID is now optional - removed the required validation

        // Check prescription selection more thoroughly
        const prescriptionCheck = checkPrescriptionSelection();
        if (!prescriptionCheck.hasSelection) {
            errors.push('Please select a prescription');
        } else if (prescriptionCheck.selectedValue) {
            selectedPrescriptionId = prescriptionCheck.selectedValue;
        }

        // Check if any medicines are selected (either prescription or additional)
        const hasPrescriptionMedicines = selectedMedicines.length > 0 && selectedMedicines.some(m => m.selected !== false);
        const hasAdditionalMedicines = additionalMedicines.length > 0;

        if (!hasPrescriptionMedicines && !hasAdditionalMedicines) {
            errors.push('No medications selected');
        }

        const paymentMethod = document.querySelector('input[name="payment"]:checked');
        if (!paymentMethod) {
            errors.push('Please select a payment method');
        }

        const dispenseStatus = document.querySelector('select[name="dispense_status"]');
        if (!dispenseStatus || !dispenseStatus.value) {
            errors.push('Please select dispense status');
        }

        const dispensedBy = document.querySelector('select[name="dispensed_by"]');
        if (!dispensedBy || !dispensedBy.value) {
            errors.push('Please select who is dispensing');
        }

        if (errors.length > 0) {
            showErrorMessage(errors.join('\n'));
            return false;
        }

        return true;
    }

    // Collect dispense form data
    function collectDispenseFormData() {
        const paymentMethod = document.querySelector('input[name="payment"]:checked');
        const dispenseStatus = document.querySelector('select[name="dispense_status"]');
        const dispensedBy = document.querySelector('select[name="dispensed_by"]');
        const specialInstructions = document.querySelector('textarea[name="special_instructions"]');

        // Ensure we have the latest prescription ID
        const prescriptionSelectElement = document.querySelector('select[name="prescription_id"]');
        const finalPrescriptionId = selectedPrescriptionId || (prescriptionSelectElement ? prescriptionSelectElement.value : null);

        console.log('Collecting form data with prescription ID:', finalPrescriptionId);

        // Calculate totals
        let subtotal = 0;
        const medicines = [];

        // Add prescription medicines
        selectedMedicines.forEach(medicine => {
            if (medicine.selected !== false && medicine.quantity > 0) {
                const unitPrice = parseFloat(medicine.unit_price) || 100;
                const totalPrice = medicine.quantity * unitPrice;
                subtotal += totalPrice;
                medicines.push({
                    medicine_id: medicine.medicine_id || medicine.id,
                    quantity: medicine.quantity,
                    unit_price: unitPrice,
                    total_price: totalPrice,
                    is_additional: false
                });
            }
        });

        // Add additional medicines
        additionalMedicines.forEach(medicine => {
            if (medicine.quantity > 0) {
                const totalPrice = parseFloat(medicine.totalPrice) || 0;
                subtotal += totalPrice;
                medicines.push({
                    medicine_id: medicine.id,
                    quantity: medicine.quantity,
                    unit_price: medicine.price,
                    total_price: totalPrice,
                    is_additional: true
                });
            }
        });

        const gstAmount = subtotal * 0.12;
        const totalAmount = subtotal + gstAmount;

        return {
            patient_id: selectedPatientId || null,
            prescription_id: finalPrescriptionId,
            medicines: medicines,
            payment_mode: paymentMethod ? paymentMethod.value : 'cash',
            dispense_status: dispenseStatus ? dispenseStatus.value : 'ready',
            dispensed_by: dispensedBy ? dispensedBy.value : '',
            special_instructions: specialInstructions ? specialInstructions.value : '',
            subtotal: subtotal,
            gst_amount: gstAmount,
            total_amount: totalAmount
        };
    }

    // Reset dispense form
    function resetDispenseForm() {
        // Reset patient selection
        selectedPatientId = null;
        if (patientSearchInput) {
            patientSearchInput.value = '';
        }

        // Reset patient card
        const patientCards = document.getElementById('patientCards');
        const patientInfoDetails = document.getElementById('patientInfoDetails');

        if (patientCards) {
            patientCards.innerHTML = `
                <div class="flex items-start">
                    <img class="h-12 w-12 rounded-full object-cover" src="/backend-assets/media/uploads/download (3).png" alt="Patient avatar">
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-ayur-brown-800">Select a patient</h4>
                        <p class="text-xs text-ayur-brown-600">Search by UHID, name, or mobile</p>
                    </div>
                </div>
            `;
        }

        // Hide patient details section
        if (patientInfoDetails) {
            patientInfoDetails.classList.add('hidden');
        }

        // Reset prescription selection
        selectedPrescriptionId = null;
        if (prescriptionSelect) {
            prescriptionSelect.value = '';
        }

        // Clear medications
        selectedMedicines = [];
        clearPrescriptionMedicines();

        // Reset form fields
        const specialInstructions = document.querySelector('textarea[name="special_instructions"]');
        if (specialInstructions) {
            specialInstructions.value = '';
        }

        // Reset payment method
        const paymentRadios = document.querySelectorAll('input[name="payment"]');
        paymentRadios.forEach(radio => {
            radio.checked = false;
        });

        // Reset dispense status
        const dispenseStatus = document.querySelector('select[name="dispense_status"]');
        if (dispenseStatus) {
            dispenseStatus.value = '';
        }

        // Reset dispensed by
        const dispensedBy = document.querySelector('select[name="dispensed_by"]');
        if (dispensedBy) {
            dispensedBy.value = '';
        }

        // Reset dosha and allergies display
        updateDoshaDisplay('');
        updateAllergiesDisplay('None');

        console.log('Form reset successfully');
    }

    // Show success message
    function showSuccessMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 5000,
                showConfirmButton: true
            });
        } else {
            alert(message);
        }
    }

    // Helper function to check prescription selection status
    function checkPrescriptionSelection() {
        const prescriptionSelectElement = document.querySelector('select[name="prescription_id"]');
        const currentValue = prescriptionSelectElement ? prescriptionSelectElement.value : null;

        console.log('Prescription selection check:', {
            selectedPrescriptionId,
            selectElementValue: currentValue,
            selectElementExists: !!prescriptionSelectElement,
            hasOptions: prescriptionSelectElement ? prescriptionSelectElement.options.length > 1 : false
        });

        return {
            hasSelection: !!(selectedPrescriptionId || currentValue),
            selectedValue: selectedPrescriptionId || currentValue,
            elementExists: !!prescriptionSelectElement
        };
    }

    // Show error message
    function showErrorMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK'
            });
        } else {
            alert(message);
        }
    }

    // Test patient search functionality
    const testButton = document.getElementById('testPatientSearch');
    if (testButton) {
        testButton.addEventListener('click', function () {
            console.log('Testing patient search...');
            if (patientSearchInput) {
                patientSearchInput.value = 'test';
                patientSearchInput.dispatchEvent(new Event('input'));
            }
        });
    }


    // Test medicine search functionality
    window.testMedicineSearch = function () {
        console.log('Testing medicine search...');
        if (medicineSearchInput) {
            medicineSearchInput.value = 'test';
            medicineSearchInput.dispatchEvent(new Event('input'));
        }
    };

    // Function to refresh patient data from Appointment module
    window.refreshPatientData = function () {
        console.log('Refreshing patient data...');

        // Show loading state
        const refreshBtn = document.getElementById('refreshPatientBtn');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        refreshBtn.disabled = true;

        // Clear current patient selection
        selectedPatientId = null;
        const selectedPatientIdInput = document.getElementById('selectedPatientId');
        if (selectedPatientIdInput) {
            selectedPatientIdInput.value = '';
        }

        // Clear patient search input
        if (patientSearchInput) {
            patientSearchInput.value = '';
        }

        // Reset patient card display
        const patientCards = document.getElementById('patientCards');
        if (patientCards) {
            patientCards.innerHTML = `
                <div class="flex items-start">
                    <img class="h-12 w-12 rounded-full object-cover" src="/backend-assets/media/uploads/download (3).png" alt="Patient avatar">
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-ayur-brown-800">Select a patient</h4>
                        <p class="text-xs text-ayur-brown-600">Search by UHID, name, or mobile</p>
                    </div>
                </div>
            `;
        }

        // Hide patient details section
        const patientInfoDetails = document.getElementById('patientInfoDetails');
        if (patientInfoDetails) {
            patientInfoDetails.classList.add('hidden');
        }

        // Clear prescription selection
        selectedPrescriptionId = null;
        const prescriptionSelect = document.querySelector('select[name="prescription_id"]');
        if (prescriptionSelect) {
            prescriptionSelect.value = '';
        }

        // Clear medications
        selectedMedicines = [];
        additionalMedicines = [];
        clearPrescriptionMedicines();
        updateBillSummary();

        // Reset dosha and allergies display
        updateDoshaDisplay('');
        updateAllergiesDisplay('None');

        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Patient Data Refreshed!',
                text: 'Patient data has been refreshed successfully. You can now search for a new patient.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Restore button state
        setTimeout(() => {
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }, 1000);

        console.log('Patient data refreshed successfully');
    };

    // Function to get patient data from Appointment module
    window.getPatientFromAppointment = function (patientId) {
        console.log('Getting patient data from Appointment module:', patientId);

        if (!patientId) {
            console.error('Patient ID is required');
            return;
        }

        // Show loading state
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Loading Patient Data',
                text: 'Fetching patient information from Appointment module...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Fetch patient data from Appointment module
        fetch(`/appointment/search-patient?query=${patientId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.pharmacyRoutes.csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const patient = data[0]; // Get the first patient

                    // Select the patient using the existing function
                    selectPatient(
                        patient.id,
                        patient.uhid,
                        patient.full_name,
                        patient.gender,
                        patient.age,
                        patient.mobile,
                        patient.prakriti,
                        patient.allergies,
                        patient.photo_path
                    );

                    // Close loading dialog
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }

                    console.log('Patient data loaded from Appointment module:', patient);
                } else {
                    throw new Error('Patient not found in Appointment module');
                }
            })
            .catch(error => {
                console.error('Error getting patient from Appointment module:', error);

                // Close loading dialog and show error
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load patient data from Appointment module. Please try searching manually.',
                        confirmButtonText: 'OK'
                    });
                }
            });
    };

    // Function to check current form state
    window.checkFormState = function () {
        console.log('Current form state:', {
            patient_id: document.getElementById('selectedPatientId')?.value || 'Not set',
            prescription_id: document.getElementById('selectedPrescriptionId')?.value || 'Not set',
            selectedMedicines: selectedMedicines.length,
            additionalMedicines: additionalMedicines.length,
            billSummary: billSummary
        });
    };

    console.log('Dispense functionality initialized successfully');
    console.log('Available test functions:testMedicineSearch(), checkFormState(), refreshPatientData(), getPatientFromAppointment()');


});
