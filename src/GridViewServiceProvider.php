<?php

declare(strict_types=1);

namespace Assurrussa\GridView;

use Illuminate\Contracts\Container\Container;
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
    protected $defer = true;

    /**
     * The providers package
     */
    protected $providers = [
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/amigrid.php', GridView::NAME);
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', GridView::NAME);
        $this->loadViewsFrom(__DIR__ . '/../resources/views', GridView::NAME);
        $this->publishes([
            __DIR__ . '/../config/amigrid.php' => config_path('amigrid.php'),
        ], 'configs');
        $this->publishes([
            __DIR__ . '/../public/css' => base_path('public/vendor/grid-view/css'),
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
    public function register(): void
    {
        $this->app->bind(\Assurrussa\GridView\Interfaces\GridInterface::class, function ($app) {
            /** @var Container $app */
            return $app->make(GridView::class);
        });
        $this->app->alias(\Assurrussa\GridView\GridView::class, GridView::NAME);
        $this->app->alias(\Assurrussa\GridView\GridView::class, \Assurrussa\GridView\Interfaces\GridInterface::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [GridView::NAME];
    }

    /**
     * Register the providers.
     */
    protected function registerProviders(): void
    {
        foreach($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}