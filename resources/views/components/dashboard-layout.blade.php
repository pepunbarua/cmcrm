<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard - CheckMate Events' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/duotone.css">
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
                
                <nav class="space-y-2 max-h-[calc(100vh-280px)] overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                        <i class="fa-duotone fa-grid-2 text-lg"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view vendors')
                    <div x-data="{ open: {{ request()->routeIs('vendors.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('vendors.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-building text-lg"></i>
                                <span class="font-medium text-sm">Vendors</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('vendors.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('vendors.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Vendors
                            </a>
                            @can('create vendors')
                            <a href="{{ route('vendors.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('vendors.create') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-plus text-xs"></i>
                                Add New Vendor
                            </a>
                            @endcan
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view leads')
                    <div x-data="{ open: {{ request()->routeIs('leads.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('leads.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-users text-lg"></i>
                                <span class="font-medium text-sm">Leads</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('leads.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leads.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Leads
                            </a>
                            @can('create leads')
                            <a href="{{ route('leads.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leads.create') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-plus text-xs"></i>
                                Add New Lead
                            </a>
                            @endcan
                            <a href="{{ route('leads.index', ['status' => 'new']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-sparkles text-xs"></i>
                                New Leads
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'contacted']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-phone text-xs"></i>
                                Contacted
                            </a>
                            <a href="{{ route('leads.index', ['status' => 'converted']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-check-circle text-xs"></i>
                                Converted
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Call Queue -->
                    <div x-data="{ open: {{ request()->routeIs('call-queue.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('call-queue.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-phone-volume text-lg"></i>
                                <span class="font-medium text-sm">Call Queue</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-7 mt-1 space-y-1 border-l-2 border-purple-200/30 dark:border-white/10 pl-3">
                            <a href="{{ route('call-queue.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('call-queue.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-chart-mixed text-xs"></i>
                                Summary
                            </a>
                            <a href="{{ route('call-queue.dialer') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('call-queue.dialer') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-bullseye text-xs"></i>
                                Lead Dialer
                            </a>
                            <a href="{{ route('call-queue.today') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('call-queue.today') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-calendar-day text-xs"></i>
                                Today's Call List
                            </a>
                            <a href="{{ route('call-queue.scheduled') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-calendar-clock text-xs"></i>
                                Scheduled
                            </a>
                            <a href="{{ route('call-queue.pending') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-hourglass-half text-xs"></i>
                                Pending Calls
                            </a>
                            <a href="{{ route('call-queue.follow-ups') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-rotate text-xs"></i>
                                Follow-up Required
                            </a>
                            <a href="{{ route('call-queue.history') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-clock-rotate-left text-xs"></i>
                                Call History
                            </a>
                        </div>
                    </div>
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view orders')
                    <div x-data="{ open: {{ request()->routeIs('orders.*') || request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('orders.*') || request()->routeIs('payments.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-file-invoice-dollar text-lg"></i>
                                <span class="font-medium text-sm">Orders</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('orders.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Orders
                            </a>
                            @can('create orders')
                            <a href="{{ route('orders.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('orders.create') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-plus text-xs"></i>
                                New Order
                            </a>
                            @endcan
                            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-clock text-xs"></i>
                                Pending Orders
                            </a>
                            <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-circle-check text-xs"></i>
                                Confirmed
                            </a>
                            <a href="{{ route('orders.index', ['payment_status' => 'due']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-bangladeshi-taka-sign text-xs"></i>
                                Payment Due
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view events')
                    <div x-data="{ open: {{ request()->routeIs('events.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('events.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-calendar-star text-lg"></i>
                                <span class="font-medium text-sm">Events</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('events.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('events.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Events
                            </a>
                            @can('create events')
                            <a href="{{ route('events.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('events.create') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-plus text-xs"></i>
                                Schedule Event
                            </a>
                            @endcan
                            <a href="{{ route('events.index', ['status' => 'scheduled']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-calendar-clock text-xs"></i>
                                Upcoming Events
                            </a>
                            <a href="{{ route('events.index', ['status' => 'in_progress']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-hourglass-half text-xs"></i>
                                In Progress
                            </a>
                            <a href="{{ route('events.index', ['status' => 'completed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-circle-check text-xs"></i>
                                Completed
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view team members')
                    <div x-data="{ open: {{ request()->routeIs('team.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('team.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-people-group text-lg"></i>
                                <span class="font-medium text-sm">Team</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('team.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('team.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Members
                            </a>
                            @can('create team members')
                            <a href="{{ route('team.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('team.create') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-user-plus text-xs"></i>
                                Add Member
                            </a>
                            @endcan
                            <a href="{{ route('team.index', ['role_type' => 'photographer']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-camera text-xs"></i>
                                Photographers
                            </a>
                            <a href="{{ route('team.index', ['role_type' => 'videographer']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-video text-xs"></i>
                                Videographers
                            </a>
                            <a href="{{ route('team.index', ['availability_status' => 'available']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-circle-check text-xs"></i>
                                Available Now
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view payments')
                    <div x-data="{ open: {{ request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('payments.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-bangladeshi-taka-sign text-lg"></i>
                                <span class="font-medium text-sm">Payments</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('payments.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payments.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Payments
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'due']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-clock text-xs"></i>
                                Due Payments
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'overdue']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-triangle-exclamation text-xs"></i>
                                Overdue
                            </a>
                            <a href="{{ route('payments.index', ['status' => 'completed']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-circle-check text-xs"></i>
                                Completed
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view deliverables')
                    <div x-data="{ open: {{ request()->routeIs('deliverables.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('deliverables.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-box-check text-lg"></i>
                                <span class="font-medium text-sm">Deliverables</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('deliverables.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('deliverables.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-list text-xs"></i>
                                All Deliverables
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'pending']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-clock text-xs"></i>
                                Pending Upload
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'uploaded']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-cloud-arrow-up text-xs"></i>
                                Uploaded
                            </a>
                            <a href="{{ route('deliverables.index', ['status' => 'delivered']) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                <i class="fa-duotone fa-truck-fast text-xs"></i>
                                Delivered
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('view reports')
                    <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-duotone fa-chart-mixed text-lg"></i>
                                <span class="font-medium text-sm">Reports</span>
                            </div>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.index') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-chart-pie text-xs"></i>
                                Overview
                            </a>
                            <a href="{{ route('reports.revenue') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.revenue') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-bangladeshi-taka-sign text-xs"></i>
                                Revenue Report
                            </a>
                            <a href="{{ route('reports.events') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.events') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-calendar-days text-xs"></i>
                                Events Report
                            </a>
                            <a href="{{ route('reports.team') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.team') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-chart-line-up text-xs"></i>
                                Team Performance
                            </a>
                        </div>
                    </div>
                    @endcan
                    
                    <!-- Menu Divider -->
                    <div class="h-px bg-gradient-to-r from-transparent via-purple-200/50 dark:via-white/10 to-transparent my-2"></div>
                    
                    @can('manage users')
                    <div x-data="{ open: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('settings.*') ? 'bg-gradient-to-r from-purple-600/20 to-pink-600/20 text-purple-600 dark:text-purple-300 border border-purple-500/30 shadow-sm' : 'text-gray-600 dark:text-white/60 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-white/5 dark:hover:to-white/5 hover:text-purple-600 dark:hover:text-purple-300' }} transition-all duration-200">
                            <i class="fa-duotone fa-gear text-lg"></i>
                            <span class="font-medium text-sm flex-1 text-left">Settings</span>
                            <i class="fa-duotone fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1 pb-2">
                            <a href="{{ route('settings.company') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.company') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-building text-xs"></i>
                                Company Profile
                            </a>
                            <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-users-gear text-xs"></i>
                                User Management
                            </a>
                            <a href="{{ route('roles.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('roles.*') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-shield-halved text-xs"></i>
                                Roles & Permissions
                            </a>
                            <a href="{{ route('settings.vendor-types.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.vendor-types.*') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-tags text-xs"></i>
                                Vendor Types
                            </a>
                            <a href="{{ route('settings.general') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.general') ? 'bg-purple-50 dark:bg-white/5 text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-600 dark:text-white/60 hover:bg-purple-50/50 dark:hover:bg-white/5 hover:text-purple-600 dark:hover:text-purple-400' }} transition-all duration-200">
                                <i class="fa-duotone fa-sliders text-xs"></i>
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

