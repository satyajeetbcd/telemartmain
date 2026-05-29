<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;

class ApiStatsController extends Controller
{
    public function index()
    {
        $totalConsultations = Appointment::where('status', 'completed')->count();
        $totalDoctors = User::role('Doctor')->where('status', 'active')->count();
        $totalPatients = Patient::where('status', 'active')->count();

        return response()->json([
            'consultations' => $totalConsultations,
            'doctors' => $totalDoctors,
            'patients' => $totalPatients,
            'states' => 28,
        ]);
    }

    /**
     * Public list of verified doctors for the marketing site.
     * "Verified" = active Doctor role with at least one approved KYC document.
     */
    public function doctors()
    {
        $doctors = User::role('Doctor')
            ->where('status', 'active')
            ->whereHas('kycDocuments', fn ($q) => $q->where('status', 'approved'))
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization ?: 'General Medicine',
                    'qualifications' => $doctor->qualifications,
                    'experience_years' => $doctor->experience_years,
                    'consultation_fee' => $doctor->consultation_fee
                        ? number_format($doctor->consultation_fee, 2)
                        : null,
                    'profile_image' => $doctor->profile_image, // raw storage path; frontend builds the URL
                    'verified' => true,
                ];
            });

        return response()->json(['doctors' => $doctors]);
    }
}
