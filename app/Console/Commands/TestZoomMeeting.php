<?php

namespace App\Console\Commands;

use App\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestZoomMeeting extends Command
{
    protected $signature = 'zoom:test';
    protected $description = 'Test Zoom meeting creation, retrieval, and deletion';

    public function handle(ZoomService $zoomService): int
    {
        $this->info('=== Zoom Integration Test ===');
        $this->newLine();

        // Step 1: Check credentials
        $this->info('[1/4] Checking Zoom credentials...');
        $accountId = config('services.zoom.account_id');
        $clientId = config('services.zoom.client_id');
        $clientSecret = config('services.zoom.client_secret');

        if (!$accountId || !$clientId || !$clientSecret) {
            $this->error('Zoom credentials are not configured in .env');
            $this->line('Required: ZOOM_ACCOUNT_ID, ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET');
            return Command::FAILURE;
        }
        $this->info('  ✓ Credentials configured');

        // Step 2: Create a test meeting
        $this->info('[2/4] Creating test meeting...');
        $startTime = Carbon::now()->addMinutes(30);

        $meeting = $zoomService->createMeeting([
            'topic' => 'Test Meeting - Zoom Integration Check',
            'start_time' => $startTime,
            'duration' => 15,
        ]);

        if (!$meeting) {
            $this->error('  ✗ Failed to create meeting. Check logs for details.');
            return Command::FAILURE;
        }

        $meetingId = $meeting['id'];
        $this->info('  ✓ Meeting created successfully');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Meeting ID', $meetingId],
                ['Topic', $meeting['topic'] ?? 'N/A'],
                ['Join URL', $meeting['join_url'] ?? 'N/A'],
                ['Start URL', substr($meeting['start_url'] ?? 'N/A', 0, 80) . '...'],
                ['Password', $meeting['password'] ?? 'N/A'],
                ['Start Time', $meeting['start_time'] ?? 'N/A'],
                ['Duration', ($meeting['duration'] ?? 'N/A') . ' minutes'],
            ]
        );
        $this->newLine();

        // Step 3: Retrieve the meeting
        $this->info('[3/4] Retrieving meeting to verify...');
        $fetched = $zoomService->getMeeting((string) $meetingId);

        if (!$fetched) {
            $this->error('  ✗ Failed to retrieve meeting');
            return Command::FAILURE;
        }
        $this->info('  ✓ Meeting retrieved successfully (ID matches: ' . ($fetched['id'] == $meetingId ? 'yes' : 'no') . ')');

        // Step 4: Delete the test meeting
        $this->info('[4/4] Deleting test meeting...');
        $deleted = $zoomService->deleteMeeting((string) $meetingId);

        if (!$deleted) {
            $this->error('  ✗ Failed to delete meeting');
            return Command::FAILURE;
        }
        $this->info('  ✓ Meeting deleted successfully');

        $this->newLine();
        $this->info('=== All Zoom API operations passed ===');
        $this->info('  ✓ OAuth token generation');
        $this->info('  ✓ Create meeting');
        $this->info('  ✓ Get meeting');
        $this->info('  ✓ Delete meeting');
        $this->newLine();
        $this->info('Zoom integration is working correctly!');

        return Command::SUCCESS;
    }
}
