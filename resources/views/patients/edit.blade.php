@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Patient</h2>
        <p class="text-gray-600 mt-1">Patient ID: {{ $patient->patient_id }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $patient->last_name) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
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
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" id="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
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
                        <select name="blood_group" id="blood_group" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
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
            </div>

            <!-- Address Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('address', $patient->address) }}</textarea>
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

            <!-- Medical Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                        <textarea name="medical_history" id="medical_history" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('medical_history', $patient->medical_history) }}</textarea>
                        @error('medical_history')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('allergies', $patient->allergies) }}</textarea>
                        @error('allergies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="current_medications" id="current_medications" rows="2" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('current_medications', $patient->current_medications) }}</textarea>
                        @error('current_medications')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
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
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_relation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Insurance Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Insurance Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="insurance_provider" class="block text-sm font-medium text-gray-700 mb-2">Insurance Provider</label>
                        <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider', $patient->insurance_provider) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('insurance_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="insurance_policy_number" class="block text-sm font-medium text-gray-700 mb-2">Policy Number</label>
                        <input type="text" name="insurance_policy_number" id="insurance_policy_number" value="{{ old('insurance_policy_number', $patient->insurance_policy_number) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('insurance_policy_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status', $patient->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $patient->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('patients.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Patient
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


