@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Activity Logs</h2>
            <p class="text-sm text-gray-600">Track all system activities and changes</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Search in values..." 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <label for="event" class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                <select name="event" id="event" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">All</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                <select name="user_type" id="user_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">All</option>
                    @foreach($userTypes as $type)
                        <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="auditable_type" class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                <select name="auditable_type" id="auditable_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <option value="">All</option>
                    @foreach($auditableTypes as $type)
                        <option value="{{ $type }}" {{ request('auditable_type') == $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end space-x-2">
                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Clear
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $activity->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $activity->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $activity->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($activity->event, ['created', 'updated', 'deleted']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($activity->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->user)
                                    <div class="text-sm text-gray-900">{{ $activity->user->name ?? $activity->user->email ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ class_basename($activity->user_type) }}</div>
                                @else
                                    <span class="text-sm text-gray-400">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->auditable)
                                    <div class="text-sm text-gray-900">
                                        @if(method_exists($activity->auditable, 'name'))
                                            {{ $activity->auditable->name }}
                                        @elseif(method_exists($activity->auditable, 'email'))
                                            {{ $activity->auditable->email }}
                                        @else
                                            {{ class_basename($activity->auditable_type) }} #{{ $activity->auditable_id }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ class_basename($activity->auditable_type) }}</div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->ip_address ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $activity->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('activity-logs.show', $activity) }}" class="text-green-600 hover:text-green-900">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection
