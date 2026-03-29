<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($patient->status === 'inactive') {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        // Generate plain token, store hashed version
        $plainToken = Str::random(64);
        $patient->update(['api_token' => hash('sha256', $plainToken)]);

        return response()->json([
            'token' => $plainToken,
            'patient' => [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'email' => $patient->email,
                'phone' => $patient->phone,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:patients,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $nameParts = explode(' ', $request->name, 2);

        $patient = Patient::create([
            'patient_id' => Patient::generatePatientId(),
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        $plainToken = Str::random(64);
        $patient->update(['api_token' => hash('sha256', $plainToken)]);

        return response()->json([
            'token' => $plainToken,
            'patient' => [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'email' => $patient->email,
                'phone' => $patient->phone,
            ],
        ], 201);
    }

    public function logout(Request $request)
    {
        $patient = $request->patient;
        $patient->update(['api_token' => null]);

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
