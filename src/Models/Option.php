<?php

namespace LPuddu\LaravelTranslationsDashboard\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Option
 *
 * @property int $id
 * @property string $name
 * @property string $value
 *
 * @package LPuddu\LaravelTranslationsDashboard\Models
 */
class Option extends Model
{
    protected $table = 'translator_options';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'value'
    ];
}
