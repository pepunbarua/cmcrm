<x-dashboard-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Order {{ $order->order_number }}</h1>
                <p class="text-gray-400 mt-1">{{ $order->client_display_name }}</p>
            </div>
            <div class="flex gap-3">
                @can('edit orders')
                    <a href="{{ route('orders.edit', $order) }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">Edit Order</a>
                @endcan
                @if(!$order->event)
                    @can('create events')
                        <a href="{{ route('events.create', ['order_id' => $order->id]) }}" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Schedule Event</a>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    @php
        $paidAmount = (float) $order->payments->sum('amount');
        $dueAmount = max(0, (float) $order->total_amount - $paidAmount);
        $orderPackage = $order->primaryOrderPackage;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Customer & Event</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-sm text-gray-400">Customer</p><p class="text-white">{{ $order->client_display_name }}</p></div>
                    <div><p class="text-sm text-gray-400">Phone</p><p class="text-white">{{ $order->client_display_phone }}</p></div>
                    <div><p class="text-sm text-gray-400">Email</p><p class="text-white">{{ $order->client_display_email ?? 'N/A' }}</p></div>
                    <div><p class="text-sm text-gray-400">Event Type</p><p class="text-white">{{ ucfirst($order->event_type) }}</p></div>
                    <div><p class="text-sm text-gray-400">Event Date</p><p class="text-white">{{ $order->event_date?->format('d M Y') }}</p></div>
                    <div><p class="text-sm text-gray-400">Duration</p><p class="text-white">{{ $order->time_duration ?: 'N/A' }}</p></div>
                    <div><p class="text-sm text-gray-400">Location</p><p class="text-white">{{ $order->location ?: $order->event_venue_name }}</p></div>
                    <div><p class="text-sm text-gray-400">Outdoor Shoot</p><p class="text-white">{{ $order->outdoor_shoot ? 'Yes' : 'No' }}</p></div>
                    <div><p class="text-sm text-gray-400">Bride</p><p class="text-white">{{ $order->bride_name ?: 'N/A' }}</p></div>
                    <div><p class="text-sm text-gray-400">Groom</p><p class="text-white">{{ $order->groom_name ?: 'N/A' }}</p></div>
                    <div><p class="text-sm text-gray-400">Photographer</p><p class="text-white">{{ $order->photographer_count }} person</p></div>
                    <div><p class="text-sm text-gray-400">Videographer</p><p class="text-white">{{ $order->videographer_count }} person</p></div>
                </div>
                @if($order->requirements)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-gray-400 mb-1">Requirements</p>
                        <p class="text-white">{{ $order->requirements }}</p>
                    </div>
                @endif
            </x-card>

            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Package Items</h2>
                <div class="mb-3">
                    <p class="text-gray-300">Package: <span class="text-white font-medium">{{ $order->package_display_name }}</span></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-sm text-gray-400">
                                <th class="py-2 text-left">Item</th>
                                <th class="py-2 text-right">Qty</th>
                                <th class="py-2 text-right">Unit Price</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($orderPackage?->contents ?? [] as $item)
                                <tr class="border-b border-white/5">
                                    <td class="py-2 text-white">{{ $item->content_name_snapshot }}</td>
                                    <td class="py-2 text-right text-gray-300">{{ number_format((float) $item->qty, 2) }}</td>
                                    <td class="py-2 text-right text-gray-300">৳{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="py-2 text-right text-white">৳{{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-400">No package items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Payments</h2>
                <div class="space-y-2">
                    @forelse($order->payments as $payment)
                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-white">৳{{ number_format((float) $payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }} • {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400">No payments recorded.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-xl font-semibold text-white mb-4">Amount Summary</h2>
                <div class="space-y-2">
                    <div class="flex justify-between"><span class="text-gray-400">Subtotal</span><span class="text-white">৳{{ number_format((float) ($orderPackage->subtotal ?? $order->total_amount), 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Discount</span><span class="text-white">৳{{ number_format((float) $order->discount_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Grand Total</span><span class="text-white font-semibold">৳{{ number_format((float) $order->total_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Paid</span><span class="text-green-400">৳{{ number_format($paidAmount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Due</span><span class="text-red-400">৳{{ number_format($dueAmount, 2) }}</span></div>
                </div>
            </x-card>

            @if($order->lead)
                <x-card>
                    <h2 class="text-xl font-semibold text-white mb-3">Lead Link</h2>
                    <a href="{{ route('leads.show', $order->lead) }}" class="block px-4 py-2 bg-blue-500/20 text-blue-300 rounded-lg text-center hover:bg-blue-500/30">View Source Lead</a>
                </x-card>
            @endif
        </div>
    </div>
</x-dashboard-layout>
