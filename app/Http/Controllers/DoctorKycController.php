<?php

namespace App\Http\Controllers;

use App\Models\DoctorKyc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorKycController extends Controller
{
    /**
     * Display KYC documents for the authenticated doctor
     */
    public function index()
    {
        $doctor = Auth::user();
        $documents = DoctorKyc::where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('document_type');

        $kycStatus = $this->getKycStatus($doctor);

        return view('doctor.kyc.index', compact('documents', 'kycStatus', 'doctor'));
    }

    /**
     * Show the form for uploading a new document
     */
    public function create()
    {
        $doctor = Auth::user();
        return view('doctor.kyc.create', compact('doctor'));
    }

    /**
     * Store a newly uploaded document
     */
    public function store(Request $request)
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'document_type' => 'required|in:aadhar_front,aadhar_back,degree,pan',
            'document_name' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $file = $request->file('document_file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('doctor-kyc/' . $doctor->id, $fileName, 'public');

        // If document_name is not provided, use a default based on type
        if (empty($validated['document_name'])) {
            $validated['document_name'] = match($validated['document_type']) {
                'aadhar_front' => 'Aadhar Card (Front)',
                'aadhar_back' => 'Aadhar Card (Back)',
                'pan' => 'PAN Card',
                'degree' => 'Degree Certificate',
                default => 'Document',
            };
        }

        DoctorKyc::create([
            'doctor_id' => $doctor->id,
            'document_type' => $validated['document_type'],
            'document_name' => $validated['document_name'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
        ]);

        return redirect()->route('doctor.kyc.index')->with('success', 'Document uploaded successfully. Waiting for approval.');
    }

    /**
     * Delete a document (only if pending)
     */
    public function destroy(DoctorKyc $doctorKyc)
    {
        $doctor = Auth::user();

        // Only allow doctor to delete their own pending documents
        if ($doctorKyc->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($doctorKyc->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending documents can be deleted.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($doctorKyc->file_path)) {
            Storage::disk('public')->delete($doctorKyc->file_path);
        }

        $doctorKyc->delete();

        return redirect()->route('doctor.kyc.index')->with('success', 'Document deleted successfully.');
    }

    /**
     * Admin: View all KYC documents
     */
    public function adminIndex(Request $request)
    {
        $query = DoctorKyc::with(['doctor', 'approver'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by document type
        if ($request->has('document_type') && $request->document_type) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by doctor
        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $documents = $query->paginate(20);
        $doctors = User::role('Doctor')->get();
        $pendingCount = DoctorKyc::where('status', 'pending')->count();

        return view('admin.kyc.index', compact('documents', 'doctors', 'pendingCount'));
    }

    /**
     * Admin: View single document
     */
    public function adminShow(DoctorKyc $doctorKyc)
    {
        $doctorKyc->load(['doctor', 'approver']);
        return view('admin.kyc.show', compact('doctorKyc'));
    }

    /**
     * Admin: Approve a document
     */
    public function approve(Request $request, DoctorKyc $doctorKyc)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $doctorKyc->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        // Check and update doctor status after approval
        self::checkAndUpdateDoctorStatus($doctorKyc->doctor);

        return redirect()->route('admin.kyc.index')->with('success', 'Document approved successfully.');
    }

    /**
     * Admin: Reject a document
     */
    public function reject(Request $request, DoctorKyc $doctorKyc)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $doctorKyc->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('admin.kyc.index')->with('success', 'Document rejected successfully.');
    }

    /**
     * Download/view document
     */
    public function download(DoctorKyc $doctorKyc)
    {
        $doctor = Auth::user();

        // Check permissions: doctor can view their own, admin can view all
        if ($doctorKyc->doctor_id !== $doctor->id && !$doctor->hasRole(['Super Admin', 'Administrator'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($doctorKyc->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($doctorKyc->file_path, $doctorKyc->file_name);
    }

    /**
     * Get KYC status for a doctor
     */
    public function getKycStatus(User $doctor): array
    {
        $documents = DoctorKyc::where('doctor_id', $doctor->id)->get();

        $aadharFront = $documents->where('document_type', 'aadhar_front')->first();
        $aadharBack = $documents->where('document_type', 'aadhar_back')->first();
        $pan = $documents->where('document_type', 'pan')->first();
        $degrees = $documents->where('document_type', 'degree');

        return [
            'aadhar_front' => [
                'uploaded' => $aadharFront !== null,
                'status' => $aadharFront?->status ?? 'not_uploaded',
                'document' => $aadharFront,
            ],
            'aadhar_back' => [
                'uploaded' => $aadharBack !== null,
                'status' => $aadharBack?->status ?? 'not_uploaded',
                'document' => $aadharBack,
            ],
            'pan' => [
                'uploaded' => $pan !== null,
                'status' => $pan?->status ?? 'not_uploaded',
                'document' => $pan,
            ],
            'degrees' => [
                'uploaded' => $degrees->count() > 0,
                'count' => $degrees->count(),
                'approved_count' => $degrees->where('status', 'approved')->count(),
                'pending_count' => $degrees->where('status', 'pending')->count(),
                'documents' => $degrees,
            ],
            'overall_status' => $this->calculateOverallStatus($aadharFront, $aadharBack, $pan, $degrees),
        ];
    }

    /**
     * Calculate overall KYC status
     */
    private function calculateOverallStatus($aadharFront, $aadharBack, $pan, $degrees): string
    {
        $hasAadharFront = $aadharFront && $aadharFront->status === 'approved';
        $hasAadharBack = $aadharBack && $aadharBack->status === 'approved';
        $hasPan = $pan && $pan->status === 'approved';
        $hasDegree = $degrees->where('status', 'approved')->count() > 0;

        // KYC is complete when both Aadhar sides and at least one degree are approved
        if ($hasAadharFront && $hasAadharBack && $hasDegree) {
            return 'approved';
        }

        if (($aadharFront && $aadharFront->status === 'rejected') || 
            ($aadharBack && $aadharBack->status === 'rejected')) {
            return 'rejected';
        }

        if ($pan && $pan->status === 'rejected') {
            return 'rejected';
        }

        return 'pending';
    }

    /**
     * Check and update doctor status based on KYC completion
     */
    public static function checkAndUpdateDoctorStatus(User $doctor): void
    {
        $kycController = new self();
        $kycStatus = $kycController->getKycStatus($doctor);

        // If KYC is complete (both Aadhar sides and degree approved), activate doctor
        if ($kycStatus['overall_status'] === 'approved') {
            $doctor->update(['status' => 'active']);
        } else {
            // If not complete, set to pending_kyc
            if ($doctor->status === 'active') {
                // Don't deactivate if already active, just keep current status
                // Or you can set to pending_kyc if you want
            } else {
                $doctor->update(['status' => 'pending_kyc']);
            }
        }
    }
}
