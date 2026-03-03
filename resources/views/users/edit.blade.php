@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
        <p class="mt-1 text-sm text-gray-600">Update user information</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <!-- Profile Image -->
                <div>
                    <label for="profile_image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                    @if($user->profile_image)
                        <div class="mt-2 mb-2">
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-24 h-24 rounded-full object-cover">
                        </div>
                    @endif
                    <input type="file" name="profile_image" id="profile_image" accept="image/*"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="aadhar_card_number" class="block text-sm font-medium text-gray-700">Aadhar Card Number</label>
                    <input type="text" name="aadhar_card_number" id="aadhar_card_number" value="{{ old('aadhar_card_number', $user->aadhar_card_number) }}" maxlength="12"
                        pattern="[0-9]{12}" placeholder="12 digit Aadhar number"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">12 digit Aadhar card number</p>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="state_id" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state_id" id="state_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $user->state_id) == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="city_id" class="block text-sm font-medium text-gray-700">City</label>
                        <select name="city_id" id="city_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $user->country ?? 'India') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const currentCityId = {{ $user->city_id ?? 'null' }};
    
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        if (stateId) {
            fetch(`{{ route('api.cities') }}?state_id=${stateId}`)
                .then(response => response.json())
                .then(cities => {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        if (currentCityId && city.id == currentCityId) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching cities:', error);
                });
        }
    });
    
    // Trigger change if state is already selected
    if (stateSelect.value) {
        stateSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection

