<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('email', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('role'), function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', request('role'));
                });
            })
            ->latest()
            ->paginate(10);

        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'avatar-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('avatars', $fileName, 'public');
            $user->avatar = $filePath;
        }
        
        $user->save();
        
        // Assign role
        $user->assignRole($request->role);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Created new user: ' . $user->name);
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'redirect' => route('users.index')
        ]);
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $file = $request->file('avatar');
            $fileName = 'avatar-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('avatars', $fileName, 'public');
            $user->avatar = $filePath;
        }
        
        $user->save();
        
        // Sync role
        $user->syncRoles([$request->role]);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Updated user: ' . $user->name);
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'redirect' => route('users.index')
        ]);
    }

    public function destroy(User $user)
    {
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account!'
            ], 400);
        }
        
        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Deleted user: ' . $user->name);
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }
}
