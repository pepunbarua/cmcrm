<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['order.lead', 'order.customer']);

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('order', function ($orderQuery) use ($status) {
                if (in_array($status, ['pending', 'partial', 'paid'], true)) {
                    $orderQuery->where('payment_status', $status);
                    return;
                }

                if ($status === 'due') {
                    $orderQuery->whereIn('payment_status', ['pending', 'partial']);
                    return;
                }

                if ($status === 'completed') {
                    $orderQuery->where('payment_status', 'paid');
                    return;
                }

                if ($status === 'overdue') {
                    // Overdue cannot be derived without due date; keep empty result for compatibility.
                    $orderQuery->whereRaw('1 = 0');
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhereHas('lead', function($q2) use ($search) {
                        $q2->where('client_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        $pendingAmount = (float) Order::whereIn('payment_status', ['pending', 'partial'])->sum('balance_due');
        $stats = [
            'total_amount' => (float) Payment::sum('amount'),
            'pending_amount' => $pendingAmount,
            'overdue_count' => 0,
            'completed_count' => (int) Payment::count(),
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
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_banking,cheque,bkash,nagad',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (in_array($validated['payment_method'], ['bkash', 'nagad'], true)) {
            $provider = ucfirst($validated['payment_method']);
            $validated['notes'] = trim(($validated['notes'] ?? '') . ' ' . "(Provider: {$provider})");
            $validated['payment_method'] = 'mobile_banking';
        }

        $validated['payment_type'] = 'partial';
        $validated['received_by'] = Auth::id();

        $payment = Payment::create($validated);

        $order = Order::findOrFail($validated['order_id']);
        $totalPaid = (float) $order->payments()->sum('amount');

        if ($totalPaid >= $order->total_amount) {
            $order->update([
                'payment_status' => 'paid',
                'balance_due' => 0,
                'advance_paid' => $totalPaid,
            ]);
        } elseif ($totalPaid > 0) {
            $order->update([
                'payment_status' => 'partial',
                'balance_due' => max(0, (float) $order->total_amount - $totalPaid),
                'advance_paid' => $totalPaid,
            ]);
        } else {
            $order->update([
                'payment_status' => 'pending',
                'balance_due' => (float) $order->total_amount,
                'advance_paid' => 0,
            ]);
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

        $totalPaid = (float) $order->payments()->sum('amount');

        if ($totalPaid >= $order->total_amount) {
            $order->update([
                'payment_status' => 'paid',
                'balance_due' => 0,
                'advance_paid' => $totalPaid,
            ]);
        } elseif ($totalPaid > 0) {
            $order->update([
                'payment_status' => 'partial',
                'balance_due' => max(0, (float) $order->total_amount - $totalPaid),
                'advance_paid' => $totalPaid,
            ]);
        } else {
            $order->update([
                'payment_status' => 'pending',
                'balance_due' => (float) $order->total_amount,
                'advance_paid' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully!'
        ]);
    }
}
