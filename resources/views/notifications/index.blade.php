@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Notifications</h2>
            <p class="text-sm text-gray-500 mt-0.5">Your latest alerts and updates</p>
        </div>
        @if(Auth::user()->notifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-green-600 hover:text-green-800 font-medium">
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
            @foreach($notifications as $notification)
            @php
                $data    = $notification->data;
                $isRead  = $notification->read_at !== null;
                $type    = $data['type'] ?? 'notification';
                $isZoom  = isset($data['zoom_join_url']) || isset($data['zoom_start_url']);
            @endphp
            <div class="flex items-start gap-4 px-5 py-4 {{ $isRead ? 'opacity-70' : 'bg-blue-50' }}">

                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    @if($type === 'appointment_confirmed' || $type === 'payment_received')
                        <div class="w-9 h-9 rounded-full flex items-center justify-center {{ $isRead ? 'bg-gray-100' : 'bg-green-100' }}">
                            <svg class="w-5 h-5 {{ $isRead ? 'text-gray-500' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $data['title'] ?? 'Notification' }}
                        @if(!$isRead)
                            <span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full align-middle"></span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $data['message'] ?? '' }}</p>
                    @if(isset($data['appointment_number']))
                        <p class="text-xs text-gray-400 mt-1">Apt # {{ $data['appointment_number'] }}</p>
                    @endif

                    {{-- Zoom quick-launch --}}
                    @if(isset($data['zoom_start_url']))
                        <a href="{{ $data['zoom_start_url'] }}" target="_blank"
                            class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-green-600 text-white text-xs rounded-full hover:bg-green-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Start Call
                        </a>
                    @elseif(isset($data['zoom_join_url']))
                        <a href="{{ $data['zoom_join_url'] }}" target="_blank"
                            class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded-full hover:bg-blue-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Join Call
                        </a>
                    @endif

                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>

                {{-- Actions --}}
                <div class="shrink-0 flex items-center gap-2">
                    @if(isset($data['url']))
                        <a href="{{ route('notifications.read', $notification->id) }}"
                            class="text-xs text-green-600 hover:text-green-800 font-medium">
                            View
                        </a>
                    @endif
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-red-500">✕</button>
                    </form>
                </div>

            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>

    @else
        <div class="bg-white rounded-lg shadow text-center py-16">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="text-gray-500 font-medium">No notifications yet</p>
            <p class="text-sm text-gray-400 mt-1">You'll see alerts here when appointments are confirmed.</p>
        </div>
    @endif
</div>
@endsection
