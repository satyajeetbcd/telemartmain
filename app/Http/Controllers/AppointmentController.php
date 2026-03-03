<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Appointment::with(['patient', 'doctor']);

        // Filter based on user role
        if ($user->hasRole('Doctor')) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            } else {
                $query->whereRaw('1 = 0'); // No results if no patient record
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('appointment_date', $request->date);
        }

        // Filter upcoming appointments
        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('appointment_date', '>=', now()->toDateString())
                  ->whereIn('status', ['pending', 'confirmed']);
        }

        $appointments = $query->latest('appointment_date')
            ->latest('appointment_time')
            ->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment (booking)
     */
    public function create()
    {
        $user = Auth::user();
        $doctors = User::role('Doctor')->get();
        
        // Get patient record
        $patient = null;
        if ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
        }

        return view('appointments.create', compact('doctors', 'patient'));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get patient
        $patient = null;
        if ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->firstOrFail();
        } elseif ($request->has('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        } else {
            return back()->withErrors(['patient_id' => 'Patient is required.'])->withInput();
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
        ]);

        // Check if doctor exists and has Doctor role
        $doctor = User::findOrFail($validated['doctor_id']);
        if (!$doctor->hasRole('Doctor')) {
            return back()->withErrors(['doctor_id' => 'Selected user is not a doctor.'])->withInput();
        }

        // Check for duplicate appointments (same doctor, date, and time)
        $existingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please choose another time.'])->withInput();
        }

        // Get consultation fee from doctor profile
        $consultationFee = $doctor->consultation_fee ?? 0;

        $appointment = Appointment::create([
            'appointment_number' => Appointment::generateAppointmentNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reason' => $validated['reason'] ?? null,
            'consultation_fee' => $consultationFee,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment booked successfully! Your appointment number is: ' . $appointment->appointment_number);
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Check access
        if ($user->hasRole('Doctor') && $appointment->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        } elseif ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $appointment->patient_id !== $patient->id) {
                abort(403, 'Unauthorized access.');
            }
        }

        $appointment->load(['patient', 'doctor']);

        $patientPrescriptions = collect();
        $patientMedicalRecords = collect();
        $canManagePatient = ($user->hasRole('Doctor') && $appointment->doctor_id === $user->id)
            || $user->hasRole('Super Admin');
        if ($canManagePatient && $appointment->payment_status === 'paid') {
            $patientPrescriptions = \App\Models\Prescription::where('patient_id', $appointment->patient_id)
                ->with(['doctor', 'items'])
                ->latest('prescription_date')
                ->take(5)
                ->get();
            $patientMedicalRecords = \App\Models\MedicalRecord::where('patient_id', $appointment->patient_id)
                ->with('doctor')
                ->latest('record_date')
                ->take(5)
                ->get();
        }

        return view('appointments.show', compact('appointment', 'patientPrescriptions', 'patientMedicalRecords', 'canManagePatient'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Only doctors can edit appointments
        if (!$user->hasRole('Doctor') || $appointment->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $appointment->load(['patient', 'doctor']);
        return view('appointments.edit', compact('appointment'));
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        
        // Only doctors can update appointments
        if (!$user->hasRole('Doctor') || $appointment->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'prescription' => 'nullable|string',
            'cancellation_reason' => 'nullable|required_if:status,cancelled|string',
        ]);

        // Update timestamps based on status
        if ($validated['status'] === 'confirmed' && $appointment->status !== 'confirmed') {
            $validated['confirmed_at'] = now();
            
            // Create Zoom meeting when appointment is confirmed
            if (!$appointment->zoom_meeting_id) {
                $this->createZoomMeeting($appointment);
            }
        } elseif ($validated['status'] === 'completed' && $appointment->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] === 'cancelled' && $appointment->status !== 'cancelled') {
            $validated['cancelled_at'] = now();
            
            // Delete Zoom meeting if cancelled
            if ($appointment->zoom_meeting_id) {
                $this->deleteZoomMeeting($appointment);
            }
        }

        $appointment->update($validated);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Mark payment as received for an appointment. When paid, appointment is confirmed (success) for doctor and patient.
     */
    public function markPaymentReceived(Appointment $appointment)
    {
        $user = Auth::user();

        $canMark = $user->hasRole('Doctor') && $appointment->doctor_id === $user->id
            || $user->hasRole('Super Admin')
            || $user->hasRole('Administrator')
            || $user->hasRole('Receptionist');

        if (!$canMark) {
            abort(403, 'You are not allowed to mark payment for this appointment.');
        }

        if ($appointment->payment_status === 'paid') {
            return redirect()->route('appointments.show', $appointment)
                ->with('info', 'Payment was already marked as received.');
        }

        $appointment->payment_status = 'paid';
        $appointment->save();

        if ($appointment->status === 'pending') {
            $appointment->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
            if (!$appointment->zoom_meeting_id) {
                $this->createZoomMeeting($appointment);
            }
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Payment marked as received. Appointment is now confirmed and visible as success to doctor and patient.');
    }

    /**
     * Remove the specified appointment
     */
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Only doctors or admins can delete appointments
        if ($user->hasRole('Doctor') && $appointment->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctor = User::findOrFail($request->doctor_id);
        if (!$doctor->hasRole('Doctor')) {
            return response()->json(['error' => 'Invalid doctor'], 400);
        }

        // Get available slots from doctor's availability settings
        $availableSlots = \App\Models\DoctorAvailability::getAvailableSlotsForDate(
            $request->doctor_id,
            $request->date
        );

        return response()->json([
            'available_slots' => array_values($availableSlots),
        ]);
    }

    /**
     * Create Zoom meeting for appointment
     */
    private function createZoomMeeting(Appointment $appointment): void
    {
        try {
            $zoomService = new ZoomService();
            
            // Prepare meeting data
            $appointmentDateTime = Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time);
            
            $meetingData = [
                'topic' => "Consultation - {$appointment->patient->full_name} with Dr. {$appointment->doctor->name}",
                'start_time' => $appointmentDateTime,
                'duration' => 30, // Default 30 minutes
                'timezone' => config('app.timezone', 'Asia/Kolkata'),
            ];

            $meeting = $zoomService->createMeeting($meetingData);

            if ($meeting) {
                $appointment->update([
                    'zoom_meeting_id' => $meeting['id'],
                    'zoom_meeting_uuid' => $meeting['uuid'],
                    'zoom_join_url' => $meeting['join_url'],
                    'zoom_start_url' => $meeting['start_url'],
                    'zoom_meeting_password' => $meeting['password'] ?? null,
                    'zoom_meeting_created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create Zoom meeting', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete Zoom meeting
     */
    private function deleteZoomMeeting(Appointment $appointment): void
    {
        try {
            if ($appointment->zoom_meeting_id) {
                $zoomService = new ZoomService();
                $zoomService->deleteMeeting($appointment->zoom_meeting_id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete Zoom meeting', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
