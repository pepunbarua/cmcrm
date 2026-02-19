<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Deliverables</h1>
                <p class="text-gray-600 dark:text-white/60">Manage event deliverables and client files</p>
            </div>
            @can('upload deliverables')
            <x-button href="{{ route('deliverables.create') }}" variant="primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Files
            </x-button>
            @endcan
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Files" 
                :value="$stats['total']" 
                icon="folder"
                color="purple"
            />
            <x-stat-card 
                label="Pending Upload" 
                :value="$stats['pending']" 
                icon="clock"
                color="yellow"
            />
            <x-stat-card 
                label="Uploaded" 
                :value="$stats['uploaded']" 
                icon="upload"
                color="blue"
            />
            <x-stat-card 
                label="Delivered" 
                :value="$stats['delivered']" 
                icon="check-circle"
                color="green"
            />
        </div>

        <!-- Filters -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('deliverables.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="File name or client..." class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Pending</option>
                        <option value="uploaded" {{ request('status') == 'uploaded' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Uploaded</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Delivered</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Event</label>
                    <select name="event_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Events</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }} style="background-color: #1f2937; color: white;">
                            {{ $event->order->client_display_name }} - {{ $event->event_date->format('M d, Y') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('deliverables.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Deliverables Grid -->
        @if($deliverables->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($deliverables as $deliverable)
            <x-card class="hover:shadow-xl transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <!-- File Type Icon -->
                    <div class="w-16 h-16 rounded-lg flex items-center justify-center
                        @if($deliverable->file_type == 'photo') bg-blue-500/20
                        @elseif($deliverable->file_type == 'video') bg-purple-500/20
                        @elseif($deliverable->file_type == 'album') bg-pink-500/20
                        @else bg-green-500/20
                        @endif">
                        @if($deliverable->file_type == 'photo')
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @elseif($deliverable->file_type == 'video')
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        @elseif($deliverable->file_type == 'album')
                        <svg class="w-8 h-8 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        @else
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    @if($deliverable->status == 'delivered')
                    <span class="px-2 py-1 text-xs bg-green-500/20 text-green-600 dark:text-green-400 rounded-full">
                        Delivered
                    </span>
                    @elseif($deliverable->status == 'uploaded')
                    <span class="px-2 py-1 text-xs bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-full">
                        Uploaded
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 rounded-full">
                        Pending
                    </span>
                    @endif
                </div>

                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 truncate" title="{{ $deliverable->file_name }}">
                    {{ $deliverable->file_name }}
                </h3>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $deliverable->event->order->client_display_name }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $deliverable->event->event_date->format('M d, Y') }}
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        {{ number_format($deliverable->file_size / 1024 / 1024, 2) }} MB
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-white/60 capitalize">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ ucfirst($deliverable->file_type) }}
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-white/10">
                    <a href="{{ route('deliverables.download', $deliverable) }}" class="flex-1 px-3 py-1.5 text-center text-sm bg-purple-100 dark:bg-white/10 text-purple-900 dark:text-white rounded hover:bg-purple-200 dark:hover:bg-white/20 transition">
                        Download
                    </a>
                    <a href="{{ route('deliverables.show', $deliverable) }}" class="flex-1 px-3 py-1.5 text-center text-sm bg-blue-100 dark:bg-blue-500/20 text-blue-900 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-500/30 transition">
                        Details
                    </a>
                </div>
            </x-card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $deliverables->links() }}
        </div>
        @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No deliverables found</h3>
                <p class="text-gray-600 dark:text-white/60 mb-4">Start uploading event deliverables for your clients</p>
                @can('upload deliverables')
                <x-button href="{{ route('deliverables.create') }}" variant="primary">
                    Upload Files
                </x-button>
                @endcan
            </div>
        </x-card>
        @endif
    </div>
</x-dashboard-layout>
