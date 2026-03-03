@extends('layouts.app')

@section('title', 'Doctor Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 h-32"></div>
        <div class="px-6 pb-6 -mt-16">
            <div class="flex items-end justify-between">
                <div class="flex items-end space-x-6">
                    <div class="bg-white rounded-full p-2 shadow-lg">
                        <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center">
                            @if($doctor->profile_image)
                                <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="{{ $doctor->name }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 pb-4">
                        <h1 class="text-3xl font-bold text-gray-900">Dr. {{ $doctor->name }}</h1>
                        <p class="text-lg text-gray-600 mt-1">{{ $doctor->specialization ?? 'General Practitioner' }}</p>
                        @if($doctor->qualifications)
                            <p class="text-sm text-gray-500 mt-1">{{ $doctor->qualifications }}</p>
                        @endif
                        @if($doctor->phone)
                            <p class="text-sm text-gray-500 mt-1">📞 {{ $doctor->phone }}</p>
                        @endif
                        @if($doctor->address || $doctor->city)
                            <p class="text-sm text-gray-500 mt-1">📍 {{ $doctor->address ?? '' }}{{ $doctor->address && $doctor->city ? ', ' : '' }}{{ $doctor->city ?? '' }}{{ ($doctor->address || $doctor->city) && $doctor->state ? ', ' : '' }}{{ $doctor->state ?? '' }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex space-x-3 pb-4">
                    <form action="{{ route('doctors.check-kyc', $doctor) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Check KYC Status
                        </button>
                    </form>
                    <a href="{{ route('doctors.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'profile']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'profile' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Doctor Profile
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'documents']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'documents' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Documents
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'kyc']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'kyc' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    KYC Status
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'patients']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'patients' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Patients
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'appointments']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'appointments' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Appointments
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'availability']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'availability' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Availability
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'reviews']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'reviews' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Reviews
                </a>
                <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'transactions']) }}" 
                   class="px-6 py-4 text-sm font-medium border-b-2 {{ $activeTab === 'transactions' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Transactions
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'profile')
                @include('admin.doctors.tabs.profile', ['doctor' => $doctor])
            @elseif($activeTab === 'documents')
                @include('admin.doctors.tabs.documents', ['doctor' => $doctor, 'kycDocuments' => $kycDocuments])
            @elseif($activeTab === 'kyc')
                @include('admin.doctors.tabs.kyc', ['doctor' => $doctor, 'kycStatus' => $kycStatus, 'kycDocuments' => $kycDocuments])
            @elseif($activeTab === 'patients')
                @include('admin.doctors.tabs.patients', ['doctor' => $doctor, 'patients' => $patients])
            @elseif($activeTab === 'appointments')
                @include('admin.doctors.tabs.appointments', ['doctor' => $doctor, 'appointments' => $appointments])
            @elseif($activeTab === 'availability')
                @include('admin.doctors.tabs.availability', ['doctor' => $doctor, 'availabilities' => $availabilities, 'specificAvailabilities' => $specificAvailabilities])
            @elseif($activeTab === 'reviews')
                @include('admin.doctors.tabs.reviews', ['doctor' => $doctor, 'reviews' => $reviews, 'averageRating' => $averageRating, 'reviewCount' => $reviewCount, 'ratingDistribution' => $ratingDistribution])
            @elseif($activeTab === 'transactions')
                @include('admin.doctors.tabs.transactions', ['doctor' => $doctor])
            @endif
        </div>
    </div>
</div>
@endsection
