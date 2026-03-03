@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Medical Record Details</h2>
            <p class="text-gray-600 mt-1">Record #{{ $medicalRecord->record_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if((Auth::user()->hasRole('Doctor') && $medicalRecord->doctor_id === Auth::id()) || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
            <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Edit Record</a>
            @endif
            <a href="{{ route('medical-records.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                &larr; Back to Records
            </a>
        </div>
    </div>

    <!-- Record Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
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
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $typeColors[$medicalRecord->record_type] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ \App\Models\MedicalRecord::getRecordTypes()[$medicalRecord->record_type] ?? ucfirst($medicalRecord->record_type) }}
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $medicalRecord->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($medicalRecord->status) }}
                </span>
            </div>
            <div class="text-sm text-gray-500">
                {{ $medicalRecord->record_date->format('F d, Y') }}
            </div>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $medicalRecord->title }}</h3>
        @if($medicalRecord->description)
            <p class="text-gray-600">{{ $medicalRecord->description }}</p>
        @endif
    </div>

    <!-- Patient & Doctor Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Patient Information
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->patient->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Patient ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->patient->patient_id }}</dd>
                </div>
                @if($medicalRecord->patient->date_of_birth)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->patient->date_of_birth->format('M d, Y') }}</dd>
                </div>
                @endif
                @if($medicalRecord->patient->blood_group)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Blood Group</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->patient->blood_group }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Doctor Information
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Doctor Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">Dr. {{ $medicalRecord->doctor->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Specialization</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->doctor->specialization ?? 'General Practitioner' }}</dd>
                </div>
                @if($medicalRecord->appointment)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Related Appointment</dt>
                    <dd class="mt-1 text-sm">
                        <a href="{{ route('appointments.show', $medicalRecord->appointment) }}" class="text-green-600 hover:text-green-700">
                            {{ $medicalRecord->appointment->appointment_number }}
                        </a>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medicalRecord->creator->name }} on {{ $medicalRecord->created_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Vitals -->
    @if($medicalRecord->vitals && count($medicalRecord->vitals) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            Vitals
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @if(!empty($medicalRecord->vitals['blood_pressure']))
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-red-600 uppercase">Blood Pressure</p>
                <p class="text-lg font-bold text-red-800 mt-1">{{ $medicalRecord->vitals['blood_pressure'] }}</p>
                <p class="text-xs text-red-500">mmHg</p>
            </div>
            @endif
            @if(!empty($medicalRecord->vitals['heart_rate']))
            <div class="bg-pink-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-pink-600 uppercase">Heart Rate</p>
                <p class="text-lg font-bold text-pink-800 mt-1">{{ $medicalRecord->vitals['heart_rate'] }}</p>
                <p class="text-xs text-pink-500">bpm</p>
            </div>
            @endif
            @if(!empty($medicalRecord->vitals['temperature']))
            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-orange-600 uppercase">Temperature</p>
                <p class="text-lg font-bold text-orange-800 mt-1">{{ $medicalRecord->vitals['temperature'] }}</p>
                <p class="text-xs text-orange-500">&deg;F</p>
            </div>
            @endif
            @if(!empty($medicalRecord->vitals['weight']))
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-blue-600 uppercase">Weight</p>
                <p class="text-lg font-bold text-blue-800 mt-1">{{ $medicalRecord->vitals['weight'] }}</p>
                <p class="text-xs text-blue-500">kg</p>
            </div>
            @endif
            @if(!empty($medicalRecord->vitals['height']))
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-green-600 uppercase">Height</p>
                <p class="text-lg font-bold text-green-800 mt-1">{{ $medicalRecord->vitals['height'] }}</p>
                <p class="text-xs text-green-500">cm</p>
            </div>
            @endif
            @if(!empty($medicalRecord->vitals['oxygen_saturation']))
            <div class="bg-indigo-50 rounded-lg p-4 text-center">
                <p class="text-xs font-medium text-indigo-600 uppercase">SpO2</p>
                <p class="text-lg font-bold text-indigo-800 mt-1">{{ $medicalRecord->vitals['oxygen_saturation'] }}</p>
                <p class="text-xs text-indigo-500">%</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Clinical Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Clinical Details
        </h3>
        <div class="space-y-6">
            @if($medicalRecord->symptoms)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Symptoms</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-4">{{ $medicalRecord->symptoms }}</p>
            </div>
            @endif

            @if($medicalRecord->diagnosis)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Diagnosis</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-400">{{ $medicalRecord->diagnosis }}</p>
            </div>
            @endif

            @if($medicalRecord->prescription)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Prescription</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap bg-green-50 rounded-lg p-4 border-l-4 border-green-400">{{ $medicalRecord->prescription }}</p>
            </div>
            @endif

            @if($medicalRecord->treatment_plan)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Treatment Plan</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">{{ $medicalRecord->treatment_plan }}</p>
            </div>
            @endif

            @if($medicalRecord->notes)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Doctor's Notes</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-4">{{ $medicalRecord->notes }}</p>
            </div>
            @endif

            @if(!$medicalRecord->symptoms && !$medicalRecord->diagnosis && !$medicalRecord->prescription && !$medicalRecord->treatment_plan && !$medicalRecord->notes)
            <p class="text-sm text-gray-500 italic">No clinical details recorded.</p>
            @endif
        </div>
    </div>

    <!-- Follow-up -->
    @if($medicalRecord->follow_up_date)
    <div class="bg-amber-50 border border-amber-200 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-amber-800 mb-2 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Follow-up Scheduled
        </h3>
        <p class="text-amber-700">Follow-up date: <strong>{{ $medicalRecord->follow_up_date->format('F d, Y') }}</strong></p>
    </div>
    @endif

    <!-- Attachments -->
    @if($medicalRecord->attachments && count($medicalRecord->attachments) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
            Attachments ({{ count($medicalRecord->attachments) }})
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($medicalRecord->attachments as $index => $attachment)
            <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center space-x-3 min-w-0">
                    @php
                        $icon = match(true) {
                            str_contains($attachment['type'] ?? '', 'pdf') => 'text-red-500',
                            str_contains($attachment['type'] ?? '', 'image') => 'text-blue-500',
                            default => 'text-gray-500',
                        };
                    @endphp
                    <svg class="w-8 h-8 {{ $icon }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ number_format(($attachment['size'] ?? 0) / 1024, 1) }} KB</p>
                    </div>
                </div>
                <a href="{{ route('medical-records.download', [$medicalRecord, $index]) }}" 
                    class="text-green-600 hover:text-green-700 flex-shrink-0 ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Delete -->
    @if((Auth::user()->hasRole('Doctor') && $medicalRecord->doctor_id === Auth::id()) || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-red-600">Delete this record</h3>
                <p class="text-xs text-gray-500 mt-1">This action cannot be undone.</p>
            </div>
            <form method="POST" action="{{ route('medical-records.destroy', $medicalRecord) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this medical record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                    Delete Record
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
