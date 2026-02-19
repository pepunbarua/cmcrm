<x-dashboard-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Events Calendar
        </h1>
        @can('create events')
            <a href="{{ route('events.create') }}" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                + Schedule Event
            </a>
        @endcan
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('events.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client name..." class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                    Filter
                </button>
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Reset
                </a>
            </div>
        </form>
    </x-card>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            <x-card class="hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location.href='{{ route('events.show', $event) }}'">
                <div class="space-y-4">
                    <!-- Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-white">{{ $event->order->client_display_name }}</h3>
                            <p class="text-sm text-gray-400">{{ ucfirst($event->order->event_type) }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $event->status === 'scheduled' ? 'bg-blue-500/20 text-blue-300' : '' }}
                            {{ $event->status === 'in_progress' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                            {{ $event->status === 'completed' ? 'bg-green-500/20 text-green-300' : '' }}
                            {{ $event->status === 'cancelled' ? 'bg-red-500/20 text-red-300' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                        </span>
                    </div>

                    <!-- Date & Time -->
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-white">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</span>
                        <span class="text-gray-400">{{ $event->event_time }}</span>
                    </div>

                    <!-- Venue -->
                    <div class="flex items-start gap-2 text-sm">
                        <svg class="w-4 h-4 text-purple-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-gray-300">{{ $event->venue }}</span>
                    </div>

                    <!-- Team -->
                    <div class="pt-3 border-t border-white/10 space-y-2">
                        @if($event->photographer)
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-gray-300">{{ $event->photographer->user->name }}</span>
                            </div>
                        @endif
                        @if($event->videographer)
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-300">{{ $event->videographer->user->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Countdown -->
                    @if($event->status === 'scheduled')
                        @php
                            $daysUntil = \Carbon\Carbon::parse($event->event_date)->diffInDays(now(), false);
                        @endphp
                        <div class="pt-3 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-400">Countdown</span>
                                <span class="text-lg font-bold {{ $daysUntil < 0 ? 'text-red-400' : 'text-purple-400' }}">
                                    {{ abs($daysUntil) }} days {{ $daysUntil < 0 ? 'overdue' : 'left' }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="col-span-3">
                <x-card>
                    <p class="text-gray-400 text-center py-8">No events scheduled yet</p>
                </x-card>
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</x-dashboard-layout>
