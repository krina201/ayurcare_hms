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
    Route::get('/patients/{patient}/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('patients.prescriptions.edit');
    Route::put('/patients/{patient}/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('patients.prescriptions.update');

    Route::get('/doctor', [DoctorController::class, 'index'])->name('doctor');
    Route::get('/doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
    Route::post('/doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

    // Appointment Routes
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment');
    Route::get('/appointment/calendar', [AppointmentController::class, 'calendar'])->name('appointment.calendar');
    Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/search-patient', [AppointmentController::class, 'searchPatient'])->name('appointment.search-patient');
    Route::get('/appointment/test-search', [AppointmentController::class, 'testPatientSearch'])->name('appointment.test-search');
    Route::get('/appointment/doctor-fees', [AppointmentController::class, 'getDoctorFees'])->name('appointment.doctor-fees');
    Route::get('/appointment/doctor-time-slots', [AppointmentController::class, 'getDoctorTimeSlots'])->name('appointment.doctor-time-slots');
    Route::get('/appointment/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');

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
    Route::delete('/accounting/{id}', [AccountingController::class, 'destroy'])->name('accounting.destroy');
    Route::get('/accounting/stats', [AccountingController::class, 'getStats'])->name('accounting.stats');

    // Pharmacy Routes
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy');
    Route::GET('/pharmacy/create', [PharmacyController::class, 'create'])->name('pharmacy.create');
    Route::post('/pharmacy/store', [PharmacyController::class, 'store'])->name('pharmacy.store');
    Route::GET('/pharmacy/dispense', [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
    Route::get('/pharmacy/restock', [PharmacyController::class, 'restock'])->name('pharmacy.restock');

    // Pharmacy API Routes (must come before parameterized routes)
    Route::get('/pharmacy/search-patient', [PharmacyController::class, 'searchPatient'])->name('pharmacy.search-patient');
    Route::get('/pharmacy/search-medicines', [PharmacyController::class, 'searchMedicines'])->name('pharmacy.search-medicines');
    Route::get('/pharmacy/get-prescriptions', [PharmacyController::class, 'getPatientPrescriptions'])->name('pharmacy.get-prescriptions');
    Route::get('/pharmacy/inventory', [PharmacyController::class, 'getMedicineInventory'])->name('pharmacy.inventory');
    Route::get('/pharmacy/available-medicines', [PharmacyController::class, 'getAvailableMedicines'])->name('pharmacy.available-medicines');
    Route::post('/pharmacy/process-dispense', [PharmacyController::class, 'processDispense'])->name('pharmacy.process-dispense');
    Route::post('/pharmacy/store-dispense-form', [PharmacyController::class, 'storeDispenseForm'])->name('pharmacy.store-dispense-form');

    // Parameterized pharmacy routes (must come after API routes)
    Route::get('/pharmacy/{medicine}/edit', [PharmacyController::class, 'edit'])->name('pharmacy.edit');
    Route::put('/pharmacy/{medicine}', [PharmacyController::class, 'update'])->name('pharmacy.update');
    Route::delete('/pharmacy/{medicine}', [PharmacyController::class, 'destroy'])->name('pharmacy.delete');
    Route::get('/pharmacy/{medicine}', [PharmacyController::class, 'show'])->name('pharmacy.show');
});
