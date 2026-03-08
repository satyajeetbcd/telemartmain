@extends('layouts.app')

@section('title', 'Invoice — ' . $appointment->appointment_number)

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #invoice-printable, #invoice-printable * { visibility: visible; }
        #invoice-printable { position: absolute; left: 0; top: 0; width: 100%; padding: 32px; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    {{-- Action bar (hidden on print) --}}
    <div class="flex justify-between items-center no-print">
        <a href="{{ route('appointments.show', $appointment) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            ← Back to Appointment
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print / Save as PDF
        </button>
    </div>

    {{-- Invoice card --}}
    <div id="invoice-printable" class="bg-white rounded-lg shadow p-8">

        {{-- Header --}}
        <div class="flex justify-between items-start pb-6 border-b border-gray-200 mb-6">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="Tele Health Mart" class="h-14 w-auto object-contain mb-1">
                <p class="text-sm text-gray-500">Telemedicine Platform</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-extrabold text-gray-800 tracking-wider">INVOICE</h2>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium">Invoice #:</span>
                    INV-{{ $appointment->appointment_number }}
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Date:</span>
                    {{ now()->format('F d, Y') }}
                </p>
                <p class="mt-2">
                    <span class="px-3 py-1 text-xs font-bold rounded-full
                        {{ $appointment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $appointment->payment_status === 'paid' ? '✓ PAID' : 'PAYMENT PENDING' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Bill To / Service By --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Bill To</p>
                <p class="font-semibold text-gray-900 text-base">{{ $appointment->patient->full_name }}</p>
                <p class="text-sm text-gray-600 mt-0.5">Patient ID: {{ $appointment->patient->patient_id }}</p>
                @if($appointment->patient->email)
                    <p class="text-sm text-gray-600">{{ $appointment->patient->email }}</p>
                @endif
                @if($appointment->patient->phone)
                    <p class="text-sm text-gray-600">{{ $appointment->patient->phone }}</p>
                @endif
                @if($appointment->patient->address)
                    <p class="text-sm text-gray-600 mt-1">{{ $appointment->patient->address }}</p>
                @endif
                @php
                    $location = array_filter([
                        $appointment->patient->city,
                        $appointment->patient->state,
                        $appointment->patient->postal_code,
                    ]);
                @endphp
                @if($location)
                    <p class="text-sm text-gray-600">{{ implode(', ', $location) }}</p>
                @endif
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Service By</p>
                <p class="font-semibold text-gray-900 text-base">Dr. {{ $appointment->doctor->name }}</p>
                @if($appointment->doctor->specialization)
                    <p class="text-sm text-gray-600">{{ $appointment->doctor->specialization }}</p>
                @endif
                @if($appointment->doctor->qualifications)
                    <p class="text-sm text-gray-600">{{ $appointment->doctor->qualifications }}</p>
                @endif
                @if($appointment->doctor->license_number)
                    <p class="text-sm text-gray-600">License: {{ $appointment->doctor->license_number }}</p>
                @endif
                @if($appointment->doctor->phone)
                    <p class="text-sm text-gray-600">{{ $appointment->doctor->phone }}</p>
                @endif
            </div>
        </div>

        {{-- Appointment summary --}}
        <div class="mb-8">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Appointment Details</p>
            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Appointment #:</span>
                    <span class="ml-1 font-medium text-gray-900">{{ $appointment->appointment_number }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Date:</span>
                    <span class="ml-1 font-medium text-gray-900">{{ $appointment->appointment_date->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Time:</span>
                    <span class="ml-1 font-medium text-gray-900">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Status:</span>
                    <span class="ml-1 font-medium text-gray-900">{{ ucfirst($appointment->status) }}</span>
                </div>
                @if($appointment->reason)
                    <div class="col-span-2">
                        <span class="text-gray-500">Reason for visit:</span>
                        <span class="ml-1 font-medium text-gray-900">{{ $appointment->reason }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Services table --}}
        <div class="mb-8">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Services</p>
            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold w-8">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Description</th>
                        <th class="px-4 py-3 text-left font-semibold">Date of Service</th>
                        <th class="px-4 py-3 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr class="border-t border-gray-200">
                        <td class="px-4 py-3 text-gray-700">1</td>
                        <td class="px-4 py-3 text-gray-700">
                            <p class="font-medium text-gray-900">Online Medical Consultation</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Dr. {{ $appointment->doctor->name }}
                                — {{ $appointment->doctor->specialization ?? 'General Practitioner' }}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $appointment->appointment_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 text-right font-semibold">
                            ₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Total block --}}
        <div class="flex justify-end mb-8">
            <div class="w-64 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium text-gray-900">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-3 text-base font-bold border-b-2 border-gray-800">
                    <span class="text-gray-900">Total</span>
                    <span class="text-green-700">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Payment Status</span>
                    <span class="font-semibold {{ $appointment->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $appointment->payment_status === 'paid' ? 'Paid' : 'Pending' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 pt-6 text-center space-y-1">
            <p class="text-sm text-gray-500">Thank you for choosing Tele Health Mart for your healthcare needs.</p>
            <p class="text-xs text-gray-400">This is a computer-generated invoice. No signature required.</p>
            <p class="text-xs text-gray-400">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>

    </div>
</div>
@endsection
