<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDoctorAvailabilityController extends Controller
{
    /**
     * Show the form for creating a new availability slot for a doctor
     */
    public function create(User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }
        return view('admin.doctors.availability.create', compact('doctor'));
    }

    /**
     * Store a newly created availability slot
     */
    public function store(Request $request, User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $validated = $request->validate([
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'specific_date' => 'nullable|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
            'break_duration' => 'nullable|integer|min:0|max:60',
            'is_available' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        // Either day_of_week or specific_date must be provided
        if (empty($validated['day_of_week']) && empty($validated['specific_date'])) {
            return back()->withErrors(['day_of_week' => 'Either day of week or specific date must be selected.'])->withInput();
        }

        // If specific_date is provided, day_of_week should be null
        if (!empty($validated['specific_date'])) {
            $validated['day_of_week'] = null;
        }

        $validated['doctor_id'] = $doctor->id;
        $validated['is_available'] = $validated['is_available'] ?? true;
        $validated['break_duration'] = $validated['break_duration'] ?? 0;

        DoctorAvailability::create($validated);

        $message = !empty($validated['specific_date']) 
            ? 'Specific date availability created successfully.'
            : 'Weekly availability slot created successfully.';

        return redirect()->route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'availability'])->with('success', $message);
    }

    /**
     * Show the form for editing an availability slot
     */
    public function edit(User $doctor, DoctorAvailability $availability)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }
        
        // Ensure availability belongs to this doctor
        if ($availability->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.doctors.availability.edit', compact('availability', 'doctor'));
    }

    /**
     * Update an availability slot
     */
    public function update(Request $request, User $doctor, DoctorAvailability $availability)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }
        
        // Ensure availability belongs to this doctor
        if ($availability->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'specific_date' => 'nullable|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
            'break_duration' => 'nullable|integer|min:0|max:60',
            'is_available' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        // Either day_of_week or specific_date must be provided
        if (empty($validated['day_of_week']) && empty($validated['specific_date'])) {
            return back()->withErrors(['day_of_week' => 'Either day of week or specific date must be selected.'])->withInput();
        }

        // If specific_date is provided, day_of_week should be null
        if (!empty($validated['specific_date'])) {
            $validated['day_of_week'] = null;
        }

        $validated['is_available'] = $validated['is_available'] ?? true;
        $validated['break_duration'] = $validated['break_duration'] ?? 0;

        $availability->update($validated);

        return redirect()->route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'availability'])->with('success', 'Availability slot updated successfully.');
    }

    /**
     * Delete an availability slot
     */
    public function destroy(User $doctor, DoctorAvailability $availability)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }
        
        // Ensure availability belongs to this doctor
        if ($availability->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $availability->delete();

        return redirect()->route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'availability'])->with('success', 'Availability slot deleted successfully.');
    }
}
