<?php

namespace Assurrussa\GridView;

use Illuminate\Support\ServiceProvider;

/**
 * Class GridViewServiceProvider
 *
 * @package Assurrussa\GridView
 */
class GridViewServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The providers package
     */
    protected $providers = [
        \Assurrussa\GridView\Providers\RouteServiceProvider::class,
    ];

    /**
     * Register the providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/amigridview.php', GridView::NAME);
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', GridView::NAME);
        $this->loadViewsFrom(__DIR__ . '/../resources/views', GridView::NAME);
        $this->publishes([
            __DIR__ . '/../config/amigridview.php' => config_path('amigridview.php'),
        ], 'configs');
        $this->publishes([
            __DIR__ . '/../public/js' => base_path('public/vendor/grid-view/js'),
        ], 'views');

        $this->registerProviders();
    }

    /**
     * Register the application services.
     * Use a Helpers service provider that loads all .php files from a folder.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GridView::NAME, function ($app) {
            return new GridView();
        });
        $this->app->alias(GridView::NAME, \Assurrussa\GridView\GridView::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [GridView::NAME];
    }
}