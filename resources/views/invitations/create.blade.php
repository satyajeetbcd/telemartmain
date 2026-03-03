@extends('layouts.app')

@section('title', 'Send Invitation')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Send Invitation</h1>
        <p class="mt-1 text-sm text-gray-600">Invite a new user to join the system</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('invitations.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        placeholder="user@example.com">
                    <p class="mt-1 text-sm text-gray-500">The invitation link will be sent to this email address.</p>
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role_id" id="role_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="expires_in_days" class="block text-sm font-medium text-gray-700">Expires In (Days)</label>
                    <input type="number" name="expires_in_days" id="expires_in_days" min="1" max="30" value="{{ old('expires_in_days', 7) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Number of days until the invitation expires (1-30 days).</p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('invitations.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium">
                    Send Invitation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

