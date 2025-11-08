<x-dashboard-layout title="Leads - CheckMate Events">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Lead Management
        </h1>
        <div class="flex gap-3">
            <a href="{{ route('leads.import.form') }}" class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Bulk Import
            </a>
            @can('create lead')
            <a href="{{ route('leads.create') }}" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                + Add New Lead
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('leads.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email..." class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <select name="vendor_id" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->vendor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="follow_up" {{ request('status') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                        <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div>
                    <select name="event_type" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="">All Event Types</option>
                        <option value="wedding" {{ request('event_type') == 'wedding' ? 'selected' : '' }}>Wedding</option>
                        <option value="birthday" {{ request('event_type') == 'birthday' ? 'selected' : '' }}>Birthday</option>
                        <option value="corporate" {{ request('event_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                        <option value="other" {{ request('event_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            
            <!-- Event Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">
                        <i class="fa-duotone fa-calendar-range text-purple-400"></i> Event Date From
                    </label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">
                        <i class="fa-duotone fa-calendar-range text-purple-400"></i> Event Date To
                    </label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition">
                        <i class="fa-duotone fa-filter"></i>
                        Filter
                    </button>
                    <a href="{{ route('leads.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-500/20 text-gray-300 rounded-lg hover:bg-gray-500/30 transition">
                        <i class="fa-duotone fa-rotate-left"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Leads Table -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Client Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Contact</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Vendor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Event Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Event Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Budget</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-300">Assigned To</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-white">{{ $lead->client_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300">
                                <div>{{ $lead->client_phone }}</div>
                                @if($lead->client_email)
                                    <div class="text-xs text-gray-400">{{ $lead->client_email }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $lead->vendor->vendor_name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-300">
                                    {{ ucfirst($lead->event_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">
                                {{ $lead->event_date ? \Carbon\Carbon::parse($lead->event_date)->format('d M Y') : 'Not Set' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $lead->budget_range ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <select onchange="updateStatus({{ $lead->id }}, this.value)" 
                                        class="px-2 py-1 text-xs rounded-full border-0 focus:ring-2 focus:ring-purple-400
                                        {{ $lead->status === 'new' ? 'bg-gray-500/20 text-gray-300' : '' }}
                                        {{ $lead->status === 'contacted' ? 'bg-blue-500/20 text-blue-300' : '' }}
                                        {{ $lead->status === 'follow_up' ? 'bg-yellow-500/20 text-yellow-300' : '' }}
                                        {{ $lead->status === 'qualified' ? 'bg-purple-500/20 text-purple-300' : '' }}
                                        {{ $lead->status === 'converted' ? 'bg-green-500/20 text-green-300' : '' }}
                                        {{ $lead->status === 'lost' ? 'bg-red-500/20 text-red-300' : '' }}"
                                        {{ !auth()->user()->can('edit leads') ? 'disabled' : '' }}>
                                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="follow_up" {{ $lead->status === 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                    <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                                    <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $lead->user->name }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('leads.show', $lead) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/30 transition-colors"
                                       title="View Details">
                                        <i class="fa-duotone fa-eye"></i>
                                    </a>
                                    @can('edit leads')
                                        <a href="{{ route('leads.edit', $lead) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 bg-purple-500/20 text-purple-300 rounded-lg hover:bg-purple-500/30 transition-colors"
                                           title="Edit Lead">
                                            <i class="fa-duotone fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete leads')
                                        <button onclick="deleteLead({{ $lead->id }})" 
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors"
                                                title="Delete Lead">
                                            <i class="fa-duotone fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">No leads found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        @endif
    </x-card>

    <script>
        async function updateStatus(leadId, status) {
            try {
                const response = await fetch(`/leads/${leadId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Failed to update status', 'error');
                    location.reload();
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
                location.reload();
            }
        }

        async function deleteLead(leadId) {
            if (!confirm('Are you sure you want to delete this lead?')) {
                return;
            }

            try {
                const response = await fetch(`/leads/${leadId}`, {
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
                    showToast(data.message || 'Failed to delete lead', 'error');
                }
            } catch (error) {
                showToast('An error occurred', 'error');
                console.error(error);
            }
        }
    </script>
</x-dashboard-layout>
