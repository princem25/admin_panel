<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate and directly download an invoice PDF from the current user's cart.
     */
    public function generate(CartService $cartService)
    {
        $summary = $cartService->getCartSummary();

        $cartItems = $summary['items'];
        $grandTotal = $summary['grandTotal'];

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cannot generate invoice for an empty cart.');
        }

        $data = [
            'cartItems'     => $cartItems,
            'grandTotal'    => $grandTotal,
            'user'          => Auth::user(),
            'invoiceNumber' => 'INV-' . strtoupper(uniqid()),
            'generatedAt'   => now()->format('d M Y'),
        ];

        $pdf = Pdf::loadView('invoice.pdf', $data)
                  ->setPaper('A4', 'portrait');

        return $pdf->download('invoice_' . now()->format('Ymd_His') . '.pdf');
    }
}

