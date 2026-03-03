<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;

class PatientController extends Controller
{
    // Middleware is applied in routes

    public function dashboard()
    {
        $user = Auth::user();
        
        // Get patient record if exists
        $patient = Patient::where('user_id', $user->id)->first();
        
        // If user has Patient role but no patient record, create one
        if ($user->hasRole('Patient') && !$patient) {
            $patient = Patient::create([
                'patient_id' => Patient::generatePatientId(),
                'user_id' => $user->id,
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'status' => 'active',
            ]);
        }

        $patientId = $patient ? $patient->id : null;
        $stats = [
            'upcoming_appointments' => $patientId ? \App\Models\Appointment::where('patient_id', $patientId)
                ->where('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count() : 0,
            'medical_records' => 0, // Will be implemented with records
            'prescriptions' => $patientId ? \App\Models\Appointment::where('patient_id', $patientId)
                ->whereNotNull('prescription')
                ->count() : 0,
            'lab_reports' => 0, // Will be implemented with reports
        ];

        return view('patient.dashboard', compact('user', 'patient', 'stats'));
    }

    public function profile()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        
        return view('patient.profile', compact('user', 'patient'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:255',
        ]);

        $patient->update($validated);

        // Also update user email if changed
        if ($request->has('email') && $request->email !== $user->email) {
            $user->update(['email' => $request->email]);
        }

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully.');
    }
}
