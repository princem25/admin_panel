<?php

namespace App\Listeners\Admin;

use App\Events\Admin\OrderPlaced;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;

class GenerateInvoicePDF implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // Load items and products
        $order->load(['items.product', 'user']);

        if ($order->items->isEmpty()) {
            Log::channel('orders')->warning("GenerateInvoicePDF skipped: Order #{$order->id} has no items.");
            return;
        }

        // Prevent invoice generation for cancelled orders
        if ($order->status === 'cancelled') {
            Log::channel('orders')->warning("GenerateInvoicePDF skipped: Order #{$order->id} is cancelled.");
            return;
        }

        $data = [
            'order'         => $order,
            'items'         => $order->items,
            'grandTotal'    => $order->total_amount,
            'paymentMethod' => $order->payment_method,
            'user'          => $order->user,
            'invoiceNumber' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'generatedAt'   => now()->format('d M Y'),
        ];
        
        $invoiceNumber = $data['invoiceNumber'];

        try {
            Log::channel('orders')->info("Background Listener: Starting PDF generation for Order #{$order->id}");

            // Simulate some delay to demonstrate background processing
            // sleep(2); 

            $pdf = Pdf::loadView('invoice.pdf', $data)
                      ->setPaper('A4', 'portrait');

            $fileName = 'invoices/invoice_order_' . $order->id . '.pdf';
            
            // Save to storage/app/public/invoices/
            Storage::disk('public')->put($fileName, $pdf->output());

            Invoice::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'invoice_number' => $invoiceNumber,
                    'file_path' => $fileName,
                ]
            );

            Log::channel('orders')->info("Background Listener: PDF generated successfully for Order #{$order->id} at {$fileName}");
        } catch (\Exception $e) {
            Log::channel('orders')->error("Background Listener: PDF generation failed for Order #{$order->id}: " . $e->getMessage());
            
            // Re-throw the exception so Laravel knows the job failed and can retry it
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('orders')->emergency("Background Listener CRITICAL: GenerateInvoicePDF has failed after all retry attempts.", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
