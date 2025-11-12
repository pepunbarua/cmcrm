<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadDialerController extends Controller
{
    /**
     * Show the lead dialer interface
     */
    public function index()
    {
        $lead = $this->getNextLead(auth()->id());
        
        if (!$lead) {
            return view('call-queue.dialer-empty');
        }

        // Load relationships needed for display
        $lead->load(['vendor', 'user', 'leadActivities' => function($query) {
            $query->latest()->limit(5);
        }]);

        return view('call-queue.dialer', compact('lead'));
    }

    /**
     * Get next lead for the user based on priority logic
     */
    public function getNextLead(int $userId)
    {
        $now = now();
        
        // Priority 0: User's own locked lead (if still valid and not expired)
        $lead = Lead::with(['vendor', 'user'])
            ->where('locked_by', $userId)
            ->where('lock_expires_at', '>', $now)
            ->first();

        if ($lead) {
            // Already locked by this user, just return it
            return $lead;
        }
        
        // Priority 1: Assigned follow-ups due within 5 minutes
        $lead = Lead::with(['vendor', 'user', 'leadActivities' => function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->latest();
            }])
            ->whereHas('leadActivities', function($q) use ($userId, $now) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->whereDate('next_follow_up_date', today())
                  ->where(function($query) use ($now) {
                      $query->whereRaw("CONCAT(next_follow_up_date, ' ', next_follow_up_time) BETWEEN ? AND ?", [
                          $now->copy()->subMinutes(5)->format('Y-m-d H:i:s'),
                          $now->copy()->addMinutes(5)->format('Y-m-d H:i:s')
                      ]);
                  });
            })
            ->unlocked()
            ->first();

        if ($lead) {
            $lead->lock($userId);
            return $lead;
        }

        // Priority 2: Today's assigned follow-ups
        $lead = Lead::with(['vendor', 'user'])
            ->whereHas('leadActivities', function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->whereDate('next_follow_up_date', today());
            })
            ->unlocked()
            ->first();

        if ($lead) {
            $lead->lock($userId);
            return $lead;
        }

        // Priority 3: Leads with upcoming events (closest first)
        $lead = Lead::with(['vendor', 'user'])
            ->whereIn('status', ['new', 'contacted', 'follow_up', 'qualified'])
            ->whereDate('event_date', '>=', today())
            ->unlocked()
            ->orderBy('event_date', 'asc')
            ->first();

        if ($lead) {
            $lead->lock($userId);
            return $lead;
        }

        // Priority 4: Any unlocked lead that needs calling
        $lead = Lead::with(['vendor', 'user'])
            ->whereIn('status', ['new', 'contacted', 'follow_up', 'qualified'])
            ->unlocked()
            ->orderBy('created_at', 'asc')
            ->first();

        if ($lead) {
            $lead->lock($userId);
        }

        return $lead;
    }

    /**
     * Lock a lead manually
     */
    public function lock(Lead $lead)
    {
        if (!$lead->canBeLocked(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Lead is already locked by another user',
                'locked_by' => $lead->lockedBy?->name,
            ], 423);
        }

        $lead->lock(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Lead locked successfully',
            'expires_at' => $lead->lock_expires_at,
        ]);
    }

    /**
     * Unlock a lead
     */
    public function unlock(Lead $lead)
    {
        if ($lead->locked_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot unlock this lead',
            ], 403);
        }

        $lead->unlock();

        return response()->json([
            'success' => true,
            'message' => 'Lead unlocked successfully',
        ]);
    }

    /**
     * Extend lock time
     */
    public function extendLock(Lead $lead)
    {
        if (!$lead->isLockedByUser(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Lead is not locked by you',
            ], 403);
        }

        $lead->extendLock();

        return response()->json([
            'success' => true,
            'message' => 'Lock extended',
            'expires_at' => $lead->lock_expires_at,
        ]);
    }

    /**
     * Record activity for a lead
     */
    public function recordActivity(Request $request, Lead $lead)
    {
        // Verify lead is locked by current user
        if (!$lead->isLockedByUser(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Lead must be locked by you to record activity',
            ], 403);
        }

        $validated = $request->validate([
            'activity_type' => 'required|in:call,email,sms,meeting,note,whatsapp,status_change',
            'call_outcome' => 'nullable|in:answered,not_answered,busy,switched_off,wrong_number,number_not_available,voicemail',
            'call_duration' => 'nullable|integer|min:0',
            'call_started_at' => 'nullable|date',
            'call_ended_at' => 'nullable|date',
            'lead_interest_level' => 'nullable|in:hot,warm,cold,not_interested,converted,lost',
            'notes' => 'nullable|string',
            'discussion_points' => 'nullable|array',
            'follow_up_required' => 'boolean',
            'next_follow_up_date' => 'nullable|date',
            'next_follow_up_time' => 'nullable|date_format:H:i',
            'follow_up_notes' => 'nullable|string',
            'actions_taken' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Create activity
            $activity = LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => $validated['activity_type'],
                'call_outcome' => $validated['call_outcome'] ?? null,
                'call_duration' => $validated['call_duration'] ?? null,
                'call_started_at' => $validated['call_started_at'] ?? null,
                'call_ended_at' => $validated['call_ended_at'] ?? null,
                'lead_interest_level' => $validated['lead_interest_level'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'discussion_points' => $validated['discussion_points'] ?? null,
                'follow_up_required' => $validated['follow_up_required'] ?? false,
                'next_follow_up_date' => $validated['next_follow_up_date'] ?? null,
                'next_follow_up_time' => $validated['next_follow_up_time'] ?? null,
                'follow_up_notes' => $validated['follow_up_notes'] ?? null,
                'actions_taken' => $validated['actions_taken'] ?? null,
                'performed_by' => auth()->id(),
                'assigned_to' => $validated['assigned_to'] ?? auth()->id(),
                'is_completed' => !($validated['follow_up_required'] ?? false),
            ]);

            // Update lead status based on interest level
            if (isset($validated['lead_interest_level'])) {
                $statusMap = [
                    'hot' => 'contacted',
                    'warm' => 'contacted',
                    'cold' => 'contacted',
                    'not_interested' => 'lost',
                    'converted' => 'converted',
                    'lost' => 'lost',
                ];

                if (isset($statusMap[$validated['lead_interest_level']])) {
                    $lead->update(['status' => $statusMap[$validated['lead_interest_level']]]);
                }
            }

            // Unlock lead after recording activity
            $lead->unlock();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activity recorded successfully',
                'activity' => $activity,
                'next_lead_url' => route('call-queue.dialer'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error recording activity: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Skip current lead and get next
     */
    public function skip(Lead $lead)
    {
        if ($lead->isLockedByUser(auth()->id())) {
            $lead->unlock();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead skipped',
            'next_lead_url' => route('call-queue.dialer'),
        ]);
    }
}
