@extends('layouts.app')

@section('title', 'Create Prescription')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Prescription</h2>
            <p class="text-gray-600 mt-1">Write a new prescription for a patient</p>
        </div>
        <a href="{{ route('prescriptions.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            &larr; Back to Prescriptions
        </a>
    </div>

    <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm">
        @csrf

        <!-- Patient & Appointment -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient & Appointment</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                    <select name="patient_id" id="patient_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (old('patient_id', $selectedPatientId) == $patient->id) ? 'selected' : '' }}>
                                {{ $patient->full_name }} ({{ $patient->patient_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700 mb-2">Related Appointment</label>
                    <select name="appointment_id" id="appointment_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">None</option>
                        @foreach($appointments as $apt)
                            <option value="{{ $apt->id }}" {{ (old('appointment_id', $selectedAppointmentId) == $apt->id) ? 'selected' : '' }}>
                                {{ $apt->appointment_number }} - {{ $apt->appointment_date->format('M d, Y') }} - Dr. {{ $apt->doctor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Prescription Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prescription Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="prescription_date" class="block text-sm font-medium text-gray-700 mb-2">Prescription Date *</label>
                    <input type="date" name="prescription_date" id="prescription_date" value="{{ old('prescription_date', date('Y-m-d')) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('prescription_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="2"
                        placeholder="Enter diagnosis..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Medicines -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="text-2xl font-bold text-green-700 mr-2">&#8478;</span>
                    Medicines *
                </h3>
                <button type="button" onclick="addMedicineRow()" class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Medicine
                </button>
            </div>

            @error('medicines')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div id="medicines-container">
                <div class="medicine-row border border-gray-200 rounded-lg p-4 mb-4" data-index="0">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-semibold text-gray-700 medicine-number">Medicine #1</span>
                        <button type="button" onclick="removeMedicineRow(this)" class="text-red-500 hover:text-red-700 text-sm hidden remove-btn">Remove</button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Medicine Name *</label>
                            <input type="text" name="medicines[0][medicine_name]" required placeholder="e.g., Paracetamol"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dosage</label>
                            <input type="text" name="medicines[0][dosage]" placeholder="e.g., 500mg"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Route</label>
                            <select name="medicines[0][route]" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                @foreach(\App\Models\Prescription::getRouteOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Frequency</label>
                            <input type="text" name="medicines[0][frequency]" placeholder="e.g., 1-0-1"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Duration</label>
                            <input type="text" name="medicines[0][duration]" placeholder="e.g., 7 days"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
                            <input type="text" name="medicines[0][quantity]" placeholder="e.g., 14 tablets"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Instructions</label>
                            <input type="text" name="medicines[0][instructions]" placeholder="e.g., After food"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <textarea name="notes" id="notes" rows="3"
                placeholder="Any additional instructions for the patient..."
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('prescriptions.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Create Prescription</button>
        </div>
    </form>
</div>

<script>
let medicineIndex = 1;

const routeOptions = `@foreach(\App\Models\Prescription::getRouteOptions() as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach`;

function addMedicineRow() {
    const container = document.getElementById('medicines-container');
    const row = document.createElement('div');
    row.className = 'medicine-row border border-gray-200 rounded-lg p-4 mb-4';
    row.dataset.index = medicineIndex;
    row.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-semibold text-gray-700 medicine-number">Medicine #${medicineIndex + 1}</span>
            <button type="button" onclick="removeMedicineRow(this)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Medicine Name *</label>
                <input type="text" name="medicines[${medicineIndex}][medicine_name]" required placeholder="e.g., Paracetamol"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dosage</label>
                <input type="text" name="medicines[${medicineIndex}][dosage]" placeholder="e.g., 500mg"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Route</label>
                <select name="medicines[${medicineIndex}][route]" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                    ${routeOptions}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Frequency</label>
                <input type="text" name="medicines[${medicineIndex}][frequency]" placeholder="e.g., 1-0-1"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Duration</label>
                <input type="text" name="medicines[${medicineIndex}][duration]" placeholder="e.g., 7 days"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
                <input type="text" name="medicines[${medicineIndex}][quantity]" placeholder="e.g., 14 tablets"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Instructions</label>
                <input type="text" name="medicines[${medicineIndex}][instructions]" placeholder="e.g., After food"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
            </div>
        </div>
    `;
    container.appendChild(row);
    medicineIndex++;
    updateRemoveButtons();
}

function removeMedicineRow(button) {
    const row = button.closest('.medicine-row');
    row.remove();
    updateMedicineNumbers();
    updateRemoveButtons();
}

function updateMedicineNumbers() {
    document.querySelectorAll('.medicine-row').forEach((row, index) => {
        row.querySelector('.medicine-number').textContent = `Medicine #${index + 1}`;
    });
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.medicine-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-btn');
        if (btn) {
            btn.classList.toggle('hidden', rows.length <= 1);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const patientSelect = document.getElementById('patient_id');
    const appointmentSelect = document.getElementById('appointment_id');

    patientSelect.addEventListener('change', function() {
        const patientId = this.value;
        appointmentSelect.innerHTML = '<option value="">Loading...</option>';

        if (!patientId) {
            appointmentSelect.innerHTML = '<option value="">None</option>';
            return;
        }

        fetch(`{{ route('prescriptions.appointments') }}?patient_id=${patientId}`)
            .then(response => response.json())
            .then(data => {
                appointmentSelect.innerHTML = '<option value="">None</option>';
                data.forEach(apt => {
                    const option = document.createElement('option');
                    option.value = apt.id;
                    option.textContent = apt.text;
                    appointmentSelect.appendChild(option);
                });
            })
            .catch(() => {
                appointmentSelect.innerHTML = '<option value="">None</option>';
            });
    });

    updateRemoveButtons();
});
</script>
@endsection
