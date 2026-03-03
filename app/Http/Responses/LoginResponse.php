<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = Auth::user();
        
        // Redirect based on user role
        if ($user->hasRole('Doctor')) {
            return redirect()->intended(route('doctor.dashboard'));
        } elseif ($user->hasRole('Patient') || $user->hasRole('Receptionist')) {
            // Patients and Receptionists see patient dashboard
            return redirect()->intended(route('patient.dashboard'));
        } else {
            // Admin, Super Admin, and other roles see admin dashboard
            return redirect()->intended(route('dashboard'));
        }
    }
}

