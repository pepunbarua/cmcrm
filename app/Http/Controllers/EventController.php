<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::with(['order.lead'])
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->search, function($q) use ($request) {
                $q->whereHas('order.lead', function($query) use ($request) {
                    $query->where('client_name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('event_date', 'asc');

        $events = $query->paginate(15);
        
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create(Request $request)
    {
        $order = null;
        if ($request->order_id) {
            $order = Order::with('lead')->findOrFail($request->order_id);
            
            // Check if order already has an event
            if ($order->event) {
                return redirect()->route('events.show', $order->event)
                    ->with('error', 'This order already has an event scheduled');
            }
        }
        
        $teamMembers = TeamMember::where('availability_status', 'available')->get();
        
        return view('events.create', compact('order', 'teamMembers'));
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'venue_address' => 'nullable|string',
            'delivery_deadline' => 'required|date|after:event_date',
            'special_instructions' => 'nullable|string',
            'photographer_id' => 'nullable|exists:team_members,id',
            'videographer_id' => 'nullable|exists:team_members,id',
            'equipment_checklist' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        // Auto-assign team members if not provided
        if (empty($validated['photographer_id'])) {
            $validated['photographer_id'] = $this->autoAssignTeamMember('photographer');
        }
        if (empty($validated['videographer_id'])) {
            $validated['videographer_id'] = $this->autoAssignTeamMember('videographer');
        }

        $event = Event::create($validated);
        
        activity()
            ->performedOn($event)
            ->causedBy(Auth::user())
            ->log('Event created');

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully!',
            'redirect' => route('events.show', $event)
        ]);
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $event->load(['order.lead', 'photographer', 'videographer', 'deliverables', 'notes.user']);
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the event
     */
    public function edit(Event $event)
    {
        $teamMembers = TeamMember::where('availability_status', 'available')->get();
        return view('events.edit', compact('event', 'teamMembers'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'venue_address' => 'nullable|string',
            'delivery_deadline' => 'required|date|after:event_date',
            'special_instructions' => 'nullable|string',
            'photographer_id' => 'nullable|exists:team_members,id',
            'videographer_id' => 'nullable|exists:team_members,id',
            'equipment_checklist' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $event->update($validated);
        
        activity()
            ->performedOn($event)
            ->causedBy(Auth::user())
            ->log('Event updated');

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully!',
            'redirect' => route('events.show', $event)
        ]);
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        activity()
            ->performedOn($event)
            ->causedBy(Auth::user())
            ->log('Event deleted');
            
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully!'
        ]);
    }

    /**
     * Auto-assign team member based on role and availability
     */
    private function autoAssignTeamMember($role)
    {
        // Get team member with least upcoming events
        $teamMember = TeamMember::where('role_type', $role)
            ->where('availability_status', 'available')
            ->withCount(['assignedEventsAsPhotographer as upcoming_count' => function($query) {
                $query->where('event_date', '>=', now())
                      ->where('status', '!=', 'completed');
            }])
            ->orderBy('upcoming_count', 'asc')
            ->first();

        return $teamMember?->id;
    }
}

