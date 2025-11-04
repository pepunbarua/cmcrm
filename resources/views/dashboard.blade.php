<x-dashboard-layout title="Dashboard - CheckMate Events">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dashboard</h1>
        <p class="text-gray-600 dark:text-white/60">Welcome back! Here's what's happening with your events.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card 
            title="Total Events" 
            value="24" 
            change="+12% from last month"
            icon="calendar"
            color="purple"
        />
        
        <x-stat-card 
            title="Active Clients" 
            value="156" 
            change="+8% from last month"
            icon="users"
            color="blue"
        />
        
        <x-stat-card 
            title="Photos Captured" 
            value="12,584" 
            change="+24% from last month"
            icon="camera"
            color="pink"
        />
        
        <x-stat-card 
            title="Revenue" 
            value="48,290" 
            change="+16% from last month"
            icon="money"
            color="green"
        />
    </div>

    <!-- Charts and Activities Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Events -->
        <x-card title="Upcoming Events" class="lg:col-span-2">
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">Sarah & John Wedding</h4>
                        <p class="text-sm text-gray-600 dark:text-white/60">Nov 15, 2025 • Grand Ballroom</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-medium">Confirmed</span>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">Tech Corp Annual Party</h4>
                        <p class="text-sm text-gray-600 dark:text-white/60">Nov 22, 2025 • Downtown Hall</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs font-medium">Pending</span>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">Emma's Birthday Bash</h4>
                        <p class="text-sm text-gray-600 dark:text-white/60">Dec 1, 2025 • Garden Venue</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-medium">Confirmed</span>
                </div>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-3">
                <x-button variant="primary" class="justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Event
                </x-button>
                
                <x-button variant="secondary" class="justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Add Client
                </x-button>
                
                <x-button variant="secondary" class="justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Create Invoice
                </x-button>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-white/10">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Recent Activity</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400 mt-1.5"></div>
                        <div>
                            <p class="text-gray-700 dark:text-white/80">Payment received from Sarah & John</p>
                            <p class="text-gray-500 dark:text-white/40 text-xs">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500 dark:bg-blue-400 mt-1.5"></div>
                        <div>
                            <p class="text-gray-700 dark:text-white/80">New client registered: Tech Corp</p>
                            <p class="text-gray-500 dark:text-white/40 text-xs">5 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 rounded-full bg-purple-500 dark:bg-purple-400 mt-1.5"></div>
                        <div>
                            <p class="text-gray-700 dark:text-white/80">Event completed: Mike's Graduation</p>
                            <p class="text-gray-500 dark:text-white/40 text-xs">1 day ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Recent Invoices -->
    <x-card title="Recent Invoices" :noPadding="true">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-white/60 uppercase tracking-wider">Invoice ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-white/60 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-white/60 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-white/60 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-white/60 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">#INV-001</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Sarah & John</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-white/60">Wedding Photography</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">৳3,500</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-medium">Paid</span></td>
                    </tr>
                    <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">#INV-002</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Tech Corp</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-white/60">Corporate Event</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">৳2,800</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 text-xs font-medium">Pending</span></td>
                    </tr>
                    <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">#INV-003</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">Emma Johnson</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-white/60">Birthday Party</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">৳1,200</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-medium">Paid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</x-dashboard-layout>

