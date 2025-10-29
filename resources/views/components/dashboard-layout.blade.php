<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard - CheckMate Events' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script>
        // Initialize theme before page renders to avoid flash
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
            } else if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (systemPrefersDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.add('dark'); // Default to dark
            }
        })();
    </script>
</head>
<body class="antialiased bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900 min-h-screen text-gray-900 dark:text-white transition-colors duration-200">
    <x-toast />
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white/70 dark:bg-white/5 backdrop-blur-xl border-r border-purple-200/50 dark:border-white/10 shadow-xl shadow-purple-500/10 dark:shadow-none">
            <div class="p-6">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="CheckMate Events" class="h-12 w-auto mb-8">
                
                <nav class="space-y-1 max-h-[calc(100vh-280px)] overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                    
                    @can('view vendors')
                    <div x-data="{ open: {{ request()->routeIs('vendors.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('vendors.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span class="font-medium text-sm">Vendors</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('vendors.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('vendors.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Vendors
                            </a>
                            @can('create vendors')
                            <a href="{{ route('vendors.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('vendors.create') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Add New Vendor
                            </a>
                            @endcan
                        </div>
                    </div>
                    @endcan
                    
                    @can('view leads')
                    <div x-data="{ open: {{ request()->routeIs('leads.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('leads.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="font-medium text-sm">Leads</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('leads.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leads.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Leads
                            </a>
                            @can('create leads')
                            <a href="{{ route('leads.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leads.create') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Add New Lead
                            </a>
                            @endcan
                            <a href="{{ route('leads.index', ['status' => 'new']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                New Leads
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'contacted']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Contacted
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'converted']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Converted
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view orders')
                    <div x-data="{ open: {{ request()->routeIs('orders.*') || request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('orders.*') || request()->routeIs('payments.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="font-medium text-sm">Orders</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('orders.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Orders
                            </a>
                            @can('create orders')
                            <a href="{{ route('orders.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('orders.create') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                New Order
                            </a>
                            @endcan
                            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Pending Orders
                            </a>
                            <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Confirmed
                            </a>
                            <a href="{{ route('orders.index', ['payment_status' => 'due']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Payment Due
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view events')
                    <div x-data="{ open: {{ request()->routeIs('events.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('events.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="font-medium text-sm">Events</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('events.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('events.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Events
                            </a>
                            @can('create events')
                            <a href="{{ route('events.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('events.create') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Schedule Event
                            </a>
                            @endcan
                            <a href="{{ route('events.index', ['status' => 'scheduled']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Upcoming Events
                            </a>
                            <a href="{{ route('events.index', ['status' => 'in_progress']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                In Progress
                            </a>
                            <a href="{{ route('events.index', ['status' => 'completed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Completed
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view team members')
                    <div x-data="{ open: {{ request()->routeIs('team.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('team.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span class="font-medium text-sm">Team</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('team.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('team.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Members
                            </a>
                            @can('create team members')
                            <a href="{{ route('team.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('team.create') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Add Member
                            </a>
                            @endcan
                            <a href="{{ route('team.index', ['role_type' => 'photographer']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Photographers
                            </a>
                            <a href="{{ route('team.index', ['role_type' => 'videographer']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Videographers
                            </a>
                            <a href="{{ route('team.index', ['availability_status' => 'available']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Available Now
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view payments')
                    <div x-data="{ open: {{ request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('payments.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="font-medium text-sm">Payments</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('payments.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payments.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Payments
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'due']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Due Payments
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'overdue']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Overdue
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'completed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Completed
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view deliverables')
                    <div x-data="{ open: {{ request()->routeIs('deliverables.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('deliverables.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-medium text-sm">Deliverables</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('deliverables.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('deliverables.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                All Deliverables
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'pending']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Pending Upload
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'uploaded']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Uploaded
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'delivered']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Delivered
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('view reports')
                    <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('reports.*') ? 'bg-purple-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span class="font-medium text-sm">Reports</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.index') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Overview
                            </a>
                            <a href="{{ route('reports.revenue') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.revenue') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Revenue Report
                            </a>
                            <a href="{{ route('reports.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.events') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Events Report
                            </a>
                            <a href="{{ route('reports.team') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.team') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Team Performance
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    @can('manage users')
                    <div x-data="{ open: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('settings.*') ? 'bg-purple-50 dark:bg-white/10 text-purple-600 dark:text-purple-400' : 'text-gray-600 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-medium text-sm flex-1 text-left">Settings</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="pl-4 space-y-1">
                            <a href="{{ route('settings.company') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.company') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Company Profile
                            </a>
                            <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                User Management
                            </a>
                            <a href="{{ route('roles.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('roles.*') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                Roles & Permissions
                            </a>
                            <a href="{{ route('settings.general') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.general') ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                                General Settings
                            </a>
                        </div>
                    </div>
                    @endcan
                </nav>
            </div>
            
            <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-white/10 dark:border-white/10 space-y-3">
                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-700 dark:text-white/60 hover:bg-gray-100 dark:hover:bg-white/5 transition" title="Toggle Theme">
                    <svg id="theme-icon-light" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg id="theme-icon-dark" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <span class="font-medium text-sm">
                        <span class="hidden dark:inline">Light Mode</span>
                        <span class="inline dark:hidden">Dark Mode</span>
                    </span>
                </button>

                <!-- User Profile -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-white/50">{{ auth()->user()->email }}</p>
                    </div>
                    <button onclick="handleLogout()" class="text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-transparent">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
            
            <footer class="mt-12 pt-8 border-t border-purple-200/50 dark:border-white/10 text-center">
                <p class="text-xs text-gray-600 dark:text-white/40">
                    CheckMate Events - Photography & Cinematography CRM
                </p>
                <p class="text-xs text-gray-500 dark:text-white/30 mt-1">
                    Powered by <span class="text-purple-600 dark:text-purple-400 font-semibold">Devswire</span>
                </p>
            </footer>
        </main>
    </div>

    <script>
        // Theme toggle function
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                showToast('Light mode activated', 'success');
                console.log('Theme changed to light, classList:', html.classList.toString());
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                showToast('Dark mode activated', 'success');
                console.log('Theme changed to dark, classList:', html.classList.toString());
            }
        }
        
        // Debug: Log current theme on page load
        console.log('Initial theme:', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        console.log('localStorage.theme:', localStorage.getItem('theme'));

        // Logout function
        async function handleLogout() {
            if (!confirm('Are you sure you want to logout?')) return;
            
            try {
                const response = await fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showToast('Logout failed. Please try again.', 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</body>
</html>

