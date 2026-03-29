<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['patient', 'doctor'])
            ->latest()
            ->paginate(20);

        return view('consultations.index', compact('consultations'));
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor', 'appointment']);

        return view('consultations.show', compact('consultation'));
    }

    public function create(Request $request)
    {
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        $doctors = User::role('Doctor')->where('status', 'active')->orderBy('name')->get();
        $selectedPatientId = $request->patient_id;

        return view('consultations.form', compact('patients', 'doctors', 'selectedPatientId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateConsultation($request);

        $consultation = Consultation::create([
            'consultation_number' => Consultation::generateConsultationNumber(),
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'] ?? null,
            'is_followup' => $request->boolean('is_followup'),
            'chief_complaints' => $this->parseJsonField($request->chief_complaints_json),
            'patient_history' => $this->parseJsonField($request->patient_history_json),
            'personal_history' => $this->parseJsonField($request->personal_history_json),
            'family_history' => $this->parseJsonField($request->family_history_json),
            'allergies' => $this->parseJsonField($request->allergies_json),
            'medications' => $this->parseJsonField($request->medications_json),
            'query' => $validated['query'] ?? null,
            'location_preference' => $validated['location_preference'] ?? null,
            'state' => $validated['state'] ?? null,
            'opd' => $validated['opd'] ?? null,
            'status' => $validated['status'] ?? 'pending',
        ]);

        $this->handleAttachments($request, $consultation);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation created successfully.');
    }

    public function edit(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor']);
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        $doctors = User::role('Doctor')->where('status', 'active')->orderBy('name')->get();

        return view('consultations.form', compact('consultation', 'patients', 'doctors'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $this->validateConsultation($request);

        $consultation->update([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'] ?? null,
            'is_followup' => $request->boolean('is_followup'),
            'chief_complaints' => $this->parseJsonField($request->chief_complaints_json),
            'patient_history' => $this->parseJsonField($request->patient_history_json),
            'personal_history' => $this->parseJsonField($request->personal_history_json),
            'family_history' => $this->parseJsonField($request->family_history_json),
            'allergies' => $this->parseJsonField($request->allergies_json),
            'medications' => $this->parseJsonField($request->medications_json),
            'query' => $validated['query'] ?? null,
            'location_preference' => $validated['location_preference'] ?? null,
            'state' => $validated['state'] ?? null,
            'opd' => $validated['opd'] ?? null,
            'status' => $validated['status'] ?? $consultation->status,
        ]);

        $this->handleAttachments($request, $consultation);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation updated successfully.');
    }

    public function destroy(Consultation $consultation)
    {
        $patientId = $consultation->patient_id;
        $consultation->delete();

        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Consultation deleted successfully.');
    }

    private function validateConsultation(Request $request): array
    {
        return $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'is_followup' => 'nullable',
            'query' => 'nullable|string|max:1500',
            'location_preference' => 'nullable|string',
            'state' => 'nullable|string',
            'opd' => 'nullable|string',
            'status' => 'nullable|in:pending,in_review,completed,cancelled',
            'health_records' => 'nullable|array|max:5',
            'health_records.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);
    }

    private function parseJsonField($value): ?array
    {
        if (empty($value)) return null;
        if (is_array($value)) return $value;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function handleAttachments(Request $request, Consultation $consultation): void
    {
        if ($request->hasFile('health_records')) {
            $existing = $consultation->health_records ?? [];
            foreach ($request->file('health_records') as $file) {
                $path = $file->store('consultations/' . $consultation->patient_id, 'public');
                $existing[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $consultation->update(['health_records' => $existing]);
        }
    }
}
