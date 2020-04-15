# Laravel Translations Dashboard

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This package is a full-featured translations dashboard for laravel 5.7+.

You will have a ready-to-use dashboard to manage multilingual translation keys, to be loaded in your website with the Laravel native helpers (`__()`, `trans()` and `@lang()`).

The package combines the best features of two popular packages, namely [spatie\laravel-permission][link-spatie] and [Waavi/translation][link-waavi].
## Installation

Via Composer

``` bash
composer require lpuddu/laravel-translations-dashboard
```

## Usage

Once installed, publish assets folder, migrations and config files by running:
```
php artisan translations-dashboard:init
```

You can also customize the views by publishing them:
```php
php artisan vendor:publish --provider="LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboardServiceProvider" --tag="laravel-translations-dashboard.views"
```


When published, the [config/laravel-translations-dashboard.php config file][link-config] contains:
```php
return [
    /**
     * The prefix applied to all the routes, eg. /translations/home
     */
    'prefix' => 'translations',

    /**
     * The route used to logout translators
     */
    'logout_route' => '/logout',

    /**
     * The list of middlewares that all routes should use.
     * You can use this to authenticate users into the dashboard via the appropriate middleware.
     */
    'middlewares' => ['web', 'auth']
];
```

Remove your config cache:
```
php artisan config:clear
```

Execute the database migrations:
```
php artisan migrate
```

By default, the package adds four permissions (`manage-languages`, `manage-pages`, `manage-settings`, `translate`) and two roles (`admin` and `translator`).
Without permissions, you will only be able to login into the dashboard without doing much.

Make sure that your user has a `HasRoles` trait, then create a user with the `translator` or `admin` role to start working.

To make an admin, just use `$user->assignRole('admin')`. The change will be reflected immediately on your database. Check the [spatie\laravel-permission documentation][link-spatie] to explore more possibilities.

## Security

If you discover any security related issues, please use the issue tracker on github or email me at info__at__lucapuddu.com

## Credits

- [Luca Puddu][link-author]
- [Spatie][link-spatie]
- [Waavi][link-waavi]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/lpuddu/laravel-translations-dashboard.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/lpuddu/laravel-translations-dashboard.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/lpuddu/laravel-translations-dashboard
[link-downloads]: https://packagist.org/packages/lpuddu/laravel-translations-dashboard
[link-travis]: https://travis-ci.org/lpuddu/laravel-translations-dashboard
[link-author]: https://github.com/LucaPuddu
[link-spatie]: https://github.com/spatie/laravel-permission
[link-waavi]: https://github.com/Waavi/translation
[link-config]: config/laravel-translations-dashboard.php
