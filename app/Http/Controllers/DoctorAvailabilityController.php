<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorAvailabilityController extends Controller
{
    /**
     * Display availability slots for the authenticated doctor
     */
    public function index()
    {
        $doctor = Auth::user();
        $availabilities = DoctorAvailability::where('doctor_id', $doctor->id)
            ->whereNull('specific_date') // Only show weekly recurring slots
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        // Get specific date overrides
        $specificAvailabilities = DoctorAvailability::where('doctor_id', $doctor->id)
            ->whereNotNull('specific_date')
            ->where('specific_date', '>=', now()->toDateString())
            ->orderBy('specific_date')
            ->orderBy('start_time')
            ->get();

        return view('doctor.availability.index', compact('availabilities', 'specificAvailabilities', 'doctor'));
    }

    /**
     * Show the form for creating a new availability slot
     */
    public function create()
    {
        $doctor = Auth::user();
        return view('doctor.availability.create', compact('doctor'));
    }

    /**
     * Store a newly created availability slot
     */
    public function store(Request $request)
    {
        $doctor = Auth::user();

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

        return redirect()->route('doctor.availability.index')->with('success', $message);
    }

    /**
     * Show the form for editing an availability slot
     */
    public function edit(DoctorAvailability $availability)
    {
        $doctor = Auth::user();
        
        // Ensure doctor owns this availability
        if ($availability->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('doctor.availability.edit', compact('availability', 'doctor'));
    }

    /**
     * Update an availability slot
     */
    public function update(Request $request, DoctorAvailability $availability)
    {
        $doctor = Auth::user();
        
        // Ensure doctor owns this availability
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

        return redirect()->route('doctor.availability.index')->with('success', 'Availability slot updated successfully.');
    }

    /**
     * Delete an availability slot
     */
    public function destroy(DoctorAvailability $availability)
    {
        $doctor = Auth::user();
        
        // Ensure doctor owns this availability
        if ($availability->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $availability->delete();

        return redirect()->route('doctor.availability.index')->with('success', 'Availability slot deleted successfully.');
    }

    /**
     * Get available slots for a specific date (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = DoctorAvailability::getAvailableSlotsForDate(
            $validated['doctor_id'],
            $validated['date']
        );

        return response()->json(['slots' => $slots]);
    }
}
