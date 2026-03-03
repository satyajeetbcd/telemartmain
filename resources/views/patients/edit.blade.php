@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Patient</h2>
        <p class="text-gray-600 mt-1">Patient ID: {{ $patient->patient_id }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $patient->last_name) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" id="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-2">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Blood Group</option>
                            <option value="A+" {{ old('blood_group', $patient->blood_group) === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group', $patient->blood_group) === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group', $patient->blood_group) === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group', $patient->blood_group) === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group', $patient->blood_group) === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group', $patient->blood_group) === 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group', $patient->blood_group) === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group', $patient->blood_group) === 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('address', $patient->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $patient->city) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $patient->state) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $patient->postal_code) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $patient->country) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                        <textarea name="medical_history" id="medical_history" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('medical_history', $patient->medical_history) }}</textarea>
                        @error('medical_history')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('allergies', $patient->allergies) }}</textarea>
                        @error('allergies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="current_medications" id="current_medications" rows="2" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('current_medications', $patient->current_medications) }}</textarea>
                        @error('current_medications')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-2">Relation</label>
                        <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('emergency_contact_relation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Insurance Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Insurance Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="insurance_provider" class="block text-sm font-medium text-gray-700 mb-2">Insurance Provider</label>
                        <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider', $patient->insurance_provider) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('insurance_provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="insurance_policy_number" class="block text-sm font-medium text-gray-700 mb-2">Policy Number</label>
                        <input type="text" name="insurance_policy_number" id="insurance_policy_number" value="{{ old('insurance_policy_number', $patient->insurance_policy_number) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('insurance_policy_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status', $patient->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('notes', $patient->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('patients.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Update Patient
                </button>
            </div>
        </form>
    </div>

    <!-- Assign Appointment -->
    <div id="assign-appointment" class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Assign Appointment with Doctor
        </h3>
        <p class="text-sm text-gray-600 mb-4">New appointments are created with <strong>Payment Pending</strong>. When payment is marked received, the appointment is confirmed and shown as success to both doctor and patient.</p>

        <form action="{{ route('appointments.store') }}" method="POST" id="assignAppointmentForm" class="mb-6">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="assign_doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Doctor *</label>
                    <select name="doctor_id" id="assign_doctor_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Choose doctor...</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" data-fee="{{ $doc->consultation_fee ?? 0 }}">Dr. {{ $doc->name }}@if($doc->specialization) - {{ $doc->specialization }}@endif @if($doc->consultation_fee) (₹{{ number_format($doc->consultation_fee, 2) }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="assign_appointment_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="appointment_date" id="assign_appointment_date" min="{{ date('Y-m-d') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="assign_appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Time *</label>
                    <select name="appointment_time" id="assign_appointment_time" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select date & doctor first</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="assign_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for visit</label>
                    <textarea name="reason" id="assign_reason" rows="2" placeholder="Optional reason..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Assign Appointment (Payment Pending)</button>
                </div>
            </div>
        </form>

        <h4 class="text-sm font-semibold text-gray-700 mb-3">Patient's Appointments</h4>
        @if($patient->appointments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($patient->appointments as $apt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $apt->appointment_number }}</td>
                        <td class="px-3 py-2">Dr. {{ $apt->doctor->name }}</td>
                        <td class="px-3 py-2">{{ $apt->appointment_date->format('M d, Y') }} {{ date('h:i A', strtotime($apt->appointment_time)) }}</td>
                        <td class="px-3 py-2">
                            @if($apt->payment_status === 'paid' && in_array($apt->status, ['confirmed', 'completed']))
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Success</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ $apt->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $apt->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $apt->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $apt->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if($apt->payment_status === 'paid')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('appointments.show', $apt) }}" class="text-green-600 hover:text-green-800 mr-2">View</a>
                            @if($apt->payment_status === 'pending' && (Auth::user()->hasRole('Doctor') && $apt->doctor_id === Auth::id() || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Receptionist')))
                            <form action="{{ route('appointments.mark-paid', $apt) }}" method="POST" class="inline" onsubmit="return confirm('Mark payment as received? Appointment will be confirmed.');">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-800">Mark paid</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500 py-4">No appointments yet. Use the form above to assign one.</p>
        @endif
    </div>

    <!-- Medical Records -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Medical Records ({{ $patient->medicalRecords->count() }})
            </h3>
            <div class="flex space-x-2">
                <a href="{{ route('medical-records.index', ['patient_id' => $patient->id]) }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                    View All
                </a>
                @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
                <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}" class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Record
                </a>
                @endif
            </div>
        </div>

        @if($patient->medicalRecords->count() > 0)
        <div class="space-y-3">
            @foreach($patient->medicalRecords->take(5) as $record)
            <div class="flex items-center justify-between border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex items-center space-x-4 min-w-0">
                    @php
                        $typeColors = [
                            'consultation' => 'bg-blue-100 text-blue-800',
                            'lab_report' => 'bg-purple-100 text-purple-800',
                            'prescription' => 'bg-green-100 text-green-800',
                            'diagnosis' => 'bg-orange-100 text-orange-800',
                            'discharge_summary' => 'bg-gray-100 text-gray-800',
                            'imaging' => 'bg-indigo-100 text-indigo-800',
                            'vaccination' => 'bg-teal-100 text-teal-800',
                            'surgical' => 'bg-red-100 text-red-800',
                            'follow_up' => 'bg-yellow-100 text-yellow-800',
                            'other' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$record->record_type] ?? 'bg-gray-100 text-gray-800' }} flex-shrink-0">
                        {{ \App\Models\MedicalRecord::getRecordTypes()[$record->record_type] ?? ucfirst($record->record_type) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $record->title }}</p>
                        <p class="text-xs text-gray-500">{{ $record->record_number }} &bull; Dr. {{ $record->doctor->name }} &bull; {{ $record->record_date->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 flex-shrink-0 ml-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($record->status) }}
                    </span>
                    <a href="{{ route('medical-records.show', $record) }}" class="text-green-600 hover:text-green-900 text-sm">View</a>
                    @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
                        @if(!Auth::user()->hasRole('Doctor') || $record->doctor_id === Auth::id())
                        <a href="{{ route('medical-records.edit', $record) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach

            @if($patient->medicalRecords->count() > 5)
            <div class="text-center pt-2">
                <a href="{{ route('medical-records.index', ['patient_id' => $patient->id]) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                    View all {{ $patient->medicalRecords->count() }} records &rarr;
                </a>
            </div>
            @endif
        </div>
        @else
        <div class="text-center py-6">
            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No medical records yet.</p>
            @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
            <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}" class="mt-2 inline-flex items-center text-green-600 hover:text-green-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Record
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Prescriptions -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Prescriptions ({{ $patient->prescriptions->count() }})
            </h3>
            <div class="flex space-x-2">
                <a href="{{ route('prescriptions.index', ['patient_id' => $patient->id]) }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                    View All
                </a>
                @if(Auth::user()->hasRole('Doctor'))
                <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Prescription
                </a>
                @endif
            </div>
        </div>

        @if($patient->prescriptions->count() > 0)
        <div class="space-y-3">
            @foreach($patient->prescriptions->take(5) as $prescription)
            <div class="flex items-center justify-between border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex items-center space-x-4 min-w-0">
                    <div class="flex-shrink-0">
                        <span class="text-lg font-bold text-green-700">&#8478;</span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-medium text-gray-900">{{ $prescription->prescription_number }}</p>
                            @foreach($prescription->items->take(2) as $item)
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs px-1.5 py-0.5 rounded">{{ $item->medicine_name }}</span>
                            @endforeach
                            @if($prescription->items->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $prescription->items->count() - 2 }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Dr. {{ $prescription->doctor->name }} &bull; {{ $prescription->prescription_date->format('M d, Y') }}
                            @if($prescription->diagnosis) &bull; {{ Str::limit($prescription->diagnosis, 40) }}@endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 flex-shrink-0 ml-4">
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'expired' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($prescription->status) }}
                    </span>
                    <a href="{{ route('prescriptions.show', $prescription) }}" class="text-green-600 hover:text-green-900 text-sm">View</a>
                    @if(Auth::user()->hasRole('Doctor') && $prescription->doctor_id === Auth::id())
                        <a href="{{ route('prescriptions.edit', $prescription) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                    @endif
                </div>
            </div>
            @endforeach

            @if($patient->prescriptions->count() > 5)
            <div class="text-center pt-2">
                <a href="{{ route('prescriptions.index', ['patient_id' => $patient->id]) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                    View all {{ $patient->prescriptions->count() }} prescriptions &rarr;
                </a>
            </div>
            @endif
        </div>
        @else
        <div class="text-center py-6">
            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No prescriptions yet.</p>
            @if(Auth::user()->hasRole('Doctor'))
            <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="mt-2 inline-flex items-center text-green-600 hover:text-green-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Prescription
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var assignDoctor = document.getElementById('assign_doctor_id');
    var assignDate = document.getElementById('assign_appointment_date');
    var assignTime = document.getElementById('assign_appointment_time');
    if (assignDoctor && assignDate && assignTime) {
        function loadSlots() {
            var doctorId = assignDoctor.value;
            var date = assignDate.value;
            assignTime.innerHTML = '<option value="">Loading...</option>';
            if (!doctorId || !date) {
                assignTime.innerHTML = '<option value="">Select date &amp; doctor first</option>';
                return;
            }
            fetch('{{ route("appointments.available-slots") }}?doctor_id=' + doctorId + '&date=' + date)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    assignTime.innerHTML = '';
                    if (data.available_slots && data.available_slots.length) {
                        data.available_slots.forEach(function(slot) {
                            var opt = document.createElement('option');
                            opt.value = slot;
                            var t = new Date('2000-01-01 ' + slot);
                            opt.textContent = t.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            assignTime.appendChild(opt);
                        });
                    } else {
                        var opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'No slots available';
                        assignTime.appendChild(opt);
                    }
                })
                .catch(function() {
                    assignTime.innerHTML = '<option value="">Error loading slots</option>';
                });
        }
        assignDoctor.addEventListener('change', loadSlots);
        assignDate.addEventListener('change', loadSlots);
    }
});
</script>
@endsection
