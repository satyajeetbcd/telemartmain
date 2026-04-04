<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Msg91Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * Send OTP to a phone number
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'purpose' => 'nullable|string|in:login,register,verify_phone',
        ]);

        $sms = new Msg91Service();
        $otp = $sms->sendOtp($request->phone);

        if ($otp) {
            return response()->json([
                'message' => 'OTP sent successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to send OTP. Please try again.',
        ], 500);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'otp' => 'required|string|size:4',
        ]);

        $sms = new Msg91Service();
        $verified = $sms->verifyOtp($request->phone, $request->otp);

        if ($verified) {
            return response()->json([
                'message' => 'OTP verified successfully.',
                'verified' => true,
            ]);
        }

        return response()->json([
            'message' => 'Invalid or expired OTP.',
            'verified' => false,
        ], 422);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'type' => 'nullable|string|in:text,voice',
        ]);

        $sms = new Msg91Service();
        $sent = $sms->resendOtp($request->phone, $request->type ?? 'text');

        if ($sent) {
            return response()->json([
                'message' => 'OTP resent successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to resend OTP. Please try again.',
        ], 500);
    }

    /**
     * Send a custom SMS (for admin/internal use, requires auth)
     */
    public function sendSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'message' => 'required|string|max:1000',
        ]);

        $sms = new Msg91Service();
        $sent = $sms->sendSms($request->phone, $request->message);

        if ($sent) {
            return response()->json([
                'message' => 'SMS sent successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to send SMS. Please try again.',
        ], 500);
    }

    /**
     * Send SMS using a Flow template (for admin/internal use, requires auth)
     */
    public function sendFlowSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:15',
            'flow_id' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        $sms = new Msg91Service();
        $sent = $sms->sendFlowSms($request->phone, $request->flow_id, $request->variables ?? []);

        if ($sent) {
            return response()->json([
                'message' => 'SMS sent successfully.',
            ]);
        }

        return response()->json([
            'message' => 'Failed to send SMS. Please try again.',
        ], 500);
    }
}
