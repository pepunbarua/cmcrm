<x-dashboard-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add Vendor Type</h1>
            <p class="text-gray-600 dark:text-white/60 text-sm mt-1">Create a new vendor type category</p>
        </div>

        <!-- Form -->
        <x-card>
            <form action="{{ route('settings.vendor-types.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div x-data="{ 
                    open: false, 
                    selected: '{{ old('icon', '') }}',
                    icons: [
                        { class: 'fa-rings-wedding', name: 'Wedding Rings' },
                        { class: 'fa-building', name: 'Building' },
                        { class: 'fa-hotel', name: 'Hotel' },
                        { class: 'fa-utensils', name: 'Restaurant' },
                        { class: 'fa-users', name: 'Community' },
                        { class: 'fa-umbrella-beach', name: 'Resort/Beach' },
                        { class: 'fa-warehouse', name: 'Warehouse' },
                        { class: 'fa-store', name: 'Store' },
                        { class: 'fa-landmark', name: 'Landmark' },
                        { class: 'fa-heart', name: 'Heart' },
                        { class: 'fa-champagne-glasses', name: 'Celebration' },
                        { class: 'fa-cake-candles', name: 'Cake' },
                        { class: 'fa-gifts', name: 'Gifts' },
                        { class: 'fa-music', name: 'Music' },
                        { class: 'fa-camera', name: 'Camera' },
                        { class: 'fa-video', name: 'Video' },
                        { class: 'fa-microphone', name: 'Microphone' },
                        { class: 'fa-tree', name: 'Garden' },
                        { class: 'fa-water', name: 'Water/Pool' },
                        { class: 'fa-mosque', name: 'Mosque' },
                        { class: 'fa-church', name: 'Church' },
                        { class: 'fa-crown', name: 'Crown/Royal' },
                        { class: 'fa-star', name: 'Star' },
                        { class: 'fa-sparkles', name: 'Sparkles' },
                        { class: 'fa-ellipsis', name: 'Other' }
                    ]
                }" @click.away="open = false" class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        Icon
                    </label>
                    <input type="hidden" name="icon" x-model="selected">
                    
                    <button type="button" @click="open = !open" class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <template x-if="selected">
                                <i :class="'fa-duotone ' + selected + ' text-xl text-purple-600 dark:text-purple-400'"></i>
                            </template>
                            <span x-text="selected ? icons.find(i => i.class === selected)?.name : 'Select an icon'"></span>
                        </div>
                        <i class="fa-duotone fa-chevron-down text-sm" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-white/10 rounded-lg shadow-lg max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-2 gap-1 p-2">
                            <template x-for="icon in icons" :key="icon.class">
                                <button type="button" 
                                        @click="selected = icon.class; open = false"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-50 dark:hover:bg-white/5 transition-colors"
                                        :class="selected === icon.class ? 'bg-purple-100 dark:bg-purple-900/20' : ''">
                                    <i :class="'fa-duotone ' + icon.class + ' text-xl text-purple-600 dark:text-purple-400'"></i>
                                    <span class="text-sm text-gray-700 dark:text-white/80" x-text="icon.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        Display Order
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-400">
                        <span class="ml-2 text-sm text-gray-700 dark:text-white/80">Active</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <i class="fa-duotone fa-check"></i>
                        Create Vendor Type
                    </button>
                    <a href="{{ route('settings.vendor-types.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition-all duration-200">
                        <i class="fa-duotone fa-xmark"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-dashboard-layout>
