@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome, {{ $user->name }}</h1>
                <p class="text-green-100">Patient ID: {{ $patient->patient_id ?? 'N/A' }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Upcoming Appointments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['upcoming_appointments'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Medical Records</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['medical_records'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Prescriptions</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['prescriptions'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Lab Reports</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['lab_reports'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('patient.profile') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-500 transition">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">My Profile</h3>
                    <p class="text-sm text-gray-500">Update your personal information</p>
                </div>
            </a>
            <a href="{{ route('appointments.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Book Appointment</h3>
                    <p class="text-sm text-gray-500">Schedule a consultation</p>
                </div>
            </a>
            <a href="{{ route('appointments.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-500 transition">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">My Appointments</h3>
                    <p class="text-sm text-gray-500">View your appointments</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    @if($patient)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">My Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Full Name</p>
                <p class="text-lg font-medium text-gray-900">{{ $patient->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date of Birth</p>
                <p class="text-lg font-medium text-gray-900">{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p class="text-lg font-medium text-gray-900">{{ $patient->phone ?? 'Not set' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Blood Group</p>
                <p class="text-lg font-medium text-gray-900">{{ $patient->blood_group ?? 'Not set' }}</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('patient.profile') }}" class="text-green-600 hover:text-green-700 font-medium">Update Profile →</a>
        </div>
    </div>
    @endif
</div>
@endsection

