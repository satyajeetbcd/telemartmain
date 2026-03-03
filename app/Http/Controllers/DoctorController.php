<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\User;
use App\Models\State;
use App\Models\City;

class DoctorController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    public function dashboard()
    {
        $doctor = Auth::user();
        $today = now()->toDateString();
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => \App\Models\Appointment::where('doctor_id', $doctor->id)
                ->where('appointment_date', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'pending_reports' => 0, // Will be implemented with reports
            'total_consultations' => \App\Models\Appointment::where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->count(),
        ];

        // Recent patients
        $recentPatients = Patient::latest()->take(5)->get();

        // KYC Status
        $kycController = new \App\Http\Controllers\DoctorKycController();
        $kycStatus = $kycController->getKycStatus($doctor);

        return view('doctor.dashboard', compact('doctor', 'stats', 'recentPatients', 'kycStatus'));
    }

    public function profile(Request $request)
    {
        $doctor = Auth::user();
        
        // Load states and cities for dropdowns
        $states = State::where('is_active', true)->orderBy('name')->get();
        $cities = $doctor->state_id ? City::where('state_id', $doctor->state_id)->where('is_active', true)->orderBy('name')->get() : collect();
        
        // Get KYC status
        $kycController = new \App\Http\Controllers\DoctorKycController();
        $kycStatus = $kycController->getKycStatus($doctor);
        $kycDocuments = \App\Models\DoctorKyc::where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('document_type');

        // Get patients who have appointments with this doctor
        $patients = \App\Models\Patient::whereHas('appointments', function($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->whereIn('status', ['confirmed', 'completed']);
        })->with(['appointments' => function($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->orderBy('appointment_date', 'desc');
        }])->distinct()->get();

        // Get all appointments for this doctor
        $appointments = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        // Get reviews (only approved)
        $reviews = \App\Models\DoctorReview::where('doctor_id', $doctor->id)
            ->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->with(['patient', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = \App\Models\DoctorReview::getAverageRating($doctor->id);
        $reviewCount = \App\Models\DoctorReview::getReviewCount($doctor->id);
        $ratingDistribution = \App\Models\DoctorReview::getRatingDistribution($doctor->id);

        $activeTab = $request->get('tab', 'profile');

        return view('doctor.profile', compact('doctor', 'kycStatus', 'kycDocuments', 'patients', 'appointments', 'reviews', 'averageRating', 'reviewCount', 'ratingDistribution', 'activeTab', 'states', 'cities'));
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();
        
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

        // Handle profile image upload - apply immediately (no approval needed)
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($doctor->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->profile_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doctor->profile_image);
            }
            
            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('profile_images', $imageName, 'public');
            $doctor->update(['profile_image' => $imagePath]);
            unset($validated['profile_image']); // Remove from validated to exclude from approval workflow
        }

        // Get original values for comparison (excluding profile_image)
        $originalValues = [];
        $changes = [];
        
        foreach ($validated as $key => $value) {
            if ($key === 'profile_image') continue; // Skip profile_image
            $originalValues[$key] = $doctor->$key;
            if ($doctor->$key != $value) {
                $changes[$key] = $value;
            }
        }

        // If there are changes, create a pending change request
        if (!empty($changes)) {
            \App\Models\DoctorProfileChange::create([
                'doctor_id' => $doctor->id,
                'changes' => $changes,
                'original_values' => $originalValues,
                'status' => 'pending',
            ]);

            // Update doctor status to pending_approval if currently active
            if ($doctor->status === 'active') {
                $doctor->update(['status' => 'pending_approval']);
            }

            return redirect()->route('doctor.profile')->with('success', 'Profile changes submitted for approval. They will be active after admin approval.');
        }

        return redirect()->route('doctor.profile')->with('info', 'No changes detected.');
    }
}
