<x-dashboard-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Order {{ $order->order_number }}
                </h1>
                <p class="text-gray-400 mt-1">{{ $order->lead->client_name }}</p>
            </div>
            <div class="flex gap-3">
                @can('edit order')
                    <a href="{{ route('orders.edit', $order) }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        Edit Order
                    </a>
                @endcan
                @if(!$order->event)
                    @can('create event')
                        <a href="{{ route('events.create', ['order_id' => $order->id]) }}" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            Schedule Event
                        </a>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Order Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400">Order Number</p>
                        <p class="text-white font-mono font-medium text-lg">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Order Date</p>
                        <p class="text-white font-medium">{{ $order->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Package Name</p>
                        <p class="text-white font-medium">{{ $order->package_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Payment Status</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full
                            {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-300' : '' }}
                            {{ $order->payment_status === 'partial' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                            {{ $order->payment_status === 'pending' ? 'bg-red-500/20 text-red-300' : '' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
                @if($order->package_details)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-gray-400 mb-2">Package Details</p>
                        <p class="text-white">{{ $order->package_details }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Client Information -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Client Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-400">Client Name</p>
                        <p class="text-white font-medium">{{ $order->lead->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Phone</p>
                        <p class="text-white font-medium">{{ $order->lead->client_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Email</p>
                        <p class="text-white font-medium">{{ $order->lead->client_email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Event Type</p>
                        <span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-300">
                            {{ ucfirst($order->lead->event_type) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Event Date</p>
                        <p class="text-white font-medium">
                            {{ $order->lead->event_date ? \Carbon\Carbon::parse($order->lead->event_date)->format('d M Y') : 'Not Set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Vendor Source</p>
                        <p class="text-white font-medium">{{ $order->lead->vendor->name }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Payment History -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-white">Payment History</h2>
                    @can('create payment')
                        <button onclick="showPaymentModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                            + Add Payment
                        </button>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($order->payments as $payment)
                        <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-semibold">৳{{ number_format($payment->amount) }}</p>
                                    <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $payment->payment_method }} • {{ ucfirst($payment->status) }}</p>
                                    @if($payment->notes)
                                        <p class="text-sm text-gray-300 mt-2">{{ $payment->notes }}</p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 text-xs rounded-full {{ $payment->status === 'completed' ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-8">No payments recorded yet</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right Column: Summary & Event -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Payment Summary</h2>
                @php
                    $paidAmount = $order->payments->where('status', 'completed')->sum('amount');
                    $dueAmount = $order->total_amount - $paidAmount;
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-white/10">
                        <span class="text-gray-400">Total Amount</span>
                        <span class="text-white font-semibold text-lg">৳{{ number_format($order->total_amount) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center pb-3 border-b border-white/10">
                            <span class="text-gray-400">Discount</span>
                            <span class="text-green-400">-৳{{ number_format($order->discount_amount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pb-3 border-b border-white/10">
                        <span class="text-gray-400">Paid Amount</span>
                        <span class="text-green-400 font-semibold">৳{{ number_format($paidAmount) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-300 font-medium">Due Amount</span>
                        <span class="text-red-400 font-bold text-xl">৳{{ number_format($dueAmount) }}</span>
                    </div>
                </div>

                @if($dueAmount > 0)
                    <div class="mt-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20">
                        <p class="text-red-300 text-sm">⚠️ Payment pending: ৳{{ number_format($dueAmount) }}</p>
                    </div>
                @else
                    <div class="mt-4 p-3 rounded-lg bg-green-500/10 border border-green-500/20">
                        <p class="text-green-300 text-sm">✓ Fully paid</p>
                    </div>
                @endif
            </x-card>

            <!-- Event Information -->
            @if($order->event)
                <x-card>
                    <h2 class="text-xl font-semibold text-white mb-4">Event Details</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-400">Event Date</p>
                            <p class="text-white font-medium">{{ \Carbon\Carbon::parse($order->event->event_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Venue</p>
                            <p class="text-white font-medium">{{ $order->event->venue }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Delivery Deadline</p>
                            <p class="text-white font-medium">{{ \Carbon\Carbon::parse($order->event->delivery_deadline)->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('events.show', $order->event) }}" class="block w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-center mt-4">
                            View Event Details
                        </a>
                    </div>
                </x-card>
            @else
                <x-card>
                    <h2 class="text-xl font-semibold text-white mb-4">Event</h2>
                    <p class="text-gray-400 text-sm mb-4">No event scheduled yet</p>
                    @can('create event')
                        <a href="{{ route('events.create', ['order_id' => $order->id]) }}" class="block w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-center">
                            Schedule Event
                        </a>
                    @endcan
                </x-card>
            @endif

            <!-- Quick Actions -->
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <button onclick="window.location.href='tel:{{ $order->lead->client_phone }}'" class="w-full px-4 py-2 bg-green-500/20 text-green-300 rounded-lg hover:bg-green-500/30 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call Client
                    </button>
                    <a href="{{ route('leads.show', $order->lead) }}" class="block w-full px-4 py-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition text-center">
                        View Lead Details
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-white/10">
            <h3 class="text-2xl font-bold text-white mb-6">Add Payment</h3>
            <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Amount (৳) <span class="text-red-400">*</span></label>
                        <input type="number" name="amount" required min="1" step="0.01" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method <span class="text-red-400">*</span></label>
                        <select name="payment_method" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Date <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="payment_date" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" id="paymentSubmitBtn" class="flex-1 px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg">
                        Add Payment
                    </button>
                    <button type="button" onclick="closePaymentModal()" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.remove('flex');
            document.getElementById('paymentForm').reset();
        }

        document.getElementById('paymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('paymentSubmitBtn');
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
                    submitBtn.textContent = 'Add Payment';
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Payment';
            }
        });
    </script>
</x-dashboard-layout>
