@extends('layouts.app')

@section('title', 'Video Calls')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-gray-900">Video Calls</h2>
        <p class="text-gray-600 mt-1">Upcoming Zoom consultations</p>
    </div>

    @if($appointments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($appointments as $appointment)
            @php
                $isToday = $appointment->appointment_date->isToday();
            @endphp
            <div class="bg-white rounded-xl shadow-sm border {{ $isToday ? 'border-green-400 ring-2 ring-green-100' : 'border-gray-200' }} p-5 flex flex-col">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="min-w-0">
                        @if(Auth::user()->hasRole('Doctor'))
                            <p class="font-semibold text-gray-900 truncate">{{ $appointment->patient->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $appointment->patient->patient_id }}</p>
                        @else
                            <p class="font-semibold text-gray-900 truncate">Dr. {{ $appointment->doctor->name }}</p>
                            <p class="text-xs text-gray-500">{{ $appointment->doctor->specialization ?? 'General Practitioner' }}</p>
                        @endif
                    </div>
                    @if($isToday)
                        <span class="ml-2 shrink-0 px-2 py-0.5 text-xs font-bold bg-green-100 text-green-700 rounded-full">Today</span>
                    @endif
                </div>

                {{-- Details --}}
                <div class="text-sm text-gray-600 space-y-1.5 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $appointment->appointment_date->format('D, M d Y') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ date('h:i A', strtotime($appointment->appointment_time)) }}
                    </div>
                    @if($appointment->zoom_meeting_id)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        ID: {{ $appointment->zoom_meeting_id }}
                    </div>
                    @endif
                    @if($appointment->zoom_meeting_password)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password: {{ $appointment->zoom_meeting_password }}
                    </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Apt # {{ $appointment->appointment_number }}
                    </div>
                </div>

                {{-- CTA buttons --}}
                <div class="mt-auto space-y-2">
                    @if(Auth::user()->hasRole('Doctor') && $appointment->zoom_start_url)
                        <a href="{{ $appointment->zoom_start_url }}" target="_blank"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Start Video Call (Host)
                        </a>
                    @elseif($appointment->zoom_join_url)
                        <a href="{{ $appointment->zoom_join_url }}" target="_blank"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Join Video Call
                        </a>
                    @endif
                    <a href="{{ route('appointments.show', $appointment) }}"
                        class="w-full block text-center text-xs text-gray-500 hover:text-gray-700 py-1">
                        View Appointment Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow text-center py-20">
            <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-600 font-semibold text-lg">No upcoming video calls</p>
            <p class="text-sm text-gray-400 mt-1">Confirmed appointments with Zoom links will appear here.</p>
            <a href="{{ route('appointments.index') }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                View Appointments
            </a>
        </div>
    @endif

</div>
@endsection
