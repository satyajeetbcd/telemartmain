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
}
