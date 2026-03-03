@extends('layouts.app')

@section('title', 'Update Appointment')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Update Appointment</h2>
        <p class="text-gray-600 mt-1">Appointment #{{ $appointment->appointment_number }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ old('status', $appointment->status) === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="cancellationReason" style="display: none;">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason *</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('cancellation_reason', $appointment->cancellation_reason) }}</textarea>
                    @error('cancellation_reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Doctor's Notes</label>
                    <textarea name="notes" id="notes" rows="5"
                        placeholder="Add consultation notes, diagnosis, recommendations..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700 mb-2">Prescription</label>
                    <textarea name="prescription" id="prescription" rows="5"
                        placeholder="Prescribe medications, dosage, and instructions..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('prescription', $appointment->prescription) }}</textarea>
                    @error('prescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Appointment Details</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Patient</dt>
                            <dd class="text-gray-900">{{ $appointment->patient->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Date & Time</dt>
                            <dd class="text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }} at {{ date('h:i A', strtotime($appointment->appointment_time)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Consultation Fee</dt>
                            <dd class="text-gray-900">₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}</dd>
                        </div>
                        @if($appointment->reason)
                        <div>
                            <dt class="text-gray-500">Reason</dt>
                            <dd class="text-gray-900">{{ $appointment->reason }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('appointments.show', $appointment) }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const cancellationReasonDiv = document.getElementById('cancellationReason');
    const cancellationReasonInput = document.getElementById('cancellation_reason');

    function toggleCancellationReason() {
        if (statusSelect.value === 'cancelled') {
            cancellationReasonDiv.style.display = 'block';
            cancellationReasonInput.required = true;
        } else {
            cancellationReasonDiv.style.display = 'none';
            cancellationReasonInput.required = false;
        }
    }

    statusSelect.addEventListener('change', toggleCancellationReason);
    toggleCancellationReason(); // Initial check
});
</script>
@endsection

