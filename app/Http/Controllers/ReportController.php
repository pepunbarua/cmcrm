<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Date range
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Overview Statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_events' => Event::whereBetween('event_date', [$startDate, $endDate])->count(),
            'total_leads' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
            'conversion_rate' => $this->calculateConversionRate($startDate, $endDate),
            'average_order_value' => $this->calculateAverageOrderValue($startDate, $endDate),
        ];

        // Monthly revenue trend (last 6 months)
        $revenueData = $this->getMonthlyRevenue(6);

        // Order status distribution
        $orderStatusData = Order::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Event status distribution
        $eventStatusData = Event::select('status', DB::raw('count(*) as count'))
            ->whereBetween('event_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Top performing team members
        $topTeamMembers = $this->getTopTeamMembers($startDate, $endDate);

        return view('reports.index', compact(
            'stats',
            'revenueData',
            'orderStatusData',
            'eventStatusData',
            'topTeamMembers',
            'startDate',
            'endDate'
        ));
    }

    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Revenue statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'pending_amount' => Payment::where('status', 'pending')
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum('amount'),
            'average_payment' => Payment::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->avg('amount'),
            'payment_count' => Payment::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
        ];

        // Revenue by payment method
        $revenueByMethod = Payment::select('payment_method', DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Daily revenue trend
        $dailyRevenue = Payment::select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('sum(amount) as total')
            )
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly comparison (current vs previous period)
        $currentPeriodRevenue = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $periodLength = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $previousStartDate = Carbon::parse($startDate)->subDays($periodLength);
        $previousEndDate = Carbon::parse($startDate)->subDay();

        $previousPeriodRevenue = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$previousStartDate, $previousEndDate])
            ->sum('amount');

        $revenueChange = $previousPeriodRevenue > 0 
            ? (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : 0;

        return view('reports.revenue', compact(
            'stats',
            'revenueByMethod',
            'dailyRevenue',
            'revenueChange',
            'startDate',
            'endDate'
        ));
    }

    public function events(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Event statistics
        $stats = [
            'total_events' => Event::whereBetween('event_date', [$startDate, $endDate])->count(),
            'completed_events' => Event::where('status', 'completed')
                ->whereBetween('event_date', [$startDate, $endDate])
                ->count(),
            'scheduled_events' => Event::where('status', 'scheduled')
                ->whereBetween('event_date', [$startDate, $endDate])
                ->count(),
            'cancelled_events' => Event::where('status', 'cancelled')
                ->whereBetween('event_date', [$startDate, $endDate])
                ->count(),
        ];

        // Events by status
        $eventsByStatus = Event::select('status', DB::raw('count(*) as count'))
            ->whereBetween('event_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Events by venue (top 10)
        $eventsByVenue = Event::select('venue', DB::raw('count(*) as count'))
            ->whereBetween('event_date', [$startDate, $endDate])
            ->groupBy('venue')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Monthly event trend (last 6 months)
        $monthlyEvents = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Event::whereYear('event_date', $month->year)
                ->whereMonth('event_date', $month->month)
                ->count();
            $monthlyEvents[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Upcoming events
        $upcomingEvents = Event::with('order.lead', 'photographer.user', 'videographer.user')
            ->where('status', 'scheduled')
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date')
            ->limit(10)
            ->get();

        return view('reports.events', compact(
            'stats',
            'eventsByStatus',
            'eventsByVenue',
            'monthlyEvents',
            'upcomingEvents',
            'startDate',
            'endDate'
        ));
    }

    public function team(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // Team statistics
        $stats = [
            'total_members' => TeamMember::count(),
            'available_members' => TeamMember::where('availability_status', 'available')->count(),
            'busy_members' => TeamMember::where('availability_status', 'busy')->count(),
            'on_leave' => TeamMember::where('availability_status', 'on_leave')->count(),
        ];

        // Team members with event counts
        $teamPerformance = TeamMember::with('user')
            ->withCount([
                'assignedEventsAsPhotographer as photographer_events' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('event_date', [$startDate, $endDate]);
                },
                'assignedEventsAsVideographer as videographer_events' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('event_date', [$startDate, $endDate]);
                }
            ])
            ->get()
            ->map(function($member) {
                $member->total_events = $member->photographer_events + $member->videographer_events;
                return $member;
            })
            ->sortByDesc('total_events');

        // Team members by role type
        $teamByRole = TeamMember::select('role_type', DB::raw('count(*) as count'))
            ->groupBy('role_type')
            ->get();

        // Team members by skill level
        $teamBySkill = TeamMember::select('skill_level', DB::raw('count(*) as count'))
            ->groupBy('skill_level')
            ->get();

        return view('reports.team', compact(
            'stats',
            'teamPerformance',
            'teamByRole',
            'teamBySkill',
            'startDate',
            'endDate'
        ));
    }

    // Helper methods
    private function calculateConversionRate($startDate, $endDate)
    {
        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedLeads = Lead::where('status', 'converted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    private function calculateAverageOrderValue($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->avg('total_amount') ?? 0;
    }

    private function getMonthlyRevenue($months = 6)
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }
        return $data;
    }

    private function getTopTeamMembers($startDate, $endDate, $limit = 5)
    {
        return TeamMember::with('user')
            ->withCount([
                'assignedEventsAsPhotographer as photographer_events' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('event_date', [$startDate, $endDate])
                          ->where('status', 'completed');
                },
                'assignedEventsAsVideographer as videographer_events' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('event_date', [$startDate, $endDate])
                          ->where('status', 'completed');
                }
            ])
            ->get()
            ->map(function($member) {
                $member->total_completed = $member->photographer_events + $member->videographer_events;
                return $member;
            })
            ->sortByDesc('total_completed')
            ->take($limit);
    }
}
