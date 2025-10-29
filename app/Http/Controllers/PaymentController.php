<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with('order.lead');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'due') {
                $query->where('status', 'pending')
                      ->where('due_date', '>=', Carbon::now());
            } elseif ($request->status == 'overdue') {
                $query->where('status', 'pending')
                      ->where('due_date', '<', Carbon::now());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Search by order number or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('lead', function($q2) use ($search) {
                      $q2->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'overdue_count' => Payment::where('status', 'pending')
                                     ->where('due_date', '<', Carbon::now())
                                     ->count(),
            'completed_count' => Payment::where('status', 'completed')->count(),
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,bkash,nagad,card',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'completed';

        $payment = Payment::create($validated);

        // Update order payment status
        $order = Order::findOrFail($validated['order_id']);
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $order->total_amount) {
            $order->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $order->update(['payment_status' => 'partial']);
        }

        activity()
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->log('Payment recorded');

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully!',
        ]);
    }

    /**
     * Delete a payment
     */
    public function destroy(Payment $payment)
    {
        $order = $payment->order;
        
        activity()
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->log('Payment deleted');
            
        $payment->delete();

        // Recalculate order payment status
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $order->total_amount) {
            $order->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $order->update(['payment_status' => 'partial']);
        } else {
            $order->update(['payment_status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully!'
        ]);
    }
}
