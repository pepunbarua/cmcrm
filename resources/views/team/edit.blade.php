<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/60 mb-2">
                <a href="{{ route('team.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Team Members</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('team.show', ['team' => $team]) }}" class="hover:text-purple-600 dark:hover:text-purple-400">{{ $team->user->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span>Edit</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Team Member</h1>
        </div>

        <form id="teamForm" class="max-w-4xl">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <x-card class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input 
                        label="Full Name" 
                        name="name" 
                        type="text" 
                        required 
                        value="{{ $team->user->name }}"
                        placeholder="Enter full name"
                    />
                    
                    <x-input 
                        label="Email Address" 
                        name="email" 
                        type="email" 
                        required 
                        value="{{ $team->user->email }}"
                        placeholder="email@example.com"
                    />
                    
                    <x-input 
                        label="Phone Number" 
                        name="phone" 
                        type="tel" 
                        value="{{ $team->user->phone ?? '' }}"
                        placeholder="+880 1XXX-XXXXXX"
                    />
                    
                    <x-input 
                        label="New Password (leave blank to keep current)" 
                        name="password" 
                        type="password" 
                        placeholder="Minimum 8 characters"
                    />
                </div>
            </x-card>

            <!-- Role & Position -->
            <x-card class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Role & Position</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">System Role *</label>
                        <select name="role" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $team->user->hasRole($role->name) ? 'selected' : '' }} style="background-color: #1f2937; color: white;">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Job Type *</label>
                        <select name="role_type" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                            <option value="photographer" {{ $team->role_type == 'photographer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Photographer</option>
                            <option value="videographer" {{ $team->role_type == 'videographer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Videographer</option>
                            <option value="editor" {{ $team->role_type == 'editor' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Editor</option>
                            <option value="assistant" {{ $team->role_type == 'assistant' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Assistant</option>
                            <option value="sales_manager" {{ $team->role_type == 'sales_manager' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Sales Manager</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Skill Level *</label>
                        <select name="skill_level" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                            <option value="junior" {{ $team->skill_level == 'junior' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Junior</option>
                            <option value="mid_level" {{ $team->skill_level == 'mid_level' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Mid-Level</option>
                            <option value="senior" {{ $team->skill_level == 'senior' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Senior</option>
                            <option value="expert" {{ $team->skill_level == 'expert' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Expert</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Availability Status *</label>
                        <select name="availability_status" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                            <option value="available" {{ $team->availability_status == 'available' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Available</option>
                            <option value="busy" {{ $team->availability_status == 'busy' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Busy</option>
                            <option value="on_leave" {{ $team->availability_status == 'on_leave' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">On Leave</option>
                        </select>
                    </div>
                </div>
            </x-card>

            <!-- Professional Details -->
            <x-card class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input 
                        label="Hourly Rate (৳)" 
                        name="hourly_rate" 
                        type="number" 
                        step="0.01"
                        value="{{ $team->hourly_rate ?? '' }}"
                        placeholder="0.00"
                    />
                    
                    <x-input 
                        label="Portfolio Link" 
                        name="portfolio_link" 
                        type="url" 
                        value="{{ $team->portfolio_link ?? '' }}"
                        placeholder="https://portfolio.example.com"
                    />
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Equipment Owned</label>
                    <textarea name="equipment_owned" rows="3" placeholder="List equipment owned (cameras, lenses, lights, etc.)" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">{{ $team->equipment_owned ?? '' }}</textarea>
                </div>
            </x-card>

            <!-- Assignment Settings -->
            <x-card class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_default_assigned" value="1" {{ $team->is_default_assigned ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 dark:border-white/20 text-purple-600 focus:ring-purple-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Default Assignment</span>
                                <p class="text-xs text-gray-600 dark:text-white/60">Auto-assign to new events</p>
                            </div>
                        </label>
                    </div>

                    <x-input 
                        label="Priority Order" 
                        name="priority_order" 
                        type="number" 
                        value="{{ $team->priority_order ?? 0 }}"
                        placeholder="0"
                        helpText="Lower number = higher priority"
                    />
                </div>
            </x-card>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Team Member
                </button>
                <a href="{{ route('team.show', ['team' => $team]) }}" class="px-6 py-3 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-white/20 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('teamForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Updating...';
            
            const formData = new FormData(form);
            
            try {
                const response = await fetch('{{ route("team.update", $team) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showToast(data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    </script>
</x-dashboard-layout>
