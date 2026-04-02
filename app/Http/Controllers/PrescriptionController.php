<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Prescription::with(['patient', 'doctor', 'items']);

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
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhere('diagnosis', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('patient_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('medicine_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('prescription_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('prescription_date', '<=', $request->date_to);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $prescriptions = $query->latest('prescription_date')->latest('id')->paginate(15)->withQueryString();

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Doctor')) {
            abort(403, 'Only doctors can create prescriptions. Patients can only view their prescriptions.');
        }

        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();

        $appointments = collect();
        if ($request->filled('patient_id')) {
            $query = Appointment::where('patient_id', $request->patient_id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->with('doctor')
                ->latest('appointment_date');

            if ($user->hasRole('Doctor')) {
                $query->where('doctor_id', $user->id);
            }

            $appointments = $query->get();
        }

        $selectedPatientId = $request->patient_id;
        $selectedAppointmentId = $request->appointment_id;

        return view('prescriptions.create', compact(
            'patients', 'appointments', 'selectedPatientId', 'selectedAppointmentId'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Doctor')) {
            abort(403, 'Only doctors can create prescriptions. Patients can only view their prescriptions.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'prescription_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:prescription_date',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_name' => 'required|string|max:255',
            'medicines.*.dosage' => 'nullable|string|max:100',
            'medicines.*.frequency' => 'nullable|string|max:100',
            'medicines.*.duration' => 'nullable|string|max:100',
            'medicines.*.quantity' => 'nullable|string|max:100',
            'medicines.*.route' => 'nullable|in:oral,topical,injection,inhaler,drops,sublingual,rectal,intravenous,other',
            'medicines.*.instructions' => 'nullable|string|max:500',
        ]);

        $prescription = DB::transaction(function () use ($validated, $user, $request) {
            $prescription = Prescription::create([
                'prescription_number' => Prescription::generatePrescriptionNumber(),
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $user->id,
                'appointment_id' => $validated['appointment_id'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'prescription_date' => $validated['prescription_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            foreach ($request->medicines as $index => $medicine) {
                if (empty($medicine['medicine_name'])) continue;
                
                $prescription->items()->create([
                    'medicine_name' => $medicine['medicine_name'],
                    'dosage' => $medicine['dosage'] ?? null,
                    'frequency' => $medicine['frequency'] ?? null,
                    'duration' => $medicine['duration'] ?? null,
                    'quantity' => $medicine['quantity'] ?? null,
                    'route' => $medicine['route'] ?? 'oral',
                    'instructions' => $medicine['instructions'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            return $prescription;
        });

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Prescription created successfully. Prescription #' . $prescription->prescription_number);
    }

    public function show(Prescription $prescription)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $prescription);

        $prescription->load(['patient', 'doctor', 'appointment', 'creator', 'items']);

        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $prescription->doctor_id !== $user->id) {
            abort(403, 'You can only edit your own prescriptions.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot edit prescriptions.');
        }

        $prescription->load(['patient', 'doctor', 'appointment', 'items']);
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();

        return view('prescriptions.edit', compact('prescription', 'patients'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $prescription->doctor_id !== $user->id) {
            abort(403, 'You can only edit your own prescriptions.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot edit prescriptions.');
        }

        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'prescription_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:prescription_date',
            'status' => 'required|in:active,completed,cancelled,expired',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_name' => 'required|string|max:255',
            'medicines.*.dosage' => 'nullable|string|max:100',
            'medicines.*.frequency' => 'nullable|string|max:100',
            'medicines.*.duration' => 'nullable|string|max:100',
            'medicines.*.quantity' => 'nullable|string|max:100',
            'medicines.*.route' => 'nullable|in:oral,topical,injection,inhaler,drops,sublingual,rectal,intravenous,other',
            'medicines.*.instructions' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $prescription, $request) {
            $prescription->update([
                'diagnosis' => $validated['diagnosis'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'prescription_date' => $validated['prescription_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'status' => $validated['status'],
            ]);

            $prescription->items()->delete();

            foreach ($request->medicines as $index => $medicine) {
                if (empty($medicine['medicine_name'])) continue;

                $prescription->items()->create([
                    'medicine_name' => $medicine['medicine_name'],
                    'dosage' => $medicine['dosage'] ?? null,
                    'frequency' => $medicine['frequency'] ?? null,
                    'duration' => $medicine['duration'] ?? null,
                    'quantity' => $medicine['quantity'] ?? null,
                    'route' => $medicine['route'] ?? 'oral',
                    'instructions' => $medicine['instructions'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription)
    {
        $user = Auth::user();

        if ($user->hasRole('Doctor') && $prescription->doctor_id !== $user->id) {
            abort(403, 'You can only delete your own prescriptions.');
        } elseif ($user->hasRole('Patient')) {
            abort(403, 'Patients cannot delete prescriptions.');
        }

        $prescription->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }

    public function getAppointments(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patients,id']);

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

    public function downloadPdf(Prescription $prescription)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $prescription);

        $prescription->load(['patient', 'doctor', 'appointment', 'items']);

        // Try to get consultation data linked to the same appointment
        $consultation = null;
        if ($prescription->appointment_id) {
            $consultation = Consultation::where('appointment_id', $prescription->appointment_id)->first();
        }

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription', 'consultation'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Prescription-' . $prescription->prescription_number . '.pdf';

        return $pdf->download($filename);
    }

    private function authorizeAccess($user, Prescription $prescription): void
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('Administrator')) {
            return;
        }

        if ($user->hasRole('Doctor') && $prescription->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->hasRole('Patient')) {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $prescription->patient_id !== $patient->id) {
                abort(403, 'Unauthorized access.');
            }
        }
    }
}
