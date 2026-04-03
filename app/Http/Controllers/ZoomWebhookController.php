<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\ZoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $event = $request->input('event');

        // Handle URL validation challenge (no signature check needed)
        if ($event === 'endpoint.url_validation') {
            $plainToken = $request->input('payload.plainToken');
            $response = ZoomService::generateValidationResponse($plainToken);
            return response()->json($response);
        }

        // Verify webhook signature
        if (!ZoomService::verifyWebhookSignature($request)) {
            Log::warning('Zoom webhook signature verification failed');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->input('payload', []);

        Log::info('Zoom webhook received', ['event' => $event]);

        try {
            match ($event) {
                'meeting.started' => $this->handleMeetingStarted($payload),
                'meeting.ended' => $this->handleMeetingEnded($payload),
                'meeting.participant_joined' => $this->handleParticipantJoined($payload),
                'meeting.participant_left' => $this->handleParticipantLeft($payload),
                default => Log::info('Unhandled Zoom webhook event', ['event' => $event]),
            };
        } catch (\Exception $e) {
            Log::error('Zoom webhook processing error', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    private function handleMeetingStarted(array $payload): void
    {
        $meetingId = (string) ($payload['object']['id'] ?? '');
        $appointment = Appointment::where('zoom_meeting_id', $meetingId)->first();

        if (!$appointment) {
            Log::info('Zoom meeting.started: no matching appointment', ['meeting_id' => $meetingId]);
            return;
        }

        $appointment->update([
            'zoom_meeting_status' => 'started',
            'zoom_meeting_started_at' => now(),
        ]);

        Log::info('Zoom meeting started', ['appointment_id' => $appointment->id, 'meeting_id' => $meetingId]);
    }

    private function handleMeetingEnded(array $payload): void
    {
        $meetingId = (string) ($payload['object']['id'] ?? '');
        $appointment = Appointment::where('zoom_meeting_id', $meetingId)->first();

        if (!$appointment) {
            Log::info('Zoom meeting.ended: no matching appointment', ['meeting_id' => $meetingId]);
            return;
        }

        $updates = [
            'zoom_meeting_status' => 'ended',
            'zoom_meeting_ended_at' => now(),
        ];

        // Compute duration if meeting was started
        if ($appointment->zoom_meeting_started_at) {
            $updates['zoom_meeting_duration_minutes'] = (int) $appointment->zoom_meeting_started_at->diffInMinutes(now());
        }

        // Auto-complete appointment if it was confirmed
        if ($appointment->status === 'confirmed') {
            $updates['status'] = 'completed';
            $updates['completed_at'] = now();
        }

        $appointment->update($updates);

        Log::info('Zoom meeting ended', ['appointment_id' => $appointment->id, 'meeting_id' => $meetingId]);
    }

    private function handleParticipantJoined(array $payload): void
    {
        $meetingId = (string) ($payload['object']['id'] ?? '');
        $participantEmail = $payload['object']['participant']['email'] ?? null;

        $appointment = Appointment::with(['doctor', 'patient'])->where('zoom_meeting_id', $meetingId)->first();

        if (!$appointment) {
            Log::info('Zoom participant_joined: no matching appointment', ['meeting_id' => $meetingId]);
            return;
        }

        if ($participantEmail && $appointment->doctor && $participantEmail === $appointment->doctor->email) {
            $appointment->update(['zoom_participant_doctor_joined_at' => now()]);
            Log::info('Doctor joined Zoom meeting', ['appointment_id' => $appointment->id]);
        } elseif ($participantEmail && $appointment->patient && $participantEmail === $appointment->patient->email) {
            $appointment->update(['zoom_participant_patient_joined_at' => now()]);
            Log::info('Patient joined Zoom meeting', ['appointment_id' => $appointment->id]);
        } else {
            Log::info('Unknown participant joined Zoom meeting', [
                'appointment_id' => $appointment->id,
                'participant_email' => $participantEmail,
            ]);
        }
    }

    private function handleParticipantLeft(array $payload): void
    {
        $meetingId = (string) ($payload['object']['id'] ?? '');
        $participantEmail = $payload['object']['participant']['email'] ?? null;

        Log::info('Zoom participant left', [
            'meeting_id' => $meetingId,
            'participant_email' => $participantEmail,
        ]);
    }
}
