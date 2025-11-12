<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CallQueueController extends Controller
{
    /**
     * Main Call Queue Dashboard
     */
    public function index()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_today' => LeadActivity::todaysFollowUps()
                ->where('assigned_to', $userId)
                ->count(),
            'pending_calls' => Lead::where('status', 'new')
                ->unlocked()
                ->count(),
            'follow_ups' => LeadActivity::followUpRequired()
                ->where('assigned_to', $userId)
                ->where('is_completed', false)
                ->count(),
            'completed_today' => LeadActivity::where('performed_by', $userId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('call-queue.index', compact('stats'));
    }

    /**
     * Today's Call List - All calls scheduled for today
     */
    public function today()
    {
        $userId = auth()->id();
        
        $leads = Lead::with(['vendor', 'user', 'leadActivities' => function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->latest()
                  ->limit(1);
            }])
            ->whereHas('leadActivities', function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->whereDate('next_follow_up_date', today());
            })
            ->orderByRaw("(SELECT next_follow_up_time FROM lead_activities WHERE lead_id = leads.id AND assigned_to = ? AND follow_up_required = 1 AND is_completed = 0 ORDER BY created_at DESC LIMIT 1) ASC", [$userId])
            ->paginate(50);

        return view('call-queue.today', compact('leads'));
    }

    /**
     * Scheduled Calls - Future follow-ups
     */
    public function scheduled()
    {
        $userId = auth()->id();
        
        $leads = Lead::with(['vendor', 'user', 'leadActivities' => function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->latest()
                  ->limit(1);
            }])
            ->whereHas('leadActivities', function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->whereDate('next_follow_up_date', '>', today());
            })
            ->orderByRaw("(SELECT next_follow_up_date FROM lead_activities WHERE lead_id = leads.id AND assigned_to = ? AND follow_up_required = 1 AND is_completed = 0 ORDER BY created_at DESC LIMIT 1) ASC", [$userId])
            ->paginate(50);

        return view('call-queue.scheduled', compact('leads'));
    }

    /**
     * Pending Calls - New leads that haven't been contacted
     */
    public function pending()
    {
        $leads = Lead::with(['vendor', 'user'])
            ->where('status', 'new')
            ->whereDoesntHave('leadActivities', function($q) {
                $q->where('activity_type', 'call');
            })
            ->unlocked()
            ->orderBy('event_date', 'asc')
            ->paginate(50);

        return view('call-queue.pending', compact('leads'));
    }

    /**
     * Follow-up Required - All leads needing follow-up
     */
    public function followUps()
    {
        $userId = auth()->id();
        
        $leads = Lead::with(['vendor', 'user', 'leadActivities' => function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false)
                  ->latest()
                  ->limit(1);
            }])
            ->whereHas('leadActivities', function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->where('follow_up_required', true)
                  ->where('is_completed', false);
            })
            ->orderByRaw("(SELECT next_follow_up_date FROM lead_activities WHERE lead_id = leads.id AND assigned_to = ? AND follow_up_required = 1 AND is_completed = 0 ORDER BY created_at DESC LIMIT 1) ASC", [$userId])
            ->paginate(50);

        return view('call-queue.follow-ups', compact('leads'));
    }

    /**
     * Call History - Past activities
     */
    public function history(Request $request)
    {
        $userId = auth()->id();
        
        $query = LeadActivity::with(['lead.vendor', 'performer'])
            ->where('performed_by', $userId)
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by call outcome
        if ($request->filled('call_outcome')) {
            $query->where('call_outcome', $request->call_outcome);
        }

        // Filter by interest level
        if ($request->filled('interest_level')) {
            $query->where('lead_interest_level', $request->interest_level);
        }

        $activities = $query->paginate(50);

        return view('call-queue.history', compact('activities'));
    }
}
