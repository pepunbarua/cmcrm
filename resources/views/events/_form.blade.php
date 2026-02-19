<form id="eventForm" method="POST" action="{{ $event ? route('events.update', $event) : route('events.store') }}">
    @csrf
    @if($event)
        @method('PUT')
    @endif

    @if($order)
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        
        <!-- Order Information Display -->
        <div class="mb-6 p-4 rounded-lg bg-purple-500/10 border border-purple-500/20">
            <h3 class="text-lg font-semibold text-purple-300 mb-3">Order Information</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Client Name</p>
                    <p class="text-white font-medium">{{ $order->client_display_name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Order Number</p>
                    <p class="text-white font-medium font-mono">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Event Type</p>
                    <p class="text-white font-medium">{{ ucfirst($order->event_type) }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Package</p>
                    <p class="text-white font-medium">{{ $order->package_display_name }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Event Date -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Event Date <span class="text-red-400">*</span>
            </label>
            <input type="date" name="event_date" value="{{ old('event_date', $event->event_date ?? $order?->event_date ?? '') }}" required 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Event Time -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Event Time <span class="text-red-400">*</span>
            </label>
            <input type="time" name="event_time" value="{{ old('event_time', $event->event_time ?? '') }}" required 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Venue -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Venue <span class="text-red-400">*</span>
            </label>
            <input type="text" name="venue" value="{{ old('venue', $event->venue ?? '') }}" required 
                   placeholder="e.g., Grand Palace Hotel"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Delivery Deadline -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Delivery Deadline <span class="text-red-400">*</span>
            </label>
            <input type="date" name="delivery_deadline" value="{{ old('delivery_deadline', $event->delivery_deadline ?? '') }}" required 
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Venue Address -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Venue Address
            </label>
            <input type="text" name="venue_address" value="{{ old('venue_address', $event->venue_address ?? '') }}" 
                   placeholder="Full address of the venue"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Photographer -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Photographer
            </label>
            <select name="photographer_id" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400" style="color-scheme: dark;">
                <option value="" class="bg-gray-800 text-white">Auto-assign</option>
                @foreach($teamMembers->where('role_type', 'photographer') as $member)
                    <option value="{{ $member->id }}" class="bg-gray-800 text-white" {{ old('photographer_id', $event->photographer_id ?? '') == $member->id ? 'selected' : '' }}>
                        {{ $member->user->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Leave empty to auto-assign based on availability</p>
        </div>

        <!-- Videographer -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Videographer
            </label>
            <select name="videographer_id" class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400" style="color-scheme: dark;">
                <option value="" class="bg-gray-800 text-white">Auto-assign</option>
                @foreach($teamMembers->where('role_type', 'videographer') as $member)
                    <option value="{{ $member->id }}" class="bg-gray-800 text-white" {{ old('videographer_id', $event->videographer_id ?? '') == $member->id ? 'selected' : '' }}>
                        {{ $member->user->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Leave empty to auto-assign based on availability</p>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Status <span class="text-red-400">*</span>
            </label>
            <select name="status" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400" style="color-scheme: dark;">
                <option value="scheduled" class="bg-gray-800 text-white" {{ old('status', $event->status ?? 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="in_progress" class="bg-gray-800 text-white" {{ old('status', $event->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" class="bg-gray-800 text-white" {{ old('status', $event->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" class="bg-gray-800 text-white" {{ old('status', $event->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <!-- Equipment Checklist -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Equipment Checklist
            </label>
            <textarea name="equipment_checklist" rows="3" placeholder="e.g., 2x DSLR Camera, 3x Lenses, Tripod, Lighting Setup..."
                      class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">{{ old('equipment_checklist', $event->equipment_checklist ?? '') }}</textarea>
        </div>

        <!-- Special Instructions -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Special Instructions
            </label>
            <textarea name="special_instructions" rows="3" placeholder="Any special requirements or instructions for the team..."
                      class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">{{ old('special_instructions', $event->special_instructions ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <x-button type="submit" id="submitBtn">
            {{ $event ? 'Update Event' : 'Schedule Event' }}
        </x-button>
        <a href="{{ route('events.index') }}" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition-all duration-300">
            Cancel
        </a>
    </div>
</form>

<script>
document.getElementById('eventForm').addEventListener('submit', async function(e) {
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
