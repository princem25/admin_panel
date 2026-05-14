<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Generate and directly download an invoice PDF from a specific order.
     */
    public function generate(Request $request, Order $order)
    {
        try {
            // Validate signed URL signature
            if (!$request->hasValidSignature()) {
                abort(403, 'Invalid or expired download link.');
            }

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

            $invoice = $this->invoiceService->getInvoiceForOrder($order);

            if (!$invoice) {
                return back()->with('info', 'Invoice is regenerating in the background. Please wait a moment and refresh.');
            }

            return response()->streamDownload(function () use ($invoice) {
                echo Storage::disk('public')->get($invoice->file_path);
            }, 'Invoice_' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }
            Log::error('InvoiceController@generate error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

