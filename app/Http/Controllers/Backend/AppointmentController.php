<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pagename = 'Appointment Booking';
        $breadcrumb = 'Appointment Booking';

        $appointment = Appointment::with(['patient', 'doctor'])->get();
        return view('backend.appointment.index', compact('pagename', 'breadcrumb', 'appointment'));
    }
}
