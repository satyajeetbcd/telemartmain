<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorProfileChange;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDoctorProfileController extends Controller
{
    /**
     * Show pending profile changes for a doctor
     */
    public function showPendingChanges(User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $pendingChanges = DoctorProfileChange::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $allChanges = DoctorProfileChange::where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.doctors.profile-changes', compact('doctor', 'pendingChanges', 'allChanges'));
    }

    /**
     * Approve profile changes
     */
    public function approve(Request $request, DoctorProfileChange $profileChange)
    {
        if ($profileChange->status !== 'pending') {
            return redirect()->back()->with('error', 'This change request has already been processed.');
        }

        $doctor = $profileChange->doctor;

        // Apply the changes to the doctor's profile
        $doctor->update($profileChange->changes);

        // Update the change request
        $profileChange->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // If doctor status was pending_approval, check if they should be active
        // Only set to active if KYC is also complete
        if ($doctor->status === 'pending_approval') {
            $kycController = new \App\Http\Controllers\DoctorKycController();
            $kycStatus = $kycController->getKycStatus($doctor);
            
            if ($kycStatus['overall_status'] === 'approved') {
                $doctor->update(['status' => 'active']);
            } else {
                $doctor->update(['status' => 'pending_kyc']);
            }
        }

        return redirect()->route('doctors.show', $doctor)->with('success', 'Profile changes approved and applied successfully.');
    }

    /**
     * Reject profile changes
     */
    public function reject(Request $request, DoctorProfileChange $profileChange)
    {
        if ($profileChange->status !== 'pending') {
            return redirect()->back()->with('error', 'This change request has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $doctor = $profileChange->doctor;

        $profileChange->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
        ]);

        // If doctor status was pending_approval, check if they should be active or pending_kyc
        if ($doctor->status === 'pending_approval') {
            $kycController = new \App\Http\Controllers\DoctorKycController();
            $kycStatus = $kycController->getKycStatus($doctor);
            
            if ($kycStatus['overall_status'] === 'approved') {
                $doctor->update(['status' => 'active']);
            } else {
                $doctor->update(['status' => 'pending_kyc']);
            }
        }

        return redirect()->route('doctors.show', $doctor)->with('success', 'Profile changes rejected.');
    }

    /**
     * Update doctor profile directly (admin can edit without approval)
     */
    public function updateProfile(Request $request, User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $doctor->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'aadhar_card_number' => 'nullable|string|max:12|regex:/^[0-9]{12}$/',
            'address' => 'nullable|string|max:500',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'city' => 'nullable|string|max:100', // Keep for backward compatibility
            'state' => 'nullable|string|max:100', // Keep for backward compatibility
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specialization' => 'nullable|string|max:255',
            'qualifications' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'nullable|numeric|min:0',
            'license_number' => 'nullable|string|max:255',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }
            
            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('profile_images', $imageName, 'public');
            $validated['profile_image'] = $imagePath;
        }

        $doctor->update($validated);

        return redirect()->route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'profile'])->with('success', 'Doctor profile updated successfully.');
    }
}
