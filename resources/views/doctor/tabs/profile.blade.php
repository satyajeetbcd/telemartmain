<!-- Doctor Profile Tab -->
<div class="space-y-6">
    @php
        $pendingChange = \App\Models\DoctorProfileChange::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->latest()
            ->first();
    @endphp

    @if($pendingChange)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Profile Changes Pending Approval</h4>
                <p class="text-sm text-yellow-700 mt-1">Your profile changes have been submitted and are awaiting admin approval. Changes will be active once approved.</p>
                <p class="text-xs text-yellow-600 mt-1">Submitted: {{ $pendingChange->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Profile Image -->
        <div class="mb-6">
            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
            @if($doctor->profile_image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="Current Profile" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                </div>
            @endif
            <input type="file" name="profile_image" id="profile_image" accept="image/*"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                @if($pendingChange) disabled @endif>
            <p class="mt-1 text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
            @error('profile_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $doctor->name) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required
                    @if($pendingChange) disabled @endif>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required
                    @if($pendingChange) disabled @endif>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->phone) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $doctor->date_of_birth ? $doctor->date_of_birth->format('Y-m-d') : '') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('date_of_birth')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="aadhar_card_number" class="block text-sm font-medium text-gray-700 mb-2">Aadhar Card Number</label>
                <input type="text" name="aadhar_card_number" id="aadhar_card_number" value="{{ old('aadhar_card_number', $doctor->aadhar_card_number) }}" 
                    maxlength="12" pattern="[0-9]{12}" placeholder="12 digit Aadhar number"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                <p class="mt-1 text-xs text-gray-500">12 digit Aadhar card number</p>
                @error('aadhar_card_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $doctor->specialization) }}" 
                    placeholder="e.g., Cardiology, Pediatrics, etc."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('specialization')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('license_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" 
                    min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('experience_years')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="consultation_fee" class="block text-sm font-medium text-gray-700 mb-2">Consultation Fee (₹)</label>
                <input type="number" name="consultation_fee" id="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" 
                    step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('consultation_fee')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <textarea name="address" id="address" rows="3" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                @if($pendingChange) disabled @endif>{{ old('address', $doctor->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                <select name="state_id" id="state_id" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                    <option value="">Select State</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $doctor->state_id) == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <select name="city_id" id="city_id" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id', $doctor->city_id) == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('city_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $doctor->postal_code) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('postal_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', $doctor->country ?? 'India') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>
                @error('country')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6">
            <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-2">Qualifications</label>
                <textarea name="qualifications" id="qualifications" rows="3" 
                    placeholder="e.g., MBBS, MD, DM, etc."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>{{ old('qualifications', $doctor->qualifications) }}</textarea>
            @error('qualifications')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Professional Bio</label>
                <textarea name="bio" id="bio" rows="5" 
                    placeholder="Write about your professional background, expertise, and achievements..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    @if($pendingChange) disabled @endif>{{ old('bio', $doctor->bio) }}</textarea>
            @error('bio')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('doctor.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" @if($pendingChange) disabled @endif>
                @if($pendingChange)
                    Changes Pending Approval
                @else
                    Update Profile
                @endif
            </button>
        </div>
        @if($pendingChange)
        <div class="mt-4 text-sm text-gray-600">
            <p>You have pending profile changes. Please wait for admin approval before making new changes.</p>
        </div>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const currentCityId = {{ $doctor->city_id ?? 'null' }};
    const pendingChange = {{ $pendingChange ? 'true' : 'false' }};
    
    if (pendingChange) return; // Don't enable dynamic loading if pending changes
    
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

