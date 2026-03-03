<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on role
        if ($user->hasRole('Doctor')) {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->hasRole('Patient') || $user->hasRole('Receptionist')) {
            return redirect()->route('patient.dashboard');
        }
        
        // Admin dashboard
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_roles' => \Spatie\Permission\Models\Role::count(),
            'total_invitations' => \App\Models\Invitation::whereNull('accepted_at')->count(),
            'pending_invitations' => \App\Models\Invitation::whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->count(),
        ];

        return view('dashboard.index', compact('user', 'stats'));
    }
}
