<?php

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
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot()
    {
        $this->registerPatterns();
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => config('amigridview.middleware'),
            'prefix' => config('amigridview.prefix')
        ], function ($router) {
            /** @var \Illuminate\Routing\Router $router */
            $routesFile = __DIR__ . '/../Http/routes.php';
            if(file_exists($routesFile)) {
                require $routesFile;
            }
        });
    }

    /**
     * All patterns url
     */
    protected function registerPatterns()
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('scope', '[A-za-z0-9-]+');
        Route::pattern('model', '[A-za-z0-9-]+');
    }
}