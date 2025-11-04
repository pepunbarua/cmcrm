<x-dashboard-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Vendor Type</h1>
            <p class="text-gray-600 dark:text-white/60 text-sm mt-1">Update vendor type category</p>
        </div>

        <!-- Form -->
        <x-card>
            <form action="{{ route('settings.vendor-types.update', $vendorType) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $vendorType->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        FontAwesome Icon Class
                    </label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $vendorType->icon) }}" placeholder="fa-building"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500 dark:text-white/50">Example: fa-building, fa-hotel, fa-warehouse</p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                        Display Order
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $vendorType->order) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-white/10 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent bg-white dark:bg-white/5 text-gray-900 dark:text-white">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vendorType->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-400">
                        <span class="ml-2 text-sm text-gray-700 dark:text-white/80">Active</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition-all duration-200">
                        <i class="fa-duotone fa-check"></i>
                        Update Vendor Type
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
