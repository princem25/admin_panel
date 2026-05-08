<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use App\Events\Admin\OrderPlaced;
use App\Listeners\Admin\GenerateInvoicePDF;

class InvoiceService
{
    /**
     * Get the invoice for an order or trigger regeneration if missing.
     *
     * @param \App\Models\Order $order
     * @return \App\Models\Invoice|null
     */
    public function getInvoiceForOrder(Order $order)
    {
        $invoice = $order->invoice;

        if (!$invoice || !Storage::disk('public')->exists($invoice->file_path)) {
            $this->regenerateInvoice($order);
            return null; // Indicates invoice is being regenerated
        }

        return $invoice;
    }

    /**
     * Trigger invoice regeneration in the background.
     *
     * @param \App\Models\Order $order
     */
    public function regenerateInvoice(Order $order)
    {
        dispatch(function () use ($order) {
            app(GenerateInvoicePDF::class)->handle(new OrderPlaced($order));
        });
    }
}
