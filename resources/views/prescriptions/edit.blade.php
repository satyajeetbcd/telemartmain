@extends('layouts.app')

@section('title', 'Edit Prescription')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Prescription</h2>
            <p class="text-gray-600 mt-1">Rx #{{ $prescription->prescription_number }}</p>
        </div>
        <a href="{{ route('prescriptions.show', $prescription) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            &larr; Back to Prescription
        </a>
    </div>

    <form action="{{ route('prescriptions.update', $prescription) }}" method="POST" id="prescriptionForm">
        @csrf
        @method('PUT')

        <!-- Patient Info (read-only) -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">
                <strong>Patient:</strong> {{ $prescription->patient->full_name }} ({{ $prescription->patient->patient_id }})
                &nbsp;&bull;&nbsp;
                <strong>Doctor:</strong> Dr. {{ $prescription->doctor->name }}
                @if($prescription->appointment)
                &nbsp;&bull;&nbsp;
                <strong>Appointment:</strong> {{ $prescription->appointment->appointment_number }}
                @endif
            </p>
        </div>

        <!-- Prescription Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prescription Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="prescription_date" class="block text-sm font-medium text-gray-700 mb-2">Prescription Date *</label>
                    <input type="date" name="prescription_date" id="prescription_date" value="{{ old('prescription_date', $prescription->prescription_date->format('Y-m-d')) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('prescription_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', $prescription->valid_until?->format('Y-m-d')) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="active" {{ old('status', $prescription->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $prescription->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $prescription->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="expired" {{ old('status', $prescription->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-3">
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="2"
                        placeholder="Enter diagnosis..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('diagnosis', $prescription->diagnosis) }}</textarea>
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
                @foreach($prescription->items as $index => $item)
                <div class="medicine-row border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-semibold text-gray-700 medicine-number">Medicine #{{ $index + 1 }}</span>
                        <button type="button" onclick="removeMedicineRow(this)" class="text-red-500 hover:text-red-700 text-sm remove-btn {{ $prescription->items->count() <= 1 ? 'hidden' : '' }}">Remove</button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Medicine Name *</label>
                            <input type="text" name="medicines[{{ $index }}][medicine_name]" required placeholder="e.g., Paracetamol"
                                value="{{ old("medicines.{$index}.medicine_name", $item->medicine_name) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dosage</label>
                            <input type="text" name="medicines[{{ $index }}][dosage]" placeholder="e.g., 500mg"
                                value="{{ old("medicines.{$index}.dosage", $item->dosage) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Route</label>
                            <select name="medicines[{{ $index }}][route]" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                                @foreach(\App\Models\Prescription::getRouteOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ old("medicines.{$index}.route", $item->route) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Frequency</label>
                            <input type="text" name="medicines[{{ $index }}][frequency]" placeholder="e.g., 1-0-1"
                                value="{{ old("medicines.{$index}.frequency", $item->frequency) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Duration</label>
                            <input type="text" name="medicines[{{ $index }}][duration]" placeholder="e.g., 7 days"
                                value="{{ old("medicines.{$index}.duration", $item->duration) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
                            <input type="text" name="medicines[{{ $index }}][quantity]" placeholder="e.g., 14 tablets"
                                value="{{ old("medicines.{$index}.quantity", $item->quantity) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Instructions</label>
                            <input type="text" name="medicines[{{ $index }}][instructions]" placeholder="e.g., After food"
                                value="{{ old("medicines.{$index}.instructions", $item->instructions) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <textarea name="notes" id="notes" rows="3"
                placeholder="Any additional instructions for the patient..."
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $prescription->notes) }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('prescriptions.show', $prescription) }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Update Prescription</button>
        </div>
    </form>
</div>

<script>
let medicineIndex = {{ $prescription->items->count() }};

const routeOptions = `@foreach(\App\Models\Prescription::getRouteOptions() as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach`;

function addMedicineRow() {
    const container = document.getElementById('medicines-container');
    const row = document.createElement('div');
    row.className = 'medicine-row border border-gray-200 rounded-lg p-4 mb-4';
    row.dataset.index = medicineIndex;
    row.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <span class="text-sm font-semibold text-gray-700 medicine-number">Medicine #${document.querySelectorAll('.medicine-row').length + 1}</span>
            <button type="button" onclick="removeMedicineRow(this)" class="text-red-500 hover:text-red-700 text-sm remove-btn">Remove</button>
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
    updateRemoveButtons();
});
</script>
@endsection
