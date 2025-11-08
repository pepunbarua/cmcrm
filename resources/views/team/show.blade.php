<x-dashboard-layout>
    <div class="p-6">
        @if(!$teamMember->user)
            <x-card>
                <div class="text-center py-12">
                    <div class="text-red-500 dark:text-red-400 mb-4">
                        <i class="fa-duotone fa-triangle-exclamation text-6xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">User Not Found</h2>
                    <p class="text-gray-600 dark:text-white/60 mb-4">
                        This team member's user account has been deleted or is missing.
                    </p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('team.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            Back to Team
                        </a>
                        @can('delete team members')
                        <form action="{{ route('team.destroy', ['team' => $teamMember]) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                Delete This Record
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </x-card>
        @else
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/60 mb-2">
                <a href="{{ route('team.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Team Members</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span>{{ $teamMember->user->name }}</span>
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $teamMember->user->name }}</h1>
                    <p class="text-gray-600 dark:text-white/60 capitalize">{{ str_replace('_', ' ', $teamMember->role_type) }}</p>
                </div>
                <div class="flex gap-2">
                    @can('edit team members')
                    <x-button href="{{ route('team.edit', ['team' => $teamMember]) }}" variant="secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-button>
                    @endcan
                    
                    @can('delete team members')
                    <button onclick="deleteMember()" class="px-4 py-2 bg-red-100 dark:bg-red-500/20 text-red-900 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-500/30 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Statistics -->
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
                label="Upcoming" 
                :value="$stats['upcoming_events']" 
                icon="clock"
                color="blue"
            />
            <x-stat-card 
                label="In Progress" 
                :value="$stats['in_progress_events']" 
                icon="play"
                color="yellow"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Personal & Professional Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <x-card>
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                            {{ strtoupper(substr($teamMember->user->name, 0, 2)) }}
                        </div>
                        
                        <!-- Availability Badge -->
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-4
                            @if($teamMember->availability_status == 'available') bg-green-500/20 text-green-600 dark:text-green-400
                            @elseif($teamMember->availability_status == 'busy') bg-yellow-500/20 text-yellow-600 dark:text-yellow-400
                            @else bg-red-500/20 text-red-600 dark:text-red-400
                            @endif">
                            <div class="w-2 h-2 rounded-full 
                                @if($teamMember->availability_status == 'available') bg-green-500
                                @elseif($teamMember->availability_status == 'busy') bg-yellow-500
                                @else bg-red-500
                                @endif">
                            </div>
                            <span class="font-medium capitalize">{{ str_replace('_', ' ', $teamMember->availability_status) }}</span>
                        </div>

                        <!-- Quick Availability Toggle -->
                        @can('edit team members')
                        <div class="flex gap-2 justify-center">
                            <button onclick="updateAvailability('available')" class="px-3 py-1 text-xs bg-green-100 dark:bg-green-500/20 text-green-900 dark:text-green-400 rounded hover:bg-green-200 dark:hover:bg-green-500/30 transition">
                                Available
                            </button>
                            <button onclick="updateAvailability('busy')" class="px-3 py-1 text-xs bg-yellow-100 dark:bg-yellow-500/20 text-yellow-900 dark:text-yellow-400 rounded hover:bg-yellow-200 dark:hover:bg-yellow-500/30 transition">
                                Busy
                            </button>
                            <button onclick="updateAvailability('on_leave')" class="px-3 py-1 text-xs bg-red-100 dark:bg-red-500/20 text-red-900 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-500/30 transition">
                                On Leave
                            </button>
                        </div>
                        @endcan
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $teamMember->user->email }}</span>
                        </div>
                        @if($teamMember->user->phone)
                        <div class="flex items-center gap-3 text-gray-600 dark:text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $teamMember->user->phone }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-3 text-gray-600 dark:text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="capitalize">{{ str_replace('_', ' ', $teamMember->role_type) }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <span class="capitalize">{{ str_replace('_', ' ', $teamMember->skill_level) }}</span>
                        </div>
                        @if($teamMember->hourly_rate)
                        <div class="flex items-center gap-3 text-gray-600 dark:text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>৳{{ number_format($teamMember->hourly_rate, 2) }}/hour</span>
                        </div>
                        @endif
                    </div>
                </x-card>

                <!-- Equipment -->
                @if($teamMember->equipment_owned)
                <x-card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Equipment Owned
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-white/60 whitespace-pre-line">{{ $teamMember->equipment_owned }}</p>
                </x-card>
                @endif

                <!-- Portfolio -->
                @if($teamMember->portfolio_link)
                <x-card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Portfolio</h3>
                    <a href="{{ $teamMember->portfolio_link }}" target="_blank" class="text-sm text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-2">
                        {{ $teamMember->portfolio_link }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </x-card>
                @endif
            </div>

            <!-- Right Column: Events & Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Assigned Events as Photographer -->
                @if($teamMember->assignedEventsAsPhotographer->count() > 0)
                <x-card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        Events as Photographer ({{ $teamMember->assignedEventsAsPhotographer->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($teamMember->assignedEventsAsPhotographer->take(5) as $event)
                        <a href="{{ route('events.show', $event) }}" class="block p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $event->order->lead->client_name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-white/60">{{ $event->venue }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($event->status == 'scheduled') bg-blue-500/20 text-blue-600 dark:text-blue-400
                                    @elseif($event->status == 'in_progress') bg-yellow-500/20 text-yellow-600 dark:text-yellow-400
                                    @elseif($event->status == 'completed') bg-green-500/20 text-green-600 dark:text-green-400
                                    @else bg-red-500/20 text-red-600 dark:text-red-400
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-white/40">
                                {{ $event->event_date->format('M d, Y') }} at {{ $event->event_time }}
                            </p>
                        </a>
                        @endforeach
                    </div>
                </x-card>
                @endif

                <!-- Assigned Events as Videographer -->
                @if($teamMember->assignedEventsAsVideographer->count() > 0)
                <x-card>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Events as Videographer ({{ $teamMember->assignedEventsAsVideographer->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($teamMember->assignedEventsAsVideographer->take(5) as $event)
                        <a href="{{ route('events.show', $event) }}" class="block p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-pink-50 dark:hover:bg-white/5 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $event->order->lead->client_name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-white/60">{{ $event->venue }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($event->status == 'scheduled') bg-blue-500/20 text-blue-600 dark:text-blue-400
                                    @elseif($event->status == 'in_progress') bg-yellow-500/20 text-yellow-600 dark:text-yellow-400
                                    @elseif($event->status == 'completed') bg-green-500/20 text-green-600 dark:text-green-400
                                    @else bg-red-500/20 text-red-600 dark:text-red-400
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-white/40">
                                {{ $event->event_date->format('M d, Y') }} at {{ $event->event_time }}
                            </p>
                        </a>
                        @endforeach
                    </div>
                </x-card>
                @endif

                <!-- No Events -->
                @if($teamMember->assignedEventsAsPhotographer->count() == 0 && $teamMember->assignedEventsAsVideographer->count() == 0)
                <x-card>
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No events assigned yet</h3>
                        <p class="text-gray-600 dark:text-white/60">This team member hasn't been assigned to any events</p>
                    </div>
                </x-card>
                @endif
            </div>
        </div>
    </div>

    <script>
        async function updateAvailability(status) {
            try {
                const response = await fetch('{{ route("team.update-availability", ['teamMember' => $teamMember]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: 'PATCH',
                        availability_status: status
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }

        async function deleteMember() {
            if (!confirm('Are you sure you want to delete this team member? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("team.destroy", ['team' => $teamMember]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    </script>
    @endif
</x-dashboard-layout>
