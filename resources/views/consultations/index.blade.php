@extends('layouts.app')

@section('title', 'Consultations')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Consultations</h2>
            <p class="text-gray-600 mt-1">All patient consultation submissions</p>
        </div>
        <a href="{{ route('consultations.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Consultation
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($consultations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chief Complaints</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($consultations as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $c->consultation_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <a href="{{ route('patients.show', $c->patient_id) }}" class="text-green-600 hover:text-green-700">
                                {{ $c->patient->full_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($c->chief_complaints)
                                @foreach(array_slice($c->chief_complaints, 0, 3) as $cc)
                                    <span class="inline-block bg-green-50 text-green-700 text-xs px-2 py-0.5 rounded mr-1">{{ $cc['name'] ?? '' }}</span>
                                @endforeach
                                @if(count($c->chief_complaints) > 3)
                                    <span class="text-xs text-gray-400">+{{ count($c->chief_complaints) - 3 }} more</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $c->doctor ? 'Dr. ' . $c->doctor->name : 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_review' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $colors[$c->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $c->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $c->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('consultations.show', $c) }}" class="text-green-600 hover:text-green-800 mr-2">View</a>
                            <a href="{{ route('consultations.edit', $c) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            {{ $consultations->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No consultations yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection
