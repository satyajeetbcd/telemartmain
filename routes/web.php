<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorKycController;
use App\Http\Controllers\DoctorListController;
use App\Http\Controllers\AdminDoctorProfileController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\AdminDoctorAvailabilityController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DoctorLeaveController;

// Public routes for invitation acceptance
Route::get('/invitations/accept/{token}', [InvitationController::class, 'showAcceptForm'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // API route for getting cities by state
    Route::get('/api/cities', [UserController::class, 'getCitiesByState'])->name('api.cities');

    // Doctor routes (only for users with Doctor role)
    Route::prefix('doctor')->name('doctor.')->middleware('role:Doctor')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [DoctorController::class, 'profile'])->name('profile');
        Route::put('/profile', [DoctorController::class, 'updateProfile'])->name('profile.update');
        
        // KYC Routes
        Route::get('/kyc', [DoctorKycController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/create', [DoctorKycController::class, 'create'])->name('kyc.create');
        Route::post('/kyc', [DoctorKycController::class, 'store'])->name('kyc.store');
        Route::delete('/kyc/{doctorKyc}', [DoctorKycController::class, 'destroy'])->name('kyc.destroy');
        Route::get('/kyc/{doctorKyc}/download', [DoctorKycController::class, 'download'])->name('kyc.download');
        
        // Availability Routes
        Route::resource('availability', DoctorAvailabilityController::class);
        Route::get('/availability/slots/available', [DoctorAvailabilityController::class, 'getAvailableSlots'])->name('availability.get-slots');

        // Leave Routes
        Route::post('/leaves', [DoctorLeaveController::class, 'store'])->name('leaves.store');
        Route::delete('/leaves/{doctorLeave}', [DoctorLeaveController::class, 'destroy'])->name('leaves.destroy');
    });

    // Patient routes
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [PatientController::class, 'profile'])->name('profile');
        Route::put('/profile', [PatientController::class, 'updateProfile'])->name('profile.update');
    });

    // Video Calls
    Route::get('/video-calls', [AppointmentController::class, 'videoCallIndex'])->name('video-calls.index');

    // Appointment routes
    Route::resource('appointments', AppointmentController::class);
    Route::get('/appointments/slots/available', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.available-slots');
    Route::post('/appointments/{appointment}/mark-paid', [AppointmentController::class, 'markPaymentReceived'])->name('appointments.mark-paid');
    Route::post('/appointments/{appointment}/create-zoom', [AppointmentController::class, 'createZoom'])->name('appointments.create-zoom');
    Route::put('/appointments/{appointment}/reassign-doctor', [AppointmentController::class, 'reassignDoctor'])->name('appointments.reassign-doctor');
    Route::get('/appointments/{appointment}/invoice', [InvoiceController::class, 'show'])->name('appointments.invoice');

    // Medical Records routes
    Route::resource('consultations', ConsultationController::class);
    Route::resource('medical-records', MedicalRecordController::class);
    Route::get('/medical-records/{medical_record}/download/{attachment}', [MedicalRecordController::class, 'download'])->name('medical-records.download');
    Route::get('/api/medical-records/appointments', [MedicalRecordController::class, 'getAppointments'])->name('medical-records.appointments');

    // Prescription routes
    Route::resource('prescriptions', PrescriptionController::class);
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'downloadPdf'])->name('prescriptions.pdf');
    Route::get('/api/prescriptions/appointments', [PrescriptionController::class, 'getAppointments'])->name('prescriptions.appointments');

    // Review routes
    Route::get('/doctors/{doctor}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/doctors/{doctor}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/doctors/{doctor}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::put('/reviews/{review}/reply', [ReviewController::class, 'updateReply'])->name('reviews.update-reply');
    Route::delete('/reviews/{review}/reply', [ReviewController::class, 'deleteReply'])->name('reviews.delete-reply');

    // Patient Management routes (accessible to admins, doctors, and receptionists)
    Route::middleware('role:Super Admin|Administrator|Doctor|Receptionist')->group(function () {
        Route::resource('patients', PatientManagementController::class);
    });

    // Admin routes (only for admins)
    Route::middleware('role:Super Admin|Administrator')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('invitations', InvitationController::class)->except(['show']);
        Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [\App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
        
        // KYC Management Routes
        Route::prefix('admin/kyc')->name('admin.kyc.')->group(function () {
            Route::get('/', [DoctorKycController::class, 'adminIndex'])->name('index');
            Route::get('/{doctorKyc}', [DoctorKycController::class, 'adminShow'])->name('show');
            Route::post('/{doctorKyc}/approve', [DoctorKycController::class, 'approve'])->name('approve');
            Route::post('/{doctorKyc}/reject', [DoctorKycController::class, 'reject'])->name('reject');
        });

        // Doctor List Routes
        Route::get('/doctors', [DoctorListController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [DoctorListController::class, 'show'])->name('doctors.show');
        Route::put('/doctors/{doctor}/status', [DoctorListController::class, 'updateStatus'])->name('doctors.update-status');
        Route::post('/doctors/{doctor}/check-kyc', [DoctorListController::class, 'checkKycStatus'])->name('doctors.check-kyc');
        
        // Doctor Profile Management Routes
        Route::get('/doctors/{doctor}/profile-changes', [AdminDoctorProfileController::class, 'showPendingChanges'])->name('admin.doctors.profile-changes');
        Route::post('/doctors/profile-changes/{profileChange}/approve', [AdminDoctorProfileController::class, 'approve'])->name('admin.doctors.approve-profile-change');
        Route::post('/doctors/profile-changes/{profileChange}/reject', [AdminDoctorProfileController::class, 'reject'])->name('admin.doctors.reject-profile-change');
        Route::put('/doctors/{doctor}/profile', [AdminDoctorProfileController::class, 'updateProfile'])->name('admin.doctors.update-profile');
        
        // Admin Doctor Availability Management
        Route::prefix('doctors/{doctor}/availability')->name('admin.doctors.availability.')->group(function () {
            Route::get('/create', [AdminDoctorAvailabilityController::class, 'create'])->name('create');
            Route::post('/', [AdminDoctorAvailabilityController::class, 'store'])->name('store');
            Route::get('/{availability}/edit', [AdminDoctorAvailabilityController::class, 'edit'])->name('edit');
            Route::put('/{availability}', [AdminDoctorAvailabilityController::class, 'update'])->name('update');
            Route::delete('/{availability}', [AdminDoctorAvailabilityController::class, 'destroy'])->name('destroy');
        });

        // Admin Review Management
        Route::get('/reviews', [ReviewController::class, 'adminIndex'])->name('admin.reviews.index');
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::post('/reviews/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('admin.reviews.bulk-approve');
        Route::post('/reviews/bulk-reject', [ReviewController::class, 'bulkReject'])->name('admin.reviews.bulk-reject');
        Route::post('/reviews/{review}/toggle-visibility', [ReviewController::class, 'toggleVisibility'])->name('admin.reviews.toggle-visibility');
    });
});
