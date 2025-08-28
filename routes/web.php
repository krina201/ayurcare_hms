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


    Route::get('/doctor', [DoctorController::class, 'index'])->name('doctor');
    Route::get('/doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
    Route::post('/doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
});
