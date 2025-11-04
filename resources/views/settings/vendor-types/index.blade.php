<x-dashboard-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Vendor Types</h1>
                <p class="text-gray-600 dark:text-white/60 text-sm mt-1">Manage vendor type categories</p>
            </div>
            <a href="{{ route('settings.vendor-types.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                <i class="fa-duotone fa-plus"></i>
                Add Vendor Type
            </a>
        </div>

        <!-- Vendor Types List -->
        <x-card>
            @if($vendorTypes->isEmpty())
                <div class="text-center py-12">
                    <i class="fa-duotone fa-tags text-6xl text-gray-300 dark:text-white/20 mb-4"></i>
                    <p class="text-gray-500 dark:text-white/50">No vendor types found. Create your first vendor type!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Icon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Vendors</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white/60 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($vendorTypes as $vendorType)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $vendorType->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vendorType->icon)
                                            <i class="fa-duotone {{ $vendorType->icon }} text-lg text-purple-600 dark:text-purple-400"></i>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-white/40">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 dark:text-white/60">{{ $vendorType->order }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendorType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' }}">
                                            {{ $vendorType->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 dark:text-white/60">{{ $vendorType->vendors->count() }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('settings.vendor-types.edit', $vendorType) }}" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                                <i class="fa-duotone fa-pen"></i>
                                            </a>
                                            <form action="{{ route('settings.vendor-types.destroy', $vendorType) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vendor type?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <i class="fa-duotone fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
</x-dashboard-layout>
