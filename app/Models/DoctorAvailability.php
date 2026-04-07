<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DoctorAvailability extends BaseModel
{
    protected $table = 'doctor_availability_slots';

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'break_duration',
        'is_available',
        'specific_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'specific_date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get available time slots for a specific date
     */
    public static function getAvailableSlotsForDate($doctorId, $date): array
    {
        // Check if doctor is on leave for this date
        $onLeave = DoctorLeave::where('doctor_id', $doctorId)
            ->where('leave_date', $date)
            ->exists();

        if ($onLeave) {
            return [];
        }

        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        // Check for specific date override first
        $specificAvailability = self::where('doctor_id', $doctorId)
            ->where('specific_date', $date)
            ->where('is_available', true)
            ->first();

        if ($specificAvailability) {
            return self::generateTimeSlots($specificAvailability);
        }

        // Check for weekly recurring availability
        $weeklyAvailability = self::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->whereNull('specific_date')
            ->get();

        $slots = [];
        foreach ($weeklyAvailability as $availability) {
            $slots = array_merge($slots, self::generateTimeSlots($availability));
        }

        // Remove slots that are already booked
        $bookedSlots = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(function($time) {
                return date('H:i', strtotime($time));
            })
            ->toArray();

        return array_values(array_diff($slots, $bookedSlots));
    }

    /**
     * Generate time slots from availability
     */
    private static function generateTimeSlots($availability): array
    {
        $slots = [];
        // Handle both string and Carbon datetime formats
        $startTime = is_string($availability->start_time) ? $availability->start_time : $availability->start_time->format('H:i:s');
        $endTime = is_string($availability->end_time) ? $availability->end_time : $availability->end_time->format('H:i:s');
        
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $duration = $availability->slot_duration;
        $break = $availability->break_duration;

        $current = $start->copy();
        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($duration + $break);
        }

        return $slots;
    }

    /**
     * Get day label
     */
    public function getDayLabelAttribute(): string
    {
        return ucfirst($this->day_of_week);
    }

    protected $auditInclude = [
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'is_available',
    ];

    protected $auditExclude = ['created_at', 'updated_at'];
}
