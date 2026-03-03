@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Appointment Details</h2>
            <p class="text-gray-600 mt-1">Appointment #{{ $appointment->appointment_number }}</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            ← Back to Appointments
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Appointment Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $appointment->appointment_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $appointment->appointment_date->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $appointment->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $appointment->status === 'no_show' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Consultation Fee</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $appointment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($appointment->payment_status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ Auth::user()->hasRole('Doctor') ? 'Patient' : 'Doctor' }} Information
                </h3>
                <dl class="space-y-3">
                    @if(Auth::user()->hasRole('Doctor'))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Patient Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Patient ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->patient_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->phone ?? '-' }}</dd>
                        </div>
                    @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Doctor Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">Dr. {{ $appointment->doctor->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Specialization</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->doctor->specialization ?? 'General Practitioner' }}</dd>
                        </div>
                        @if($appointment->doctor->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->doctor->phone }}</dd>
                        </div>
                        @endif
                    @endif
                </dl>
            </div>
        </div>

        @if($appointment->reason)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Reason for Visit</h3>
            <p class="text-sm text-gray-700">{{ $appointment->reason }}</p>
        </div>
        @endif

        @if($appointment->notes && Auth::user()->hasRole('Doctor'))
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Doctor's Notes</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $appointment->notes }}</p>
        </div>
        @endif

        @if($appointment->prescription)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Prescription</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $appointment->prescription }}</p>
        </div>
        @endif

        @if($appointment->cancellation_reason)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancellation Reason</h3>
            <p class="text-sm text-red-700">{{ $appointment->cancellation_reason }}</p>
        </div>
        @endif

        @if($appointment->zoom_join_url)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Video Consultation</h3>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-700 mb-3">Join the video consultation using the link below:</p>
                <div class="space-y-2">
                    <a href="{{ $appointment->zoom_join_url }}" target="_blank" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Join Zoom Meeting
                    </a>
                    @if($appointment->zoom_meeting_password)
                    <p class="text-xs text-gray-600 mt-2">
                        <strong>Meeting Password:</strong> {{ $appointment->zoom_meeting_password }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(Auth::user()->hasRole('Doctor') && in_array($appointment->status, ['pending', 'confirmed']))
        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('appointments.edit', $appointment) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Update Appointment
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

