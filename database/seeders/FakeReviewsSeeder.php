<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\DoctorReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakeReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctor = User::find(2);
        
        if (!$doctor) {
            $this->command->error('User ID 2 not found!');
            return;
        }

        if (!$doctor->hasRole('Doctor')) {
            $this->command->error('User ID 2 is not a doctor!');
            return;
        }

        // Get existing patients or create some fake ones
        $patients = Patient::take(10)->get();
        
        if ($patients->count() < 10) {
            // Create additional patients if needed
            $needed = 10 - $patients->count();
            for ($i = 0; $i < $needed; $i++) {
                $patients->push(Patient::create([
                    'patient_id' => Patient::generatePatientId(),
                    'first_name' => 'Patient',
                    'last_name' => 'User ' . ($patients->count() + $i + 1),
                    'email' => 'patient' . ($patients->count() + $i + 1) . '@example.com',
                    'phone' => '123456789' . ($patients->count() + $i),
                ]));
            }
        }

        $comments = [
            'Excellent doctor! Very professional and caring.',
            'Great consultation experience. Highly recommended.',
            'Very knowledgeable and patient. Explained everything clearly.',
            'Good doctor but could improve communication.',
            'Outstanding service! The best doctor I have visited.',
            'Very satisfied with the treatment and care.',
            'Professional and friendly. Made me feel comfortable.',
            'Good experience overall. Would visit again.',
            'Excellent bedside manner. Very thorough examination.',
            'Great doctor! Helped me understand my condition better.',
        ];

        $ratings = [5, 5, 4, 4, 5, 5, 4, 3, 5, 4]; // Mix of ratings

        $this->command->info('Creating 10 fake reviews for Dr. ' . $doctor->name . '...');

        for ($i = 0; $i < 10; $i++) {
            $patient = $patients[$i];
            
            // Set some reviews as approved, some as pending
            $approvalStatus = $i < 7 ? 'approved' : 'pending'; // 7 approved, 3 pending
            
            $review = DoctorReview::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_id' => null, // Can be null for reviews
                'rating' => $ratings[$i],
                'comment' => $comments[$i],
                'doctor_reply' => $i < 3 ? 'Thank you for your feedback! We appreciate your kind words.' : null, // Some with replies
                'replied_at' => $i < 3 ? now()->subDays(rand(1, 5)) : null,
                'is_visible' => true,
                'approval_status' => $approvalStatus,
                'approved_by' => $approvalStatus === 'approved' ? 1 : null, // Assuming admin user ID is 1
                'approved_at' => $approvalStatus === 'approved' ? now()->subDays(rand(1, 30)) : null,
                'rejection_reason' => null,
                'created_at' => now()->subDays(rand(1, 60)), // Random dates in last 60 days
                'updated_at' => now()->subDays(rand(1, 60)),
            ]);

            $reviewNumber = $i + 1;
            $this->command->info("Created review #{$reviewNumber}: {$ratings[$i]} stars - Status: {$approvalStatus}");
        }

        $this->command->info('Successfully created 10 fake reviews!');
        $this->command->info('7 reviews are approved and visible');
        $this->command->info('3 reviews are pending approval');
    }
}
