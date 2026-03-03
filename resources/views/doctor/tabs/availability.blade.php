<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Your Availability</h3>
            <p class="text-sm text-gray-600 mt-1">Set your daily and weekly appointment time slots. Only these slots will be available for patients to book.</p>
        </div>
        <a href="{{ route('doctor.availability.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Availability
        </a>
    </div>

    <!-- Weekly Availability -->
    <div class="bg-white border rounded-lg p-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">Weekly Recurring Availability</h4>
        
        @php
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $availabilities = \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)
                ->whereNull('specific_date')
                ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
        @endphp

        @if($availabilities->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>No weekly availability set. Click "Add Availability" to get started.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($days as $day)
                    @php
                        $daySlots = $availabilities->get($day, collect());
                    @endphp
                    <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-gray-900 capitalize">{{ $day }}</h5>
                            @if($daySlots->isEmpty())
                                <span class="text-sm text-gray-500">Not available</span>
                            @endif
                        </div>
                        @if($daySlots->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
                                @foreach($daySlots as $slot)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border {{ $slot->is_available ? 'border-green-200' : 'border-red-200' }}">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}
                                                </span>
                                                @if(!$slot->is_available)
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unavailable</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Duration: {{ $slot->slot_duration }} min
                                                @if($slot->break_duration > 0)
                                                    | Break: {{ $slot->break_duration }} min
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <a href="{{ route('doctor.availability.edit', $slot) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('doctor.availability.destroy', $slot) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this availability slot?');">
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
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Specific Date Overrides -->
    <div class="bg-white border rounded-lg p-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">Specific Date Availability</h4>
        
        @php
            $specificAvailabilities = \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)
                ->whereNotNull('specific_date')
                ->where('specific_date', '>=', now()->toDateString())
                ->orderBy('specific_date')
                ->orderBy('start_time')
                ->get();
        @endphp

        @if($specificAvailabilities->isEmpty())
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">No specific date overrides set.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($specificAvailabilities as $slot)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($slot->specific_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $slot->slot_duration }} min
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $slot->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $slot->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('doctor.availability.edit', $slot) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('doctor.availability.destroy', $slot) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

