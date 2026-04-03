<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate and directly download an invoice PDF from a specific order.
     */
    public function generate(Order $order)
    {
        // Authorization check
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // Load items and products
        $order->load('items.product');

        if ($order->items->isEmpty()) {
            return back()->with('error', 'Cannot generate invoice for an empty order.');
        }

        // 3. Prevent invoice generation for cancelled orders
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Invoices cannot be generated for cancelled orders.');
        }

        $data = [
            'order'         => $order,
            'items'         => $order->items,
            'grandTotal'    => $order->total_amount,
            'paymentMethod' => $order->payment_method,
            'user'          => $order->user ?? Auth::user(),
            'invoiceNumber' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'generatedAt'   => now()->format('d M Y'),
        ];

        $pdf = Pdf::loadView('invoice.pdf', $data)
                  ->setPaper('A4', 'portrait');

        return $pdf->download('invoice_order_' . $order->id . '_' . now()->format('Ymd_His') . '.pdf');
    }
}

