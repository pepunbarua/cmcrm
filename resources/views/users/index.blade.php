<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">User Management</h1>
                <p class="text-gray-600 dark:text-white/60">Manage system users and their access levels</p>
            </div>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New User
            </a>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                label="Total Users" 
                :value="$users->total()" 
                icon="users"
                color="purple"
            />
            <x-stat-card 
                label="Admins" 
                :value="App\Models\User::role('admin')->count()" 
                icon="shield-check"
                color="red"
            />
            <x-stat-card 
                label="Managers" 
                :value="App\Models\User::role('manager')->count()" 
                icon="briefcase"
                color="blue"
            />
            <x-stat-card 
                label="Photographers" 
                :value="App\Models\User::role('photographer')->count()" 
                icon="camera"
                color="green"
            />
        </div>

        <!-- Filters -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="w-48">
                    <select name="role" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Filter
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Clear
                </a>
                @endif
            </form>
        </x-card>

        <!-- Users Table -->
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">User</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Email</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Joined Date</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700 dark:text-white/80">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($users as $user)
                        <tr class="hover:bg-purple-50 dark:hover:bg-white/5 transition">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ $user->email }}
                            </td>
                            <td class="py-3 px-4">
                                @foreach($user->roles as $role)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium
                                    {{ $role->name == 'admin' ? 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' : '' }}
                                    {{ $role->name == 'manager' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : '' }}
                                    {{ $role->name == 'photographer' ? 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400' : '' }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/20 rounded-lg transition" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="p-2 text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/20 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <button onclick="deleteUser({{ $user->id }})" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-600 dark:text-white/60">
                                No users found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="mt-4 border-t border-gray-200 dark:border-white/10 pt-4">
                {{ $users->links() }}
            </div>
            @endif
        </x-card>
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
                    setTimeout(() => window.location.reload(), 1000);
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
