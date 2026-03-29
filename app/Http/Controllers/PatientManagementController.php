<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientManagementController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::with('user')->latest();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $patients = $query->paginate(15);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email',
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
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,archived',
            'notes' => 'nullable|string',
            'create_user_account' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_user_account,1|string|min:8|confirmed',
        ]);

        // Generate patient ID
        $validated['patient_id'] = Patient::generatePatientId();
        $validated['status'] = $validated['status'] ?? 'active';

        // Create user account if requested
        if ($request->has('create_user_account') && $request->create_user_account) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            // Assign Patient role
            $user->assignRole('Patient');
            $validated['user_id'] = $user->id;
        }

        // Remove user account creation fields
        unset($validated['create_user_account'], $validated['password'], $validated['password_confirmation']);

        $patient = Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load(['user', 'medicalRecords' => function ($q) {
            $q->with('doctor')->latest('record_date')->latest('id');
        }, 'prescriptions' => function ($q) {
            $q->with(['doctor', 'items'])->latest('prescription_date')->latest('id');
        }, 'appointments' => function ($q) {
            $q->with('doctor')->latest('appointment_date')->latest('appointment_time')->latest('id');
        }, 'consultations' => function ($q) {
            $q->with('doctor')->latest('created_at');
        }]);
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        $patient->load(['user', 'medicalRecords' => function ($q) {
            $q->with('doctor')->latest('record_date')->latest('id');
        }, 'prescriptions' => function ($q) {
            $q->with(['doctor', 'items'])->latest('prescription_date')->latest('id');
        }, 'appointments' => function ($q) {
            $q->with('doctor')->latest('appointment_date')->latest('appointment_time')->latest('id');
        }]);
        $doctors = User::role('Doctor')->orderBy('name')->get();
        return view('patients.edit', compact('patient', 'doctors'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
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
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,archived',
            'notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        // Update user email if linked
        if ($patient->user_id) {
            $user = User::find($patient->user_id);
            if ($user && $user->email !== $validated['email']) {
                $user->update(['email' => $validated['email']]);
            }
        }

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}
