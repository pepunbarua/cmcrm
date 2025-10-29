<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Roles & Permissions</h1>
                <p class="text-gray-600 dark:text-white/60">Manage user roles and their permission levels</p>
            </div>
            <button onclick="openCreateModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New Role
            </button>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
            <x-card>
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $role->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-white/60">{{ $role->permissions->count() }} permissions</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick='openEditModal(@json($role))' class="p-2 text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/20 rounded-lg transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if(!in_array($role->name, ['admin', 'manager', 'photographer']))
                        <button onclick="deleteRole({{ $role->id }})" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/20 rounded-lg transition" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($role->permissions->sortBy('name') as $permission)
                    <div class="flex items-center gap-2 px-2 py-1 bg-gray-50 dark:bg-white/5 rounded text-xs">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-900 dark:text-white">{{ $permission->name }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-600 dark:text-white/60">No permissions assigned</p>
                    @endforelse
                </div>
            </x-card>
            @endforeach
        </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <div id="roleModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Add New Role</h3>
                    <button onclick="closeModal()" class="text-gray-600 dark:text-white/60 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="roleForm" class="p-6">
                @csrf
                <input type="hidden" id="roleId" name="role_id">
                <input type="hidden" id="formMethod" value="POST">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Role Name *</label>
                    <input type="text" id="roleName" name="name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-white/80 mb-3">Permissions *</h4>
                    <p class="text-xs text-gray-600 dark:text-white/60 mb-4">Select at least one permission</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($permissions as $module => $perms)
                        <div class="border border-gray-200 dark:border-white/10 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">{{ $module }}</h5>
                            <div class="space-y-2">
                                @foreach($perms as $permission)
                                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 p-2 rounded">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="permission-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:border-white/20 dark:bg-white/5">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-white/10">
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Save Role
                    </button>
                    <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add New Role';
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = '';
            document.getElementById('formMethod').value = 'POST';
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('roleModal').classList.remove('hidden');
        }

        function openEditModal(role) {
            document.getElementById('modalTitle').textContent = 'Edit Role';
            document.getElementById('roleId').value = role.id;
            document.getElementById('roleName').value = role.name;
            document.getElementById('formMethod').value = 'PUT';
            
            // Check existing permissions
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            role.permissions.forEach(permission => {
                const checkbox = document.querySelector(`input[value="${permission.name}"]`);
                if (checkbox) checkbox.checked = true;
            });
            
            document.getElementById('roleModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }

        // Form submission
        document.getElementById('roleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const roleId = document.getElementById('roleId').value;
            const method = document.getElementById('formMethod').value;
            
            // Check if at least one permission is selected
            const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'));
            if (selectedPermissions.length === 0) {
                showToast('Please select at least one permission!', 'error');
                return;
            }
            
            const url = roleId ? `/roles/${roleId}` : '/roles';
            
            try {
                const response = await fetch(url, {
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
                showToast('Error saving role', 'error');
                console.error(error);
            }
        });

        async function deleteRole(roleId) {
            if (!confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                return;
            }
            
            try {
                const response = await fetch(`/roles/${roleId}`, {
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
                    showToast(data.message || 'Failed to delete role', 'error');
                }
            } catch (error) {
                showToast('Error deleting role', 'error');
                console.error(error);
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-dashboard-layout>
