<?php

namespace App\Http\Controllers\Admin;

use App\Events\Admin\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with search and filters.
     */
    public function index(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Admin\OrderController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Display the specified order details and its timeline.
     */
    public function show(Order $order)
    {
        try {
            $order->load(['items.product', 'user', 'statusHistories.user']);
            
            $downloadUrl = URL::temporarySignedRoute(
                'invoices.download', now()->addMinutes(10), ['order' => $order->id]
            );

            return view('admin.orders.show', compact('order', 'downloadUrl'));
        } catch (\Exception $e) {
            Log::error('Admin\OrderController@show error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update the order status and internal notes.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        // 🚀 terminal state: cancelled orders cannot be modified
        if ($order->status === 'cancelled') {
            return back()->with('error', 'This order is cancelled and cannot be modified further.');
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $currentIndex = array_search($oldStatus, $statuses);
        $newIndex = array_search($newStatus, $statuses);

        $allowed = false;
        if ($newStatus === $oldStatus) {
            $allowed = true;
        } elseif ($currentIndex !== false && $newIndex !== false) {
            $allowed = abs($currentIndex - $newIndex) <= 1;
        } elseif ($newStatus === 'cancelled' && $oldStatus !== 'delivered') {
            $allowed = true;
        }

        if (!$allowed) {
            return back()->with('error', 'Invalid status transition. You can only move to the adjacent status or cancel the order.');
        }

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
                    'status'          => $newStatus,
                    'tracking_number' => $request->tracking_number ?? $order->tracking_number,
                    'admin_note'      => $request->admin_note,
                ]);

                // 3. Dispatch Notification if Shipped with Rate Limiting
                if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
                    $user = $order->user;
                    $executed = RateLimiter::attempt(
                        'notifications:' . $user->id,
                        5,
                        function () use ($user, $order) {
                            $user->notify(new OrderShipped($order));
                        },
                        60
                    );

                    if (!$executed) {
                        Log::warning('Notification rate limit exceeded', [
                            'user_id' => $user->id,
                            'notification' => 'OrderShipped',
                        ]);
                    }
                }
            });

            Log::info('Order updated by admin', ['order_id' => $order->id, 'admin_id' => auth()->id(), 'new_status' => $newStatus]);
            
            // NOTE: OrderStatusUpdated is dispatched automatically via OrderObserver when $order->update() is called above.


            return redirect()->route('admin.orders.show', $order->id)
                             ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            Log::error('Admin order update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating the order.');
        }
    }
}
