<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ZoomService
{
    private $accountId;
    private $clientId;
    private $clientSecret;
    private $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    /**
     * Get OAuth access token
     */
    private function getAccessToken(): ?string
    {
        if (!$this->accountId || !$this->clientId || !$this->clientSecret) {
            Log::error('Zoom credentials not configured');
            return null;
        }

        try {
            // Server-to-Server OAuth endpoint
            $response = Http::asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ])->withBasicAuth($this->clientId, $this->clientSecret);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('Zoom token request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom token error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a Zoom meeting
     */
    public function createMeeting(array $data): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $meetingData = [
            'topic' => $data['topic'] ?? 'Medical Consultation',
            'type' => 2, // Scheduled meeting
            'start_time' => $this->formatDateTime($data['start_time']),
            'duration' => $data['duration'] ?? 30, // minutes
            'timezone' => $data['timezone'] ?? config('app.timezone', 'UTC'),
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => false,
                'waiting_room' => false,
                'approval_type' => 0, // Automatically approve
                'audio' => 'both',
                'auto_recording' => 'none',
            ],
        ];

        // Add password if provided
        if (isset($data['password'])) {
            $meetingData['password'] = $data['password'];
        }

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/users/me/meetings", $meetingData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom meeting creation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom meeting creation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update a Zoom meeting
     */
    public function updateMeeting(string $meetingId, array $data): bool
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $updateData = [];
        if (isset($data['topic'])) {
            $updateData['topic'] = $data['topic'];
        }
        if (isset($data['start_time'])) {
            $updateData['start_time'] = $this->formatDateTime($data['start_time']);
        }
        if (isset($data['duration'])) {
            $updateData['duration'] = $data['duration'];
        }

        try {
            $response = Http::withToken($token)
                ->patch("{$this->baseUrl}/meetings/{$meetingId}", $updateData);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Zoom meeting update error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting(string $meetingId): bool
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        try {
            $response = Http::withToken($token)
                ->delete("{$this->baseUrl}/meetings/{$meetingId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Zoom meeting deletion error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get meeting details
     */
    public function getMeeting(string $meetingId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/meetings/{$meetingId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Zoom meeting fetch error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Format datetime for Zoom API
     */
    private function formatDateTime($datetime): string
    {
        if ($datetime instanceof Carbon) {
            return $datetime->format('Y-m-d\TH:i:s');
        }

        if (is_string($datetime)) {
            return Carbon::parse($datetime)->format('Y-m-d\TH:i:s');
        }

        return Carbon::now()->format('Y-m-d\TH:i:s');
    }
}

