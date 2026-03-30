<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate an invoice based on the current user's cart contents.
     */
    public function generate(CartService $cartService)
    {
        $summary = $cartService->getCartSummary();

        $cartItems = $summary['items'];
        $grandTotal = $summary['grandTotal'];

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cannot generate invoice for an empty cart.');
        }

        // Return a fresh invoice layout view
        return view('invoice.show', [
            'cartItems' => $cartItems,
            'grandTotal' => $grandTotal,
            'user' => Auth::user(),
            'invoiceNumber' => 'INV-' . strtoupper(uniqid())
        ]);
    }
}
