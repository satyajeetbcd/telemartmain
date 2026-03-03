<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $states = State::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('roles', 'states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'aadhar_card_number' => 'nullable|string|max:12|regex:/^[0-9]{12}$/',
            'address' => 'nullable|string|max:500',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'city' => 'nullable|string|max:100', // Keep for backward compatibility
            'state' => 'nullable|string|max:100', // Keep for backward compatibility
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'aadhar_card_number' => $validated['aadhar_card_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'state_id' => $validated['state_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'city' => $validated['city'] ?? null, // Keep for backward compatibility
            'state' => $validated['state'] ?? null, // Keep for backward compatibility
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? 'India',
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('profile_images', $imageName, 'public');
            $userData['profile_image'] = $imagePath;
        }

        $user = User::create($userData);

        $role = Role::findById($validated['role']);
        $user->assignRole($role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $states = State::where('is_active', true)->orderBy('name')->get();
        $cities = $user->state_id ? City::where('state_id', $user->state_id)->where('is_active', true)->orderBy('name')->get() : collect();
        return view('users.edit', compact('user', 'roles', 'states', 'cities'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'aadhar_card_number' => 'nullable|string|max:12|regex:/^[0-9]{12}$/',
            'address' => 'nullable|string|max:500',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'city' => 'nullable|string|max:100', // Keep for backward compatibility
            'state' => 'nullable|string|max:100', // Keep for backward compatibility
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'aadhar_card_number' => $validated['aadhar_card_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'state_id' => $validated['state_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'city' => $validated['city'] ?? null, // Keep for backward compatibility
            'state' => $validated['state'] ?? null, // Keep for backward compatibility
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? 'India',
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('profile_images', $imageName, 'public');
            $userData['profile_image'] = $imagePath;
        }

        $user->update($userData);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $role = Role::findById($validated['role']);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Get cities by state ID (AJAX)
     */
    public function getCitiesByState(Request $request)
    {
        $stateId = $request->get('state_id');
        
        if (!$stateId) {
            return response()->json([]);
        }

        $cities = City::where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
