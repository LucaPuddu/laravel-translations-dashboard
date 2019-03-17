<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 17/02/2019
 * Time: 21:35
 */


use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => config('laravel-translations-dashboard.middlewares'),
        'prefix' => config('laravel-translations-dashboard.prefix')
    ],
    function () {
        Route::group(['middleware' => ['permission:manage-settings']], function () {
            Route::get('/settings', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@settings')->name('languages-settings');
            Route::post('/settings', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@editSettings')->name('languages-settings-edit');
        });

        Route::group(['middleware' => ['permission:manage-pages']], function () {
            Route::post('/pages/edit', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@editPage')->name('languages-page-edit');
            Route::post('/pages/delete', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@deletePage')->name('languages-page-delete');
            Route::post('/pages/new', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@newPage')->name('languages-page-new');

            Route::post('/element/delete', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@deleteElement')->name('languages-element-delete');
        });

        Route::group(['middleware' => ['permission:manage-languages']], function () {
            Route::post('/languages/edit', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@editLanguage')->name('languages-language-edit');
            Route::post('/languages/delete', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@deleteLanguage')->name('languages-language-delete');
            Route::post('/languages/new', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@newLanguage')->name('languages-language-new');
        });

        Route::group(['middleware' => ['permission:translate']], function () {
            Route::post('/element/edit', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@editElement')->name('languages-element-edit');
            Route::post('/element/new', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@newElement')->name('languages-element-new');
        });

        Route::get('/', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@home')->name('languages-home');
        Route::get('/languages', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@languages')->name('languages-languages');
        Route::get('/pages', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@pages')->name('languages-pages');
        Route::get('/pages/{page}', 'LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboard@page')->name('languages-page');
    }
);


