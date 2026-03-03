@extends('layouts.app')

@section('title', 'Edit Medical Record')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Medical Record</h2>
            <p class="text-gray-600 mt-1">Record #{{ $medicalRecord->record_number }}</p>
        </div>
        <a href="{{ route('medical-records.show', $medicalRecord) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
            &larr; Back to Record
        </a>
    </div>

    <form action="{{ route('medical-records.update', $medicalRecord) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Patient Info (read-only) -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">
                <strong>Patient:</strong> {{ $medicalRecord->patient->full_name }} ({{ $medicalRecord->patient->patient_id }})
                &nbsp;&bull;&nbsp;
                <strong>Doctor:</strong> Dr. {{ $medicalRecord->doctor->name }}
                @if($medicalRecord->appointment)
                &nbsp;&bull;&nbsp;
                <strong>Appointment:</strong> {{ $medicalRecord->appointment->appointment_number }}
                @endif
            </p>
        </div>

        <!-- Record Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="record_type" class="block text-sm font-medium text-gray-700 mb-2">Record Type *</label>
                    <select name="record_type" id="record_type" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @foreach(\App\Models\MedicalRecord::getRecordTypes() as $key => $label)
                            <option value="{{ $key }}" {{ old('record_type', $medicalRecord->record_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('record_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">Record Date *</label>
                    <input type="date" name="record_date" id="record_date" value="{{ old('record_date', $medicalRecord->record_date->format('Y-m-d')) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('record_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">Follow-up Date</label>
                    <input type="date" name="follow_up_date" id="follow_up_date" value="{{ old('follow_up_date', $medicalRecord->follow_up_date?->format('Y-m-d')) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('follow_up_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="active" {{ old('status', $medicalRecord->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ old('status', $medicalRecord->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $medicalRecord->title) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('description', $medicalRecord->description) }}</textarea>
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
                    <input type="text" name="blood_pressure" id="blood_pressure" 
                        value="{{ old('blood_pressure', $medicalRecord->vitals['blood_pressure'] ?? '') }}"
                        placeholder="120/80"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">Heart Rate</label>
                    <input type="text" name="heart_rate" id="heart_rate" 
                        value="{{ old('heart_rate', $medicalRecord->vitals['heart_rate'] ?? '') }}"
                        placeholder="72"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature</label>
                    <input type="text" name="temperature" id="temperature" 
                        value="{{ old('temperature', $medicalRecord->vitals['temperature'] ?? '') }}"
                        placeholder="98.6"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                    <input type="text" name="weight" id="weight" 
                        value="{{ old('weight', $medicalRecord->vitals['weight'] ?? '') }}"
                        placeholder="70"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                    <input type="text" name="height" id="height" 
                        value="{{ old('height', $medicalRecord->vitals['height'] ?? '') }}"
                        placeholder="170"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">SpO2 (%)</label>
                    <input type="text" name="oxygen_saturation" id="oxygen_saturation" 
                        value="{{ old('oxygen_saturation', $medicalRecord->vitals['oxygen_saturation'] ?? '') }}"
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
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('symptoms', $medicalRecord->symptoms) }}</textarea>
                    @error('symptoms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="3"
                        placeholder="Enter the diagnosis..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700 mb-2">Prescription</label>
                    <textarea name="prescription" id="prescription" rows="3"
                        placeholder="Enter the prescription details..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('prescription', $medicalRecord->prescription) }}</textarea>
                    @error('prescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan</label>
                    <textarea name="treatment_plan" id="treatment_plan" rows="3"
                        placeholder="Outline the treatment plan..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('treatment_plan', $medicalRecord->treatment_plan) }}</textarea>
                    @error('treatment_plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        placeholder="Any additional notes..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $medicalRecord->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Existing Attachments -->
        @if($medicalRecord->attachments && count($medicalRecord->attachments) > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Existing Attachments</h3>
            <div class="space-y-3">
                @foreach($medicalRecord->attachments as $index => $attachment)
                <div class="flex items-center justify-between border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ number_format(($attachment['size'] ?? 0) / 1024, 1) }} KB</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('medical-records.download', [$medicalRecord, $index]) }}" class="text-green-600 hover:text-green-700 text-sm">Download</a>
                        <label class="flex items-center space-x-2 text-sm text-red-600">
                            <input type="checkbox" name="remove_attachments[]" value="{{ $index }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>Remove</span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- New Attachments -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Attachments</h3>
            <div>
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
            <a href="{{ route('medical-records.show', $medicalRecord) }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Update Record
            </button>
        </div>
    </form>
</div>
@endsection
