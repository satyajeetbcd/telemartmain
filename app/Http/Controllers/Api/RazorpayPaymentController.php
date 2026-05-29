<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayPaymentController extends Controller
{
    protected function getPatient(Request $request): Patient
    {
        return $request->patient;
    }

    protected function api(): Api
    {
        return new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret'),
        );
    }

    /**
     * Create (or reuse) a Razorpay order for an appointment and record a payment row.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'appointment_id' => 'required|integer',
        ]);

        $patient = $this->getPatient($request);

        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        if ($appointment->payment_status === 'paid') {
            return response()->json(['message' => 'This appointment is already paid.'], 422);
        }

        if (!$appointment->consultation_fee || (float) $appointment->consultation_fee <= 0) {
            return response()->json(['message' => 'No consultation fee is set for this appointment.'], 422);
        }

        $amountPaise = (int) round((float) $appointment->consultation_fee * 100);

        // Reuse an existing un-paid order for this appointment if the amount is unchanged.
        $payment = Payment::where('appointment_id', $appointment->id)
            ->where('status', 'created')
            ->where('amount', $appointment->consultation_fee)
            ->latest()
            ->first();

        try {
            if (!$payment) {
                $order = $this->api()->order->create([
                    'receipt' => $appointment->appointment_number,
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'notes' => [
                        'appointment_id' => (string) $appointment->id,
                        'appointment_number' => $appointment->appointment_number,
                        'patient_id' => (string) $patient->id,
                    ],
                ]);

                $payment = Payment::create([
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patient->id,
                    'razorpay_order_id' => $order['id'],
                    'amount' => $appointment->consultation_fee,
                    'currency' => 'INR',
                    'status' => 'created',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Razorpay order creation failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Could not initiate payment. Please try again.'], 502);
        }

        return response()->json([
            'key_id' => config('services.razorpay.key_id'),
            'order_id' => $payment->razorpay_order_id,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'name' => config('app.name'),
            'description' => 'Consultation fee — ' . $appointment->appointment_number,
            'appointment_number' => $appointment->appointment_number,
            'prefill' => [
                'name' => trim($patient->first_name . ' ' . $patient->last_name),
                'email' => $patient->email,
                'contact' => $patient->phone,
            ],
        ]);
    }

    /**
     * Verify the inline checkout signature and mark the payment/appointment as paid.
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $patient = $this->getPatient($request);

        $payment = Payment::where('razorpay_order_id', $data['razorpay_order_id'])
            ->where('patient_id', $patient->id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment record not found.'], 404);
        }

        // Already reconciled (e.g. webhook arrived first) — treat as success, idempotent.
        if ($payment->status === 'paid') {
            return response()->json(['message' => 'Payment already confirmed.', 'status' => 'paid']);
        }

        try {
            $this->api()->utility->verifyPaymentSignature($data);
        } catch (SignatureVerificationError $e) {
            $payment->update([
                'status' => 'failed',
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'error_description' => 'Signature verification failed.',
            ]);
            Log::warning('Razorpay payment signature verification failed', [
                'order_id' => $data['razorpay_order_id'],
            ]);
            return response()->json(['message' => 'Payment verification failed.'], 400);
        }

        $payment->update([
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'],
            'status' => 'paid',
        ]);

        $payment->appointment?->update(['payment_status' => 'paid']);

        return response()->json(['message' => 'Payment successful.', 'status' => 'paid']);
    }

    /**
     * Handle Razorpay webhooks (no auth). Source of truth for reconciliation.
     */
    public function webhook(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');
        $eventId = $request->header('X-Razorpay-Event-Id');
        $secret = config('services.razorpay.webhook_secret');

        $signatureValid = false;
        if ($secret && $signature) {
            try {
                $this->api()->utility->verifyWebhookSignature($rawBody, $signature, $secret);
                $signatureValid = true;
            } catch (SignatureVerificationError $e) {
                $signatureValid = false;
            }
        }

        $data = json_decode($rawBody, true) ?: [];
        $event = $data['event'] ?? 'unknown';

        // Idempotency: skip if we've already recorded this delivery.
        if ($eventId && PaymentEvent::where('razorpay_event_id', $eventId)->exists()) {
            return response()->json(['status' => 'duplicate']);
        }

        if (!$signatureValid) {
            PaymentEvent::create([
                'razorpay_event_id' => $eventId,
                'event' => $event,
                'signature_valid' => false,
                'payload' => $data,
            ]);
            Log::warning('Razorpay webhook signature verification failed', ['event' => $event]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payment = $this->resolvePaymentFromWebhook($event, $data);

        PaymentEvent::create([
            'payment_id' => $payment?->id,
            'razorpay_event_id' => $eventId,
            'event' => $event,
            'signature_valid' => true,
            'payload' => $data,
        ]);

        if ($payment) {
            try {
                $this->applyWebhookEvent($payment, $event, $data);
            } catch (\Throwable $e) {
                Log::error('Razorpay webhook processing error', [
                    'event' => $event,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Patient-scoped payment transaction log.
     */
    public function index(Request $request): JsonResponse
    {
        $patient = $this->getPatient($request);

        $payments = Payment::with('appointment:id,appointment_number')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'appointment_number' => $p->appointment?->appointment_number,
                    'razorpay_order_id' => $p->razorpay_order_id,
                    'razorpay_payment_id' => $p->razorpay_payment_id,
                    'amount' => '₹' . number_format((float) $p->amount, 2),
                    'currency' => $p->currency,
                    'status' => $p->status,
                    'method' => $p->method,
                    'date' => $p->created_at?->format('M d, Y h:i A'),
                ];
            });

        return response()->json(['payments' => $payments]);
    }

    private function resolvePaymentFromWebhook(string $event, array $data): ?Payment
    {
        $entity = $data['payload'] ?? [];

        if (isset($entity['payment']['entity']['order_id'])) {
            return Payment::where('razorpay_order_id', $entity['payment']['entity']['order_id'])->first();
        }

        if (isset($entity['order']['entity']['id'])) {
            return Payment::where('razorpay_order_id', $entity['order']['entity']['id'])->first();
        }

        if (isset($entity['refund']['entity']['payment_id'])) {
            return Payment::where('razorpay_payment_id', $entity['refund']['entity']['payment_id'])->first();
        }

        return null;
    }

    private function applyWebhookEvent(Payment $payment, string $event, array $data): void
    {
        $paymentEntity = $data['payload']['payment']['entity'] ?? [];

        switch ($event) {
            case 'payment.captured':
            case 'order.paid':
                if ($payment->status !== 'paid') {
                    $payment->update([
                        'status' => 'paid',
                        'razorpay_payment_id' => $paymentEntity['id'] ?? $payment->razorpay_payment_id,
                        'method' => $paymentEntity['method'] ?? $payment->method,
                    ]);
                    $payment->appointment?->update(['payment_status' => 'paid']);
                }
                break;

            case 'payment.failed':
                if (!in_array($payment->status, ['paid', 'refunded'])) {
                    $payment->update([
                        'status' => 'failed',
                        'razorpay_payment_id' => $paymentEntity['id'] ?? $payment->razorpay_payment_id,
                        'method' => $paymentEntity['method'] ?? $payment->method,
                        'error_code' => $paymentEntity['error_code'] ?? null,
                        'error_description' => $paymentEntity['error_description'] ?? null,
                    ]);
                }
                break;

            case 'refund.processed':
            case 'refund.created':
                $payment->update(['status' => 'refunded']);
                $payment->appointment?->update(['payment_status' => 'refunded']);
                break;

            default:
                Log::info('Unhandled Razorpay webhook event', ['event' => $event]);
        }
    }
}
