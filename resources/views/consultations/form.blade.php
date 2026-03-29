@extends('layouts.app')

@section('title', isset($consultation) ? 'Edit Consultation' : 'Add Consultation')

@section('content')
<div class="max-w-5xl mx-auto" x-data="consultationForm()">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ isset($consultation) ? 'Edit Consultation' : 'Add Consultation' }}</h2>
            <p class="text-gray-600 mt-1">
                @if(isset($consultation))
                    {{ $consultation->consultation_number }}
                @else
                    Fill in consultation details for the patient
                @endif
            </p>
        </div>
        <a href="{{ isset($consultation) ? route('consultations.show', $consultation) : route('consultations.index') }}"
           class="text-green-600 hover:text-green-700 text-sm font-medium">&larr; Back</a>
    </div>

    <form action="{{ isset($consultation) ? route('consultations.update', $consultation) : route('consultations.store') }}"
          method="POST" enctype="multipart/form-data" @submit="prepareSubmit()">
        @csrf
        @if(isset($consultation))
            @method('PUT')
        @endif

        <!-- Hidden JSON fields -->
        <input type="hidden" name="chief_complaints_json" :value="JSON.stringify(chiefComplaints)">
        <input type="hidden" name="patient_history_json" :value="JSON.stringify(selectedPatientHistory)">
        <input type="hidden" name="personal_history_json" :value="JSON.stringify(personalHistory)">
        <input type="hidden" name="family_history_json" :value="JSON.stringify(selectedFamilyHistory)">
        <input type="hidden" name="allergies_json" :value="JSON.stringify(selectedAllergies)">
        <input type="hidden" name="medications_json" :value="JSON.stringify(medications)">

        <!-- Patient & Status -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient & Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                    <select name="patient_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                        <option value="">Select Patient...</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id', isset($consultation) ? $consultation->patient_id : ($selectedPatientId ?? '')) == $p->id ? 'selected' : '' }}>
                                {{ $p->full_name }} ({{ $p->patient_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                    <select name="doctor_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                        <option value="">Unassigned</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ old('doctor_id', $consultation->doctor_id ?? '') == $doc->id ? 'selected' : '' }}>
                                Dr. {{ $doc->name }} - {{ $doc->specialization ?? 'General' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                        <option value="pending" {{ old('status', $consultation->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_review" {{ old('status', $consultation->status ?? '') === 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="completed" {{ old('status', $consultation->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $consultation->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up?</label>
                    <div class="flex items-center space-x-4 mt-2">
                        <label class="flex items-center">
                            <input type="radio" name="is_followup" value="1" {{ old('is_followup', isset($consultation) && $consultation->is_followup ? '1' : '0') === '1' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="ml-1.5 text-sm text-gray-700">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="is_followup" value="0" {{ old('is_followup', isset($consultation) && $consultation->is_followup ? '1' : '0') === '0' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="ml-1.5 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Query -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Query</h3>
            <textarea name="query" rows="4" maxlength="1500" placeholder="Enter patient query (Recommended: minimum 100 characters)"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">{{ old('query', $consultation->query ?? '') }}</textarea>
            @error('query') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Chief Complaints -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chief Complaints</h3>

            <!-- Add complaint -->
            <div class="flex flex-wrap gap-2 mb-4">
                <template x-for="c in availableComplaints" :key="c">
                    <button type="button" @click="toggleComplaint(c)"
                            class="px-3 py-1.5 rounded-full text-xs font-medium border transition"
                            :class="chiefComplaints.some(cc => cc.name === c) ? 'bg-green-100 border-green-500 text-green-700' : 'bg-white border-gray-300 text-gray-600 hover:border-green-400'">
                        <span x-text="c"></span>
                    </button>
                </template>
            </div>

            <!-- Custom complaint -->
            <div class="flex gap-2 mb-4">
                <input type="text" x-model="customComplaint" placeholder="Add custom complaint..."
                       class="flex-1 max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                <button type="button" @click="addCustomComplaint()"
                        class="px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Add</button>
            </div>

            <!-- Selected complaints with sub-answers -->
            <template x-for="(complaint, cIdx) in chiefComplaints" :key="'c-'+cIdx">
                <div class="border border-gray-200 rounded-lg p-4 mb-3">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-green-700 text-sm" x-text="complaint.name"></h4>
                        <button type="button" @click="removeComplaint(cIdx)" class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <!-- Sub answers key-value pairs -->
                    <div class="space-y-2">
                        <template x-for="key in Object.keys(complaint.sub_answers || {})" :key="'sa-'+cIdx+'-'+key">
                            <div class="grid grid-cols-3 gap-2 items-center">
                                <label class="text-xs font-medium text-gray-500 capitalize" x-text="key.replace(/_/g, ' ')"></label>
                                <div class="col-span-2">
                                    <div x-show="Array.isArray(complaint.sub_answers[key])">
                                        <input type="text" :value="Array.isArray(complaint.sub_answers[key]) ? complaint.sub_answers[key].join(', ') : ''"
                                               @input="complaint.sub_answers[key] = $event.target.value.split(',').map(s => s.trim()).filter(s => s)"
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs"
                                               placeholder="Comma separated values">
                                    </div>
                                    <div x-show="!Array.isArray(complaint.sub_answers[key])">
                                        <input type="text" :value="complaint.sub_answers[key]"
                                               @input="complaint.sub_answers[key] = $event.target.value"
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <!-- Add new sub-answer -->
                        <div class="flex gap-2 mt-2">
                            <input type="text" x-model="newSubKey[cIdx]" placeholder="Field name"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                            <input type="text" x-model="newSubVal[cIdx]" placeholder="Value"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                            <button type="button" @click="addSubAnswer(cIdx)"
                                    class="px-2 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700">+ Add</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- History -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">History</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient History -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Patient History</h4>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="item in patientHistoryOptions" :key="item">
                            <label class="flex items-center text-xs cursor-pointer">
                                <input type="checkbox" :value="item" x-model="selectedPatientHistory"
                                       class="text-green-600 rounded focus:ring-green-500 w-3.5 h-3.5">
                                <span class="ml-1.5 text-gray-700" x-text="item"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Family History -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Family History</h4>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="item in familyHistoryOptions" :key="item">
                            <label class="flex items-center text-xs cursor-pointer">
                                <input type="checkbox" :value="item" x-model="selectedFamilyHistory"
                                       class="text-green-600 rounded focus:ring-green-500 w-3.5 h-3.5">
                                <span class="ml-1.5 text-gray-700" x-text="item"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Personal History -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Personal History</h4>
                    <div class="space-y-2">
                        <template x-for="item in personalHistoryOptions" :key="item.key">
                            <div class="flex items-center justify-between bg-gray-50 rounded px-3 py-2">
                                <span class="text-xs font-medium text-gray-700" x-text="item.label"></span>
                                <div class="flex space-x-3">
                                    <label class="flex items-center text-xs">
                                        <input type="radio" :name="'ph_'+item.key" value="yes"
                                               @click="personalHistory[item.key] = true"
                                               :checked="personalHistory[item.key] === true"
                                               class="text-green-600 focus:ring-green-500 w-3.5 h-3.5">
                                        <span class="ml-1">Yes</span>
                                    </label>
                                    <label class="flex items-center text-xs">
                                        <input type="radio" :name="'ph_'+item.key" value="no"
                                               @click="personalHistory[item.key] = false"
                                               :checked="personalHistory[item.key] === false"
                                               class="text-green-600 focus:ring-green-500 w-3.5 h-3.5">
                                        <span class="ml-1">No</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Allergies -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Allergies</h4>
                    <div class="flex gap-2 mb-2">
                        <input type="text" x-model="newAllergy" placeholder="Add allergy..."
                               @keydown.enter.prevent="addAllergy()"
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                        <button type="button" @click="addAllergy()"
                                class="px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700">Add</button>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="(allergy, idx) in selectedAllergies" :key="'al-'+idx">
                            <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                <span x-text="allergy"></span>
                                <button type="button" @click="selectedAllergies.splice(idx, 1)" class="ml-1 text-yellow-600 hover:text-red-600">&times;</button>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medications -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Medications</h3>
            <template x-for="(med, mIdx) in medications" :key="'med-'+mIdx">
                <div class="grid grid-cols-5 gap-2 mb-2 items-end">
                    <div>
                        <label x-show="mIdx === 0" class="block text-xs font-medium text-gray-500 mb-1">Medicine</label>
                        <input type="text" x-model="med.name" placeholder="Medicine name"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                    </div>
                    <div>
                        <label x-show="mIdx === 0" class="block text-xs font-medium text-gray-500 mb-1">Dose</label>
                        <input type="text" x-model="med.dose" placeholder="Dose"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                    </div>
                    <div>
                        <label x-show="mIdx === 0" class="block text-xs font-medium text-gray-500 mb-1">Frequency</label>
                        <input type="text" x-model="med.frequency" placeholder="Frequency"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                    </div>
                    <div>
                        <label x-show="mIdx === 0" class="block text-xs font-medium text-gray-500 mb-1">Duration</label>
                        <input type="text" x-model="med.duration_value" placeholder="Duration"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs">
                    </div>
                    <div>
                        <button type="button" @click="medications.splice(mIdx, 1)" x-show="medications.length > 1"
                                class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                    </div>
                </div>
            </template>
            <button type="button" @click="medications.push({name:'', dose:'', frequency:'', duration_value:'', duration_type:''})"
                    class="mt-2 text-sm text-green-600 hover:text-green-700 font-medium">+ Add Medication</button>
        </div>

        <!-- Location & OPD -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Preference</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location Preference</label>
                    <select name="location_preference" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                        <option value="">Select...</option>
                        <option value="within_state" {{ old('location_preference', $consultation->location_preference ?? '') === 'within_state' ? 'selected' : '' }}>Within State Only</option>
                        <option value="neighbouring" {{ old('location_preference', $consultation->location_preference ?? '') === 'neighbouring' ? 'selected' : '' }}>Neighbouring States</option>
                        <option value="anywhere" {{ old('location_preference', $consultation->location_preference ?? '') === 'anywhere' ? 'selected' : '' }}>Anywhere in India</option>
                        <option value="hospital" {{ old('location_preference', $consultation->location_preference ?? '') === 'hospital' ? 'selected' : '' }}>Hospital/Organization</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State/UT</label>
                    <input type="text" name="state" value="{{ old('state', $consultation->state ?? '') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" placeholder="State">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">OPD</label>
                    <input type="text" name="opd" value="{{ old('opd', $consultation->opd ?? '') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-sm" placeholder="OPD">
                </div>
            </div>
        </div>

        <!-- Health Records Upload -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Records</h3>
            @if(isset($consultation) && $consultation->health_records)
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-2">Existing files:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($consultation->health_records as $file)
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-xs text-gray-700">
                                <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                {{ $file['name'] ?? 'File' }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            <input type="file" name="health_records[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            <p class="mt-1 text-xs text-gray-400">Max 5 files (PDF, JPG, PNG, DOC). Up to 10MB each.</p>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ isset($consultation) ? route('consultations.show', $consultation) : route('consultations.index') }}"
               class="px-6 py-2.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                {{ isset($consultation) ? 'Update Consultation' : 'Create Consultation' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function consultationForm() {
    @php
        $existing = isset($consultation) ? $consultation : null;
    @endphp

    return {
        availableComplaints: ['Joint Pain', 'Fever', 'Headache', 'Common Cold', 'Cough', 'Acidity', 'Stomach Pain', 'Loose Motion', 'Diabetes Follow-Up'],
        customComplaint: '',
        chiefComplaints: @json($existing?->chief_complaints ?? []).map(c => ({
            name: c.name || '',
            sub_answers: c.sub_answers || {}
        })),
        newSubKey: {},
        newSubVal: {},

        patientHistoryOptions: ['Diabetes', 'Mental Disorders', 'Hypertension', 'Past Surgeries', 'Endocrine Disorders', 'Metabolic Disorders', 'Cardiovascular Disease', 'Thyroid Disease', 'Stroke'],
        selectedPatientHistory: @json($existing?->patient_history ?? []) || [],

        familyHistoryOptions: ['Diabetes', 'Osteoporosis', 'High Cholesterol', 'Hypertension', 'Asthma', 'Birth Defects', 'Mental Illness', 'Stroke', 'Heart Disease', 'Cancer', 'Genetic Conditions'],
        selectedFamilyHistory: @json($existing?->family_history ?? []) || [],

        personalHistoryOptions: [
            { key: 'alcohol', label: 'Alcohol Use' },
            { key: 'drug', label: 'Drug Use' },
            { key: 'smoking', label: 'Smoking' },
        ],
        personalHistory: Object.assign({alcohol: null, drug: null, smoking: null}, @json($existing?->personal_history ?? []) || {}),

        selectedAllergies: @json($existing?->allergies ?? []) || [],
        newAllergy: '',

        medications: (() => {
            const meds = @json($existing?->medications ?? []);
            return (Array.isArray(meds) && meds.length > 0) ? meds : [{name: '', dose: '', frequency: '', duration_value: '', duration_type: ''}];
        })(),

        toggleComplaint(name) {
            const idx = this.chiefComplaints.findIndex(c => c.name === name);
            if (idx >= 0) {
                this.chiefComplaints.splice(idx, 1);
            } else {
                this.chiefComplaints.push({ name: name, sub_answers: {} });
            }
        },

        addCustomComplaint() {
            const name = this.customComplaint.trim();
            if (name && !this.chiefComplaints.some(c => c.name === name)) {
                this.chiefComplaints.push({ name: name, sub_answers: {} });
                this.customComplaint = '';
            }
        },

        removeComplaint(idx) {
            this.chiefComplaints.splice(idx, 1);
        },

        addSubAnswer(cIdx) {
            const key = (this.newSubKey[cIdx] || '').trim().toLowerCase().replace(/\s+/g, '_');
            const val = (this.newSubVal[cIdx] || '').trim();
            if (key && val) {
                this.chiefComplaints[cIdx].sub_answers[key] = val;
                this.newSubKey[cIdx] = '';
                this.newSubVal[cIdx] = '';
            }
        },

        addAllergy() {
            const val = this.newAllergy.trim();
            if (val && !this.selectedAllergies.includes(val)) {
                this.selectedAllergies.push(val);
                this.newAllergy = '';
            }
        },

        prepareSubmit() {
            // JSON fields are already bound via :value
        }
    };
}
</script>
@endpush
@endsection
