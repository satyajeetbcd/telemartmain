@extends('layouts.app')

@section('title', 'Patient Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 h-32"></div>
        <div class="px-6 pb-6 -mt-16">
            <div class="flex items-end space-x-6">
                <div class="bg-white rounded-full p-2 shadow-lg">
                    <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 pb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $patient->full_name }}</h1>
                    <p class="text-lg text-gray-600 mt-1">Patient ID: {{ $patient->patient_id }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Personal Information</h2>
        
        <form action="{{ route('patient.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $patient->first_name) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $patient->last_name) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alternate_phone" class="block text-sm font-medium text-gray-700 mb-2">Alternate Phone</label>
                    <input type="text" name="alternate_phone" id="alternate_phone" value="{{ old('alternate_phone', $patient->alternate_phone) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('alternate_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" id="gender" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                    <select name="blood_group" id="blood_group" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Blood Group</option>
                        <option value="A+" {{ old('blood_group', $patient->blood_group) === 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_group', $patient->blood_group) === 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_group', $patient->blood_group) === 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_group', $patient->blood_group) === 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_group', $patient->blood_group) === 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_group', $patient->blood_group) === 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_group', $patient->blood_group) === 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_group', $patient->blood_group) === 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                    @error('blood_group')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('address', $patient->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $patient->city) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $patient->state) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $patient->postal_code) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $patient->country) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-2">Relation</label>
                        <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}" 
                            placeholder="e.g., Spouse, Parent, etc."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_relation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('patient.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

