<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\DoctorAvailability;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiPatientController extends Controller
{
    protected function getPatient(Request $request): Patient
    {
        return $request->patient;
    }

    public function dashboardStats(Request $request)
    {
        $patient = $this->getPatient($request);

        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $medicalRecords = MedicalRecord::where('patient_id', $patient->id)->count();
        $prescriptions = Prescription::where('patient_id', $patient->id)->count();
        $totalConsultations = Consultation::where('patient_id', $patient->id)->count();

        $recentAppointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($apt) {
                return [
                    'id' => $apt->id,
                    'doctor_name' => $apt->doctor->name ?? 'N/A',
                    'appointment_date' => $apt->appointment_date->format('M d, Y'),
                    'appointment_time' => date('h:i A', strtotime($apt->appointment_time)),
                    'status' => $apt->status,
                ];
            });

        $recentConsultations = Consultation::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'consultation_number' => $c->consultation_number,
                    'chief_complaints' => collect($c->chief_complaints ?? [])->pluck('name')->take(3)->toArray(),
                    'query' => $c->query,
                    'doctor_name' => $c->doctor?->name ?? 'Unassigned',
                    'status' => $c->status,
                    'date' => $c->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'upcoming_appointments' => $upcomingAppointments,
            'medical_records' => $medicalRecords,
            'prescriptions' => $prescriptions,
            'total_consultations' => $totalConsultations,
            'recent_appointments' => $recentAppointments,
            'recent_consultations' => $recentConsultations,
        ]);
    }

    public function profile(Request $request)
    {
        $patient = $this->getPatient($request);

        return response()->json([
            'name' => $patient->first_name . ' ' . $patient->last_name,
            'email' => $patient->email,
            'phone' => $patient->phone,
            'date_of_birth' => $patient->date_of_birth?->format('Y-m-d'),
            'address' => $patient->address,
            'postal_code' => $patient->postal_code,
            'patient' => [
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'gender' => $patient->gender,
                'blood_group' => $patient->blood_group,
                'medical_history' => $patient->medical_history,
                'allergies' => $patient->allergies,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $patient = $this->getPatient($request);
        $nameParts = explode(' ', $request->name, 2);

        $patient->update([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
        ]);

        return response()->json(['message' => 'Profile updated successfully.']);
    }

    public function appointments(Request $request)
    {
        $patient = $this->getPatient($request);

        $appointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get()
            ->map(function ($apt) {
                return [
                    'id' => $apt->id,
                    'appointment_number' => $apt->appointment_number,
                    'doctor_name' => $apt->doctor->name ?? 'N/A',
                    'specialization' => $apt->doctor->specialization ?? 'General',
                    'appointment_date' => $apt->appointment_date->format('M d, Y'),
                    'appointment_time' => date('h:i A', strtotime($apt->appointment_time)),
                    'status' => $apt->status,
                    'consultation_fee' => $apt->consultation_fee ? '₹' . number_format($apt->consultation_fee, 2) : 'N/A',
                    'reason' => $apt->reason,
                    'zoom_join_url' => $apt->zoom_join_url,
                ];
            });

        return response()->json(['appointments' => $appointments]);
    }

    public function doctors(Request $request)
    {
        $query = User::role('Doctor')->where('status', 'active');

        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $doctors = $query->orderBy('name')->get()->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization ?? 'General',
                'experience_years' => $doctor->experience_years,
                'consultation_fee' => $doctor->consultation_fee ? number_format($doctor->consultation_fee, 2) : null,
                'profile_image' => $doctor->profile_image,
                'qualifications' => $doctor->qualifications,
            ];
        });

        // Get unique specializations for filter
        $specializations = User::role('Doctor')
            ->where('status', 'active')
            ->whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization')
            ->sort()
            ->values();

        return response()->json([
            'doctors' => $doctors,
            'specializations' => $specializations,
        ]);
    }

    public function doctorSlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctor = User::role('Doctor')->where('status', 'active')->find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        $slots = DoctorAvailability::getAvailableSlotsForDate($doctorId, $request->date);

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'consultation_fee' => $doctor->consultation_fee ? number_format($doctor->consultation_fee, 2) : null,
            ],
            'date' => $request->date,
            'slots' => $slots,
        ]);
    }

    public function bookAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
        ]);

        $patient = $this->getPatient($request);

        $doctor = User::role('Doctor')->where('status', 'active')->find($request->doctor_id);
        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found or not available.'], 422);
        }

        // Check for duplicate booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This time slot is no longer available. Please select another.'], 422);
        }

        $appointment = Appointment::create([
            'appointment_number' => Appointment::generateAppointmentNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
            'status' => 'pending',
            'payment_status' => 'pending',
            'consultation_fee' => $doctor->consultation_fee,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully!',
            'appointment' => [
                'id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'doctor_name' => $doctor->name,
                'specialization' => $doctor->specialization ?? 'General',
                'appointment_date' => $appointment->appointment_date->format('M d, Y'),
                'appointment_time' => date('h:i A', strtotime($appointment->appointment_time)),
                'consultation_fee' => $doctor->consultation_fee ? '₹' . number_format($doctor->consultation_fee, 2) : 'N/A',
                'status' => $appointment->status,
            ],
        ], 201);
    }

    public function medicalRecords(Request $request)
    {
        $patient = $this->getPatient($request);

        $records = MedicalRecord::with(['appointment.doctor', 'doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('record_date', 'desc')
            ->get()
            ->map(function ($record) {
                $doctorName = $record->doctor?->name ?? $record->appointment?->doctor?->name ?? null;

                return [
                    'id' => $record->id,
                    'record_number' => $record->record_number,
                    'title' => $record->title ?? 'Medical Record',
                    'doctor_name' => $doctorName,
                    'date' => $record->record_date->format('M d, Y'),
                    'record_type' => $record->record_type ?? 'General',
                    'description' => $record->description,
                    'notes' => $record->notes,
                    'attachments' => collect($record->attachments ?? [])->map(function ($att, $index) use ($record) {
                        return [
                            'index' => $index,
                            'name' => $att['name'] ?? 'File',
                            'size' => $att['size'] ?? 0,
                            'type' => $att['type'] ?? '',
                        ];
                    })->values()->toArray(),
                ];
            });

        $recordTypes = MedicalRecord::getRecordTypes();

        return response()->json([
            'records' => $records,
            'record_types' => $recordTypes,
        ]);
    }

    public function storeMedicalRecord(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'record_type' => 'required|string|in:' . implode(',', array_keys(MedicalRecord::getRecordTypes())),
            'record_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $patient = $this->getPatient($request);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical-records/' . $patient->id, 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => null,
            'record_type' => $request->record_type,
            'title' => $request->title,
            'description' => $request->description,
            'notes' => $request->notes,
            'attachments' => $attachments ?: null,
            'record_date' => $request->record_date,
            'status' => 'active',
            'created_by' => null,
        ]);

        return response()->json([
            'message' => 'Medical record added successfully!',
            'record' => [
                'id' => $record->id,
                'record_number' => $record->record_number,
                'title' => $record->title,
            ],
        ], 201);
    }

    public function downloadAttachment(Request $request, $recordId, $index)
    {
        $patient = $this->getPatient($request);

        $record = MedicalRecord::where('patient_id', $patient->id)->findOrFail($recordId);

        $attachments = $record->attachments ?? [];

        if (!isset($attachments[$index])) {
            return response()->json(['message' => 'Attachment not found.'], 404);
        }

        $attachment = $attachments[$index];
        $path = Storage::disk('public')->path($attachment['path']);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download($path, $attachment['name']);
    }

    public function storeConsultation(Request $request)
    {
        $patient = $this->getPatient($request);

        $consultation = Consultation::create([
            'consultation_number' => Consultation::generateConsultationNumber(),
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'is_followup' => $request->boolean('is_followup'),
            'chief_complaints' => $request->chief_complaints,
            'patient_history' => $request->patient_history,
            'personal_history' => $request->personal_history,
            'family_history' => $request->family_history,
            'allergies' => $request->allergies,
            'medications' => $request->medications,
            'query' => $request->input('query'),
            'location_preference' => $request->location_preference,
            'state' => $request->state,
            'opd' => $request->opd,
            'status' => 'pending',
        ]);

        // Handle health record attachments
        if ($request->hasFile('health_records')) {
            $attachments = [];
            foreach ($request->file('health_records') as $file) {
                $path = $file->store('consultations/' . $patient->id, 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $consultation->update(['health_records' => $attachments]);
        }

        return response()->json([
            'message' => 'Consultation submitted successfully!',
            'consultation' => [
                'id' => $consultation->id,
                'consultation_number' => $consultation->consultation_number,
                'status' => $consultation->status,
            ],
        ], 201);
    }

    public function consultations(Request $request)
    {
        $patient = $this->getPatient($request);

        $consultations = Consultation::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'consultation_number' => $c->consultation_number,
                    'is_followup' => $c->is_followup,
                    'chief_complaints' => $c->chief_complaints,
                    'query' => $c->query,
                    'doctor_name' => $c->doctor?->name ?? 'Unassigned',
                    'status' => $c->status,
                    'date' => $c->created_at->format('M d, Y h:i A'),
                ];
            });

        return response()->json(['consultations' => $consultations]);
    }

    public function showConsultation(Request $request, $id)
    {
        $patient = $this->getPatient($request);

        $consultation = Consultation::with('doctor')
            ->where('patient_id', $patient->id)
            ->findOrFail($id);

        return response()->json([
            'consultation' => [
                'id' => $consultation->id,
                'consultation_number' => $consultation->consultation_number,
                'is_followup' => $consultation->is_followup,
                'chief_complaints' => $consultation->chief_complaints,
                'patient_history' => $consultation->patient_history,
                'personal_history' => $consultation->personal_history,
                'family_history' => $consultation->family_history,
                'allergies' => $consultation->allergies,
                'medications' => $consultation->medications,
                'query' => $consultation->query,
                'location_preference' => $consultation->location_preference,
                'state' => $consultation->state,
                'opd' => $consultation->opd,
                'doctor_id' => $consultation->doctor_id,
                'doctor_name' => $consultation->doctor?->name ?? 'Unassigned',
                'health_records' => $consultation->health_records,
                'status' => $consultation->status,
                'date' => $consultation->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }

    public function updateConsultation(Request $request, $id)
    {
        $patient = $this->getPatient($request);

        $consultation = Consultation::where('patient_id', $patient->id)->findOrFail($id);

        if ($consultation->status !== 'pending') {
            return response()->json(['message' => 'Only pending consultations can be edited.'], 403);
        }

        $consultation->update([
            'is_followup' => $request->boolean('is_followup'),
            'chief_complaints' => $request->chief_complaints,
            'patient_history' => $request->patient_history,
            'personal_history' => $request->personal_history,
            'family_history' => $request->family_history,
            'allergies' => $request->allergies,
            'medications' => $request->medications,
            'query' => $request->input('query'),
            'location_preference' => $request->location_preference,
            'state' => $request->state,
            'opd' => $request->opd,
            'doctor_id' => $request->doctor_id ?? $consultation->doctor_id,
        ]);

        // Handle health record attachments
        if ($request->hasFile('health_records')) {
            $existing = $consultation->health_records ?? [];
            foreach ($request->file('health_records') as $file) {
                $path = $file->store('consultations/' . $patient->id, 'public');
                $existing[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $consultation->update(['health_records' => $existing]);
        }

        return response()->json([
            'message' => 'Consultation updated successfully!',
            'consultation' => [
                'id' => $consultation->id,
                'consultation_number' => $consultation->consultation_number,
                'status' => $consultation->status,
            ],
        ]);
    }

    public function prescriptions(Request $request)
    {
        $patient = $this->getPatient($request);

        $prescriptions = Prescription::with(['appointment.doctor', 'items'])
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($prescription) {
                return [
                    'id' => $prescription->id,
                    'doctor_name' => $prescription->appointment?->doctor?->name ?? 'N/A',
                    'date' => $prescription->created_at->format('M d, Y'),
                    'diagnosis' => $prescription->diagnosis ?? '-',
                    'notes' => $prescription->notes,
                    'items' => $prescription->items->map(function ($item) {
                        return [
                            'medicine_name' => $item->medicine_name,
                            'dosage' => $item->dosage,
                            'frequency' => $item->frequency,
                            'duration' => $item->duration,
                        ];
                    }),
                ];
            });

        return response()->json(['prescriptions' => $prescriptions]);
    }

    public function invoicePdf(Request $request, $appointmentId)
    {
        $patient = $this->getPatient($request);

        $appointment = Appointment::with(['patient', 'doctor'])
            ->where('patient_id', $patient->id)
            ->findOrFail($appointmentId);

        $pdf = Pdf::loadView('invoices.pdf', compact('appointment'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Invoice-INV-' . $appointment->appointment_number . '.pdf';

        return $pdf->download($filename);
    }

    public function prescriptionPdf(Request $request, $id)
    {
        $patient = $this->getPatient($request);

        $prescription = Prescription::with(['patient', 'doctor', 'appointment', 'items'])
            ->where('patient_id', $patient->id)
            ->findOrFail($id);

        $consultation = null;
        if ($prescription->appointment_id) {
            $consultation = Consultation::where('appointment_id', $prescription->appointment_id)->first();
        }

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription', 'consultation'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Prescription-' . $prescription->prescription_number . '.pdf';

        return $pdf->download($filename);
    }
}
