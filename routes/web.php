<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Backend\PatientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\DoctorController;
use App\Http\Controllers\Backend\PrescriptionController;
use App\Http\Controllers\Backend\AppointmentController;
use App\Http\Controllers\Backend\CalendarApiController;
use App\Http\Controllers\Backend\AccountingController;
use App\Http\Controllers\Backend\PharmacyController;
use App\Http\Controllers\Backend\TreatmentPlanController;
use App\Http\Controllers\Backend\TherapistAssignmentController;
use App\Http\Controllers\Backend\TherapistScheduleController;
use App\Http\Controllers\Backend\TreatmentRoomController;
use App\Http\Controllers\Backend\TreatmentTrackerController;
use App\Http\Controllers\Backend\TreatmentFeedbackController;
use App\Http\Controllers\Backend\BillController;


Route::get('/', function () {
    return view('backend.auth.login');
})->name('login');
// Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Handle login submit
Route::post('/', [LoginController::class, 'login']);

Route::match(['GET', 'POST'], '/logout', [LoginController::class, 'logout'])->name('logout');
// Forgot & Reset Password
Route::GET('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::POST('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::GET('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::POST('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Auth::routes();
Route::group(['middleware' =>  ['auth'], 'prefix' => 'admin'], function () {

    // User module
    Route::GET('/user', [UserController::class, 'index'])->name('user');
    Route::GET('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::GET('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::PATCH('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

    // User profile
    // Route::get('/edit-profile', [UserProfileController::class, 'editProfile'])->name('edit.profile');
    // Route::get('/edit-password', [UserProfileController::class, 'editpassword'])->name('edit.password');
    // Route::get('/edit-password-admin/{id}', [UserProfileController::class, 'editpasswordadmin'])->name('edit.password.admin');
    // Route::PATCH('/save-password/{id}', [UserProfileController::class, 'saveChangePassword'])->name('saveChangePassword');
    // Route::PATCH('/save-password-admin/{id}', [UserProfileController::class, 'saveChangePasswordAdmin'])->name('saveChangePasswordAdmin');

    // permission
    Route::GET('/permission', [PermissionController::class, 'index'])->name('permission');
    Route::GET('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');
    Route::GET('/permission/edit/{id}', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::PATCH('/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/permission/delete/{id}', [PermissionController::class, 'destroy'])->name('permission.delete');

    // Role
    Route::GET('/role', [RoleController::class, 'index'])->name('role');
    Route::GET('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
    Route::GET('/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::PATCH('/role/update/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete');

    // Patient Management
    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.delete');

    // Prescription Routes
    Route::get('/patients/{patient}/prescriptions/create', [PrescriptionController::class, 'create'])->name('patients.prescriptions.create');
    Route::post('/patients/{patient}/prescriptions', [PrescriptionController::class, 'store'])->name('patients.prescriptions.store');

    // doctor routes
    Route::get('/doctor', [DoctorController::class, 'index'])->name('doctor');
    Route::get('/doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
    Route::post('/doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

    // Appointment Routes
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment');
    Route::get('/appointment/calendar', [AppointmentController::class, 'calendar'])->name('appointment.calendar');
    Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/search-patient', [AppointmentController::class, 'searchPatient'])->name('appointment.search-patient');
    Route::get('/appointment/doctors-by-department', [AppointmentController::class, 'getDoctorsByDepartment'])->name('appointment.doctors-by-department');
    Route::get('/appointment/doctor-fees', [AppointmentController::class, 'getDoctorFees'])->name('appointment.doctor-fees');
    Route::get('/appointment/doctor-time-slots', [AppointmentController::class, 'getDoctorTimeSlots'])->name('appointment.doctor-time-slots');
    Route::get('/appointment/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointment/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointment.update-status');

    // Calendar API Routes
    Route::get('/calendar/data', [CalendarApiController::class, 'getCalendarData'])->name('calendar.data');
    Route::get('/calendar/time-slots', [CalendarApiController::class, 'getAvailableTimeSlots'])->name('calendar.time-slots');
    Route::get('/calendar/stats', [CalendarApiController::class, 'getCalendarStats'])->name('calendar.stats');
    Route::get('/calendar/appointments', [CalendarApiController::class, 'getAppointments'])->name('calendar.appointments');
    Route::get('/calendar/doctor-schedule', [CalendarApiController::class, 'getDoctorSchedule'])->name('calendar.doctor-schedule');
    Route::get('/calendar/overlaps', [CalendarApiController::class, 'checkOverlaps'])->name('calendar.overlaps');

    // Accounting Routes
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting');
    Route::post('/accounting', [AccountingController::class, 'store'])->name('accounting.store');
    Route::get('/accounting/{id}/edit', [AccountingController::class, 'edit'])->name('accounting.edit');
    Route::PATCH('/accounting/{id}', [AccountingController::class, 'update'])->name('accounting.update');
    Route::delete('/accounting/{id}', [AccountingController::class, 'destroy'])->name('accounting.delete');
    Route::get('/accounting/stats', [AccountingController::class, 'getStats'])->name('accounting.stats');

    // Pharmacy Routes
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy');
    Route::GET('/pharmacy/create', [PharmacyController::class, 'create'])->name('pharmacy.create');
    Route::post('/pharmacy/store', [PharmacyController::class, 'store'])->name('pharmacy.store');
    Route::GET('/pharmacy/dispense', [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
    Route::get('/pharmacy/restock', [PharmacyController::class, 'restock'])->name('pharmacy.restock');
    Route::post('/pharmacy/restock', [PharmacyController::class, 'storeRestock'])->name('pharmacy.restock.store');
    Route::get('/pharmacy/restocks', [PharmacyController::class, 'allRestocks'])->name('pharmacy.restocks');
    Route::get('/pharmacy/restock/{id}', [PharmacyController::class, 'viewRestock'])->name('pharmacy.restock.view');
    Route::patch('/pharmacy/restock/{id}/status', [PharmacyController::class, 'updateRestockStatus'])->name('pharmacy.restock.update-status');

    // Pharmacy API Routes (must come before parameterized routes)
    Route::get('/pharmacy/search-patient', [PharmacyController::class, 'searchPatient'])->name('pharmacy.search-patient');
    Route::get('/pharmacy/search-medicines', [PharmacyController::class, 'searchMedicines'])->name('pharmacy.search-medicines');
    Route::get('/pharmacy/get-prescriptions', [PharmacyController::class, 'getPatientPrescriptions'])->name('pharmacy.get-prescriptions');
    Route::get('/pharmacy/inventory', [PharmacyController::class, 'getMedicineInventory'])->name('pharmacy.inventory');
    Route::get('/pharmacy/available-medicines', [PharmacyController::class, 'getAvailableMedicines'])->name('pharmacy.available-medicines');
    Route::get('/pharmacy/medicine-details', [PharmacyController::class, 'getMedicineDetails'])->name('pharmacy.medicine-details');
    Route::post('/pharmacy/process-dispense', [PharmacyController::class, 'processDispense'])->name('pharmacy.process-dispense');
    Route::post('/pharmacy/store-dispense-form', [PharmacyController::class, 'storeDispenseForm'])->name('pharmacy.store-dispense-form');
    Route::get('/pharmacy/dispensations', [PharmacyController::class, 'allDispensations'])->name('pharmacy.dispensations');

    // Parameterized pharmacy routes (must come after API routes)
    Route::get('/pharmacy/{medicine}/edit', [PharmacyController::class, 'edit'])->name('pharmacy.edit');
    Route::PATCH('/pharmacy/{medicine}', [PharmacyController::class, 'update'])->name('pharmacy.update');
    Route::delete('/pharmacy/{medicine}', [PharmacyController::class, 'destroy'])->name('pharmacy.delete');
    Route::get('/pharmacy/{medicine}', [PharmacyController::class, 'show'])->name('pharmacy.show');

    // Treatment Plan Routes
    Route::get('/treatment-plan', [TreatmentPlanController::class, 'index'])->name('treatment-plan');
    Route::post('/treatment-plan', [TreatmentPlanController::class, 'store'])->name('treatment-plan.store');
    Route::get('/treatment-plan/search-patient', [TreatmentPlanController::class, 'searchPatient'])->name('treatment-plan.search-patient');
    Route::get('/treatment-plan/therapists-by-category', [TreatmentPlanController::class, 'getTherapistsByCategory'])->name('treatment-plan.therapists-by-category');

    // Parameterized treatment plan routes (must come after API routes)
    Route::get('/treatment-plan/{treatmentPlan}/edit', [TreatmentPlanController::class, 'edit'])->name('treatment-plan.edit');
    Route::patch('/treatment-plan/{treatmentPlan}', [TreatmentPlanController::class, 'update'])->name('treatment-plan.update');
    Route::delete('/treatment-plan/{treatmentPlan}', [TreatmentPlanController::class, 'destroy'])->name('treatment-plan.delete');
    Route::patch('/treatment-plan/{treatmentPlan}/status', [TreatmentPlanController::class, 'updateStatus'])->name('treatment-plan.update-status');

    // Treatment Tracker Routes
    Route::get('/treatment-plan/{treatmentPlan}/tracker', [TreatmentTrackerController::class, 'show'])->name('treatment-plan.tracker');
    Route::post('/treatment-tracker', [TreatmentTrackerController::class, 'store'])->name('treatment-tracker.store');
    Route::get('/treatment-tracker/{tracker}/details', [TreatmentTrackerController::class, 'getSessionDetails'])->name('treatment-tracker.details');
    Route::post('/treatment-tracker/{tracker}/cancel', [TreatmentTrackerController::class, 'cancel'])->name('treatment-tracker.cancel');
    Route::delete('/treatment-tracker/{tracker}/image', [TreatmentTrackerController::class, 'deleteImage'])->name('treatment-tracker.delete-image');

    // Treatment Feedback Routes
    Route::get('/treatment-feedback', [TreatmentFeedbackController::class, 'index'])->name('treatment-plan.feedback');
    Route::post('/treatment-feedback', [TreatmentFeedbackController::class, 'store'])->name('treatment-plan.feedback.store');
    Route::get('/treatment-feedback/search-patient', [TreatmentFeedbackController::class, 'searchPatient'])->name('treatment-plan.feedback.search-patient');
    Route::get('/treatment-feedback/patient-details', [TreatmentFeedbackController::class, 'getPatientDetails'])->name('treatment-plan.feedback.patient-details');
    Route::get('/treatment-feedback/treatment-plan-details', [TreatmentFeedbackController::class, 'getTreatmentPlanDetails'])->name('treatment-plan.feedback.treatment-plan-details');
    Route::get('/treatment-feedback/previous-evaluations', [TreatmentFeedbackController::class, 'getPreviousEvaluations'])->name('treatment-plan.feedback.previous-evaluations');

    // Therapist Assignment Routes
    Route::get('/therapist-assignment', [TherapistAssignmentController::class, 'index'])->name('doctor.therapist-assignment');
    Route::post('/therapist-assignment', [TherapistAssignmentController::class, 'store'])->name('therapist-assignment.store');
    Route::get('/therapist-assignment/pending-treatments', [TherapistAssignmentController::class, 'getPendingTreatments'])->name('therapist-assignment.pending-treatments');
    Route::get('/therapist-assignment/details', [TherapistAssignmentController::class, 'getAssignmentDetails'])->name('therapist-assignment.details');
    Route::get('/therapist-assignment/therapists-by-category', [TherapistAssignmentController::class, 'getTherapistsByTreatmentCategory'])->name('therapist-assignment.therapists-by-category');
    Route::patch('/therapist-assignment/{assignment}/status', [TherapistAssignmentController::class, 'updateStatus'])->name('therapist-assignment.update-status');

    // Therapist Schedule Routes
    Route::get('/therapist-schedule', [TherapistScheduleController::class, 'index'])->name('doctor.therapist-schedule');
    Route::get('/therapist-schedule/appointments', [TherapistScheduleController::class, 'getAppointments'])->name('therapist-schedule.appointments');
    Route::get('/therapist-schedule/therapist-status', [TherapistScheduleController::class, 'getTherapistStatus'])->name('therapist-schedule.therapist-status');
    Route::get('/therapist-schedule/schedule-data', [TherapistScheduleController::class, 'getScheduleData'])->name('therapist-schedule.schedule-data');
    Route::patch('/therapist-schedule/{assignment}/status', [TherapistScheduleController::class, 'updateStatus'])->name('therapist-schedule.update-status');

    // Treatment Room Routes
    Route::get('/treatment-room', [TreatmentRoomController::class, 'index'])->name('doctor.treatment-room');
    Route::get('/treatment-room/booking-form-data', [TreatmentRoomController::class, 'getBookingFormData'])->name('treatment-room.booking-form-data');
    Route::post('/treatment-room/booking', [TreatmentRoomController::class, 'storeBooking'])->name('treatment-room.store-booking');
    Route::get('/treatment-room/search-patients', [TreatmentRoomController::class, 'searchPatients'])->name('treatment-room.search-patients');
    Route::get('/treatment-room/{room}/details', [TreatmentRoomController::class, 'getRoomDetails'])->name('treatment-room.room-details');
    Route::get('/treatment-room/filter-options', [TreatmentRoomController::class, 'getFilterOptions'])->name('treatment-room.filter-options');
    Route::post('/treatment-room/filter', [TreatmentRoomController::class, 'filterRooms'])->name('treatment-room.filter');
    Route::patch('/treatment-room/{assignment}/status', [TreatmentRoomController::class, 'updateStatus'])->name('treatment-room.update-status');

    // Bill Routes
    Route::get('/bill', [BillController::class, 'index'])->name('bill');
    Route::get('/bill/create', [BillController::class, 'create'])->name('bill.create');
    Route::post('/bill', [BillController::class, 'store'])->name('bill.store');

    // Bill API Routes (must come before parameterized routes)
    Route::get('/bill/search-patient', [BillController::class, 'searchPatient'])->name('bill.search-patient');
    Route::get('/bill/patient-bills', [BillController::class, 'getPatientBills'])->name('bill.patient-bills');
    Route::get('/bill/appointment-data', [BillController::class, 'getAppointmentData'])->name('bill.appointment-data');
    Route::get('/bill/prescription-data', [BillController::class, 'getPrescriptionData'])->name('bill.prescription-data');
    Route::get('/bill/export', [BillController::class, 'export'])->name('bill.export');

    // Parameterized bill routes (must come after API routes)
    Route::get('/bill/{bill}', [BillController::class, 'show'])->name('bill.show');
    Route::get('/bill/{bill}/edit', [BillController::class, 'edit'])->name('bill.edit');
    Route::patch('/bill/{bill}', [BillController::class, 'update'])->name('bill.update');
    Route::delete('/bill/{bill}', [BillController::class, 'destroy'])->name('bill.delete');
    Route::post('/bill/{bill}/payment', [BillController::class, 'processPayment'])->name('bill.payment');
});
