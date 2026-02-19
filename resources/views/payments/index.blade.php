<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Payments</h1>
                <p class="text-gray-600 dark:text-white/60">Track all payment transactions and due amounts</p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Received" 
                :value="'৳' . number_format($stats['total_amount'], 2)" 
                icon="check-circle"
                color="green"
            />
            <x-stat-card 
                label="Pending Amount" 
                :value="'৳' . number_format($stats['pending_amount'], 2)" 
                icon="clock"
                color="yellow"
            />
            <x-stat-card 
                label="Overdue Payments" 
                :value="$stats['overdue_count']" 
                icon="exclamation"
                color="red"
            />
            <x-stat-card 
                label="Completed" 
                :value="$stats['completed_count']" 
                icon="check"
                color="blue"
            />
        </div>

        <!-- Filters -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or Client..." class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Status</option>
                        <option value="due" {{ request('status') == 'due' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Due</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Overdue</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                        <option value="" style="background-color: #1f2937; color: white;">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Cash</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Bank Transfer</option>
                        <option value="mobile_banking" {{ request('payment_method') == 'mobile_banking' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Mobile Banking</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="md:col-span-5 flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('payments.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Payments Table -->
        @if($payments->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Order #</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Client</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Amount</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Method</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Payment Date</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Balance Due</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Order Payment</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('orders.show', $payment->order) }}" class="text-purple-600 dark:text-purple-400 hover:underline font-medium">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">
                                {{ $payment->order->client_display_name }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-gray-900 dark:text-white">৳{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white capitalize">
                                    {{ $payment->payment_method === 'mobile_banking' ? 'mobile banking' : str_replace('_', ' ', $payment->payment_method) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-white/60">
                                @if($payment->payment_date)
                                {{ $payment->payment_date->format('M d, Y') }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-white/60">
                                ৳{{ number_format((float) $payment->order->balance_due, 2) }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $orderPaymentStatus = $payment->order->payment_status ?? 'pending';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($orderPaymentStatus === 'paid') bg-green-500/20 text-green-600 dark:text-green-400
                                    @elseif($orderPaymentStatus === 'partial') bg-yellow-500/20 text-yellow-600 dark:text-yellow-400
                                    @else bg-red-500/20 text-red-600 dark:text-red-400
                                    @endif">
                                    {{ ucfirst($orderPaymentStatus) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('orders.show', $payment->order) }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300" title="View Order">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('delete payments')
                                    <button onclick="deletePayment({{ $payment->id }})" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </x-card>
        @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No payments found</h3>
                <p class="text-gray-600 dark:text-white/60">Start by creating orders and recording payments</p>
            </div>
        </x-card>
        @endif
    </div>

    <script>
        async function deletePayment(paymentId) {
            if (!confirm('Are you sure you want to delete this payment record?')) {
                return;
            }

            try {
                const response = await fetch(`/payments/${paymentId}`, {
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
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</x-dashboard-layout>
