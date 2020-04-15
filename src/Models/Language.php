<?php

namespace LPuddu\LaravelTranslationsDashboard\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Language
 * @package LPuddu\LaravelTranslationsDashboard\Models
 * @property bool $visible
 */
class Language extends \Waavi\Translation\Models\Language
{
    protected $fillable = [
        'locale',
        'name',
        'visible'
    ];
}
