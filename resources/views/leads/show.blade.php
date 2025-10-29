<x-dashboard-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    {{ $lead->client_name }}
                </h1>
                <p class="text-gray-400 mt-1">Lead Details</p>
            </div>
            <div class="flex gap-3">
                @can('edit lead')
                    <a href="{{ route('leads.edit', $lead) }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        Edit Lead
                    </a>
                @endcan
                @if($lead->status === 'qualified' && !$lead->order)
                    @can('create order')
                        <button onclick="convertToOrder()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            Convert to Order
                        </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Lead Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400">Client Name</p>
                        <p class="text-white font-medium">{{ $lead->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Phone</p>
                        <p class="text-white font-medium">{{ $lead->client_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Email</p>
                        <p class="text-white font-medium">{{ $lead->client_email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Vendor Source</p>
                        <p class="text-white font-medium">{{ $lead->vendor->vendor_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Event Type</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-300">
                            {{ ucfirst($lead->event_type) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Event Date</p>
                        <p class="text-white font-medium">
                            {{ $lead->event_date ? \Carbon\Carbon::parse($lead->event_date)->format('d M Y') : 'Not Set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Budget Range</p>
                        <p class="text-white font-medium">{{ $lead->budget_range ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Package Interest</p>
                        <p class="text-white font-medium">{{ $lead->package_interest ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Assigned To</p>
                        <p class="text-white font-medium">{{ $lead->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Status</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full
                            {{ $lead->status === 'new' ? 'bg-gray-500/20 text-gray-300' : '' }}
                            {{ $lead->status === 'contacted' ? 'bg-blue-500/20 text-blue-300' : '' }}
                            {{ $lead->status === 'follow_up' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                            {{ $lead->status === 'qualified' ? 'bg-purple-500/20 text-purple-300' : '' }}
                            {{ $lead->status === 'converted' ? 'bg-green-500/20 text-green-300' : '' }}
                            {{ $lead->status === 'lost' ? 'bg-red-500/20 text-red-300' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                        </span>
                    </div>
                </div>
                @if($lead->notes)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Notes</p>
                        <p class="text-white">{{ $lead->notes }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Follow-ups -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-white">Follow-ups</h2>
                    @can('create follow-up')
                        <button onclick="showFollowUpModal()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-sm">
                            + Add Follow-up
                        </button>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($lead->followUps as $followUp)
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $followUp->status === 'completed' ? 'bg-green-500/20 text-green-300' : ($followUp->status === 'scheduled' ? 'bg-blue-500/20 text-blue-300' : 'bg-yellow-500/20 text-yellow-300') }}">
                                            {{ ucfirst($followUp->status) }}
                                        </span>
                                        <span class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($followUp->follow_up_date)->format('d M Y, h:i A') }}</span>
                                    </div>
                                    <p class="text-white">{{ $followUp->notes }}</p>
                                    @if($followUp->next_follow_up_date)
                                        <p class="text-sm text-purple-400 mt-2">Next: {{ \Carbon\Carbon::parse($followUp->next_follow_up_date)->format('d M Y') }}</p>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-400">
                                    by {{ $followUp->user->name }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-8">No follow-ups yet</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right Column: Timeline & Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <button onclick="window.location.href='tel:{{ $lead->client_phone }}'" class="w-full px-4 py-2 bg-green-500/20 text-green-300 rounded-lg hover:bg-green-500/30 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call Client
                    </button>
                    @if($lead->client_email)
                        <button onclick="window.location.href='mailto:{{ $lead->client_email }}'" class="w-full px-4 py-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Email
                        </button>
                    @endif
                    @can('create follow-up')
                        <button onclick="showFollowUpModal()" class="w-full px-4 py-2 bg-purple-500/20 text-purple-300 rounded-lg hover:bg-purple-500/30 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Follow-up
                        </button>
                    @endcan
                </div>
            </x-card>

            <!-- Order Info (if converted) -->
            @if($lead->order)
                <x-card>
                    <h2 class="text-xl font-semibold text-white mb-4">Order Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-400">Order Number</p>
                            <p class="text-white font-medium">{{ $lead->order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Total Amount</p>
                            <p class="text-white font-medium">৳{{ number_format($lead->order->total_amount) }}</p>
                        </div>
                        <a href="{{ route('orders.show', $lead->order) }}" class="block w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-center">
                            View Order
                        </a>
                    </div>
                </x-card>
            @endif

            <!-- Activity Timeline -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Activity Timeline</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-purple-400 mt-1.5"></div>
                        <div>
                            <p class="text-white">Lead created</p>
                            <p class="text-gray-400 text-xs">{{ $lead->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @foreach($lead->followUps->take(3) as $followUp)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5"></div>
                            <div>
                                <p class="text-white">Follow-up {{ $followUp->status }}</p>
                                <p class="text-gray-400 text-xs">{{ $followUp->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                    @if($lead->order)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-green-400 mt-1.5"></div>
                            <div>
                                <p class="text-white">Converted to order</p>
                                <p class="text-gray-400 text-xs">{{ $lead->order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- Follow-up Modal -->
    <div id="followUpModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6">Add Follow-up</h3>
            <form id="followUpForm" method="POST" action="{{ route('follow-ups.store') }}">
                @csrf
                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Follow-up Date & Time <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="follow_up_date" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status <span class="text-red-400">*</span></label>
                        <select name="status" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="missed">Missed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Notes <span class="text-red-400">*</span></label>
                        <textarea name="notes" required rows="3" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Next Follow-up Date</label>
                        <input type="date" name="next_follow_up_date" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" id="followUpSubmitBtn" class="flex-1 px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                        Add Follow-up
                    </button>
                    <button type="button" onclick="closeFollowUpModal()" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showFollowUpModal() {
            document.getElementById('followUpModal').classList.remove('hidden');
            document.getElementById('followUpModal').classList.add('flex');
        }

        function closeFollowUpModal() {
            document.getElementById('followUpModal').classList.add('hidden');
            document.getElementById('followUpModal').classList.remove('flex');
            document.getElementById('followUpForm').reset();
        }

        document.getElementById('followUpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('followUpSubmitBtn');
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
                    submitBtn.textContent = 'Add Follow-up';
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Follow-up';
            }
        });

        function convertToOrder() {
            if (confirm('Are you sure you want to convert this lead to an order?')) {
                window.location.href = '/orders/create?lead_id={{ $lead->id }}';
            }
        }
    </script>
</x-dashboard-layout>
