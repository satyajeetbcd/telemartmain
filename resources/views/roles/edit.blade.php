@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Role</h1>
        <p class="mt-1 text-sm text-gray-600">Update role and permissions</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('roles.update', $role) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $role->name) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ old('description', $role->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="space-y-4 max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2 capitalize">{{ $group }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('roles.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

