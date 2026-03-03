@extends('layouts.app')

@section('title', 'KYC Documents')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">KYC Documents</h2>
            <p class="text-gray-600 mt-1">Upload and manage your verification documents</p>
        </div>
        <a href="{{ route('doctor.kyc.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Upload Document
        </a>
    </div>

    <!-- KYC Status Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">KYC Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Aadhar Front Status -->
            <div class="border rounded-lg p-4 {{ $kycStatus['aadhar_front']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['aadhar_front']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Aadhar Front</h4>
                    @if($kycStatus['aadhar_front']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['aadhar_front']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['aadhar_front']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
                @if($kycStatus['aadhar_front']['uploaded'])
                    <p class="text-sm text-gray-600">Uploaded: {{ $kycStatus['aadhar_front']['document']->created_at->format('M d, Y') }}</p>
                    @if($kycStatus['aadhar_front']['status'] === 'rejected' && $kycStatus['aadhar_front']['document']->rejection_reason)
                        <p class="text-xs text-red-600 mt-2">Reason: {{ $kycStatus['aadhar_front']['document']->rejection_reason }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Not uploaded yet</p>
                @endif
            </div>

            <!-- Aadhar Back Status -->
            <div class="border rounded-lg p-4 {{ $kycStatus['aadhar_back']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['aadhar_back']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Aadhar Back</h4>
                    @if($kycStatus['aadhar_back']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['aadhar_back']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['aadhar_back']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
                @if($kycStatus['aadhar_back']['uploaded'])
                    <p class="text-sm text-gray-600">Uploaded: {{ $kycStatus['aadhar_back']['document']->created_at->format('M d, Y') }}</p>
                    @if($kycStatus['aadhar_back']['status'] === 'rejected' && $kycStatus['aadhar_back']['document']->rejection_reason)
                        <p class="text-xs text-red-600 mt-2">Reason: {{ $kycStatus['aadhar_back']['document']->rejection_reason }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Not uploaded yet</p>
                @endif
            </div>

            <!-- PAN Card Status -->
            <div class="border rounded-lg p-4 {{ $kycStatus['pan']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['pan']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">PAN Card</h4>
                    @if($kycStatus['pan']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['pan']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['pan']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
                @if($kycStatus['pan']['uploaded'])
                    <p class="text-sm text-gray-600">Uploaded: {{ $kycStatus['pan']['document']->created_at->format('M d, Y') }}</p>
                    @if($kycStatus['pan']['status'] === 'rejected' && $kycStatus['pan']['document']->rejection_reason)
                        <p class="text-xs text-red-600 mt-2">Reason: {{ $kycStatus['pan']['document']->rejection_reason }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Not uploaded yet</p>
                @endif
            </div>

            <!-- Degree Certificates Status -->
            <div class="border rounded-lg p-4 {{ $kycStatus['degrees']['approved_count'] > 0 ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Degree Certificates</h4>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $kycStatus['degrees']['approved_count'] > 0 ? 'bg-green-100 text-green-800' : ($kycStatus['degrees']['pending_count'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $kycStatus['degrees']['approved_count'] }} Approved
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    Total: {{ $kycStatus['degrees']['count'] }} | 
                    Pending: {{ $kycStatus['degrees']['pending_count'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Uploaded Documents</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($documents->flatten() as $document)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $document->document_name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $document->document_type_label }} • 
                                        Uploaded: {{ $document->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : ($document->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($document->status) }}
                            </span>
                            @if($document->status === 'rejected' && $document->rejection_reason)
                                <div class="text-xs text-red-600 max-w-xs">
                                    <p><strong>Reason:</strong> {{ $document->rejection_reason }}</p>
                                </div>
                            @endif
                            <a href="{{ route('doctor.kyc.download', $document) }}" 
                                class="text-green-600 hover:text-green-700 text-sm font-medium">
                                View
                            </a>
                            @if($document->status === 'pending')
                                <form action="{{ route('doctor.kyc.destroy', $document) }}" method="POST" class="inline" 
                                    onsubmit="return confirm('Are you sure you want to delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-500">No documents uploaded yet.</p>
                    <a href="{{ route('doctor.kyc.create') }}" class="mt-4 inline-block text-green-600 hover:text-green-700">
                        Upload your first document
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

