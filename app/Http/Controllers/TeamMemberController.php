<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamMember::with('user');

        // Filter by role type
        if ($request->filled('role_type')) {
            $query->where('role_type', $request->role_type);
        }

        // Filter by availability status
        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }

        // Filter by skill level
        if ($request->filled('skill_level')) {
            $query->where('skill_level', $request->skill_level);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teamMembers = $query->paginate(10);

        return view('team.index', compact('teamMembers'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['photographer', 'videographer', 'manager'])->get();
        return view('team.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'role_type' => ['required', Rule::in(['photographer', 'videographer', 'editor', 'assistant', 'sales_manager'])],
            'skill_level' => ['required', Rule::in(['junior', 'mid_level', 'senior', 'expert'])],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'on_leave'])],
            'hourly_rate' => 'nullable|numeric|min:0',
            'equipment_owned' => 'nullable|string',
            'portfolio_link' => 'nullable|url',
            'is_default_assigned' => 'boolean',
            'priority_order' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();
            
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            // Create team member profile
            $teamMember = TeamMember::create([
                'user_id' => $user->id,
                'role_type' => $validated['role_type'],
                'skill_level' => $validated['skill_level'],
                'availability_status' => $validated['availability_status'],
                'hourly_rate' => $validated['hourly_rate'] ?? null,
                'equipment_owned' => $validated['equipment_owned'] ?? null,
                'portfolio_link' => $validated['portfolio_link'] ?? null,
                'is_default_assigned' => $validated['is_default_assigned'] ?? false,
                'priority_order' => $validated['priority_order'] ?? 0,
            ]);

            activity()
                ->performedOn($teamMember)
                ->causedBy(auth()->user())
                ->log('Team member created: ' . $user->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team member created successfully',
                'redirect' => route('team.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(TeamMember $team)
    {
        $team->load([
            'user',
            'assignedEventsAsPhotographer.order.customer',
            'assignedEventsAsPhotographer.order.lead',
            'assignedEventsAsVideographer.order.customer',
            'assignedEventsAsVideographer.order.lead'
        ]);

        // Calculate statistics
        $stats = [
            'total_events' => $team->assignedEventsAsPhotographer->count() + 
                            $team->assignedEventsAsVideographer->count(),
            'completed_events' => $team->assignedEventsAsPhotographer->where('status', 'completed')->count() + 
                                $team->assignedEventsAsVideographer->where('status', 'completed')->count(),
            'upcoming_events' => $team->assignedEventsAsPhotographer->where('status', 'scheduled')->count() + 
                               $team->assignedEventsAsVideographer->where('status', 'scheduled')->count(),
            'in_progress_events' => $team->assignedEventsAsPhotographer->where('status', 'in_progress')->count() + 
                                  $team->assignedEventsAsVideographer->where('status', 'in_progress')->count(),
        ];

        return view('team.show', compact('team', 'stats'));
    }

    public function edit(TeamMember $team)
    {
        $team->load('user');
        $roles = Role::whereIn('name', ['photographer', 'videographer', 'manager'])->get();
        return view('team.edit', compact('team', 'roles'));
    }

    public function update(Request $request, TeamMember $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($team->user_id)],
            'password' => 'nullable|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'role_type' => ['required', Rule::in(['photographer', 'videographer', 'editor', 'assistant', 'sales_manager'])],
            'skill_level' => ['required', Rule::in(['junior', 'mid_level', 'senior', 'expert'])],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'on_leave'])],
            'hourly_rate' => 'nullable|numeric|min:0',
            'equipment_owned' => 'nullable|string',
            'portfolio_link' => 'nullable|url',
            'is_default_assigned' => 'boolean',
            'priority_order' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();
            
            // Update user
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $team->user->update($updateData);

            // Update role if changed
            if ($team->user->roles->first()->name != $validated['role']) {
                $team->user->syncRoles([$validated['role']]);
            }

            // Update team member profile
            $team->update([
                'role_type' => $validated['role_type'],
                'skill_level' => $validated['skill_level'],
                'availability_status' => $validated['availability_status'],
                'hourly_rate' => $validated['hourly_rate'] ?? null,
                'equipment_owned' => $validated['equipment_owned'] ?? null,
                'portfolio_link' => $validated['portfolio_link'] ?? null,
                'is_default_assigned' => $validated['is_default_assigned'] ?? false,
                'priority_order' => $validated['priority_order'] ?? 0,
            ]);

            activity()
                ->performedOn($team)
                ->causedBy(auth()->user())
                ->log('Team member updated: ' . $team->user->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team member updated successfully',
                'redirect' => route('team.show', $team)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(TeamMember $team)
    {
        try {
            // Check if team member has assigned events
            $hasEvents = $team->assignedEventsAsPhotographer()->exists() || 
                        $team->assignedEventsAsVideographer()->exists();

            if ($hasEvents) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team member with assigned events'
                ], 422);
            }

            $userName = $team->user ? $team->user->name : 'Unknown User';
            $user = $team->user;

            $team->delete();
            
            if ($user) {
                $user->delete();
            }

            activity()
                ->causedBy(auth()->user())
                ->log('Team member deleted: ' . $userName);

            return response()->json([
                'success' => true,
                'message' => 'Team member deleted successfully',
                'redirect' => route('team.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAvailability(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'availability_status' => ['required', Rule::in(['available', 'busy', 'on_leave'])]
        ]);

        $teamMember->update([
            'availability_status' => $validated['availability_status']
        ]);

        activity()
            ->performedOn($teamMember)
            ->causedBy(auth()->user())
            ->log('Availability updated: ' . ($teamMember->user ? $teamMember->user->name : 'Unknown') . ' - ' . $validated['availability_status']);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully'
        ]);
    }
}
