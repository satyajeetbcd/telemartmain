@extends('layouts.app')

@section('title', 'Prescriptions')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Prescriptions</h2>
            <p class="text-gray-600 mt-1">{{ Auth::user()->hasRole('Patient') ? 'View your prescriptions' : 'Manage patient prescriptions' }}</p>
        </div>
        @if(Auth::user()->hasRole('Doctor'))
        <a href="{{ route('prescriptions.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Prescription
        </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('prescriptions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Rx #, diagnosis, patient, medicine..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Filter</button>
                <a href="{{ route('prescriptions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rx #</th>
                        @if(!Auth::user()->hasRole('Patient'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        @endif
                        @if(!Auth::user()->hasRole('Doctor'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicines</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prescriptions as $prescription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $prescription->prescription_number }}</div>
                                @if($prescription->diagnosis)
                                <div class="text-xs text-gray-500 truncate max-w-[150px]">{{ $prescription->diagnosis }}</div>
                                @endif
                            </td>
                            @if(!Auth::user()->hasRole('Patient'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $prescription->patient->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $prescription->patient->patient_id }}</div>
                            </td>
                            @endif
                            @if(!Auth::user()->hasRole('Doctor'))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Dr. {{ $prescription->doctor->name }}</div>
                            </td>
                            @endif
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @foreach($prescription->items->take(2) as $item)
                                        <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">{{ $item->medicine_name }}</span>
                                    @endforeach
                                    @if($prescription->items->count() > 2)
                                        <span class="text-xs text-gray-500">+{{ $prescription->items->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $prescription->prescription_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($prescription->valid_until)
                                    <span class="{{ $prescription->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $prescription->valid_until->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$prescription->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('prescriptions.show', $prescription) }}" class="text-green-600 hover:text-green-900 mr-3">View</a>
                                <a href="{{ route('prescriptions.pdf', $prescription) }}" class="text-red-600 hover:text-red-900 mr-3" title="Download PDF">PDF</a>
                                @if(Auth::user()->hasRole('Doctor') && $prescription->doctor_id === Auth::id())
                                    <a href="{{ route('prescriptions.edit', $prescription) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No prescriptions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $prescriptions->links() }}
        </div>
    </div>
</div>
@endsection
