@extends('layouts.app')

@section('title', 'Doctor Profile')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex items-start space-x-6">
                {{-- Square photo --}}
                <div class="flex-shrink-0">
                    <div class="w-36 h-36 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                        @if($doctor->profile_image)
                            <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Doctor details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Dr. {{ $doctor->name }}</h1>
                            <p class="text-base text-green-600 font-medium mt-0.5">{{ $doctor->specialization ?? 'General Practitioner' }}</p>
                        </div>
                        <div class="text-right">
                            @if($doctor->consultation_fee)
                                <p class="text-xl font-bold text-gray-900">₹{{ number_format($doctor->consultation_fee, 2) }}</p>
                                <p class="text-xs text-gray-500">Consultation Fee</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                        @if($doctor->qualifications)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                {{ $doctor->qualifications }}
                            </div>
                        @endif
                        @if($doctor->experience_years)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $doctor->experience_years }} years of experience
                            </div>
                        @endif
                        @if($doctor->license_number)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                License: {{ $doctor->license_number }}
                            </div>
                        @endif
                        @if($doctor->email)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $doctor->email }}
                            </div>
                        @endif
                        @if($doctor->phone)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $doctor->phone }}
                            </div>
                        @endif
                        @if($doctor->address || $doctor->city)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ collect([$doctor->address, $doctor->city, $doctor->state])->filter()->implode(', ') }}
                            </div>
                        @endif
                    </div>

                    @if($doctor->bio)
                        <p class="mt-3 text-sm text-gray-500 line-clamp-2">{{ $doctor->bio }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('doctor.profile', ['tab' => 'profile']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'profile' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Doctor Profile
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'documents']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'documents' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Document Upload
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'kyc']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'kyc' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    KYC Status
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'patients']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'patients' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Patients
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'appointments']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'appointments' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Appointments
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'availability']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'availability' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Availability
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'reviews']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'reviews' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Reviews
                </a>
                <a href="{{ route('doctor.profile', ['tab' => 'transactions']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'transactions' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Transactions
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'profile')
                @include('doctor.tabs.profile', ['doctor' => $doctor])
            @elseif($activeTab === 'documents')
                @include('doctor.tabs.documents', ['doctor' => $doctor, 'kycDocuments' => $kycDocuments])
            @elseif($activeTab === 'kyc')
                @include('doctor.tabs.kyc', ['doctor' => $doctor, 'kycStatus' => $kycStatus, 'kycDocuments' => $kycDocuments])
            @elseif($activeTab === 'patients')
                @include('doctor.tabs.patients', ['doctor' => $doctor, 'patientAppointments' => $patientAppointments])
            @elseif($activeTab === 'appointments')
                @include('doctor.tabs.appointments', ['doctor' => $doctor, 'appointments' => $appointments])
            @elseif($activeTab === 'availability')
                @include('doctor.tabs.availability', ['doctor' => $doctor])
            @elseif($activeTab === 'reviews')
                @include('doctor.tabs.reviews', ['doctor' => $doctor, 'reviews' => $reviews, 'averageRating' => $averageRating, 'reviewCount' => $reviewCount, 'ratingDistribution' => $ratingDistribution])
            @elseif($activeTab === 'transactions')
                @include('doctor.tabs.transactions', ['doctor' => $doctor])
            @endif
        </div>
    </div>
</div>
@endsection
