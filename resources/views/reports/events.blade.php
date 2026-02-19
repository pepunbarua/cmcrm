<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Events Report</h1>
                    <p class="text-gray-600 dark:text-white/60">Event analytics and scheduling insights</p>
                </div>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Back to Overview
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('reports.events') }}" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Apply
                </button>
            </form>
        </x-card>

        <!-- Event Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Events" 
                :value="$stats['total_events']" 
                icon="calendar"
                color="purple"
            />
            <x-stat-card 
                label="Completed" 
                :value="$stats['completed_events']" 
                icon="check-circle"
                color="green"
            />
            <x-stat-card 
                label="Scheduled" 
                :value="$stats['scheduled_events']" 
                icon="clock"
                color="blue"
            />
            <x-stat-card 
                label="Cancelled" 
                :value="$stats['cancelled_events']" 
                icon="x-circle"
                color="red"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Events by Status -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Events by Status
                </h3>
                <div class="space-y-3">
                    @foreach($eventsByStatus as $status)
                    <div class="flex items-center justify-between p-3 rounded-lg
                        {{ $status->status == 'completed' ? 'bg-green-50 dark:bg-green-500/10' : '' }}
                        {{ $status->status == 'scheduled' ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}
                        {{ $status->status == 'in_progress' ? 'bg-yellow-50 dark:bg-yellow-500/10' : '' }}
                        {{ $status->status == 'cancelled' ? 'bg-red-50 dark:bg-red-500/10' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                {{ $status->status == 'completed' ? 'bg-green-500' : '' }}
                                {{ $status->status == 'scheduled' ? 'bg-blue-500' : '' }}
                                {{ $status->status == 'in_progress' ? 'bg-yellow-500' : '' }}
                                {{ $status->status == 'cancelled' ? 'bg-red-500' : '' }}"></div>
                            <span class="font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $status->status) }}</span>
                        </div>
                        <span class="text-lg font-bold
                            {{ $status->status == 'completed' ? 'text-green-600 dark:text-green-400' : '' }}
                            {{ $status->status == 'scheduled' ? 'text-blue-600 dark:text-blue-400' : '' }}
                            {{ $status->status == 'in_progress' ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                            {{ $status->status == 'cancelled' ? 'text-red-600 dark:text-red-400' : '' }}">
                            {{ $status->count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Top Venues -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Top Event Venues
                </h3>
                <div class="space-y-3">
                    @php
                        $maxVenue = $eventsByVenue->max('count');
                    @endphp
                    @forelse($eventsByVenue as $venue)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-white/80">{{ $venue->venue }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $venue->count }} events</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-pink-500 to-purple-500 rounded-full"
                                 style="width: {{ $maxVenue > 0 ? ($venue->count / $maxVenue) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600 dark:text-white/60">
                        No venue data available
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Monthly Event Trend -->
        <x-card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Monthly Event Trend (Last 6 Months)
            </h3>
            <div class="space-y-3">
                @foreach($monthlyTrend as $month)
                <div class="flex items-center gap-3">
                    <div class="w-24 text-sm text-gray-600 dark:text-white/60">{{ $month['month'] }}</div>
                    <div class="flex-1">
                        <div class="h-8 bg-gray-200 dark:bg-white/10 rounded-lg overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center px-3 text-white text-sm font-medium"
                                 style="width: {{ $monthlyTrend->max('count') > 0 ? ($month['count'] / $monthlyTrend->max('count')) * 100 : 0 }}%">
                                @if($month['count'] > 0)
                                {{ $month['count'] }} events
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>

        <!-- Upcoming Events -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Upcoming Events (Next 30 Days)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Client</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Event Type</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Venue</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Date & Time</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Photographer</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Videographer</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($upcomingEvents as $event)
                        <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white font-medium">
                                {{ $event->order->client_display_name }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $event->event_type) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-white/80">
                                {{ $event->venue }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                <div>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-600 dark:text-white/60">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ $event->photographer?->user->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ $event->videographer?->user->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    {{ $event->status == 'completed' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400' : '' }}
                                    {{ $event->status == 'scheduled' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : '' }}
                                    {{ $event->status == 'in_progress' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400' : '' }}
                                    {{ $event->status == 'cancelled' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-600 dark:text-white/60">
                                No upcoming events in the next 30 days
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-dashboard-layout>
