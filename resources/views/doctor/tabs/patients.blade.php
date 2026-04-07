<!-- Patients Tab (Appointment-Centric) -->
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">My Patients</h3>
        <p class="text-sm text-gray-600">Patients who have booked appointments with you</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($patientAppointments->count() > 0)
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($patientAppointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->patient->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $appointment->patient->patient_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->appointment_date->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ date('h:i A', strtotime($appointment->appointment_time)) }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $appointment->reason ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($appointment->status === 'completed' ? 'bg-blue-100 text-blue-800' : ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : ($appointment->status === 'no_show' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        {{-- Approve Button (pending only) --}}
                                        @if($appointment->status === 'pending')
                                            <form action="{{ route('appointments.update', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <input type="hidden" name="_redirect" value="{{ route('doctor.profile', ['tab' => 'patients']) }}">
                                                <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Decline Button (pending only) --}}
                                        @if($appointment->status === 'pending')
                                            <form action="{{ route('appointments.update', $appointment) }}" method="POST" class="inline"
                                                  onsubmit="let r = prompt('Reason for declining this appointment?'); if(!r){return false;} this.querySelector('[name=cancellation_reason]').value=r;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <input type="hidden" name="cancellation_reason" value="">
                                                <input type="hidden" name="_redirect" value="{{ route('doctor.profile', ['tab' => 'patients']) }}">
                                                <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 transition-colors">
                                                    Decline
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit Time (pending/confirmed only) --}}
                                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                                            <a href="{{ route('appointments.edit', $appointment) }}" class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                                Edit Time
                                            </a>
                                        @endif

                                        {{-- View --}}
                                        <a href="{{ route('appointments.show', $appointment) }}" class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                                            View
                                        </a>

                                        {{-- Start Call (confirmed with Zoom) --}}
                                        @if($appointment->zoom_start_url && $appointment->status === 'confirmed')
                                            <a href="{{ $appointment->zoom_start_url }}" target="_blank"
                                                title="Start video call"
                                                class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Call
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $patientAppointments->appends(['tab' => 'patients'])->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500">No patients found. You haven't received any appointment bookings yet.</p>
        </div>
    @endif
</div>
