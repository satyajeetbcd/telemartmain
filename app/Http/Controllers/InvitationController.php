<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with(['inviter', 'role'])
            ->latest()
            ->paginate(15);
        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('invitations.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email|unique:invitations,email',
            'role_id' => 'required|exists:roles,id',
            'expires_in_days' => 'nullable|integer|min:1|max:30',
        ]);

        $expiresInDays = $validated['expires_in_days'] ?? 7;

        $invitation = Invitation::create([
            'email' => $validated['email'],
            'token' => Invitation::generateToken(),
            'invited_by' => Auth::id(),
            'role_id' => $validated['role_id'],
            'expires_at' => now()->addDays($expiresInDays),
        ]);

        // Send invitation email
        $invitationUrl = route('invitations.accept', ['token' => $invitation->token]);
        
        // In a real application, you would send an email here
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));

        // Activity will be logged automatically by Laravel Auditing
        
        return redirect()->route('invitations.index')
            ->with('success', 'Invitation sent successfully. Link: ' . $invitationUrl);
    }

    public function showAcceptForm($token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('auth.register', compact('invitation'));
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'invitation_token' => $token,
            'invited_by' => $invitation->invited_by,
            'invited_at' => now(),
            'email_verified_at' => now(),
        ]);

        if ($invitation->role_id) {
            $user->assignRole($invitation->role_id);
        }

        $invitation->update(['accepted_at' => now()]);
        // Activity will be logged automatically by Laravel Auditing

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    public function destroy(Invitation $invitation)
    {
        // Activity will be logged automatically by Laravel Auditing
        $invitation->delete();
        return redirect()->route('invitations.index')->with('success', 'Invitation deleted successfully.');
    }
}
