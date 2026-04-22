<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $invoice = $order->invoice;

        if (!$invoice || !Storage::disk('public')->exists($invoice->file_path)) {
            return back()->with('info', 'Invoice is generating. Please wait.');
        }

        return Storage::disk('public')->download($invoice->file_path, 'Invoice_' . $invoice->invoice_number . '.pdf');
    }
}

