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
</div>
@endsection


