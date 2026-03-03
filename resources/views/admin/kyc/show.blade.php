@extends('layouts.app')

@section('title', 'Review KYC Document')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Review KYC Document</h2>
            <p class="text-gray-600 mt-1">Review and approve/reject doctor verification document</p>
        </div>
        <a href="{{ route('admin.kyc.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Back to List
        </a>
    </div>

    <!-- Document Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Doctor</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $doctorKyc->doctor->name }}</p>
                <p class="text-sm text-gray-600">{{ $doctorKyc->doctor->email }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Document Type</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $doctorKyc->document_type_label }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Document Name</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $doctorKyc->document_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                    {{ $doctorKyc->status === 'approved' ? 'bg-green-100 text-green-800' : ($doctorKyc->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($doctorKyc->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Uploaded</h3>
                <p class="text-sm text-gray-900">{{ $doctorKyc->created_at->format('M d, Y h:i A') }}</p>
            </div>
            @if($doctorKyc->status === 'approved' && $doctorKyc->approver)
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Approved By</h3>
                <p class="text-sm text-gray-900">{{ $doctorKyc->approver->name }}</p>
                <p class="text-xs text-gray-500">{{ $doctorKyc->approved_at?->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            @if($doctorKyc->status === 'rejected' && $doctorKyc->rejection_reason)
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Rejection Reason</h3>
                <p class="text-sm text-red-600 bg-red-50 p-3 rounded">{{ $doctorKyc->rejection_reason }}</p>
            </div>
            @endif
        </div>

        <!-- Document Preview -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Document Preview</h3>
            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $doctorKyc->file_name }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($doctorKyc->file_size / 1024, 2) }} KB</p>
                    </div>
                    <a href="{{ route('doctor.kyc.download', $doctorKyc) }}" target="_blank" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        View Full Document
                    </a>
                </div>
                @if($doctorKyc->mime_type && str_contains($doctorKyc->mime_type, 'image'))
                    <img src="{{ Storage::disk('public')->url($doctorKyc->file_path) }}" 
                        alt="{{ $doctorKyc->document_name }}" 
                        class="max-w-full h-auto rounded border border-gray-300">
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">PDF Document</p>
                        <p class="text-xs text-gray-400">Click "View Full Document" to open</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approval/Rejection Form -->
    @if($doctorKyc->status === 'pending')
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Action</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Approve Form -->
            <form action="{{ route('admin.kyc.approve', $doctorKyc) }}" method="POST" 
                onsubmit="return confirm('Are you sure you want to approve this document?');">
                @csrf
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="font-medium text-green-900 mb-2">Approve Document</h4>
                    <p class="text-sm text-green-700 mb-4">This document meets all requirements and can be approved.</p>
                    <div class="mb-4">
                        <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" id="approve_notes" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Add any notes about this approval..."></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Approve Document
                    </button>
                </div>
            </form>

            <!-- Reject Form -->
            <form action="{{ route('admin.kyc.reject', $doctorKyc) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to reject this document?');">
                @csrf
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="font-medium text-red-900 mb-2">Reject Document</h4>
                    <p class="text-sm text-red-700 mb-4">This document does not meet requirements and should be rejected.</p>
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="Please provide a reason for rejection..."></textarea>
                        @error('rejection_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject Document
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

