<?php

namespace Assurrussa\GridView\Providers;

use Illuminate\Routing\Router;
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
    public function boot(Router $router)
    {
        $this->registerPatterns($router);
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapWebRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
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
    protected function registerPatterns(Router $router)
    {
        $router->pattern('id', '[0-9]+');
        $router->pattern('scope', '[A-za-z0-9-]+');
        $router->pattern('model', '[A-za-z0-9-]+');
    }
}