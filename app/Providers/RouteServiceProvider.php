<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelController;
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

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

            Route::middleware('web')
                ->group(base_path('routes/web.php'));           
                
        });

        Route::match(['get', 'post'],'/paineis-usinagem', [PainelController::class, 'paineisUsinagem'])->name('paineis-usinagem');
        Route::match(['get', 'post'],'/paineis-montagem', [PainelController::class, 'paineisMontagem'])->name('paineis-montagem');
        Route::match(['get', 'post'],'/paineis-acabamento', [PainelController::class, 'paineisAcabamento'])->name('paineis-acabamento');
        Route::match(['get', 'post'],'/paineis-inspecao', [PainelController::class, 'paineisInspecao'])->name('paineis-inspecao');
        Route::match(['get', 'post'],'/paineis-embalar', [PainelController::class, 'paineisEmbalar'])->name('paineis-embalar');

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
