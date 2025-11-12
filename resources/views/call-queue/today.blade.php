<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fa-duotone fa-calendar-day text-blue-600 mr-2"></i>
                        Today's Call List
                    </h1>
                    <p class="text-gray-600 dark:text-white/60">Follow-ups scheduled for today</p>
                </div>
                <a href="{{ route('call-queue.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-white/20 transition">
                    <i class="fa-duotone fa-arrow-left mr-2"></i>Back to Queue
                </a>
            </div>
        </div>

        <!-- Leads List -->
        @if($leads->count() > 0)
            <div class="grid gap-4">
                @foreach($leads as $lead)
                @php
                    $latestActivity = $lead->leadActivities()
                        ->where('follow_up_required', true)
                        ->whereDate('next_follow_up_date', today())
                        ->orderBy('next_follow_up_time')
                        ->first();
                @endphp
                <x-card class="hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $lead->client_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-white/60 capitalize">{{ str_replace('_', ' ', $lead->event_type) }} Event</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-900 dark:text-blue-300 rounded-lg text-sm font-medium">
                                        <i class="fa-duotone fa-clock mr-1"></i>{{ $latestActivity?->next_follow_up_time ?? 'Not Set' }}
                                    </span>
                                    @if($latestActivity && $latestActivity->next_follow_up_time)
                                    <p class="text-xs text-gray-500 dark:text-white/50 mt-1">
                                        {{ \Carbon\Carbon::parse($latestActivity->next_follow_up_time)->diffForHumans() }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Phone</p>
                                    <a href="tel:{{ $lead->client_phone }}" class="text-sm font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                                        <i class="fa-duotone fa-phone mr-1"></i>{{ $lead->client_phone }}
                                    </a>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Event Date</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $lead->event_date->format('M d, Y') }}
                                    </p>
                                </div>

                                @if($latestActivity && $latestActivity->lead_interest_level)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Interest</p>
                                    <span class="text-sm font-semibold capitalize
                                        {{ $latestActivity->lead_interest_level == 'hot' ? 'text-red-600' : '' }}
                                        {{ $latestActivity->lead_interest_level == 'warm' ? 'text-orange-600' : '' }}
                                        {{ $latestActivity->lead_interest_level == 'cold' ? 'text-blue-600' : '' }}
                                    ">
                                        {{ str_replace('_', ' ', $latestActivity->lead_interest_level) }}
                                    </span>
                                </div>
                                @endif

                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Status</p>
                                    <span class="text-sm font-semibold capitalize text-gray-900 dark:text-white">
                                        {{ str_replace('_', ' ', $lead->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($latestActivity && $latestActivity->follow_up_notes)
                            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg">
                                <p class="text-xs text-blue-700 dark:text-blue-400 font-semibold mb-1">Follow-up Notes:</p>
                                <p class="text-sm text-gray-700 dark:text-white/70">{{ $latestActivity->follow_up_notes }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="ml-6 flex flex-col gap-2">
                            <a href="{{ route('leads.show', $lead) }}" class="px-4 py-2 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-500/30 transition text-center">
                                <i class="fa-duotone fa-eye mr-2"></i>View
                            </a>
                        </div>
                    </div>
                </x-card>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $leads->links() }}
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <i class="fa-duotone fa-calendar-check text-6xl text-blue-500 dark:text-blue-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Calls Scheduled for Today</h3>
                    <p class="text-gray-600 dark:text-white/60">Check back later or schedule some follow-ups!</p>
                </div>
            </x-card>
        @endif
    </div>
</x-dashboard-layout>
