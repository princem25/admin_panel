<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index()
    {
        try {
            $invoices = Invoice::with(['order', 'user'])->latest()->paginate(15);
            
            // Enhance invoices with file size and modified date
            foreach ($invoices as $invoice) {
                if (Storage::disk('public')->exists($invoice->file_path)) {
                    $invoice->file_size = Storage::disk('public')->size($invoice->file_path);
                    $invoice->last_modified = Storage::disk('public')->lastModified($invoice->file_path);
                } else {
                    $invoice->file_size = 0;
                    $invoice->last_modified = null;
                }
                $invoice->downloadUrl = URL::temporarySignedRoute(
                    'invoices.download', now()->addMinutes(10), ['order' => $invoice->order_id]
                );
            }

            return view('admin.invoices.index', compact('invoices'));
        } catch (\Exception $e) {
            Log::error('Admin\InvoiceController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
