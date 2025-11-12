<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">📞 Call Queue</h1>
            <p class="text-gray-600 dark:text-white/60">Manage and track all your lead calls efficiently</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                title="Today's Calls" 
                :value="(string)$stats['total_today']" 
                icon="calendar-day"
                color="purple"
            />
            <x-stat-card 
                title="Pending Calls" 
                :value="(string)$stats['pending_calls']" 
                icon="phone"
                color="blue"
            />
            <x-stat-card 
                title="Follow-ups" 
                :value="(string)$stats['follow_ups']" 
                icon="rotate"
                color="orange"
            />
            <x-stat-card 
                title="Completed Today" 
                :value="(string)$stats['completed_today']" 
                icon="check-circle"
                color="green"
            />
        </div>

        <!-- Quick Actions -->
        <x-card class="mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('call-queue.dialer') }}" class="block p-6 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fa-duotone fa-phone-volume text-2xl text-white"></i>
                        </div>
                        <div class="flex-1 text-white">
                            <h3 class="font-bold text-lg">🎯 Lead Dialer</h3>
                            <p class="text-sm text-white/80">Start calling leads</p>
                        </div>
                        <i class="fa-duotone fa-arrow-right text-white text-xl"></i>
                    </div>
                </a>

                <a href="{{ route('call-queue.today') }}" class="block p-6 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fa-duotone fa-calendar-day text-2xl text-white"></i>
                        </div>
                        <div class="flex-1 text-white">
                            <h3 class="font-bold text-lg">📅 Today's Calls</h3>
                            <p class="text-sm text-white/80">{{ $stats['total_today'] }} scheduled</p>
                        </div>
                        <i class="fa-duotone fa-arrow-right text-white text-xl"></i>
                    </div>
                </a>

                <a href="{{ route('call-queue.follow-ups') }}" class="block p-6 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl hover:shadow-xl transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                            <i class="fa-duotone fa-rotate text-2xl text-white"></i>
                        </div>
                        <div class="flex-1 text-white">
                            <h3 class="font-bold text-lg">🔄 Follow-ups</h3>
                            <p class="text-sm text-white/80">{{ $stats['follow_ups'] }} pending</p>
                        </div>
                        <i class="fa-duotone fa-arrow-right text-white text-xl"></i>
                    </div>
                </a>
            </div>
        </x-card>

        <!-- Call Queue Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scheduled Calls -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fa-duotone fa-calendar-clock text-blue-600 mr-2"></i>
                        Scheduled Calls
                    </h3>
                    <a href="{{ route('call-queue.scheduled') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                        View All <i class="fa-duotone fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <p class="text-gray-600 dark:text-white/60 text-sm mb-4">Upcoming follow-ups and scheduled calls</p>
                <a href="{{ route('call-queue.scheduled') }}" class="block w-full py-3 text-center bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-500/30 transition font-medium">
                    View Scheduled Calls
                </a>
            </x-card>

            <!-- Pending Calls -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fa-duotone fa-hourglass-half text-orange-600 mr-2"></i>
                        Pending Calls
                    </h3>
                    <a href="{{ route('call-queue.pending') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                        View All <i class="fa-duotone fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <p class="text-gray-600 dark:text-white/60 text-sm mb-4">New leads waiting to be contacted</p>
                <a href="{{ route('call-queue.pending') }}" class="block w-full py-3 text-center bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-500/30 transition font-medium">
                    View Pending Calls
                </a>
            </x-card>

            <!-- Call History -->
            <x-card class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fa-duotone fa-clock-rotate-left text-green-600 mr-2"></i>
                        Call History
                    </h3>
                    <a href="{{ route('call-queue.history') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                        View All <i class="fa-duotone fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <p class="text-gray-600 dark:text-white/60 text-sm mb-4">Track all your past call activities and outcomes</p>
                <a href="{{ route('call-queue.history') }}" class="block w-full py-3 text-center bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-500/30 transition font-medium">
                    View Call History
                </a>
            </x-card>
        </div>
    </div>
</x-dashboard-layout>
