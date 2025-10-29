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
        <form method="GET" action="{{ route('leads.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                    Filter
                </button>
                <a href="{{ route('leads.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Reset
                </a>
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
                                        {{ !auth()->user()->can('edit lead') ? 'disabled' : '' }}>
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
                                    <a href="{{ route('leads.show', $lead) }}" class="text-blue-400 hover:text-blue-300 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('edit lead')
                                        <a href="{{ route('leads.edit', $lead) }}" class="text-purple-400 hover:text-purple-300 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete lead')
                                        <button onclick="deleteLead({{ $lead->id }})" class="text-red-400 hover:text-red-300 transition">
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
