<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * Store a newly created follow-up
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'follow_up_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,missed',
            'notes' => 'required|string',
            'next_follow_up_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();

        $followUp = FollowUp::create($validated);
        
        activity()
            ->performedOn($followUp)
            ->causedBy(Auth::user())
            ->log('Follow-up created');

        return response()->json([
            'success' => true,
            'message' => 'Follow-up added successfully!',
        ]);
    }

    /**
     * Update follow-up status
     */
    public function updateStatus(Request $request, FollowUp $followUp)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,missed',
        ]);

        $followUp->update($validated);

        activity()
            ->performedOn($followUp)
            ->causedBy(Auth::user())
            ->log('Follow-up status updated');

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
    }

    /**
     * Delete a follow-up
     */
    public function destroy(FollowUp $followUp)
    {
        activity()
            ->performedOn($followUp)
            ->causedBy(Auth::user())
            ->log('Follow-up deleted');
            
        $followUp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Follow-up deleted successfully!'
        ]);
    }
}
