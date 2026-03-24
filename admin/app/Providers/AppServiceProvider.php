<?php

namespace App\Providers;

use App\Services\greetingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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

        Blade::directive('currency', function ($expression) {
            return "<?php echo '₹' . number_format($expression, 2); ?>";
        });
    }
}
