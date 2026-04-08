<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with search and filters.
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // 1. Search by Order ID or Customer Name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(12)->withQueryString();
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details and its timeline.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'statusHistories.user']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status and internal notes.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'     => 'required|in:pending,processing,shipped,delivered,cancelled',
            'admin_note' => 'nullable|string',
            'history_note' => 'nullable|string|max:255',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus, $request) {
                
                // 1. If status changed, log the history and handle stock if cancelled
                if ($oldStatus !== $newStatus) {
                    
                    // Create History Record
                    OrderStatusHistory::create([
                        'order_id'   => $order->id,
                        'status'     => $newStatus,
                        'changed_by' => auth()->id(),
                        'notes'      => $request->history_note ?? "Status changed from {$oldStatus} to {$newStatus}",
                    ]);

                    // Restore Stock if specifically being cancelled now (and wasn't cancelled before)
                    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                        foreach ($order->items as $item) {
                            if ($item->product) {
                                $item->product->increment('stock', $item->quantity);
                            }
                        }
                    }
                    
                    // (Future) Reduce Stock if moving away from cancelled? 
                    // Usually, cancelled is a terminal state, but for simplicity we only handle restoration.
                }

                // 2. Update the Order
                $order->update([
                    'status'     => $newStatus,
                    'admin_note' => $request->admin_note,
                ]);
            });

            Log::info('Order updated by admin', ['order_id' => $order->id, 'admin_id' => auth()->id(), 'new_status' => $newStatus]);
            
            if ($oldStatus !== $newStatus) {
                event(new OrderStatusUpdated($newStatus, (string)$order->id));
            }

            return redirect()->route('admin.orders.show', $order->id)
                             ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            Log::error('Admin order update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating the order.');
        }
    }
}
