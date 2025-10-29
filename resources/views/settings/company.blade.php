<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Company Profile</h1>
            <p class="text-gray-600 dark:text-white/60">Manage your company information and branding</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Company Logo Preview -->
            <div class="lg:col-span-1">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Logo</h3>
                    <div class="text-center">
                        <div id="logo-preview" class="w-48 h-48 mx-auto mb-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-white/20 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-white/5">
                            @if($company['company_logo'])
                            <img src="{{ asset('storage/' . $company['company_logo']) }}" alt="Company Logo" class="w-full h-full object-contain">
                            @else
                            <div class="text-center p-4">
                                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-white/60">No logo uploaded</p>
                            </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-white/60">Recommended: 500x500px, PNG or JPG, Max 2MB</p>
                    </div>
                </x-card>
            </div>

            <!-- Company Information Form -->
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Information</h3>
                    
                    <form id="companyForm" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Company Name *</label>
                                <input type="text" name="company_name" value="{{ $company['company_name'] }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Email *</label>
                                    <input type="email" name="company_email" value="{{ $company['company_email'] }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Phone</label>
                                    <input type="text" name="company_phone" value="{{ $company['company_phone'] }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Website</label>
                                <input type="url" name="company_website" value="{{ $company['company_website'] }}" placeholder="https://example.com" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Address</label>
                                <textarea name="company_address" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">{{ $company['company_address'] }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Upload Logo</label>
                                <input type="file" name="company_logo" id="logoInput" accept="image/jpeg,image/jpg,image/png" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>

    <script>
        // Logo preview
        document.getElementById('logoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').innerHTML = 
                        `<img src="${e.target.result}" alt="Logo Preview" class="w-full h-full object-contain">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('companyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('{{ route('settings.company.update') }}', {
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
                showToast('Error updating company profile', 'error');
                console.error(error);
            }
        });
    </script>
</x-dashboard-layout>
