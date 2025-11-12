<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fa-duotone fa-clock-rotate-left text-gray-600 mr-2"></i>
                        Call History
                    </h1>
                    <p class="text-gray-600 dark:text-white/60">View past call activities</p>
                </div>
                <a href="{{ route('call-queue.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-white/20 transition">
                    <i class="fa-duotone fa-arrow-left mr-2"></i>Back to Queue
                </a>
            </div>
        </div>

        <!-- Filters -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('call-queue.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/70 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/70 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/70 mb-2">Activity Type</label>
                    <select name="activity_type" class="w-full px-4 py-2 bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-gray-900 dark:text-white">
                        <option value="">All Types</option>
                        <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>Call</option>
                        <option value="whatsapp" {{ request('activity_type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="note" {{ request('activity_type') == 'note' ? 'selected' : '' }}>Note</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/70 mb-2">Interest Level</label>
                    <select name="interest_level" class="w-full px-4 py-2 bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-lg text-gray-900 dark:text-white">
                        <option value="">All Levels</option>
                        <option value="hot" {{ request('interest_level') == 'hot' ? 'selected' : '' }}>Hot</option>
                        <option value="warm" {{ request('interest_level') == 'warm' ? 'selected' : '' }}>Warm</option>
                        <option value="cold" {{ request('interest_level') == 'cold' ? 'selected' : '' }}>Cold</option>
                        <option value="not_interested" {{ request('interest_level') == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                        <i class="fa-duotone fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('call-queue.history') }}" class="px-4 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        <i class="fa-duotone fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Activities List -->
        @if($activities->count() > 0)
            <div class="grid gap-4">
                @foreach($activities as $activity)
                <x-card class="hover:shadow-lg transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $activity->lead->client_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-white/60">
                                        <i class="fa-duotone fa-user mr-1"></i>{{ $activity->performer->name }}
                                        <span class="mx-2">•</span>
                                        <i class="fa-duotone fa-clock mr-1"></i>{{ $activity->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white rounded-lg text-sm font-medium capitalize">
                                    <i class="fa-duotone 
                                        {{ $activity->activity_type == 'call' ? 'fa-phone' : '' }}
                                        {{ $activity->activity_type == 'whatsapp' ? 'fa-comment' : '' }}
                                        {{ $activity->activity_type == 'email' ? 'fa-envelope' : '' }}
                                        {{ $activity->activity_type == 'note' ? 'fa-note' : '' }}
                                        mr-1">
                                    </i>
                                    {{ str_replace('_', ' ', $activity->activity_type) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                @if($activity->call_outcome)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Call Outcome</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white capitalize">
                                        {{ str_replace('_', ' ', $activity->call_outcome) }}
                                    </p>
                                </div>
                                @endif

                                @if($activity->lead_interest_level)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Interest Level</p>
                                    <span class="text-sm font-semibold capitalize
                                        {{ $activity->lead_interest_level == 'hot' ? 'text-red-600' : '' }}
                                        {{ $activity->lead_interest_level == 'warm' ? 'text-orange-600' : '' }}
                                        {{ $activity->lead_interest_level == 'cold' ? 'text-blue-600' : '' }}
                                    ">
                                        <i class="fa-duotone 
                                            {{ $activity->lead_interest_level == 'hot' ? 'fa-fire' : '' }}
                                            {{ $activity->lead_interest_level == 'warm' ? 'fa-temperature-half' : '' }}
                                            {{ $activity->lead_interest_level == 'cold' ? 'fa-snowflake' : '' }}
                                            mr-1">
                                        </i>
                                        {{ str_replace('_', ' ', $activity->lead_interest_level) }}
                                    </span>
                                </div>
                                @endif

                                @if($activity->call_duration)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Duration</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $activity->call_duration }} sec
                                    </p>
                                </div>
                                @endif

                                @if($activity->follow_up_required)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-white/50 mb-1">Follow-up</p>
                                    <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                        <i class="fa-duotone fa-calendar-check mr-1"></i>
                                        {{ $activity->next_follow_up_date?->format('M d, Y') }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            @if($activity->notes)
                            <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg mb-3">
                                <p class="text-xs text-gray-700 dark:text-white/50 font-semibold mb-1">Notes:</p>
                                <p class="text-sm text-gray-700 dark:text-white/70">{{ $activity->notes }}</p>
                            </div>
                            @endif

                            @if($activity->follow_up_notes)
                            <div class="p-3 bg-orange-50 dark:bg-orange-500/10 rounded-lg">
                                <p class="text-xs text-orange-700 dark:text-orange-400 font-semibold mb-1">Follow-up Notes:</p>
                                <p class="text-sm text-gray-700 dark:text-white/70">{{ $activity->follow_up_notes }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="ml-6">
                            <a href="{{ route('leads.show', $activity->lead) }}" class="px-4 py-2 bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-white/20 transition">
                                <i class="fa-duotone fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </x-card>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <i class="fa-duotone fa-inbox text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Activities Found</h3>
                    <p class="text-gray-600 dark:text-white/60">Try adjusting your filters or check back later</p>
                </div>
            </x-card>
        @endif
    </div>
</x-dashboard-layout>
