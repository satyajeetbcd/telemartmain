<?php

namespace App\Http\Middleware;

use App\Models\Patient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePatientToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $patient = Patient::where('api_token', hash('sha256', $token))->first();

        if (!$patient) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($patient->status === 'inactive') {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        $request->merge(['patient' => $patient]);
        $request->setUserResolver(function () use ($patient) {
            return $patient;
        });

        return $next($request);
    }
}
