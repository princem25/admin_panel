<?php

namespace App\Providers;

use App\Models\Cart;
use App\Services\greetingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('greeting', function () {
            return new greetingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('company_name', 'Intern Training App');
        View::composer('*', function ($view) {
            $view->with('current_logged_user', Auth::user());
        });

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $count = Cart::where('user_id', auth()->id())->sum('quantity');
            } else {
                $count = 0;
            }

            $view->with('cartCount', $count);
        });

        View::composer('*', function ($view) {

            $categorySummary = DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(products.id) as total'))
                ->groupBy('categories.name')
                ->get();

            $view->with('categorySummary', $categorySummary);
        });

        Blade::directive('currency', function ($expression) {
            return "<?php echo '₹' . number_format($expression, 2); ?>";
        });
    }
}
