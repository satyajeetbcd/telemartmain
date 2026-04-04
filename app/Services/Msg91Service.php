<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Service
{
    private string $authKey;
    private string $senderId;
    private string $route;
    private string $baseUrl = 'https://control.msg91.com/api/v5';

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->senderId = config('services.msg91.sender_id');
        $this->route = config('services.msg91.route');
    }

    /**
     * Send a custom SMS message
     */
    public function sendSms(string $phone, string $message): bool
    {
        if (!$this->authKey) {
            Log::error('MSG91 auth key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/flow/", [
                'sender' => $this->senderId,
                'route' => $this->route,
                'mobiles' => $this->formatPhone($phone),
                'body' => $message,
            ]);

            if ($response->successful()) {
                Log::info('MSG91 SMS sent', ['phone' => $this->maskPhone($phone)]);
                return true;
            }

            Log::error('MSG91 SMS failed', [
                'phone' => $this->maskPhone($phone),
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 SMS error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send OTP via MSG91 OTP API
     */
    public function sendOtp(string $phone, int $expiry = 10): ?string
    {
        if (!$this->authKey) {
            Log::error('MSG91 auth key not configured');
            return null;
        }

        $otp = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $formattedPhone = $this->formatPhone($phone);
        $templateId = config('services.msg91.otp_template_id');

        try {
            // Use MSG91 Send OTP API if template ID is configured
            if ($templateId) {
                $response = Http::withHeaders([
                    'authkey' => $this->authKey,
                    'Content-Type' => 'application/json',
                ])->post("{$this->baseUrl}/otp", [
                    'template_id' => $templateId,
                    'mobile' => $formattedPhone,
                    'otp' => $otp,
                    'otp_expiry' => $expiry,
                ]);
            } else {
                // Fallback: send OTP as a custom SMS
                $response = Http::withHeaders([
                    'authkey' => $this->authKey,
                    'Content-Type' => 'application/json',
                ])->post("{$this->baseUrl}/otp", [
                    'mobile' => $formattedPhone,
                    'otp' => $otp,
                    'sender' => $this->senderId,
                    'otp_expiry' => $expiry,
                ]);
            }

            if ($response->successful()) {
                // Store OTP locally for verification
                OtpVerification::updateOrCreate(
                    ['phone' => $formattedPhone],
                    [
                        'otp' => $otp,
                        'expires_at' => now()->addMinutes($expiry),
                        'attempts' => 0,
                        'verified' => false,
                    ]
                );

                Log::info('MSG91 OTP sent', ['phone' => $this->maskPhone($phone)]);
                return $otp;
            }

            Log::error('MSG91 OTP send failed', [
                'phone' => $this->maskPhone($phone),
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('MSG91 OTP error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $phone, string $otp): bool
    {
        $formattedPhone = $this->formatPhone($phone);

        $record = OtpVerification::where('phone', $formattedPhone)
            ->where('verified', false)
            ->first();

        if (!$record) {
            return false;
        }

        // Check max attempts (5)
        if ($record->attempts >= 5) {
            Log::warning('OTP max attempts exceeded', ['phone' => $this->maskPhone($phone)]);
            return false;
        }

        // Check expiry
        if ($record->expires_at->isPast()) {
            return false;
        }

        $record->increment('attempts');

        if ($record->otp === $otp) {
            $record->update(['verified' => true]);
            Log::info('OTP verified', ['phone' => $this->maskPhone($phone)]);
            return true;
        }

        return false;
    }

    /**
     * Resend OTP via MSG91 retry API
     */
    public function resendOtp(string $phone, string $retryType = 'text'): bool
    {
        $formattedPhone = $this->formatPhone($phone);

        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
            ])->get("{$this->baseUrl}/otp/retry", [
                'mobile' => $formattedPhone,
                'retrytype' => $retryType, // 'text' or 'voice'
            ]);

            if ($response->successful()) {
                // Reset attempts on resend
                OtpVerification::where('phone', $formattedPhone)->update(['attempts' => 0]);
                Log::info('MSG91 OTP resent', ['phone' => $this->maskPhone($phone)]);
                return true;
            }

            Log::error('MSG91 OTP resend failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 OTP resend error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send SMS using MSG91 Flow (template-based)
     */
    public function sendFlowSms(string $phone, string $flowId, array $variables = []): bool
    {
        if (!$this->authKey) {
            Log::error('MSG91 auth key not configured');
            return false;
        }

        try {
            $recipients = array_merge(['mobiles' => $this->formatPhone($phone)], $variables);

            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/flow/", [
                'flow_id' => $flowId,
                'sender' => $this->senderId,
                'recipients' => [$recipients],
            ]);

            if ($response->successful()) {
                Log::info('MSG91 Flow SMS sent', ['phone' => $this->maskPhone($phone), 'flow_id' => $flowId]);
                return true;
            }

            Log::error('MSG91 Flow SMS failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('MSG91 Flow SMS error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Format phone number to include country code (default: 91 for India)
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (strlen($phone) === 10) {
            return '91' . $phone;
        }

        return $phone;
    }

    /**
     * Mask phone number for logging
     */
    private function maskPhone(string $phone): string
    {
        return str_repeat('*', max(0, strlen($phone) - 4)) . substr($phone, -4);
    }
}
