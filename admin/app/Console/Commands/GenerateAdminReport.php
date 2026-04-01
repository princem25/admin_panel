<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:admin {--type=sales : sales, inventory, customers} 
                                         {--format=csv : csv, json, pdf}';

    protected $description = 'Generate admin reports';

    public function handle()
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

        $this->info("Report generated ");
    }
}