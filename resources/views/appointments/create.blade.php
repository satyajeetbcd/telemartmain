@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Book an Appointment</h2>
        <p class="text-gray-600 mt-1">Schedule a consultation with a doctor</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Select Doctor *</label>
                    <select name="doctor_id" id="doctor_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Choose a doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" 
                                data-fee="{{ $doctor->consultation_fee ?? 0 }}"
                                {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->name }} 
                                @if($doctor->specialization)
                                    - {{ $doctor->specialization }}
                                @endif
                                @if($doctor->consultation_fee)
                                    (₹{{ number_format($doctor->consultation_fee, 2) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">Appointment Date *</label>
                    <input type="date" name="appointment_date" id="appointment_date" 
                        value="{{ old('appointment_date') }}" 
                        min="{{ date('Y-m-d') }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('appointment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">Appointment Time *</label>
                    <select name="appointment_time" id="appointment_time" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select date and doctor first</option>
                    </select>
                    @error('appointment_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Visit</label>
                    <textarea name="reason" id="reason" rows="3" 
                        placeholder="Briefly describe the reason for your appointment..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($patient)
                <div class="md:col-span-2 bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <strong>Patient:</strong> {{ $patient->full_name }} ({{ $patient->patient_id }})
                    </p>
                </div>
                @endif

                <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4" id="feeInfo" style="display: none;">
                    <p class="text-sm text-gray-700">
                        <strong>Consultation Fee:</strong> ₹<span id="consultationFee">0.00</span>
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('appointments.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const feeInfo = document.getElementById('feeInfo');
    const consultationFee = document.getElementById('consultationFee');

    // Update consultation fee when doctor is selected
    doctorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const fee = selectedOption.getAttribute('data-fee') || 0;
        
        if (fee > 0) {
            consultationFee.textContent = parseFloat(fee).toFixed(2);
            feeInfo.style.display = 'block';
        } else {
            feeInfo.style.display = 'none';
        }

        // Load available time slots if date is selected
        if (dateInput.value && this.value) {
            loadAvailableSlots(this.value, dateInput.value);
        }
    });

    // Load available time slots when date or doctor changes
    dateInput.addEventListener('change', function() {
        if (doctorSelect.value && this.value) {
            loadAvailableSlots(doctorSelect.value, this.value);
        }
    });

    function loadAvailableSlots(doctorId, date) {
        timeSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`{{ route('appointments.available-slots') }}?doctor_id=${doctorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '';
                
                if (data.available_slots && data.available_slots.length > 0) {
                    data.available_slots.forEach(slot => {
                        const option = document.createElement('option');
                        const time = new Date('2000-01-01 ' + slot);
                        option.value = slot;
                        option.textContent = time.toLocaleTimeString('en-US', { 
                            hour: '2-digit', 
                            minute: '2-digit',
                            hour12: true 
                        });
                        timeSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No available slots for this date';
                    timeSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error loading slots:', error);
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }
});
</script>
@endsection

