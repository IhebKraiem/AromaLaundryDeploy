<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Load web routes (for browser-based requests)
            Route::middleware('web')
                 ->group(base_path('routes/web.php'));

            // Load API routes (for API-based requests)
            Route::prefix('api')
                 ->middleware('api')
                 ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });

        RateLimiter::for('password_email', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email'));
        });

        RateLimiter::for('password_reset', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email'));
        });
    }
}