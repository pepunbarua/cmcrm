<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Team Performance Report</h1>
                    <p class="text-gray-600 dark:text-white/60">Team member efficiency and workload analysis</p>
                </div>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Back to Overview
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('reports.team') }}" class="flex items-end gap-4">
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

        <!-- Team Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Members" 
                :value="$stats['total_members']" 
                icon="users"
                color="purple"
            />
            <x-stat-card 
                label="Available" 
                :value="$stats['available_members']" 
                icon="check-circle"
                color="green"
            />
            <x-stat-card 
                label="Busy" 
                :value="$stats['busy_members']" 
                icon="clock"
                color="yellow"
            />
            <x-stat-card 
                label="On Leave" 
                :value="$stats['on_leave']" 
                icon="x-circle"
                color="red"
            />
        </div>

        <!-- Team Performance -->
        <x-card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Individual Performance (Events in Period)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Team Member</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Skill Level</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">As Photographer</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">As Videographer</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Total Events</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($teamPerformance as $member)
                        <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->user->name }}</div>
                                        <div class="text-xs text-gray-600 dark:text-white/60">{{ $member->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $member->role_type) }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    {{ $member->skill_level == 'expert' ? 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400' : '' }}
                                    {{ $member->skill_level == 'senior' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : '' }}
                                    {{ $member->skill_level == 'intermediate' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400' : '' }}
                                    {{ $member->skill_level == 'junior' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400' : '' }}">
                                    {{ ucfirst($member->skill_level) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ $member->photographer_events_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-lg font-bold text-pink-600 dark:text-pink-400">
                                    {{ $member->videographer_events_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ $member->photographer_events_count + $member->videographer_events_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    {{ $member->availability_status == 'available' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400' : '' }}
                                    {{ $member->availability_status == 'busy' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400' : '' }}
                                    {{ $member->availability_status == 'on_leave' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $member->availability_status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-600 dark:text-white/60">
                                No team members found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Team Distribution by Role -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Team Distribution by Role
                </h3>
                <div class="space-y-3">
                    @php
                        $maxRole = $teamByRole->max('count');
                    @endphp
                    @forelse($teamByRole as $role)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-white/80 capitalize">{{ str_replace('_', ' ', $role->role_type) }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->count }} members</span>
                        </div>
                        <div class="h-3 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full
                                {{ $role->role_type == 'photographer' ? 'bg-blue-500' : '' }}
                                {{ $role->role_type == 'videographer' ? 'bg-pink-500' : '' }}
                                {{ $role->role_type == 'both' ? 'bg-purple-500' : '' }}"
                                 style="width: {{ $maxRole > 0 ? ($role->count / $maxRole) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600 dark:text-white/60">
                        No role data available
                    </div>
                    @endforelse
                </div>
            </x-card>

            <!-- Team Distribution by Skill Level -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Team Distribution by Skill Level
                </h3>
                <div class="space-y-3">
                    @php
                        $maxSkill = $teamBySkill->max('count');
                    @endphp
                    @forelse($teamBySkill as $skill)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-white/80 capitalize">{{ $skill->skill_level }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $skill->count }} members</span>
                        </div>
                        <div class="h-3 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full
                                {{ $skill->skill_level == 'expert' ? 'bg-purple-500' : '' }}
                                {{ $skill->skill_level == 'senior' ? 'bg-blue-500' : '' }}
                                {{ $skill->skill_level == 'intermediate' ? 'bg-green-500' : '' }}
                                {{ $skill->skill_level == 'junior' ? 'bg-yellow-500' : '' }}"
                                 style="width: {{ $maxSkill > 0 ? ($skill->count / $maxSkill) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600 dark:text-white/60">
                        No skill level data available
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</x-dashboard-layout>
