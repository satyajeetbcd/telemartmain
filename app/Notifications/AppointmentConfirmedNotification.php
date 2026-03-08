<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param Appointment $appointment
     * @param string      $role  'doctor' | 'patient' | 'admin'
     */
    public function __construct(
        public Appointment $appointment,
        public string $role = 'patient'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    // ──────────────────────────────────────────────
    // EMAIL
    // ──────────────────────────────────────────────
    public function toMail(object $notifiable): MailMessage
    {
        $apt     = $this->appointment;
        $date    = $apt->appointment_date->format('F d, Y');
        $time    = date('h:i A', strtotime($apt->appointment_time));
        $appName = config('app.name', 'Tele Health Mart');

        if ($this->role === 'doctor') {
            return (new MailMessage)
                ->subject("New Confirmed Appointment — {$apt->appointment_number}")
                ->greeting("Hello Dr. {$notifiable->name},")
                ->line("A new appointment has been confirmed and payment has been received.")
                ->line("**Appointment #:** {$apt->appointment_number}")
                ->line("**Patient:** {$apt->patient->full_name} ({$apt->patient->patient_id})")
                ->line("**Date & Time:** {$date} at {$time}")
                ->line("**Consultation Fee:** ₹" . number_format($apt->consultation_fee ?? 0, 2))
                ->when($apt->zoom_start_url, fn($mail) => $mail
                    ->line("---")
                    ->line("**Zoom Meeting Ready** — You are the host.")
                    ->line("**Meeting ID:** {$apt->zoom_meeting_id}")
                    ->when($apt->zoom_meeting_password, fn($m) => $m->line("**Password:** {$apt->zoom_meeting_password}"))
                    ->action('Start Video Call (Host)', $apt->zoom_start_url)
                    ->line("Share the patient join link: {$apt->zoom_join_url}")
                )
                ->when(!$apt->zoom_start_url, fn($mail) => $mail
                    ->action('View Appointment', route('appointments.show', $apt))
                )
                ->line("Thank you for using {$appName}.");
        }

        if ($this->role === 'admin') {
            return (new MailMessage)
                ->subject("Payment Received — Appointment {$apt->appointment_number}")
                ->greeting("Hello Admin,")
                ->line("A payment has been received and appointment confirmed.")
                ->line("**Appointment #:** {$apt->appointment_number}")
                ->line("**Patient:** {$apt->patient->full_name}")
                ->line("**Doctor:** Dr. {$apt->doctor->name}")
                ->line("**Date & Time:** {$date} at {$time}")
                ->line("**Fee:** ₹" . number_format($apt->consultation_fee ?? 0, 2))
                ->action('View Appointment', route('appointments.show', $apt))
                ->line("Thank you for using {$appName}.");
        }

        // Patient (default)
        return (new MailMessage)
            ->subject("Appointment Confirmed — Zoom Link Inside")
            ->greeting("Hello {$apt->patient->full_name},")
            ->line("Your appointment has been confirmed and payment received.")
            ->line("**Appointment #:** {$apt->appointment_number}")
            ->line("**Doctor:** Dr. {$apt->doctor->name}" . ($apt->doctor->specialization ? " ({$apt->doctor->specialization})" : ''))
            ->line("**Date & Time:** {$date} at {$time}")
            ->when($apt->zoom_join_url, fn($mail) => $mail
                ->line("---")
                ->line("**Your Zoom Video Call Link is Ready!**")
                ->line("**Meeting ID:** {$apt->zoom_meeting_id}")
                ->when($apt->zoom_meeting_password, fn($m) => $m->line("**Password:** {$apt->zoom_meeting_password}"))
                ->action('Join Video Call', $apt->zoom_join_url)
                ->line("Click the button above at your appointment time. You may need to install Zoom if not already installed.")
            )
            ->when(!$apt->zoom_join_url, fn($mail) => $mail
                ->action('View Appointment', route('appointments.show', $apt))
            )
            ->line("Thank you for choosing {$appName} for your healthcare needs.");
    }

    // ──────────────────────────────────────────────
    // DATABASE (in-app)
    // ──────────────────────────────────────────────
    public function toDatabase(object $notifiable): array
    {
        $apt  = $this->appointment;
        $date = $apt->appointment_date->format('M d, Y');
        $time = date('h:i A', strtotime($apt->appointment_time));

        if ($this->role === 'doctor') {
            return [
                'type'             => 'appointment_confirmed',
                'title'            => 'New Confirmed Appointment',
                'message'          => "Payment received for {$apt->patient->full_name} — {$date} at {$time}",
                'appointment_id'   => $apt->id,
                'appointment_number' => $apt->appointment_number,
                'zoom_start_url'   => $apt->zoom_start_url,
                'url'              => route('appointments.show', $apt),
            ];
        }

        if ($this->role === 'admin') {
            return [
                'type'             => 'payment_received',
                'title'            => 'Payment Received',
                'message'          => "Apt #{$apt->appointment_number} — {$apt->patient->full_name} with Dr. {$apt->doctor->name}",
                'appointment_id'   => $apt->id,
                'appointment_number' => $apt->appointment_number,
                'url'              => route('appointments.show', $apt),
            ];
        }

        // Patient
        return [
            'type'             => 'appointment_confirmed',
            'title'            => 'Appointment Confirmed',
            'message'          => "Your appointment with Dr. {$apt->doctor->name} on {$date} at {$time} is confirmed.",
            'appointment_id'   => $apt->id,
            'appointment_number' => $apt->appointment_number,
            'zoom_join_url'    => $apt->zoom_join_url,
            'url'              => route('appointments.show', $apt),
        ];
    }
}
