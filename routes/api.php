<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiPatientController;
use App\Http\Controllers\Api\ApiStatsController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\RazorpayPaymentController;
use App\Http\Controllers\ZoomWebhookController;

// Zoom webhook (verified by HMAC signature, no auth middleware)
Route::post('/zoom/webhook', [ZoomWebhookController::class, 'handle']);

// Razorpay webhook (verified by HMAC signature, no auth middleware)
Route::post('/payments/webhook', [RazorpayPaymentController::class, 'webhook']);

// Authenticated user (for backend/admin usage)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API endpoints
Route::post('/patient/login', [ApiAuthController::class, 'login']);
Route::post('/patient/register', [ApiAuthController::class, 'register']);
Route::get('/stats', [ApiStatsController::class, 'index']);
Route::get('/doctors', [ApiStatsController::class, 'doctors']);

// SMS & OTP endpoints (public - for login/register flows)
Route::post('/sms/send-otp', [SmsController::class, 'sendOtp']);
Route::post('/sms/verify-otp', [SmsController::class, 'verifyOtp']);
Route::post('/sms/resend-otp', [SmsController::class, 'resendOtp']);

// Admin SMS endpoints (requires Sanctum auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sms/send', [SmsController::class, 'sendSms']);
    Route::post('/sms/send-flow', [SmsController::class, 'sendFlowSms']);
});

// Protected patient endpoints (uses patient token, NOT sanctum)
Route::middleware('patient.token')->group(function () {
    Route::post('/patient/logout', [ApiAuthController::class, 'logout']);
    Route::get('/patient/profile', [ApiPatientController::class, 'profile']);
    Route::put('/patient/profile', [ApiPatientController::class, 'updateProfile']);
    Route::get('/patient/appointments', [ApiPatientController::class, 'appointments']);
    Route::get('/patient/doctors', [ApiPatientController::class, 'doctors']);
    Route::get('/patient/doctors/{doctor}/slots', [ApiPatientController::class, 'doctorSlots']);
    Route::post('/patient/appointments', [ApiPatientController::class, 'bookAppointment']);
    Route::get('/patient/medical-records', [ApiPatientController::class, 'medicalRecords']);
    Route::post('/patient/medical-records', [ApiPatientController::class, 'storeMedicalRecord']);
    Route::get('/patient/medical-records/{record}/download/{index}', [ApiPatientController::class, 'downloadAttachment']);
    Route::post('/patient/consultations', [ApiPatientController::class, 'storeConsultation']);
    Route::get('/patient/consultations', [ApiPatientController::class, 'consultations']);
    Route::get('/patient/consultations/{id}', [ApiPatientController::class, 'showConsultation']);
    Route::put('/patient/consultations/{id}', [ApiPatientController::class, 'updateConsultation']);
    Route::get('/patient/appointments/{id}/invoice-pdf', [ApiPatientController::class, 'invoicePdf']);
    Route::get('/patient/prescriptions', [ApiPatientController::class, 'prescriptions']);
    Route::get('/patient/prescriptions/{id}/pdf', [ApiPatientController::class, 'prescriptionPdf']);
    Route::get('/patient/dashboard-stats', [ApiPatientController::class, 'dashboardStats']);

    // Razorpay payments
    Route::post('/patient/payments/create-order', [RazorpayPaymentController::class, 'createOrder']);
    Route::post('/patient/payments/verify', [RazorpayPaymentController::class, 'verifyPayment']);
    Route::get('/patient/payments', [RazorpayPaymentController::class, 'index']);
});
