@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">My Appointments</h2>
            <p class="text-gray-600 mt-1">Manage your appointments</p>
        </div>
        @if(Auth::user()->hasRole('Patient'))
        <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Book New Appointment
        </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ Auth::user()->hasRole('Doctor') ? 'Patient' : 'Doctor' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $appointment->appointment_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if(Auth::user()->hasRole('Doctor'))
                                        {{ $appointment->patient->full_name }}
                                    @else
                                        Dr. {{ $appointment->doctor->name }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if(Auth::user()->hasRole('Doctor'))
                                        {{ $appointment->patient->patient_id }}
                                    @else
                                        {{ $appointment->doctor->specialization ?? 'General Practitioner' }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $appointment->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $appointment->status === 'no_show' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-green-600 hover:text-green-900 mr-3">
                                    View
                                </a>
                                @if($appointment->zoom_join_url && in_array($appointment->status, ['confirmed']))
                                    <a href="{{ $appointment->zoom_join_url }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Join Zoom
                                    </a>
                                @endif
                                @if(Auth::user()->hasRole('Doctor') && in_array($appointment->status, ['pending', 'confirmed']))
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900">
                                        Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No appointments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection

