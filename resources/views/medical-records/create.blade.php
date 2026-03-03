@extends('layouts.app')

@section('title', 'Create Medical Record')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Medical Record</h2>
            <p class="text-gray-600 mt-1">Add a new medical record for a patient</p>
        </div>
        <a href="{{ route('medical-records.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            &larr; Back to Records
        </a>
    </div>

    <form action="{{ route('medical-records.store') }}" method="POST" enctype="multipart/form-data">
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
                        <option value="">None (standalone record)</option>
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

        <!-- Record Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="record_type" class="block text-sm font-medium text-gray-700 mb-2">Record Type *</label>
                    <select name="record_type" id="record_type" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @foreach(\App\Models\MedicalRecord::getRecordTypes() as $key => $label)
                            <option value="{{ $key }}" {{ old('record_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('record_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">Record Date *</label>
                    <input type="date" name="record_date" id="record_date" value="{{ old('record_date', date('Y-m-d')) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('record_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                    <input type="date" name="follow_up_date" id="follow_up_date" value="{{ old('follow_up_date') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('follow_up_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-3">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        placeholder="e.g., General Consultation, Blood Test Results..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-3">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="2"
                        placeholder="Brief description of this record..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Vitals -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vitals</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                    <input type="text" name="blood_pressure" id="blood_pressure" value="{{ old('blood_pressure') }}"
                        placeholder="120/80"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">Heart Rate</label>
                    <input type="text" name="heart_rate" id="heart_rate" value="{{ old('heart_rate') }}"
                        placeholder="72"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature</label>
                    <input type="text" name="temperature" id="temperature" value="{{ old('temperature') }}"
                        placeholder="98.6"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                    <input type="text" name="weight" id="weight" value="{{ old('weight') }}"
                        placeholder="70"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                    <input type="text" name="height" id="height" value="{{ old('height') }}"
                        placeholder="170"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">SpO2 (%)</label>
                    <input type="text" name="oxygen_saturation" id="oxygen_saturation" value="{{ old('oxygen_saturation') }}"
                        placeholder="98"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
        </div>

        <!-- Clinical Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Clinical Details</h3>
            <div class="space-y-6">
                <div>
                    <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms</label>
                    <textarea name="symptoms" id="symptoms" rows="3"
                        placeholder="Describe the patient's symptoms..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('symptoms') }}</textarea>
                    @error('symptoms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="3"
                        placeholder="Enter the diagnosis..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700 mb-2">Prescription</label>
                    <textarea name="prescription" id="prescription" rows="3"
                        placeholder="Enter the prescription details..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('prescription') }}</textarea>
                    @error('prescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                    <textarea name="treatment_plan" id="treatment_plan" rows="3"
                        placeholder="Outline the treatment plan..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('treatment_plan') }}</textarea>
                    @error('treatment_plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        placeholder="Any additional notes..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attachments</h3>
            <div>
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Upload Files</label>
                <input type="file" name="attachments[]" id="attachments" multiple
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                <p class="mt-1 text-xs text-gray-500">Accepted: PDF, JPG, PNG, DOC, DOCX. Max 10MB per file.</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('medical-records.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Create Record
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientSelect = document.getElementById('patient_id');
    const appointmentSelect = document.getElementById('appointment_id');

    patientSelect.addEventListener('change', function() {
        const patientId = this.value;
        appointmentSelect.innerHTML = '<option value="">Loading...</option>';

        if (!patientId) {
            appointmentSelect.innerHTML = '<option value="">None (standalone record)</option>';
            return;
        }

        fetch(`{{ route('medical-records.appointments') }}?patient_id=${patientId}`)
            .then(response => response.json())
            .then(data => {
                appointmentSelect.innerHTML = '<option value="">None (standalone record)</option>';
                data.forEach(apt => {
                    const option = document.createElement('option');
                    option.value = apt.id;
                    option.textContent = apt.text;
                    appointmentSelect.appendChild(option);
                });
            })
            .catch(() => {
                appointmentSelect.innerHTML = '<option value="">None (standalone record)</option>';
            });
    });
});
</script>
@endsection
