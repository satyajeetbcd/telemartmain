@extends('layouts.app')

@section('title', 'Prescription Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Prescription Details</h2>
            <p class="text-gray-600 mt-1">Prescription #{{ $prescription->prescription_number }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('prescriptions.pdf', $prescription) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            @if(Auth::user()->hasRole('Doctor') && $prescription->doctor_id === Auth::id())
            <a href="{{ route('prescriptions.edit', $prescription) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Edit</a>
            @endif
            <a href="{{ route('prescriptions.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Printable Prescription -->
    <div class="bg-white rounded-lg shadow p-8 print:shadow-none print:p-0" id="printable">
        <!-- Header -->
        <div class="border-b-2 border-green-600 pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-green-700">Tele Health Mart</h3>
                    <p class="text-sm text-gray-600 mt-1">Telemedicine Consultation Platform</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">Rx #{{ $prescription->prescription_number }}</p>
                    <p class="text-sm text-gray-600">Date: {{ $prescription->prescription_date->format('F d, Y') }}</p>
                    @if($prescription->valid_until)
                    <p class="text-sm text-gray-600">Valid Until: {{ $prescription->valid_until->format('F d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Doctor & Patient Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Doctor</h4>
                <p class="text-sm font-medium text-gray-900">Dr. {{ $prescription->doctor->name }}</p>
                <p class="text-sm text-gray-600">{{ $prescription->doctor->specialization ?? 'General Practitioner' }}</p>
                @if($prescription->doctor->license_number)
                <p class="text-xs text-gray-500 mt-1">Reg. No: {{ $prescription->doctor->license_number }}</p>
                @endif
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Patient</h4>
                <p class="text-sm font-medium text-gray-900">{{ $prescription->patient->full_name }}</p>
                <p class="text-sm text-gray-600">ID: {{ $prescription->patient->patient_id }}</p>
                @if($prescription->patient->date_of_birth)
                <p class="text-sm text-gray-600">DOB: {{ $prescription->patient->date_of_birth->format('M d, Y') }}
                    @if($prescription->patient->gender)
                     | {{ ucfirst($prescription->patient->gender) }}
                    @endif
                </p>
                @endif
                @if($prescription->patient->blood_group)
                <p class="text-sm text-gray-600">Blood Group: {{ $prescription->patient->blood_group }}</p>
                @endif
            </div>
        </div>

        @if($prescription->appointment)
        <div class="mb-4 text-sm text-gray-600">
            <strong>Appointment:</strong>
            <a href="{{ route('appointments.show', $prescription->appointment) }}" class="text-green-600 hover:text-green-700 print:text-gray-900">
                {{ $prescription->appointment->appointment_number }}
            </a>
            ({{ $prescription->appointment->appointment_date->format('M d, Y') }})
        </div>
        @endif

        <!-- Diagnosis -->
        @if($prescription->diagnosis)
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Diagnosis</h4>
            <p class="text-sm text-gray-800 bg-yellow-50 rounded-lg p-3 border-l-4 border-yellow-400">{{ $prescription->diagnosis }}</p>
        </div>
        @endif

        <!-- Rx Symbol & Medicines Table -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <span class="text-3xl font-bold text-green-700 mr-3">&#8478;</span>
                <h4 class="text-lg font-semibold text-gray-900">Medicines</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Medicine</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Dosage</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Route</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Frequency</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($prescription->items as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->medicine_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->dosage ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">
                                    {{ \App\Models\Prescription::getRouteOptions()[$item->route] ?? ucfirst($item->route) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->frequency ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->duration ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->quantity ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 italic">{{ $item->instructions ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        @if($prescription->notes)
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Notes</h4>
            <p class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $prescription->notes }}</p>
        </div>
        @endif

        <!-- Status & Footer -->
        <div class="border-t-2 border-gray-200 pt-4 mt-6">
            <div class="flex justify-between items-center">
                <div>
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($prescription->status) }}
                    </span>
                    @if($prescription->isExpired() && $prescription->status === 'active')
                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                    @endif
                </div>
                <div class="text-right text-xs text-gray-500">
                    <p>Created by {{ $prescription->creator->name }} on {{ $prescription->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Doctor Signature Area (print only) -->
        <div class="hidden print:block mt-12">
            <div class="flex justify-end">
                <div class="text-center">
                    <div class="border-t border-gray-400 w-48 mb-2"></div>
                    <p class="text-sm font-medium">Dr. {{ $prescription->doctor->name }}</p>
                    <p class="text-xs text-gray-600">{{ $prescription->doctor->specialization ?? 'General Practitioner' }}</p>
                    @if($prescription->doctor->license_number)
                    <p class="text-xs text-gray-600">Reg. No: {{ $prescription->doctor->license_number }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete -->
    @if(Auth::user()->hasRole('Doctor') && $prescription->doctor_id === Auth::id())
    <div class="bg-white rounded-lg shadow p-6 print:hidden">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-red-600">Delete this prescription</h3>
                <p class="text-xs text-gray-500 mt-1">This action cannot be undone.</p>
            </div>
            <form method="POST" action="{{ route('prescriptions.destroy', $prescription) }}"
                  onsubmit="return confirm('Are you sure you want to delete this prescription?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                    Delete Prescription
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    aside, header, footer, .print\\:hidden { display: none !important; }
    .flex.h-screen { display: block !important; }
    main { padding: 0 !important; overflow: visible !important; }
    .max-w-5xl { max-width: 100% !important; }
    body { background: white !important; }
}
</style>
@endsection
