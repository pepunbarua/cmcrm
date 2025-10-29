<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Reports Overview</h1>
            <p class="text-gray-600 dark:text-white/60">Comprehensive analytics and business insights</p>
        </div>

        <!-- Date Range Filter -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-end gap-4">
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

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-stat-card 
                label="Total Revenue" 
                :value="'৳' . number_format($stats['total_revenue'], 2)" 
                icon="currency-dollar"
                color="green"
            />
            <x-stat-card 
                label="Total Orders" 
                :value="$stats['total_orders']" 
                icon="shopping-cart"
                color="blue"
            />
            <x-stat-card 
                label="Total Events" 
                :value="$stats['total_events']" 
                icon="calendar"
                color="purple"
            />
            <x-stat-card 
                label="Total Leads" 
                :value="$stats['total_leads']" 
                icon="users"
                color="yellow"
            />
            <x-stat-card 
                label="Conversion Rate" 
                :value="$stats['conversion_rate'] . '%'" 
                icon="trending-up"
                color="pink"
            />
            <x-stat-card 
                label="Avg Order Value" 
                :value="'৳' . number_format($stats['average_order_value'], 2)" 
                icon="chart-bar"
                color="indigo"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue Trend -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Monthly Revenue Trend (Last 6 Months)
                </h3>
                <div class="space-y-3">
                    @foreach($revenueData as $data)
                    <div class="flex items-center gap-3">
                        <div class="w-24 text-sm text-gray-600 dark:text-white/60">{{ $data['month'] }}</div>
                        <div class="flex-1">
                            <div class="h-8 bg-gray-200 dark:bg-white/10 rounded-lg overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center px-3 text-white text-sm font-medium"
                                     style="width: {{ $revenueData->max('revenue') > 0 ? ($data['revenue'] / $revenueData->max('revenue')) * 100 : 0 }}%">
                                    @if($data['revenue'] > 0)
                                    ৳{{ number_format($data['revenue'], 0) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Order Status Distribution -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Order Status Distribution
                </h3>
                <div class="space-y-3">
                    @foreach($orderStatusData as $status)
                    <div class="flex items-center justify-between p-3 rounded-lg 
                        @if($status->status == 'confirmed') bg-green-50 dark:bg-green-500/10
                        @elseif($status->status == 'pending') bg-yellow-50 dark:bg-yellow-500/10
                        @elseif($status->status == 'completed') bg-blue-50 dark:bg-blue-500/10
                        @else bg-red-50 dark:bg-red-500/10
                        @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                @if($status->status == 'confirmed') bg-green-500
                                @elseif($status->status == 'pending') bg-yellow-500
                                @elseif($status->status == 'completed') bg-blue-500
                                @else bg-red-500
                                @endif"></div>
                            <span class="font-medium capitalize text-gray-900 dark:text-white">{{ $status->status }}</span>
                        </div>
                        <span class="text-lg font-bold
                            @if($status->status == 'confirmed') text-green-600 dark:text-green-400
                            @elseif($status->status == 'pending') text-yellow-600 dark:text-yellow-400
                            @elseif($status->status == 'completed') text-blue-600 dark:text-blue-400
                            @else text-red-600 dark:text-red-400
                            @endif">
                            {{ $status->count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Event Status Distribution -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Event Status Distribution
                </h3>
                <div class="space-y-3">
                    @foreach($eventStatusData as $status)
                    <div class="flex items-center justify-between p-3 rounded-lg
                        @if($status->status == 'completed') bg-green-50 dark:bg-green-500/10
                        @elseif($status->status == 'scheduled') bg-blue-50 dark:bg-blue-500/10
                        @elseif($status->status == 'in_progress') bg-yellow-50 dark:bg-yellow-500/10
                        @else bg-red-50 dark:bg-red-500/10
                        @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                @if($status->status == 'completed') bg-green-500
                                @elseif($status->status == 'scheduled') bg-blue-500
                                @elseif($status->status == 'in_progress') bg-yellow-500
                                @else bg-red-500
                                @endif"></div>
                            <span class="font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $status->status) }}</span>
                        </div>
                        <span class="text-lg font-bold
                            @if($status->status == 'completed') text-green-600 dark:text-green-400
                            @elseif($status->status == 'scheduled') text-blue-600 dark:text-blue-400
                            @elseif($status->status == 'in_progress') text-yellow-600 dark:text-yellow-400
                            @else text-red-600 dark:text-red-400
                            @endif">
                            {{ $status->count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Top Performing Team Members -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Top Performing Team Members
                </h3>
                <div class="space-y-3">
                    @forelse($topTeamMembers as $index => $member)
                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-purple-50 dark:hover:bg-white/5 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $member->user->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-white/60 capitalize">{{ str_replace('_', ' ', $member->role_type) }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $member->total_completed }}</div>
                            <div class="text-xs text-gray-600 dark:text-white/60">Events</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600 dark:text-white/60">
                        No completed events in this period
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Quick Links to Detailed Reports -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('reports.revenue') }}" class="block p-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-white/20 hover:border-purple-500 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-500/10 transition text-center group">
                <svg class="w-12 h-12 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Revenue Report</h3>
                <p class="text-sm text-gray-600 dark:text-white/60">Detailed revenue analysis and trends</p>
            </a>

            <a href="{{ route('reports.events') }}" class="block p-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-white/20 hover:border-purple-500 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-500/10 transition text-center group">
                <svg class="w-12 h-12 mx-auto mb-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Events Report</h3>
                <p class="text-sm text-gray-600 dark:text-white/60">Event analytics and schedules</p>
            </a>

            <a href="{{ route('reports.team') }}" class="block p-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-white/20 hover:border-purple-500 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-500/10 transition text-center group">
                <svg class="w-12 h-12 mx-auto mb-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Team Performance</h3>
                <p class="text-sm text-gray-600 dark:text-white/60">Team member efficiency metrics</p>
            </a>
        </div>
    </div>
</x-dashboard-layout>
