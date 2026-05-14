<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\Order\CheckoutService;
use App\Http\Requests\StoreCheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * Show the checkout form.
     */
    public function index()
    {
        try {
            $summary = $this->cartService->getCartSummary();

            if ($summary['items']->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            return view('checkout.index', [
                'cartItems'    => $summary['items'],
                'grandTotal'   => $summary['grandTotal'],
                'totalSavings' => $summary['totalSavings'],
            ]);
        } catch (\Exception $e) {
            Log::error('CheckoutController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Store a new order.
     */
    public function store(StoreCheckoutRequest $request)
    {
        $summary = $this->cartService->getCartSummary();
        $cartItems = $summary['items'];

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cannot place an order with an empty cart.');
        }

        try {
            $order = $this->checkoutService->placeOrder($request->validated(), auth()->user());

            return redirect()->route('checkout.success', $order->id)->with('success', 'Your order has been placed successfully! 🎉');
        } catch (\Exception $e) {
            Log::error('Order placement failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Something went wrong while placing your order. Please try again.');
        }
    }

    /**
     * Show order success page.
     */
    public function success(Order $order)
    {
        try {
            // Ensure the order belongs to the authenticated user
            if ($order->user_id !== auth()->id()) {
                abort(403);
            }

            return view('checkout.success', compact('order'));
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }
            Log::error('CheckoutController@success error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
