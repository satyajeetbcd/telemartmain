@extends('layouts.app')

@section('title', 'Invitations')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Invitations</h1>
            <p class="mt-1 text-sm text-gray-600">Manage user invitations</p>
        </div>
        <a href="{{ route('invitations.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Send Invitation
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($invitations as $invitation)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $invitation->email }}</div>
                                    @if($invitation->accepted_at)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Accepted
                                        </span>
                                    @elseif($invitation->expires_at->isPast())
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Expired
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    Role: <strong>{{ $invitation->role->name ?? 'N/A' }}</strong> | 
                                    Invited by: {{ $invitation->inviter->name }} | 
                                    Expires: {{ $invitation->expires_at->format('M d, Y') }}
                                </div>
                                @if(!$invitation->accepted_at && !$invitation->expires_at->isPast())
                                    <div class="mt-2">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ route('invitations.accept', $invitation->token) }}</code>
                                    </div>
                                @endif
                            </div>
                            @if(!$invitation->accepted_at)
                                <form action="{{ route('invitations.destroy', $invitation) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invitation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    No invitations found. <a href="{{ route('invitations.create') }}" class="text-green-600 hover:text-green-700">Send one</a>
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $invitations->links() }}
    </div>
</div>
@endsection

