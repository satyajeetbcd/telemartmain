@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Patient Details</h2>
            <p class="text-gray-600 mt-1">Patient ID: {{ $patient->patient_id }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('patients.edit', $patient) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Edit Patient
            </a>
            <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $patient->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Patient ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->patient_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->gender ? ucfirst($patient->gender) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Blood Group</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->blood_group ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $patient->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $patient->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $patient->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($patient->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Address Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">City</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->city ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">State</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->state ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->postal_code ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->country ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Medical Information -->
        @if($patient->medical_history || $patient->allergies || $patient->current_medications)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @if($patient->medical_history)
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-2">Medical History</dt>
                    <dd class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patient->medical_history }}</dd>
                </div>
                @endif
                @if($patient->allergies)
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-2">Allergies</dt>
                    <dd class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patient->allergies }}</dd>
                </div>
                @endif
                @if($patient->current_medications)
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-2">Current Medications</dt>
                    <dd class="text-sm text-gray-900 whitespace-pre-wrap">{{ $patient->current_medications }}</dd>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Emergency Contact -->
        @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
            <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_phone ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Relation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_relation ?? '-' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <!-- Insurance Information -->
        @if($patient->insurance_provider || $patient->insurance_policy_number)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Insurance Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Insurance Provider</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->insurance_provider ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Policy Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->insurance_policy_number ?? '-' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <!-- Notes -->
        @if($patient->notes)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $patient->notes }}</p>
        </div>
        @endif

        <!-- User Account Link -->
        @if($patient->user)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">User Account</h3>
            <p class="text-sm text-gray-700">
                Linked to user account: <a href="{{ route('users.show', $patient->user) }}" class="text-green-600 hover:text-green-700">{{ $patient->user->email }}</a>
            </p>
        </div>
        @endif
    </div>

    <!-- Appointments -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Appointments ({{ $patient->appointments->count() }})
            </h3>
            <a href="{{ route('patients.edit', $patient) }}#assign-appointment" class="text-sm text-green-600 hover:text-green-700">Assign appointment (Edit patient)</a>
        </div>
        @if($patient->appointments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($patient->appointments as $apt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $apt->appointment_number }}</td>
                        <td class="px-3 py-2">Dr. {{ $apt->doctor->name }}</td>
                        <td class="px-3 py-2">{{ $apt->appointment_date->format('M d, Y') }} {{ date('h:i A', strtotime($apt->appointment_time)) }}</td>
                        <td class="px-3 py-2">
                            @if($apt->payment_status === 'paid' && in_array($apt->status, ['confirmed', 'completed']))
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Success</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ $apt->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $apt->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $apt->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $apt->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if($apt->payment_status === 'paid')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('appointments.show', $apt) }}" class="text-green-600 hover:text-green-800 mr-2">View</a>
                            @if($apt->payment_status === 'pending' && (Auth::user()->hasRole('Doctor') && $apt->doctor_id === Auth::id() || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Receptionist')))
                            <form action="{{ route('appointments.mark-paid', $apt) }}" method="POST" class="inline" onsubmit="return confirm('Mark payment as received? Appointment will be confirmed.');">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800">Mark paid</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500 py-4">No appointments yet. <a href="{{ route('patients.edit', $patient) }}" class="text-green-600 hover:text-green-700">Edit patient</a> to assign one.</p>
        @endif
    </div>

    <!-- Medical Records -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Medical Records ({{ $patient->medicalRecords->count() }})
            </h3>
            @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
            <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Record
            </a>
            @endif
        </div>

        @if($patient->medicalRecords->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patient->medicalRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $record->record_number }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $typeColors = [
                                    'consultation' => 'bg-blue-100 text-blue-800',
                                    'lab_report' => 'bg-purple-100 text-purple-800',
                                    'prescription' => 'bg-green-100 text-green-800',
                                    'diagnosis' => 'bg-orange-100 text-orange-800',
                                    'discharge_summary' => 'bg-gray-100 text-gray-800',
                                    'imaging' => 'bg-indigo-100 text-indigo-800',
                                    'vaccination' => 'bg-teal-100 text-teal-800',
                                    'surgical' => 'bg-red-100 text-red-800',
                                    'follow_up' => 'bg-yellow-100 text-yellow-800',
                                    'other' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$record->record_type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ \App\Models\MedicalRecord::getRecordTypes()[$record->record_type] ?? ucfirst($record->record_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ $record->title }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Dr. {{ $record->doctor->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $record->record_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('medical-records.show', $record) }}" class="text-green-600 hover:text-green-900 mr-2">View</a>
                            @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
                                @if(!Auth::user()->hasRole('Doctor') || $record->doctor_id === Auth::id())
                                <a href="{{ route('medical-records.edit', $record) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No medical records yet.</p>
            @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
            <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Record
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Prescriptions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Prescriptions ({{ $patient->prescriptions->count() }})
            </h3>
            @if(Auth::user()->hasRole('Doctor'))
            <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Prescription
            </a>
            @endif
        </div>

        @if($patient->prescriptions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rx #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicines</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patient->prescriptions as $prescription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $prescription->prescription_number }}</div>
                            @if($prescription->diagnosis)
                            <div class="text-xs text-gray-500 truncate max-w-[120px]">{{ $prescription->diagnosis }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Dr. {{ $prescription->doctor->name }}</td>
                        <td class="px-4 py-3">
                            @foreach($prescription->items->take(2) as $item)
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">{{ $item->medicine_name }}</span>
                            @endforeach
                            @if($prescription->items->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $prescription->items->count() - 2 }} more</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $prescription->prescription_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($prescription->valid_until)
                                <span class="{{ $prescription->isExpired() ? 'text-red-600' : 'text-gray-900' }}">{{ $prescription->valid_until->format('M d, Y') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'expired' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($prescription->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('prescriptions.show', $prescription) }}" class="text-green-600 hover:text-green-900 mr-2">View</a>
                            @if(Auth::user()->hasRole('Doctor') && $prescription->doctor_id === Auth::id())
                                <a href="{{ route('prescriptions.edit', $prescription) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No prescriptions yet.</p>
            @if(Auth::user()->hasRole('Doctor'))
            <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Prescription
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
