<x-dashboard-layout title="Packages - CheckMate Events">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Packages</h1>
            <p class="text-white/60 text-sm">Build reusable package templates for order creation</p>
        </div>

        <a href="{{ route('packages.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 rounded-xl text-white font-semibold flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Package
        </a>
    </div>

    <x-card class="mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search package name or code" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500">

            <select name="pricing_mode" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">All Pricing Modes</option>
                <option value="fixed" {{ request('pricing_mode') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="item_sum" {{ request('pricing_mode') === 'item_sum' ? 'selected' : '' }}>Item Sum</option>
                <option value="hybrid" {{ request('pricing_mode') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
            </select>

            <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <div class="flex gap-2">
                <x-button type="submit" class="w-full justify-center">Filter</x-button>
                <a href="{{ route('packages.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-semibold transition">Reset</a>
            </div>
        </form>
    </x-card>

    <x-card :noPadding="true">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Package</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Pricing Mode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Base Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/60 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($packages as $package)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4">
                            <p class="text-white font-medium">{{ $package->name }}</p>
                            <p class="text-white/50 text-sm">{{ $package->code ?: 'No code' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300">
                                {{ str($package->pricing_mode)->replace('_', ' ')->title() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-white/90 font-medium">৳{{ number_format((float) $package->base_price, 2) }}</td>
                        <td class="px-6 py-4 text-white/80">{{ $package->items_count }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $package->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('packages.edit', $package) }}" class="text-blue-400 hover:text-blue-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button type="button" onclick="deletePackage({{ $package->id }})" class="text-red-400 hover:text-red-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-white/60">
                            No package found. <a href="{{ route('packages.create') }}" class="text-purple-400 hover:text-purple-300">Create first package</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($packages->hasPages())
        <div class="px-6 py-4 border-t border-white/10">
            {{ $packages->links() }}
        </div>
        @endif
    </x-card>

    <script>
        async function deletePackage(id) {
            if (!confirm('Are you sure you want to delete this package?')) return;

            try {
                const response = await fetch(`/packages/${id}`, {
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
                    return;
                }

                showToast(data.message || 'Failed to delete package.', 'error');
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</x-dashboard-layout>
