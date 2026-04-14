<?php

use App\Services\AdminService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('report:sales', function (AdminService $report) {

    $data = $report->revenueByPaymentMethod();
    $total = $report->sumTotalRevenue();

    $filePath = storage_path('app/reports');

    if (!file_exists($filePath)) {
        mkdir($filePath, 0777, true);
    }

    $file = $filePath . '/sales.csv';

    $handle = fopen($file, 'a');

    if (!$handle) {
        $this->error("Cannot open file: {$file}");
        return;
    }

    // Add Date Section Header
    fputcsv($handle, ['Date: ' . now()->format('Y-m-d')]);

    // Column headers
    fputcsv($handle, ['payment_method', 'Total']);

    foreach ($data as $row) {
        fputcsv($handle, [
            $row['payment_method'],
            $row['total']
        ]);
    }

    fputcsv($handle, ['Total Revenue', $total]);

    // ✅ Empty line for spacing between days
    fputcsv($handle, []);


    fclose($handle);

    $this->info("Report updated: {$file}");
});
        
Artisan::command('report:stock', function (AdminService $stockService) {

    $stock = $stockService->stockCheck();

    $filePath = storage_path('app/reports');

    if (!file_exists($filePath)) {
        mkdir($filePath, 0777, true);
    }

    $file = $filePath . '/stock.csv';

    $handle = fopen($file, 'a');

    if (!$handle) {
        $this->error("Cannot open file: {$file}");
        return;
    }

    fputcsv($handle, ['Date: ' . now()->format('Y-m-d')]);
    fputcsv($handle, ['Name', 'Category', 'Stock']);

    foreach ($stock as $row) {
        fputcsv($handle, [
            $row->name,
            $row->category->name ?? 'N/A',
            $row->stock
        ]);
    }

    fputcsv($handle, []);

    fclose($handle);

    $this->info("Stock report updated: {$file}");
});

app(Schedule::class)
    ->command('report:admin')
    ->everyFourHours();

app(Schedule::class)
    ->command('report:sales')
    ->dailyAt('2:00');

app(Schedule::class)
    ->command('report:stock')
    ->dailyAt('8:00');
