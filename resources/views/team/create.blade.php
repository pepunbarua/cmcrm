<x-dashboard-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/60 mb-4">
                <a href="{{ route('team.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400">Team Members</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span>Add New Member</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Team Member</h1>
        </div>

        <!-- Form -->
        <x-card>
            <form id="teamForm" class="space-y-6">
                <!-- Personal Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            label="Full Name" 
                            name="name" 
                            type="text" 
                            required 
                            placeholder="John Doe"
                        />
                        
                        <x-input 
                            label="Email" 
                            name="email" 
                            type="email" 
                            required 
                            placeholder="john@example.com"
                        />
                        
                        <x-input 
                            label="Password" 
                            name="password" 
                            type="password" 
                            required 
                            placeholder="Minimum 8 characters"
                        />
                        
                        <x-input 
                            label="Phone (Optional)" 
                            name="phone" 
                            type="text" 
                            placeholder="+880 1XXX-XXXXXX"
                        />
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Role & Permissions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                                System Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                                <option value="" style="background-color: #1f2937; color: white;">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" style="background-color: #1f2937; color: white;">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                                Role Type <span class="text-red-500">*</span>
                            </label>
                            <select name="role_type" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                                <option value="" style="background-color: #1f2937; color: white;">Select Type</option>
                                <option value="photographer" style="background-color: #1f2937; color: white;">Photographer</option>
                                <option value="videographer" style="background-color: #1f2937; color: white;">Videographer</option>
                                <option value="editor" style="background-color: #1f2937; color: white;">Editor</option>
                                <option value="assistant" style="background-color: #1f2937; color: white;">Assistant</option>
                                <option value="sales_manager" style="background-color: #1f2937; color: white;">Sales Manager</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Professional Details -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                                Skill Level <span class="text-red-500">*</span>
                            </label>
                            <select name="skill_level" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                                <option value="" style="background-color: #1f2937; color: white;">Select Level</option>
                                <option value="junior" style="background-color: #1f2937; color: white;">Junior</option>
                                <option value="mid_level" style="background-color: #1f2937; color: white;">Mid-Level</option>
                                <option value="senior" style="background-color: #1f2937; color: white;">Senior</option>
                                <option value="expert" style="background-color: #1f2937; color: white;">Expert</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">
                                Availability Status <span class="text-red-500">*</span>
                            </label>
                            <select name="availability_status" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500" style="color-scheme: dark;">
                                <option value="available" selected style="background-color: #1f2937; color: white;">Available</option>
                                <option value="busy" style="background-color: #1f2937; color: white;">Busy</option>
                                <option value="on_leave" style="background-color: #1f2937; color: white;">On Leave</option>
                            </select>
                        </div>

                        <x-input 
                            label="Hourly Rate (৳)" 
                            name="hourly_rate" 
                            type="number" 
                            step="0.01"
                            placeholder="1500.00"
                        />
                    </div>
                </div>

                <!-- Additional Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white/80 mb-2">Equipment Owned</label>
                            <textarea name="equipment_owned" rows="3" placeholder="Camera: Canon EOS R5, Lenses: 24-70mm, 70-200mm, Lighting: Godox AD600..." class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>

                        <x-input 
                            label="Portfolio Link" 
                            name="portfolio_link" 
                            type="url" 
                            placeholder="https://portfolio.example.com"
                        />

                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_default_assigned" value="1" id="is_default_assigned" class="w-4 h-4 text-purple-600 border-gray-300 dark:border-white/20 rounded focus:ring-purple-500">
                                <label for="is_default_assigned" class="text-sm text-gray-700 dark:text-white/80">Default for auto-assignment</label>
                            </div>

                            <x-input 
                                label="Priority Order" 
                                name="priority_order" 
                                type="number" 
                                min="0"
                                value="0"
                                class="w-32"
                            />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-6 border-t border-gray-200 dark:border-white/10">
                    <x-button type="submit" variant="primary" id="submitBtn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Team Member
                    </x-button>
                    <x-button href="{{ route('team.index') }}" variant="secondary">
                        Cancel
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.getElementById('teamForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('{{ route('team.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showToast(result.message, 'error');
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
