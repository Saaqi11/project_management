<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'common'])
                ->group(base_path('routes/web.php'));

            Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                ->group(base_path('routes/admin.php'));

            if (isAddonInstalled('PROTYSAAS') > 1) {
                Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                    ->group(base_path('routes/saas.php'));
            }

            if (isAddonInstalled('PROTYSMS') > 0) {
                Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                    ->group(base_path('routes/bulk-sms-mail.php'));
            }
            if (isAddonInstalled('PROTYAGREEMENT') > 0) {
                Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                    ->group(base_path('routes/agreement.php'));
            }

            Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                ->group(base_path('routes/owner.php'));

            Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                ->group(base_path('routes/tenant.php'));

            Route::middleware(['web', 'common', 'version.update', 'addon.update'])
                ->group(base_path('routes/maintainer.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
