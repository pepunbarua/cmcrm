@php
    $initialItems = old('items');
    if ($initialItems === null) {
        $initialItems = isset($package)
            ? $package->items->map(function ($item) {
                return [
                    'package_content_id' => $item->package_content_id,
                    'default_qty' => $item->default_qty,
                    'default_unit_price' => $item->default_unit_price,
                    'is_mandatory' => $item->is_mandatory ? 1 : 0,
                    'is_editable' => $item->is_editable ? 1 : 0,
                    'sort_order' => $item->sort_order,
                ];
            })->toArray()
            : [];
    }

    $contentOptions = $contents->map(function ($content) {
        return [
            'id' => $content->id,
            'name' => $content->name,
            'unit' => $content->unit,
            'base_price' => number_format((float) $content->base_price, 2, '.', ''),
        ];
    })->values();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-white mb-2">Package Name *</label>
        <input type="text" name="name" required value="{{ old('name', $package->name ?? '') }}" placeholder="e.g., Premium Wedding Package" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Package Code</label>
        <input type="text" name="code" value="{{ old('code', $package->code ?? '') }}" placeholder="e.g., WED-PREMIUM" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Pricing Mode *</label>
        <select name="pricing_mode" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="fixed" {{ old('pricing_mode', $package->pricing_mode ?? 'item_sum') === 'fixed' ? 'selected' : '' }}>Fixed</option>
            <option value="item_sum" {{ old('pricing_mode', $package->pricing_mode ?? 'item_sum') === 'item_sum' ? 'selected' : '' }}>Item Sum</option>
            <option value="hybrid" {{ old('pricing_mode', $package->pricing_mode ?? 'item_sum') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Base Price (৳) *</label>
        <input type="number" name="base_price" required min="0" step="0.01" value="{{ old('base_price', isset($package) ? number_format((float) $package->base_price, 2, '.', '') : '0.00') }}" class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-white mb-2">Status *</label>
        <select name="is_active" required class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="1" {{ old('is_active', isset($package) ? (int) $package->is_active : 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', isset($package) ? (int) $package->is_active : 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-white mb-2">Description</label>
        <textarea name="description" rows="3" placeholder="Optional package details..." class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('description', $package->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-8">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-white">Package Items</h3>
            <p class="text-white/60 text-sm">Select content, quantity, and default unit price for this package</p>
        </div>
        <button type="button" id="addItemBtn" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-medium transition">
            + Add Content
        </button>
    </div>

    @if($contents->isEmpty())
        <div class="mb-3 px-4 py-3 rounded-xl bg-yellow-500/10 border border-yellow-500/30 text-yellow-200 text-sm">
            No active package content found.
            <a href="{{ route('package-contents.create') }}" class="underline font-medium">Create package content first</a>.
        </div>
    @endif

    <div id="itemsContainer" class="space-y-3">
        @foreach($initialItems as $index => $item)
            <div class="package-item-row grid grid-cols-1 lg:grid-cols-12 gap-3 p-4 rounded-xl bg-white/5 border border-white/10" data-index="{{ $index }}">
                <div class="lg:col-span-4">
                    <label class="block text-xs font-medium text-white/60 mb-1">Content</label>
                    <select name="items[{{ $index }}][package_content_id]" required class="content-select w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Content</option>
                        @foreach($contents as $content)
                            <option value="{{ $content->id }}" data-default-price="{{ number_format((float) $content->base_price, 2, '.', '') }}" {{ (string) ($item['package_content_id'] ?? '') === (string) $content->id ? 'selected' : '' }}>
                                {{ $content->name }} ({{ $content->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-white/60 mb-1">Qty</label>
                    <input type="number" name="items[{{ $index }}][default_qty]" required min="0.01" step="0.01" value="{{ $item['default_qty'] ?? '1.00' }}" class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-white/60 mb-1">Unit Price</label>
                    <input type="number" name="items[{{ $index }}][default_unit_price]" min="0" step="0.01" value="{{ array_key_exists('default_unit_price', $item) && $item['default_unit_price'] !== null && $item['default_unit_price'] !== '' ? number_format((float) $item['default_unit_price'], 2, '.', '') : '' }}" class="unit-price-input w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-white/60 mb-1">Mandatory</label>
                    <select name="items[{{ $index }}][is_mandatory]" required class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="1" {{ (int) ($item['is_mandatory'] ?? 0) === 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ (int) ($item['is_mandatory'] ?? 0) === 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-white/60 mb-1">Editable</label>
                    <div class="flex items-center gap-2">
                        <select name="items[{{ $index }}][is_editable]" required class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="1" {{ (int) ($item['is_editable'] ?? 1) === 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ (int) ($item['is_editable'] ?? 1) === 0 ? 'selected' : '' }}>No</option>
                        </select>
                        <button type="button" class="remove-item-btn p-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 transition" title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="items[{{ $index }}][sort_order]" value="{{ $item['sort_order'] ?? $index }}" class="sort-order-input">
            </div>
        @endforeach
    </div>

    <div id="emptyItemState" class="mt-3 px-4 py-3 rounded-xl border border-dashed border-white/20 text-sm text-white/60 {{ count($initialItems) > 0 ? 'hidden' : '' }}">
        No content added yet. Click <strong class="text-white">Add Content</strong> to start.
    </div>
</div>

<div class="mt-8 flex gap-3">
    <x-button type="submit" id="submitBtn">
        <span id="btnText">{{ isset($package) ? 'Update Package' : 'Create Package' }}</span>
        <span id="btnLoader" class="hidden">
            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </x-button>
    <a href="{{ route('packages.index') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition">Cancel</a>
</div>

<script>
    const contents = @json($contentOptions);
    const itemsContainer = document.getElementById('itemsContainer');
    const emptyState = document.getElementById('emptyItemState');
    const addItemBtn = document.getElementById('addItemBtn');

    if (!contents.length) {
        addItemBtn.disabled = true;
        addItemBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    let nextIndex = (() => {
        const rows = Array.from(document.querySelectorAll('.package-item-row'));
        if (!rows.length) return 0;
        return Math.max(...rows.map(row => Number(row.dataset.index || 0))) + 1;
    })();

    function escapeHtml(text) {
        return String(text)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function contentOptionsMarkup(selectedId = '') {
        let markup = '<option value="">Select Content</option>';
        contents.forEach(content => {
            const selected = String(selectedId) === String(content.id) ? 'selected' : '';
            markup += `<option value="${content.id}" data-default-price="${content.base_price}" ${selected}>${escapeHtml(content.name)} (${escapeHtml(content.unit)})</option>`;
        });
        return markup;
    }

    function updateSortOrders() {
        document.querySelectorAll('.package-item-row').forEach((row, index) => {
            const sortInput = row.querySelector('.sort-order-input');
            if (sortInput) sortInput.value = index;
        });
    }

    function toggleEmptyState() {
        const hasRows = document.querySelectorAll('.package-item-row').length > 0;
        emptyState.classList.toggle('hidden', hasRows);
    }

    function addRow(data = {}) {
        const index = nextIndex++;
        const row = document.createElement('div');
        row.className = 'package-item-row grid grid-cols-1 lg:grid-cols-12 gap-3 p-4 rounded-xl bg-white/5 border border-white/10';
        row.dataset.index = String(index);

        row.innerHTML = `
            <div class="lg:col-span-4">
                <label class="block text-xs font-medium text-white/60 mb-1">Content</label>
                <select name="items[${index}][package_content_id]" required class="content-select w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    ${contentOptionsMarkup(data.package_content_id || '')}
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-white/60 mb-1">Qty</label>
                <input type="number" name="items[${index}][default_qty]" required min="0.01" step="0.01" value="${data.default_qty ?? '1.00'}" class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-white/60 mb-1">Unit Price</label>
                <input type="number" name="items[${index}][default_unit_price]" min="0" step="0.01" value="${data.default_unit_price ?? ''}" class="unit-price-input w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-white/60 mb-1">Mandatory</label>
                <select name="items[${index}][is_mandatory]" required class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="1" ${String(data.is_mandatory ?? '0') === '1' ? 'selected' : ''}>Yes</option>
                    <option value="0" ${String(data.is_mandatory ?? '0') === '0' ? 'selected' : ''}>No</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-white/60 mb-1">Editable</label>
                <div class="flex items-center gap-2">
                    <select name="items[${index}][is_editable]" required class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="1" ${String(data.is_editable ?? '1') === '1' ? 'selected' : ''}>Yes</option>
                        <option value="0" ${String(data.is_editable ?? '1') === '0' ? 'selected' : ''}>No</option>
                    </select>
                    <button type="button" class="remove-item-btn p-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 transition" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <input type="hidden" name="items[${index}][sort_order]" value="${data.sort_order ?? 0}" class="sort-order-input">
        `;

        itemsContainer.appendChild(row);
        updateSortOrders();
        toggleEmptyState();
    }

    addItemBtn.addEventListener('click', () => addRow());

    itemsContainer.addEventListener('click', (event) => {
        const button = event.target.closest('.remove-item-btn');
        if (!button) return;

        button.closest('.package-item-row')?.remove();
        updateSortOrders();
        toggleEmptyState();
    });

    itemsContainer.addEventListener('change', (event) => {
        const select = event.target.closest('.content-select');
        if (!select) return;

        const row = select.closest('.package-item-row');
        const priceInput = row?.querySelector('.unit-price-input');
        const selectedOption = select.options[select.selectedIndex];
        const defaultPrice = selectedOption?.dataset.defaultPrice;

        if (priceInput && !priceInput.value && defaultPrice) {
            priceInput.value = defaultPrice;
        }
    });

    toggleEmptyState();
    updateSortOrders();

    document.getElementById('packageForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        updateSortOrders();

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
            showToast(firstError || data.message || 'Failed to save package.', 'error');
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        }
    });
</script>
