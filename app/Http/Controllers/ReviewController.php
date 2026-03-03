<?php

namespace App\Http\Controllers;

use App\Models\DoctorReview;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display reviews for a specific doctor (only approved)
     */
    public function index(User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $reviews = DoctorReview::where('doctor_id', $doctor->id)
            ->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->with(['patient', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = DoctorReview::getAverageRating($doctor->id);
        $reviewCount = DoctorReview::getReviewCount($doctor->id);
        $ratingDistribution = DoctorReview::getRatingDistribution($doctor->id);

        return view('reviews.index', compact('doctor', 'reviews', 'averageRating', 'reviewCount', 'ratingDistribution'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create(Request $request, User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $patient = null;
        $appointment = null;

        // If user is authenticated and is a patient
        if (Auth::check() && Auth::user()->hasRole('Patient')) {
            $patient = Patient::where('user_id', Auth::id())->first();
            
            // Get appointment if provided
            if ($request->has('appointment_id')) {
                $appointment = Appointment::where('id', $request->appointment_id)
                    ->where('doctor_id', $doctor->id)
                    ->where('patient_id', $patient->id ?? 0)
                    ->where('status', 'completed')
                    ->first();
            }
        }

            // Get completed appointments for this doctor and patient
            $completedAppointments = [];
            if ($patient) {
                $reviewedAppointmentIds = DoctorReview::where('doctor_id', $doctor->id)
                    ->where('patient_id', $patient->id)
                    ->whereNotNull('appointment_id')
                    ->pluck('appointment_id')
                    ->toArray();

                $completedAppointments = Appointment::where('doctor_id', $doctor->id)
                    ->where('patient_id', $patient->id)
                    ->where('status', 'completed')
                    ->whereNotIn('id', $reviewedAppointmentIds)
                    ->orderBy('appointment_date', 'desc')
                    ->get();
            }

        return view('reviews.create', compact('doctor', 'patient', 'appointment', 'completedAppointments'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, User $doctor)
    {
        if (!$doctor->hasRole('Doctor')) {
            abort(404);
        }

        $patient = null;
        if (Auth::check() && Auth::user()->hasRole('Patient')) {
            $patient = Patient::where('user_id', Auth::id())->first();
        }

        if (!$patient) {
            return back()->withErrors(['error' => 'You must be logged in as a patient to leave a review.'])->withInput();
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        // Check if appointment belongs to this patient and doctor
        if (!empty($validated['appointment_id'])) {
            $appointment = Appointment::where('id', $validated['appointment_id'])
                ->where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->where('status', 'completed')
                ->first();

            if (!$appointment) {
                return back()->withErrors(['appointment_id' => 'Invalid appointment.'])->withInput();
            }

            // Check if review already exists for this appointment
            $existingReview = DoctorReview::where('doctor_id', $doctor->id)
                ->where('appointment_id', $validated['appointment_id'])
                ->first();

            if ($existingReview) {
                return back()->withErrors(['appointment_id' => 'You have already reviewed this appointment.'])->withInput();
            }
        }

        $validated['doctor_id'] = $doctor->id;
        $validated['patient_id'] = $patient->id;
        $validated['approval_status'] = 'pending'; // New reviews need approval

        DoctorReview::create($validated);

        return redirect()->route('reviews.index', $doctor)->with('success', 'Review submitted successfully. It will be visible after admin approval.');
    }

    /**
     * Show a specific review
     */
    public function show(DoctorReview $review)
    {
        $review->load(['doctor', 'patient', 'appointment']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Store a doctor's reply to a review
     */
    public function reply(Request $request, DoctorReview $review)
    {
        $doctor = Auth::user();

        // Ensure the logged-in user is the doctor for this review
        if ($review->doctor_id !== $doctor->id || !$doctor->hasRole('Doctor')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'doctor_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'doctor_reply' => $validated['doctor_reply'],
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    /**
     * Update a doctor's reply
     */
    public function updateReply(Request $request, DoctorReview $review)
    {
        $doctor = Auth::user();

        // Ensure the logged-in user is the doctor for this review
        if ($review->doctor_id !== $doctor->id || !$doctor->hasRole('Doctor')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'doctor_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'doctor_reply' => $validated['doctor_reply'],
        ]);

        return back()->with('success', 'Reply updated successfully.');
    }

    /**
     * Delete a doctor's reply
     */
    public function deleteReply(DoctorReview $review)
    {
        $doctor = Auth::user();

        // Ensure the logged-in user is the doctor for this review
        if ($review->doctor_id !== $doctor->id || !$doctor->hasRole('Doctor')) {
            abort(403, 'Unauthorized action.');
        }

        $review->update([
            'doctor_reply' => null,
            'replied_at' => null,
        ]);

        return back()->with('success', 'Reply deleted successfully.');
    }

    /**
     * Toggle review visibility (admin only)
     */
    public function toggleVisibility(DoctorReview $review)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        $message = $review->is_visible ? 'Review is now visible.' : 'Review is now hidden.';
        return back()->with('success', $message);
    }

    /**
     * Admin review management index
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $query = DoctorReview::with(['doctor', 'patient', 'appointment', 'approver']);

        // Filter by approval status
        if ($request->has('status') && $request->status) {
            $query->where('approval_status', $request->status);
        } else {
            $query->where('approval_status', 'pending'); // Default to pending
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('doctor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);
        $pendingCount = DoctorReview::where('approval_status', 'pending')->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    /**
     * Approve a review
     */
    public function approve(DoctorReview $review)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $review->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Review approved successfully.');
    }

    /**
     * Reject a review
     */
    public function reject(Request $request, DoctorReview $review)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $review->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return back()->with('success', 'Review rejected successfully.');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'review_ids' => 'required|string',
        ]);

        $reviewIds = explode(',', $request->review_ids);
        $reviewIds = array_filter(array_map('intval', $reviewIds));

        if (empty($reviewIds)) {
            return back()->withErrors(['error' => 'No reviews selected.']);
        }

        $count = DoctorReview::whereIn('id', $reviewIds)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', "{$count} review(s) approved successfully.");
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request)
    {
        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'review_ids' => 'required|string',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $reviewIds = explode(',', $validated['review_ids']);
        $reviewIds = array_filter(array_map('intval', $reviewIds));

        if (empty($reviewIds)) {
            return back()->withErrors(['error' => 'No reviews selected.']);
        }

        $count = DoctorReview::whereIn('id', $reviewIds)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'] ?? null,
            ]);

        return back()->with('success', "{$count} review(s) rejected successfully.");
    }
}
