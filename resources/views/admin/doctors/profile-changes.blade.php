@extends('layouts.app')

@section('title', 'Review Profile Changes')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Review Profile Changes</h2>
            <p class="text-gray-600 mt-1">Doctor: {{ $doctor->name }}</p>
        </div>
        <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'profile']) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Back to Profile
        </a>
    </div>

    @if($pendingChanges)
    <!-- Pending Changes -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Pending Changes</h3>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Awaiting Approval
            </span>
        </div>

        <div class="space-y-4">
            @foreach($pendingChanges->changes as $field => $newValue)
                @php
                    $originalValue = $pendingChanges->original_values[$field] ?? null;
                    $fieldLabel = ucwords(str_replace('_', ' ', $field));
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">{{ $fieldLabel }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Current Value</p>
                            <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ $originalValue ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">New Value</p>
                            <p class="text-sm text-green-900 bg-green-50 p-2 rounded">{{ $newValue ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-4">Submitted: {{ $pendingChanges->created_at->format('M d, Y h:i A') }}</p>
            <div class="flex space-x-4">
                <form action="{{ route('admin.doctors.approve-profile-change', $pendingChanges) }}" method="POST" 
                    onsubmit="return confirm('Are you sure you want to approve these changes?');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Approve Changes
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" 
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Reject Changes
                </button>
            </div>

            <form id="rejectForm" action="{{ route('admin.doctors.reject-profile-change', $pendingChanges) }}" method="POST" class="hidden mt-4">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                        placeholder="Please provide a reason for rejection..."></textarea>
                    @error('rejection_reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Submit Rejection
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-500">No pending profile changes.</p>
    </div>
    @endif

    <!-- Change History -->
    @if($allChanges->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Change History</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($allChanges as $change)
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $change->created_at->format('M d, Y h:i A') }}
                            </p>
                            @if($change->approver)
                                <p class="text-xs text-gray-500">
                                    {{ $change->status === 'approved' ? 'Approved' : 'Rejected' }} by {{ $change->approver->name }}
                                    @if($change->approved_at)
                                        on {{ $change->approved_at->format('M d, Y') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $change->status === 'approved' ? 'bg-green-100 text-green-800' : ($change->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($change->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        @foreach($change->changes as $field => $value)
                            <div>
                                <span class="text-gray-500">{{ ucwords(str_replace('_', ' ', $field)) }}:</span>
                                <span class="text-gray-900 ml-2">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if($change->rejection_reason)
                        <div class="mt-4 p-3 bg-red-50 rounded">
                            <p class="text-xs text-red-600"><strong>Rejection Reason:</strong> {{ $change->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $allChanges->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

