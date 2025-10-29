<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Revenue Report</h1>
                    <p class="text-gray-600 dark:text-white/60">Detailed financial analytics and payment trends</p>
                </div>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Back to Overview
                </a>
            </div>
        </div>

        <!-- Date Range Filter -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('reports.revenue') }}" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Apply
                </button>
            </form>
        </x-card>

        <!-- Revenue Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Revenue" 
                :value="'৳' . number_format($stats['total_revenue'], 2)" 
                icon="currency-dollar"
                color="green"
            />
            <x-stat-card 
                label="Pending Amount" 
                :value="'৳' . number_format($stats['pending_amount'], 2)" 
                icon="clock"
                color="yellow"
            />
            <x-stat-card 
                label="Average Payment" 
                :value="'৳' . number_format($stats['average_payment'], 2)" 
                icon="chart-bar"
                color="blue"
            />
            <x-stat-card 
                label="Total Payments" 
                :value="$stats['payment_count']" 
                icon="receipt-tax"
                color="purple"
            />
        </div>

        <!-- Period Comparison -->
        @if(isset($comparison))
        <x-card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Period Comparison</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm text-gray-600 dark:text-white/60 mb-2">Current Period</div>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                        ৳{{ number_format($comparison['current'], 2) }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-600 dark:text-white/60 mb-2">Previous Period</div>
                    <div class="text-3xl font-bold text-gray-600 dark:text-white/60">
                        ৳{{ number_format($comparison['previous'], 2) }}
                    </div>
                </div>
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="text-sm text-gray-600 dark:text-white/60">Change:</div>
                        <div class="flex items-center gap-2">
                            @if($comparison['percentage_change'] > 0)
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">+{{ number_format($comparison['percentage_change'], 2) }}%</span>
                            @elseif($comparison['percentage_change'] < 0)
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($comparison['percentage_change'], 2) }}%</span>
                            @else
                            <span class="text-lg font-bold text-gray-600 dark:text-white/60">0%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue by Payment Method -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Revenue by Payment Method
                </h3>
                <div class="space-y-4">
                    @php
                        $maxRevenue = $revenueByMethod->max('total_amount');
                    @endphp
                    @foreach($revenueByMethod as $method)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-white/80 capitalize">{{ str_replace('_', ' ', $method->payment_method) }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">৳{{ number_format($method->total_amount, 2) }}</span>
                        </div>
                        <div class="h-3 bg-gray-200 dark:bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full
                                @if($method->payment_method == 'cash') bg-green-500
                                @elseif($method->payment_method == 'bank_transfer') bg-blue-500
                                @elseif($method->payment_method == 'bkash') bg-pink-500
                                @elseif($method->payment_method == 'nagad') bg-orange-500
                                @else bg-purple-500
                                @endif"
                                 style="width: {{ $maxRevenue > 0 ? ($method->total_amount / $maxRevenue) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Daily Revenue Trend -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Daily Revenue Trend
                </h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @php
                        $maxDaily = $dailyRevenue->max('total');
                    @endphp
                    @forelse($dailyRevenue as $day)
                    <div class="flex items-center gap-3">
                        <div class="w-20 text-xs text-gray-600 dark:text-white/60">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</div>
                        <div class="flex-1">
                            <div class="h-6 bg-gray-200 dark:bg-white/10 rounded overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded flex items-center px-2 text-white text-xs font-medium"
                                     style="width: {{ $maxDaily > 0 ? ($day->total / $maxDaily) * 100 : 0 }}%">
                                    @if($day->total > 0)
                                    ৳{{ number_format($day->total, 0) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600 dark:text-white/60">
                        No revenue data for this period
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Payment Details Table -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Payments</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Date</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Order #</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Client</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Method</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Amount</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @php
                            $payments = \App\Models\Payment::with('order.lead')
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->latest('payment_date')
                                ->limit(50)
                                ->get();
                        @endphp
                        @forelse($payments as $payment)
                        <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white font-medium">
                                {{ $payment->order->order_number }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ $payment->order->lead->client_name }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    @if($payment->payment_method == 'cash') bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400
                                    @elseif($payment->payment_method == 'bank_transfer') bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400
                                    @elseif($payment->payment_method == 'bkash') bg-pink-100 dark:bg-pink-500/20 text-pink-700 dark:text-pink-400
                                    @elseif($payment->payment_method == 'nagad') bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400
                                    @else bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-right font-bold text-gray-900 dark:text-white">
                                ৳{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    @if($payment->status == 'completed') bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400
                                    @elseif($payment->status == 'pending' && \Carbon\Carbon::parse($payment->due_date)->isPast()) bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400
                                    @else bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400
                                    @endif">
                                    @if($payment->status == 'completed')
                                        Completed
                                    @elseif($payment->status == 'pending' && \Carbon\Carbon::parse($payment->due_date)->isPast())
                                        Overdue
                                    @else
                                        Pending
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-600 dark:text-white/60">
                                No payments found for this period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-dashboard-layout>
