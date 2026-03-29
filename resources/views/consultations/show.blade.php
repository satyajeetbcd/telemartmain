@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Consultation Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Consultation Details</h2>
            <p class="text-gray-600 mt-1">{{ $consultation->consultation_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('consultations.edit', $consultation) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Edit</a>
            <a href="{{ route('patients.show', $consultation->patient_id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">Back to Patient</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Header Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500">Patient</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">
                    <a href="{{ route('patients.show', $consultation->patient_id) }}" class="text-green-600 hover:text-green-700">
                        {{ $consultation->patient->full_name }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Doctor</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->doctor ? 'Dr. ' . $consultation->doctor->name : 'Unassigned' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                        {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $consultation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $consultation->status === 'in_review' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $consultation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                    </span>
                    @if($consultation->is_followup)
                        <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Follow-up</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Submitted</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->created_at->format('M d, Y h:i A') }}</dd>
            </div>
        </div>
    </div>

    <!-- Patient Query -->
    @if($consultation->query)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Patient Query</h3>
        <p class="text-sm text-gray-800 bg-blue-50 p-4 rounded-lg leading-relaxed">{{ $consultation->query }}</p>
    </div>
    @endif

    <!-- Chief Complaints -->
    @if($consultation->chief_complaints && count($consultation->chief_complaints) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Chief Complaints</h3>
        <div class="space-y-4">
            @foreach($consultation->chief_complaints as $complaint)
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-700 text-sm mb-2">{{ $complaint['name'] ?? 'Unknown' }}</h4>
                @if(!empty($complaint['sub_answers']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($complaint['sub_answers'] as $key => $answer)
                    <div class="bg-gray-50 rounded px-3 py-2">
                        <span class="text-xs font-medium text-gray-500 capitalize block">{{ str_replace('_', ' ', $key) }}</span>
                        <span class="text-sm text-gray-900">
                            @if(is_array($answer))
                                {{ implode(', ', $answer) }}
                            @else
                                {{ $answer }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 italic">No assessment answers recorded</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- History Section -->
    @php
        $hasHistory = ($consultation->patient_history && count($consultation->patient_history) > 0) ||
                      ($consultation->family_history && count($consultation->family_history) > 0) ||
                      ($consultation->personal_history && count(array_filter((array)$consultation->personal_history, fn($v) => $v !== null)) > 0) ||
                      ($consultation->allergies && count($consultation->allergies) > 0);
    @endphp
    @if($hasHistory)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">History</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($consultation->patient_history && count($consultation->patient_history) > 0)
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Patient History</h4>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($consultation->patient_history as $item)
                        <span class="px-2 py-1 bg-orange-50 text-orange-700 text-xs rounded-full font-medium">{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($consultation->family_history && count($consultation->family_history) > 0)
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Family History</h4>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($consultation->family_history as $item)
                        <span class="px-2 py-1 bg-red-50 text-red-700 text-xs rounded-full font-medium">{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($consultation->personal_history && is_array($consultation->personal_history))
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Personal History</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($consultation->personal_history as $key => $value)
                        @if($value !== null)
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $value ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($key) }}: {{ $value ? 'Yes' : 'No' }}
                        </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($consultation->allergies && count($consultation->allergies) > 0)
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Allergies</h4>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($consultation->allergies as $item)
                        <span class="px-2 py-1 bg-yellow-50 text-yellow-700 text-xs rounded-full font-medium">{{ $item }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Medications -->
    @if($consultation->medications && count($consultation->medications) > 0)
    @php $hasMeds = collect($consultation->medications)->filter(fn($m) => !empty($m['name'] ?? null))->count() > 0; @endphp
    @if($hasMeds)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Medications</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dose</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Frequency</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($consultation->medications as $med)
                        @if(!empty($med['name'] ?? null))
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $med['name'] }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $med['dose'] ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $med['frequency'] ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ ($med['duration_value'] ?? '') . ' ' . ($med['duration_type'] ?? '') }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- Location Info -->
    @if($consultation->location_preference || $consultation->state || $consultation->opd)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Preference</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($consultation->location_preference)
            <div>
                <dt class="text-xs font-medium text-gray-500">Preference</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $consultation->location_preference)) }}</dd>
            </div>
            @endif
            @if($consultation->state)
            <div>
                <dt class="text-xs font-medium text-gray-500">State/UT</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->state }}</dd>
            </div>
            @endif
            @if($consultation->opd)
            <div>
                <dt class="text-xs font-medium text-gray-500">OPD</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->opd }}</dd>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Health Records -->
    @if($consultation->health_records && count($consultation->health_records) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Health Records</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($consultation->health_records as $file)
            <a href="{{ Storage::url($file['path']) }}" target="_blank"
               class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5 text-gray-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $file['name'] ?? 'File' }}</p>
                    @if(isset($file['size']))
                        <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow p-6 border border-red-100">
        <h3 class="text-lg font-semibold text-red-600 mb-3">Danger Zone</h3>
        <form action="{{ route('consultations.destroy', $consultation) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this consultation? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                Delete Consultation
            </button>
        </form>
    </div>
</div>
@endsection
