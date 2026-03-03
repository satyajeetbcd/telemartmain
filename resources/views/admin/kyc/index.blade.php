@extends('layouts.app')

@section('title', 'Doctor KYC Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Doctor KYC Management</h2>
            <p class="text-gray-600 mt-1">Review and approve doctor verification documents</p>
        </div>
        @if($pendingCount > 0)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded-md">
            <span class="font-semibold">{{ $pendingCount }}</span> documents pending review
        </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.kyc.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                <select name="document_type" id="document_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Types</option>
                    <option value="aadhar_front" {{ request('document_type') == 'aadhar_front' ? 'selected' : '' }}>Aadhar Card (Front)</option>
                    <option value="aadhar_back" {{ request('document_type') == 'aadhar_back' ? 'selected' : '' }}>Aadhar Card (Back)</option>
                    <option value="degree" {{ request('document_type') == 'degree' ? 'selected' : '' }}>Degree Certificate</option>
                    <option value="pan" {{ request('document_type') == 'pan' ? 'selected' : '' }}>PAN Card</option>
                </select>
            </div>
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                <select name="doctor_id" id="doctor_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('admin.kyc.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $document->doctor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $document->doctor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $document->document_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->document_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $document->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : ($document->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($document->status) }}
                                </span>
                                @if($document->status === 'approved' && $document->approver)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Approved by: {{ $document->approver->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.kyc.show', $document) }}" class="text-green-600 hover:text-green-900 mr-3">
                                    Review
                                </a>
                                <a href="{{ route('doctor.kyc.download', $document) }}" class="text-blue-600 hover:text-blue-900" target="_blank">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No documents found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection

