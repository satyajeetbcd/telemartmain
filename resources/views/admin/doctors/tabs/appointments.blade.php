<!-- Appointments Tab -->
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Appointments</h3>
    </div>

    @if($appointments->count() > 0)
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $appointment->appointment_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $appointment->patient->full_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->appointment_date->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($appointment->status === 'completed' ? 'bg-blue-100 text-blue-800' : ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₹{{ number_format($appointment->consultation_fee ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="text-green-600 hover:text-green-900">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $appointments->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500">No appointments found.</p>
        </div>
    @endif
</div>

