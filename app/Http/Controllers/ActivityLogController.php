<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['user', 'auditable'])
            ->latest();

        // Filter by event if provided
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Filter by user type if provided
        if ($request->has('user_type') && $request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by auditable type if provided
        if ($request->has('auditable_type') && $request->auditable_type) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Search in old_values or new_values
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('old_values', 'like', '%' . $request->search . '%')
                  ->orWhere('new_values', 'like', '%' . $request->search . '%');
            });
        }

        $activities = $query->paginate(20);

        // Get unique events, user types, and auditable types for filters
        $events = Audit::distinct()->pluck('event')->filter()->sort()->values();
        $userTypes = Audit::distinct()->pluck('user_type')->filter()->sort()->values();
        $auditableTypes = Audit::distinct()->pluck('auditable_type')->filter()->sort()->values();

        return view('activity-logs.index', compact('activities', 'events', 'userTypes', 'auditableTypes'));
    }

    public function show($id)
    {
        $activityLog = Audit::with(['user', 'auditable'])->findOrFail($id);
        return view('activity-logs.show', compact('activityLog'));
    }
}
