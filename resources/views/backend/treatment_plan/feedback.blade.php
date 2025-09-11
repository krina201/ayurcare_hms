@extends('backend.layouts.master')

@section('styles')
@endsection

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-4">
        <!-- TABS -->
        <div id="treatmentTabs" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Treatment Plans
                    </button>
                    <button
                        class="py-2 px-4 border-b-2 border-transparent text-ayur-brown-600 hover:text-ayur-brown-800 hover:border-ayur-brown-300">
                        Day-wise Tracker
                    </button>
                    <button class="py-2 px-4 border-b-2 border-ayur-green-500 text-ayur-green-600 font-medium">
                        Outcome &amp; Feedback
                    </button>
                </nav>
            </div>
        </div>

        <!-- PATIENT SELECTION SECTION -->
        <div id="patientSelectionSection" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-ayur-brown-800 mb-4">Select Patient for Evaluation</h3>

            <form id="feedbackForm" method="POST" action="{{ route('treatment-plan.feedback.store') }}">
                @csrf

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-search text-ayur-brown-400"></i>
                            </div>
                            <input type="text" id="patientSearchInput"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-ayur-green-500 focus:border-ayur-green-500"
                                placeholder="Search by patient name, UHID, or treatment...">
                            <input type="hidden" name="patient_id" id="selectedPatientId" required>
                        </div>

                        <!-- Patient Search Results -->
                        <div id="patientSearchResults"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                            <div class="max-h-60 overflow-y-auto">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>

                        <!-- Selected Patient Display -->
                        <div id="selectedPatientDisplay" class="mt-2 p-3 bg-ayur-offwhite rounded-lg hidden">
                            <div class="flex items-center">
                                <img id="selectedPatientPhoto" class="h-8 w-8 rounded-full object-cover mr-3" src=""
                                    alt="">
                                <div>
                                    <div class="text-sm font-medium text-ayur-brown-800" id="selectedPatientName"></div>
                                    <div class="text-xs text-ayur-brown-600" id="selectedPatientDetails"></div>
                                </div>
                                <button type="button" id="clearPatientSelection"
                                    class="ml-auto text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <select id="treatmentTypeFilter"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-ayur-green-500 focus:border-ayur-green-500 py-2 px-3">
                            <option value="">All Treatment Types</option>
                            <option value="abhyanga">Abhyanga</option>
                            <option value="shirodhara">Shirodhara</option>
                            <option value="nasya">Nasya</option>
                            <option value="basti">Basti</option>
                            <option value="virechana">Virechana</option>
                            <option value="panchakarma">Panchakarma</option>
                        </select>
                    </div>
                </div>

                <!-- Test button to show sections (remove this in production) -->
                <div class="mt-4">
                    <button type="button" id="testShowSections"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Test: Show All Sections
                    </button>
                </div>
            </form>
        </div>

        <!-- PATIENT CARD -->
        <div id="patientCard" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                <div class="flex items-center">
                    <img class="h-16 w-16 rounded-full object-cover"
                        src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg"
                        alt="Patient avatar" id="patientAvatar">
                    <div class="ml-4">
                        <h3 class="text-xl font-semibold text-ayur-brown-800" id="patientName">Patient Name</h3>
                        <div class="flex flex-wrap items-center mt-1">
                            <span class="text-sm text-ayur-brown-600 mr-3" id="patientUHID">UHID: -</span>
                            <span class="text-sm text-ayur-brown-600 mr-3" id="patientAgeGender">-</span>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800"
                                id="patientPrakriti">
                                -
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 md:mt-0 flex flex-col md:items-end">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-ayur-brown-600 mr-2">Treatment:</span>
                        <span class="text-sm font-semibold text-ayur-brown-800" id="treatmentType">-</span>
                    </div>
                    <div class="flex items-center mt-1">
                        <span class="text-sm font-medium text-ayur-brown-600 mr-2">Duration:</span>
                        <span class="text-sm font-semibold text-ayur-brown-800" id="treatmentDuration">-</span>
                    </div>
                    <div class="flex items-center mt-1">
                        <span class="text-sm font-medium text-ayur-brown-600 mr-2">Attending Therapist:</span>
                        <span class="text-sm font-semibold text-ayur-brown-800" id="attendingTherapist">-</span>
                    </div>
                </div>
            </div>

            <div class="bg-ayur-offwhite rounded-lg p-4">
                <h4 class="font-medium text-ayur-brown-800 mb-2">Treatment Progress</h4>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-ayur-green-600 h-2.5 rounded-full" id="progressBar" style="width: 0%"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-ayur-brown-600" id="startDate">Started: -</span>
                    <span class="text-xs font-medium text-ayur-green-600" id="progressText">0% completed</span>
                    <span class="text-xs text-ayur-brown-600" id="endDate">Ends: -</span>
                </div>
            </div>
        </div>

        <!-- TREATMENT OUTCOME EVALUATION -->
        <div id="treatmentOutcomeSection" class="grid md:grid-cols-2 gap-6 mb-6" style="display: none;">
            <!-- SYMPTOM ASSESSMENT -->
            <div id="symptomAssessment" class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Symptom Assessment</h3>

                <div class="space-y-4" id="symptomContainer">
                    <!-- Default symptoms -->
                    <div class="symptom-field border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-ayur-brown-700">Joint Pain</label>
                            <span class="text-xs text-ayur-green-600" id="jointPainStatus">Significant improvement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-ayur-brown-600 w-12">Before</span>
                            <div class="w-full bg-ayur-yellow-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-yellow-500 h-2 rounded-full" id="jointPainBeforeBar"
                                    style="width: 85%"></div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="jointPainBeforeValue">8.5</span>
                        </div>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-ayur-brown-600 w-12">After</span>
                            <div class="w-full bg-ayur-green-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-green-500 h-2 rounded-full" id="jointPainAfterBar"
                                    style="width: 30%"></div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="jointPainAfterValue">3.0</span>
                        </div>
                        <input type="hidden" name="symptom_assessments[joint_pain][name]" value="Joint Pain">
                        <input type="hidden" name="symptom_assessments[joint_pain][before]" value="8.5">
                        <input type="hidden" name="symptom_assessments[joint_pain][after]" value="3.0">
                    </div>

                    <div class="symptom-field border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-ayur-brown-700">Digestive Issues</label>
                            <span class="text-xs text-ayur-green-600" id="digestiveStatus">Moderate improvement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-ayur-brown-600 w-12">Before</span>
                            <div class="w-full bg-ayur-yellow-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-yellow-500 h-2 rounded-full" id="digestiveBeforeBar"
                                    style="width: 70%"></div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="digestiveBeforeValue">7.0</span>
                        </div>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-ayur-brown-600 w-12">After</span>
                            <div class="w-full bg-ayur-green-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-green-500 h-2 rounded-full" id="digestiveAfterBar"
                                    style="width: 40%"></div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="digestiveAfterValue">4.0</span>
                        </div>
                        <input type="hidden" name="symptom_assessments[digestive][name]" value="Digestive Issues">
                        <input type="hidden" name="symptom_assessments[digestive][before]" value="7.0">
                        <input type="hidden" name="symptom_assessments[digestive][after]" value="4.0">
                    </div>

                    <div class="symptom-field border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-ayur-brown-700">Sleep Quality</label>
                            <span class="text-xs text-ayur-green-600" id="sleepStatus">Major improvement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-ayur-brown-600 w-12">Before</span>
                            <div class="w-full bg-ayur-yellow-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-yellow-500 h-2 rounded-full" id="sleepBeforeBar" style="width: 60%">
                                </div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="sleepBeforeValue">6.0</span>
                        </div>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-ayur-brown-600 w-12">After</span>
                            <div class="w-full bg-ayur-green-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-green-500 h-2 rounded-full" id="sleepAfterBar" style="width: 15%">
                                </div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="sleepAfterValue">1.5</span>
                        </div>
                        <input type="hidden" name="symptom_assessments[sleep][name]" value="Sleep Quality">
                        <input type="hidden" name="symptom_assessments[sleep][before]" value="6.0">
                        <input type="hidden" name="symptom_assessments[sleep][after]" value="1.5">
                    </div>

                    <div class="symptom-field border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-ayur-brown-700">Stress Levels</label>
                            <span class="text-xs text-ayur-green-600" id="stressStatus">Moderate improvement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-ayur-brown-600 w-12">Before</span>
                            <div class="w-full bg-ayur-yellow-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-yellow-500 h-2 rounded-full" id="stressBeforeBar" style="width: 75%">
                                </div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="stressBeforeValue">7.5</span>
                        </div>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-ayur-brown-600 w-12">After</span>
                            <div class="w-full bg-ayur-green-200 rounded-full h-2 mx-2">
                                <div class="bg-ayur-green-500 h-2 rounded-full" id="stressAfterBar" style="width: 35%">
                                </div>
                            </div>
                            <span class="text-xs text-ayur-brown-600 w-6" id="stressAfterValue">3.5</span>
                        </div>
                        <input type="hidden" name="symptom_assessments[stress][name]" value="Stress Levels">
                        <input type="hidden" name="symptom_assessments[stress][before]" value="7.5">
                        <input type="hidden" name="symptom_assessments[stress][after]" value="3.5">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" id="addSymptomBtn"
                        class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium flex items-center">
                        <i class="fa-solid fa-plus mr-1"></i> Add symptom
                    </button>
                </div>
            </div>

            <!-- DOSHA BALANCE CHART -->
            <div id="doshaBalanceChart" class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Dosha Balance Evaluation</h3>

                <div id="doshaChart" class="h-[250px] mb-4">
                    <!-- Chart will be rendered here -->
                    <div class="flex items-center justify-center h-full bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-sm text-gray-500 mb-2">Dosha Balance Visualization</div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold">V</span>
                                    </div>
                                    <div class="text-xs text-gray-600">Vata</div>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 mx-auto mb-2 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-red-600 font-bold">P</span>
                                    </div>
                                    <div class="text-xs text-gray-600">Pitta</div>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600 font-bold">K</span>
                                    </div>
                                    <div class="text-xs text-gray-600">Kapha</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-ayur-offwhite rounded-lg p-3 text-center">
                        <div class="text-sm font-medium text-ayur-brown-700">Vata</div>
                        <div class="flex items-center justify-center mt-1">
                            <i class="fa-solid fa-arrow-down text-ayur-green-600 mr-1"></i>
                            <span class="text-ayur-green-600 font-semibold" id="vataChange">-25%</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Before: <span id="vataBefore">7.0</span></span>
                                <span>After: <span id="vataAfter">5.0</span></span>
                            </div>
                        </div>
                        <input type="hidden" name="dosha_balance[vata][before]" value="7.0">
                        <input type="hidden" name="dosha_balance[vata][after]" value="5.0">
                    </div>
                    <div class="bg-ayur-offwhite rounded-lg p-3 text-center">
                        <div class="text-sm font-medium text-ayur-brown-700">Pitta</div>
                        <div class="flex items-center justify-center mt-1">
                            <i class="fa-solid fa-arrow-down text-ayur-green-600 mr-1"></i>
                            <span class="text-ayur-green-600 font-semibold" id="pittaChange">-18%</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Before: <span id="pittaBefore">8.0</span></span>
                                <span>After: <span id="pittaAfter">6.0</span></span>
                            </div>
                        </div>
                        <input type="hidden" name="dosha_balance[pitta][before]" value="8.0">
                        <input type="hidden" name="dosha_balance[pitta][after]" value="6.0">
                    </div>
                    <div class="bg-ayur-offwhite rounded-lg p-3 text-center">
                        <div class="text-sm font-medium text-ayur-brown-700">Kapha</div>
                        <div class="flex items-center justify-center mt-1">
                            <i class="fa-solid fa-arrow-up text-ayur-yellow-600 mr-1"></i>
                            <span class="text-ayur-yellow-600 font-semibold" id="kaphaChange">+5%</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Before: <span id="kaphaBefore">6.0</span></span>
                                <span>After: <span id="kaphaAfter">7.0</span></span>
                            </div>
                        </div>
                        <input type="hidden" name="dosha_balance[kapha][before]" value="6.0">
                        <input type="hidden" name="dosha_balance[kapha][after]" value="7.0">
                    </div>
                </div>
            </div>
        </div>

        <!-- PATIENT FEEDBACK SECTION -->
        <div id="patientFeedbackSection" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
            <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Patient Feedback</h3>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- SATISFACTION RATINGS -->
                <div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Overall Treatment
                                Satisfaction</label>
                            <div class="flex items-center">
                                <div class="flex" id="overallRating">
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="1"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="2"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="3"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="4"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="5"></i>
                                </div>
                                <input type="hidden" name="patient_satisfaction[overall]" value="0">
                                <span class="ml-2 text-sm font-medium text-ayur-brown-700"
                                    id="overallRatingText">0/5</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Staff Behavior &
                                Support</label>
                            <div class="flex items-center">
                                <div class="flex" id="staffRating">
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="1"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="2"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="3"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="4"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="5"></i>
                                </div>
                                <input type="hidden" name="staff_behavior_rating" value="0">
                                <span class="ml-2 text-sm font-medium text-ayur-brown-700" id="staffRatingText">0/5</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Facility Cleanliness &
                                Comfort</label>
                            <div class="flex items-center">
                                <div class="flex" id="facilityRating">
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="1"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="2"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="3"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="4"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="5"></i>
                                </div>
                                <input type="hidden" name="facility_cleanliness_rating" value="0">
                                <span class="ml-2 text-sm font-medium text-ayur-brown-700"
                                    id="facilityRatingText">0/5</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Would Recommend to
                                Others</label>
                            <div class="flex items-center">
                                <div class="flex" id="recommendationRating">
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="1"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="2"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="3"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="4"></i>
                                    <i class="fa-solid fa-star text-gray-300 cursor-pointer rating-star"
                                        data-rating="5"></i>
                                </div>
                                <input type="hidden" name="recommendation_rating" value="0">
                                <span class="ml-2 text-sm font-medium text-ayur-brown-700"
                                    id="recommendationRatingText">0/5</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COMMENTS SECTION -->
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Patient Comments</label>
                    <div class="border border-gray-300 rounded-lg p-4 bg-ayur-offwhite h-[180px] overflow-y-auto">
                        <textarea name="patient_comments" rows="6"
                            class="w-full border-none bg-transparent resize-none focus:outline-none text-sm text-ayur-brown-700"
                            placeholder="Enter patient feedback comments..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOCTOR'S EVALUATION & FOLLOW-UP -->
        <div id="doctorsEvaluationSection" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
            <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Doctor's Evaluation & Follow-up</h3>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- DOCTOR'S NOTES -->
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Treatment Outcome Assessment</label>
                    <textarea name="doctor_assessment" rows="5"
                        class="w-full border border-gray-300 rounded-md shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-3"
                        placeholder="Enter your assessment of the treatment outcome..."></textarea>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Overall Outcome</label>
                        <select name="overall_outcome"
                            class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-ayur-green-500 focus:border-ayur-green-500 py-2 px-3">
                            <option value="">Select Outcome</option>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="moderate">Moderate</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>
                </div>

                <!-- FOLLOW-UP RECOMMENDATIONS -->
                <div>
                    <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Follow-up Recommendations</label>
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="follow_up_recommendations[]" type="checkbox" value="maintenance_herbs"
                                    class="focus:ring-ayur-green-500 h-4 w-4 text-ayur-green-600 border-gray-300 rounded">
                            </div>
                            <label class="ml-3 text-sm text-ayur-brown-700">
                                Maintenance herbs for 4 weeks
                            </label>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="follow_up_recommendations[]" type="checkbox" value="diet_modifications"
                                    class="focus:ring-ayur-green-500 h-4 w-4 text-ayur-green-600 border-gray-300 rounded">
                            </div>
                            <label class="ml-3 text-sm text-ayur-brown-700">
                                Diet modifications (Pitta-pacifying diet)
                            </label>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="follow_up_recommendations[]" type="checkbox" value="yoga_routine"
                                    class="focus:ring-ayur-green-500 h-4 w-4 text-ayur-green-600 border-gray-300 rounded">
                            </div>
                            <label class="ml-3 text-sm text-ayur-brown-700">
                                Yoga routine (specifically for joints)
                            </label>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="follow_up_recommendations[]" type="checkbox" value="followup_consultation"
                                    class="focus:ring-ayur-green-500 h-4 w-4 text-ayur-green-600 border-gray-300 rounded">
                            </div>
                            <label class="ml-3 text-sm text-ayur-brown-700">
                                Follow-up consultation in 2 weeks
                            </label>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="follow_up_recommendations[]" type="checkbox" value="self_massage_training"
                                    class="focus:ring-ayur-green-500 h-4 w-4 text-ayur-green-600 border-gray-300 rounded">
                            </div>
                            <label class="ml-3 text-sm text-ayur-brown-700">
                                Self-massage technique training
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-ayur-brown-700 mb-1">Next Follow-up Date</label>
                        <input type="date" name="next_followup_date"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:border-ayur-green-500 focus:ring focus:ring-ayur-green-200 focus:ring-opacity-50 p-2">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="printReportBtn"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                    <i class="fa-solid fa-print mr-2"></i> Print Report
                </button>
                <button type="submit" id="saveEvaluationBtn"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Evaluation
                </button>
            </div>
        </div>

        <!-- PREVIOUS EVALUATIONS -->
        <div id="previousEvaluationsSection" class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-ayur-brown-800">Previous Evaluations</h3>
                <button class="text-ayur-green-600 hover:text-ayur-green-700 text-sm font-medium">
                    View All <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-ayur-offwhite">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Treatment</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Outcome</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Patient Rating</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-ayur-brown-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">05 Mar 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Abhyanga &amp;
                                Swedana</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dr. Meera Joshi</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-green-100 text-ayur-green-800">Excellent</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                <div class="flex">
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-regular fa-star text-ayur-yellow-500"></i>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-print"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">12 Jan 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Nasya</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">Dr. Anand Vaidya
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-ayur-yellow-100 text-ayur-yellow-800">Good</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                <div class="flex">
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star text-ayur-yellow-500"></i>
                                    <i class="fa-solid fa-star-half-alt text-ayur-yellow-500"></i>
                                    <i class="fa-regular fa-star text-ayur-yellow-500"></i>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3"><i
                                        class="fa-solid fa-eye"></i></button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3"><i
                                        class="fa-solid fa-print"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let symptomCount = 0;
            let currentPatient = null;
            let currentTreatmentPlan = null;

            // Patient search functionality
            let searchTimeout;
            document.getElementById('patientSearchInput').addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    hidePatientSearchResults();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchPatients(query);
                }, 300);
            });

            // Clear patient selection
            document.getElementById('clearPatientSelection').addEventListener('click', function() {
                clearPatientSelection();
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#patientSearchInput') && !e.target.closest(
                        '#patientSearchResults')) {
                    hidePatientSearchResults();
                }
            });

            // Treatment plan selection change (removed - not in design)

            // Dosha slider updates
            document.querySelectorAll('.dosha-slider').forEach(slider => {
                slider.addEventListener('input', function() {
                    const dosha = this.dataset.dosha;
                    const type = this.dataset.type;
                    const value = this.value;

                    document.querySelector(
                            `[data-dosha="${dosha}"][data-type="${type}"].dosha-value`)
                        .textContent = value;
                });
            });

            // Star rating functionality
            document.querySelectorAll('.rating-star').forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    const container = this.closest('.flex');
                    const stars = container.querySelectorAll('.rating-star');
                    const hiddenInput = container.nextElementSibling;
                    const textSpan = hiddenInput.nextElementSibling;

                    // Update stars
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.className =
                                'fa-solid fa-star text-ayur-yellow-500 cursor-pointer rating-star';
                        } else {
                            s.className =
                                'fa-solid fa-star text-gray-300 cursor-pointer rating-star';
                        }
                    });

                    // Update hidden input and text
                    hiddenInput.value = rating;
                    textSpan.textContent = rating + '/5';
                });
            });

            // Add symptom functionality
            document.getElementById('addSymptomBtn').addEventListener('click', function() {
                addSymptomField();
            });

            // Initialize symptom sliders
            initializeSymptomSliders();

            // Test button to show sections
            document.getElementById('testShowSections').addEventListener('click', function() {
                showSections();
                console.log('Sections should now be visible');
            });

            // Form submission
            document.getElementById('saveEvaluationBtn').addEventListener('click', function(e) {
                e.preventDefault();
                submitFeedbackForm();
            });

            // Print report
            document.getElementById('printReportBtn').addEventListener('click', function() {
                window.print();
            });

            function searchPatients(query) {
                fetch(`{{ route('treatment-plan.feedback.search-patient') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            displayPatientSearchResults(data);
                        } else {
                            hidePatientSearchResults();
                        }
                    })
                    .catch(error => {
                        console.error('Error searching patients:', error);
                        hidePatientSearchResults();
                    });
            }

            function displayPatientSearchResults(patients) {
                const resultsContainer = document.getElementById('patientSearchResults');
                const resultsDiv = resultsContainer.querySelector('.max-h-60');

                resultsDiv.innerHTML = patients.map(patient => `
                    <div class="patient-result p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                         data-patient-id="${patient.id}" data-patient='${JSON.stringify(patient)}'>
                        <div class="flex items-center">
                            <img class="h-8 w-8 rounded-full object-cover mr-3" 
                                 src="${patient.photo_path || 'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg'}" 
                                 alt="Patient photo">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-ayur-brown-800">${patient.full_name}</div>
                                <div class="text-xs text-ayur-brown-600">UHID: ${patient.uhid} | ${patient.age}/${patient.gender} | ${patient.mobile}</div>
                                <div class="text-xs text-ayur-brown-500">Prakriti: ${patient.prakriti}</div>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Add click event listeners to patient results
                resultsDiv.querySelectorAll('.patient-result').forEach(result => {
                    result.addEventListener('click', function() {
                        const patientData = JSON.parse(this.dataset.patient);
                        selectPatient(patientData);
                    });
                });

                resultsContainer.classList.remove('hidden');
            }

            function hidePatientSearchResults() {
                document.getElementById('patientSearchResults').classList.add('hidden');
            }

            function selectPatient(patient) {
                currentPatient = patient;

                // Update hidden input
                document.getElementById('selectedPatientId').value = patient.id;

                // Update selected patient display
                document.getElementById('selectedPatientName').textContent = patient.full_name;
                document.getElementById('selectedPatientDetails').textContent =
                    `UHID: ${patient.uhid} | ${patient.age}/${patient.gender}`;
                document.getElementById('selectedPatientPhoto').src = patient.photo_path ||
                    'https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg';

                // Show selected patient display and hide search results
                document.getElementById('selectedPatientDisplay').classList.remove('hidden');
                hidePatientSearchResults();

                // Clear search input
                document.getElementById('patientSearchInput').value = '';

                // Update patient card immediately
                updatePatientCard(patient);

                // Show all sections immediately
                showSections();

                // Load previous evaluations
                loadPreviousEvaluations(patient.id);
            }

            function clearPatientSelection() {
                currentPatient = null;
                document.getElementById('selectedPatientId').value = '';
                document.getElementById('selectedPatientDisplay').classList.add('hidden');
                document.getElementById('patientSearchInput').value = '';
                hideSections();
            }

            function loadPatientDetails(patientId) {
                fetch(`{{ route('treatment-plan.feedback.patient-details') }}?patient_id=${patientId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentPatient = data.patient;
                            updatePatientCard(data.patient);
                            showSections();
                            loadPreviousEvaluations(patientId);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading patient details:', error);
                    });
            }

            function loadPreviousEvaluations(patientId) {
                fetch(`{{ route('treatment-plan.feedback.previous-evaluations') }}?patient_id=${patientId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updatePreviousEvaluationsTable(data.evaluations);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading previous evaluations:', error);
                    });
            }

            function updatePreviousEvaluationsTable(evaluations) {
                const tbody = document.querySelector('#previousEvaluationsSection tbody');
                if (!tbody) return;

                if (evaluations.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No previous evaluations found for this patient.
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = evaluations.map(evaluation => {
                    const date = new Date(evaluation.created_at).toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const overallRating = evaluation.patient_satisfaction?.overall || 0;
                    const stars = generateStarRating(overallRating);

                    const outcomeClass = getOutcomeClass(evaluation.overall_outcome);
                    const outcomeText = evaluation.overall_outcome ? evaluation.overall_outcome.charAt(0)
                        .toUpperCase() + evaluation.overall_outcome.slice(1) : 'Not rated';

                    return `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">${date}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                ${evaluation.treatmentPlan?.procedure_name || 'N/A'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                ${evaluation.therapist?.full_name || 'N/A'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${outcomeClass}">
                                    ${outcomeText}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-ayur-brown-700">
                                <div class="flex">
                                    ${stars}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-ayur-green-600 hover:text-ayur-green-900 mr-3" onclick="viewEvaluation(${evaluation.id})">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="text-ayur-brown-600 hover:text-ayur-brown-900 mr-3" onclick="printEvaluation(${evaluation.id})">
                                    <i class="fa-solid fa-print"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function generateStarRating(rating) {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) {
                        stars += '<i class="fa-solid fa-star text-ayur-yellow-500"></i>';
                    } else if (i - 0.5 <= rating) {
                        stars += '<i class="fa-solid fa-star-half-alt text-ayur-yellow-500"></i>';
                    } else {
                        stars += '<i class="fa-regular fa-star text-ayur-yellow-500"></i>';
                    }
                }
                return stars;
            }

            function getOutcomeClass(outcome) {
                switch (outcome) {
                    case 'excellent':
                        return 'bg-ayur-green-100 text-ayur-green-800';
                    case 'good':
                        return 'bg-ayur-blue-100 text-ayur-blue-800';
                    case 'moderate':
                        return 'bg-ayur-yellow-100 text-ayur-yellow-800';
                    case 'poor':
                        return 'bg-red-100 text-red-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }

            function viewEvaluation(evaluationId) {
                // TODO: Implement view evaluation modal
                console.log('View evaluation:', evaluationId);
            }

            function printEvaluation(evaluationId) {
                // TODO: Implement print evaluation
                console.log('Print evaluation:', evaluationId);
            }

            // Removed loadTreatmentPlanDetails function as treatment plan selection is not in design

            function updatePatientCard(patient) {
                document.getElementById('patientName').textContent = patient.first_name + ' ' + patient.last_name;
                document.getElementById('patientUHID').textContent = 'UHID: ' + patient.uhid;
                document.getElementById('patientAgeGender').textContent = patient.age + '/' + patient.gender;
                document.getElementById('patientPrakriti').textContent = patient.prakriti || 'Not specified';

                // Set default treatment information since we're not using dropdowns
                document.getElementById('treatmentType').textContent = 'Panchakarma - Virechana';
                document.getElementById('attendingTherapist').textContent = 'Dr. Anand Vaidya';

                // Set default progress information
                const today = new Date();
                const startDate = new Date(today.getTime() - (18 * 24 * 60 * 60 * 1000)); // 18 days ago
                const endDate = new Date(today.getTime() + (3 * 24 * 60 * 60 * 1000)); // 3 days from now

                document.getElementById('treatmentDuration').textContent = '21 Days (12 Jul - 02 Aug 2025)';
                document.getElementById('startDate').textContent = 'Started: 12 Jul 2025';
                document.getElementById('endDate').textContent = 'Ends: 02 Aug 2025';
                document.getElementById('progressText').textContent = '18 of 21 days completed (85%)';
                document.getElementById('progressBar').style.width = '85%';
            }

            function addSymptomField() {
                symptomCount++;
                const container = document.getElementById('symptomContainer');
                const symptomDiv = document.createElement('div');
                symptomDiv.className = 'symptom-field border border-gray-200 rounded-lg p-4';
                symptomDiv.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <input type="text" name="symptom_assessments[${symptomCount}][name]" 
                            placeholder="Symptom name" 
                            class="flex-1 border border-gray-300 rounded-md px-3 py-1 text-sm">
                        <button type="button" class="remove-symptom text-red-600 hover:text-red-800 ml-2">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div class="flex justify-between mb-1">
                        <label class="block text-sm font-medium text-ayur-brown-700">Custom Symptom</label>
                        <span class="text-xs text-ayur-green-600" id="customSymptomStatus${symptomCount}">Moderate improvement</span>
                    </div>
                    <div class="flex items-center mb-1">
                        <span class="text-xs text-ayur-brown-600 w-12">Before</span>
                        <div class="w-full bg-ayur-yellow-200 rounded-full h-2 mx-2">
                            <div class="bg-ayur-yellow-500 h-2 rounded-full" id="customSymptomBeforeBar${symptomCount}" style="width: 50%"></div>
                        </div>
                        <span class="text-xs text-ayur-brown-600 w-6" id="customSymptomBeforeValue${symptomCount}">5.0</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-ayur-brown-600 w-12">After</span>
                        <div class="w-full bg-ayur-green-200 rounded-full h-2 mx-2">
                            <div class="bg-ayur-green-500 h-2 rounded-full" id="customSymptomAfterBar${symptomCount}" style="width: 30%"></div>
                        </div>
                        <span class="text-xs text-ayur-brown-600 w-6" id="customSymptomAfterValue${symptomCount}">3.0</span>
                    </div>
                    <input type="hidden" name="symptom_assessments[${symptomCount}][before]" value="5.0">
                    <input type="hidden" name="symptom_assessments[${symptomCount}][after]" value="3.0">
                `;

                container.appendChild(symptomDiv);

                // Add remove functionality
                symptomDiv.querySelector('.remove-symptom').addEventListener('click', function() {
                    symptomDiv.remove();
                });
            }

            // Initialize default symptom sliders
            function initializeSymptomSliders() {
                const symptoms = ['jointPain', 'digestive', 'sleep', 'stress'];

                symptoms.forEach(symptom => {
                    const beforeSlider = document.getElementById(symptom + 'BeforeSlider');
                    const afterSlider = document.getElementById(symptom + 'AfterSlider');

                    if (beforeSlider) {
                        beforeSlider.addEventListener('input', function() {
                            updateSymptomDisplay(symptom, 'before', this.value);
                        });
                    }

                    if (afterSlider) {
                        afterSlider.addEventListener('input', function() {
                            updateSymptomDisplay(symptom, 'after', this.value);
                        });
                    }
                });
            }

            function updateSymptomDisplay(symptom, type, value) {
                const valueSpan = document.getElementById(symptom + type.charAt(0).toUpperCase() + type.slice(1) +
                    'Value');
                const progressBar = document.getElementById(symptom + type.charAt(0).toUpperCase() + type.slice(1) +
                    'Bar');
                const hiddenInput = document.querySelector(
                    `input[name="symptom_assessments[${symptom}][${type}]"]`);

                if (valueSpan) valueSpan.textContent = value;
                if (progressBar) progressBar.style.width = (value * 10) + '%';
                if (hiddenInput) hiddenInput.value = value;

                // Update improvement status
                const beforeValue = parseFloat(document.getElementById(symptom + 'BeforeValue').textContent);
                const afterValue = parseFloat(document.getElementById(symptom + 'AfterValue').textContent);
                const improvement = beforeValue - afterValue;

                const statusSpan = document.getElementById(symptom + 'Status');
                if (statusSpan) {
                    if (improvement >= 3) {
                        statusSpan.textContent = 'Major improvement';
                        statusSpan.className = 'text-xs text-ayur-green-600';
                    } else if (improvement >= 1.5) {
                        statusSpan.textContent = 'Significant improvement';
                        statusSpan.className = 'text-xs text-ayur-green-600';
                    } else if (improvement >= 0.5) {
                        statusSpan.textContent = 'Moderate improvement';
                        statusSpan.className = 'text-xs text-ayur-green-600';
                    } else if (improvement > 0) {
                        statusSpan.textContent = 'Slight improvement';
                        statusSpan.className = 'text-xs text-ayur-yellow-600';
                    } else {
                        statusSpan.textContent = 'No improvement';
                        statusSpan.className = 'text-xs text-ayur-red-600';
                    }
                }
            }

            function showSections() {
                console.log('showSections() called');

                const patientCard = document.getElementById('patientCard');
                const treatmentOutcomeSection = document.getElementById('treatmentOutcomeSection');
                const patientFeedbackSection = document.getElementById('patientFeedbackSection');
                const doctorsEvaluationSection = document.getElementById('doctorsEvaluationSection');

                console.log('Elements found:', {
                    patientCard: !!patientCard,
                    treatmentOutcomeSection: !!treatmentOutcomeSection,
                    patientFeedbackSection: !!patientFeedbackSection,
                    doctorsEvaluationSection: !!doctorsEvaluationSection
                });

                if (patientCard) {
                    patientCard.style.display = 'block';
                    console.log('Patient card shown');
                }
                if (treatmentOutcomeSection) {
                    treatmentOutcomeSection.style.display = 'grid';
                    console.log('Treatment outcome section shown');
                }
                if (patientFeedbackSection) {
                    patientFeedbackSection.style.display = 'block';
                    console.log('Patient feedback section shown');
                }
                if (doctorsEvaluationSection) {
                    doctorsEvaluationSection.style.display = 'block';
                    console.log('Doctor evaluation section shown');
                }
            }

            function hideSections() {
                document.getElementById('patientCard').style.display = 'none';
                document.getElementById('treatmentOutcomeSection').style.display = 'none';
                document.getElementById('patientFeedbackSection').style.display = 'none';
                document.getElementById('doctorsEvaluationSection').style.display = 'none';
            }

            function submitFeedbackForm() {
                const form = document.getElementById('feedbackForm');
                const formData = new FormData(form);

                // Add all form data
                const allFormData = new FormData();

                // Basic form fields
                allFormData.append('patient_id', formData.get('patient_id'));
                allFormData.append('doctor_id', ''); // Will be set from current user or patient's assigned doctor
                allFormData.append('treatment_plan_id', ''); // Optional field

                // Collect symptom assessments
                const symptoms = {};

                // Collect default symptoms
                const defaultSymptoms = ['joint_pain', 'digestive', 'sleep', 'stress'];
                defaultSymptoms.forEach(symptom => {
                    const beforeValue = document.getElementById(symptom + 'BeforeValue');
                    const afterValue = document.getElementById(symptom + 'AfterValue');
                    if (beforeValue && afterValue) {
                        symptoms[symptom] = {
                            name: document.querySelector(
                                `input[name="symptom_assessments[${symptom}][name]"]`).value,
                            before: parseFloat(beforeValue.textContent),
                            after: parseFloat(afterValue.textContent)
                        };
                    }
                });

                // Collect custom symptoms
                document.querySelectorAll('.symptom-field').forEach((field, index) => {
                    const nameInput = field.querySelector('input[type="text"]');
                    if (nameInput && nameInput.value) {
                        const beforeValue = field.querySelector(`[id$="BeforeValue"]`);
                        const afterValue = field.querySelector(`[id$="AfterValue"]`);
                        if (beforeValue && afterValue) {
                            symptoms[`custom_${index}`] = {
                                name: nameInput.value,
                                before: parseFloat(beforeValue.textContent),
                                after: parseFloat(afterValue.textContent)
                            };
                        }
                    }
                });

                allFormData.append('symptom_assessments', JSON.stringify(symptoms));

                // Collect dosha balance
                const doshaBalance = {};
                const doshas = ['vata', 'pitta', 'kapha'];

                doshas.forEach(dosha => {
                    const beforeValue = document.getElementById(dosha + 'Before');
                    const afterValue = document.getElementById(dosha + 'After');
                    if (beforeValue && afterValue) {
                        doshaBalance[dosha] = {
                            before: parseFloat(beforeValue.textContent),
                            after: parseFloat(afterValue.textContent)
                        };
                    }
                });

                allFormData.append('dosha_balance', JSON.stringify(doshaBalance));

                // Collect patient satisfaction
                const patientSatisfaction = {};
                document.querySelectorAll('input[name^="patient_satisfaction"]').forEach(input => {
                    const key = input.name.match(/\[(.*?)\]/)[1];
                    patientSatisfaction[key] = parseFloat(input.value);
                });
                allFormData.append('patient_satisfaction', JSON.stringify(patientSatisfaction));

                // Other fields
                allFormData.append('staff_behavior_rating', formData.get('staff_behavior_rating') || 0);
                allFormData.append('facility_cleanliness_rating', formData.get('facility_cleanliness_rating') || 0);
                allFormData.append('recommendation_rating', formData.get('recommendation_rating') || 0);
                allFormData.append('patient_comments', formData.get('patient_comments') || '');
                allFormData.append('doctor_assessment', formData.get('doctor_assessment') || '');
                allFormData.append('overall_outcome', formData.get('overall_outcome') || '');
                allFormData.append('next_followup_date', formData.get('next_followup_date') || '');

                // Collect follow-up recommendations
                const followUpRecommendations = [];
                document.querySelectorAll('input[name="follow_up_recommendations[]"]:checked').forEach(checkbox => {
                    followUpRecommendations.push(checkbox.value);
                });
                allFormData.append('follow_up_recommendations', JSON.stringify(followUpRecommendations));

                // Submit form
                fetch(form.action, {
                        method: 'POST',
                        body: allFormData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                form.reset();
                                hideSections();
                                document.getElementById('symptomContainer').innerHTML = '';
                                symptomCount = 0;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while saving the feedback.',
                            confirmButtonColor: '#ef4444'
                        });
                    });
            }

            function formatDate(date) {
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }
        });
    </script>
@endsection
