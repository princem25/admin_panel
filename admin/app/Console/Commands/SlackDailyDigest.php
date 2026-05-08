<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DailyDigest;

class SlackDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:daily-digest {--preview}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily digest of store metrics to Slack';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Collecting metrics for yesterday...');

        // Fetch orders from yesterday as a collection
        $orders = Order::whereDate('created_at', now()->subDay())->get();
        
        // Metric 1: Total order count
        $totalOrders = $orders->count();
        
        // Metric 2: Total revenue
        // Using Collection method: sum()
        // We count all orders regardless of status as requested
        $totalRevenue = $orders->sum('total_amount');

        // Metric 3: New customers count
        $newCustomers = User::whereDate('created_at', now()->subDay())->get();
        $newCustomersCount = $newCustomers->count();
        
        // Using Collection methods: pluck() and join()
        // Listing new customer names for the digest
        $newCustomerNames = $newCustomers->pluck('name')->join(', ');
        if (empty($newCustomerNames)) {
            $newCustomerNames = 'None';
        }

        // Metric 4: Low-stock products
        // Assuming stock < 10 is low stock based on project conventions
        // Using Collection method: filter()
        $products = Product::all();
        $lowStockProducts = $products->filter(function ($product) {
            return $product->stock < 10;
        });
        $lowStockCount = $lowStockProducts->count();
        
        // Using Collection methods: map() and join()
        // Formatting the low stock list
        $lowStockList = $lowStockProducts->map(function ($product) {
            return "{$product->name} (Stock: {$product->stock})";
        })->join(', ');
        
        if (empty($lowStockList)) {
            $lowStockList = 'None';
        }

        // Metric 5: Failed jobs count
        $failedJobsCount = DB::table('failed_jobs')
            ->whereDate('failed_at', now()->subDay())
            ->count();

        $metrics = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'new_customers' => $newCustomersCount,
            'new_customer_names' => $newCustomerNames,
            'low_stock_products' => $lowStockCount,
            'low_stock_list' => $lowStockList,
            'failed_jobs' => $failedJobsCount,
        ];

        $this->info("Metrics collected:");
        $this->line("- Orders: {$totalOrders}");
        $this->line("- Revenue: {$totalRevenue}");
        $this->line("- New Customers: {$newCustomersCount}");
        $this->line("- Low Stock: {$lowStockCount}");
        $this->line("- Failed Jobs: {$failedJobsCount}");

        // Determine channel routing
        $preview = $this->option('preview');
        $webhookUrl = $preview 
            ? config('services.slack.testing_webhook') 
            : config('services.slack.orders_webhook');

        if (!$webhookUrl) {
            $this->error('Slack webhook URL not configured.');
            return 1;
        }

        $this->info('Sending notification to Slack...');

        /**
         * WHY ON-DEMAND NOTIFICATIONS ARE IDEAL HERE:
         * On-demand notifications (using Notification::route) are ideal because this digest is sent to a specific
         * channel rather than a specific user in the database. We don't need a User model instance to route the
         * notification, making it perfect for system alerts, logs, or team channel digests.
         *
         * DIFFERENCE BETWEEN SCHEDULING A COMMAND VS SCHEDULING A NOTIFICATION DIRECTLY:
         * 1. Scheduling a Command: Allows complex logic to be executed (like fetching data, processing, and then
         *    deciding whether to send a notification). It encapsulates the business logic of "gathering the digest".
         * 2. Scheduling a Notification Directly: Useful if the notification data is static or requires no processing.
         *    However, usually we need to gather data first, which makes a command the better container for this workflow.
         */
        Notification::route('slack', $webhookUrl)
            ->notify(new DailyDigest($metrics));

        $this->info('Daily digest sent successfully!');
        
        return 0;
    }
}
