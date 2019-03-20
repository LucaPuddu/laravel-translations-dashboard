<?php

namespace LPuddu\LaravelTranslationsDashboard;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class LaravelTranslationsDashboardServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @param Filesystem $filesystem
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        // Add middleware to app
        $router = $this->app['router'];
        $router->aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \Spatie\Permission\Middlewares\PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class);

        $this->loadViewsFrom(__DIR__ . '/../frontend/src/views', 'laravel-translations-dashboard');
        
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-translations-dashboard.php', 'laravel-translations-dashboard');

        // Register the service the package provides.
        $this->app->singleton('laravel-translations-dashboard', function ($app) {
            return new LaravelTranslationsDashboard;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-translations-dashboard'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-translations-dashboard.php' => config_path('laravel-translations-dashboard.php'),
        ], 'laravel-translations-dashboard.config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../frontend/src/views' => base_path('resources/views/vendor/lpuddu/laravel-translations-dashboard'),
        ], 'laravel-translations-dashboard.views');

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../frontend/dist' => public_path('vendor/lpuddu/laravel-translations-dashboard'),
        ], 'laravel-translations-dashboard.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/lpuddu'),
        ], 'laravel-translations-dashboard.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
