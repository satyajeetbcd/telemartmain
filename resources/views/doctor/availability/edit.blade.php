@extends('layouts.app')

@section('title', 'Edit Availability')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Availability Slot</h2>
            <a href="{{ route('doctor.availability.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <form action="{{ route('doctor.availability.update', $availability) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Availability Type</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="availability_type" value="weekly" {{ $availability->specific_date ? '' : 'checked' }} onchange="toggleAvailabilityType()" class="mr-2">
                        <span>Weekly Recurring</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="availability_type" value="specific" {{ $availability->specific_date ? 'checked' : '' }} onchange="toggleAvailabilityType()" class="mr-2">
                        <span>Specific Date</span>
                    </label>
                </div>
            </div>

            <!-- Day of Week (for weekly) -->
            <div id="weekly_fields" class="mb-4 {{ $availability->specific_date ? 'hidden' : '' }}">
                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
                <select name="day_of_week" id="day_of_week" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Day</option>
                    <option value="monday" {{ old('day_of_week', $availability->day_of_week) === 'monday' ? 'selected' : '' }}>Monday</option>
                    <option value="tuesday" {{ old('day_of_week', $availability->day_of_week) === 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                    <option value="wednesday" {{ old('day_of_week', $availability->day_of_week) === 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                    <option value="thursday" {{ old('day_of_week', $availability->day_of_week) === 'thursday' ? 'selected' : '' }}>Thursday</option>
                    <option value="friday" {{ old('day_of_week', $availability->day_of_week) === 'friday' ? 'selected' : '' }}>Friday</option>
                    <option value="saturday" {{ old('day_of_week', $availability->day_of_week) === 'saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="sunday" {{ old('day_of_week', $availability->day_of_week) === 'sunday' ? 'selected' : '' }}>Sunday</option>
                </select>
                @error('day_of_week')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Specific Date (for specific date) -->
            <div id="specific_fields" class="mb-4 {{ $availability->specific_date ? '' : 'hidden' }}">
                <label for="specific_date" class="block text-sm font-medium text-gray-700 mb-2">Specific Date</label>
                <input type="date" name="specific_date" id="specific_date" value="{{ old('specific_date', $availability->specific_date ? $availability->specific_date->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                @error('specific_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Range -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $availability->start_time ? date('H:i', strtotime($availability->start_time)) : '') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $availability->end_time ? date('H:i', strtotime($availability->end_time)) : '') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Slot Duration -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="slot_duration" class="block text-sm font-medium text-gray-700 mb-2">Slot Duration (minutes)</label>
                    <input type="number" name="slot_duration" id="slot_duration" value="{{ old('slot_duration', $availability->slot_duration) }}" min="15" max="120" step="15" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <p class="mt-1 text-xs text-gray-500">Duration of each appointment slot (15-120 minutes)</p>
                    @error('slot_duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="break_duration" class="block text-sm font-medium text-gray-700 mb-2">Break Between Slots (minutes)</label>
                    <input type="number" name="break_duration" id="break_duration" value="{{ old('break_duration', $availability->break_duration) }}" min="0" max="60" step="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <p class="mt-1 text-xs text-gray-500">Break time between consecutive slots</p>
                    @error('break_duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Availability Status -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $availability->is_available) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Available for booking</span>
                </label>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $availability->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('doctor.availability.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Availability
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAvailabilityType() {
    const type = document.querySelector('input[name="availability_type"]:checked').value;
    const weeklyFields = document.getElementById('weekly_fields');
    const specificFields = document.getElementById('specific_fields');
    
    if (type === 'weekly') {
        weeklyFields.classList.remove('hidden');
        specificFields.classList.add('hidden');
        document.getElementById('day_of_week').required = true;
        document.getElementById('specific_date').required = false;
        document.getElementById('specific_date').value = '';
    } else {
        weeklyFields.classList.add('hidden');
        specificFields.classList.remove('hidden');
        document.getElementById('day_of_week').required = false;
        document.getElementById('specific_date').required = true;
        document.getElementById('day_of_week').value = '';
    }
}
</script>
@endsection

