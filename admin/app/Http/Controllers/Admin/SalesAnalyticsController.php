<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesAnalyticsService;
use Illuminate\Support\Facades\Log;

class SalesAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(SalesAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        try {
            $monthlySales = $this->analyticsService->getMonthlySales();
            $topProducts = $this->analyticsService->getTopProducts();
            $topCustomers = $this->analyticsService->getTopCustomers();
            $salesByCategory = $this->analyticsService->getSalesByCategory();

            return view('admin.analytics.index', compact(
                'monthlySales',
                'topProducts',
                'topCustomers',
                'salesByCategory'
            ));
        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Could not load analytics data.');
        }
    }

    /**
     * Export reports to CSV
     */
    public function export(string $type)
    {
        try {
            $data = collect();
            $filename = "sales_report_{$type}_" . date('Y-m-d') . ".csv";
            $headers = [];

            switch ($type) {
                case 'monthly-sales':
                    $data = $this->analyticsService->getMonthlySales();
                    $headers = ['Month', 'Total Revenue', 'Average Order Value', 'Total Orders'];
                    break;

                case 'top-products':
                    $data = $this->analyticsService->getTopProducts();
                    $headers = ['Product ID', 'Product Name', 'Total Quantity Sold'];
                    break;

                case 'top-customers':
                    $data = $this->analyticsService->getTopCustomers();
                    $headers = ['Customer ID', 'Customer Name', 'Total Spent', 'Total Orders'];
                    break;

                case 'sales-by-category':
                    $data = $this->analyticsService->getSalesByCategory();
                    $headers = ['Category', 'Total Revenue', 'Total Items Sold'];
                    break;

                default:
                    return back()->with('error', 'Invalid export type.');
            }

            return response()->streamDownload(function () use ($data, $headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);

                foreach ($data as $row) {
                    fputcsv($file, array_values((array)$row));
                }

                fclose($file);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);

        } catch (\Exception $e) {
            Log::error('Sales analytics error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Export failed.');
        }
    }
}
