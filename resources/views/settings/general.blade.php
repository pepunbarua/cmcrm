<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">General Settings</h1>
            <p class="text-gray-600 dark:text-white/60">Configure system-wide preferences and defaults</p>
        </div>

        <x-card>
            <form id="settingsForm">
                @csrf
                <div class="space-y-6">
                    <!-- Application Settings -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Application Name *</label>
                                <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Timezone *</label>
                                <select name="timezone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="Asia/Dhaka" {{ $settings['timezone'] == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (GMT+6)</option>
                                    <option value="Asia/Kolkata" {{ $settings['timezone'] == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (GMT+5:30)</option>
                                    <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                    <option value="Europe/London" {{ $settings['timezone'] == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Format -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Date & Time Format</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Date Format *</label>
                                <select name="date_format" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-10-30)</option>
                                    <option value="d-m-Y" {{ $settings['date_format'] == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (30-10-2025)</option>
                                    <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (10/30/2025)</option>
                                    <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (30/10/2025)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Time Format *</label>
                                <select name="time_format" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                    <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Settings -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Currency Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Currency *</label>
                                <select name="currency" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="BDT" {{ $settings['currency'] == 'BDT' ? 'selected' : '' }}>BDT - Bangladeshi Taka</option>
                                    <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="INR" {{ $settings['currency'] == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Currency Symbol *</label>
                                <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Display Settings -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Display Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Items Per Page *</label>
                                <input type="number" name="items_per_page" value="{{ $settings['items_per_page'] }}" min="5" max="100" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <p class="mt-1 text-xs text-gray-600 dark:text-white/60">Number of items to display per page in lists</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('{{ route('settings.general.update') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1000);
                    }
                } else {
                    showToast(data.message || 'Something went wrong!', 'error');
                }
            } catch (error) {
                showToast('Error updating settings', 'error');
                console.error(error);
            }
        });
    </script>
</x-dashboard-layout>
