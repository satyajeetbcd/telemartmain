@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <p class="mb-6 text-sm text-gray-600">Welcome back, {{ $user->name }}!</p>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_users'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Roles</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_roles'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Invitations</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_invitations'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Invitations</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $stats['pending_invitations'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <a href="{{ route('users.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-green-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Create User</p>
                        <p class="text-sm text-gray-500 truncate">Add a new user</p>
                    </div>
                </a>

                <a href="{{ route('invitations.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-green-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Send Invitation</p>
                        <p class="text-sm text-gray-500 truncate">Invite a new user</p>
                    </div>
                </a>

                <a href="{{ route('roles.create') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-green-500 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Create Role</p>
                        <p class="text-sm text-gray-500 truncate">Add a new role</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

