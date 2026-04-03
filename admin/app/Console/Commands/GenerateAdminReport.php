<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:admin {--type=sales : sales, inventory, customers} 
                                         {--format=csv : csv, json, pdf}';

    protected $description = 'Generate admin reports';

    public function handle(ProductService $service)
    {
        $type = $this->option('type');
        $format = $this->option('format');

        $this->info("Generating {$type} report in {$format} format...");

        // Progress bar
        $bar = $this->output->createProgressBar(100);
        $bar->start();

        for ($i = 0; $i < 100; $i++) {
            usleep(20000);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $csv = $service->getCsvString();

        $fileName = 'reports/products_' . now()->format('Y_m_d_H_i_s') . '.csv';

        Storage::put($fileName, $csv);

        $this->info("Saved: storage/app/{$fileName}");
        $this->info("Report generated ");
    }
}
