<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Assurrussa\GridView\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(): void
    {
        $this->registerPatterns();
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(): void
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::group([
            'namespace'  => $this->namespace,
            'middleware' => config('amigrid.middleware'),
            'prefix'     => config('amigrid.prefix')
        ], function ($router) {
            /** @var \Illuminate\Routing\Router $router */
            $routesFile = __DIR__ . '/../Routes/routes.php';
            if(file_exists($routesFile)) {
                require $routesFile;
            }
        });
    }

    /**
     * All patterns url
     */
    protected function registerPatterns(): void
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('scope', '[A-za-z0-9-]+');
        Route::pattern('model', '[A-za-z0-9-]+');
    }
}