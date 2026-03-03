@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Patients</h2>
            <p class="text-gray-600 mt-1">Manage patient records</p>
        </div>
        <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Add New Patient
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('patients.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Search by name, email, patient ID..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('patients.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $patient->patient_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $patient->full_name }}</div>
                                @if($patient->gender)
                                    <div class="text-xs text-gray-500">{{ ucfirst($patient->gender) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $patient->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $patient->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $patient->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $patient->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $patient->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('patients.show', $patient) }}" class="text-green-600 hover:text-green-900 mr-3">
                                    View
                                </a>
                                <a href="{{ route('patients.edit', $patient) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </a>
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection


