@extends('layouts.app')

@section('title', 'Doctors List')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Doctors List</h2>
            <p class="text-gray-600 mt-1">Manage doctors and view KYC status</p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('doctors.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Search by name, email, specialization..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending_kyc" {{ request('status') == 'pending_kyc' ? 'selected' : '' }}>Pending KYC</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                </select>
            </div>
            <div>
                <label for="kyc_status" class="block text-sm font-medium text-gray-700 mb-1">KYC Status</label>
                <select name="kyc_status" id="kyc_status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All KYC Statuses</option>
                    <option value="approved" {{ request('kyc_status') == 'approved' ? 'selected' : '' }}>KYC Approved</option>
                    <option value="pending" {{ request('kyc_status') == 'pending' ? 'selected' : '' }}>KYC Pending</option>
                    <option value="rejected" {{ request('kyc_status') == 'rejected' ? 'selected' : '' }}>KYC Rejected</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('doctors.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KYC Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KYC Documents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                                        @if($doctor->profile_image)
                                            <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="{{ $doctor->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <span class="text-green-600 font-medium text-sm">{{ substr($doctor->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $doctor->email }}</div>
                                        @if($doctor->phone)
                                            <div class="text-xs text-gray-500">{{ $doctor->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $doctor->specialization ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($doctor->review_count > 0)
                                    <div class="flex items-center space-x-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($doctor->average_rating, 1) }}</span>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= round($doctor->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500">({{ $doctor->review_count }})</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No reviews</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $doctor->status === 'active' ? 'bg-green-100 text-green-800' : ($doctor->status === 'inactive' ? 'bg-red-100 text-red-800' : ($doctor->status === 'pending_approval' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $doctor->status)) }}
                                </span>
                                @if($doctor->status === 'pending_approval')
                                    <p class="text-xs text-orange-600 mt-1">Profile changes pending</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $kycStatus = $doctor->kyc_status['overall_status'] ?? 'pending';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $kycStatus === 'approved' ? 'bg-green-100 text-green-800' : ($kycStatus === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($kycStatus) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="space-y-1">
                                    <div class="flex items-center">
                                        <span class="text-xs {{ ($doctor->kyc_status['aadhar_front']['status'] ?? 'not_uploaded') === 'approved' ? 'text-green-600' : 'text-gray-500' }}">
                                            Aadhar Front: {{ ucfirst($doctor->kyc_status['aadhar_front']['status'] ?? 'not_uploaded') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs {{ ($doctor->kyc_status['aadhar_back']['status'] ?? 'not_uploaded') === 'approved' ? 'text-green-600' : 'text-gray-500' }}">
                                            Aadhar Back: {{ ucfirst($doctor->kyc_status['aadhar_back']['status'] ?? 'not_uploaded') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs {{ ($doctor->kyc_status['degrees']['approved_count'] ?? 0) > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                            Degrees: {{ $doctor->kyc_status['degrees']['approved_count'] ?? 0 }} approved
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="text-green-600 hover:text-green-900" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'tab' => 'profile']) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No doctors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $doctors->links() }}
        </div>
    </div>
</div>
@endsection

