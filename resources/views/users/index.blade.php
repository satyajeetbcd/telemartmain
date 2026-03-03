@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <p class="mt-1 text-sm text-gray-600">Manage system users</p>
        </div>
        <a href="{{ route('users.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create User
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($users as $user)
                <li>
                    <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                @if($user->phone)
                                    <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                                @endif
                                <div class="mt-1">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('users.edit', $user) }}" class="text-green-600 hover:text-green-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    No users found. <a href="{{ route('users.create') }}" class="text-green-600 hover:text-green-700">Create one</a>
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection

