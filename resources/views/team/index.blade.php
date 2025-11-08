<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Team Members</h1>
                <p class="text-gray-600 dark:text-white/60">Manage your team members and their availability</p>
            </div>
            @can('create team members')
            <x-button href="{{ route('team.create') }}" variant="primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Team Member
            </x-button>
            @endcan
        </div>

        <!-- Filters -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('team.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Role Type</label>
                    <select name="role_type" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Roles</option>
                        <option value="photographer" {{ request('role_type') == 'photographer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Photographer</option>
                        <option value="videographer" {{ request('role_type') == 'videographer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Videographer</option>
                        <option value="editor" {{ request('role_type') == 'editor' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Editor</option>
                        <option value="assistant" {{ request('role_type') == 'assistant' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Assistant</option>
                        <option value="sales_manager" {{ request('role_type') == 'sales_manager' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Sales Manager</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Availability</label>
                    <select name="availability_status" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Status</option>
                        <option value="available" {{ request('availability_status') == 'available' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Available</option>
                        <option value="busy" {{ request('availability_status') == 'busy' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Busy</option>
                        <option value="on_leave" {{ request('availability_status') == 'on_leave' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">On Leave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Skill Level</label>
                    <select name="skill_level" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Levels</option>
                        <option value="junior" {{ request('skill_level') == 'junior' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Junior</option>
                        <option value="mid_level" {{ request('skill_level') == 'mid_level' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Mid-Level</option>
                        <option value="senior" {{ request('skill_level') == 'senior' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Senior</option>
                        <option value="expert" {{ request('skill_level') == 'expert' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Expert</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('team.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Team Members Grid -->
        @if($teamMembers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($teamMembers as $member)
            @if($member->user)
            <x-card class="hover:shadow-xl transition-shadow cursor-pointer" onclick="window.location='{{ route('team.show', ['team' => $member]) }}'">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($member->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $member->user->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-white/60 capitalize">{{ str_replace('_', ' ', $member->role_type) }}</p>
                        </div>
                    </div>
                    
                    <!-- Availability Badge -->
                    @if($member->availability_status == 'available')
                    <span class="px-2 py-1 text-xs bg-green-500/20 text-green-600 dark:text-green-400 rounded-full">
                        Available
                    </span>
                    @elseif($member->availability_status == 'busy')
                    <span class="px-2 py-1 text-xs bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 rounded-full">
                        Busy
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs bg-red-500/20 text-red-600 dark:text-red-400 rounded-full">
                        On Leave
                    </span>
                    @endif
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $member->user->email }}
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        <span class="capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $member->skill_level) }}</span>
                    </div>
                    @if($member->hourly_rate)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ৳{{ number_format($member->hourly_rate, 2) }}/hour
                    </div>
                    @endif
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-white/10">
                    <a href="{{ route('team.show', ['team' => $member]) }}" class="flex-1 px-3 py-1.5 text-center text-sm bg-purple-100 dark:bg-white/10 text-purple-900 dark:text-white rounded hover:bg-purple-200 dark:hover:bg-white/20 transition">
                        View Details
                    </a>
                    @can('edit team members')
                    <a href="{{ route('team.edit', ['team' => $member]) }}" class="flex-1 px-3 py-1.5 text-center text-sm bg-blue-100 dark:bg-blue-500/20 text-blue-900 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-500/30 transition">
                        Edit
                    </a>
                    @endcan
                </div>
            </x-card>
            @endif
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $teamMembers->links() }}
        </div>
        @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No team members found</h3>
                <p class="text-gray-600 dark:text-white/60 mb-4">Get started by adding your first team member</p>
                @can('create team members')
                <x-button href="{{ route('team.create') }}" variant="primary">
                    Add Team Member
                </x-button>
                @endcan
            </div>
        </x-card>
        @endif
    </div>
</x-dashboard-layout>
