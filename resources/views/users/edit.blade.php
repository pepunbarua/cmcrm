<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Edit User</h1>
                    <p class="text-gray-600 dark:text-white/60">Update user information and settings</p>
                </div>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Avatar Preview -->
            <div class="lg:col-span-1">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h3>
                    <div class="text-center">
                        <div id="avatar-preview" class="w-48 h-48 mx-auto mb-4 rounded-full border-2 border-dashed border-gray-300 dark:border-white/20 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-white/5">
                            @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-4xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-white/60">Recommended: 500x500px, PNG or JPG, Max 2MB</p>
                    </div>
                </x-card>
            </div>

            <!-- User Form -->
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Information</h3>
                    
                    <form id="userForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Full Name *</label>
                                <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Email Address *</label>
                                <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">New Password</label>
                                    <input type="password" name="password" minlength="8" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <p class="mt-1 text-xs text-gray-600 dark:text-white/60">Leave blank to keep current password</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" minlength="8" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Role *</label>
                                <select name="role" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-1">Change Profile Picture</label>
                                <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/jpg,image/png" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                    Update User
                                </button>
                                <a href="{{ route('users.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>

    <script>
        // Avatar preview
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').innerHTML = 
                        `<img src="${e.target.result}" alt="Avatar Preview" class="w-full h-full object-cover rounded-full">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validate passwords match if password is being changed
            const password = formData.get('password');
            const passwordConfirm = formData.get('password_confirmation');
            
            if (password && password !== passwordConfirm) {
                showToast('Passwords do not match!', 'error');
                return;
            }
            
            try {
                const response = await fetch('{{ route('users.update', $user) }}', {
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
                showToast('Error updating user', 'error');
                console.error(error);
            }
        });
    </script>
</x-dashboard-layout>
