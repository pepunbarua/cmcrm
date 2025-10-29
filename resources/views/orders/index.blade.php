<x-dashboard-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Orders Management
        </h1>
        @can('create order')
            <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                + Create Order
            </a>
        @endcan
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number or client name..." class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
            </div>
            <div>
                <select name="payment_status" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <option value="">All Payment Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                    Filter
                </button>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Reset
                </a>
            </div>
        </form>
    </x-card>

    <!-- Orders Table -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Order Number</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Client</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Package</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Total Amount</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Paid</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Due</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Payment Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Date</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                        @php
                            $paidAmount = $order->payments->where('status', 'completed')->sum('amount');
                            $dueAmount = $order->total_amount - $paidAmount;
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-mono text-purple-400">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-white">{{ $order->lead->client_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $order->package_name }}</td>
                            <td class="px-4 py-3 text-white font-semibold">৳{{ number_format($order->total_amount) }}</td>
                            <td class="px-4 py-3 text-green-400">৳{{ number_format($paidAmount) }}</td>
                            <td class="px-4 py-3 text-red-400">৳{{ number_format($dueAmount) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-300' : '' }}
                                    {{ $order->payment_status === 'partial' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                                    {{ $order->payment_status === 'pending' ? 'bg-red-500/20 text-red-300' : '' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('edit order')
                                        <a href="{{ route('orders.edit', $order) }}" class="text-purple-400 hover:text-purple-300 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete order')
                                        <button onclick="deleteOrder({{ $order->id }})" class="text-red-400 hover:text-red-300 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </x-card>

    <script>
        async function deleteOrder(orderId) {
            if (!confirm('Are you sure you want to delete this order?')) {
                return;
            }

            try {
                const response = await fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Failed to delete order', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
            }
        }
    </script>
</x-dashboard-layout>
