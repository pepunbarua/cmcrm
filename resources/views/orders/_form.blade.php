@php
    $leadIdValue = old('lead_id', $lead->id ?? $order->lead_id ?? null);
    $selectedCustomerId = old('customer_id', $order->customer_id ?? null);
    $selectedPackageId = old('package_id', $order->package_id ?? $order->primaryOrderPackage?->package_id ?? null);

    $initialItems = old('items');
    if ($initialItems === null) {
        $initialItems = $order?->primaryOrderPackage?->contents
            ? $order->primaryOrderPackage->contents->map(function ($item) {
                return [
                    'package_content_id' => $item->package_content_id,
                    'content_name' => $item->content_name_snapshot,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'is_mandatory' => $item->is_mandatory ? 1 : 0,
                    'is_editable' => $item->is_editable ? 1 : 0,
                ];
            })->values()->all()
            : [];
    }

    $customersPayload = $customers->map(function ($customer) {
        return [
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'address' => $customer->address,
        ];
    })->values();

    $packagesPayload = $packages->map(function ($package) {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'items' => $package->items->map(function ($item) {
                return [
                    'package_content_id' => $item->package_content_id,
                    'content_name' => $item->content_name_snapshot,
                    'qty' => number_format((float) $item->default_qty, 2, '.', ''),
                    'unit_price' => number_format((float) ($item->default_unit_price ?? $item->packageContent?->base_price ?? 0), 2, '.', ''),
                    'is_mandatory' => $item->is_mandatory,
                    'is_editable' => $item->is_editable,
                ];
            })->values(),
        ];
    })->values();
@endphp

