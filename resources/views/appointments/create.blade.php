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

                {{-- ── PATIENT SELECTION ── --}}
                @if(Auth::user()->hasRole('Patient'))
                    {{-- Patient sees their own info --}}
                    @if($patient)
                    <div class="md:col-span-2 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700">
                            <strong>Patient:</strong> {{ $patient->full_name }} ({{ $patient->patient_id }})
                        </p>
                    </div>
                    @endif
                @else
                    {{-- Doctor / Admin / Receptionist selects a patient --}}
                    <div class="md:col-span-2">
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Select Patient *</label>
                        <select name="patient_id" id="patient_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Choose a patient…</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->full_name }} — {{ $p->patient_id }}
                                    @if($p->phone) ({{ $p->phone }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- ── DOCTOR SELECTION ── --}}
                @if(Auth::user()->hasRole('Doctor'))
                    {{-- Doctor is booking — they ARE the doctor --}}
                    <input type="hidden" name="doctor_id" value="{{ Auth::id() }}">
                    <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700">
                            <strong>Doctor:</strong> Dr. {{ Auth::user()->name }}
                            @if(Auth::user()->specialization)
                                <span class="text-gray-500">({{ Auth::user()->specialization }})</span>
                            @endif
                        </p>
                        @if(Auth::user()->consultation_fee)
                        <p class="text-sm text-gray-700 mt-1">
                            <strong>Consultation Fee:</strong> ₹{{ number_format(Auth::user()->consultation_fee, 2) }}
                        </p>
                        @endif
                    </div>
                @else
                    {{-- Admin / Receptionist / Patient selects a doctor --}}
                    <div class="md:col-span-2">
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">Select Doctor *</label>
                        <select name="doctor_id" id="doctor_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Choose a doctor…</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    data-fee="{{ $doctor->consultation_fee ?? 0 }}"
                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->name }}
                                    @if($doctor->specialization) — {{ $doctor->specialization }} @endif
                                    @if($doctor->consultation_fee) (₹{{ number_format($doctor->consultation_fee, 2) }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4" id="feeInfo" style="display: none;">
                        <p class="text-sm text-gray-700">
                            <strong>Consultation Fee:</strong> ₹<span id="consultationFee">0.00</span>
                        </p>
                    </div>
                @endif

                {{-- ── DATE ── --}}
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

                {{-- ── TIME ── --}}
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

                {{-- ── REASON ── --}}
                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Visit</label>
                    <textarea name="reason" id="reason" rows="3"
                        placeholder="Briefly describe the reason for this appointment…"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
document.addEventListener('DOMContentLoaded', function () {
    const doctorSelectEl = document.getElementById('doctor_id'); // null when Doctor role (hidden input)
    const dateInput      = document.getElementById('appointment_date');
    const timeSelect     = document.getElementById('appointment_time');
    const feeInfo        = document.getElementById('feeInfo');        // may be null
    const feeSpan        = document.getElementById('consultationFee'); // may be null

    // Get current doctor id regardless of whether it's a select or hidden input
    function getDoctorId() {
        if (doctorSelectEl) return doctorSelectEl.value;
        const hidden = document.querySelector('input[name="doctor_id"]');
        return hidden ? hidden.value : '';
    }

    // Show fee when doctor select changes
    if (doctorSelectEl && feeInfo && feeSpan) {
        doctorSelectEl.addEventListener('change', function () {
            const fee = this.options[this.selectedIndex]?.getAttribute('data-fee') || 0;
            if (parseFloat(fee) > 0) {
                feeSpan.textContent = parseFloat(fee).toFixed(2);
                feeInfo.style.display = 'block';
            } else {
                feeInfo.style.display = 'none';
            }
            if (dateInput.value && this.value) {
                loadAvailableSlots(this.value, dateInput.value);
            }
        });
    }

    // Load slots when date changes
    dateInput.addEventListener('change', function () {
        const doctorId = getDoctorId();
        if (doctorId && this.value) {
            loadAvailableSlots(doctorId, this.value);
        }
    });

    function loadAvailableSlots(doctorId, date) {
        timeSelect.innerHTML = '<option value="">Loading…</option>';

        fetch(`{{ route('appointments.available-slots') }}?doctor_id=${doctorId}&date=${date}`)
            .then(r => r.json())
            .then(data => {
                timeSelect.innerHTML = '';
                if (data.available_slots && data.available_slots.length > 0) {
                    data.available_slots.forEach(slot => {
                        const option = document.createElement('option');
                        const t = new Date('2000-01-01 ' + slot);
                        option.value = slot;
                        option.textContent = t.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                        timeSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No available slots for this date';
                    timeSelect.appendChild(option);
                }
            })
            .catch(() => {
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }
});
</script>
@endsection
