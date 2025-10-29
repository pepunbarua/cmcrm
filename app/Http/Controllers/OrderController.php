<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['lead'])
            ->when($request->search, function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('lead', function($query) use ($request) {
                      $query->where('client_name', 'like', '%' . $request->search . '%');
                  });
            })
            ->when($request->payment_status, function($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->latest();

        $orders = $query->paginate(15);
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create(Request $request)
    {
        $lead = null;
        if ($request->lead_id) {
            $lead = Lead::with('vendor')->findOrFail($request->lead_id);
            
            // Check if lead already has an order
            if ($lead->order) {
                return redirect()->route('orders.show', $lead->order)
                    ->with('error', 'This lead has already been converted to an order');
            }
        }
        
        return view('orders.create', compact('lead'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'package_name' => 'required|string|max:255',
            'package_details' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);

        DB::beginTransaction();
        try {
            // Generate order number
            $year = date('Y');
            $lastOrder = Order::whereYear('created_at', $year)->latest()->first();
            $nextNumber = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
            $validated['order_number'] = 'ORD-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $order = Order::create($validated);

            // Update lead status to converted
            $lead = Lead::findOrFail($validated['lead_id']);
            $lead->update(['status' => 'converted']);

            // Record advance payment if provided
            if (!empty($validated['advance_amount']) && $validated['advance_amount'] > 0) {
                $order->payments()->create([
                    'amount' => $validated['advance_amount'],
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                    'status' => 'completed',
                    'notes' => 'Advance payment'
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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['lead.vendor', 'event', 'payments']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'package_details' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);

        $order->update($validated);
        
        activity()
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->log('Order updated');

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully!',
            'redirect' => route('orders.show', $order)
        ]);
    }

    /**
     * Remove the specified order
     */
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
}