<form id="orderForm" method="POST" action="{{ $order ? route('orders.update', $order) : route('orders.store') }}">
    @csrf
    @if($order)
        @method('PUT')
    @endif

    @if($leadIdValue)
        <input type="hidden" name="lead_id" value="{{ $leadIdValue }}">
    @endif

    <div class="space-y-8">
        <div>
            <h3 class="text-lg font-semibold text-white mb-3">Customer Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-300 mb-2">Existing Customer (Optional)</label>
                    <select id="customerSelect" name="customer_id" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        <option value="">Create New Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (string) $selectedCustomerId === (string) $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-300 mb-2">Name *</label>
                    <input type="text" id="customerName" name="customer_name" value="{{ old('customer_name', $order?->client_name ?? $lead?->client_name ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Phone *</label>
                    <input type="text" id="customerPhone" name="customer_phone" value="{{ old('customer_phone', $order?->client_phone ?? $lead?->client_phone ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Email</label>
                    <input type="email" id="customerEmail" name="customer_email" value="{{ old('customer_email', $order?->client_email ?? $lead?->client_email ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Address</label>
                    <input type="text" id="customerAddress" name="customer_address" value="{{ old('customer_address', $order?->customer?->address ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-white mb-3">Event Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Event Type *</label>
                    <select name="event_type" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        @foreach(['wedding', 'birthday', 'corporate', 'anniversary', 'other'] as $eventType)
                            <option value="{{ $eventType }}" {{ old('event_type', $order?->event_type ?? $lead?->event_type ?? 'wedding') === $eventType ? 'selected' : '' }}>{{ ucfirst($eventType) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Event Date *</label>
                    <input type="date" name="event_date" value="{{ old('event_date', isset($order?->event_date) ? $order->event_date->format('Y-m-d') : ($lead?->event_date?->format('Y-m-d') ?? '')) }}" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Time Duration *</label>
                    <input type="text" name="time_duration" value="{{ old('time_duration', $order?->time_duration ?? '') }}" placeholder="e.g., 8 hours" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Location *</label>
                    <input type="text" name="location" value="{{ old('location', $order?->location ?? $order?->event_venue_name ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Bride Name</label>
                    <input type="text" name="bride_name" value="{{ old('bride_name', $order?->bride_name ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Groom Name</label>
                    <input type="text" name="groom_name" value="{{ old('groom_name', $order?->groom_name ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Photographer</label>
                    <select name="photographer_count" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        @for($count = 1; $count <= 5; $count++)
                            <option value="{{ $count }}" {{ (int) old('photographer_count', $order?->photographer_count ?? 1) === $count ? 'selected' : '' }}>{{ $count }} person</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Videographer</label>
                    <select name="videographer_count" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        @for($count = 1; $count <= 5; $count++)
                            <option value="{{ $count }}" {{ (int) old('videographer_count', $order?->videographer_count ?? 1) === $count ? 'selected' : '' }}>{{ $count }} person</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Outdoor Shoot</label>
                    <select name="outdoor_shoot" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        <option value="1" {{ (int) old('outdoor_shoot', $order?->outdoor_shoot ?? 0) === 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ (int) old('outdoor_shoot', $order?->outdoor_shoot ?? 0) === 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-300 mb-2">Requirements</label>
                    <textarea name="requirements" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">{{ old('requirements', $order?->requirements ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-white mb-3">Package & Items</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Select Package</label>
                    <select id="packageSelect" name="package_id" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white" style="color-scheme: dark;">
                        <option value="">Custom Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ (string) $selectedPackageId === (string) $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Package Name</label>
                    <input type="text" id="packageName" name="package_name" value="{{ old('package_name', $order?->package_name ?? $lead?->package_interest ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-300">Items (add/remove/edit price)</p>
                <button type="button" id="addItemBtn" class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm">+ Add Item</button>
            </div>

            <div id="itemsContainer" class="space-y-2">
                @foreach($initialItems as $index => $item)
                    <div class="order-item-row grid grid-cols-12 gap-2 p-2 rounded-lg bg-white/5 border border-white/10" data-index="{{ $index }}">
                        <input type="hidden" name="items[{{ $index }}][package_content_id]" value="{{ $item['package_content_id'] ?? '' }}" class="item-content-id col-span-1">
                        <input type="text" name="items[{{ $index }}][content_name]" value="{{ $item['content_name'] ?? '' }}" required placeholder="Item" class="col-span-5 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
                        <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" min="0.01" step="0.01" required class="item-qty col-span-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
                        <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" min="0" step="0.01" required class="item-price col-span-3 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
                        <input type="hidden" name="items[{{ $index }}][is_mandatory]" value="{{ $item['is_mandatory'] ?? 0 }}">
                        <input type="hidden" name="items[{{ $index }}][is_editable]" value="{{ $item['is_editable'] ?? 1 }}">
                        <input type="hidden" name="items[{{ $index }}][sort_order]" value="{{ $index }}" class="sort-order-input">
                        <button type="button" class="remove-item-btn col-span-1 text-red-300">✕</button>
                    </div>
                @endforeach
            </div>

            <div id="emptyItemState" class="mt-2 text-sm text-white/60 {{ count($initialItems) > 0 ? 'hidden' : '' }}">No items yet.</div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Subtotal</label>
                    <input type="text" id="subtotalDisplay" readonly class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Discount</label>
                    <input type="number" id="discountInput" name="discount_amount" min="0" step="0.01" value="{{ old('discount_amount', $order?->discount_amount ?? 0) }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                @if(!$order)
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Advance</label>
                    <input type="number" name="advance_amount" min="0" step="0.01" value="{{ old('advance_amount', 0) }}" class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white">
                </div>
                @endif
                <div>
                    <label class="block text-sm text-gray-300 mb-2">Grand Total</label>
                    <input type="text" id="grandTotalDisplay" readonly class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex gap-3">
        <x-button type="submit" id="submitBtn"><span id="btnText">{{ $order ? 'Update Order' : 'Create Order' }}</span></x-button>
        <a href="{{ route('orders.index') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold transition">Cancel</a>
    </div>
</form>

<script>
    const customers = @json($customersPayload);
    const packages = @json($packagesPayload);
    const customerMap = new Map(customers.map(c => [String(c.id), c]));
    const packageMap = new Map(packages.map(p => [String(p.id), p]));

    const customerSelect = document.getElementById('customerSelect');
    const customerName = document.getElementById('customerName');
    const customerPhone = document.getElementById('customerPhone');
    const customerEmail = document.getElementById('customerEmail');
    const customerAddress = document.getElementById('customerAddress');

    const packageSelect = document.getElementById('packageSelect');
    const packageName = document.getElementById('packageName');
    const itemsContainer = document.getElementById('itemsContainer');
    const emptyItemState = document.getElementById('emptyItemState');
    const discountInput = document.getElementById('discountInput');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    const addItemBtn = document.getElementById('addItemBtn');

    let nextIndex = (() => {
        const rows = Array.from(document.querySelectorAll('.order-item-row'));
        if (!rows.length) return 0;
        return Math.max(...rows.map(row => Number(row.dataset.index || 0))) + 1;
    })();

    function syncSortOrder() {
        document.querySelectorAll('.order-item-row').forEach((row, index) => {
            const sortInput = row.querySelector('.sort-order-input');
            if (sortInput) sortInput.value = index;
        });
    }

    function toggleEmptyState() {
        emptyItemState.classList.toggle('hidden', document.querySelectorAll('.order-item-row').length > 0);
    }

    function recalcTotals() {
        let subtotal = 0;
        document.querySelectorAll('.order-item-row').forEach((row) => {
            const qty = Number(row.querySelector('.item-qty')?.value || 0);
            const price = Number(row.querySelector('.item-price')?.value || 0);
            subtotal += qty * price;
        });

        const discount = Number(discountInput?.value || 0);
        const grand = Math.max(0, subtotal - discount);
        subtotalDisplay.value = subtotal.toFixed(2);
        grandTotalDisplay.value = grand.toFixed(2);
    }

    function addItemRow(data = {}) {
        const index = nextIndex++;
        const row = document.createElement('div');
        row.className = 'order-item-row grid grid-cols-12 gap-2 p-2 rounded-lg bg-white/5 border border-white/10';
        row.dataset.index = String(index);

        row.innerHTML = `
            <input type="hidden" name="items[${index}][package_content_id]" value="${data.package_content_id || ''}" class="item-content-id col-span-1">
            <input type="text" name="items[${index}][content_name]" value="${data.content_name || ''}" required placeholder="Item" class="col-span-5 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
            <input type="number" name="items[${index}][qty]" value="${data.qty || '1.00'}" min="0.01" step="0.01" required class="item-qty col-span-2 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
            <input type="number" name="items[${index}][unit_price]" value="${data.unit_price || '0.00'}" min="0" step="0.01" required class="item-price col-span-3 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white">
            <input type="hidden" name="items[${index}][is_mandatory]" value="${data.is_mandatory ? 1 : 0}">
            <input type="hidden" name="items[${index}][is_editable]" value="${data.is_editable === false ? 0 : 1}">
            <input type="hidden" name="items[${index}][sort_order]" value="${index}" class="sort-order-input">
            <button type="button" class="remove-item-btn col-span-1 text-red-300">✕</button>
        `;

        itemsContainer.appendChild(row);
        syncSortOrder();
        toggleEmptyState();
        recalcTotals();
    }

    function loadPackageItems(packageId) {
        const selected = packageMap.get(String(packageId));
        if (!selected) return;

        itemsContainer.innerHTML = '';
        selected.items.forEach(item => {
            addItemRow({
                package_content_id: item.package_content_id,
                content_name: item.content_name,
                qty: item.qty,
                unit_price: item.unit_price,
                is_mandatory: item.is_mandatory,
                is_editable: item.is_editable,
            });
        });

        packageName.value = selected.name;
    }
    customerSelect?.addEventListener('change', function () {
        const customer = customerMap.get(String(this.value));
        if (!customer) return;
        customerName.value = customer.full_name || '';
        customerPhone.value = customer.phone || '';
        customerEmail.value = customer.email || '';
        customerAddress.value = customer.address || '';
    });

    packageSelect?.addEventListener('change', function () {
        if (!this.value) return;
        if (document.querySelectorAll('.order-item-row').length > 0 && !confirm('Replace current items with selected package items?')) {
            return;
        }
        loadPackageItems(this.value);
    });

    addItemBtn?.addEventListener('click', () => addItemRow());

    itemsContainer.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.remove-item-btn');
        if (!removeBtn) return;
        removeBtn.closest('.order-item-row')?.remove();
        syncSortOrder();
        toggleEmptyState();
        recalcTotals();
    });

    itemsContainer.addEventListener('input', (event) => {
        if (event.target.closest('.item-qty') || event.target.closest('.item-price')) {
            recalcTotals();
        }
    });

    discountInput?.addEventListener('input', recalcTotals);

    syncSortOrder();
    toggleEmptyState();
    recalcTotals();

    document.getElementById('orderForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        submitBtn.disabled = true;
        if (btnText) btnText.textContent = 'Processing...';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: new FormData(this),
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = data.redirect, 800);
                return;
            }

            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            showToast(firstError || data.message || 'Failed to save order.', 'error');
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            if (btnText) btnText.textContent = '{{ $order ? 'Update Order' : 'Create Order' }}';
        }
    });
</script>
