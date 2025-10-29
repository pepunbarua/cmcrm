<form id="orderForm" method="POST" action="{{ $order ? route('orders.update', $order) : route('orders.store') }}">
    @csrf
    @if($order)
        @method('PUT')
    @endif

    @if($lead)
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
        
        <!-- Lead Information Display -->
        <div class="mb-6 p-4 rounded-lg bg-purple-500/10 border border-purple-500/20">
            <h3 class="text-lg font-semibold text-purple-300 mb-3">Client Information</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Client Name</p>
                    <p class="text-white font-medium">{{ $lead->client_name }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Phone</p>
                    <p class="text-white font-medium">{{ $lead->client_phone }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Event Type</p>
                    <p class="text-white font-medium">{{ ucfirst($lead->event_type) }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Event Date</p>
                    <p class="text-white font-medium">{{ $lead->event_date ? \Carbon\Carbon::parse($lead->event_date)->format('d M Y') : 'Not Set' }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Package Name -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Package Name <span class="text-red-400">*</span>
            </label>
            <input type="text" name="package_name" value="{{ old('package_name', $order->package_name ?? $lead->package_interest ?? '') }}" required 
                   placeholder="e.g., Premium Wedding Photography & Videography"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Package Details -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Package Details
            </label>
            <textarea name="package_details" rows="4" placeholder="Describe what's included in the package..."
                      class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">{{ old('package_details', $order->package_details ?? '') }}</textarea>
        </div>

        <!-- Total Amount -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Total Amount (৳) <span class="text-red-400">*</span>
            </label>
            <input type="number" name="total_amount" value="{{ old('total_amount', $order->total_amount ?? '') }}" required min="0" step="0.01"
                   placeholder="e.g., 50000"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Discount Amount -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Discount Amount (৳)
            </label>
            <input type="number" name="discount_amount" value="{{ old('discount_amount', $order->discount_amount ?? 0) }}" min="0" step="0.01"
                   placeholder="e.g., 5000"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>

        <!-- Advance Amount (Only for new orders) -->
        @if(!$order)
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Advance Payment (৳)
            </label>
            <input type="number" name="advance_amount" value="{{ old('advance_amount', 0) }}" min="0" step="0.01"
                   placeholder="e.g., 10000"
                   class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400">
            <p class="text-xs text-gray-400 mt-1">Initial payment received with order</p>
        </div>
        @endif

        <!-- Payment Status -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Payment Status <span class="text-red-400">*</span>
            </label>
            <select name="payment_status" required class="w-full px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="pending" {{ old('payment_status', $order->payment_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ old('payment_status', $order->payment_status ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ old('payment_status', $order->payment_status ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <x-button type="submit" id="submitBtn">
            {{ $order ? 'Update Order' : 'Create Order' }}
        </x-button>
        <a href="{{ route('orders.index') }}" class="px-6 py-2 bg-gray-500/20 text-white rounded-lg hover:bg-gray-500/30 transition-all duration-300">
            Cancel
        </a>
    </div>
</form>

<script>
document.getElementById('orderForm').addEventListener('submit', async function(e) {
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
