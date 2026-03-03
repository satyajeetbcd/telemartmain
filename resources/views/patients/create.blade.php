@extends('layouts.app')

@section('title', 'Create Patient')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create New Patient</h2>
        <p class="text-gray-600 mt-1">Add a new patient to the system</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf

            <!-- Personal Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" id="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Blood Group</option>
                            <option value="A+" {{ old('blood_group') === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group') === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group') === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group') === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group') === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group') === 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group') === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group') === 'O-' ? 'selected' : '' }}>O-</option>
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
                        <textarea name="address" id="address" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', 'India') }}"
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
                            placeholder="Previous medical conditions, surgeries, etc."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('medical_history') }}</textarea>
                        @error('medical_history')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" 
                            placeholder="Known allergies..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('allergies') }}</textarea>
                        @error('allergies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="current_medications" id="current_medications" rows="2" 
                            placeholder="Current medications and dosages..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('current_medications') }}</textarea>
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
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-2">Relation</label>
                        <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}"
                            placeholder="e.g., Spouse, Parent"
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
                        <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('insurance_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="insurance_policy_number" class="block text-sm font-medium text-gray-700 mb-2">Policy Number</label>
                        <input type="text" name="insurance_policy_number" id="insurance_policy_number" value="{{ old('insurance_policy_number') }}"
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
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                            placeholder="Additional notes about the patient..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- User Account Creation -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <input type="checkbox" name="create_user_account" id="create_user_account" value="1" 
                        {{ old('create_user_account') ? 'checked' : '' }}
                        class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <div class="ml-3">
                        <label for="create_user_account" class="text-sm font-medium text-gray-700">Create User Account</label>
                        <p class="text-xs text-gray-500 mt-1">Create a user account for this patient with "Patient" role</p>
                    </div>
                </div>
                <div id="passwordFields" style="display: none;" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('patients.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Create Patient
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('create_user_account');
    const passwordFields = document.getElementById('passwordFields');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            passwordFields.style.display = 'block';
            passwordInput.required = true;
            passwordConfirmationInput.required = true;
        } else {
            passwordFields.style.display = 'none';
            passwordInput.required = false;
            passwordConfirmationInput.required = false;
            passwordInput.value = '';
            passwordConfirmationInput.value = '';
        }
    });

    // Check on page load if checkbox was checked
    if (checkbox.checked) {
        passwordFields.style.display = 'block';
        passwordInput.required = true;
        passwordConfirmationInput.required = true;
    }
});
</script>
@endsection


