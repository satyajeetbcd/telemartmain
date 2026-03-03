@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome, Dr. {{ $doctor->name }}</h1>
                <p class="text-green-100">{{ $doctor->specialization ?? 'General Practitioner' }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Patients</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_patients'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Today's Appointments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['today_appointments'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Reports</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_reports'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Consultations</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_consultations'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- KYC Status Alert -->
    @if(isset($kycStatus) && $kycStatus['overall_status'] !== 'approved')
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">KYC Verification Required</h3>
                    <p class="text-sm text-yellow-700 mt-1">Please complete your KYC verification by uploading required documents.</p>
                </div>
            </div>
            <a href="{{ route('doctor.kyc.index') }}" class="ml-4 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium">
                Upload Documents
            </a>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('doctor.profile') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-500 transition">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">My Profile</h3>
                    <p class="text-sm text-gray-500">Update your professional information</p>
                </div>
            </a>
            <a href="{{ route('doctor.kyc.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-500 transition">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">KYC Documents</h3>
                    <p class="text-sm text-gray-500">Upload and manage verification documents</p>
                </div>
            </a>
            <a href="{{ route('appointments.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Appointments</h3>
                    <p class="text-sm text-gray-500">View and manage appointments</p>
                </div>
            </a>
            <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-500 transition">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Medical Records</h3>
                    <p class="text-sm text-gray-500">Access patient records</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Patients -->
    @if($recentPatients->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Patients</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPatients as $patient)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $patient->patient_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $patient->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->email ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $patient->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($patient->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

