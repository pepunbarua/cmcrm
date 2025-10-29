<form id="leadForm" method="POST" action="{{ $lead ? route('leads.update', $lead) : route('leads.store') }}">
    @csrf
    @if($lead)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Vendor -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Vendor Source <span class="text-red-400">*</span>
            </label>
            <select name="vendor_id" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="">Select Vendor</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $lead->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->vendor_name }} ({{ ucfirst(str_replace('_', ' ', $vendor->vendor_type)) }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Client Name -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Client Name <span class="text-red-400">*</span>
            </label>
            <input type="text" name="client_name" value="{{ old('client_name', $lead->client_name ?? '') }}" required 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Client Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Client Phone <span class="text-red-400">*</span>
            </label>
            <input type="text" name="client_phone" value="{{ old('client_phone', $lead->client_phone ?? '') }}" required 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Client Email -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Client Email
            </label>
            <input type="email" name="client_email" value="{{ old('client_email', $lead->client_email ?? '') }}" 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Event Type -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Event Type <span class="text-red-400">*</span>
            </label>
            <select name="event_type" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="">Select Event Type</option>
                <option value="wedding" {{ old('event_type', $lead->event_type ?? '') == 'wedding' ? 'selected' : '' }}>Wedding</option>
                <option value="birthday" {{ old('event_type', $lead->event_type ?? '') == 'birthday' ? 'selected' : '' }}>Birthday</option>
                <option value="corporate" {{ old('event_type', $lead->event_type ?? '') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                <option value="portrait" {{ old('event_type', $lead->event_type ?? '') == 'portrait' ? 'selected' : '' }}>Portrait</option>
                <option value="other" {{ old('event_type', $lead->event_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <!-- Event Date -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Event Date
            </label>
            <input type="date" name="event_date" value="{{ old('event_date', $lead->event_date ?? '') }}" 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Budget Range -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Budget Range
            </label>
            <input type="text" name="budget_range" value="{{ old('budget_range', $lead->budget_range ?? '') }}" placeholder="e.g., 50,000 - 1,00,000" 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Package Interest -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Package Interest
            </label>
            <input type="text" name="package_interest" value="{{ old('package_interest', $lead->package_interest ?? '') }}" placeholder="e.g., Premium Photography + Videography" 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Status <span class="text-red-400">*</span>
            </label>
            <select name="status" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="new" {{ old('status', $lead->status ?? 'new') == 'new' ? 'selected' : '' }}>New</option>
                <option value="contacted" {{ old('status', $lead->status ?? '') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="follow_up" {{ old('status', $lead->status ?? '') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                <option value="qualified" {{ old('status', $lead->status ?? '') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                <option value="converted" {{ old('status', $lead->status ?? '') == 'converted' ? 'selected' : '' }}>Converted</option>
                <option value="lost" {{ old('status', $lead->status ?? '') == 'lost' ? 'selected' : '' }}>Lost</option>
            </select>
        </div>

        <!-- Notes -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Notes
            </label>
            <textarea name="notes" rows="4" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">{{ old('notes', $lead->notes ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <x-button type="submit" id="submitBtn">
            {{ $lead ? 'Update Lead' : 'Create Lead' }}
        </x-button>
        <a href="{{ route('leads.index') }}" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition-all duration-300">
            Cancel
        </a>
    </div>
</form>

<script>
document.getElementById('leadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            showToast(result.message || 'An error occurred', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    } catch (error) {
        showToast('An error occurred', 'error');
        console.error(error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});
</script>
