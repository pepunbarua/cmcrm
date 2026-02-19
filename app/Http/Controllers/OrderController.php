<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Lead;
use App\Models\Package;
use App\Models\PackageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['lead', 'customer', 'primaryOrderPackage.contents', 'payments'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->search);
                $q->where(function ($inner) use ($search) {
                    $inner->where('order_number', 'like', '%' . $search . '%')
                        ->orWhere('client_name', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('full_name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('lead', function ($leadQuery) use ($search) {
                            $leadQuery->where('client_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->latest();

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $lead = null;
        if ($request->filled('lead_id')) {
            $lead = Lead::with('vendor')->findOrFail($request->lead_id);

            if ($lead->order) {
                return redirect()->route('orders.show', $lead->order)
                    ->with('error', 'This lead already has an order.');
            }
        }

        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone', 'email', 'address']);

        $packages = Package::query()
            ->where('is_active', true)
            ->with(['items.packageContent'])
            ->orderBy('name')
            ->get();

        $packageContents = PackageContent::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'base_price']);

        return view('orders.create', compact('lead', 'customers', 'packages', 'packageContents'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateOrderRequest($request);
            $customer = $this->resolveCustomer($validated);
            $selectedPackage = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;

            [$lineItems, $subtotal] = $this->buildLineItems($validated['items']);
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $advance = min((float) ($validated['advance_amount'] ?? 0), $grandTotal);
            $balance = max(0, $grandTotal - $advance);
            $paymentStatus = $this->resolvePaymentStatus($grandTotal, $balance);

            $order = Order::create([
                'lead_id' => $validated['lead_id'] ?? null,
                'customer_id' => $customer->id,
                'package_id' => $selectedPackage?->id,
                'order_number' => $this->generateOrderNumber(),
                'client_name' => $customer->full_name,
                'client_phone' => $customer->phone,
                'client_email' => $customer->email,
                'event_type' => $validated['event_type'],
                'event_date' => $validated['event_date'],
                'event_end_date' => $validated['event_end_date'] ?? null,
                'time_duration' => $validated['time_duration'],
                'event_venue_name' => $validated['location'],
                'location' => $validated['location'],
                'event_address' => $validated['event_address'] ?? null,
                'bride_name' => $validated['bride_name'] ?? null,
                'groom_name' => $validated['groom_name'] ?? null,
                'requirements' => $validated['requirements'] ?? null,
                'photographer_count' => $validated['photographer_count'],
                'videographer_count' => $validated['videographer_count'],
                'outdoor_shoot' => (bool) $validated['outdoor_shoot'],
                'package_type' => 'custom',
                'package_name' => $selectedPackage?->name ?? ($validated['package_name'] ?? 'Custom Package'),
                'package_details' => $validated['package_details'] ?? null,
                'services_included' => collect($lineItems)->pluck('content_name_snapshot')->values()->all(),
                'total_amount' => $grandTotal,
                'discount_amount' => $discount,
                'advance_paid' => $advance,
                'balance_due' => $balance,
                'payment_status' => $paymentStatus,
                'order_status' => 'confirmed',
                'special_requests' => $validated['requirements'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->syncOrderPackage($order, $selectedPackage, $lineItems, $subtotal, $discount, $grandTotal);

            if (!empty($validated['lead_id'])) {
                $lead = Lead::find($validated['lead_id']);
                if ($lead) {
                    $lead->update(['status' => 'converted']);
                }
            }

            if ($advance > 0) {
                $order->payments()->create([
                    'payment_date' => now()->toDateString(),
                    'amount' => $advance,
                    'payment_method' => 'cash',
                    'payment_type' => 'advance',
                    'notes' => 'Advance payment at order creation',
                    'received_by' => Auth::id(),
                ]);
            }

            activity()
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->log('Order created');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'redirect' => route('orders.show', $order)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->load([
            'lead.vendor',
            'customer',
            'event',
            'payments',
            'primaryOrderPackage.contents.packageContent',
        ]);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'primaryOrderPackage.contents']);

        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone', 'email', 'address']);

        $packages = Package::query()
            ->where('is_active', true)
            ->with(['items.packageContent'])
            ->orderBy('name')
            ->get();

        $packageContents = PackageContent::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'base_price']);

        return view('orders.edit', compact('order', 'customers', 'packages', 'packageContents'));
    }

    public function update(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateOrderRequest($request, true);
            $customer = $this->resolveCustomer($validated, $order);
            $selectedPackage = !empty($validated['package_id']) ? Package::find($validated['package_id']) : null;

            [$lineItems, $subtotal] = $this->buildLineItems($validated['items']);
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount);

            $paidAmount = (float) $order->payments()->sum('amount');
            $balance = max(0, $grandTotal - $paidAmount);
            $paymentStatus = $this->resolvePaymentStatus($grandTotal, $balance);

            $order->update([
                'lead_id' => $validated['lead_id'] ?? null,
                'customer_id' => $customer->id,
                'package_id' => $selectedPackage?->id,
                'client_name' => $customer->full_name,
                'client_phone' => $customer->phone,
                'client_email' => $customer->email,
                'event_type' => $validated['event_type'],
                'event_date' => $validated['event_date'],
                'event_end_date' => $validated['event_end_date'] ?? null,
                'time_duration' => $validated['time_duration'],
                'event_venue_name' => $validated['location'],
                'location' => $validated['location'],
                'event_address' => $validated['event_address'] ?? null,
                'bride_name' => $validated['bride_name'] ?? null,
                'groom_name' => $validated['groom_name'] ?? null,
                'requirements' => $validated['requirements'] ?? null,
                'photographer_count' => $validated['photographer_count'],
                'videographer_count' => $validated['videographer_count'],
                'outdoor_shoot' => (bool) $validated['outdoor_shoot'],
                'package_type' => 'custom',
                'package_name' => $selectedPackage?->name ?? ($validated['package_name'] ?? 'Custom Package'),
                'package_details' => $validated['package_details'] ?? null,
                'services_included' => collect($lineItems)->pluck('content_name_snapshot')->values()->all(),
                'total_amount' => $grandTotal,
                'discount_amount' => $discount,
                'advance_paid' => $paidAmount,
                'balance_due' => $balance,
                'payment_status' => $paymentStatus,
                'special_requests' => $validated['requirements'] ?? null,
            ]);

            $this->syncOrderPackage($order, $selectedPackage, $lineItems, $subtotal, $discount, $grandTotal);

            if (!empty($validated['lead_id'])) {
                $lead = Lead::find($validated['lead_id']);
                if ($lead) {
                    $lead->update(['status' => 'converted']);
                }
            }

            activity()
                ->performedOn($order)
                ->causedBy(Auth::user())
                ->log('Order updated');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully!',
                'redirect' => route('orders.show', $order)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Order $order)
    {
        activity()
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->log('Order deleted');
            
        $order->delete();

        return response()->json([
            'success' => true,
                'message' => 'Order deleted successfully!'
        ]);
    }

    protected function validateOrderRequest(Request $request, bool $isUpdate = false): array
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'nullable|exists:leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'event_type' => 'required|in:wedding,birthday,corporate,anniversary,other',
            'event_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'time_duration' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'event_address' => 'nullable|string',
            'bride_name' => 'nullable|string|max:255',
            'groom_name' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'photographer_count' => 'required|integer|min:1|max:5',
            'videographer_count' => 'required|integer|min:1|max:5',
            'outdoor_shoot' => 'required|boolean',
            'package_id' => 'nullable|exists:packages,id',
            'package_name' => 'nullable|string|max:255',
            'package_details' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.package_content_id' => 'nullable|integer|exists:package_contents,id',
            'items.*.content_name' => 'nullable|string|max:255',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.is_mandatory' => 'nullable|boolean',
            'items.*.is_editable' => 'nullable|boolean',
            'items.*.sort_order' => 'nullable|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $customerId = $request->input('customer_id');
            if (empty($customerId)) {
                if (empty($request->input('customer_name'))) {
                    $validator->errors()->add('customer_name', 'Customer name is required when customer is not selected.');
                }
                if (empty($request->input('customer_phone'))) {
                    $validator->errors()->add('customer_phone', 'Customer phone is required when customer is not selected.');
                }
            }

            $items = $request->input('items', []);
            foreach ($items as $index => $item) {
                $hasContentId = !empty($item['package_content_id']);
                $hasCustomName = !empty(trim((string) ($item['content_name'] ?? '')));
                if (!$hasContentId && !$hasCustomName) {
                    $validator->errors()->add("items.{$index}.content_name", 'Select a content or provide a custom content name.');
                }
            }
        });

        return $validator->validate();
    }

    protected function resolveCustomer(array $validated, ?Order $order = null): Customer
    {
        if (!empty($validated['customer_id'])) {
            return Customer::findOrFail($validated['customer_id']);
        }

        $name = trim((string) $validated['customer_name']);
        $phone = trim((string) $validated['customer_phone']);
        $email = !empty($validated['customer_email']) ? trim((string) $validated['customer_email']) : null;
        $address = $validated['customer_address'] ?? null;

        $customer = Customer::query()
            ->when($phone !== '', fn ($query) => $query->where('phone', $phone))
            ->when($email, fn ($query) => $query->orWhere('email', $email))
            ->first();

        if ($customer) {
            $customer->update([
                'full_name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'is_active' => true,
            ]);

            return $customer;
        }

        return Customer::create([
            'full_name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'is_active' => true,
        ]);
    }

    protected function buildLineItems(array $rawItems): array
    {
        $lineItems = collect($rawItems)
            ->values()
            ->map(function (array $item, int $index) {
                $qty = (float) $item['qty'];
                $unitPrice = (float) $item['unit_price'];
                $lineTotal = round($qty * $unitPrice, 2);

                return [
                    'package_content_id' => !empty($item['package_content_id']) ? (int) $item['package_content_id'] : null,
                    'content_name_snapshot' => !empty($item['content_name']) ? trim((string) $item['content_name']) : 'Custom Item',
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'is_mandatory' => (bool) ($item['is_mandatory'] ?? false),
                    'is_editable' => (bool) ($item['is_editable'] ?? true),
                    'sort_order' => (int) ($item['sort_order'] ?? $index),
                ];
            })
            ->all();

        $subtotal = round(collect($lineItems)->sum('line_total'), 2);

        return [$lineItems, $subtotal];
    }

    protected function syncOrderPackage(
        Order $order,
        ?Package $selectedPackage,
        array $lineItems,
        float $subtotal,
        float $discount,
        float $grandTotal
    ): void {
        $order->orderPackages()->delete();

        $orderPackage = $order->orderPackages()->create([
            'package_id' => $selectedPackage?->id,
            'package_name_snapshot' => $selectedPackage?->name ?? ($order->package_name ?: 'Custom Package'),
            'pricing_mode' => $selectedPackage?->pricing_mode ?? 'custom',
            'base_price' => (float) ($selectedPackage?->base_price ?? 0),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'adjustment' => 0,
            'grand_total' => $grandTotal,
            'package_snapshot' => [
                'package_id' => $selectedPackage?->id,
                'package_name' => $selectedPackage?->name ?? $order->package_name,
                'pricing_mode' => $selectedPackage?->pricing_mode ?? 'custom',
                'base_price' => (float) ($selectedPackage?->base_price ?? 0),
                'items' => $lineItems,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'snapshot_at' => now()->toDateTimeString(),
            ],
        ]);

        $orderPackage->contents()->createMany($lineItems);
    }

    protected function resolvePaymentStatus(float $grandTotal, float $balance): string
    {
        if ($grandTotal <= 0) {
            return 'paid';
        }

        if ($balance <= 0) {
            return 'paid';
        }

        if ($balance < $grandTotal) {
            return 'partial';
        }

        return 'pending';
    }

    protected function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $lastOrder = Order::query()
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastOrder && preg_match('/(\d{4})$/', (string) $lastOrder->order_number, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'ORD-' . $year . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
