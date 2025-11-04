<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-white mb-2">Vendor Name *</label>
        <input type="text" name="vendor_name" value="{{ old('vendor_name', $vendor->vendor_name ?? '') }}" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Vendor Type</label>
        <select name="vendor_type_id" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">Select Type</option>
            @foreach($vendorTypes as $type)
                <option value="{{ $type->id }}" {{ old('vendor_type_id', $vendor->vendor_type_id ?? '') == $type->id ? 'selected' : '' }}>
                    @if($type->icon)
                        <i class="fa-duotone {{ $type->icon }}"></i>
                    @endif
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Phone *</label>
        <input type="text" name="phone" value="{{ old('phone', $vendor->phone ?? '') }}" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email', $vendor->email ?? '') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">City</label>
        <input type="text" name="city" value="{{ old('city', $vendor->city ?? '') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $vendor->contact_person ?? '') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-white mb-2">Address</label>
        <textarea name="address" rows="3" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('address', $vendor->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Commission Rate (%)</label>
        <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate', $vendor->commission_rate ?? '') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Status *</label>
        <select name="status" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="active" {{ old('status', $vendor->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $vendor->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-button type="submit" id="submitBtn">
        <span id="btnText">{{ isset($vendor) ? 'Update Vendor' : 'Create Vendor' }}</span>
        <span id="btnLoader" class="hidden">
            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </x-button>
    <a href="{{ route('vendors.index') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition">Cancel</a>
</div>

<script>
    document.getElementById('vendorForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        
        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                showToast(data.message || 'Validation failed', 'error');
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        }
    });
</script>
