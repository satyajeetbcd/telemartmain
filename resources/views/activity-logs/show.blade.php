@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div>
    <div class="mb-6">
        <a href="{{ route('activity-logs.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium mb-4 inline-block">
            ← Back to Activity Logs
        </a>
        <h2 class="text-xl font-semibold text-gray-900">Activity Log Details</h2>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Event</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $activityLog->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $activityLog->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $activityLog->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($activityLog->event) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($activityLog->user)
                            {{ $activityLog->user->name ?? $activityLog->user->email ?? 'N/A' }}
                            <span class="text-gray-500">({{ class_basename($activityLog->user_type) }})</span>
                        @else
                            <span class="text-gray-400">System</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Model</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($activityLog->auditable)
                            @if(method_exists($activityLog->auditable, 'name'))
                                {{ $activityLog->auditable->name }}
                            @elseif(method_exists($activityLog->auditable, 'email'))
                                {{ $activityLog->auditable->email }}
                            @else
                                {{ class_basename($activityLog->auditable_type) }} #{{ $activityLog->auditable_id }}
                            @endif
                            <span class="text-gray-500">({{ class_basename($activityLog->auditable_type) }})</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $activityLog->created_at->format('F d, Y h:i A') }}
                    </dd>
                </div>
                @if($activityLog->ip_address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activityLog->ip_address }}</dd>
                </div>
                @endif
                @if($activityLog->user_agent)
                <div>
                    <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activityLog->user_agent }}</dd>
                </div>
                @endif
                @if($activityLog->url)
                <div>
                    <dt class="text-sm font-medium text-gray-500">URL</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $activityLog->url }}</dd>
                </div>
                @endif
            </dl>

            @if($activityLog->old_values && count($activityLog->old_values) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Old Values</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            @foreach($activityLog->old_values as $key => $value)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">{{ $key }}</dt>
                                    <dd class="mt-1 text-xs text-gray-900">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif

            @if($activityLog->new_values && count($activityLog->new_values) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">New Values</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            @foreach($activityLog->new_values as $key => $value)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500">{{ $key }}</dt>
                                    <dd class="mt-1 text-xs text-gray-900">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif

            @if($activityLog->old_values && $activityLog->new_values)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Changes Comparison</h3>
                    <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Field</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Old Value</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $allKeys = array_unique(array_merge(array_keys($activityLog->old_values ?? []), array_keys($activityLog->new_values ?? [])));
                                @endphp
                                @foreach($allKeys as $key)
                                    <tr>
                                        <td class="px-4 py-2 text-xs font-medium text-gray-900">{{ $key }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-600">
                                            {{ isset($activityLog->old_values[$key]) ? (is_array($activityLog->old_values[$key]) ? json_encode($activityLog->old_values[$key]) : $activityLog->old_values[$key]) : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-600">
                                            {{ isset($activityLog->new_values[$key]) ? (is_array($activityLog->new_values[$key]) ? json_encode($activityLog->new_values[$key]) : $activityLog->new_values[$key]) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
