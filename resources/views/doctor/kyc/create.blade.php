@extends('layouts.app')

@section('title', 'Upload KYC Document')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Upload KYC Document</h2>
        <p class="text-gray-600 mt-1">Upload your verification documents for approval</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('doctor.kyc.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Document Type -->
                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                    <select name="document_type" id="document_type" required 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">Select Document Type</option>
                        <option value="aadhar_front" {{ old('document_type') === 'aadhar_front' ? 'selected' : '' }}>Aadhar Card (Front)</option>
                        <option value="aadhar_back" {{ old('document_type') === 'aadhar_back' ? 'selected' : '' }}>Aadhar Card (Back)</option>
                        <option value="degree" {{ old('document_type') === 'degree' ? 'selected' : '' }}>Degree Certificate</option>
                        <option value="pan" {{ old('document_type') === 'pan' ? 'selected' : '' }}>PAN Card</option>
                    </select>
                    @error('document_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Name (for degrees) -->
                <div id="document_name_field" style="display: none;">
                    <label for="document_name" class="block text-sm font-medium text-gray-700 mb-2">Document Name</label>
                    <input type="text" name="document_name" id="document_name" value="{{ old('document_name') }}"
                        placeholder="e.g., MBBS Degree, MD Cardiology, etc."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <p class="mt-1 text-xs text-gray-500">Enter a descriptive name for this degree certificate</p>
                    @error('document_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700 mb-2">Document File *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-green-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                    <span>Upload a file</span>
                                    <input id="document_file" name="document_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only" onchange="updateFileName(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, PNG, JPG up to 10MB</p>
                            <p id="file_name_display" class="text-sm text-gray-900 mt-2"></p>
                        </div>
                    </div>
                    @error('document_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Upload Instructions:</h4>
                    <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                        <li>Ensure the document is clear and readable</li>
                        <li>File size should not exceed 10MB</li>
                        <li>Accepted formats: PDF, JPG, JPEG, PNG</li>
                        <li>For degree certificates, you can upload multiple documents</li>
                        <li>Documents will be reviewed by admin before approval</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('doctor.kyc.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Upload Document
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentType = document.getElementById('document_type');
    const documentNameField = document.getElementById('document_name_field');
    const documentName = document.getElementById('document_name');

    documentType.addEventListener('change', function() {
        if (this.value === 'degree') {
            documentNameField.style.display = 'block';
            documentName.required = false; // Optional but recommended
        } else {
            documentNameField.style.display = 'none';
            documentName.required = false;
            documentName.value = '';
        }
    });

    // Trigger on page load if value is set
    if (documentType.value === 'degree') {
        documentNameField.style.display = 'block';
    }
});

function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file_name_display');
    if (input.files && input.files[0]) {
        fileNameDisplay.textContent = 'Selected: ' + input.files[0].name;
        fileNameDisplay.classList.remove('hidden');
    } else {
        fileNameDisplay.textContent = '';
        fileNameDisplay.classList.add('hidden');
    }
}
</script>
@endsection

