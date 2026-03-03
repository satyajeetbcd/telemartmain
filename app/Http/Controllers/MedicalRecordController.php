<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MedicalRecord::with(['patient', 'doctor', 'appointment']);

        if ($user->hasRole('Doctor')) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient) {
                $query->where('patient_id', $patient->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('record_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('diagnosis', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('patient_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('record_date', '<=', $request->date_to);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $records = $query->latest('record_date')->latest('id')->paginate(15)->withQueryString();

        return view('medical-records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Doctor') && !$user->hasRole('Super Admin') && !$user->hasRole('Administrator')) {
            abort(403, 'Only doctors can create medical records.');
        }

        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();

        $appointments = collect();
        if ($request->filled('patient_id')) {
            $appointments = Appointment::where('patient_id', $request->patient_id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->with('doctor')
                ->latest('appointment_date')
                ->get();
        }

        $selectedPatientId = $request->patient_id;
        $selectedAppointmentId = $request->appointment_id;

        return view('medical-records.create', compact(
            'patients', 'appointments', 'selectedPatientId', 'selectedAppointmentId'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Doctor') && !$user->hasRole('Super Admin') && !$user->hasRole('Administrator')) {
            abort(403, 'Only doctors can create medical records.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'record_type' => 'required|in:consultation,lab_report,prescription,diagnosis,discharge_summary,imaging,vaccination,surgical,follow_up,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'prescription' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'record_date' => 'required|date',
            'follow_up_date' => 'nullable|date|after:record_date',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'oxygen_saturation' => 'nullable|string|max:20',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $vitals = array_filter([
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'weight' => $request->weight,
            'height' => $request->height,
            'oxygen_saturation' => $request->oxygen_saturation,
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical-records/' . $validated['patient_id'], 'public');
                $attachmentPaths[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $record = MedicalRecord::create([
            'record_number' => MedicalRecord::generateRecordNumber(),
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $user->hasRole('Doctor') ? $user->id : ($validated['appointment_id'] ? Appointment::find($validated['appointment_id'])->doctor_id : $user->id),
            'appointment_id' => $validated['appointment_id'],
            'record_type' => $validated['record_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'symptoms' => $validated['symptoms'] ?? null,
            'vitals' => !empty($vitals) ? $vitals : null,
            'prescription' => $validated['prescription'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'record_date' => $validated['record_date'],
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        return redirect()->route('medical-records.show', $record)
            ->with('success', 'Medical record created successfully. Record #' . $record->record_number);
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $medicalRecord);

        $medicalRecord->load(['patient', 'doctor', 'appointment', 'creator']);

        return view('medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $medicalRecord->doctor_id !== $user->id) {
            abort(403, 'You can only edit your own medical records.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot edit medical records.');
        }

        $medicalRecord->load(['patient', 'doctor', 'appointment']);
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();

        return view('medical-records.edit', compact('medicalRecord', 'patients'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $medicalRecord->doctor_id !== $user->id) {
            abort(403, 'You can only edit your own medical records.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot edit medical records.');
        }

        $validated = $request->validate([
            'record_type' => 'required|in:consultation,lab_report,prescription,diagnosis,discharge_summary,imaging,vaccination,surgical,follow_up,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'prescription' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'record_date' => 'required|date',
            'follow_up_date' => 'nullable|date|after:record_date',
            'status' => 'required|in:active,archived',
            'blood_pressure' => 'nullable|string|max:20',
            'heart_rate' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'oxygen_saturation' => 'nullable|string|max:20',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
            'remove_attachments' => 'nullable|array',
        ]);

        $vitals = array_filter([
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'weight' => $request->weight,
            'height' => $request->height,
            'oxygen_saturation' => $request->oxygen_saturation,
        ]);

        $existingAttachments = $medicalRecord->attachments ?? [];

        if ($request->filled('remove_attachments')) {
            foreach ($request->remove_attachments as $index) {
                if (isset($existingAttachments[$index])) {
                    Storage::disk('public')->delete($existingAttachments[$index]['path']);
                    unset($existingAttachments[$index]);
                }
            }
            $existingAttachments = array_values($existingAttachments);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('medical-records/' . $medicalRecord->patient_id, 'public');
                $existingAttachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
        }

        $medicalRecord->update([
            'record_type' => $validated['record_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'symptoms' => $validated['symptoms'] ?? null,
            'vitals' => !empty($vitals) ? $vitals : null,
            'prescription' => $validated['prescription'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'attachments' => !empty($existingAttachments) ? $existingAttachments : null,
            'record_date' => $validated['record_date'],
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $medicalRecord->doctor_id !== $user->id) {
            abort(403, 'You can only delete your own medical records.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot delete medical records.');
        }

        if ($medicalRecord->attachments) {
            foreach ($medicalRecord->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record deleted successfully.');
    }

    public function download(MedicalRecord $medicalRecord, int $attachmentIndex)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $medicalRecord);

        $attachments = $medicalRecord->attachments;
        if (!$attachments || !isset($attachments[$attachmentIndex])) {
            abort(404, 'Attachment not found.');
        }

        $attachment = $attachments[$attachmentIndex];
        $path = Storage::disk('public')->path($attachment['path']);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->download($path, $attachment['name']);
    }

    public function getAppointments(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $user = Auth::user();
        $query = Appointment::where('patient_id', $request->patient_id)
            ->where('status', 'completed')
            ->with('doctor');

        if ($user->hasRole('Doctor')) {
            $query->where('doctor_id', $user->id);
        }

        $appointments = $query->latest('appointment_date')->get()->map(function ($apt) {
            return [
                'id' => $apt->id,
                'text' => $apt->appointment_number . ' - ' . $apt->appointment_date->format('M d, Y') . ' - Dr. ' . $apt->doctor->name,
            ];
        });

        return response()->json($appointments);
    }

    private function authorizeAccess($user, MedicalRecord $record): void
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('Administrator')) {
            return;
        }

        if ($user->hasRole('Doctor') && $record->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $record->patient_id !== $patient->id) {
                abort(403, 'Unauthorized access.');
            }
        }
    }
}
