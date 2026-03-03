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
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Pending Profile Changes</h4>
                <p class="text-sm text-yellow-700 mt-1">This doctor has submitted profile changes that are awaiting approval.</p>
                <p class="text-xs text-yellow-600 mt-1">Submitted: {{ $pendingChange->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route('admin.doctors.profile-changes', $doctor) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">
                Review Changes
            </a>
        </div>
    </div>
    @endif

    <!-- Current Profile Information -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Profile Information</h3>
        
        <!-- Profile Image -->
        @if($doctor->profile_image)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Profile Image</h4>
            <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="{{ $doctor->name }}" class="w-32 h-32 rounded-full object-cover border-4 border-green-200">
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Name</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->name }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->email }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Phone</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->phone ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Date of Birth</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->date_of_birth ? $doctor->date_of_birth->format('M d, Y') : '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Aadhar Card Number</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->aadhar_card_number ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Address</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->address ?? '-' }}</p>
            </div>
            @if($doctor->cityRelation || $doctor->stateRelation || $doctor->postal_code)
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">City, State, Postal Code</h4>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $doctor->cityRelation->name ?? ($doctor->city ?? '') }}{{ ($doctor->cityRelation || $doctor->city) && ($doctor->stateRelation || $doctor->state) ? ', ' : '' }}{{ $doctor->stateRelation->name ?? ($doctor->state ?? '') }}{{ (($doctor->cityRelation || $doctor->city) || ($doctor->stateRelation || $doctor->state)) && $doctor->postal_code ? ' - ' : '' }}{{ $doctor->postal_code ?? '' }}
                </p>
            </div>
            @endif
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Country</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->country ?? 'India' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Specialization</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->specialization ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">License Number</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->license_number ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Experience</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $doctor->experience_years ?? 0 }} Years</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Consultation Fee</h4>
                <p class="text-lg font-semibold text-gray-900">₹{{ number_format($doctor->consultation_fee ?? 0, 2) }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Status</h4>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                    {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : ($doctor->status === 'inactive' ? 'bg-red-100 text-red-800' : ($doctor->status === 'pending_approval' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                </span>
            </div>
        </div>

        @if($doctor->qualifications)
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Qualifications</h4>
            <p class="text-gray-900 whitespace-pre-wrap">{{ $doctor->qualifications }}</p>
        </div>
        @endif

        @if($doctor->bio)
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Bio</h4>
            <p class="text-gray-900 whitespace-pre-wrap">{{ $doctor->bio }}</p>
        </div>
        @endif
    </div>

    <!-- Edit Profile Form (Admin can edit directly) -->
    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Profile (Admin)</h3>
        <form action="{{ route('admin.doctors.update-profile', $doctor) }}" method="POST" enctype="multipart/form-data">
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
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                <p class="mt-1 text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                @error('profile_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $doctor->name) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->phone) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $doctor->date_of_birth ? $doctor->date_of_birth->format('Y-m-d') : '') }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="aadhar_card_number" class="block text-sm font-medium text-gray-700 mb-2">Aadhar Card Number</label>
                    <input type="text" name="aadhar_card_number" id="aadhar_card_number" value="{{ old('aadhar_card_number', $doctor->aadhar_card_number) }}" 
                        maxlength="12" pattern="[0-9]{12}" placeholder="12 digit Aadhar number"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <p class="mt-1 text-xs text-gray-500">12 digit Aadhar card number</p>
                    @error('aadhar_card_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                    <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $doctor->specialization) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('specialization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $doctor->license_number) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('license_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                    <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" 
                        min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('experience_years')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consultation_fee" class="block text-sm font-medium text-gray-700 mb-2">Consultation Fee (₹)</label>
                    <input type="number" name="consultation_fee" id="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" 
                        step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('consultation_fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" id="address" rows="3" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('address', $doctor->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <select name="state_id" id="state_id" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
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
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
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
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" id="country" value="{{ old('country', $doctor->country ?? 'India') }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-2">Qualifications</label>
                <textarea name="qualifications" id="qualifications" rows="3" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('qualifications', $doctor->qualifications) }}</textarea>
                @error('qualifications')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Professional Bio</label>
                <textarea name="bio" id="bio" rows="5" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('bio', $doctor->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');
        const currentCityId = {{ $doctor->city_id ?? 'null' }};
        
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

    <!-- Update Status Form -->
    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Doctor Status</h3>
        <form action="{{ route('doctors.update-status', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex items-center space-x-4">
                <select name="status" id="status" required class="border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="active" {{ $doctor->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $doctor->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending_kyc" {{ $doctor->status === 'pending_kyc' ? 'selected' : '' }}>Pending KYC</option>
                    <option value="pending_approval" {{ $doctor->status === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
