<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorKyc;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\DoctorKycController;

class DoctorListController extends Controller
{
    /**
     * Display a listing of doctors with KYC status
     */
    public function index(Request $request)
    {
        $query = User::role('Doctor')->with('kycDocuments');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by KYC status
        if ($request->has('kyc_status') && $request->kyc_status) {
            $doctors = $query->get();
            $filteredDoctors = $doctors->filter(function($doctor) use ($request) {
                $kycController = new DoctorKycController();
                $kycStatus = $kycController->getKycStatus($doctor);
                return $kycStatus['overall_status'] === $request->kyc_status;
            });
            $doctors = $filteredDoctors;
        } else {
            $doctors = $query->get();
        }

        // Calculate KYC status and average rating for each doctor
        $kycController = new DoctorKycController();
        $doctorsWithKyc = $doctors->map(function($doctor) use ($kycController) {
            $doctor->kyc_status = $kycController->getKycStatus($doctor);
            $doctor->average_rating = \App\Models\DoctorReview::getAverageRating($doctor->id);
            $doctor->review_count = \App\Models\DoctorReview::getReviewCount($doctor->id);
            return $doctor;
        });

        // Paginate manually
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $items = $doctorsWithKyc->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $doctorsWithKyc->count();
        
        $doctors = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Show doctor details with KYC information
     */
    public function show(User $doctor, Request $request)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $kycController = new DoctorKycController();
        $kycStatus = $kycController->getKycStatus($doctor);
        $kycDocuments = DoctorKyc::where('doctor_id', $doctor->id)
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

        // Get availability slots
        $availabilities = \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)
            ->whereNull('specific_date')
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $specificAvailabilities = \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)
            ->whereNotNull('specific_date')
            ->where('specific_date', '>=', now()->toDateString())
            ->orderBy('specific_date')
            ->orderBy('start_time')
            ->get();

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

        // Load states and cities for dropdowns
        $states = State::where('is_active', true)->orderBy('name')->get();
        $cities = $doctor->state_id ? City::where('state_id', $doctor->state_id)->where('is_active', true)->orderBy('name')->get() : collect();

        $activeTab = $request->get('tab', 'profile');

        return view('admin.doctors.show', compact('doctor', 'kycStatus', 'kycDocuments', 'patients', 'appointments', 'availabilities', 'specificAvailabilities', 'reviews', 'averageRating', 'reviewCount', 'ratingDistribution', 'activeTab', 'states', 'cities'));
    }

    /**
     * Update doctor status manually
     */
    public function updateStatus(Request $request, User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending_kyc,pending_approval',
        ]);

        $doctor->update($validated);

        return redirect()->route('doctors.show', $doctor)->with('success', 'Doctor status updated successfully.');
    }

    /**
     * Manually trigger KYC status check and update
     */
    public function checkKycStatus(User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        DoctorKycController::checkAndUpdateDoctorStatus($doctor);

        return redirect()->route('doctors.show', $doctor)->with('success', 'KYC status checked and doctor status updated.');
    }
}
