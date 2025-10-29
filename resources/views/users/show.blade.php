<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">User Profile</h1>
                    <p class="text-gray-600 dark:text-white/60">View user details and permissions</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        ← Back to Users
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <x-card>
                    <div class="text-center">
                        @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-32 h-32 mx-auto rounded-full object-cover mb-4">
                        @else
                        <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-4xl font-bold mb-4">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        @endif
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $user->name }}</h3>
                        <p class="text-gray-600 dark:text-white/60 mb-4">{{ $user->email }}</p>
                        
                        <div class="flex items-center justify-center gap-2 mb-4">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium
                                {{ $role->name == 'admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' : '' }}
                                {{ $role->name == 'manager' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : '' }}
                                {{ $role->name == 'photographer' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400' : '' }}">
                                {{ ucfirst($role->name) }}
                            </span>
                            @endforeach
                        </div>
                        
                        <div class="text-sm text-gray-600 dark:text-white/60">
                            <p class="mb-2">
                                <span class="font-medium">Joined:</span> {{ $user->created_at->format('M d, Y') }}
                            </p>
                            <p>
                                <span class="font-medium">Last Updated:</span> {{ $user->updated_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </x-card>

                <!-- Quick Actions -->
                @if($user->id !== auth()->id())
                <x-card class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <button onclick="deleteUser({{ $user->id }})" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete User
                        </button>
                    </div>
                </x-card>
                @endif
            </div>

            <!-- User Details & Permissions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Permissions Card -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assigned Permissions</h3>
                    
                    @if($user->roles->isNotEmpty())
                        @foreach($user->roles as $role)
                        <div class="mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">
                                Via {{ ucfirst($role->name) }} Role:
                            </h4>
                            
                            @if($role->permissions->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($role->permissions->sortBy('name') as $permission)
                                <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 dark:bg-purple-500/10 rounded-lg">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-600 dark:text-white/60">No permissions assigned to this role.</p>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-600 dark:text-white/60">No roles assigned to this user.</p>
                    @endif
                </x-card>

                <!-- Activity Log (if available) -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    
                    @php
                        $activities = $user->actions()->latest()->limit(10)->get();
                    @endphp
                    
                    @if($activities->count() > 0)
                    <div class="space-y-3">
                        @foreach($activities as $activity)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-white/5">
                            <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-600 dark:text-white/60 mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-600 dark:text-white/60">No recent activity.</p>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    <script>
        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }
            
            try {
                const response = await fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.href = '{{ route('users.index') }}', 1000);
                } else {
                    showToast(data.message || 'Failed to delete user', 'error');
                }
            } catch (error) {
                showToast('Error deleting user', 'error');
                console.error(error);
            }
        }
    </script>
</x-dashboard-layout>
