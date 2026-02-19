<x-dashboard-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Orders</h1>
        @can('create orders')
            <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition">
                + Create Order
            </a>
        @endcan
    </div>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order/client/phone" class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
            <select name="payment_status" class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                <option value="">All Payment Status</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg">Filter</button>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Reset</a>
            </div>
        </form>
    </x-card>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10 text-sm text-gray-300">
                        <th class="px-4 py-3 text-left">Order</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Package</th>
                        <th class="px-4 py-3 text-left">Event</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Paid</th>
                        <th class="px-4 py-3 text-left">Due</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                        @php
                            $paid = (float) $order->payments->sum('amount');
                            $due = max(0, (float) $order->total_amount - $paid);
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-purple-300 font-mono">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-white">{{ $order->client_display_name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->client_display_phone }}</p>
                            </td>
                            <td class="px-4 py-3 text-white/90">{{ $order->package_display_name }}</td>
                            <td class="px-4 py-3">
                                <p class="text-white/90">{{ ucfirst($order->event_type) }}</p>
                                <p class="text-xs text-gray-400">{{ $order->event_date?->format('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-white">৳{{ number_format((float) $order->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-green-400">৳{{ number_format($paid, 2) }}</td>
                            <td class="px-4 py-3 text-red-400">৳{{ number_format($due, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-300' : ($order->payment_status === 'partial' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-red-500/20 text-red-300') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300">View</a>
                                    @can('edit orders')
                                        <a href="{{ route('orders.edit', $order) }}" class="text-purple-400 hover:text-purple-300">Edit</a>
                                    @endcan
                                    @can('delete orders')
                                        <button onclick="deleteOrder({{ $order->id }})" class="text-red-400 hover:text-red-300">Delete</button>
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
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </x-card>

    <script>
        async function deleteOrder(orderId) {
            if (!confirm('Are you sure you want to delete this order?')) return;
            try {
                const response = await fetch(`/orders/${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Delete failed', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
            }
        }
    </script>
</x-dashboard-layout>
