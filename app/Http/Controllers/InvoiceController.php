<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->hasRole('Patient')) {
            $patient = $appointment->patient;
            if (!$patient || $patient->user_id !== $user->id) {
                abort(403);
            }
        } elseif ($user->hasRole('Doctor')) {
            if ($appointment->doctor_id !== $user->id) {
                abort(403);
            }
        }

        $appointment->load(['patient', 'doctor']);

        return view('invoices.show', compact('appointment'));
    }
}
