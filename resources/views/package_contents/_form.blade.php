<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-white mb-2">Content Name *</label>
        <input type="text" name="name" required value="{{ old('name', $packageContent->name ?? '') }}" placeholder="e.g., Event Coverage Hours" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Unit *</label>
        <input type="text" name="unit" required value="{{ old('unit', $packageContent->unit ?? 'item') }}" placeholder="e.g., hour, piece, day" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Base Price (৳) *</label>
        <input type="number" name="base_price" required min="0" step="0.01" value="{{ old('base_price', isset($packageContent) ? number_format((float) $packageContent->base_price, 2, '.', '') : '0.00') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Status *</label>
        <select name="is_active" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="1" {{ old('is_active', isset($packageContent) ? (int) $packageContent->is_active : 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', isset($packageContent) ? (int) $packageContent->is_active : 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-white mb-2">Description</label>
        <textarea name="description" rows="4" placeholder="Optional notes about this content..." class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('description', $packageContent->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-button type="submit" id="submitBtn">
        <span id="btnText">{{ isset($packageContent) ? 'Update Content' : 'Create Content' }}</span>
        <span id="btnLoader" class="hidden">
            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </x-button>
    <a href="{{ route('package-contents.index') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition">Cancel</a>
</div>

<script>
    document.getElementById('packageContentForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = e.target;
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');

        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: new FormData(form)
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = data.redirect, 900);
                return;
            }

            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            showToast(firstError || data.message || 'Failed to save package content.', 'error');
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        }
    });
</script>
