<?php

namespace App\Providers;
use App\Models\Category;
use App\Services\greetingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\SyncCartOnLogin;
use App\Listeners\SyncCartOnLogout;
use Illuminate\Support\Facades\Cache;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('greeting', function () {
            return new greetingService;
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

        View::composer('layouts.navigation', function ($view) {
            $cart = session()->get('cart', []);
            $count = collect($cart)->count('product_id');

            $view->with('cartCount', $count);
        });

        Event::listen(Login::class, SyncCartOnLogin::class);
        Event::listen(Logout::class, SyncCartOnLogout::class);

        View::share('categories', Cache::tags(['products', 'admin'])->remember('categories', 7200, function () {
            return Category::all();
        }));

        View::share('categorySummary', Cache::tags(['products', 'admin'])->remember('category_summary', 7200, function () {
            return DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(products.id) as total'))
                ->groupBy('categories.name')
                ->get();
        }));    

        Blade::directive('currency', function ($expression) {
            return "<?php echo '₹' . number_format($expression, 2); ?>";
        });

        if ($this->app->environment('local')) {
            DB::listen(function ($query) {
                Log::channel('DBinteraction')->debug($query->sql, [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time, 
                ]);
            });
        }
    }
}
