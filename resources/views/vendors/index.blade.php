<x-dashboard-layout title="Vendors - CheckMate Events">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Vendors</h1>
            <p class="text-white/60 text-sm">Manage your venue and vendor partners</p>
        </div>
        @can('create vendors')
        <a href="{{ route('vendors.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl text-white font-semibold flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vendor
        </a>
        @endcan
    </div>

    <x-card :noPadding="true">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Vendor Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">City</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($vendors as $vendor)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4">
                            <p class="text-white font-medium">{{ $vendor->vendor_name }}</p>
                            @if($vendor->contact_person)
                            <p class="text-white/50 text-sm">{{ $vendor->contact_person }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white/80 capitalize">{{ str_replace('_', ' ', $vendor->vendor_type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-white/80">{{ $vendor->city ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <p class="text-white/80">{{ $vendor->phone }}</p>
                            @if($vendor->email)
                            <p class="text-white/50 text-sm">{{ $vendor->email }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $vendor->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ ucfirst($vendor->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @can('edit vendors')
                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-blue-400 hover:text-blue-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endcan
                                @can('delete vendors')
                                <button onclick="deleteVendor({{ $vendor->id }})" class="text-red-400 hover:text-red-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-white/60">
                            No vendors found. <a href="{{ route('vendors.create') }}" class="text-purple-400 hover:text-purple-300">Add your first vendor</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="px-6 py-4 border-t border-white/10">
            {{ $vendors->links() }}
        </div>
        @endif
    </x-card>

    <script>
        async function deleteVendor(id) {
            if (!confirm('Are you sure you want to delete this vendor?')) return;

            try {
                const response = await fetch(`/vendors/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to delete vendor', 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</x-dashboard-layout>
