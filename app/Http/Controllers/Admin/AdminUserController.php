<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['bookings', 'reviews']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:user,admin'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }

        // Handle associated records (bookings, reviews, etc.)
        $user->bookings()->update(['status' => 'cancelled']);
        $user->reviews()->delete();
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function impersonate(User $user)
    {
        // Store the admin's original ID in the session
        session()->put('admin_id', auth()->id());
        
        // Login as the target user
        auth()->login($user);

        return redirect('/')->with('success', 'Now impersonating ' . $user->name);
    }

    public function stopImpersonating()
    {
        // Get the admin's original ID from session
        $adminId = session()->pull('admin_id');
        
        if ($adminId) {
            $admin = User::find($adminId);
            auth()->login($admin);
            return redirect()->route('admin.users')->with('success', 'Stopped impersonating user.');
        }

        return redirect()->route('admin.users')->withErrors(['impersonate' => 'Not currently impersonating.']);
    }
}
