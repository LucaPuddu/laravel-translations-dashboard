<?php

namespace LPuddu\LaravelTranslationsDashboard\Models;

/**
 * Class Language
 * @package LPuddu\LaravelTranslationsDashboard\Models
 * @property string $locale
 * @property string $name
 * @property bool   $visible
 * @property bool   $rtl
 */
class Language extends \Waavi\Translation\Models\Language
{
    protected $fillable = [
        'locale',
        'name',
        'visible',
        'rtl',
    ];
}
