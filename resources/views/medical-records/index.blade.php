@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Medical Records</h2>
            <p class="text-gray-600 mt-1">{{ Auth::user()->hasRole('Patient') ? 'View your medical records' : 'Manage patient medical records' }}</p>
        </div>
        @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
        <a href="{{ route('medical-records.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Record
        </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('medical-records.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Record #, title, patient..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="record_type" class="block text-sm font-medium text-gray-700 mb-1">Record Type</label>
                <select name="record_type" id="record_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Types</option>
                    @foreach(\App\Models\MedicalRecord::getRecordTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('record_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Filter</button>
                <a href="{{ route('medical-records.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </div>

    <!-- Records Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record #</th>
                        @if(!Auth::user()->hasRole('Patient'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        @endif
                        @if(!Auth::user()->hasRole('Doctor'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $record->record_number }}</div>
                            </td>
                            @if(!Auth::user()->hasRole('Patient'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $record->patient->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $record->patient->patient_id }}</div>
                            </td>
                            @endif
                            @if(!Auth::user()->hasRole('Doctor'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Dr. {{ $record->doctor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $record->doctor->specialization ?? 'General' }}</div>
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$record->record_type] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ \App\Models\MedicalRecord::getRecordTypes()[$record->record_type] ?? ucfirst($record->record_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $record->title }}</div>
                                @if($record->diagnosis)
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $record->diagnosis }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $record->record_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('medical-records.show', $record) }}" class="text-green-600 hover:text-green-900 mr-3">View</a>
                                @if(Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Administrator'))
                                    @if(!Auth::user()->hasRole('Doctor') || $record->doctor_id === Auth::id())
                                    <a href="{{ route('medical-records.edit', $record) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No medical records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
