<x-dashboard-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    {{ $event->order->client_display_name }}
                </h1>
                <p class="text-gray-400 mt-1">{{ ucfirst($event->order->event_type) }} Event</p>
            </div>
            <div class="flex gap-3">
                @can('edit events')
                    <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        Edit Event
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Event Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Information -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Event Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400">Event Date</p>
                        <p class="text-white font-medium text-lg">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Event Time</p>
                        <p class="text-white font-medium">{{ $event->event_time }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Venue</p>
                        <p class="text-white font-medium">{{ $event->venue }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Status</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full
                            {{ $event->status === 'scheduled' ? 'bg-blue-500/20 text-blue-300' : '' }}
                            {{ $event->status === 'in_progress' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                            {{ $event->status === 'completed' ? 'bg-green-500/20 text-green-300' : '' }}
                            {{ $event->status === 'cancelled' ? 'bg-red-500/20 text-red-300' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                        </span>
                    </div>
                </div>
                @if($event->venue_address)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Venue Address</p>
                        <p class="text-white">{{ $event->venue_address }}</p>
                    </div>
                @endif
                @if($event->special_instructions)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Special Instructions</p>
                        <p class="text-white">{{ $event->special_instructions }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Team Assignment -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Team Assignment</h2>
                <div class="grid grid-cols-2 gap-4">
                    @if($event->photographer)
                        <div class="p-4 rounded-lg bg-blue-500/10 border border-blue-500/20">
                            <p class="text-sm text-gray-400 mb-2">Photographer</p>
                            <p class="text-white font-medium">{{ $event->photographer->user->name }}</p>
                            <p class="text-sm text-gray-400">{{ $event->photographer->user->phone ?? 'N/A' }}</p>
                        </div>
                    @endif
                    @if($event->videographer)
                        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20">
                            <p class="text-sm text-gray-400 mb-2">Videographer</p>
                            <p class="text-white font-medium">{{ $event->videographer->user->name }}</p>
                            <p class="text-sm text-gray-400">{{ $event->videographer->user->phone ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Equipment Checklist -->
            @if($event->equipment_checklist)
                <x-card>
                    <h2 class="text-xl font-semibold text-white mb-4">Equipment Checklist</h2>
                    <p class="text-gray-300 whitespace-pre-line">{{ $event->equipment_checklist }}</p>
                </x-card>
            @endif

            <!-- Event Notes -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-white">Event Notes</h2>
                    <button onclick="showNoteModal()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-sm">
                        + Add Note
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($event->notes as $note)
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-white">{{ $note->note }}</p>
                                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-400">
                                        <span>{{ $note->user->name }}</span>
                                        <span>•</span>
                                        <span>{{ $note->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-8">No notes yet</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right Column: Countdown & Order Info -->
        <div class="space-y-6">
            <!-- Countdown Card -->
            @if($event->status === 'scheduled')
                <x-card class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 border-purple-500/30">
                    <h2 class="text-xl font-semibold text-white mb-4">Countdown</h2>
                    @php
                        $daysUntil = \Carbon\Carbon::parse($event->event_date)->diffInDays(now(), false);
                        $isOverdue = $daysUntil < 0;
                    @endphp
                    <div class="text-center py-6">
                        <div class="text-6xl font-bold {{ $isOverdue ? 'text-red-400' : 'text-purple-300' }}">
                            {{ abs($daysUntil) }}
                        </div>
                        <div class="text-lg text-gray-300 mt-2">
                            days {{ $isOverdue ? 'overdue' : 'until event' }}
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Delivery Deadline -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Delivery Deadline</h2>
                @php
                    $deliveryDays = \Carbon\Carbon::parse($event->delivery_deadline)->diffInDays(now(), false);
                    $deliveryOverdue = $deliveryDays < 0;
                @endphp
                <div class="text-center py-4">
                    <div class="text-3xl font-bold {{ $deliveryOverdue ? 'text-red-400' : 'text-green-400' }}">
                        {{ \Carbon\Carbon::parse($event->delivery_deadline)->format('d M Y') }}
                    </div>
                    <div class="text-sm text-gray-300 mt-2">
                        {{ abs($deliveryDays) }} days {{ $deliveryOverdue ? 'overdue' : 'remaining' }}
                    </div>
                </div>
            </x-card>

            <!-- Order Information -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Order Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-400">Order Number</p>
                        <p class="text-white font-mono font-medium">{{ $event->order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Client Phone</p>
                        <p class="text-white font-medium">{{ $event->order->client_display_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Package</p>
                        <p class="text-white font-medium">{{ $event->order->package_display_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Total Amount</p>
                        <p class="text-white font-medium">৳{{ number_format($event->order->total_amount) }}</p>
                    </div>
                    <a href="{{ route('orders.show', $event->order) }}" class="block w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-center mt-4">
                        View Order
                    </a>
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <button onclick="window.location.href='tel:{{ $event->order->client_display_phone }}'" class="w-full px-4 py-2 bg-green-500/20 text-green-300 rounded-lg hover:bg-green-500/30 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call Client
                    </button>
                    @if($event->photographer && $event->photographer->user->phone)
                        <button onclick="window.location.href='tel:{{ $event->photographer->user->phone }}'" class="w-full px-4 py-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Call Photographer
                        </button>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- Note Modal -->
    <div id="noteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6">Add Event Note</h3>
            <form id="noteForm" method="POST" action="{{ route('event-notes.store') }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Note <span class="text-red-400">*</span></label>
                        <textarea name="note" required rows="4" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" id="noteSubmitBtn" class="flex-1 px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                        Add Note
                    </button>
                    <button type="button" onclick="closeNoteModal()" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showNoteModal() {
            document.getElementById('noteModal').classList.remove('hidden');
            document.getElementById('noteModal').classList.add('flex');
        }

        function closeNoteModal() {
            document.getElementById('noteModal').classList.add('hidden');
            document.getElementById('noteModal').classList.remove('flex');
            document.getElementById('noteForm').reset();
        }

        document.getElementById('noteForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('noteSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'An error occurred', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Add Note';
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Note';
            }
        });
    </script>
</x-dashboard-layout>
