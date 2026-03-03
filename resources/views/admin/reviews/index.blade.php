@extends('layouts.app')

@section('title', 'Review Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Review Management</h2>
            <p class="text-gray-600 mt-1">Approve or reject patient reviews for doctors</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Approval Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="pending" {{ request('status') == 'pending' || !request('status') ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Search by doctor or patient name..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-gray-700">
                    <span id="selectedCount">0</span> review(s) selected
                </span>
            </div>
            <div class="flex space-x-3">
                <form id="bulkApproveForm" action="{{ route('admin.reviews.bulk-approve') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="review_ids" id="bulkApproveIds">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" onclick="return confirm('Approve selected reviews?');">
                        Approve Selected
                    </button>
                </form>
                <button onclick="showBulkRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Reject Selected
                </button>
                <button onclick="clearSelection()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="review_ids[]" value="{{ $review->id }}" class="review-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500" onchange="updateBulkActions()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $review->doctor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $review->doctor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $review->patient->full_name ?? 'Anonymous' }}</div>
                                @if($review->appointment)
                                    <div class="text-xs text-gray-500">Appt: {{ $review->appointment->appointment_date->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $review->rating }}/5</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $review->comment ?? 'No comment' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $review->approval_status === 'approved' ? 'bg-green-100 text-green-800' : ($review->approval_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($review->approval_status) }}
                                </span>
                                @if($review->approval_status === 'rejected' && $review->rejection_reason)
                                    <p class="text-xs text-red-600 mt-1">{{ Str::limit($review->rejection_reason, 30) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $review->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($review->approval_status === 'pending')
                                    <div class="flex space-x-2">
                                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this review?');">
                                                Approve
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $review->id }})" class="text-red-600 hover:text-red-900">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reviews->links() }}
        </div>
    </div>
</div>

<!-- Reject Modal (Single) -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Review</h3>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason (Optional)</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Enter reason for rejection..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div id="bulkRejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Selected Reviews</h3>
            <form id="bulkRejectForm" method="POST" action="{{ route('admin.reviews.bulk-reject') }}">
                @csrf
                <input type="hidden" name="review_ids" id="bulkRejectIds">
                <div class="mb-4">
                    <label for="bulk_rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason (Optional)</label>
                    <textarea name="rejection_reason" id="bulk_rejection_reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Enter reason for rejection..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideBulkRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedReviews = [];

function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.review-checkbox:checked');
    selectedReviews = Array.from(checkboxes).map(cb => cb.value);
    const count = selectedReviews.length;
    
    document.getElementById('selectedCount').textContent = count;
    
    if (count > 0) {
        document.getElementById('bulkActions').classList.remove('hidden');
        document.getElementById('bulkApproveIds').value = selectedReviews.join(',');
        document.getElementById('bulkRejectIds').value = selectedReviews.join(',');
    } else {
        document.getElementById('bulkActions').classList.add('hidden');
    }
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.review-checkbox');
    const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
    document.getElementById('selectAll').checked = allChecked;
}

function clearSelection() {
    document.querySelectorAll('.review-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function showRejectModal(reviewId) {
    document.getElementById('rejectForm').action = '{{ route("admin.reviews.reject", ":id") }}'.replace(':id', reviewId);
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

function showBulkRejectModal() {
    if (selectedReviews.length === 0) {
        alert('Please select at least one review to reject.');
        return;
    }
    document.getElementById('bulkRejectModal').classList.remove('hidden');
}

function hideBulkRejectModal() {
    document.getElementById('bulkRejectModal').classList.add('hidden');
    document.getElementById('bulk_rejection_reason').value = '';
}
</script>
@endsection

