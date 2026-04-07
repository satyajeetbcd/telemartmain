<?php

namespace App\Http\Controllers;

use App\Models\DoctorLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorLeaveController extends Controller
{
    public function store(Request $request)
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'leave_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:500',
        ]);

        // Check if leave already exists for this date
        $exists = DoctorLeave::where('doctor_id', $doctor->id)
            ->where('leave_date', $validated['leave_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['leave_date' => 'You already have a leave on this date.'])->withInput();
        }

        DoctorLeave::create([
            'doctor_id' => $doctor->id,
            'leave_date' => $validated['leave_date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->route('doctor.profile', ['tab' => 'availability'])
            ->with('success', 'Leave added successfully.');
    }

    public function destroy(DoctorLeave $doctorLeave)
    {
        $doctor = Auth::user();

        if ($doctorLeave->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $doctorLeave->delete();

        return redirect()->route('doctor.profile', ['tab' => 'availability'])
            ->with('success', 'Leave cancelled successfully.');
    }
}
