<?php

namespace LPuddu\LaravelTranslationsDashboard\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelTranslationsDashboard extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-translations-dashboard';
    }
}
