<?php

namespace LPuddu\LaravelTranslationsDashboard;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use LPuddu\LaravelTranslationsDashboard\Commands\Init;
use LPuddu\LaravelTranslationsDashboard\Commands\PublishAssets;
use LPuddu\LaravelTranslationsDashboard\Commands\PublishMigrations;
use LPuddu\LaravelTranslationsDashboard\Commands\PublishSpatieMigrations;

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
        $router->aliasMiddleware('role_or_permission',
            \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class);

        $this->loadViewsFrom(base_path('resources/views/vendor/lpuddu/laravel-translations-dashboard'),
            'laravel-translations-dashboard');
        $this->loadViewsFrom(__DIR__ . '/../frontend/src/views', 'laravel-translations-dashboard');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole($filesystem);
        }
    }

    /**
     * Console-specific booting.
     *
     * @param Filesystem $filesystem
     * @return void
     */
    protected function bootForConsole(Filesystem $filesystem)
    {
        // Publishing migrations
        if (function_exists('config_path')) { // function not available and 'publish' not relevant in Lumen
            $migrations = [
                'create_options_table',
                'add_is_visible_to_translator_languages_tables',
                'add_permissions_and_roles',
            ];

            $toPublish = [];
            foreach ($migrations as $migration) {
                $toPublish[__DIR__ . "/../database/migrations/{$migration}.php.stub"] = $this->getMigrationFileName(
                    $filesystem,
                    $migration
                );
            }
            $this->publishes($toPublish, 'laravel-translations-dashboard.migrations');
        }

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/laravel-translations-dashboard.php' => config_path('laravel-translations-dashboard.php'),
            __DIR__ . '/../config/translator.php' => config_path('translator.php'),
        ], 'laravel-translations-dashboard.config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../frontend/src/views' => base_path('resources/views/vendor/lpuddu/laravel-translations-dashboard'),
        ], 'laravel-translations-dashboard.views');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/../frontend/dist' => public_path('vendor/lpuddu/laravel-translations-dashboard'),
        ], 'laravel-translations-dashboard.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/lpuddu'),
        ], 'laravel-translations-dashboard.views');*/

        // Registering package commands.
        $this->commands([
            Init::class,
            PublishAssets::class,
            PublishSpatieMigrations::class,
            PublishMigrations::class,
        ]);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @param string     $fileName
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem, string $fileName): string
    {
        $timestamp = date('Y_m_d_His', mktime(
            date('H'),
            date('i'),
            date('s') + 5,
            date("m"),
            date("d"),
            date("Y")
        ));

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $fileName) {
                return $filesystem->glob("{$path}*_{$fileName}.php");
            })->push($this->app->databasePath() . "/migrations/{$timestamp}_{$fileName}.php")
            ->first();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-translations-dashboard.php',
            'laravel-translations-dashboard');
        $this->mergeConfigFrom(__DIR__ . '/../config/translator.php', 'translator');
        $this->publishes([
            __DIR__ . '/../config/translator.php' => config_path('translator.php'),
        ]);

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
}
