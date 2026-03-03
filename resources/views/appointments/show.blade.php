@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if($appointment->payment_status === 'paid' && in_array($appointment->status, ['confirmed', 'completed']))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
        <svg class="w-8 h-8 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="font-semibold text-green-800">Appointment success</p>
            <p class="text-sm text-green-700">Payment received. This appointment is confirmed and active for both doctor and patient.</p>
        </div>
    </div>
    @endif

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
                    @if($appointment->payment_status === 'pending' && (Auth::user()->hasRole('Doctor') && $appointment->doctor_id === Auth::id() || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Receptionist')))
                    <div class="pt-2">
                        <form action="{{ route('appointments.mark-paid', $appointment) }}" method="POST" onsubmit="return confirm('Mark payment as received? The appointment will be confirmed and shown as success to doctor and patient.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                Mark payment received
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-1">Once marked, appointment will be confirmed and visible as success.</p>
                    </div>
                    @endif
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

    @if($canManagePatient && $appointment->payment_status === 'paid')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Patient care — Add & view history
        </h3>
        <p class="text-sm text-gray-600 mb-4">Payment received. You can add prescription, add medical record, and view full patient history.</p>

        <div class="flex flex-wrap gap-3 mb-6">
            @if(Auth::user()->hasRole('Doctor'))
            <a href="{{ route('prescriptions.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Add prescription
            </a>
            @endif
            @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
            <a href="{{ route('medical-records.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Add medical record
            </a>
            @endif
            <a href="{{ route('patients.show', $appointment->patient) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                View full patient history
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Recent prescriptions</h4>
                @if($patientPrescriptions->count() > 0)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rx #</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($patientPrescriptions as $rx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $rx->prescription_number }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $rx->prescription_date->format('M d, Y') }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('prescriptions.show', $rx) }}" class="text-green-600 hover:text-green-800">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('prescriptions.index', ['patient_id' => $appointment->patient_id]) }}" class="mt-2 inline-block text-sm text-green-600 hover:text-green-700">View all prescriptions →</a>
                @else
                <p class="text-sm text-gray-500 py-3">No prescriptions yet. Use "Add prescription" above.</p>
                @endif
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Recent medical records</h4>
                @if($patientMedicalRecords->count() > 0)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Record #</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($patientMedicalRecords as $rec)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $rec->record_number }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ \App\Models\MedicalRecord::getRecordTypes()[$rec->record_type] ?? $rec->record_type }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $rec->record_date->format('M d, Y') }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('medical-records.show', $rec) }}" class="text-green-600 hover:text-green-800">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('medical-records.index', ['patient_id' => $appointment->patient_id]) }}" class="mt-2 inline-block text-sm text-green-600 hover:text-green-700">View all medical records →</a>
                @else
                <p class="text-sm text-gray-500 py-3">No medical records yet. Use "Add medical record" above.</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

